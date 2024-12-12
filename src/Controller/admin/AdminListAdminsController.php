<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
}