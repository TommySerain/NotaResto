<?php

namespace App\Controller;

use App\Entity\ReviewResponse;
use App\Form\ReviewResponseType;
use App\Repository\ReviewRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review/response')]
class ReviewResponseController extends AbstractController
{
    #[Route('/new', name: 'app_review_response_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reviewResponse = new ReviewResponse();
        $form = $this->createForm(ReviewResponseType::class, $reviewResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewResponse->setPostedDate(new DateTime());
            $entityManager->persist($reviewResponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('review_response/new.html.twig', [
            'review_response' => $reviewResponse,
            'form' => $form,
        ]);
    }
    #[Route('/responseToAll', name:'app_reponse_to_all', methods: ['GET','POST'])]
    public function responseToAll(Request $request, EntityManagerInterface $entityManager, ReviewRepository $reviewRepository): Response
    {
        if (!$request->isMethod('POST') || !$request->getContent()) {
            return new JsonResponse(['error' => 'Invalid request'], Response::HTTP_BAD_REQUEST);
        }

        $jsonData = json_decode($request->getContent());

        if ($jsonData === null) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($jsonData as $response) {

            $reviewResponse = new ReviewResponse();
            $review = $reviewRepository->find(intval($response->id));
            $reviewResponse->setReview($review);
            $reviewResponse->setComment($response->value);
            $reviewResponse->setPostedDate(new DateTime());

            $entityManager->persist($reviewResponse);
        }
            $entityManager->flush();
            return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/edit', name: 'app_review_response_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReviewResponse $reviewResponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReviewResponseType::class, $reviewResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('review_response/edit.html.twig', [
            'review_response' => $reviewResponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_response_delete', methods: ['POST'])]
    public function delete(Request $request, ReviewResponse $reviewResponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reviewResponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reviewResponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
