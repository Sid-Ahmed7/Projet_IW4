<?php

namespace App\Controller;

use App\Entity\Reque;
use App\Form\PublicRequeType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicDevisController extends AbstractController
{
    #[Route('/devis/{companyId}', name: 'app_public_devis_form', methods: ['GET', 'POST'])]
    public function devisForm(Request $request, EntityManagerInterface $entityManager, CompanyRepository $companyRepository, int $companyId): Response
    {
        $company = $companyRepository->find($companyId);
        if (!$company) {
            throw $this->createNotFoundException('L\'organisation demandée n\'existe pas.');
        }

        $reque = new Reque();
        $form = $this->createForm(PublicRequeType::class, $reque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $reque->setCreatedAt($now);
            $reque->setState('en attente');
            $reque->setCompanie($companyId);

            $entityManager->persist($reque);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande de devis a été envoyée avec succès. Nous vous contacterons bientôt.');

            return $this->redirectToRoute('app_public_devis_success');
        }

        return $this->render('public/devis/form.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
        ]);
    }

    #[Route('/devis/success', name: 'app_public_devis_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('public/devis/success.html.twig');
    }
} 