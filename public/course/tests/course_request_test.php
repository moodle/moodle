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

use core\context\system as context_system;
use core\context\coursecat as context_coursecat;
use core_course_category;

/**
 * Tests for course_request class.
 *
 * @package    core_course
 * @category   test
 * @copyright  Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(course_request::class)]
final class course_request_test extends \advanced_testcase {
    public function test_create_request(): void {
        global $DB, $USER;
        $this->resetAfterTest(true);

        $defaultcategory = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");
        set_config('enablecourserequests', 1);
        set_config('lockrequestcategory', 1);
        set_config('defaultrequestcategory', $defaultcategory);

        // Create some categories.
        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category();
        $cat3 = $this->getDataGenerator()->create_category();

        // Basic course request.
        $data = new \stdClass();
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
        set_config('lockrequestcategory', 0);
        $data->category = $cat1->id;
        $cr = course_request::create($data);
        $this->assertEquals($cat1->id, $cr->category);
    }

    public function test_approve_request(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        $defaultcategory = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");
        set_config('enablecourserequests', 1);
        set_config('lockrequestcategory', 1);
        set_config('defaultrequestcategory', $defaultcategory);

        // Create some categories.
        $cat1 = $this->getDataGenerator()->create_category();
        $cat2 = $this->getDataGenerator()->create_category();

        // Create a user and allow course requests for him.
        $requester = $this->getDataGenerator()->create_user();
        $roleid = create_role('Course requestor role', 'courserequestor', '');
        assign_capability(
            'moodle/course:request',
            CAP_ALLOW,
            $roleid,
            \context_system::instance()->id
        );
        role_assign($roleid, $requester->id, \context_system::instance()->id);
        accesslib_clear_all_caches_for_unit_testing();

        $data = new \stdClass();
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
        $this->assertCount(1, $sink->get_messages_by_component_and_type('core', 'courserequestapproved'));
        $sink->close();
        $course = $DB->get_record('course', ['id' => $id]);
        $this->assertEquals($data->fullname, $course->fullname);
        $this->assertEquals($data->shortname, $course->shortname);
        $this->assertEquals($data->summary_editor['text'], $course->summary);
        $this->assertEquals($data->summary_editor['format'], $course->summaryformat);
        $this->assertEquals(1, $course->requested);
        $this->assertEquals($defaultcategory, $course->category);

        // Test with category.
        set_config('lockrequestcategory', 0);
        set_config('defaultrequestcategory', $cat2->id);
        $data->shortname .= ' 2nd';
        $data->category = $cat1->id;
        $this->setUser($requester);
        $cr = course_request::create($data);
        $this->setAdminUser();
        $sink = $this->redirectMessages();
        $id = $cr->approve();
        $this->assertCount(1, $sink->get_messages_by_component_and_type('core', 'courserequestapproved'));
        $sink->close();
        $course = $DB->get_record('course', ['id' => $id]);
        $this->assertEquals($data->category, $course->category);
    }

    public function test_reject_request(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        $this->setAdminUser();
        set_config('enablecourserequests', 1);
        set_config('lockrequestcategory', 1);
        set_config('defaultrequestcategory', $DB->get_field_select('course_categories', "MIN(id)", "parent=0"));

        // Create a user and allow course requests for him.
        $requester = $this->getDataGenerator()->create_user();
        $roleid = create_role('Course requestor role', 'courserequestor', '');
        assign_capability(
            'moodle/course:request',
            CAP_ALLOW,
            $roleid,
            \context_system::instance()->id
        );
        role_assign($roleid, $requester->id, \context_system::instance()->id);
        accesslib_clear_all_caches_for_unit_testing();

        $data = new \stdClass();
        $data->fullname = 'Həllo World!';
        $data->shortname = 'Hi th€re!';
        $data->summary_editor['text'] = 'Lorem Ipsum ©';
        $data->summary_editor['format'] = FORMAT_HTML;
        $data->reason = 'Because PHP Unit is cool.';

        $this->setUser($requester);
        $cr = course_request::create($data);
        $this->assertTrue($DB->record_exists('course_request', ['id' => $cr->id]));

        $this->setAdminUser();
        $sink = $this->redirectMessages();
        $cr->reject('Sorry!');
        $this->assertFalse($DB->record_exists('course_request', ['id' => $cr->id]));
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
    }

    /**
     * Tests for the course_request::can_request
     */
    public function test_can_request_course(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $cat1 = $CFG->defaultrequestcategory;
        $cat2 = $this->getDataGenerator()->create_category()->id;
        $cat3 = $this->getDataGenerator()->create_category()->id;
        $context1 = context_coursecat::instance($cat1);
        $context2 = context_coursecat::instance($cat2);
        $context3 = context_coursecat::instance($cat3);
        $this->setUser($user);

        // By default users don't have capability to request courses.
        $this->assertFalse(course_request::can_request(context_system::instance()));
        $this->assertFalse(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertFalse(course_request::can_request($context3));

        // Allow for the 'user' role the capability to request courses.
        $userroleid = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability(
            'moodle/course:request',
            CAP_ALLOW,
            $userroleid,
            context_system::instance()->id
        );
        accesslib_clear_all_caches_for_unit_testing();

        // Lock category selection.
        $CFG->lockrequestcategory = 1;

        // Now user can only request course in the default category or in system context.
        $this->assertTrue(course_request::can_request(context_system::instance()));
        $this->assertTrue(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertFalse(course_request::can_request($context3));

        // Enable category selection. User can request course anywhere.
        $CFG->lockrequestcategory = 0;
        $this->assertTrue(course_request::can_request(context_system::instance()));
        $this->assertTrue(course_request::can_request($context1));
        $this->assertTrue(course_request::can_request($context2));
        $this->assertTrue(course_request::can_request($context3));

        // Remove cap from cat2.
        $roleid = create_role('Test role', 'testrole', 'Test role description');
        assign_capability(
            'moodle/course:request',
            CAP_PROHIBIT,
            $roleid,
            $context2->id,
            true
        );
        role_assign($roleid, $user->id, $context2->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertTrue(course_request::can_request(context_system::instance()));
        $this->assertTrue(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertTrue(course_request::can_request($context3));

        // Disable course request functionality.
        $CFG->enablecourserequests = false;
        $this->assertFalse(course_request::can_request(context_system::instance()));
        $this->assertFalse(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertFalse(course_request::can_request($context3));
    }

    /**
     * Tests for the course_request::can_approve
     */
    public function test_can_approve_course_request(): void {
        global $CFG;
        $this->resetAfterTest();

        $requestor = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();
        $cat1 = $CFG->defaultrequestcategory;
        $cat2 = $this->getDataGenerator()->create_category()->id;
        $cat3 = $this->getDataGenerator()->create_category()->id;

        // Enable course requests. Default 'user' role has capability to request courses.
        $CFG->enablecourserequests = true;
        $CFG->lockrequestcategory = 0;
        $this->setUser($requestor);
        $requestdata = ['summary_editor' => ['text' => '', 'format' => 0], 'name' => 'Req', 'reason' => 'test'];
        $request1 = course_request::create((object)($requestdata));
        $request2 = course_request::create((object)($requestdata + ['category' => $cat2]));
        $request3 = course_request::create((object)($requestdata + ['category' => $cat3]));

        $this->setUser($user);
        // Add capability to approve courses.
        $roleid = create_role('Test role', 'testrole', 'Test role description');
        assign_capability(
            'moodle/site:approvecourse',
            CAP_ALLOW,
            $roleid,
            context_system::instance()->id,
            true
        );
        role_assign($roleid, $user->id, context_coursecat::instance($cat2)->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertFalse($request1->can_approve());
        $this->assertTrue($request2->can_approve());
        $this->assertFalse($request3->can_approve());

        // Delete category where course was requested. Now only site-wide manager can approve it.
        core_course_category::get($cat2, MUST_EXIST, true)->delete_full(false);
        $this->assertFalse($request2->can_approve());

        $this->setAdminUser();
        $this->assertTrue($request2->can_approve());
    }
}
