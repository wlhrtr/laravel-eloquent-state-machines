<?php

namespace Wlhrtr\StateMachine\Tests\TestModels;

use Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders\StatusWithBeforeTransitionHookStateMachine;
use Wlhrtr\StateMachine\Traits\HasStateMachines;
use Illuminate\Database\Eloquent\Model;

class SalesOrderWithBeforeTransitionHook extends Model
{
    use HasStateMachines;

    protected $table = 'sales_orders';

    protected $guarded = [];

    public $stateMachines = [
        'status' => StatusWithBeforeTransitionHookStateMachine::class,
    ];
}
