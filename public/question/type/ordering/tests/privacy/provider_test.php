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

namespace qtype_ordering\privacy;

use core_privacy\local\request\user_preference_provider;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/ordering/classes/privacy/provider.php');

/**
 * Privacy provider tests.
 *
 * @package    qtype_ordering
 * @copyright  2024 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qtype_ordering\privacy\provider
 */
final class provider_test extends \core_privacy\tests\provider_testcase {
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('qtype_ordering');
        $actual = \qtype_ordering\privacy\provider::get_metadata($collection);
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
        set_user_preference("qtype_ordering_$name", $value, $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());
        $preferences = $writer->get_user_preferences('qtype_ordering');
        foreach ($preferences as $key => $pref) {
            $preference = get_user_preferences("qtype_ordering_$key", null, $user->id);
            if ($preference === null) {
                continue;
            }
            $desc = get_string("privacy:preference:{$key}", 'qtype_ordering');
            $this->assertEquals($expected, $pref->value);
            $this->assertEquals($desc, $pref->description);
        }
    }

    /**
     * Create an array of valid user preferences for the ordering question type.
     *
     * @return array Array of valid user preferences.
     */
    public static function user_preference_provider(): array {
        return [
            'layouttype H' => ['layouttype', 0, 0],
            'layouttype V' => ['layouttype', 1, 1],
            'selecttype ALL' => ['selecttype', 0, 0],
            'selecttype RAND' => ['selecttype', 1, 1],
            'selecttype CONT' => ['selecttype', 2, 2],
            'selectcount 1' => ['selectcount', 1, 1],
            'selectcount 3' => ['selectcount', 3, 3],
            'selectcount 5' => ['selectcount', 5, 5],
            'gradingtype ALL' => ['gradingtype', -1, -1],
            'gradingtype ABS' => ['gradingtype', 0, 0],
            'gradingtype REL' => ['gradingtype', 7, 7],
            'gradingtype REL NEXT EXC' => ['gradingtype', 1, 1],
            'gradingtype REL NEXT INC' => ['gradingtype', 2, 2],
            'gradingtype REL BOTH' => ['gradingtype', 3, 3],
            'gradingtype REL ALL' => ['gradingtype', 4, 4],
            'gradingtype LONG ORDER' => ['gradingtype', 5, 5],
            'gradingtype LONG CONT' => ['gradingtype', 6, 6],
            'showgrading F' => ['showgrading', 0, 0],
            'showgrading T' => ['showgrading', 1, 1],
            'numberingstyle NONE' => ['numberingstyle', 'none', 'none'],
            'numberingstyle abc' => ['numberingstyle', 'abc', 'abc'],
            'numberingstyle ABC' => ['numberingstyle', 'ABC', 'ABC'],
            'numberingstyle 123' => ['numberingstyle', '123', '123'],
            'numberingstyle iii' => ['numberingstyle', 'iii', 'iii'],
            'numberingstyle IIII' => ['numberingstyle', 'IIII', 'IIII'],
        ];
    }
}
