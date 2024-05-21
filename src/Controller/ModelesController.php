<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ModelesController extends AbstractController
{
    #[Route('/modeles', name: 'app_modeles')]
    public function index(): Response
    {
        return $this->render('modeles/index.html.twig', [
            'controller_name' => 'ModelesController',
        ]);
    }
}
