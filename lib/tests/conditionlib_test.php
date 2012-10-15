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
    private $oldcfg, $olduser;

    protected function setUp() {
        global $CFG, $USER, $DB;
        parent::setUp();

        $this->resetAfterTest(true);

        $CFG->enableavailability = 1;
        $CFG->enablecompletion = 1;
        $user = $this->getDataGenerator()->create_user();;
        $this->setUser($user);
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
                'conditionsgrade'=>array(), 'conditionscompletion'=>array(),
                'visible' => 1, 'conditionsfield' => array()),
            $test->get_full_course_module());

        // just the course_modules stuff; check it doesn't request that from db
        $cm->showavailability=0;
        $cm->availablefrom=2;
        $cm->availableuntil=74;
        $cm->course=38;
        $cm->visible = 1;
        $test=new condition_info($cm,CONDITION_MISSING_EXTRATABLE);
        $this->assertEquals(
            (object)array('id'=>$id,'showavailability'=>0,
                'availablefrom'=>2,'availableuntil'=>74,'course'=>38,
                'conditionsgrade' => array(), 'conditionscompletion' => array(),
                'visible' => 1, 'conditionsfield' => array()),
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

    /**
     * Same as above test but for course_sections instead of course_modules.
     */
    public function test_section_constructor() {
        global $DB, $CFG;

        // Test records
        $id = $DB->insert_record('course_sections', (object)array(
                'showavailability' => 1, 'availablefrom' => 17,
                'availableuntil' => 398, 'course' => 64, 'groupingid' => 13));

        // No ID
        $section = new stdClass;
        try {
            $test = new condition_info_section($section);
            $this->fail();
        } catch (coding_exception $e) {
            // Do nothing
        }

        // No other data
        $section->id = $id;
        $test = new condition_info_section($section, CONDITION_MISSING_EVERYTHING);
        $this->assertEquals(
                (object)array('id' => $id, 'showavailability' => 1, 'groupingid' => 13,
                    'availablefrom' => 17, 'availableuntil' => 398, 'course' => 64,
                    'conditionsgrade' => array(), 'conditionscompletion' => array(),
                    'visible' => 1, 'conditionsfield' => array()),
                $test->get_full_section());

        // Just the course_sections stuff; check it doesn't request that from db
        // (by using fake values and ensuring it still has those)
        $section->showavailability = 0;
        $section->availablefrom = 2;
        $section->availableuntil = 74;
        $section->course = 38;
        $section->groupingid = 99;
        $section->visible = 1;
        $test = new condition_info_section($section, CONDITION_MISSING_EXTRATABLE);
        $this->assertEquals(
                (object)array('id' => $id, 'showavailability' => 0, 'groupingid' => 99,
                    'availablefrom' => 2, 'availableuntil' => 74, 'course' => 38,
                    'conditionsgrade' => array(), 'conditionscompletion' => array(),
                    'visible' => 1, 'conditionsfield' => array()),
                $test->get_full_section());

        // Now let's add some actual grade/completion conditions
        $DB->insert_record('course_sections_availability', (object)array(
                'coursesectionid' => $id,
                'sourcecmid' => 42,
                'requiredcompletion' => 2
        ));
        $DB->insert_record('course_sections_availability', (object)array(
                'coursesectionid' => $id,
                'sourcecmid' => 666,
                'requiredcompletion' => 1
        ));
        $DB->insert_record('course_sections_availability', (object)array(
                'coursesectionid' => $id,
                'gradeitemid' => 37,
                'grademin' => 5.5
        ));

        $section = (object)array('id' => $id);
        $test = new condition_info_section($section, CONDITION_MISSING_EVERYTHING);
        $fullsection = $test->get_full_section();
        $this->assertEquals(array(42 => 2, 666 => 1), $fullsection->conditionscompletion);
        $this->assertEquals(array(37 => (object)array('min' => 5.5, 'max' => null, 'name' => '!missing')),
                $fullsection->conditionsgrade);
    }

    private function make_course() {
        global $DB;
        $categoryid = $DB->insert_record('course_categories', (object)array('name'=>'conditionlibtest'));
        $courseid = $DB->insert_record('course', (object)array(
            'fullname'=>'Condition test','shortname'=>'CT1',
            'category'=>$categoryid,'enablecompletion'=>1));
        context_course::instance($courseid);
        return $courseid;
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
        $cmid = $DB->insert_record('course_modules', $settings);
        rebuild_course_cache($courseid, true);
        return $cmid;
    }

    private function make_section($courseid, $cmids, $sectionnum=0, $params=array()) {
        global $DB;
        $record = (object)array(
            'course' => $courseid,
            'sequence' => implode(',', $cmids),
            'section' => $sectionnum);
        foreach ($params as $name => $value) {
            $record->{$name} = $value;
        }
        $sectionid = $DB->insert_record('course_sections', $record);
        rebuild_course_cache($courseid, true);
        return $sectionid;
    }

    private function make_grouping($courseid, $name) {
        global $CFG;
        require_once($CFG->dirroot . '/group/lib.php');
        return groups_create_grouping((object)array('courseid' => $courseid,
                'name' => $name));
    }

    private function make_group($courseid, $name, $groupingid=0) {
        global $CFG;
        require_once($CFG->dirroot . '/group/lib.php');
        $groupid = groups_create_group((object)array('courseid' => $courseid,
                'name' => $name));
        if ($groupingid) {
            groups_assign_grouping($groupingid, $groupid);
        }
        return $groupid;
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
        $modinfo=get_fast_modinfo($courseid);

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

    public function test_section_modinfo() {
        global $DB;

        // Let's make a course
        $courseid = $this->make_course();

        // Now let's make a couple sections on that course, one of which has a cm
        $cmid = $this->make_course_module($courseid);
        $sectionid1 = $this->make_section($courseid, array($cmid), 1, array(
                'showavailability' => 1, 'availablefrom' => 17,
                'availableuntil' => 398, 'groupingid' => 13));
        $sectionid2 = $this->make_section($courseid, array(), 2);

        // Add a fake grade item
        $gradeitemid = $DB->insert_record('grade_items', (object)array(
                'courseid' => $courseid, 'itemname' => 'frog'));

        // One of the sections has grade and completion conditions, other doesn't
        $DB->insert_record('course_sections_availability', (object)array(
            'coursesectionid' => $sectionid2,
            'sourcecmid' => $cmid,
            'requiredcompletion'=>1
        ));
        $DB->insert_record('course_sections_availability', (object)array(
            'coursesectionid' => $sectionid2,
            'gradeitemid' => $gradeitemid,
            'grademin' => 5.5
        ));

        rebuild_course_cache($courseid, true);
        // Okay sweet, now get modinfo
        $modinfo = get_fast_modinfo($courseid);

        // Test basic data
        $section1 = $modinfo->get_section_info(1);
        $this->assertEquals(1, $section1->showavailability);
        $this->assertEquals(17, $section1->availablefrom);
        $this->assertEquals(398, $section1->availableuntil);
        $this->assertEquals(13, $section1->groupingid);
        $section2 = $modinfo->get_section_info(2);
        $this->assertEquals(0, $section2->showavailability);
        $this->assertEquals(0, $section2->availablefrom);
        $this->assertEquals(0, $section2->availableuntil);
        $this->assertEquals(0, $section2->groupingid);

        // Test condition arrays
        $this->assertEquals(array(), $section1->conditionscompletion);
        $this->assertEquals(array(), $section1->conditionsgrade);
        $this->assertEquals(array($cmid => 1),
                $section2->conditionscompletion);
        $this->assertEquals(array($gradeitemid => (object)array('min' => 5.5, 'max' => null, 'name' => 'frog')),
                $section2->conditionsgrade);
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

    public function test_section_add_and_remove() {
        global $DB;

        // Make course and module
        $courseid = $this->make_course();
        $cmid = $this->make_course_module($courseid);
        $sectionid = $this->make_section($courseid, array($cmid));

        // Check it has no conditions
        $test1 = new condition_info_section((object)array('id'=>$sectionid),
                CONDITION_MISSING_EVERYTHING);
        $section = $test1->get_full_section();
        $this->assertEquals(array(), $section->conditionscompletion);
        $this->assertEquals(array(), $section->conditionsgrade);

        // Add conditions of each type
        $test1->add_completion_condition(13, 3);
        $this->assertEquals(array(13 => 3), $section->conditionscompletion);
        $test1->add_grade_condition(666, 0.4, null, true);
        $this->assertEquals(array(666 => (object)array('min' => 0.4, 'max' => null, 'name' => '!missing')),
                $section->conditionsgrade);

        // Check they were really added in db
        $test2 = new condition_info_section((object)array('id' => $sectionid),
                CONDITION_MISSING_EVERYTHING);
        $section = $test2->get_full_section();
        $this->assertEquals(array(13 => 3), $section->conditionscompletion);
        $this->assertEquals(array(666 => (object)array('min' => 0.4, 'max' => null, 'name' => '!missing')),
                $section->conditionsgrade);

        // Wipe conditions
        $test2->wipe_conditions();
        $this->assertEquals(array(), $section->conditionscompletion);
        $this->assertEquals(array(), $section->conditionsgrade);

        // Check they were really wiped
        $test3 = new condition_info_section((object)array('id' => $cmid),
                CONDITION_MISSING_EVERYTHING);
        $section = $test3->get_full_section();
        $this->assertEquals(array(), $section->conditionscompletion);
        $this->assertEquals(array(), $section->conditionsgrade);
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

    public function test_section_is_available() {
        global $DB, $USER;
        $courseid = $this->make_course();

        // Enrol user (needed for groups)
        $enrolplugin = enrol_get_plugin('manual');
        $course = $DB->get_record('course', array('id' => $courseid));
        $enrolplugin->add_instance($course);
        $enrolinstances = enrol_get_instances($courseid, false);
        foreach ($enrolinstances as $enrolinstance) {
            if ($enrolinstance->enrol === 'manual') {
                break;
            }
        }
        $enrolplugin->enrol_user($enrolinstance, $USER->id);

        // Module for conditions later
        $cmid = $this->make_course_module($courseid);

        // No conditions
        $sectionid = $this->make_section($courseid, array($cmid), 1);
        $ci = new condition_info_section((object)array('id' => $sectionid),
                CONDITION_MISSING_EVERYTHING);
        $this->assertTrue($ci->is_available($text, false, 0));
        $this->assertEquals('', $text);

        // Time (from)
        $time = time() + 100;
        $sectionid = $this->make_section($courseid, array(), 2, array('availablefrom' => $time));
        $ci = new condition_info_section((object)array('id' => $sectionid),
                CONDITION_MISSING_EVERYTHING);
        $this->assertFalse($ci->is_available($text));
        $timetext = userdate($time, get_string('strftimedate', 'langconfig'));
        $this->assertRegExp('~' . preg_quote($timetext) . '~', $text);

        $time=time()-100;
        $sectionid = $this->make_section($courseid, array(), 3, array('availablefrom' => $time));
        $ci = new condition_info_section((object)array('id' => $sectionid),
                CONDITION_MISSING_EVERYTHING);
        $this->assertTrue($ci->is_available($text));
        $this->assertEquals('', $text);
        $timetext = userdate($time, get_string('strftimedate', 'langconfig'));
        $this->assertRegExp('~' . preg_quote($timetext) . '~', $ci->get_full_information());

        // Time (until)
        $sectionid = $this->make_section($courseid, array(), 4, array('availableuntil' => time() - 100));
        $ci = new condition_info_section((object)array('id' => $sectionid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals('', $text);

        // Completion: first set up cm
        $sectionid = $this->make_section($courseid, array(), 5);
        $cm = $DB->get_record('course_modules', array('id' => $cmid));
        $cm->completion = COMPLETION_TRACKING_MANUAL;
        $DB->update_record('course_modules', $cm);

        // Completion: Reset modinfo after changing the options
        rebuild_course_cache($courseid);

        // Completion: Add condition
        $ci = new condition_info_section((object)array('id' => $sectionid),
                CONDITION_MISSING_EVERYTHING);
        $ci->add_completion_condition($cmid, COMPLETION_COMPLETE);
        condition_info_section::wipe_session_cache();

        // Completion: Check
        $this->assertFalse($ci->is_available($text, false));
        $this->assertEquals(get_string('requires_completion_1', 'condition', 'xxx'), $text);
        completion_info::wipe_session_cache();
        $completion = new completion_info($DB->get_record('course', array('id' => $courseid)));
        $completion->update_state($cm, COMPLETION_COMPLETE);
        completion_info::wipe_session_cache();
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));
        $this->assertFalse($ci->is_available($text, false, $USER->id + 1));

        // Completion: Uncheck
        completion_info::wipe_session_cache();
        condition_info_section::wipe_session_cache();
        $completion = new completion_info($DB->get_record('course', array('id' => $courseid)));
        $completion->update_state($cm, COMPLETION_INCOMPLETE);
        $this->assertFalse($ci->is_available($text));

        // Completion: Incomplete condition
        $ci->wipe_conditions();
        $ci->add_completion_condition($cmid, COMPLETION_INCOMPLETE);
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));
        $this->assertTrue($ci->is_available($text, false, $USER->id + 1));
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text, true));

        // Grade: Add a fake grade item
        $gradeitemid = $DB->insert_record('grade_items', (object)array(
            'courseid' => $courseid, 'itemname' => 'frog'));

        // Grade: Add a condition on a value existing
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid, null, null, true);
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_any', 'condition', 'frog'), $text);

        // Grade: Fake it existing
        $DB->insert_record('grade_grades', (object)array(
            'itemid' => $gradeitemid, 'userid' => $USER->id, 'finalgrade' => 3.78));
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text, true));

        // Grade: Now require that user gets more than 3.78001
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid, 3.78001, null, true);
        condition_info_section::wipe_session_cache();
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_min', 'condition', 'frog'), $text);

        // Grade: ...just on 3.78...
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid, 3.78, null, true);
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));

        // Grade: ...less than 3.78
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid, null, 3.78, true);
        condition_info_section::wipe_session_cache();
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_max', 'condition', 'frog'), $text);

        // Grade: ...less than 3.78001
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid, null, 3.78001, true);
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));

        // Grade: ...in a range that includes it
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid, 3, 4, true);
        condition_info_section::wipe_session_cache();
        $this->assertTrue($ci->is_available($text));

        // Grade: ...in a range that doesn't include it
        $ci->wipe_conditions();
        $ci->add_grade_condition($gradeitemid, 4, 5, true);
        condition_info_section::wipe_session_cache();
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(get_string('requires_grade_range', 'condition', 'frog'), $text);

        // Grouping: Not member
        $groupingid = $this->make_grouping($courseid, 'Grouping');
        $groupid = $this->make_group($courseid, 'Group', $groupingid);
        $sectionid = $this->make_section($courseid, array(), 6, array('groupingid' => $groupingid));
        $ci = new condition_info_section((object)array('id' => $sectionid),
                CONDITION_MISSING_EVERYTHING);
        $this->assertFalse($ci->is_available($text));
        $this->assertEquals(trim(get_string('groupingnoaccess', 'condition')), $text);

        // Grouping: Member
        $this->assertTrue(groups_add_member($groupid, $USER->id));

        condition_info_section::init_global_cache();
        $this->assertTrue($ci->is_available($text));
        $this->assertEquals('', $text);
        $this->assertTrue($ci->is_available($text, true));

        // Grouping: Somebody else
        $this->assertFalse($ci->is_available($text, false, $USER->id + 1));
        $this->assertFalse($ci->is_available($text, true, $USER->id + 1));
    }
}

