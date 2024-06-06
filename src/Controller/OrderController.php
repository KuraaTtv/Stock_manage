<?php

namespace App\Controller;

use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Product;
use App\Form\OrderFromType;
use App\Repository\ProductRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request,EntityManagerInterface $em,ProductRepository $productRepository,SessionInterface $session): Response
    {
        $order = new Orders();
        $cart = $session->get('cart', []);
        $orderForm = $this->createForm(OrderFromType::class, $order);
        $orderForm->handleRequest($request);
        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            foreach ($cart as $productId => $quantity) {
                $product = $productRepository->find($productId);     
                $total = $product->getPrice() * $quantity;           
                if ($product) {
                    $newStockQu = $product->getStockQu() - $quantity;
                    $orderItem = new OrderItems();
                    $orderItem->setProdId($product);
                    $orderItem->setTotal($total);
                    $orderItem->setQuantity($quantity);
                    $order->addOrderItem($orderItem);
                    $product->setStockQu($newStockQu); 
                    $em->persist($product);              
                }
            }
            $em->persist($order);
            $em->flush();

            $session->remove('cart');

            $this->addFlash('success', 'Order placed successfully.');
            return $this->redirectToRoute('app_test');
        }
        return $this->render('order/index.html.twig',[
            'form'=>$orderForm
        ]);


    }
}
