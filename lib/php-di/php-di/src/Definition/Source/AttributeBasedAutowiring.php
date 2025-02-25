<?php

declare(strict_types=1);

namespace DI\Definition\Source;

use DI\Attribute\Inject;
use DI\Attribute\Injectable;
use DI\Definition\Exception\InvalidAttribute;
use DI\Definition\ObjectDefinition;
use DI\Definition\ObjectDefinition\MethodInjection;
use DI\Definition\ObjectDefinition\PropertyInjection;
use DI\Definition\Reference;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use Throwable;

/**
 * Provides DI definitions by reading PHP 8 attributes such as #[Inject] and #[Injectable].
 *
 * This source automatically includes the reflection source.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class AttributeBasedAutowiring implements DefinitionSource, Autowiring
{
    /**
     * @throws InvalidAttribute
     */
    public function autowire(string $name, ?ObjectDefinition $definition = null) : ObjectDefinition|null
    {
        $className = $definition ? $definition->getClassName() : $name;

        if (!class_exists($className) && !interface_exists($className)) {
            return $definition;
        }

        $definition = $definition ?: new ObjectDefinition($name);

        $class = new ReflectionClass($className);

        $this->readInjectableAttribute($class, $definition);

        // Browse the class properties looking for annotated properties
        $this->readProperties($class, $definition);

        // Browse the object's methods looking for annotated methods
        $this->readMethods($class, $definition);

        return $definition;
    }

    /**
     * @throws InvalidAttribute
     * @throws InvalidArgumentException The class doesn't exist
     */
    public function getDefinition(string $name) : ObjectDefinition|null
    {
        return $this->autowire($name);
    }

    /**
     * Autowiring cannot guess all existing definitions.
     */
    public function getDefinitions() : array
    {
        return [];
    }

    /**
     * Browse the class properties looking for annotated properties.
     */
    private function readProperties(ReflectionClass $class, ObjectDefinition $definition) : void
    {
        foreach ($class->getProperties() as $property) {
            $this->readProperty($property, $definition);
        }

        // Read also the *private* properties of the parent classes
        /** @noinspection PhpAssignmentInConditionInspection */
        while ($class = $class->getParentClass()) {
            foreach ($class->getProperties(ReflectionProperty::IS_PRIVATE) as $property) {
                $this->readProperty($property, $definition, $class->getName());
            }
        }
    }

    /**
     * @throws InvalidAttribute
     */
    private function readProperty(ReflectionProperty $property, ObjectDefinition $definition, ?string $classname = null) : void
    {
        if ($property->isStatic() || $property->isPromoted()) {
            return;
        }

        // Look for #[Inject] attribute
        try {
            $attribute = $property->getAttributes(Inject::class)[0] ?? null;
            if (! $attribute) {
                return;
            }
            /** @var Inject $inject */
            $inject = $attribute->newInstance();
        } catch (Throwable $e) {
            throw new InvalidAttribute(sprintf(
                '#[Inject] annotation on property %s::%s is malformed. %s',
                $property->getDeclaringClass()->getName(),
                $property->getName(),
                $e->getMessage()
            ), 0, $e);
        }

        // Try to #[Inject("name")] or look for the property type
        $entryName = $inject->getName();

        // Try using typed properties
        $propertyType = $property->getType();
        if ($entryName === null && $propertyType instanceof ReflectionNamedType) {
            if (! class_exists($propertyType->getName()) && ! interface_exists($propertyType->getName())) {
                throw new InvalidAttribute(sprintf(
                    '#[Inject] found on property %s::%s but unable to guess what to inject, the type of the property does not look like a valid class or interface name',
                    $property->getDeclaringClass()->getName(),
                    $property->getName()
                ));
            }
            $entryName = $propertyType->getName();
        }

        if ($entryName === null) {
            throw new InvalidAttribute(sprintf(
                '#[Inject] found on property %s::%s but unable to guess what to inject, please add a type to the property',
                $property->getDeclaringClass()->getName(),
                $property->getName()
            ));
        }

        $definition->addPropertyInjection(
            new PropertyInjection($property->getName(), new Reference($entryName), $classname)
        );
    }

    /**
     * Browse the object's methods looking for annotated methods.
     */
    private function readMethods(ReflectionClass $class, ObjectDefinition $objectDefinition) : void
    {
        // This will look in all the methods, including those of the parent classes
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $methodInjection = $this->getMethodInjection($method);

            if (! $methodInjection) {
                continue;
            }

            if ($method->isConstructor()) {
                $objectDefinition->completeConstructorInjection($methodInjection);
            } else {
                $objectDefinition->completeFirstMethodInjection($methodInjection);
            }
        }
    }

    private function getMethodInjection(ReflectionMethod $method) : ?MethodInjection
    {
        // Look for #[Inject] attribute
        $attribute = $method->getAttributes(Inject::class)[0] ?? null;

        if ($attribute) {
            /** @var Inject $inject */
            $inject = $attribute->newInstance();
            $annotationParameters = $inject->getParameters();
        } elseif ($method->isConstructor()) {
            // #[Inject] on constructor is implicit, we continue
            $annotationParameters = [];
        } else {
            return null;
        }

        $parameters = [];
        foreach ($method->getParameters() as $index => $parameter) {
            $entryName = $this->getMethodParameter($index, $parameter, $annotationParameters);

            if ($entryName !== null) {
                $parameters[$index] = new Reference($entryName);
            }
        }

        if ($method->isConstructor()) {
            return MethodInjection::constructor($parameters);
        }

        return new MethodInjection($method->getName(), $parameters);
    }

    /**
     * @return string|null Entry name or null if not found.
     */
    private function getMethodParameter(int $parameterIndex, ReflectionParameter $parameter, array $annotationParameters) : ?string
    {
        // Let's check if this parameter has an #[Inject] attribute
        $attribute = $parameter->getAttributes(Inject::class)[0] ?? null;
        if ($attribute) {
            /** @var Inject $inject */
            $inject = $attribute->newInstance();

            return $inject->getName();
        }

        // #[Inject] has definition for this parameter (by index, or by name)
        if (isset($annotationParameters[$parameterIndex])) {
            return $annotationParameters[$parameterIndex];
        }
        if (isset($annotationParameters[$parameter->getName()])) {
            return $annotationParameters[$parameter->getName()];
        }

        // Skip optional parameters if not explicitly defined
        if ($parameter->isOptional()) {
            return null;
        }

        // Look for the property type
        $parameterType = $parameter->getType();
        if ($parameterType instanceof ReflectionNamedType && !$parameterType->isBuiltin()) {
            return $parameterType->getName();
        }

        return null;
    }

    /**
     * @throws InvalidAttribute
     */
    private function readInjectableAttribute(ReflectionClass $class, ObjectDefinition $definition) : void
    {
        try {
            $attribute = $class->getAttributes(Injectable::class)[0] ?? null;
            if (! $attribute) {
                return;
            }
            $attribute = $attribute->newInstance();
        } catch (Throwable $e) {
            throw new InvalidAttribute(sprintf(
                'Error while reading #[Injectable] on %s: %s',
                $class->getName(),
                $e->getMessage()
            ), 0, $e);
        }

        if ($attribute->isLazy() !== null) {
            $definition->setLazy($attribute->isLazy());
        }
    }
}
