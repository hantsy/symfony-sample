<?php

namespace App\Tests;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\DsnParser;
use Testcontainers\Container\PostgresContainer;

class TestConnectionFactory extends ConnectionFactory
{
    static string $testDsn;

    public function __construct(array $typesConfig, ?DsnParser $dsnParser = null)
    {
        if (!$this::$testDsn) {
            $psql = PostgresContainer::make('16.0', 'password');
            $psql->withPostgresDatabase('testdb');
            $psql->withPostgresUser('user');
            $psql->run();
            $this::$testDsn = sprintf('postgresql://user:password@%s:5432/testdb?serverVersion=16&charset=utf8', $psql->getAddress());
        }
        parent::__construct($typesConfig, $dsnParser);
    }


    public function createConnection(array $params, ?Configuration $config = null, ?EventManager $eventManager = null, array $mappingTypes = []): Connection
    {
        $params['url'] = $this::$testDsn;
        return parent::createConnection($params, $config, $eventManager, $mappingTypes);
    }

}