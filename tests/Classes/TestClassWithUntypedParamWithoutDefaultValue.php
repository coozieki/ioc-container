<?php

namespace tests\Classes;

class TestClassWithUntypedParamWithoutDefaultValue
{
    public function __construct(public $a)
    {
    }
}