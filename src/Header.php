<?php

namespace Robertbaelde\PersistingMessageBus;

interface Header
{
    public const MESSAGE_ID = '__message_id';
    public const MESSAGE_TYPE = '__message_type';
    public const MESSAGE_TOPIC = '__message_topic';
}
