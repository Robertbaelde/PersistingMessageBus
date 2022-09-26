<?php

namespace Robertbaelde\PersistingMessageBus;

use EventSauce\EventSourcing\ReplayingMessages\TriggerBeforeReplay;

class MessageConsumer
{
    public function __construct(
        protected MessageBus $messageBus,
        protected MessageConsumerState $messageConsumerState,
        protected \EventSauce\EventSourcing\MessageConsumer $messageConsumer,
    ){}

    public function handleNewMessages(): int
    {
        $cursor = $this->messageConsumerState->getCursor();

        $paginatedMessages = $this->messageBus->getMessages($cursor);

        if($cursor->isAtStart() && $this->messageConsumer instanceof TriggerBeforeReplay)
        {
            $this->messageConsumer->beforeReplay();
        }

        foreach ($paginatedMessages->messages as $message) {
            $this->messageConsumer->handle($message);
        }

        $this->messageConsumerState->storeNewCursor($paginatedMessages->cursor);

        return count($paginatedMessages->messages);
    }


}
