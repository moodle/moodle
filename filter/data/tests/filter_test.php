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
 * Unit tests.
 *
 * @package filter_data
 * @category test
 * @copyright 2015 David Monllao
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/data/filter.php');

/**
 * Tests for filter_data.
 *
 * @package filter_data
 * @copyright 2015 David Monllao
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_data_filter_testcase extends advanced_testcase {

    /**
     * Tests that the filter applies the required changes.
     *
     * @return void
     */
    public function test_filter() {

        $this->resetAfterTest(true);
        $this->setAdminUser();
        filter_manager::reset_caches();

        filter_set_global_state('data', TEXTFILTER_ON);

        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = context_course::instance($course1->id);

        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext2 = context_course::instance($course2->id);

        $sitecontext = context_course::instance(SITEID);

        $site = get_site();
        $this->add_simple_database_instance($site, array('SiteEntry'));
        $this->add_simple_database_instance($course1, array('CourseEntry'));

        $html = '<p>I like CourseEntry and SiteEntry</p>';

        // Testing at course level (both site and course).
        $filtered = format_text($html, FORMAT_HTML, array('context' => $coursecontext1));
        $this->assertMatchesRegularExpression('/title=(\'|")CourseEntry(\'|")/', $filtered);
        $this->assertMatchesRegularExpression('/title=(\'|")SiteEntry(\'|")/', $filtered);

        // Testing at site level (only site).
        $filtered = format_text($html, FORMAT_HTML, array('context' => $sitecontext));
        $this->assertDoesNotMatchRegularExpression('/title=(\'|")CourseEntry(\'|")/', $filtered);
        $this->assertMatchesRegularExpression('/title=(\'|")SiteEntry(\'|")/', $filtered);

        // Changing to another course to test the caches invalidation (only site).
        $filtered = format_text($html, FORMAT_HTML, array('context' => $coursecontext2));
        $this->assertDoesNotMatchRegularExpression('/title=(\'|")CourseEntry(\'|")/', $filtered);
        $this->assertMatchesRegularExpression('/title=(\'|")SiteEntry(\'|")/', $filtered);
    }

    /**
     * Adds a database instance to the provided course + a text field + adds all attached entries.
     *
     * @param stdClass $course
     * @param array $entries A list of entry names.
     * @return void
     */
    protected function add_simple_database_instance($course, $entries = false) {
        global $DB;

        $database = $this->getDataGenerator()->create_module('data',
                array('course' => $course->id));

        // A database field.
        $field = data_get_field_new('text', $database);
        $fielddetail = new stdClass();
        $fielddetail->d = $database->id;
        $fielddetail->mode = 'add';
        $fielddetail->type = 'text';
        $fielddetail->sesskey = sesskey();
        $fielddetail->name = 'Name';
        $fielddetail->description = 'Some name';
        $fielddetail->param1 = '1';
        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($database);

        // Database entries.
        foreach ($entries as $entrytext) {
            $datacontent = array();
            $datacontent['fieldid'] = $field->field->id;
            $datacontent['recordid'] = $recordid;
            $datacontent['content'] = $entrytext;
            $contentid = $DB->insert_record('data_content', $datacontent);
        }
    }
}
