<?php

namespace Robertbaelde\PersistingMessageBus;

use EventSauce\Clock\Clock;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;
use Ramsey\Uuid\Uuid;

class DefaultMessageDecorator implements MessageDecorator
{
    public function __construct(protected Clock $clock)
    {
    }

    public function decorate(Message $message): Message
    {
        return $message->withHeaders([
            Header::MESSAGE_ID => Uuid::uuid4()->toString(),
        ])->withTimeOfRecording($this->clock->now());
    }
}
