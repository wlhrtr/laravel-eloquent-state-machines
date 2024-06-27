<?php


namespace Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders;


use Wlhrtr\StateMachine\StateMachines\StateMachine;

class StatusFromAnyStateMachine extends StateMachine
{
    public function recordHistory(): bool
    {
        return false;
    }

    public function transitions(): array
    {
        return [
            '*' => ['pending', 'approved', 'processed'],
        ];
    }

    public function defaultState(): ?string
    {
        return 'pending';
    }
}
