<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditFormType;
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


    #[Route('/{id}/edit', name:'edit_profil')]
    public function edit(User $user,EntityManagerInterface $entityManagerInterface , Request $request )
    {
      $EditForm = $this->createForm(EditFormType::class , $user);
      $EditForm->handleRequest($request);
      if($EditForm->isSubmitted()  && $EditForm->isValid()){
        $entityManagerInterface->flush();
        $this->addFlash('sucess','Profil Updated Sucessfuly');
        return $this->redirectToRoute('app_test');
      }
      return $this->render('Admin/edit.html.twig',[
        // 'test'=>$test,
        'Editform'=>$EditForm->createview(),
    ]);
    }
}
