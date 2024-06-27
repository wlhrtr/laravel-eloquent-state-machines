<?php


namespace Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders;


use Wlhrtr\StateMachine\StateMachines\StateMachine;

class StatusStateMachine extends StateMachine
{
    public function recordHistory(): bool
    {
        return true;
    }

    public function transitions(): array
    {
        return [
            'pending' => ['approved', 'waiting'],
            'approved' => ['processed'],
            'waiting' => ['cancelled'],
        ];
    }

    public function defaultState(): ?string
    {
        return 'pending';
    }
}
