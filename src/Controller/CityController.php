<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/city')]
class CityController extends AbstractController
{
    #[Route('/', name: 'app_city_index', methods:['GET'])]
    public function getCities(CityRepository $cityRepository): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_city_new', methods:['GET','POST'])]
    public function createCity(Request $request, EntityManagerInterface $em): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();
            return $this->redirectToRoute('app_city_index', ['id' => $city->getId()]);
        }
        return $this->render('city/new.html.twig', [
            'city' => $city,
            'formCreate' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_city_show', methods:['GET'])]
    public function getCity(CityRepository $cityRepository, int $id): Response
    {
        return $this->render('city/details.html.twig', [
            'city' => $cityRepository->find($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_city_edit', methods:['GET', 'POST'])]
    public function editCity(CityRepository $cityRepository, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $city = $cityRepository->find($id);
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();
            return $this->redirectToRoute('app_city_index', ['id' => $city->getId()]);
        }
        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'formEdit' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_city_delete', methods:['POST'])]
    public function deleteCity(CityRepository $cityRepository, EntityManagerInterface $em, int $id): Response
    {
        $city = $cityRepository->find($id);
        $em->remove($city);
        $em->flush();
        
        return $this->redirectToRoute('app_city_index');
    }
}
