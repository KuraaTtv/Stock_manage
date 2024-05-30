<?php

namespace App\Controller;

use App\Entity\Models;
use App\Form\ModelsType;
use Doctrine\ORM\EntityManager;
use App\Repository\ModelsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModelesController extends AbstractController
{
    #[Route('/modeles', name: 'app_modeles')]
    public function index(ModelsRepository $modelsRepository):Response
    {
        $models = $modelsRepository->findAll();
        return $this->render('modeles/index.html.twig', [
            'models' => $models,
        ]);
    }


    #[Route('/DataModele',  name: 'models_data')]
    public function show(ModelsRepository $modelsRepository, Request $request):JsonResponse{
        $draw = $request->query->get('draw');
        $start = $request->query->get('start') ?? 0;
        $length = $request->query->get('length') ?? 10;
        $search = $request->query->all('search')['value']  ?? '';
        $order = $request->query->all('order');
        $orderColumnIndex = isset($order[0]['column']) ? $order[0]['column'] : null;
        $columns = $request->query->all('columns');
        $orderColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : null;
        $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $queryBuilder = $modelsRepository->createQueryBuilder('m');

        if (!empty($search)) {
            $queryBuilder->andWhere('m.name LIKE :search  OR m.path LIKE :search OR m.icon LIKE :search OR m.role LIKE :search')
                ->setParameter('search', "%$search%");
        }
        if (!empty($orderColumn)) {
            $queryBuilder->orderBy("m.$orderColumn", $orderDir);
        }
        $totalRecords = $modelsRepository->count([]);
        $queryBuilder->setFirstResult($start)
            ->setMaxResults($length);

        $results = $queryBuilder->getQuery()->getResult();
        $ModeldData = [];

        foreach ($results as $model) {
            // $role = in_array('ROLE_ADMIN',$model['role'])?'ADMIN':'USER';
                $ModeldData[] = [
                    'id' => $model->getId(),
                    'name' => $model->getName(),
                    'path' => $model->getPath(),
                    'icon'=>$model->getIcon(),
                    'role'=>$model->getRole(),
                    'title'=>$model->getTitle(),
                    'actions' => '<a href="/model/'.$model->getId().'/edit" class="btn btn-info mb-2">Edit</a>
                                 <a href="/model/'.$model->getId().'/delete" class="btn btn-danger mb-2" onclick="return confirm(\'Are you sure you want to delete this modele?\')">Delete</a>'
    
    
                    ];
            }
            return new JsonResponse([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $ModeldData,
            ]);
    }


    #[Route('/model/create',name:'mod_create')]
    public function create(EntityManagerInterface $em,Request $request){
        // try{
        $models = new Models();
        $ModForm = $this->createForm(ModelsType::class , $models);
        $ModForm->handleRequest($request);
        if($ModForm->isSubmitted() && $ModForm->isValid()){
            $em->persist($models);
            $em->flush();
            $this->addFlash('success','Models Added Successfully');
            return $this->redirectToRoute('app_modeles');
        // }
        }
        // catch(\Exception $e){
        // $this->addFlash('error','Error Model Not Added.');
        return $this->render('modeles/create.html.twig',[
            'Form'=>$ModForm
        ]);
    
    }
    #[Route('/model/{id}/edit', name:'model_edit')]
    public function editModel(Models $models, EntityManagerInterface $entityManagerInterface, Request $request){
        // try{
            $editFormMod = $this->createForm(ModelsType::class, $models);
            $editFormMod->handleRequest($request);
            if ($editFormMod->isSubmitted() && $editFormMod->isValid()) {
                $entityManagerInterface->persist($models);
                $entityManagerInterface->flush();
                $this->addFlash('success', 'Modele Updated Successfully');
                // Redirect to a different route
                return $this->redirectToRoute('app_modeles');
            }
        // }
        // catch(\Exception $e){
            // $this->addFlash('error','Error Modele Not Updatedd.');
            return $this->render('modeles/edit.html.twig', [
                'Form' => $editFormMod->createView()
            ]);

        // }
    }

        #[Route('/model/{id}/delete', name:'model_delete')]
        public function delete (EntityManagerInterface $em,Models $models){
            $em->remove($models);
            $em->flush();
            $this->addFlash('success', 'Modele Deleted Successfully');
            return $this->redirectToRoute('app_modeles');
        }






    


    

}
