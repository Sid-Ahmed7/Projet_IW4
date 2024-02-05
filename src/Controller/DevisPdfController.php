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
}
