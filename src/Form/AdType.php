<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Titre de l'annonce"))
            ->add('slug', TextType::class, $this->getConfiguration("Adresse Web", "Adresse web (automatique)",[
                'required' => false
            ]))
            ->add('coverImage', UrlType::class, $this->getConfiguration("Image", "Url de votre image"))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Introduction de votre bien"))
            ->add('content', TextareaType::class, $this->getConfiguration("Description ", "Description de votre bien"))
            ->add('price', MoneyType::class, $this->getConfiguration("Prix par nuit", "Prix pour une nuit"))
            ->add('rooms', IntegerType::class, $this->getConfiguration("Nombres de chambres", "Nombres de chambres disponible"))
            ->add('images', CollectionType::class,[
                'entry_type' =>ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
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