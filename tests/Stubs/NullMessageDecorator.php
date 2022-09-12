<?php

namespace Tests\Stubs;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class NullMessageDecorator implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        return $message;
    }
}
