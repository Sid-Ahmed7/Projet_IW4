<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\DevisAsset;
use App\Entity\Notification;
use App\Form\DevisType;
use App\Repository\DevisAssetRepository;
use App\Repository\DevisRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

#[Route('account')]
class DevisController extends AbstractController
{
    #[Route('/devis', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $devis = $devisRepository->findBy(['hubuser' => $user]);

        return $this->render('devis/index.html.twig', [
            'devis' => $devis,
        ]);
    }

    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les assets du formulaire HTML
            $assetsData = $request->request->all()['devis_assets'] ?? [];
            $totalPrice = 0;

            foreach ($assetsData as $assetData) {
                $asset = new DevisAsset();
                $asset->setName($assetData['name']);
                $asset->setDescription($assetData['description']);
                $unitPrice = floatval($assetData['unitPrice']);
                $size = floatval($assetData['size']);
                $price = $unitPrice * $size;
                
                $asset->setUnitPrice($unitPrice);
                $asset->setPrice($price);
                $asset->setSize($size);
                $asset->setDevis($devis);
                $asset->setCreatedAt(new \DateTimeImmutable());
                $asset->setUpdatedAt(new \DateTimeImmutable());
                $asset->setState('online');
                
                $totalPrice += $price;
                $entityManager->persist($asset);
            }

            $devis->setHubuser($user);
            $devis->setPrice((string)$totalPrice);
            $devis->setState('En attente');
            $devis->setUpdatedAt(new \DateTimeImmutable());
            
            $entityManager->persist($devis);
            $entityManager->flush();

            $this->addFlash('success', 'Le devis a été créé avec succès.');
            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/new.html.twig', [
            'devis' => $devis,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/show/', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devi): Response
    {
        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
            'assets' => $devi->getDevisAssets() 

        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager, $id, DevisRepository $devisRepository, DevisAssetRepository $devisAssetRepository): Response
    {
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le changement de statut
            $newState = $request->request->get('devis')['state'] ?? null;
            if ($newState) {
                $devi->setState($newState);
            }

            // Supprimer les anciens assets
            foreach ($devi->getDevisAssets() as $asset) {
                $entityManager->remove($asset);
            }
            $entityManager->flush();

            // Ajouter les nouveaux assets
            $assetsData = $request->request->all()['devis_assets'] ?? [];
            $totalPrice = 0;

            foreach ($assetsData as $assetData) {
                $asset = new DevisAsset();
                $asset->setName($assetData['name']);
                $asset->setDescription($assetData['description']);
                $unitPrice = floatval($assetData['unitPrice']);
                $size = floatval($assetData['size']);
                $price = $unitPrice * $size;
                
                $asset->setUnitPrice($unitPrice);
                $asset->setPrice($price);
                $asset->setSize($size);
                $asset->setDevis($devi);
                $asset->setCreatedAt(new \DateTimeImmutable());
                $asset->setUpdatedAt(new \DateTimeImmutable());
                $asset->setState('online');
                
                $totalPrice += $price;
                $entityManager->persist($asset);
            }

            $now = new \DateTimeImmutable();
            $devi->setUpdatedAt($now);
            $devi->setPrice((string)$totalPrice);
            
            // Créer une notification pour le changement de statut
            if ($newState === 'accepted' && $devi->getState() !== 'accepted') {
                $notification = new Notification();
                $notification->setType('devis_accepted');
                $notification->setTitle('Devis accepté');
                $notification->setMessage('Le devis #' . $devi->getId() . ' a été accepté');
                $notification->setIsRead(false);
                $notification->setCreatedAt(new \DateTimeImmutable());
                $notification->addUser($devi->getHubuser());
                $entityManager->persist($notification);
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Le devis a été modifié avec succès.');
            return $this->redirectToRoute('app_devis_show', ['id' => $devi->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/edit.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/edit/price', name: 'app_devis_edit_price', methods: ['GET', 'POST'])]
    public function updatePrice(Request $request, Devis $devi, EntityManagerInterface $entityManager, $id, DevisRepository $devisRepository, DevisAssetRepository $devisAssetRepository): Response
    {
        $devis = $devisRepository->find($id);
        if (!$devis) {
            throw $this->createNotFoundException('Le devis avec l\'ID ' . $id . ' n\'existe pas.');
        }
        $devisAssets = $devisAssetRepository->findBy(['devis' => $devis]);

        $totalPrice = 0;
        foreach ($devisAssets as $devisAsset) {
            $totalPrice += $devisAsset->getPrice();
        }
        $now = new \DateTimeImmutable();
        $devi->setPrice($totalPrice);
        $devi->setUpdatedAt($now);
        $entityManager->flush();

        return $this->redirectToRoute('app_devis_show', ['id' => $id]);

        // return $this->render('devis/edit.html.twig', [
        //     'devi' => $devi,
        // ]);
    }


    #[Route('/{id}', name: 'app_devis_delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $devi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/company/{companyId}/devis', name: 'app_company_devis')]
    public function companyDevis(int $companyId, DevisRepository $devisRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $devis = $devisRepository->findBy(['hubuser' => $user, 'company' => $companyId]);

        return $this->render('devis/company.html.twig', [
            'devis' => $devis,
            'companyId' => $companyId,
        ]);
    }
}
