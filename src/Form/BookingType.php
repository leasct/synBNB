<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    /**
     * 
     * transformer si Called before submit -> transformer le string en datetime
     *             si Called initialized valued -> transformer le datetime en string
     * 
     */
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer=$transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',TextType::class,$this->getConfigurationFormulaire(
                "Date d'arrivée","La date à laquelle vous comptez arriver."))
            ->add('endDate',TextType::class,$this->getConfigurationFormulaire(
                "Date de départ","La date à laquelle vous quittez les lieux."))
            ->add('comment',TextareaType::class,$this->getConfigurationFormulaire(
                false,"Si vous avez un commentaire, n'hésitez pas à en faire part !",['required' => false]));
        ;
        //called le transformer sur les champs dates pour modifier la valeur quand issubmited ou initalized value
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
