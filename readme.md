

## Aggregate root

* Agg root repository
* -> message dispatchers
* -> outbox (goes to consumer, consumer can forward to domainMessageDispatcher) or otherwise use dispatcher directly in repo
* -> Sooo package needs to provide a message dispatcher

Message Dispatcher requirements

Guard that message is configured in topic.
Stores message in message repository (using special class name inflector with topic configuration)

query by topic, sorted by time and offset by timestamp

Dispatcher -> persists domain event

repository information: 
* messageId
* topic
* eventName
* payload
* recorded_at


```php 

// publishing a message on the bus 

$message = new SimpleDomainMessage('bar');

$messageBus = new MessageBus(
        new TestTopic(),
        $this->messageRepository
);
    
$messageDispatcher = new MessageDispatcher(
    $messageBus,
    new DefaultMessageDecorator(new SystemClock()),
);

$messageDispatcher->publish($message);

// Some other context 
$messageConsumer = new MessageConsumer(
    $messageBus,
    $messageConsumerStateRepository
    $consumers,
);

while(true){
    $messageConsumer->handleNewMessages();
}
```
