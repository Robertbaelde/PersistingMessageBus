<?php

namespace Robertbaelde\PersistingMessageBus;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class MessageBus
{
    public function __construct(
        protected Topic $topic,
        protected MessageRepository $messageRepository
    )
    {
    }

    /**
     * @throws MessageCouldNotBePublished
     */
    public function publish(PublicMessage $message, MessageDecorator $decorator)
    {
        $this->verifyMessageIsConfiguredOnTopic($message);

        $message = new Message($message);
        $message = $decorator->decorate($message);

        $this->verifyMessageHasRequiredHeaders($message);

        $rawMessage = $this->topic->getSerializer()->serializeMessage($message);

        $this->messageRepository->persist($rawMessage);

    }

    private function verifyMessageIsConfiguredOnTopic(PublicMessage $message): void
    {
        if(!$this->topic->allowsMessage($message)){
            throw MessageCouldNotBePublished::topicDoesNotAllowEvent($this->topic, $message);
        }
    }

    private function verifyMessageHasRequiredHeaders(Message $message): void
    {
        $requiredHeaders = [Header::MESSAGE_ID];
        $missingHeaders = [];
        foreach ($requiredHeaders as $requiredHeader)
        {
            if($message->header($requiredHeader) !== null){
                continue;
            }
            $missingHeaders[] = $requiredHeader;
        }
        if(count($missingHeaders) > 0){
            throw MessageCouldNotBePublished::missingRequiredHeaders($missingHeaders);
        }
    }
}
