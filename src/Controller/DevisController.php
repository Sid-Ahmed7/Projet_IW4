<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\DevisAsset;
use App\Entity\Notification;
use App\Form\DevisType;
use App\Repository\DevisAssetRepository;
use App\Repository\DevisRepository;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Uid\Uuid;


#[Route('/account')]
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

    #[Route('/company/{companyId}/devis', name: 'app_company_devis')]
    public function companyDevis(int $companyId, DevisRepository $devisRepository, CompanyRepository $companyRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Vérifier que l'entreprise appartient à l'utilisateur
        $company = $companyRepository->findOneBy(['id' => $companyId, 'createdBy' => $user->getId()]);
        
        if (!$company) {
            throw $this->createNotFoundException('Organisation non trouvée ou accès non autorisé.');
        }

        $devis = $devisRepository->findBy(['company' => $company]);

        return $this->render('devis/company.html.twig', [
            'devis' => $devis,
            'company' => $company,
        ]);
    }

    #[Route('/devis/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $devis->setHubuser($user);
            $devis->setState('En attente');
            $devis->setPaymentToken(Uuid::v4());
            
            // Traitement des lignes du devis
            $devisAssets = $request->request->all('devis_assets') ?? [];
            $totalPrice = 0;
            
            foreach ($devisAssets as $assetData) {
                if (!empty($assetData['name']) && !empty($assetData['description'])) {
                    $devisAsset = new DevisAsset();
                    $devisAsset->setName($assetData['name']);
                    $devisAsset->setDescription($assetData['description']);
                    $devisAsset->setUnitPrice((int)($assetData['unitPrice'] * 100)); // Prix unitaire en centimes
                    $devisAsset->setPrice((int)($assetData['unitPrice'] * $assetData['size'] * 100)); // Prix total en centimes
                    $devisAsset->setSize((int)$assetData['size']);
                    $devisAsset->setState('En attente');
                    $devisAsset->setCreatedAt(new \DateTimeImmutable());
                    $devisAsset->setDevis($devis);
                    
                    // Calculer le total pour cette ligne
                    $lineTotal = $assetData['unitPrice'] * $assetData['size'];
                    $totalPrice += $lineTotal;
                    
                    $entityManager->persist($devisAsset);
                }
            }
            
            // Définir le prix total du devis
            $devis->setPrice((string)$totalPrice);
            
            $entityManager->persist($devis);
            
            // Notification
            $notification = new Notification();
            $notification->addUser($user);
            $notification->setType('Systeme');
            $notification->setTitle('Votre devis est disponible');
            $notification->setMessage("Salut {$user->getFirstname()}, votre devis est disponible.");
            $notification->setIsRead(false);
            $notification->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($notification);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/new.html.twig', [
            'devis' => $devis,
            'form' => $form,
        ]);
    }

    #[Route('/devis/{id}/show', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devi, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Vérifier l'accès au devis
        $hasAccess = false;
        
        // 1. L'utilisateur est le créateur du devis
        if ($devi->getHubuser() === $user) {
            $hasAccess = true;
        }
        
        // 2. L'utilisateur fait partie de l'entreprise destinataire
        if ($devi->getCompany() && $user->getCompany() === $devi->getCompany()) {
            $hasAccess = true;
        }
        
        // 3. L'utilisateur est le créateur de l'entreprise destinataire
        if ($devi->getCompany() && $devi->getCompany()->getCreatedBy() === $user->getId()) {
            $hasAccess = true;
        }
        
        if (!$hasAccess) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }
        
        $context = $request->query->get('context', 'account'); // par défaut "account"

        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
            'assets' => $devi->getDevisAssets(),
            'context' => $context,
        ]);
    }


    #[Route('/devis/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Seul le créateur du devis peut le modifier
        if ($devi->getHubuser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }
        
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Supprimer les anciens assets
                foreach ($devi->getDevisAssets() as $asset) {
                    $entityManager->remove($asset);
                }
                $entityManager->flush();

                // Ajouter les nouveaux assets
                $assetsData = $request->request->all()['devis_assets'] ?? [];
                $totalPrice = 0;

                foreach ($assetsData as $assetData) {
                    if (empty($assetData['name']) || empty($assetData['description']) || 
                        !isset($assetData['unitPrice']) || !isset($assetData['size'])) {
                        continue;
                    }

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

                $devi->setUpdatedAt(new \DateTimeImmutable());
                $devi->setPrice((string)$totalPrice);
                $entityManager->flush();

                $this->addFlash('success', 'Le devis a été modifié avec succès.');
                return $this->redirectToRoute('app_devis_show', ['id' => $devi->getId()], Response::HTTP_SEE_OTHER);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification du devis : ' . $e->getMessage());
            }
        }

        return $this->render('devis/edit.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }

    #[Route('/devis/{id}/edit/price', name: 'app_devis_edit_price', methods: ['GET', 'POST'])]
    public function updatePrice(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Seul le créateur du devis peut modifier le prix
        if ($devi->getHubuser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }
        
        $devisAssets = $devi->getDevisAssets();

        $totalPrice = 0;
        foreach ($devisAssets as $devisAsset) {
            $totalPrice += $devisAsset->getPrice();
        }
        $devi->setUpdatedAt(new \DateTimeImmutable());
        $devi->setPrice((string)$totalPrice);
        $entityManager->flush();

        return $this->redirectToRoute('app_devis_show', ['id' => $devi->getId()]);
    }

    #[Route('/devis/{id}', name: 'app_devis_delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Seul le créateur du devis peut le supprimer
        if ($devi->getHubuser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }
        
        if ($this->isCsrfTokenValid('delete' . $devi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/devis/{id}/relance', name: 'app_devis_reminder')]
    public function sendReminderDevis(Devis $devis, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vérifier l'accès au devis (même logique que la méthode show)
        $hasAccess = false;
        
        // 1. L'utilisateur est le créateur du devis
        if ($devis->getHubuser() === $user) {
            $hasAccess = true;
        }
        
        // 2. L'utilisateur fait partie de l'entreprise destinataire
        if ($devis->getCompany() && $user->getCompany() === $devis->getCompany()) {
            $hasAccess = true;
        }
        
        // 3. L'utilisateur est le créateur de l'entreprise destinataire
        if ($devis->getCompany() && $devis->getCompany()->getCreatedBy() === $user->getId()) {
            $hasAccess = true;
        }
        
        if (!$hasAccess) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }

        $email = (new Email())
            ->from(new Address('ibrahim60200@gmail.com', 'FactuPro'))
            ->to($devis->getHubuser()->getEmail()) // Envoyer au CRÉATEUR du devis
            ->subject('Relance de devis - ' . $devis->getTitle())
            ->html("<p>Bonjour {$devis->getHubuser()->getFirstname()},<br> Ceci est une relance pour le devis intitulé : <strong>{$devis->getTitle()}</strong>.<br> Montant estimé : <strong>{$devis->getPrice()} €</strong><br> Destinataire : <strong>{$devis->getCompany()->getName()}</strong></p>");

        $mailer->send($email);

        $this->addFlash('success', 'Relance envoyée avec succès.');
        return $this->redirectToRoute('app_devis_show', ['id' => $devis->getId()]);
    }
}
