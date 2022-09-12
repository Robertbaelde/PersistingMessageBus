<?php

namespace Tests;

use EventSauce\Clock\Clock;
use EventSauce\Clock\SystemClock;
use EventSauce\Clock\TestClock;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Robertbaelde\PersistingMessageBus\MessageRepository\IncrementalCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\MessageRepository;
use Robertbaelde\PersistingMessageBus\RawMessage;
use Symfony\Component\Console\Cursor;

abstract class IncrementalIdMessageRepositoryTestCase extends TestCase
{
    abstract public function getRepository(): MessageRepository;

    /** @test */
    public function it_can_persist_and_retrieve_messages()
    {
        $testClock = new TestClock();
        $testClock->fixate('2022-01-01 10:00:00');

        $rawMessage = $this->getRawMessage($testClock);

        $repository = $this->getRepository();
        $repository->persist($rawMessage);

        $paginatedMessages = $repository->getMessagesForTopic($rawMessage->topic, new IncrementalCursor());
        $this->assertCount(1, $paginatedMessages->messages);
        $this->assertEquals($rawMessage, $paginatedMessages->messages[0]);

        $this->assertEquals(new IncrementalCursor(1, 50), $paginatedMessages->cursor);

        $paginatedMessages = $repository->getMessagesForTopic($rawMessage->topic, $paginatedMessages->cursor);
        $this->assertCount(0, $paginatedMessages->messages);
    }

    /** @test */
    public function it_can_retrieve_messages_for_a_specific_topic()
    {
        $rawMessage = $this->getRawMessage();

        $repository = $this->getRepository();
        $repository->persist($rawMessage);

        $paginatedMessages = $repository->getMessagesForTopic('SomeRandomTopic', new IncrementalCursor());
        $this->assertCount(0, $paginatedMessages->messages);

        $paginatedMessages = $repository->getMessagesForTopic($rawMessage->topic, new IncrementalCursor());
        $this->assertCount(1, $paginatedMessages->messages);
    }

    /** @test */
    public function it_sorts_messages_on_provided_cursor()
    {
        $testClock = new TestClock();
        $testClock->fixate('2022-01-01 10:00:00');

        $rawMessage = $this->getRawMessage($testClock);
        $testClock->moveForward(new \DateInterval('PT1S'));
        $rawMessage2 = $this->getRawMessage($testClock);

        $repository = $this->getRepository();

        $repository->persist($rawMessage);
        $repository->persist($rawMessage2);

        $messages = $repository->getMessagesForTopic($rawMessage->topic, new IncrementalCursor());
        $this->assertCount(2, $messages->messages);
        $this->assertEquals($rawMessage, $messages->messages[0]);
        $this->assertEquals($rawMessage2, $messages->messages[1]);
    }

    /** @test */
    public function it_uses_offset_and_limit_of_cursor()
    {
        $testClock = new TestClock();
        $testClock->fixate('2022-01-01 10:00:00');

        $rawMessage = $this->getRawMessage($testClock);
        $rawMessage2 = $this->getRawMessage($testClock);

        $repository = $this->getRepository();

        $repository->persist($rawMessage);
        $repository->persist($rawMessage2);

        $paginatedMessages = $repository->getMessagesForTopic($rawMessage->topic, new IncrementalCursor(0, 1));
        $this->assertCount(1, $paginatedMessages->messages);
        $this->assertEquals($rawMessage, $paginatedMessages->messages[0]);

        $paginatedMessages = $repository->getMessagesForTopic($rawMessage->topic, $paginatedMessages->cursor);
        $this->assertCount(1, $paginatedMessages->messages);
        $this->assertEquals($rawMessage2, $paginatedMessages->messages[0]);
    }


    private function getRawMessage(?Clock $clock = null): RawMessage
    {
        $clock = $clock === null ? new SystemClock() : $clock;
        return new RawMessage(
            Uuid::uuid4()->toString(),
            'fooTopic',
            'someMessageType',
            'some message payload',
            'some header payload',
            $clock->now(),
        );
    }
}
