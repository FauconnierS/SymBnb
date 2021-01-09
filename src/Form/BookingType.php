<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookingType extends ApplicationType
{
    private $transformer ; 
    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this -> transformer = $transformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    { 
        $builder
            ->add('startDate',TextType::class, $this -> getConfiguration('Arrivée', false))
            ->add('endDate',TextType::class, $this -> getConfiguration('Départ', false))
            ->add('comment',TextareaType::class, $this -> getConfiguration(false, "Commentaire de sréservation ...",['required' => false]))

        ; 

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
