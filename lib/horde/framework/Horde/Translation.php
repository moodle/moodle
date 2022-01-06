<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Translation
 */

/**
 * Horde_Translation is the base class for any translation wrapper classes in
 * libraries that want to utilize the Horde_Translation library for
 * translations.
 *
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Translation
 */
abstract class Horde_Translation
{
    /**
     * The translation domain, e.g. the library name, for the default gettext
     * handler.
     *
     * @var string
     */
    protected static $_domain;

    /**
     * The relative path to the translations for the default gettext handler.
     *
     * @var string
     */
    protected static $_directory;

    /**
     * The handlers providing the actual translations.
     *
     * @var array
     */
    protected static $_handlers = array();

    /**
     * Loads a translation handler class pointing to the library's translations
     * and assigns it to $_handler.
     *
     * @param string $handlerClass  The name of a class implementing the
     *                              Horde_Translation_Handler interface.
     */
    public static function loadHandler($handlerClass)
    {
        if (!static::$_domain || !static::$_directory) {
            throw new Horde_Translation_Exception('The domain and directory properties must be set by the class that extends Horde_Translation.');
        }
        static::setHandler(static::$_domain, new $handlerClass(static::$_domain, static::$_directory));
    }

    /**
     * Assigns a translation handler object to $_handlers.
     *
     * Type hinting isn't used on purpose. You should extend a custom
     * translation handler passed here from the Horde_Translation interface,
     * but technically it's sufficient if you provide the API of that
     * interface.
     *
     * @param string $domain                      The translation domain.
     * @param Horde_Translation_Handler $handler  An object implementing the
     *                                            Horde_Translation_Handler
     *                                            interface.
     */
    public static function setHandler($domain, $handler)
    {
        static::$_handlers[$domain] = $handler;
    }

    /**
     * Returns the translation of a message.
     *
     * @var string $message  The string to translate.
     *
     * @return string  The string translation, or the original string if no
     *                 translation exists.
     */
    public static function t($message)
    {
        if (!isset(static::$_handlers[static::$_domain])) {
            static::loadHandler('Horde_Translation_Handler_Gettext');
        }
        return static::$_handlers[static::$_domain]->t($message);
    }

    /**
     * Returns the plural translation of a message.
     *
     * @param string $singular  The singular version to translate.
     * @param string $plural    The plural version to translate.
     * @param integer $number   The number that determines singular vs. plural.
     *
     * @return string  The string translation, or the original string if no
     *                 translation exists.
     */
    public static function ngettext($singular, $plural, $number)
    {
        if (!isset(static::$_handlers[static::$_domain])) {
            static::loadHandler('Horde_Translation_Handler_Gettext');
        }
        return static::$_handlers[static::$_domain]->ngettext($singular, $plural, $number);
    }

    /**
     * Allows a gettext string to be defined and recognized as a string by
     * the horde translation utilities, but no translation is actually
     * performed (raw gettext = r()).
     *
     * @since 2.1.0
     *
     * @param string $message  The raw string to mark for translation.
     *
     * @return string  The raw string.
     */
    public static function r($message)
    {
        return $message;
    }

}
