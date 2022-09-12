<?php

namespace Tests\Laravel;

use Illuminate\Database\Capsule\Manager;
use PHPUnit\Framework\TestCase;
use Robertbaelde\PersistingMessageBus\Laravel\IlluminateMessageConsumerStateRepository;
use Robertbaelde\PersistingMessageBus\Laravel\MessageConsumerStateRepository;
use Robertbaelde\PersistingMessageBus\MessageRepository\IncrementalCursor;

class IlluminateMessageConsumerStateRepositoryTest extends TestCase
{
    private $tableName = 'message_consumer_state';
    private \Illuminate\Database\Connection $connection;
    private IlluminateMessageConsumerStateRepository $repository;

    public function setUp(): void
    {
        $manager = new Manager;
        $manager->addConnection(
            [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'message_bus_test',
                'username' => 'root',
                'password' => 'secret',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
            ]
        );

        $this->connection = $manager->getConnection();
        $this->connection->table($this->tableName)->truncate();
        parent::setUp();

        $this->repository = new IlluminateMessageConsumerStateRepository(
            $this->connection,
            $this->tableName,
            'test_consumer'
        );
    }

    /** @test */
    public function it_returns_null_when_no_cursor_in_database()
    {
        $this->assertNull($this->repository->getCursor());
    }

    /** @test */
    public function it_can_persist_and_retrieve_cursor()
    {
        $cursor = new IncrementalCursor(4, 5);
        $this->repository->storeNewCursor($cursor);
        $this->assertEquals($cursor, $this->repository->getCursor());
    }
}
