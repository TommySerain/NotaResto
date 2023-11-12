<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/verifyIdentity', name: 'app_user_verify', methods: ['GET', 'POST'])]
    public function verifyIdentity(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        $user = new User();
        if ($form->isSubmitted()) {
            $user = $userRepository->findBy(['email'=>$form->get('email')->getData()])[0];
            if ($user){
                $userFirstname = $user->getFirstname();
                $userLastname = $user->getLastname();
                $userAddress = $user->getAddress();
                $userCity = $user->getCity();

                $formFirstname = $form->get('firstname')->getData();
                $formLastname = $form->get('lastname')->getData();
                $formAddress = $form->get('address')->getData();
                $formCity = $form->get('city')->getData();

                if ($userFirstname === $formFirstname &&
                    $userLastname === $formLastname &&
                    $userAddress === $formAddress &&
                    $userCity === $formCity)
                {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    );
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('security/verifyIdentity.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
