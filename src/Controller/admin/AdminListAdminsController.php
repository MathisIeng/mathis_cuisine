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

        return $this->render('admin/list_admins.html.twig', [
            'users' => $users
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


    #[Route('/admin/delete/{id}', name: 'admin_delete')]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager) {

        // Je récupère le user grâce à son id
        $user = $userRepository->find($id);
        // Je supprime l'user en question
        $entityManager->remove($user);
        // J'envoie l'info en bdd
        $entityManager->flush();

        return $this->redirectToRoute('admin_list_admins');
    }
}