<?php

namespace App\Controller;

use App\Entity\Post;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LandingController extends AbstractController
{
    /**
     * @Route("/", name="landing")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Post::class)->BuscarTodosLosPosts();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)

        );
        return $this->render('landing/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
