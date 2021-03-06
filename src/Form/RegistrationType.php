<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Elodie, Yohan ..."))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Dupont ..."))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "yoyo@gmail.com ..."))
            ->add('avatarFile', VichImageType::class, $this->getConfiguration("Photo de Profil", "Url...", [
                'required' => false,
                'allow_delete' => false
            ]))
            ->add('hash', PasswordType::class, $this->getConfiguration("Mot de passe", "password..."))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration('Confirmation', 'Confirmation password ...'))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Presentez vous .."))
            ->add('description', TextareaType::class, $this->getConfiguration("Description", "Decrivez-vous..."));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
