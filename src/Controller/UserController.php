<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}", name="user_show")
     */
    public function index($slug,UserRepository $repository): Response
    {
        $user = $repository->findOneBy(['slug'=>$slug]);
        return $this->render('user/index.html.twig',[
            'user'=>$user
        ]);
    }
}
