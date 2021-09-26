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
     * @Route(path="/authentication/token", name="movies_authentication")
     */
    public function connectToMovies(
        MoviesService $moviesService
    ): Response
    {
       $connect = $moviesService->authenticate();
       if (key_exists("success", $connect) && $connect['success']) {
           $response = $this->redirectToRoute('movies_movie_list');
       } else {
           $response = $this->redirectToRoute('movies_index');
       }

       return $response;
    }

    /**
     * @Route(path="/movie/list", name="movies_movie_list")
     */
    public function movieList(
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

    /**
     * @Route(path="/movies", name="movies_movies_list_json", methods={"GET","HEAD"}))
     *
     * @param MoviesService $moviesService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMovies(
        MoviesService $moviesService
    ){
        return $this->json($moviesService->getTopRatedMovies());
    }

    /**
     * @Route(path="/movies/genres", name="movies_movie_gender_list_json", methods={"GET","HEAD"}))
     **/
    public function getMovieGendre(
        MoviesService $moviesService
    )
    {
        return $this->json($moviesService->getMoviesGender());
    }

    /**
     * @Route(path="/movie/genre/{id}", name="movies_movie_list_by_gender_json", methods={"GET","HEAD"}))
     **/
    public function movieByGenre(
        int $id,
        MoviesService $moviesService
    )
    {
        return $this->json($moviesService->getMovieByGenre($id));
    }

    /**
     * @Route(path="/movie/{id}/streaming", name="movies_movie_stream_json", methods={"GET","HEAD"}))
     * @param int $id
     * @param MoviesService $moviesService
     */
    public function getStreamMovieData(
        int $id,
        MoviesService $moviesService
    )
    {
        return $this->json($moviesService->getSteamMovieData($id));
    }
}
