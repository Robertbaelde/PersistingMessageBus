<?php

namespace Tests\Fixtures;

use Robertbaelde\PersistingMessageBus\MessageConsumerState;
use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;

class InMemoryMessageConsumerState implements MessageConsumerState
{

    private Cursor $cursor;

    public function getCursor(): ?Cursor
    {
        return $this->cursor ?? null;
    }

    public function storeNewCursor(Cursor $cursor): void
    {
        $this->cursor = $cursor;
    }
}
