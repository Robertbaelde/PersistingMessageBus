<?php

namespace Robertbaelde\PersistingMessageBus\MessageRepository;

final class IncrementalCursor implements Cursor
{
    public function __construct(
        private int $offset = 0,
        private int $limit = 50,
    )
    {
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function nextPage(int $resultCount): self
    {
        return new self(offset: $this->offset + $resultCount, limit: $this->limit);
    }

    public function toPayload(): array
    {
        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['offset'], $payload['limit']);
    }

    public function isAtStart(): bool
    {
        return $this->offset === 0;
    }
}
