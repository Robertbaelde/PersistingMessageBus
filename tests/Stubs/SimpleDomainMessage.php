<?php

namespace Tests\Stubs;

use Robertbaelde\PersistingMessageBus\PublicMessage;

class SimpleDomainMessage implements PublicMessage
{
    public function __construct(public readonly string $foo)
    {
    }

    public function toPayload(): array
    {
        return [
            'foo' => $this->foo,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['foo']);
    }
}
