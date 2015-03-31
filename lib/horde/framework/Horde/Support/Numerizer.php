<?php
/**
 * Copyright 2010-2014 Horde LLC (http://www.horde.org/)
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */

/**
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */
class Horde_Support_Numerizer
{
    public static function numerize($string, $args = array())
    {
        return self::factory($args)->numerize($string);
    }

    public static function factory($args = array())
    {
        $locale = isset($args['locale']) ? $args['locale'] : null;
        if ($locale && strtolower($locale) != 'base') {
            $locale = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($locale))));
            $class = 'Horde_Support_Numerizer_Locale_' . $locale;
            if (class_exists($class)) {
                return new $class($args);
            }

            list($language,) = explode('_', $locale);
            if ($language != $locale) {
                $class = 'Horde_Support_Numerizer_Locale_' . $language;
                if (class_exists($class)) {
                    return new $class($args);
                }
            }
        }

        return new Horde_Support_Numerizer_Locale_Base($args);
    }

}
