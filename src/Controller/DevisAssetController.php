<?php

namespace App\Controller;

use App\Entity\DevisAsset;
use App\Form\DevisAssetType;
use App\Repository\DevisAssetRepository;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/devis/asset')]
class DevisAssetController extends AbstractController
{
    #[Route('/', name: 'app_devis_asset_index', methods: ['GET'])]
    public function index(DevisAssetRepository $devisAssetRepository): Response
    {
        return $this->render('devis_asset/index.html.twig', [
            'devis_assets' => $devisAssetRepository->findAll(),
        ]);
    }

    #[Route('/new/{devisId}', name: 'app_devis_asset_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,$devisId,DevisRepository $devisRepository): Response
    {
        $devisAsset = new DevisAsset();
        $devis = $devisRepository->findOneBy(['id' => $devisId]);

        $form = $this->createForm(DevisAssetType::class, $devisAsset);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $totalPrice = $devisAsset->getSize() * $devisAsset->getUnitPrice();
            $devisAsset->setPrice($totalPrice); 
            $now = new \DateTimeImmutable(); 
            $devisAsset->setCreatedAt($now); 
            $devisAsset->setState('online'); 
            $devisAsset->setDevis($devis); 
            $entityManager->persist($devisAsset);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_devis_asset_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('devis_asset/new.html.twig', [
            'devis_asset' => $devisAsset,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_devis_asset_show', methods: ['GET'])]
    public function show(DevisAsset $devisAsset): Response
    {
        return $this->render('devis_asset/show.html.twig', [
            'devis_asset' => $devisAsset,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_asset_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DevisAsset $devisAsset, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisAssetType::class, $devisAsset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalPrice = $devisAsset->getSize() * $devisAsset->getUnitPrice();
            $devisAsset->setPrice($totalPrice); 
            $now = new \DateTimeImmutable(); 
            $devisAsset->setUpdatedAt($now); 
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_asset_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis_asset/edit.html.twig', [
            'devis_asset' => $devisAsset,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_asset_delete', methods: ['POST'])]
    public function delete(Request $request, DevisAsset $devisAsset, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devisAsset->getId(), $request->request->get('_token'))) {
            $entityManager->remove($devisAsset);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_asset_index', [], Response::HTTP_SEE_OTHER);
    }
}
