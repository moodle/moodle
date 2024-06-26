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

namespace core_courseformat\local;
use ReflectionMethod;
use section_info;
use cm_info;

/**
 * Base format actions class tests.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\local\baseactions
 */
class baseactions_test extends \advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Get the reflection method for a base class instance.
     * @param baseactions $baseinstance
     * @param string $methodname
     * @return ReflectionMethod
     */
    private function get_base_reflection_method(baseactions $baseinstance, string $methodname): ReflectionMethod {
        $reflectionclass = new \reflectionclass($baseinstance);
        $method = $reflectionclass->getMethod($methodname);
        return $method;
    }

    /**
     * Test for get_instance static method.
     * @covers ::get_format
     */
    public function test_get_format(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics']);

        $baseactions = new baseactions($course);
        $method = $this->get_base_reflection_method($baseactions, 'get_format');

        $format = $method->invoke($baseactions);
        $this->assertEquals('topics', $format->get_format());
        $this->assertEquals('format_topics', $format::class);

        // Format should be always the most updated one.
        $course->format = 'weeks';
        $DB->update_record('course', $course);

        $format = $method->invoke($baseactions);
        $this->assertEquals('weeks', $format->get_format());
        $this->assertEquals('format_weeks', $format::class);
    }

    /**
     * Test for get_instance static method.
     * @covers ::get_section_info
     */
    public function test_get_section_info(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 4],
            ['createsections' => true]
        );

        $modinfo = get_fast_modinfo($course->id);
        $originalsection = $modinfo->get_section_info(1);

        $baseactions = new baseactions($course);
        $method = $this->get_base_reflection_method($baseactions, 'get_section_info');

        $sectioninfo = $method->invoke($baseactions, $originalsection->id);
        $this->assertInstanceOf(section_info::class, $sectioninfo);
        $this->assertEquals($originalsection->id, $sectioninfo->id);
        $this->assertEquals($originalsection->section, $sectioninfo->section);

        // Section info should be always the most updated one.
        course_update_section($course, $originalsection, (object)['name' => 'New name']);
        move_section_to($course, 1, 3);

        $sectioninfo = $method->invoke($baseactions, $originalsection->id);
        $this->assertInstanceOf(section_info::class, $sectioninfo);
        $this->assertEquals($originalsection->id, $sectioninfo->id);
        $this->assertEquals(3, $sectioninfo->section);

        $this->assertEquals('New name', $sectioninfo->name);
    }

    /**
     * Test for get_instance static method.
     * @covers ::get_cm_info
     */
    public function test_get_cm_info(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 4],
            ['createsections' => true]
        );
        $activity = $this->getDataGenerator()->create_module('label', ['course' => $course->id]);

        $modinfo = get_fast_modinfo($course->id);
        $destinationsection = $modinfo->get_section_info(3);
        $originalcm = $modinfo->get_cm($activity->cmid);

        $baseactions = new baseactions($course);
        $method = $this->get_base_reflection_method($baseactions, 'get_cm_info');

        $cm = $method->invoke($baseactions, $originalcm->id);
        $this->assertInstanceOf(cm_info::class, $cm);
        $this->assertEquals($originalcm->id, $cm->id);
        $this->assertEquals($originalcm->sectionnum, $cm->sectionnum);
        $this->assertEquals($originalcm->name, $cm->name);

        // CM info should be always the most updated one.
        moveto_module($originalcm, $destinationsection);

        $cm = $method->invoke($baseactions, $originalcm->id);
        $this->assertInstanceOf(cm_info::class, $cm);
        $this->assertEquals($originalcm->id, $cm->id);
        $this->assertEquals($destinationsection->section, $cm->sectionnum);
    }
}
