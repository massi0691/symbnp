<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+?>}", name="admin_ads_index")
     */
    public function index(PaginationService $pagination, $page = 1): Response
    {
        $pagination->setEntityClass(Ad::class)
            ->setCurrentPage($page);
        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ad);
            $em->flush();
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> à bien été enregitrer ! ");
        }
        return $this->render('admin/ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     */
    public function delete(Ad $ad, EntityManagerInterface $em): Response
    {
        if (count($ad->getBookings()) > 0) {
            $this->addFlash('warning', "L'annonce <strong>{$ad->getTitle()}</strong> à déja des réservations ne peut pas étre supprimer ! ");
        } else {
            $em->remove($ad);
            $em->flush();
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> à bien été supprimer ! ");
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
