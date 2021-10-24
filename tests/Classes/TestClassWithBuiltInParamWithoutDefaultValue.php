<?php

namespace tests\Classes;

class TestClassWithBuiltInParamWithoutDefaultValue
{
    public function __construct(public int $a)
    {
    }
}