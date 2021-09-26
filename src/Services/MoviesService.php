<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MoviesService
{
    private $apiKey;
    private $client;
    private $movies = [];
    private $gender = [];

    public function __construct(
        HttpClientInterface $client,
        string $apiKey
    ) {
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $path
     *
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getClientRequest(string $method, string $path)
    {
        return $this->client->request($method, $path);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    private function formatYoutubeUrl(string $code): string
    {
        return "https://www.youtube.com/embed/{$code}";
    }

    /**
     * @param int $genreId
     *
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMovieByGenre(int $genreId): array
    {
        $requestPath = "https://api.themoviedb.org/3/discover/movie?api_key="
            .$this->apiKey."&with_genres=".$genreId;
        $apiConnect = $this->authenticate();
        if ($apiConnect['success']) {
            $data = $this->getClientRequest('GET', $requestPath);
            if ($data->getStatusCode() === Response::HTTP_OK) {
                return json_decode($data->getContent(), true);
            }
        }
    }

    public function getMoviesData(): array
    {
        $apiConnect = $this->authenticate();

        if ($apiConnect['success']) {
            $genders = $this->getMoviesGender();
            $movies = $this->getTopRatedMovies();
            $topMovie = $this->getTopOneMovies();
        }

        return [
            "top"     => $topMovie ? $topMovie : [],
            "genders" => $genders ? $genders : [],
            "movies"  => $movies ? $movies : [],
        ];
    }

    public function getTopRatedMovies()
    {
        $requestPath = "https://api.themoviedb.org/3/movie/top_rated?api_key="
            .$this->apiKey."&language=en-US";
        /** @var ResponseInterface $data */
        $data = $this->getClientRequest('GET', $requestPath);
        if ($data->getStatusCode() === Response::HTTP_OK) {
            $this->movies = json_decode($data->getContent(), true);

            return $this->movies;
        } else {
            return [
                'status'  => Response::HTTP_NOT_FOUND,
                'message' => 'Data not found',
            ];
        }
    }

    /**
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getTopOneMovies(): array
    {
        $requestPath = "https://api.themoviedb.org/3/movie/top_rated?api_key="
            .$this->apiKey."&language=en-US&page=1";
        $data = $this->getClientRequest('GET', $requestPath);
        if ($data->getStatusCode() === response::HTTP_OK) {
            $movie = json_decode($data->getContent(), true);
            if ($movie) {
                $movieId = $movie['results'][0]['id'];
                $videoMovieData = $this->getVideos($movieId);

                return [
                    "movie_id"             => $movieId,
                    "movie_key"            => $videoMovieData['results'][0]['key'],
                    "movie_streaming_path" => $this->formatYoutubeUrl(
                        $videoMovieData['results'][0]['key']
                    ),
                ];
            }
        } else {
            return [
                'status'  => 404,
                'message' => 'Top movie data not found',
            ];
        }
    }

    /**
     * @param string $movieID
     *
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getSteamMovieData(string $movieID)
    {
        $requestPath = "https://api.themoviedb.org/3/movie/{$movieID}?api_key="
            .$this->apiKey."&language=en-US";
        $data = $this->getClientRequest('GET', $requestPath);

        if ($data->getStatusCode() === response::HTTP_OK) {
            $movie = json_decode($data->getContent(), true);
            if ($movie) {
                $videoMovieData = $this->getVideos($movieID);

                return [
                    "movie_id"             => $movieID,
                    "movie_key"            => $videoMovieData['results'][0]['key'],
                    "movie_streaming_path" => $this->formatYoutubeUrl(
                        $videoMovieData['results'][0]['key']
                    ),
                ];
            }
        } else {
            return [
                'status'  => 404,
                'message' => sprintf('Movie ID %s  not found', $movieID),
            ];
        }
    }

    public function getVideos(int $movieId)
    {
        $movieRequestPath
            = "https://api.themoviedb.org/3/movie/283566/videos?api_key="
            .$this->apiKey."&language=en-US";
        $topMovieData = $this->getClientRequest('GET', $movieRequestPath);

        if ($topMovieData->getStatusCode() === response::HTTP_OK) {
            return json_decode($topMovieData->getContent(), true);
        } else {
            return [
                'status'  => 404,
                'message' => 'Data not found',
            ];
        }
    }

    /**
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMoviesGender(): array
    {
        $requestPath = "https://api.themoviedb.org/3/genre/movie/list?api_key="
            .$this->apiKey."&language=en-US";

        $data = $this->getClientRequest('GET', $requestPath);
        if ($data->getStatusCode() === response::HTTP_OK) {
            $this->gender = json_decode($data->getContent(), true);

            return $this->gender;
        } else {
            return [
                'status'  => 404,
                'message' => 'Genre data not found',
            ];
        }
    }

    /**
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function authenticate(): array
    {
        $requestPath
            = "https://api.themoviedb.org/3/authentication/guest_session/new?api_key="
            .$this->apiKey;
        $response = $this->getClientRequest('GET', $requestPath);

        if ($response->getStatusCode() === response::HTTP_OK) {
            return json_decode($response->getContent(), true);
        }

        return [
            'status'  => $response->getStatusCode(),
            'message' => "Connexion error",
        ];
    }
}
