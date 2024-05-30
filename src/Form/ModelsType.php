<?php

namespace App\Form;

use Assert\Length;
use App\Entity\User;
use Assert\NotBlank;
use App\Entity\Models;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
// use Webmozart\Assert\Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ModelsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Name',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Name should not be blank.',
                ]),
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => 'Name cannot be longer than {{ limit }} characters.',
                ]),
            ],
        ])
        ->add('path', TextType::class, [
            'label' => 'Path',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Path should not be blank.',
                ]),
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => 'Path cannot be longer than {{ limit }} characters.',
                ]),
                // new Assert\Url([
                //     'message' => 'Path should be a valid URL.',
                // ]),
            ],
        ])
        ->add('icon', TextType::class, [
            'label' => 'Icon',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Icon should not be blank.',
                ]),
                new Assert\Length([
                    'max' => 50,
                    'maxMessage' => 'Icon cannot be longer than {{ limit }} characters.',
                ]),
            ],
        ])
        ->add('role', ChoiceType::class, [
            'label' => 'Role',
            'choices' => [
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                'ROLE_USER' => 'ROLE_USER'
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Role should not be blank.',
                ]),
            ],
        ])
        ->add('Title', TextType::class, [
                'label'=>'Title'
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Models::class,
        ]);
    }
}
