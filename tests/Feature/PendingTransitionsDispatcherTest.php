<?php

namespace Wlhrtr\StateMachine\Tests\Feature;

use Wlhrtr\StateMachine\Exceptions\TransitionNotAllowedException;
use Wlhrtr\StateMachine\Jobs\PendingTransitionExecutor;
use Wlhrtr\StateMachine\Jobs\PendingTransitionsDispatcher;
use Wlhrtr\StateMachine\Models\PendingTransition;
use Wlhrtr\StateMachine\Tests\TestJobs\StartSalesOrderFulfillmentJob;
use Wlhrtr\StateMachine\Tests\TestCase;
use Wlhrtr\StateMachine\Tests\TestModels\SalesOrder;
use Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders\FulfillmentStateMachine;
use Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders\StatusStateMachine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Queue;

class PendingTransitionsDispatcherTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function should_dispatch_pending_transition()
    {
        //Arrange
        $salesOrder = factory(SalesOrder::class)->create();

        $pendingTransition =
            $salesOrder->status()->postponeTransitionTo('approved', Carbon::now()->subSecond());

        $this->assertTrue($salesOrder->status()->hasPendingTransitions());

        //Act
        PendingTransitionsDispatcher::dispatchNow();

        //Assert
        $salesOrder->refresh();

        $this->assertFalse($salesOrder->status()->hasPendingTransitions());

        Queue::assertPushed(PendingTransitionExecutor::class, function ($job) use ($pendingTransition) {
            $this->assertEquals($pendingTransition->id, $job->pendingTransition->id);
            return true;
        });
    }

    /** @test */
    public function should_not_dispatch_future_pending_transitions()
    {
        //Arrange
        $salesOrder = factory(SalesOrder::class)->create();

        $salesOrder->status()->postponeTransitionTo('approved', Carbon::tomorrow());

        $this->assertTrue($salesOrder->status()->hasPendingTransitions());

        //Act
        PendingTransitionsDispatcher::dispatchNow();

        //Assert
        $salesOrder->refresh();

        $this->assertTrue($salesOrder->status()->hasPendingTransitions());

        Queue::assertNothingPushed();
    }
}
