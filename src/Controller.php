<?php

namespace Bubblegum;

use Bubblegum\Routes\RoutedComponent;
use ReflectionMethod;
use InvalidArgumentException;
use ReflectionException;

class Controller extends RoutedComponent
{

    protected string $destinationName;

    /**
     * @throws ReflectionException
     */
    public function handle(Request $request, array $data = []): string|array
    {
        $data['request'] = $request;
        return $this->callDestinationWithArguments($data);
    }

    public function setDestinationName($destinationName): void
    {
        $this->destinationName = $destinationName;
    }

    public function getDestinationName(): string
    {
        return $this->destinationName;
    }

    /**
     * @param array $args
     * @return mixed
     * @throws ReflectionException
     */
    protected function callDestinationWithArguments(array $args): mixed
    {
        $reflection = new ReflectionMethod($this, $this->destinationName);
        $orderedArgs = [];
        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $args)) {
                $orderedArgs[] = $args[$name];
            } elseif ($param->isOptional()) {
                $orderedArgs[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Missing parameter \"$name\" for method \"{$this->destinationName}\" in controller " . get_class($this));
            }
        }
        return $reflection->invokeArgs($this, $orderedArgs);
    }
}