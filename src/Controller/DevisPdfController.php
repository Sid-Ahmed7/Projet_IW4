<?php
// src/Controller/DevisPdfController.php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Devis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Attachment;
use Symfony\Component\Security\Core\Security;

class DevisPdfController extends AbstractController
{

    #[Route('/devis/pdf/{id}', name: 'app_devis_pdf', methods: ['GET'])]
    public function generateDevisPdf(EntityManagerInterface $em, $id): Response
    {

        $devis = $em->getRepository(Devis::class)->find($id);

        if (!$devis) {
            throw $this->createNotFoundException('Le devis demandé n\'existe pas.');
        }
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('devis/devis_pdf.html.twig', [
            'devi' => $devis
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Envoyer le PDF au navigateur
        $dompdf->stream("devis_" . $devis->getId() . ".pdf", [
            "Attachment" => false 
        ]);

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    #[Route('/devis/send/pdf/{id}', name: 'app_devis_send_pdf')]
    public function sendDevisPdf(EntityManagerInterface $em, MailerInterface $mailer, Security $security, $id): Response
    {
        $devis = $em->getRepository(Devis::class)->find($id);

        if (!$devis) {
            throw $this->createNotFoundException('Le devis demandé n\'existe pas.');
        }
        
            // Obtenir l'utilisateur connecté
    $user = $security->getUser();
    // ou $user = $this->getUser();

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    // Assurez-vous que l'utilisateur a une adresse e-mail valide
    $userEmail = $user->getEmail();
    if (!$userEmail) {
        throw new \Exception('L\'utilisateur n\'a pas d\'adresse e-mail valide.');
    }


        // Générer le PDF
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('devis/devis_pdf.html.twig', ['devi' => $devis]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();

        // Créer l'e-mail
        $email = (new Email())
            ->from('ibrahim60200@gmail.com')
            ->to($userEmail)
            ->subject('Votre devis')
            ->html($this->renderView('devis/devis_pdf.html.twig', ['devi' => $devis]))
            ->attach($output, "devis_{$devis->getId()}.pdf", 'application/pdf');

        // Envoyer l'e-mail
        $mailer->send($email);

        $this->addFlash('success', 'Le devis a été envoyé par mail.');

        return $this->redirectToRoute('app_devis_index');
    }
}
