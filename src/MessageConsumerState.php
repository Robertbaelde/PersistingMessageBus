<?php

namespace Robertbaelde\PersistingMessageBus;

use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\SorryConsumerIsLocked;

interface MessageConsumerState
{
    /**
     * @throws SorryConsumerIsLocked
     */
    public function getCursor(): Cursor;

    public function storeNewCursor(Cursor $cursor): void;
}
