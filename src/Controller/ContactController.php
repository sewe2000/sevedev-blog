<?php

namespace App\Controller;

use App\Form\ContactType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(ManagerRegistry $reg, Request $req): Response
    {
        $entityManager = $reg->getManager();
        $contactData = [];
        $form = $this->createForm(ContactType::class, $contactData);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            $contactData = $form->getData();
            print_r($contactData);
        }
        
        return $this->render('forms/contact.html.twig', [
            'user' => $this->getUser(),
            'form' => $form
        ]);
    }
}
