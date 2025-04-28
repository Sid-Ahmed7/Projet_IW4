<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Reque;
use App\Form\RequeType;
use App\Repository\CompanyRepository;
use App\Repository\RequeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reque')]
class RequeController extends AbstractController
{
    #[Route('/my-requests', name: 'app_my_reque_index', methods: ['GET'])]
    public function myRequests(RequeRepository $requeRepository, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour voir cette page.');
        }

        // Obtenez toutes les requêtes pour l'utilisateur connecté
        $reques = $requeRepository->findBy(['usr' => $user]);

        return $this->render('account/dashboard.html.twig', [
            'user' => $user,
            'reques' => $reques,
        ]);
    }

    #[Route('/company/{companyId}', name: 'app_reque_company_index', methods: ['GET'])]
    public function companyRequests(RequeRepository $requeRepository, CompanyRepository $companyRepository, int $companyId): Response
    {
        $company = $companyRepository->find($companyId);
        if (!$company) {
            throw $this->createNotFoundException('L\'entreprise demandée n\'existe pas.');
        }

        $reques = $requeRepository->findBy(['companie' => $companyId]);

        return $this->render('account/dashboard.html.twig', [
            'user' => $this->getUser(),
            'reques' => $reques,
            'company' => $company,
        ]);
    }

    #[Route('/new/{companyId}', name: 'app_reque_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CompanyRepository $companyRepository, $companyId): Response
    {
        $reque = new Reque();
        $form = $this->createForm(RequeType::class, $reque);
        $form->handleRequest($request);
        $company = $companyRepository->find($companyId);
        if (!$company) {
            throw $this->createNotFoundException('La company demandée n\'existe pas.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $reque->setCreatedAt($now);
            $reque->setState('en attente');
            $reque->setCompanie($companyId);
            $reque->setUsr($this->getUser());

            $entityManager->persist($reque);
            $entityManager->flush();

            return $this->redirectToRoute('app_reque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/dashboard.html.twig', [
            'reque' => $reque,
            'form' => $form->createView(),
            'company' => $company,
        ]);
    }

    #[Route('/{id}', name: 'app_reque_show', methods: ['GET'])]
    public function show(Reque $reque): Response
    {
        return $this->render('reque/show.html.twig', [
            'reque' => $reque,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reque $reque, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RequeType::class, $reque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reque/edit.html.twig', [
            'reque' => $reque,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reque_delete', methods: ['POST'])]
    public function delete(Request $request, Reque $reque, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reque->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reque);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reque_index', [], Response::HTTP_SEE_OTHER);
    }
}
