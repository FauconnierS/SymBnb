<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class AdType extends AbstractType
{

    private function getConfiguration($label, $placeholder, $options = []) {

        return array_merge([
            'label' => $label, 
            'attr' => [
                'placeholder' => $placeholder
            ]
            ], $options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, $this->getConfiguration("Titre", "Villa T5 au Sud de Marseille..."))
            ->add('slug',TextType::class, $this -> getConfiguration("Chaine URL", "Automatique",['required' => false]))
            ->add('coverImage',UrlType::class, $this -> getConfiguration("Image Principal", "http://placehold.it/350x540..."))
            ->add('introduction',TextType::class, $this -> getConfiguration("Introduction", "Description globale de l'annonce"))
            ->add('content',TextareaType::class, $this -> getConfiguration("Description", "Detail de l'annonce"))
            ->add('rooms',IntegerType::class, $this -> getConfiguration("Chambres", "4,5,2..."))
            ->add('price',MoneyType::class, $this -> getConfiguration("Prix / nuit" , "457,59,125...")) 
            ->add('images',CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                "allow_delete" => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
