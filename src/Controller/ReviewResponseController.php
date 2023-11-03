<?php

namespace App\Controller;

use App\Entity\ReviewResponse;
use App\Form\ReviewResponseType;
use App\Repository\ReviewResponseRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review/response')]
class ReviewResponseController extends AbstractController
{
    #[Route('/', name: 'app_review_response_index', methods: ['GET'])]
    public function index(ReviewResponseRepository $reviewResponseRepository): Response
    {
        return $this->render('review_response/index.html.twig', [
            'review_responses' => $reviewResponseRepository->findAll(),
        ]);
    }

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

            return $this->redirectToRoute('app_review_response_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('review_response/new.html.twig', [
            'review_response' => $reviewResponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_response_show', methods: ['GET'])]
    public function show(ReviewResponse $reviewResponse): Response
    {
        return $this->render('review_response/show.html.twig', [
            'review_response' => $reviewResponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_review_response_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReviewResponse $reviewResponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReviewResponseType::class, $reviewResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_review_response_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_review_response_index', [], Response::HTTP_SEE_OTHER);
    }
}
