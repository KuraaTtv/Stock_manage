<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
  #[Route('/product', name:'app_product')]
  public function index(){
    return $this->render('product/index.html.twig');
  }
    #[Route('/DataProduct',  name: 'Prod_data')]
    public function show(ProductRepository $productRepository, Request $request):JsonResponse{
        $draw = $request->query->get('draw');
        $start = $request->query->get('start') ?? 0;
        $length = $request->query->get('length') ?? 10;
        $search = $request->query->all('search')['value']  ?? '';
        $order = $request->query->all('order');
        $orderColumnIndex = isset($order[0]['column']) ? $order[0]['column'] : null;
        $columns = $request->query->all('columns');
        $orderColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : null;
        $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $queryBuilder = $productRepository->createQueryBuilder('p');

        if (!empty($search)) {
            $queryBuilder->andWhere('p.Name LIKE :search OR p.Price LIKE :search OR p.Stock_qu LIKE :search OR p.category LIKE :search ')
                ->setParameter('search', "%$search%");
        }
        if (!empty($orderColumn)) {
            $queryBuilder->orderBy("p.$orderColumn", $orderDir);
        }
        $totalRecords = $productRepository->count([]);
        $queryBuilder->setFirstResult($start)
            ->setMaxResults($length);
        $results = $queryBuilder->getQuery()->getResult();
        $Products = [];
        foreach ($results as $product) {
                $Products[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'stockQu'=>$product->getStockQu(),
                    'picture'=>$product->getPicture(),
                    'description'=>$product->getDescription(),
                    'category' =>$product->getCategory()->getName(),
                    'actions' => '<a href="/product/'.$product->getId().'/edit" class="btn btn-info mb-2">Edit</a>
                                 <a href="/product/'.$product->getId().'/delete" class="btn btn-danger mb-2" onclick="return confirm(\'Are you sure you want to delete this modele?\')">Delete</a>'
                    ];
            }
            return new JsonResponse([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $Products,
            ]);
        }
#[Route('/product/create',name:'create_product')]
    public function create (EntityManagerInterface $em,Request $request)
    {
        $product = new Product();
        $ProdForm = $this->createForm(ProductType::class,$product);
        $ProdForm->handleRequest($request);
        if($ProdForm->isSubmitted() && $ProdForm->isValid()){
            // Upload img
            $img = $ProdForm['picture']->getData();
            if ($img) {
                $newFilename = uniqid().'.'.$img->guessExtension();
                $img->move($this->getParameter('uploads'),$newFilename);
                $product->setPicture($newFilename);
            }
            $em->persist($product);
            $em->flush();
            $this->addFlash('success','Product Added Successfully');
            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/create.html.twig',[
            'Form'=>$ProdForm
        ]);
    }

    #[Route('/product/{id}/delete' , name:'prod_delete')]

    public function delete(EntityManagerInterface $em,Product $product){
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Product Deleted Successfully');
        return $this->redirectToRoute('app_product');

    }


    #[Route('/product/{id}/edit' , name:'prod_edit')]

    public function edit(EntityManagerInterface $em,Product $product,Request $request){
        $editFormPro = $this->createForm(ProductType::class, $product);
        $editFormPro->handleRequest($request);
        if ($editFormPro->isSubmitted() && $editFormPro->isValid()) {
            $img = $editFormPro['picture']->getData();
            if ($img) {
                $newFilename = uniqid().'.'.$img->guessExtension();
                $img->move($this->getParameter('uploads'),$newFilename);
                $product->setPicture($newFilename);
            }
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Product Updated Successfully');
            // Redirect to a different route
            return $this->redirectToRoute('app_product');
        }
    // }
    // catch(\Exception $e){
        // $this->addFlash('error','Error Modele Not Updatedd.');
        return $this->render('product/edit.html.twig', [
            'Form' => $editFormPro->createView()
        ]);
        

    }
}
