<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Reque;
use App\Form\RequeType;
use App\Repository\CompanyRepository;
use App\Repository\RequeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reque')]
class RequeController extends AbstractController
{
    #[Route('/all/{companyId}', name: 'app_reque_index', methods: ['GET'])]
public function index(RequeRepository $requeRepository, $companyId): Response
{
    if (!is_numeric($companyId)) {
        throw $this->createNotFoundException('Company ID must be a number.');
    }
    $reques = $requeRepository->findBy(['companie' => $companyId]);

    return $this->render('reque/index.html.twig', [
        'reques' => $reques,
    ]);
}


#[Route('/new/{companyId}', name: 'app_reque_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, CompanyRepository $companyRepository, $companyId): Response
{
    $reque = new Reque();
    $form = $this->createForm(RequeType::class, $reque);
    $form->handleRequest($request);

    $user = $this->getUser();

    $company = $companyRepository->find($companyId);
    if (!$company) {
        throw $this->createNotFoundException('La company demandée n\'existe pas.');
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $reque->setUsr($user);
        $now = new \DateTimeImmutable();
        $reque->setCreatedAt($now);
        $reque->setState('pending');
        $reque->setCompanie($companyId); 

        $entityManager->persist($reque);
        $entityManager->flush();

        return $this->redirectToRoute('app_test_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('reque/new.html.twig', [
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
