<?php

/**
 * An internationalization extension for Twig that allows you to specify the functions to use for translation.
 *
 * @author Jaime Pérez Crespo
 */

namespace SimpleSAML\TwigConfigurableI18n\Twig\Extensions\Extension;

use SimpleSAML\TwigConfigurableI18n\Twig\Extensions\TokenParser\Trans;
use Twig\TwigFilter;

class I18n extends \Twig\Extensions\I18nExtension
{
    /** @var array */
    protected $filters = [];


    /**
     * Build a new I18N extension.
     *
     * Two filters, "trans" and "transchoice" are registered by default. These two will allow you to translate
     * singular and plural sentences, respectively.
     */
    public function __construct()
    {
        $this->filters = [
            new TwigFilter('trans', [$this, 'translateSingular'], ['needs_environment' => true]),
            new TwigFilter('transchoice', [$this, 'translatePlural'], ['needs_environment' => true]),
        ];
    }


    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return \Twig\TokenParser\TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [new Trans()];
    }


    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return \Twig\TwigFilter[] An array of filters
     */
    public function getFilters(): array
    {
        return $this->filters;
    }


    /**
     * Wrapper around the given callable we have to use to translate singular strings.
     *
     * Defaults to gettext().
     *
     * @return string|null|false
     */
    public function translateSingular()
    {
        $singular = 'gettext';
        $args = func_get_args();

        /** @var \SimpleSAML\TwigConfigurableI18n\Twig\Environment $env */
        $env = array_shift($args);
        $options = $env->getOptions();
        if (
            array_key_exists('translation_function', $options)
            && is_callable($options['translation_function'], false, $callable)
        ) {
            $singular = $options['translation_function'];
        }
        return call_user_func_array($singular, $args);
    }


    /**
     * Wrapper around the given callable we have to use to translate plural strings.
     *
     * Defaults to ngettext().
     *
     * @return string|null|false
     */
    public function translatePlural()
    {
        $plural = 'ngettext';
        $args = func_get_args();

        /** @var \SimpleSAML\TwigConfigurableI18n\Twig\Environment $env */
        $env = array_shift($args);
        $options = $env->getOptions();

        if (
            array_key_exists('translation_function_plural', $options)
            && is_callable($options['translation_function_plural'])
        ) {
            $plural = $options['translation_function_plural'];
        }
        return call_user_func_array($plural, $args);
    }
}
