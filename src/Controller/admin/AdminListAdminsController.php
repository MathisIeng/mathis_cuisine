<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminListAdminsController extends AbstractController
{

    #[Route('/admin/list/admins', name: 'admin_list_admins')]
    public function listAdmins(UserRepository $userRepository) {

        $users = $userRepository->findAll();
        // On crée une variable currentUser pour afficher dans notre header Admin
        // L'utilisateur qui est connecté
        $currentUser = $this->getUser();

        return $this->render('admin/list_admins.html.twig', [
            'users' => $users,
            'currentUser' => $currentUser
        ]);
    }

    #[Route('/admin/create/user', name: 'admin_create_user')]
    public function createUser(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager) {

        $user = new User();

        // Création du formulaire
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        // dd('test');

        // Si le formulaire est soumis et valide
        if ($userForm->isSubmitted() && $userForm->isValid()) {

            // On récupère le mot de passe tel qu'il est soumis
            $password = $userForm->get('password')->getData();
            // Grâce à ce qu'on a passer en paramètre on le hache
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);

            // Enregistrement en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list_admins');
        }

        return $this->render('admin/create_user.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }


    #[Route('/admin/delete/{id}', name: 'admin_delete', requirements: ['id' => '\d+'])]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager) {

        // Je récupère le user grâce à son id
        $user = $userRepository->find($id);

        // Vérification si l'utilisateur connecté est le même qu'on veut supprimer
        // On interdit la suppression
        if ($id === $this->getUser()->getId()) {
            $this->addFlash('success', 'Vous ne pouvez pas supprimer l\'utilisateur connecté');

            return $this->redirectToRoute('admin_list_admins');
        }

        // Je supprime l'user en question
        $entityManager->remove($user);
        // J'envoie l'info en bdd
        $entityManager->flush();

        return $this->redirectToRoute('admin_list_admins');
    }

    #[Route('/admin/update/{id}', name: 'admin_update', requirements: ['id' => '\d+'])]
    public function updateUser(int $id, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher) {

        // On récupère l'utilisateur
        $user = $entityManager->find(User::class, $id);

        // On crée le formulaire
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        // Si le formulaire a été soumis et est valide
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // On récupère le mot de passe soumis
            $password = $userForm->get('password')->getData();

            // Si un mot de passe est fourni, on le hache et on l'enregistre
            if (!empty($password)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }
            // Sinon, ne rien faire, on garde l'ancien mot de passe

            // Enregistrement des changements
            $entityManager->flush();

            // Redirection après mise à jour
            return $this->redirectToRoute('admin_list_admins');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('admin/update_user.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
}