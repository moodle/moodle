<?php

declare(strict_types=1);

namespace DI\Definition;

use DI\Definition\Dumper\ObjectDefinitionDumper;
use DI\Definition\ObjectDefinition\MethodInjection;
use DI\Definition\ObjectDefinition\PropertyInjection;
use DI\Definition\Source\DefinitionArray;
use ReflectionClass;

/**
 * Defines how an object can be instantiated.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectDefinition implements Definition
{
    /**
     * Entry name (most of the time, same as $classname).
     */
    private string $name;

    /**
     * Class name (if null, then the class name is $name).
     */
    protected ?string $className = null;

    protected ?MethodInjection $constructorInjection = null;

    protected array $propertyInjections = [];

    /**
     * Method calls.
     * @var MethodInjection[][]
     */
    protected array $methodInjections = [];

    protected ?bool $lazy = null;

    /**
     * Store if the class exists. Storing it (in cache) avoids recomputing this.
     */
    private bool $classExists;

    /**
     * Store if the class is instantiable. Storing it (in cache) avoids recomputing this.
     */
    private bool $isInstantiable;

    /**
     * @param string $name Entry name
     */
    public function __construct(string $name, string $className = null)
    {
        $this->name = $name;
        $this->setClassName($className);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    public function setClassName(?string $className) : void
    {
        $this->className = $className;

        $this->updateCache();
    }

    public function getClassName() : string
    {
        return $this->className ?? $this->name;
    }

    public function getConstructorInjection() : ?MethodInjection
    {
        return $this->constructorInjection;
    }

    public function setConstructorInjection(MethodInjection $constructorInjection) : void
    {
        $this->constructorInjection = $constructorInjection;
    }

    public function completeConstructorInjection(MethodInjection $injection) : void
    {
        if ($this->constructorInjection !== null) {
            // Merge
            $this->constructorInjection->merge($injection);
        } else {
            // Set
            $this->constructorInjection = $injection;
        }
    }

    /**
     * @return PropertyInjection[] Property injections
     */
    public function getPropertyInjections() : array
    {
        return $this->propertyInjections;
    }

    public function addPropertyInjection(PropertyInjection $propertyInjection) : void
    {
        $className = $propertyInjection->getClassName();
        if ($className) {
            // Index with the class name to avoid collisions between parent and
            // child private properties with the same name
            $key = $className . '::' . $propertyInjection->getPropertyName();
        } else {
            $key = $propertyInjection->getPropertyName();
        }

        $this->propertyInjections[$key] = $propertyInjection;
    }

    /**
     * @return MethodInjection[] Method injections
     */
    public function getMethodInjections() : array
    {
        // Return array leafs
        $injections = [];
        array_walk_recursive($this->methodInjections, function ($injection) use (&$injections) {
            $injections[] = $injection;
        });

        return $injections;
    }

    public function addMethodInjection(MethodInjection $methodInjection) : void
    {
        $method = $methodInjection->getMethodName();
        if (! isset($this->methodInjections[$method])) {
            $this->methodInjections[$method] = [];
        }
        $this->methodInjections[$method][] = $methodInjection;
    }

    public function completeFirstMethodInjection(MethodInjection $injection) : void
    {
        $method = $injection->getMethodName();

        if (isset($this->methodInjections[$method][0])) {
            // Merge
            $this->methodInjections[$method][0]->merge($injection);
        } else {
            // Set
            $this->addMethodInjection($injection);
        }
    }

    public function setLazy(bool $lazy = null) : void
    {
        $this->lazy = $lazy;
    }

    public function isLazy() : bool
    {
        if ($this->lazy !== null) {
            return $this->lazy;
        }

        // Default value
        return false;
    }

    public function classExists() : bool
    {
        return $this->classExists;
    }

    public function isInstantiable() : bool
    {
        return $this->isInstantiable;
    }

    public function replaceNestedDefinitions(callable $replacer) : void
    {
        array_walk($this->propertyInjections, function (PropertyInjection $propertyInjection) use ($replacer) {
            $propertyInjection->replaceNestedDefinition($replacer);
        });

        $this->constructorInjection?->replaceNestedDefinitions($replacer);

        array_walk($this->methodInjections, function ($injectionArray) use ($replacer) {
            array_walk($injectionArray, function (MethodInjection $methodInjection) use ($replacer) {
                $methodInjection->replaceNestedDefinitions($replacer);
            });
        });
    }

    /**
     * Replaces all the wildcards in the string with the given replacements.
     *
     * @param string[] $replacements
     */
    public function replaceWildcards(array $replacements) : void
    {
        $className = $this->getClassName();

        foreach ($replacements as $replacement) {
            $pos = strpos($className, DefinitionArray::WILDCARD);
            if ($pos !== false) {
                $className = substr_replace($className, $replacement, $pos, 1);
            }
        }

        $this->setClassName($className);
    }

    public function __toString() : string
    {
        return (new ObjectDefinitionDumper)->dump($this);
    }

    private function updateCache() : void
    {
        $className = $this->getClassName();

        $this->classExists = class_exists($className) || interface_exists($className);

        if (! $this->classExists) {
            $this->isInstantiable = false;

            return;
        }

        /** @var class-string $className */
        $class = new ReflectionClass($className);
        $this->isInstantiable = $class->isInstantiable();
    }
}
