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
 * Unit tests for workshop api class defined in mod/workshop/locallib.php
 *
 * @package    mod_workshop
 * @category   phpunit
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_workshop;

use testable_workshop;
use workshop;
use workshop_example_assessment;
use workshop_example_reference_assessment;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/workshop/locallib.php'); // Include the code to test
require_once(__DIR__ . '/fixtures/testable.php');


/**
 * Test cases for the internal workshop api
 */
#[\PHPUnit\Framework\Attributes\CoversClass(workshop::class)]
final class locallib_test extends \advanced_testcase {

    /** @var object */
    protected $course;

    /** @var workshop */
    protected $workshop;

    /** setup testing environment */
    protected function setUp(): void {
        parent::setUp();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $this->course));
        $cm = get_coursemodule_from_instance('workshop', $workshop->id, $this->course->id, false, MUST_EXIST);
        $this->workshop = new testable_workshop($workshop, $cm, $this->course);
    }

    protected function tearDown(): void {
        $this->workshop = null;
        parent::tearDown();
    }

    public function test_aggregate_submission_grades_process_notgraded(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => null);
        //$DB->expectNever('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_single(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => 10.12345);
        $expected = 10.12345;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_null_doesnt_influence(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => 45.54321);
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 1, 'grade' => null);
        $expected = 45.54321;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_weighted_single(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 12, 'submissiongrade' => null, 'weight' => 4, 'grade' => 14.00012);
        $expected = 14.00012;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 0.00000);
        $expected = 19.67750;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean_changed(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 12.57750, 'weight' => 1, 'grade' => 0.00000);
        $expected = 19.67750;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_mean_nochange(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 56.12000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 12.59000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => 19.67750, 'weight' => 1, 'grade' => 0.00000);
        //$DB->expectNever('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_rounding(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 4.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 2.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 1.00000);
        $expected = 2.33333;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_submission_grades_process_weighted_mean(): void {
        $this->resetAfterTest(true);

        // fixture set-up
        $batch = array();   // batch of a submission's assessments
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 3, 'grade' => 12.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 2, 'grade' => 30.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 1, 'grade' => 10.00000);
        $batch[] = (object)array('submissionid' => 45, 'submissiongrade' => null, 'weight' => 0, 'grade' => 1000.00000);
        $expected = 17.66667;
        //$DB->expectOnce('update_record');
        // exercise SUT
        $this->workshop->aggregate_submission_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_nograding(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>2, 'gradinggrade'=>null, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        // expectation
        //$DB->expectNever('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_single_grade_new(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>3, 'gradinggrade'=>82.87670, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        // expectation
        $now = time();
        $expected = new \stdClass();
        $expected->workshopid = $this->workshop->id;
        $expected->userid = 3;
        $expected->gradinggrade = 82.87670;
        $expected->timegraded = $now;
        //$DB->expectOnce('insert_record', array('workshop_aggregations', $expected));
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch, $now);
    }

    public function test_aggregate_grading_grades_process_single_grade_update(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>3, 'gradinggrade'=>90.00000, 'gradinggradeover'=>null, 'aggregationid'=>1, 'aggregatedgrade'=>82.87670);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_single_grade_uptodate(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>3, 'gradinggrade'=>90.00000, 'gradinggradeover'=>null, 'aggregationid'=>1, 'aggregatedgrade'=>90.00000);
        // expectation
        //$DB->expectNever('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_single_grade_overridden(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>4, 'gradinggrade'=>91.56700, 'gradinggradeover'=>82.32105, 'aggregationid'=>2, 'aggregatedgrade'=>91.56700);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_new(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>99.45670, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>87.34311, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>51.12000, 'gradinggradeover'=>null, 'aggregationid'=>null, 'aggregatedgrade'=>null);
        // expectation
        $now = time();
        $expected = new \stdClass();
        $expected->workshopid = $this->workshop->id;
        $expected->userid = 5;
        $expected->gradinggrade = 79.3066;
        $expected->timegraded = $now;
        //$DB->expectOnce('insert_record', array('workshop_aggregations', $expected));
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch, $now);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_update(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>56.23400, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>79.30660);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>87.34311, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>79.30660);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>51.12000, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>79.30660);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_overriden(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>56.23400, 'gradinggradeover'=>99.45670, 'aggregationid'=>2, 'aggregatedgrade'=>64.89904);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>87.34311, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>64.89904);
        $batch[] = (object)array('reviewerid'=>5, 'gradinggrade'=>51.12000, 'gradinggradeover'=>null, 'aggregationid'=>2, 'aggregatedgrade'=>64.89904);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_one_missing(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>50.00000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>null, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>52.20000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_aggregate_grading_grades_process_multiple_grades_missing_overridden(): void {
        $this->resetAfterTest(true);
        // fixture set-up
        $batch = array();
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>50.00000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>null, 'gradinggradeover'=>69.00000, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        $batch[] = (object)array('reviewerid'=>6, 'gradinggrade'=>52.20000, 'gradinggradeover'=>null, 'aggregationid'=>3, 'aggregatedgrade'=>100.00000);
        // expectation
        //$DB->expectOnce('update_record');
        // excersise SUT
        $this->workshop->aggregate_grading_grades_process($batch);
    }

    public function test_percent_to_value(): void {
        $this->resetAfterTest(true);
        // fixture setup
        $total = 185;
        $percent = 56.6543;
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
        // verify
        $this->assertEquals($part, $total * $percent / 100);
    }

    public function test_percent_to_value_negative(): void {
        $this->resetAfterTest(true);
        // fixture setup
        $total = 185;
        $percent = -7.098;

        // exercise SUT
        $this->expectException(\coding_exception::class);
        $part = workshop::percent_to_value($percent, $total);
    }

    public function test_percent_to_value_over_hundred(): void {
        $this->resetAfterTest(true);
        // fixture setup
        $total = 185;
        $percent = 121.08;

        // exercise SUT
        $this->expectException(\coding_exception::class);
        $part = workshop::percent_to_value($percent, $total);
    }

    public function test_lcm(): void {
        $this->resetAfterTest(true);
        // fixture setup + exercise SUT + verify in one step
        $this->assertEquals(workshop::lcm(1,4), 4);
        $this->assertEquals(workshop::lcm(2,4), 4);
        $this->assertEquals(workshop::lcm(4,2), 4);
        $this->assertEquals(workshop::lcm(2,3), 6);
        $this->assertEquals(workshop::lcm(6,4), 12);
    }

    public function test_lcm_array(): void {
        $this->resetAfterTest(true);
        // fixture setup
        $numbers = array(5,3,15);
        // excersise SUT
        $lcm = array_reduce($numbers, 'workshop::lcm', 1);
        // verify
        $this->assertEquals($lcm, 15);
    }

    public function test_prepare_example_assessment(): void {
        $this->resetAfterTest(true);
        // fixture setup
        $fakerawrecord = (object)array(
            'id'                => 42,
            'submissionid'      => 56,
            'weight'            => 0,
            'timecreated'       => time() - 10,
            'timemodified'      => time() - 5,
            'grade'             => null,
            'gradinggrade'      => null,
            'gradinggradeover'  => null,
            'feedbackauthor'    => null,
            'feedbackauthorformat' => 0,
            'feedbackauthorattachment' => 0,
        );
        // excersise SUT
        $a = $this->workshop->prepare_example_assessment($fakerawrecord);
        // verify
        $this->assertTrue($a instanceof workshop_example_assessment);
        $this->assertTrue($a->url instanceof \moodle_url);

        // modify setup
        $fakerawrecord->weight = 1;
        $this->expectException('coding_exception');
        // excersise SUT
        $a = $this->workshop->prepare_example_assessment($fakerawrecord);
    }

    public function test_prepare_example_reference_assessment(): void {
        global $USER;
        $this->resetAfterTest(true);
        // fixture setup
        $fakerawrecord = (object)array(
            'id'                => 38,
            'submissionid'      => 56,
            'weight'            => 1,
            'timecreated'       => time() - 100,
            'timemodified'      => time() - 50,
            'grade'             => 0.75000,
            'gradinggrade'      => 1.00000,
            'gradinggradeover'  => null,
            'feedbackauthor'    => null,
            'feedbackauthorformat' => 0,
            'feedbackauthorattachment' => 0,
        );
        // excersise SUT
        $a = $this->workshop->prepare_example_reference_assessment($fakerawrecord);
        // verify
        $this->assertTrue($a instanceof workshop_example_reference_assessment);

        // modify setup
        $fakerawrecord->weight = 0;
        $this->expectException('coding_exception');
        // excersise SUT
        $a = $this->workshop->prepare_example_reference_assessment($fakerawrecord);
    }

    /**
     * Tests user restrictions, as they affect lists of users returned by
     * core API functions.
     *
     * This includes the groupingid option (when group mode is in use), and
     * standard activity restrictions using the availability API.
     */
    public function test_user_restrictions(): void {
        global $DB, $CFG;

        $this->resetAfterTest();

        // Use existing sample course from setUp.
        $courseid = $this->workshop->course->id;

        // Make a test grouping and two groups.
        $generator = $this->getDataGenerator();
        $grouping = $generator->create_grouping(array('courseid' => $courseid));
        $group1 = $generator->create_group(array('courseid' => $courseid));
        groups_assign_grouping($grouping->id, $group1->id);
        $group2 = $generator->create_group(array('courseid' => $courseid));
        groups_assign_grouping($grouping->id, $group2->id);

        // Group 3 is not in the grouping.
        $group3 = $generator->create_group(array('courseid' => $courseid));

        // Enrol some students.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $student3 = $generator->create_user();
        $generator->enrol_user($student1->id, $courseid, $roleids['student']);
        $generator->enrol_user($student2->id, $courseid, $roleids['student']);
        $generator->enrol_user($student3->id, $courseid, $roleids['student']);

        // Place students in groups (except student 3).
        groups_add_member($group1, $student1);
        groups_add_member($group2, $student2);
        groups_add_member($group3, $student3);

        // The existing workshop doesn't have any restrictions, so user lists
        // should include all three users.
        $allusers = get_enrolled_users(\context_course::instance($courseid));
        $result = $this->workshop->get_grouped($allusers);
        $this->assertCount(4, $result);
        $users = array_keys($result[0]);
        sort($users);
        $this->assertEquals(array($student1->id, $student2->id, $student3->id), $users);
        $this->assertEquals(array($student1->id), array_keys($result[$group1->id]));
        $this->assertEquals(array($student2->id), array_keys($result[$group2->id]));
        $this->assertEquals(array($student3->id), array_keys($result[$group3->id]));

        $users = $this->workshop->get_potential_authors(false);
        $this->assertCount(3, $users);
        $users = $this->workshop->get_potential_authors(false, $group2->id);
        $this->assertEquals(array($student2->id), array_keys($users));

        // Create another test workshop with grouping set.
        $workshopitem = $this->getDataGenerator()->create_module('workshop',
                array('course' => $courseid, 'groupmode' => SEPARATEGROUPS,
                'groupingid' => $grouping->id));
        $cm = get_coursemodule_from_instance('workshop', $workshopitem->id,
                $courseid, false, MUST_EXIST);
        $workshopgrouping = new testable_workshop($workshopitem, $cm, $this->workshop->course);

        // This time the result should only include users and groups in the
        // selected grouping.
        $result = $workshopgrouping->get_grouped($allusers);
        $this->assertCount(3, $result);
        $users = array_keys($result[0]);
        sort($users);
        $this->assertEquals(array($student1->id, $student2->id), $users);
        $this->assertEquals(array($student1->id), array_keys($result[$group1->id]));
        $this->assertEquals(array($student2->id), array_keys($result[$group2->id]));

        $users = $workshopgrouping->get_potential_authors(false);
        $userids = array_keys($users);
        sort($userids);
        $this->assertEquals(array($student1->id, $student2->id), $userids);
        $users = $workshopgrouping->get_potential_authors(false, $group2->id);
        $this->assertEquals(array($student2->id), array_keys($users));

        // Enable the availability system and create another test workshop with
        // availability restriction on grouping.
        $CFG->enableavailability = true;
        $workshopitem = $this->getDataGenerator()->create_module('workshop',
                array('course' => $courseid, 'availability' => json_encode(
                    \core_availability\tree::get_root_json(array(
                    \availability_grouping\condition::get_json($grouping->id)),
                    \core_availability\tree::OP_AND, false))));
        $cm = get_coursemodule_from_instance('workshop', $workshopitem->id,
                $courseid, false, MUST_EXIST);
        $workshoprestricted = new testable_workshop($workshopitem, $cm, $this->workshop->course);

        // The get_grouped function isn't intended to apply this restriction,
        // so it should be the same as the base workshop. (Note: in reality,
        // get_grouped is always run with the parameter being the result of
        // one of the get_potential_xxx functions, so it works.)
        $result = $workshoprestricted->get_grouped($allusers);
        $this->assertCount(4, $result);
        $this->assertCount(3, $result[0]);

        $users = $workshoprestricted->get_potential_authors(false);
        $userids = array_keys($users);
        sort($userids);
        $this->assertEquals(array($student1->id, $student2->id), $userids);
        $users = $workshoprestricted->get_potential_authors(false, $group2->id);
        $this->assertEquals(array($student2->id), array_keys($users));
    }

    /**
     * Test the workshop reset feature.
     */
    public function test_reset_phase(): void {
        $this->resetAfterTest(true);

        $this->workshop->switch_phase(workshop::PHASE_CLOSED);
        $this->assertEquals(workshop::PHASE_CLOSED, $this->workshop->phase);

        $settings = (object)array(
            'reset_workshop_phase' => 0,
        );
        $status = $this->workshop->reset_userdata($settings);
        $this->assertEquals(workshop::PHASE_CLOSED, $this->workshop->phase);

        $settings = (object)array(
            'reset_workshop_phase' => 1,
        );
        $status = $this->workshop->reset_userdata($settings);
        $this->assertEquals(workshop::PHASE_SETUP, $this->workshop->phase);
        foreach ($status as $result) {
            $this->assertFalse($result['error']);
        }
    }

    /**
     * Test deleting assessments related data on workshop reset.
     */
    public function test_reset_userdata_assessments(): void {
        global $DB;
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $this->workshop->course->id);

        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');

        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);
        $subid2 = $workshopgenerator->create_submission($this->workshop->id, $student2->id);

        $asid1 = $workshopgenerator->create_assessment($subid1, $student2->id);
        $asid2 = $workshopgenerator->create_assessment($subid2, $student1->id);

        $settings = (object)array(
            'reset_workshop_assessments' => 1,
        );
        $status = $this->workshop->reset_userdata($settings);

        foreach ($status as $result) {
            $this->assertFalse($result['error']);
        }

        $this->assertEquals(2, $DB->count_records('workshop_submissions', array('workshopid' => $this->workshop->id)));
        $this->assertEquals(0, $DB->count_records('workshop_assessments'));
    }

    /**
     * Test deleting submissions related data on workshop reset.
     */
    public function test_reset_userdata_submissions(): void {
        global $DB;
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $this->workshop->course->id);

        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');

        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);
        $subid2 = $workshopgenerator->create_submission($this->workshop->id, $student2->id);

        $asid1 = $workshopgenerator->create_assessment($subid1, $student2->id);
        $asid2 = $workshopgenerator->create_assessment($subid2, $student1->id);

        $settings = (object)array(
            'reset_workshop_submissions' => 1,
        );
        $status = $this->workshop->reset_userdata($settings);

        foreach ($status as $result) {
            $this->assertFalse($result['error']);
        }

        $this->assertEquals(0, $DB->count_records('workshop_submissions', array('workshopid' => $this->workshop->id)));
        $this->assertEquals(0, $DB->count_records('workshop_assessments'));
    }

    /**
     * Test normalizing list of extensions.
     */
    public function test_normalize_file_extensions(): void {
        $this->resetAfterTest(true);

        workshop::normalize_file_extensions('');
        $this->assertDebuggingCalled();
    }

    /**
     * Test cleaning list of extensions.
     */
    public function test_clean_file_extensions(): void {
        $this->resetAfterTest(true);

        workshop::clean_file_extensions('');
        $this->assertDebuggingCalledCount(2);
    }

    /**
     * Test validation of the list of file extensions.
     */
    public function test_invalid_file_extensions(): void {
        $this->resetAfterTest(true);

        workshop::invalid_file_extensions('', '');
        $this->assertDebuggingCalledCount(3);
    }

    /**
     * Test checking file name against the list of allowed extensions.
     */
    public function test_is_allowed_file_type(): void {
        $this->resetAfterTest(true);

        workshop::is_allowed_file_type('', '');
        $this->assertDebuggingCalledCount(2);
    }

    /**
     * Test workshop::check_group_membership() functionality.
     */
    public function test_check_group_membership(): void {
        global $DB, $CFG;

        $this->resetAfterTest();

        $courseid = $this->course->id;
        $generator = $this->getDataGenerator();

        // Make test groups.
        $group1 = $generator->create_group(array('courseid' => $courseid));
        $group2 = $generator->create_group(array('courseid' => $courseid));
        $group3 = $generator->create_group(array('courseid' => $courseid));

        // Revoke the accessallgroups from non-editing teachers (tutors).
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        unassign_capability('moodle/site:accessallgroups', $roleids['teacher']);

        // Create test use accounts.
        $teacher1 = $generator->create_user();
        $tutor1 = $generator->create_user();
        $tutor2 = $generator->create_user();
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $student3 = $generator->create_user();

        // Enrol the teacher (has the access all groups permission).
        $generator->enrol_user($teacher1->id, $courseid, $roleids['editingteacher']);

        // Enrol tutors (can not access all groups).
        $generator->enrol_user($tutor1->id, $courseid, $roleids['teacher']);
        $generator->enrol_user($tutor2->id, $courseid, $roleids['teacher']);

        // Enrol students.
        $generator->enrol_user($student1->id, $courseid, $roleids['student']);
        $generator->enrol_user($student2->id, $courseid, $roleids['student']);
        $generator->enrol_user($student3->id, $courseid, $roleids['student']);

        // Add users in groups.
        groups_add_member($group1, $tutor1);
        groups_add_member($group2, $tutor2);
        groups_add_member($group1, $student1);
        groups_add_member($group2, $student2);
        groups_add_member($group3, $student3);

        // Workshop with no groups.
        $workshopitem1 = $this->getDataGenerator()->create_module('workshop', [
            'course' => $courseid,
            'groupmode' => NOGROUPS,
        ]);
        $cm = get_coursemodule_from_instance('workshop', $workshopitem1->id, $courseid, false, MUST_EXIST);
        $workshop1 = new testable_workshop($workshopitem1, $cm, $this->course);

        $this->setUser($teacher1);
        $this->assertTrue($workshop1->check_group_membership($student1->id));
        $this->assertTrue($workshop1->check_group_membership($student2->id));
        $this->assertTrue($workshop1->check_group_membership($student3->id));

        $this->setUser($tutor1);
        $this->assertTrue($workshop1->check_group_membership($student1->id));
        $this->assertTrue($workshop1->check_group_membership($student2->id));
        $this->assertTrue($workshop1->check_group_membership($student3->id));

        // Workshop in visible groups mode.
        $workshopitem2 = $this->getDataGenerator()->create_module('workshop', [
            'course' => $courseid,
            'groupmode' => VISIBLEGROUPS,
        ]);
        $cm = get_coursemodule_from_instance('workshop', $workshopitem2->id, $courseid, false, MUST_EXIST);
        $workshop2 = new testable_workshop($workshopitem2, $cm, $this->course);

        $this->setUser($teacher1);
        $this->assertTrue($workshop2->check_group_membership($student1->id));
        $this->assertTrue($workshop2->check_group_membership($student2->id));
        $this->assertTrue($workshop2->check_group_membership($student3->id));

        $this->setUser($tutor1);
        $this->assertTrue($workshop2->check_group_membership($student1->id));
        $this->assertTrue($workshop2->check_group_membership($student2->id));
        $this->assertTrue($workshop2->check_group_membership($student3->id));

        // Workshop in separate groups mode.
        $workshopitem3 = $this->getDataGenerator()->create_module('workshop', [
            'course' => $courseid,
            'groupmode' => SEPARATEGROUPS,
        ]);
        $cm = get_coursemodule_from_instance('workshop', $workshopitem3->id, $courseid, false, MUST_EXIST);
        $workshop3 = new testable_workshop($workshopitem3, $cm, $this->course);

        $this->setUser($teacher1);
        $this->assertTrue($workshop3->check_group_membership($student1->id));
        $this->assertTrue($workshop3->check_group_membership($student2->id));
        $this->assertTrue($workshop3->check_group_membership($student3->id));

        $this->setUser($tutor1);
        $this->assertTrue($workshop3->check_group_membership($student1->id));
        $this->assertFalse($workshop3->check_group_membership($student2->id));
        $this->assertFalse($workshop3->check_group_membership($student3->id));

        $this->setUser($tutor2);
        $this->assertFalse($workshop3->check_group_membership($student1->id));
        $this->assertTrue($workshop3->check_group_membership($student2->id));
        $this->assertFalse($workshop3->check_group_membership($student3->id));
    }

    /**
     * Test init_initial_bar function.
     */
    public function test_init_initial_bar(): void {
        global $SESSION;
        $this->resetAfterTest();

        $_GET['ifirst'] = 'A';
        $_GET['ilast'] = 'B';
        $contextid = $this->workshop->context->id;

        $this->workshop->init_initial_bar();
        $initialbarprefs = $this->get_initial_bar_prefs_property();

        $this->assertEquals('A', $initialbarprefs['i_first']);
        $this->assertEquals('B', $initialbarprefs['i_last']);
        $this->assertEquals('A', $SESSION->mod_workshop->initialbarprefs['id-' . $contextid]['i_first']);
        $this->assertEquals('B', $SESSION->mod_workshop->initialbarprefs['id-' . $contextid]['i_last']);

        $_GET['ifirst'] = null;
        $_GET['ilast'] = null;
        $SESSION->mod_workshop->initialbarprefs['id-' . $contextid]['i_first'] = 'D';
        $SESSION->mod_workshop->initialbarprefs['id-' . $contextid]['i_last'] = 'E';

        $this->workshop->init_initial_bar();
        $initialbarprefs = $this->get_initial_bar_prefs_property();

        $this->assertEquals('D', $initialbarprefs['i_first']);
        $this->assertEquals('E', $initialbarprefs['i_last']);
    }

    /**
     * Test empty init_initial_bar
     */
    public function test_init_initial_bar_empty(): void {
        $this->resetAfterTest();

        $this->workshop->init_initial_bar();
        $initialbarprefs = $this->get_initial_bar_prefs_property();

        $this->assertEmpty($initialbarprefs);
    }

    /**
     * Test get_initial_first function
     */
    public function test_get_initial_first(): void {
        $this->resetAfterTest();
        $this->workshop->init_initial_bar();
        $this->assertEquals(null, $this->workshop->get_initial_first());

        $_GET['ifirst'] = 'D';
        $this->workshop->init_initial_bar();
        $this->assertEquals('D', $this->workshop->get_initial_first());
    }

    /**
     * Test get_initial_last function
     */
    public function test_get_initial_last(): void {
        $this->resetAfterTest();
        $this->workshop->init_initial_bar();
        $this->assertEquals(null, $this->workshop->get_initial_last());

        $_GET['ilast'] = 'D';
        $this->workshop->init_initial_bar();
        $this->assertEquals('D', $this->workshop->get_initial_last());
    }

    /**
     * Get the protected propertyinitialbarprefs from workshop class.
     *
     * @return array initialbarspref property. eg ['i_first' => 'A', 'i_last' => 'B']
     */
    private function get_initial_bar_prefs_property(): array {

        $reflector = new \ReflectionObject($this->workshop);
        $initialbarprefsprop = $reflector->getProperty('initialbarprefs');
        $initialbarprefs = $initialbarprefsprop->getValue($this->workshop);

        return $initialbarprefs;
    }

    /**
     * Test count_all_submissions and count_all_assessments methods.
     */
    public function test_count_submissions_count_assessments(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student4 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        groups_add_member($group1, $student1);
        groups_add_member($group1, $student3);
        groups_add_member($group2, $student2);

        $activity = $this->getDataGenerator()->create_module(
            'workshop',
            ['course' => $course->id , 'groupmode' => SEPARATEGROUPS],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Set up a generator to create content.
        /** @var \mod_workshop_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');

        // Create some submissions.
        $submission1id = $generator->create_submission(
            $activity->id,
            $student1->id,
            ['title' => 'My custom title', 'grade' => 85.00000],
        );
        $submission2id = $generator->create_submission(
            $activity->id,
            $student2->id,
            ['title' => 'My custom title', 'grade' => null],
        );
        $submission3id = $generator->create_submission(
            $activity->id,
            $student3->id,
            ['title' => 'My custom title', 'grade' => null],
        );
        $submission4id = $generator->create_submission(
            $activity->id,
            $student4->id,
            ['title' => 'My custom title', 'grade' => null],
        );
        // Assess one submission.
        $generator->create_assessment(
            $submission1id,
            $student1->id,
            ['weight' => 3, 'grade' => 95.00000],
        );
        $generator->create_assessment(
            $submission2id,
            $student2->id,
            ['weight' => 3, 'grade' => null],
        );
        $generator->create_assessment(
            $submission3id,
            $student3->id,
            ['weight' => 3, 'grade' => null],
        );
        $generator->create_assessment(
            $submission4id,
            $student4->id,
            ['weight' => 3, 'grade' => 35.00000],
        );

        $manager = new workshop($activity, $cm, $course, $cm->context);

        $this->assertEquals(4, $manager->count_all_submissions());
        $this->assertEquals(1, $manager->count_all_submissions(authorids: [$student1->id]));
        $this->assertEquals(2, $manager->count_all_submissions(authorids: [$student1->id, $student2->id]));

        $this->assertEquals(2, $manager->count_all_submissions(groupids: [$group1->id]));
        $this->assertEquals(1, $manager->count_all_submissions(groupids: [$group2->id]));
        $this->assertEquals(3, $manager->count_all_submissions(groupids: [$group1->id, $group2->id]));
        $this->assertEquals(1, $manager->count_all_submissions(authorids: [$student1->id], groupids: [$group1->id]));
        $this->assertEquals(0, $manager->count_all_submissions(authorids: [$student2->id], groupids: [$group1->id]));

        $this->assertEquals(4, $manager->count_all_assessments());
        $this->assertEquals(2, $manager->count_all_assessments(onlygraded: true));

        $this->assertEquals(2, $manager->count_all_assessments(onlygraded: false, groupids: [$group1->id]));
        $this->assertEquals(1, $manager->count_all_assessments(onlygraded: true, groupids: [$group1->id]));

        $this->assertEquals(1, $manager->count_all_assessments(onlygraded: false, groupids: [$group2->id]));
        $this->assertEquals(0, $manager->count_all_assessments(onlygraded: true, groupids: [$group2->id]));

        $this->assertEquals(3, $manager->count_all_assessments(onlygraded: false, groupids: [$group1->id, $group2->id]));
        $this->assertEquals(1, $manager->count_all_assessments(onlygraded: true, groupids: [$group1->id, $group2->id]));
    }

    /**
     * Test count_all_participants method.
     */
    public function test_count_all_participants(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student3 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student4 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $guest = $this->getDataGenerator()->create_and_enrol($course, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);

        $activity = $this->getDataGenerator()->create_module(
            'workshop',
            ['course' => $course->id, 'groupmode' => SEPARATEGROUPS],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $manager = new workshop($activity, $cm, $course, $cm->context);

        $this->assertEquals(4, $manager->count_all_participants());
        $this->assertEquals(2, $manager->count_all_participants(groupids: [$group1->id]));
        $this->assertEquals(1, $manager->count_all_participants(groupids: [$group2->id]));
        $this->assertEquals(3, $manager->count_all_participants(groupids: [$group1->id, $group2->id]));
    }
}
