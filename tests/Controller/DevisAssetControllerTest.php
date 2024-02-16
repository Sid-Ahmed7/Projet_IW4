<?php

namespace App\Test\Controller;

use App\Entity\DevisAsset;
use App\Repository\DevisAssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DevisAssetControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DevisAssetRepository $repository;
    private string $path = '/devis/asset/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(DevisAsset::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DevisAsset index');

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
            'devis_asset[name]' => 'Testing',
            'devis_asset[price]' => 'Testing',
            'devis_asset[createdAt]' => 'Testing',
            'devis_asset[updatedAt]' => 'Testing',
            'devis_asset[state]' => 'Testing',
            'devis_asset[size]' => 'Testing',
            'devis_asset[description]' => 'Testing',
            'devis_asset[picture1]' => 'Testing',
            'devis_asset[picture2]' => 'Testing',
            'devis_asset[picture3]' => 'Testing',
            'devis_asset[devis]' => 'Testing',
        ]);

        self::assertResponseRedirects('/devis/asset/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new DevisAsset();
        $fixture->setName('My Title');
        $fixture->setPrice('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setState('My Title');
        $fixture->setSize('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPicture1('My Title');
        $fixture->setPicture2('My Title');
        $fixture->setPicture3('My Title');
        $fixture->setDevis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DevisAsset');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new DevisAsset();
        $fixture->setName('My Title');
        $fixture->setPrice('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setState('My Title');
        $fixture->setSize('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPicture1('My Title');
        $fixture->setPicture2('My Title');
        $fixture->setPicture3('My Title');
        $fixture->setDevis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'devis_asset[name]' => 'Something New',
            'devis_asset[price]' => 'Something New',
            'devis_asset[createdAt]' => 'Something New',
            'devis_asset[updatedAt]' => 'Something New',
            'devis_asset[state]' => 'Something New',
            'devis_asset[size]' => 'Something New',
            'devis_asset[description]' => 'Something New',
            'devis_asset[picture1]' => 'Something New',
            'devis_asset[picture2]' => 'Something New',
            'devis_asset[picture3]' => 'Something New',
            'devis_asset[devis]' => 'Something New',
        ]);

        self::assertResponseRedirects('/devis/asset/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getSize());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getPicture1());
        self::assertSame('Something New', $fixture[0]->getPicture2());
        self::assertSame('Something New', $fixture[0]->getPicture3());
        self::assertSame('Something New', $fixture[0]->getDevis());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new DevisAsset();
        $fixture->setName('My Title');
        $fixture->setPrice('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setState('My Title');
        $fixture->setSize('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPicture1('My Title');
        $fixture->setPicture2('My Title');
        $fixture->setPicture3('My Title');
        $fixture->setDevis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/devis/asset/');
    }
}
