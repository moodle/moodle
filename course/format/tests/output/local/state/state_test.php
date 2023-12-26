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

namespace core_courseformat\output\local\state;

/**
 * Tests for state classes (course, section, cm).
 *
 * @package    core
 * @subpackage course
 * @copyright  2021 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_output_course_format_state.php');
    }

    /**
     * Test the behaviour of state::export_for_template().
     *
     * @dataProvider state_provider
     * @covers \core_courseformat\output\local\state
     *
     * @param string $format The course format of the course where the method will be executed.
     */
    public function test_state(string $format = 'topics') {
        global $PAGE;

        $this->resetAfterTest();

        // Create a course.
        $numsections = 6;
        $course = $this->getDataGenerator()->create_course(['numsections' => $numsections, 'format' => $format]);
        $hiddensections = [4, 6];
        foreach ($hiddensections as $section) {
            set_section_visible($course->id, $section, 0);
        }

        // Create and enrol user.
        $this->setAdminUser();
        $courseformat = course_get_format($course->id);
        $modinfo = $courseformat->get_modinfo();
        $issocialformat = $courseformat->get_format() === 'social';

        // Only create activities if the course format is not social.
        // There's no course home page (and sections) for social course format.
        if (!$issocialformat || $format == 'theunittest') {
            // Add some activities to the course.
            $this->getDataGenerator()->create_module('page', ['course' => $course->id], ['section' => 1,
                'visible' => 1]);
            $this->getDataGenerator()->create_module('forum', ['course' => $course->id], ['section' => 1,
                'visible' => 1]);
            $this->getDataGenerator()->create_module('assign', ['course' => $course->id], ['section' => 2,
                'visible' => 0]);
            $this->getDataGenerator()->create_module('glossary', ['course' => $course->id], ['section' => 4,
                'visible' => 1]);
            $this->getDataGenerator()->create_module('label', ['course' => $course->id], ['section' => 5,
                'visible' => 0]);
            $this->getDataGenerator()->create_module('feedback', ['course' => $course->id], ['section' => 5,
                'visible' => 1]);
        }

        $courseclass = $courseformat->get_output_classname('state\\course');
        $sectionclass = $courseformat->get_output_classname('state\\section');
        $cmclass = $courseformat->get_output_classname('state\\cm');

        // Get the proper renderer.
        $renderer = $courseformat->get_renderer($PAGE);
        $result = (object)[
            'course' => (object)[],
            'section' => [],
            'cm' => [],
        ];

        // General state.
        $coursestate = new $courseclass($courseformat);
        $result->course = $coursestate->export_for_template($renderer);

        if ($format == 'theunittest') {
            // These course format's hasn't the renderer file, so a debugging message will be displayed.
            $this->assertDebuggingCalled();
        }

        $this->assertEquals($course->id, $result->course->id);
        $this->assertEquals($numsections, $result->course->numsections);
        $this->assertFalse($result->course->editmode);
        $sections = $modinfo->get_section_info_all();

        foreach ($sections as $key => $section) {
            $this->assertEquals($section->id, $result->course->sectionlist[$key]);
            if (!$issocialformat || $format == 'theunittest') {
                if (!empty($section->uservisible)) {
                    $sectionstate = new $sectionclass($courseformat, $section);
                    $result->section[$key] = $sectionstate->export_for_template($renderer);
                    $this->assertEquals($section->id, $result->section[$key]->id);
                    $this->assertEquals($section->section, $result->section[$key]->section);
                    $this->assertTrue($section->visible == $result->section[$key]->visible);

                    if ($key === 0 || $key === 3 || $key === 6) {
                        $this->assertEmpty($result->section[$key]->cmlist);
                    } else if ($key === 1) {
                        $this->assertEquals(2, count($result->section[$key]->cmlist));
                    } else if ($key === 2 || $key === 4) {
                        $this->assertEquals(1, count($result->section[$key]->cmlist));
                    } else if ($key === 5) {
                        $this->assertEquals(2, count($result->section[$key]->cmlist));
                    }
                }
            } else {
                // Social course format doesn't have sections.
                $this->assertEmpty($result->section);
            }
        }

        foreach ($modinfo->cms as $key => $cm) {
            $section = $sections[$cm->sectionnum];
            $cmstate = new $cmclass($courseformat, $section, $cm);
            $result->cm[$key] = $cmstate->export_for_template($renderer);
            $this->assertEquals($cm->id, $result->cm[$key]->id);
            $this->assertEquals($cm->name, $result->cm[$key]->name);
            $this->assertTrue($cm->visible == $result->cm[$key]->visible);
        }
    }

    /**
     * Data provider for test_state().
     *
     * @return array
     */
    public function state_provider(): array {
        return [
            // COURSEFORMAT. Test behaviour depending on course formats.
            'Single activity format' => [
                'format' => 'singleactivity',
            ],
            'Social format' => [
                'format' => 'social',
            ],
            'Weeks format' => [
                'format' => 'weeks',
            ],
            'The unit tests format' => [
                'format' => 'theunittest',
            ],
        ];
    }
}
