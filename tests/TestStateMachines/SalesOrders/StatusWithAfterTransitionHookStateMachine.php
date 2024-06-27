<?php


namespace Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders;


use Wlhrtr\StateMachine\StateMachines\StateMachine;
use Wlhrtr\StateMachine\Tests\TestJobs\AfterTransitionJob;

class StatusWithAfterTransitionHookStateMachine extends StateMachine
{
    public function recordHistory(): bool
    {
        return true;
    }

    public function transitions(): array
    {
        return [
            'pending' => ['approved'],
            'approved' => ['processed'],
        ];
    }

    public function defaultState(): ?string
    {
        return 'pending';
    }

    public function afterTransitionHooks(): array
    {
        return [
            'approved' => [
                function($from, $model) {
                    $model->total = 200;
                    $model->save();
                },
                function($from, $model) {
                    $model->notes = 'after';
                    $model->save();
                },
                function ($from, $model) {
                    AfterTransitionJob::dispatch();
                },
            ]
        ];
    }
}
