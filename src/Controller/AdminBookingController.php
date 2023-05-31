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
     * @Route("/admin/bookings", name="admin_booking_index")
     */
    public function index(BookingRepository $repository): Response
    {
        return $this->render('admin/booking/index.html.twig', [
           'bookings'=>$repository->findAll()
        ]);
    }

    /**
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $em
     * @Route("/admin/bookings/{id}/edit" ,name="admin_booking_edit")
     * @return Response
     */
    public function edit(Booking $booking,Request $request,EntityManagerInterface $em):Response
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $booking->setAmount(0);
            $em->persist($booking);
            $em->flush();
            $this->addFlash('success', "la réservation à <strong>{$booking->getId()}</strong> est bien été modifiée");
            return $this->redirectToRoute('admin_booking_index');
        };

        return $this->render('admin/booking/edit.html.twig',[
            'form'=> $form->createView(),
            'booking'=> $booking
        ]);
    }

    /**
     * @param Booking $booking
     * @param EntityManagerInterface $em
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $em):Response
    {
        $this->addFlash('success',"La réservation n° {$booking->getId()} viens d'étre supprimer !");
        $em->remove($booking);
        $em->flush();
        return $this->redirectToRoute('admin_booking_index');
    }
}
