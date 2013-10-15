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
 * Course request related unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/course/lib.php');

class core_course_courserequest_testcase extends advanced_testcase {

    public function test_create_request() {
        global $DB, $USER;
        $this->resetAfterTest(true);

        $defaultcategory = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");
        set_config('enablecourserequests', 1);
        set_config('requestcategoryselection', 0);
        set_config('defaultrequestcategory', $defaultcategory);

        // Create some categories.
        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category();
        $cat3 = $this->getDataGenerator()->create_category();

        // Basic course request.
        $data = new stdClass();
        $data->fullname = 'Həllo World!';
        $data->shortname = 'Hi th€re!';
        $data->summary_editor['text'] = 'Lorem Ipsum ©';
        $data->summary_editor['format'] = FORMAT_HTML;
        $data->reason = 'Because PHP Unit is cool.';
        $cr = course_request::create($data);

        $this->assertEquals($data->fullname, $cr->fullname);
        $this->assertEquals($data->shortname, $cr->shortname);
        $this->assertEquals($data->summary_editor['text'], $cr->summary);
        $this->assertEquals($data->summary_editor['format'], $cr->summaryformat);
        $this->assertEquals($data->reason, $cr->reason);
        $this->assertEquals($USER->id, $cr->requester);
        $this->assertEquals($defaultcategory, $cr->category);

        // Request with category but category selection not allowed.
        set_config('defaultrequestcategory', $cat2->id);
        $data->category = $cat1->id;
        $cr = course_request::create($data);
        $this->assertEquals($cat2->id, $cr->category);

        // Request with category different than default and category selection allowed.
        set_config('defaultrequestcategory', $cat3->id);
        set_config('requestcategoryselection', 1);
        $data->category = $cat1->id;
        $cr = course_request::create($data);
        $this->assertEquals($cat1->id, $cr->category);
    }

    public function test_approve_request() {
        global $DB;
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        unset_config('noemailever');

        $defaultcategory = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");
        set_config('enablecourserequests', 1);
        set_config('requestcategoryselection', 0);
        set_config('defaultrequestcategory', $defaultcategory);

        // Create some categories.
        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category();

        $requester = $this->getDataGenerator()->create_user();

        $data = new stdClass();
        $data->fullname = 'Həllo World!';
        $data->shortname = 'Hi th€re!';
        $data->summary_editor['text'] = 'Lorem Ipsum ©';
        $data->summary_editor['format'] = FORMAT_HTML;
        $data->reason = 'Because PHP Unit is cool.';

        // Test without category.
        $this->setUser($requester);
        $cr = course_request::create($data);
        $this->setAdminUser();
        $sink = $this->redirectMessages();
        $id = $cr->approve();
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
        $course = $DB->get_record('course', array('id' => $id));
        $this->assertEquals($data->fullname, $course->fullname);
        $this->assertEquals($data->shortname, $course->shortname);
        $this->assertEquals($data->summary_editor['text'], $course->summary);
        $this->assertEquals($data->summary_editor['format'], $course->summaryformat);
        $this->assertEquals(1, $course->requested);
        $this->assertEquals($defaultcategory, $course->category);

        // Test with category.
        set_config('requestcategoryselection', 1);
        set_config('defaultrequestcategory', $cat2->id);
        $data->shortname .= ' 2nd';
        $data->category = $cat1->id;
        $this->setUser($requester);
        $cr = course_request::create($data);
        $this->setAdminUser();
        $sink = $this->redirectMessages();
        $id = $cr->approve();
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
        $course = $DB->get_record('course', array('id' => $id));
        $this->assertEquals($data->category, $course->category);
    }

    public function test_reject_request() {
        global $DB;
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        unset_config('noemailever');

        $this->setAdminUser();
        set_config('enablecourserequests', 1);
        set_config('requestcategoryselection', 0);
        set_config('defaultrequestcategory', $DB->get_field_select('course_categories', "MIN(id)", "parent=0"));

        $requester = $this->getDataGenerator()->create_user();

        $data = new stdClass();
        $data->fullname = 'Həllo World!';
        $data->shortname = 'Hi th€re!';
        $data->summary_editor['text'] = 'Lorem Ipsum ©';
        $data->summary_editor['format'] = FORMAT_HTML;
        $data->reason = 'Because PHP Unit is cool.';

        $this->setUser($requester);
        $cr = course_request::create($data);
        $this->assertTrue($DB->record_exists('course_request', array('id' => $cr->id)));

        $this->setAdminUser();
        $sink = $this->redirectMessages();
        $cr->reject('Sorry!');
        $this->assertFalse($DB->record_exists('course_request', array('id' => $cr->id)));
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
    }
}
