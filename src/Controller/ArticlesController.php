<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\PostCategory;
use App\Form\CommentType;
use Doctrine\Persistence\ManagerRegistry;

class ArticlesController extends AbstractController
{

    #[Route('/articles/best', name: 'top5')]
    public function articlesTop5(ManagerRegistry $reg): Response
    {
        $entityMgr = $reg->getManager();

        $categories = $entityMgr->getRepository(PostCategory::class)->getAllCategoriesNames();
        foreach($categories as $key => $category)
        {
            $categories[$category] = $entityMgr->getRepository(BlogPost::class)->findTop5OfCategory($category);
            unset($categories[$key]);
        }

        return $this->render('best-articles.html.twig', ['categories' => $categories, 'user' => $this->getUser()]);
    }

    #[Route('/articles/{articleID}', name: 'articles')]
    public function getArticle(string $articleID, ManagerRegistry $reg) : Response
    {
        $entityMgr = $reg->getManager();
        $post = $entityMgr->find(BlogPost::class, $articleID);

        if(!$post) {
            throw $this->createNotFoundException('Nie ma takiego artykułu!');
        }

        return $this->render('article.html.twig', ['article' => $post,
                                                   'user' => $this->getUser(),
        ]);
    }


    #[Route('/categories/{category}', name: 'categories')]
    public function getArticlesByCategory(string $category, ManagerRegistry $reg) :Response
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
