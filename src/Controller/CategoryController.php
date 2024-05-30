<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/category.html.twig');
    }

    #[Route('/categroy',name:'categroy_create')]
    public function create(Request $request , EntityManagerInterface $em){
        // try{
        $category = new Category();
        $CatForm = $this->createForm(CategoryType::class , $category);
        $CatForm->handleRequest($request);
        if($CatForm->isSubmitted() && $CatForm->isValid()){
            $em->persist($category);
            $em->flush();
            $this->addFlash('success','Category Added Successfully');
            return $this->redirectToRoute('app_category');
        }
    // }
    // catch(\Exception $e){
        // $this->addFlash('error','Error Category Not Added.');
        return $this->render('category/create.html.twig',[
            'Form'=>$CatForm
        ]);
    // }
    }

    #[Route('/category/{id}/edit', name:'categroy_edit')]
    public function editCa(Category $category, EntityManagerInterface $entityManagerInterface, Request $request)
    {
        // try{
        $editFormCa = $this->createForm(CategoryType::class, $category);
        $editFormCa->handleRequest($request);
        if ($editFormCa->isSubmitted() && $editFormCa->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();
            $this->addFlash('success', 'Category Updated Successfully');
            // Redirect to a different route
            return $this->redirectToRoute('categroy_create');
        }
        // }
        // catch(\Exception $e){
        // $this->addFlash('error','Error Category Not Updatedd.');
        return $this->render('category/edit.html.twig', [
            'form' => $editFormCa->createView()
        ]);
    // }
    }


    #[Route('/category/{id}/delete',name:'categroy_delete')]
    public function delete (EntityManagerInterface $entityManagerInterface,Category $category){
       
      $entityManagerInterface->remove($category);
      $entityManagerInterface->flush();
      $this->addFlash('success', 'Category Deleted Successfully');
      return $this->redirectToRoute('app_category');
        // $this->addFlash('error','Error Category Not Deleted.');
        // return $this->redirectToRoute('app_category');
       
    }
    
}
