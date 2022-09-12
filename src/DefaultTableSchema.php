<?php

namespace Robertbaelde\PersistingMessageBus;

use Robertbaelde\PersistingMessageBus\MessageRepository\TableSchema;

class DefaultTableSchema implements TableSchema
{

    public function messageIdColumn(): string
    {
        return 'message_id';
    }

    public function topicColumn(): string
    {
        return 'topic';
    }

    public function messageType(): string
    {
        return 'message_type';
    }

    public function messagePayloadColumn(): string
    {
        return 'payload';
    }

    public function headersPayloadColumn(): string
    {
        return 'headers';
    }

    public function publishedAtColumn(): string
    {
        return 'published_at';
    }

    public function publishedAtDateFormat(): string
    {
        return 'Y-m-d H:i:s';
    }
}
