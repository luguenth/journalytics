<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;

use App\Repository\UserRepository;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function requestLoginLink(
        NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler, 
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager,
        Request $request)    
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);
            if (!$user) {
                // create an unactivated user
                $user = new User();
                $user->setEmail($email);
                $user->setActivated(false);
                $entityManager->persist($user);
                $entityManager->flush();
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

            // create a notification based on the login link details
            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Welcome to MY WEBSITE!'
            );
            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $notifier->send($notification, $recipient);

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig', [
                'email' => $email,
            ]);
        }

        return $this->render('security/login.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - 
            it will be intercepted by the logout key on your firewall.');
    }
}
