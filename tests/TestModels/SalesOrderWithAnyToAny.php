<?php

namespace Wlhrtr\StateMachine\Tests\TestModels;

use Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders\StatusAnyToAnyStateMachine;
use Wlhrtr\StateMachine\Traits\HasStateMachines;
use Illuminate\Database\Eloquent\Model;

class SalesOrderWithAnyToAny extends Model
{
    use HasStateMachines;

    protected $table = 'sales_orders';

    protected $guarded = [];

    public $stateMachines = [
        'status' => StatusAnyToAnyStateMachine::class,
    ];
}
