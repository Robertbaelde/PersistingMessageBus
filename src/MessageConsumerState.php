<?php

namespace Robertbaelde\PersistingMessageBus;

interface MessageConsumerState
{
    public function getLastProcessedTimestamp(): \DateTimeImmutable;

    public function storeLastProcessedTimestamp(\DateTimeImmutable $lastProcessed): void;
}
