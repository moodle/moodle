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
 * Course related unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/tests/fixtures/course_capability_assignment.php');
require_once($CFG->dirroot . '/enrol/imsenterprise/tests/imsenterprise_test.php');

class core_course_courselib_testcase extends advanced_testcase {

    /**
     * Set forum specific test values for calling create_module().
     *
     * @param object $moduleinfo - the moduleinfo to add some specific values - passed in reference.
     */
    private function forum_create_set_values(&$moduleinfo) {
        // Completion specific to forum - optional.
        $moduleinfo->completionposts = 3;
        $moduleinfo->completiondiscussions = 1;
        $moduleinfo->completionreplies = 2;

        // Specific values to the Forum module.
        $moduleinfo->forcesubscribe = FORUM_INITIALSUBSCRIBE;
        $moduleinfo->type = 'single';
        $moduleinfo->trackingtype = FORUM_TRACKING_FORCED;
        $moduleinfo->maxbytes = 10240;
        $moduleinfo->maxattachments = 2;

        // Post threshold for blocking - specific to forum.
        $moduleinfo->blockperiod = 60*60*24;
        $moduleinfo->blockafter = 10;
        $moduleinfo->warnafter = 5;
    }

    /**
     * Execute test asserts on the saved DB data by create_module($forum).
     *
     * @param object $moduleinfo - the specific forum values that were used to create a forum.
     * @param object $dbmodinstance - the DB values of the created forum.
     */
    private function forum_create_run_asserts($moduleinfo, $dbmodinstance) {
        // Compare values specific to forums.
        $this->assertEquals($moduleinfo->forcesubscribe, $dbmodinstance->forcesubscribe);
        $this->assertEquals($moduleinfo->type, $dbmodinstance->type);
        $this->assertEquals($moduleinfo->assessed, $dbmodinstance->assessed);
        $this->assertEquals($moduleinfo->completionposts, $dbmodinstance->completionposts);
        $this->assertEquals($moduleinfo->completiondiscussions, $dbmodinstance->completiondiscussions);
        $this->assertEquals($moduleinfo->completionreplies, $dbmodinstance->completionreplies);
        $this->assertEquals($moduleinfo->scale, $dbmodinstance->scale);
        $this->assertEquals($moduleinfo->assesstimestart, $dbmodinstance->assesstimestart);
        $this->assertEquals($moduleinfo->assesstimefinish, $dbmodinstance->assesstimefinish);
        $this->assertEquals($moduleinfo->rsstype, $dbmodinstance->rsstype);
        $this->assertEquals($moduleinfo->rssarticles, $dbmodinstance->rssarticles);
        $this->assertEquals($moduleinfo->trackingtype, $dbmodinstance->trackingtype);
        $this->assertEquals($moduleinfo->maxbytes, $dbmodinstance->maxbytes);
        $this->assertEquals($moduleinfo->maxattachments, $dbmodinstance->maxattachments);
        $this->assertEquals($moduleinfo->blockperiod, $dbmodinstance->blockperiod);
        $this->assertEquals($moduleinfo->blockafter, $dbmodinstance->blockafter);
        $this->assertEquals($moduleinfo->warnafter, $dbmodinstance->warnafter);
    }

    /**
     * Set assign module specific test values for calling create_module().
     *
     * @param object $moduleinfo - the moduleinfo to add some specific values - passed in reference.
     */
    private function assign_create_set_values(&$moduleinfo) {
        // Specific values to the Assign module.
        $moduleinfo->alwaysshowdescription = true;
        $moduleinfo->submissiondrafts = true;
        $moduleinfo->requiresubmissionstatement = true;
        $moduleinfo->sendnotifications = true;
        $moduleinfo->sendlatenotifications = true;
        $moduleinfo->duedate = time() + (7 * 24 * 3600);
        $moduleinfo->cutoffdate = time() + (7 * 24 * 3600);
        $moduleinfo->allowsubmissionsfromdate = time();
        $moduleinfo->teamsubmission = true;
        $moduleinfo->requireallteammemberssubmit = true;
        $moduleinfo->teamsubmissiongroupingid = true;
        $moduleinfo->blindmarking = true;
        $moduleinfo->markingworkflow = true;
        $moduleinfo->markingallocation = true;
        $moduleinfo->assignsubmission_onlinetext_enabled = true;
        $moduleinfo->assignsubmission_file_enabled = true;
        $moduleinfo->assignsubmission_file_maxfiles = 1;
        $moduleinfo->assignsubmission_file_maxsizebytes = 1000000;
        $moduleinfo->assignsubmission_comments_enabled = true;
        $moduleinfo->assignfeedback_comments_enabled = true;
        $moduleinfo->assignfeedback_offline_enabled = true;
        $moduleinfo->assignfeedback_file_enabled = true;

        // Advanced grading.
        $gradingmethods = grading_manager::available_methods();
        $moduleinfo->advancedgradingmethod_submissions = current(array_keys($gradingmethods));
    }

    /**
     * Execute test asserts on the saved DB data by create_module($assign).
     *
     * @param object $moduleinfo - the specific assign module values that were used to create an assign module.
     * @param object $dbmodinstance - the DB values of the created assign module.
     */
    private function assign_create_run_asserts($moduleinfo, $dbmodinstance) {
        global $DB;

        $this->assertEquals($moduleinfo->alwaysshowdescription, $dbmodinstance->alwaysshowdescription);
        $this->assertEquals($moduleinfo->submissiondrafts, $dbmodinstance->submissiondrafts);
        $this->assertEquals($moduleinfo->requiresubmissionstatement, $dbmodinstance->requiresubmissionstatement);
        $this->assertEquals($moduleinfo->sendnotifications, $dbmodinstance->sendnotifications);
        $this->assertEquals($moduleinfo->duedate, $dbmodinstance->duedate);
        $this->assertEquals($moduleinfo->cutoffdate, $dbmodinstance->cutoffdate);
        $this->assertEquals($moduleinfo->allowsubmissionsfromdate, $dbmodinstance->allowsubmissionsfromdate);
        $this->assertEquals($moduleinfo->teamsubmission, $dbmodinstance->teamsubmission);
        $this->assertEquals($moduleinfo->requireallteammemberssubmit, $dbmodinstance->requireallteammemberssubmit);
        $this->assertEquals($moduleinfo->teamsubmissiongroupingid, $dbmodinstance->teamsubmissiongroupingid);
        $this->assertEquals($moduleinfo->blindmarking, $dbmodinstance->blindmarking);
        $this->assertEquals($moduleinfo->markingworkflow, $dbmodinstance->markingworkflow);
        $this->assertEquals($moduleinfo->markingallocation, $dbmodinstance->markingallocation);
        // The goal not being to fully test assign_add_instance() we'll stop here for the assign tests - to avoid too many DB queries.

        // Advanced grading.
        $cm = get_coursemodule_from_instance('assign', $dbmodinstance->id);
        $contextmodule = context_module::instance($cm->id);
        $advancedgradingmethod = $DB->get_record('grading_areas',
            array('contextid' => $contextmodule->id,
                'activemethod' => $moduleinfo->advancedgradingmethod_submissions));
        $this->assertEquals($moduleinfo->advancedgradingmethod_submissions, $advancedgradingmethod);
    }

    /**
     * Run some asserts test for a specific module for the function create_module().
     *
     * The function has been created (and is called) for $this->test_create_module().
     * Note that the call to MODULE_create_set_values and MODULE_create_run_asserts are done after the common set values/run asserts.
     * So if you want, you can overwrite the default values/asserts in the respective functions.
     * @param string $modulename Name of the module ('forum', 'assign', 'book'...).
     */
    private function create_specific_module_test($modulename) {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $this->setAdminUser();

        // Warnings: you'll need to change this line if ever you come to test a module not following Moodle standard.
        require_once($CFG->dirroot.'/mod/'. $modulename .'/lib.php');

        // Enable avaibility.
        // If not enabled all conditional fields will be ignored.
        set_config('enableavailability', 1);

        // Enable course completion.
        // If not enabled all completion settings will be ignored.
        set_config('enablecompletion', COMPLETION_ENABLED);

        // Enable forum RSS feeds.
        set_config('enablerssfeeds', 1);
        set_config('forum_enablerssfeeds', 1);

        $course = $this->getDataGenerator()->create_course(array('numsections'=>1, 'enablecompletion' => COMPLETION_ENABLED),
           array('createsections'=>true));

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        // Create assign module instance for test.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course->id;
        $instance = $generator->create_instance($params);
        $assigncm = get_coursemodule_from_instance('assign', $instance->id);

        // Module test values.
        $moduleinfo = new stdClass();

        // Always mandatory generic values to any module.
        $moduleinfo->modulename = $modulename;
        $moduleinfo->section = 1; // This is the section number in the course. Not the section id in the database.
        $moduleinfo->course = $course->id;
        $moduleinfo->groupingid = $grouping->id;
        $moduleinfo->visible = true;

        // Sometimes optional generic values for some modules.
        $moduleinfo->name = 'My test module';
        $moduleinfo->showdescription = 1; // standard boolean
        require_once($CFG->libdir . '/gradelib.php');
        $gradecats = grade_get_categories_menu($moduleinfo->course, false);
        $gradecatid = current(array_keys($gradecats)); // Retrieve the first key of $gradecats
        $moduleinfo->gradecat = $gradecatid;
        $moduleinfo->groupmode = VISIBLEGROUPS;
        $moduleinfo->cmidnumber = 'idnumber_XXX';

        // Completion common to all module.
        $moduleinfo->completion = COMPLETION_TRACKING_AUTOMATIC;
        $moduleinfo->completionview = COMPLETION_VIEW_REQUIRED;
        $moduleinfo->completiongradeitemnumber = 1;
        $moduleinfo->completionexpected = time() + (7 * 24 * 3600);

        // Conditional activity.
        $moduleinfo->availability = '{"op":"&","showc":[true,true],"c":[' .
                '{"type":"date","d":">=","t":' . time() . '},' .
                '{"type":"date","d":"<","t":' . (time() + (7 * 24 * 3600)) . '}' .
                ']}';
        $coursegradeitem = grade_item::fetch_course_item($moduleinfo->course); //the activity will become available only when the user reach some grade into the course itself.
        $moduleinfo->conditiongradegroup = array(array('conditiongradeitemid' => $coursegradeitem->id, 'conditiongrademin' => 10, 'conditiongrademax' => 80));
        $moduleinfo->conditionfieldgroup = array(array('conditionfield' => 'email', 'conditionfieldoperator' => \availability_profile\condition::OP_CONTAINS, 'conditionfieldvalue' => '@'));
        $moduleinfo->conditioncompletiongroup = array(array('conditionsourcecmid' => $assigncm->id, 'conditionrequiredcompletion' => COMPLETION_COMPLETE)); // "conditionsourcecmid == 0" => none

        // Grading and Advanced grading.
        require_once($CFG->dirroot . '/rating/lib.php');
        $moduleinfo->assessed = RATING_AGGREGATE_AVERAGE;
        $moduleinfo->scale = 10; // Note: it could be minus (for specific course scale). It is a signed number.
        $moduleinfo->assesstimestart = time();
        $moduleinfo->assesstimefinish = time() + (7 * 24 * 3600);

        // RSS.
        $moduleinfo->rsstype = 2;
        $moduleinfo->rssarticles = 10;

        // Optional intro editor (depends of module).
        $draftid_editor = 0;
        file_prepare_draft_area($draftid_editor, null, null, null, null);
        $moduleinfo->introeditor = array('text' => 'This is a module', 'format' => FORMAT_HTML, 'itemid' => $draftid_editor);

        // Following is the advanced grading method area called 'submissions' for the 'assign' module.
        if (plugin_supports('mod', $modulename, FEATURE_GRADE_HAS_GRADE, false) && !plugin_supports('mod', $modulename, FEATURE_RATE, false)) {
            $moduleinfo->grade = 100;
        }

        // Plagiarism form values.
        // No plagiarism plugin installed by default. Use this space to make your own test.

        // Values specific to the module.
        $modulesetvalues = $modulename.'_create_set_values';
        $this->$modulesetvalues($moduleinfo);

        // Create the module.
        $result = create_module($moduleinfo);

        // Retrieve the module info.
        $dbmodinstance = $DB->get_record($moduleinfo->modulename, array('id' => $result->instance));
        $dbcm = get_coursemodule_from_instance($moduleinfo->modulename, $result->instance);
        // We passed the course section number to create_courses but $dbcm contain the section id.
        // We need to retrieve the db course section number.
        $section = $DB->get_record('course_sections', array('course' => $dbcm->course, 'id' => $dbcm->section));
        // Retrieve the grade item.
        $gradeitem = $DB->get_record('grade_items', array('courseid' => $moduleinfo->course,
            'iteminstance' => $dbmodinstance->id, 'itemmodule' => $moduleinfo->modulename));

        // Compare the values common to all module instances.
        $this->assertEquals($moduleinfo->modulename, $dbcm->modname);
        $this->assertEquals($moduleinfo->section, $section->section);
        $this->assertEquals($moduleinfo->course, $dbcm->course);
        $this->assertEquals($moduleinfo->groupingid, $dbcm->groupingid);
        $this->assertEquals($moduleinfo->visible, $dbcm->visible);
        $this->assertEquals($moduleinfo->completion, $dbcm->completion);
        $this->assertEquals($moduleinfo->completionview, $dbcm->completionview);
        $this->assertEquals($moduleinfo->completiongradeitemnumber, $dbcm->completiongradeitemnumber);
        $this->assertEquals($moduleinfo->completionexpected, $dbcm->completionexpected);
        $this->assertEquals($moduleinfo->availability, $dbcm->availability);
        $this->assertEquals($moduleinfo->showdescription, $dbcm->showdescription);
        $this->assertEquals($moduleinfo->groupmode, $dbcm->groupmode);
        $this->assertEquals($moduleinfo->cmidnumber, $dbcm->idnumber);
        $this->assertEquals($moduleinfo->gradecat, $gradeitem->categoryid);

        // Optional grade testing.
        if (plugin_supports('mod', $modulename, FEATURE_GRADE_HAS_GRADE, false) && !plugin_supports('mod', $modulename, FEATURE_RATE, false)) {
            $this->assertEquals($moduleinfo->grade, $dbmodinstance->grade);
        }

        // Some optional (but quite common) to some module.
        $this->assertEquals($moduleinfo->name, $dbmodinstance->name);
        $this->assertEquals($moduleinfo->intro, $dbmodinstance->intro);
        $this->assertEquals($moduleinfo->introformat, $dbmodinstance->introformat);

        // Test specific to the module.
        $modulerunasserts = $modulename.'_create_run_asserts';
        $this->$modulerunasserts($moduleinfo, $dbmodinstance);
        return $moduleinfo;
    }

    /**
     * Test create_module() for multiple modules defined in the $modules array (first declaration of the function).
     */
    public function test_create_module() {
        // Add the module name you want to test here.
        // Create the match MODULENAME_create_set_values() and MODULENAME_create_run_asserts().
        $modules = array('forum', 'assign');
        // Run all tests.
        foreach ($modules as $modulename) {
            $this->create_specific_module_test($modulename);
        }
    }

    /**
     * Test update_module() for multiple modules defined in the $modules array (first declaration of the function).
     */
    public function test_update_module() {
        // Add the module name you want to test here.
        // Create the match MODULENAME_update_set_values() and MODULENAME_update_run_asserts().
        $modules = array('forum');
        // Run all tests.
        foreach ($modules as $modulename) {
            $this->update_specific_module_test($modulename);
        }
    }

    /**
     * Set forum specific test values for calling update_module().
     *
     * @param object $moduleinfo - the moduleinfo to add some specific values - passed in reference.
     */
    private function forum_update_set_values(&$moduleinfo) {
        // Completion specific to forum - optional.
        $moduleinfo->completionposts = 3;
        $moduleinfo->completiondiscussions = 1;
        $moduleinfo->completionreplies = 2;

        // Specific values to the Forum module.
        $moduleinfo->forcesubscribe = FORUM_INITIALSUBSCRIBE;
        $moduleinfo->type = 'single';
        $moduleinfo->trackingtype = FORUM_TRACKING_FORCED;
        $moduleinfo->maxbytes = 10240;
        $moduleinfo->maxattachments = 2;

        // Post threshold for blocking - specific to forum.
        $moduleinfo->blockperiod = 60*60*24;
        $moduleinfo->blockafter = 10;
        $moduleinfo->warnafter = 5;
    }

    /**
     * Execute test asserts on the saved DB data by update_module($forum).
     *
     * @param object $moduleinfo - the specific forum values that were used to update a forum.
     * @param object $dbmodinstance - the DB values of the updated forum.
     */
    private function forum_update_run_asserts($moduleinfo, $dbmodinstance) {
        // Compare values specific to forums.
        $this->assertEquals($moduleinfo->forcesubscribe, $dbmodinstance->forcesubscribe);
        $this->assertEquals($moduleinfo->type, $dbmodinstance->type);
        $this->assertEquals($moduleinfo->assessed, $dbmodinstance->assessed);
        $this->assertEquals($moduleinfo->completionposts, $dbmodinstance->completionposts);
        $this->assertEquals($moduleinfo->completiondiscussions, $dbmodinstance->completiondiscussions);
        $this->assertEquals($moduleinfo->completionreplies, $dbmodinstance->completionreplies);
        $this->assertEquals($moduleinfo->scale, $dbmodinstance->scale);
        $this->assertEquals($moduleinfo->assesstimestart, $dbmodinstance->assesstimestart);
        $this->assertEquals($moduleinfo->assesstimefinish, $dbmodinstance->assesstimefinish);
        $this->assertEquals($moduleinfo->rsstype, $dbmodinstance->rsstype);
        $this->assertEquals($moduleinfo->rssarticles, $dbmodinstance->rssarticles);
        $this->assertEquals($moduleinfo->trackingtype, $dbmodinstance->trackingtype);
        $this->assertEquals($moduleinfo->maxbytes, $dbmodinstance->maxbytes);
        $this->assertEquals($moduleinfo->maxattachments, $dbmodinstance->maxattachments);
        $this->assertEquals($moduleinfo->blockperiod, $dbmodinstance->blockperiod);
        $this->assertEquals($moduleinfo->blockafter, $dbmodinstance->blockafter);
        $this->assertEquals($moduleinfo->warnafter, $dbmodinstance->warnafter);
    }



    /**
     * Test a specific type of module.
     *
     * @param string $modulename - the module name to test
     */
    private function update_specific_module_test($modulename) {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $this->setAdminUser();

        // Warnings: you'll need to change this line if ever you come to test a module not following Moodle standard.
        require_once($CFG->dirroot.'/mod/'. $modulename .'/lib.php');

        // Enable avaibility.
        // If not enabled all conditional fields will be ignored.
        set_config('enableavailability', 1);

        // Enable course completion.
        // If not enabled all completion settings will be ignored.
        set_config('enablecompletion', COMPLETION_ENABLED);

        // Enable forum RSS feeds.
        set_config('enablerssfeeds', 1);
        set_config('forum_enablerssfeeds', 1);

        $course = $this->getDataGenerator()->create_course(array('numsections'=>1, 'enablecompletion' => COMPLETION_ENABLED),
           array('createsections'=>true));

        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        // Create assign module instance for testing gradeitem.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course->id;
        $instance = $generator->create_instance($params);
        $assigncm = get_coursemodule_from_instance('assign', $instance->id);

        // Create the test forum to update.
        $initvalues = new stdClass();
        $initvalues->introformat = FORMAT_HTML;
        $initvalues->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $initvalues);

        // Retrieve course module.
        $cm = get_coursemodule_from_instance('forum', $forum->id);

        // Module test values.
        $moduleinfo = new stdClass();

        // Always mandatory generic values to any module.
        $moduleinfo->coursemodule = $cm->id;
        $moduleinfo->modulename = $modulename;
        $moduleinfo->course = $course->id;
        $moduleinfo->groupingid = $grouping->id;
        $moduleinfo->visible = true;

        // Sometimes optional generic values for some modules.
        $moduleinfo->name = 'My test module';
        $moduleinfo->showdescription = 1; // standard boolean
        require_once($CFG->libdir . '/gradelib.php');
        $gradecats = grade_get_categories_menu($moduleinfo->course, false);
        $gradecatid = current(array_keys($gradecats)); // Retrieve the first key of $gradecats
        $moduleinfo->gradecat = $gradecatid;
        $moduleinfo->groupmode = VISIBLEGROUPS;
        $moduleinfo->cmidnumber = 'idnumber_XXX';

        // Completion common to all module.
        $moduleinfo->completion = COMPLETION_TRACKING_AUTOMATIC;
        $moduleinfo->completionview = COMPLETION_VIEW_REQUIRED;
        $moduleinfo->completiongradeitemnumber = 1;
        $moduleinfo->completionexpected = time() + (7 * 24 * 3600);
        $moduleinfo->completionunlocked = 1;

        // Conditional activity.
        $coursegradeitem = grade_item::fetch_course_item($moduleinfo->course); //the activity will become available only when the user reach some grade into the course itself.
        $moduleinfo->availability = json_encode(\core_availability\tree::get_root_json(
                array(\availability_date\condition::get_json('>=', time()),
                \availability_date\condition::get_json('<', time() + (7 * 24 * 3600)),
                \availability_grade\condition::get_json($coursegradeitem->id, 10, 80),
                \availability_profile\condition::get_json(false, 'email', 'contains', '@'),
                \availability_completion\condition::get_json($assigncm->id, COMPLETION_COMPLETE)), '&'));

        // Grading and Advanced grading.
        require_once($CFG->dirroot . '/rating/lib.php');
        $moduleinfo->assessed = RATING_AGGREGATE_AVERAGE;
        $moduleinfo->scale = 10; // Note: it could be minus (for specific course scale). It is a signed number.
        $moduleinfo->assesstimestart = time();
        $moduleinfo->assesstimefinish = time() + (7 * 24 * 3600);

        // RSS.
        $moduleinfo->rsstype = 2;
        $moduleinfo->rssarticles = 10;

        // Optional intro editor (depends of module).
        $draftid_editor = 0;
        file_prepare_draft_area($draftid_editor, null, null, null, null);
        $moduleinfo->introeditor = array('text' => 'This is a module', 'format' => FORMAT_HTML, 'itemid' => $draftid_editor);

        // Following is the advanced grading method area called 'submissions' for the 'assign' module.
        if (plugin_supports('mod', $modulename, FEATURE_GRADE_HAS_GRADE, false) && !plugin_supports('mod', $modulename, FEATURE_RATE, false)) {
            $moduleinfo->grade = 100;
        }
        // Plagiarism form values.
        // No plagiarism plugin installed by default. Use this space to make your own test.

        // Values specific to the module.
        $modulesetvalues = $modulename.'_update_set_values';
        $this->$modulesetvalues($moduleinfo);

        // Create the module.
        $result = update_module($moduleinfo);

        // Retrieve the module info.
        $dbmodinstance = $DB->get_record($moduleinfo->modulename, array('id' => $result->instance));
        $dbcm = get_coursemodule_from_instance($moduleinfo->modulename, $result->instance);
        // Retrieve the grade item.
        $gradeitem = $DB->get_record('grade_items', array('courseid' => $moduleinfo->course,
            'iteminstance' => $dbmodinstance->id, 'itemmodule' => $moduleinfo->modulename));

        // Compare the values common to all module instances.
        $this->assertEquals($moduleinfo->modulename, $dbcm->modname);
        $this->assertEquals($moduleinfo->course, $dbcm->course);
        $this->assertEquals($moduleinfo->groupingid, $dbcm->groupingid);
        $this->assertEquals($moduleinfo->visible, $dbcm->visible);
        $this->assertEquals($moduleinfo->completion, $dbcm->completion);
        $this->assertEquals($moduleinfo->completionview, $dbcm->completionview);
        $this->assertEquals($moduleinfo->completiongradeitemnumber, $dbcm->completiongradeitemnumber);
        $this->assertEquals($moduleinfo->completionexpected, $dbcm->completionexpected);
        $this->assertEquals($moduleinfo->availability, $dbcm->availability);
        $this->assertEquals($moduleinfo->showdescription, $dbcm->showdescription);
        $this->assertEquals($moduleinfo->groupmode, $dbcm->groupmode);
        $this->assertEquals($moduleinfo->cmidnumber, $dbcm->idnumber);
        $this->assertEquals($moduleinfo->gradecat, $gradeitem->categoryid);

        // Optional grade testing.
        if (plugin_supports('mod', $modulename, FEATURE_GRADE_HAS_GRADE, false) && !plugin_supports('mod', $modulename, FEATURE_RATE, false)) {
            $this->assertEquals($moduleinfo->grade, $dbmodinstance->grade);
        }

        // Some optional (but quite common) to some module.
        $this->assertEquals($moduleinfo->name, $dbmodinstance->name);
        $this->assertEquals($moduleinfo->intro, $dbmodinstance->intro);
        $this->assertEquals($moduleinfo->introformat, $dbmodinstance->introformat);

        // Test specific to the module.
        $modulerunasserts = $modulename.'_update_run_asserts';
        $this->$modulerunasserts($moduleinfo, $dbmodinstance);
        return $moduleinfo;
   }

    /**
     * Data provider for course_delete module
     *
     * @return array An array of arrays contain test data
     */
    public function provider_course_delete_module() {
        $data = array();

        $data['assign'] = array('assign', array('duedate' => time()));
        $data['quiz'] = array('quiz', array('duedate' => time()));

        return $data;
    }

    /**
     * Test the create_course function
     */
    public function test_create_course() {
        global $DB;
        $this->resetAfterTest(true);
        $defaultcategory = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");

        $course = new stdClass();
        $course->fullname = 'Apu loves Unit Təsts';
        $course->shortname = 'Spread the lŭve';
        $course->idnumber = '123';
        $course->summary = 'Awesome!';
        $course->summaryformat = FORMAT_PLAIN;
        $course->format = 'topics';
        $course->newsitems = 0;
        $course->numsections = 5;
        $course->category = $defaultcategory;
        $original = (array) $course;

        $created = create_course($course);
        $context = context_course::instance($created->id);

        // Compare original and created.
        $this->assertEquals($original, array_intersect_key((array) $created, $original));

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $created->id, 'section' => 0));
        $this->assertTrue($sectioncreated);

        // Ensure blocks have been associated to the course.
        $blockcount = $DB->count_records('block_instances', array('parentcontextid' => $context->id));
        $this->assertGreaterThan(0, $blockcount);

        // Ensure that the shortname isn't duplicated.
        try {
            $created = create_course($course);
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertSame(get_string('shortnametaken', 'error', $course->shortname), $e->getMessage());
        }

        // Ensure that the idnumber isn't duplicated.
        $course->shortname .= '1';
        try {
            $created = create_course($course);
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertSame(get_string('courseidnumbertaken', 'error', $course->idnumber), $e->getMessage());
        }
    }

    public function test_create_course_with_generator() {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $course->id, 'section' => 0));
        $this->assertTrue($sectioncreated);
    }

    public function test_create_course_sections() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(
                array('shortname' => 'GrowingCourse',
                    'fullname' => 'Growing Course',
                    'numsections' => 5),
                array('createsections' => true));

        // Ensure all 6 (0-5) sections were created and course content cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $course->numsections), $sectionscreated);

        // this will do nothing, section already exists
        $this->assertFalse(course_create_sections_if_missing($course, $course->numsections));

        // this will create new section
        $this->assertTrue(course_create_sections_if_missing($course, $course->numsections + 1));

        // Ensure all 7 (0-6) sections were created and modinfo/sectioninfo cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $course->numsections + 1), $sectionscreated);
    }

    public function test_update_course() {
        global $DB;

        $this->resetAfterTest();

        $defaultcategory = $DB->get_field_select('course_categories', 'MIN(id)', 'parent = 0');

        $course = new stdClass();
        $course->fullname = 'Apu loves Unit Təsts';
        $course->shortname = 'test1';
        $course->idnumber = '1';
        $course->summary = 'Awesome!';
        $course->summaryformat = FORMAT_PLAIN;
        $course->format = 'topics';
        $course->newsitems = 0;
        $course->numsections = 5;
        $course->category = $defaultcategory;

        $created = create_course($course);
        // Ensure the checks only work on idnumber/shortname that are not already ours.
        update_course($created);

        $course->shortname = 'test2';
        $course->idnumber = '2';

        $created2 = create_course($course);

        // Test duplicate idnumber.
        $created2->idnumber = '1';
        try {
            update_course($created2);
            $this->fail('Expected exception when trying to update a course with duplicate idnumber');
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('courseidnumbertaken', 'error', $created2->idnumber), $e->getMessage());
        }

        // Test duplicate shortname.
        $created2->idnumber = '2';
        $created2->shortname = 'test1';
        try {
            update_course($created2);
            $this->fail('Expected exception when trying to update a course with a duplicate shortname');
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('shortnametaken', 'error', $created2->shortname), $e->getMessage());
        }
    }

    public function test_course_add_cm_to_section() {
        global $DB;
        $this->resetAfterTest(true);

        // Create course with 1 section.
        $course = $this->getDataGenerator()->create_course(
                array('shortname' => 'GrowingCourse',
                    'fullname' => 'Growing Course',
                    'numsections' => 1),
                array('createsections' => true));

        // Trash modinfo.
        rebuild_course_cache($course->id, true);

        // Create some cms for testing.
        $cmids = array();
        for ($i=0; $i<4; $i++) {
            $cmids[$i] = $DB->insert_record('course_modules', array('course' => $course->id));
        }

        // Add it to section that exists.
        course_add_cm_to_section($course, $cmids[0], 1);

        // Check it got added to sequence.
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0], $sequence);

        // Add a second, this time using courseid variant of parameters.
        $coursecacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        course_add_cm_to_section($course->id, $cmids[1], 1);
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0] . ',' . $cmids[1], $sequence);

        // Check that modinfo cache was reset but not rebuilt (important for performance if calling repeatedly).
        $this->assertGreaterThan($coursecacherev, $DB->get_field('course', 'cacherev', array('id' => $course->id)));
        $this->assertEmpty(cache::make('core', 'coursemodinfo')->get($course->id));

        // Add one to section that doesn't exist (this might rebuild modinfo).
        course_add_cm_to_section($course, $cmids[2], 2);
        $this->assertEquals(3, $DB->count_records('course_sections', array('course' => $course->id)));
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 2));
        $this->assertEquals($cmids[2], $sequence);

        // Add using the 'before' option.
        course_add_cm_to_section($course, $cmids[3], 2, $cmids[2]);
        $this->assertEquals(3, $DB->count_records('course_sections', array('course' => $course->id)));
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 2));
        $this->assertEquals($cmids[3] . ',' . $cmids[2], $sequence);
    }

    public function test_reorder_sections() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id), 'id') as $section) {
            $oldsections[$section->section] = $section->id;
            $sections[$section->id] = $section->section;
        }
        ksort($oldsections);

        $neworder = reorder_sections($sections, 2, 4);
        $neworder = array_keys($neworder);
        $this->assertEquals($oldsections[0], $neworder[0]);
        $this->assertEquals($oldsections[1], $neworder[1]);
        $this->assertEquals($oldsections[2], $neworder[4]);
        $this->assertEquals($oldsections[3], $neworder[2]);
        $this->assertEquals($oldsections[4], $neworder[3]);
        $this->assertEquals($oldsections[5], $neworder[5]);
        $this->assertEquals($oldsections[6], $neworder[6]);

        $neworder = reorder_sections($sections, 4, 2);
        $neworder = array_keys($neworder);
        $this->assertEquals($oldsections[0], $neworder[0]);
        $this->assertEquals($oldsections[1], $neworder[1]);
        $this->assertEquals($oldsections[2], $neworder[3]);
        $this->assertEquals($oldsections[3], $neworder[4]);
        $this->assertEquals($oldsections[4], $neworder[2]);
        $this->assertEquals($oldsections[5], $neworder[5]);
        $this->assertEquals($oldsections[6], $neworder[6]);

        $neworder = reorder_sections(1, 2, 4);
        $this->assertFalse($neworder);
    }

    public function test_move_section_down() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
        }
        ksort($oldsections);

        // Test move section down..
        move_section_to($course, 2, 4);
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $sections[$section->section] = $section->id;
        }
        ksort($sections);

        $this->assertEquals($oldsections[0], $sections[0]);
        $this->assertEquals($oldsections[1], $sections[1]);
        $this->assertEquals($oldsections[2], $sections[4]);
        $this->assertEquals($oldsections[3], $sections[2]);
        $this->assertEquals($oldsections[4], $sections[3]);
        $this->assertEquals($oldsections[5], $sections[5]);
        $this->assertEquals($oldsections[6], $sections[6]);
    }

    public function test_move_section_up() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
        }
        ksort($oldsections);

        // Test move section up..
        move_section_to($course, 6, 4);
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $sections[$section->section] = $section->id;
        }
        ksort($sections);

        $this->assertEquals($oldsections[0], $sections[0]);
        $this->assertEquals($oldsections[1], $sections[1]);
        $this->assertEquals($oldsections[2], $sections[2]);
        $this->assertEquals($oldsections[3], $sections[3]);
        $this->assertEquals($oldsections[4], $sections[5]);
        $this->assertEquals($oldsections[5], $sections[6]);
        $this->assertEquals($oldsections[6], $sections[4]);
    }

    public function test_move_section_marker() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));

        // Set course marker to the section we are going to move..
        course_set_marker($course->id, 2);
        // Verify that the course marker is set correctly.
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(2, $course->marker);

        // Test move the marked section down..
        move_section_to($course, 2, 4);

        // Verify that the coruse marker has been moved along with the section..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(4, $course->marker);

        // Test move the marked section up..
        move_section_to($course, 4, 3);

        // Verify that the course marker has been moved along with the section..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(3, $course->marker);

        // Test moving a non-marked section above the marked section..
        move_section_to($course, 4, 2);

        // Verify that the course marker has been moved down to accomodate..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(4, $course->marker);

        // Test moving a non-marked section below the marked section..
        move_section_to($course, 3, 6);

        // Verify that the course marker has been up to accomodate..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(3, $course->marker);
    }

    public function test_course_can_delete_section() {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $courseweeks = $generator->create_course(
            array('numsections' => 5, 'format' => 'weeks'),
            array('createsections' => true));
        $assign1 = $generator->create_module('assign', array('course' => $courseweeks, 'section' => 1));
        $assign2 = $generator->create_module('assign', array('course' => $courseweeks, 'section' => 2));

        $coursetopics = $generator->create_course(
            array('numsections' => 5, 'format' => 'topics'),
            array('createsections' => true));

        $coursesingleactivity = $generator->create_course(
            array('format' => 'singleactivity'),
            array('createsections' => true));

        // Enrol student and teacher.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $student = $generator->create_user();
        $teacher = $generator->create_user();

        $generator->enrol_user($student->id, $courseweeks->id, $roleids['student']);
        $generator->enrol_user($teacher->id, $courseweeks->id, $roleids['editingteacher']);

        $generator->enrol_user($student->id, $coursetopics->id, $roleids['student']);
        $generator->enrol_user($teacher->id, $coursetopics->id, $roleids['editingteacher']);

        $generator->enrol_user($student->id, $coursesingleactivity->id, $roleids['student']);
        $generator->enrol_user($teacher->id, $coursesingleactivity->id, $roleids['editingteacher']);

        // Teacher should be able to delete sections (except for 0) in topics and weeks format.
        $this->setUser($teacher);

        // For topics and weeks formats will return false for section 0 and true for any other section.
        $this->assertFalse(course_can_delete_section($courseweeks, 0));
        $this->assertTrue(course_can_delete_section($courseweeks, 1));

        $this->assertFalse(course_can_delete_section($coursetopics, 0));
        $this->assertTrue(course_can_delete_section($coursetopics, 1));

        // For singleactivity course format no section can be deleted.
        $this->assertFalse(course_can_delete_section($coursesingleactivity, 0));
        $this->assertFalse(course_can_delete_section($coursesingleactivity, 1));

        // Now let's revoke a capability from teacher to manage activity in section 1.
        $modulecontext = context_module::instance($assign1->cmid);
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $roleids['editingteacher'],
            $modulecontext);
        $modulecontext->mark_dirty();
        $this->assertFalse(course_can_delete_section($courseweeks, 1));
        $this->assertTrue(course_can_delete_section($courseweeks, 2));

        // Student does not have permissions to delete sections.
        $this->setUser($student);
        $this->assertFalse(course_can_delete_section($courseweeks, 1));
        $this->assertFalse(course_can_delete_section($coursetopics, 1));
        $this->assertFalse(course_can_delete_section($coursesingleactivity, 1));
    }

    public function test_course_delete_section() {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course(array('numsections' => 6, 'format' => 'topics'),
            array('createsections' => true));
        $assign0 = $generator->create_module('assign', array('course' => $course, 'section' => 0));
        $assign1 = $generator->create_module('assign', array('course' => $course, 'section' => 1));
        $assign21 = $generator->create_module('assign', array('course' => $course, 'section' => 2));
        $assign22 = $generator->create_module('assign', array('course' => $course, 'section' => 2));
        $assign3 = $generator->create_module('assign', array('course' => $course, 'section' => 3));
        $assign5 = $generator->create_module('assign', array('course' => $course, 'section' => 5));
        $assign6 = $generator->create_module('assign', array('course' => $course, 'section' => 6));

        $this->setAdminUser();

        // Attempt to delete non-existing section.
        $this->assertFalse(course_delete_section($course, 10, false));
        $this->assertFalse(course_delete_section($course, 9, true));

        // Attempt to delete 0-section.
        $this->assertFalse(course_delete_section($course, 0, true));
        $this->assertTrue($DB->record_exists('course_modules', array('id' => $assign0->cmid)));

        // Delete last section.
        $this->assertTrue(course_delete_section($course, 6, true));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign6->cmid)));
        $this->assertEquals(5, course_get_format($course)->get_course()->numsections);

        // Delete empty section.
        $this->assertTrue(course_delete_section($course, 4, false));
        $this->assertEquals(4, course_get_format($course)->get_course()->numsections);

        // Delete section in the middle (2).
        $this->assertFalse(course_delete_section($course, 2, false));
        $this->assertTrue(course_delete_section($course, 2, true));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign21->cmid)));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign22->cmid)));
        $this->assertEquals(3, course_get_format($course)->get_course()->numsections);
        $this->assertEquals(array(0 => array($assign0->cmid),
            1 => array($assign1->cmid),
            2 => array($assign3->cmid),
            3 => array($assign5->cmid)), get_fast_modinfo($course)->sections);

        // Make last section orphaned.
        update_course((object)array('id' => $course->id, 'numsections' => 2));
        $this->assertEquals(2, course_get_format($course)->get_course()->numsections);

        // Remove orphaned section.
        $this->assertTrue(course_delete_section($course, 3, true));
        $this->assertEquals(2, course_get_format($course)->get_course()->numsections);

        // Remove marked section.
        course_set_marker($course->id, 1);
        $this->assertTrue(course_get_format($course)->is_section_current(1));
        $this->assertTrue(course_delete_section($course, 1, true));
        $this->assertFalse(course_get_format($course)->is_section_current(1));
    }

    public function test_get_course_display_name_for_list() {
        global $CFG;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('shortname' => 'FROG101', 'fullname' => 'Introduction to pond life'));

        $CFG->courselistshortnames = 0;
        $this->assertEquals('Introduction to pond life', get_course_display_name_for_list($course));

        $CFG->courselistshortnames = 1;
        $this->assertEquals('FROG101 Introduction to pond life', get_course_display_name_for_list($course));
    }

    public function test_move_module_in_course() {
        global $DB;

        $this->resetAfterTest(true);
        // Setup fixture
        $course = $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections' => true));
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));

        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        $newsection = get_fast_modinfo($course)->get_section_info(3);
        $oldsectionid = $cm->section;

        // Perform the move
        moveto_module($cm, $newsection);

        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        // Check that the cached modinfo contains the correct section info
        $modinfo = get_fast_modinfo($course);
        $this->assertTrue(empty($modinfo->sections[0]));
        $this->assertFalse(empty($modinfo->sections[3]));

        // Check that the old section's sequence no longer contains this ID
        $oldsection = $DB->get_record('course_sections', array('id' => $oldsectionid));
        $oldsequences = explode(',', $newsection->sequence);
        $this->assertFalse(in_array($cm->id, $oldsequences));

        // Check that the new section's sequence now contains this ID
        $newsection = $DB->get_record('course_sections', array('id' => $newsection->id));
        $newsequences = explode(',', $newsection->sequence);
        $this->assertTrue(in_array($cm->id, $newsequences));

        // Check that the section number has been changed in the cm
        $this->assertEquals($newsection->id, $cm->section);


        // Perform a second move as some issues were only seen on the second move
        $newsection = get_fast_modinfo($course)->get_section_info(2);
        $oldsectionid = $cm->section;
        moveto_module($cm, $newsection);

        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        // Check that the cached modinfo contains the correct section info
        $modinfo = get_fast_modinfo($course);
        $this->assertTrue(empty($modinfo->sections[0]));
        $this->assertFalse(empty($modinfo->sections[2]));

        // Check that the old section's sequence no longer contains this ID
        $oldsection = $DB->get_record('course_sections', array('id' => $oldsectionid));
        $oldsequences = explode(',', $newsection->sequence);
        $this->assertFalse(in_array($cm->id, $oldsequences));

        // Check that the new section's sequence now contains this ID
        $newsection = $DB->get_record('course_sections', array('id' => $newsection->id));
        $newsequences = explode(',', $newsection->sequence);
        $this->assertTrue(in_array($cm->id, $newsequences));
    }

    public function test_module_visibility() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create course and modules.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(), 'course' => $course->id));
        $modules = compact('forum', 'assign');

        // Hiding the modules.
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 0);
            $this->check_module_visibility($mod, 0, 0);
        }

        // Showing the modules.
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 1);
            $this->check_module_visibility($mod, 1, 1);
        }
    }

    public function test_section_visibility_events() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('numsections' => 1), array('createsections' => true));
        $sectionnumber = 1;
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => $sectionnumber));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(),
            'course' => $course->id), array('section' => $sectionnumber));
        $sink = $this->redirectEvents();
        set_section_visible($course->id, $sectionnumber, 0);
        $events = $sink->get_events();

        // Extract the number of events related to what we are testing, other events
        // such as course_section_updated could have been triggered.
        $count = 0;
        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_updated) {
                $count++;
            }
        }
        $this->assertSame(2, $count);
        $sink->close();
    }

    public function test_section_visibility() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create course.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 3), array('createsections' => true));

        $sink = $this->redirectEvents();

        // Testing an empty section.
        $sectionnumber = 1;
        set_section_visible($course->id, $sectionnumber, 0);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 0);
        set_section_visible($course->id, $sectionnumber, 1);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 1);

        // Checking that an event was fired.
        $events = $sink->get_events();
        $this->assertInstanceOf('\core\event\course_section_updated', $events[0]);
        $sink->close();

        // Testing a section with visible modules.
        $sectionnumber = 2;
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
                array('section' => $sectionnumber));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(),
                'course' => $course->id), array('section' => $sectionnumber));
        $modules = compact('forum', 'assign');
        set_section_visible($course->id, $sectionnumber, 0);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 0);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 0, 1);
        }
        set_section_visible($course->id, $sectionnumber, 1);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 1);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 1, 1);
        }

        // Testing a section with hidden modules, which should stay hidden.
        $sectionnumber = 3;
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
                array('section' => $sectionnumber));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(),
                'course' => $course->id), array('section' => $sectionnumber));
        $modules = compact('forum', 'assign');
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 0);
            $this->check_module_visibility($mod, 0, 0);
        }
        set_section_visible($course->id, $sectionnumber, 0);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 0);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 0, 0);
        }
        set_section_visible($course->id, $sectionnumber, 1);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 1);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 0, 0);
        }
    }

    /**
     * Helper function to assert that a module has correctly been made visible, or hidden.
     *
     * @param stdClass $mod module information
     * @param int $visibility the current state of the module
     * @param int $visibleold the current state of the visibleold property
     * @return void
     */
    public function check_module_visibility($mod, $visibility, $visibleold) {
        global $DB;
        $cm = get_fast_modinfo($mod->course)->get_cm($mod->cmid);
        $this->assertEquals($visibility, $cm->visible);
        $this->assertEquals($visibleold, $cm->visibleold);

        // Check the module grade items.
        $grade_items = grade_item::fetch_all(array('itemtype' => 'mod', 'itemmodule' => $cm->modname,
                'iteminstance' => $cm->instance, 'courseid' => $cm->course));
        if ($grade_items) {
            foreach ($grade_items as $grade_item) {
                if ($visibility) {
                    $this->assertFalse($grade_item->is_hidden(), "$cm->modname grade_item not visible");
                } else {
                    $this->assertTrue($grade_item->is_hidden(), "$cm->modname grade_item not hidden");
                }
            }
        }

        // Check the events visibility.
        if ($events = $DB->get_records('event', array('instance' => $cm->instance, 'modulename' => $cm->modname))) {
            foreach ($events as $event) {
                $calevent = new calendar_event($event);
                $this->assertEquals($visibility, $calevent->visible, "$cm->modname calendar_event visibility");
            }
        }
    }

    public function test_course_page_type_list() {
        global $DB;
        $this->resetAfterTest(true);

        // Create a category.
        $category = new stdClass();
        $category->name = 'Test Category';

        $testcategory = $this->getDataGenerator()->create_category($category);

        // Create a course.
        $course = new stdClass();
        $course->fullname = 'Apu loves Unit Təsts';
        $course->shortname = 'Spread the lŭve';
        $course->idnumber = '123';
        $course->summary = 'Awesome!';
        $course->summaryformat = FORMAT_PLAIN;
        $course->format = 'topics';
        $course->newsitems = 0;
        $course->numsections = 5;
        $course->category = $testcategory->id;

        $testcourse = $this->getDataGenerator()->create_course($course);

        // Create contexts.
        $coursecontext = context_course::instance($testcourse->id);
        $parentcontext = $coursecontext->get_parent_context(); // Not actually used.
        $pagetype = 'page-course-x'; // Not used either.
        $pagetypelist = course_page_type_list($pagetype, $parentcontext, $coursecontext);

        // Page type lists for normal courses.
        $testpagetypelist1 = array();
        $testpagetypelist1['*'] = 'Any page';
        $testpagetypelist1['course-*'] = 'Any course page';
        $testpagetypelist1['course-view-*'] = 'Any type of course main page';

        $this->assertEquals($testpagetypelist1, $pagetypelist);

        // Get the context for the front page course.
        $sitecoursecontext = context_course::instance(SITEID);
        $pagetypelist = course_page_type_list($pagetype, $parentcontext, $sitecoursecontext);

        // Page type list for the front page course.
        $testpagetypelist2 = array('*' => 'Any page');
        $this->assertEquals($testpagetypelist2, $pagetypelist);

        // Make sure that providing no current context to the function doesn't result in an error.
        // Calls made from generate_page_type_patterns() may provide null values.
        $pagetypelist = course_page_type_list($pagetype, null, null);
        $this->assertEquals($pagetypelist, $testpagetypelist1);
    }

    public function test_compare_activities_by_time_desc() {

        // Let's create some test data.
        $activitiesivities = array();
        $x = new stdClass();
        $x->timestamp = null;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 1;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 3;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 0;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 5;
        $activities[] = $x;

        $x = new stdClass();
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 5;
        $activities[] = $x;

        // Do the sorting.
        usort($activities, 'compare_activities_by_time_desc');

        // Let's check the result.
        $last = 10;
        foreach($activities as $activity) {
            if (empty($activity->timestamp)) {
                $activity->timestamp = 0;
            }
            $this->assertLessThanOrEqual($last, $activity->timestamp);
        }
    }

    public function test_compare_activities_by_time_asc() {

        // Let's create some test data.
        $activities = array();
        $x = new stdClass();
        $x->timestamp = null;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 1;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 3;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 0;
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 5;
        $activities[] = $x;

        $x = new stdClass();
        $activities[] = $x;

        $x = new stdClass();
        $x->timestamp = 5;
        $activities[] = $x;

        // Do the sorting.
        usort($activities, 'compare_activities_by_time_asc');

        // Let's check the result.
        $last = 0;
        foreach($activities as $activity) {
            if (empty($activity->timestamp)) {
                $activity->timestamp = 0;
            }
            $this->assertGreaterThanOrEqual($last, $activity->timestamp);
        }
    }

    /**
     * Tests moving a module between hidden/visible sections and
     * verifies that the course/module visiblity seettings are
     * retained.
     */
    public function test_moveto_module_between_hidden_sections() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('numsections' => 4), array('createsections' => true));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $quiz= $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        // Set the page as hidden
        set_coursemodule_visible($page->cmid, 0);

        // Set sections 3 as hidden.
        set_section_visible($course->id, 3, 0);

        $modinfo = get_fast_modinfo($course);

        $hiddensection = $modinfo->get_section_info(3);
        // New section is definitely not visible:
        $this->assertEquals($hiddensection->visible, 0);

        $forumcm = $modinfo->cms[$forum->cmid];
        $pagecm = $modinfo->cms[$page->cmid];

        // Move the forum and the page to a hidden section, make sure moveto_module returns 0 as new visibility state.
        $this->assertEquals(0, moveto_module($forumcm, $hiddensection));
        $this->assertEquals(0, moveto_module($pagecm, $hiddensection));

        $modinfo = get_fast_modinfo($course);

        // Verify that forum and page have been moved to the hidden section and quiz has not.
        $this->assertContains($forum->cmid, $modinfo->sections[3]);
        $this->assertContains($page->cmid, $modinfo->sections[3]);
        $this->assertNotContains($quiz->cmid, $modinfo->sections[3]);

        // Verify that forum has been made invisible.
        $forumcm = $modinfo->cms[$forum->cmid];
        $this->assertEquals($forumcm->visible, 0);
        // Verify that old state has been retained.
        $this->assertEquals($forumcm->visibleold, 1);

        // Verify that page has stayed invisible.
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
        // Verify that old state has been retained.
        $this->assertEquals($pagecm->visibleold, 0);

        // Verify that quiz has been unaffected.
        $quizcm = $modinfo->cms[$quiz->cmid];
        $this->assertEquals($quizcm->visible, 1);

        // Move forum and page back to visible section.
        // Make sure the visibility is restored to the original value (visible for forum and hidden for page).
        $visiblesection = $modinfo->get_section_info(2);
        $this->assertEquals(1, moveto_module($forumcm, $visiblesection));
        $this->assertEquals(0, moveto_module($pagecm, $visiblesection));

        $modinfo = get_fast_modinfo($course);

        // Double check that forum has been made visible.
        $forumcm = $modinfo->cms[$forum->cmid];
        $this->assertEquals($forumcm->visible, 1);

        // Double check that page has stayed invisible.
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);

        // Move the page in the same section (this is what mod duplicate does).
        // Visibility of page remains 0.
        $this->assertEquals(0, moveto_module($pagecm, $visiblesection, $forumcm));

        // Double check that the the page is still hidden.
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
    }

    /**
     * Tests moving a module around in the same section. moveto_module()
     * is called this way in modduplicate.
     */
    public function test_moveto_module_in_same_section() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('numsections' => 3), array('createsections' => true));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        // Simulate inconsistent visible/visibleold values (MDL-38713).
        $cm = $DB->get_record('course_modules', array('id' => $page->cmid), '*', MUST_EXIST);
        $cm->visible = 0;
        $cm->visibleold = 1;
        $DB->update_record('course_modules', $cm);

        $modinfo = get_fast_modinfo($course);
        $forumcm = $modinfo->cms[$forum->cmid];
        $pagecm = $modinfo->cms[$page->cmid];

        // Verify that page is hidden.
        $this->assertEquals($pagecm->visible, 0);

        // Verify section 0 is where all mods added.
        $section = $modinfo->get_section_info(0);
        $this->assertEquals($section->id, $forumcm->section);
        $this->assertEquals($section->id, $pagecm->section);


        // Move the page inside the hidden section. Make sure it is hidden.
        $this->assertEquals(0, moveto_module($pagecm, $section, $forumcm));

        // Double check that the the page is still hidden.
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
    }

    /**
     * Tests the function that deletes a course module
     *
     * @param string $type The type of module for the test
     * @param array $options The options for the module creation
     * @dataProvider provider_course_delete_module
     */
    public function test_course_delete_module($type, $options) {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create course and modules.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5));
        $options['course'] = $course->id;

        // Generate an assignment with due date (will generate a course event).
        $module = $this->getDataGenerator()->create_module($type, $options);

        // Get the module context.
        $modcontext = context_module::instance($module->cmid);

        // Verify context exists.
        $this->assertInstanceOf('context_module', $modcontext);

        // Make module specific messes.
        switch ($type) {
            case 'assign':
                // Add some tags to this assignment.
                core_tag_tag::set_item_tags('mod_assign', 'assign', $module->id, $modcontext, array('Tag 1', 'Tag 2', 'Tag 3'));
                core_tag_tag::set_item_tags('core', 'course_modules', $module->cmid, $modcontext, array('Tag 3', 'Tag 4', 'Tag 5'));

                // Confirm the tag instances were added.
                $criteria = array('component' => 'mod_assign', 'itemtype' => 'assign', 'contextid' => $modcontext->id);
                $this->assertEquals(3, $DB->count_records('tag_instance', $criteria));
                $criteria = array('component' => 'core', 'itemtype' => 'course_modules', 'contextid' => $modcontext->id);
                $this->assertEquals(3, $DB->count_records('tag_instance', $criteria));

                // Verify event assignment event has been generated.
                $eventcount = $DB->count_records('event', array('instance' => $module->id, 'modulename' => $type));
                $this->assertEquals(1, $eventcount);

                break;
            case 'quiz':
                $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
                $qcat = $qgen->create_question_category(array('contextid' => $modcontext->id));
                $questions = array(
                    $qgen->create_question('shortanswer', null, array('category' => $qcat->id)),
                    $qgen->create_question('shortanswer', null, array('category' => $qcat->id)),
                );
                $this->expectOutputRegex('/'.get_string('unusedcategorydeleted', 'question').'/');
                break;
            default:
                break;
        }

        // Run delete..
        course_delete_module($module->cmid);

        // Verify the context has been removed.
        $this->assertFalse(context_module::instance($module->cmid, IGNORE_MISSING));

        // Verify the course_module record has been deleted.
        $cmcount = $DB->count_records('course_modules', array('id' => $module->cmid));
        $this->assertEmpty($cmcount);

        // Test clean up of module specific messes.
        switch ($type) {
            case 'assign':
                // Verify event assignment events have been removed.
                $eventcount = $DB->count_records('event', array('instance' => $module->id, 'modulename' => $type));
                $this->assertEmpty($eventcount);

                // Verify the tag instances were deleted.
                $criteria = array('component' => 'mod_assign', 'contextid' => $modcontext->id);
                $this->assertEquals(0, $DB->count_records('tag_instance', $criteria));

                $criteria = array('component' => 'core', 'itemtype' => 'course_modules', 'contextid' => $modcontext->id);
                $this->assertEquals(0, $DB->count_records('tag_instance', $criteria));
                break;
            case 'quiz':
                // Verify category deleted.
                $criteria = array('contextid' => $modcontext->id);
                $this->assertEquals(0, $DB->count_records('question_categories', $criteria));

                // Verify questions deleted.
                $criteria = array('category' => $qcat->id);
                $this->assertEquals(0, $DB->count_records('question', $criteria));
                break;
            default:
                break;
        }
    }

    /**
     * Test that triggering a course_created event works as expected.
     */
    public function test_course_created_event() {
        global $DB;

        $this->resetAfterTest();

        // Catch the events.
        $sink = $this->redirectEvents();

        // Create the course with an id number which is used later when generating a course via the imsenterprise plugin.
        $data = new stdClass();
        $data->idnumber = 'idnumber';
        $course = $this->getDataGenerator()->create_course($data);
        // Get course from DB for comparison.
        $course = $DB->get_record('course', array('id' => $course->id));

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\course_created', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($course->id, $event->objectid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($course, $event->get_record_snapshot('course', $course->id));
        $this->assertEquals('course_created', $event->get_legacy_eventname());
        $this->assertEventLegacyData($course, $event);
        $expectedlog = array(SITEID, 'course', 'new', 'view.php?id=' . $course->id, $course->fullname . ' (ID ' . $course->id . ')');
        $this->assertEventLegacyLogData($expectedlog, $event);

        // Now we want to trigger creating a course via the imsenterprise.
        // Delete the course we created earlier, as we want the imsenterprise plugin to create this.
        // We do not want print out any of the text this function generates while doing this, which is why
        // we are using ob_start() and ob_end_clean().
        ob_start();
        delete_course($course);
        ob_end_clean();

        // Create the XML file we want to use.
        $imstestcase = new enrol_imsenterprise_testcase();
        $imstestcase->imsplugin = enrol_get_plugin('imsenterprise');
        $imstestcase->set_test_config();
        $imstestcase->set_xml_file(false, array($course));

        // Capture the event.
        $sink = $this->redirectEvents();
        $imstestcase->imsplugin->cron();
        $events = $sink->get_events();
        $sink->close();
        $event = $events[0];

        // Validate the event triggered is \core\event\course_created. There is no need to validate the other values
        // as they have already been validated in the previous steps. Here we only want to make sure that when the
        // imsenterprise plugin creates a course an event is triggered.
        $this->assertInstanceOf('\core\event\course_created', $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_updated event works as expected.
     */
    public function test_course_updated_event() {
        global $DB;

        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a category we are going to move this course to.
        $category = $this->getDataGenerator()->create_category();

        // Create a hidden category we are going to move this course to.
        $categoryhidden = $this->getDataGenerator()->create_category(array('visible' => 0));

        // Update course and catch course_updated event.
        $sink = $this->redirectEvents();
        update_course($course);
        $events = $sink->get_events();
        $sink->close();

        // Get updated course information from the DB.
        $updatedcourse = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);
        // Validate event.
        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_updated', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($updatedcourse->id, $event->objectid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $url = new moodle_url('/course/edit.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($updatedcourse, $event->get_record_snapshot('course', $event->objectid));
        $this->assertEquals('course_updated', $event->get_legacy_eventname());
        $this->assertEventLegacyData($updatedcourse, $event);
        $expectedlog = array($updatedcourse->id, 'course', 'update', 'edit.php?id=' . $course->id, $course->id);
        $this->assertEventLegacyLogData($expectedlog, $event);

        // Move course and catch course_updated event.
        $sink = $this->redirectEvents();
        move_courses(array($course->id), $category->id);
        $events = $sink->get_events();
        $sink->close();

        // Return the moved course information from the DB.
        $movedcourse = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);
        // Validate event.
        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_updated', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($movedcourse->id, $event->objectid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($movedcourse, $event->get_record_snapshot('course', $movedcourse->id));
        $this->assertEquals('course_updated', $event->get_legacy_eventname());
        $this->assertEventLegacyData($movedcourse, $event);
        $expectedlog = array($movedcourse->id, 'course', 'move', 'edit.php?id=' . $movedcourse->id, $movedcourse->id);
        $this->assertEventLegacyLogData($expectedlog, $event);

        // Move course to hidden category and catch course_updated event.
        $sink = $this->redirectEvents();
        move_courses(array($course->id), $categoryhidden->id);
        $events = $sink->get_events();
        $sink->close();

        // Return the moved course information from the DB.
        $movedcoursehidden = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);
        // Validate event.
        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_updated', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($movedcoursehidden->id, $event->objectid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($movedcoursehidden, $event->get_record_snapshot('course', $movedcoursehidden->id));
        $this->assertEquals('course_updated', $event->get_legacy_eventname());
        $this->assertEventLegacyData($movedcoursehidden, $event);
        $expectedlog = array($movedcoursehidden->id, 'course', 'move', 'edit.php?id=' . $movedcoursehidden->id, $movedcoursehidden->id);
        $this->assertEventLegacyLogData($expectedlog, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_deleted event works as expected.
     */
    public function test_course_deleted_event() {
        $this->resetAfterTest();

        // Create the course.
        $course = $this->getDataGenerator()->create_course();

        // Save the course context before we delete the course.
        $coursecontext = context_course::instance($course->id);

        // Catch the update event.
        $sink = $this->redirectEvents();

        // Call delete_course() which will trigger the course_deleted event and the course_content_deleted
        // event. This function prints out data to the screen, which we do not want during a PHPUnit test,
        // so use ob_start and ob_end_clean to prevent this.
        ob_start();
        delete_course($course);
        ob_end_clean();

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\course_deleted', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($course->id, $event->objectid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($course, $event->get_record_snapshot('course', $course->id));
        $this->assertEquals('course_deleted', $event->get_legacy_eventname());
        $eventdata = $event->get_data();
        $this->assertSame($course->idnumber, $eventdata['other']['idnumber']);
        $this->assertSame($course->fullname, $eventdata['other']['fullname']);
        $this->assertSame($course->shortname, $eventdata['other']['shortname']);

        // The legacy data also passed the context in the course object and substitutes timemodified with the current date.
        $expectedlegacy = clone($course);
        $expectedlegacy->context = $coursecontext;
        $expectedlegacy->timemodified = $event->timecreated;
        $this->assertEventLegacyData($expectedlegacy, $event);

        // Validate legacy log data.
        $expectedlog = array(SITEID, 'course', 'delete', 'view.php?id=' . $course->id, $course->fullname . '(ID ' . $course->id . ')');
        $this->assertEventLegacyLogData($expectedlog, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_content_deleted event works as expected.
     */
    public function test_course_content_deleted_event() {
        global $DB;

        $this->resetAfterTest();

        // Create the course.
        $course = $this->getDataGenerator()->create_course();

        // Get the course from the DB. The data generator adds some extra properties, such as
        // numsections, to the course object which will fail the assertions later on.
        $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);

        // Save the course context before we delete the course.
        $coursecontext = context_course::instance($course->id);

        // Catch the update event.
        $sink = $this->redirectEvents();

        remove_course_contents($course->id, false);

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\course_content_deleted', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($course->id, $event->objectid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($course, $event->get_record_snapshot('course', $course->id));
        $this->assertEquals('course_content_removed', $event->get_legacy_eventname());
        // The legacy data also passed the context and options in the course object.
        $course->context = $coursecontext;
        $course->options = array();
        $this->assertEventLegacyData($course, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_category_deleted event works as expected.
     */
    public function test_course_category_deleted_event() {
        $this->resetAfterTest();

        // Create a category.
        $category = $this->getDataGenerator()->create_category();

        // Save the context before it is deleted.
        $categorycontext = context_coursecat::instance($category->id);

        // Catch the update event.
        $sink = $this->redirectEvents();

        // Delete the category.
        $category->delete_full();

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\course_category_deleted', $event);
        $this->assertEquals('course_categories', $event->objecttable);
        $this->assertEquals($category->id, $event->objectid);
        $this->assertEquals($categorycontext->id, $event->contextid);
        $this->assertEquals('course_category_deleted', $event->get_legacy_eventname());
        $this->assertEquals(null, $event->get_url());
        $this->assertEventLegacyData($category, $event);
        $expectedlog = array(SITEID, 'category', 'delete', 'index.php', $category->name . '(ID ' . $category->id . ')');
        $this->assertEventLegacyLogData($expectedlog, $event);

        // Create two categories.
        $category = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        // Save the context before it is moved and then deleted.
        $category2context = context_coursecat::instance($category2->id);

        // Catch the update event.
        $sink = $this->redirectEvents();

        // Move the category.
        $category2->delete_move($category->id);

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\course_category_deleted', $event);
        $this->assertEquals('course_categories', $event->objecttable);
        $this->assertEquals($category2->id, $event->objectid);
        $this->assertEquals($category2context->id, $event->contextid);
        $this->assertEquals('course_category_deleted', $event->get_legacy_eventname());
        $this->assertEventLegacyData($category2, $event);
        $expectedlog = array(SITEID, 'category', 'delete', 'index.php', $category2->name . '(ID ' . $category2->id . ')');
        $this->assertEventLegacyLogData($expectedlog, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_restored event works as expected.
     */
    public function test_course_restored_event() {
        global $CFG;

        // Get the necessary files to perform backup and restore.
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $this->resetAfterTest();

        // Set to admin user.
        $this->setAdminUser();

        // The user id is going to be 2 since we are the admin user.
        $userid = 2;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create backup file and save it to the backup location.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $userid);
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course-event';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Now we want to catch the restore course event.
        $sink = $this->redirectEvents();

        // Now restore the course to trigger the event.
        $rc = new restore_controller('test-restore-course-event', $course->id, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $userid, backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\course_restored', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($rc->get_courseid(), $event->objectid);
        $this->assertEquals(context_course::instance($rc->get_courseid())->id, $event->contextid);
        $this->assertEquals('course_restored', $event->get_legacy_eventname());
        $legacydata = (object) array(
            'courseid' => $rc->get_courseid(),
            'userid' => $rc->get_userid(),
            'type' => $rc->get_type(),
            'target' => $rc->get_target(),
            'mode' => $rc->get_mode(),
            'operation' => $rc->get_operation(),
            'samesite' => $rc->is_samesite()
        );
        $url = new moodle_url('/course/view.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventLegacyData($legacydata, $event);
        $this->assertEventContextNotUsed($event);

        // Destroy the resource controller since we are done using it.
        $rc->destroy();
    }

    /**
     * Test that triggering a course_section_updated event works as expected.
     */
    public function test_course_section_updated_event() {
        global $DB;

        $this->resetAfterTest();

        // Create the course with sections.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10), array('createsections' => true));
        $sections = $DB->get_records('course_sections', array('course' => $course->id));

        $coursecontext = context_course::instance($course->id);

        $section = array_pop($sections);
        $section->name = 'Test section';
        $section->summary = 'Test section summary';
        $DB->update_record('course_sections', $section);

        // Trigger an event for course section update.
        $event = \core\event\course_section_updated::create(
                array(
                    'objectid' => $section->id,
                    'courseid' => $course->id,
                    'context' => context_course::instance($course->id),
                    'other' => array(
                        'sectionnum' => $section->section
                    )
                )
            );
        $event->add_record_snapshot('course_sections', $section);
        // Trigger and catch event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\course_section_updated', $event);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($section->section, $event->other['sectionnum']);
        $expecteddesc = "The user with id '{$event->userid}' updated section number '{$event->other['sectionnum']}' for the course with id '{$event->courseid}'";
        $this->assertEquals($expecteddesc, $event->get_description());
        $url = new moodle_url('/course/editsection.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($section, $event->get_record_snapshot('course_sections', $event->objectid));
        $id = $section->id;
        $sectionnum = $section->section;
        $expectedlegacydata = array($course->id, "course", "editsection", 'editsection.php?id=' . $id, $sectionnum);
        $this->assertEventLegacyLogData($expectedlegacydata, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_section_deleted event works as expected.
     */
    public function test_course_section_deleted_event() {
        global $USER, $DB;
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // Create the course with sections.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10), array('createsections' => true));
        $sections = $DB->get_records('course_sections', array('course' => $course->id), 'section');
        $coursecontext = context_course::instance($course->id);
        $section = array_pop($sections);
        course_delete_section($course, $section);
        $events = $sink->get_events();
        $event = array_pop($events); // Delete section event.
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\course_section_deleted', $event);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($section->section, $event->other['sectionnum']);
        $expecteddesc = "The user with id '{$event->userid}' deleted section number '{$event->other['sectionnum']}' " .
                "(section name '{$event->other['sectionname']}') for the course with id '{$event->courseid}'";
        $this->assertEquals($expecteddesc, $event->get_description());
        $this->assertEquals($section, $event->get_record_snapshot('course_sections', $event->objectid));
        $this->assertNull($event->get_url());

        // Test legacy data.
        $sectionnum = $section->section;
        $expectedlegacydata = array($course->id, "course", "delete section", 'view.php?id=' . $course->id, $sectionnum);
        $this->assertEventLegacyLogData($expectedlegacydata, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_course_integrity_check() {
        global $DB;

        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course(array('numsections' => 1),
           array('createsections'=>true));

        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
                array('section' => 0));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id),
                array('section' => 0));
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id),
                array('section' => 0));
        $correctseq = join(',', array($forum->cmid, $page->cmid, $quiz->cmid));

        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($correctseq, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$page->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);
        $this->assertEmpty(course_integrity_check($course->id));

        // Now let's make manual change in DB and let course_integrity_check() fix it:

        // 1. Module appears twice in one section.
        $DB->update_record('course_sections', array('id' => $section0->id, 'sequence' => $section0->sequence. ','. $page->cmid));
        $this->assertEquals(
                array('Failed integrity check for course ['. $course->id.
                ']. Sequence for course section ['. $section0->id. '] is "'.
                $section0->sequence. ','. $page->cmid. '", must be "'.
                $section0->sequence. '"'),
                course_integrity_check($course->id));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($correctseq, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$page->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 2. Module appears in two sections (last section wins).
        $DB->update_record('course_sections', array('id' => $section1->id, 'sequence' => ''. $page->cmid));
        // First message about double mentioning in sequence, second message about wrong section field for $page.
        $this->assertEquals(array(
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] must be removed from sequence of section ['. $section0->id.
            '] because it is also present in sequence of section ['. $section1->id. ']',
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] points to section ['. $section0->id. '] instead of ['. $section1->id. ']'),
                course_integrity_check($course->id));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEquals(''. $page->cmid, $section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section1->id, $cms[$page->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 3. Module id is not present in course_section.sequence (integrity check with $fullcheck = false).
        $DB->update_record('course_sections', array('id' => $section1->id, 'sequence' => ''));
        $this->assertEmpty(course_integrity_check($course->id)); // Not an error!
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section1->id, $cms[$page->cmid]->section); // Not changed.
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 4. Module id is not present in course_section.sequence (integrity check with $fullcheck = true).
        $this->assertEquals(array('Failed integrity check for course ['. $course->id. ']. Course module ['.
                $page->cmid. '] is missing from sequence of section ['. $section1->id. ']'),
                course_integrity_check($course->id, null, null, true)); // Error!
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEquals(''. $page->cmid, $section1->sequence);  // Yay, module added to section.
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section1->id, $cms[$page->cmid]->section); // Not changed.
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 5. Module id is not present in course_section.sequence and it's section is invalid (integrity check with $fullcheck = true).
        $DB->update_record('course_modules', array('id' => $page->cmid, 'section' => 8765));
        $DB->update_record('course_sections', array('id' => $section1->id, 'sequence' => ''));
        $this->assertEquals(array(
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] is missing from sequence of section ['. $section0->id. ']',
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] points to section [8765] instead of ['. $section0->id. ']'),
                course_integrity_check($course->id, null, null, true));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid. ','. $page->cmid, $section0->sequence); // Module added to section.
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$page->cmid]->section); // Section changed to section0.
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 6. Module is deleted from course_modules but not deleted in sequence (integrity check with $fullcheck = true).
        $DB->delete_records('course_modules', array('id' => $page->cmid));
        $this->assertEquals(array('Failed integrity check for course ['. $course->id. ']. Course module ['.
                $page->cmid. '] does not exist but is present in the sequence of section ['. $section0->id. ']'),
                course_integrity_check($course->id, null, null, true));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);
        $this->assertEquals(2, count($cms));
    }

    /**
     * Tests for event related to course module creation.
     */
    public function test_course_module_created_event() {
        global $USER, $DB;
        $this->resetAfterTest();

        // Create an assign module.
        $sink = $this->redirectEvents();
        $modinfo = $this->create_specific_module_test('assign');
        $events = $sink->get_events();
        $event = array_pop($events);

        $cm = get_coursemodule_from_id('assign', $modinfo->coursemodule, 0, false, MUST_EXIST);
        $mod = $DB->get_record('assign', array('id' => $modinfo->instance), '*', MUST_EXIST);

        // Validate event data.
        $this->assertInstanceOf('\core\event\course_module_created', $event);
        $this->assertEquals($cm->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_modules', $event->objecttable);
        $url = new moodle_url('/mod/assign/view.php', array('id' => $cm->id));
        $this->assertEquals($url, $event->get_url());

        // Test legacy data.
        $this->assertSame('mod_created', $event->get_legacy_eventname());
        $eventdata = new stdClass();
        $eventdata->modulename = 'assign';
        $eventdata->name       = $mod->name;
        $eventdata->cmid       = $cm->id;
        $eventdata->courseid   = $cm->course;
        $eventdata->userid     = $USER->id;
        $this->assertEventLegacyData($eventdata, $event);

        $arr = array(
            array($cm->course, "course", "add mod", "../mod/assign/view.php?id=$cm->id", "assign $cm->instance"),
            array($cm->course, "assign", "add", "view.php?id=$cm->id", $cm->instance, $cm->id)
        );
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEventContextNotUsed($event);

        // Let us see if duplicating an activity results in a nice course module created event.
        $sink->clear();
        $course = get_course($mod->course);
        $newcm = duplicate_module($course, $cm);
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\course_module_created', $event);
        $this->assertEquals($newcm->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($course->id, $event->courseid);
        $url = new moodle_url('/mod/assign/view.php', array('id' => $newcm->id));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Tests for event validations related to course module creation.
     */
    public function test_course_module_created_event_exceptions() {

        $this->resetAfterTest();

        // Generate data.
        $modinfo = $this->create_specific_module_test('assign');
        $context = context_module::instance($modinfo->coursemodule);

        // Test not setting instanceid.
        try {
            $event = \core\event\course_module_created::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'modulename' => 'assign',
                    'name'       => 'My assignment',
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_created to be triggered without
                    other['instanceid']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'instanceid' value must be set in other.", $e->getMessage());
        }

        // Test not setting modulename.
        try {
            $event = \core\event\course_module_created::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'instanceid' => $modinfo->instance,
                    'name'       => 'My assignment',
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_created to be triggered without
                    other['modulename']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'modulename' value must be set in other.", $e->getMessage());
        }

        // Test not setting name.

        try {
            $event = \core\event\course_module_created::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'modulename' => 'assign',
                    'instanceid' => $modinfo->instance,
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_created to be triggered without
                    other['name']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'name' value must be set in other.", $e->getMessage());
        }

    }

    /**
     * Tests for event related to course module updates.
     */
    public function test_course_module_updated_event() {
        global $USER, $DB;
        $this->resetAfterTest();

        // Update a forum module.
        $sink = $this->redirectEvents();
        $modinfo = $this->update_specific_module_test('forum');
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();

        $cm = $DB->get_record('course_modules', array('id' => $modinfo->coursemodule), '*', MUST_EXIST);
        $mod = $DB->get_record('forum', array('id' => $cm->instance), '*', MUST_EXIST);

        // Validate event data.
        $this->assertInstanceOf('\core\event\course_module_updated', $event);
        $this->assertEquals($cm->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_modules', $event->objecttable);
        $url = new moodle_url('/mod/forum/view.php', array('id' => $cm->id));
        $this->assertEquals($url, $event->get_url());

        // Test legacy data.
        $this->assertSame('mod_updated', $event->get_legacy_eventname());
        $eventdata = new stdClass();
        $eventdata->modulename = 'forum';
        $eventdata->name       = $mod->name;
        $eventdata->cmid       = $cm->id;
        $eventdata->courseid   = $cm->course;
        $eventdata->userid     = $USER->id;
        $this->assertEventLegacyData($eventdata, $event);

        $arr = array(
            array($cm->course, "course", "update mod", "../mod/forum/view.php?id=$cm->id", "forum $cm->instance"),
            array($cm->course, "forum", "update", "view.php?id=$cm->id", $cm->instance, $cm->id)
        );
        $this->assertEventLegacyLogData($arr, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for create_from_cm method.
     */
    public function test_course_module_create_from_cm() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and modules.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5));

        // Generate an assignment.
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        // Get the module context.
        $modcontext = context_module::instance($assign->cmid);

        // Get course module.
        $cm = get_coursemodule_from_id(null, $assign->cmid, $course->id, false, MUST_EXIST);

        // Create an event from course module.
        $event = \core\event\course_module_updated::create_from_cm($cm, $modcontext);

        // Trigger the events.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event2 = array_pop($events);

        // Test event data.
        $this->assertInstanceOf('\core\event\course_module_updated', $event);
        $this->assertEquals($cm->id, $event2->objectid);
        $this->assertEquals($modcontext, $event2->get_context());
        $this->assertEquals($cm->modname, $event2->other['modulename']);
        $this->assertEquals($cm->instance, $event2->other['instanceid']);
        $this->assertEquals($cm->name, $event2->other['name']);
        $this->assertEventContextNotUsed($event2);
        $this->assertSame('mod_updated', $event2->get_legacy_eventname());
        $arr = array(
            array($cm->course, "course", "update mod", "../mod/assign/view.php?id=$cm->id", "assign $cm->instance"),
            array($cm->course, "assign", "update", "view.php?id=$cm->id", $cm->instance, $cm->id)
        );
        $this->assertEventLegacyLogData($arr, $event);
    }

    /**
     * Tests for event validations related to course module update.
     */
    public function test_course_module_updated_event_exceptions() {

        $this->resetAfterTest();

        // Generate data.
        $modinfo = $this->create_specific_module_test('assign');
        $context = context_module::instance($modinfo->coursemodule);

        // Test not setting instanceid.
        try {
            $event = \core\event\course_module_updated::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'modulename' => 'assign',
                    'name'       => 'My assignment',
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_updated to be triggered without
                    other['instanceid']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'instanceid' value must be set in other.", $e->getMessage());
        }

        // Test not setting modulename.
        try {
            $event = \core\event\course_module_updated::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'instanceid' => $modinfo->instance,
                    'name'       => 'My assignment',
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_updated to be triggered without
                    other['modulename']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'modulename' value must be set in other.", $e->getMessage());
        }

        // Test not setting name.

        try {
            $event = \core\event\course_module_updated::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'modulename' => 'assign',
                    'instanceid' => $modinfo->instance,
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_updated to be triggered without
                    other['name']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'name' value must be set in other.", $e->getMessage());
        }

    }

    /**
     * Tests for event related to course module delete.
     */
    public function test_course_module_deleted_event() {
        global $USER, $DB;
        $this->resetAfterTest();

        // Create and delete a module.
        $sink = $this->redirectEvents();
        $modinfo = $this->create_specific_module_test('forum');
        $cm = $DB->get_record('course_modules', array('id' => $modinfo->coursemodule), '*', MUST_EXIST);
        course_delete_module($modinfo->coursemodule);
        $events = $sink->get_events();
        $event = array_pop($events); // delete module event.;
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\course_module_deleted', $event);
        $this->assertEquals($cm->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_modules', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($cm, $event->get_record_snapshot('course_modules', $cm->id));

        // Test legacy data.
        $this->assertSame('mod_deleted', $event->get_legacy_eventname());
        $eventdata = new stdClass();
        $eventdata->modulename = 'forum';
        $eventdata->cmid       = $cm->id;
        $eventdata->courseid   = $cm->course;
        $eventdata->userid     = $USER->id;
        $this->assertEventLegacyData($eventdata, $event);

        $arr = array($cm->course, 'course', "delete mod", "view.php?id=$cm->course", "forum $cm->instance", $cm->id);
        $this->assertEventLegacyLogData($arr, $event);

    }

    /**
     * Tests for event validations related to course module deletion.
     */
    public function test_course_module_deleted_event_exceptions() {

        $this->resetAfterTest();

        // Generate data.
        $modinfo = $this->create_specific_module_test('assign');
        $context = context_module::instance($modinfo->coursemodule);

        // Test not setting instanceid.
        try {
            $event = \core\event\course_module_deleted::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'modulename' => 'assign',
                    'name'       => 'My assignment',
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_deleted to be triggered without
                    other['instanceid']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'instanceid' value must be set in other.", $e->getMessage());
        }

        // Test not setting modulename.
        try {
            $event = \core\event\course_module_deleted::create(array(
                'courseid' => $modinfo->course,
                'context'  => $context,
                'objectid' => $modinfo->coursemodule,
                'other'    => array(
                    'instanceid' => $modinfo->instance,
                    'name'       => 'My assignment',
                )
            ));
            $this->fail("Event validation should not allow \\core\\event\\course_module_deleted to be triggered without
                    other['modulename']");
        } catch (coding_exception $e) {
            $this->assertContains("The 'modulename' value must be set in other.", $e->getMessage());
        }
    }

    /**
     * Returns a user object and its assigned new role.
     *
     * @param testing_data_generator $generator
     * @param $contextid
     * @return array The user object and the role ID
     */
    protected function get_user_objects(testing_data_generator $generator, $contextid) {
        global $USER;

        if (empty($USER->id)) {
            $user  = $generator->create_user();
            $this->setUser($user);
        }
        $roleid = create_role('Test role', 'testrole', 'Test role description');
        if (!is_array($contextid)) {
            $contextid = array($contextid);
        }
        foreach ($contextid as $cid) {
            $assignid = role_assign($roleid, $user->id, $cid);
        }
        return array($user, $roleid);
    }

    /**
     * Test course move after course.
     */
    public function test_course_change_sortorder_after_course() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course3 = $generator->create_course(array('category' => $category->id));
        $course2 = $generator->create_course(array('category' => $category->id));
        $course1 = $generator->create_course(array('category' => $category->id));
        $context = $category->get_context();

        list($user, $roleid) = $this->get_user_objects($generator, $context->id);
        $caps = course_capability_assignment::allow('moodle/category:manage', $roleid, $context->id);

        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving down.
        $this->assertTrue(course_change_sortorder_after_course($course1->id, $course3->id));
        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course2->id, $course3->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving up.
        $this->assertTrue(course_change_sortorder_after_course($course1->id, $course2->id));
        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving to the top.
        $this->assertTrue(course_change_sortorder_after_course($course1->id, 0));
        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));
    }

    /**
     * Tests changing the visibility of a course.
     */
    public function test_course_change_visibility() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course = $generator->create_course(array('category' => $category->id));

        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        $this->assertTrue(course_change_visibility($course->id, false));
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals('0', $course->visible);
        $this->assertEquals('0', $course->visibleold);

        $this->assertTrue(course_change_visibility($course->id, true));
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);
    }

    /**
     * Tests moving the course up and down by one.
     */
    public function test_course_change_sortorder_by_one() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course3 = $generator->create_course(array('category' => $category->id));
        $course2 = $generator->create_course(array('category' => $category->id));
        $course1 = $generator->create_course(array('category' => $category->id));

        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving down.
        $course1 = get_course($course1->id);
        $this->assertTrue(course_change_sortorder_by_one($course1, false));
        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving up.
        $course1 = get_course($course1->id);
        $this->assertTrue(course_change_sortorder_by_one($course1, true));
        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving the top course up one.
        $course1 = get_course($course1->id);
        $this->assertFalse(course_change_sortorder_by_one($course1, true));
        // Check nothing changed.
        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving the bottom course up down.
        $course3 = get_course($course3->id);
        $this->assertFalse(course_change_sortorder_by_one($course3, false));
        // Check nothing changed.
        $courses = $category->get_courses();
        $this->assertInternalType('array', $courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));
    }

    public function test_view_resources_list() {
        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $event = \core\event\course_resources_list_viewed::create(array('context' => context_course::instance($course->id)));
        $event->set_legacy_logdata(array('book', 'page', 'resource'));
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\course_resources_list_viewed', $event);
        $this->assertEquals(null, $event->objecttable);
        $this->assertEquals(null, $event->objectid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $expectedlegacydata = array(
            array($course->id, "book", "view all", 'index.php?id=' . $course->id, ''),
            array($course->id, "page", "view all", 'index.php?id=' . $course->id, ''),
            array($course->id, "resource", "view all", 'index.php?id=' . $course->id, ''),
        );
        $this->assertEventLegacyLogData($expectedlegacydata, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test duplicate_module()
     */
    public function test_duplicate_module() {
        $this->setAdminUser();
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $res = self::getDataGenerator()->create_module('resource', array('course' => $course));
        $cm = get_coursemodule_from_id('resource', $res->cmid, 0, false, MUST_EXIST);

        $newcm = duplicate_module($course, $cm);

        // Make sure they are the same, except obvious id changes.
        foreach ($cm as $prop => $value) {
            if ($prop == 'id' || $prop == 'url' || $prop == 'instance' || $prop == 'added') {
                // Ignore obviously different properties.
                continue;
            }
            $this->assertEquals($value, $newcm->$prop);
        }
    }

    /**
     * Tests that when creating or updating a module, if the availability settings
     * are present but set to an empty tree, availability is set to null in
     * database.
     */
    public function test_empty_availability_settings() {
        global $DB;
        $this->setAdminUser();
        $this->resetAfterTest();

        // Enable availability.
        set_config('enableavailability', 1);

        // Test add.
        $emptyavailability = json_encode(\core_availability\tree::get_root_json(array()));
        $course = self::getDataGenerator()->create_course();
        $label = self::getDataGenerator()->create_module('label', array(
                'course' => $course, 'availability' => $emptyavailability));
        $this->assertNull($DB->get_field('course_modules', 'availability',
                array('id' => $label->cmid)));

        // Test update.
        $formdata = $DB->get_record('course_modules', array('id' => $label->cmid));
        unset($formdata->availability);
        $formdata->availabilityconditionsjson = $emptyavailability;
        $formdata->modulename = 'label';
        $formdata->coursemodule = $label->cmid;
        $draftid = 0;
        file_prepare_draft_area($draftid, context_module::instance($label->cmid)->id,
                'mod_label', 'intro', 0);
        $formdata->introeditor = array(
            'itemid' => $draftid,
            'text' => '<p>Yo</p>',
            'format' => FORMAT_HTML);
        update_module($formdata);
        $this->assertNull($DB->get_field('course_modules', 'availability',
                array('id' => $label->cmid)));
    }

    /**
     * Test update_inplace_editable()
     */
    public function test_update_module_name_inplace() {
        global $CFG, $DB, $PAGE;
        require_once($CFG->dirroot . '/lib/external/externallib.php');

        $this->setUser($this->getDataGenerator()->create_user());

        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $forum = self::getDataGenerator()->create_module('forum', array('course' => $course->id, 'name' => 'forum name'));

        // Call service for core_course component without necessary permissions.
        try {
            core_external::update_inplace_editable('core_course', 'activityname', $forum->cmid, 'New forum name');
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertEquals('Course or activity not accessible. (Not enrolled)',
                $e->getMessage());
        }

        // Change to admin user and make sure that cm name can be updated using web service update_inplace_editable().
        $this->setAdminUser();
        $res = core_external::update_inplace_editable('core_course', 'activityname', $forum->cmid, 'New forum name');
        $res = external_api::clean_returnvalue(core_external::update_inplace_editable_returns(), $res);
        $this->assertEquals('New forum name', $res['value']);
        $this->assertEquals('New forum name', $DB->get_field('forum', 'name', array('id' => $forum->id)));
    }

    /**
     * Testing function course_get_tagged_course_modules - search tagged course modules
     */
    public function test_course_get_tagged_course_modules() {
        global $DB;
        $this->resetAfterTest();
        $course3 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();
        $cm11 = $this->getDataGenerator()->create_module('assign', array('course' => $course1->id,
            'tags' => 'Cat, Dog'));
        $cm12 = $this->getDataGenerator()->create_module('page', array('course' => $course1->id,
            'tags' => 'Cat, Mouse', 'visible' => 0));
        $cm13 = $this->getDataGenerator()->create_module('page', array('course' => $course1->id,
            'tags' => 'Cat, Mouse, Dog'));
        $cm21 = $this->getDataGenerator()->create_module('forum', array('course' => $course2->id,
            'tags' => 'Cat, Mouse'));
        $cm31 = $this->getDataGenerator()->create_module('forum', array('course' => $course3->id,
            'tags' => 'Cat, Mouse'));

        // Admin is able to view everything.
        $this->setAdminUser();
        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$page = */0);
        $this->assertRegExp('/'.$cm11->name.'/', $res->content);
        $this->assertRegExp('/'.$cm12->name.'/', $res->content);
        $this->assertRegExp('/'.$cm13->name.'/', $res->content);
        $this->assertRegExp('/'.$cm21->name.'/', $res->content);
        $this->assertRegExp('/'.$cm31->name.'/', $res->content);
        // Results from course1 are returned before results from course2.
        $this->assertTrue(strpos($res->content, $cm11->name) < strpos($res->content, $cm21->name));

        // Ordinary user is not able to see anything.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$page = */0);
        $this->assertNull($res);

        // Enrol user as student in course1 and course2.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($user->id, $course1->id, $roleids['student']);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id, $roleids['student']);
        core_tag_index_builder::reset_caches();

        // Searching in the course context returns visible modules in this course.
        $context = context_course::instance($course1->id);
        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */0, /*$ctx = */$context->id, /*$rec = */1, /*$page = */0);
        $this->assertRegExp('/'.$cm11->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm12->name.'/', $res->content);
        $this->assertRegExp('/'.$cm13->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm21->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm31->name.'/', $res->content);

        // Searching FROM the course context returns visible modules in all courses.
        $context = context_course::instance($course2->id);
        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */$context->id, /*$ctx = */0, /*$rec = */1, /*$page = */0);
        $this->assertRegExp('/'.$cm11->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm12->name.'/', $res->content);
        $this->assertRegExp('/'.$cm13->name.'/', $res->content);
        $this->assertRegExp('/'.$cm21->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm31->name.'/', $res->content); // No access to course3.
        // Results from course2 are returned before results from course1.
        $this->assertTrue(strpos($res->content, $cm21->name) < strpos($res->content, $cm11->name));

        // Enrol user in course1 as a teacher - now he should be able to see hidden module.
        $this->getDataGenerator()->enrol_user($user->id, $course1->id, $roleids['editingteacher']);
        get_fast_modinfo(0,0,true);

        $context = context_course::instance($course1->id);
        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */$context->id, /*$ctx = */0, /*$rec = */1, /*$page = */0);
        $this->assertRegExp('/'.$cm12->name.'/', $res->content);

        // Create more modules and try pagination.
        $cm14 = $this->getDataGenerator()->create_module('assign', array('course' => $course1->id,
            'tags' => 'Cat, Dog'));
        $cm15 = $this->getDataGenerator()->create_module('page', array('course' => $course1->id,
            'tags' => 'Cat, Mouse', 'visible' => 0));
        $cm16 = $this->getDataGenerator()->create_module('page', array('course' => $course1->id,
            'tags' => 'Cat, Mouse, Dog'));

        $context = context_course::instance($course1->id);
        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */0, /*$ctx = */$context->id, /*$rec = */1, /*$page = */0);
        $this->assertRegExp('/'.$cm11->name.'/', $res->content);
        $this->assertRegExp('/'.$cm12->name.'/', $res->content);
        $this->assertRegExp('/'.$cm13->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm21->name.'/', $res->content);
        $this->assertRegExp('/'.$cm14->name.'/', $res->content);
        $this->assertRegExp('/'.$cm15->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm16->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm31->name.'/', $res->content); // No access to course3.
        $this->assertEmpty($res->prevpageurl);
        $this->assertNotEmpty($res->nextpageurl);

        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */0, /*$ctx = */$context->id, /*$rec = */1, /*$page = */1);
        $this->assertNotRegExp('/'.$cm11->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm12->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm13->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm21->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm14->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm15->name.'/', $res->content);
        $this->assertRegExp('/'.$cm16->name.'/', $res->content);
        $this->assertNotRegExp('/'.$cm31->name.'/', $res->content); // No access to course3.
        $this->assertNotEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);
    }

    /**
     * Test test_update_course_frontpage_category.
     */
    public function test_update_course_frontpage_category() {
        // Fetch front page course.
        $course = get_course(SITEID);
        // Test update information on front page course.
        $course->category = 99;
        $this->setExpectedException('moodle_exception');
        update_course($course);
    }
}
