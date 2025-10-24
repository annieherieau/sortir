<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'disabled' => true, // non modifiable
            ])

            ->add('pseudo', TextType::class,['label' => 'Pseudo'])
            ->add('firstname', TextType::class,['label' => 'Prénom'])
            ->add('name', textType::class,['label' => 'Nom'])
            ->add('phoneNumber', TextType::class,
                ['label' => 'Téléphone', 'required' => false])
            ->add('email', EmailType::class,
                ['label' => 'Email'])

                // pour vérification dans le controller avant mise à jour des données
            ->add('current_password', PasswordType::class,
                ['label' => 'Mot de passe actuel', 'mapped' => false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
