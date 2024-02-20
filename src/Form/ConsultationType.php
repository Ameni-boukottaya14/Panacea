<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Consultation;
use App\Entity\Medecin;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Import EntityType
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix',NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']), // Corrected syntax
                ],
            ])
            ->add('date',DateTimeType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire']), // Corrected syntax
                ],
            ])
            ->add('medecin', EntityType::class, [ // Use EntityType for related entity Medecin
                'class' => Medecin::class,
                'choice_label' => 'nom',
            ])
            ->add('client', EntityType::class, [ // Use EntityType for related entity Client
                'class' => Client::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}
