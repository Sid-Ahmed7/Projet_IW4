<?php

namespace App\Controller;

use App\Entity\Companie;
use App\Form\CompanieType;
use App\Repository\CompanieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/companie')]
class CompanieController extends AbstractController
{
    #[Route('/', name: 'app_companie_index', methods: ['GET'])]
    public function index(CompanieRepository $companieRepository): Response
    {
        return $this->render('companie/index.html.twig', [
            'companies' => $companieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_companie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $companie = new Companie();
        $form = $this->createForm(CompanieType::class, $companie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($companie);
            $entityManager->flush();

            return $this->redirectToRoute('app_companie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('companie/new.html.twig', [
            'companie' => $companie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_companie_show', methods: ['GET'])]
    public function show(Companie $companie): Response
    {
        return $this->render('companie/show.html.twig', [
            'companie' => $companie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_companie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Companie $companie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanieType::class, $companie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_companie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('companie/edit.html.twig', [
            'companie' => $companie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_companie_delete', methods: ['POST'])]
    public function delete(Request $request, Companie $companie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$companie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($companie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_companie_index', [], Response::HTTP_SEE_OTHER);
    }
}
