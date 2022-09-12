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

        $messageConsumer = new MessageBusConsumer(
            $this->messageBus,
            $consumers,
//            $messageConsumerStateRepository
        );

        while(true){
            $messageConsumer->handleNewMessages();
        }
    }

    private function givenAMessageOnTheBus()
    {
        $message = new SimpleDomainMessage('bar');



        $messageDispatcher = new MessageDispatcher(
            $messageBus,
            new DefaultMessageDecorator(new SystemClock()),
        );

        $messageDispatcher->dispatch($message);
    }
}
