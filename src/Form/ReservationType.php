<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Description', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description ne peut pas être vide',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])->add('Etat', ChoiceType::class, [
                'choices' => [
                    'grave' => 'grave',
                    'normale' => 'normale',
                ],
                'placeholder' => 'Choisir un état',
                'constraints' => [
                    new Choice([
                        'choices' => ['grave', 'normale'],
                        'message' => 'Veuillez sélectionner un état valide.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
