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

namespace core_course;

use advanced_testcase;
use backup_controller;
use backup;
use blog_entry;
use cache;
use calendar_event;
use coding_exception;
use comment;
use completion_criteria_date;
use completion_completion;
use context_course;
use context_module;
use context_system;
use context_coursecat;
use core\event\section_viewed;
use core_completion_external;
use core_external;
use core_tag_index_builder;
use core_tag_tag;
use course_capability_assignment;
use course_request;
use core_course_category;
use enrol_imsenterprise\imsenterprise_test;
use core_external\external_api;
use grade_item;
use grading_manager;
use moodle_exception;
use moodle_url;
use phpunit_util;
use rating_manager;
use restore_controller;
use stdClass;
use testing_data_generator;

defined('MOODLE_INTERNAL') or die();

// Require library globally because it's constants are used within dataProvider methods, executed before setUpBeforeClass.
global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Course related unit tests
 *
 * @package    core_course
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class courselib_test extends advanced_testcase {

    /**
     * Load required libraries and fixtures.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/course/tests/fixtures/course_capability_assignment.php');
        require_once($CFG->dirroot . '/enrol/imsenterprise/tests/imsenterprise_test.php');
        parent::setUpBeforeClass();
    }

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

        // Grading of whole forum settings.
        $moduleinfo->grade_forum = 0;
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
        $moduleinfo->gradingduedate = time() + (7 * 24 * 3600);
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
        $moduleinfo->visibleoncoursepage = true;

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
        $moduleinfo->completionpassgrade = 0;
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
        $this->assertEquals($moduleinfo->completionpassgrade, $dbcm->completionpassgrade);
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
     * Create module associated blog and tags.
     *
     * @param object $course Course.
     * @param object $modulecontext The context of the module.
     */
    private function create_module_asscociated_blog($course, $modulecontext) {
        global $DB, $CFG;

        // Create default group.
        $group = new stdClass();
        $group->courseid = $course->id;
        $group->name = 'Group';
        $group->id = $DB->insert_record('groups', $group);

        // Create default user.
        $user = $this->getDataGenerator()->create_user(array(
            'username' => 'testuser',
            'firstname' => 'Firsname',
            'lastname' => 'Lastname'
        ));

        // Create default post.
        $post = new stdClass();
        $post->userid = $user->id;
        $post->groupid = $group->id;
        $post->content = 'test post content text';
        $post->module = 'blog';
        $post->id = $DB->insert_record('post', $post);

        // Create default tag.
        $tag = $this->getDataGenerator()->create_tag(array('userid' => $user->id,
            'rawname' => 'Testtagname', 'isstandard' => 1));
        // Apply the tag to the blog.
        $DB->insert_record('tag_instance', array('tagid' => $tag->id, 'itemtype' => 'user',
            'component' => 'core', 'itemid' => $post->id, 'ordering' => 0));

        require_once($CFG->dirroot . '/blog/locallib.php');
        $blog = new blog_entry($post->id);
        $blog->add_association($modulecontext->id);

        return $blog;
    }

    /**
     * Test create_module() for multiple modules defined in the $modules array (first declaration of the function).
     */
    public function test_create_module(): void {
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
    public function test_update_module(): void {
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

        // Grading of whole forum settings.
        $moduleinfo->grade_forum = 0;
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
        $moduleinfo->visibleoncoursepage = true;

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
        $moduleinfo->completionpassgrade = 0;
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
        $this->assertEquals($moduleinfo->completionpassgrade, $dbcm->completionpassgrade);
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
    public static function provider_course_delete_module(): array {
        $data = array();

        $data['assign'] = array('assign', array('duedate' => time()));
        $data['quiz'] = array('quiz', array('duedate' => time()));

        return $data;
    }

    /**
     * Test the create_course function
     */
    public function test_create_course(): void {
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
        $course->category = $defaultcategory;
        $original = (array) $course;

        $created = create_course($course);
        $context = context_course::instance($created->id);

        // Compare original and created.
        $this->assertEquals($original, array_intersect_key((array) $created, $original));

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $created->id, 'section' => 0));
        $this->assertTrue($sectioncreated);

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

    public function test_create_course_with_generator(): void {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $course->id, 'section' => 0));
        $this->assertTrue($sectioncreated);
    }

    public function test_create_course_sections(): void {
        global $DB;
        $this->resetAfterTest(true);

        $numsections = 5;
        $course = $this->getDataGenerator()->create_course(
                array('shortname' => 'GrowingCourse',
                    'fullname' => 'Growing Course',
                    'numsections' => $numsections),
                array('createsections' => true));

        // Ensure all 6 (0-5) sections were created and course content cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $numsections), $sectionscreated);

        // this will do nothing, section already exists
        $this->assertFalse(course_create_sections_if_missing($course, $numsections));

        // this will create new section
        $this->assertTrue(course_create_sections_if_missing($course, $numsections + 1));

        // Ensure all 7 (0-6) sections were created and modinfo/sectioninfo cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $numsections + 1), $sectionscreated);
    }

    public function test_update_course(): void {
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

    public function test_update_course_section_time_modified(): void {
        global $DB;

        $this->resetAfterTest();

        // Create the course with sections.
        $course = $this->getDataGenerator()->create_course(
            ['numsections' => 10],
            ['createsections' => true]
        );
        $sections = $DB->get_records('course_sections', ['course' => $course->id]);

        // Get the last section's time modified value.
        $section = array_pop($sections);
        $oldtimemodified = $section->timemodified;

        // Ensuring that the section update occurs at a different timestamp.
        $this->waitForSecond();

        // The timemodified should only be updated if the section is actually updated.
        course_update_section($course, $section, []);
        $sectionrecord = $DB->get_record('course_sections', ['id' => $section->id]);
        $this->assertEquals($oldtimemodified, $sectionrecord->timemodified);

        // Now update something to prove timemodified changes.
        course_update_section($course, $section, ['name' => 'New name']);
        $section = $DB->get_record('course_sections', ['id' => $section->id]);
        $newtimemodified = $section->timemodified;
        $this->assertGreaterThan($oldtimemodified, $newtimemodified);
    }

    /**
     * Relative dates mode settings provider for course creation.
     */
    public static function create_course_relative_dates_provider(): array {
        return [
            [0, 0, 0],
            [0, 1, 0],
            [1, 0, 0],
            [1, 1, 1],
        ];
    }

    /**
     * Test create_course by attempting to change the relative dates mode.
     *
     * @dataProvider create_course_relative_dates_provider
     * @param int $setting The value for the 'enablecourserelativedates' admin setting.
     * @param int $mode The value for the course's 'relativedatesmode' field.
     * @param int $expectedvalue The expected value of the 'relativedatesmode' field after course creation.
     */
    public function test_relative_dates_mode_for_course_creation($setting, $mode, $expectedvalue): void {
        global $DB;

        $this->resetAfterTest();

        set_config('enablecourserelativedates', $setting);

        // Generate a course with relative dates mode set to $mode.
        $course = $this->getDataGenerator()->create_course(['relativedatesmode' => $mode]);

        // Verify that the relative dates match what's expected.
        $relativedatesmode = $DB->get_field('course', 'relativedatesmode', ['id' => $course->id]);
        $this->assertEquals($expectedvalue, $relativedatesmode);
    }

    /**
     * Test update_course by attempting to change the relative dates mode.
     */
    public function test_relative_dates_mode_for_course_update(): void {
        global $DB;

        $this->resetAfterTest();

        set_config('enablecourserelativedates', 1);

        // Generate a course with relative dates mode set to 1.
        $course = $this->getDataGenerator()->create_course(['relativedatesmode' => 1]);

        // Attempt to update the course with a changed relativedatesmode.
        $course->relativedatesmode = 0;
        update_course($course);

        // Verify that the relative dates mode has not changed.
        $relativedatesmode = $DB->get_field('course', 'relativedatesmode', ['id' => $course->id]);
        $this->assertEquals(1, $relativedatesmode);
    }

    public function test_course_add_cm_to_section(): void {
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
        $mods = $DB->get_records('modules');
        $mod = reset($mods);
        $cmids = array();
        for ($i=0; $i<4; $i++) {
            $cmids[$i] = $DB->insert_record('course_modules', ['course' => $course->id, 'module' => $mod->id]);
        }

        // Add it to section that exists.
        course_add_cm_to_section($course, $cmids[0], 1, null, $mod->name);

        // Check it got added to sequence.
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0], $sequence);

        // Add a second, this time using courseid variant of parameters.
        $coursecacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        course_add_cm_to_section($course->id, $cmids[1], 1, null, $mod->name);
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0] . ',' . $cmids[1], $sequence);

        // Check that modinfo cache was reset but not rebuilt (important for performance if calling repeatedly).
        $newcacherev = $DB->get_field('course', 'cacherev', ['id' => $course->id]);
        $this->assertGreaterThan($coursecacherev, $newcacherev);
        $this->assertEmpty(cache::make('core', 'coursemodinfo')->get_versioned($course->id, $newcacherev));

        // Add one to section that doesn't exist (this might rebuild modinfo).
        course_add_cm_to_section($course, $cmids[2], 2, null, $mod->name);
        $this->assertEquals(3, $DB->count_records('course_sections', array('course' => $course->id)));
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 2));
        $this->assertEquals($cmids[2], $sequence);

        // Add using the 'before' option.
        course_add_cm_to_section($course, $cmids[3], 2, $cmids[2], $mod->name);
        $this->assertEquals(3, $DB->count_records('course_sections', array('course' => $course->id)));
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 2));
        $this->assertEquals($cmids[3] . ',' . $cmids[2], $sequence);
    }

    /**
     * Module types that have FEATURE_CAN_DISPLAY flag set to false cannot be in any section other than 0.
     *
     * @return void
     * @covers ::course_add_cm_to_section()
     */
    public function test_add_non_display_types_to_cm_section(): void {
        global $DB;

        $this->resetAfterTest(true);
        $generator = self::getDataGenerator();

        // Create course with 1 section.
        $course = self::getDataGenerator()->create_course(
            [
                'shortname' => 'GrowingCourse',
                'fullname' => 'Growing Course',
                'numsections' => 1,
            ],
            ['createsections' => true]
        );

        // Create the module and assert in section 0.
        $sectionzero = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 0], '*', MUST_EXIST);
        $module = $generator->create_module('qbank', ['course' => $course, 'section' => $sectionzero->section]);

        // Try to add to section 1.
        $this->expectExceptionMessage("Modules with FEATURE_CAN_DISPLAY set to false can not be moved from section 0");

        try {
            course_add_cm_to_section($course, $module->cmid, 1, null, 'qbank');
        } finally {
            // Assert still in section 0.
            $cm = $DB->get_record('course_modules', ['id' => $module->cmid]);
            $modsection = $DB->get_record('course_sections', ['id' => $cm->section]);
            $this->assertEquals($sectionzero->section, $modsection->section);
        }
    }

    public function test_reorder_sections(): void {
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

    public function test_move_section_down(): void {
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

    public function test_move_section_up(): void {
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

    public function test_move_section_marker(): void {
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

        // Verify that the course marker has been moved along with the section..
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

    /**
     * Test move_section_to method with caching
     *
     * @covers ::move_section_to
     * @return void
     */
    public function test_move_section_with_section_cache(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite module.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 3], ['createsections' => true]);
        // Reset course cache.
        rebuild_course_cache($course->id, true);

        // Build course cache.
        $modinfo = get_fast_modinfo($course->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        $numberedsections = $modinfo->get_section_info_all();

        // Make sure that we will have 4 section caches here.
        $this->assertCount(4, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[0]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[1]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[2]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[3]->id, $sectioncaches);

        // Move section.
        move_section_to($course, 2, 3);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        // Make sure that we will have 2 section caches left.
        $this->assertCount(2, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[0]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[1]->id, $sectioncaches);
        $this->assertArrayNotHasKey($numberedsections[2]->id, $sectioncaches);
        $this->assertArrayNotHasKey($numberedsections[3]->id, $sectioncaches);
    }

    /**
     * Test move_section_to method.
     * Make sure that we only update the moving sections, not all the sections in the current course.
     *
     * @covers ::move_section_to
     * @return void
     */
    public function test_move_section_to(): void {
        global $DB, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Generate the course and pre-requisite module.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics', 'numsections' => 3], ['createsections' => true]);

        ob_start();
        $DB->set_debug(true);
        // Move section.
        move_section_to($course, 2, 3);
        $DB->set_debug(false);
        $debuginfo = ob_get_contents();
        ob_end_clean();
        $sectionmovequerycount = substr_count($debuginfo, 'UPDATE ' . $CFG->phpunit_prefix . 'course_sections SET');
        // We are updating the course_section table in steps to avoid breaking database uniqueness constraint.
        // So the queries will be doubled. See: course/lib.php:1423
        // Make sure that we only need 4 queries to update the position of section 2 and section 3.
        $this->assertEquals(4, $sectionmovequerycount);
    }

    public function test_course_can_delete_section(): void {
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
        $this->assertFalse(course_can_delete_section($courseweeks, 1));
        $this->assertTrue(course_can_delete_section($courseweeks, 2));

        // Student does not have permissions to delete sections.
        $this->setUser($student);
        $this->assertFalse(course_can_delete_section($courseweeks, 1));
        $this->assertFalse(course_can_delete_section($coursetopics, 1));
        $this->assertFalse(course_can_delete_section($coursesingleactivity, 1));
    }

    public function test_course_delete_section(): void {
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
        $this->assertEquals(5, course_get_format($course)->get_last_section_number());

        // Delete empty section.
        $this->assertTrue(course_delete_section($course, 4, false));
        $this->assertEquals(4, course_get_format($course)->get_last_section_number());

        // Delete section in the middle (2).
        $this->assertFalse(course_delete_section($course, 2, false));
        $this->assertEquals(4, course_get_format($course)->get_last_section_number());
        $this->assertTrue(course_delete_section($course, 2, true));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign21->cmid)));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign22->cmid)));
        $this->assertEquals(3, course_get_format($course)->get_last_section_number());
        $this->assertEquals(array(0 => array($assign0->cmid),
            1 => array($assign1->cmid),
            2 => array($assign3->cmid),
            3 => array($assign5->cmid)), get_fast_modinfo($course)->sections);

        // Remove marked section.
        course_set_marker($course->id, 1);
        $this->assertTrue(course_get_format($course)->is_section_current(1));
        $this->assertTrue(course_delete_section($course, 1, true));
        $this->assertFalse(course_get_format($course)->is_section_current(1));
    }

    public function test_get_course_display_name_for_list(): void {
        global $CFG;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('shortname' => 'FROG101', 'fullname' => 'Introduction to pond life'));

        $CFG->courselistshortnames = 0;
        $this->assertEquals('Introduction to pond life', get_course_display_name_for_list($course));

        $CFG->courselistshortnames = 1;
        $this->assertEquals('FROG101 Introduction to pond life', get_course_display_name_for_list($course));
    }

    public function test_move_module_in_course(): void {
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

    /**
     * Ensure that qbank module which has feature flag FEATURE_CAN_DISPLAY set to false cannot be moved from section 0.
     *
     * @return void
     * @covers ::moveto_module()
     */
    public function test_move_feature_cannot_display(): void {
        $this->resetAfterTest(true);
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course(['numsections' => 5], ['createsections' => true]);
        $qbank = $this->getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbankcms = get_fast_modinfo($course)->get_instances_of('qbank');
        $qbankcm = reset($qbankcms);

        // Check that mods with FEATURE_CAN_DISPLAY set to false cannot be moved from section 0.
        $newsection = get_fast_modinfo($course)->get_section_info(3);

        $codingerror = "/ .* Modules with FEATURE_CAN_DISPLAY set to false can not be moved from section 0/";

        // Try to perform the move.
        $this->expectExceptionMessageMatches($codingerror);
        try {
            moveto_module($qbankcm, $newsection);
        } finally {
            $qbankcms = get_fast_modinfo($course)->get_instances_of('qbank');
            $qbankcm = reset($qbankcms);
            $this->assertEquals(0, $qbankcm->sectionnum);
        }
    }

    public function test_module_visibility(): void {
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

    /**
     * Test rebuildcache = false behaviour.
     *
     * When we pass rebuildcache = false to set_coursemodule_visible, the corusemodinfo cache will still contain
     * the original visibility until we trigger a rebuild.
     *
     * @return void
     * @covers ::set_coursemodule_visible
     */
    public function test_module_visibility_no_rebuild(): void {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create course and modules.
        $course = $this->getDataGenerator()->create_course(['numsections' => 5]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $assign = $this->getDataGenerator()->create_module('assign', ['duedate' => time(), 'course' => $course->id]);
        $modules = compact('forum', 'assign');

        // Hiding the modules.
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 0, 1, false);
            // The modinfo cache still has the original visibility until we manually trigger a rebuild.
            $cm = get_fast_modinfo($mod->course)->get_cm($mod->cmid);
            $this->assertEquals(1, $cm->visible);
        }

        rebuild_course_cache($course->id);

        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 0, 0);
        }

        // Showing the modules.
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 1, 1, false);
            $cm = get_fast_modinfo($mod->course)->get_cm($mod->cmid);
            $this->assertEquals(0, $cm->visible);
        }

        rebuild_course_cache($course->id);

        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 1, 1);
        }
    }

    public function test_section_visibility_events(): void {
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

    public function test_section_visibility(): void {
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

    public function test_course_page_type_list(): void {
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

    public function test_compare_activities_by_time_desc(): void {

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

    public function test_compare_activities_by_time_asc(): void {

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
    public function test_moveto_module_between_hidden_sections(): void {
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
        $this->assertContainsEquals($forum->cmid, $modinfo->sections[3]);
        $this->assertContainsEquals($page->cmid, $modinfo->sections[3]);
        $this->assertNotContainsEquals($quiz->cmid, $modinfo->sections[3]);

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
    public function test_moveto_module_in_same_section(): void {
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
    public function test_course_delete_module($type, $options): void {
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

        $assocblog = $this->create_module_asscociated_blog($course, $modcontext);

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
                $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
                $qgen->create_question('shortanswer', null, array('category' => $qcat->id));
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

        // Verify the blog_association record has been deleted.
        $this->assertCount(0, $DB->get_records('blog_association',
                array('contextid' => $modcontext->id)));

        // Verify the blog post record has been deleted.
        $this->assertCount(0, $DB->get_records('post',
                array('id' => $assocblog->id)));

        // Verify the tag instance record has been deleted.
        $this->assertCount(0, $DB->get_records('tag_instance',
                array('itemid' => $assocblog->id)));

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
                $criteria = [$qcat->id];
                $sql = 'SELECT COUNT(q.id)
                          FROM {question} q
                          JOIN {question_versions} qv ON qv.questionid = q.id
                          JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                          WHERE qbe.questioncategoryid = ?';
                $this->assertEquals(0, $DB->count_records_sql($sql, $criteria));
                break;
            default:
                break;
        }
    }

    /**
     * Test that triggering a course_created event works as expected.
     */
    public function test_course_created_event(): void {
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

        // Now we want to trigger creating a course via the imsenterprise.
        // Delete the course we created earlier, as we want the imsenterprise plugin to create this.
        // We do not want print out any of the text this function generates while doing this, which is why
        // we are using ob_start() and ob_end_clean().
        ob_start();
        delete_course($course);
        ob_end_clean();

        // Create the XML file we want to use.
        $course->category = (array)$course->category;

        // Note: this is a violation of component communication principles.
        // TODO MDL-83789.
        $imstestcase = new imsenterprise_test('courselib_imsenterprise_test');
        $imstestcase->imsplugin = enrol_get_plugin('imsenterprise');
        $imstestcase->set_test_config();
        $imstestcase->set_xml_file(false, array($course));

        // Capture the event.
        $sink = $this->redirectEvents();
        $imstestcase->imsplugin->cron();
        $events = $sink->get_events();
        $sink->close();
        $event = null;
        foreach ($events as $eventinfo) {
            if ($eventinfo instanceof \core\event\course_created ) {
                $event = $eventinfo;
                break;
            }
        }

        // Validate the event triggered is \core\event\course_created. There is no need to validate the other values
        // as they have already been validated in the previous steps. Here we only want to make sure that when the
        // imsenterprise plugin creates a course an event is triggered.
        $this->assertInstanceOf('\core\event\course_created', $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_updated event works as expected.
     */
    public function test_course_updated_event(): void {
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
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_updated event logs changes.
     */
    public function test_course_updated_event_with_changes(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course((object)['visible' => 1]);

        $editedcourse = $DB->get_record('course', ['id' => $course->id]);
        $editedcourse->visible = 0;

        // Update course and catch course_updated event.
        $sink = $this->redirectEvents();
        update_course($editedcourse);
        $events = $sink->get_events();
        $sink->close();

        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_updated', $event);
        $otherdata = [
            'shortname' => $course->shortname,
            'fullname' => $course->fullname,
            'updatedfields' => [
                'visible' => 0
            ]
        ];
        $this->assertEquals($otherdata, $event->other);

    }

    /**
     * Test that triggering a course_deleted event works as expected.
     */
    public function test_course_deleted_event(): void {
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
        $eventdata = $event->get_data();
        $this->assertSame($course->idnumber, $eventdata['other']['idnumber']);
        $this->assertSame($course->fullname, $eventdata['other']['fullname']);
        $this->assertSame($course->shortname, $eventdata['other']['shortname']);

        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_content_deleted event works as expected.
     */
    public function test_course_content_deleted_event(): void {
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
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_category_deleted event works as expected.
     */
    public function test_course_category_deleted_event(): void {
        $this->resetAfterTest();

        // Create a category.
        $category = $this->getDataGenerator()->create_category();

        // Save the original record/context before it is deleted.
        $categoryrecord = $category->get_db_record();
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
        $this->assertEquals([
            'name' => $category->name,
        ], $event->other);
        $this->assertEquals($categoryrecord, $event->get_record_snapshot($event->objecttable, $event->objectid));
        $this->assertEquals(null, $event->get_url());

        // Create two categories.
        $category = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        // Save the original record/context before it is moved and then deleted.
        $category2record = $category2->get_db_record();
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
        $this->assertEquals([
            'name' => $category2->name,
            'contentmovedcategoryid' => $category->id,
        ], $event->other);
        $this->assertEquals($category2record, $event->get_record_snapshot($event->objecttable, $event->objectid));
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_backup_created event works as expected.
     */
    public function test_course_backup_created_event(): void {
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
        $sink = $this->redirectEvents();
        $bc->execute_plan();

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\course_backup_created', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($bc->get_courseid(), $event->objectid);
        $this->assertEquals(context_course::instance($bc->get_courseid())->id, $event->contextid);

        $url = new moodle_url('/course/view.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        // Destroy the resource controller since we are done using it.
        $bc->destroy();
    }

    /**
     * Test that triggering a course_restored event works as expected.
     */
    public function test_course_restored_event(): void {
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
        $this->assertEventContextNotUsed($event);

        // Destroy the resource controller since we are done using it.
        $rc->destroy();
    }

    /**
     * Test that triggering a course_section_updated event works as expected.
     */
    public function test_course_section_updated_event(): void {
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
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_section_deleted event works as expected.
     */
    public function test_course_section_deleted_event(): void {
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
        $this->assertEventContextNotUsed($event);
    }

    public function test_course_integrity_check(): void {
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
    public function test_course_module_created_event(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create an assign module.
        $sink = $this->redirectEvents();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $events = $sink->get_events();
        $eventscount = 0;

        // Validate event data.
        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_created) {
                $eventscount++;

                $this->assertEquals($module->cmid, $event->objectid);
                $this->assertEquals($USER->id, $event->userid);
                $this->assertEquals('course_modules', $event->objecttable);
                $url = new moodle_url('/mod/assign/view.php', array('id' => $module->cmid));
                $this->assertEquals($url, $event->get_url());
                $this->assertEventContextNotUsed($event);
            }
        }
        // Only one \core\event\course_module_created event should be triggered.
        $this->assertEquals(1, $eventscount);

        // Let us see if duplicating an activity results in a nice course module created event.
        $sink->clear();
        $course = get_course($module->course);
        $cm = get_coursemodule_from_id('assign', $module->cmid, 0, false, MUST_EXIST);
        $newcm = duplicate_module($course, $cm);
        $events = $sink->get_events();
        $eventscount = 0;
        $sink->close();

        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_created) {
                $eventscount++;
                // Validate event data.
                $this->assertInstanceOf('\core\event\course_module_created', $event);
                $this->assertEquals($newcm->id, $event->objectid);
                $this->assertEquals($USER->id, $event->userid);
                $this->assertEquals($course->id, $event->courseid);
                $url = new moodle_url('/mod/assign/view.php', array('id' => $newcm->id));
                $this->assertEquals($url, $event->get_url());
            }
        }

        // Only one \core\event\course_module_created event should be triggered.
        $this->assertEquals(1, $eventscount);
    }

    /**
     * Tests for event validations related to course module creation.
     */
    public function test_course_module_created_event_exceptions(): void {

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
            $this->assertStringContainsString("The 'instanceid' value must be set in other.", $e->getMessage());
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
            $this->assertStringContainsString("The 'modulename' value must be set in other.", $e->getMessage());
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
            $this->assertStringContainsString("The 'name' value must be set in other.", $e->getMessage());
        }

    }

    /**
     * Tests for event related to course module updates.
     */
    public function test_course_module_updated_event(): void {
        global $USER, $DB;
        $this->resetAfterTest();

        // Update a forum module.
        $sink = $this->redirectEvents();
        $modinfo = $this->update_specific_module_test('forum');
        $events = $sink->get_events();
        $eventscount = 0;
        $sink->close();

        $cm = $DB->get_record('course_modules', array('id' => $modinfo->coursemodule), '*', MUST_EXIST);
        $mod = $DB->get_record('forum', array('id' => $cm->instance), '*', MUST_EXIST);

        // Validate event data.
        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_updated) {
                $eventscount++;

                $this->assertEquals($cm->id, $event->objectid);
                $this->assertEquals($USER->id, $event->userid);
                $this->assertEquals('course_modules', $event->objecttable);
                $url = new moodle_url('/mod/forum/view.php', array('id' => $cm->id));
                $this->assertEquals($url, $event->get_url());
                $this->assertEventContextNotUsed($event);
            }
        }

        // Only one \core\event\course_module_updated event should be triggered.
        $this->assertEquals(1, $eventscount);
    }

    /**
     * Tests for create_from_cm method.
     */
    public function test_course_module_create_from_cm(): void {
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
    }

    /**
     * Tests for event validations related to course module update.
     */
    public function test_course_module_updated_event_exceptions(): void {

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
            $this->assertStringContainsString("The 'instanceid' value must be set in other.", $e->getMessage());
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
            $this->assertStringContainsString("The 'modulename' value must be set in other.", $e->getMessage());
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
            $this->assertStringContainsString("The 'name' value must be set in other.", $e->getMessage());
        }

    }

    /**
     * Tests for event related to course module delete.
     */
    public function test_course_module_deleted_event(): void {
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
    }

    /**
     * Tests for event validations related to course module deletion.
     */
    public function test_course_module_deleted_event_exceptions(): void {

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
            $this->assertStringContainsString("The 'instanceid' value must be set in other.", $e->getMessage());
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
            $this->assertStringContainsString("The 'modulename' value must be set in other.", $e->getMessage());
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
    public function test_course_change_sortorder_after_course(): void {
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
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving down.
        $this->assertTrue(course_change_sortorder_after_course($course1->id, $course3->id));
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course3->id, $course1->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving up.
        $this->assertTrue(course_change_sortorder_after_course($course1->id, $course2->id));
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving to the top.
        $this->assertTrue(course_change_sortorder_after_course($course1->id, 0));
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));
    }

    /**
     * Tests changing the visibility of a course.
     */
    public function test_course_change_visibility(): void {
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
    public function test_course_change_sortorder_by_one(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course3 = $generator->create_course(array('category' => $category->id));
        $course2 = $generator->create_course(array('category' => $category->id));
        $course1 = $generator->create_course(array('category' => $category->id));

        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving down.
        $course1 = get_course($course1->id);
        $this->assertTrue(course_change_sortorder_by_one($course1, false));
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving up.
        $course1 = get_course($course1->id);
        $this->assertTrue(course_change_sortorder_by_one($course1, true));
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving the top course up one.
        $course1 = get_course($course1->id);
        $this->assertFalse(course_change_sortorder_by_one($course1, true));
        // Check nothing changed.
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving the bottom course up down.
        $course3 = get_course($course3->id);
        $this->assertFalse(course_change_sortorder_by_one($course3, false));
        // Check nothing changed.
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));
    }

    public function test_view_resources_list(): void {
        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $event = \core\event\course_resources_list_viewed::create(array('context' => context_course::instance($course->id)));
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
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test duplicate_module()
     */
    public function test_duplicate_module(): void {
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
            if ($prop == 'name') {
                // We expect ' (copy)' to be added to the original name since MDL-59227.
                $value = get_string('duplicatedmodule', 'moodle', $value);
            }
            $this->assertEquals($value, $newcm->$prop);
        }
    }

    /**
     * Test that permissions are duplicated correctly after duplicate_module().
     * @covers ::duplicate_module
     * @return void
     */
    public function test_duplicate_module_permissions(): void {
        global $DB;
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create course and course module.
        $course = self::getDataGenerator()->create_course();
        $res = self::getDataGenerator()->create_module('assign', ['course' => $course]);
        $cm = get_coursemodule_from_id('assign', $res->cmid, 0, false, MUST_EXIST);
        $cmcontext = \context_module::instance($cm->id);

        // Enrol student user.
        $user = self::getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        // Add capability to original course module.
        assign_capability('gradereport/grader:view', CAP_ALLOW, $roleid, $cmcontext->id);

        // Duplicate module.
        $newcm = duplicate_module($course, $cm);
        $newcmcontext = \context_module::instance($newcm->id);

        // Assert that user still has capability.
        $this->assertTrue(has_capability('gradereport/grader:view', $newcmcontext, $user));

        // Assert that both modules contain the same count of overrides.
        $overrides = $DB->get_records('role_capabilities', ['contextid' => $cmcontext->id]);
        $newoverrides = $DB->get_records('role_capabilities', ['contextid' => $newcmcontext->id]);
        $this->assertEquals(count($overrides), count($newoverrides));
    }

    /**
     * Test that locally assigned roles are duplicated correctly after duplicate_module().
     * @covers ::duplicate_module
     * @return void
     */
    public function test_duplicate_module_role_assignments(): void {
        global $DB;
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create course and course module.
        $course = self::getDataGenerator()->create_course();
        $res = self::getDataGenerator()->create_module('assign', ['course' => $course]);
        $cm = get_coursemodule_from_id('assign', $res->cmid, 0, false, MUST_EXIST);
        $cmcontext = \context_module::instance($cm->id);

        // Enrol student user.
        $user = self::getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        // Assign user a new local role.
        $newroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        role_assign($newroleid, $user->id, $cmcontext->id);

        // Duplicate module.
        $newcm = duplicate_module($course, $cm);
        $newcmcontext = \context_module::instance($newcm->id);

        // Assert that user still has role assigned.
        $this->assertTrue(user_has_role_assignment($user->id, $newroleid, $newcmcontext->id));

        // Assert that both modules contain the same count of overrides.
        $overrides = $DB->get_records('role_assignments', ['contextid' => $cmcontext->id]);
        $newoverrides = $DB->get_records('role_assignments', ['contextid' => $newcmcontext->id]);
        $this->assertEquals(count($overrides), count($newoverrides));
    }

    /**
     * Ensure that modules with the feature flag FEATURE_CAN_DISPLAY set to false cannot be duplicated into a section other than 0.
     * @covers ::duplicate_module()
     */
    public function test_duplicate_cannot_display_mods(): void {
        self::setAdminUser();
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course(['numsections' => 2], ['createsections' => true]);
        $res = self::getDataGenerator()->create_module('qbank', ['course' => $course]);
        $cm = get_coursemodule_from_id('qbank', $res->cmid, 0, false, MUST_EXIST);
        $sectionid = get_fast_modinfo($course)->get_section_info(1)->id;

        $this->expectExceptionMessage("Modules with FEATURE_CAN_DISPLAY set to false can not be moved from section 0");
        duplicate_module($course, $cm, $sectionid);
    }

    /**
     * Tests that when creating or updating a module, if the availability settings
     * are present but set to an empty tree, availability is set to null in
     * database.
     */
    public function test_empty_availability_settings(): void {
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
    public function test_update_module_name_inplace(): void {
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
    public function test_course_get_tagged_course_modules(): void {
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
        $this->assertMatchesRegularExpression('/'.$cm11->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm12->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm13->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm21->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm31->name.'/', $res->content);
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
        $this->assertMatchesRegularExpression('/'.$cm11->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm12->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm13->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm21->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm31->name.'/', $res->content);

        // Searching FROM the course context returns visible modules in all courses.
        $context = context_course::instance($course2->id);
        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */$context->id, /*$ctx = */0, /*$rec = */1, /*$page = */0);
        $this->assertMatchesRegularExpression('/'.$cm11->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm12->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm13->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm21->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm31->name.'/', $res->content); // No access to course3.
        // Results from course2 are returned before results from course1.
        $this->assertTrue(strpos($res->content, $cm21->name) < strpos($res->content, $cm11->name));

        // Enrol user in course1 as a teacher - now he should be able to see hidden module.
        $this->getDataGenerator()->enrol_user($user->id, $course1->id, $roleids['editingteacher']);
        get_fast_modinfo(0,0,true);

        $context = context_course::instance($course1->id);
        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */$context->id, /*$ctx = */0, /*$rec = */1, /*$page = */0);
        $this->assertMatchesRegularExpression('/'.$cm12->name.'/', $res->content);

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
        $this->assertMatchesRegularExpression('/'.$cm11->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm12->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm13->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm21->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm14->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm15->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm16->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm31->name.'/', $res->content); // No access to course3.
        $this->assertEmpty($res->prevpageurl);
        $this->assertNotEmpty($res->nextpageurl);

        $res = course_get_tagged_course_modules(core_tag_tag::get_by_name(0, 'Cat'),
                /*$exclusivemode = */false, /*$fromctx = */0, /*$ctx = */$context->id, /*$rec = */1, /*$page = */1);
        $this->assertDoesNotMatchRegularExpression('/'.$cm11->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm12->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm13->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm21->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm14->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm15->name.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$cm16->name.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$cm31->name.'/', $res->content); // No access to course3.
        $this->assertNotEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);
    }

    /**
     * Test course_get_user_navigation_options for frontpage.
     */
    public function test_course_get_user_navigation_options_for_frontpage(): void {
        global $CFG, $SITE, $DB;
        $this->resetAfterTest();
        $context = context_system::instance();
        $course = clone $SITE;
        $this->setAdminUser();

        $navoptions = course_get_user_navigation_options($context, $course);
        $this->assertTrue($navoptions->blogs);
        $this->assertTrue($navoptions->notes);
        $this->assertTrue($navoptions->participants);
        $this->assertTrue($navoptions->badges);
        $this->assertTrue($navoptions->tags);
        $this->assertFalse($navoptions->search);
        $this->assertTrue($navoptions->competencies);

        // Enable global search now.
        $CFG->enableglobalsearch = 1;
        $navoptions = course_get_user_navigation_options($context, $course);
        $this->assertTrue($navoptions->search);

        // Disable competencies.
        $oldcompetencies = get_config('core_competency', 'enabled');
        set_config('enabled', false, 'core_competency');
        $navoptions = course_get_user_navigation_options($context, $course);
        $this->assertFalse($navoptions->competencies);
        set_config('enabled', $oldcompetencies, 'core_competency');

        // Now try with a standard user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $navoptions = course_get_user_navigation_options($context, $course);
        $this->assertTrue($navoptions->blogs);
        $this->assertFalse($navoptions->notes);
        $this->assertFalse($navoptions->participants);
        $this->assertTrue($navoptions->badges);
        $this->assertTrue($navoptions->tags);
        $this->assertTrue($navoptions->search);
    }

    /**
     * Test course_get_user_navigation_options for managers in a normal course.
     */
    public function test_course_get_user_navigation_options_for_managers(): void {
        global $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $this->setAdminUser();

        $navoptions = course_get_user_navigation_options($context);
        $this->assertTrue($navoptions->blogs);
        $this->assertTrue($navoptions->notes);
        $this->assertTrue($navoptions->participants);
        $this->assertTrue($navoptions->badges);
    }

    /**
     * Test course_get_user_navigation_options for students in a normal course.
     */
    public function test_course_get_user_navigation_options_for_students(): void {
        global $DB, $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);

        $navoptions = course_get_user_navigation_options($context);
        $this->assertTrue($navoptions->blogs);
        $this->assertFalse($navoptions->notes);
        $this->assertTrue($navoptions->participants);
        $this->assertFalse($navoptions->badges);

        // Disable some options.
        $CFG->badges_allowcoursebadges = 0;
        $CFG->enableblogs = 0;
        // Disable view participants capability.
        assign_capability('moodle/course:viewparticipants', CAP_PROHIBIT, $roleid, $context);

        $navoptions = course_get_user_navigation_options($context);
        $this->assertFalse($navoptions->blogs);
        $this->assertFalse($navoptions->notes);
        $this->assertFalse($navoptions->participants);
        $this->assertFalse($navoptions->badges);

        // Re-enable some options to check badges are displayed as expected.
        $CFG->badges_allowcoursebadges = 1;
        assign_capability('moodle/badges:createbadge', CAP_ALLOW, $roleid, $context);

        $navoptions = course_get_user_navigation_options($context);
        $this->assertTrue($navoptions->badges);
    }

    /**
     * Test course_get_user_administration_options for frontpage.
     */
    public function test_course_get_user_administration_options_for_frontpage(): void {
        global $CFG, $SITE;
        $this->resetAfterTest();
        $course = clone $SITE;
        $context = context_course::instance($course->id);
        $this->setAdminUser();

        $adminoptions = course_get_user_administration_options($course, $context);
        $this->assertTrue($adminoptions->update);
        $this->assertTrue($adminoptions->filters);
        $this->assertTrue($adminoptions->reports);
        $this->assertTrue($adminoptions->backup);
        $this->assertTrue($adminoptions->restore);
        $this->assertFalse($adminoptions->files);
        $this->assertFalse($adminoptions->tags);

        // Now try with a standard user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $adminoptions = course_get_user_administration_options($course, $context);
        $this->assertFalse($adminoptions->update);
        $this->assertFalse($adminoptions->filters);
        $this->assertFalse($adminoptions->reports);
        $this->assertFalse($adminoptions->backup);
        $this->assertFalse($adminoptions->restore);
        $this->assertFalse($adminoptions->files);
        $this->assertFalse($adminoptions->tags);

    }

    /**
     * Test course_get_user_administration_options for managers in a normal course.
     */
    public function test_course_get_user_administration_options_for_managers(): void {
        global $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $this->setAdminUser();

        $adminoptions = course_get_user_administration_options($course, $context);
        $this->assertTrue($adminoptions->update);
        $this->assertTrue($adminoptions->filters);
        $this->assertTrue($adminoptions->reports);
        $this->assertTrue($adminoptions->backup);
        $this->assertTrue($adminoptions->restore);
        $this->assertFalse($adminoptions->files);
        $this->assertTrue($adminoptions->tags);
        $this->assertTrue($adminoptions->gradebook);
        $this->assertFalse($adminoptions->outcomes);
        $this->assertTrue($adminoptions->badges);
        $this->assertTrue($adminoptions->import);
        $this->assertTrue($adminoptions->reset);
        $this->assertTrue($adminoptions->roles);
    }

    /**
     * Test course_get_user_administration_options for students in a normal course.
     */
    public function test_course_get_user_administration_options_for_students(): void {
        global $DB, $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);
        $adminoptions = course_get_user_administration_options($course, $context);

        $this->assertFalse($adminoptions->update);
        $this->assertFalse($adminoptions->filters);
        $this->assertFalse($adminoptions->reports);
        $this->assertFalse($adminoptions->backup);
        $this->assertFalse($adminoptions->restore);
        $this->assertFalse($adminoptions->files);
        $this->assertFalse($adminoptions->tags);
        $this->assertFalse($adminoptions->gradebook);
        $this->assertFalse($adminoptions->outcomes);
        $this->assertTrue($adminoptions->badges);
        $this->assertFalse($adminoptions->import);
        $this->assertFalse($adminoptions->reset);
        $this->assertFalse($adminoptions->roles);

        $CFG->enablebadges = false;
        $adminoptions = course_get_user_administration_options($course, $context);
        $this->assertFalse($adminoptions->badges);
    }

    /**
     * Test test_update_course_frontpage_category.
     */
    public function test_update_course_frontpage_category(): void {
        // Fetch front page course.
        $course = get_course(SITEID);
        // Test update information on front page course.
        $course->category = 99;
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('invalidcourse', 'error'));
        update_course($course);
    }

    /**
     * test_course_enddate
     *
     * @dataProvider course_enddate_provider
     * @param int $startdate
     * @param int $enddate
     * @param string $errorcode
     */
    public function test_course_enddate($startdate, $enddate, $errorcode): void {

        $this->resetAfterTest(true);

        $record = array('startdate' => $startdate, 'enddate' => $enddate);
        try {
            $course1 = $this->getDataGenerator()->create_course($record);
            if ($errorcode !== false) {
                $this->fail('Expected exception with "' . $errorcode . '" error code in create_create');
            }
        } catch (moodle_exception $e) {
            if ($errorcode === false) {
                $this->fail('Got "' . $errorcode . '" exception error code and no exception was expected');
            }
            if ($e->errorcode != $errorcode) {
                $this->fail('Got "' . $e->errorcode. '" exception error code and "' . $errorcode . '" was expected');
            }
            return;
        }

        $this->assertEquals($startdate, $course1->startdate);
        $this->assertEquals($enddate, $course1->enddate);
    }

    /**
     * Provider for test_course_enddate.
     *
     * @return array
     */
    public static function course_enddate_provider(): array {
        // Each provided example contains startdate, enddate and the expected exception error code if there is any.
        return [
            [
                111,
                222,
                false
            ], [
                222,
                111,
                'enddatebeforestartdate'
            ], [
                111,
                0,
                false
            ], [
                0,
                222,
                'nostartdatenoenddate'
            ]
        ];
    }


    /**
     * test_course_dates_reset
     *
     * @dataProvider course_dates_reset_provider
     * @param int $startdate
     * @param int $enddate
     * @param int $resetstartdate
     * @param int $resetenddate
     * @param int $resultingstartdate
     * @param int $resultingenddate
     */
    public function test_course_dates_reset($startdate, $enddate, $resetstartdate, $resetenddate, $resultingstartdate, $resultingenddate): void {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_date.php');

        $this->resetAfterTest(true);

        $this->setAdminUser();

        $CFG->enablecompletion = true;

        $this->setTimezone('UTC');

        $record = array('startdate' => $startdate, 'enddate' => $enddate, 'enablecompletion' => 1);
        $originalcourse = $this->getDataGenerator()->create_course($record);
        $coursecriteria = new completion_criteria_date(array('course' => $originalcourse->id, 'timeend' => $startdate + DAYSECS));
        $coursecriteria->insert();

        $activitycompletiondate = $startdate + DAYSECS;
        $data = $this->getDataGenerator()->create_module('data', array('course' => $originalcourse->id),
                        array('completion' => 1, 'completionexpected' => $activitycompletiondate));

        $resetdata = new stdClass();
        $resetdata->id = $originalcourse->id;
        $resetdata->reset_start_date_old = $originalcourse->startdate;
        $resetdata->reset_start_date = $resetstartdate;
        $resetdata->reset_end_date = $resetenddate;
        $resetdata->reset_end_date_old = $record['enddate'];
        reset_course_userdata($resetdata);

        $course = $DB->get_record('course', array('id' => $originalcourse->id));

        $this->assertEquals($resultingstartdate, $course->startdate);
        $this->assertEquals($resultingenddate, $course->enddate);

        $coursecompletioncriteria = completion_criteria_date::fetch(array('course' => $originalcourse->id));
        $this->assertEquals($resultingstartdate + DAYSECS, $coursecompletioncriteria->timeend);

        $this->assertEquals($resultingstartdate + DAYSECS, $DB->get_field('course_modules', 'completionexpected',
            array('id' => $data->cmid)));
    }

    /**
     * Provider for test_course_dates_reset.
     *
     * @return array
     */
    public static function course_dates_reset_provider(): array {

        // Each example contains the following:
        // - course startdate
        // - course enddate
        // - startdate to reset to (false if not reset)
        // - enddate to reset to (false if not reset)
        // - resulting startdate
        // - resulting enddate
        $time = 1445644800;
        return [
            // No date changes.
            [
                $time,
                $time + DAYSECS,
                false,
                false,
                $time,
                $time + DAYSECS
            ],
            // End date changes to a valid value.
            [
                $time,
                $time + DAYSECS,
                false,
                $time + DAYSECS + 111,
                $time,
                $time + DAYSECS + 111
            ],
            // Start date changes to a valid value. End date does not get updated because it does not have value.
            [
                $time,
                0,
                $time + DAYSECS,
                false,
                $time + DAYSECS,
                0
            ],
            // Start date changes to a valid value. End date gets updated accordingly.
            [
                $time,
                $time + DAYSECS,
                $time + WEEKSECS,
                false,
                $time + WEEKSECS,
                $time + WEEKSECS + DAYSECS
            ],
            // Start date and end date change to a valid value.
            [
                $time,
                $time + DAYSECS,
                $time + WEEKSECS,
                $time + YEARSECS,
                $time + WEEKSECS,
                $time + YEARSECS
            ],
            // Time shift is between exact times, not midnight(s) (MDL-65233).
            [
                $time + HOURSECS,
                $time + DAYSECS,
                $time + WEEKSECS + HOURSECS,
                false,
                $time + WEEKSECS + HOURSECS,
                $time + WEEKSECS + DAYSECS,
            ],
        ];
    }

    /**
     * Test reset_course_userdata()
     *    - with reset_roles_overrides enabled
     *    - with selective role unenrolments
     */
    public function test_course_roles_reset(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create test course and user, enrol one in the other.
        $course = $generator->create_course();
        $user = $generator->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'), MUST_EXIST);
        $generator->enrol_user($user->id, $course->id, $roleid);

        // Test case with reset_roles_overrides enabled.
        // Override course so it does NOT allow students 'mod/forum:viewdiscussion'.
        $coursecontext = context_course::instance($course->id);
        assign_capability('mod/forum:viewdiscussion', CAP_PREVENT, $roleid, $coursecontext->id);

        // Check expected capabilities so far.
        $this->assertFalse(has_capability('mod/forum:viewdiscussion', $coursecontext, $user));

        // Oops, preventing student from viewing forums was a mistake, let's reset the course.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->reset_roles_overrides = true;
        reset_course_userdata($resetdata);

        // Check new expected capabilities - override at the course level should be reset.
        $this->assertTrue(has_capability('mod/forum:viewdiscussion', $coursecontext, $user));

        // Test case with selective role unenrolments.
        $roles = array();
        $roles['student'] = $DB->get_field('role', 'id', array('shortname' => 'student'), MUST_EXIST);
        $roles['teacher'] = $DB->get_field('role', 'id', array('shortname' => 'teacher'), MUST_EXIST);

        // We enrol a user with student and teacher roles.
        $generator->enrol_user($user->id, $course->id, $roles['student']);
        $generator->enrol_user($user->id, $course->id, $roles['teacher']);

        // When we reset only student role, we expect to keep teacher role.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->unenrol_users = array($roles['student']);
        reset_course_userdata($resetdata);

        $usersroles = enrol_get_course_users_roles($course->id);
        $this->assertArrayHasKey($user->id, $usersroles);
        $this->assertArrayHasKey($roles['teacher'], $usersroles[$user->id]);
        $this->assertArrayNotHasKey($roles['student'], $usersroles[$user->id]);
        $this->assertCount(1, $usersroles[$user->id]);

        // We reenrol user as student.
        $generator->enrol_user($user->id, $course->id, $roles['student']);

        // When we reset student and teacher roles, we expect no roles left.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->unenrol_users = array($roles['student'], $roles['teacher']);
        reset_course_userdata($resetdata);

        $usersroles = enrol_get_course_users_roles($course->id);
        $this->assertEmpty($usersroles);
    }

    public function test_course_check_module_updates_since(): void {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mod/glossary/lib.php');
        require_once($CFG->dirroot . '/rating/lib.php');
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest(true);

        $CFG->enablecompletion = true;
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $glossary = $this->getDataGenerator()->create_module('glossary', array(
            'course' => $course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1,
            'allowcomments' => 1,
            'assessed' => RATING_AGGREGATE_AVERAGE,
            'scale' => 100
        ));
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $context = context_module::instance($glossary->cmid);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($glossary->cmid);
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        $from = time();

        $teacher = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        assign_capability('mod/glossary:viewanyrating', CAP_ALLOW, $studentrole->id, $context->id, true);

        // Check nothing changed right now.
        $updates = course_check_module_updates_since($cm, $from);
        $this->assertFalse($updates->configuration->updated);
        $this->assertFalse($updates->completion->updated);
        $this->assertFalse($updates->gradeitems->updated);
        $this->assertFalse($updates->comments->updated);
        $this->assertFalse($updates->ratings->updated);
        $this->assertFalse($updates->introfiles->updated);
        $this->assertFalse($updates->outcomes->updated);

        $this->waitForSecond();

        // Do some changes.
        $this->setUser($user);
        $entry = $glossarygenerator->create_content($glossary);

        $this->setUser($teacher);
        // Name.
        set_coursemodule_name($glossary->cmid, 'New name');

        // Add some ratings.
        $rm = new rating_manager();
        $result = $rm->add_rating($cm, $context, 'mod_glossary', 'entry', $entry->id, 100, 50, $user->id, RATING_AGGREGATE_AVERAGE);

        // Change grades.
        $glossary->cmidnumber = $glossary->cmid;
        glossary_update_grades($glossary, $user->id);

        $this->setUser($user);
        // Completion status.
        glossary_view($glossary, $course, $cm, $context, 'letter');

        // Add one comment.
        $args = new stdClass;
        $args->context   = $context;
        $args->course    = $course;
        $args->cm        = $cm;
        $args->area      = 'glossary_entry';
        $args->itemid    = $entry->id;
        $args->client_id = 1;
        $args->component = 'mod_glossary';
        $manager = new comment($args);
        $manager->add('blah blah blah');

        // Check upgrade status.
        $updates = course_check_module_updates_since($cm, $from);
        $this->assertTrue($updates->configuration->updated);
        $this->assertTrue($updates->completion->updated);
        $this->assertTrue($updates->gradeitems->updated);
        $this->assertTrue($updates->comments->updated);
        $this->assertTrue($updates->ratings->updated);
        $this->assertFalse($updates->introfiles->updated);
        $this->assertFalse($updates->outcomes->updated);
    }

    public function test_async_module_deletion_hook_implemented(): void {
        // Async module deletion depends on the 'true' being returned by at least one plugin implementing the hook,
        // 'course_module_adhoc_deletion_recommended'. In core, is implemented by the course recyclebin, which will only return
        // true if the recyclebin plugin is enabled. To make sure async deletion occurs, this test force-enables the recyclebin.
        global $DB, $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Ensure recyclebin is enabled.
        set_config('coursebinenable', true, 'tool_recyclebin');

        // Create course, module and context.
        $course = $this->getDataGenerator()->create_course(['numsections' => 5]);
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $modcontext = context_module::instance($module->cmid);

        // Verify context exists.
        $this->assertInstanceOf('context_module', $modcontext);

        // Check events generated on the course_delete_module call.
        $sink = $this->redirectEvents();

        // Try to delete the module using the async flag.
        course_delete_module($module->cmid, true); // Try to delete the module asynchronously.

        // Verify that no event has been generated yet.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertEmpty($event);

        // Grab the record, in it's final state before hard deletion, for comparison with the event snapshot.
        // We need to do this because the 'deletioninprogress' flag has changed from '0' to '1'.
        $cm = $DB->get_record('course_modules', ['id' => $module->cmid], '*', MUST_EXIST);

        // Verify the course_module is marked as 'deletioninprogress'.
        $this->assertNotEquals($cm, false);
        $this->assertEquals($cm->deletioninprogress, '1');

        // Verify the context has not yet been removed.
        $this->assertEquals($modcontext, context_module::instance($module->cmid, IGNORE_MISSING));

        // Set up a sink to catch the 'course_module_deleted' event.
        $sink = $this->redirectEvents();

        // Now, run the adhoc task which performs the hard deletion.
        phpunit_util::run_all_adhoc_tasks();

        // Fetch and validate the event data.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertInstanceOf('\core\event\course_module_deleted', $event);
        $this->assertEquals($module->cmid, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_modules', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($cm, $event->get_record_snapshot('course_modules', $module->cmid));

        // Verify the context has been removed.
        $this->assertFalse(context_module::instance($module->cmid, IGNORE_MISSING));

        // Verify the course_module record has been deleted.
        $cmcount = $DB->count_records('course_modules', ['id' => $module->cmid]);
        $this->assertEmpty($cmcount);
    }

    public function test_async_module_deletion_hook_not_implemented(): void {
        // Only proceed if we are sure that no plugin is going to advocate async removal of a module. I.e. no plugin returns
        // 'true' from the 'course_module_adhoc_deletion_recommended' hook.
        // In the case of core, only recyclebin implements this hook, and it will only return true if enabled, so disable it.
        global $DB, $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        set_config('coursebinenable', false, 'tool_recyclebin');

        // Non-core plugins might implement the 'course_module_adhoc_deletion_recommended' hook and spoil this test.
        // If at least one plugin still returns true, then skip this test.
        if ($pluginsfunction = get_plugins_with_function('course_module_background_deletion_recommended')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    if ($pluginfunction()) {
                        $this->markTestSkipped();
                    }
                }
            }
        }

        // Create course, module and context.
        $course = $this->getDataGenerator()->create_course(['numsections' => 5]);
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $modcontext = context_module::instance($module->cmid);
        $cm = $DB->get_record('course_modules', ['id' => $module->cmid], '*', MUST_EXIST);

        // Verify context exists.
        $this->assertInstanceOf('context_module', $modcontext);

        // Check events generated on the course_delete_module call.
        $sink = $this->redirectEvents();

        // Try to delete the module using the async flag.
        course_delete_module($module->cmid, true); // Try to delete the module asynchronously.

        // Fetch and validate the event data.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertInstanceOf('\core\event\course_module_deleted', $event);
        $this->assertEquals($module->cmid, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_modules', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($cm, $event->get_record_snapshot('course_modules', $module->cmid));

        // Verify the context has been removed.
        $this->assertFalse(context_module::instance($module->cmid, IGNORE_MISSING));

        // Verify the course_module record has been deleted.
        $cmcount = $DB->count_records('course_modules', ['id' => $module->cmid]);
        $this->assertEmpty($cmcount);
    }

    public function test_async_section_deletion_hook_implemented(): void {
        // Async section deletion (provided section contains modules), depends on the 'true' being returned by at least one plugin
        // implementing the 'course_module_adhoc_deletion_recommended' hook. In core, is implemented by the course recyclebin,
        // which will only return true if the plugin is enabled. To make sure async deletion occurs, this test enables recyclebin.
        global $DB, $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Ensure recyclebin is enabled.
        set_config('coursebinenable', true, 'tool_recyclebin');

        // Create course, module and context.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => 4, 'format' => 'topics'], ['createsections' => true]);
        $assign0 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign1 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign2 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign3 = $generator->create_module('assign', ['course' => $course, 'section' => 0]);

        // Delete empty section. No difference from normal, synchronous behaviour.
        $this->assertTrue(course_delete_section($course, 4, false, true));
        $this->assertEquals(3, course_get_format($course)->get_last_section_number());

        // Delete a module in section 2 (using async). Need to verify this doesn't generate two tasks when we delete
        // the section in the next step.
        course_delete_module($assign2->cmid, true);

        // Confirm that the module is pending deletion in its current section.
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => '2']); // For event comparison.
        $this->assertEquals(true, $DB->record_exists('course_modules', ['id' => $assign2->cmid, 'deletioninprogress' => 1,
                                                     'section' => $section->id]));

        // Now, delete section 2.
        $this->assertFalse(course_delete_section($course, 2, false, true)); // Non-empty section, no forcedelete, so no change.

        $sink = $this->redirectEvents(); // To capture the event.
        $this->assertTrue(course_delete_section($course, 2, true, true));

        // Now, confirm that:
        // a) the section's modules have been flagged for deletion and moved to section 0 and;
        // b) the section has been deleted and;
        // c) course_section_deleted event has been fired. The course_module_deleted events will only fire once they have been
        // removed from section 0 via the adhoc task.

        // Modules should have been flagged for deletion and moved to section 0.
        $sectionid = $DB->get_field('course_sections', 'id', ['course' => $course->id, 'section' => 0]);
        $this->assertEquals(3, $DB->count_records('course_modules', ['section' => $sectionid, 'deletioninprogress' => 1]));

        // Confirm the section has been deleted.
        $this->assertEquals(2, course_get_format($course)->get_last_section_number());

        // Check event fired.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertInstanceOf('\core\event\course_section_deleted', $event);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($section, $event->get_record_snapshot('course_sections', $section->id));

        // Now, run the adhoc task to delete the modules from section 0.
        $sink = $this->redirectEvents(); // To capture the events.
        phpunit_util::run_all_adhoc_tasks();

        // Confirm the modules have been deleted.
        list($insql, $assignids) = $DB->get_in_or_equal([$assign0->cmid, $assign1->cmid, $assign2->cmid]);
        $cmcount = $DB->count_records_select('course_modules', 'id ' . $insql,  $assignids);
        $this->assertEmpty($cmcount);

        // Confirm other modules in section 0 still remain.
        $this->assertEquals(1, $DB->count_records('course_modules', ['id' => $assign3->cmid]));

        // Confirm that events were generated for all 3 of the modules.
        $events = $sink->get_events();
        $sink->close();
        $count = 0;
        while (!empty($events)) {
            $event = array_pop($events);
            if ($event instanceof \core\event\course_module_deleted &&
                in_array($event->objectid, [$assign0->cmid, $assign1->cmid, $assign2->cmid])) {
                $count++;
            }
        }
        $this->assertEquals(3, $count);
    }

    public function test_async_section_deletion_hook_not_implemented(): void {
        // If no plugins advocate async removal, then normal synchronous removal will take place.
        // Only proceed if we are sure that no plugin is going to advocate async removal of a module. I.e. no plugin returns
        // 'true' from the 'course_module_adhoc_deletion_recommended' hook.
        // In the case of core, only recyclebin implements this hook, and it will only return true if enabled, so disable it.
        global $DB, $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        set_config('coursebinenable', false, 'tool_recyclebin');

        // Non-core plugins might implement the 'course_module_adhoc_deletion_recommended' hook and spoil this test.
        // If at least one plugin still returns true, then skip this test.
        if ($pluginsfunction = get_plugins_with_function('course_module_background_deletion_recommended')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    if ($pluginfunction()) {
                        $this->markTestSkipped();
                    }
                }
            }
        }

        // Create course, module and context.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => 4, 'format' => 'topics'], ['createsections' => true]);
        $assign0 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);
        $assign1 = $generator->create_module('assign', ['course' => $course, 'section' => 2]);

        // Delete empty section. No difference from normal, synchronous behaviour.
        $this->assertTrue(course_delete_section($course, 4, false, true));
        $this->assertEquals(3, course_get_format($course)->get_last_section_number());

        // Delete section in the middle (2).
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => '2']); // For event comparison.
        $this->assertFalse(course_delete_section($course, 2, false, true)); // Non-empty section, no forcedelete, so no change.

        $sink = $this->redirectEvents(); // To capture the event.
        $this->assertTrue(course_delete_section($course, 2, true, true));

        // Now, confirm that:
        // a) The section's modules have deleted and;
        // b) the section has been deleted and;
        // c) course_section_deleted event has been fired and;
        // d) course_module_deleted events have both been fired.

        // Confirm modules have been deleted.
        list($insql, $assignids) = $DB->get_in_or_equal([$assign0->cmid, $assign1->cmid]);
        $cmcount = $DB->count_records_select('course_modules', 'id ' . $insql, $assignids);
        $this->assertEmpty($cmcount);

        // Confirm the section has been deleted.
        $this->assertEquals(2, course_get_format($course)->get_last_section_number());

        // Confirm the course_section_deleted event has been generated.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertInstanceOf('\core\event\course_section_deleted', $event);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($section, $event->get_record_snapshot('course_sections', $section->id));

        // Confirm that the course_module_deleted events have both been generated.
        $count = 0;
        while (!empty($events)) {
            $event = array_pop($events);
            if ($event instanceof \core\event\course_module_deleted &&
                in_array($event->objectid, [$assign0->cmid, $assign1->cmid])) {
                $count++;
            }
        }
        $this->assertEquals(2, $count);
    }

    public function test_classify_course_for_timeline(): void {
        global $DB, $CFG;

        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_self.php');

        set_config('enablecompletion', COMPLETION_ENABLED);
        set_config('coursegraceperiodbefore', 0);
        set_config('coursegraceperiodafter', 0);

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create courses for testing.
        $generator = $this->getDataGenerator();
        $future = time() + 3600;
        $past = time() - 3600;
        $futurecourse = $generator->create_course(['startdate' => $future]);
        $pastcourse = $generator->create_course(['startdate' => $past - 60, 'enddate' => $past]);
        $completedcourse = $generator->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $inprogresscourse = $generator->create_course();

        // Set completion rules.
        $criteriadata = new stdClass();
        $criteriadata->id = $completedcourse->id;

        // Self completion.
        $criteriadata->criteria_self = COMPLETION_CRITERIA_TYPE_SELF;
        $class = 'completion_criteria_self';
        $criterion = new $class();
        $criterion->update_config($criteriadata);

        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $futurecourse->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user->id, $pastcourse->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user->id, $completedcourse->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user->id, $inprogresscourse->id, $studentrole->id);

        $this->setUser($user);
        core_completion_external::mark_course_self_completed($completedcourse->id);
        $ccompletion = new completion_completion(array('course' => $completedcourse->id, 'userid' => $user->id));
        $ccompletion->mark_complete();

        // Aggregate the completions.
        $this->assertEquals(COURSE_TIMELINE_PAST, course_classify_for_timeline($pastcourse));
        $this->assertEquals(COURSE_TIMELINE_FUTURE, course_classify_for_timeline($futurecourse));
        $this->assertEquals(COURSE_TIMELINE_PAST, course_classify_for_timeline($completedcourse));
        $this->assertEquals(COURSE_TIMELINE_INPROGRESS, course_classify_for_timeline($inprogresscourse));

        // Test grace period.
        set_config('coursegraceperiodafter', 1);
        set_config('coursegraceperiodbefore', 1);
        $this->assertEquals(COURSE_TIMELINE_INPROGRESS, course_classify_for_timeline($pastcourse));
        $this->assertEquals(COURSE_TIMELINE_INPROGRESS, course_classify_for_timeline($futurecourse));
        $this->assertEquals(COURSE_TIMELINE_PAST, course_classify_for_timeline($completedcourse));
        $this->assertEquals(COURSE_TIMELINE_INPROGRESS, course_classify_for_timeline($inprogresscourse));
    }

    /**
     * Test the main function for updating all calendar events for a module.
     */
    public function test_course_module_calendar_event_update_process(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $completionexpected = time();
        $duedate = time();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $assign = $this->getDataGenerator()->create_module('assign', [
                    'course' => $course,
                    'completionexpected' => $completionexpected,
                    'duedate' => $duedate
                ]);

        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);
        $events = $DB->get_records('event', ['courseid' => $course->id, 'instance' => $assign->id]);
        // Check that both events are using the expected dates.
        foreach ($events as $event) {
            if ($event->eventtype == \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED) {
                $this->assertEquals($completionexpected, $event->timestart);
            }
            if ($event->eventtype == ASSIGN_EVENT_TYPE_DUE) {
                $this->assertEquals($duedate, $event->timestart);
            }
        }

        // We have to manually update the module and the course module.
        $newcompletionexpected = time() + DAYSECS * 60;
        $newduedate = time() + DAYSECS * 45;
        $newmodulename = 'Assign - new name';

        $moduleobject = (object)array('id' => $assign->id, 'duedate' => $newduedate, 'name' => $newmodulename);
        $DB->update_record('assign', $moduleobject);
        $cmobject = (object)array('id' => $cm->id, 'completionexpected' => $newcompletionexpected);
        $DB->update_record('course_modules', $cmobject);

        $assign = $DB->get_record('assign', ['id' => $assign->id]);
        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        course_module_calendar_event_update_process($assign, $cm);

        $events = $DB->get_records('event', ['courseid' => $course->id, 'instance' => $assign->id]);
        // Now check that the details have been updated properly from the function.
        foreach ($events as $event) {
            if ($event->eventtype == \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED) {
                $this->assertEquals($newcompletionexpected, $event->timestart);
                $this->assertEquals(get_string('completionexpectedfor', 'completion', (object)['instancename' => $newmodulename]),
                        $event->name);
            }
            if ($event->eventtype == ASSIGN_EVENT_TYPE_DUE) {
                $this->assertEquals($newduedate, $event->timestart);
                $this->assertEquals(get_string('calendardue', 'assign', $newmodulename), $event->name);
            }
        }
    }

    /**
     * Test the higher level checks for updating calendar events for an instance.
     */
    public function test_course_module_update_calendar_events(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $completionexpected = time();
        $duedate = time();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $assign = $this->getDataGenerator()->create_module('assign', [
                    'course' => $course,
                    'completionexpected' => $completionexpected,
                    'duedate' => $duedate
                ]);

        $cm = get_coursemodule_from_instance('assign', $assign->id, $course->id);

        // Both the instance and cm objects are missing.
        $this->assertFalse(course_module_update_calendar_events('assign'));
        // Just using the assign instance.
        $this->assertTrue(course_module_update_calendar_events('assign', $assign));
        // Just using the course module object.
        $this->assertTrue(course_module_update_calendar_events('assign', null, $cm));
        // Using both the assign instance and the course module object.
        $this->assertTrue(course_module_update_calendar_events('assign', $assign, $cm));
    }

    /**
     * Test the higher level checks for updating calendar events for a module.
     */
    public function test_course_module_bulk_update_calendar_events(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $completionexpected = time();
        $duedate = time();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $course2 = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $assign = $this->getDataGenerator()->create_module('assign', [
                    'course' => $course,
                    'completionexpected' => $completionexpected,
                    'duedate' => $duedate
                ]);

        // No assign instances in this course.
        $this->assertFalse(course_module_bulk_update_calendar_events('assign', $course2->id));
        // No book instances for the site.
        $this->assertFalse(course_module_bulk_update_calendar_events('book'));
        // Update all assign instances.
        $this->assertTrue(course_module_bulk_update_calendar_events('assign'));
        // Update the assign instances for this course.
        $this->assertTrue(course_module_bulk_update_calendar_events('assign', $course->id));
    }

    /**
     * Test that a student can view participants in a course they are enrolled in.
     */
    public function test_course_can_view_participants_as_student(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $this->setUser($user);

        $this->assertTrue(course_can_view_participants($coursecontext));
    }

    /**
     * Test that a student in a course can not view participants on the site.
     */
    public function test_course_can_view_participants_as_student_on_site(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $this->setUser($user);

        $this->assertFalse(course_can_view_participants(context_system::instance()));
    }

    /**
     * Test that an admin can view participants on the site.
     */
    public function test_course_can_view_participants_as_admin_on_site(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        $this->assertTrue(course_can_view_participants(context_system::instance()));
    }

    /**
     * Test teachers can view participants in a course they are enrolled in.
     */
    public function test_course_can_view_participants_as_teacher(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);

        $this->assertTrue(course_can_view_participants($coursecontext));
    }

    /**
     * Check the teacher can still view the participants page without the 'viewparticipants' cap.
     */
    public function test_course_can_view_participants_as_teacher_without_view_participants_cap(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);

        // Disable one of the capabilties.
        assign_capability('moodle/course:viewparticipants', CAP_PROHIBIT, $roleid, $coursecontext);

        // Should still be able to view the page as they have the 'moodle/course:enrolreview' cap.
        $this->assertTrue(course_can_view_participants($coursecontext));
    }

    /**
     * Check the teacher can still view the participants page without the 'moodle/course:enrolreview' cap.
     */
    public function test_course_can_view_participants_as_teacher_without_enrol_review_cap(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);

        // Disable one of the capabilties.
        assign_capability('moodle/course:enrolreview', CAP_PROHIBIT, $roleid, $coursecontext);

        // Should still be able to view the page as they have the 'moodle/course:viewparticipants' cap.
        $this->assertTrue(course_can_view_participants($coursecontext));
    }

    /**
     * Check the teacher can not view the participants page without the required caps.
     */
    public function test_course_can_view_participants_as_teacher_without_required_caps(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);

        // Disable the capabilities.
        assign_capability('moodle/course:viewparticipants', CAP_PROHIBIT, $roleid, $coursecontext);
        assign_capability('moodle/course:enrolreview', CAP_PROHIBIT, $roleid, $coursecontext);

        $this->assertFalse(course_can_view_participants($coursecontext));
    }

    /**
     * Check that an exception is not thrown if we can view the participants page.
     */
    public function test_course_require_view_participants(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $this->setUser($user);

        course_require_view_participants($coursecontext);
    }

    /**
     * Check that an exception is thrown if we can't view the participants page.
     */
    public function test_course_require_view_participants_as_student_on_site(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $this->setUser($user);

        $this->expectException('required_capability_exception');
        course_require_view_participants(context_system::instance());
    }

    /**
     *  Testing the can_download_from_backup_filearea fn.
     */
    public function test_can_download_from_backup_filearea(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $user = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $teacherrole->id);

        // The 'automated' backup area. Downloading from this area requires two capabilities.
        // If the user has only the 'backup:downloadfile' capability.
        unassign_capability('moodle/restore:userinfo', $teacherrole->id, $context);
        assign_capability('moodle/backup:downloadfile', CAP_ALLOW, $teacherrole->id, $context);
        $this->assertFalse(can_download_from_backup_filearea('automated', $context, $user));

        // If the user has only the 'restore:userinfo' capability.
        unassign_capability('moodle/backup:downloadfile', $teacherrole->id, $context);
        assign_capability('moodle/restore:userinfo', CAP_ALLOW, $teacherrole->id, $context);
        $this->assertFalse(can_download_from_backup_filearea('automated', $context, $user));

        // If the user has both capabilities.
        assign_capability('moodle/backup:downloadfile', CAP_ALLOW, $teacherrole->id, $context);
        assign_capability('moodle/restore:userinfo', CAP_ALLOW, $teacherrole->id, $context);
        $this->assertTrue(can_download_from_backup_filearea('automated', $context, $user));

        // Is the user has neither of the capabilities.
        unassign_capability('moodle/backup:downloadfile', $teacherrole->id, $context);
        unassign_capability('moodle/restore:userinfo', $teacherrole->id, $context);
        $this->assertFalse(can_download_from_backup_filearea('automated', $context, $user));

        // The 'course ' and 'backup' backup file areas. These are governed by the same download capability.
        // User has the capability.
        unassign_capability('moodle/restore:userinfo', $teacherrole->id, $context);
        assign_capability('moodle/backup:downloadfile', CAP_ALLOW, $teacherrole->id, $context);
        $this->assertTrue(can_download_from_backup_filearea('course', $context, $user));
        $this->assertTrue(can_download_from_backup_filearea('backup', $context, $user));

        // User doesn't have the capability.
        unassign_capability('moodle/backup:downloadfile', $teacherrole->id, $context);
        $this->assertFalse(can_download_from_backup_filearea('course', $context, $user));
        $this->assertFalse(can_download_from_backup_filearea('backup', $context, $user));

        // A file area that doesn't exist. No permissions, regardless of capabilities.
        assign_capability('moodle/backup:downloadfile', CAP_ALLOW, $teacherrole->id, $context);
        $this->assertFalse(can_download_from_backup_filearea('testing', $context, $user));
    }

    /**
     * Test cases for the course_classify_courses_for_timeline test.
     */
    public static function get_course_classify_courses_for_timeline_test_cases(): array {
        $now = time();
        $day = 86400;

        return [
            'no courses' => [
                'coursesdata' => [],
                'expected' => [
                    COURSE_TIMELINE_PAST => [],
                    COURSE_TIMELINE_FUTURE => [],
                    COURSE_TIMELINE_INPROGRESS => []
                ]
            ],
            'only past' => [
                'coursesdata' => [
                    [
                        'shortname' => 'past1',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ],
                    [
                        'shortname' => 'past2',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => ['past1', 'past2'],
                    COURSE_TIMELINE_FUTURE => [],
                    COURSE_TIMELINE_INPROGRESS => []
                ]
            ],
            'only in progress' => [
                'coursesdata' => [
                    [
                        'shortname' => 'inprogress1',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ],
                    [
                        'shortname' => 'inprogress2',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => [],
                    COURSE_TIMELINE_FUTURE => [],
                    COURSE_TIMELINE_INPROGRESS => ['inprogress1', 'inprogress2']
                ]
            ],
            'only future' => [
                'coursesdata' => [
                    [
                        'shortname' => 'future1',
                        'startdate' => $now + $day
                    ],
                    [
                        'shortname' => 'future2',
                        'startdate' => $now + $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => [],
                    COURSE_TIMELINE_FUTURE => ['future1', 'future2'],
                    COURSE_TIMELINE_INPROGRESS => []
                ]
            ],
            'combination' => [
                'coursesdata' => [
                    [
                        'shortname' => 'past1',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ],
                    [
                        'shortname' => 'past2',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ],
                    [
                        'shortname' => 'inprogress1',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ],
                    [
                        'shortname' => 'inprogress2',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ],
                    [
                        'shortname' => 'future1',
                        'startdate' => $now + $day
                    ],
                    [
                        'shortname' => 'future2',
                        'startdate' => $now + $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => ['past1', 'past2'],
                    COURSE_TIMELINE_FUTURE => ['future1', 'future2'],
                    COURSE_TIMELINE_INPROGRESS => ['inprogress1', 'inprogress2']
                ]
            ],
        ];
    }

    /**
     * Test the course_classify_courses_for_timeline function.
     *
     * @dataProvider get_course_classify_courses_for_timeline_test_cases
     * @param array $coursesdata Courses to create
     * @param array $expected Expected test results.
     */
    public function test_course_classify_courses_for_timeline($coursesdata, $expected): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $courses = array_map(function($coursedata) use ($generator) {
            return $generator->create_course($coursedata);
        }, $coursesdata);

        sort($expected[COURSE_TIMELINE_PAST]);
        sort($expected[COURSE_TIMELINE_FUTURE]);
        sort($expected[COURSE_TIMELINE_INPROGRESS]);

        $results = course_classify_courses_for_timeline($courses);

        $actualpast = array_map(function($result) {
            return $result->shortname;
        }, $results[COURSE_TIMELINE_PAST]);

        $actualfuture = array_map(function($result) {
            return $result->shortname;
        }, $results[COURSE_TIMELINE_FUTURE]);

        $actualinprogress = array_map(function($result) {
            return $result->shortname;
        }, $results[COURSE_TIMELINE_INPROGRESS]);

        sort($actualpast);
        sort($actualfuture);
        sort($actualinprogress);

        $this->assertEquals($expected[COURSE_TIMELINE_PAST], $actualpast);
        $this->assertEquals($expected[COURSE_TIMELINE_FUTURE], $actualfuture);
        $this->assertEquals($expected[COURSE_TIMELINE_INPROGRESS], $actualinprogress);
    }

    /**
     * Test cases for the course_get_enrolled_courses_for_logged_in_user tests.
     */
    public static function get_course_get_enrolled_courses_for_logged_in_user_test_cases(): array {
        $buildexpectedresult = function($limit, $offset) {
            $result = [];
            for ($i = $offset; $i < $offset + $limit; $i++) {
                $result[] = "testcourse{$i}";
            }
            return $result;
        };

        return [
            'zero records' => [
                'dbquerylimit' => 3,
                'totalcourses' => 0,
                'limit' => 0,
                'offset' => 0,
                'expecteddbqueries' => 4,
                'expectedresult' => $buildexpectedresult(0, 0)
            ],
            'less than query limit' => [
                'dbquerylimit' => 3,
                'totalcourses' => 2,
                'limit' => 0,
                'offset' => 0,
                'expecteddbqueries' => 2,
                'expectedresult' => $buildexpectedresult(2, 0)
            ],
            'more than query limit' => [
                'dbquerylimit' => 3,
                'totalcourses' => 7,
                'limit' => 0,
                'offset' => 0,
                'expecteddbqueries' => 4,
                'expectedresult' => $buildexpectedresult(7, 0)
            ],
            'limit less than query limit' => [
                'dbquerylimit' => 3,
                'totalcourses' => 7,
                'limit' => 2,
                'offset' => 0,
                'expecteddbqueries' => 2,
                'expectedresult' => $buildexpectedresult(2, 0)
            ],
            'limit less than query limit with offset' => [
                'dbquerylimit' => 3,
                'totalcourses' => 7,
                'limit' => 2,
                'offset' => 2,
                'expecteddbqueries' => 2,
                'expectedresult' => $buildexpectedresult(2, 2)
            ],
            'limit less than total' => [
                'dbquerylimit' => 3,
                'totalcourses' => 9,
                'limit' => 6,
                'offset' => 0,
                'expecteddbqueries' => 3,
                'expectedresult' => $buildexpectedresult(6, 0)
            ],
            'less results than limit' => [
                'dbquerylimit' => 4,
                'totalcourses' => 9,
                'limit' => 20,
                'offset' => 0,
                'expecteddbqueries' => 4,
                'expectedresult' => $buildexpectedresult(9, 0)
            ],
            'less results than limit exact divisible' => [
                'dbquerylimit' => 3,
                'totalcourses' => 9,
                'limit' => 20,
                'offset' => 0,
                'expecteddbqueries' => 5,
                'expectedresult' => $buildexpectedresult(9, 0)
            ],
            'less results than limit with offset' => [
                'dbquerylimit' => 3,
                'totalcourses' => 9,
                'limit' => 10,
                'offset' => 5,
                'expecteddbqueries' => 3,
                'expectedresult' => $buildexpectedresult(4, 5)
            ],
        ];
    }

    /**
     * Test the course_get_enrolled_courses_for_logged_in_user function.
     *
     * @dataProvider get_course_get_enrolled_courses_for_logged_in_user_test_cases
     * @param int $dbquerylimit Number of records to load per DB request
     * @param int $totalcourses Number of courses to create
     * @param int $limit Maximum number of results to get.
     * @param int $offset Skip this number of results from the start of the result set.
     * @param int $expecteddbqueries The number of DB queries expected during the test.
     * @param array $expectedresult Expected test results.
     */
    public function test_course_get_enrolled_courses_for_logged_in_user(
        $dbquerylimit,
        $totalcourses,
        $limit,
        $offset,
        $expecteddbqueries,
        $expectedresult
    ): void {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $student = $generator->create_user();

        for ($i = 0; $i < $totalcourses; $i++) {
            $shortname = "testcourse{$i}";
            $course = $generator->create_course(['shortname' => $shortname]);
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $this->setUser($student);

        $initialquerycount = $DB->perf_get_queries();
        $courses = course_get_enrolled_courses_for_logged_in_user($limit, $offset, 'shortname ASC', 'shortname', $dbquerylimit);

        // Loop over the result set to force the lazy loading to kick in so that we can check the
        // number of DB queries.
        $actualresult = array_map(function($course) {
            return $course->shortname;
        }, iterator_to_array($courses, false));

        sort($expectedresult);

        $this->assertEquals($expectedresult, $actualresult);
        $this->assertLessThanOrEqual($expecteddbqueries, $DB->perf_get_queries() - $initialquerycount);
    }

    /**
     * Test cases for the course_filter_courses_by_timeline_classification tests.
     */
    public static function get_course_filter_courses_by_timeline_classification_test_cases(): array {
        $now = time();
        $day = 86400;

        $coursedata = [
            [
                'shortname' => 'apast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'bpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'cpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'dpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'epast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'ainprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'binprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'cinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'dinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'einprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'afuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'bfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'cfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'dfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'efuture',
                'startdate' => $now + $day
            ]
        ];

        // Raw enrolled courses result set should be returned in this order:
        // afuture, ainprogress, apast, bfuture, binprogress, bpast, cfuture, cinprogress, cpast,
        // dfuture, dinprogress, dpast, efuture, einprogress, epast
        //
        // By classification the offset values for each record should be:
        // COURSE_TIMELINE_FUTURE
        // 0 (afuture), 3 (bfuture), 6 (cfuture), 9 (dfuture), 12 (efuture)
        // COURSE_TIMELINE_INPROGRESS
        // 1 (ainprogress), 4 (binprogress), 7 (cinprogress), 10 (dinprogress), 13 (einprogress)
        // COURSE_TIMELINE_PAST
        // 2 (apast), 5 (bpast), 8 (cpast), 11 (dpast), 14 (epast).
        return [
            'empty set' => [
                'coursedata' => [],
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => [],
                'expectedprocessedcount' => 0
            ],
            // COURSE_TIMELINE_FUTURE.
            'future not limit no offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 0,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 15
            ],
            'future no offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture'],
                'expectedprocessedcount' => 4
            ],
            'future offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 2,
                'expectedcourses' => ['bfuture', 'cfuture'],
                'expectedprocessedcount' => 5
            ],
            'future exact limit' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 5,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 13
            ],
            'future limit less results' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 15
            ],
            'future limit less results with offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 10,
                'offset' => 5,
                'expectedcourses' => ['cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 10
            ],
        ];
    }

    /**
     * Test the course_get_enrolled_courses_for_logged_in_user_from_search function.
     */
    public function test_course_get_enrolled_courses_for_logged_in_user_from_search(): void {
        global $DB;

        // Set up.

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $student = $generator->create_user();

        $cat1 = core_course_category::create(['name' => 'Cat1']);
        $cat2 = core_course_category::create(['name' => 'Cat2', 'parent' => $cat1->id]);
        $c1 = $this->getDataGenerator()->create_course(['category' => $cat1->id, 'fullname' => 'Test 3', 'summary' => 'Magic', 'idnumber' => 'ID3']);
        $c2 = $this->getDataGenerator()->create_course(['category' => $cat1->id, 'fullname' => 'Test 1', 'summary' => 'Magic']);
        $c3 = $this->getDataGenerator()->create_course(['category' => $cat1->id, 'fullname' => 'Математика', 'summary' => ' Test Magic']);
        $c4 = $this->getDataGenerator()->create_course(['category' => $cat1->id, 'fullname' => 'Test 4', 'summary' => 'Magic', 'idnumber' => 'ID4']);

        $c5 = $this->getDataGenerator()->create_course(['category' => $cat2->id, 'fullname' => 'Test 5', 'summary' => 'Magic']);
        $c6 = $this->getDataGenerator()->create_course(['category' => $cat2->id, 'fullname' => 'Дискретная Математика', 'summary' => 'Magic']);
        $c7 = $this->getDataGenerator()->create_course(['category' => $cat2->id, 'fullname' => 'Test 7', 'summary' => 'Magic']);
        $c8 = $this->getDataGenerator()->create_course(['category' => $cat2->id, 'fullname' => 'Test 8', 'summary' => 'Magic']);

        for ($i = 1; $i < 9; $i++) {
            $generator->enrol_user($student->id, ${"c$i"}->id, 'student');
        }

        $this->setUser($student);

        $returnedcourses = course_get_enrolled_courses_for_logged_in_user_from_search(
            0,
            0,
            'id ASC',
            null,
            COURSE_DB_QUERY_LIMIT,
            ['search' => 'test'],
            ['idonly' => true]
        );

        $actualresult = array_map(function($course) {
            return $course->id;
        }, iterator_to_array($returnedcourses, false));

        $this->assertEquals([$c1->id, $c2->id, $c3->id, $c4->id, $c5->id, $c7->id, $c8->id], $actualresult);

        // Test no courses matching the search.
        $returnedcourses = course_get_enrolled_courses_for_logged_in_user_from_search(
            0,
            0,
            'id ASC',
            null,
            COURSE_DB_QUERY_LIMIT,
            ['search' => 'foobar'],
            ['idonly' => true]
        );

        $actualresult = array_map(function($course) {
            return $course->id;
        }, iterator_to_array($returnedcourses, false));

        $this->assertEquals([], $actualresult);

        // Test returning all courses that have a mutual summary.
        $returnedcourses = course_get_enrolled_courses_for_logged_in_user_from_search(
            0,
            0,
            'id ASC',
            null,
            COURSE_DB_QUERY_LIMIT,
            ['search' => 'Magic'],
            ['idonly' => true]
        );

        $actualresult = array_map(function($course) {
            return $course->id;
        }, iterator_to_array($returnedcourses, false));

        $this->assertEquals([$c1->id, $c2->id, $c3->id, $c4->id, $c5->id, $c6->id, $c7->id, $c8->id], $actualresult);

        // Test returning a unique course.
        $returnedcourses = course_get_enrolled_courses_for_logged_in_user_from_search(
            0,
            0,
            'id ASC',
            null,
            COURSE_DB_QUERY_LIMIT,
            ['search' => 'Дискретная'],
            ['idonly' => true]
        );

        $actualresult = array_map(function($course) {
            return $course->id;
        }, iterator_to_array($returnedcourses, false));

        $this->assertEquals([$c6->id], $actualresult);
    }

    /**
     * Test the course_filter_courses_by_timeline_classification function.
     *
     * @dataProvider get_course_filter_courses_by_timeline_classification_test_cases
     * @param array $coursedata Course test data to create.
     * @param string $classification Timeline classification.
     * @param int $limit Maximum number of results to return.
     * @param int $offset Results to skip at the start of the result set.
     * @param string[] $expectedcourses Expected courses in results.
     * @param int $expectedprocessedcount Expected number of course records to be processed.
     */
    public function test_course_filter_courses_by_timeline_classification(
        $coursedata,
        $classification,
        $limit,
        $offset,
        $expectedcourses,
        $expectedprocessedcount
    ): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $courses = array_map(function($coursedata) use ($generator) {
            return $generator->create_course($coursedata);
        }, $coursedata);

        $student = $generator->create_user();

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $this->setUser($student);

        $coursesgenerator = course_get_enrolled_courses_for_logged_in_user(0, $offset, 'shortname ASC', 'shortname');
        list($result, $processedcount) = course_filter_courses_by_timeline_classification(
            $coursesgenerator,
            $classification,
            $limit
        );

        $actual = array_map(function($course) {
            return $course->shortname;
        }, $result);

        $this->assertEquals($expectedcourses, $actual);
        $this->assertEquals($expectedprocessedcount, $processedcount);
    }

    /**
     * Test cases for the course_filter_courses_by_timeline_classification tests.
     */
    public static function get_course_filter_courses_by_customfield_test_cases(): array {
        global $CFG;
        require_once($CFG->dirroot.'/blocks/myoverview/lib.php');
        $coursedata = [
            [
                'shortname' => 'C1',
                'customfield_checkboxfield' => 1,
                'customfield_datefield' => strtotime('2001-02-01T12:00:00Z'),
                'customfield_selectfield' => 1,
                'customfield_textfield' => 'fish',
            ],
            [
                'shortname' => 'C2',
                'customfield_checkboxfield' => 0,
                'customfield_datefield' => strtotime('1980-08-05T13:00:00Z'),
            ],
            [
                'shortname' => 'C3',
                'customfield_checkboxfield' => 0,
                'customfield_datefield' => strtotime('2001-02-01T12:00:00Z'),
                'customfield_selectfield' => 2,
                'customfield_textfield' => 'dog',
            ],
            [
                'shortname' => 'C4',
                'customfield_checkboxfield' => 1,
                'customfield_selectfield' => 3,
                'customfield_textfield' => 'cat',
            ],
            [
                'shortname' => 'C5',
                'customfield_datefield' => strtotime('1980-08-06T13:00:00Z'),
                'customfield_selectfield' => 2,
                'customfield_textfield' => 'fish',
            ],
        ];

        return [
            'empty set' => [
                'coursedata' => [],
                'customfield' => 'checkboxfield',
                'customfieldvalue' => 1,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => [],
                'expectedprocessedcount' => 0
            ],
            'checkbox yes' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => 1,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1', 'C4'],
                'expectedprocessedcount' => 5
            ],
            'checkbox no' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C2', 'C3', 'C5'],
                'expectedprocessedcount' => 5
            ],
            'date 1 Feb 2001' => [
                'coursedata' => $coursedata,
                'customfield' => 'datefield',
                'customfieldvalue' => strtotime('2001-02-01T12:00:00Z'),
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1', 'C3'],
                'expectedprocessedcount' => 5
            ],
            'date 6 Aug 1980' => [
                'coursedata' => $coursedata,
                'customfield' => 'datefield',
                'customfieldvalue' => strtotime('1980-08-06T13:00:00Z'),
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C5'],
                'expectedprocessedcount' => 5
            ],
            'date no date' => [
                'coursedata' => $coursedata,
                'customfield' => 'datefield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C4'],
                'expectedprocessedcount' => 5
            ],
            'select Option 1' => [
                'coursedata' => $coursedata,
                'customfield' => 'selectfield',
                'customfieldvalue' => 1,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1'],
                'expectedprocessedcount' => 5
            ],
            'select Option 2' => [
                'coursedata' => $coursedata,
                'customfield' => 'selectfield',
                'customfieldvalue' => 2,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C3', 'C5'],
                'expectedprocessedcount' => 5
            ],
            'select no select' => [
                'coursedata' => $coursedata,
                'customfield' => 'selectfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C2'],
                'expectedprocessedcount' => 5
            ],
            'text fish' => [
                'coursedata' => $coursedata,
                'customfield' => 'textfield',
                'customfieldvalue' => 'fish',
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1', 'C5'],
                'expectedprocessedcount' => 5
            ],
            'text dog' => [
                'coursedata' => $coursedata,
                'customfield' => 'textfield',
                'customfieldvalue' => 'dog',
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C3'],
                'expectedprocessedcount' => 5
            ],
            'text no text' => [
                'coursedata' => $coursedata,
                'customfield' => 'textfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C2'],
                'expectedprocessedcount' => 5
            ],
            'checkbox limit no' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => ['C2', 'C3'],
                'expectedprocessedcount' => 3
            ],
            'checkbox limit offset no' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 2,
                'offset' => 3,
                'expectedcourses' => ['C5'],
                'expectedprocessedcount' => 2
            ],
        ];
    }

    /**
     * Test the course_filter_courses_by_customfield function.
     *
     * @dataProvider get_course_filter_courses_by_customfield_test_cases
     * @param array $coursedata Course test data to create.
     * @param string $customfield Shortname of the customfield.
     * @param string $customfieldvalue the value to filter by.
     * @param int $limit Maximum number of results to return.
     * @param int $offset Results to skip at the start of the result set.
     * @param string[] $expectedcourses Expected courses in results.
     * @param int $expectedprocessedcount Expected number of course records to be processed.
     */
    public function test_course_filter_courses_by_customfield(
        $coursedata,
        $customfield,
        $customfieldvalue,
        $limit,
        $offset,
        $expectedcourses,
        $expectedprocessedcount
    ): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create the custom fields.
        $generator->create_custom_field_category([
            'name' => 'Course fields',
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
        ]);
        $generator->create_custom_field([
            'name' => 'Checkbox field',
            'category' => 'Course fields',
            'type' => 'checkbox',
            'shortname' => 'checkboxfield',
        ]);
        $generator->create_custom_field([
            'name' => 'Date field',
            'category' => 'Course fields',
            'type' => 'date',
            'shortname' => 'datefield',
            'configdata' => '{"mindate":0, "maxdate":0}',
        ]);
        $generator->create_custom_field([
            'name' => 'Select field',
            'category' => 'Course fields',
            'type' => 'select',
            'shortname' => 'selectfield',
            'configdata' => '{"options":"Option 1\nOption 2\nOption 3\nOption 4"}',
        ]);
        $generator->create_custom_field([
            'name' => 'Text field',
            'category' => 'Course fields',
            'type' => 'text',
            'shortname' => 'textfield',
        ]);

        $courses = array_map(function($coursedata) use ($generator) {
            return $generator->create_course($coursedata);
        }, $coursedata);

        $student = $generator->create_user();

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $this->setUser($student);

        $coursesgenerator = course_get_enrolled_courses_for_logged_in_user(0, $offset, 'shortname ASC', 'shortname');
        list($result, $processedcount) = course_filter_courses_by_customfield(
            $coursesgenerator,
            $customfield,
            $customfieldvalue,
            $limit
        );

        $actual = array_map(function($course) {
            return $course->shortname;
        }, $result);

        $this->assertEquals($expectedcourses, $actual);
        $this->assertEquals($expectedprocessedcount, $processedcount);
    }

    /**
     * Test cases for the course_filter_courses_by_timeline_classification w/ hidden courses tests.
     */
    public static function get_course_filter_courses_by_timeline_classification_hidden_courses_test_cases(): array {
        $now = time();
        $day = 86400;

        $coursedata = [
            [
                'shortname' => 'apast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'bpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'cpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'dpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'epast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'ainprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'binprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'cinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'dinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'einprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'afuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'bfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'cfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'dfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'efuture',
                'startdate' => $now + $day
            ]
        ];

        // Raw enrolled courses result set should be returned in this order:
        // afuture, ainprogress, apast, bfuture, binprogress, bpast, cfuture, cinprogress, cpast,
        // dfuture, dinprogress, dpast, efuture, einprogress, epast
        //
        // By classification the offset values for each record should be:
        // COURSE_TIMELINE_FUTURE
        // 0 (afuture), 3 (bfuture), 6 (cfuture), 9 (dfuture), 12 (efuture)
        // COURSE_TIMELINE_INPROGRESS
        // 1 (ainprogress), 4 (binprogress), 7 (cinprogress), 10 (dinprogress), 13 (einprogress)
        // COURSE_TIMELINE_PAST
        // 2 (apast), 5 (bpast), 8 (cpast), 11 (dpast), 14 (epast).
        return [
            'empty set' => [
                'coursedata' => [],
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => [],
                'expectedprocessedcount' => 0,
                'hiddencourse' => ''
            ],
            // COURSE_TIMELINE_FUTURE.
            'future not limit no offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 0,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 15,
                'hiddencourse' => 'bfuture'
            ],
            'future no offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'cfuture'],
                'expectedprocessedcount' => 7,
                'hiddencourse' => 'bfuture'
            ],
            'future offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 2,
                'expectedcourses' => ['bfuture', 'dfuture'],
                'expectedprocessedcount' => 8,
                'hiddencourse' => 'cfuture'
            ],
            'future exact limit' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 5,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 15,
                'hiddencourse' => 'bfuture'
            ],
            'future limit less results' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 15,
                'hiddencourse' => 'bfuture'
            ],
            'future limit less results with offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 10,
                'offset' => 5,
                'expectedcourses' => ['cfuture', 'efuture'],
                'expectedprocessedcount' => 10,
                'hiddencourse' => 'dfuture'
            ],
        ];
    }

    /**
     * Test the course_filter_courses_by_timeline_classification function hidden courses.
     *
     * @dataProvider get_course_filter_courses_by_timeline_classification_hidden_courses_test_cases
     * @param array $coursedata Course test data to create.
     * @param string $classification Timeline classification.
     * @param int $limit Maximum number of results to return.
     * @param int $offset Results to skip at the start of the result set.
     * @param string[] $expectedcourses Expected courses in results.
     * @param int $expectedprocessedcount Expected number of course records to be processed.
     * @param int $hiddencourse The course to hide as part of this process
     */
    public function test_course_filter_courses_by_timeline_classification_with_hidden_courses(
        $coursedata,
        $classification,
        $limit,
        $offset,
        $expectedcourses,
        $expectedprocessedcount,
        $hiddencourse
    ): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $student = $generator->create_user();
        $this->setUser($student);

        $courses = array_map(function($coursedata) use ($generator, $hiddencourse) {
            $course = $generator->create_course($coursedata);
            if ($course->shortname == $hiddencourse) {
                set_user_preference('block_myoverview_hidden_course_' . $course->id, true);
            }
            return $course;
        }, $coursedata);

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $coursesgenerator = course_get_enrolled_courses_for_logged_in_user(0, $offset, 'shortname ASC', 'shortname');
        list($result, $processedcount) = course_filter_courses_by_timeline_classification(
            $coursesgenerator,
            $classification,
            $limit
        );

        $actual = array_map(function($course) {
            return $course->shortname;
        }, $result);

        $this->assertEquals($expectedcourses, $actual);
        $this->assertEquals($expectedprocessedcount, $processedcount);
    }


    /**
     * Testing core_course_core_calendar_get_valid_event_timestart_range when the course has no end date.
     */
    public function test_core_course_core_calendar_get_valid_event_timestart_range_no_enddate(): void {
        global $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $now = time();
        $course = $generator->create_course(['startdate' => $now - 86400]);

        // Create a course event.
        $event = new \calendar_event([
            'name' => 'Test course event',
            'eventtype' => 'course',
            'courseid' => $course->id,
        ]);

        list ($min, $max) = core_course_core_calendar_get_valid_event_timestart_range($event, $course);
        $this->assertEquals($course->startdate, $min[0]);
        $this->assertNull($max);
    }

    /**
     * Testing core_course_core_calendar_get_valid_event_timestart_range when the course has end date.
     */
    public function test_core_course_core_calendar_get_valid_event_timestart_range_with_enddate(): void {
        global $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $now = time();
        $course = $generator->create_course(['startdate' => $now - 86400, 'enddate' => $now + 86400]);

        // Create a course event.
        $event = new \calendar_event([
            'name' => 'Test course event',
            'eventtype' => 'course',
            'courseid' => $course->id,
        ]);

        list ($min, $max) = core_course_core_calendar_get_valid_event_timestart_range($event, $course);
        $this->assertEquals($course->startdate, $min[0]);
        $this->assertNull($max);
    }

    /**
     * Test the course_get_recent_courses function.
     */
    public function test_course_get_recent_courses(): void {
        global $DB;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $courses = array();
        for ($i = 1; $i < 4; $i++) {
            $courses[]  = $generator->create_course();
        };

        $student = $generator->create_user();

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $this->setUser($student);

        $result = course_get_recent_courses($student->id);

        // No course accessed.
        $this->assertCount(0, $result);

        $time = time();
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            course_view($context);
            $DB->set_field('user_lastaccess', 'timeaccess', $time, [
                'userid' => $student->id,
                'courseid' => $course->id,
                ]);
            $time++;
        }

        // Every course accessed.
        $result = course_get_recent_courses($student->id);
        $this->assertCount(3, $result);

        // Every course accessed, result limited to 2 courses.
        $result = course_get_recent_courses($student->id, 2);
        $this->assertCount(2, $result);

        // Every course accessed, with limit and offset should return the first course.
        $result = course_get_recent_courses($student->id, 3, 2);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey($courses[0]->id, $result);

        // Every course accessed, order by shortname DESC. The last create course ($course[2]) should have the greater shortname.
        $result = course_get_recent_courses($student->id, 0, 0, 'shortname DESC');
        $this->assertCount(3, $result);
        $this->assertEquals($courses[2]->id, array_values($result)[0]->id);
        $this->assertEquals($courses[1]->id, array_values($result)[1]->id);
        $this->assertEquals($courses[0]->id, array_values($result)[2]->id);

        // Every course accessed, order by shortname ASC.
        $result = course_get_recent_courses($student->id, 0, 0, 'shortname ASC');
        $this->assertCount(3, $result);
        $this->assertEquals($courses[0]->id, array_values($result)[0]->id);
        $this->assertEquals($courses[1]->id, array_values($result)[1]->id);
        $this->assertEquals($courses[2]->id, array_values($result)[2]->id);

        $guestcourse = $generator->create_course(
            (object)array('shortname' => 'guestcourse',
                'enrol_guest_status_0' => ENROL_INSTANCE_ENABLED,
                'enrol_guest_password_0' => ''));
        $context = context_course::instance($guestcourse->id);
        course_view($context);

        // Every course accessed, even the not enrolled one.
        $result = course_get_recent_courses($student->id);
        $this->assertCount(4, $result);

        // Suspended student.
        $this->getDataGenerator()->enrol_user($student->id, $courses[0]->id, 'student', 'manual', 0, 0, ENROL_USER_SUSPENDED);

        // The course with suspended enrolment is not returned by the function.
        $result = course_get_recent_courses($student->id);
        $this->assertCount(3, $result);
        $this->assertArrayNotHasKey($courses[0]->id, $result);
    }

    /**
     * Test the validation of the sort value in course_get_recent_courses().
     *
     * @dataProvider course_get_recent_courses_sort_validation_provider
     * @param string $sort The sort value
     * @param string $expectedexceptionmsg The expected exception message
     */
    public function test_course_get_recent_courses_sort_validation(string $sort, string $expectedexceptionmsg): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        if (!empty($expectedexceptionmsg)) {
            $this->expectException('invalid_parameter_exception');
            $this->expectExceptionMessage($expectedexceptionmsg);
        }
        course_get_recent_courses($user->id, 0, 0, $sort);
    }

    /**
     * Data provider for test_course_get_recent_courses_sort_validation().
     *
     * @return array
     */
    public static function course_get_recent_courses_sort_validation_provider(): array {
        return [
            'Invalid sort format (SQL injection attempt)' =>
                [
                    'shortname DESC LIMIT 1--',
                    'Invalid structure of the sort parameter, allowed structure: fieldname [ASC|DESC].',
                ],
            'Sort uses \'sort by\' field that does not exist' =>
                [
                    'shortname DESC, xyz ASC',
                    'Invalid field in the sort parameter, allowed fields: id, idnumber, summary, summaryformat, ' .
                    'startdate, enddate, category, shortname, fullname, timeaccess, component, visible, ' .
                    'showactivitydates, showcompletionconditions, pdfexportfont.',
            ],
            'Sort uses invalid value for the sorting direction' =>
                [
                    'shortname xyz, lastaccess',
                    'Invalid sort direction in the sort parameter, allowed values: asc, desc.',
                ],
            'Valid sort format' =>
                [
                    'shortname asc, timeaccess',
                    ''
                ]
        ];
    }

    /**
     * Test the course_get_recent_courses function.
     */
    public function test_course_get_recent_courses_with_guest(): void {
        global $DB;
        $this->resetAfterTest(true);

        $student = $this->getDataGenerator()->create_user();

        // Course 1 with guest access and no direct enrolment.
        $course1 = $this->getDataGenerator()->create_course();
        $context1 = context_course::instance($course1->id);
        $record = $DB->get_record('enrol', ['courseid' => $course1->id, 'enrol' => 'guest']);
        enrol_get_plugin('guest')->update_status($record, ENROL_INSTANCE_ENABLED);

        // Course 2 where student is enrolled with two enrolment methods.
        $course2 = $this->getDataGenerator()->create_course();
        $context2 = context_course::instance($course2->id);
        $record = $DB->get_record('enrol', ['courseid' => $course2->id, 'enrol' => 'self']);
        enrol_get_plugin('guest')->update_status($record, ENROL_INSTANCE_ENABLED);
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, 'student', 'manual', 0, 0, ENROL_USER_ACTIVE);
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, 'student', 'self', 0, 0, ENROL_USER_ACTIVE);

        // Course 3.
        $course3 = $this->getDataGenerator()->create_course();
        $context3 = context_course::instance($course3->id);

        // Student visits first two courses, course_get_recent_courses returns two courses.
        $this->setUser($student);
        course_view($context1);
        course_view($context2);

        $result = course_get_recent_courses($student->id);
        $this->assertEqualsCanonicalizing([$course2->id, $course1->id], array_column($result, 'id'));

        // Admin visits all three courses. Only the one with guest access is returned.
        $this->setAdminUser();
        course_view($context1);
        course_view($context2);
        course_view($context3);
        $result = course_get_recent_courses(get_admin()->id);
        $this->assertEqualsCanonicalizing([$course1->id], array_column($result, 'id'));
    }

    /**
     * Test cases for the course_get_course_dates_for_user_ids tests.
     */
    public static function get_course_get_course_dates_for_user_ids_test_cases(): array {
        $now = time();
        $pastcoursestart = $now - 100;
        $futurecoursestart = $now + 100;

        return [
            'future course start fixed no users enrolled' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [[], []],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 1 users enrolled future' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 1 users enrolled past' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled future' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled past' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$futurecoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled mixed' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$futurecoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled 2 methods' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled 2 methods 1 disabled' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled 2 methods 2 disabled' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_DISABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled 2 methods 0 disabled 1 user suspended' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start fixed 2 users enrolled 2 methods 0 disabled 2 user suspended' => [
                'relativedatemode' => false,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 20, ENROL_USER_SUSPENDED]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 10, ENROL_USER_SUSPENDED]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start relative no users enrolled' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [[], []],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start relative 1 users enrolled future' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start relative 1 users enrolled past' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start relative 2 users enrolled future' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $futurecoursestart + 20,
                        'startoffset' => 20
                    ]
                ]
            ],
            'future course start relative 2 users enrolled past' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$futurecoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start relative 2 users enrolled mixed' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$futurecoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start relative 2 users enrolled 2 methods' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $futurecoursestart + 10,
                        'startoffset' => 10
                    ]
                ]
            ],
            'future course start relative 2 users enrolled 2 methods 1 disabled' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart + 20,
                        'startoffset' => 20
                    ],
                    [
                        'start' => $futurecoursestart + 10,
                        'startoffset' => 10
                    ]
                ]
            ],
            'future course start relative 2 users enrolled 2 methods 2 disabled' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_DISABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'future course start relative 2 users enrolled 2 methods 0 disabled 1 user suspended' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart + 20,
                        'startoffset' => 20
                    ],
                    [
                        'start' => $futurecoursestart + 10,
                        'startoffset' => 10
                    ]
                ]
            ],
            'future course start relative 2 users enrolled 2 methods 0 disabled 2 user suspended' => [
                'relativedatemode' => true,
                'coursestart' => $futurecoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$futurecoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 20, ENROL_USER_SUSPENDED]
                    ],
                    // User 2.
                    [
                        'manual' => [$futurecoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$futurecoursestart + 10, ENROL_USER_SUSPENDED]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $futurecoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],

            // Course start date in the past.
            'past course start fixed no users enrolled' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [[], []],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 1 users enrolled future' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 1 users enrolled past' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled future' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled past' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$pastcoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled mixed' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$pastcoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled 2 methods' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled 2 methods 1 disabled' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled 2 methods 2 disabled' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_DISABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled 2 methods 0 disabled 1 user suspended' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start fixed 2 users enrolled 2 methods 0 disabled 2 user suspended' => [
                'relativedatemode' => false,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 20, ENROL_USER_SUSPENDED]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 10, ENROL_USER_SUSPENDED]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start relative no users enrolled' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [[], []],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start relative 1 users enrolled future' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start relative 1 users enrolled past' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    []
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start relative 2 users enrolled future' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $pastcoursestart + 20,
                        'startoffset' => 20
                    ]
                ]
            ],
            'past course start relative 2 users enrolled past' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart - 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$pastcoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start relative 2 users enrolled mixed' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    ['manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]],
                    // User 2.
                    ['manual' => [$pastcoursestart - 20, ENROL_USER_ACTIVE]]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start relative 2 users enrolled 2 methods' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart + 10,
                        'startoffset' => 10
                    ],
                    [
                        'start' => $pastcoursestart + 10,
                        'startoffset' => 10
                    ]
                ]
            ],
            'past course start relative 2 users enrolled 2 methods 1 disabled' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart + 20,
                        'startoffset' => 20
                    ],
                    [
                        'start' => $pastcoursestart + 10,
                        'startoffset' => 10
                    ]
                ]
            ],
            'past course start relative 2 users enrolled 2 methods 2 disabled' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_DISABLED],
                    ['self', ENROL_INSTANCE_DISABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_ACTIVE],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ],
            'past course start relative 2 users enrolled 2 methods 0 disabled 1 user suspended' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 20, ENROL_USER_ACTIVE]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 10, ENROL_USER_ACTIVE]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart + 20,
                        'startoffset' => 20
                    ],
                    [
                        'start' => $pastcoursestart + 10,
                        'startoffset' => 10
                    ]
                ]
            ],
            'past course start relative 2 users enrolled 2 methods 0 disabled 2 user suspended' => [
                'relativedatemode' => true,
                'coursestart' => $pastcoursestart,
                'usercount' => 2,
                'enrolmentmethods' => [
                    ['manual', ENROL_INSTANCE_ENABLED],
                    ['self', ENROL_INSTANCE_ENABLED]
                ],
                'enrolled' => [
                    // User 1.
                    [
                        'manual' => [$pastcoursestart + 10, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 20, ENROL_USER_SUSPENDED]
                    ],
                    // User 2.
                    [
                        'manual' => [$pastcoursestart + 20, ENROL_USER_SUSPENDED],
                        'self' => [$pastcoursestart + 10, ENROL_USER_SUSPENDED]
                    ]
                ],
                'expected' => [
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ],
                    [
                        'start' => $pastcoursestart,
                        'startoffset' => 0
                    ]
                ]
            ]
        ];
    }

    /**
     * Test the course_get_course_dates_for_user_ids function.
     *
     * @dataProvider get_course_get_course_dates_for_user_ids_test_cases
     * @param bool $relativedatemode Set the course to relative dates mode
     * @param int $coursestart Course start date
     * @param int $usercount Number of users to create
     * @param array $enrolmentmethods Enrolment methods to set for the course
     * @param array $enrolled Enrolment config for to set for the users
     * @param array $expected Expected output
     */
    public function test_course_get_course_dates_for_user_ids(
        $relativedatemode,
        $coursestart,
        $usercount,
        $enrolmentmethods,
        $enrolled,
        $expected
    ): void {
        global $DB;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course  = $generator->create_course(['startdate' => $coursestart]);
        $course->relativedatesmode = $relativedatemode;
        $users = [];

        for ($i = 0; $i < $usercount; $i++) {
            $users[] = $generator->create_user();
        }

        foreach ($enrolmentmethods as [$type, $status]) {
            $record = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => $type]);
            $plugin = enrol_get_plugin($type);
            if ($record->status != $status) {
                $plugin->update_status($record, $status);
            }
        }

        foreach ($enrolled as $index => $enrolconfig) {
            $user = $users[$index];
            foreach ($enrolconfig as $type => [$starttime, $status]) {
                $generator->enrol_user($user->id, $course->id, 'student', $type, $starttime, 0, $status);
            }
        }

        $userids = array_map(function($user) {
            return $user->id;
        }, $users);
        $actual = course_get_course_dates_for_user_ids($course, $userids);

        foreach ($expected as $index => $exp) {
            $userid = $userids[$index];
            $act = $actual[$userid];

            $this->assertEquals($exp['start'], $act['start']);
            $this->assertEquals($exp['startoffset'], $act['startoffset']);
        }
    }

    /**
     * Test that calling course_get_course_dates_for_user_ids multiple times in the
     * same request fill fetch the correct data for the user.
     */
    public function test_course_get_course_dates_for_user_ids_multiple_calls(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $now = time();
        $coursestart = $now - 1000;
        $course  = $generator->create_course(['startdate' => $coursestart]);
        $course->relativedatesmode = true;
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user1start = $coursestart + 100;
        $user2start = $coursestart + 200;

        $generator->enrol_user($user1->id, $course->id, 'student', 'manual', $user1start);
        $generator->enrol_user($user2->id, $course->id, 'student', 'manual', $user2start);

        $result = course_get_course_dates_for_user_ids($course, [$user1->id]);
        $this->assertEquals($user1start, $result[$user1->id]['start']);

        $result = course_get_course_dates_for_user_ids($course, [$user1->id, $user2->id]);
        $this->assertEquals($user1start, $result[$user1->id]['start']);
        $this->assertEquals($user2start, $result[$user2->id]['start']);

        $result = course_get_course_dates_for_user_ids($course, [$user2->id]);
        $this->assertEquals($user2start, $result[$user2->id]['start']);
    }

    /**
     * Data provider for test_course_modules_pending_deletion.
     *
     * @return array An array of arrays contain test data
     */
    public static function provider_course_modules_pending_deletion(): array {
        return [
            'Non-gradable activity, check all'              => [['forum'], 0, false, true],
            'Gradable activity, check all'                  => [['assign'], 0, false, true],
            'Non-gradable activity, check gradables'        => [['forum'], 0, true, false],
            'Gradable activity, check gradables'            => [['assign'], 0, true, true],
            'Non-gradable within multiple, check all'       => [['quiz', 'forum', 'assign'], 1, false, true],
            'Non-gradable within multiple, check gradables' => [['quiz', 'forum', 'assign'], 1, true, false],
            'Gradable within multiple, check all'           => [['quiz', 'forum', 'assign'], 2, false, true],
            'Gradable within multiple, check gradables'     => [['quiz', 'forum', 'assign'], 2, true, true],
        ];
    }

    /**
     * Tests the function course_modules_pending_deletion.
     *
     * @param string[] $modules A complete list aff all available modules before deletion
     * @param int $indextodelete The index of the module in the $modules array that we want to test with
     * @param bool $gradable The value to pass to the gradable argument of the course_modules_pending_deletion function
     * @param bool $expected The expected result
     * @dataProvider provider_course_modules_pending_deletion
     */
    public function test_course_modules_pending_deletion(array $modules, int $indextodelete, bool $gradable, bool $expected): void {
        $this->resetAfterTest();

        // Ensure recyclebin is enabled.
        set_config('coursebinenable', true, 'tool_recyclebin');

        // Create course and modules.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $moduleinstances = [];
        foreach ($modules as $module) {
            $moduleinstances[] = $generator->create_module($module, array('course' => $course->id));
        }

        course_delete_module($moduleinstances[$indextodelete]->cmid, true); // Try to delete the instance asynchronously.
        $this->assertEquals($expected, course_modules_pending_deletion($course->id, $gradable));
    }

    /**
     * Tests for the course_request::can_request
     */
    public function test_can_request_course(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $cat1 = $CFG->defaultrequestcategory;
        $cat2 = $this->getDataGenerator()->create_category()->id;
        $cat3 = $this->getDataGenerator()->create_category()->id;
        $context1 = context_coursecat::instance($cat1);
        $context2 = context_coursecat::instance($cat2);
        $context3 = context_coursecat::instance($cat3);
        $this->setUser($user);

        // By default users don't have capability to request courses.
        $this->assertFalse(course_request::can_request(context_system::instance()));
        $this->assertFalse(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertFalse(course_request::can_request($context3));

        // Allow for the 'user' role the capability to request courses.
        $userroleid = $DB->get_field('role', 'id', ['shortname' => 'user']);
        assign_capability('moodle/course:request', CAP_ALLOW, $userroleid,
            context_system::instance()->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Lock category selection.
        $CFG->lockrequestcategory = 1;

        // Now user can only request course in the default category or in system context.
        $this->assertTrue(course_request::can_request(context_system::instance()));
        $this->assertTrue(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertFalse(course_request::can_request($context3));

        // Enable category selection. User can request course anywhere.
        $CFG->lockrequestcategory = 0;
        $this->assertTrue(course_request::can_request(context_system::instance()));
        $this->assertTrue(course_request::can_request($context1));
        $this->assertTrue(course_request::can_request($context2));
        $this->assertTrue(course_request::can_request($context3));

        // Remove cap from cat2.
        $roleid = create_role('Test role', 'testrole', 'Test role description');
        assign_capability('moodle/course:request', CAP_PROHIBIT, $roleid,
            $context2->id, true);
        role_assign($roleid, $user->id, $context2->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertTrue(course_request::can_request(context_system::instance()));
        $this->assertTrue(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertTrue(course_request::can_request($context3));

        // Disable course request functionality.
        $CFG->enablecourserequests = false;
        $this->assertFalse(course_request::can_request(context_system::instance()));
        $this->assertFalse(course_request::can_request($context1));
        $this->assertFalse(course_request::can_request($context2));
        $this->assertFalse(course_request::can_request($context3));
    }

    /**
     * Tests for the course_request::can_approve
     */
    public function test_can_approve_course_request(): void {
        global $CFG;
        $this->resetAfterTest();

        $requestor = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();
        $cat1 = $CFG->defaultrequestcategory;
        $cat2 = $this->getDataGenerator()->create_category()->id;
        $cat3 = $this->getDataGenerator()->create_category()->id;

        // Enable course requests. Default 'user' role has capability to request courses.
        $CFG->enablecourserequests = true;
        $CFG->lockrequestcategory = 0;
        $this->setUser($requestor);
        $requestdata = ['summary_editor' => ['text' => '', 'format' => 0], 'name' => 'Req', 'reason' => 'test'];
        $request1 = course_request::create((object)($requestdata));
        $request2 = course_request::create((object)($requestdata + ['category' => $cat2]));
        $request3 = course_request::create((object)($requestdata + ['category' => $cat3]));

        $this->setUser($user);
        // Add capability to approve courses.
        $roleid = create_role('Test role', 'testrole', 'Test role description');
        assign_capability('moodle/site:approvecourse', CAP_ALLOW, $roleid,
            context_system::instance()->id, true);
        role_assign($roleid, $user->id, context_coursecat::instance($cat2)->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->assertFalse($request1->can_approve());
        $this->assertTrue($request2->can_approve());
        $this->assertFalse($request3->can_approve());

        // Delete category where course was requested. Now only site-wide manager can approve it.
        core_course_category::get($cat2, MUST_EXIST, true)->delete_full(false);
        $this->assertFalse($request2->can_approve());

        $this->setAdminUser();
        $this->assertTrue($request2->can_approve());
    }

    /**
     * Test the course allowed module method.
     */
    public function test_course_allowed_module(): void {
        $this->resetAfterTest();
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $manager = $this->getDataGenerator()->create_and_enrol($course, 'manager');

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        assign_capability('mod/assign:addinstance', CAP_PROHIBIT, $teacherrole->id, \context_course::instance($course->id));

        // Global user (teacher) has no permissions in this course.
        $this->setUser($teacher);
        $this->assertFalse(course_allowed_module($course, 'assign'));

        // Manager has permissions.
        $this->assertTrue(course_allowed_module($course, 'assign', $manager));
    }

    /**
     * Test the {@link average_number_of_participants()} function.
     */
    public function test_average_number_of_participants(): void {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $now = time();

        // If there are no courses, expect zero number of participants per course.
        $this->assertEquals(0, average_number_of_participants());

        $c1 = $generator->create_course();
        $c2 = $generator->create_course();

        // If there are no users, expect zero number of participants per course.
        $this->assertEquals(0, average_number_of_participants());

        $t1 = $generator->create_user(['lastlogin' => $now]);
        $s1 = $generator->create_user(['lastlogin' => $now]);
        $s2 = $generator->create_user(['lastlogin' => $now - WEEKSECS]);
        $s3 = $generator->create_user(['lastlogin' => $now - WEEKSECS]);
        $s4 = $generator->create_user(['lastlogin' => $now - YEARSECS]);

        // We have courses, we have users, but no enrolments yet.
        $this->assertEquals(0, average_number_of_participants());

        // Front page enrolments are ignored.
        $generator->enrol_user($t1->id, SITEID, 'teacher');
        $this->assertEquals(0, average_number_of_participants());

        // The teacher enrolled into one of the two courses.
        $generator->enrol_user($t1->id, $c1->id, 'editingteacher');
        $this->assertEquals(0.5, average_number_of_participants());

        // The teacher enrolled into both courses.
        $generator->enrol_user($t1->id, $c2->id, 'editingteacher');
        $this->assertEquals(1, average_number_of_participants());

        // Student 1 enrolled in the Course 1 only.
        $generator->enrol_user($s1->id, $c1->id, 'student');
        $this->assertEquals(1.5, average_number_of_participants());

        // Student 2 enrolled in both courses, but the enrolment in the Course 2 not active yet (enrolment starts in the future).
        $generator->enrol_user($s2->id, $c1->id, 'student');
        $generator->enrol_user($s2->id, $c2->id, 'student', 'manual', $now + WEEKSECS);
        $this->assertEquals(2.5, average_number_of_participants());
        $this->assertEquals(2, average_number_of_participants(true));

        // Student 3 enrolled in the Course 1, but the enrolment already expired.
        $generator->enrol_user($s3->id, $c1->id, 'student', 'manual', 0, $now - YEARSECS);
        $this->assertEquals(3, average_number_of_participants());
        $this->assertEquals(2, average_number_of_participants(true));

        // Student 4 enrolled in both courses, but the enrolment has been suspended.
        $generator->enrol_user($s4->id, $c1->id, 'student', 'manual', 0, 0, ENROL_USER_SUSPENDED);
        $generator->enrol_user($s4->id, $c2->id, 'student', 'manual', $now - DAYSECS, $now + YEARSECS, ENROL_USER_SUSPENDED);
        $this->assertEquals(4, average_number_of_participants());
        $this->assertEquals(2, average_number_of_participants(true));

        // Consider only t1 and s1 who logged in recently.
        $this->assertEquals(1.5, average_number_of_participants(false, $now - DAYSECS));

        // Consider only t1, s1, s2 and s3 who logged in in recent weeks.
        $this->assertEquals(3, average_number_of_participants(false, $now - 4 * WEEKSECS));

        // Hidden courses are excluded from stats.
        $DB->set_field('course', 'visible', 0, ['id' => $c1->id]);
        $this->assertEquals(3, average_number_of_participants());
        $this->assertEquals(1, average_number_of_participants(true));
    }

    /**
     * Test the set_downloadcontent() function.
     */
    public function test_set_downloadcontent(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page = $generator->create_module('page', ['course' => $course]);

        // Test the module 'downloadcontent' field is set to enabled.
        set_downloadcontent($page->cmid, DOWNLOAD_COURSE_CONTENT_ENABLED);
        $modinfo = get_fast_modinfo($course)->get_cm($page->cmid);
        $this->assertEquals(DOWNLOAD_COURSE_CONTENT_ENABLED, $modinfo->downloadcontent);

        // Now let's test the 'downloadcontent' value is updated to disabled.
        set_downloadcontent($page->cmid, DOWNLOAD_COURSE_CONTENT_DISABLED);
        $modinfo = get_fast_modinfo($course)->get_cm($page->cmid);
        $this->assertEquals(DOWNLOAD_COURSE_CONTENT_DISABLED, $modinfo->downloadcontent);

        // Nothing to update, the download course content value is the same, it should return false.
        $this->assertFalse(set_downloadcontent($page->cmid, DOWNLOAD_COURSE_CONTENT_DISABLED));

        // The download course content value has changed, it should return true in this case.
        $this->assertTrue(set_downloadcontent($page->cmid, DOWNLOAD_COURSE_CONTENT_ENABLED));
    }

    /**
     * Test for course_get_courseimage.
     *
     * @covers ::course_get_courseimage
     */
    public function test_course_get_courseimage(): void {
        global $CFG;

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $this->assertNull(course_get_courseimage($course));

        $fs = get_file_storage();
        $file = $fs->create_file_from_pathname((object) [
            'contextid' => \core\context\course::instance($course->id)->id,
            'component' => 'course',
            'filearea' => 'overviewfiles',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'logo.png',
        ], "{$CFG->dirroot}/lib/tests/fixtures/gd-logo.png");

        $image = course_get_courseimage($course);
        $this->assertInstanceOf(\stored_file::class, $image);
        $this->assertEquals(
            $file->get_id(),
            $image->get_id(),
        );
    }

    /**
     * Test the course_get_communication_instance_data() function.
     *
     * @covers ::course_get_communication_instance_data
     */
    public function test_course_get_communication_instance_data(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();

        // Set admin user as a valid enrolment will be checked in the callback function.
        $this->setAdminUser();

        // Use the callback function and return the data.
        list($instance, $context, $heading, $returnurl) = component_callback(
            'core_course',
            'get_communication_instance_data',
            [$course->id]
        );

        // Check the url is as expected.
        $expectedreturnurl = new moodle_url('/course/view.php', ['id' => $course->id]);
        $this->assertEquals($expectedreturnurl, $returnurl);

        // Check the context is as expected.
        $expectedcontext = context_course::instance($course->id);
        $this->assertEquals($expectedcontext, $context);

        // Check the instance id is as expected.
        $this->assertEquals($course->id, $instance->id);

        // Check the heading is as expected.
        $this->assertEquals($course->fullname, $heading);
    }

    /**
     * Test course_section_view() function
     *
     * @covers ::course_section_view
     */
    public function test_course_section_view(): void {

        $this->resetAfterTest();

        // Course without sections.
        $course = $this->getDataGenerator()->create_course(['numsections' => 5], ['createsections' => true]);
        $coursecontext = context_course::instance($course->id);
        $format = course_get_format($course->id);
        $sections = $format->get_sections();
        $section = reset($sections);

        // Redirect events to the sink, so we can recover them later.
        $sink = $this->redirectEvents();

        course_section_view($coursecontext, $section->id);

        $events = $sink->get_events();
        $event = reset($events);

        // Check the event details are correct.
        $this->assertInstanceOf('\core\event\section_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals($section->id, $event->objectid);
    }

    /**
     * Tests get_sorted_course_formats returns plugins in cases where plugins are
     * installed previously but no longer exist, or not installed yet.
     *
     * @covers ::get_sorted_course_formats()
     */
    public function test_get_sorted_course_formats_installed_or_not(): void {
        global $DB;

        $this->resetAfterTest();

        // If there is an extra format installed that no longer exists, include in list (at end).
        $DB->insert_record('config_plugins', [
            'plugin' => 'format_frogs',
            'name' => 'version',
            'value' => '20240916',
        ]);
        \core\plugin_manager::reset_caches();
        $formats = get_sorted_course_formats();
        $this->assertContains('frogs', $formats);

        // If one of the formats is not installed yet, we still return it.
        $DB->delete_records('config_plugins', ['plugin' => 'format_weeks']);
        \core\plugin_manager::reset_caches();
        $formats = get_sorted_course_formats();
        $this->assertContains('weeks', $formats);
    }
}
