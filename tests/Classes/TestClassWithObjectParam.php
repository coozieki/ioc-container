<?php

namespace tests\Classes;

class TestClassWithObjectParam
{
    public function __construct(TestClassWithoutConstructor $testClass1, TestClassWithEmptyConstructor $testClass2)
    {
    }
}