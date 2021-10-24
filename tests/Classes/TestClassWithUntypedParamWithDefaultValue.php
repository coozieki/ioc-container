<?php

namespace tests\Classes;

class TestClassWithUntypedParamWithDefaultValue
{
    public function __construct(public $a = 5)
    {
    }
}