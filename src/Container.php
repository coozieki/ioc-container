<?php

namespace Container;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container
{
    private array $singletons;
    private array $bindings;

    public function bind(string $abstract, mixed $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * @throws UnresolvableBindingException
     */
    public function singleton(string $abstract, object $concrete): void
    {
        if (!$concrete instanceof $abstract) {
            throw new UnresolvableBindingException();
        }

        $this->singletons[$abstract] = $concrete;
    }

    /**
     * @throws UnresolvableBindingException
     * @throws ReflectionException
     * @throws Exception
     */
    public function make(string $abstract): mixed
    {
        if (isset($this->singletons[$abstract])) {
            return $this->singletons[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            if (class_exists($abstract)) {
                return $this->resolveClassBinding($abstract);
            }

            throw new BindingNotFoundException("No binding with \"$abstract\" key found.");
        }

        $binding = $this->bindings[$abstract];

        if (
            class_exists($abstract)
            && !(is_subclass_of($binding, $abstract) || $abstract === $binding || $binding instanceof $abstract)
        ) {
            throw new UnresolvableBindingException();
        }

        /*
         * If binding is string value then we know that binding is class name
         */
        if (is_string($binding)) {
            return $this->resolveClassBinding($binding);
        }

        if (is_object($binding)) {
            return clone $binding;
        }

        throw new UnresolvableBindingException();
    }

    /**
     * @throws ReflectionException|UnresolvableBindingException
     */
    private function resolveClassBinding(string $concrete): object
    {
        $reflection = new ReflectionClass($concrete);

        $constructor = $reflection->getConstructor();
        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();
        $resolvedParameters = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type->isBuiltin()) {
                $resolvedParameters[] = $this->resolveBuiltInParameter($parameter);
                continue;
            }

            $resolvedParameters[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($resolvedParameters);
    }

    /**
     * @throws UnresolvableBindingException
     */
    private function resolveBuiltInParameter(ReflectionParameter $parameter): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new UnresolvableBindingException();
    }
}
