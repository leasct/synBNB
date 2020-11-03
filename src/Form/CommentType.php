<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', IntegerType::class, $this->getConfigurationFormulaire("Note sur 5", "Veuillez indiquer votre notre de 0 à 5", [
                'attr' => [
                    'min' => 0,
                    'max'=> 5,
                    'step' => 1
                ]
            ]))
            ->add('content', TextareaType::class, $this->getConfigurationFormulaire("Votre a vie", "N'hésitez pas être précis, cle aidera les futurs voyageurs!"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    { 
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
