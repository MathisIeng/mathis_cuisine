<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard() {

        $user = $this->getUser();


        return $this->render('admin/dashboard.html.twig', [
            'user' => $user
        ]);

    }
}