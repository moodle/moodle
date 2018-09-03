<?php
/**
 * @package Translation
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

/**
 * The Horde_Translation_Handler_Gettext provides translations through the
 * gettext extension, but fails gracefully if gettext is not installed.
 *
 * @author  Jan Schneider <jan@horde.org>
 * @package Translation
 */
class Horde_Translation_Handler_Gettext implements Horde_Translation_Handler
{
    /**
     * The translation domain, e.g. package name.
     *
     * @var string
     */
    protected $_domain;

    /**
     * Whether the gettext extension is installed.
     *
     * @var boolean
     */
    protected $_gettext;

    /**
     * Constructor.
     *
     * @param string $domain  The translation domain, e.g. package name.
     * @param string $path    The path to the gettext catalog.
     */
    public function __construct($domain, $path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("$path is not a directory");
        }
        $this->_gettext = function_exists('_');
        if (!$this->_gettext) {
            return;
        }
        $this->_domain = $domain;
        bindtextdomain($this->_domain, $path);
    }

    /**
     * Returns the translation of a message.
     *
     * @param string $message  The string to translate.
     *
     * @return string  The string translation, or the original string if no
     *                 translation exists.
     */
    public function t($message)
    {
        return $this->_gettext ? dgettext($this->_domain, $message) : $message;
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
    public function ngettext($singular, $plural, $number)
    {
        return $this->_gettext
          ? dngettext($this->_domain, $singular, $plural, $number)
          : ($number > 1 ? $plural : $singular);
    }
}
