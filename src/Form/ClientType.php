<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\D+$/',
                        'message' => 'Le nom ne peut pas contenir de chiffres.'
                    ])
                ]
            ])
            ->add('prenom', null, [
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\D+$/',
                        'message' => 'Le prénom ne peut pas contenir de chiffres.'
                    ])
                ]
            ])
            ->add('email', null, [
                'constraints' => [
                    new NotBlank(),
                    new Email(['message' => 'L\'email "{{ value }}" n\'est pas valide.'])
                ]
            ])
            ->add('motdepasse', null, [
                // No constraints needed for password field, usually marked as 'password' in the form
            ])
            ->add('telephone', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le numéro de téléphone doit comporter exactement 8 chiffres.'
                    ])
                ]
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}