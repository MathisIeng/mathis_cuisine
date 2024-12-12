<?php

namespace App\Controller\public;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/recipes/{id}', name: 'recipe', requirements: ['id' => '\d+'], methods: ['GET'])]
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


    #[Route('/recipes/search', name: 'recipes_search', methods: ['GET'])]
    public function searchRecipes(RecipeRepository $recipeRepository, Request $request) {

        // dd('test')

        $search = $request->query->get('search');

        $recipes = $recipeRepository->findBySearchInTitle($search);

        return $this->render('/public/recipe/search.html.twig', [
            'recipes' => $recipes, 'search' => $search
        ]);
    }

}