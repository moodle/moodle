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
 * @category   phpunit
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
 * @category   phpunit
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_imsenterprise_testcase extends advanced_testcase {

    protected $imsplugin;

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
        $user1->username = 'u1';
        $user1->email = 'u1@u1.org';
        $user1->firstname = 'U';
        $user1->lastname = '1';

        $users = array($user1);
        $this->set_xml_file($users);
        $this->imsplugin->cron();

        $this->assertEquals(($prevnusers + 1), $DB->count_records('user'));
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
        $course1->idnumber = 'id1';
        $course1->imsshort = 'id1';
        $course1->category = 'DEFAULT CATNAME';

        $course2 = new StdClass();
        $course2->idnumber = 'id2';
        $course2->imsshort = 'id2';
        $course2->category = 'DEFAULT CATNAME';

        $courses = array($course1, $course2);
        $this->set_xml_file(false, $courses);
        $this->imsplugin->cron();

        $this->assertEquals(($prevncourses + 2), $DB->count_records('course'));
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
        $course1->idnumber = 'id1';
        $course1->imsshort = 'description_short1';
        $course1->imslong = 'description_long';
        $course1->imsfull = 'description_full';
        $course1->category = 'DEFAULT CATNAME';

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
        $course2->idnumber = 'id2';
        $course2->imsshort = 'description_short2';
        $course2->imslong = 'description_long';
        $course2->imsfull = 'description_full';
        $course2->category = 'DEFAULT CATNAME';

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
        $course3->idnumber = 'id3';
        $course3->imsshort = 'description_short3';
        $course3->category = 'DEFAULT CATNAME';

        $this->set_xml_file(false, array($course3));
        $this->imsplugin->cron();

        $dbcourse = $DB->get_record('course', array('idnumber' => $course3->idnumber));
        $this->assertFalse(!$dbcourse);
        $this->assertEquals($dbcourse->shortname, $course3->imsshort);
        $this->assertEquals($dbcourse->fullname, $course3->idnumber);
        $this->assertEquals($dbcourse->summary, $course3->idnumber);

    }


    /**
     * Sets the plugin configuration for testing
     */
    protected function set_test_config() {
        $this->imsplugin->set_config('mailadmins', false);
        $this->imsplugin->set_config('prev_path', '');
        $this->imsplugin->set_config('createnewusers', true);
        $this->imsplugin->set_config('createnewcourses', true);
        $this->imsplugin->set_config('createnewcategories', true);
    }


    /**
     * Creates an IMS enterprise XML file and adds it's path to config settings
     *
     * @param array Array of users StdClass
     * @param array Array of courses StdClass
     */
    protected function set_xml_file($users = false, $courses = false) {
        global $DB;

        $xmlcontent = '<enterprise>';

        // Users.
        if (!empty($users)) {
            foreach ($users as $user) {
                $xmlcontent .= '
  <person>
    <sourcedid>
      <source>TestSource</source>
      <id>'.$user->username.'</id>
    </sourcedid>
    <userid>'.$user->username.'</userid>
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
  <group>
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

                // orgunit tag value is used by moodle as category name.
                $xmlcontent .= '
    </description>
    <org>
      <orgunit>'.$course->category.'</orgunit>
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
}
