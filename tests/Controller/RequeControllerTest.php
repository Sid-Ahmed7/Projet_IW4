<?php

namespace App\Test\Controller;

use App\Entity\Reque;
use App\Repository\RequeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RequeRepository $repository;
    private string $path = '/reque/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Reque::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reque index');

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
            'reque[eventdate]' => 'Testing',
            'reque[createdAt]' => 'Testing',
            'reque[operatorID]' => 'Testing',
            'reque[eventLocation]' => 'Testing',
            'reque[eventCountry]' => 'Testing',
            'reque[eventCity]' => 'Testing',
            'reque[eventCode]' => 'Testing',
            'reque[lastame]' => 'Testing',
            'reque[firstname]' => 'Testing',
            'reque[phoneNumber]' => 'Testing',
            'reque[maxBudget]' => 'Testing',
            'reque[state]' => 'Testing',
            'reque[usr]' => 'Testing',
            'reque[company]' => 'Testing',
        ]);

        self::assertResponseRedirects('/reque/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reque();
        $fixture->setEventdate('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setOperatorID('My Title');
        $fixture->setEventLocation('My Title');
        $fixture->setEventCountry('My Title');
        $fixture->setEventCity('My Title');
        $fixture->setEventCode('My Title');
        $fixture->setLastame('My Title');
        $fixture->setFirstname('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setMaxBudget('My Title');
        $fixture->setState('My Title');
        $fixture->setUsr('My Title');
        $fixture->setCompany('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Reque');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Reque();
        $fixture->setEventdate('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setOperatorID('My Title');
        $fixture->setEventLocation('My Title');
        $fixture->setEventCountry('My Title');
        $fixture->setEventCity('My Title');
        $fixture->setEventCode('My Title');
        $fixture->setLastame('My Title');
        $fixture->setFirstname('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setMaxBudget('My Title');
        $fixture->setState('My Title');
        $fixture->setUsr('My Title');
        $fixture->setCompany('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'reque[eventdate]' => 'Something New',
            'reque[createdAt]' => 'Something New',
            'reque[operatorID]' => 'Something New',
            'reque[eventLocation]' => 'Something New',
            'reque[eventCountry]' => 'Something New',
            'reque[eventCity]' => 'Something New',
            'reque[eventCode]' => 'Something New',
            'reque[lastame]' => 'Something New',
            'reque[firstname]' => 'Something New',
            'reque[phoneNumber]' => 'Something New',
            'reque[maxBudget]' => 'Something New',
            'reque[state]' => 'Something New',
            'reque[usr]' => 'Something New',
            'reque[company]' => 'Something New',
        ]);

        self::assertResponseRedirects('/reque/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getEventdate());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getOperatorID());
        self::assertSame('Something New', $fixture[0]->getEventLocation());
        self::assertSame('Something New', $fixture[0]->getEventCountry());
        self::assertSame('Something New', $fixture[0]->getEventCity());
        self::assertSame('Something New', $fixture[0]->getEventCode());
        self::assertSame('Something New', $fixture[0]->getLastame());
        self::assertSame('Something New', $fixture[0]->getFirstname());
        self::assertSame('Something New', $fixture[0]->getPhoneNumber());
        self::assertSame('Something New', $fixture[0]->getMaxBudget());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getUsr());
        self::assertSame('Something New', $fixture[0]->getCompany());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Reque();
        $fixture->setEventdate('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setOperatorID('My Title');
        $fixture->setEventLocation('My Title');
        $fixture->setEventCountry('My Title');
        $fixture->setEventCity('My Title');
        $fixture->setEventCode('My Title');
        $fixture->setLastame('My Title');
        $fixture->setFirstname('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setMaxBudget('My Title');
        $fixture->setState('My Title');
        $fixture->setUsr('My Title');
        $fixture->setCompany('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/reque/');
    }
}
