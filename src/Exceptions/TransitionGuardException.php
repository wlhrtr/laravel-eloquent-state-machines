<?php


namespace Wlhrtr\StateMachine\Exceptions;


use Exception;

class TransitionGuardException extends Exception
{
    protected array $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
        parent::__construct("Transition validation failed with following errors: \n" . implode("\n", $errors));
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
