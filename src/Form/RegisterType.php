<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File; 
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre nom',
                ]),
            ],
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
        ->add('motdepasse', PasswordType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre mot de passe',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('email', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre adresse email',
                ]),
                new Email([
                    'message' => 'L\'adresse email "{{ value }}" est invalide.',
                    // vous pouvez ajouter plus de contraintes ici si nécessaire
                ]),
            ],
        ])
        ->add('telephone', TextType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre mot de passe',
                ]),
                new Length([
                    'min' => 8,
                    'max' => 8,
                    'exactMessage' => 'Le numéro CIN doit contenir exactement {{ limit }} chiffres',
                    // vous pouvez ajouter plus de contraintes ici si nécessaire
                ]),
            ],
        ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class, // Utilisez le bon espace de noms ici
        ]);
    }
}