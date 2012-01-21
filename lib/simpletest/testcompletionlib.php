<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}
require_once($CFG->libdir.'/completionlib.php');

global $DB;
Mock::generate(get_class($DB), 'mock_database');
Mock::generate('moodle_transaction', 'mock_transaction');

Mock::generatePartial('completion_info','completion_cutdown',
    array('delete_all_state','get_tracked_users','update_state',
        'internal_get_grade_state','is_enabled','get_data','internal_get_state','internal_set_data'));
Mock::generatePartial('completion_info','completion_cutdown2',
    array('is_enabled','get_data','internal_get_state','internal_set_data'));
Mock::generatePartial('completion_info','completion_cutdown3',
    array('internal_get_grade_state'));

class fake_recordset implements Iterator {
    var $closed;
    var $values,$index;

    function fake_recordset($values) {
        $this->values=$values;
        $this->index=0;
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
        $this->index=0;
    }

    function valid() {
        return count($this->values) > $this->index;
    }

    function close() {
        $this->closed=true;
    }

    function was_closed() {
        return $this->closed;
    }
}

/**
 * Expectation that checks an object for given values (normal equality test)
 * plus a 'timemodified' field that is current (last second or two).
 */
class TimeModifiedExpectation extends SimpleExpectation {
    private $otherfields;

    /**
     * @param array $otherfields Array key=>value of required object fields
     */
    function TimeModifiedExpectation($otherfields) {
        $this->otherfields=$otherfields;
    }

    function test($thing) {
        $thingfields=(array)$thing;
        foreach($this->otherfields as $key=>$value) {
            if(!array_key_exists($key,$thingfields)) {
                return false;
            }
            if($thingfields[$key]!=$value) {
                return false;
            }
        }

        $timedifference=time()-$thing->timemodified;
        return ($timedifference < 2 && $timedifference>=0);
    }

    function testMessage($thing) {
        return "Object does not match fields/time requirement";
    }
}

class completionlib_test extends UnitTestCaseUsingDatabase {

    public static $includecoverage = array('lib/completionlib.php');
    var $realdb,$realcfg,$realsession,$realuser;

    function setUp() {
        global $DB,$CFG,$SESSION,$USER;
        $this->realdb=$DB;
        $this->realcfg=$CFG;
        $this->realsession=$SESSION;
        $this->prevuser=$USER;
        $DB=new mock_database();
        $CFG=clone($this->realcfg);
        $CFG->prefix='test_';
        $CFG->enablecompletion=COMPLETION_ENABLED;
        $SESSION=new stdClass();
        $USER=(object)array('id'=>314159);
    }

    function tearDown() {
        global $DB,$CFG,$SESSION,$USER;
        $DB=$this->realdb;
        $CFG=$this->realcfg;
        $SESSION=$this->realsession;
        $USER=$this->prevuser;
    }

    function test_is_enabled() {
        global $CFG;

        // Config alone
        $CFG->enablecompletion=COMPLETION_DISABLED;
        $this->assertEqual(COMPLETION_DISABLED,completion_info::is_enabled_for_site());
        $CFG->enablecompletion=COMPLETION_ENABLED;
        $this->assertEqual(COMPLETION_ENABLED,completion_info::is_enabled_for_site());

        // Course
        //$course=new stdClass;
        $course=(object)array('id'=>13);
        $c=new completion_info($course);
        $course->enablecompletion=COMPLETION_DISABLED;
        $this->assertEqual(COMPLETION_DISABLED,$c->is_enabled());
        $course->enablecompletion=COMPLETION_ENABLED;
        $this->assertEqual(COMPLETION_ENABLED,$c->is_enabled());
        $CFG->enablecompletion=COMPLETION_DISABLED;
        $this->assertEqual(COMPLETION_DISABLED,$c->is_enabled());

        // Course and CM
        $cm=new stdClass;
        $cm->completion=COMPLETION_TRACKING_MANUAL;
        $this->assertEqual(COMPLETION_DISABLED,$c->is_enabled($cm));
        $CFG->enablecompletion=COMPLETION_ENABLED;
        $course->enablecompletion=COMPLETION_DISABLED;
        $this->assertEqual(COMPLETION_DISABLED,$c->is_enabled($cm));
        $course->enablecompletion=COMPLETION_ENABLED;
        $this->assertEqual(COMPLETION_TRACKING_MANUAL,$c->is_enabled($cm));
        $cm->completion=COMPLETION_TRACKING_NONE;
        $this->assertEqual(COMPLETION_TRACKING_NONE,$c->is_enabled($cm));
        $cm->completion=COMPLETION_TRACKING_AUTOMATIC;
        $this->assertEqual(COMPLETION_TRACKING_AUTOMATIC,$c->is_enabled($cm));
    }

    function test_update_state() {
        $c=new completion_cutdown2();
        $c->__construct((object)array('id'=>42));
        $cm=(object)array('id'=>13,'course'=>42);

        // Not enabled, should do nothing
        $c->expectAt(0,'is_enabled',array($cm));
        $c->setReturnValueAt(0,'is_enabled',false);
        $c->update_state($cm);

        // Enabled, but current state is same as possible result, do nothing
        $current=(object)array('completionstate'=>COMPLETION_COMPLETE);
        $c->expectAt(1,'is_enabled',array($cm));
        $c->setReturnValueAt(1,'is_enabled',true);

        $c->expectAt(0,'get_data',array($cm,false,0));
        $c->setReturnValueAt(0,'get_data',$current);
        $c->update_state($cm,COMPLETION_COMPLETE);

        // Enabled, but current state is a specific one and new state is just
        // omplete, so do nothing
        $current->completionstate=COMPLETION_COMPLETE_PASS;
        $c->expectAt(2,'is_enabled',array($cm));
        $c->setReturnValueAt(2,'is_enabled',true);
        $c->expectAt(1,'get_data',array($cm,false,0));
        $c->setReturnValueAt(1,'get_data',$current);
        $c->update_state($cm,COMPLETION_COMPLETE);

        // Manual, change state (no change)
        $cm->completion=COMPLETION_TRACKING_MANUAL;
        $current->completionstate=COMPLETION_COMPLETE;
        $c->expectAt(3,'is_enabled',array($cm));
        $c->setReturnValueAt(3,'is_enabled',true);
        $c->expectAt(2,'get_data',array($cm,false,0));
        $c->setReturnValueAt(2,'get_data',$current);
        $c->update_state($cm,COMPLETION_COMPLETE);

        // Manual, change state (change)
        $c->expectAt(4,'is_enabled',array($cm));
        $c->setReturnValueAt(4,'is_enabled',true);
        $c->expectAt(3,'get_data',array($cm,false,0));
        $c->setReturnValueAt(3,'get_data',$current);
        $c->expectAt(0,'internal_set_data',array($cm,
            new TimeModifiedExpectation(array('completionstate'=>COMPLETION_INCOMPLETE))));
        $c->update_state($cm,COMPLETION_INCOMPLETE);

        // Auto, change state
        $cm->completion=COMPLETION_TRACKING_AUTOMATIC;
        $c->expectAt(5,'is_enabled',array($cm));
        $c->setReturnValueAt(5,'is_enabled',true);
        $c->expectAt(4,'get_data',array($cm,false,0));
        $c->setReturnValueAt(4,'get_data',$current);
        $c->expectAt(0,'internal_get_state',array($cm,0,$current));
        $c->setReturnValueAt(0,'internal_get_state',COMPLETION_COMPLETE_PASS);
        $c->expectAt(1,'internal_set_data',array($cm,
            new TimeModifiedExpectation(array('completionstate'=>COMPLETION_COMPLETE_PASS))));
        $c->update_state($cm,COMPLETION_COMPLETE_PASS);

        $c->tally();
    }

    function test_internal_get_state() {
        global $DB;

        $c=new completion_cutdown3();
        $c->__construct((object)array('id'=>42));
        $cm=(object)array('id'=>13,'course'=>42,'completiongradeitemnumber'=>null);

        // If view is required, but they haven't viewed it yet
        $cm->completionview=COMPLETION_VIEW_REQUIRED;
        $current=(object)array('viewed'=>COMPLETION_NOT_VIEWED);
        $this->assertEqual(COMPLETION_INCOMPLETE,$c->internal_get_state($cm,123,$current));

        // OK set view not required
        $cm->completionview=COMPLETION_VIEW_NOT_REQUIRED;

        // Test not getting module name
        $cm->modname='label';
        $this->assertEqual(COMPLETION_COMPLETE,$c->internal_get_state($cm,123,$current));

        // Test getting module name
        $cm->module=13;
        unset($cm->modname);
        $DB->expectOnce('get_field',array('modules','name',array('id'=>13)));
        $DB->setReturnValue('get_field','label');
        $this->assertEqual(COMPLETION_COMPLETE,$c->internal_get_state($cm,123,$current));

        // Note: This function is not fully tested (including kind of the main
        // part) because:
        // * the grade_item/grade_grade calls are static and can't be mocked
        // * the plugin_supports call is static and can't be mocked

        $DB->tally();
        $c->tally();
    }

    function test_set_module_viewed() {
        $c=new completion_cutdown();
        $c->__construct((object)array('id'=>42));
        $cm=(object)array('id'=>13,'course'=>42);

        // Not tracking completion, should do nothing
        $cm->completionview=COMPLETION_VIEW_NOT_REQUIRED;
        $c->set_module_viewed($cm);

        // Tracking completion but completion is disabled, should do nothing
        $cm->completionview=COMPLETION_VIEW_REQUIRED;
        $c->expectAt(0,'is_enabled',array($cm));
        $c->setReturnValueAt(0,'is_enabled',false);
        $c->set_module_viewed($cm);

        // Now it's enabled, we expect it to get data. If data already has
        // viewed, still do nothing
        $c->expectAt(1,'is_enabled',array($cm));
        $c->setReturnValueAt(1,'is_enabled',true);
        $c->expectAt(0,'get_data',array($cm,0));
        $hasviewed=(object)array('viewed'=>COMPLETION_VIEWED);
        $c->setReturnValueAt(0,'get_data',$hasviewed);
        $c->set_module_viewed($cm);

        // OK finally one that hasn't been viewed, now it should set it viewed
        // and update state
        $c->expectAt(2,'is_enabled',array($cm));
        $c->setReturnValueAt(2,'is_enabled',true);
        $notviewed=(object)array('viewed'=>COMPLETION_NOT_VIEWED);
        $c->expectAt(1,'get_data',array($cm,1337));
        $c->setReturnValueAt(1,'get_data',$notviewed);
        $c->expectOnce('internal_set_data',array($cm,$hasviewed));
        $c->expectOnce('update_state',array($cm,COMPLETION_COMPLETE,1337));
        $c->set_module_viewed($cm,1337);

        $c->tally();
    }

    function test_count_user_data() {
        global $DB;
        $course=(object)array('id'=>13);
        $cm=(object)array('id'=>42);
        $DB->setReturnValue('get_field_sql',666);
        $DB->expectOnce('get_field_sql',array(new IgnoreWhitespaceExpectation("SELECT
    COUNT(1)
FROM
    {course_modules_completion}
WHERE
    coursemoduleid=? AND completionstate<>0"),array(42)));
        $c=new completion_info($course);
        $this->assertEqual(666,$c->count_user_data($cm));

        $DB->tally();
    }

    function test_delete_all_state() {
        global $DB,$SESSION;
        $course=(object)array('id'=>13);
        $cm=(object)array('id'=>42,'course'=>13);
        $c=new completion_info($course);
        // Check it works ok without data in session
        $DB->expectAt(0,'delete_records',
            array('course_modules_completion',array('coursemoduleid'=>42)));
        $c->delete_all_state($cm);

        // Build up a session to check it deletes the right bits from it
        // (and not other bits)
        $SESSION->completioncache=array();
        $SESSION->completioncache[13]=array();
        $SESSION->completioncache[13][42]='foo';
        $SESSION->completioncache[13][43]='foo';
        $SESSION->completioncache[14]=array();
        $SESSION->completioncache[14][42]='foo';
        $DB->expectAt(1,'delete_records',
            array('course_modules_completion',array('coursemoduleid'=>42)));
        $c->delete_all_state($cm);
        $this->assertEqual(array(13=>array(43=>'foo'),14=>array(42=>'foo')),
            $SESSION->completioncache);

        $DB->tally();
    }

    function test_reset_all_state() {
        global $DB;
        $c=new completion_cutdown();
        $c->__construct((object)array('id'=>42));

        $cm=(object)array('id'=>13,'course'=>42,
            'completion'=>COMPLETION_TRACKING_AUTOMATIC);

        $DB->setReturnValue('get_recordset',new fake_recordset(array(
            (object)array('id'=>1,'userid'=>100),
            (object)array('id'=>2,'userid'=>101),
        )));
        $DB->expectOnce('get_recordset',array('course_modules_completion',
            array('coursemoduleid'=>13),'','userid'));
        $c->expectOnce('delete_all_state',array($cm));
        $c->expectOnce('get_tracked_users',array());
        $c->setReturnValue('get_tracked_users',array(
            (object)array('id'=>100,'firstname'=>'Woot','lastname'=>'Plugh'),
            (object)array('id'=>201,'firstname'=>'Vroom','lastname'=>'Xyzzy'),
            ));

        $c->expectAt(0,'update_state',array($cm,COMPLETION_UNKNOWN,100));
        $c->expectAt(1,'update_state',array($cm,COMPLETION_UNKNOWN,101));
        $c->expectAt(2,'update_state',array($cm,COMPLETION_UNKNOWN,201));

        $c->reset_all_state($cm);

        $DB->tally();
        $c->tally();
    }

    function test_get_data() {
        global $DB,$SESSION;

        $c=new completion_info((object)array('id'=>42));
        $cm=(object)array('id'=>13,'course'=>42);

        // 1. Not current user, record exists
        $sillyrecord=(object)array('frog'=>'kermit');
        $DB->expectAt(0,'get_record',array('course_modules_completion',
            array('coursemoduleid'=>13,'userid'=>123)));
        $DB->setReturnValueAt(0,'get_record',$sillyrecord);
        $result=$c->get_data($cm,false,123);
        $this->assertEqual($sillyrecord,$result);
        $this->assertTrue(empty($SESSION->completioncache));

        // 2. Not current user, default record, wholecourse (ignored)
        $DB->expectAt(1,'get_record',array('course_modules_completion',
            array('coursemoduleid'=>13,'userid'=>123)));
        $DB->setReturnValueAt(1,'get_record',false);
        $result=$c->get_data($cm,true,123);
        $this->assertEqual((object)array(
            'id'=>'0','coursemoduleid'=>13,'userid'=>123,'completionstate'=>0,
            'viewed'=>0,'timemodified'=>0),$result);
        $this->assertTrue(empty($SESSION->completioncache));

        // 3. Current user, single record, not from cache
        $DB->expectAt(2,'get_record',array('course_modules_completion',
            array('coursemoduleid'=>13,'userid'=>314159)));
        $DB->setReturnValueAt(2,'get_record',$sillyrecord);
        $result=$c->get_data($cm);
        $this->assertEqual($sillyrecord,$result);
        $this->assertEqual($sillyrecord,$SESSION->completioncache[42][13]);
        // When checking time(), allow for second overlaps
        $this->assertTrue(time()-$SESSION->completioncache[42]['updated']<2);

        // 4. Current user, 'whole course', but from cache
        $result=$c->get_data($cm,true);
        $this->assertEqual($sillyrecord,$result);

        // 5. Current user, single record, cache expired
        $SESSION->completioncache[42]['updated']=37; // Quite a long time ago
        $now=time();
        $SESSION->completioncache[17]['updated']=$now;
        $SESSION->completioncache[39]['updated']=72; // Also a long time ago
        $DB->expectAt(3,'get_record',array('course_modules_completion',
            array('coursemoduleid'=>13,'userid'=>314159)));
        $DB->setReturnValueAt(3,'get_record',$sillyrecord);
        $result=$c->get_data($cm,false);
        $this->assertEqual($sillyrecord,$result);
        // Check that updated value is right, then fudge it to make next compare
        // work
        $this->assertTrue(time()-$SESSION->completioncache[42]['updated']<2);
        $SESSION->completioncache[42]['updated']=$now;
        // Check things got expired from cache
        $this->assertEqual(array(42=>array(13=>$sillyrecord,'updated'=>$now),
            17=>array('updated'=>$now)),$SESSION->completioncache);

        // 6. Current user, 'whole course' and record not in cache
        unset($SESSION->completioncache);

        // Scenario: Completion data exists for one CMid
        $basicrecord=(object)array('coursemoduleid'=>13);
        $DB->setReturnValueAt(0,'get_records_sql',array(
            1=>$basicrecord
        ));
        $DB->expectAt(0,'get_records_sql',array(new IgnoreWhitespaceExpectation("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cmc.coursemoduleid=cm.id
WHERE
    cm.course=? AND cmc.userid=?"),array(42,314159)));

        // There are two CMids in total, the one we had data for and another one
        $modinfo->cms=array((object)array('id'=>13),(object)array('id'=>14));
        $result=$c->get_data($cm,true,0,$modinfo);

        // Check result
        $this->assertEqual($basicrecord,$result);

        // Check the cache contents
        $this->assertTrue(time()-$SESSION->completioncache[42]['updated']<2);
        $SESSION->completioncache[42]['updated']=$now;
        $this->assertEqual(array(42=>array(13=>$basicrecord,14=>(object)array(
            'id'=>'0','coursemoduleid'=>14,'userid'=>314159,'completionstate'=>0,
            'viewed'=>0,'timemodified'=>0),'updated'=>$now)),$SESSION->completioncache);

        $DB->tally();
    }

    function test_internal_set_data() {
        global $DB,$SESSION;

        $cm = (object)array('course' => 42,'id' => 13);
        $c = new completion_info((object)array('id' => 42));

        // 1) Test with new data
        $data = (object)array('id'=>0, 'userid' => 314159, 'coursemoduleid' => 99);
        $DB->setReturnValueAt(0, 'start_delegated_transaction', new mock_transaction());
        $DB->setReturnValueAt(0, 'insert_record', 4);
        $DB->expectAt(0, 'get_field', array('course_modules_completion', 'id',
                array('coursemoduleid' => 99, 'userid' => 314159)));
        $DB->expectAt(0, 'insert_record', array('course_modules_completion', $data));
        $c->internal_set_data($cm, $data);
        $this->assertEqual(4, $data->id);
        $this->assertEqual(array(42 => array(13 => $data)), $SESSION->completioncache);

        // 2) Test with existing data and for different user (not cached)
        unset($SESSION->completioncache);
        $d2 = (object)array('id' => 7, 'userid' => 17, 'coursemoduleid' => 66);
        $DB->setReturnValueAt(1, 'start_delegated_transaction', new mock_transaction());
        $DB->expectAt(0,'update_record', array('course_modules_completion', $d2));
        $c->internal_set_data($cm, $d2);
        $this->assertFalse(isset($SESSION->completioncache));

        // 3) Test where it THINKS the data is new (from cache) but actually
        // in the database it has been set since
        // 1) Test with new data
        $data = (object)array('id'=>0, 'userid' => 314159, 'coursemoduleid' => 99);
        $DB->setReturnValueAt(2, 'start_delegated_transaction', new mock_transaction());
        $DB->setReturnValueAt(1, 'get_field', 13);
        $DB->expectAt(1, 'get_field', array('course_modules_completion', 'id',
                array('coursemoduleid' => 99, 'userid' => 314159)));
        $d3 = (object)array('id' => 13, 'userid' => 314159, 'coursemoduleid' => 99);
        $DB->expectAt(1,'update_record', array('course_modules_completion', $d3));
        $c->internal_set_data($cm, $data);

        $DB->tally();
    }

    function test_get_activities() {
        global $DB;

        $c=new completion_info((object)array('id'=>42));

        // Try with no activities
        $DB->expectAt(0,'get_records_select',array('course_modules',
              'course=42 AND completion<>'.COMPLETION_TRACKING_NONE));
        $DB->setReturnValueAt(0,'get_records_select',array());
        $result=$c->get_activities();
        $this->assertEqual(array(),$result);

        // Try with an activity (need to fake up modinfo for it as well)
        $DB->expectAt(1,'get_records_select',array('course_modules',
              'course=42 AND completion<>'.COMPLETION_TRACKING_NONE));
        $DB->setReturnValueAt(1,'get_records_select',array(
            13=>(object)array('id'=>13)
        ));
        $modinfo=new stdClass;
        $modinfo->sections=array(array(1,2,3),array(12,13,14));
        $modinfo->cms[13]=(object)array('modname'=>'frog','name'=>'kermit');
        $result=$c->get_activities($modinfo);
        $this->assertEqual(array(13=>(object)array('id'=>13,'modname'=>'frog','name'=>'kermit')),$result);

        $DB->tally();
    }

    // get_tracked_users() cannot easily be tested because it uses
    // get_role_users, so skipping that

    function test_get_progress_all() {
        global $DB;

        $c=new completion_cutdown();
        $c->__construct((object)array('id'=>42));

        // 1) Basic usage
        $c->expectAt(0,'get_tracked_users',array(false, array(), 0, '', '', ''));
        $c->setReturnValueAt(0,'get_tracked_users',array(
            (object)array('id'=>100,'firstname'=>'Woot','lastname'=>'Plugh'),
            (object)array('id'=>201,'firstname'=>'Vroom','lastname'=>'Xyzzy'),
            ));
        $DB->expectAt(0,'get_in_or_equal',array(array(100,201)));
        $DB->setReturnValueAt(0,'get_in_or_equal',array(' IN (100,201)',array()));
        $DB->expectAt(0,'get_recordset_sql',array(new IgnoreWhitespaceExpectation("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cm.id=cmc.coursemoduleid
WHERE
    cm.course=? AND cmc.userid IN (100,201)"),array(42)));
        $progress1=(object)array('userid'=>100,'coursemoduleid'=>13);
        $progress2=(object)array('userid'=>201,'coursemoduleid'=>14);
        $DB->setReturnValueAt(0,'get_recordset_sql',new fake_recordset(array(
            $progress1,$progress2
        )));
        $this->assertEqual(array(

                100 => (object)array('id'=>100,'firstname'=>'Woot','lastname'=>'Plugh',
                    'progress'=>array(13=>$progress1)),
                201 => (object)array('id'=>201,'firstname'=>'Vroom','lastname'=>'Xyzzy',
                    'progress'=>array(14=>$progress2)),
            ),$c->get_progress_all(false));

        // 2) With more than 1,000 results
        $c->expectAt(1,'get_tracked_users',array(true, 3, 0, '', '', ''));

        $tracked=array();
        $ids=array();
        $progress=array();
        for($i=100;$i<2000;$i++) {
            $tracked[]=(object)array('id'=>$i,'firstname'=>'frog','lastname'=>$i);
            $ids[]=$i;
            $progress[]=(object)array('userid'=>$i,'coursemoduleid'=>13);
            $progress[]=(object)array('userid'=>$i,'coursemoduleid'=>14);
        }
        $c->setReturnValueAt(1,'get_tracked_users',$tracked);

        $DB->expectAt(1,'get_in_or_equal',array(array_slice($ids,0,1000)));
        $DB->setReturnValueAt(1,'get_in_or_equal',array(' IN whatever',array()));
        $DB->expectAt(1,'get_recordset_sql',array(new IgnoreWhitespaceExpectation("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cm.id=cmc.coursemoduleid
WHERE
    cm.course=? AND cmc.userid IN whatever"),array(42)));
        $DB->setReturnValueAt(1,'get_recordset_sql',new fake_recordset(array_slice($progress,0,1000)));
        $DB->expectAt(2,'get_in_or_equal',array(array_slice($ids,1000)));
        $DB->setReturnValueAt(2,'get_in_or_equal',array(' IN whatever2',array()));
        $DB->expectAt(2,'get_recordset_sql',array(new IgnoreWhitespaceExpectation("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cm.id=cmc.coursemoduleid
WHERE
    cm.course=? AND cmc.userid IN whatever2"),array(42)));
        $DB->setReturnValueAt(2,'get_recordset_sql',new fake_recordset(array_slice($progress,1000)));
        $result=$c->get_progress_all(true,3);
        $resultok=true;
        $resultok = $resultok && ($ids==array_keys($result));

        foreach($result as $userid => $data) {
            $resultok = $resultok && $data->firstname=='frog';
            $resultok = $resultok && $data->lastname==$userid;
            $resultok = $resultok && $data->id==$userid;
            $cms=$data->progress;
            $resultok= $resultok && (array(13,14)==array_keys($cms));
            $resultok= $resultok && ((object)array('userid'=>$userid,'coursemoduleid'=>13)==$cms[13]);
            $resultok= $resultok && ((object)array('userid'=>$userid,'coursemoduleid'=>14)==$cms[14]);
        }
        $this->assertTrue($resultok);

        $DB->tally();
        $c->tally();
    }

    function test_inform_grade_changed() {
        $c=new completion_cutdown();
        $c->__construct((object)array('id'=>42));

        $cm=(object)array('course'=>42,'id'=>13,'completion'=>0,'completiongradeitemnumber'=>null);
        $item=(object)array('itemnumber'=>3);
        $grade=(object)array('userid'=>31337);

        // Not enabled (should do nothing)
        $c->setReturnValueAt(0,'is_enabled',false);
        $c->expectAt(0,'is_enabled',array($cm));
        $c->inform_grade_changed($cm,$item,$grade,false);

        // Enabled but still no grade completion required, should still do nothing
        $c->setReturnValueAt(1,'is_enabled',true);
        $c->expectAt(1,'is_enabled',array($cm));
        $c->inform_grade_changed($cm,$item,$grade,false);

        // Enabled and completion required but item number is wrong, does nothing
        $cm->completiongradeitemnumber=7;
        $c->setReturnValueAt(2,'is_enabled',true);
        $c->expectAt(2,'is_enabled',array($cm));
        $c->inform_grade_changed($cm,$item,$grade,false);

        // Enabled and completion required and item number right. It is supposed
        // to call update_state with the new potential state being obtained from
        // internal_get_grade_state.
        $cm->completiongradeitemnumber=3;
        $c->setReturnValueAt(3,'is_enabled',true);
        $c->expectAt(3,'is_enabled',array($cm));
        $c->expectAt(0,'internal_get_grade_state',array($item,$grade));
        $c->setReturnValueAt(0,'internal_get_grade_state',COMPLETION_COMPLETE_PASS);
        $c->expectAt(0,'update_state',array($cm,COMPLETION_COMPLETE_PASS,31337));
        $c->inform_grade_changed($cm,$item,$grade,false);

        // Same as above but marked deleted. It is supposed to call update_state
        // with new potential state being COMPLETION_INCOMPLETE
        $c->setReturnValueAt(4,'is_enabled',false);
        $c->expectAt(4,'is_enabled',array($cm));
        $c->expectAt(1,'update_state',array($cm,COMPLETION_INCOMPLETE,31337));
        $c->inform_grade_changed($cm,$item,$grade,false);

        $c->tally();
    }

    function test_internal_get_grade_state() {
        $item=new stdClass;
        $grade=new stdClass;

        $item->gradepass=4;
        $item->hidden=0;
        $grade->rawgrade=4.0;
        $grade->finalgrade=null;

        // Grade has pass mark and is not hidden, user passes
        $this->assertEqual(
            COMPLETION_COMPLETE_PASS,
            completion_info::internal_get_grade_state($item,$grade));

        // Same but user fails
        $grade->rawgrade=3.9;
        $this->assertEqual(
            COMPLETION_COMPLETE_FAIL,
            completion_info::internal_get_grade_state($item,$grade));

        // User fails on raw grade but passes on final
        $grade->finalgrade=4.0;
        $this->assertEqual(
            COMPLETION_COMPLETE_PASS,
            completion_info::internal_get_grade_state($item,$grade));

        // Item is hidden
        $item->hidden=1;
        $this->assertEqual(
            COMPLETION_COMPLETE,
            completion_info::internal_get_grade_state($item,$grade));

        // Item isn't hidden but has no pass mark
        $item->hidden=0;
        $item->gradepass=0;
        $this->assertEqual(
            COMPLETION_COMPLETE,
            completion_info::internal_get_grade_state($item,$grade));
    }
}

