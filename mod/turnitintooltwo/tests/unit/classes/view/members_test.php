<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/turnitintooltwo/classes/view/members.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_assignment.class.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_view.class.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/lti/lib.php');

/**
 * Tests for classes/view/members
 *
 * @package turnitintooltwo
 */
class mod_turnitintooltwo_view_members_testcase extends advanced_testcase {
    /**
     * Test display role given returns as the expected Turnitin role
     */
    public function test_get_role_for_display_role() {
        $members = new members_view();

        $role = $members->get_role_for_display_role(null);
        $this->assertEquals('Learner', $role);

        $role = $members->get_role_for_display_role("tutors");
        $this->assertEquals('Instructor', $role);

        $role = $members->get_role_for_display_role("students");
        $this->assertEquals('Learner', $role);

        $role = $members->get_role_for_display_role("foobar");
        $this->assertEquals('Learner', $role);
    }

    /**
     * Test given a role the correct intro message for the members view is
     * generated.
     */
    public function test_build_intro_message() {
        $members = new members_view();

        $actualmessage       = $members->build_intro_message();
        $expectedmessagetext = get_string('turnitinstudents_desc', 'turnitintooltwo');

        $this->assertStringContainsString($expectedmessagetext, $actualmessage);

        $actualmessage       = $members->build_intro_message("students");
        $expectedmessagetext = get_string("turnitinstudents_desc", "turnitintooltwo");

        $this->assertStringContainsString($expectedmessagetext, $actualmessage);

        $actualmessage       = $members->build_intro_message("foobar");
        $expectedmessagetext = get_string("turnitinstudents_desc", "turnitintooltwo");

        $this->assertStringContainsString($expectedmessagetext, $actualmessage);

        $actualmessage       = $members->build_intro_message("tutors");
        $expectedmessagetext = get_string("turnitintutors_desc", "turnitintooltwo");

        $this->assertStringContainsString($expectedmessagetext, $actualmessage);
    }

    /**
     * Test given a display role the correct table HTML is generated
     */
    public function test_build_members_table() {
        // fake/stub a turnitin two view class and the method to render the
        // table
        $observer = $this->getMockBuilder(turnitintooltwo_view::class)
            ->setMethods(['init_tii_member_by_role_table'])
            ->getMock();

        // add assertions to the turnitin two view class method that renders the
        // members table is called with the expected arguments
        $observer->expects($this->exactly(4))
            ->method('init_tii_member_by_role_table')
            ->willReturn('<table>fake table!</table>')
            ->withConsecutive(
                [$this->equalTo('fakemodule'), $this->equalTo('faketiiassignment'), $this->equalTo('Learner')],
                [$this->equalTo('fakemodule'), $this->equalTo('faketiiassignment'), $this->equalTo('Instructor')],
                [$this->equalTo('fakemodule'), $this->equalTo('faketiiassignment'), $this->equalTo('Learner')],
                [$this->equalTo('fakemodule'), $this->equalTo('faketiiassignment'), $this->equalTo('Learner')]
            );

        // create out members view instance passing our stubbed turnitin two
        // view class instance
        $members = new members_view('fakecourse', 'fakemodule', $observer, 'faketiiassignment');

        // check with valid Learner role
        $table = $members->build_members_table('students');
        $this->assertEquals('<table>fake table!</table>', $table);

        // check with valid Instructor role
        $table = $members->build_members_table('tutors');
        $this->assertEquals('<table>fake table!</table>', $table);

        // check no role falls back to Learner
        $table = $members->build_members_table();
        $this->assertEquals('<table>fake table!</table>', $table);

        // check invalid role falls back to Learner
        $table = $members->build_members_table('foobar');
        $this->assertEquals('<table>fake table!</table>', $table);
    }

    /**
     * Test given a display role the correct add tutors form is generated.
     */
    public function test_build_add_tutors_form() {
        // fake/stub a turnitin two view class and method to render the add
        // tutors form
        $faketiiview = $this->getMockBuilder(turnitintooltwo_view::class)
            ->setMethods(['show_add_tii_tutors_form'])
            ->getMock();

        // add assertions that the generate add tutors form is called as
        // expected
        $faketiiview->expects($this->once())
            ->method('show_add_tii_tutors_form')
            ->willReturn('<form>fake form!</form>')
            ->withConsecutive(
                [$this->equalTo('fakemodule'), $this->equalTo('faketutors')]
            );

        // fake/stub a fake tii assignment
        $faketiiassignment = $this->getMockBuilder(turnitintooltwo_assignment::class)
            ->disableOriginalConstructor()
            ->setMethods(['get_tii_users_by_role'])
            ->getMock();

        // make assignment get users by role to always return something, we
        // check that our tii two view stub gets called with the result of this
        // stub.
        $faketiiassignment->expects($this->once())
            ->method('get_tii_users_by_role')
            ->willReturn('faketutors');

        // create our members view class instance with the fake tii view and
        // assignment
        $members = new members_view(null, 'fakemodule', $faketiiview, $faketiiassignment);

        // test when not displaying tutor members
        $form = $members->build_add_tutors_form("foobar");
        $this->assertEquals('', $form);

        // test when displaying tutor members
        $form = $members->build_add_tutors_form("tutors");
        $this->assertEquals('<form>fake form!</form>', $form);
    }
}
