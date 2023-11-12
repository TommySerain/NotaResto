<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\ReviewResponse;
use App\Entity\User;
use App\Form\ReviewResponseType;
use App\Form\ReviewType;
use App\Repository\RestaurantRepository;
use App\Repository\ReviewRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/review')]
class ReviewController extends AbstractController
{
    private UserInterface $user;
    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/new', name: 'app_review_new', methods:['GET','POST'])]
    public function createReview(EntityManagerInterface $em, Request $request): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setPostedDate(new DateTime());
            $em->persist($review);
            $em->flush();
            return $this->redirectToRoute('app_user_review', ['id' => $review->getId()]);
        }
        return $this->renderForm('review/new.html.twig', [
            'review' => $review,
            'formCreate' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'app_review_edit', methods:['GET', 'POST'])]
    public function editReview(ReviewRepository $reviewRepository, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $review = $reviewRepository->find($id);
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($review);
            $em->flush();

            return $this->redirectToRoute('app_user_review');
        }
        return $this->renderForm('review/edit.html.twig', [
            'review' => $review,
            'formEdit' => $form,
        ]);
    }

    #[Route('/restaurantuserreviews', name: 'app_restaurant_user_review', methods:['GET', 'POST'])]
    public function getReviewsByRestaurantUser(ReviewRepository $reviewRepository, Security $security,  Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        $result = "";
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

            return $this->redirectToRoute('app_restaurant_user_review', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('review/restorerReviews.html.twig', [
            'reviews' => $reviewRepository->getByRestaurantUser($user),
            'form' => $formReviewResponse,
            'result' => $result
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods:['POST'])]
    public function deleteReview(ReviewRepository $reviewRepository, EntityManagerInterface $em,  int $id, RestaurantRepository $restaurantRepository): Response
    {

        $review = $reviewRepository->find($id);
        $em->remove($review);
        $em->flush();

        if ($this->user instanceof User){
            return $this->redirectToRoute('app_user_show', ['id' => $this->user->getId()]);
        }
        $restaurants = $restaurantRepository->findAll();
        return $this->redirectToRoute('app_index', [
            'restaurants'=>$restaurants
        ]);
    }

    #[Route('/myreviews', name: 'app_user_review', methods:['GET'])]
    public function getUserReviews(ReviewRepository $reviewRepository, Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('review/myReviews.html.twig', [
            'reviews' => $reviewRepository->findBy(['user'=> $user]),
        ]);
    }



}
