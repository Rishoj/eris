<?php
namespace Quantifier;

final class Evaluation
{
    private $assertion;
    private $onFailure;
    private $onSuccess;
    
    public static function of($assertion)
    {
        return new self($assertion);
    }
    
    private function __construct($assertion)
    {
        $this->assertion = $assertion;
        $this->onFailure = function() {};
        $this->onSuccess = function() {};
    }

    public function with($values)
    {
        $this->values = $values;
        return $this;
    }

    public function onFailure(callable $action)
    {
        $this->onFailure = $action;
        return $this;
    }

    public function onSuccess(callable $action)
    {
        $this->onSuccess = $action;
        return $this;
    }

    public function execute()
    {
        try {
            call_user_func_array(
                $this->assertion, $this->values
            );
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            // TODO: change order of arguments
            call_user_func($this->onFailure, $e, $this->values);
            return;
        }
        call_user_func_array($this->onSuccess, $this->values);
    }
}
