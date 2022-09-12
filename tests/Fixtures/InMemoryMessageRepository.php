<?php

namespace Tests\Fixtures;

use Robertbaelde\PersistingMessageBus\RawMessage;

class InMemoryMessageRepository implements \Robertbaelde\PersistingMessageBus\MessageRepository
{

    private array $messages = [];

    public function persist(RawMessage $message): void
    {
        $this->messages[] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getMessagesForTopic(
        string $topicName,
        ?\DateTimeInterface $since = null,
        int $messagesPerPage = 50
    ): array {
        $messages = array_filter($this->messages, fn(RawMessage $rawMessage) => $rawMessage->topic === $topicName);
        $messages = array_filter($messages, fn(RawMessage $rawMessage) => $rawMessage->publishedAt >= $since);
        usort($messages, function(RawMessage $thisRawMessage, RawMessage $thatRawMessage){
            return $thisRawMessage->publishedAt > $thatRawMessage->publishedAt ? 1 : 0;
        });

        return $messages;
    }
}
