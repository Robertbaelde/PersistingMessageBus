<?php

namespace Robertbaelde\PersistingMessageBus;

interface Topic
{
    public function getName(): string;

    public function allowsMessage(PublicMessage $message);

    public function getMessageType(PublicMessage $message): string;

    public function getMessageClassFromType(string $type): string;

    public function getMessages(): array;

    public function getSerializer(): TopicMessageSerializer;


}
