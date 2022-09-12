<?php

namespace Robertbaelde\PersistingMessageBus\MessageRepository;

interface TableSchema
{
    public function messageIdColumn(): string;

    public function topicColumn(): string;

    public function messageType(): string;

    public function messagePayloadColumn(): string;

    public function headersPayloadColumn(): string;

    public function publishedAtColumn(): string;

    public function publishedAtDateFormat(): string;

}
