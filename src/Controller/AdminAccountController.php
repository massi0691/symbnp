<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('admin/account/login.html.twig',[
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * @Route("/admin/logout", name="account_admin_logout")
     */
    public function logout(){}

    /**
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $em):Response
    {
        $form = $this->createForm(AdType::class,$ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($ad);
            $em->flush();
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> à bien été enregitrer ! ");
        }
        return $this->render('admin/ad/edit.html.twig',[
           'form'=>$form->createView(),
            'ad'=>$ad
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     */
    public function delete(Ad $ad, EntityManagerInterface $em):Response
    {
             if (count($ad->getBookings()) > 0){
                 $this->addFlash('warning', "L'annonce <strong>{$ad->getTitle()}</strong> à déja des réservations ne peut pas étre supprimer ! ");
             }
             else{
                 $em->remove($ad);
                 $em->flush();
                 $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> à bien été supprimer ! ");
             }

        return $this->redirectToRoute('admin_ads_index');
    }

}
