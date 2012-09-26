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
 * User external PHPunit tests
 *
 * @package    core_user
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/user/externallib.php');

class core_user_external_testcase extends externallib_advanced_testcase {

    /**
     * Test get_course_user_profiles
     */
    public function test_get_course_user_profiles() {
        global $USER, $CFG;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();
        $user1 = array(
            'username' => 'usernametest1',
            'idnumber' => 'idnumbertest1',
            'firstname' => 'First Name User Test 1',
            'lastname' => 'Last Name User Test 1',
            'email' => 'usertest1@email.com',
            'address' => '2 Test Street Perth 6000 WA',
            'phone1' => '01010101010',
            'phone2' => '02020203',
            'icq' => 'testuser1',
            'skype' => 'testuser1',
            'yahoo' => 'testuser1',
            'aim' => 'testuser1',
            'msn' => 'testuser1',
            'department' => 'Department of user 1',
            'institution' => 'Institution of user 1',
            'description' => 'This is a description for user 1',
            'descriptionformat' => FORMAT_MOODLE,
            'city' => 'Perth',
            'url' => 'http://moodle.org',
            'country' => 'au'
            );
        $user1 = self::getDataGenerator()->create_user($user1);
        if (!empty($CFG->usetags)) {
            require_once($CFG->dirroot . '/user/editlib.php');
            require_once($CFG->dirroot . '/tag/lib.php');
            $user1->interests = array('Cinema', 'Tennis', 'Dance', 'Guitar', 'Cooking');
            useredit_update_interests($user1, $user1->interests);
        }
        $user2 = self::getDataGenerator()->create_user();

        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/user:viewdetails', $context->id);

        // Enrol the users in the course.
        // We use the manual plugin.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance, $user1->id, $roleid);
        $enrol->enrol_user($instance, $user2->id, $roleid);
        $enrol->enrol_user($instance, $USER->id, $roleid);

        // Call the external function.
        $enrolledusers = core_user_external::get_course_user_profiles(array(
                    array('userid' => $USER->id, 'courseid' => $course->id),
                    array('userid' => $user1->id, 'courseid' => $course->id),
                    array('userid' => $user2->id, 'courseid' => $course->id)));

        // Check we retrieve the good total number of enrolled users + no error on capability.
        $this->assertEquals(3, count($enrolledusers));

        // Do the same call as admin to receive all possible fields.
        $this->setAdminUser();
        $USER->email = "admin@fakeemail.com";

        // Call the external function.
        $enrolledusers = core_user_external::get_course_user_profiles(array(
                    array('userid' => $USER->id, 'courseid' => $course->id),
                    array('userid' => $user1->id, 'courseid' => $course->id),
                    array('userid' => $user2->id, 'courseid' => $course->id)));

        foreach($enrolledusers as $enrolleduser) {
            if ($enrolleduser['username'] == $user1->username) {
                $this->assertEquals($user1->idnumber, $enrolleduser['idnumber']);
                $this->assertEquals($user1->firstname, $enrolleduser['firstname']);
                $this->assertEquals($user1->lastname, $enrolleduser['lastname']);
                $this->assertEquals($user1->email, $enrolleduser['email']);
                $this->assertEquals($user1->address, $enrolleduser['address']);
                $this->assertEquals($user1->phone1, $enrolleduser['phone1']);
                $this->assertEquals($user1->phone2, $enrolleduser['phone2']);
                $this->assertEquals($user1->icq, $enrolleduser['icq']);
                $this->assertEquals($user1->skype, $enrolleduser['skype']);
                $this->assertEquals($user1->yahoo, $enrolleduser['yahoo']);
                $this->assertEquals($user1->aim, $enrolleduser['aim']);
                $this->assertEquals($user1->msn, $enrolleduser['msn']);
                $this->assertEquals($user1->department, $enrolleduser['department']);
                $this->assertEquals($user1->institution, $enrolleduser['institution']);
                $this->assertEquals($user1->description, $enrolleduser['description']);
                $this->assertEquals(FORMAT_HTML, $enrolleduser['descriptionformat']);
                $this->assertEquals($user1->city, $enrolleduser['city']);
                $this->assertEquals($user1->country, $enrolleduser['country']);
                $this->assertEquals($user1->url, $enrolleduser['url']);
                if (!empty($CFG->usetags)) {
                    $this->assertEquals(implode(', ', $user1->interests), $enrolleduser['interests']);
                }
            }
        }
    }

    /**
     * Test create_users
     */
    public function test_create_users() {
         global $USER, $CFG, $DB;

        $this->resetAfterTest(true);

        $user1 = array(
            'username' => 'usernametest1',
            'password' => 'Moodle2012!',
            'idnumber' => 'idnumbertest1',
            'firstname' => 'First Name User Test 1',
            'lastname' => 'Last Name User Test 1',
            'email' => 'usertest1@email.com',
            'description' => 'This is a description for user 1',
            'city' => 'Perth',
            'country' => 'au'
            );

        $context = context_system::instance();
        $roleid = $this->assignUserCapability('moodle/user:create', $context->id);

        // Call the external function.
        $createdusers = core_user_external::create_users(array($user1));

        // Check we retrieve the good total number of created users + no error on capability.
        $this->assertEquals(1, count($createdusers));

        foreach($createdusers as $createduser) {
            $dbuser = $DB->get_record('user', array('id' => $createduser['id']));
            $this->assertEquals($dbuser->username, $user1['username']);
            $this->assertEquals($dbuser->idnumber, $user1['idnumber']);
            $this->assertEquals($dbuser->firstname, $user1['firstname']);
            $this->assertEquals($dbuser->lastname, $user1['lastname']);
            $this->assertEquals($dbuser->email, $user1['email']);
            $this->assertEquals($dbuser->description, $user1['description']);
            $this->assertEquals($dbuser->city, $user1['city']);
            $this->assertEquals($dbuser->country, $user1['country']);
        }

        // Call without required capability
        $this->unassignUserCapability('moodle/user:create', $context->id, $roleid);
        $this->setExpectedException('required_capability_exception');
        $createdusers = core_user_external::create_users(array($user1));
    }

    /**
     * Test delete_users
     */
    public function test_delete_users() {
        global $USER, $CFG, $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Check the users were correctly created.
        $this->assertEquals(2, $DB->count_records_select('user', 'deleted = 0 AND (id = :userid1 OR id = :userid2)',
                array('userid1' => $user1->id, 'userid2' => $user2->id)));

        $context = context_system::instance();
        $roleid = $this->assignUserCapability('moodle/user:delete', $context->id);

        // Call the external function.
        core_user_external::delete_users(array($user1->id, $user2->id));

        // Check we retrieve no users + no error on capability.
        $this->assertEquals(0, $DB->count_records_select('user', 'deleted = 0 AND (id = :userid1 OR id = :userid2)',
                array('userid1' => $user1->id, 'userid2' => $user2->id)));

        // Call without required capability.
        $this->unassignUserCapability('moodle/user:delete', $context->id, $roleid);
        $this->setExpectedException('required_capability_exception');
        core_user_external::delete_users(array($user1->id, $user2->id));
    }

    /**
     * Test get_users_by_id
     */
    public function test_get_users_by_id() {
        global $USER, $CFG;

        $this->resetAfterTest(true);

        $user1 = array(
            'username' => 'usernametest1',
            'idnumber' => 'idnumbertest1',
            'firstname' => 'First Name User Test 1',
            'lastname' => 'Last Name User Test 1',
            'email' => 'usertest1@email.com',
            'address' => '2 Test Street Perth 6000 WA',
            'phone1' => '01010101010',
            'phone2' => '02020203',
            'icq' => 'testuser1',
            'skype' => 'testuser1',
            'yahoo' => 'testuser1',
            'aim' => 'testuser1',
            'msn' => 'testuser1',
            'department' => 'Department of user 1',
            'institution' => 'Institution of user 1',
            'description' => 'This is a description for user 1',
            'descriptionformat' => FORMAT_MOODLE,
            'city' => 'Perth',
            'url' => 'http://moodle.org',
            'country' => 'au'
            );
        $user1 = self::getDataGenerator()->create_user($user1);
        if (!empty($CFG->usetags)) {
            require_once($CFG->dirroot . '/user/editlib.php');
            require_once($CFG->dirroot . '/tag/lib.php');
            $user1->interests = array('Cinema', 'Tennis', 'Dance', 'Guitar', 'Cooking');
            useredit_update_interests($user1, $user1->interests);
        }
        $user2 = self::getDataGenerator()->create_user();

        $context = context_system::instance();
        $roleid = $this->assignUserCapability('moodle/user:viewdetails', $context->id);

        // Call the external function.
        $returnedusers = core_user_external::get_users_by_id(array(
                    $USER->id, $user1->id, $user2->id));

        // Check we retrieve the good total number of enrolled users + no error on capability.
        $this->assertEquals(3, count($returnedusers));

        // Do the same call as admin to receive all possible fields.
        $this->setAdminUser();
        $USER->email = "admin@fakeemail.com";

        // Call the external function.
        $returnedusers = core_user_external::get_users_by_id(array(
                    $USER->id, $user1->id, $user2->id));

        foreach($returnedusers as $enrolleduser) {
            if ($enrolleduser['username'] == $user1->username) {
                $this->assertEquals($user1->idnumber, $enrolleduser['idnumber']);
                $this->assertEquals($user1->firstname, $enrolleduser['firstname']);
                $this->assertEquals($user1->lastname, $enrolleduser['lastname']);
                $this->assertEquals($user1->email, $enrolleduser['email']);
                $this->assertEquals($user1->address, $enrolleduser['address']);
                $this->assertEquals($user1->phone1, $enrolleduser['phone1']);
                $this->assertEquals($user1->phone2, $enrolleduser['phone2']);
                $this->assertEquals($user1->icq, $enrolleduser['icq']);
                $this->assertEquals($user1->skype, $enrolleduser['skype']);
                $this->assertEquals($user1->yahoo, $enrolleduser['yahoo']);
                $this->assertEquals($user1->aim, $enrolleduser['aim']);
                $this->assertEquals($user1->msn, $enrolleduser['msn']);
                $this->assertEquals($user1->department, $enrolleduser['department']);
                $this->assertEquals($user1->institution, $enrolleduser['institution']);
                $this->assertEquals($user1->description, $enrolleduser['description']);
                $this->assertEquals(FORMAT_HTML, $enrolleduser['descriptionformat']);
                $this->assertEquals($user1->city, $enrolleduser['city']);
                $this->assertEquals($user1->country, $enrolleduser['country']);
                $this->assertEquals($user1->url, $enrolleduser['url']);
                if (!empty($CFG->usetags)) {
                    $this->assertEquals(implode(', ', $user1->interests), $enrolleduser['interests']);
                }
            }
        }
    }

    /**
     * Test update_users
     */
    public function test_update_users() {
        global $USER, $CFG, $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();

        $user1 = array(
            'id' => $user1->id,
            'username' => 'usernametest1',
            'password' => 'Moodle2012!',
            'idnumber' => 'idnumbertest1',
            'firstname' => 'First Name User Test 1',
            'lastname' => 'Last Name User Test 1',
            'email' => 'usertest1@email.com',
            'description' => 'This is a description for user 1',
            'city' => 'Perth',
            'country' => 'au'
            );

        $context = context_system::instance();
        $roleid = $this->assignUserCapability('moodle/user:update', $context->id);

        // Call the external function.
        core_user_external::update_users(array($user1));

        $dbuser = $DB->get_record('user', array('id' => $user1['id']));
        $this->assertEquals($dbuser->username, $user1['username']);
        $this->assertEquals($dbuser->idnumber, $user1['idnumber']);
        $this->assertEquals($dbuser->firstname, $user1['firstname']);
        $this->assertEquals($dbuser->lastname, $user1['lastname']);
        $this->assertEquals($dbuser->email, $user1['email']);
        $this->assertEquals($dbuser->description, $user1['description']);
        $this->assertEquals($dbuser->city, $user1['city']);
        $this->assertEquals($dbuser->country, $user1['country']);

        // Call without required capability.
        $this->unassignUserCapability('moodle/user:update', $context->id, $roleid);
        $this->setExpectedException('required_capability_exception');
        core_user_external::update_users(array($user1));
    }
}