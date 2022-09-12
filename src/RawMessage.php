<?php

namespace Robertbaelde\PersistingMessageBus;

class RawMessage
{
    public function __construct(
        public readonly string $messageId,
        public readonly string $topic,
        public readonly string $messageType,
        public readonly string $messagePayload,
        public readonly string $headerPayload,
        public readonly \DateTimeImmutable $publishedAt,
        public readonly ?string $publishedAtFormat = null,
    ) {

    }

}
