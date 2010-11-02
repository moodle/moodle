<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot . '/lib/conditionlib.php');

class conditionlib_test extends UnitTestCaseUsingDatabase {
    public static $includecoverage = array('lib/conditionlib.php');

    public $conditionlib_tables = array(
               'lib' => array(
                   'files','context', 'capabilities', 'role',
                   'role_capabilities', 'role_assignments',
                   'course_categories', 'course',
                   'modules',
                   'course_sections', 'course_modules',
                   'course_modules_availability',
                   'course_modules_completion',
                   'grade_items', 'grade_grades'),
               'mod/resource' => array(
                   'resource'));
    public $oldcfg;

    public function setUp() {
        global $CFG;
        parent::setUp();
        $this->oldcfg=clone $CFG;
        $CFG->enableavailability=true;
        $CFG->enablecompletion=true;

        $this->switch_to_test_db(); // All operations until end of test method will happen in test DB

        foreach ($this->conditionlib_tables as $dir => $tables) {
            $this->create_test_tables($tables, $dir); // Create tables
        }
        $this->fill_records(); // Add common stuff needed by various test methods
    }

    /**
     * Method called after each test method. Doesn't do anything extraordinary except restore the global $DB to the real one.
     */
    public function tearDown() {
        global $CFG;
        $CFG->enableavailability=$this->oldcfg->enableavailability;
        $CFG->enablecompletion=$this->oldcfg->enablecompletion;
        parent::tearDown(); // All the test tables created in setUp will be dropped by this
    }

    private function fill_records() {
        global $DB;

        // We need the resource modules record available
        $DB->insert_record('modules', (object)array('name' => 'resource'));

        // We (get_fast_modinfo) need some capabilities present
        $DB->insert_record('capabilities', (object)array('name' => 'moodle/course:viewhiddenactivities',
                                                         'contextlevel' => CONTEXT_COURSE));
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
        $this->assertEqual(
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
        $this->assertEqual(
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
        $this->assertEqual(array(42=>2,666=>1),$fullcm->conditionscompletion);
        $this->assertEqual(array(37=>(object)array('min'=>5.5,'max'=>null,'name'=>'!missing')),
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
        $modinfo=get_fast_modinfo($DB->get_record('course',array('id'=>$courseid)));

        // Test basic data
        $this->assertEqual(1,$modinfo->cms[$cmid1]->showavailability);
        $this->assertEqual(17,$modinfo->cms[$cmid1]->availablefrom);
        $this->assertEqual(398,$modinfo->cms[$cmid1]->availableuntil);
        $this->assertEqual(0,$modinfo->cms[$cmid2]->showavailability);
        $this->assertEqual(0,$modinfo->cms[$cmid2]->availablefrom);
        $this->assertEqual(0,$modinfo->cms[$cmid2]->availableuntil);

        // Test condition arrays
        $this->assertEqual(array(),$modinfo->cms[$cmid1]->conditionscompletion);
        $this->assertEqual(array(),$modinfo->cms[$cmid1]->conditionsgrade);
        $this->assertEqual(array($cmid1=>1),
            $modinfo->cms[$cmid2]->conditionscompletion);
        $this->assertEqual(array($gradeitemid=>(object)array('min'=>5.5,'max'=>null,'name'=>'frog')),
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
        $this->assertEqual(array(),$cm->conditionscompletion);
        $this->assertEqual(array(),$cm->conditionsgrade);

        // Add conditions of each type
        $test1->add_completion_condition(13,3);
        $this->assertEqual(array(13=>3),$cm->conditionscompletion);
        $test1->add_grade_condition(666,0.4,null,true);
        $this->assertEqual(array(666=>(object)array('min'=>0.4,'max'=>null,'name'=>'!missing')),
            $cm->conditionsgrade);

        // Check they were really added in db
        $test2=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $cm=$test2->get_full_course_module();
        $this->assertEqual(array(13=>3),$cm->conditionscompletion);
        $this->assertEqual(array(666=>(object)array('min'=>0.4,'max'=>null,'name'=>'!missing')),
            $cm->conditionsgrade);

        // Wipe conditions
        $test2->wipe_conditions();
        $this->assertEqual(array(),$cm->conditionscompletion);
        $this->assertEqual(array(),$cm->conditionsgrade);

        // Check they were really wiped
        $test3=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $cm=$test3->get_full_course_module();
        $this->assertEqual(array(),$cm->conditionscompletion);
        $this->assertEqual(array(),$cm->conditionsgrade);
    }

    function test_is_available() {
        global $DB,$USER;
        $courseid=$this->make_course();

        // No conditions
        $cmid=$this->make_course_module($courseid);
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertTrue($ci->is_available($text,false,0));
        $this->assertEqual('',$text);

        // Time (from)
        $time=time()+100;
        $cmid=$this->make_course_module($courseid,array('availablefrom'=>$time));
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertFalse($ci->is_available($text));
        $this->assert(new PatternExpectation(
            '/'.preg_quote(userdate($time,get_string('strftimedate','langconfig'))).'/'),$text);

        $time=time()-100;
        $cmid=$this->make_course_module($courseid,array('availablefrom'=>$time));
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertTrue($ci->is_available($text));
        $this->assertEqual('',$text);
        $this->assert(new PatternExpectation(
            '/'.preg_quote(userdate($time,get_string('strftimedate','langconfig'))).'/'),$ci->get_full_information());

        // Time (until)
        $cmid=$this->make_course_module($courseid,array('availableuntil'=>time()-100));
        $ci=new condition_info((object)array('id'=>$cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertFalse($ci->is_available($text));
        $this->assertEqual('',$text);

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
        $this->assertEqual(get_string('requires_completion_1','condition','xxx'),$text);
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
        $this->assertEqual(get_string('requires_grade_any','condition','frog'),$text);

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
        $this->assertEqual(get_string('requires_grade_min','condition','frog'),$text);

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
        $this->assertEqual(get_string('requires_grade_max','condition','frog'),$text);

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
        $this->assertEqual(get_string('requires_grade_range','condition','frog'),$text);
    }

}

