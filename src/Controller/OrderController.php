<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\Orders;
use App\Entity\Product;
use App\Entity\OrderItems;
use App\Form\OrderFromType;
use App\Service\PdfService;
use App\Repository\UserRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/user/orders', name: 'app_orders')]
    public function index(Security $security,Request $request,EntityManagerInterface $em,ProductRepository $productRepository,SessionInterface $session): Response
    {
        $order = new Orders();
        $totalAmount = 0;
        $user = $security->getUser();
        $cart = $session->get('cart', []);
        // Payment info
        $SessionName =  $session->get('SessionName');
        $SessionLast =  $session->get('SessionLast');
        $id = $session->get('id');
        
        $orderForm = $this->createForm(OrderFromType::class, $order);
        $orderForm->handleRequest($request);
        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            foreach ($cart as $productId => $quantity) {
                $product = $productRepository->find($productId);     
                $total = $product->getPrice() * $quantity;           
                if ($product) {
                    // $newStockQu = $product->getStockQu() - $quantity;
                    $orderItem = new OrderItems();
                    $orderItem->setProdId($product);
                    $orderItem->setTotal($total);
                    $orderItem->setQuantity($quantity);
                    $order->addOrderItem($orderItem);
                    // $product->setStockQu($newStockQu); 
                    $em->persist($product);  
                    $totalAmount +=$total;  
                    $firstname = $order->getFirstName(); 
                    $lastname = $order->getLastName();
                    $session->get('order_form_filled');
                    $orderid = $order->getId();
                    
                }
            }
            $order->setUserId($user);
            $em->persist($order);
            $em->flush();
            $session->set('totalAmount' , $totalAmount);
            $session->set('SessionName',$firstname);
            $session->set('SessionLast',$lastname);
            $session->set('order_form_filled',true);
            $session->set('id',$orderid);
            $this->addFlash('success', 'Order placed successfully.');
            return $this->redirectToRoute('app_payment');
        }
        return $this->render('order/index.html.twig',[
            'form'=>$orderForm,
            'totalAmount'=> $totalAmount,
            'SessionName'=>$SessionName,
            'SessionLast'=>$SessionLast,
            'id'=>$id
        ]);
    }

    #[Route('/admin/order',name:'app_order')]
    public function showorder(){
        return $this->render('order/show.html.twig');
    }

    #[Route('/DataOrder',  name: 'DataOrder')]
    public function show(OrdersRepository $ordersRepository, Request $request,UserRepository $userRepository):JsonResponse{
        $draw = $request->query->get('draw');
        $start = $request->query->get('start') ?? 0;
        $length = $request->query->get('length') ?? 10;
        $search = $request->query->all('search')['value']  ?? '';
        $order = $request->query->all('order');
        $orderColumnIndex = isset($order[0]['column']) ? $order[0]['column'] : null;
        $columns = $request->query->all('columns');
        $orderColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : null;
        $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $queryBuilder = $ordersRepository->createQueryBuilder('o');

        if (!empty($search)) {
            $queryBuilder->andWhere('o.Name LIKE :search')
                ->setParameter('search', "%$search%");
        }
        if (!empty($orderColumn)) {
            $queryBuilder->orderBy("o.$orderColumn", $orderDir);
        }
        $totalRecords = $ordersRepository->count([]);
        $queryBuilder->setFirstResult($start)
            ->setMaxResults($length);
        $results = $queryBuilder->getQuery()->getResult();
        $Orders = [];
        foreach ($results as $order) {
                $Orders[] = [
                    'id' => $order->getId(),
                    'FirstName'=>$order->getFirstName(),
                    'LastName'=>$order->getLastName(),
                    'Adresse'=>$order->getAdresse(),
                    'Phone'=>$order->getPhone(),
                    'OrderDate'=>$order->getOrderDate()->format('Y-m-d'),
                    // 'userId'=>$order->getUserId(),
                    'actions' => '<a href="/order/'.$order->getId().'/delete" class="btn btn-danger mb-2" onclick="return confirm(\'Are you sure you want to delete this modele?\')">Delete</a>'
                    ];
            }
            return new JsonResponse([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $Orders,
            ]);
        }

        #[Route('/order/{id}/delete',name:'order_delete')]
        public function delete (EntityManagerInterface $entityManagerInterface,Orders $orders){
           
          $entityManagerInterface->remove($orders);
          $entityManagerInterface->flush();
          $this->addFlash('success', 'Orders Deleted Successfully');
          return $this->redirectToRoute('app_order');
        }


        #[Route('/admin/pdf',  name: 'app_pdf')]
        public function orderPdf (PdfService $pdfService,OrdersRepository $ordersRepository){
            $DateOrder  = $ordersRepository->findAll();
        return $pdfService->orderpdf('order/pdf.html.twig',[
            'DateOrder'=>$DateOrder
        ]);


        }

     
}
