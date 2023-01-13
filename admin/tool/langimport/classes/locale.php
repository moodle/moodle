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

/**
 * Helper class for the language import tool.
 *
 * @package    tool_langimport
 * @copyright  2018 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_langimport;

use coding_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * Helper class for the language import tool.
 *
 * @copyright  2018 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locale {
    /**
     * Checks availability of locale on current operating system.
     *
     * @param string $langpackcode E.g.: en, es, fr, de.
     * @return bool TRUE if the locale is available on OS.
     * @throws coding_exception when $langpackcode parameter is a non-empty string.
     */
    public function check_locale_availability(string $langpackcode) : bool {
        global $CFG;

        if (empty($langpackcode)) {
            throw new coding_exception('Invalid language pack code in \\'.__METHOD__.'() call, only non-empty string is allowed');
        }

        // Fetch the correct locale based on ostype.
        if ($CFG->ostype === 'WINDOWS') {
            $stringtofetch = 'localewin';
        } else {
            $stringtofetch = 'locale';
        }

        // Store current locale.
        $currentlocale = $this->set_locale(LC_ALL, 0);

        $locale = get_string_manager()->get_string($stringtofetch, 'langconfig', $a = null, $langpackcode);

        // Try to set new locale.
        $return = $this->set_locale(LC_ALL, $locale);

        // Restore current locale.
        $this->set_locale(LC_ALL, $currentlocale);

        // If $return is not equal to false, it means that setlocale() succeed to change locale.
        return $return !== false;
    }

    /**
     * Wrap for the native PHP function setlocale().
     *
     * @param int $category Specifying the category of the functions affected by the locale setting.
     * @param string $locale E.g.: en_AU.utf8, en_GB.utf8, es_ES.utf8, fr_FR.utf8, de_DE.utf8.
     * @return string|false Returns the new current locale, or FALSE on error.
     */
    protected function set_locale(int $category = LC_ALL, string $locale = '0') {
        if (strlen($locale) <= 255 || PHP_OS_FAMILY === 'BSD' || PHP_OS_FAMILY === 'Darwin') {
            // We can set the whole locale all together.
            return setlocale($category, $locale);
        }

        // Too long locale with linux or windows, let's split it into known and supported categories.
        $split = explode(';', $locale);
        foreach ($split as $element) {
            [$category, $value] = explode('=', $element);
            if (defined($category)) { // Only if the category exists, there are OS differences.
                setlocale(constant($category), $value);
            }
        }
        return setlocale(LC_ALL, 0); // Finally, return the complete configured locale.
    }
}
