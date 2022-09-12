<?php

namespace Tests;

use EventSauce\Clock\Clock;
use EventSauce\Clock\SystemClock;
use EventSauce\Clock\TestClock;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Robertbaelde\PersistingMessageBus\MessageRepository;
use Robertbaelde\PersistingMessageBus\RawMessage;

abstract class MessageRepositoryTestCase extends TestCase
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

        $messages = $repository->getMessagesForTopic($rawMessage->topic);
        $this->assertCount(1, $messages);
        $this->assertEquals($rawMessage, $messages[0]);
    }

    /** @test */
    public function it_can_retrieve_messages_for_a_specific_topic()
    {
        $rawMessage = $this->getRawMessage();

        $repository = $this->getRepository();
        $repository->persist($rawMessage);

        $messages = $repository->getMessagesForTopic('SomeRandomTopic');
        $this->assertCount(0, $messages);

        $messages = $repository->getMessagesForTopic($rawMessage->topic);
        $this->assertCount(1, $messages);
    }

    /** @test */
    public function it_sorts_messages_on_date()
    {
        $testClock = new TestClock();
        $testClock->fixate('2022-01-01 10:00:00');

        $rawMessage = $this->getRawMessage($testClock);
        $testClock->moveForward(new \DateInterval('PT1S'));
        $rawMessage2 = $this->getRawMessage($testClock);

        $repository = $this->getRepository();

        $repository->persist($rawMessage2);
        $repository->persist($rawMessage);

        $messages = $repository->getMessagesForTopic($rawMessage->topic);
        $this->assertCount(2, $messages);
        $this->assertEquals($rawMessage, $messages[0]);
        $this->assertEquals($rawMessage2, $messages[1]);
    }

    /** @test */
    public function it_filters_based_on_time()
    {
        $testClock = new TestClock();
        $testClock->fixate('2022-01-01 10:00:00');

        $rawMessage = $this->getRawMessage($testClock);
        $testClock->moveForward(new \DateInterval('PT1S'));
        $rawMessage2 = $this->getRawMessage($testClock);

        $repository = $this->getRepository();

        $repository->persist($rawMessage2);
        $repository->persist($rawMessage);

        $messages = $repository->getMessagesForTopic($rawMessage->topic, $testClock->now());
        $this->assertCount(1, $messages);
        $this->assertEquals($rawMessage2, $messages[0]);
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
