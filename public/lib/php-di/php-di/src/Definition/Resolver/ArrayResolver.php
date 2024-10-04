<?php

declare(strict_types=1);

namespace DI\Definition\Resolver;

use DI\Definition\ArrayDefinition;
use DI\Definition\Definition;
use DI\DependencyException;
use Exception;

/**
 * Resolves an array definition to a value.
 *
 * @template-implements DefinitionResolver<ArrayDefinition>
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ArrayResolver implements DefinitionResolver
{
    /**
     * @param DefinitionResolver $definitionResolver Used to resolve nested definitions.
     */
    public function __construct(
        private DefinitionResolver $definitionResolver
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * Resolve an array definition to a value.
     *
     * An array definition can contain simple values or references to other entries.
     *
     * @param ArrayDefinition $definition
     */
    public function resolve(Definition $definition, array $parameters = []) : array
    {
        $values = $definition->getValues();

        // Resolve nested definitions
        array_walk_recursive($values, function (& $value, $key) use ($definition) {
            if ($value instanceof Definition) {
                $value = $this->resolveDefinition($value, $definition, $key);
            }
        });

        return $values;
    }

    public function isResolvable(Definition $definition, array $parameters = []) : bool
    {
        return true;
    }

    /**
     * @throws DependencyException
     */
    private function resolveDefinition(Definition $value, ArrayDefinition $definition, int|string $key) : mixed
    {
        try {
            return $this->definitionResolver->resolve($value);
        } catch (DependencyException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new DependencyException(sprintf(
                'Error while resolving %s[%s]. %s',
                $definition->getName(),
                $key,
                $e->getMessage()
            ), 0, $e);
        }
    }
}
