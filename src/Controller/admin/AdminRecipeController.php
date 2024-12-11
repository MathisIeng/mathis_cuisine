<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use App\Repository\RecipeRepository;
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

            // Si le formulaire est bien soumis, on sauvegarde les données avec persist
            $entityManager->persist($recipe);
            // Et on envoie tout dans le base de donnée
            $entityManager->flush();

            $this->addFlash('success', 'Recette bien crée !');
        }

        $formView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/create.html.twig', [
            'formView' => $formView,
        ]);

    }

    #[Route('/admin/recipes/list', name: 'admin_recipes_list', methods: ['GET'])]
    public function listRecipes(RecipeRepository $recipeRepository) {

       // dd('test');
        // On viens récupérer grâce au repo toutes les recettes et on les affiche dans la vue
        $recipes = $recipeRepository->findAll();

        return $this->render('admin/recipe/list.html.twig', [
            'recipes' => $recipes,
        ]);

    }

}