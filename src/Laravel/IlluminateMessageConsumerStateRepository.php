<?php

namespace Robertbaelde\PersistingMessageBus\Laravel;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Expression;
use Robertbaelde\PersistingMessageBus\MessageConsumerState;
use Robertbaelde\PersistingMessageBus\MessageRepository\Cursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\IncrementalCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\LockableIncrementalCursor;

class IlluminateMessageConsumerStateRepository implements MessageConsumerState
{

    public function __construct(
        private ConnectionInterface $connection,
        private string $tableName,
        private string $consumerName,
        private ?array $cursorTypeMap = null,
        private ?Cursor $defaultCursor = null,
    )
    {
        if($this->cursorTypeMap === null) {
            $this->cursorTypeMap = [
                IncrementalCursor::class => 'incrementalCursor',
                LockableIncrementalCursor::class => 'lockableIncrementalCursor',
            ];
        }

        if($this->defaultCursor === null){
            $this->defaultCursor = new IncrementalCursor();
        }
    }

    public function getCursor(): Cursor
    {
        $row = $this->connection->table($this->tableName)->where('consumer_name', $this->consumerName)->first();
        if(!$row){
            return $this->defaultCursor;
        }

        $cursorData = json_decode($row->cursor, true);
        return $this->cursorFromType($cursorData['type'])::fromPayload($cursorData['payload']);
    }

    public function storeNewCursor(Cursor $cursor): void
    {
        $this->connection->table($this->tableName)
            ->updateOrInsert(
                ['consumer_name' => $this->consumerName],
                [
                    'cursor' => json_encode([
                        'payload' => $cursor->toPayload(),
                        'type' => $this->getCursorType($cursor)
                    ]),
                    'last_updated_at' => new Expression('NOW()')
                ]
            );
    }

    private function getCursorType(Cursor $cursor): string
    {
        if(array_key_exists($cursor::class, $this->cursorTypeMap)) {
            return $this->cursorTypeMap[$cursor::class];
        }
        throw new \Exception('Cursor type not found in map');
    }

    private function cursorFromType(mixed $type): string
    {
        return array_search($type, $this->cursorTypeMap);
    }
}
