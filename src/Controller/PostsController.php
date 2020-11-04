<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PostsController extends AbstractController
{
    /**
     * @Route("/registrar-post", name="RegistrarPost")
     */
    public function index(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('imagen')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = iconv('UTF-8', 'ASCII//TRANSLIT', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('¡Ups...Al parecer algo salio mal!');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setImagen($newFilename);
            }
            $user = $this->getUser();
            $post->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('landing');
        }
        return $this->render('posts/index.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/post/{id}", name="Verpost")
     */
    public function VerPost($id){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        return $this->render('posts/verPost.html.twig', ['post' =>$post]);
    }

    /**
     * @Route("/misPosts", name="MisPosts")
     */
    public function MisPosts(){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $posts = $em->getRepository(Post::class)->findBy(['user'=>$user]);
        return $this->render('posts/misPosts.html.twig', ['posts' =>$posts]);
    }



}
