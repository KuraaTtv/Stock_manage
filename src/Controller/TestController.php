<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/dashboard', name: 'app_test')]
    public function index(): Response
    {
        // $this->isGranted('ROLE_ADMIN');
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
