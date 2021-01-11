<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings_index")
     */
    public function index(BookingRepository $repo): Response
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo -> findAll()
        ]);
    } 
    
    /**
     * Permet d'edidter une réservation
     * 
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     *
     * @param Booking $booking
     * @return void
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager){

        $form= $this -> createForm(AdminBookingType::class, $booking);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            $booking -> setAmount(0);
            $manager -> persist($booking);
            $manager -> flush();

            $this -> addFlash('success' , "La réservation <strong> N° " . $booking -> getId() ."</strong> a bien été modifiée.");
            return $this -> redirectToRoute('admin_bookings_index');

        }

        return $this -> render('admin/booking/edit.html.twig', [
           'booking' => $booking,
           'form' => $form -> createView() 
        ]);
    }

    /**
     * Supprime une réservation
     * 
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     *
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(Booking $booking , EntityManagerInterface $manager){
        $manager -> remove($booking);
        $manager -> flush();

        $this -> addFlash('success', "La réservation de : <strong>" . $booking -> getBooker() -> getFullName() . "</strong> a bien été supprimée.");
        return $this -> redirectToRoute('admin_bookings_index');
    }
}
