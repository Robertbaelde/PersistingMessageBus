<?php

namespace Robertbaelde\PersistingMessageBus;


use EventSauce\EventSourcing\Message;

class TopicMessageSerializer implements MessageSerializer
{
    public function __construct(
        protected Topic $topic,
    )
    {
    }

    public function serializeMessage(Message $message): RawMessage
    {
        /** @var PublicMessage $payload */
        $payload = $message->payload();
        return new RawMessage(
            $message->header(Header::MESSAGE_ID),
            $this->topic->getName(),
            $this->topic->getMessageType($payload),
            json_encode($payload->toPayload()),
            json_encode($message->headers()),
            $message->timeOfRecording(),
            $message->header(\EventSauce\EventSourcing\Header::TIME_OF_RECORDING_FORMAT),
        );
    }

    public function unserializePayload(RawMessage $rawMessage): Message
    {
        $messageClass = $this->topic->getMessageClassFromType($rawMessage->messageType);
        $payload = $messageClass::fromPayload(json_decode($rawMessage->messagePayload, true));
        $headers = json_decode($rawMessage->headerPayload, true);
        $message = new Message($payload, [
            Header::MESSAGE_ID => $rawMessage->messageId,
            Header::MESSAGE_TYPE => $rawMessage->messageType,
            Header::MESSAGE_TOPIC => $rawMessage->topic,
        ] + $headers);
        if($rawMessage->publishedAtFormat !== null){
            return $message->withTimeOfRecording($rawMessage->publishedAt, $rawMessage->publishedAtFormat);
        }
        return $message->withTimeOfRecording($rawMessage->publishedAt);
    }
}
