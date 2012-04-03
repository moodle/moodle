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
 * Tests for conditional activities
 *
 * @package    core
 * @category   phpunit
 * @copyright  &copy; 2008 The Open University
 * @author     Sam Marshall
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/conditionlib.php');


class conditionlib_testcase extends advanced_testcase {
    protected function setUp() {
        global $CFG;
        parent::setUp();

        $this->resetAfterTest(true);

        $CFG->enableavailability = 1;
        $CFG->enablecompletion = 1;
    }

    function test_constructor() {
        global $DB,$CFG;
        $cm=new stdClass;

        // Test records
        $id=$DB->insert_record('course_modules',(object)array(
            'showavailability'=>1,'availablefrom'=>17,'availableuntil'=>398,'course'=>64));

        // no ID
        try {
            $test=new condition_info($cm);
            $this->fail();
        } catch(coding_exception $e) {
        }

        // no other data
        $cm->id=$id;
        $test=new condition_info($cm,CONDITION_MISSING_EVERYTHING);
        $this->assertEquals(
            (object)array('id'=>$id,'showavailability'=>1,
                'availablefrom'=>17,'availableuntil'=>398,'course'=>64,
                'conditionsgrade'=>array(), 'conditionscompletion'=>array()),
            $test->get_full_course_module());

        // just the course_modules stuff; check it doesn't request that from db
        $cm->showavailability=0;
        $cm->availablefrom=2;
        $cm->availableuntil=74;
        $cm->course=38;
        $test=new condition_info($cm,CONDITION_MISSING_EXTRATABLE);
        $this->assertEquals(
            (object)array('id'=>$id,'showavailability'=>0,
                'availablefrom'=>2,'availableuntil'=>74,'course'=>38,
                'conditionsgrade'=>array(), 'conditionscompletion'=>array()),
            $test->get_full_course_module());

        // Now let's add some actual grade/completion conditions
        $DB->insert_record('course_modules_availability',(object)array(
            'coursemoduleid'=>$id,
            'sourcecmid'=>42,
            'requiredcompletion'=>2
        ));
        $DB->insert_record('course_modules_availability',(object)array(
            'coursemoduleid'=>$id,
            'sourcecmid'=>666,
            'requiredcompletion'=>1
        ));
        $DB->insert_record('course_modules_availability',(object)array(
            'coursemoduleid'=>$id,
            'gradeitemid'=>37,
            'grademin'=>5.5
        ));

        $cm=(object)array('id'=>$id);
        $test=new condition_info($cm,CONDITION_MISSING_EVERYTHING);
        $fullcm=$test->get_full_course_module();
        $this->assertEquals(array(42=>2,666=>1),$fullcm->conditionscompletion);
        $this->assertEquals(array(37=>(object)array('min'=>5.5,'max'=>null,'name'=>'!missing')),
            $fullcm->conditionsgrade);
    }

    private function make_course() {
        global $DB;
        $categoryid=$DB->insert_record('course_categories',(object)array('name'=>'conditionlibtest'));
        return $DB->insert_record('course',(object)array(
            'fullname'=>'Condition test','shortname'=>'CT1',
            'category'=>$categoryid,'enablecompletion'=>1));
    }

    private function make_course_module($courseid,$params=array()) {
        global $DB;
        static $moduleid=0;
        if(!$moduleid) {
            $moduleid=$DB->get_field('modules','id',array('name'=>'resource'));
        }

        $rid=$DB->insert_record('resource',(object)array('course'=>$courseid,
            'name'=>'xxx','alltext'=>'','popup'=>''));
        $settings=(object)array(
            'course'=>$courseid,'module'=>$moduleid,'instance'=>$rid);
        foreach($params as $name=>$value) {
            $settings->{$name}=$value;
        }
        return $DB->insert_record('course_modules',$settings);
    }

    private function make_section($courseid,$cmids,$sectionnum=0) {
        global $DB;
        $DB->insert_record('course_sections',(object)array(
            'course'=>$courseid,'sequence'=>implode(',',$cmids),'section'=>$sectionnum));
    }

    function test_modinfo() {
        global $DB;

        // Let's make a course
        $courseid=$this->make_course();

        // Now let's make a couple modules on that course
        $cmid1=$this->make_course_module($courseid,array(
            'showavailability'=>1,'availablefrom'=>17,'availableuntil'=>398,
            'completion'=>COMPLETION_TRACKING_MANUAL));
        $cmid2=$this->make_course_module($courseid,array(
            'showavailability'=>0,'availablefrom'=>0,'availableuntil'=>0));
        $this->make_section($courseid,array($cmid1,$cmid2));

        // Add a fake grade item
        $gradeitemid=$DB->insert_record('grade_items',(object)array(
            'courseid'=>$courseid,'itemname'=>'frog'));

        // One of the modules has grade and completion conditions, other doesn't
        $DB->insert_record('course_modules_availability',(object)array(
            'coursemoduleid'=>$cmid2,
            'sourcecmid'=>$cmid1,
            'requiredcompletion'=>1
        ));
        $DB->insert_record('course_modules_availability',(object)array(
            'coursemoduleid'=>$cmid2,
            'gradeitemid'=>$gradeitemid,
            'grademin'=>5.5
        ));

        // Okay sweet, now get modinfo
        $course = $DB->get_record('course',array('id'=>$courseid));
        $modinfo=get_fast_modinfo($course);

        // Test basic data
        $this->assertEquals(1,$modinfo->cms[$cmid1]->showavailability);
        $this->assertEquals(17,$modinfo->cms[$cmid1]->availablefrom);
        $this->assertEquals(398,$modinfo->cms[$cmid1]->availableuntil);
        $this->assertEquals(0,$modinfo->cms[$cmid2]->showavailability);
        $this->assertEquals(0,$modinfo->cms[$cmid2]->availablefrom);
        $this->assertEquals(0,$modinfo->cms[$cmid2]->availableuntil);

        // Test condition arrays
        $this->assertEquals(array(),$modinfo->cms[$cmid1]->conditionscompletion);
        $this->assertEquals(array(),$modinfo->cms[$cmid1]->conditionsgrade);
        $this->assertEquals(array($cmid1=>1),
            $modinfo->cms[$cmid2]->conditionscompletion);
        $this->assertEquals(array($gradeitemid=>(object)array('min'=>5.5,'max'=>null,'name'=>'frog')),
            $modinfo->cms[$cmid2]->conditionsgrade);
    }

    function test_add_and_remove() {
        global $DB;
        // Make course and module
        $courseid=$this->make_course();
        $cmid=$this->make_course_module($courseid,array(
            'showavailability'=>0,'availablefrom'=>0,'availableuntil'=>0));
        $this->make_section($courseid,array($cmid));

        // Check it has no conditions
        $test1=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $cm=$test1->get_full_course_module();
        $this->assertEquals(array(),$cm->conditionscompletion);
        $this->assertEquals(array(),$cm->conditionsgrade);

        // Add conditions of each type
        $test1->add_completion_condition(13,3);
        $this->assertEquals(array(13=>3),$cm->conditionscompletion);
        $test1->add_grade_condition(666,0.4,null,true);
        $this->assertEquals(array(666=>(object)array('min'=>0.4,'max'=>null,'name'=>'!missing')),
            $cm->conditionsgrade);

        // Check they were really added in db
        $test2=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $cm=$test2->get_full_course_module();
        $this->assertEquals(array(13=>3),$cm->conditionscompletion);
        $this->assertEquals(array(666=>(object)array('min'=>0.4,'max'=>null,'name'=>'!missing')),
            $cm->conditionsgrade);

        // Wipe conditions
        $test2->wipe_conditions();
        $this->assertEquals(array(),$cm->conditionscompletion);
        $this->assertEquals(array(),$cm->conditionsgrade);

        // Check they were really wiped
        $test3=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $cm=$test3->get_full_course_module();
        $this->assertEquals(array(),$cm->conditionscompletion);
        $this->assertEquals(array(),$cm->conditionsgrade);
    }

    function test_is_available() {
        global $DB,$USER;
        $courseid=$this->make_course();

        // No conditions
        $cmid=$this->make_course_module($courseid);
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertTrue($ci->is_available($text,false,0));
        $this->assertEquals('',$text);

        // Time (from)
        $time=time()+100;
        $cmid=$this->make_course_module($courseid,array('availablefrom'=>$time));
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertFalse($ci->is_available($text));
        $this->assertRegExp('/'.preg_quote(userdate($time,get_string('strftimedate','langconfig'))).'/',$text);

        $time=time()-100;
        $cmid=$this->make_course_module($courseid,array('availablefrom'=>$time));
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertTrue($ci->is_available($text));
        $this->assertEquals('',$text);
        $this->assertRegExp('/'.preg_quote(userdate($time,get_string('strftimedate','langconfig'))).'/',$ci->get_full_information());

        // Time (until)
        $cmid=$this->make_course_module($courseid,array('availableuntil'=>time()-100));
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals('',$text);

        // Completion
        $oldid=$cmid;
        $cmid=$this->make_course_module($courseid);
        $this->make_section($courseid,array($oldid,$cmid));
        $oldcm=$DB->get_record('course_modules',array('id'=>$oldid));
        $oldcm->completion=COMPLETION_TRACKING_MANUAL;
        $DB->update_record('course_modules',$oldcm);

        // Need to reset modinfo after changing the options
        rebuild_course_cache($courseid);
        $reset = 'reset';
        get_fast_modinfo($reset);

        $ci=new condition_info((object)array('id'=>$cmid),CONDITION_MISSING_EVERYTHING);
        $ci->add_completion_condition($oldid,COMPLETION_COMPLETE);
        condition_info::wipe_session_cache();

        $this->assertFalse($ci->is_available($text,false));
        $this->assertEquals(get_string('requires_completion_1','condition','xxx'),$text);
        completion_info::wipe_session_cache();
        $completion=new completion_info($DB->get_record('course',array('id'=>$courseid)));
        $completion->update_state($oldcm,COMPLETION_COMPLETE);
        completion_info::wipe_session_cache();
        condition_info::wipe_session_cache();

        $this->assertTrue($ci->is_available($text));
        $this->assertFalse($ci->is_available($text,false,$USER->id+1));
        completion_info::wipe_session_cache();
        condition_info::wipe_session_cache();
        $completion=new completion_info($DB->get_record('course',array('id'=>$courseid)));
        $completion->update_state($oldcm,COMPLETION_INCOMPLETE);
        $this->assertFalse($ci->is_available($text));

        $ci->wipe_conditions();
        $ci->add_completion_condition($oldid,COMPLETION_INCOMPLETE);
        condition_info::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));
        $this->assertTrue($ci->is_available($text,false,$USER->id+1));

        condition_info::wipe_session_cache();
        $this->assertTrue($ci->is_available($text,true));

        // Grade
        $ci->wipe_conditions();
        // Add a fake grade item
        $gradeitemid=$DB->insert_record('grade_items',(object)array(
            'courseid'=>$courseid,'itemname'=>'frog'));
        // Add a condition on a value existing...
        $ci->add_grade_condition($gradeitemid,null,null,true);
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_any','condition','frog'),$text);

        // Fake it existing
        $DB->insert_record('grade_grades',(object)array(
            'itemid'=>$gradeitemid,'userid'=>$USER->id,'finalgrade'=>3.78));
        condition_info::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));

        condition_info::wipe_session_cache();
        $this->assertTrue($ci->is_available($text,true));

        // Now require that user gets more than 3.78001
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid,3.78001,null,true);
        condition_info::wipe_session_cache();
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_min','condition','frog'),$text);

        // ...just on 3.78...
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid,3.78,null,true);
        condition_info::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));

        // ...less than 3.78
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid,null,3.78,true);
        condition_info::wipe_session_cache();
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_max','condition','frog'),$text);

        // ...less than 3.78001
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid,null,3.78001,true);
        condition_info::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));

        // ...in a range that includes it
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid,3,4,true);
        condition_info::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));

        // ...in a range that doesn't include it
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid,4,5,true);
        condition_info::wipe_session_cache();
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_range','condition','frog'),$text);
    }

}

