<?php

namespace App\Test\Controller;

use App\Entity\Request;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RequestRepository $repository;
    private string $path = '/request/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Request::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Request index');

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
            'request[category]' => 'Testing',
            'request[description]' => 'Testing',
            'request[status]' => 'Testing',
            'request[uuid]' => 'Testing',
            'request[createdAt]' => 'Testing',
            'request[evenDate]' => 'Testing',
            'request[response]' => 'Testing',
            'request[devisAmont]' => 'Testing',
            'request[eventLocation]' => 'Testing',
            'request[maxBudget]' => 'Testing',
            'request[slug]' => 'Testing',
            'request[users]' => 'Testing',
            'request[companie]' => 'Testing',
        ]);

        self::assertResponseRedirects('/request/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Request();
        $fixture->setCategory('My Title');
        $fixture->setDescription('My Title');
        $fixture->setStatus('My Title');
        $fixture->setUuid('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setEvenDate('My Title');
        $fixture->setResponse('My Title');
        $fixture->setDevisAmont('My Title');
        $fixture->setEventLocation('My Title');
        $fixture->setMaxBudget('My Title');
        $fixture->setSlug('My Title');
        $fixture->setUsers('My Title');
        $fixture->setCompanie('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Request');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Request();
        $fixture->setCategory('My Title');
        $fixture->setDescription('My Title');
        $fixture->setStatus('My Title');
        $fixture->setUuid('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setEvenDate('My Title');
        $fixture->setResponse('My Title');
        $fixture->setDevisAmont('My Title');
        $fixture->setEventLocation('My Title');
        $fixture->setMaxBudget('My Title');
        $fixture->setSlug('My Title');
        $fixture->setUsers('My Title');
        $fixture->setCompanie('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'request[category]' => 'Something New',
            'request[description]' => 'Something New',
            'request[status]' => 'Something New',
            'request[uuid]' => 'Something New',
            'request[createdAt]' => 'Something New',
            'request[evenDate]' => 'Something New',
            'request[response]' => 'Something New',
            'request[devisAmont]' => 'Something New',
            'request[eventLocation]' => 'Something New',
            'request[maxBudget]' => 'Something New',
            'request[slug]' => 'Something New',
            'request[users]' => 'Something New',
            'request[companie]' => 'Something New',
        ]);

        self::assertResponseRedirects('/request/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCategory());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getUuid());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getEvenDate());
        self::assertSame('Something New', $fixture[0]->getResponse());
        self::assertSame('Something New', $fixture[0]->getDevisAmont());
        self::assertSame('Something New', $fixture[0]->getEventLocation());
        self::assertSame('Something New', $fixture[0]->getMaxBudget());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getUsers());
        self::assertSame('Something New', $fixture[0]->getCompanie());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Request();
        $fixture->setCategory('My Title');
        $fixture->setDescription('My Title');
        $fixture->setStatus('My Title');
        $fixture->setUuid('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setEvenDate('My Title');
        $fixture->setResponse('My Title');
        $fixture->setDevisAmont('My Title');
        $fixture->setEventLocation('My Title');
        $fixture->setMaxBudget('My Title');
        $fixture->setSlug('My Title');
        $fixture->setUsers('My Title');
        $fixture->setCompanie('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/request/');
    }
}
