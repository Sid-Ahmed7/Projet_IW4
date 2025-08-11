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
use App\Service\NotificationService;
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
    public function new(Request $request, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $devis->setHubuser($user);
            $devis->setState('En attente de validation');
            $devis->setPaymentToken(Uuid::v4());
            
            // Traitement des lignes du devis
            $devisAssets = $request->request->all('devis_assets') ?? [];
            $totalPrice = 0;
            
            foreach ($devisAssets as $assetData) {
                if (!empty($assetData['name']) && !empty($assetData['description'])) {
                    $devisAsset = new DevisAsset();
                    $devisAsset->setName($assetData['name']);
                    $devisAsset->setDescription($assetData['description']);
                    
                    $unitPrice = floatval($assetData['unitPrice']);
                    $size = floatval($assetData['size']);
                    $lineTotal = $unitPrice * $size;
                    
                    $devisAsset->setUnitPrice($unitPrice); // Prix unitaire en euros
                    $devisAsset->setPrice($lineTotal); // Prix total en euros
                    $devisAsset->setSize($size);
                    $devisAsset->setState('En attente');
                    $devisAsset->setCreatedAt(new \DateTimeImmutable());
                    $devisAsset->setDevis($devis);
                    
                    $totalPrice += $lineTotal;
                    
                    $entityManager->persist($devisAsset);
                }
            }
            
            // Définir le prix total du devis
            $devis->setPrice((string)$totalPrice);
            
            $entityManager->persist($devis);
            
            // Notification pour le créateur
            $notification = new Notification();
            $notification->addUser($user);
            $notification->setType('Systeme');
            $notification->setTitle('Votre devis est créé');
            $notification->setMessage("Salut {$user->getFirstname()}, votre devis est créé et en attente de validation par l'entreprise destinataire.");
            $notification->setIsRead(false);
            $notification->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($notification);
            
            // Si le devis est destiné à une entreprise, envoyer un email de notification à l'entreprise
            if ($devis->getCompany()) {
                $companyEmail = $devis->getCompany()->getEmail();
                if ($companyEmail) {
                    $email = (new Email())
                        ->from(new Address('ibrahim60200@gmail.com', 'FactuPro'))
                        ->to($companyEmail)
                        ->subject('Nouveau devis à valider - ' . $devis->getTitle())
                        ->html("
                            <h2>Nouveau devis à valider</h2>
                            <p>Bonjour,</p>
                            <p>Un nouveau devis a été créé et nécessite votre validation :</p>
                            <ul>
                                <li><strong>De :</strong> {$user->getFirstname()} {$user->getLastname()}</li>
                                <li><strong>Titre :</strong> {$devis->getTitle()}</li>
                                <li><strong>Montant :</strong> {$devis->getPrice()} €</li>
                                <li><strong>Date de création :</strong> " . (new \DateTime())->format('d/m/Y à H:i') . "</li>
                            </ul>
                            <p>Connectez-vous à votre espace FactuPro pour valider ou rejeter ce devis.</p>
                            <p>Cordialement,<br>L'équipe FactuPro</p>
                        ");

                    $mailer->send($email);
                }
            }
            
            $entityManager->flush();

            // Envoi de la notification par email
            $notificationService->notifyNewQuote($devis);

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
        
        // Seul le créateur du devis ou l'entreprise destinataire peut le modifier
        $canEdit = false;
        
        // Le créateur peut toujours éditer
        if ($devi->getHubuser() === $user) {
            $canEdit = true;
        }
        
        // Si l'utilisateur a des entreprises et que le devis est destiné à l'une d'elles
        if (!$canEdit && $user->getAccountType() === 'company') {
            foreach ($user->getCompanies() as $userCompany) {
                if ($devi->getCompany() === $userCompany) {
                    $canEdit = true;
                    break;
                }
            }
        }
        
        if (!$canEdit) {
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
        
        // Seul le créateur du devis ou l'entreprise destinataire peut modifier le prix
        $canEdit = false;
        
        // Le créateur peut toujours éditer
        if ($devi->getHubuser() === $user) {
            $canEdit = true;
        }
        
        // Si l'utilisateur a des entreprises et que le devis est destiné à l'une d'elles
        if (!$canEdit && $user->getAccountType() === 'company') {
            foreach ($user->getCompanies() as $userCompany) {
                if ($devi->getCompany() === $userCompany) {
                    $canEdit = true;
                    break;
                }
            }
        }
        
        if (!$canEdit) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }
        
        $devisAssets = $devi->getDevisAssets();

        $totalPrice = 0;
        foreach ($devisAssets as $devisAsset) {
            // Recalculer le prix à partir du prix unitaire et de la taille
            $unitPrice = $devisAsset->getUnitPrice();
            $size = $devisAsset->getSize();
            $lineTotal = $unitPrice * $size;
            
            // Mettre à jour le prix de la ligne
            $devisAsset->setPrice($lineTotal);
            $totalPrice += $lineTotal;
        }
        
        $devi->setUpdatedAt(new \DateTimeImmutable());
        $devi->setPrice((string)$totalPrice);
        $entityManager->flush();

        return $this->redirectToRoute('app_devis_show', ['id' => $devi->getId()]);
    }

    #[Route('/devis/{id}/recalculate', name: 'app_devis_recalculate', methods: ['GET'])]
    public function recalculatePrice(Devis $devi, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Seul le créateur du devis ou l'entreprise destinataire peut recalculer
        $canEdit = false;
        
        // Le créateur peut toujours recalculer
        if ($devi->getHubuser() === $user) {
            $canEdit = true;
        }
        
        // Si l'utilisateur a des entreprises et que le devis est destiné à l'une d'elles
        if (!$canEdit && $user->getAccountType() === 'company') {
            foreach ($user->getCompanies() as $userCompany) {
                if ($devi->getCompany() === $userCompany) {
                    $canEdit = true;
                    break;
                }
            }
        }
        
        if (!$canEdit) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce devis.');
        }
        
        $devisAssets = $devi->getDevisAssets();
        $totalPrice = 0;
        
        foreach ($devisAssets as $devisAsset) {
            // Recalculer le prix de chaque ligne à partir du prix unitaire et de la quantité
            $unitPrice = $devisAsset->getUnitPrice(); // En centimes ou euros selon le contexte
            $size = $devisAsset->getSize();
            
            // Si le prix unitaire est très grand (probablement en centimes), on le divise par 100
            if ($unitPrice > 10000) {
                $unitPriceInEuros = $unitPrice / 100;
            } else {
                $unitPriceInEuros = $unitPrice;
            }
            
            $lineTotal = $unitPriceInEuros * $size;
            $totalPrice += $lineTotal;
            
            // Mettre à jour le prix de la ligne (en euros)
            $devisAsset->setPrice($lineTotal);
            $devisAsset->setUnitPrice($unitPriceInEuros);
        }
        
        $devi->setUpdatedAt(new \DateTimeImmutable());
        $devi->setPrice((string)$totalPrice);
        $entityManager->flush();

        $this->addFlash('success', 'Prix du devis recalculé avec succès.');
        return $this->redirectToRoute('app_devis_index');
    }

    #[Route('/devis/{id}/validate', name: 'app_devis_validate', methods: ['POST'])]
    public function validateDevis(Request $request, Devis $devi, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('validate' . $devi->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        
        // Seule l'entreprise destinataire peut valider le devis
        $canValidate = false;
        
        if ($devi->getCompany() && $user->getAccountType() === 'company') {
            foreach ($user->getCompanies() as $userCompany) {
                if ($devi->getCompany() === $userCompany) {
                    $canValidate = true;
                    break;
                }
            }
        }
        
        if (!$canValidate) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de valider ce devis.');
        }
        
        // Valider le devis
        $devi->setState('Validé');
        $devi->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();
        
        // Envoi de la notification par email
        $notificationService->notifyQuoteAccepted($devi);
            ->subject('Devis validé - ' . $devi->getTitle())
            ->html("
                <h2>Votre devis a été validé !</h2>
                <p>Bonjour {$devi->getHubuser()->getFirstname()},</p>
                <p>Bonne nouvelle ! L'entreprise <strong>{$companyName}</strong> a validé votre devis :</p>
                <ul>
                    <li><strong>Titre :</strong> {$devi->getTitle()}</li>
                    <li><strong>Montant :</strong> {$devi->getPrice()} €</li>
                    <li><strong>Date de validation :</strong> " . (new \DateTime())->format('d/m/Y à H:i') . "</li>
                </ul>
                <p>Vous pouvez maintenant procéder au paiement de ce devis.</p>
                <p>Cordialement,<br>L'équipe FactuPro</p>
            ");

        $mailer->send($email);
        
        $this->addFlash('success', 'Devis validé avec succès. Un email de notification a été envoyé au créateur.');
        return $this->redirectToRoute('app_devis_show', ['id' => $devi->getId()]);
    }

    #[Route('/devis/{id}/reject', name: 'app_devis_reject', methods: ['POST'])]
    public function rejectDevis(Request $request, Devis $devi, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('reject' . $devi->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        
        // Seule l'entreprise destinataire peut rejeter le devis
        $canReject = false;
        
        if ($devi->getCompany() && $user->getAccountType() === 'company') {
            foreach ($user->getCompanies() as $userCompany) {
                if ($devi->getCompany() === $userCompany) {
                    $canReject = true;
                    break;
                }
            }
        }
        
        if (!$canReject) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de rejeter ce devis.');
        }
        
        // Rejeter le devis
        $devi->setState('Rejeté');
        $devi->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();
        
        // Récupérer le motif de refus s'il existe
        $reason = $request->request->get('reason', '');
        
        // Envoi de la notification par email
        $notificationService->notifyQuoteRejected($devi, $reason);
            ->subject('Devis rejeté - ' . $devi->getTitle())
            ->html("
                <h2>Votre devis a été rejeté</h2>
                <p>Bonjour {$devi->getHubuser()->getFirstname()},</p>
                <p>L'entreprise <strong>{$companyName}</strong> a rejeté votre devis :</p>
                <ul>
                    <li><strong>Titre :</strong> {$devi->getTitle()}</li>
                    <li><strong>Montant :</strong> {$devi->getPrice()} €</li>
                    <li><strong>Date de rejet :</strong> " . (new \DateTime())->format('d/m/Y à H:i') . "</li>
                </ul>
                <p>Vous pouvez modifier ce devis et le soumettre à nouveau pour validation.</p>
                <p>Cordialement,<br>L'équipe FactuPro</p>
            ");

        $mailer->send($email);
        
        $this->addFlash('error', 'Devis rejeté. Un email de notification a été envoyé au créateur.');
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
