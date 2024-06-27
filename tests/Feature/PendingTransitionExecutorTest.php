<?php

namespace Wlhrtr\StateMachine\Tests\Feature;

use Wlhrtr\StateMachine\Jobs\PendingTransitionExecutor;
use Wlhrtr\StateMachine\Tests\TestCase;
use Wlhrtr\StateMachine\Tests\TestModels\SalesManager;
use Wlhrtr\StateMachine\Tests\TestModels\SalesOrder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;

class PendingTransitionExecutorTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function should_apply_pending_transition()
    {
        //Arrange
        $salesManager = factory(SalesManager::class)->create();

        $salesOrder = factory(SalesOrder::class)->create();

        $pendingTransition = $salesOrder->status()->postponeTransitionTo(
            'approved',
            Carbon::now(),
            ['comments' => 'All good!'],
            $responsible = $salesManager
        );

        $this->assertTrue($salesOrder->status()->is('pending'));

        $this->assertTrue($salesOrder->status()->hasPendingTransitions());

        Queue::after(function (JobProcessed $event) {
            $this->assertFalse($event->job->hasFailed());
        });

        //Act
        PendingTransitionExecutor::dispatch($pendingTransition);

        //Assert
        $salesOrder->refresh();

        $this->assertTrue($salesOrder->status()->is('approved'));

        $this->assertEquals('All good!', $salesOrder->status()->getCustomProperty('comments'));

        $this->assertEquals($salesManager->id, $salesOrder->status()->responsible()->id);

        $this->assertFalse($salesOrder->status()->hasPendingTransitions());
    }

    /** @test */
    public function should_fail_job_automatically_if_starting_transition_is_not_the_same_as_when_postponed()
    {
        //Arrange
        $salesOrder = factory(SalesOrder::class)->create();

        $salesOrder->status()->postponeTransitionTo('approved', Carbon::now());

        //Manually update state
        $salesOrder->update(['status' => 'processed']);
        $this->assertTrue($salesOrder->status()->is('processed'));

        $this->assertTrue($salesOrder->status()->hasPendingTransitions());

        Queue::after(function (JobProcessed $event) {
            $this->assertTrue($event->job->hasFailed());
        });

        //Act
        $pendingTransition = $salesOrder->status()->pendingTransitions()->first();

        PendingTransitionExecutor::dispatch($pendingTransition);
    }
}
