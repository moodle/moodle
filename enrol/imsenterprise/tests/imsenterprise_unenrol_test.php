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

namespace enrol_imsenterprise;

use core_course_category;
use enrol_imsenterprise_plugin;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/enrol/imsenterprise/locallib.php');
require_once($CFG->dirroot . '/enrol/imsenterprise/lib.php');

/**
 * IMS Enterprise test case
 *
 * @package    enrol_imsenterprise
 * @category   test
 * @copyright  2019 Segun Babalola
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \enrol_imsenterprise_plugin
 */
class imsenterprise_unenrol_test extends \advanced_testcase {

    /**
     * @var $imsplugin enrol_imsenterprise_plugin IMS plugin instance.
     */
    public $imsplugin;

    /**
     * Setup required for all tests.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->imsplugin = enrol_get_plugin('imsenterprise');
        $this->set_test_config();
    }

    /**
     * Sets the plugin configuration for testing
     */
    public function set_test_config() {
        $this->imsplugin->set_config('mailadmins', false);
        $this->imsplugin->set_config('prev_path', '');
        $this->imsplugin->set_config('createnewusers', true);
        $this->imsplugin->set_config('imsupdateusers', true);
        $this->imsplugin->set_config('createnewcourses', true);
        $this->imsplugin->set_config('updatecourses', true);
        $this->imsplugin->set_config('createnewcategories', true);
        $this->imsplugin->set_config('categoryseparator', '');
        $this->imsplugin->set_config('categoryidnumber', false);
        $this->imsplugin->set_config('nestedcategories', false);
    }


    /**
     * Creates an IMS enterprise XML file and adds it's path to config settings.
     *
     * @param bool|array $users false or array of users StdClass
     * @param bool|array $courses false or of courses StdClass
     * @param bool|array $usercoursemembership false or of courses StdClass
     */
    public function set_xml_file($users = false, $courses = false, $usercoursemembership = false) {

        $xmlcontent = '<enterprise>';

        // Users.
        if (!empty($users) && is_array($users)) {
            foreach ($users as $user) {
                $xmlcontent .= '<person' . (!empty($user->recstatus) ? ' recstatus="'.$user->recstatus.'"' : '').'>';
                $xmlcontent .= '<sourcedid><source>TestSource</source><id>'.$user->idnumber.'</id></sourcedid>';
                $xmlcontent .= '<userid' . (!empty($user->auth) ? ' authenticationtype="'.$user->auth.'"' : '');
                $xmlcontent .= '>'.$user->username.'</userid>';
                $xmlcontent .= '<name>'
                                .'<fn>'.$user->firstname.' '.$user->lastname.'</fn>'
                                .'<n><family>'.$user->lastname.'</family><given>'.$user->firstname.'</given></n>'
                                .'</name>'
                                .'<email>'.$user->email.'</email>';
                $xmlcontent .= '</person>';
            }
        }

        // Courses.
        // Mapping based on default course attributes - IMS group tags mapping.
        if (!empty($courses) && is_array($courses)) {
            foreach ($courses as $course) {

                $xmlcontent .= '<group' . (!empty($course->recstatus) ? ' recstatus="'.$course->recstatus.'"' : '').'>';
                $xmlcontent .= '<sourcedid><source>TestSource</source><id>'.$course->idnumber.'</id></sourcedid>';
                $xmlcontent .= '<description>'.(!empty($course->imsshort) ? '<short>'.$course->imsshort.'</short>' : '');
                $xmlcontent .= (!empty($course->imslong) ? '<long>'.$course->imslong.'</long>' : '');
                $xmlcontent .= (!empty($course->imsfull) ? '<full>'.$course->imsfull.'</full>' : '');
                $xmlcontent .= '</description>';

                // The orgunit tag value is used by moodle as category name.
                $xmlcontent .= '<org>';

                // Optional category name.
                if (isset($course->category)) {
                    if (is_array($course->category)) {
                        foreach ($course->category as $category) {
                            $xmlcontent .= '<orgunit>' . $category . '</orgunit>';
                        }
                    } else if (is_object($course->category)) {
                        $xmlcontent .= '<orgunit>' . $course->category->name . '</orgunit>';
                    } else if (!empty($course->category)) {
                        $xmlcontent .= '<orgunit>' . $course->category . '</orgunit>';
                    }
                }

                $xmlcontent .= '</org>';
                $xmlcontent .= '</group>';
            }
        }

        // User course membership (i.e. roles and enrolments).
        if (!empty($usercoursemembership) && is_array($usercoursemembership)) {
            foreach ($usercoursemembership as $crsemship) {

                // Only process records that have a source/id (i.e. course code) set in the IMS file.
                // Note that we could also check that there is a corresponding $course with the course code given here,
                // however it is possible that we want to test the behaviour of orphan  membership elements in future,
                // so leaving the check out for now.
                if (isset($crsemship->crseidnumber) && isset($crsemship->member) && is_array($crsemship->member)
                    && count($crsemship->member)) {
                    $xmlcontent .= '<membership><sourcedid><source>TestSource</source><id>'
                        .$crsemship->crseidnumber . '</id></sourcedid>';

                    foreach ($crsemship->member as $crsemember) {
                        if (!empty($crsemember->useridnumber)) {
                            $xmlcontent .= '<member>';
                            $xmlcontent .= '<sourcedid><source>TestSource</source><id>'. $crsemember->useridnumber
                                .'</id></sourcedid>';

                            // Indicates whether the member is a Person (1) or another Group (2).
                            // We're only handling user membership here, so hard-code value of 1.
                            $xmlcontent .= '<idtype>1</idtype>';

                            if (isset($crsemember->role) && is_array($crsemember->role)) {
                                foreach ($crsemember->role as $role) {
                                    $xmlcontent .= '<role roletype="'.$role->roletype.'" recstatus="'.$role->recstatus.'">';
                                    $xmlcontent .= '<userid/>';
                                    $xmlcontent .= '<status>' . $role->rolestatus . '</status>';
                                    $xmlcontent .= '</role>';
                                }
                            }

                            $xmlcontent .= '</member>';
                        }
                    }

                    $xmlcontent .= '</membership>';
                }
            }
        }

        $xmlcontent .= '</enterprise>';

        // Creating the XML file.
        $filename = 'ims_' . rand(1000, 9999) . '.xml';
        $tmpdir = make_temp_directory('enrol_imsenterprise');
        $xmlfilepath = $tmpdir . '/' . $filename;
        file_put_contents($xmlfilepath, $xmlcontent);

        // Setting the file path in CFG.
        $this->imsplugin->set_config('imsfilelocation', $xmlfilepath);
    }

    /**
     * Utility function for generating test user records
     *
     * @param int $numberofrecordsrequired - number of test users required
     * @return array of StdClass objects representing test user records
     */
    private function generate_test_user_records($numberofrecordsrequired) {
        $users = [];
        for ($i = 0; $i < $numberofrecordsrequired; $i++) {
            $usernumber = $i + 101;
            $users[] = (object)[
                'recstatus' => enrol_imsenterprise_plugin::IMSENTERPRISE_ADD,
                'idnumber' => $usernumber,
                'username' => 'UID' .$usernumber,
                'email' => 'user' . $usernumber . '@moodle.org',
                'firstname' => 'User' . $usernumber . ' firstname',
                'lastname' => 'User' . $usernumber . ' lastname'
            ];
        }

        return $users;
    }

    /**
     * Utility function for generating test course records
     *
     * @param int $numberofrecordsrequired - number of test course records required
     * @return array of StdClass objects representing test course records
     */
    private function generate_test_course_records($numberofrecordsrequired) {
        $courses = [];
        for ($i = 0; $i < $numberofrecordsrequired; $i++) {
            $coursenumber = $i + 101;
            $courses[] = (object)[
                'recstatus' => enrol_imsenterprise_plugin::IMSENTERPRISE_ADD,
                'idnumber' => 'CID' . $coursenumber,
                'imsshort' => 'Course ' . $coursenumber,
                'category' => core_course_category::get_default()
            ];
        }

        return $courses;
    }

    /**
     * Utility function for generating test membership structure for given users and courses.
     * Linkmatrix is expected to be in [row, col] format, where courses are rows and users are columns.
     * Each element of the link matrix is expected to contain <roletype>:<role status>:<role recstatus>.
     *
     * @param array $users
     * @param array $courses
     * @param array $linkmatrix - matrix/two dimensional array of required user course enrolments
     * @return array
     */
    private function link_users_with_courses($users, $courses, $linkmatrix) {

        $memberships = [];

        foreach ($courses as $i => $c) {

            $membership = new stdClass();
            $membership->member = [];
            $membership->crseidnumber = $c->idnumber;

            foreach ($users as $j => $u) {
                if (isset($linkmatrix[$i][$j])) {

                    list($roletype, $rolestatus, $rolerecstatus) = explode(':', $linkmatrix[$i][$j]);

                    if (strlen($rolerecstatus) && strlen($roletype) && strlen($rolestatus)) {
                        $membership->member[] = (object)[
                            'useridnumber' => $u->idnumber,
                            'role' => [(object)[
                                'roletype' => $roletype,
                                'rolestatus' => $rolestatus,
                                'recstatus' => $rolerecstatus
                            ]]
                        ];
                    }
                }
            }

            $memberships[] = $membership;
        }

        return $memberships;
    }

    /**
     * Add new users, courses and enrolments
     */
    public function test_users_are_enroled_on_courses(): void {
        global $DB;

        $prevnuserenrolments = $DB->count_records('user_enrolments');
        $prevnusers = $DB->count_records('user');
        $prevncourses = $DB->count_records('course');

        $courses = $this->generate_test_course_records(1);
        $users = $this->generate_test_user_records(1);
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            [
                ['01:1:1'] // First course.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevnuserenrolments + 1), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));
    }

    /**
     * Check that the unenrol actions are completely ignored when "unenrol" setting is disabled
     */
    public function test_no_action_when_unenrol_disabled(): void {
        global $DB;

        $prevnuserenrolments = $DB->count_records('user_enrolments');
        $prevnusers = $DB->count_records('user');
        $prevncourses = $DB->count_records('course');

        // Create user and course.
        $courses = $this->generate_test_course_records(3);
        $users = $this->generate_test_user_records(2);
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:1:1', '01:1:1'], // Course 1.
                ['01:1:1', '01:1:1'], // Course 2.
                ['::', '01:1:1'], // Course 3.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevnuserenrolments + 5), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnusers + 2), $DB->count_records('user'));
        $this->assertEquals(($prevncourses + 3), $DB->count_records('course'));

        // Disallow unenrolment, and check that unenroling has no effect.
        $this->imsplugin->set_config('imsunenrol', 0);

        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:1:3', '01:1:3'], // Course 1.
                ['::', '01:1:3'], // Course 2.
                ['::', '01:1:3'], // Course 3.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevnuserenrolments + 5), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnusers + 2), $DB->count_records('user'));
        $this->assertEquals(($prevncourses + 3), $DB->count_records('course'));
    }

    /**
     * When a user has existing roles and enrolments, they are unaffected by IMS instructions for other courses
     */
    public function test_existing_roles_and_enrolments_unaffected(): void {

        global $DB;

        $this->imsplugin->set_config('imsunenrol', 1);
        $this->imsplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        $prevnuserenrolments = $DB->count_records('user_enrolments');
        $prevnusers = $DB->count_records('user');
        $prevncourses = $DB->count_records('course');

        $courses = $this->generate_test_course_records(2);

        // Create_course seems to expect the category to be passed as ID, so extract from the object.
        $course1 = $courses[0];
        $course1->category = $course1->category->id;
        $course1 = $this->getDataGenerator()->create_course($courses[0]);

        // Enrol user1 on course1.
        $DB->insert_record('enrol', (object)['enrol' => 'imsenterprise',
            'courseid' => $course1->id, 'status' => 1, 'roleid' => 5
        ], true);

        $user1 = $this->getDataGenerator()->create_and_enrol($course1, 'student',
            ['idnumber' => 'UserIDNumber100'], 'imsenterprise');
        $user1->username = $user1->idnumber;

        // Confirm user was added and that the enrolment happened.
        $this->assertEquals(($prevnuserenrolments + 1), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));

        // Capture DB id of enrolment record.
        $initialusernerolment = $DB->get_record('user_enrolments', ['userid' => $user1->id],
            '*', MUST_EXIST);
        $initialroleassigned = $DB->get_record('role_assignments', ['userid' => $user1->id],
            '*', MUST_EXIST);

        // Add a new enrolment for the same user via IMS file.
        $coursemembership = $this->link_users_with_courses(
            [$user1],
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['::'], // Course 1.
                ['01:1:1'], // Course 2.
            ]
        );

        $this->set_xml_file([$user1], $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(2, $DB->count_records('user_enrolments', ['userid' => $user1->id]));
        $this->assertEquals(($prevncourses + 2), $DB->count_records('course'));

        // Unenrol the user from course2 via IMS file.
        $coursemembership = $this->link_users_with_courses(
            [$user1],
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['::'], // Course 1.
                ['01:0:3'], // Course 2.
            ]
        );

        $this->set_xml_file([$user1], $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(1, $DB->count_records('user_enrolments', ['userid' => $user1->id]));
        $this->assertTrue($DB->record_exists('user_enrolments', ['id' => $initialusernerolment->id,
            'userid' => $initialusernerolment->userid]));
        $this->assertTrue($DB->record_exists('role_assignments', ['id' => $initialroleassigned->id,
            'userid' => $initialusernerolment->userid]));
    }

    /**
     * Enrolments alone are disabled
     */
    public function test_disable_enrolments_only(): void {

        global $DB;

        $this->imsplugin->set_config('imsunenrol', 1);
        $this->imsplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPEND);

        $prevnuserenrolments = $DB->count_records('user_enrolments');
        $prevnroles = $DB->count_records('role_assignments');
        $prevnusers = $DB->count_records('user');
        $prevncourses = $DB->count_records('course');

        $courses = $this->generate_test_course_records(1);
        $users = $this->generate_test_user_records(1);

        // Add a new enrolment for the same user via IMS file.
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:1:1'], // Course 1.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevnuserenrolments + 1), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnroles + 1), $DB->count_records('role_assignments'));

        // Capture DB ids.
        $dbuser = $DB->get_record('user', ['idnumber' => $users[0]->idnumber], '*', MUST_EXIST);

        $dbenrolment = $DB->get_record('user_enrolments',
            ['userid' => $dbuser->id, 'status' => ENROL_USER_ACTIVE],
            '*', MUST_EXIST
        );

        $dbrole = $DB->get_record('role_assignments', ['userid' => $dbuser->id], '*', MUST_EXIST);

        // Unenrol the user, check that the enrolment and role exist, but the enrolment is suspended.
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:0:3'], // Course 1.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevnuserenrolments + 1), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnroles + 1), $DB->count_records('role_assignments'));

        $this->assertEquals(1, $DB->count_records('user_enrolments',
            ['userid' => $dbuser->id, 'id' => $dbenrolment->id, 'status' => ENROL_USER_SUSPENDED]));

        $this->assertEquals(1, $DB->count_records('role_assignments',
            ['userid' => $dbuser->id, 'id' => $dbrole->id]));
    }

    /**
     * Enrolments are disabled but retained) and roles removed
     */
    public function test_disable_enrolments_and_remove_roles(): void {

        global $DB;

        $this->imsplugin->set_config('imsunenrol', 1);
        $this->imsplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);

        $prevnuserenrolments = $DB->count_records('user_enrolments');
        $prevnroles = $DB->count_records('role_assignments');
        $prevnusers = $DB->count_records('user');
        $prevncourses = $DB->count_records('course');

        $courses = $this->generate_test_course_records(1);
        $users = $this->generate_test_user_records(1);

        // Add a new enrolment for the same user via IMS file.
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:1:1'], // Course 1.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevnuserenrolments + 1), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnroles + 1), $DB->count_records('role_assignments'));

        // Capture DB ids.
        $dbuser = $DB->get_record('user', ['idnumber' => $users[0]->idnumber], '*', MUST_EXIST);

        $dbenrolment = $DB->get_record('user_enrolments',
            ['userid' => $dbuser->id, 'status' => ENROL_USER_ACTIVE],
            '*', MUST_EXIST
        );

        $dbrole = $DB->get_record('role_assignments', ['userid' => $dbuser->id], '*', MUST_EXIST);

        // Unenrol the user, check that the enrolment and role exist, but the enrolment is suspended.
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:0:3'], // Course 1.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevnuserenrolments + 1), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnroles), $DB->count_records('role_assignments'));

        $this->assertEquals(1, $DB->count_records('user_enrolments',
            ['userid' => $dbuser->id, 'id' => $dbenrolment->id, 'status' => ENROL_USER_SUSPENDED]));

        $this->assertEquals(0, $DB->count_records('role_assignments',
            ['userid' => $dbuser->id, 'id' => $dbrole->id]));

    }

    /**
     * Enrolments and roles are deleted for specified user
     */
    public function test_delete_roles_and_enrolments(): void {

        global $DB;

        $this->imsplugin->set_config('imsunenrol', 1);
        $this->imsplugin->set_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        $prevnuserenrolments = $DB->count_records('user_enrolments');
        $prevnroles = $DB->count_records('role_assignments');
        $prevnusers = $DB->count_records('user');
        $prevncourses = $DB->count_records('course');

        $courses = $this->generate_test_course_records(1);
        $users = $this->generate_test_user_records(1);

        // Add a new enrolment for the same user via IMS file.
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:1:1'], // Course 1.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevnuserenrolments + 1), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnroles + 1), $DB->count_records('role_assignments'));

        // Capture DB ids.
        $dbuser = $DB->get_record('user', ['idnumber' => $users[0]->idnumber], '*', MUST_EXIST);

        $dbenrolment = $DB->get_record('user_enrolments',
            ['userid' => $dbuser->id, 'status' => ENROL_USER_ACTIVE],
            '*', MUST_EXIST
        );

        $dbrole = $DB->get_record('role_assignments', ['userid' => $dbuser->id], '*', MUST_EXIST);

        // Unenrol the user, check that the enrolment and role exist, but the enrolment is suspended.
        $coursemembership = $this->link_users_with_courses(
            $users,
            $courses,
            // Role types: 01=Learner, 02=Instructor, 03=Content Dev, 04=Member, 05=Manager, 06=Mentor, 07=Admin, 08=TA.
            // Role statuses: 0=Inactive, 1=Active.
            // Role recstatus:  1=Add, 2=Update, 3=Delete.
            // Format of matrix elements: <roletype>:<role status>:<role recstatus>.
            [
                ['01:1:3'], // Course 1.
            ]
        );

        $this->set_xml_file($users, $courses, $coursemembership);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));
        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals(($prevnuserenrolments), $DB->count_records('user_enrolments'));
        $this->assertEquals(($prevnroles), $DB->count_records('role_assignments'));

        $this->assertEquals(0, $DB->count_records('user_enrolments',
            ['userid' => $dbuser->id, 'id' => $dbenrolment->id, 'status' => ENROL_USER_SUSPENDED]));

        $this->assertEquals(0, $DB->count_records('role_assignments',
            ['userid' => $dbuser->id, 'id' => $dbrole->id]));
    }
}
