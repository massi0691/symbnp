<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $title = "Massinissa";
        $prenoms = ["Lior" => 31, "Joseph" => 50, "Anne" => 15];
        return $this->render('home/index.html.twig', [
            'title' => $title,
            'age' => 8,
            'prenoms' => $prenoms
        ]);
    }

    /**
     * @Route("/bonjour/{prenom}/age/{age}", name="bonjour_premom_age")
     * @Route("/bonjour/{prenom}", name="bonjour_prenom")
     * @Route("/salut", name="bonjour")
     * Montre la page qui dit bonjour
     * @return void
     */
    public function hello($prenom ='anonyme', $age=0)
    {
        return $this->render('home/hello.html.twig',[
            'prenom'=>$prenom,
            'age'=>$age
        ]);
    }

}
