<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/register", name="account_register")
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $user->getHash();
            $hash = $hasher->hashPassword($user, $password);
            $user->setHash($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été crée ! Vous pouvez maintenant vous connecter !');
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Editing a profile of user
     * @Route("/account/profile",name="account_profil")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Vos modifications en bien été prise en compte !');
        }
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("//account/password-update", name="account_update_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);
                $user->setHash($hash);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été modifier ');
                return $this->redirectToRoute('homepage');
            }

        }
        return $this->render('account/password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount():Response
    {
        return $this->render('user/index.html.twig',[
            'user'=>$this->getUser()
        ]);
    }
}
