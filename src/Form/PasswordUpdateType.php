<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this -> getConfiguration('Ancien Mot de passe', 'Mot de passe actuel'))
            ->add('newPassword', PasswordType::class, $this -> getConfiguration('Nouveau Mot de passe', 'password ...'))
            ->add('confirmPassword', PasswordType::class, $this -> getConfiguration('Confirmation', 'password Confirm...'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
