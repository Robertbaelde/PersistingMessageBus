<?php

namespace Robertbaelde\PersistingMessageBus;

class MessageConsumer
{
    public function __construct(
        protected MessageBus $messageBus,
        protected MessageConsumerState $messageConsumerState,
        protected \EventSauce\EventSourcing\MessageConsumer $messageConsumer,
    ){}

    public function handleNewMessages()
    {
        $cursor = $this->messageConsumerState->getCursor();

        $paginatedMessages = $this->messageBus->getMessages($cursor);

        foreach ($paginatedMessages->messages as $message) {
            $this->messageConsumer->handle($message);
        }

        $this->messageConsumerState->storeNewCursor($paginatedMessages->cursor);
    }


}
