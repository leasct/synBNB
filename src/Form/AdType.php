<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champs
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */ 
    private function getConfiguration($label,$placeholder){
        return[
                'label' => $label, 
                'attr' => [
                    'placeholder' => $placeholder
                ]
            ];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Titre de l'annonce"))
            ->add('slug', TextType::class, $this->getConfiguration("Adresse Web", "Adresse web (automatique"))
            ->add('coverImage', UrlType::class, $this->getConfiguration("Image", "Url de votre image"))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Introduction de votre bien"))
            ->add('content', TextareaType::class, $this->getConfiguration("Description ", "Description de votre bien"))
            ->add('price', MoneyType::class, $this->getConfiguration("Prix par nuit", "Prix pour une nuit"))
            ->add('rooms', IntegerType::class, $this->getConfiguration("Nombres de chambres", "Nombres de chambres disponible"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
