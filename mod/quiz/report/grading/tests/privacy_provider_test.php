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
 * @package   quiz_grading
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use quiz_grading\privacy\provider;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/questionattempt.php');

/**
 * Privacy provider tests class.
 */
class quiz_grading_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {
    /**
     * When no preference exists, there should be no export.
     */
    public function test_preference_unset() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        provider::export_user_preferences($USER->id);

        $this->assertFalse(writer::with_context(\context_system::instance())->has_any_data());
    }

    /**
     * Preference does exist.
     */
    public function test_preference_bool_true() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_user_preference('quiz_grading_pagesize', 42);
        set_user_preference('quiz_grading_order', 'random');

        provider::export_user_preferences($USER->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $preferences = $writer->get_user_preferences('quiz_grading');

        $this->assertNotEmpty($preferences->pagesize);
        $this->assertEquals(42, $preferences->pagesize->value);

        $this->assertNotEmpty($preferences->order);
        $this->assertEquals('Randomly', $preferences->order->value);
    }
}
