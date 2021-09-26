<?php

namespace App\Controller;

use App\Services\MoviesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route(path="/", name="movies_index")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'application' => [
                'name' => getenv('APP_NAME')
            ]
        ]);
    }


    /**
     * @Route(path="/movie/list", name="movies_movie_list")
     */
    public function list(
        MoviesService $moviesService
    ): Response
    {
        return $this->render('default/movieList.html.twig', [
            'application' => [
                'name' => getenv('APP_NAME')
            ],
            'items' => $moviesService->getMoviesData()
        ]);
    }
}
