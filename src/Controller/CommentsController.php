<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comment;
use App\Entity\BlogPost;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;


class CommentsController extends AbstractController
{
    #[Route('/add-comment/{post}', name: 'submit_comment')]
    public function create(ManagerRegistry $reg, BlogPost $post, Request $req) :Response {

        
        $entityMgr = $reg->getManager();
        $comment = new Comment();

        $submitCommentForm = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('submit_comment', ['post' => $post->getId()])
        ]);
        $submitCommentForm->handleRequest($req);
        if($submitCommentForm->isSubmitted() && $submitCommentForm->isValid()) {
            
            $comment = $submitCommentForm->getData();
            $comment->setAuthor($this->getUser());
            $comment->setArticle($post);
            $comment->setLikes(0);
            $comment->setDislikes(0);


            $entityMgr->persist($comment);
            $entityMgr->flush();

            return $this->redirectToRoute('articles', ['articleID' => $post->getId()]);
        }
        return new Response();
    }

    public function read(BlogPost $blogPost): Response
    {
        $comment = new Comment();
        $submitCommentForm = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('submit_comment', ['post' => $blogPost->getId()])
        ]);

        return $this->render('partial/_comments.html.twig', [
            'user' => $this->getUser(),
            'article' => $blogPost,
            'form' => $submitCommentForm,
            'avatar_dir' => $this->getParameter('app.avatar_images_dir')
        ]);
    }
}
