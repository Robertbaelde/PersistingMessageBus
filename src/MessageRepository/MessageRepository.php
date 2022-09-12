<?php

namespace Robertbaelde\PersistingMessageBus\MessageRepository;

use Robertbaelde\PersistingMessageBus\RawMessage;

interface MessageRepository
{
    public function persist(RawMessage $message): void;

    public function getMessagesForTopic(string $topicName, Cursor $cursor): PaginatedMessages;

//    public function getMessagesOfTypeForTopic(string $messageType, string $topicName, \DateTimeInterface $since, int $messagesPerPage = 50): array;
}
