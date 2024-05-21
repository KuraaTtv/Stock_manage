<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashController extends AbstractController
{
    #[Route('/dash', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('dashboard.html.twig', [
            'controller_name' => 'DashController',
        ]);
    }
}
