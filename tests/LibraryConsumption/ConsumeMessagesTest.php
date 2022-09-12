<?php

namespace Tests\LibraryConsumption;

use EventSauce\Clock\SystemClock;
use EventSauce\EventSourcing\CollectingMessageConsumer;
use PHPUnit\Framework\TestCase;
use Robertbaelde\PersistingMessageBus\DefaultMessageDecorator;
use Robertbaelde\PersistingMessageBus\MessageBus;
use Robertbaelde\PersistingMessageBus\MessageConsumer;
use Robertbaelde\PersistingMessageBus\MessageDispatcher;
use Tests\Fixtures\InMemoryMessageConsumerState;
use Tests\Fixtures\InMemoryMessageRepository;
use Tests\Stubs\SimpleDomainMessage;
use Tests\Stubs\TestTopic;

class ConsumeMessagesTest extends TestCase
{
    private InMemoryMessageRepository $messageRepository;
    private MessageBus $messageBus;

    public function setUp(): void
    {
        $this->messageRepository = new InMemoryMessageRepository();

        $this->messageBus = new MessageBus(
            new TestTopic(),
            $this->messageRepository
        );
        parent::setUp();
    }

    /** @test */
    public function messages_can_be_consumed()
    {
        $this->givenAMessageOnTheBus();

        $collectionConsumer = new CollectingMessageConsumer();

        $messageConsumer = new MessageConsumer(
            messageBus: $this->messageBus,
            messageConsumerState: new InMemoryMessageConsumerState(),
            messageConsumer: $collectionConsumer
        );

        $messageConsumer->handleNewMessages();

        $this->assertCount(1, $collectionConsumer->collectedMessages());
        $this->assertInstanceOf(SimpleDomainMessage::class, $collectionConsumer->collectedMessages()[0]->payload());
    }

    /** @test */
    public function message_will_resume_consuming()
    {
        $this->givenAMessageOnTheBus();

        $collectionConsumer = new CollectingMessageConsumer();

        $messageConsumer = new MessageConsumer(
            messageBus: $this->messageBus,
            messageConsumerState: new InMemoryMessageConsumerState(),
            messageConsumer: $collectionConsumer
        );

        $messageConsumer->handleNewMessages();

        $this->assertCount(1, $collectionConsumer->collectedMessages());

        $this->givenAMessageOnTheBus();
        $messageConsumer->handleNewMessages();

        $this->assertCount(2, $collectionConsumer->collectedMessages());
    }

    private function givenAMessageOnTheBus()
    {
        $message = new SimpleDomainMessage('bar');

        $messageDispatcher = new MessageDispatcher(
            $this->messageBus,
            new DefaultMessageDecorator(new SystemClock()),
        );

        $messageDispatcher->dispatch($message);
    }
}
