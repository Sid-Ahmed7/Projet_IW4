<?php

namespace App\Test\Controller;

use App\Entity\UserRoles;
use App\Repository\UserRolesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRolesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRolesRepository $repository;
    private string $path = '/user/roles/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(UserRoles::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UserRole index');

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
            'user_role[createdAt]' => 'Testing',
            'user_role[deletedAt]' => 'Testing',
            'user_role[slug]' => 'Testing',
            'user_role[updatedAt]' => 'Testing',
            'user_role[users]' => 'Testing',
            'user_role[company]' => 'Testing',
            'user_role[updatedBy]' => 'Testing',
            'user_role[role]' => 'Testing',
        ]);

        self::assertResponseRedirects('/user/roles/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new UserRoles();
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUsers('My Title');
        $fixture->setCompany('My Title');
        $fixture->setUpdatedBy('My Title');
        $fixture->setRole('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UserRole');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new UserRoles();
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUsers('My Title');
        $fixture->setCompany('My Title');
        $fixture->setUpdatedBy('My Title');
        $fixture->setRole('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user_role[createdAt]' => 'Something New',
            'user_role[deletedAt]' => 'Something New',
            'user_role[slug]' => 'Something New',
            'user_role[updatedAt]' => 'Something New',
            'user_role[users]' => 'Something New',
            'user_role[company]' => 'Something New',
            'user_role[updatedBy]' => 'Something New',
            'user_role[role]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/roles/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getDeletedAt());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getUsers());
        self::assertSame('Something New', $fixture[0]->getCompany());
        self::assertSame('Something New', $fixture[0]->getUpdatedBy());
        self::assertSame('Something New', $fixture[0]->getRole());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new UserRoles();
        $fixture->setCreatedAt('My Title');
        $fixture->setDeletedAt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUsers('My Title');
        $fixture->setCompany('My Title');
        $fixture->setUpdatedBy('My Title');
        $fixture->setRole('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/user/roles/');
    }
}
