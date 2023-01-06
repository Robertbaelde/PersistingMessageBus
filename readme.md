# Persisting message bus

This package will provide a message bus that persists its messages. This can be used for cross context communication with public events.
The message bus will be for a topic. A topic is a set of event types.This allows for the consuming context to only know about the topic and its event types, and not where they originate from.

## Installation

```bash
composer require robertbaelde/persisting-message-bus
```
## Usage

### Configuring a topic

A topic is a set of message classes, mapped to a string name. Each topic must have a unique name.

```php
<?php

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
```

Messages must implement the PublicMessage interface

```php
use Robertbaelde\PersistingMessageBus\PublicMessage;

class SimpleDomainMessage implements PublicMessage
{
}
```

### Dispatching messages
In order to dispatch messages you'll need a message bus.
This can be constructed using a topic and a message repository.

With this topic you are able to construct a MessageDispatcher, which can be used to dispatch messages.
Message dispatchers can decorate messages using a MessageDecorator. 

```php
$message = new SimpleDomainMessage('bar');

$messageBus = new MessageBus(
    new TestTopic(),
    $this->messageRepository
);

$messageDispatcher = new MessageDispatcher(
    $messageBus,
    new DefaultMessageDecorator(new SystemClock()),
);

$messageDispatcher->dispatch($message);
```

### Consuming messages
In order to consume messages you'll need a message bus and a repository that keeps track of your offset to the message stream. 
Eventsauce's message consumers can be used as consumers.

```php
$messageConsumer = new MessageConsumer(
    messageBus: $this->messageBus,
    messageConsumerState: new InMemoryMessageConsumerState(),
    messageConsumer: $consumer
);

$messageConsumer->handleNewMessages();
```

When using a message consumer you might want to run handleNewMessages in a loop. Make sure only one process is handling new messages at a time. Otherwise messages might be handled double.

### Illuminate repositories

In order to persist messages and consumer state 2 repositories are provided.

Database scheme for messages: 
```sql
CREATE TABLE IF NOT EXISTS `public_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message_id` varchar (255) NOT NULL,
  `topic` varchar (255) NOT NULL,
  `message_type` varchar (255) NOT NULL,
  `payload` varchar (1200) NOT NULL,
  `headers` varchar (1200) NOT NULL,
  `published_at` timestamp NOT NULL,
  PRIMARY KEY (`id` ASC),
  KEY `topic` (`topic`, `id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Database scheme for consumers:
```sql
CREATE TABLE IF NOT EXISTS `message_consumer_state` (
    `consumer_name` varchar (255) NOT NULL,
    `cursor` varchar (1200) NOT NULL,
    `last_updated_at` timestamp NOT NULL,
    PRIMARY KEY (`consumer_name`),
    KEY `consumer_name` (`consumer_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Roadmap

* [ ] Add a consumer that makes http requests for cross service sync
* [ ] Add system for Correlation & Causation id's?
* [ ] Message inbox pattern
* [ ] Message outbox pattern

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
