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
 * Test fixture: a string manager that pretends a specific set of languages are installed.
 *
 * @package    core
 * @category   test
 * @copyright  2022 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

/**
 * A string manager subclass that pretends a caller-specified set of language packs are installed.
 *
 * Useful for tests that exercise language-selection logic without requiring real lang packs on disk.
 *
 * Usage:
 * testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages([
 *     'en' => 'English',
 *     'fr' => 'French',
 * ]);
 * // ... run assertions ...
 * testable_string_manager_for_current_language_tests::reset_installed_languages_override();
 *
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_string_manager_for_current_language_tests extends \core_string_manager_standard {
    /** @var array $installedlanguages map of lang code => human name to pretend are installed. */
    protected $installedlanguages;

    /**
     * Start pretending that a specific set of languages are installed.
     *
     * @param array $installedlanguages e.g. ['en' => 'English', 'fr' => 'French']
     */
    public static function set_fake_list_of_installed_languages(array $installedlanguages): void {
        global $CFG;

        $oldsetting = $CFG->config_php_settings['customstringmanager'] ?? null;
        $CFG->config_php_settings['customstringmanager'] = self::class;
        get_string_manager(true)->installedlanguages = $installedlanguages;

        unset($CFG->config_php_settings['customstringmanager']);
        if ($oldsetting) {
            $CFG->config_php_settings['customstringmanager'] = $oldsetting;
        }
    }

    /**
     * Reset the string manager back to normal after the test.
     */
    public static function reset_installed_languages_override(): void {
        get_string_manager(true);
    }

    /**
     * Returns the fake list of installed translations.
     *
     * @param bool $returnall return all or just enabled
     * @return array moodle translation code => localised translation name
     */
    public function get_list_of_translations($returnall = false): array {
        return $this->installedlanguages;
    }
}
