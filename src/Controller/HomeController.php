<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController{

    /**
     * Affiche page d'accueil
     * @Route("/",name="homepage")
     * 
     */
    public function Home(){
        return $this->render('home.html.twig');
    }
}

?>