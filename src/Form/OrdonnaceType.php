<?php

namespace App\Form;

use App\Entity\Ordonnance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class OrdonnanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomMalade', TextType::class, [
                'label' => 'Nom du malade',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du malade ne doit pas être vide.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\']+$/',
                        'message' => 'Le nom du malade ne doit contenir que des lettres.'
                    ]),
                ],
            ])
            ->add('date', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La date ne doit pas être vide.']),
                ],
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Servis' => 'Servis',
                    'En attente' => 'En attente',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'état ne doit pas être vide.']),
                ],
            ])
            ->add('pharmacie', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La pharmacie ne doit pas être vide.']),
                ],
            ])
            ->add('medicaments', TextType::class, [
                'label' => 'Médicaments',
                'constraints' => [
                    new NotBlank(['message' => 'La liste des médicaments ne doit pas être vide.']),
                ],
            ])
            ->add('prenomMalade', TextType::class, [
                'label' => 'Prénom du malade',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom du malade ne doit pas être vide.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\']+$/',
                        'message' => 'Le prénom du malade ne doit contenir que des lettres.'
                    ]),
                ],
            ])
            ->add('medecinTraitant', TextType::class, [
                'label' => 'Médecin traitant',
                'constraints' => [
                    new NotBlank(['message' => 'Le médecin traitant ne doit pas être vide.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ordonnance::class,
        ]);
    }
}
