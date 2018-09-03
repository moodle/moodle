<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/bsd BSD
 * @package  Idna
 */

/**
 * Provide normalized encoding/decoding support for IDNA strings.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Idna
 */
class Horde_Idna
{
    /**
     * The backend to use.
     *
     * @var mixed
     */
    protected static $_backend;

    /**
     * @throws Horde_Idna_Exception
     */
    public static function encode($data)
    {
        switch ($backend = static::_getBackend()) {
        case 'INTL':
            return idn_to_ascii($data);

        case 'INTL_UTS46':
            $result = idn_to_ascii($data, 0, INTL_IDNA_VARIANT_UTS46, $info);
            self::_checkForError($info);
            return $result;

        default:
            return $backend->encode($data);
        }
    }

    /**
     * @throws Horde_Idna_Exception
     */
    public static function decode($data)
    {
        switch ($backend = static::_getBackend()) {
        case 'INTL':
        case 'INTL_UTS46':
            $parts = explode('.', $data);
            foreach ($parts as &$part) {
                if (strpos($part, 'xn--') === 0) {
                    switch ($backend) {
                    case 'INTL':
                        $part = idn_to_utf8($part);
                        break;

                    case 'INTL_UTS46':
                        $part = idn_to_utf8($part, 0, INTL_IDNA_VARIANT_UTS46, $info);
                        self::_checkForError($info);
                        break;
                    }
                }
            }
            return implode('.', $parts);

        default:
            return $backend->decode($data);
        }
    }

    /**
     * Checks if the $idna_info parameter of idn_to_ascii() or idn_to_utf8()
     * contains errors.
     *
     * @param array $info  Fourth parameter to idn_to_ascii() or idn_to_utf8().
     *
     * @throws Horde_Idna_Exception
     */
    protected static function _checkForError($info)
    {
        switch (true) {
        case $info['errors'] & IDNA_ERROR_EMPTY_LABEL:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Domain name is empty"
            ));
        case $info['errors'] & IDNA_ERROR_LABEL_TOO_LONG:
        case $info['errors'] & IDNA_ERROR_DOMAIN_NAME_TOO_LONG:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Domain name is too long"
            ));
        case $info['errors'] & IDNA_ERROR_LEADING_HYPHEN:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Starts with a hyphen"
            ));
        case $info['errors'] & IDNA_ERROR_TRAILING_HYPHEN:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Ends with a hyphen"
            ));
        case $info['errors'] & IDNA_ERROR_HYPHEN_3_4:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Contains hyphen in the third and fourth positions"
            ));
        case $info['errors'] & IDNA_ERROR_LEADING_COMBINING_MARK:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Starts with a combining mark"
            ));
        case $info['errors'] & IDNA_ERROR_DISALLOWED:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Contains disallowed characters"
            ));
        case $info['errors'] & IDNA_ERROR_PUNYCODE:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Starts with \"xn--\" but does not contain valid Punycode"
            ));
        case $info['errors'] & IDNA_ERROR_LABEL_HAS_DOT:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Contains a dot"
            ));
        case $info['errors'] & IDNA_ERROR_INVALID_ACE_LABEL:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "ACE label does not contain a valid label string"
            ));
        case $info['errors'] & IDNA_ERROR_BIDI:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Does not meet the IDNA BiDi requirements (for right-to-left characters)"
            ));
        case $info['errors'] & IDNA_ERROR_CONTEXTJ:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Does not meet the IDNA CONTEXTJ requirements"
            ));
        case $info['errors']:
            throw new Horde_Idna_Exception(Horde_Idna_Translation::t(
                "Unknown error"
            ));
        }
    }

    /**
     * Return the IDNA backend.
     *
     * @return mixed  IDNA backend (false if none available).
     */
    protected static function _getBackend()
    {
        if (!isset(self::$_backend)) {
            if (extension_loaded('intl')) {
                /* Only available in PHP > 5.4.0 */
                self::$_backend = defined('INTL_IDNA_VARIANT_UTS46')
                    ? 'INTL_UTS46'
                    : 'INTL';
            } else {
                self::$_backend = new Horde_Idna_Punycode();
            }
        }

        return self::$_backend;
    }

}
