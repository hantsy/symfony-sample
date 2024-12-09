<?php

namespace App\Tests\Repository;

use App\Entity\PostFactory;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Testcontainers\Container\PostgresContainer;

class RepositoryTestBase extends KernelTestCase
{

    protected EntityManagerInterface $entityManager;

    protected Container $container;

    private static PostgresContainer $postgresContainer;

    public static function setUpBeforeClass(): void{
        parent::setUpBeforeClass();
        // starting Postgres Container
        self::$postgresContainer = PostgresContainer::make('16', 'password');
        self::$postgresContainer->withPostgresDatabase('blogdb');
        self::$postgresContainer->withPostgresUser('user');
        self::$postgresContainer->withPort("5432", "5432");

        try {
            self::$postgresContainer->run();
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public static function tearDownAfterClass(): void{
        parent::tearDownAfterClass();
        self::$postgresContainer->stop();
    }

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
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
    }

}
