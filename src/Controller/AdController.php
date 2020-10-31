<?php

namespace App\Controller;
use App\Entity\Ad;
use App\Controller;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdController extends AbstractController
{
    /**
     * Permet d'afficher toutes les annonces
     * @Route("/ads", name="ads_index")
     * ON lui passes un tableau avec toutes les annonces qu'on envoie a index.html.twig
     * RESUMER: 1. en écrivant 127.0.0.1:8000/ads
     *       => on appelle une certaine Route
     *       => qui appelle une certaine fonction
     *       => au sein du ad controller
     *       => il va lui même instancier AdController
     *       => il va lui même appeler la fonction index
     * jamais controller = new controller();
     * 
     * ps: la fonction index a besoin d'une instance Adrepository appeler $repo
     */
    public function index(AdRepository $repo)
    {
       // $repo=$this->getDoctrine()->getRepository(Ad::class);
        $ads=$repo->findAll();        
        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

  /**
     * Creer une annonce
     * Etre connecter pour créer une annonce
     * 
     *@Route("/ads/new",name="ads_create")
    * @IsGranted("ROLE_USER")
     * @return Response
     * le composant de sécurité nous envoie automatiquement vers la connexion
     */
    public function create(Request $request){

        $ad = new Ad();     

        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){//si ya des données && submit : enregistre affiche twig
            $manager=$this->getDoctrine()->getManager();
            //le formulaire AnnonceType rattache automatiquement les images a $ad
            //on doit persister les images rattacher a ad avant de persister ad SINON ERROR
            foreach($ad->getImages() as $image){

               $image->setAd($ad);
               $manager->persist($image);               
            }          

           $user = $this->getUser();
           $ad->setAuthor($user);

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',
            " La page <strong>{$ad->getTitle()}</strong> à bien été enregistrer.");       

            return $this->redirectToRoute("ads_show", [
                'slug' => $ad->getSlug()
            ]);
         }
        return $this->render('ad/new.html.twig',[ //si pas de données: créer un formulaire et envoie le a twig
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet d'afficher une seule annonce
     *
     *@Route("/ads/{slug}",name="ads_show")

     *quand sur la page ads on clique sur une image on lui envoie le lien du slug du repo de limage choisis
     *ici on récupere le slug envoyer de lannonce selectionner et on laffiche
     * @return Response ;
     */
    public function show(Ad $ad){
       
        //$ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig' , [
            'ad' => $ad
        ] );
    }

    /**
     * Permet d'afficher un formulaire pour éditer les annonces
     * Seule une personnne connecter et author de l'annonce peut modifier
     * 
     *@Route( "/ads/{slug}/edit" , name="ads_edit")
     *@Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier.")
     * @return void
     */
    public function editFormulaire(Ad $ad, Request $request){

       
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){//si ya des données && submit : enregistre affiche twig
            $manager=$this->getDoctrine()->getManager();
            //le formulaire AnnonceType rattache automatiquement les images a $ad
            //on doit persister les images rattacher a ad avant de persister ad SINON ERROR
            foreach($ad->getImages() as $image){

               $image->setAd($ad);
               $manager->persist($image);               
            }
            dump($ad->getImages());
            dump($ad);
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',
            " La page <strong>{$ad->getTitle()}</strong> à bien été modifier.");       

            return $this->redirectToRoute("ads_show", [
                'slug' => $ad->getSlug(),
                
            ]);
         }

        return $this->render('ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * 
     *@Route("/ads/{slug}/delete",name="ads_delete")
     *@Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="Vous n'avez pas le droit d'accéder à cette ressource")
     * @return void
     */
    public function adDelete(Ad $ad,EntityManagerInterface $manager){

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success',"l'annonce <strong> {$ad->getTitle()} </strong> à bien été supprimer.");
        return $this->redirectToRoute("ads_index");
       
    }

  
}
