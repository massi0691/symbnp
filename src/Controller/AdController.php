<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo): Response
    {

        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * For Ad creation
     * @Route("/ads/new", name="ads_create")
     * @return Response
     */

    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $ad = new Ad();
        $ad->setAuthor($this->getUser());
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad ->getImages() as $image ){
                $image->setAd($ad);
                $em->persist($image);
            }
            $em->persist($ad);
            $em->flush();
            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrer ! "
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }
        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * display edit form
     * @Route("/ads/{slug}/edit",name="ads_edit")
     * @return Response
     */
    public function edit($slug, AdRepository $adRepository, Request $request, EntityManagerInterface $em ):Response
    {
        $ad = $adRepository->findOneBy(['slug'=>$slug]);
        $form= $this->createForm(AdType::class,$ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad ->getImages() as $image ){
                $image->setAd($ad);
                $em->persist($image);
            }
            $em->persist($ad);
            $em->flush();
            $this->addFlash(
                'success',
                "les modifications de l'annonce<strong>{$ad->getTitle()}</strong> a bien été enregistrées ! "
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

      return $this->render('ad/edit.html.twig',[
          'form'=>$form->createView(),
          'ad'=>$ad
      ]);
    }

    /**
     * to show one ad
     * @Route("/ads/{slug}", name="ads_show")
     * @return Response
     */
    public function show($slug, AdRepository $repo): Response
    {
        // fetch add corresponding to the slug
        $ad = $repo->findOneBy(['slug' => $slug]);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }


}
