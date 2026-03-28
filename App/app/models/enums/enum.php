<?php

namespace app\models\enums;

use app\models\enums\BasicEnums;
use Exception;

abstract class Enum extends BasicEnums
{
    // protected string $value='';
    final public function __construct($value)
    {
        try {
            $c = new \ReflectionClass($this);
            if (!in_array($value, $c->getConstants())) {
                try {
                    throw new Exception("IllegalArgumentException");
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                }
            }
            $this->value = $value;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    final public function __toString()
    {
        return $this->value;
    }
}
