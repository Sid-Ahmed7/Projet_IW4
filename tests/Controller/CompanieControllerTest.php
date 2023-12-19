<?php

namespace App\Test\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompanyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CompanyRepository $repository;
    private string $path = '/Company/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Company::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Company index');

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
            'Company[name]' => 'Testing',
            'Company[address]' => 'Testing',
            'Company[logo]' => 'Testing',
            'Company[banner]' => 'Testing',
            'Company[email]' => 'Testing',
            'Company[phoneNumber]' => 'Testing',
            'Company[taxNumber]' => 'Testing',
            'Company[siretNumber]' => 'Testing',
            'Company[likes]' => 'Testing',
            'Company[updatedAt]' => 'Testing',
            'Company[createdAt]' => 'Testing',
            'Company[state]' => 'Testing',
            'Company[uuid]' => 'Testing',
            'Company[slug]' => 'Testing',
        ]);

        self::assertResponseRedirects('/Company/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Company();
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
        self::assertPageTitleContains('Company');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Company();
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
            'Company[name]' => 'Something New',
            'Company[address]' => 'Something New',
            'Company[logo]' => 'Something New',
            'Company[banner]' => 'Something New',
            'Company[email]' => 'Something New',
            'Company[phoneNumber]' => 'Something New',
            'Company[taxNumber]' => 'Something New',
            'Company[siretNumber]' => 'Something New',
            'Company[likes]' => 'Something New',
            'Company[updatedAt]' => 'Something New',
            'Company[createdAt]' => 'Something New',
            'Company[state]' => 'Something New',
            'Company[uuid]' => 'Something New',
            'Company[slug]' => 'Something New',
        ]);

        self::assertResponseRedirects('/Company/');

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

        $fixture = new Company();
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
        self::assertResponseRedirects('/Company/');
    }
}
