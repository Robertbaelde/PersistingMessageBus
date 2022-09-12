<?php

namespace Tests\Stubs;

use Robertbaelde\PersistingMessageBus\BaseTopic;

class TestTopic extends BaseTopic
{
    public const SimpleDomainMessage = SimpleDomainMessage::class;

    public function getMessages(): array
    {
        return [
            'SimpleDomainMessage' => self::SimpleDomainMessage
        ];
    }

    public function getName(): string
    {
        return 'TestTopic';
    }
}
