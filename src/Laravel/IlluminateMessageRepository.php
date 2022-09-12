<?php

namespace Robertbaelde\PersistingMessageBus\Laravel;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Robertbaelde\PersistingMessageBus\MessageRepository;
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
        ?\DateTimeInterface $since = null,
        int $messagesPerPage = 50
    ): array {
        return $this->connection->table($this->tableName)
            ->where($this->tableSchema->topicColumn(), $topicName)
            ->when($since !== null,
                fn(Builder $builder) => $builder->where($this->tableSchema->publishedAtColumn(), '>=', $since->format($this->tableSchema->publishedAtDateFormat()))
            )
            ->orderBy($this->tableSchema->publishedAtColumn(), 'asc')
            ->take($messagesPerPage)
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
    }
}
