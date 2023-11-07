<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/review')]
class ReviewController extends AbstractController
{
    private $user;
    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }
    #[Route('/admin', name: 'app_review_index', methods:['GET'])]
    public function getReviews(ReviewRepository $ReviewRepository): Response
    {
        return $this->render('review/index.html.twig', [
            'reviews' => $ReviewRepository->findAll(),
        ]);
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
            return $this->redirectToRoute('app_review_index', ['id' => $review->getId()]);
        }
        return $this->renderForm('review/new.html.twig', [
            'review' => $review,
            'formCreate' => $form
        ]);
    }

    #[Route('/admin/{id}', name: 'app_review_show', methods:['GET'])]
    public function getReview(ReviewRepository $reviewRepository, int $id): Response
    {
        return $this->render('review/details.html.twig', [
            'review' => $reviewRepository->find($id),
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

            if (in_array('ROLE_ADMIN',$this->user->getRoles())) {
                return $this->redirectToRoute('app_review_index', ['id' => $review->getId()]);
            }
            return $this->redirectToRoute('app_user_review');
        }
        return $this->renderForm('review/edit.html.twig', [
            'review' => $review,
            'formEdit' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods:['POST'])]
    public function deleteReview(ReviewRepository $reviewRepository, EntityManagerInterface $em,  int $id): Response
    {
        
        $review = $reviewRepository->find($id);
        $em->remove($review);
        $em->flush();
        
        if (in_array('ROLE_ADMIN',$this->user->getRoles())) {
            return $this->redirectToRoute('app_review_index');
        }
        return $this->redirectToRoute('app_user_review');
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
