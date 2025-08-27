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
 * Contains the tests for the course_content_item_exporter class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course;

defined('MOODLE_INTERNAL') || die();

use core_course\local\exporters\course_content_item_exporter;
use core_course\local\repository\content_item_readonly_repository;

/**
 * The tests for the course_content_item_exporter class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class exporters_content_item_test extends \advanced_testcase {

    /**
     * Test confirming a content_item can be exported for a course.
     */
    public function test_export_course_content_item(): void {
        $this->resetAfterTest();
        global $PAGE;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cir = new content_item_readonly_repository();
        $contentitems = $cir->find_all_for_course($course, $user);
        $contentitem = array_shift($contentitems);

        $ciexporter = new course_content_item_exporter($contentitem, ['context' => \context_course::instance($course->id)]);
        $renderer = $PAGE->get_renderer('core');
        $exporteditem = $ciexporter->export($renderer);

        $this->assertObjectHasProperty('id', $exporteditem);
        $this->assertEquals($exporteditem->id, $contentitem->get_id());
        $this->assertObjectHasProperty('name', $exporteditem);
        $this->assertEquals($exporteditem->name, $contentitem->get_name());
        $this->assertObjectHasProperty('title', $exporteditem);
        $this->assertEquals($exporteditem->title, $contentitem->get_title()->get_value());
        $this->assertObjectHasProperty('link', $exporteditem);
        $this->assertEquals($exporteditem->link, $contentitem->get_link()->out(false));
        $this->assertObjectHasProperty('icon', $exporteditem);
        $this->assertEquals($exporteditem->icon, $contentitem->get_icon());
        $this->assertObjectHasProperty('help', $exporteditem);
        $this->assertEquals($exporteditem->help, format_text($contentitem->get_help(), FORMAT_MARKDOWN));
        $this->assertObjectHasProperty('archetype', $exporteditem);
        $this->assertEquals($exporteditem->archetype, $contentitem->get_archetype());
        $this->assertObjectHasProperty('componentname', $exporteditem);
        $this->assertEquals($exporteditem->componentname, $contentitem->get_component_name());
        $this->assertObjectHasProperty('legacyitem', $exporteditem);
        $this->assertFalse($exporteditem->legacyitem);
        $this->assertEquals($exporteditem->purpose, $contentitem->get_purpose());
        $this->assertEquals($exporteditem->branded, $contentitem->is_branded());
    }

    /**
     * Test that legacy items (with id of -1) are exported correctly.
     */
    public function test_export_course_content_item_legacy(): void {
        $this->resetAfterTest();
        global $PAGE;

        $course = $this->getDataGenerator()->create_course();

        $contentitem = new \core_course\local\entity\content_item(
            id: -1,
            name: 'test_name',
            title: new \core_course\local\entity\string_title('test_title'),
            link: new \moodle_url(''),
            icon: '',
            help: '* First point
            * Another point',
            archetype: MOD_ARCHETYPE_OTHER,
            componentname: 'core_test',
            purpose: MOD_PURPOSE_CONTENT,
            branded: false,
            gradable: false,
            otherpurpose: MOD_PURPOSE_INTERACTIVECONTENT,
        );

        $ciexporter = new course_content_item_exporter($contentitem, ['context' => \context_course::instance($course->id)]);
        $renderer = $PAGE->get_renderer('core');
        $exporteditem = $ciexporter->export($renderer);

        $this->assertObjectHasProperty('id', $exporteditem);
        $this->assertEquals($exporteditem->id, $contentitem->get_id());
        $this->assertObjectHasProperty('name', $exporteditem);
        $this->assertEquals($exporteditem->name, $contentitem->get_name());
        $this->assertObjectHasProperty('title', $exporteditem);
        $this->assertEquals($exporteditem->title, $contentitem->get_title()->get_value());
        $this->assertObjectHasProperty('link', $exporteditem);
        $this->assertEquals($exporteditem->link, $contentitem->get_link()->out(false));
        $this->assertObjectHasProperty('icon', $exporteditem);
        $this->assertEquals($exporteditem->icon, $contentitem->get_icon());
        $this->assertObjectHasProperty('help', $exporteditem);
        $this->assertEquals($exporteditem->help, format_text($contentitem->get_help(), FORMAT_MARKDOWN));
        $this->assertObjectHasProperty('archetype', $exporteditem);
        $this->assertEquals($exporteditem->archetype, $contentitem->get_archetype());
        $this->assertObjectHasProperty('componentname', $exporteditem);
        $this->assertEquals($exporteditem->componentname, $contentitem->get_component_name());
        $this->assertEquals($exporteditem->purpose, $contentitem->get_purpose());
        $this->assertEquals($exporteditem->branded, $contentitem->is_branded());
        $this->assertEquals($exporteditem->gradable, $contentitem->is_gradable());
        $this->assertEquals($exporteditem->otherpurpose, $contentitem->get_other_purpose());
        // Most important, is this a legacy item?
        $this->assertObjectHasProperty('legacyitem', $exporteditem);
        $this->assertTrue($exporteditem->legacyitem);
    }
}
