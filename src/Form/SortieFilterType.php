<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Utils\SortiesFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SortieFilterType extends AbstractType
{
    // TODO ajouter filtre par campus
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortieName', TextType::class, [
                'label' => 'Nom partiel ou complet de la sortie',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('minStartDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Entre ',
                'empty_data' => '01/01/1970',
                'required' => false,
            ])
            ->add('maxStartDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'et ',
                'empty_data' => '01/01/3000',
                'required' => false,
            ])
            ->add('isOwner', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
            ])
            ->add('isRegisteredUser', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
            ])
            ->add('isNotRegisteredUser', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('isFinishedSortie', CheckboxType::class, [
                'label' => 'Sorties terminées',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SortiesFilter::class,
        ]);
    }

}
