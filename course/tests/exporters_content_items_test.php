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
 * Contains the tests for the course_content_items_exporter class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course;

defined('MOODLE_INTERNAL') || die();

use core_course\local\exporters\course_content_items_exporter;
use core_course\local\repository\content_item_readonly_repository;

/**
 * The tests for the course_content_items_exporter class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exporters_content_items_test extends \advanced_testcase {

    /**
     * Test confirming the collection of content_items can be exported for a course.
     */
    public function test_export_course_content_items() {
        $this->resetAfterTest();
        global $PAGE;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cir = new content_item_readonly_repository();
        $contentitems = $cir->find_all_for_course($course, $user);

        $ciexporter = new course_content_items_exporter($contentitems, ['context' => \context_course::instance($course->id)]);
        $renderer = $PAGE->get_renderer('core');
        $exportedcontentitems = $ciexporter->export($renderer);

        $this->assertObjectHasAttribute('content_items', $exportedcontentitems);
        foreach ($exportedcontentitems->content_items as $key => $dto) {
            $this->assertObjectHasAttribute('id', $dto);
            $this->assertObjectHasAttribute('name', $dto);
            $this->assertObjectHasAttribute('title', $dto);
            $this->assertObjectHasAttribute('link', $dto);
            $this->assertObjectHasAttribute('icon', $dto);
            $this->assertObjectHasAttribute('help', $dto);
            $this->assertObjectHasAttribute('archetype', $dto);
            $this->assertObjectHasAttribute('componentname', $dto);
        }
    }
}
