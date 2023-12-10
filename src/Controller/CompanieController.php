<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_Company_index', methods: ['GET'])]
    public function index(CompanyRepository $CompanyRepository): Response
    {
        return $this->render('Company/index.html.twig', [
            'Companys' => $CompanyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_Company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Company = new Company();
        $form = $this->createForm(CompanyType::class, $Company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($Company);
            $entityManager->flush();

            return $this->redirectToRoute('app_Company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Company/new.html.twig', [
            'Company' => $Company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_Company_show', methods: ['GET'])]
    public function show(Company $Company): Response
    {
        return $this->render('Company/show.html.twig', [
            'Company' => $Company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_Company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $Company, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanyType::class, $Company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_Company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Company/edit.html.twig', [
            'Company' => $Company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_Company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $Company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($Company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_Company_index', [], Response::HTTP_SEE_OTHER);
    }
}
