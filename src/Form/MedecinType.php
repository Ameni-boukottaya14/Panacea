<?php

namespace App\Form;

use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedecinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                ],
                'attr' => [
                    'class' => 'styled-form-field',
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']),
                ],
                'attr' => [
                    'class' => 'styled-form-field',
                ]
            ])
            ->add('specialite', ChoiceType::class, [
                'choices' => [
                    'Cardiologie' => 'Cardiologie',
                    'Pédiatrie' => 'Pédiatrie',
                    'Gynécologie' => 'Gynécologie',
                    'Neurologie' => 'Neurologie',
                    'Dermatologie' => 'Dermatologie',
                ],
                'attr' => [
                    'class' => 'styled-form-field',
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                    new Email(['message' => 'Email non valide.'])
                ],
                'attr' => [
                    'class' => 'styled-form-field',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medecin::class,
        ]);
    }
}
