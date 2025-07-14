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
 * Privacy provider tests.
 *
 * @package    qtype_numerical
 * @copyright  2021 The Open university
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_numerical\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\user_preference_provider;
use qtype_numerical\privacy\provider;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');

/**
 * Privacy provider tests class.
 *
 * @package    qtype_numerical
 * @copyright  2021 The Open university
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends \core_privacy\tests\provider_testcase {
    // Include the privacy helper which has assertions on it.

    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('qtype_numerical');
        $actual = provider::get_metadata($collection);
        $this->assertEquals($collection, $actual);
    }

    public function test_export_user_preferences_no_pref(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test the export_user_preferences given different inputs
     * @dataProvider user_preference_provider

     * @param string $name The name of the user preference to get/set
     * @param string $value The value stored in the database
     * @param string $expected The expected transformed value
     */
    public function test_export_user_preferences($name, $value, $expected): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_user_preference("qtype_numerical_$name", $value, $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());
        $preferences = $writer->get_user_preferences('qtype_numerical');
        foreach ($preferences as $key => $pref) {
            $preference = get_user_preferences("qtype_numerical_{$key}", null, $user->id);
            if ($preference === null) {
                continue;
            }
            $desc = get_string("privacy:preference:{$key}", 'qtype_numerical');
            $this->assertEquals($expected, $pref->value);
            $this->assertEquals($desc, $pref->description);
        }
    }

    /**
     * Create an array of valid user preferences for the multiple choice question type.
     *
     * @return array Array of valid user preferences.
     */
    public static function user_preference_provider(): array {
        return [
                'default mark 1.5' => ['defaultmark', 1.5, 1.5],
                'penalty 20%' => ['penalty', 0.2000000, '20%'],
                'unitrole only numerical' => ['unitrole', \qtype_numerical::UNITNONE,
                        get_string('onlynumerical', 'qtype_numerical')],
                'unitrole many numerical' => ['unitrole', \qtype_numerical::UNITOPTIONAL,
                        get_string('manynumerical', 'qtype_numerical')],
                'unitrole unit graded' => ['unitrole', \qtype_numerical::UNITGRADED,
                        get_string('unitgraded', 'qtype_numerical')],
                'unit penalty 0' => ['unitpenalty', 0.01, 0.01],
                'unit grading types response grade' => ['unitgradingtypes', \qtype_numerical::UNITGRADEDOUTOFMARK,
                        get_string('decfractionofresponsegrade', 'qtype_numerical')],
                'unit grading types question grade' => ['unitgradingtypes', \qtype_numerical::UNITGRADEDOUTOFMAX,
                        get_string('decfractionofquestiongrade', 'qtype_numerical')],
                'multichoice display editable unit text' => ['multichoicedisplay', \qtype_numerical::UNITINPUT,
                        get_string('editableunittext', 'qtype_numerical')],
                'multichoice display radio buttons' => ['multichoicedisplay', \qtype_numerical::UNITRADIO,
                        get_string('unitchoice', 'qtype_numerical')],
                'multichoice display select menu' => ['multichoicedisplay', \qtype_numerical::UNITSELECT,
                        get_string('unitselect', 'qtype_numerical')],
                'unitsleft left example' => ['unitsleft', '1', get_string('leftexample', 'qtype_numerical')],
                'unitsleft right example' => ['unitsleft', '0', get_string('rightexample', 'qtype_numerical')],
        ];
    }
}
