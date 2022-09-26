<?php

namespace Robertbaelde\PersistingMessageBus\Laravel;

use Illuminate\Database\ConnectionInterface;
use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\IncrementalCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\MessageRepository;
use Robertbaelde\PersistingMessageBus\MessageRepository\PaginatedMessages;
use Robertbaelde\PersistingMessageBus\MessageRepository\TableSchema;
use Robertbaelde\PersistingMessageBus\RawMessage;

class IlluminateMessageRepository implements MessageRepository
{
    public function __construct(
        private ConnectionInterface $connection,
        private string $tableName,
        private TableSchema $tableSchema
    ){
    }

    public function persist(RawMessage $message): void
    {
        $this->connection->table($this->tableName)->insert(array_merge([
                $this->tableSchema->messageIdColumn() => $message->messageId,
                $this->tableSchema->topicColumn() => $message->topic,
                $this->tableSchema->messageType() => $message->messageType,
                $this->tableSchema->messagePayloadColumn() => $message->messagePayload,
                $this->tableSchema->headersPayloadColumn() => $message->headerPayload,
                $this->tableSchema->publishedAtColumn() => $message->publishedAt->format($this->tableSchema->publishedAtDateFormat()),
            ])
        );
    }

    public function getMessagesForTopic(
        string $topicName,
        Cursor $cursor
    ): PaginatedMessages {

        if(!$cursor instanceof IncrementalCursor){
            throw new \InvalidArgumentException('Only IncrementalCursor is supported');
        }

        $messages = $this->connection->table($this->tableName)
            ->where($this->tableSchema->topicColumn(), $topicName)
            ->orderBy($this->tableSchema->getSortingColumn(), 'asc')
            ->where($this->tableSchema->getSortingColumn(), '>', $cursor->offset())
            ->where($this->tableSchema->getSortingColumn(), '<=', $cursor->offset() + $cursor->limit())
            ->get()
            ->map(function (object $row){
                return new RawMessage(
                    $row->{$this->tableSchema->messageIdColumn()},
                    $row->{$this->tableSchema->topicColumn()},
                    $row->{$this->tableSchema->messageType()},
                    $row->{$this->tableSchema->messagePayloadColumn()},
                    $row->{$this->tableSchema->headersPayloadColumn()},
                    \DateTimeImmutable::createFromFormat($this->tableSchema->publishedAtDateFormat(), $row->{$this->tableSchema->publishedAtColumn()}),
                );
            })->toArray();

        return new PaginatedMessages($messages, $cursor->nextPage(count($messages)));
    }
}
