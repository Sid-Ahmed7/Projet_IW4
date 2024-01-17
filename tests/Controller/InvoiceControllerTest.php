<?php

namespace App\Test\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvoiceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private InvoiceRepository $repository;
    private string $path = '/invoice/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Invoice::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Invoice index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'invoice[stripePaymentID]' => 'Testing',
            'invoice[paymentType]' => 'Testing',
            'invoice[Vat]' => 'Testing',
            'invoice[paymentDetails]' => 'Testing',
            'invoice[state]' => 'Testing',
            'invoice[updatedAt]' => 'Testing',
            'invoice[createdAt]' => 'Testing',
            'invoice[deletedAt]' => 'Testing',
            'invoice[slug]' => 'Testing',
            'invoice[devis]' => 'Testing',
        ]);

        self::assertResponseRedirects('/invoice/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Invoice();
        $fixture->setStripePaymentID('My Title');
        $fixture->setPaymentType('My Title');
        $fixture->setVat('My Title');
        $fixture->setPaymentDetails('My Title');
        $fixture->setState('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setDevis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Invoice');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Invoice();
        $fixture->setStripePaymentID('My Title');
        $fixture->setPaymentType('My Title');
        $fixture->setVat('My Title');
        $fixture->setPaymentDetails('My Title');
        $fixture->setState('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setDevis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'invoice[stripePaymentID]' => 'Something New',
            'invoice[paymentType]' => 'Something New',
            'invoice[Vat]' => 'Something New',
            'invoice[paymentDetails]' => 'Something New',
            'invoice[state]' => 'Something New',
            'invoice[updatedAt]' => 'Something New',
            'invoice[createdAt]' => 'Something New',
            'invoice[deletedAt]' => 'Something New',
            'invoice[slug]' => 'Something New',
            'invoice[devis]' => 'Something New',
        ]);

        self::assertResponseRedirects('/invoice/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getStripePaymentID());
        self::assertSame('Something New', $fixture[0]->getPaymentType());
        self::assertSame('Something New', $fixture[0]->getVat());
        self::assertSame('Something New', $fixture[0]->getPaymentDetails());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getDeletedAt());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getDevis());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Invoice();
        $fixture->setStripePaymentID('My Title');
        $fixture->setPaymentType('My Title');
        $fixture->setVat('My Title');
        $fixture->setPaymentDetails('My Title');
        $fixture->setState('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setDevis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/invoice/');
    }
}
