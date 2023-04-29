<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\SignInType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/signin', name: 'sign_in')]
    public function signin(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher) : Response
    {
        $user = new User();
        $form = $this->createForm(SignInType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $hashedPassword = $hasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home_page');
        }


        return $this->render('forms/signin.html.twig', ['form' => $form]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authUtils) : Response
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('forms/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    #[Route('/logout', name: 'logout')]
    public function logout(): void {}
}
