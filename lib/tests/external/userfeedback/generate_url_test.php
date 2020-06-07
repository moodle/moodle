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
 * External functions test for generate_url.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\external\userfeedback;

defined('MOODLE_INTERNAL') || die();

use externallib_advanced_testcase;
use context_system;
use context_course;
use external_api;

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Class generate_url_testcase
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass generate_url
 */
class generate_url_testcase extends externallib_advanced_testcase {

    /**
     * Test the behaviour of generate_url().
     *
     * @covers ::execute
     */
    public function test_record_action_system() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $context = context_system::instance();

        $this->setUser($user);

        // Call the WS and check the requested data is returned as expected.
        $result = generate_url::execute($context->id);
        $result = external_api::clean_returnvalue(generate_url::execute_returns(), $result);

        $this->assertStringStartsWith('https://feedback.moodle.org/lms', $result);
        $this->assertStringContainsString('?lang=en', $result);
        $this->assertStringContainsString('&moodle_url=https%3A%2F%2Fwww.example.com%2Fmoodle', $result);
        $this->assertStringContainsString('&theme=boost', $result);
    }

    /**
     * Test the behaviour of generate_url() in a course with a course theme.
     *
     * @covers ::execute
     */
    public function test_record_action_course_theme() {
        $this->resetAfterTest();

        // Enable course themes.
        set_config('allowcoursethemes', 1);

        $course = $this->getDataGenerator()->create_course(['theme' => 'classic']);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $context = context_course::instance($course->id);

        $this->setUser($user);

        // Call the WS and check the requested data is returned as expected.
        $result = generate_url::execute($context->id);
        $result = external_api::clean_returnvalue(generate_url::execute_returns(), $result);

        $this->assertStringContainsString('&theme=classic', $result);
    }

    /**
     * Test the behaviour of generate_url() when a custom feedback url is set.
     *
     * @covers ::execute
     */
    public function test_record_action_custom_feedback_url() {
        $this->resetAfterTest();

        // Enable course themes.
        set_config('userfeedback_url', 'https://feedback.moodle.org/abc');

        $user = $this->getDataGenerator()->create_user();
        $context = context_system::instance();

        $this->setUser($user);

        // Call the WS and check the requested data is returned as expected.
        $result = generate_url::execute($context->id);
        $result = external_api::clean_returnvalue(generate_url::execute_returns(), $result);

        $this->assertStringStartsWith('https://feedback.moodle.org/abc', $result);
    }
}
