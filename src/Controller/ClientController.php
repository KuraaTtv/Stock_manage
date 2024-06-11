<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\session;

class ClientController extends AbstractController
{
    #[Route('/user/cart', name: 'app_cart')]
    public function index(SessionInterface $session,ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $products = [];
        foreach($cart as $id=>$quntity){
        $product = $productRepository->find($id);
        if($product){
            $product->StockQu = $quntity;
            $products[]=$product;
        }
    }
        return $this->render('client/cart.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/user/cart/{id}/add', name: 'cart_add')]
    public function add(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (!isset($cart[$id])) {
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        }
        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/user/cart/{id}/remove' , name:'cart_remove')]
    public function remove(SessionInterface $session,int $id){
        $cart = $session->get('cart', []);
        if(isset($cart[$id])){
            unset($cart[$id]);
            $session->set('cart',$cart);
            $this->addFlash('success', 'Item(s) removed from cart successfully.');
            return $this->redirectToRoute('app_test');
        }
        return $this->redirectToRoute('app_cart');
    }

}