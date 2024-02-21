<?php

namespace App\Form;

use App\Entity\Abonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DateE')
            ->add('DateC')
            ->add('ClientId', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le Client ne doit pas être vide.']),
                ],
            ])
            ->add('OffreId', null, [
                'constraints' => [
                    new NotBlank(['message' => 'L Offre ne doit pas être vide.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
?>