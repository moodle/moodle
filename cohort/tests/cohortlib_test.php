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
 * Cohort library tests.
 *
 * @package    core_cohort
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/cohort/lib.php");


/**
 * Cohort library tests.
 *
 * @package    core_cohort
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_testcase extends advanced_testcase {

    public function test_cohort_add_cohort() {
        global $DB;

        $this->resetAfterTest();

        $cohort = new stdClass();
        $cohort->contextid = context_system::instance()->id;
        $cohort->name = 'test cohort';
        $cohort->idnumber = 'testid';
        $cohort->description = 'test cohort desc';
        $cohort->descriptionformat = FORMAT_HTML;

        $id = cohort_add_cohort($cohort);
        $this->assertNotEmpty($id);

        $newcohort = $DB->get_record('cohort', array('id'=>$id));
        $this->assertEquals($cohort->contextid, $newcohort->contextid);
        $this->assertSame($cohort->name, $newcohort->name);
        $this->assertSame($cohort->description, $newcohort->description);
        $this->assertEquals($cohort->descriptionformat, $newcohort->descriptionformat);
        $this->assertNotEmpty($newcohort->timecreated);
        $this->assertSame($newcohort->component, '');
        $this->assertSame($newcohort->timecreated, $newcohort->timemodified);

        try {
            $cohort = new stdClass();
            $cohort->contextid = context_system::instance()->id;
            $cohort->name = null;
            $cohort->idnumber = 'testid';
            $cohort->description = 'test cohort desc';
            $cohort->descriptionformat = FORMAT_HTML;
            cohort_add_cohort($cohort);

            $this->fail('Exception expected when trying to add cohort without name');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_cohort_update_cohort() {
        global $DB;

        $this->resetAfterTest();

        $cohort = new stdClass();
        $cohort->contextid = context_system::instance()->id;
        $cohort->name = 'test cohort';
        $cohort->idnumber = 'testid';
        $cohort->description = 'test cohort desc';
        $cohort->descriptionformat = FORMAT_HTML;
        $id = cohort_add_cohort($cohort);
        $this->assertNotEmpty($id);
        $DB->set_field('cohort', 'timecreated', $cohort->timecreated - 10, array('id'=>$id));
        $DB->set_field('cohort', 'timemodified', $cohort->timemodified - 10, array('id'=>$id));
        $cohort = $DB->get_record('cohort', array('id'=>$id));

        $cohort->name = 'test cohort 2';
        cohort_update_cohort($cohort);

        $newcohort = $DB->get_record('cohort', array('id'=>$id));

        $this->assertSame($cohort->contextid, $newcohort->contextid);
        $this->assertSame($cohort->name, $newcohort->name);
        $this->assertSame($cohort->description, $newcohort->description);
        $this->assertSame($cohort->descriptionformat, $newcohort->descriptionformat);
        $this->assertSame($cohort->timecreated, $newcohort->timecreated);
        $this->assertSame($cohort->component, $newcohort->component);
        $this->assertGreaterThan($newcohort->timecreated, $newcohort->timemodified);
        $this->assertLessThanOrEqual(time(), $newcohort->timemodified);
    }

    public function test_cohort_delete_cohort() {
        global $DB;

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();

        cohort_delete_cohort($cohort);

        $this->assertFalse($DB->record_exists('cohort', array('id'=>$cohort->id)));
    }

    public function test_cohort_delete_category() {
        global $DB;

        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category();

        $cohort = $this->getDataGenerator()->create_cohort(array('contextid'=>context_coursecat::instance($category->id)->id));

        cohort_delete_category($category);

        $this->assertTrue($DB->record_exists('cohort', array('id'=>$cohort->id)));
        $newcohort = $DB->get_record('cohort', array('id'=>$cohort->id));
        $this->assertEquals(context_system::instance()->id, $newcohort->contextid);
    }

    public function test_cohort_add_member() {
        global $DB;

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();

        $this->assertFalse($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));
        cohort_add_member($cohort->id, $user->id);
        $this->assertTrue($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));
    }

    public function test_cohort_remove_member() {
        global $DB;

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();

        cohort_add_member($cohort->id, $user->id);
        $this->assertTrue($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));

        cohort_remove_member($cohort->id, $user->id);
        $this->assertFalse($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));
    }

    public function test_cohort_is_member() {
        global $DB;

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();

        $this->assertFalse(cohort_is_member($cohort->id, $user->id));
        cohort_add_member($cohort->id, $user->id);
        $this->assertTrue(cohort_is_member($cohort->id, $user->id));
    }

    public function test_cohort_get_visible_list() {
        global $DB;

        $this->resetAfterTest();

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        $course1 = $this->getDataGenerator()->create_course(array('category'=>$category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('category'=>$category2->id));
        $course3 = $this->getDataGenerator()->create_course();

        $cohort1 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_coursecat::instance($category1->id)->id));
        $cohort2 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_coursecat::instance($category2->id)->id));
        $cohort3 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_system::instance()->id));
        $cohort4 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_system::instance()->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();

        $manualenrol = enrol_get_plugin('manual');
        $enrol1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'));
        $enrol2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'));

        $manualenrol->enrol_user($enrol1, $user1->id);
        $manualenrol->enrol_user($enrol1, $user3->id);
        $manualenrol->enrol_user($enrol1, $user4->id);
        $manualenrol->enrol_user($enrol2, $user2->id);

        cohort_add_member($cohort1->id, $user1->id);
        cohort_add_member($cohort3->id, $user1->id);
        cohort_add_member($cohort1->id, $user3->id);
        cohort_add_member($cohort2->id, $user2->id);

        $list = cohort_get_visible_list($course1);
        $this->assertEquals(2, count($list));
        $this->assertNotEmpty($list[$cohort1->id]);
        $this->assertRegExp('/\(2\)$/', $list[$cohort1->id]);
        $this->assertNotEmpty($list[$cohort3->id]);
        $this->assertRegExp('/\(1\)$/', $list[$cohort3->id]);

        $list = cohort_get_visible_list($course1, false);
        $this->assertEquals(3, count($list));
        $this->assertNotEmpty($list[$cohort1->id]);
        $this->assertRegExp('/\(2\)$/', $list[$cohort1->id]);
        $this->assertNotEmpty($list[$cohort3->id]);
        $this->assertRegExp('/\(1\)$/', $list[$cohort3->id]);
        $this->assertNotEmpty($list[$cohort4->id]);
        $this->assertRegExp('/[^\)]$/', $list[$cohort4->id]);

        $list = cohort_get_visible_list($course2);
        $this->assertEquals(1, count($list));
        $this->assertNotEmpty($list[$cohort2->id]);
        $this->assertRegExp('/\(1\)$/', $list[$cohort2->id]);

        $list = cohort_get_visible_list($course2, false);
        $this->assertEquals(3, count($list));
        $this->assertNotEmpty($list[$cohort2->id]);
        $this->assertRegExp('/\(1\)$/', $list[$cohort2->id]);
        $this->assertNotEmpty($list[$cohort3->id]);
        $this->assertRegExp('/[^\)]$/', $list[$cohort3->id]);
        $this->assertNotEmpty($list[$cohort4->id]);
        $this->assertRegExp('/[^\)]$/', $list[$cohort4->id]);

        $list = cohort_get_visible_list($course3);
        $this->assertEquals(0, count($list));

        $list = cohort_get_visible_list($course3, false);
        $this->assertEquals(2, count($list));
        $this->assertNotEmpty($list[$cohort3->id]);
        $this->assertRegExp('/[^\)]$/', $list[$cohort3->id]);
        $this->assertNotEmpty($list[$cohort4->id]);
        $this->assertRegExp('/[^\)]$/', $list[$cohort4->id]);
    }

    public function test_cohort_get_cohorts() {
        global $DB;

        $this->resetAfterTest();

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        $cohort1 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_coursecat::instance($category1->id)->id, 'name'=>'aaagrrryyy', 'idnumber'=>'','description'=>''));
        $cohort2 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_coursecat::instance($category1->id)->id, 'name'=>'bbb', 'idnumber'=>'', 'description'=>'yyybrrr'));
        $cohort3 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_coursecat::instance($category1->id)->id, 'name'=>'ccc', 'idnumber'=>'xxarrrghyyy', 'description'=>'po_us'));
        $cohort4 = $this->getDataGenerator()->create_cohort(array('contextid'=>context_system::instance()->id));

        $result = cohort_get_cohorts(context_coursecat::instance($category2->id)->id);
        $this->assertEquals(0, $result['totalcohorts']);
        $this->assertEquals(0, count($result['cohorts']));
        $this->assertEquals(0, $result['allcohorts']);

        $result = cohort_get_cohorts(context_coursecat::instance($category1->id)->id);
        $this->assertEquals(3, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1, $cohort2->id=>$cohort2, $cohort3->id=>$cohort3), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(context_coursecat::instance($category1->id)->id, 0, 100, 'arrrgh');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort3->id=>$cohort3), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(context_coursecat::instance($category1->id)->id, 0, 100, 'brrr');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort2->id=>$cohort2), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(context_coursecat::instance($category1->id)->id, 0, 100, 'grrr');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(context_coursecat::instance($category1->id)->id, 1, 1, 'yyy');
        $this->assertEquals(3, $result['totalcohorts']);
        $this->assertEquals(array($cohort2->id=>$cohort2), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(context_coursecat::instance($category1->id)->id, 0, 100, 'po_us');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort3->id=>$cohort3), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(context_coursecat::instance($category1->id)->id, 0, 100, 'pokus');
        $this->assertEquals(0, $result['totalcohorts']);
        $this->assertEquals(array(), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);
    }
}
