<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils){

        $error = $utils -> getLastAuthenticationError();
        $username = $utils -> getLastUsername();
        
        return $this->render('account/login.html.twig', [
            'hasError' => $error != null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se deconnecter
     *
     * @Route("/logout", name="account_logout")
     * @return void
     */
    public function logout(){}

    /**
     * Inscription utilisateur 
     * 
     * @Route("/register", name="account_register")
     *$=
     * @return Response
     */
    public function register (Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {


        $user = new User;

        $form = $this -> createForm(RegistrationType::class,$user);

        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid()){
        
            $user -> setHash($encoder -> encodePassword($user, $user -> getHash()));
            $manager -> persist($user);
            $manager -> flush();
            $this -> addFlash('success', '<strong>' . $user -> getFirstName() . '</strong>, votre compte à  bien été crée');
            return $this -> redirectToRoute('account_login');
        }


        return $this -> render('account/registration.html.twig' , [
            'form' => $form -> createView()
        ]);

    }


    /**
     * Gestion du profile 
     * @Route("/account/profile", name ="account_profile")
     *
     * @return void
     */
    public function profile(Request $request, EntityManagerInterface $manager) {  

        $user = $this -> getUser();

        $form = $this -> createForm(AccountType::class, $user);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $manager -> persist($user);
            $manager -> flush();
        }


        return $this -> render('account/profile.html.twig', [
            'form' => $form -> createView()
        ]);
    }

    /**
     * Modification mot de passe
     *
     * @Route("/account/update-password", name="account_password")
     * @return Response
     */
    public function updatePassword(Request $request , EntityManagerInterface $manager, UserPasswordEncoderInterface $encode){
        $user = $this -> getUser();

        $passwordUpdate = new PasswordUpdate();

        $form = $this -> createForm(PasswordUpdateType::class, $passwordUpdate);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            if(!$encode->isPasswordValid($user,$passwordUpdate ->getOldPassword())){
                $form ->get('oldPassword') ->addError(new FormError("Le mot de passe actuel est incorrect !"));
            }
            else { 
            $user->setHash($encode -> encodePassword($user,$passwordUpdate -> getNewPassword()));
            $manager -> persist($user);
            $manager -> flush();
            
            $this ->addFlash('success', 'Votre Mot de passe à bien été changé');
            return $this -> redirectToRoute('account_profile');
            }
        }

        return $this -> render('account/password.html.twig', [
            'form' => $form -> createView()
        ]);
    }
    /**
     * Permet d'afficher de le profil du User connecté
     * 
     * @Route("/account", name="account_home")
     */
    public function myAccount() {
        return $this -> render('user/index.html.twig', [
            'user' => $this -> getUser()
        ]);
    }

    /**
     * Affiche la liste des réservation faite par l'utilisateur
     * 
     * @Route("/account/bookings", name="account_bookings")
     *
     * @return void
     */
    public function bookings() {

        return $this -> render('account/bookings.html.twig');
    }
}
