<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core;

/**
 * Helper utility to interact with Locales.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locale {
    /**
     * Wrap for the native PHP function setlocale().
     *
     * @param int $category Specifying the category of the functions affected by the locale setting.
     * @param string $locale E.g.: en_AU.utf8, en_GB.utf8, es_ES.utf8, fr_FR.utf8, de_DE.utf8.
     * @return string|false Returns the new current locale, or FALSE on error.
     */
    public static function set_locale(int $category = LC_ALL, string $locale = '0'): string|false {
        if (strlen($locale) <= 255 || PHP_OS_FAMILY === 'BSD' || PHP_OS_FAMILY === 'Darwin') {
            // We can set the whole locale all together.
            return setlocale($category, $locale);
        }

        // Too long locale with linux or windows, let's split it into known and supported categories.
        $split = explode(';', self::standardise_locale($locale));
        foreach ($split as $element) {
            [$category, $value] = explode('=', $element);
            if (defined($category)) { // Only if the category exists, there are OS differences.
                setlocale(constant($category), $value);
            }
        }

        // Finally, return the complete configured locale.
        return self::get_locale();
    }

    /**
     * Get the current locale.
     *
     * @param int $category
     * @return string|false
     */
    public static function get_locale(int $category = LC_ALL): string|false {
        return setlocale($category, "0");
    }

    /**
     * Standardise a string-based locale, removing any deprecated locale categories and ordering it.
     *
     * @param string $locale
     * @return string
     */
    public static function standardise_locale(string $locale): string {
        $locales = array_filter(
            explode(';', $locale),
            function ($locale): bool {
                [$category, ] = explode('=', $locale);
                return defined($category);
            },
        );
        sort($locales);
        return implode(';', $locales);
    }
}
