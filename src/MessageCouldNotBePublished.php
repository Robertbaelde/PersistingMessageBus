<?php

namespace Robertbaelde\PersistingMessageBus;

class MessageCouldNotBePublished extends \Exception
{

    public static function topicDoesNotAllowEvent(Topic $topic, PublicMessage $message): self
    {
        return new self("Topic {$topic->getName()} doesnt allow event of type " . get_class($message) . ". Configure the event on the topic, or publish it on the right topic.");
    }

    public static function missingRequiredHeaders(array $missingHeaders): self
    {
        return new self("Cant publish event, the following headers are missing. Make sure you add them to the decorator. " . implode(", ", $missingHeaders));
    }
}
