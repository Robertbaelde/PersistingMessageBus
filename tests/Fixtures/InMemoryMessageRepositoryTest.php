<?php

namespace Tests\Fixtures;

use Robertbaelde\PersistingMessageBus\MessageRepository\MessageRepository;
use Tests\IncrementalIdMessageRepositoryTestCase;
use Tests\MessageRepositoryTestCase;

class InMemoryMessageRepositoryTest extends IncrementalIdMessageRepositoryTestCase
{
    private InMemoryMessageRepository $messageRepository;

    public function setUp(): void
    {
        $this->messageRepository = new InMemoryMessageRepository();
        parent::setUp();
    }

    public function getRepository(): MessageRepository
    {
        return $this->messageRepository;
    }
}
