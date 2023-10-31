<?php

declare(strict_types=1);

namespace DI\Definition\ObjectDefinition;

use DI\Definition\Definition;

/**
 * Describe an injection in an object method.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MethodInjection implements Definition
{
    /**
     * @param mixed[] $parameters
     */
    public function __construct(
        private string $methodName,
        private array $parameters = [],
    ) {
    }

    public static function constructor(array $parameters = []) : self
    {
        return new self('__construct', $parameters);
    }

    public function getMethodName() : string
    {
        return $this->methodName;
    }

    /**
     * @return mixed[]
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Replace the parameters of the definition by a new array of parameters.
     */
    public function replaceParameters(array $parameters) : void
    {
        $this->parameters = $parameters;
    }

    public function merge(self $definition) : void
    {
        // In case of conflicts, the current definition prevails.
        $this->parameters += $definition->parameters;
    }

    public function getName() : string
    {
        return '';
    }

    public function setName(string $name) : void
    {
        // The name does not matter for method injections
    }

    public function replaceNestedDefinitions(callable $replacer) : void
    {
        $this->parameters = array_map($replacer, $this->parameters);
    }

    public function __toString() : string
    {
        return sprintf('method(%s)', $this->methodName);
    }
}
