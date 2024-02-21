<?php

namespace App\Form;

use App\Entity\Pharmacie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PharmacieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom ne doit pas être vide.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\']+$/',
                        'message' => 'Le nom ne doit contenir que des lettres.'
                    ]),
                ],
            ])
            ->add('adress', null, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse ne doit pas être vide.']),
                ],
            ])
            ->add('numTell', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone ne doit pas être vide.']),
                    new Regex([
                        'pattern' => '/^(5|2|9)\d{7}$/',
                        'message' => 'Le numéro de téléphone doit commencer par 5, 2 ou 9 et être constitué de 8 chiffres.'
                    ]),
                ],
            ])
            ->add('adressEmail', null, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse email ne doit pas être vide.']),
                    new Email(['message' => 'L\'adresse email n\'est pas valide.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pharmacie::class,
        ]);
    }
}
