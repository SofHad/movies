<?php

namespace App\Controller;

use App\Services\MoviesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route(path="/movies", name="movies_movies_list_json", methods={"GET","HEAD"}))
     *
     * @param MoviesService $moviesService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getList(
        MoviesService $moviesService
    ){
        return $this->json($moviesService->getTopRatedMovies());
    }

    /**
     * @Route(path="/movies/genres", name="movies_movie_gender_list_json", methods={"GET","HEAD"}))
     **/
    public function getGendre(
        MoviesService $moviesService
    )
    {
        return $this->json($moviesService->getMoviesGender());
    }

    /**
     * @Route(path="/movie/genre/{id}", name="movies_movie_list_by_gender_json", methods={"GET","HEAD"}))
     **/
    public function byGenre(
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
    public function getStreamData(
        int $id,
        MoviesService $moviesService
    )
    {
        return $this->json($moviesService->getSteamMovieData($id));
    }
}
