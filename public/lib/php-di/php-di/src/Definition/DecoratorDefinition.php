<?php

declare(strict_types=1);

namespace DI\Definition;

/**
 * Factory that decorates a sub-definition.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DecoratorDefinition extends FactoryDefinition implements Definition, ExtendsPreviousDefinition
{
    private ?Definition $decorated = null;

    public function setExtendedDefinition(Definition $definition) : void
    {
        $this->decorated = $definition;
    }

    public function getDecoratedDefinition() : ?Definition
    {
        return $this->decorated;
    }

    public function replaceNestedDefinitions(callable $replacer) : void
    {
        // no nested definitions
    }

    public function __toString() : string
    {
        return 'Decorate(' . $this->getName() . ')';
    }
}
