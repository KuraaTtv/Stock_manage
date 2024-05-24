<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

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

}