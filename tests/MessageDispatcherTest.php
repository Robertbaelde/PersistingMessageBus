<?php

namespace Tests;

use EventSauce\Clock\SystemClock;
use PHPUnit\Framework\TestCase;
use Robertbaelde\PersistingMessageBus\DefaultMessageDecorator;
use Robertbaelde\PersistingMessageBus\MessageBus;
use Robertbaelde\PersistingMessageBus\MessageCouldNotBePublished;
use Robertbaelde\PersistingMessageBus\RawMessage;
use Tests\Fixtures\InMemoryMessageRepository;
use Tests\Stubs\MessageNotConfiguredOnTestTopic;
use Tests\Stubs\NullMessageDecorator;
use Tests\Stubs\SimpleDomainMessage;
use Tests\Stubs\TestTopic;

class MessageDispatcherTest extends TestCase
{
    private InMemoryMessageRepository $messageRepository;

    public function setUp(): void
    {
        $this->messageRepository = new InMemoryMessageRepository();
        parent::setUp();
    }

    /** @test */
    public function it_can_publish_a_message_on_the_message_bus()
    {
        $message = new SimpleDomainMessage('bar');

        $messageBus = new MessageBus(
            new TestTopic(),
            new DefaultMessageDecorator(new SystemClock()),
            $this->messageRepository
        );
        $messageBus->publish($message);

        $messages = $this->messageRepository->getMessages();
        $this->assertCount(1, $messages);
    }

    /** @test */
    public function it_throws_an_exception_when_message_type_is_not_configured_on_the_message_bus()
    {
        $message = new MessageNotConfiguredOnTestTopic('bar');

        $messageBus = new MessageBus(
            new TestTopic(),
            new NullMessageDecorator(),
            $this->messageRepository
        );

        $this->expectException(MessageCouldNotBePublished::class);

        $messageBus->publish($message);

        $messages = $this->messageRepository->getMessages();
        $this->assertCount(0, $messages);
    }

    /** @test */
    public function a_message_must_contain_a_message_id_header()
    {
        $message = new SimpleDomainMessage('bar');

        $messageBus = new MessageBus(
            new TestTopic(),
            new NullMessageDecorator(),
            $this->messageRepository
        );
        $this->expectException(MessageCouldNotBePublished::class);
        $messageBus->publish($message);

        $messages = $this->messageRepository->getMessages();
        $this->assertCount(1, $messages);
    }

    /** @test */
    public function a_message_gets_decorated_with_the_message_type_and_topic_name()
    {
        $message = new SimpleDomainMessage('bar');

        $messageBus = new MessageBus(
            new TestTopic(),
            new DefaultMessageDecorator(new SystemClock()),
            $this->messageRepository
        );
        $messageBus->publish($message);

        $messages = $this->messageRepository->getMessages();
        $this->assertCount(1, $messages);

        /** @var RawMessage $message */
        $message = $messages[0];
        $this->assertEquals('SimpleDomainMessage', $message->messageType);
        $this->assertEquals('TestTopic', $message->topic);
    }
}
