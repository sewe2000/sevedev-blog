<?php
namespace App\Controller;

use App\Form\BlogPostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\BlogPost;
use App\Entity\PostCategory;
use Symfony\Component\HttpFoundation\Request;

class AdminPanelController extends AbstractController
{
    #[Route('/admin', name: 'admin_panel')]
    public function admin(ManagerRegistry $reg) : Response
    {
        $posts = $reg->getRepository(BlogPost::class)->findAll();
        
        return $this->render('admin-panel.html.twig', [
            'user' => $this->getUser(),
            'posts' => $posts
        ]);
    }
}
