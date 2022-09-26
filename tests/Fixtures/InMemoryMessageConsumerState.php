<?php

namespace Tests\Fixtures;

use Robertbaelde\PersistingMessageBus\MessageConsumerState;
use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\IncrementalCursor;

class InMemoryMessageConsumerState implements MessageConsumerState
{

    private Cursor $cursor;

    public function getCursor(): Cursor
    {
        return $this->cursor ?? new IncrementalCursor();
    }

    public function storeNewCursor(Cursor $cursor): void
    {
        $this->cursor = $cursor;
    }
}
