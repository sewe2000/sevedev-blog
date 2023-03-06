<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\BlogPost;
use App\Entity\PostCategory;

class NavbarController extends AbstractController
{
    public function navbar(ManagerRegistry $reg): Response
    {
        $entityManager = $reg->getManager();

        $categories = $entityManager->getRepository(PostCategory::class)->getAllCategoriesNames();
        foreach($categories as $key => $category)
        {
            $categories[$category] = $entityManager->getRepository(BlogPost::class)->findTop5OfCategory($category);
            unset($categories[$key]);
        }

        return $this->render('partial/_navbar.html.twig', [
            'categories' => $categories,
            'user' => $this->getUser()
        ]);
    }
}
