<?php

namespace App\Test\Controller;

use App\Entity\Roles;
use App\Repository\RolesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RolesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RolesRepository $repository;
    private string $path = '/roles/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Roles::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Role index');

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
            'role[rolename]' => 'Testing',
            'role[roleDescription]' => 'Testing',
            'role[createdAt]' => 'Testing',
            'role[deletedAt]' => 'Testing',
            'role[updatedAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/roles/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Roles();
        $fixture->setRolename('My Title');
        $fixture->setRoleDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Role');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Roles();
        $fixture->setRolename('My Title');
        $fixture->setRoleDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'role[rolename]' => 'Something New',
            'role[roleDescription]' => 'Something New',
            'role[createdAt]' => 'Something New',
            'role[deletedAt]' => 'Something New',
            'role[updatedAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/roles/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getRolename());
        self::assertSame('Something New', $fixture[0]->getRoleDescription());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getDeletedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Roles();
        $fixture->setRolename('My Title');
        $fixture->setRoleDescription('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setUpdatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/roles/');
    }
}
