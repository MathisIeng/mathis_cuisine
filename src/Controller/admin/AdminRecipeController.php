<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{

    #[Route('/admin/create/recipe', name: 'admin_create_recipe', methods: ['GET'])]
    public function createRecipe() {

        $recipe = new Recipe();

        $adminRecipeForm = $this->createForm(AdminRecipeType::class);

        $formView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/create.html.twig', [
            'formView' => $formView,
        ]);

    }

}