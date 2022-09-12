<?php

namespace Robertbaelde\PersistingMessageBus\MessageRepository;

class PaginatedMessages
{
    public function __construct(
        public readonly array $messages,
        public readonly Cursor $cursor
    ) {
    }
}
