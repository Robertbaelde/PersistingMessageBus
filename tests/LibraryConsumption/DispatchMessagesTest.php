<?php

namespace Tests\LibraryConsumption;

use EventSauce\Clock\SystemClock;
use PHPUnit\Framework\TestCase;
use Robertbaelde\PersistingMessageBus\DefaultMessageDecorator;
use Robertbaelde\PersistingMessageBus\MessageBus;
use Robertbaelde\PersistingMessageBus\MessageDispatcher;
use Tests\Fixtures\InMemoryMessageRepository;
use Tests\Stubs\SimpleDomainMessage;
use Tests\Stubs\TestTopic;

class DispatchMessagesTest extends TestCase
{
    private InMemoryMessageRepository $messageRepository;

    public function setUp(): void
    {
        $this->messageRepository = new InMemoryMessageRepository();
        parent::setUp();
    }

    /** @test */
    public function a_message_can_be_dispatched_on_the_bus()
    {
        $message = new SimpleDomainMessage('bar');

        $messageBus = new MessageBus(
            new TestTopic(),
            $this->messageRepository
        );

        $messageDispatcher = new MessageDispatcher(
            $messageBus,
            new DefaultMessageDecorator(new SystemClock()),
        );

        $messageDispatcher->dispatch($message);

        $messages = $this->messageRepository->getMessagesForTopic((new TestTopic())->getName());
        $this->assertCount(1, $messages);
    }
}
