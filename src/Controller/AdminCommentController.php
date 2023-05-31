<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comments_index")
     */
    public function index(CommentRepository $repository): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repository->findAll(),
        ]);
    }

    /**
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $em
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     * @return Response
     */

    public function edit(Comment $comment, Request $request, EntityManagerInterface $em) :Response
    {
        $form = $this->createForm(AdminCommentType::class,$comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success',"le commentaire de l'annonce <strong>{$comment->getAd()->getTitle()}</strong> viens d'étre modifiée !");
            $em->persist($comment);
            $em->flush();
        }
        return  $this->render("admin/comment/edit.html.twig", [
           'form'=>$form->createView(),
            'comment'=>$comment
        ]);
    }


    /**
     * @param Comment $comment
     * @param EntityManagerInterface $em
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $em): Response
    {
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success',"le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> viens d'étre supprimer !");
        return $this->redirectToRoute('admin_ads_index');
    }
}
