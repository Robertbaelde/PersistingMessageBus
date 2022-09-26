<?php

namespace Tests\Laravel;

use PHPUnit\Framework\TestCase;
use Robertbaelde\PersistingMessageBus\MessageRepository\LockableIncrementalCursor;
use Robertbaelde\PersistingMessageBus\MessageRepository\SorryConsumerIsLocked;
use Tests\Fixtures\InMemoryMessageConsumerState;

class LockableMessageConsumerStateRepositoryTest extends TestCase
{

    public function setUp(): void
    {
        $this->repository = new \Robertbaelde\PersistingMessageBus\LockableMessageConsumerStateRepository(
            new InMemoryMessageConsumerState()
        );
    }

    /** @test */
    public function it_can_persist_and_retrieve_cursor()
    {
        $cursor = new LockableIncrementalCursor(4, 5);
        $this->repository->storeNewCursor($cursor);

        $cursor = $this->repository->getCursor();
        $this->assertTrue($cursor->isLocked());
    }

    /** @test */
    public function it_throws_exception_when_cursor_is_already_locked()
    {
        $cursor = new LockableIncrementalCursor(4, 5);
        $this->repository->storeNewCursor($cursor);

        // This operation locks the cursor
        $this->repository->getCursor();

        $this->expectExceptionObject(new SorryConsumerIsLocked());
        $this->repository->getCursor();
    }

    /** @test */
    public function on_storing_the_cursor_its_lock_is_released_again()
    {
        $cursor = new LockableIncrementalCursor(4, 5);
        $this->repository->storeNewCursor($cursor);

        // This operation locks the cursor
        $cursor = $this->repository->getCursor();

        // This releases it again.
        $this->repository->storeNewCursor($cursor);

        // So this operation succeeds
        $this->assertNotNull($this->repository->getCursor());
    }
}
