<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\BlogPost;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_page')]
    public function home(ManagerRegistry $reg) : Response
    {
        $user = $this->getUser();
        $entityManager = $reg->getManager();
        $repository = $entityManager->getRepository(BlogPost::class);
        $posts = $repository->findBy([], ['date' => 'DESC'], 5);
        

        return $this->render('home.html.twig', ['user' => $user,
                                                'posts' => $posts,
                                                'title' => 'Blog Seweryna - sevedev',
                                                'heading' => 'Nowe artykuły',
        ]);
    }

}
