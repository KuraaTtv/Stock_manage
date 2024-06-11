<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\CardScheme;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('PaymentMethode', ChoiceType::class, [
            'choices'  => [
                'Credit Card' => 'credit_card',
                'Debit Card' => 'debit_card',
                'Payonner' => 'Payonner',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])
        // ->add('PaymentDate', null, [
        //     'widget' => 'single_text',
        //     'constraints' => [
        //         new NotBlank(),
        //     ],
        // ])
        ->add('PaymentAmount', null, [ // Remove 'widget' option from here
            'constraints' => [
                new NotBlank(),
            ],
        ])
        ->add('CardOwner', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ])
        ->add('CardNumber', TextType::class, [
            'constraints' => [
                new NotBlank(),
                // new CardScheme([
                //     'schemes' => ['VISA', 'MASTERCARD']
                // ]),
            ],
        ])
        ->add('Cvv', TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 3, 'max' => 4]), // Ensure the length matches typical CVV lengths
            ],
        ])
        ->add('ExpirationDate', DateType::class, [
            'constraints' => [
                new NotBlank(),
                // new Regex([
                //     'pattern' => '/^(0[1-9]|1[0-2])\/?([0-9]{4}|[0-9]{2})$/',
                //     'message' => 'The expiration date must be in the format MM/YY or MM/YYYY.'
                // ])
            ],
        ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
