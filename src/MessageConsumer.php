<?php

namespace Robertbaelde\PersistingMessageBus;

class MessageConsumer
{
    public function __construct(
        protected MessageBus $messageBus,
        protected MessageConsumerState $messageConsumerState,
        protected \EventSauce\EventSourcing\MessageConsumer $messageConsumer
    ){}


}
