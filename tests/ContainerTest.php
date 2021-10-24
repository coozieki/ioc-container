<?php

namespace tests;

require_once __DIR__ . '/Classes/TestClassWithoutConstructor.php';
require_once __DIR__ . '/Classes/ChildTestClassWithoutConstructor.php';
require_once __DIR__ . '/Classes/TestClassWithEmptyConstructor.php';
require_once __DIR__ . '/Classes/TestClassWithBuiltInParamWithoutDefaultValue.php';
require_once __DIR__ . '/Classes/TestClassWithBuiltInParamWithDefaultValue.php';
require_once __DIR__ . '/Classes/TestClassWithUntypedParamWithoutDefaultValue.php';
require_once __DIR__ . '/Classes/TestClassWithUntypedParamWithDefaultValue.php';
require_once __DIR__ . '/Classes/TestClassWithObjectParam.php';

use Container\BindingNotFoundException;
use Container\Container;
use Container\UnresolvableBindingException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use tests\Classes\ChildTestClassWithoutConstructor;
use tests\Classes\TestClassWithBuiltInParamWithDefaultValue;
use tests\Classes\TestClassWithBuiltInParamWithoutDefaultValue;
use tests\Classes\TestClassWithEmptyConstructor;
use tests\Classes\TestClassWithObjectParam;
use tests\Classes\TestClassWithoutConstructor;
use tests\Classes\TestClassWithUntypedParamWithDefaultValue;
use tests\Classes\TestClassWithUntypedParamWithoutDefaultValue;

class ContainerTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::singleton
     */
    public function testMakeWhenBindingIsSingleton(): void
    {
        $container = new Container();
        $testObject = new TestClassWithoutConstructor();

        $container->singleton(TestClassWithoutConstructor::class, $testObject);

        $this->assertSame($testObject, $container->make(TestClassWithoutConstructor::class));
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     */
    public function testMakeWhenBindingIsNotFoundAndClassExists(): void
    {
        $container = new Container();

        $this->assertEquals(new TestClassWithoutConstructor(), $container->make(TestClassWithoutConstructor::class));
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     */
    public function testMakeWhenBindingIsNotFoundAndClassDoesntExist(): void
    {
        $key = '::some_key::';

        $this->expectException(BindingNotFoundException::class);
        $this->expectExceptionMessage('No binding with "' . $key . '" key found.');

        $container = new Container();

        $container->make($key);
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndConcreteNotInstanceOfAbstract(): void
    {
        $this->expectException(UnresolvableBindingException::class);

        $container = new Container();
        $container->bind(TestClassWithEmptyConstructor::class, TestClassWithoutConstructor::class);

        $container->make(TestClassWithEmptyConstructor::class);
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsChildClassNameWithoutConstructor(): void
    {
        $container = new Container();
        $container->bind(TestClassWithoutConstructor::class, ChildTestClassWithoutConstructor::class);

        $this->assertEquals(new ChildTestClassWithoutConstructor(), $container->make(TestClassWithoutConstructor::class));
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsClassNameWithEmptyConstructor(): void
    {
        $container = new Container();
        $container->bind(TestClassWithEmptyConstructor::class, TestClassWithEmptyConstructor::class);

        $this->assertEquals(new TestClassWithEmptyConstructor(), $container->make(TestClassWithEmptyConstructor::class));
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsClassNameWithBuiltInParamWithoutDefaultValue(): void
    {
        $this->expectException(UnresolvableBindingException::class);
        $container = new Container();
        $container->bind(TestClassWithBuiltInParamWithoutDefaultValue::class, TestClassWithBuiltInParamWithoutDefaultValue::class);

        $container->make(TestClassWithBuiltInParamWithoutDefaultValue::class);
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsClassNameWithBuiltInParamWithDefaultValue(): void
    {
        $container = new Container();
        $container->bind(TestClassWithBuiltInParamWithDefaultValue::class, TestClassWithBuiltInParamWithDefaultValue::class);

        $this->assertEquals(new TestClassWithBuiltInParamWithDefaultValue(), $container->make(TestClassWithBuiltInParamWithDefaultValue::class));
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsClassNameWithUntypedParamWithoutDefaultValue(): void
    {
        $this->expectException(UnresolvableBindingException::class);
        $container = new Container();
        $container->bind(TestClassWithUntypedParamWithoutDefaultValue::class, TestClassWithUntypedParamWithoutDefaultValue::class);

        $container->make(TestClassWithUntypedParamWithoutDefaultValue::class);
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsClassNameWithUntypedParamWithDefaultValue(): void
    {
        $container = new Container();
        $container->bind(TestClassWithUntypedParamWithDefaultValue::class, TestClassWithUntypedParamWithDefaultValue::class);

        $this->assertEquals(new TestClassWithUntypedParamWithDefaultValue(), $container->make(TestClassWithUntypedParamWithDefaultValue::class));
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsClassNameWithObjectParams(): void
    {
        $container = new Container();
        $container->bind(TestClassWithObjectParam::class, TestClassWithObjectParam::class);

        $expected = new TestClassWithObjectParam(
            new TestClassWithoutConstructor(),
            new TestClassWithEmptyConstructor()
        );

        $this->assertEquals($expected, $container->make(TestClassWithObjectParam::class));
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsObjectOfSameClass(): void
    {
        $boundObject = new TestClassWithoutConstructor();

        $container = new Container();
        $container->bind(TestClassWithoutConstructor::class, $boundObject);

        $actual = $container->make(TestClassWithoutConstructor::class);
        $this->assertEquals($boundObject, $actual);
        $this->assertNotSame($boundObject, $actual);
    }

    /**
     * @throws ReflectionException
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::make
     * @covers \Container\Container::bind
     */
    public function testMakeWhenBindingIsFoundAndItIsObjectOfChildClass(): void
    {
        $boundObject = new ChildTestClassWithoutConstructor();

        $container = new Container();
        $container->bind(TestClassWithoutConstructor::class, $boundObject);

        $actual = $container->make(TestClassWithoutConstructor::class);
        $this->assertEquals($boundObject, $actual);
        $this->assertNotSame($boundObject, $actual);
    }

    /**
     * @throws UnresolvableBindingException
     *
     * @covers \Container\Container::singleton
     */
    public function testSingletonWhenConcreteIsNotInstanceOfAbstract(): void
    {
        $this->expectException(UnresolvableBindingException::class);

        $container = new Container();
        $container->singleton(TestClassWithEmptyConstructor::class, new TestClassWithoutConstructor());
    }
}