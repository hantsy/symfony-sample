<?php

namespace App\Tests\Repository;

use App\Entity\Post;
use App\Entity\PostFactory;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Testcontainers\Container\PostgresContainer;

class PostRepositoryTestWithTestConnectionFactory extends KernelTestCase
{

    private EntityManagerInterface $entityManager;

    private PostRepository $postRepository;

    protected function setUp(): void
    {
        // boot the Symfony kernel
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $application = new Application($kernel);
        $command = $application->find('doctrine:schema:create');
        $commandTester = new CommandTester($command);
        $result = $commandTester->execute(['n']);
        var_dump("schema:create result:".$result);

        // use static::getContainer() to access the service container
        // $container = static::getContainer();

        // get PostRepository from container.
        // $this->postRepository = $container->get(PostRepository::class);
        // instead get repository from entity manager directly
        $this->postRepository = $this->entityManager->getRepository(Post::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
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
