<?php

namespace App\Test\Controller;

use App\Entity\Companie;
use App\Repository\CompanieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompanieControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CompanieRepository $repository;
    private string $path = '/companie/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Companie::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Companie index');

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
            'companie[name]' => 'Testing',
            'companie[address]' => 'Testing',
            'companie[logo]' => 'Testing',
            'companie[banner]' => 'Testing',
            'companie[email]' => 'Testing',
            'companie[phoneNumber]' => 'Testing',
            'companie[taxNumber]' => 'Testing',
            'companie[siretNumber]' => 'Testing',
            'companie[likes]' => 'Testing',
            'companie[updatedAt]' => 'Testing',
            'companie[createdAt]' => 'Testing',
            'companie[state]' => 'Testing',
            'companie[uuid]' => 'Testing',
            'companie[slug]' => 'Testing',
        ]);

        self::assertResponseRedirects('/companie/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Companie();
        $fixture->setName('My Title');
        $fixture->setAddress('My Title');
        $fixture->setLogo('My Title');
        $fixture->setBanner('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setTaxNumber('My Title');
        $fixture->setSiretNumber('My Title');
        $fixture->setLikes('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setState('My Title');
        $fixture->setUuid('My Title');
        $fixture->setSlug('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Companie');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Companie();
        $fixture->setName('My Title');
        $fixture->setAddress('My Title');
        $fixture->setLogo('My Title');
        $fixture->setBanner('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setTaxNumber('My Title');
        $fixture->setSiretNumber('My Title');
        $fixture->setLikes('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setState('My Title');
        $fixture->setUuid('My Title');
        $fixture->setSlug('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'companie[name]' => 'Something New',
            'companie[address]' => 'Something New',
            'companie[logo]' => 'Something New',
            'companie[banner]' => 'Something New',
            'companie[email]' => 'Something New',
            'companie[phoneNumber]' => 'Something New',
            'companie[taxNumber]' => 'Something New',
            'companie[siretNumber]' => 'Something New',
            'companie[likes]' => 'Something New',
            'companie[updatedAt]' => 'Something New',
            'companie[createdAt]' => 'Something New',
            'companie[state]' => 'Something New',
            'companie[uuid]' => 'Something New',
            'companie[slug]' => 'Something New',
        ]);

        self::assertResponseRedirects('/companie/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getLogo());
        self::assertSame('Something New', $fixture[0]->getBanner());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPhoneNumber());
        self::assertSame('Something New', $fixture[0]->getTaxNumber());
        self::assertSame('Something New', $fixture[0]->getSiretNumber());
        self::assertSame('Something New', $fixture[0]->getLikes());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getUuid());
        self::assertSame('Something New', $fixture[0]->getSlug());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Companie();
        $fixture->setName('My Title');
        $fixture->setAddress('My Title');
        $fixture->setLogo('My Title');
        $fixture->setBanner('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setTaxNumber('My Title');
        $fixture->setSiretNumber('My Title');
        $fixture->setLikes('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setState('My Title');
        $fixture->setUuid('My Title');
        $fixture->setSlug('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/companie/');
    }
}
