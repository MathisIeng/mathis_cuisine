<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{

    #[Route('/admin/create/category', name: 'admin_create_category')]
    public function createCategory(Request $request, EntityManagerInterface $entityManager)
    {

        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list_categories');
        }

        return $this->render('admin/category/create.html.twig', [
            'categoryForm' => $categoryForm->createView(),
        ]);

    }

    #[Route('/admin/list/categories', name: 'admin_list_categories')]
    public function listCategories(CategoryRepository $categoryRepository)
    {

        $categories = $categoryRepository->findAll();

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/update/category/{id}', name: 'admin_update_category')]
    public function updateCategory(int $id, Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {


        $category = $categoryRepository->find($id);
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list_categories');
        }

        return $this->render('admin/category/update.html.twig', [
            'categoryForm' => $categoryForm->createView(),
            'category' => $category,
        ]);

    }

    #[Route('/admin/delete/category/{id}', name: 'admin_delete_category')]
    public function deleteCategory(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager) {

        $category = $categoryRepository->find($id);

        $entityManager->remove($category);

        $entityManager->flush();

        return $this->redirectToRoute('admin_list_categories');
    }
}