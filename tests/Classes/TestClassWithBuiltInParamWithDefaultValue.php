<?php

namespace tests\Classes;

class TestClassWithBuiltInParamWithDefaultValue
{
    public function __construct(public int $a = 5)
    {
    }
}