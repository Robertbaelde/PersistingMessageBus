<?php

namespace Robertbaelde\PersistingMessageBus;

use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;

interface MessageConsumerState
{
    public function getCursor(): ?Cursor;

    public function storeNewCursor(Cursor $cursor): void;
}
