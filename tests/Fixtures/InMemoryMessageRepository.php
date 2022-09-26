<?php

namespace Tests\Fixtures;

use LogicException;
use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\IncrementalCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\MessageRepository;
use Robertbaelde\PersistingMessageBus\MessageRepository\PaginatedMessages;
use Robertbaelde\PersistingMessageBus\RawMessage;

class InMemoryMessageRepository implements MessageRepository
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
        Cursor $cursor
    ): PaginatedMessages {
        if(!$cursor instanceof IncrementalCursor){
            throw new LogicException('Only IncrementalCursor is supported');
        }

        $messages = array_filter($this->messages, fn(RawMessage $rawMessage) => $rawMessage->topic === $topicName);
        $messages = array_slice($messages, $cursor->offset(), $cursor->limit());
        return new PaginatedMessages($messages, $cursor->nextPage(count($messages)));
    }
}
