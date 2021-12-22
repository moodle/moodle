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
 * @package    quiz_responses
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace quiz_responses\privacy;

use core_privacy\local\metadata\collection;
use quiz_responses\privacy\provider;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/questionattempt.php');

/**
 * Privacy provider tests class.
 *
 * @package    quiz_responses
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {
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

        set_user_preference('quiz_report_responses_qtext', true);
        set_user_preference('quiz_report_responses_resp', true);
        set_user_preference('quiz_report_responses_right', true);

        provider::export_user_preferences($USER->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $preferences = $writer->get_user_preferences('quiz_responses');

        $this->assertNotEmpty($preferences->qtext);
        $this->assertEquals(transform::yesno(1), $preferences->qtext->value);

        $this->assertNotEmpty($preferences->resp);
        $this->assertEquals(transform::yesno(1), $preferences->resp->value);

        $this->assertNotEmpty($preferences->right);
        $this->assertEquals(transform::yesno(1), $preferences->right->value);
    }

    /**
     * Preference does exist.
     */
    public function test_preference_bool_false() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_user_preference('quiz_report_responses_qtext', false);
        set_user_preference('quiz_report_responses_resp', false);
        set_user_preference('quiz_report_responses_right', false);

        provider::export_user_preferences($USER->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $preferences = $writer->get_user_preferences('quiz_responses');

        $this->assertNotEmpty($preferences->qtext);
        $this->assertEquals(transform::yesno(0), $preferences->qtext->value);

        $this->assertNotEmpty($preferences->resp);
        $this->assertEquals(transform::yesno(0), $preferences->resp->value);

        $this->assertNotEmpty($preferences->right);
        $this->assertEquals(transform::yesno(0), $preferences->right->value);
    }

    /**
     * Preference does exist.
     */
    public function test_preference_bool_which_first() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_user_preference('quiz_report_responses_which_tries', \question_attempt::FIRST_TRY);

        provider::export_user_preferences($USER->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $preferences = $writer->get_user_preferences('quiz_responses');

        $expected = get_string("privacy:preference:which_tries:first", 'quiz_responses');
        $this->assertNotEmpty($preferences->which_tries);
        $this->assertEquals($expected, $preferences->which_tries->value);
    }
}
