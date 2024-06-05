<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Models;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\EditFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\ModelsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TestController extends AbstractController
{
    

    #[Route('/dashboard', name: 'app_test')]
    public function index(UserRepository $userRepository,ProductRepository $productRepository): Response
    { 
        $users = $userRepository->HowMuchUser();
        $admins = $userRepository->HowUserAdmin();
        $all_user = $userRepository->findAll();
        $products = $productRepository->count();
        $pro = $productRepository->findAll();

        if(!$this->IsGranted('ROLE_ADMIN')){
            return $this->render('client/index.html.twig' ,[
                'products' =>$pro
            ]);
        }
        return $this->render('test/index.html.twig',[
            'users'=>$users,
            'admin'=>$admins,
            'all_user'=>$all_user,
            'products'=>$products
        ]);
    
    }


    #[Route('/{id}/edit', name:'edit_profil' , methods: ['GET', 'POST'])]
    public function edit(User $user, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        
        $editForm = $this->createForm(EditFormType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            $this->addFlash('success', 'Profile Updated Successfully');
    
            // Redirect to a different route
            return $this->redirectToRoute('app_test');
        }
    
        // Render the form in the template
        return $this->render('Admin/edit.html.twig', [
            'form' => $editForm->createView()
        ]);
    }

    #[Route('/delete/{id}',name:'delete')]
    public function delete (EntityManagerInterface $entityManagerInterface,User $user){
      $entityManagerInterface->remove($user);
      $entityManagerInterface->flush();
      $this->addFlash('success', 'Profile Deleted Successfully');
      return $this->redirectToRoute('app_test');
    }
    #[Route('/data',  name: 'category_data')]
    public function getData(CategoryRepository $categoryRepository, Request $request): JsonResponse
    {
        $draw = $request->query->get('draw');
        $start = $request->query->get('start') ?? 0;
        $length = $request->query->get('length') ?? 10;
        $search = $request->query->all('search')['value']  ?? '';
        $order = $request->query->all('order');
        $orderColumnIndex = isset($order[0]['column']) ? $order[0]['column'] : null;
        $columns = $request->query->all('columns');
        $orderColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : null;
        $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $queryBuilder = $categoryRepository->createQueryBuilder('c');
        // Apply search query
        if (!empty($search)) {
            $queryBuilder->andWhere('c.Name LIKE :search OR c.Description LIKE :search')
                ->setParameter('search', "%$search%");
        }
        if (!empty($orderColumn)) {
            $queryBuilder->orderBy("c.$orderColumn", $orderDir);
        }
        $totalRecords = $categoryRepository->count([]);
        $queryBuilder->setFirstResult($start)
            ->setMaxResults($length);
            
        $results = $queryBuilder->getQuery()->getResult();
        $formattedData = [];
        foreach ($results as $category) {
        
            $formattedData[] = [
                'id' => $category->getId(),
                'Name' => $category->getName(),
                'Description' => $category->getDescription(),
                'actions' => '<a href="/category/'.$category->getId().'/edit" class="btn btn-info mb-2">Edit</a>
                             <a href="/category/'.$category->getId().'/delete" class="btn btn-danger mb-2" onclick="return confirm(\'Are you sure you want to delete this category?\')">Delete</a>'
                ];
        }
        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $formattedData,
        ]);
    }

    // Count The product

    



    
    
}


