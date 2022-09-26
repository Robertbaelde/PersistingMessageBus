<?php

namespace Robertbaelde\PersistingMessageBus\MessageRepository;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

interface Cursor extends SerializablePayload
{
    public function isAtStart(): bool;
}
