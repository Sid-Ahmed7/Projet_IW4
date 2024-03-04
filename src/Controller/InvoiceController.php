<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\DevisRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Price;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }


    #[Route('/new/{id}', name: 'app_invoice_new', methods: ['GET'])]
    public function new(EntityManagerInterface $entityManager, DevisRepository $devisRepository, $id, InvoiceRepository $invoice ): Response
    {
        $devis = $devisRepository->find($id);

        if (!$devis) {
            throw $this->createNotFoundException('Le devis avec l\'id "' . $id . '" n\'existe pas.');
        }

         // Voir si une facture n'existe pas déja , je oense qu'il faut supprimer les factures existantes soit au bout d'un certain temps sans paiement soit si le devis est mofifié(meme si la facturation se fait une fois le devis validé)
         // mise en place du paiment en plusieurs fois ou trop compliqué ? are ca pour le moment
         $existingInvoice = $invoice->findOneBy(['devis' => $devis]);

    if ($existingInvoice) {
       // Il faut une page de redirection soit sur la page show de la facture existante 
        return $this->redirectToRoute('app_invoice_show', ['id' => $existingInvoice->getId()]);
    }

        $invoice = new Invoice();

        $invoice->setDevis($devis); // lier la facture au devis 
        $invoice->setState('pending');
        $invoice->setCreatedAt(new \DateTimeImmutable());

        // Initialisez et configurez Stripe ici...
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $price = Price::create([
            'unit_amount' => $devis->getPrice() * 100, // Le prix en centimes *100
            'currency' => 'eur', // La devise (ici l'euro)
            'product_data' => [
                'name' => $devis->getTitle(), // Le titre de l'annonce comme nom du produit
            ],
        ]);
        //ici on set toutes les données dont la facture à besoin dont l'ID de paiment construit en haut 
        $invoice->setStripePaymentID($price->id);
        $invoice->setPaymentType('carte');
        $entityManager->persist($invoice);
        $entityManager->flush();

        return $this->redirectToRoute('stripe', ['id' => $invoice->getId(),'devisID' => $devis->getId()]);
    }



    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
