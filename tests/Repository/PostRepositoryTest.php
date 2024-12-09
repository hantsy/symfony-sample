<?php

namespace App\Tests\Repository;

use App\Entity\PostFactory;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostRepositoryTest extends  RepositoryTestBase// KernelTestCase
{

   // private EntityManagerInterface $entityManager;

    private PostRepository $postRepository;

    protected function setUp(): void
    {
        // (1) boot the Symfony kernel
//        $kernel = self::bootKernel();
//        $this->assertSame('test', $kernel->getEnvironment());
//        $this->entityManager = $kernel->getContainer()
//            ->get('doctrine')
//            ->getManager();

        parent::setUp();

        //(2) use static::getContainer() to access the service container
        $container = static::getContainer();

        //(3) get PostRepository from container.
        $this->postRepository = $container->get(PostRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

    }


    public function testCreatePost(): void
    {
        // persist entities with EntityManager
        $entity = PostFactory::create("test post", "test content");
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->assertNotNull($entity->getId());

        // query this post by PostRepository
        $byId = $this->postRepository->findOneBy(["id" => $entity->getId()]);
        $this->assertEquals("test post", $byId->getTitle());
        $this->assertEquals("test content", $byId->getContent());
    }

}
