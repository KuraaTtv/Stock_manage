<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class EditFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'EMAIL',
            'required' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter an Email',
                ]),
            ],
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Role Admin' => 'ROLE_ADMIN',
                'Role User' => 'ROLE_USER',
            ],
            'multiple' => true
        ]);
        // ->add('password', PasswordType::class, [
        //     'label' => 'Password',
        //     'required' => false,
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
