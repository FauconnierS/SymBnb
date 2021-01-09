<?php

namespace App\Controller;

use DateTime;
use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER" )
     */
    public function index(Ad $ad, Request $request, EntityManagerInterface  $manager): Response
    {
        $booking = new Booking();
        $form = $this -> createForm(BookingType::class, $booking );
        $form -> handleRequest($request);
        
        if($form -> isSubmitted() && $form -> isValid()){
            $booking -> setBooker($this -> getUser())
                     -> setAd($ad);
            if(!$booking -> isBookableDates()){
                $this -> addFlash('warning',' Désolé mais ces dates sont déja réservés.');
            }
            else {
                $manager -> persist($booking);
                $manager -> flush();
                return $this -> redirectToRoute('booking_show', ['id' => $booking-> getId(), 'success' => true]);

            }

        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form -> createView()
         ]);
    }

    /**
     * Affiche les rsevations
     * 
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking){

        return $this -> render ('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }
}
