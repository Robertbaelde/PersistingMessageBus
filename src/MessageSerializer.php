<?php

namespace Robertbaelde\PersistingMessageBus;

use EventSauce\EventSourcing\Message;

interface MessageSerializer
{
    public function serializeMessage(Message $message): RawMessage;

    public function unserializePayload(RawMessage $payload): Message;
}
