<?php

namespace Robertbaelde\PersistingMessageBus;

use EventSauce\EventSourcing\MessageDecorator;

class MessageDispatcher
{
    public function __construct(
        protected MessageBus $messageBus,
        protected MessageDecorator $messageDecorator
    ){}

    /**
     * @throws MessageCouldNotBePublished
     */
    public function dispatch(PublicMessage $message): void
    {
        $this->messageBus->publish($message, $this->messageDecorator);
    }
}
