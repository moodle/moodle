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
 * IMS Enterprise enrolment tests.
 *
 * @package    enrol_imsenterprise
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/enrol/imsenterprise/locallib.php');
require_once($CFG->dirroot . '/enrol/imsenterprise/lib.php');

/**
 * IMS Enterprise test case
 *
 * @package    enrol_imsenterprise
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_imsenterprise_testcase extends advanced_testcase {

    /**
     * @var $imsplugin enrol_imsenterprise_plugin IMS plugin instance.
     */
    public $imsplugin;

    /**
     * Setup required for all tests.
     */
    protected function setUp() {
        $this->resetAfterTest(true);
        $this->imsplugin = enrol_get_plugin('imsenterprise');
        $this->set_test_config();
    }

    /**
     * With an empty IMS enterprise file
     */
    public function test_emptyfile() {
        global $DB;

        $prevncourses = $DB->count_records('course');
        $prevnusers = $DB->count_records('user');

        $this->set_xml_file(false, false);
        $this->imsplugin->cron();

        $this->assertEquals($prevncourses, $DB->count_records('course'));
        $this->assertEquals($prevnusers, $DB->count_records('user'));
    }

    /**
     * Existing users are not created again
     */
    public function test_users_existing() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $prevnusers = $DB->count_records('user');

        $users = array($user1, $user2);
        $this->set_xml_file($users);
        $this->imsplugin->cron();

        $this->assertEquals($prevnusers, $DB->count_records('user'));
    }

    /**
     * Add new users
     */
    public function test_users_add() {
        global $DB;

        $prevnusers = $DB->count_records('user');

        $user1 = new StdClass();
        $user1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $user1->username = 'u1';
        $user1->email = 'u1@example.com';
        $user1->firstname = 'U';
        $user1->lastname = '1';

        $users = array($user1);
        $this->set_xml_file($users);
        $this->imsplugin->cron();

        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
    }

    /**
     * Add new users and set an auth type
     */
    public function test_users_add_with_auth() {
        global $DB;

        $prevnusers = $DB->count_records('user');

        $user2 = new StdClass();
        $user2->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $user2->username = 'u2';
        $user2->auth = 'cas';
        $user2->email = 'u2@u2.org';
        $user2->firstname = 'U';
        $user2->lastname = '2';

        $users = array($user2);
        $this->set_xml_file($users);
        $this->imsplugin->cron();

        $dbuser = $DB->get_record('user', array('username' => $user2->username));
        // TODO: MDL-15863 this needs more work due to multiauth changes, use first auth for now.
        $dbauth = explode(',', $dbuser->auth);
        $dbauth = reset($dbauth);

        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
        $this->assertEquals($dbauth, $user2->auth);
    }


    /**
     * Update user
     */
    public function test_user_update() {
        global $DB;

        $user = $this->getDataGenerator()->create_user(array('idnumber' => 'test-update-user'));
        $imsuser = new stdClass();
        $imsuser->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_UPDATE;
        // THIS SHOULD WORK, surely?: $imsuser->username = $user->username;
        // But this is required...
        $imsuser->username = $user->idnumber;
        $imsuser->email = 'u3@u3.org';
        $imsuser->firstname = 'U';
        $imsuser->lastname = '3';

        $this->set_xml_file(array($imsuser));
        $this->imsplugin->cron();
        $dbuser = $DB->get_record('user', array('id' => $user->id), '*', MUST_EXIST);
        $this->assertEquals($imsuser->email, $dbuser->email);
        $this->assertEquals($imsuser->firstname, $dbuser->firstname);
        $this->assertEquals($imsuser->lastname, $dbuser->lastname);
    }

    public function test_user_update_disabled() {
        global $DB;

        $this->imsplugin->set_config('imsupdateusers', false);

        $user = $this->getDataGenerator()->create_user(array('idnumber' => 'test-update-user'));
        $imsuser = new stdClass();
        $imsuser->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_UPDATE;
        // THIS SHOULD WORK, surely?: $imsuser->username = $user->username;
        // But this is required...
        $imsuser->username = $user->idnumber;
        $imsuser->email = 'u3@u3.org';
        $imsuser->firstname = 'U';
        $imsuser->lastname = '3';

        $this->set_xml_file(array($imsuser));
        $this->imsplugin->cron();

        // Verify no changes have been made.
        $dbuser = $DB->get_record('user', array('id' => $user->id), '*', MUST_EXIST);
        $this->assertEquals($user->email, $dbuser->email);
        $this->assertEquals($user->firstname, $dbuser->firstname);
        $this->assertEquals($user->lastname, $dbuser->lastname);
    }

    /**
     * Delete user
     */
    public function test_user_delete() {
        global $DB;

        $this->imsplugin->set_config('imsdeleteusers', true);
        $user = $this->getDataGenerator()->create_user(array('idnumber' => 'test-update-user'));

        $imsuser = new stdClass();
        $imsuser->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_DELETE;
        $imsuser->username = $user->username;
        $imsuser->firstname = $user->firstname;
        $imsuser->lastname = $user->lastname;
        $imsuser->email = $user->email;
        $this->set_xml_file(array($imsuser));

        $this->imsplugin->cron();
        $this->assertEquals(1, $DB->get_field('user', 'deleted', array('id' => $user->id), '*', MUST_EXIST));
    }

    /**
     * Delete user disabled
     */
    public function test_user_delete_disabled() {
        global $DB;

        $this->imsplugin->set_config('imsdeleteusers', false);
        $user = $this->getDataGenerator()->create_user(array('idnumber' => 'test-update-user'));

        $imsuser = new stdClass();
        $imsuser->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_DELETE;
        $imsuser->username = $user->username;
        $imsuser->firstname = $user->firstname;
        $imsuser->lastname = $user->lastname;
        $imsuser->email = $user->email;
        $this->set_xml_file(array($imsuser));

        $this->imsplugin->cron();
        $this->assertEquals(0, $DB->get_field('user', 'deleted', array('id' => $user->id), '*', MUST_EXIST));
    }

    /**
     * Existing courses are not created again
     */
    public function test_courses_existing() {
        global $DB;

        $course1 = $this->getDataGenerator()->create_course(array('idnumber' => 'id1'));
        $course2 = $this->getDataGenerator()->create_course(array('idnumber' => 'id2'));

        // Default mapping according to default course attributes - IMS description tags mapping.
        $course1->imsshort = $course1->fullname;
        $course2->imsshort = $course2->fullname;
        unset($course1->category);
        unset($course2->category);

        $prevncourses = $DB->count_records('course');

        $courses = array($course1, $course2);
        $this->set_xml_file(false, $courses);
        $this->imsplugin->cron();

        $this->assertEquals($prevncourses, $DB->count_records('course'));
    }

    /**
     * Add new courses
     */
    public function test_courses_add() {
        global $DB;

        $prevncourses = $DB->count_records('course');

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = 'id1';
        $course1->imsshort = 'id1';
        $course1->category[] = 'DEFAULT CATNAME';

        $course2 = new StdClass();
        $course2->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course2->idnumber = 'id2';
        $course2->imsshort = 'id2';
        $course2->category[] = 'DEFAULT CATNAME';

        $courses = array($course1, $course2);
        $this->set_xml_file(false, $courses);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 2), $DB->count_records('course'));
        $this->assertTrue($DB->record_exists('course', array('idnumber' => $course1->idnumber)));
        $this->assertTrue($DB->record_exists('course', array('idnumber' => $course2->idnumber)));
    }

    /**
     * Verify that courses are not created when createnewcourses
     * option is diabled.
     */
    public function test_courses_add_createnewcourses_disabled() {
        global $DB;

        $this->imsplugin->set_config('createnewcourses', false);
        $prevncourses = $DB->count_records('course');

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = 'id1';
        $course1->imsshort = 'id1';
        $course1->category[] = 'DEFAULT CATNAME';

        $course2 = new StdClass();
        $course2->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course2->idnumber = 'id2';
        $course2->imsshort = 'id2';
        $course2->category[] = 'DEFAULT CATNAME';

        $courses = array($course1, $course2);
        $this->set_xml_file(false, $courses);
        $this->imsplugin->cron();

        $courses = array($course1, $course2);
        $this->set_xml_file(false, $courses);
        $this->imsplugin->cron();

        // Verify the courses have not ben creased.
        $this->assertEquals($prevncourses , $DB->count_records('course'));
        $this->assertFalse($DB->record_exists('course', array('idnumber' => $course1->idnumber)));
        $this->assertFalse($DB->record_exists('course', array('idnumber' => $course2->idnumber)));
    }

    /**
     * Test adding a course with no idnumber.
     */
    public function test_courses_no_idnumber() {
        global $DB;

        $prevncourses = $DB->count_records('course');

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = '';
        $course1->imsshort = 'id1';
        $course1->category[] = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course1));
        $this->imsplugin->cron();

        // Verify no action.
        $this->assertEquals($prevncourses, $DB->count_records('course'));
    }

    /**
     * Add new course with the truncateidnumber setting.
     */
    public function test_courses_add_truncate_idnumber() {
        global $DB;

        $truncatelength = 4;

        $this->imsplugin->set_config('truncatecoursecodes', $truncatelength);
        $prevncourses = $DB->count_records('course');

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = '123456789';
        $course1->imsshort = 'id1';
        $course1->category[] = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course1));
        $this->imsplugin->cron();

        // Verify the new course has been added.
        $this->assertEquals(($prevncourses + 1), $DB->count_records('course'));

        $truncatedidnumber = substr($course1->idnumber, 0, $truncatelength);

        $this->assertTrue($DB->record_exists('course', array('idnumber' => $truncatedidnumber)));
    }

    /**
     * Add new course without a category.
     */
    public function test_course_add_default_category() {
        global $DB, $CFG;
        require_once($CFG->libdir.'/coursecatlib.php');

        $this->imsplugin->set_config('createnewcategories', false);

        // Delete the default category, to ensure the plugin handles this gracefully.
        $defaultcat = coursecat::get_default();
        $defaultcat->delete_full(false);

        // Create an course with the IMS plugin without a category.
        $course1 = new stdClass();
        $course1->idnumber = 'id1';
        $course1->imsshort = 'id1';
        $course1->category[] = '';
        $this->set_xml_file(false, array($course1));
        $this->imsplugin->cron();

        // Check the course has been created.
        $dbcourse = $DB->get_record('course', array('idnumber' => $course1->idnumber), '*', MUST_EXIST);
        // Check that it belongs to a category which exists.
        $this->assertTrue($DB->record_exists('course_categories', array('id' => $dbcourse->category)));
    }

    /**
     * Course attributes mapping to IMS enterprise group description tags
     */
    public function test_courses_attrmapping() {
        global $DB;

        // Setting a all = coursecode (idnumber) mapping.
        $this->imsplugin->set_config('imscoursemapshortname', 'coursecode');
        $this->imsplugin->set_config('imscoursemapfullname', 'coursecode');
        $this->imsplugin->set_config('imscoursemapsummary', 'coursecode');

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = 'id1';
        $course1->imsshort = 'description_short1';
        $course1->imslong = 'description_long';
        $course1->imsfull = 'description_full';
        $course1->category[] = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course1));
        $this->imsplugin->cron();

        $dbcourse = $DB->get_record('course', array('idnumber' => $course1->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->shortname, $course1->idnumber);
        $this->assertEquals($dbcourse->fullname, $course1->idnumber);
        $this->assertEquals($dbcourse->summary, $course1->idnumber);

        // Setting a mapping using all the description tags.
        $this->imsplugin->set_config('imscoursemapshortname', 'short');
        $this->imsplugin->set_config('imscoursemapfullname', 'long');
        $this->imsplugin->set_config('imscoursemapsummary', 'full');

        $course2 = new StdClass();
        $course2->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course2->idnumber = 'id2';
        $course2->imsshort = 'description_short2';
        $course2->imslong = 'description_long';
        $course2->imsfull = 'description_full';
        $course2->category[] = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course2));
        $this->imsplugin->cron();

        $dbcourse = $DB->get_record('course', array('idnumber' => $course2->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->shortname, $course2->imsshort);
        $this->assertEquals($dbcourse->fullname, $course2->imslong);
        $this->assertEquals($dbcourse->summary, $course2->imsfull);

        // Setting a mapping where the specified description tags doesn't exist in the XML file (must delegate into idnumber).
        $this->imsplugin->set_config('imscoursemapshortname', 'short');
        $this->imsplugin->set_config('imscoursemapfullname', 'long');
        $this->imsplugin->set_config('imscoursemapsummary', 'full');

        $course3 = new StdClass();
        $course3->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course3->idnumber = 'id3';
        $course3->imsshort = 'description_short3';
        $course3->category[] = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course3));
        $this->imsplugin->cron();

        $dbcourse = $DB->get_record('course', array('idnumber' => $course3->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->shortname, $course3->imsshort);
        $this->assertEquals($dbcourse->fullname, $course3->idnumber);
        $this->assertEquals($dbcourse->summary, $course3->idnumber);

    }

    /**
     * Course updates
     */
    public function test_course_update() {
        global $DB;

        $course4 = new StdClass();
        $course4->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course4->idnumber = 'id4';
        $course4->imsshort = 'id4';
        $course4->imsfull = 'id4';
        $course4->category[] = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course4));
        $this->imsplugin->cron();

        $course4u = $DB->get_record('course', array('idnumber' => $course4->idnumber));

        $course4u->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_UPDATE;
        $course4u->imsshort = 'description_short_updated';
        $course4u->imsfull = 'description_full_updated';
        unset($course4u->category);

        $this->set_xml_file(false, array($course4u));
        $this->imsplugin->cron();

        $dbcourse = $DB->get_record('course', array('idnumber' => $course4->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->shortname, $course4u->imsshort);
        $this->assertEquals($dbcourse->fullname, $course4u->imsfull);
    }

    /**
     * Course delete. Make it hidden.
     */
    public function test_course_delete() {
        global $DB;

        $course8 = new StdClass();
        $course8->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course8->idnumber = 'id8';
        $course8->imsshort = 'id8';
        $course8->imsfull = 'id8';
        $course8->category[] = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course8));
        $this->imsplugin->cron();

        $course8d = $DB->get_record('course', array('idnumber' => $course8->idnumber));
        $this->assertEquals($course8d->visible, 1);

        $course8d->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_DELETE;
        unset($course8d->category);

        $this->set_xml_file(false, array($course8d));
        $this->imsplugin->cron();

        $dbcourse = $DB->get_record('course', array('idnumber' => $course8d->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->visible, 0);
    }


    /**
     * Nested categories with name during course creation
     */
    public function test_nested_categories() {
        global $DB;

        $this->imsplugin->set_config('nestedcategories', true);

        $topcat = 'DEFAULT CATNAME';
        $subcat = 'DEFAULT SUB CATNAME';

        $course5 = new StdClass();
        $course5->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course5->idnumber = 'id5';
        $course5->imsshort = 'description_short';
        $course5->imslong = 'description_long';
        $course5->imsfull = 'description_full';
        $course5->category = array();
        $course5->category[] = $topcat;
        $course5->category[] = $subcat;

        $this->set_xml_file(false, array($course5));
        $this->imsplugin->cron();

        $parentcatid = $DB->get_field('course_categories', 'id', array('name' => $topcat));
        $subcatid = $DB->get_field('course_categories', 'id', array('name' => $subcat, 'parent' => $parentcatid));

        $this->assertTrue(isset($subcatid));
        $this->assertTrue($subcatid > 0);

        $topcat = 'DEFAULT CATNAME';
        $subcat = 'DEFAULT SUB CATNAME TEST2';

        $course6 = new StdClass();
        $course6->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course6->idnumber = 'id6';
        $course6->imsshort = 'description_short';
        $course6->imslong = 'description_long';
        $course6->imsfull = 'description_full';
        $course6->category = array();
        $course6->category[] = $topcat;
        $course6->category[] = $subcat;

        $this->set_xml_file(false, array($course6));
        $this->imsplugin->cron();

        $parentcatid = $DB->get_field('course_categories', 'id', array('name' => $topcat));
        $subcatid = $DB->get_field('course_categories', 'id', array('name' => $subcat, 'parent' => $parentcatid));

        $this->assertTrue(isset($subcatid));
        $this->assertTrue($subcatid > 0);
    }


    /**
     * Test that duplicate nested categories with name are not created
     */
    public function test_nested_categories_for_dups() {
        global $DB;

        $this->imsplugin->set_config('nestedcategories', true);

        $topcat = 'DEFAULT CATNAME';
        $subcat = 'DEFAULT SUB CATNAME DUPTEST';

        $course7 = new StdClass();
        $course7->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course7->idnumber = 'id7';
        $course7->imsshort = 'description_short';
        $course7->imslong = 'description_long';
        $course7->imsfull = 'description_full';
        $course7->category[] = $topcat;
        $course7->category[] = $subcat;

        $this->set_xml_file(false, array($course7));
        $this->imsplugin->cron();

        $prevncategories = $DB->count_records('course_categories');

        $course8 = new StdClass();
        $course8->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course8->idnumber = 'id8';
        $course8->imsshort = 'description_short';
        $course8->imslong = 'description_long';
        $course8->imsfull = 'description_full';
        $course8->category[] = $topcat;
        $course8->category[] = $subcat;

        $this->set_xml_file(false, array($course8));
        $this->imsplugin->cron();

        $this->assertEquals($prevncategories, $DB->count_records('course_categories'));
    }

    /**
     * Nested categories with idnumber during course creation
     */
    public function test_nested_categories_idnumber() {
        global $DB;

        $this->imsplugin->set_config('nestedcategories', true);
        $this->imsplugin->set_config('categoryidnumber', true);
        $this->imsplugin->set_config('categoryseparator', '|');

        $catsep = trim($this->imsplugin->get_config('categoryseparator'));

        $topcatname = 'DEFAULT CATNAME';
        $subcatname = 'DEFAULT SUB CATNAME';
        $topcatidnumber = '01';
        $subcatidnumber = '0101';

        $topcat = $topcatname.$catsep.$topcatidnumber;
        $subcat = $subcatname.$catsep.$subcatidnumber;

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = 'id5';
        $course1->imsshort = 'description_short';
        $course1->imslong = 'description_long';
        $course1->imsfull = 'description_full';
        $course1->category[] = $topcat;
        $course1->category[] = $subcat;

        $this->set_xml_file(false, array($course1));
        $this->imsplugin->cron();

        $parentcatid = $DB->get_field('course_categories', 'id', array('idnumber' => $topcatidnumber));
        $subcatid = $DB->get_field('course_categories', 'id', array('idnumber' => $subcatidnumber, 'parent' => $parentcatid));

        $this->assertTrue(isset($subcatid));
        $this->assertTrue($subcatid > 0);

        // Change the category separator character.
        $this->imsplugin->set_config('categoryseparator', ':');

        $catsep = trim($this->imsplugin->get_config('categoryseparator'));

        $topcatname = 'DEFAULT CATNAME';
        $subcatname = 'DEFAULT SUB CATNAME TEST2';
        $topcatidnumber = '01';
        $subcatidnumber = '0102';

        $topcat = $topcatname.$catsep.$topcatidnumber;
        $subcat = $subcatname.$catsep.$subcatidnumber;

        $course2 = new StdClass();
        $course2->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course2->idnumber = 'id6';
        $course2->imsshort = 'description_short';
        $course2->imslong = 'description_long';
        $course2->imsfull = 'description_full';
        $course2->category[] = $topcat;
        $course2->category[] = $subcat;

        $this->set_xml_file(false, array($course2));
        $this->imsplugin->cron();

        $parentcatid = $DB->get_field('course_categories', 'id', array('idnumber' => $topcatidnumber));
        $subcatid = $DB->get_field('course_categories', 'id', array('idnumber' => $subcatidnumber, 'parent' => $parentcatid));

        $this->assertTrue(isset($subcatid));
        $this->assertTrue($subcatid > 0);
    }

    /**
     * Test that duplicate nested categories with idnumber are not created
     */
    public function test_nested_categories_idnumber_for_dups() {
        global $DB;

        $this->imsplugin->set_config('nestedcategories', true);
        $this->imsplugin->set_config('categoryidnumber', true);
        $this->imsplugin->set_config('categoryseparator', '|');

        $catsep = trim($this->imsplugin->get_config('categoryseparator'));

        $topcatname = 'DEFAULT CATNAME';
        $subcatname = 'DEFAULT SUB CATNAME';
        $topcatidnumber = '01';
        $subcatidnumber = '0101';

        $topcat = $topcatname.$catsep.$topcatidnumber;
        $subcat = $subcatname.$catsep.$subcatidnumber;

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = 'id1';
        $course1->imsshort = 'description_short';
        $course1->imslong = 'description_long';
        $course1->imsfull = 'description_full';
        $course1->category[] = $topcat;
        $course1->category[] = $subcat;

        $this->set_xml_file(false, array($course1));
        $this->imsplugin->cron();

        $prevncategories = $DB->count_records('course_categories');

        $course2 = new StdClass();
        $course2->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course2->idnumber = 'id2';
        $course2->imsshort = 'description_short';
        $course2->imslong = 'description_long';
        $course2->imsfull = 'description_full';
        $course2->category[] = $topcat;
        $course2->category[] = $subcat;

        $this->set_xml_file(false, array($course2));
        $this->imsplugin->cron();

        $this->assertEquals($prevncategories, $DB->count_records('course_categories'));
    }

    /**
     * Test that nested categories with idnumber is not created if name is missing
     */
    public function test_categories_idnumber_missing_name() {
        global $DB, $CFG;

        $this->imsplugin->set_config('nestedcategories', true);
        $this->imsplugin->set_config('categoryidnumber', true);
        $this->imsplugin->set_config('categoryseparator', '|');
        $catsep = trim($this->imsplugin->get_config('categoryseparator'));

        $topcatname = 'DEFAULT CATNAME';
        $subcatname = '';
        $topcatidnumber = '01';
        $subcatidnumber = '0101';

        $topcat = $topcatname.$catsep.$topcatidnumber;
        $subcat = $subcatname.$catsep.$subcatidnumber;

        $course1 = new StdClass();
        $course1->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course1->idnumber = 'id1';
        $course1->imsshort = 'description_short';
        $course1->imslong = 'description_long';
        $course1->imsfull = 'description_full';
        $course1->category[] = $topcat;
        $course1->category[] = $subcat;

        $this->set_xml_file(false, array($course1));
        $this->imsplugin->cron();

        // Check all categories except the last subcategory was created.
        $parentcatid = $DB->get_field('course_categories', 'id', array('idnumber' => $topcatidnumber));
        $this->assertTrue((boolean)$parentcatid);
        $subcatid = $DB->get_field('course_categories', 'id', array('idnumber' => $subcatidnumber, 'parent' => $parentcatid));
        $this->assertFalse((boolean)$subcatid);

        // Check course was put in default category.
        $defaultcat = coursecat::get_default();
        $dbcourse = $DB->get_record('course', array('idnumber' => $course1->idnumber), '*', MUST_EXIST);
        $this->assertEquals($dbcourse->category, $defaultcat->id);

    }

    /**
     * Create category with name (nested categories not activated).
     */
    public function test_create_category_name_no_nested() {
        global $DB;

        $course = new StdClass();
        $course->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course->idnumber = 'id';
        $course->imsshort = 'description_short';
        $course->imslong = 'description_long';
        $course->imsfull = 'description_full';
        $course->category[] = 'CATNAME';

        $this->set_xml_file(false, array($course));
        $this->imsplugin->cron();

        $dbcat = $DB->get_record('course_categories', array('name' => $course->category[0]));
        $this->assertFalse(!$dbcat);
        $this->assertEquals($dbcat->parent, 0);

        $dbcourse = $DB->get_record('course', array('idnumber' => $course->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->category, $dbcat->id);

    }

    /**
     * Find a category with name (nested categories not activated).
     */
    public function test_find_category_name_no_nested() {
        global $DB;

        $cattop = $this->getDataGenerator()->create_category(array('name' => 'CAT-TOP'));
        $catsub = $this->getDataGenerator()->create_category(array('name' => 'CAT-SUB', 'parent' => $cattop->id));
        $prevcats = $DB->count_records('course_categories');

        $course = new StdClass();
        $course->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course->idnumber = 'id';
        $course->imsshort = 'description_short';
        $course->imslong = 'description_long';
        $course->imsfull = 'description_full';
        $course->category[] = 'CAT-SUB';

        $this->set_xml_file(false, array($course));
        $this->imsplugin->cron();

        $newcats = $DB->count_records('course_categories');

        // Check that no new category was not created.
        $this->assertEquals($prevcats, $newcats);

        // Check course is associated to CAT-SUB.
        $dbcourse = $DB->get_record('course', array('idnumber' => $course->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->category, $catsub->id);

    }

    /**
     * Create category with idnumber (nested categories not activated).
     */
    public function test_create_category_idnumber_no_nested() {
        global $DB;

        $this->imsplugin->set_config('categoryidnumber', true);
        $this->imsplugin->set_config('categoryseparator', '|');
        $catsep = trim($this->imsplugin->get_config('categoryseparator'));

        $course = new StdClass();
        $course->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course->idnumber = 'id';
        $course->imsshort = 'description_short';
        $course->imslong = 'description_long';
        $course->imsfull = 'description_full';
        $course->category[] = 'CATNAME'. $catsep .  'CATIDNUMBER';

        $this->set_xml_file(false, array($course));
        $this->imsplugin->cron();

        $dbcat = $DB->get_record('course_categories', array('idnumber' => 'CATIDNUMBER'));
        $this->assertFalse(!$dbcat);
        $this->assertEquals($dbcat->parent, 0);
        $this->assertEquals($dbcat->name, 'CATNAME');

        $dbcourse = $DB->get_record('course', array('idnumber' => $course->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->category, $dbcat->id);

    }

    /**
     * Find a category with idnumber (nested categories not activated).
     */
    public function test_find_category_idnumber_no_nested() {
        global $DB;

        $this->imsplugin->set_config('categoryidnumber', true);
        $this->imsplugin->set_config('categoryseparator', '|');
        $catsep = trim($this->imsplugin->get_config('categoryseparator'));

        $topcatname = 'CAT-TOP';
        $subcatname = 'CAT-SUB';
        $topcatidnumber = 'ID-TOP';
        $subcatidnumber = 'ID-SUB';

        $cattop = $this->getDataGenerator()->create_category(array('name' => $topcatname, 'idnumber' => $topcatidnumber));
        $catsub = $this->getDataGenerator()->create_category(array('name' => $subcatname, 'idnumber' => $subcatidnumber,
                'parent' => $cattop->id));
        $prevcats = $DB->count_records('course_categories');

        $course = new StdClass();
        $course->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course->idnumber = 'id';
        $course->imsshort = 'description_short';
        $course->imslong = 'description_long';
        $course->imsfull = 'description_full';
        $course->category[] = $subcatname . $catsep . $subcatidnumber;

        $this->set_xml_file(false, array($course));
        $this->imsplugin->cron();

        $newcats = $DB->count_records('course_categories');

        // Check that no new category was not created.
        $this->assertEquals($prevcats, $newcats);

        $dbcourse = $DB->get_record('course', array('idnumber' => $course->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->category, $catsub->id);

    }

    /**
     * Test that category with idnumber is not created if name is missing (nested categories not activated).
     */
    public function test_category_idnumber_missing_name_no_nested() {
        global $DB;

        $this->imsplugin->set_config('categoryidnumber', true);
        $this->imsplugin->set_config('categoryseparator', '|');
        $catsep = trim($this->imsplugin->get_config('categoryseparator'));

        $catidnumber = '01';

        $course = new StdClass();
        $course->recstatus = enrol_imsenterprise_plugin::IMSENTERPRISE_ADD;
        $course->idnumber = 'id1';
        $course->imsshort = 'description_short';
        $course->imslong = 'description_long';
        $course->imsfull = 'description_full';
        $course->category[] = '' . $catsep . $catidnumber;

        $this->set_xml_file(false, array($course));
        $this->imsplugin->cron();

        // Check category was not created.
        $catid = $DB->get_record('course_categories', array('idnumber' => $catidnumber));
        $this->assertFalse($catid);

        // Check course was put in default category.
        $defaultcat = coursecat::get_default();
        $dbcourse = $DB->get_record('course', array('idnumber' => $course->idnumber), '*', MUST_EXIST);
        $this->assertEquals($dbcourse->category, $defaultcat->id);

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
     */
    public function set_xml_file($users = false, $courses = false) {

        $xmlcontent = '<enterprise>';

        // Users.
        if (!empty($users)) {
            foreach ($users as $user) {
                $xmlcontent .= '
  <person';

                // Optional recstatus (1=add, 2=update, 3=delete).
                if (!empty($user->recstatus)) {
                    $xmlcontent .= ' recstatus="'.$user->recstatus.'"';
                }

                $xmlcontent .= '>
    <sourcedid>
      <source>TestSource</source>
      <id>'.$user->username.'</id>
    </sourcedid>
    <userid';

                // Optional authentication type.
                if (!empty($user->auth)) {
                    $xmlcontent .= ' authenticationtype="'.$user->auth.'"';
                }

                $xmlcontent .= '>'.$user->username.'</userid>
    <name>
      <fn>'.$user->firstname.' '.$user->lastname.'</fn>
      <n>
        <family>'.$user->lastname.'</family>
        <given>'.$user->firstname.'</given>
      </n>
    </name>
    <email>'.$user->email.'</email>
  </person>';
            }
        }

        // Courses.
        // Mapping based on default course attributes - IMS group tags mapping.
        if (!empty($courses)) {
            foreach ($courses as $course) {

                $xmlcontent .= '
  <group';

                // Optional recstatus (1=add, 2=update, 3=delete).
                if (!empty($course->recstatus)) {
                    $xmlcontent .= ' recstatus="'.$course->recstatus.'"';
                }

                $xmlcontent .= '>
    <sourcedid>
      <source>TestSource</source>
      <id>'.$course->idnumber.'</id>
    </sourcedid>
    <description>';

                // Optional to test course attributes mappings.
                if (!empty($course->imsshort)) {
                    $xmlcontent .= '
      <short>'.$course->imsshort.'</short>';
                }

                // Optional to test course attributes mappings.
                if (!empty($course->imslong)) {
                    $xmlcontent .= '
      <long>'.$course->imslong.'</long>';
                }

                // Optional to test course attributes mappings.
                if (!empty($course->imsfull)) {
                    $xmlcontent .= '
      <full>'.$course->imsfull.'</full>';
                }

                // The orgunit tag value is used by moodle as category name.
                $xmlcontent .= '
    </description>
    <org>';
                // Optional category name.
                if (isset($course->category) && !empty($course->category)) {
                    foreach ($course->category as $category) {
                        $xmlcontent .= '
      <orgunit>'.$category.'</orgunit>';
                    }
                }

                $xmlcontent .= '
    </org>
  </group>';
            }
        }

        $xmlcontent .= '
</enterprise>';

        // Creating the XML file.
        $filename = 'ims_' . rand(1000, 9999) . '.xml';
        $tmpdir = make_temp_directory('enrol_imsenterprise');
        $xmlfilepath = $tmpdir . '/' . $filename;
        file_put_contents($xmlfilepath, $xmlcontent);

        // Setting the file path in CFG.
        $this->imsplugin->set_config('imsfilelocation', $xmlfilepath);
    }

    /**
     * IMS Enterprise enrolment task test.
     */
    public function test_imsenterprise_cron_task() {
        global $DB;
        $prevnusers = $DB->count_records('user');

        $user1 = new StdClass();
        $user1->username = 'u1';
        $user1->email = 'u1@example.com';
        $user1->firstname = 'U';
        $user1->lastname = '1';

        $users = array($user1);
        $this->set_xml_file($users);

        $task = new enrol_imsenterprise\task\cron_task();
        $task->execute();

        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
    }
}
