<?php

use Gettext\BaseTranslator;

/**
 * Returns the translation of a string.
 *
 * @param string $original
 *
 * @return string
 */
function __($original)
{
    $text = BaseTranslator::$current->gettext($original);

    if (func_num_args() === 1) {
        return $text;
    }

    $args = array_slice(func_get_args(), 1);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}

/**
 * Noop, marks the string for translation but returns it unchanged.
 *
 * @param string $original
 *
 * @return string
 */
function noop__($original)
{
    return $original;
}

/**
 * Returns the singular/plural translation of a string.
 *
 * @param string $original
 * @param string $plural
 * @param string $value
 *
 * @return string
 */
function n__($original, $plural, $value)
{
    $text = BaseTranslator::$current->ngettext($original, $plural, $value);

    if (func_num_args() === 3) {
        return $text;
    }

    $args = array_slice(func_get_args(), 3);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}

/**
 * Returns the translation of a string in a specific context.
 *
 * @param string $context
 * @param string $original
 *
 * @return string
 */
function p__($context, $original)
{
    $text = BaseTranslator::$current->pgettext($context, $original);

    if (func_num_args() === 2) {
        return $text;
    }

    $args = array_slice(func_get_args(), 2);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}

/**
 * Returns the translation of a string in a specific domain.
 *
 * @param string $domain
 * @param string $original
 *
 * @return string
 */
function d__($domain, $original)
{
    $text = BaseTranslator::$current->dgettext($domain, $original);

    if (func_num_args() === 2) {
        return $text;
    }

    $args = array_slice(func_get_args(), 2);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}

/**
 * Returns the translation of a string in a specific domain and context.
 *
 * @param string $domain
 * @param string $context
 * @param string $original
 *
 * @return string
 */
function dp__($domain, $context, $original)
{
    $text = BaseTranslator::$current->dpgettext($domain, $context, $original);

    if (func_num_args() === 3) {
        return $text;
    }

    $args = array_slice(func_get_args(), 3);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}

/**
 * Returns the singular/plural translation of a string in a specific domain.
 *
 * @param string $domain
 * @param string $original
 * @param string $plural
 * @param string $value
 *
 * @return string
 */
function dn__($domain, $original, $plural, $value)
{
    $text = BaseTranslator::$current->dngettext($domain, $original, $plural, $value);

    if (func_num_args() === 4) {
        return $text;
    }

    $args = array_slice(func_get_args(), 4);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}

/**
 * Returns the singular/plural translation of a string in a specific context.
 *
 * @param string $context
 * @param string $original
 * @param string $plural
 * @param string $value
 *
 * @return string
 */
function np__($context, $original, $plural, $value)
{
    $text = BaseTranslator::$current->npgettext($context, $original, $plural, $value);

    if (func_num_args() === 4) {
        return $text;
    }

    $args = array_slice(func_get_args(), 4);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}

/**
 * Returns the singular/plural translation of a string in a specific domain and context.
 *
 * @param string $domain
 * @param string $context
 * @param string $original
 * @param string $plural
 * @param string $value
 *
 * @return string
 */
function dnp__($domain, $context, $original, $plural, $value)
{
    $text = BaseTranslator::$current->dnpgettext($domain, $context, $original, $plural, $value);

    if (func_num_args() === 5) {
        return $text;
    }

    $args = array_slice(func_get_args(), 5);

    return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
}
