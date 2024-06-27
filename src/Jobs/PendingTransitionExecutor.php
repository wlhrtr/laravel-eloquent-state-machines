<?php


namespace Wlhrtr\StateMachine\Jobs;


use Wlhrtr\StateMachine\Exceptions\InvalidStartingStateException;
use Wlhrtr\StateMachine\Models\PendingTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PendingTransitionExecutor implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Dispatchable, SerializesModels;

    public $pendingTransition;

    public function __construct(PendingTransition $pendingTransition)
    {
        $this->pendingTransition = $pendingTransition;
    }

    public function handle()
    {
        $field = $this->pendingTransition->field;
        $model = $this->pendingTransition->model;
        $from = $this->pendingTransition->from;
        $to = $this->pendingTransition->to;
        $customProperties = $this->pendingTransition->custom_properties;
        $responsible = $this->pendingTransition->responsible;

        if ($model->$field()->isNot($from)) {
            $exception = new InvalidStartingStateException(
                $expectedState = $from,
                $actualState = $model->$field()->state()
            );

            $this->fail($exception);
            return;
        }

        $model->$field()->transitionTo($to, $customProperties, $responsible);
    }
}
