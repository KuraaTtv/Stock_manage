<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditFormType;
use Doctrine\ORM\EntityManager;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends AbstractController
{
    
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request ,EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }
        $user_data = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        if (!$user_data) {
            // throw $this->createNotFoundException('');
            $this->addFlash('error','User not found.');
        }
        $ProfilForm = $this->createForm(RegistrationFormType::class, $user_data);
        $ProfilForm->handleRequest($request);

        if ($ProfilForm->isSubmitted() && $ProfilForm->isValid()) {
            $plainPassword = $ProfilForm->get('plainPassword')->getData(); // Assuming 'plainPassword' is the field name in your form
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user_data, $plainPassword);
                $user_data->setPassword($hashedPassword);
            }
            $entityManager->persist($user_data);
            $entityManager->flush();
            $this->addFlash('success', 'Your profile has been updated successfully!');
            return $this->redirectToRoute('app_test');
        }
        
        return $this->render('profile/index.html.twig', [
            'ProfilForm' => $ProfilForm->createView(),
        ]);
    }
#[Route('/admin/profils', name:'users_profil')]
public function profils(){
    return $this->render('profile/users.html.twig');
}

    #[Route('/DataUser',  name: 'User_data')]
    public function show(UserRepository $userRepository, Request $request , SecurityController $securityController):JsonResponse{
        $draw = $request->query->get('draw');
        $start = $request->query->get('start') ?? 0;
        $length = $request->query->get('length') ?? 10;
        $search = $request->query->all('search')['value']  ?? '';
        $order = $request->query->all('order');
        $orderColumnIndex = isset($order[0]['column']) ? $order[0]['column'] : null;
        $columns = $request->query->all('columns');
        $orderColumn = isset($columns[$orderColumnIndex]['data']) ? $columns[$orderColumnIndex]['data'] : null;
        $orderDir = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';
        $queryBuilder = $userRepository->createQueryBuilder('u');

        if (!empty($search)) {
            $queryBuilder->andWhere('u.email LIKE :search  OR u.roles LIKE :search')
                ->setParameter('search', "%$search%");
        }
        if (!empty($orderColumn)) {
            $queryBuilder->orderBy("u.$orderColumn", $orderDir);
        }
        $totalRecords = $userRepository->count([]);
        $queryBuilder->setFirstResult($start)
            ->setMaxResults($length);

        $results = $queryBuilder->getQuery()->getResult();
        $UserData = [];
        // $currentuser =$securityController->getUser();
        foreach ($results as $user) {
            $role = in_array('ROLE_ADMIN',$user->getRoles())?'ADMIN':'USER';
            if($role =='ADMIN'){
                $actions ='There is no actions';
            }
            elseif($role =='USER'){
                 $actions = '<a href="/profile/'.$user->getId().'/edit" class="btn btn-info mb-2">Edit</a>
                 <a href="/profile/'.$user->getId().'/delete" class="btn btn-danger mb-2" onclick="return confirm(\'Are you sure you want to delete this modele?\')">Delete</a>';
            }
                $UserData[] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'roles' => $role,
                    'actions' => $actions
                    ];
           
        }
            return new JsonResponse([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $UserData,
            ]);
    }

    #[Route('/admin/profile/{id}/edit', name:'user_edit')]
    public function editUser(User $user, EntityManagerInterface $entityManagerInterface, Request $request){
        // try{
            $editFormMod = $this->createForm(EditFormType::class, $user);
            $editFormMod->handleRequest($request);
            if ($editFormMod->isSubmitted() && $editFormMod->isValid()) {
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();
                $this->addFlash('success', 'User Updated Successfully');
                return $this->redirectToRoute('users_profil');
            }
            return $this->render('profile/edit.html.twig', [
                'form' => $editFormMod->createView()
            ]);
        }
        #[Route('/admin/profile/{id}/delete',name:'delete_user')]
        public function delete (EntityManagerInterface $entityManagerInterface,User $user){
          $entityManagerInterface->remove($user);
          $entityManagerInterface->flush();
          $this->addFlash('success', 'Profile Deleted Successfully');
          return $this->redirectToRoute('users_profil');
        }
}

