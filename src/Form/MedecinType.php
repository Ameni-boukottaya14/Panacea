<?php

namespace App\Form;

use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Corrected import
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
                    new NotBlank(['message' => 'Le nom est obligatoire']), // Corrected syntax
                ],
                'attr' => [
                    'class' => 'search-form input',
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']), // Corrected syntax
                ],
                'attr' => [
                    'class' => 'search-form input',
                ]
            ])
            ->add('specialite', ChoiceType::class, [
                'choices' => [
                    'Généraliste' => 'Généraliste',
                    'Dentiste' => 'Dentiste',
                    'Cardiologue' => 'Cardiologue',
                    "Pédiatrie" => "Pédiatrie",
                    "Gynécologie" => "Gynécologie",
                    "Neurologie" => "Neurologie",
                    "Dermatologie" => "Dermatologie",
                ],
                'attr' => [
                    'class' => 'search-form input',
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(message:"L'Email est obligatoire"),
                    new Email(['message' => 'Email is not valid.']) // Corrected syntax
                ],
                'attr' => [
                    'class' => 'search-form input',
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
