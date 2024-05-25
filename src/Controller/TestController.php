<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\CategoryType;
use App\Form\EditFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TestController extends AbstractController
{

    #[Route('/dashboard', name: 'app_test')]
    public function index(UserRepository $userRepository): Response
    { 
        // dd($users);
        // $this->isGranted('ROLE_ADMIN');
        $users = $userRepository->HowMuchUser();
        $admins = $userRepository->HowUserAdmin();
        $all_user = $userRepository->findAll();
        return $this->render('test/index.html.twig',[
            'users'=>$users,
            'admin'=>$admins,
            'all_user'=>$all_user,
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
    public function delete (Request $request,EntityManagerInterface $entityManagerInterface,User $user){
      $entityManagerInterface->remove($user);
      $entityManagerInterface->flush();
      $this->addFlash('success', 'Profile Deleted Successfully');
      return $this->redirectToRoute('app_test');
    }


    // CRUD OF CATEGORY

    #[Route('/categroy',name:'categroy')]
    public function create(Request $request , EntityManagerInterface $em){
        $category = new Category();
        $CatForm = $this->createForm(CategoryType::class , $category);
        $CatForm->handleRequest($request);
        if($CatForm->isSubmitted() && $CatForm->isValid()){
            $em->persist($category);
            $em->flush();
            $this->addFlash('success','Category Added Successfully');
            return $this->redirectToRoute('app_test');
        }
        
        // $this->addFlash('error','Error Category Not Added.');
        return $this->render('Admin/category.html.twig',[
            'Form'=>$CatForm
        ]);

    }
    
}
