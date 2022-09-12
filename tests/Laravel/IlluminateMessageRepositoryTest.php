<?php

namespace Tests\Laravel;

use Illuminate\Database\Capsule\Manager;
use Robertbaelde\PersistingMessageBus\DefaultTableSchema;
use Robertbaelde\PersistingMessageBus\Laravel\IlluminateMessageRepository;
use Robertbaelde\PersistingMessageBus\MessageRepository\MessageRepository;
use Tests\IncrementalIdMessageRepositoryTestCase;

class IlluminateMessageRepositoryTest extends IncrementalIdMessageRepositoryTestCase
{
    private $tableName = 'public_messages';
    private \Illuminate\Database\Connection $connection;

    public function setUp(): void
    {
        $manager = new Manager;
        $manager->addConnection(
            [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'message_bus_test',
                'username' => 'username',
                'password' => 'password',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
            ]
        );

        $this->connection = $manager->getConnection();
        $this->connection->table($this->tableName)->truncate();
        parent::setUp();
    }

    public function getRepository(): MessageRepository
    {
        return new IlluminateMessageRepository(
            $this->connection,
            $this->tableName,
            new DefaultTableSchema()
        );
    }
}
