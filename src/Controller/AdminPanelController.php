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

    #[Route('/removepost', name: 'remove_post')]
    public function removePost(Request $request, ManagerRegistry $registry) :Response
    {
        $id = $request->request->get('id');
        
        $entityMgr = $registry->getManager();
        $repository = $entityMgr->getRepository(BlogPost::class);
        $entityMgr->remove($repository->find($id));
        $entityMgr->flush();

        return $this->redirectToRoute('admin_panel');
    }
    #[Route('/post/edit', name: 'edit_post')]
    public function editPost(Request $request, ManagerRegistry $registry) :Response
    {
        $entityMgr = $registry->getManager();
        $blogPostId = $request->get('id');
        $blogPost = $entityMgr->getRepository(BlogPost::class)->find($blogPostId);
        $form = $this->createForm(BlogPostType::class, $blogPost);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityMgr->persist($blogPost);
            $entityMgr->flush();
            return $this->redirectToRoute('admin_panel');
        }
        return $this->render('forms/submit-post.html.twig', ['form' => $form]);
    }


    #[Route('/addcategory', name: 'add_category')]
    public function addCategory(Request $request, ManagerRegistry $reg) :Response
    {
        $category = new PostCategory();
        $name = $request->request->get('name');

        $category->setName($name);

        $entityMgr = $reg->getManager();
        $entityMgr->persist($category);
        $entityMgr->flush();

        return $this->redirectToRoute('admin_panel');

    }
}
