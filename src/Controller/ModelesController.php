<?php

namespace App\Controller;

use App\Repository\ModelsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ModelesController extends AbstractController
{
    // private $modelsRepository;
    // // public function __construct(ModelsRepository $modelsRepository)
    // // {
    // //     $this->modelsRepository = $modelsRepository;
    // // }
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
            // $role = in_array('ROLE_ADMIN',$model['roles'])?'ADMIN':'USER';
                $ModeldData[] = [
                    'id' => $model->getId(),
                    'name' => $model->getName(),
                    'path' => $model->getPath(),
                    'icon'=>$model->getIcon(),
                    'role'=>$model->getRole(),
                    'actions' => '<a href="/category/'.$model->getId().'/edit" class="btn btn-info mb-2">Edit</a>
                                 <a href="/category/'.$model->getId().'/delete" class="btn btn-danger mb-2" onclick="return confirm(\'Are you sure you want to delete this category?\')">Delete</a>'
    
    
                    ];
            }
            return new JsonResponse([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $ModeldData,
            ]);
    }
    

}
