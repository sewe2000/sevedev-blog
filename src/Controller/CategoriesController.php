<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\PostCategory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/categories/add', name: 'add_category')]
    public function addCategory(Request $request, ManagerRegistry $reg) :Response
    {
        $category = new PostCategory();
        $name = $request->request->get('name');
        if(!$name || gettype($name) != 'string')
            throw new \Exception('Please provide the "name" POST variable');

        $category->setName($name);

        $entityMgr = $reg->getManager();
        $entityMgr->persist($category);
        $entityMgr->flush();

        return $this->redirectToRoute('admin_panel');
    }
    #[Route('/categories/{category}', name: 'categories')]
    public function getPostsByCategory(string $category, ManagerRegistry $reg) :Response
    {
        $posts = $reg->getManager()->getRepository(BlogPost::class)->findAllOfCategory($category);

        if(!$posts) {
            throw $this->createNotFoundException("Nie znaleziono artykułów dla kategorii '{$category}'");
        }

        return $this->render('home.html.twig', ['posts' => $posts,
            'title' => "{$category} - sevedev",
            'heading' => "Najlepsze posty z kategorii {$category}"
        ]);
    }
}
