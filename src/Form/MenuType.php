<?php

namespace App\Form;

use App\Entity\Burger;
use App\Entity\Complement;
use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du menu',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Menu Classic'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'XOF',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('burgers', EntityType::class, [
                'class' => Burger::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Burgers inclus',
                'attr' => ['class' => 'form-select', 'size' => 5],
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('b')
                        ->where('b.isActive = true')
                        ->orderBy('b.nom', 'ASC');
                },
            ])
            ->add('complements', EntityType::class, [
                'class' => Complement::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Compléments inclus',
                'attr' => ['class' => 'form-select', 'size' => 5],
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('c')
                        ->where('c.isActive = true')
                        ->orderBy('c.nom', 'ASC');
                },
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide',
                    ])
                ],
                'attr' => ['class' => 'form-control', 'accept' => 'image/*'],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
