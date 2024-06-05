<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Form\OrderFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/order/create/{id}', name: 'app_order')]
    public function index(EntityManagerInterface $em , Request $request,ProductRepository $productRepository): Response
    {
        $order = new Orders();
        $OrderForm = $this->createForm(OrderFormType::class,$order);
        $OrderForm->handleRequest($request);
        if($OrderForm->isSubmitted() && $OrderForm->isValid()){
            $em->persist($order);
            $em->flush();
            $this->addFlash('success','Order Added Successfully');
            return $this->redirectToRoute('app_order');
        }
        return $this->render('order/index.html.twig',[
            'form'=>$OrderForm
        ]);
    }



    
}
