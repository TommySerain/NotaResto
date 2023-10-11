<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route('/reviews', name: 'app_reviews', methods:['GET'])]
    public function getReviews(ReviewRepository $ReviewRepository): Response
    {
        return $this->render('review/index.html.twig', [
            'reviews' => $ReviewRepository->findAll(),
        ]);
    }

    #[Route('/review/{id}', name: 'app_review', methods:['GET'])]
    public function getReview(ReviewRepository $reviewRepository, int $id): Response
    {
        return $this->render('review/details.html.twig', [
            'review' => $reviewRepository->find($id),
        ]);
    }

    #[Route('/review', name: 'app_review_new', methods:['GET','POST'])]
    public function createReview(ReviewRepository $reviewRepository, Request $request): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setPostedDate(new DateTime());
            $reviewRepository->save($review);
            return $this->redirectToRoute('app_review', ['id' => $review->getId()]);
        }
        return $this->renderForm('review/new.html.twig', [
            'review' => $review,
            'formCreate' => $form
        ]);
    }

    #[Route('/review/{id}/edit', name: 'app_review_edit', methods:['GET', 'POST'])]
    public function editReview(ReviewRepository $reviewRepository, int $id, Request $request): Response
    {
        $review = $reviewRepository->find($id);
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->save($review);
            return $this->redirectToRoute('app_review', ['id' => $review->getId()]);
        }
        return $this->renderForm('review/edit.html.twig', [
            'review' => $review,
            'formEdit' => $form->createView(),
        ]);
    }

    #[Route('/review/{id}', name: 'app_review_delete', methods:['DELETE'])]
    public function deleteReview(ReviewRepository $reviewRepository, int $id): Response
    {
        $review = $reviewRepository->find($id);
        $reviewRepository->remove($review);
        $reviewRepository->flush();
        
        return $this->redirectToRoute('app_reviews');
    }
}
