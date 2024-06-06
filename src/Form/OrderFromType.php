<?php

namespace App\Form;

use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OrderFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('FirstName', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'First name is required.',
                ]),
                new Assert\Length([
                    'max' => 50,
                    'maxMessage' => 'First name cannot be longer than {{ limit }} characters.',
                ]),
            ],
        ])
        ->add('LastName', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Last name is required.',
                ]),
                new Assert\Length([
                    'max' => 50,
                    'maxMessage' => 'Last name cannot be longer than {{ limit }} characters.',
                ]),
            ],
        ])
        ->add('Adresse', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Address is required.',
                ]),
            ],
        ])
        ->add('Phone', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Phone number is required.',
                ]),
                new Assert\Regex([
                    'pattern' => '/^\+?[0-9\s\-]+$/',
                    'message' => 'Please enter a valid phone number.',
                ]),
            ],
        ])
        ->add('OrderDate', DateType::class, [
            'widget' => 'single_text',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Order date is required.',
                ])
                // new Assert\Date([
                //     'message' => 'Please enter a valid date.',
                // ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
