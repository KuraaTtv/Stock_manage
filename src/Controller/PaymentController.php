<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\User;
use App\Entity\Orders;
use App\Entity\Payment;
use App\Entity\Product;
use App\Form\PaymentType;
use App\Service\PdfService;
use App\Repository\OrdersRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/user/payment', name: 'app_payment')]
    public function index(Security $security,Request $request,EntityManagerInterface $em ,ProductRepository $productRepository , SessionInterface $session , Orders $orders,OrdersRepository $ordersRepository): Response
    {    
        
        if(!$session->get('order_form_filled')){
            $this->addFlash('danger', 'Please create an order first.');
            return $this->redirectToRoute('app_order');
        }
        
        $cart = $session->get('cart', []);
        $PaymentAmount =  $session->get('totalAmount');
        $id = $session->get('id');
        $CardOwner = $session->get('SessionName');
        $CardOwnerLast = $session->get('SessionLast');
        $user = $security->getUser();
        $products = [];
        foreach($cart as $id=>$quntity){
        $product = $productRepository->find($id);
        if($product){
            $newStockQu = $product->getStockQu() - $quntity;
            $product->StockQu = $quntity;
            $product->setStockQu($newStockQu);
            $products[]=$product;
        }
        }        
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $payment->setPaymentAmount($PaymentAmount);
                $payment->setUserId($user);
                $payment->setPaymentDate(new \DateTime('today'));
                $em->persist($payment);
                $em->flush();
                $session->remove('totalAmount');
                // $session->remove('SessionName');
                // $session->remove('SessionLast');
                return $this->redirectToRoute('payment_show', ['id' => $payment->getId()]);
            }
                return $this->render('payment/index.html.twig', [
                'payment' => $payment,
                'form' => $form->createView(),
                'products' => $products,
                'totalAmount'=>$PaymentAmount,
                'CardOwner'=>$CardOwner,
                'CardOwnerLast'=>$CardOwnerLast,
                'id' =>$id
            ]);
        }
       
        #[Route('/user/payment/{id}',name:'payment_show')]
        public function show(Payment $payment,SessionInterface $session, OrdersRepository $ordersRepository,ProductRepository $productRepository)
        {
        $cart = $session->get('cart', []);
        $total = $session->get('totalAmount');
        $products = [];
        foreach($cart as $id=>$quntity){
        $product = $productRepository->find($id);
        if($product){
            $product->StockQu = $quntity;
            $products[]=$product;
        }
    }
            return $this->render('payment/show.html.twig', [
                'payment' => $payment,
                'products' => $products,
                'totalAmount'=>$total
            ]);
        }

        #[Route('/user/payment/{id}/print' , 'pdf_print')]
        public function print(Payment $payment,PdfService $pdfService,SessionInterface $session ,ProductRepository $productRepository): Response
        {
        $CardOwner = $session->get('SessionName');
        $CardOwnerLast = $session->get('SessionLast');
        $cart = $session->get('cart', []);
        $total = $session->get('totalAmount');
        $products = [];
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $product->StockQu = $quantity;
                $products[] = $product;
            }
        }
        $pdfData = [
            'payment' => $payment,
            'products' => $products,
            'totalAmount' => $total,
            'CardOwner'=>$CardOwner,
            'CardOwnerLast'=>$CardOwnerLast,
        ];
         $session->remove('cart');
        return $pdfService->generatePdf('payment/invoice.html.twig', $pdfData);
    }        
}


