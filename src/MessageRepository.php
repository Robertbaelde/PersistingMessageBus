<?php

namespace Robertbaelde\PersistingMessageBus;

interface MessageRepository
{
    public function persist(RawMessage $message): void;

    public function getMessagesForTopic(string $topicName, ?\DateTimeInterface $since = null, int $messagesPerPage = 50): array;

//    public function getMessagesOfTypeForTopic(string $messageType, string $topicName, \DateTimeInterface $since, int $messagesPerPage = 50): array;
}
