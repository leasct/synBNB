<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class AnnonceType extends ApplicationType
{
    /**
     * permet d'avoir la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, $this->getConfigurationFormulaire("Titre","saisir le super titre de votre annonce"))
            ->add('slug',TextType::class, $this->getConfigurationFormulaire("Adresse Web","adresse web (automatique)",[
                'required' => false
            ]))
            ->add('coverImage',TextType::class,$this->getConfigurationFormulaire("Url de l'image principale","Donnez l'adressed d'une image qui donne vraiment envie"))           
            ->add('introduction',TextType::class,$this->getConfigurationFormulaire("Introduction","Donnez une description globale de l'annonce"))
            ->add('content', TextareaType::class,$this->getConfigurationFormulaire("Description détaillée","Tapez une construction qui donne vraiment envie de venir chez vous !"))       
            ->add('price',MoneyType::class, $this->getConfigurationFormulaire("Prix par nuit","Indiquez le prix que vous voulez pour une nuit"))
            ->add('rooms',IntegerType::class,$this->getConfigurationFormulaire("Nombre de chambres","Le nombre de chambres dipsonibles"))
            ->add('images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class, //les champs que je dois répéter
                    'allow_add' => true,
                    'allow_delete' => true
                ])
                ;//répete autant de formulaireImage => que le nbre d'images rattacher a ad
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
