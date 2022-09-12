<?php

namespace Tests\Fixtures;

use Robertbaelde\PersistingMessageBus\MessageRepository;
use Tests\MessageRepositoryTestCase;

class InMemoryMessageRepositoryTest extends MessageRepositoryTestCase
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
