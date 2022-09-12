<?php

namespace Robertbaelde\PersistingMessageBus;

abstract class BaseTopic implements Topic
{
    public function allowsMessage($message): bool
    {
        foreach ($this->getMessages() as $eventName => $event){
            if($message instanceof $event){
                return true;
            }
        }
        return false;
    }

    public function getSerializer(): TopicMessageSerializer
    {
        return new TopicMessageSerializer($this);
    }

    public function getName(): string
    {
        return get_class($this);
    }

    public function getMessageType(PublicMessage $message): string
    {
        foreach($this->getMessages() as $messageName => $messageClass){
            if($message instanceof $messageClass){
                return $messageName;
            }
        }
        throw new \Exception();
    }

    public function getMessageClassFromType(string $type): string
    {
        foreach($this->getMessages() as $messageName => $messageClass){
            if($type === $messageName){
                return $messageClass;
            }
        }
        throw new \Exception();
    }
}
