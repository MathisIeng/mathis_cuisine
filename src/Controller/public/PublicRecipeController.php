<?php

namespace App\Controller\public;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicRecipeController extends AbstractController
{

    #[Route('/recipes', name: 'recipes')]
    public function listPublicRecipes(RecipeRepository $recipeRepository) {

        $recipes = $recipeRepository->findBy(['isPublished' => true]);

        return $this->render('/public/recipe/list.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recipes/{id}', name: 'recipe')]
    public function showRecipe(int $id, RecipeRepository $recipeRepository) {

        $recipe = $recipeRepository->find($id);

        if (!$recipe) {
            $notFoundResponse = new Response('Recette non trouvée', 404);
            return $notFoundResponse;
        }

        return $this->render('/public/recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

}