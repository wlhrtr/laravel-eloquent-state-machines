<?php

namespace Wlhrtr\StateMachine\Tests\TestModels;

use Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders\StatusWithAfterTransitionHookStateMachine;
use Wlhrtr\StateMachine\Traits\HasStateMachines;
use Illuminate\Database\Eloquent\Model;

class SalesOrderWithAfterTransitionHook extends Model
{
    use HasStateMachines;

    protected $table = 'sales_orders';

    protected $guarded = [];

    public $stateMachines = [
        'status' => StatusWithAfterTransitionHookStateMachine::class,
    ];
}
