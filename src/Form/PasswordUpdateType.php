<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword',PasswordType::class,$this->getConfiguration('Ancien mot de passe','Donnez votre ancien mot de passe') )
            ->add('newPassword',PasswordType::class,$this->getConfiguration('Nouveau mot de passe','Créer votre Nouveau mot de passe') )
            ->add('confirmPassword',PasswordType::class,$this->getConfiguration('Confirmer votre nouveau mot de passe','Veuillez confirmer  votre Nouveau mot de passe') )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
