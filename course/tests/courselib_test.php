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
require_once($CFG->dirroot.'/course/lib.php');

class courselib_testcase extends advanced_testcase {

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
        $moduleinfo->trackingtype = FORUM_TRACKING_ON;
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
        // The goal not being to fully test assign_add_instance() we'll stop here for the assign tests - to avoid too many DB queries.

        // Advanced grading.
        $contextmodule = context_module::instance($dbmodinstance->id);
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
        $moduleinfo->groupmembersonly = 0;
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
        $moduleinfo->availablefrom = time();
        $moduleinfo->availableuntil = time() + (7 * 24 * 3600);
        $moduleinfo->showavailability = CONDITION_STUDENTVIEW_SHOW;
        $coursegradeitem = grade_item::fetch_course_item($moduleinfo->course); //the activity will become available only when the user reach some grade into the course itself.
        $moduleinfo->conditiongradegroup = array(array('conditiongradeitemid' => $coursegradeitem->id, 'conditiongrademin' => 10, 'conditiongrademax' => 80));
        $moduleinfo->conditionfieldgroup = array(array('conditionfield' => 'email', 'conditionfieldoperator' => OP_CONTAINS, 'conditionfieldvalue' => '@'));
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
        $this->assertEquals($moduleinfo->groupmembersonly, $dbcm->groupmembersonly);
        $this->assertEquals($moduleinfo->visible, $dbcm->visible);
        $this->assertEquals($moduleinfo->completion, $dbcm->completion);
        $this->assertEquals($moduleinfo->completionview, $dbcm->completionview);
        $this->assertEquals($moduleinfo->completiongradeitemnumber, $dbcm->completiongradeitemnumber);
        $this->assertEquals($moduleinfo->completionexpected, $dbcm->completionexpected);
        $this->assertEquals($moduleinfo->availablefrom, $dbcm->availablefrom);
        $this->assertEquals($moduleinfo->availableuntil, $dbcm->availableuntil);
        $this->assertEquals($moduleinfo->showavailability, $dbcm->showavailability);
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

        // Common values when conditional activity is enabled.
        foreach ($moduleinfo->conditionfieldgroup as $fieldgroup) {
            $isfieldgroupsaved = $DB->count_records('course_modules_avail_fields', array('coursemoduleid' => $dbcm->id,
                'userfield' => $fieldgroup['conditionfield'], 'operator' => $fieldgroup['conditionfieldoperator'],
                'value' => $fieldgroup['conditionfieldvalue']));
            $this->assertEquals(1, $isfieldgroupsaved);
        }
        foreach ($moduleinfo->conditiongradegroup as $gradegroup) {
            $isgradegroupsaved = $DB->count_records('course_modules_availability', array('coursemoduleid' => $dbcm->id,
                'grademin' => $gradegroup['conditiongrademin'], 'grademax' => $gradegroup['conditiongrademax'],
                'gradeitemid' => $gradegroup['conditiongradeitemid']));
            $this->assertEquals(1, $isgradegroupsaved);
        }
        foreach ($moduleinfo->conditioncompletiongroup as $completiongroup) {
            $iscompletiongroupsaved = $DB->count_records('course_modules_availability', array('coursemoduleid' => $dbcm->id,
                'sourcecmid' => $completiongroup['conditionsourcecmid'], 'requiredcompletion' => $completiongroup['conditionrequiredcompletion']));
            $this->assertEquals(1, $iscompletiongroupsaved);
        }

        // Test specific to the module.
        $modulerunasserts = $modulename.'_create_run_asserts';
        $this->$modulerunasserts($moduleinfo, $dbmodinstance);
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
        $moduleinfo->trackingtype = FORUM_TRACKING_ON;
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
        $moduleinfo->groupmembersonly = 0;
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
        $moduleinfo->availablefrom = time();
        $moduleinfo->availableuntil = time() + (7 * 24 * 3600);
        $moduleinfo->showavailability = CONDITION_STUDENTVIEW_SHOW;
        $coursegradeitem = grade_item::fetch_course_item($moduleinfo->course); //the activity will become available only when the user reach some grade into the course itself.
        $moduleinfo->conditiongradegroup = array(array('conditiongradeitemid' => $coursegradeitem->id, 'conditiongrademin' => 10, 'conditiongrademax' => 80));
        $moduleinfo->conditionfieldgroup = array(array('conditionfield' => 'email', 'conditionfieldoperator' => OP_CONTAINS, 'conditionfieldvalue' => '@'));
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
        $this->assertEquals($moduleinfo->groupmembersonly, $dbcm->groupmembersonly);
        $this->assertEquals($moduleinfo->visible, $dbcm->visible);
        $this->assertEquals($moduleinfo->completion, $dbcm->completion);
        $this->assertEquals($moduleinfo->completionview, $dbcm->completionview);
        $this->assertEquals($moduleinfo->completiongradeitemnumber, $dbcm->completiongradeitemnumber);
        $this->assertEquals($moduleinfo->completionexpected, $dbcm->completionexpected);
        $this->assertEquals($moduleinfo->availablefrom, $dbcm->availablefrom);
        $this->assertEquals($moduleinfo->availableuntil, $dbcm->availableuntil);
        $this->assertEquals($moduleinfo->showavailability, $dbcm->showavailability);
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

        // Common values when conditional activity is enabled.
        foreach ($moduleinfo->conditionfieldgroup as $fieldgroup) {
            $isfieldgroupsaved = $DB->count_records('course_modules_avail_fields', array('coursemoduleid' => $dbcm->id,
                'userfield' => $fieldgroup['conditionfield'], 'operator' => $fieldgroup['conditionfieldoperator'],
                'value' => $fieldgroup['conditionfieldvalue']));
            $this->assertEquals(1, $isfieldgroupsaved);
        }
        foreach ($moduleinfo->conditiongradegroup as $gradegroup) {
            $isgradegroupsaved = $DB->count_records('course_modules_availability', array('coursemoduleid' => $dbcm->id,
                'grademin' => $gradegroup['conditiongrademin'], 'grademax' => $gradegroup['conditiongrademax'],
                'gradeitemid' => $gradegroup['conditiongradeitemid']));
            $this->assertEquals(1, $isgradegroupsaved);
        }
        foreach ($moduleinfo->conditioncompletiongroup as $completiongroup) {
            $iscompletiongroupsaved = $DB->count_records('course_modules_availability', array('coursemoduleid' => $dbcm->id,
                'sourcecmid' => $completiongroup['conditionsourcecmid'], 'requiredcompletion' => $completiongroup['conditionrequiredcompletion']));
            $this->assertEquals(1, $iscompletiongroupsaved);
        }

        // Test specific to the module.
        $modulerunasserts = $modulename.'_update_run_asserts';
        $this->$modulerunasserts($moduleinfo, $dbmodinstance);
   }


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

        // Ensure all 6 (0-5) sections were created and modinfo/sectioninfo cache works properly
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
        $defaultcategory = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");

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
            $this->assertEquals(get_string('idnumbertaken', 'error'), $e->getMessage());
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
        course_add_cm_to_section($course->id, $cmids[1], 1);
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0] . ',' . $cmids[1], $sequence);

        // Check modinfo was not rebuilt (important for performance if calling
        // repeatedly).
        $this->assertNull($DB->get_field('course', 'modinfo', array('id' => $course->id)));

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

        // reset of get_fast_modinfo is usually called the code calling moveto_module so call it here
        get_fast_modinfo(0, 0, true);
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
        $result = moveto_module($cm, $newsection);
        $this->assertTrue($result);

        // reset of get_fast_modinfo is usually called the code calling moveto_module so call it here
        get_fast_modinfo(0, 0, true);
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

    public function test_section_visibility() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create course.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 3), array('createsections' => true));

        // Testing an empty section.
        $sectionnumber = 1;
        set_section_visible($course->id, $sectionnumber, 0);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 0);
        set_section_visible($course->id, $sectionnumber, 1);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 1);

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

        // Move the forum and the page to a hidden section.
        moveto_module($forumcm, $hiddensection);
        moveto_module($pagecm, $hiddensection);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);

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
        $visiblesection = $modinfo->get_section_info(2);
        moveto_module($forumcm, $visiblesection);
        moveto_module($pagecm, $visiblesection);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);
        $modinfo = get_fast_modinfo($course);

        // Verify that forum has been made visible.
        $forumcm = $modinfo->cms[$forum->cmid];
        $this->assertEquals($forumcm->visible, 1);

        // Verify that page has stayed invisible.
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);

        // Move the page in the same section (this is what mod duplicate does_
        moveto_module($pagecm, $visiblesection, $forumcm);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);

        // Verify that the the page is still hidden
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


        // Move the forum and the page to a hidden section.
        moveto_module($pagecm, $section, $forumcm);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);

        // Verify that the the page is still hidden
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
    }
}
