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

declare(strict_types=1);

namespace core_customfield;

use advanced_testcase;
use core_course\customfield\course_handler;
use moodle_exception;

/**
 * Unit tests for the abstract custom fields handler
 *
 * @package     core_customfield
 * @covers      \core_customfield\handler
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class handler_test extends advanced_testcase {

    /**
     * Test retrieving handler for given component/area
     */
    public function test_get_handler(): void {
        $handler = handler::get_handler('core_course', 'course');
        $this->assertInstanceOf(course_handler::class, $handler);
    }

    /**
     * Test retrieving handler for invalid component/area
     */
    public function test_get_handler_invalid(): void {
        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Unable to find handler for custom fields for component core_blimey and area test');
        handler::get_handler('core_blimey', 'test');
    }

    /**
     * Test getting instances data
     */
    public function test_get_instances_data(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $sharedcustomfieldcategory = $this->getDataGenerator()->create_custom_field_category([
            'component' => 'core_customfield',
            'area' => 'shared',
        ]);
        $sharedfield = $this->getDataGenerator()->create_custom_field([
            'categoryid' => $sharedcustomfieldcategory->get('id'),
            'name' => 'Color',
            'type' => 'text',
            'shortname' => 'color',
        ]);

        // Enable the shared custom field category for both courses and cohorts.
        $shared = new shared(0, (object)[
            'categoryid' => $sharedcustomfieldcategory->get('id'),
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
        ]);
        $shared->create();
        $shared = new shared(0, (object)[
            'categoryid' => $sharedcustomfieldcategory->get('id'),
            'component' => 'core_cohort',
            'area' => 'cohort',
            'itemid' => 0,
        ]);
        $shared->create();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $sharedid1 = $course1->id;
        $sharedid2 = $course2->id;

        // Manually insert cohorts in the database to have both courses and cohorts using the same IDs.
        $now = time();
        $DB->insert_record_raw('cohort', [
            'id' => $sharedid1,
            'contextid' => 1,
            'name' => 'Cohort 1',
            'descriptionformat' => 0,
            'visible' => 1,
            'component' => '',
            'timecreated' => $now,
            'timemodified' => $now,
        ]);
        $DB->insert_record_raw('cohort', [
            'id' => $sharedid2,
            'contextid' => 1,
            'name' => 'Cohort 2',
            'descriptionformat' => 0,
            'visible' => 1,
            'component' => '',
            'timecreated' => $now,
            'timemodified' => $now,
        ]);

        $fieldid = $sharedfield->get('id');

        // Manually insert customfield_data records as well.
        $DB->insert_record_raw('customfield_data', [
            'fieldid' => $fieldid,
            'instanceid' => $sharedid1,
            'charvalue' => 'orange',
            'value' => 'orange',
            'valueformat' => 0,
            'valuetrust' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
        ]);
        $DB->insert_record_raw('customfield_data', [
            'fieldid' => $fieldid,
            'instanceid' => $sharedid2,
            'charvalue' => 'red',
            'value' => 'red',
            'valueformat' => 0,
            'valuetrust' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
        ]);
        $DB->insert_record_raw('customfield_data', [
            'fieldid' => $fieldid,
            'instanceid' => $sharedid1,
            'charvalue' => 'blue',
            'value' => 'blue',
            'valueformat' => 0,
            'valuetrust' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
            'component' => 'core_cohort',
            'area' => 'cohort',
            'itemid' => 0,
        ]);
        $DB->insert_record_raw('customfield_data', [
            'fieldid' => $fieldid,
            'instanceid' => $sharedid2,
            'charvalue' => 'green',
            'value' => 'green',
            'valueformat' => 0,
            'valuetrust' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
            'component' => 'core_cohort',
            'area' => 'cohort',
            'itemid' => 0,
        ]);

        // Verify courses retrieve their own custom field values despite sharing IDs with cohorts.
        $coursehandler = handler::get_handler('core_course', 'course');
        $coursedata = $coursehandler->get_instances_data([$sharedid1, $sharedid2], true);
        $this->assertArrayHasKey($sharedid1, $coursedata);
        $this->assertArrayHasKey($sharedid2, $coursedata);
        $this->assertEquals('orange', $coursedata[$sharedid1][$fieldid]->get_value());
        $this->assertEquals('red', $coursedata[$sharedid2][$fieldid]->get_value());

        // Verify cohorts retrieve their own custom field values despite sharing IDs with courses.
        $cohorthandler = handler::get_handler('core_cohort', 'cohort');
        $cohortdata = $cohorthandler->get_instances_data([$sharedid1, $sharedid2], true);
        $this->assertArrayHasKey($sharedid1, $cohortdata);
        $this->assertArrayHasKey($sharedid2, $cohortdata);
        $this->assertEquals('blue', $cohortdata[$sharedid1][$fieldid]->get_value());
        $this->assertEquals('green', $cohortdata[$sharedid2][$fieldid]->get_value());
    }
}
