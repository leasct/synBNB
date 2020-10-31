<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManager;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer la connexion
     * 
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        
        dump($error);

        return $this->render('account/login.html.twig',[
            'hasError' => $error !== null,
            'lastUserName' => $username
        ]
        );
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout",name="account_logout")
     *
     * @return  Response
     */
    public function logout(){
        //rien !!!
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     *@Route("/register",name="account_register")
     * @return Response
     */
    public function register(Request $request,ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $form=$this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);
        
    
        if($form->isSubmitted() && $form->isValid()){

            $passwordHasher = $encoder->encodePassword($user,$user->getHash());
            $user->setHash($passwordHasher);
          
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Votre compte a bien été enregistrée.");
            
            return $this->redirectToRoute('account_login');
        }
      
        return $this->render('account/registration.html.twig',[
            'form' => $form->createView()
        ]);      
    }
    /**
     * Permet d'afficher et de traiter le formulaire de modification
     * 
     *@Route("/account/profile",name="account_profile")
     *@IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updateProfile(Request $request, ObjectManager $manager){
        
       $user = $this->getUser();

       $form = $this->createForm(AccountType::class, $user);

       $form->handleRequest($request); 

       if($form->isSubmitted() && $form->isvalid()){
           
            $manager->persist($user);
            $manager->flush();
           
            $this->addFlash('success','Les données du profil ont été enregistré avec succès.');
       }
       dump($request);

        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update",name="account_password")
     *@IsGranted("ROLE_USER")
     * @return void
     */
    public function updatePassword(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, Request $request){

        $passwordUpdate = new PasswordUpdate();

        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $user = $this->getUser();

            $oldpassword = $passwordUpdate->getOldPassword();
            
            if(password_verify($oldpassword,$user->getHash())){//si oldpassword === hash

                $newpassword = $passwordUpdate->getNewPassword();

                $hash = $encoder->encodePassword($user,$newpassword);

                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success','Votre mot de passe à bien été modifié.');

                return $this->redirectToRoute('ads_index');
            }
            else{ //erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé 
                n'est pas votre mot de passe actuel."));
            }
    
        }
            return $this->render('account/password.html.twig',[
                'form' => $form->createView()
            ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account",name="account_index")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function myAccount(){

        return $this->render('user/index.html.twig',[
            'user' => $this->getUser()
        ]);
    }

    /**
     * Pemert d'afficher la liste des réservations faites par l'utilisateur
     *
     * @Route("/account/bookings",name="account_bookings")
     * 
     * @return Response
     */
    public function bookings () {
        return $this->render('account/bookings.html.twig');
    }
}
