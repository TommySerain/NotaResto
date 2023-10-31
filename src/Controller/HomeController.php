<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RestaurantRepository $restaurantRepository, ReviewRepository $reviewRepository): Response
    {


        $tenBestRestaurantsId = $reviewRepository->findBestTenRatings();
        // var_dump($tenBestRestaurantsId);

        $tenBestRestaurants = array_map(function($data) use ($restaurantRepository) {
            return($restaurantRepository->find($data['restaurantId'])) ;
        }, $tenBestRestaurantsId);
        // var_dump($tenBestRestaurants);

        return $this->render('home/index.html.twig', [
        'restaurants' => $tenBestRestaurants,
        ]);
    }
}