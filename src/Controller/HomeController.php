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

        foreach ($tenBestRestaurantsId as $value) {
            $tenBestRestaurants[] = $restaurantRepository->find($value['restaurantId']);
        }
        // $tenBestRestaurants = array_map(function ($data) use ($restaurantRepository) {
        //     return ($restaurantRepository->find($data['restaurantId']));
        // }, $tenBestRestaurantsId);
        if ($tenBestRestaurants){
            return $this->render('home/index.html.twig', [
                'restaurants' => $tenBestRestaurants,
            ]);
        }
        $allRestaurant=$restaurantRepository->findAll();
        return $this->render("restaurant/listAll.html.twig",[
            'restaurants'=>$allRestaurant
        ]);
    }
}
