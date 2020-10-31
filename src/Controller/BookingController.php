<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * Permet de créer une réservation
     * 
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function createReservation(Ad $ad, Request $request,EntityManagerInterface $manager)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class,$booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           //createAt et Amount instanciez dans prePersist

            $booking->setAd($ad);
            $booking->setBooker($this->getUser());

            //si les dates ne sont pas disponibles -> message d'erreur
            if(!$booking->isBookableDates()){

                $this->addFlash('warning','Les dates que vous avez choisis ne peuvent être réserverées: elle sont déjà déjà prises.');
               
            }else{//sinon enregistrement et redirection
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute("booking_show", [ //le paremetre en GET va se placer 
                    'id' => $booking->getId(),                  //bookig/id/?withAlert
                    'withAlert' => true
                ]);
            }
               

         
        }

        return $this->render('booking/book.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Permet d'afficher une réservation
     *
     * @Route("/booking/{id}",name="booking_show")
     * @return Response
     */
    public function showReservation(Booking $booking){

        return $this->render('booking/show.html.twig',[
            'booking' => $booking
        ]);
    }
}
