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
 * Installation time string manager.
 *
 * @package    core
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Fetches minimum strings for installation
 *
 * Minimalistic string fetching implementation
 * that is used in installer before we fetch the wanted
 * language pack from moodle.org lang download site.
 *
 * @package    core
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_string_manager_install implements core_string_manager {
    /** @var string location of pre-install packs for all langs */
    protected $installroot;

    /**
     * Crate new instance of install string manager
     */
    public function __construct() {
        global $CFG;
        $this->installroot = "$CFG->dirroot/install/lang";
    }

    /**
     * Load all strings for one component
     * @param string $component The module the string is associated with
     * @param string $lang
     * @param bool $disablecache Do not use caches, force fetching the strings from sources
     * @param bool $disablelocal Do not use customized strings in xx_local language packs
     * @return array of all string for given component and lang
     */
    public function load_component_strings($component, $lang, $disablecache = false, $disablelocal = false) {
        // Not needed in installer.
        return array();
    }

    /**
     * Does the string actually exist?
     *
     * get_string() is throwing debug warnings, sometimes we do not want them
     * or we want to display better explanation of the problem.
     *
     * Use with care!
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @return boot true if exists
     */
    public function string_exists($identifier, $component) {
        // Simple old style hack ;).
        $str = get_string($identifier, $component);
        return (strpos($str, '[[') === false);
    }

    /**
     * Has string been deprecated?
     *
     * No deprecated string in installation, unused strings are simply removed.
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @return bool true if deprecated
     */
    public function string_deprecated($identifier, $component) {
        return false;
    }

    /**
     * Get String returns a requested string
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @param string|object|array $a An object, string or number that can be used
     *      within translation strings
     * @param string $lang moodle translation language, null means use current
     * @return string The String !
     */
    public function get_string($identifier, $component = '', $a = null, $lang = null) {
        if (!$component) {
            $component = 'moodle';
        }

        if ($lang === null) {
            $lang = current_language();
        }

        // Get parent lang.
        $parent = '';
        if ($lang !== 'en' and $identifier !== 'parentlanguage' and $component !== 'langconfig') {
            if (file_exists("$this->installroot/$lang/langconfig.php")) {
                $string = array();
                include("$this->installroot/$lang/langconfig.php");
                if (isset($string['parentlanguage'])) {
                    $parent = $string['parentlanguage'];
                }
            }
        }

        // Include en string first.
        if (!file_exists("$this->installroot/en/$component.php")) {
            return "[[$identifier]]";
        }
        $string = array();
        include("$this->installroot/en/$component.php");

        // Now override en with parent if defined.
        if ($parent and $parent !== 'en' and file_exists("$this->installroot/$parent/$component.php")) {
            include("$this->installroot/$parent/$component.php");
        }

        // Finally override with requested language.
        if ($lang !== 'en' and file_exists("$this->installroot/$lang/$component.php")) {
            include("$this->installroot/$lang/$component.php");
        }

        if (!isset($string[$identifier])) {
            return "[[$identifier]]";
        }

        $string = $string[$identifier];

        if ($a !== null) {
            if (is_object($a) or is_array($a)) {
                $a = (array)$a;
                $search = array();
                $replace = array();
                foreach ($a as $key => $value) {
                    if (is_int($key)) {
                        // We do not support numeric keys - sorry!
                        continue;
                    }
                    $search[] = '{$a->' . $key . '}';
                    $replace[] = (string)$value;
                }
                if ($search) {
                    $string = str_replace($search, $replace, $string);
                }
            } else {
                $string = str_replace('{$a}', (string)$a, $string);
            }
        }

        return $string;
    }

    /**
     * Returns a localised list of all country names, sorted by country keys.
     *
     * @param bool $returnall return all or just enabled
     * @param string $lang moodle translation language, null means use current
     * @return array two-letter country code => translated name.
     */
    public function get_list_of_countries($returnall = false, $lang = null) {
        // Not used in installer.
        return array();
    }

    /**
     * Returns a localised list of languages, sorted by code keys.
     *
     * @param string $lang moodle translation language, null means use current
     * @param string $standard language list standard
     *                     iso6392: three-letter language code (ISO 639-2/T) => translated name.
     * @return array language code => translated name
     */
    public function get_list_of_languages($lang = null, $standard = 'iso6392') {
        // Not used in installer.
        return array();
    }

    /**
     * Checks if the translation exists for the language
     *
     * @param string $lang moodle translation language code
     * @param bool $includeall include also disabled translations
     * @return bool true if exists
     */
    public function translation_exists($lang, $includeall = true) {
        return file_exists($this->installroot . '/' . $lang . '/langconfig.php');
    }

    /**
     * Returns localised list of installed translations
     * @param bool $returnall return all or just enabled
     * @return array moodle translation code => localised translation name
     */
    public function get_list_of_translations($returnall = false) {
        // Return all is ignored here - we need to know all langs in installer.
        $languages = array();
        // Get raw list of lang directories.
        $langdirs = get_list_of_plugins('install/lang');
        asort($langdirs);
        // Get some info from each lang.
        foreach ($langdirs as $lang) {
            if (file_exists($this->installroot . '/' . $lang . '/langconfig.php')) {
                $string = array();
                include($this->installroot . '/' . $lang . '/langconfig.php');
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'] . ' (' . $lang . ')';
                }
            }
        }
        // Return array.
        return $languages;
    }

    /**
     * Returns localised list of currencies.
     *
     * @param string $lang moodle translation language, null means use current
     * @return array currency code => localised currency name
     */
    public function get_list_of_currencies($lang = null) {
        // Not used in installer.
        return array();
    }

    /**
     * This implementation does not use any caches.
     *
     * @param bool $phpunitreset true means called from our PHPUnit integration test reset
     */
    public function reset_caches($phpunitreset = false) {
        // Nothing to do.
    }

    /**
     * Returns string revision counter, this is incremented after any string cache reset.
     * @return int lang string revision counter, -1 if unknown
     */
    public function get_revision() {
        return -1;
    }
}
