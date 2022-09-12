<?php

namespace Tests;

use DateTimeImmutable;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;
use Robertbaelde\PersistingMessageBus\Header;
use Robertbaelde\PersistingMessageBus\PublicMessage;
use Robertbaelde\PersistingMessageBus\Topic;
use Robertbaelde\PersistingMessageBus\TopicMessageSerializer;
use Tests\Stubs\SimpleDomainMessage;
use Tests\Stubs\TestTopic;

class TopicMessageSerializerTest extends TestCase
{
    /**
     * @test
     * @dataProvider providesMessages
     */
    public function it_can_serialise_and_deserialize(PublicMessage $publicMessage, Topic $topic)
    {
        $topicSerializer = new TopicMessageSerializer($topic);
        $message = new Message($publicMessage, [
            Header::MESSAGE_ID => 'foo'
        ]);
        $message = $message->withTimeOfRecording(new DateTimeImmutable('now'));
        $rawMessage = $topicSerializer->serializeMessage($message);

        $reconstructedMessage = $topicSerializer->unserializePayload($rawMessage);
        $this->assertEquals($publicMessage, $reconstructedMessage->payload());
    }

    public function providesMessages(): \Generator
    {
        yield [new SimpleDomainMessage('foo'), new TestTopic()];
    }
}
