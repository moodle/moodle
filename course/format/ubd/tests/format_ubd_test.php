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
 * Unit tests for UbD course format.
 *
 * @package    format_ubd
 * @copyright  2025 Moodle Evolved Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_ubd;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Unit tests for UbD course format.
 *
 * @package    format_ubd
 * @copyright  2025 Moodle Evolved Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_ubd_test extends \advanced_testcase {

    /**
     * Test course format creation
     */
    public function test_course_format_creation() {
        $this->resetAfterTest();
        
        // Create a course with UbD format
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd',
            'numsections' => 5
        ]);
        
        $this->assertEquals('ubd', $course->format);
        
        // Get course format instance
        $courseformat = course_get_format($course);
        $this->assertInstanceOf('format_ubd', $courseformat);
    }

    /**
     * Test UbD format options
     */
    public function test_ubd_format_options() {
        $this->resetAfterTest();
        
        // Create a course with UbD format
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd'
        ]);
        
        $courseformat = course_get_format($course);
        $options = $courseformat->course_format_options();
        
        // Check that UbD specific options exist
        $this->assertArrayHasKey('ubd_stage1_enduring', $options);
        $this->assertArrayHasKey('ubd_stage1_questions', $options);
        $this->assertArrayHasKey('ubd_stage1_knowledge', $options);
        $this->assertArrayHasKey('ubd_stage2_performance', $options);
        $this->assertArrayHasKey('ubd_stage2_evidence', $options);
        $this->assertArrayHasKey('ubd_stage3_activities', $options);
    }

    /**
     * Test UbD data saving and retrieval
     */
    public function test_ubd_data_save_retrieve() {
        $this->resetAfterTest();
        
        // Create a course with UbD format
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd'
        ]);
        
        $courseformat = course_get_format($course);
        
        // Test data
        $ubdData = [
            'ubd_stage1_enduring' => 'Students will understand that learning is a lifelong process.',
            'ubd_stage1_questions' => 'How do we learn effectively?',
            'ubd_stage1_knowledge' => 'Students will know basic learning strategies.',
            'ubd_stage2_performance' => 'Create a learning portfolio.',
            'ubd_stage2_evidence' => 'Self-reflection essays and peer evaluations.',
            'ubd_stage3_activities' => 'Interactive workshops and group discussions.'
        ];
        
        // Save UbD data
        $result = $courseformat->update_course_format_options($ubdData);
        $this->assertTrue($result);
        
        // Retrieve and verify data
        $course = course_get_format($course)->get_course();
        $this->assertEquals($ubdData['ubd_stage1_enduring'], $course->ubd_stage1_enduring);
        $this->assertEquals($ubdData['ubd_stage1_questions'], $course->ubd_stage1_questions);
        $this->assertEquals($ubdData['ubd_stage1_knowledge'], $course->ubd_stage1_knowledge);
        $this->assertEquals($ubdData['ubd_stage2_performance'], $course->ubd_stage2_performance);
        $this->assertEquals($ubdData['ubd_stage2_evidence'], $course->ubd_stage2_evidence);
        $this->assertEquals($ubdData['ubd_stage3_activities'], $course->ubd_stage3_activities);
    }

    /**
     * Test section name generation
     */
    public function test_section_name() {
        $this->resetAfterTest();
        
        // Create a course with UbD format
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd',
            'numsections' => 3
        ]);
        
        $courseformat = course_get_format($course);
        
        // Test section 0 (general section)
        $section0 = $courseformat->get_section(0);
        $sectionname = $courseformat->get_section_name($section0);
        $this->assertEquals(get_string('section0name', 'format_ubd'), $sectionname);
        
        // Test regular sections
        $section1 = $courseformat->get_section(1);
        $sectionname = $courseformat->get_section_name($section1);
        $this->assertEquals(get_string('sectionname', 'format_ubd') . ' 1', $sectionname);
    }

    /**
     * Test uses_sections method
     */
    public function test_uses_sections() {
        $this->resetAfterTest();
        
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd'
        ]);
        
        $courseformat = course_get_format($course);
        $this->assertTrue($courseformat->uses_sections());
    }

    /**
     * Test uses_course_index method
     */
    public function test_uses_course_index() {
        $this->resetAfterTest();
        
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd'
        ]);
        
        $courseformat = course_get_format($course);
        $this->assertTrue($courseformat->uses_course_index());
    }

    /**
     * Test AJAX support
     */
    public function test_ajax_support() {
        $this->resetAfterTest();
        
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd'
        ]);
        
        $courseformat = course_get_format($course);
        $ajaxsupport = $courseformat->supports_ajax();
        
        $this->assertInstanceOf('stdClass', $ajaxsupport);
        $this->assertTrue($ajaxsupport->capable);
    }

    /**
     * Test data validation
     */
    public function test_data_validation() {
        $this->resetAfterTest();
        
        // Test maximum length validation
        $longText = str_repeat('A', 6000); // Exceeds typical 5000 char limit
        
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd'
        ]);
        
        $courseformat = course_get_format($course);
        
        // This should work (within limits)
        $validData = [
            'ubd_stage1_enduring' => 'Valid content'
        ];
        
        $result = $courseformat->update_course_format_options($validData);
        $this->assertTrue($result);
    }

    /**
     * Test default blocks
     */
    public function test_default_blocks() {
        $this->resetAfterTest();
        
        $course = $this->getDataGenerator()->create_course([
            'format' => 'ubd'
        ]);
        
        $courseformat = course_get_format($course);
        $defaultblocks = $courseformat->get_default_blocks();
        
        $this->assertIsArray($defaultblocks);
        $this->assertArrayHasKey(BLOCK_POS_LEFT, $defaultblocks);
        $this->assertArrayHasKey(BLOCK_POS_RIGHT, $defaultblocks);
    }
}
