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

class CityController extends AbstractController
{
    #[Route('/cities', name: 'app_cities', methods:['GET'])]
    public function getCities(CityRepository $cityRepository): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    #[Route('/city/{id}', name: 'app_city', methods:['GET'])]
    public function getCity(CityRepository $cityRepository, int $id): Response
    {
        return $this->render('city/details.html.twig', [
            'city' => $cityRepository->find($id),
        ]);
    }

    #[Route('/city', name: 'app_city_new', methods:['GET','POST'])]
    public function createCity(CityRepository $cityRepository, Request $request, EntityManagerInterface $em): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();
            return $this->redirectToRoute('app_city', ['id' => $city->getId()]);
        }
        return $this->render('city/new.html.twig', [
            'city' => $city,
            'formCreate' => $form->createView(),
        ]);
    }

    #[Route('/city/{id}/edit', name: 'app_city_edit', methods:['GET', 'POST'])]
    public function editCity(CityRepository $cityRepository, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $city = $cityRepository->find($id);
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();
            return $this->redirectToRoute('app_city', ['id' => $city->getId()]);
        }
        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'formEdit' => $form->createView(),
        ]);
    }

    #[Route('/city/{id}', name: 'app_city_delete', methods:['DELETE'])]
    public function deleteCity(CityRepository $cityRepository, int $id): Response
    {
        $city = $cityRepository->find($id);
        $cityRepository->remove($city);
        $cityRepository->flush();
        
        return $this->redirectToRoute('app_cities');
    }
}
