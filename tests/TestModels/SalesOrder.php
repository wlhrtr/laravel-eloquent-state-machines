<?php

namespace Wlhrtr\StateMachine\Tests\TestModels;

use Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders\FulfillmentStateMachine;
use Wlhrtr\StateMachine\Tests\TestStateMachines\SalesOrders\StatusStateMachine;
use Wlhrtr\StateMachine\Traits\HasStateMachines;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasStateMachines;

    protected $guarded = [];

    public $stateMachines = [
        'status' => StatusStateMachine::class,
        'fulfillment' => FulfillmentStateMachine::class,
    ];
}
