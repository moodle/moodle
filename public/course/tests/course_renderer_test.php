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

namespace core_course;

use context_course;
use moodle_url;

/**
 * Tests for the \core_course_renderer class.
 *
 * @package    core_course
 * @copyright  2025 Ilya Tregubov <ilya.tregubov@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_course_renderer
 */
final class course_renderer_test extends \advanced_testcase {
    /**
     * Data provider for {@see self::test_enrolment_options}.
     *
     * @return array
     */
    public static function enrolment_options_provider(): array {
        return [
            'with forms' => [
                'forms' => ['<form>Form 1</form>', '<form>Form 2</form>'],
                'isguestuser' => false,
                'expected' => ['<form>Form 1</form>', '<form>Form 2</form>'],
                'notexpected' => [get_string('noguestaccess', 'enrol'), get_string('notenrollable', 'enrol')],
            ],
            'no forms guest user' => [
                'forms' => [],
                'isguestuser' => true,
                'expected' => [get_string('noguestaccess', 'enrol')],
                'notexpected' => [get_string('notenrollable', 'enrol')],
            ],
            'no forms not guest user' => [
                'forms' => [],
                'isguestuser' => false,
                'expected' => [get_string('notenrollable', 'enrol')],
                'notexpected' => [get_string('noguestaccess', 'enrol')],
            ],
        ];
    }

    /**
     * Test render_enrolment_options with data provider.
     *
     * @param array $forms
     * @param bool $isguestuser
     * @param array $expected
     * @param array $notexpected
     * @dataProvider enrolment_options_provider
     */
    public function test_enrolment_options(array $forms, bool $isguestuser, array $expected, array $notexpected): void {
        global $PAGE;

        $this->resetAfterTest();
        $isguestuser ? $this->setGuestUser() : $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $PAGE->set_context($context);
        $PAGE->set_course($course);

        /** @var \core_course_renderer $renderer */
        $renderer = $PAGE->get_renderer('core', 'course');
        $returnurl = new moodle_url('/course/view.php', ['id' => $course->id]);

        $output = $renderer->enrolment_options($course, $forms, $returnurl);

        foreach ($expected as $expectedstring) {
            $this->assertStringContainsString($expectedstring, $output);
        }
        foreach ($notexpected as $notexpectedstring) {
            $this->assertStringNotContainsString($notexpectedstring, $output);
        }
    }
}
