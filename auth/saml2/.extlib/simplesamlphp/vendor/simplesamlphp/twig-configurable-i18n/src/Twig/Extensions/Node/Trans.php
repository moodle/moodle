<?php

/**
 * A class for translation nodes that can be translated with customizable functions.
 *
 * @author Jaime Pérez Crespo
 */

namespace SimpleSAML\TwigConfigurableI18n\Twig\Extensions\Node;

use ReflectionClass;
use Twig\Compiler;

class Trans extends \Twig\Extensions\Node\TransNode
{
    /**
     * Compiles the node to PHP.
     *
     * If SimpleSAML\TwigConfigurableI18n\Twig\Environment was used to configure Twig, and the version of
     * Twig_Extensions_Extension_I18n allows it, we will try to change all calls to the default translation methods
     * to whatever is configured in the environment.
     *
     * @param \Twig\Compiler $compiler A \Twig\Compiler instance
     * @return void
     */
    public function compile(Compiler $compiler): void
    {
        parent::compile($compiler);

        // get the reflection class for Twig_Compiler and evaluate if we can parasite it
        $class = new ReflectionClass(Compiler::class);
        if (!$class->hasProperty('source')) {
            // the source must have changed, we don't have the "source" property, so nothing we can do here...
            return;
        }

        // looks doable, set the "source" property accessible
        $property = $class->getProperty('source');
        $property->setAccessible(true);

        // now, if we have proper configuration, rename the calls to gettext with the ones configured in the environment
        $env = $compiler->getEnvironment();
        if (is_a($env, \SimpleSAML\TwigConfigurableI18n\Twig\Environment::class)) {
            /** @var \SimpleSAML\TwigConfigurableI18n\Twig\Environment $env */
            $options = $env->getOptions();
            $source = $compiler->getSource();
            if (
                array_key_exists('translation_function', $options)
                && is_callable($options['translation_function'], false, $callable)
            ) {
                $source = preg_replace('/([^\w_$])gettext\(/', '$1' . $callable . '(', $source);
                $property->setValue($compiler, $source);
            }
            if (
                array_key_exists('translation_function_plural', $options)
                && is_callable($options['translation_function_plural'], false, $callable)
            ) {
                $source = preg_replace(
                    '/([^\w_$])ngettext\(/',
                    '$1' . $callable . '(',
                    $source
                );
                $property->setValue($compiler, $source);
            }
        }
    }
}
