<?php

namespace App\Controller;


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
    public function resetPasssword(Request $request, UserRepository $userRepository, MailerInterface $mailer)
    {
        $data = $request->query->all("filter_form");
        $form = $this->createForm(CheckIdentityType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $checkUser = $userRepository
                ->findOneBy([
                    'email' => $form->get('email')->getData(),
                ]);
            if ($checkUser === null || $checkUser->getValidation() === false) {
                $this->addFlash('failed', 'Unknow email');
                return $this->redirectToRoute('app_login');
            }
            $validation = random_int(100000, 9999999);
            $hash = md5($validation);

            $email = (new TemplatedEmail())
                ->from('smptpserveur@gmail.com')
                ->to($checkUser->getEmail())
                ->subject('Nous allons changer votre mot de passe !')
                ->htmlTemplate('emails/checkMail.html.twig')
                ->context([
                    'expiration_date' => new \DateTime('+2 days'),
                    'username' => $checkUser->getName(),
                    'hash' => $hash,
                ]);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('failed', 'Can try again later !');
                return $this->redirectToRoute('app_login');
            }
            $checkUser->setValidationToken($hash);
            $checkUser->setValidation(0);
            $checkUser->setResetDate(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($checkUser);
            $entityManager->flush();
            $this->addFlash('success', 'Check your email, a message is came or your spam in case of !');
            return $this->redirectToRoute('front_index');
        }
        return $this->render('security/checkIdentity.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/checked/{token}", name="app_checked" )
     */
    public function confirmPass($token, UserRepository $users)
    {
        $user = $users->findOneBy(['validationToken' => $token]);
        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
        $now = new \DateTime();
        $interval = $now->diff($user->getResetDate());
        $interval = (int)$interval->format('%R%a');
        if ($interval >> 2) {
            throw $this->createNotFoundException('You must ask an other email, this identifiant is not valid anymore  !');
        }
        return $this->redirectToRoute('app_reset_password', ['token' => $token]);
    }


    /**
     * @Route("/reset/{token}", name="app_reset_password" )
     */
    public function resetPass($token, UserRepository $users, Request $request, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = $request->query->all("filter_form");
        $form = $this->createForm(ResetPasswordType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $users->findOneBy(['validationToken' => $token]);
            if (!$user) {
                throw $this->createNotFoundException('Unknow user');
            }

            $pattern = "#[?,;./:§!%µ*¨^£$\¤{}()[\]\-\|`_\\@&~\#]#";

            if (!preg_match($pattern, $form->get('plainPassword')->getData())) {
                $this->addFlash('failed', 'Special characters must be used  !');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }

            $email = (new TemplatedEmail())
                ->from('smptpserveur@gmail.com')
                ->to($user->getEmail())
                ->subject('Confirmation de changement de mot de passe !')
                ->htmlTemplate('emails/confirmationResetPassword.html.twig')
                ->context([
                    'username' => $user->getName(),
                ]);

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('failed', 'Bloody fate a problem happened, can you try it again later !');
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

            $this->addFlash('success', 'Congrats your password is resetted, welcome back !');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/resetPassword.html.twig', [
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
