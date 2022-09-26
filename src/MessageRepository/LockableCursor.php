<?php

namespace Robertbaelde\PersistingMessageBus\MessageRepository;

abstract class LockableCursor implements Cursor
{
    protected bool $locked = false;

    public function lock(): void
    {
        if($this->locked){
            throw new \Exception('Cursor is already locked');
        }
        $this->locked = true;
    }

    public function releaseLock(): void
    {
        $this->locked = false;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }
}
