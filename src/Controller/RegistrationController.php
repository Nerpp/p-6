<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class RegistrationController extends AbstractController
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(MailerInterface $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $userCheck = $this->security->getUser();

        if ($userCheck) {
            return $this->redirectToRoute('front_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = $form->get('email')->getData();
            $pattern = "#[?,;./:§!%µ*¨^£$\¤{}()[\]\-\|`_\\@&~\#]#";

            if (!preg_match($pattern, $form->get('plainPassword')->getData())) {
                $this->addFlash('failed', 'Special characters must be used  !');
                return $this->redirectToRoute('app_register');
            }

            if (preg_match($pattern, $form->get('name')->getData())) {
                $this->addFlash('failed', 'Special characters must not be used  !');
                return $this->redirectToRoute('app_register');
            }

            if (preg_match($pattern, $form->get('surname')->getData())) {
                $this->addFlash('failed', 'Special characters must not be used  !');
                return $this->redirectToRoute('app_register');
            }

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $imageFile = $form->get('image')->getData();
            
            $image = new Images();
           
            if ($imageFile) {
              
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('failed', 'Bloody fate, a problem happened, can you try again later !');
                    return $this->redirectToRoute('app_register');
                }
                $image->setSource($newFilename);
            }else{
                $image->setSource('defaultAvatar');
            }

            if (!$imageFile){
                $image->setSource('defaultAvatar.jpg');
            }

            $user->setImages($image);
            $validation = random_int(100000, 9999999);
            $hash = md5($validation);
            $user->setValidationToken($hash);

            $email = (new TemplatedEmail())
                ->from('smptpserveur@gmail.com')
                ->to($mail)
                ->subject('Thanks for signing up!')
                ->htmlTemplate('emails/signup.html.twig')
                ->context([
                    'expiration_date' => new \DateTime('+2 days'),
                    'username' => $form->get('name')->getData(),
                    'hash' => $hash,
                ]);

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('failed', 'Bloody fate, a problem happened, can you try again later !');
                return $this->redirectToRoute('app_register');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Congrat an user is born, would you check your email for confirm it !');
            return $this->redirectToRoute('front_index');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


     /**
     * @Route("/confirmation/{token}", name="app_confirmation" )
     */
    public function confirmPass($token, UserRepository $users):Response
    {
        $user = $users->findOneBy(['validationToken' => $token]);
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Unknow user');
        }

        $now = new \DateTime();
        $interval = $now->diff($user->getCreationDate());
        $interval = (int)$interval->format('%R%a');

        if ($interval >> 2) {
            throw $this->createNotFoundException('Bloody fate, the confirmation is out of concern, can you try again !');
        }

        $user->setValidationToken(null);
        $user->setValidation(1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Congrats that was the last step, Welcome among us  !');
        return $this->redirectToRoute('app_login');
    }
}
