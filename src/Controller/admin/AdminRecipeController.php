<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{

    #[Route('/admin/create/recipe', name: 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function createRecipe(Request $request, EntityManagerInterface $entityManager) {

        // J'instancie une nouvelle class Recipe
        $recipe = new Recipe();

        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        // La fonction handleRequest récupère les données soumis par le formulaire
        // Il modifie l'entity pour chaque donnée et remplir l'entity
        // Grâce aux données du formulaire
        $adminRecipeForm->handleRequest($request);

        // On oublie pas de mettre dans l'entity Recipe une méthode constructeur
        // Qui entrera le DateTime qu'on a enlever du form pour pas laisser
        // La main sur la date aux user du site
        if ($adminRecipeForm->isSubmitted()) {
            $this->addFlash('success', 'Recette bien crée !');
            // Si le formulaire est bien soumis, on sauvegarde les données avec persist
            $entityManager->persist($recipe);
            // Et on envoie tout dans le base de donnée
            $entityManager->flush();
        }

        $formView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/create.html.twig', [
            'formView' => $formView,
        ]);

    }

}