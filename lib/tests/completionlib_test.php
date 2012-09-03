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
 * Completion tests
 *
 * @package    core_completion
 * @category   phpunit
 * @copyright  2008 Sam Marshall
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/completionlib.php');


class completionlib_testcase extends basic_testcase {

    var $realdb, $realcfg, $realsession, $realuser;

    protected function setUp() {
        global $DB, $CFG, $SESSION, $USER;
        parent::setUp();

        $this->realdb = $DB;
        $this->realcfg = $CFG;
        $this->realsession = $SESSION;
        $this->prevuser = $USER;

        $DB =  $this->getMock(get_class($DB));
        $CFG = clone($this->realcfg);
        $CFG->prefix = 'test_';
        $CFG->enablecompletion = COMPLETION_ENABLED;
        $SESSION = new stdClass();
        $USER = (object)array('id' =>314159);
    }

    protected function tearDown() {
        global $DB,$CFG,$SESSION,$USER;
        $DB = $this->realdb;
        $CFG = $this->realcfg;
        $SESSION = $this->realsession;
        $USER = $this->prevuser;

        parent::tearDown();
    }

    function test_is_enabled() {
        global $CFG;

        // Config alone
        $CFG->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, completion_info::is_enabled_for_site());
        $CFG->enablecompletion = COMPLETION_ENABLED;
        $this->assertEquals(COMPLETION_ENABLED, completion_info::is_enabled_for_site());

        // Course
        $course = (object)array('id' =>13);
        $c = new completion_info($course);
        $course->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled());
        $course->enablecompletion = COMPLETION_ENABLED;
        $this->assertEquals(COMPLETION_ENABLED, $c->is_enabled());
        $CFG->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled());

        // Course and CM
        $cm = new stdClass();
        $cm->completion = COMPLETION_TRACKING_MANUAL;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled($cm));
        $CFG->enablecompletion = COMPLETION_ENABLED;
        $course->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled($cm));
        $course->enablecompletion = COMPLETION_ENABLED;
        $this->assertEquals(COMPLETION_TRACKING_MANUAL, $c->is_enabled($cm));
        $cm->completion = COMPLETION_TRACKING_NONE;
        $this->assertEquals(COMPLETION_TRACKING_NONE, $c->is_enabled($cm));
        $cm->completion = COMPLETION_TRACKING_AUTOMATIC;
        $this->assertEquals(COMPLETION_TRACKING_AUTOMATIC, $c->is_enabled($cm));
    }

    function test_update_state() {

        $c = $this->getMock('completion_info', array('is_enabled','get_data','internal_get_state','internal_set_data'), array((object)array('id'=>42)));
        $cm = (object)array('id'=>13, 'course'=>42);

        // Not enabled, should do nothing
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(false));
        $c->update_state($cm);

        // Enabled, but current state is same as possible result, do nothing
        $current = (object)array('completionstate'=>COMPLETION_COMPLETE);
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('get_data')
            ->with($cm, false, 0)
            ->will($this->returnValue($current));
        $c->update_state($cm, COMPLETION_COMPLETE);

        // Enabled, but current state is a specific one and new state is just
        // complete, so do nothing
        $current->completionstate = COMPLETION_COMPLETE_PASS;
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('get_data')
            ->with($cm, false, 0)
            ->will($this->returnValue($current));
        $c->update_state($cm, COMPLETION_COMPLETE);

        // Manual, change state (no change)
        $cm = (object)array('id'=>13,'course'=>42, 'completion'=>COMPLETION_TRACKING_MANUAL);
        $current->completionstate=COMPLETION_COMPLETE;
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('get_data')
            ->with($cm, false, 0)
            ->will($this->returnValue($current));
        $c->update_state($cm, COMPLETION_COMPLETE);

        // Manual, change state (change)
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('get_data')
            ->with($cm, false, 0)
            ->will($this->returnValue($current));
        $changed = clone($current);
        $changed->timemodified = time();
        $changed->completionstate = COMPLETION_INCOMPLETE;
        $c->expects($this->at(2))
            ->method('internal_set_data')
            ->with($cm, $changed);
        $c->update_state($cm, COMPLETION_INCOMPLETE);

        // Auto, change state
        $cm = (object)array('id'=>13,'course'=>42, 'completion'=>COMPLETION_TRACKING_AUTOMATIC);
        $current = (object)array('completionstate'=>COMPLETION_COMPLETE);
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('get_data')
            ->with($cm, false, 0)
            ->will($this->returnValue($current));
        $c->expects($this->at(2))
            ->method('internal_get_state')
            ->will($this->returnValue(COMPLETION_COMPLETE_PASS));
        $changed = clone($current);
        $changed->timemodified = time();
        $changed->completionstate = COMPLETION_COMPLETE_PASS;
        $c->expects($this->at(3))
            ->method('internal_set_data')
            ->with($cm, $changed);
        $c->update_state($cm, COMPLETION_COMPLETE_PASS);
    }

    function test_internal_get_state() {
        global $DB;

        $c = $this->getMock('completion_info', array('internal_get_grade_state'), array((object)array('id'=>42)));
        $cm = (object)array('id'=>13, 'course'=>42, 'completiongradeitemnumber'=>null);

        // If view is required, but they haven't viewed it yet
        $cm->completionview = COMPLETION_VIEW_REQUIRED;
        $current = (object)array('viewed'=>COMPLETION_NOT_VIEWED);
        $this->assertEquals(COMPLETION_INCOMPLETE, $c->internal_get_state($cm, 123, $current));

        // OK set view not required
        $cm->completionview = COMPLETION_VIEW_NOT_REQUIRED;

        // Test not getting module name
        $cm->modname='label';
        $this->assertEquals(COMPLETION_COMPLETE, $c->internal_get_state($cm, 123, $current));

        // Test getting module name
        $cm->module = 13;
        unset($cm->modname);
        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->once())
            ->method('get_field')
            ->with('modules', 'name', array('id'=>13))
            ->will($this->returnValue('lable'));
        $this->assertEquals(COMPLETION_COMPLETE, $c->internal_get_state($cm, 123, $current));

        // Note: This function is not fully tested (including kind of the main
        // part) because:
        // * the grade_item/grade_grade calls are static and can't be mocked
        // * the plugin_supports call is static and can't be mocked
    }

    function test_set_module_viewed() {

        $c = $this->getMock('completion_info',
            array('delete_all_state', 'get_tracked_users', 'update_state', 'internal_get_grade_state', 'is_enabled', 'get_data', 'internal_get_state', 'internal_set_data'),
            array((object)array('id'=>42)));
        $cm = (object)array('id'=>13, 'course'=>42);

        // Not tracking completion, should do nothing
        $cm->completionview = COMPLETION_VIEW_NOT_REQUIRED;
        $c->set_module_viewed($cm);

        // Tracking completion but completion is disabled, should do nothing
        $cm->completionview = COMPLETION_VIEW_REQUIRED;
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(false));
        $c->set_module_viewed($cm);

        // Now it's enabled, we expect it to get data. If data already has
        // viewed, still do nothing
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('get_data')
            ->with($cm, 0)
            ->will($this->returnValue((object)array('viewed'=>COMPLETION_VIEWED)));
        $c->set_module_viewed($cm);

        // OK finally one that hasn't been viewed, now it should set it viewed
        // and update state
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('get_data')
            ->with($cm, false, 1337)
            ->will($this->returnValue((object)array('viewed'=>COMPLETION_NOT_VIEWED)));
        $c->expects($this->at(2))
            ->method('internal_set_data')
            ->with($cm, (object)array('viewed'=>COMPLETION_VIEWED));
        $c->expects($this->at(3))
            ->method('update_state')
            ->with($cm, COMPLETION_COMPLETE, 1337);
        $c->set_module_viewed($cm, 1337);
    }

    function test_count_user_data() {
        global $DB;

        $course = (object)array('id'=>13);
        $cm = (object)array('id'=>42);

        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->at(0))
            ->method('get_field_sql')
            ->will($this->returnValue(666));

/*
        $DB->expectOnce('get_field_sql',array(new IgnoreWhitespaceExpectation("SELECT
    COUNT(1)
FROM
    {course_modules_completion}
WHERE
    coursemoduleid=? AND completionstate<>0"),array(42)));
*/

        $c = new completion_info($course);
        $this->assertEquals(666, $c->count_user_data($cm));
    }

    function test_delete_all_state() {
        global $DB, $SESSION;

        $course = (object)array('id'=>13);
        $cm = (object)array('id'=>42,'course'=>13);
        $c = new completion_info($course);

        // Check it works ok without data in session
        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->at(0))
            ->method('delete_records')
            ->with('course_modules_completion', array('coursemoduleid'=>42))
            ->will($this->returnValue(true));
        $c->delete_all_state($cm);

        // Build up a session to check it deletes the right bits from it
        // (and not other bits)
        $SESSION->completioncache=array();
        $SESSION->completioncache[13]=array();
        $SESSION->completioncache[13][42]='foo';
        $SESSION->completioncache[13][43]='foo';
        $SESSION->completioncache[14]=array();
        $SESSION->completioncache[14][42]='foo';
        $DB->expects($this->at(0))
            ->method('delete_records')
            ->with('course_modules_completion', array('coursemoduleid'=>42))
            ->will($this->returnValue(true));
        $c->delete_all_state($cm);
        $this->assertEquals(array(13=>array(43=>'foo'), 14=>array(42=>'foo')), $SESSION->completioncache);
    }

    function test_reset_all_state() {
        global $DB;

        $c = $this->getMock('completion_info',
            array('delete_all_state', 'get_tracked_users','update_state', 'internal_get_grade_state', 'is_enabled', 'get_data', 'internal_get_state', 'internal_set_data'),
            array((object)array('id'=>42)));

        $cm = (object)array('id'=>13, 'course'=>42, 'completion'=>COMPLETION_TRACKING_AUTOMATIC);

        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->at(0))
            ->method('get_recordset')
            ->will($this->returnValue(
                new completion_test_fake_recordset(array((object)array('id'=>1, 'userid'=>100),(object)array('id'=>2, 'userid'=>101)))));

        $c->expects($this->at(0))
            ->method('delete_all_state')
            ->with($cm);

        $c->expects($this->at(1))
            ->method('get_tracked_users')
            ->will($this->returnValue(array(
            (object)array('id'=>100,'firstname'=>'Woot','lastname'=>'Plugh'),
            (object)array('id'=>201,'firstname'=>'Vroom','lastname'=>'Xyzzy'))));

        $c->expects($this->at(2))
            ->method('update_state')
            ->with($cm,COMPLETION_UNKNOWN, 100);
        $c->expects($this->at(3))
            ->method('update_state')
            ->with($cm,COMPLETION_UNKNOWN, 101);
        $c->expects($this->at(4))
            ->method('update_state')
            ->with($cm,COMPLETION_UNKNOWN, 201);

        $c->reset_all_state($cm);
    }

    function test_get_data() {
        global $DB, $SESSION;

        $c = new completion_info((object)array('id'=>42));
        $cm = (object)array('id'=>13, 'course'=>42);

        // 1. Not current user, record exists
        $sillyrecord = (object)array('frog'=>'kermit');

        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->at(0))
            ->method('get_record')
            ->with('course_modules_completion', array('coursemoduleid'=>13,'userid'=>123))
            ->will($this->returnValue($sillyrecord));
        $result = $c->get_data($cm,false,123);
        $this->assertEquals($sillyrecord, $result);
        $this->assertTrue(empty($SESSION->completioncache));

        // 2. Not current user, default record, wholecourse (ignored)
        $DB->expects($this->at(0))
            ->method('get_record')
            ->with('course_modules_completion', array('coursemoduleid'=>13,'userid'=>123))
            ->will($this->returnValue(false));
        $result=$c->get_data($cm,true,123);
        $this->assertEquals((object)array(
            'id'=>'0','coursemoduleid'=>13,'userid'=>123,'completionstate'=>0,
            'viewed'=>0,'timemodified'=>0),$result);
        $this->assertTrue(empty($SESSION->completioncache));

        // 3. Current user, single record, not from cache
        $DB->expects($this->at(0))
            ->method('get_record')
            ->with('course_modules_completion', array('coursemoduleid'=>13,'userid'=>314159))
            ->will($this->returnValue($sillyrecord));
        $result = $c->get_data($cm);
        $this->assertEquals($sillyrecord, $result);
        $this->assertEquals($sillyrecord, $SESSION->completioncache[42][13]);
        // When checking time(), allow for second overlaps
        $this->assertTrue(time()-$SESSION->completioncache[42]['updated']<2);

        // 4. Current user, 'whole course', but from cache
        $result = $c->get_data($cm, true);
        $this->assertEquals($sillyrecord, $result);

        // 5. Current user, single record, cache expired
        $SESSION->completioncache[42]['updated']=37; // Quite a long time ago
        $now = time();
        $SESSION->completioncache[17]['updated']=$now;
        $SESSION->completioncache[39]['updated']=72; // Also a long time ago
        $DB->expects($this->at(0))
            ->method('get_record')
            ->with('course_modules_completion', array('coursemoduleid'=>13,'userid'=>314159))
            ->will($this->returnValue($sillyrecord));
        $result = $c->get_data($cm, false);
        $this->assertEquals($sillyrecord, $result);

        // Check that updated value is right, then fudge it to make next compare
        // work
        $this->assertTrue(time()-$SESSION->completioncache[42]['updated']<2);
        $SESSION->completioncache[42]['updated']=$now;
        // Check things got expired from cache
        $this->assertEquals(array(42=>array(13=>$sillyrecord, 'updated'=>$now), 17=>array('updated'=>$now)), $SESSION->completioncache);

        // 6. Current user, 'whole course' and record not in cache
        unset($SESSION->completioncache);

        // Scenario: Completion data exists for one CMid
        $basicrecord = (object)array('coursemoduleid'=>13);
        $DB->expects($this->at(0))
            ->method('get_records_sql')
            ->will($this->returnValue(array('1'=>$basicrecord)));

/*
        $DB->expectAt(0,'get_records_sql',array(new IgnoreWhitespaceExpectation("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cmc.coursemoduleid=cm.id
WHERE
    cm.course=? AND cmc.userid=?"),array(42,314159)));
*/
        // There are two CMids in total, the one we had data for and another one
        $modinfo = new stdClass();
        $modinfo->cms = array((object)array('id'=>13), (object)array('id'=>14));
        $result = $c->get_data($cm, true, 0, $modinfo);

        // Check result
        $this->assertEquals($basicrecord, $result);

        // Check the cache contents
        $this->assertTrue(time()-$SESSION->completioncache[42]['updated']<2);
        $SESSION->completioncache[42]['updated'] = $now;
        $this->assertEquals(array(42=>array(13=>$basicrecord, 14=>(object)array(
            'id'=>'0', 'coursemoduleid'=>14, 'userid'=>314159, 'completionstate'=>0,
            'viewed'=>0, 'timemodified'=>0), 'updated'=>$now)), $SESSION->completioncache);
    }

    function test_internal_set_data() {
        global $DB, $SESSION;

        $cm = (object)array('course' => 42,'id' => 13);
        $c = new completion_info((object)array('id' => 42));

        // 1) Test with new data
        $data = (object)array('id'=>0, 'userid' => 314159, 'coursemoduleid' => 99);
        $DB->expects($this->at(0))
            ->method('start_delegated_transaction')
            ->will($this->returnValue($this->getMock('moodle_transaction', array(), array($DB))));

        $DB->expects($this->at(1))
            ->method('get_field')
            ->with('course_modules_completion', 'id', array('coursemoduleid'=>99, 'userid'=>314159))
            ->will($this->returnValue(false));

        $DB->expects($this->at(2))
            ->method('insert_record')
            ->will($this->returnValue(4));

        $c->internal_set_data($cm, $data);
        $this->assertEquals(4, $data->id);
        $this->assertEquals(array(42 => array(13 => $data)), $SESSION->completioncache);

        // 2) Test with existing data and for different user (not cached)
        unset($SESSION->completioncache);
        $d2 = (object)array('id' => 7, 'userid' => 17, 'coursemoduleid' => 66);
        $DB->expects($this->at(0))
            ->method('start_delegated_transaction')
            ->will($this->returnValue($this->getMock('moodle_transaction', array(), array($DB))));
        $DB->expects($this->at(1))
            ->method('update_record')
            ->with('course_modules_completion', $d2);
        $c->internal_set_data($cm, $d2);
        $this->assertFalse(isset($SESSION->completioncache));

        // 3) Test where it THINKS the data is new (from cache) but actually
        // in the database it has been set since
        // 1) Test with new data
        $data = (object)array('id'=>0, 'userid' => 314159, 'coursemoduleid' => 99);
        $d3 = (object)array('id' => 13, 'userid' => 314159, 'coursemoduleid' => 99);
        $DB->expects($this->at(0))
            ->method('start_delegated_transaction')
            ->will($this->returnValue($this->getMock('moodle_transaction', array(), array($DB))));
        $DB->expects($this->at(1))
            ->method('get_field')
            ->with('course_modules_completion', 'id', array('coursemoduleid' => 99, 'userid' => 314159))
            ->will($this->returnValue(13));
        $DB->expects($this->at(2))
            ->method('update_record')
            ->with('course_modules_completion', $d3);
        $c->internal_set_data($cm, $data);
    }

    function test_get_activities() {
        global $DB;

        $c = new completion_info((object)array('id'=>42));

        // Try with no activities
        $DB->expects($this->at(0))
            ->method('get_records_select')
            ->with('course_modules', 'course=42 AND completion<>'.COMPLETION_TRACKING_NONE)
            ->will($this->returnValue(array()));
        $result = $c->get_activities();
        $this->assertEquals(array(), $result);

        // Try with an activity (need to fake up modinfo for it as well)
        $DB->expects($this->at(0))
            ->method('get_records_select')
            ->with('course_modules', 'course=42 AND completion<>'.COMPLETION_TRACKING_NONE)
            ->will($this->returnValue(array(13=>(object)array('id'=>13))));
        $modinfo = new stdClass;
        $modinfo->sections = array(array(1, 2, 3), array(12, 13, 14));
        $modinfo->cms[13] = (object)array('modname'=>'frog', 'name'=>'kermit');
        $result = $c->get_activities($modinfo);
        $this->assertEquals(array(13=>(object)array('id'=>13, 'modname'=>'frog', 'name'=>'kermit')), $result);
    }

    // get_tracked_users() cannot easily be tested because it uses
    // get_role_users, so skipping that

    function test_get_progress_all() {
        global $DB;

        $c = $this->getMock('completion_info',
            array('delete_all_state', 'get_tracked_users', 'update_state', 'internal_get_grade_state', 'is_enabled', 'get_data', 'internal_get_state', 'internal_set_data'),
            array((object)array('id'=>42)));

        // 1) Basic usage
        $c->expects($this->at(0))
            ->method('get_tracked_users')
            ->with(false,  array(),  0,  '',  '',  '',  null)
            ->will($this->returnValue(array(
                (object)array('id'=>100, 'firstname'=>'Woot', 'lastname'=>'Plugh'),
                (object)array('id'=>201, 'firstname'=>'Vroom', 'lastname'=>'Xyzzy'))));
        $DB->expects($this->at(0))
            ->method('get_in_or_equal')
            ->with(array(100, 201))
            ->will($this->returnValue(array(' IN (100, 201)', array())));
        $progress1 = (object)array('userid'=>100, 'coursemoduleid'=>13);
        $progress2 = (object)array('userid'=>201, 'coursemoduleid'=>14);
        $DB->expects($this->at(1))
            ->method('get_recordset_sql')
            ->will($this->returnValue(new completion_test_fake_recordset(array($progress1, $progress2))));

/*
        $DB->expectAt(0, 'get_recordset_sql', array(new IgnoreWhitespaceExpectation("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid
WHERE
    cm.course = ? AND cmc.userid IN (100, 201)"), array(42)));
*/

        $this->assertEquals(array(
                100 => (object)array('id'=>100, 'firstname'=>'Woot', 'lastname'=>'Plugh',
                    'progress'=>array(13=>$progress1)),
                201 => (object)array('id'=>201, 'firstname'=>'Vroom', 'lastname'=>'Xyzzy',
                    'progress'=>array(14=>$progress2)),
            ), $c->get_progress_all(false));

        // 2) With more than 1, 000 results
        $tracked = array();
        $ids = array();
        $progress = array();
        for($i = 100;$i<2000;$i++) {
            $tracked[] = (object)array('id'=>$i, 'firstname'=>'frog', 'lastname'=>$i);
            $ids[] = $i;
            $progress[] = (object)array('userid'=>$i, 'coursemoduleid'=>13);
            $progress[] = (object)array('userid'=>$i, 'coursemoduleid'=>14);
        }
        $c->expects($this->at(0))
            ->method('get_tracked_users')
            ->with(true,  3,  0,  '',  '',  '',  null)
            ->will($this->returnValue($tracked));
        $DB->expects($this->at(0))
            ->method('get_in_or_equal')
            ->with(array_slice($ids, 0, 1000))
            ->will($this->returnValue(array(' IN whatever', array())));
        $DB->expects($this->at(1))
            ->method('get_recordset_sql')
            ->will($this->returnValue(new completion_test_fake_recordset(array_slice($progress, 0, 1000))));

/*
        $DB->expectAt(1, 'get_recordset_sql', array(new IgnoreWhitespaceExpectation("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cm.id = cmc.coursemoduleid
WHERE
    cm.course = ? AND cmc.userid IN whatever"), array(42)));
*/

        $DB->expects($this->at(2))
            ->method('get_in_or_equal')
            ->with(array_slice($ids, 1000))
            ->will($this->returnValue(array(' IN whatever2', array())));
        $DB->expects($this->at(3))
            ->method('get_recordset_sql')
            ->will($this->returnValue(new completion_test_fake_recordset(array_slice($progress, 1000))));

        $result = $c->get_progress_all(true, 3);
        $resultok = true;
        $resultok  =  $resultok && ($ids == array_keys($result));

        foreach($result as $userid => $data) {
            $resultok  =  $resultok && $data->firstname == 'frog';
            $resultok  =  $resultok && $data->lastname == $userid;
            $resultok  =  $resultok && $data->id == $userid;
            $cms = $data->progress;
            $resultok =  $resultok && (array(13, 14) == array_keys($cms));
            $resultok =  $resultok && ((object)array('userid'=>$userid, 'coursemoduleid'=>13) == $cms[13]);
            $resultok =  $resultok && ((object)array('userid'=>$userid, 'coursemoduleid'=>14) == $cms[14]);
        }
        $this->assertTrue($resultok);
    }

    function test_inform_grade_changed() {
        $c = $this->getMock('completion_info',
            array('delete_all_state', 'get_tracked_users', 'update_state', 'internal_get_grade_state', 'is_enabled', 'get_data', 'internal_get_state', 'internal_set_data'),
            array((object)array('id'=>42)));

        $cm = (object)array('course'=>42, 'id'=>13, 'completion'=>0, 'completiongradeitemnumber'=>null);
        $item = (object)array('itemnumber'=>3,  'gradepass'=>1,  'hidden'=>0);
        $grade = (object)array('userid'=>31337,  'finalgrade'=>0,  'rawgrade'=>0);

        // Not enabled (should do nothing)
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(false));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Enabled but still no grade completion required,  should still do nothing
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Enabled and completion required but item number is wrong,  does nothing
        $cm = (object)array('course'=>42, 'id'=>13, 'completion'=>0, 'completiongradeitemnumber'=>7);
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Enabled and completion required and item number right. It is supposed
        // to call update_state with the new potential state being obtained from
        // internal_get_grade_state.
        $cm = (object)array('course'=>42, 'id'=>13, 'completion'=>0, 'completiongradeitemnumber'=>3);
        $grade = (object)array('userid'=>31337,  'finalgrade'=>1,  'rawgrade'=>0);
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('update_state')
            ->with($cm, COMPLETION_COMPLETE_PASS, 31337)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Same as above but marked deleted. It is supposed to call update_state
        // with new potential state being COMPLETION_INCOMPLETE
        $cm = (object)array('course'=>42, 'id'=>13, 'completion'=>0, 'completiongradeitemnumber'=>3);
        $grade = (object)array('userid'=>31337,  'finalgrade'=>1,  'rawgrade'=>0);
        $c->expects($this->at(0))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->at(1))
            ->method('update_state')
            ->with($cm, COMPLETION_INCOMPLETE, 31337)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, true);
    }

    function test_internal_get_grade_state() {
        $item = new stdClass;
        $grade = new stdClass;

        $item->gradepass = 4;
        $item->hidden = 0;
        $grade->rawgrade = 4.0;
        $grade->finalgrade = null;

        // Grade has pass mark and is not hidden,  user passes
        $this->assertEquals(
            COMPLETION_COMPLETE_PASS,
            completion_info::internal_get_grade_state($item, $grade));

        // Same but user fails
        $grade->rawgrade = 3.9;
        $this->assertEquals(
            COMPLETION_COMPLETE_FAIL,
            completion_info::internal_get_grade_state($item, $grade));

        // User fails on raw grade but passes on final
        $grade->finalgrade = 4.0;
        $this->assertEquals(
            COMPLETION_COMPLETE_PASS,
            completion_info::internal_get_grade_state($item, $grade));

        // Item is hidden
        $item->hidden = 1;
        $this->assertEquals(
            COMPLETION_COMPLETE,
            completion_info::internal_get_grade_state($item, $grade));

        // Item isn't hidden but has no pass mark
        $item->hidden = 0;
        $item->gradepass = 0;
        $this->assertEquals(
            COMPLETION_COMPLETE,
            completion_info::internal_get_grade_state($item, $grade));
    }
}


class completion_test_fake_recordset implements Iterator {
    var $closed;
    var $values, $index;

    function __construct($values) {
        $this->values = $values;
        $this->index = 0;
    }

    function current() {
        return $this->values[$this->index];
    }

    function key() {
        return $this->values[$this->index];
    }

    function next() {
        $this->index++;
    }

    function rewind() {
        $this->index = 0;
    }

    function valid() {
        return count($this->values) > $this->index;
    }

    function close() {
        $this->closed = true;
    }

    function was_closed() {
        return $this->closed;
    }
}
