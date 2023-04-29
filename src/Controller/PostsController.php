<?php
namespace App\Controller;

use App\Form\BlogPostType;
use Exception;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\BlogPost;
use App\Entity\PostCategory;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostsController extends AbstractController
{
    #[Route('/posts/best', name: 'top5')]
    public function getTop5Posts(ManagerRegistry $reg): Response
    {
        $entityMgr = $reg->getManager();

        $categories = $entityMgr->getRepository(PostCategory::class)->getAllCategoriesNames();
        foreach($categories as $key => $category)
        {
            $categories[$category] = $entityMgr->getRepository(BlogPost::class)->findTop5OfCategory($category);
            unset($categories[$key]);
        }

        return $this->render('best-posts.twig', ['categories' => $categories, 'user' => $this->getUser()]);
    }
    #[Route('/posts/remove', name: 'remove_post')]
    public function removePost(Request $request, ManagerRegistry $registry) :Response
    {
        $id = $request->request->get('id');
        echo $id;
        $entityMgr = $registry->getManager();
        $repository = $entityMgr->getRepository(BlogPost::class);

        $postToBeRemoved = $repository->find($id);
        if(!$postToBeRemoved)
            throw new Exception("Couldn't find the post of id equal $id to be removed!");

        $entityMgr->remove($postToBeRemoved);
        $entityMgr->flush();

        return $this->redirectToRoute('admin_panel');
    }
    #[Route('/posts/add', name: 'submit_post')]
    public function addPost(Request $request, ManagerRegistry $registryMgr, SluggerInterface $slugger) : Response
    {
        $post = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $post);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();

            $imageFile = $form->get('image')->getData();
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $path = $this->getParameter('app.blogpost_images_dir')
                    or throw new Exception("Please set the 'app.blogpost_images_dir' attribute in security.yml");
                $imageFile->move(
                    $path,
                    $newFilename
                );

                $path .= '/'.$newFilename;
                $imagine = new Imagine();
                $image = $imagine->open($path);
                $image->resize(new Box(900,600));
                $image->save($path);

            } catch(FileException $e) {
                return new Response('Problem z wysłaniem pliku na serwer. Błąd o kodzie:'.$e->getCode());
            }

            $post->setImage($newFilename);
            $post->setDate(new DateTime());

            $entityMgr = $registryMgr->getManager();
            $entityMgr->persist($post);
            $entityMgr->flush();

            return $this->redirectToRoute('admin_panel');

        }
        return $this->render('forms/submit-post.html.twig', ['form' => $form]);
    }
    #[Route('/posts/edit', name: 'edit_post')]
    public function editPost(Request $request, ManagerRegistry $registry) :Response
    {
        $entityMgr = $registry->getManager();
        $blogPostId = $request->query->get('id');
        $blogPost = $entityMgr->getRepository(BlogPost::class)->find($blogPostId)
            or throw new Exception("Cannot edit post of id equal to $blogPostId, because it couldn't be found!");
        $form = $this->createForm(BlogPostType::class, $blogPost);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityMgr->persist($blogPost);
            $entityMgr->flush();
            return $this->redirectToRoute('admin_panel');
        }
        return $this->render('forms/submit-post.html.twig', ['form' => $form]);
    }

    #[Route('/posts/{postId}', name: 'posts')]
    public function getPost(string $postId, ManagerRegistry $reg) : Response
    {
        $entityMgr = $reg->getManager();
        $post = $entityMgr->find(BlogPost::class, $postId);

        if(!$post) {
            throw $this->createNotFoundException('Nie ma takiego artykułu!');
        }

        return $this->render('post.html.twig', ['article' => $post,
                                                   'user' => $this->getUser(),
        ]);
    }

}
