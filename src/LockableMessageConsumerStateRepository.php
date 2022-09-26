<?php

namespace Robertbaelde\PersistingMessageBus;

use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\LockableCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\LockableIncrementalCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\SorryConsumerIsLocked;

class LockableMessageConsumerStateRepository implements LockableMessageConsumerState
{

    public function __construct(
        private MessageConsumerState $messageConsumerStateRepository,
    )
    {
    }

    public function getCursor(): LockableCursor
    {
        /** @var ?LockableIncrementalCursor $cursor */
        $cursor = $this->messageConsumerStateRepository->getCursor();

        if($cursor->isLocked()){
            throw new SorryConsumerIsLocked();
        }

        $cursor->lock();
        $this->messageConsumerStateRepository->storeNewCursor($cursor);

        return $cursor;
    }

    public function storeNewCursor(Cursor $cursor): void
    {
        if(!$cursor instanceof LockableIncrementalCursor){
            throw new \Exception("Cursor type not supported");
        }
        $cursor->releaseLock();
        $this->messageConsumerStateRepository->storeNewCursor($cursor);
    }
}
