<?php
// src/Controller/InvoicePdfController.php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoicePdfController extends AbstractController
{

    #[Route('/invoice/pdf/{id}', name: 'app_invoice_pdf', methods: ['GET'])]
    public function generateInvoicePdf(EntityManagerInterface $em, $id): Response
    {

        $invoice = $em->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            throw $this->createNotFoundException('Le invoice demandé n\'existe pas.');
        }
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('invoice/invoice_pdf.html.twig', [
            'invo' => $invoice
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Envoyer le PDF au navigateur
        $dompdf->stream("invoice_" . $invoice->getId() . ".pdf", [
            "Attachment" => false 
        ]);

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
