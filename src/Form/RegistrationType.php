<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('firstName',TextType::class,$this->getConfigurationFormulaire('Prénom',"Votre prénom ..."))
            ->add('lastName',TextType::class,$this->getConfigurationFormulaire("Nom",'Votre nom de famille ...'))
            ->add('email',EmailType::class,$this->getConfigurationFormulaire("Email","Votre adresse email"))
            ->add('picture',UrlType::class,$this->getConfigurationFormulaire("Photo de profil",'URL de votre avatar...',[
                'required' => false
            ]))
            ->add('hash',PasswordType::class,$this->getConfigurationFormulaire('Mot de passe','Choisisser un bon mot de passe'))
            ->add('passwordConfirme',PasswordType::class,$this->getConfigurationFormulaire('Confirmation de mot de passe:','Veuillez confirmer votre mot de passe'))
            ->add('introduction',TextType::class,$this->getConfigurationFormulaire("Introduction","Présentez vous en quelques mots..."))
            ->add('description',TextareaType::class,$this->getConfigurationFormulaire('Description détaillée',"C'est le moment de vous présenter en détails !"))
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
