<?php

require '/var/www/symfony/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv('/var/www/symfony/.env');

$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();

// Créer un devis pour l'entreprise ESGI (ID: 1) et l'utilisateur company (ID: 3)
$devis = new \App\Entity\Devis();
$devis->setTitle('Séminaire Entreprise');
$devis->setContent('Organisation du séminaire annuel de l\'entreprise');
$devis->setPrice('15000.00');
$devis->setState('En attente');
$devis->setIsNegotiable(false);
$devis->setCreatedAt(new \DateTimeImmutable());

// Récupérer les entités
$company = $em->getRepository(\App\Entity\Company::class)->find(1);
$user = $em->getRepository(\App\Entity\User::class)->find(3);

$devis->setCompany($company);
$devis->setHubuser($user);

$em->persist($devis);
$em->flush();

echo "Devis créé avec succès!\n";

// Créer une facture pour tester
$invoice = new \App\Entity\Invoice();
$invoice->setNumber('202508-TEST-' . uniqid());
$invoice->setAmount(15000);
$invoice->setStatus('pending');
$invoice->setCreatedAt(new \DateTimeImmutable());
$invoice->setCompany($company);
$invoice->setHubuser($user);
$invoice->setDevis($devis);

$em->persist($invoice);
$em->flush();

echo "Facture créée avec succès!\n";
