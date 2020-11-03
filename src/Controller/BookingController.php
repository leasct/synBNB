<?php

namespace App\Controller;

use DateTime;
use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
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
     * 
     * @param Booking $booking
     * @param Resquest $request
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

                    $manager->persist($comment);
                    $manager->flush();

                    $this->addFlash(
                        'success', "Votre commentraire a bien été pris en compte!"
                    );
        }


        return $this->render('booking/show.html.twig',[
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
