<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
// use Webmozart\Assert\Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Name should not be blank.']),
            ],
        ])
        ->add('price', MoneyType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Price should not be blank.']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'The price should be a numeric value.',
                ]),
                new Assert\GreaterThanOrEqual([
                    'value' => 0,
                    'message' => 'The price should be equal or greater than zero.',
                ]),
            ],
        ])
        ->add('stockQu', IntegerType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Stock quantity should not be blank.']),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'Stock quantity should be an integer.',
                ]),
                new Assert\GreaterThanOrEqual([
                    'value' => 0,
                    'message' => 'Stock quantity should be equal or greater than zero.',
                ]),
            ],
        ])
        ->add('picture', FileType::class, [
            'label' => 'Product picture',
            'constraints' => [
                new Assert\File([
                    'maxSize' => '2M',
                    'mimeTypes' => ['image/jpeg', 'image/png'],
                    'mimeTypesMessage' => 'Please upload a valid image (JPEG or PNG).',
                ]),
            ],
            'data_class' => null
        ])
        ->add('description', TextareaType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Description should not be blank.']),
            ],
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'constraints' => [
                new Assert\NotNull(['message' => 'Category should not be null.']),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
