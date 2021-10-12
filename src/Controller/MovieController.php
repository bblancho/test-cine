<?php

namespace App\Controller;

use App\Services\TMDbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
//    https://adminlte.io/docs/3.0/javascript/card-refresh.html
    /**
     * @Route("/", name="movie")
     */
    public function index(TMDbService $TMDbService): Response
    {
        $getLatestMovie = $TMDbService->getPopularMovies();
        $genres = $TMDbService->getGenres();
//        dd($genres);
//      dd($getLatestMovie['results'][0]);
        /*  $movies = $TMDbService->getGenres();
        $movies = $TMDbService->searchMovie('Jack Reacher');
        $movies = $TMDbService->getPopularMovies();
        dd($movies);*/
        return $this->render('movie/index.html.twig', [
            'imag_base_url' => $TMDbService::Image_base_url,
            'latestMovie' => $getLatestMovie['results'][0],
            'popularvideos' => $getLatestMovie['results'],
            'genres' => $genres['genres'],

        ]);
    }


    /**
     * @Route("/searchbymoviename", name="searchbymoviename",methods={"POST","GET"})
     */
    public function searchbymoviename(Request $request,TMDbService $TMDbService): Response
    {
        $movies = $TMDbService->searchMovie($request->get('moviename'));
        return $this->json([
            'data'=>$movies
        ]);
    }



    /**
     * @Route("/searchbygenre", name="searchbygenre",methods={"POST","GET"})
     */
    public function searchbygenre(Request $request,TMDbService $TMDbService): Response
    {


        $data = [];
        if (!empty($request->get('array_genre'))) {
            foreach (explode(",", $request->get('array_genre')) as $movieId){
                $movies = $TMDbService->getMoviesByGenre($movieId);
                $data = array_merge($data, $movies['results']);
            }
            if (count(explode(",", $request->get('array_genre'))) > 0) {
                $data = array_reverse($data);
            }
        }else{
            $data =$TMDbService->getPopularMovies()['results'];
        }


        return $this->json([
            'data'=>$data
        ]);
    }
}
