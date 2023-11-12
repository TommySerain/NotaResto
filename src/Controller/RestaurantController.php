<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Restaurant;
use App\Entity\Review;
use App\Entity\ReviewResponse;
use App\Entity\User;
use App\Form\NewPictureType;
use App\Form\NewReviewType;
use App\Form\RestaurantType;
use App\Form\ReviewResponseType;
use App\Repository\CityRepository;
use App\Repository\RestaurantRepository;
use App\Repository\ReviewRepository;
use App\Services\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{

    private UserInterface $user;
    public function __construct(Security $security)
    {
        if($security->getUser()){
            $this->user = $security->getUser();
        }
    }

    #[Route('/new', name: 'app_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant = $form->getData();
            $restaurant->setUser($this->getUser());
            $entityManager->persist($restaurant);
            $entityManager->flush();

            if ($this->user instanceof User){
                return $this->redirectToRoute('app_my_restaurants', ['id'=>$this->user->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/', name: 'app_restaurant_all', methods: ['GET'])]
    public function getAllRestaurants(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
        ]);
    }

    #[Route('/MyRestaurants/{id}', name: 'app_my_restaurants', methods: ['GET'])]
    public function getRestaurantsByCurrentUser(RestaurantRepository $restaurantRepository): Response
    {

        $restaurants = $restaurantRepository->findBy(['user' => $this->user]);

        return $this->render('restaurant/restaurantsByUser.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    #[Route('/restaurantsByZip', name: 'app_restaurants_by_zip', methods: ['GET'])]
    public function getRestaurantsByZipCode(RestaurantRepository $restaurantRepository, CityRepository $cityRepository): Response
    {
        if ($_GET) {
            $zipCode = $_GET['zipcode'];
            $city = $cityRepository->findBy(['zipCode' => $zipCode]);

            if (!$city) {
                return $this->redirectToRoute('app_restaurant_all');
            } else {
                $restaurants = $restaurantRepository->findBy(['city' => $city]);
            }

            return $this->render('home/index.html.twig', [
                'restaurants' => $restaurants,
            ]);
        }
        $restaurants = $restaurantRepository->findAll();
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    #[Route('/details/{id}', name: 'app_restaurant_show_details', methods: ['GET', 'POST'])]
    public function showOne(Restaurant $restaurant,  Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, ReviewRepository $reviewRepository): Response
    {
        $picture = new Picture();
        $result = "";
        $formPicture = $this->createForm(NewPictureType::class, $picture);
        $formPicture->handleRequest($request);
        if ($formPicture->isSubmitted() && $formPicture->isValid()) {
            $file = $formPicture['fileName']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                $picture->setFileName($fileName);
                $picture->setRestaurant($restaurant);
            } else {
                return $this->redirectToRoute('app_restaurant_show_details', ['id' => $restaurant->getId()], Response::HTTP_SEE_OTHER);
            }
            $entityManager->persist($picture);
            $entityManager->flush();
            return $this->redirectToRoute('app_restaurant_show_details', ['id' => $restaurant->getId()], Response::HTTP_SEE_OTHER);
        }

        $review = new Review();
        $formReview = $this->createForm(NewReviewType::class, $review);
        $formReview->handleRequest($request);
        if ($formReview->isSubmitted() && $formReview->isValid()) {
            $review = $formReview->getData();
            $review->setuser($this->getUser());
            $review->setRestaurant($restaurant);
            $review->setPostedDate(new DateTime());
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_show_details', ['id' => $restaurant->getId()], Response::HTTP_SEE_OTHER);
        }

        $reviewResponse = new ReviewResponse();
        $formReviewResponse = $this->createForm(ReviewResponseType::class, $reviewResponse);
        $formReviewResponse->handleRequest($request);
        if ($formReviewResponse->isSubmitted() && $formReviewResponse->isValid()) {
            $reviewResponse = $formReviewResponse->getData();
            $reviewResponse->setPostedDate(new DateTime());
            $review = $reviewRepository->find($request->request->get('review_id'));
            $reviewResponse->setReview($review);

            $entityManager->persist($reviewResponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_restaurant_show_details', ['id' => $restaurant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('restaurant/details.html.twig', [
            'restaurant' => $restaurant,
            'formReview' => $formReview->createView(),
            'formPicture' => $formPicture->createView(),
            'formReviewResponse' => $formReviewResponse->createView(),
            'result' => $result
        ]);
    }

    #[Route('/{id}/edit', name: 'app_restaurant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if($this->user instanceof User){
            return $this->redirectToRoute('app_my_restaurants', ['id'=>$this->user->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_delete', methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager,RestaurantRepository $restaurantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $restaurant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }
        if($this->user instanceof User){
            return $this->redirectToRoute('app_my_restaurants', ['id'=>$this->user->getId()], Response::HTTP_SEE_OTHER);
        }
        $restaurants = $restaurantRepository->findAll();
        return $this->redirectToRoute('app_index', [
            'restaurants'=>$restaurants
        ]);
    }
//    public function getLastRestaurants(RestaurantRepository $restaurantRepository): Response
//    {
//        return $this->render('home/index.html.twig', [
//            'lastRestaurants' => $restaurantRepository->getLastRestaurants(),
//        ]);
//    }
//
//    public function getReviews(Restaurant $restaurant): Response
//    {
//        return $this->render('home/index.html.twig', [
//            'reviews' => $restaurant->getReviews(),
//        ]);
//    }
}
