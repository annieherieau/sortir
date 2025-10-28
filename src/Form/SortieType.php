<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $minDateTime = (new \DateTime())->modify('+1 hour')->format("Y-m-d H:i");
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('startingDate', DateTimeType::class, [
                'label' => 'Date et Heure de la sortie',
                'widget' => 'single_text',
                'attr' => ['min' => $minDateTime],
            ])
            ->add('registerLimitDate', DateTimeType::class, [
                'label' => "Date limite d'inscription",
                'widget' => 'single_text',
                'attr' => ['min' => $minDateTime],
            ])
            ->add('maxRegistrationNumber', IntegerType::class,
                ['label' => 'Nombre de places',
                    'attr' => ['min' => 1,],
                ])
            ->add('durationInMunites', IntegerType::class,
                ['label' => 'Durée (en minutes)',
                    'attr' => ['min' => 5, 'step' => '5'],
                    'mapped' => false])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'name',
                'disabled' => true,
            ])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu',
                'class' => Lieu::class,
                'choice_label' => 'name',
                'placeholder' => '--Choisir un lieu--',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('l')
                        ->join('l.ville', 'v')
                        ->orderBy('l.name', 'ASC');
                },
            ])
            ->add('description', TextareaType::class,
                ['label' => 'Description et informations',
                    'attr' => ['rows' => 5],
                    'required' => false])

            // Champs cachés
            ->add('owner', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'pseudo',
                'attr' => ['hidden' => true],
                'label_attr' => ['hidden' => true],
            ])
            ->add('state', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'libelle',
                'attr' => ['hidden' => true],
                'label_attr' => ['hidden' => true],
            ])
            ->add('publier', CheckboxType::class, [
                'required' => false,
                'attr' => [
                   'checked' => false,
                   'hidden' => true,
                   ],
                'label_attr' => ['hidden' => true],
                "mapped" => false,
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }

}
