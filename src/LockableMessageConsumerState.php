<?php

namespace Robertbaelde\PersistingMessageBus;

use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\LockableCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\SorryConsumerIsLocked;

interface LockableMessageConsumerState extends MessageConsumerState
{
    /**
     * @throws SorryConsumerIsLocked
     */
    public function getCursor(): LockableCursor;

    public function storeNewCursor(Cursor | LockableCursor $cursor): void;
}
