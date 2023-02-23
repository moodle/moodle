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
 * Unit tests for current_language() in moodlelib.php.
 *
 * @package   core
 * @category  test
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use moodle_page;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for current_language() in moodlelib.php.
 *
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    ::current_language
 */
class moodlelib_current_language_test extends \advanced_testcase {

    public function test_current_language_site_default(): void {
        $this->resetAfterTest();
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages(
                ['en' => 'English', 'en_ar' => 'English (pirate)']);

        set_config('lang', 'en_ar');

        $this->assertEquals('en_ar', current_language());

        testable_string_manager_for_current_language_tests::reset_installed_languages_override();
    }

    public function test_current_language_user_pref(): void {
        $this->resetAfterTest();
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages(
                ['en' => 'English', 'en_ar' => 'English (pirate)', 'fr' => 'French']);

        set_config('lang', 'en_ar');
        $this->setUser($this->getDataGenerator()->create_user(['lang' => 'fr']));

        $this->assertEquals('fr', current_language());

        testable_string_manager_for_current_language_tests::reset_installed_languages_override();
    }

    public function test_current_language_forced(): void {
        $this->resetAfterTest();
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages(
                ['en' => 'English', 'en_ar' => 'English (pirate)', 'fr' => 'French', 'de' => 'German']);

        set_config('lang', 'en_ar');
        $this->setUser($this->getDataGenerator()->create_user(['lang' => 'fr']));
        force_current_language('en');

        $this->assertEquals('en', current_language());
    }

    public function test_current_language_course_setting(): void {
        global $PAGE;
        $this->resetAfterTest();
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages(
                ['en' => 'English', 'en_ar' => 'English (pirate)', 'fr' => 'French']);

        set_config('lang', 'en_ar');
        $this->setUser($this->getDataGenerator()->create_user(['lang' => 'fr']));
        $PAGE = new moodle_page();
        $PAGE->set_course($this->getDataGenerator()->create_course(['lang' => 'de']));

        $this->assertEquals('de', current_language());

        testable_string_manager_for_current_language_tests::reset_installed_languages_override();
    }

    public function test_current_language_in_course_no_lang_set(): void {
        global $PAGE;
        $this->resetAfterTest();
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages(
                ['en' => 'English', 'en_ar' => 'English (pirate)', 'fr' => 'French']);

        set_config('lang', 'en_ar');
        $PAGE = new moodle_page();
        $PAGE->set_course($this->getDataGenerator()->create_course());

        $this->assertEquals('en_ar', current_language());

        testable_string_manager_for_current_language_tests::reset_installed_languages_override();
    }

    public function test_current_language_activity_setting(): void {
        global $PAGE;
        $this->resetAfterTest();
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages(
                ['en' => 'English', 'en_ar' => 'English (pirate)', 'fr' => 'French']);

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['lang' => 'de']);
        $pageactivity = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'lang' => 'en']);
        $cm = get_fast_modinfo($course)->get_cm($pageactivity->cmid);

        set_config('lang', 'en_ar');
        $this->setUser($this->getDataGenerator()->create_user(['lang' => 'fr']));
        $PAGE = new moodle_page();
        $PAGE->set_cm($cm, $course, $pageactivity);

        $this->assertEquals('en', current_language());

        testable_string_manager_for_current_language_tests::reset_installed_languages_override();
    }

    public function test_current_language_activity_setting_not_set(): void {
        global $PAGE;
        $this->resetAfterTest();
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages(
                ['en' => 'English', 'en_ar' => 'English (pirate)', 'fr' => 'French']);

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['lang' => 'de']);
        $pageactivity = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $cm = get_fast_modinfo($course)->get_cm($pageactivity->cmid);

        set_config('lang', 'en_ar');
        $this->setUser($this->getDataGenerator()->create_user(['lang' => 'fr']));
        $PAGE = new moodle_page();
        $PAGE->set_cm($cm, $course, $pageactivity);

        $this->assertEquals('de', current_language());

        testable_string_manager_for_current_language_tests::reset_installed_languages_override();
    }
}


/**
 * Test helper class for test which need Moodle to think there are other languages installed.
 *
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_string_manager_for_current_language_tests extends \core_string_manager_standard {

    /** @var array $installedlanguages list of languages which we want to pretend are installed. */
    protected $installedlanguages;

    /**
     * Start pretending that the list of installed languages is other than what it is.
     *
     * You need to pass in an array like ['en' => 'English', 'fr' => 'French'].
     *
     * @param array $installedlanguages the list of languages to assume are installed.
     */
    public static function set_fake_list_of_installed_languages(array $installedlanguages): void {
        global $CFG;

        // Re-create the custom string-manager instance using this class, and force the thing we are overriding.
        $oldsetting = $CFG->config_php_settings['customstringmanager'] ?? null;
        $CFG->config_php_settings['customstringmanager'] = self::class;
        get_string_manager(true)->installedlanguages = $installedlanguages;

        // Reset the setting we overrode.
        unset($CFG->config_php_settings['customstringmanager']);
        if ($oldsetting) {
            $CFG->config_php_settings['customstringmanager'] = $oldsetting;
        }
    }

    /**
     * Must be called at the end of any test which called set_fake_list_of_installed_languages to reset things.
     */
    public static function reset_installed_languages_override(): void {
        get_string_manager(true);
    }

    public function get_list_of_translations($returnall = false) {
        return $this->installedlanguages;
    }
}
