<?php

namespace App\Controller;

use App\Services\MoviesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route(path="/authentication/token", name="movies_login")
     */
    public function login(
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
}
