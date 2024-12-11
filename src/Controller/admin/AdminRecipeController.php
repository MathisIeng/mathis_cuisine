<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{

    #[Route('/admin/create/recipe', name: 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function createRecipe(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {

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

            // On pense pour les images à installer
            // composer require symfony/mime

            // Je récupère le fichier envoyé dans le champ image du formulaire
            $recipeImage = $adminRecipeForm->get('image')->getData();

            // Si il y a bien une image envoyée
            if ($recipeImage) {

                // Je génère un nom unique avec uniqid pour l'image
                $imageNewName = uniqid() . '.' . $recipeImage->guessExtension();

                // Récupère le chemin racine du projet Symfony grâce à parameterBag
                $rootDir = $parameterBag->get('kernel.project_dir');
                // Définit le répertoire de destination pour les fichiers uploadés
                // Ici, les fichiers seront stockés dans `public/assets/uploads`
                $uploadDir = $rootDir . '/public/assets/uploads';

                // Je déplace mon image dans le dossier uploads et je la renomme
                // avec le nom unique
                $recipeImage->move($uploadDir, $imageNewName);

                // Je stocke dans l'entity
                $recipe->setImage($imageNewName);
            }

            // Si le formulaire est bien soumis, on sauvegarde les données avec persist
            $entityManager->persist($recipe);
            // Et on envoie tout dans le base de donnée
            $entityManager->flush();

            $this->addFlash('success', 'Recette bien crée !');

            return $this->redirectToRoute('admin_recipe_list');
        }


        $formView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/create.html.twig', [
            'formView' => $formView,
        ]);

    }

    #[Route('/admin/recipes/list', name: 'admin_recipe_list', methods: ['GET'])]
    public function listRecipes(RecipeRepository $recipeRepository)
    {

        // dd('test');
        // On viens récupérer grâce au repo toutes les recettes et on les affiche dans la vue
        $recipes = $recipeRepository->findAll();

        return $this->render('admin/recipe/list.html.twig', [
            'recipes' => $recipes,
        ]);

    }

    #[Route('/admin/recipes/{id}/delete', name: 'admin_recipe_delete', requirements: ['id' => '\d+'])]
    public function deleteRecipe(int $id, RecipeRepository $recipeRepository, Recipe $recipe, EntityManagerInterface $entityManager)
    {

        // dd($id);

        $recipe = $recipeRepository->find($id);

        // dd($recipe);

        $entityManager->remove($recipe);
        $entityManager->flush();

        $this->addFlash('success', 'Recette bien supprimer !');

        return $this->redirectToRoute('admin_recipe_list');
    }

    #[Route('/admin/recipes/{id}/update', 'admin_recipe_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function updateRecipe(int $id, RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $recipe = $recipeRepository->find($id);

        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        $adminRecipeForm->handleRequest($request);

        if ($adminRecipeForm->isSubmitted()) {

            $recipeImage = $adminRecipeForm->get('image')->getData();

            if ($recipeImage) {
                $imageNewName = uniqid() . '.' . $recipeImage->guessExtension();

                $rootDir = $parameterBag->get('kernel.project_dir');
                $uploadsDir = $rootDir . '/public/assets/uploads';
                $recipeImage->move($uploadsDir, $imageNewName);

                $recipe->setImage($imageNewName);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recette modifiée');
        }

        $formView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/update.html.twig', [
            'formView' => $formView,
            'recipe' => $recipe
        ]);
    }
}