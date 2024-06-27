<?php


namespace Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders;


use Wlhrtr\StateMachine\StateMachines\StateMachine;

class StatusAnyToAnyStateMachine extends StateMachine
{
    public function recordHistory(): bool
    {
        return false;
    }

    public function transitions(): array
    {
        return [
            '*' => '*',
        ];
    }

    public function defaultState(): ?string
    {
        return 'new';
    }
}
