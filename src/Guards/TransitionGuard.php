<?php

namespace Wlhrtr\StateMachine\Guards;

use Illuminate\Support\Collection;
use Wlhrtr\StateMachine\Exceptions\TransitionGuardException;

abstract class TransitionGuard
{
    public Collection $errors;

    public function __construct()
    {
        $this->errors = collect();
    }

    public function execute($model, $from): void
    {
        $this->validate($model, $from);

        if ($this->errors->isNotEmpty()) {
            $this->throw();
        }
    }

    public function throw(?string $error = null): void
    {
        if ($error) {
            $this->error($error);
        }

        $exception = new TransitionGuardException($this->errors->all());

        $this->onError($exception);

        throw $exception;
    }

    public function error(string $error): void
    {
        $this->errors->push($error);
    }

    public function errors(array $errors): void
    {
        $this->errors = $this->errors->concat($errors);
    }

    abstract public function validate($model, $from): void;

    public function onError(TransitionGuardException $exception): void
    {
    }
}
