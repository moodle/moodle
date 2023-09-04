<?php

/**
 * This class extends the Twig_Environment class.
 *
 * It allows you to keep the configuration options passed to the constructor of the environment, so that you can
 * configure not only twig but also its extensions in one single place that's easily available.
 *
 * @author Jaime Pérez Crespo
 */

namespace SimpleSAML\TwigConfigurableI18n\Twig;

use Twig\Loader\LoaderInterface;

class Environment extends \Twig\Environment
{
    /**
     * @var array The array of options passed to the constructor.
     */
    protected $options = [];


    /**
     * Extended constructor.
     *
     * Additional options supported:
     *
     *  * translation_function: the name of a function to translate a message in singular.
     *
     *  * translation_function_plural: the name of a function to translate a message in plural.
     *
     * @see \Twig\Environment::__construct()
     * @param \Twig\Loader\LoaderInterface $loader A Twig_LoaderInterface instance.
     * @param array $options An array of options.
     */
    public function __construct(LoaderInterface $loader, $options = [])
    {
        parent::__construct($loader, $options);
        $this->options = $options;
    }


    /**
     * Gets the array of options used in this environment.
     *
     * @return array An array of options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
