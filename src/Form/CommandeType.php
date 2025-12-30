<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Zone;
use App\Entity\Livreur;
use App\Repository\ZoneRepository;
use App\Repository\LivreurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('clientNom', TextType::class, [
                'label' => 'Nom du client',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom complet du client'
                ]
            ])
            ->add('clientTelephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '+221 XX XXX XX XX'
                ]
            ])
            ->add('adresseLivraison', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Adresse complète de livraison'
                ]
            ])
            ->add('zone', EntityType::class, [
                'class' => Zone::class,
                'choice_label' => function(Zone $zone) {
                    return $zone->getNom() . ' (' . number_format($zone->getFraisLivraison(), 0, ',', ' ') . ' FCFA)';
                },
                'query_builder' => function(ZoneRepository $repo) {
                    return $repo->createQueryBuilder('z')
                        ->where('z.isActive = true')
                        ->orderBy('z.nom', 'ASC');
                },
                'label' => 'Zone de livraison',
                'placeholder' => 'Sélectionnez une zone',
                'attr' => ['class' => 'form-select']
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'Instructions spéciales (optionnel)'
                ]
            ])
        ;
        
        if ($options['include_etat']) {
            $builder->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'En attente' => Commande::ETAT_EN_ATTENTE,
                    'En préparation' => Commande::ETAT_EN_PREPARATION,
                    'Prête' => Commande::ETAT_PRETE,
                    'En livraison' => Commande::ETAT_EN_LIVRAISON,
                    'Livrée' => Commande::ETAT_LIVREE,
                    'Annulée' => Commande::ETAT_ANNULEE,
                ],
                'attr' => ['class' => 'form-select']
            ]);
        }
        
        if ($options['include_livreur']) {
            $builder->add('livreur', EntityType::class, [
                'class' => Livreur::class,
                'choice_label' => function(Livreur $livreur) {
                    return $livreur->getPrenom() . ' ' . $livreur->getNom() . ' - ' . ucfirst($livreur->getStatut());
                },
                'query_builder' => function(LivreurRepository $repo) {
                    return $repo->createQueryBuilder('l')
                        ->where('l.isActive = true')
                        ->orderBy('l.nom', 'ASC');
                },
                'label' => 'Livreur',
                'required' => false,
                'placeholder' => 'Sélectionnez un livreur',
                'attr' => ['class' => 'form-select']
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'include_etat' => false,
            'include_livreur' => false,
        ]);
    }
}
