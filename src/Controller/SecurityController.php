<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CheckIdentityType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('front_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/verify_identity", name="app_check_identity")
     */   
    public function resetPasssword(Request $request,UserRepository $userRepository,MailerInterface $mailer)
    {
        $data = $request->query->all("filter_form");
        $form = $this->createForm(CheckIdentityType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $checkUser = $userRepository
                ->findOneBy([
                    'email' =>$form->get('email')->getData(),
                ]);

            if ($checkUser === null) {
                $this->addFlash('failed', 'Cette adresse e-mail est inconnue');
                return $this->redirectToRoute('app_login');
            }

            $validation = random_int(100000, 9999999);
            $hash = md5($validation);
            
            $email = (new TemplatedEmail())
                ->from('smptpserveur@gmail.com')
                // ->to(new Address('ryan@example.com'))
                ->to($checkUser->getEmail())
                ->subject('Nous allons changer votre mot de passe !')

                // path of the Twig template to render
                ->htmlTemplate('emails/checkMail.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'expiration_date' => new \DateTime('+2 days'),
                    'username' => $checkUser->getName(),
                    'hash' => $hash,
                ]);

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('failed', 'Un problème est survenue veuillez réessayer ultèrieurement !');
                return $this->redirectToRoute('app_login');
            }

            $checkUser->setValidationToken($hash);
            $checkUser->setValidation(0);
            $checkUser->setResetDate(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($checkUser);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez recu un message dans vôtre boite mail, verifier votre dossier spam si vous le voyez pas !');

            return $this->redirectToRoute('front_index');
        }

        return $this->render('security/checkIdentity.html.twig',[
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/checked/{token}", name="app_checked" )
     */
    public function confirmPass($token, UserRepository $users)
    {
        $user = $users->findOneBy(['validationToken' => $token]);

        
        if(!$user){
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        $now = new \DateTime();
        $interval = $now->diff($user->getResetDate());
        $interval = (int)$interval->format('%R%a');

        if ($interval >> 2) {
            throw $this->createNotFoundException('Vous devez redemander un email afin de changer votre mot de passe !');
        }

        return $this->redirectToRoute('app_reset_password',['token'=> $token]);

    }


    /**
     * @Route("/reset/{token}", name="app_reset_password" )
     */
    public function resetPass($token, UserRepository $users,Request $request,MailerInterface $mailer,UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = $request->query->all("filter_form");
        $form = $this->createForm(ResetPasswordType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $users->findOneBy(['validationToken' => $token]);

            if(!$user){
                throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
            }

            $email = (new TemplatedEmail())
            ->from('smptpserveur@gmail.com')
            
            ->to($user->getEmail())
            ->subject('Confirmation de changement de mot de passe !')

            // path of the Twig template to render
            ->htmlTemplate('emails/confirmationResetPassword.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'username' => $user->getName(),
            ]);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('failed', 'Un problème est survenue veuillez réessayer ultèrieurement !');
            return $this->redirectToRoute('app_login');
        }

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

            $user->setValidationToken(null);
            $user->setValidation(1);
            $user->setResetDate(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez réinitialiser votre mot de passe avec succés, vous pouvez vous connecter à nouveau !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/resetPassword.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
