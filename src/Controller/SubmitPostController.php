<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\BlogPost;
use App\Form\BlogPostType;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SubmitPostController extends AbstractController
{
    #[Route('/submit', name: 'submit_post')]
    public function post(Request $request, ManagerRegistry $registryMgr, SluggerInterface $slugger) : Response
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
                $path = $this->getParameter('app.blogpost_images_dir');
                $imageFile->move(
                    $this->getParameter('app.blogpost_images_dir'),
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
}
