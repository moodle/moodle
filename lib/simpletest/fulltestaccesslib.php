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
 * Full functional accesslib test
 *
 * It is implemented as one test case because it would take hours
 * to prepare the fake test site for each test, at the same time
 * we have to work around multiple problems in UnitTestCaseUsingDatabase.
 *
 * @package    core
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Context caching fixture
 */
class context_inspection extends context_helper {
    public static function test_context_cache_size() {
        return self::$cache_count;
    }
}


/**
 * Functional test for accesslib.php
 *
 * Note: execution may take many minutes especially on slower servers.
 */
class accesslib_test extends UnitTestCaseUsingDatabase {
    // NOTE: The UnitTestCaseUsingDatabase is problematic and VERY dangerous especially for production servers!
    //
    // 1. We could create full install without plugins and use the test DB since the very beginning in lib/setup.php.
    // 2. We should not execute the tests from the web UI because the static caches all over the place collide, instead
    //    we could use CLI
    // 3. SimpleTest appears to be dead - unfortunately nobody else thinks we should switch to PHPUnit asap
    // 4. switching of USER->id alone is not acceptable at all, we must switch the complete USER record
    //    and probably deal with _SESSION['USER'] too
    // 5. we must close session before messing with anything in $USER or $SESSION
    // 6. $SITE is not switched
    // 7. tons of other static caches are not reset
    // 8. etc.
    //
    // --
    // skodak

    //TODO: add more tests for the remaining accesslib parts that were not touched by the refactoring in 2.2dev

    public static $includecoverage = array('lib/accesslib.php');

    protected $accesslibprevuser = null;
    protected $accesslibprevsite = null;

    /**
     * A small functional test of accesslib functions and classes.
     */
    public function test_everything_in_accesslib() {
        global $USER, $SITE, $CFG, $DB, $ACCESSLIB_PRIVATE;

        // First of all finalize the session, we must not carry over any of this mess to the next page via SESSION!!!
        session_get_instance()->write_close();

        // hack - this is going to take very long time
        set_time_limit(3600);

        // Get rid of current user that would not work anyway,
        // do NOT even think about using $this->switch_global_user_id()!
        if (!isset($this->accesslibprevuser)) {
            $this->accesslibprevuser = clone($USER);
            $USER = new stdClass();
            $USER->id = 0;
        }

        // Backup $SITE global
        if (!isset($this->accesslibprevsite)) {
            $this->accesslibprevsite = $SITE;
        }

        // Switch DB, if it somehow fails or you specified wrong unittest prefix it will nuke real data, you have been warned!
        $this->switch_to_test_db();

        // Let's switch the CFG
        $prevcfg = clone($CFG);
        $this->switch_to_test_cfg();
        $CFG->config_php_settings = $prevcfg->config_php_settings;
        $CFG->noemailever = true;
        $CFG->admin       = $prevcfg->admin;
        $CFG->lang        = 'en';
        $CFG->tempdir     = $prevcfg->tempdir;
        $CFG->cachedir    = $prevcfg->cachedir;
        $CFG->directorypermissions = $prevcfg->directorypermissions;
        $CFG->filepermissions      = $prevcfg->filepermissions;

        // Reset all caches
        accesslib_clear_all_caches_for_unit_testing();

        // Add some core tables - this is known to break constantly because we keep adding dependencies...
        $tablenames = array('config', 'config_plugins', 'modules', 'course', 'course_modules', 'course_sections', 'course_categories', 'mnet_host', 'mnet_application',
                'capabilities', 'context', 'context_temp', 'role', 'role_capabilities', 'role_allow_switch', 'license', 'my_pages', 'block', 'block_instances', 'block_positions',
                'role_allow_assign', 'role_allow_override', 'role_assignments', 'role_context_levels' ,'enrol', 'user_enrolments', 'filter_active', 'filter_config', 'comments',
                'user', 'groups_members', 'cache_flags', 'events_handlers', 'user_lastaccess', 'rating', 'files', 'role_names', 'user_preferences', 'grading_areas');
        $this->create_test_tables($tablenames, 'lib');

        // Create all core default records and default settings
        require_once("$CFG->libdir/db/install.php");
        xmldb_main_install(); // installs the capabilities too

        // Fake mod_page install
        $tablenames = array('page');
        $this->create_test_tables($tablenames, 'mod/page');
        $module = new stdClass();
        require($CFG->dirroot .'/mod/page/version.php');
        $module->name = 'page';
        $pagemoduleid = $DB->insert_record('modules', $module);
        update_capabilities('mod_page');

        // Fake block_online_users install
        $plugin = new stdClass();
        $plugin->version = NULL;
        $plugin->cron    = 0;
        include($CFG->dirroot.'/blocks/online_users/version.php');
        $plugin->name     = 'online_users';
        $onlineusersblockid = $DB->insert_record('block', $plugin);
        update_capabilities('block_online_users');

        // Finish roles setup
        set_config('defaultfrontpageroleid', $DB->get_field('role', 'id', array('archetype'=>'frontpage')));
        set_config('defaultuserroleid', $DB->get_field('role', 'id', array('archetype'=>'user')));
        set_config('notloggedinroleid', $DB->get_field('role', 'id', array('archetype'=>'guest')));
        set_config('rolesactive', 1);

        // Init manual enrol
        set_config('status', ENROL_INSTANCE_ENABLED, 'enrol_manual');
        set_config('defaultperiod', 0, 'enrol_manual');
        $manualenrol = enrol_get_plugin('manual');

        // Fill the site with some real data
        $testcategories = array();
        $testcourses = array();
        $testpages = array();
        $testblocks = array();
        $allroles = $DB->get_records_menu('role', array(), 'id', 'archetype, id');

        $systemcontext = context_system::instance();
        $frontpagecontext = context_course::instance(SITEID);

        // Add block to system context
        $bi = new stdClass();
        $bi->blockname         = 'online_users';
        $bi->parentcontextid   = $systemcontext->id;
        $bi->showinsubcontexts = 1;
        $bi->pagetypepattern   = '';
        $bi->subpagepattern    = '';
        $bi->defaultregion     = '';
        $bi->defaultweight     = 0;
        $bi->configdata        = '';
        $biid = $DB->insert_record('block_instances', $bi);
        context_block::instance($biid);
        $testblocks[] = $biid;

        // Some users
        $testusers = array();
        for($i=0; $i<20; $i++) {
            $user = new stdClass();
            $user->auth         = 'manual';
            $user->firstname    = 'user'.$i;
            $user->lastname     = 'user'.$i;
            $user->username     = 'user'.$i;
            $user->password     = 'doesnotexist';
            $user->email        = "user$i@example.com";
            $user->confirmed    = 1;
            $user->mnethostid   = $CFG->mnet_localhost_id;
            $user->lang         = $CFG->lang;
            $user->maildisplay  = 1;
            $user->timemodified = time();
            $user->deleted      = 0;
            $user->lastip       = '0.0.0.0';
            $userid = $DB->insert_record('user', $user);
            $testusers[$i] = $userid;
            $usercontext = context_user::instance($userid);

            // Add block to user profile
            $bi->parentcontextid = $usercontext->id;
            $biid = $DB->insert_record('block_instances', $bi);
            context_block::instance($biid);
            $testblocks[] = $biid;
        }
        // Deleted user - should be ignored everywhere, can not have context
        $user = new stdClass();
        $user->auth         = 'manual';
        $user->firstname    = '';
        $user->lastname     = '';
        $user->username     = 'user@example.com121132132';
        $user->password     = '';
        $user->email        = '';
        $user->confirmed    = 1;
        $user->mnethostid   = $CFG->mnet_localhost_id;
        $user->lang         = $CFG->lang;
        $user->maildisplay  = 1;
        $user->timemodified = time();
        $user->deleted      = 1;
        $user->lastip       = '0.0.0.0';
        $DB->insert_record('user', $user);
        unset($user);

        // Add block to frontpage
        $bi->parentcontextid = $frontpagecontext->id;
        $biid = $DB->insert_record('block_instances', $bi);
        $frontpageblockcontext = context_block::instance($biid);
        $testblocks[] = $biid;

        // Add a resource to frontpage
        $page = new stdClass();
        $page->course        = $SITE->id;
        $page->intro         = '...';
        $page->introformat   = FORMAT_HTML;
        $page->content       = '...';
        $page->contentformat = FORMAT_HTML;
        $pageid = $DB->insert_record('page', $page);
        $testpages[] = $pageid;
        $cm = new stdClass();
        $cm->course   = $SITE->id;
        $cm->module   = $pagemoduleid;
        $cm->section  = 1;
        $cm->instance = $pageid;
        $cmid = $DB->insert_record('course_modules', $cm);
        $frontpagepagecontext = context_module::instance($cmid);

        // Add block to frontpage resource
        $bi->parentcontextid = $frontpagepagecontext->id;
        $biid = $DB->insert_record('block_instances', $bi);
        $frontpagepageblockcontext = context_block::instance($biid);
        $testblocks[] = $biid;

        // Some nested course categories with courses
        require_once("$CFG->dirroot/course/lib.php");
        $path = '';
        $parentcat = 0;
        for($i=0; $i<5; $i++) {
            $cat = new stdClass();
            $cat->name         = 'category'.$i;
            $cat->parent       = $parentcat;
            $cat->depth        = $i+1;
            $cat->sortorder    = MAX_COURSES_IN_CATEGORY * ($i+2);
            $cat->timemodified = time();
            $catid = $DB->insert_record('course_categories', $cat);
            $path = $path . '/' . $catid;
            $DB->set_field('course_categories', 'path', $path, array('id'=>$catid));
            $parentcat = $catid;
            $testcategories[] = $catid;
            $catcontext = context_coursecat::instance($catid);

            if ($i >=4) {
                continue;
            }

            // Add resource to each category
            $bi->parentcontextid = $catcontext->id;
            $biid = $DB->insert_record('block_instances', $bi);
            context_block::instance($biid);

            // Add a few courses to each category
            for($j=0; $j<6; $j++) {
                $course = new stdClass();
                $course->fullname     = 'course'.$j;
                $course->shortname    = 'c'.$j;
                $course->summary      = 'bah bah bah';
                $course->newsitems    = 0;
                $course->numsections  = 1;
                $course->category     = $catid;
                $course->format       = 'topics';
                $course->timecreated  = time();
                $course->visible      = 1;
                $course->timemodified = $course->timecreated;
                $courseid = $DB->insert_record('course', $course);
                $section = new stdClass();
                $section->course        = $courseid;
                $section->section       = 0;
                $section->summaryformat = FORMAT_HTML;
                $DB->insert_record('course_sections', $section);
                $section->section       = 1;
                $DB->insert_record('course_sections', $section);
                $testcourses[] = $courseid;
                $coursecontext = context_course::instance($courseid);

                if ($j >= 5) {
                    continue;
                }
                // Add manual enrol instance
                $manualenrol->add_default_instance($DB->get_record('course', array('id'=>$courseid)));

                // Add block to each course
                $bi->parentcontextid = $coursecontext->id;
                $biid = $DB->insert_record('block_instances', $bi);
                context_block::instance($biid);
                $testblocks[] = $biid;

                // Add a resource to each course
                $page->course = $courseid;
                $pageid = $DB->insert_record('page', $page);
                $testpages[] = $pageid;
                $cm->course   = $courseid;
                $cm->instance = $pageid;
                $cm->id = $DB->insert_record('course_modules', $cm);
                $modcontext = context_module::instance($cm->id);

                // Add block to each module
                $bi->parentcontextid = $modcontext->id;
                $biid = $DB->insert_record('block_instances', $bi);
                context_block::instance($biid);
                $testblocks[] = $biid;
            }
        }

        // Make sure all contexts were created properly
        $count = 1; //system
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEqual($DB->count_records('context'), $count);
        $this->assertEqual($DB->count_records('context', array('depth'=>0)), 0);
        $this->assertEqual($DB->count_records('context', array('path'=>NULL)), 0);


        // ====== context_helper::get_level_name() ================================

        $levels = context_helper::get_all_levels();
        foreach ($levels as $level=>$classname) {
            $name = context_helper::get_level_name($level);
            $this->assertFalse(empty($name));
        }


        // ======= context::instance_by_id(), context_xxx::instance();

        $context = context::instance_by_id($frontpagecontext->id);
        $this->assertidentical($context->contextlevel, CONTEXT_COURSE);
        $this->assertFalse(context::instance_by_id(-1, IGNORE_MISSING));
        try {
            context::instance_by_id(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertTrue(context_system::instance() instanceof context_system);
        $this->assertTrue(context_coursecat::instance($testcategories[0]) instanceof context_coursecat);
        $this->assertTrue(context_course::instance($testcourses[0]) instanceof context_course);
        $this->assertTrue(context_module::instance($testpages[0]) instanceof context_module);
        $this->assertTrue(context_block::instance($testblocks[0]) instanceof context_block);

        $this->assertFalse(context_coursecat::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_course::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_module::instance(-1, IGNORE_MISSING));
        $this->assertFalse(context_block::instance(-1, IGNORE_MISSING));
        try {
            context_coursecat::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_course::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_module::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        try {
            context_block::instance(-1);
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }


        // ======= $context->get_url(), $context->get_context_name(), $context->get_capabilities() =========

        $testcontexts = array();
        $testcontexts[CONTEXT_SYSTEM]    = context_system::instance();
        $testcontexts[CONTEXT_COURSECAT] = context_coursecat::instance($testcategories[0]);
        $testcontexts[CONTEXT_COURSE]    = context_course::instance($testcourses[0]);
        $testcontexts[CONTEXT_MODULE]    = context_module::instance($testpages[0]);
        $testcontexts[CONTEXT_BLOCK]     = context_block::instance($testblocks[0]);

        foreach ($testcontexts as $context) {
            $name = $context->get_context_name(true, true);
            $this->assertFalse(empty($name));

            $this->assertTrue($context->get_url() instanceof moodle_url);

            $caps = $context->get_capabilities();
            $this->assertTrue(is_array($caps));
            foreach ($caps as $cap) {
                $cap = (array)$cap;
                $this->assertIdentical(array_keys($cap), array('id', 'name', 'captype', 'contextlevel', 'component', 'riskbitmask'));
            }
        }
        unset($testcontexts);

        // ===== $context->get_course_context() =========================================

        $this->assertFalse($systemcontext->get_course_context(false));
        try {
            $systemcontext->get_course_context();
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $context = context_coursecat::instance($testcategories[0]);
        $this->assertFalse($context->get_course_context(false));
        try {
            $context->get_course_context();
            $this->fail('exception expected');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
        $this->assertIdentical($frontpagecontext->get_course_context(true), $frontpagecontext);
        $this->assertIdentical($frontpagepagecontext->get_course_context(true), $frontpagecontext);
        $this->assertIdentical($frontpagepageblockcontext->get_course_context(true), $frontpagecontext);


        // ======= $context->get_parent_context(), $context->get_parent_contexts(), $context->get_parent_context_ids() =======

        $userid = reset($testusers);
        $usercontext = context_user::instance($userid);
        $this->assertIdentical($usercontext->get_parent_context(), $systemcontext);
        $this->assertIdentical($usercontext->get_parent_contexts(), array($systemcontext->id=>$systemcontext));
        $this->assertIdentical($usercontext->get_parent_contexts(true), array($usercontext->id=>$usercontext, $systemcontext->id=>$systemcontext));

        $this->assertIdentical($systemcontext->get_parent_contexts(), array());
        $this->assertIdentical($systemcontext->get_parent_contexts(true), array($systemcontext->id=>$systemcontext));
        $this->assertIdentical($systemcontext->get_parent_context_ids(), array());
        $this->assertIdentical($systemcontext->get_parent_context_ids(true), array($systemcontext->id));

        $this->assertIdentical($frontpagecontext->get_parent_context(), $systemcontext);
        $this->assertIdentical($frontpagecontext->get_parent_contexts(), array($systemcontext->id=>$systemcontext));
        $this->assertIdentical($frontpagecontext->get_parent_contexts(true), array($frontpagecontext->id=>$frontpagecontext, $systemcontext->id=>$systemcontext));
        $this->assertIdentical($frontpagecontext->get_parent_context_ids(), array($systemcontext->id));
        $this->assertEqual($frontpagecontext->get_parent_context_ids(true), array($frontpagecontext->id, $systemcontext->id));

        $this->assertIdentical($systemcontext->get_parent_context(), false);
        $frontpagecontext = context_course::instance($SITE->id);
        $parent = $systemcontext;
        foreach ($testcategories as $catid) {
            $catcontext = context_coursecat::instance($catid);
            $this->assertIdentical($catcontext->get_parent_context(), $parent);
            $parent = $catcontext;
        }
        $this->assertIdentical($frontpagepagecontext->get_parent_context(), $frontpagecontext);
        $this->assertIdentical($frontpageblockcontext->get_parent_context(), $frontpagecontext);
        $this->assertIdentical($frontpagepageblockcontext->get_parent_context(), $frontpagepagecontext);


        // ====== $context->get_child_contexts() ================================

        $children = $systemcontext->get_child_contexts();
        $this->assertEqual(count($children)+1, $DB->count_records('context'));

        $context = context_coursecat::instance($testcategories[3]);
        $children = $context->get_child_contexts();
        $countcats    = 0;
        $countcourses = 0;
        $countblocks  = 0;
        foreach ($children as $child) {
            if ($child->contextlevel == CONTEXT_COURSECAT) {
                $countcats++;
            }
            if ($child->contextlevel == CONTEXT_COURSE) {
                $countcourses++;
            }
            if ($child->contextlevel == CONTEXT_BLOCK) {
                $countblocks++;
            }
        }
        $this->assertEqual(count($children), 8);
        $this->assertEqual($countcats, 1);
        $this->assertEqual($countcourses, 6);
        $this->assertEqual($countblocks, 1);

        $context = context_course::instance($testcourses[2]);
        $children = $context->get_child_contexts();
        $this->assertEqual(count($children), 3);

        $context = context_module::instance($testpages[3]);
        $children = $context->get_child_contexts();
        $this->assertEqual(count($children), 1);

        $context = context_block::instance($testblocks[1]);
        $children = $context->get_child_contexts();
        $this->assertEqual(count($children), 0);

        unset($children);
        unset($countcats);
        unset($countcourses);
        unset($countblocks);


        // ======= context_helper::reset_caches() ============================

        context_helper::reset_caches();
        $this->assertEqual(context_inspection::test_context_cache_size(), 0);
        context_course::instance($SITE->id);
        $this->assertEqual(context_inspection::test_context_cache_size(), 1);


        // ======= context preloading ========================================

        context_helper::reset_caches();
        $sql = "SELECT ".context_helper::get_preload_record_columns_sql('c')."
                  FROM {context} c
                 WHERE c.contextlevel <> ".CONTEXT_SYSTEM;
        $records = $DB->get_records_sql($sql);
        $firstrecord = reset($records);
        $columns = context_helper::get_preload_record_columns('c');
        $firstrecord = (array)$firstrecord;
        $this->assertIdentical(array_keys($firstrecord), array_values($columns));
        context_helper::reset_caches();
        foreach ($records as $record) {
            context_helper::preload_from_record($record);
            $this->assertIdentical($record, new stdClass());
        }
        $this->assertEqual(context_inspection::test_context_cache_size(), count($records));
        unset($records);
        unset($columns);

        context_helper::reset_caches();
        context_helper::preload_course($SITE->id);
        $this->assertEqual(context_inspection::test_context_cache_size(), 4);

        // ====== assign_capability(), unassign_capability() ====================

        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $allroles['teacher'], $frontpagecontext->id);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEqual($rc->permission, CAP_ALLOW);
        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $allroles['teacher'], $frontpagecontext->id);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEqual($rc->permission, CAP_ALLOW);
        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $allroles['teacher'], $frontpagecontext, true);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertEqual($rc->permission, CAP_PREVENT);

        assign_capability('moodle/site:accessallgroups', CAP_INHERIT, $allroles['teacher'], $frontpagecontext);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $allroles['teacher'], $frontpagecontext);
        unassign_capability('moodle/site:accessallgroups', $allroles['teacher'], $frontpagecontext, true);
        $rc = $DB->get_record('role_capabilities', array('contextid'=>$frontpagecontext->id, 'roleid'=>$allroles['teacher'], 'capability'=>'moodle/site:accessallgroups'));
        $this->assertFalse($rc);
        unassign_capability('moodle/site:accessallgroups', $allroles['teacher'], $frontpagecontext->id, true);
        unset($rc);

        accesslib_clear_all_caches(false); // must be done after assign_capability()


        // ======= role_assign(), role_unassign(), role_unassign_all() ==============

        $context = context_course::instance($testcourses[1]);
        $this->assertEqual($DB->count_records('role_assignments', array('contextid'=>$context->id)), 0);
        role_assign($allroles['teacher'], $testusers[1], $context->id);
        role_assign($allroles['teacher'], $testusers[2], $context->id);
        role_assign($allroles['manager'], $testusers[1], $context->id);
        $this->assertEqual($DB->count_records('role_assignments', array('contextid'=>$context->id)), 3);
        role_unassign($allroles['teacher'], $testusers[1], $context->id);
        $this->assertEqual($DB->count_records('role_assignments', array('contextid'=>$context->id)), 2);
        role_unassign_all(array('contextid'=>$context->id));
        $this->assertEqual($DB->count_records('role_assignments', array('contextid'=>$context->id)), 0);
        unset($context);

        accesslib_clear_all_caches(false); // just in case


        // ====== has_capability(), get_users_by_capability(), role_switch(), reload_all_capabilities() and friends ========================

        $adminid = get_admin()->id;
        $guestid = $CFG->siteguest;

        // Enrol some users into some courses
        $course1 = $DB->get_record('course', array('id'=>$testcourses[22]), '*', MUST_EXIST);
        $course2 = $DB->get_record('course', array('id'=>$testcourses[7]), '*', MUST_EXIST);
        $cms = $DB->get_records('course_modules', array('course'=>$course1->id), 'id');
        $cm1 = reset($cms);
        $blocks = $DB->get_records('block_instances', array('parentcontextid'=>context_module::instance($cm1->id)->id), 'id');
        $block1 = reset($blocks);
        $instance1 = $DB->get_record('enrol', array('enrol'=>'manual', 'courseid'=>$course1->id));
        $instance2 = $DB->get_record('enrol', array('enrol'=>'manual', 'courseid'=>$course2->id));
        for($i=0; $i<9; $i++) {
            $manualenrol->enrol_user($instance1, $testusers[$i], $allroles['student']);
        }
        $manualenrol->enrol_user($instance1, $testusers[8], $allroles['teacher']);
        $manualenrol->enrol_user($instance1, $testusers[9], $allroles['editingteacher']);

        for($i=10; $i<15; $i++) {
            $manualenrol->enrol_user($instance2, $testusers[$i], $allroles['student']);
        }
        $manualenrol->enrol_user($instance2, $testusers[15], $allroles['editingteacher']);

        // Add tons of role assignments - the more the better
        role_assign($allroles['coursecreator'], $testusers[11], context_coursecat::instance($testcategories[2]));
        role_assign($allroles['manager'], $testusers[12], context_coursecat::instance($testcategories[1]));
        role_assign($allroles['student'], $testusers[9], context_module::instance($cm1->id));
        role_assign($allroles['teacher'], $testusers[8], context_module::instance($cm1->id));
        role_assign($allroles['guest'], $testusers[13], context_course::instance($course1->id));
        role_assign($allroles['teacher'], $testusers[7], context_block::instance($block1->id));
        role_assign($allroles['manager'], $testusers[9], context_block::instance($block1->id));
        role_assign($allroles['editingteacher'], $testusers[9], context_course::instance($course1->id));

        role_assign($allroles['teacher'], $adminid, context_course::instance($course1->id));
        role_assign($allroles['editingteacher'], $adminid, context_block::instance($block1->id));

        // Add tons of overrides - the more the better
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultuserroleid, $frontpageblockcontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpageblockcontext, true);
        assign_capability('moodle/block:view', CAP_PROHIBIT, $allroles['guest'], $frontpageblockcontext, true);
        assign_capability('block/online_users:viewlist', CAP_PREVENT, $allroles['user'], $frontpageblockcontext, true);
        assign_capability('block/online_users:viewlist', CAP_PREVENT, $allroles['student'], $frontpageblockcontext, true);

        assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $CFG->defaultuserroleid, $frontpagepagecontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagepagecontext, true);
        assign_capability('mod/page:view', CAP_PREVENT, $allroles['guest'], $frontpagepagecontext, true);
        assign_capability('mod/page:view', CAP_ALLOW, $allroles['user'], $frontpagepagecontext, true);
        assign_capability('moodle/page:view', CAP_ALLOW, $allroles['student'], $frontpagepagecontext, true);

        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultuserroleid, $frontpagecontext, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagecontext, true);
        assign_capability('mod/page:view', CAP_ALLOW, $allroles['guest'], $frontpagecontext, true);
        assign_capability('mod/page:view', CAP_PROHIBIT, $allroles['user'], $frontpagecontext, true);

        assign_capability('mod/page:view', CAP_PREVENT, $allroles['guest'], $systemcontext, true);

        accesslib_clear_all_caches(false); // must be done after assign_capability()

        // Extra tests for guests and not-logged-in users because they can not be verified by cross checking
        // with get_users_by_capability() where they are ignored
        $this->assertFalse(has_capability('moodle/block:view', $frontpageblockcontext, $guestid));
        $this->assertFalse(has_capability('mod/page:view', $frontpagepagecontext, $guestid));
        $this->assertTrue(has_capability('mod/page:view', $frontpagecontext, $guestid));
        $this->assertFalse(has_capability('mod/page:view', $systemcontext, $guestid));

        $this->assertFalse(has_capability('moodle/block:view', $frontpageblockcontext, 0));
        $this->assertFalse(has_capability('mod/page:view', $frontpagepagecontext, 0));
        $this->assertTrue(has_capability('mod/page:view', $frontpagecontext, 0));
        $this->assertFalse(has_capability('mod/page:view', $systemcontext, 0));

        $this->assertFalse(has_capability('moodle/course:create', $systemcontext, $testusers[11]));
        $this->assertTrue(has_capability('moodle/course:create', context_coursecat::instance($testcategories[2]), $testusers[11]));
        $this->assertFalse(has_capability('moodle/course:create', context_course::instance($testcourses[1]), $testusers[11]));
        $this->assertTrue(has_capability('moodle/course:create', context_course::instance($testcourses[19]), $testusers[11]));

        $this->assertFalse(has_capability('moodle/course:update', context_course::instance($testcourses[1]), $testusers[9]));
        $this->assertFalse(has_capability('moodle/course:update', context_course::instance($testcourses[19]), $testusers[9]));
        $this->assertFalse(has_capability('moodle/course:update', $systemcontext, $testusers[9]));

        // Test the list of enrolled users
        $coursecontext = context_course::instance($course1->id);
        $enrolled = get_enrolled_users($coursecontext);
        $this->assertEqual(count($enrolled), 10);
        for($i=0; $i<10; $i++) {
            $this->assertTrue(isset($enrolled[$testusers[$i]]));
        }
        $enrolled = get_enrolled_users($coursecontext, 'moodle/course:update');
        $this->assertEqual(count($enrolled), 1);
        $this->assertTrue(isset($enrolled[$testusers[9]]));
        unset($enrolled);

        // role switching
        $userid = $testusers[9];
        $USER = $DB->get_record('user', array('id'=>$userid));
        load_all_capabilities();
        $coursecontext = context_course::instance($course1->id);
        $this->assertTrue(has_capability('moodle/course:update', $coursecontext));
        $this->assertFalse(is_role_switched($course1->id));
        role_switch($allroles['student'], $coursecontext);
        $this->assertTrue(is_role_switched($course1->id));
        $this->assertEqual($USER->access['rsw'][$coursecontext->path],  $allroles['student']);
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext));
        reload_all_capabilities();
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext));
        role_switch(0, $coursecontext);
        $this->assertTrue(has_capability('moodle/course:update', $coursecontext));
        $userid = $adminid;
        $USER = $DB->get_record('user', array('id'=>$userid));
        load_all_capabilities();
        $coursecontext = context_course::instance($course1->id);
        $blockcontext = context_block::instance($block1->id);
        $this->assertTrue(has_capability('moodle/course:update', $blockcontext));
        role_switch($allroles['student'], $coursecontext);
        $this->assertEqual($USER->access['rsw'][$coursecontext->path],  $allroles['student']);
        $this->assertFalse(has_capability('moodle/course:update', $blockcontext));
        reload_all_capabilities();
        $this->assertFalse(has_capability('moodle/course:update', $blockcontext));
        load_all_capabilities();
        $this->assertTrue(has_capability('moodle/course:update', $blockcontext));

        // temp course role for enrol
        $DB->delete_records('cache_flags', array()); // this prevents problem with dirty contexts immediately resetting the temp role - this is a known problem...
        $userid = $testusers[5];
        $roleid = $allroles['editingteacher'];
        $USER = $DB->get_record('user', array('id'=>$userid));
        load_all_capabilities();
        $coursecontext = context_course::instance($course1->id);
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext));
        $this->assertFalse(isset($USER->access['ra'][$coursecontext->path][$roleid]));
        load_temp_course_role($coursecontext, $roleid);
        $this->assertEqual($USER->access['ra'][$coursecontext->path][$roleid], $roleid);
        $this->assertTrue(has_capability('moodle/course:update', $coursecontext));
        remove_temp_course_roles($coursecontext);
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext, $userid));
        load_temp_course_role($coursecontext, $roleid);
        reload_all_capabilities();
        $this->assertFalse(has_capability('moodle/course:update', $coursecontext, $userid));
        $USER = new stdClass();
        $USER->id = 0;

        // Now cross check has_capability() with get_users_by_capability(), each using different code paths,
        // they have to be kept in sync, usually only one of them breaks, so we know when something is wrong,
        // at the same time validate extra restrictions (guest read only no risks, admin exception, non existent and deleted users)
        $contexts = $DB->get_records('context', array(), 'id');
        $contexts = array_values($contexts);
        $capabilities = $DB->get_records('capabilities', array(), 'id');
        $capabilities = array_values($capabilities);
        $roles = array($allroles['guest'], $allroles['user'], $allroles['teacher'], $allroles['editingteacher'], $allroles['coursecreator'], $allroles['manager']);

        // Random time!
        srand(666);
        foreach($testusers as $userid) { // no guest or deleted
            // each user gets 0-20 random roles
            $rcount = rand(0, 10);
            for($j=0; $j<$rcount; $j++) {
                $roleid = $roles[rand(0, count($roles)-1)];
                $contextid = $contexts[rand(0, count($contexts)-1)]->id;
                role_assign($roleid, $userid, $contextid);
            }
        }
        $permissions = array(CAP_ALLOW, CAP_PREVENT, CAP_INHERIT, CAP_PREVENT);
        for($j=0; $j<10000; $j++) {
            $roleid = $roles[rand(0, count($roles)-1)];
            $contextid = $contexts[rand(0, count($contexts)-1)]->id;
            $permission = $permissions[rand(0,count($permissions)-1)];
            $capname = $capabilities[rand(0, count($capabilities)-1)]->name;
            assign_capability($capname, $permission, $roleid, $contextid, true);
        }
        unset($permissions);
        unset($roles);
        unset($contexts);
        unset($users);
        unset($capabilities);

        accesslib_clear_all_caches(false); // must be done after assign_capability()

        // Test time - let's set up some real user, just in case the logic for USER affects the others...
        $USER = $DB->get_record('user', array('id'=>$testusers[3]));
        load_all_capabilities();

        $contexts = $DB->get_records('context', array(), 'id');
        $users = $DB->get_records('user', array(), 'id', 'id');
        $capabilities = $DB->get_records('capabilities', array(), 'id');
        $users[0]  = null; // not-logged-in user
        $users[-1] = null; // non-existent user

        foreach ($contexts as $crecord) {
            $context = context::instance_by_id($crecord->id);
            if ($coursecontext = $context->get_course_context(false)) {
                $enrolled = get_enrolled_users($context);
            } else {
                $enrolled = array();
            }
            foreach ($capabilities as $cap) {
                $allowed = get_users_by_capability($context, $cap->name, 'u.id, u.username');
                if ($enrolled) {
                    $enrolledwithcap = get_enrolled_users($context, $cap->name);
                } else {
                    $enrolledwithcap = array();
                }
                foreach ($users as $userid=>$unused) {
                    if ($userid == 0 or isguestuser($userid)) {
                        if ($userid == 0) {
                            $CFG->forcelogin = true;
                            $this->assertFalse(has_capability($cap->name, $context, $userid));
                            unset($CFG->forcelogin);
                        }
                        if (($cap->captype === 'write') or ($cap->riskbitmask & (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))) {
                            $this->assertFalse(has_capability($cap->name, $context, $userid));
                        }
                        $this->assertFalse(isset($allowed[$userid]));
                    } else {
                        if (is_siteadmin($userid)) {
                            $this->assertTrue(has_capability($cap->name, $context, $userid, true));
                        }
                        $hascap = has_capability($cap->name, $context, $userid, false);
                        $this->assertIdentical($hascap, isset($allowed[$userid]), "Capability result mismatch user:$userid, context:$context->id, $cap->name, hascap: ".(int)$hascap." ");
                        if (isset($enrolled[$userid])) {
                            $this->assertIdentical(isset($allowed[$userid]), isset($enrolledwithcap[$userid]), "Enrolment with capability result mismatch user:$userid, context:$context->id, $cap->name, hascap: ".(int)$hascap." ");
                        }
                    }
                }
            }
        }
        // Back to nobody
        $USER = new stdClass();
        $USER->id = 0;
        unset($contexts);
        unset($users);
        unset($capabilities);

        // Now let's do all the remaining tests that break our carefully prepared fake site



        // ======= $context->mark_dirty() =======================================

        $DB->delete_records('cache_flags', array());
        accesslib_clear_all_caches(false);
        $systemcontext->mark_dirty();
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$systemcontext->path]));
        $this->assertTrue(isset($ACCESSLIB_PRIVATE->dirtycontexts[$systemcontext->path]));


        // ======= $context->reload_if_dirty(); =================================

        $DB->delete_records('cache_flags', array());
        accesslib_clear_all_caches(false);
        load_all_capabilities();
        $context = context_course::instance($testcourses[2]);
        $page = $DB->get_record('page', array('course'=>$testcourses[2]));
        $pagecontext = context_module::instance($page->id);

        $context->mark_dirty();
        $this->assertTrue(isset($ACCESSLIB_PRIVATE->dirtycontexts[$context->path]));
        $USER->access['test'] = true;
        $context->reload_if_dirty();
        $this->assertFalse(isset($USER->access['test']));

        $context->mark_dirty();
        $this->assertTrue(isset($ACCESSLIB_PRIVATE->dirtycontexts[$context->path]));
        $USER->access['test'] = true;
        $pagecontext->reload_if_dirty();
        $this->assertFalse(isset($USER->access['test']));


        // ======= context_helper::build_all_paths() ============================

        $oldcontexts = $DB->get_records('context', array(), 'id');
        $DB->set_field_select('context', 'path', NULL, "contextlevel <> ".CONTEXT_SYSTEM);
        $DB->set_field_select('context', 'depth', 0, "contextlevel <> ".CONTEXT_SYSTEM);
        context_helper::build_all_paths();
        $newcontexts = $DB->get_records('context', array(), 'id');
        $this->assertIdentical($oldcontexts, $newcontexts);
        unset($oldcontexts);
        unset($newcontexts);


        // ======= $context->reset_paths() ======================================

        $context = context_course::instance($testcourses[2]);
        $children = $context->get_child_contexts();
        $context->reset_paths(false);
        $this->assertIdentical($DB->get_field('context', 'path', array('id'=>$context->id)), NULL);
        $this->assertEqual($DB->get_field('context', 'depth', array('id'=>$context->id)), 0);
        foreach ($children as $child) {
            $this->assertIdentical($DB->get_field('context', 'path', array('id'=>$child->id)), NULL);
            $this->assertEqual($DB->get_field('context', 'depth', array('id'=>$child->id)), 0);
        }
        $this->assertEqual(count($children)+1, $DB->count_records('context', array('depth'=>0)));
        $this->assertEqual(count($children)+1, $DB->count_records('context', array('path'=>NULL)));

        $context = context_course::instance($testcourses[2]);
        $context->reset_paths(true);
        $context = context_course::instance($testcourses[2]);
        $this->assertEqual($DB->get_field('context', 'path', array('id'=>$context->id)), $context->path);
        $this->assertEqual($DB->get_field('context', 'depth', array('id'=>$context->id)), $context->depth);
        $this->assertEqual(0, $DB->count_records('context', array('depth'=>0)));
        $this->assertEqual(0, $DB->count_records('context', array('path'=>NULL)));


        // ====== $context->update_moved(); ======================================

        accesslib_clear_all_caches(false);
        $DB->delete_records('cache_flags', array());
        $course = $DB->get_record('course', array('id'=>$testcourses[0]));
        $context = context_course::instance($course->id);
        $oldpath = $context->path;
        $miscid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $categorycontext = context_coursecat::instance($miscid);
        $course->category = $miscid;
        $DB->update_record('course', $course);
        $context->update_moved($categorycontext);

        $context = context_course::instance($course->id);
        $this->assertIdentical($context->get_parent_context(), $categorycontext);
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$oldpath]));
        $this->assertTrue(isset($dirty[$context->path]));


        // ====== $context->delete_content() =====================================

        context_helper::reset_caches();
        $context = context_module::instance($testpages[3]);
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEqual(1, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        $context->delete_content();
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEqual(0, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));


        // ====== $context->delete() =============================

        context_helper::reset_caches();
        $context = context_module::instance($testpages[4]);
        $this->assertTrue($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertEqual(1, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        $bi = $DB->get_record('block_instances', array('parentcontextid'=>$context->id));
        $bicontext = context_block::instance($bi->id);
        $DB->delete_records('cache_flags', array());
        $context->delete(); // should delete also linked blocks
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$context->path]));
        $this->assertFalse($DB->record_exists('context', array('id'=>$context->id)));
        $this->assertFalse($DB->record_exists('context', array('id'=>$bicontext->id)));
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_MODULE, 'instanceid'=>$testpages[4])));
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_BLOCK, 'instanceid'=>$bi->id)));
        $this->assertEqual(0, $DB->count_records('block_instances', array('parentcontextid'=>$context->id)));
        context_module::instance($testpages[4]);


        // ====== context_helper::delete_instance() =============================

        context_helper::reset_caches();
        $lastcourse = array_pop($testcourses);
        $this->assertTrue($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$lastcourse)));
        $coursecontext = context_course::instance($lastcourse);
        $this->assertEqual(context_inspection::test_context_cache_size(), 1);
        $this->assertFalse($coursecontext->instanceid == CONTEXT_COURSE);
        $DB->delete_records('cache_flags', array());
        context_helper::delete_instance(CONTEXT_COURSE, $lastcourse);
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$coursecontext->path]));
        $this->assertEqual(context_inspection::test_context_cache_size(), 0);
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$lastcourse)));
        context_course::instance($lastcourse);


        // ======= context_helper::create_instances() ==========================

        $prevcount = $DB->count_records('context');
        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        context_helper::create_instances(null, true);
        $this->assertIdentical($DB->count_records('context'), $prevcount);
        $this->assertEqual($DB->count_records('context', array('depth'=>0)), 0);
        $this->assertEqual($DB->count_records('context', array('path'=>NULL)), 0);

        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        $DB->delete_records('block_instances', array());
        $prevcount = $DB->count_records('context');
        $DB->delete_records_select('context', 'contextlevel <> '.CONTEXT_SYSTEM);
        context_helper::create_instances(null, true);
        $this->assertIdentical($DB->count_records('context'), $prevcount);
        $this->assertEqual($DB->count_records('context', array('depth'=>0)), 0);
        $this->assertEqual($DB->count_records('context', array('path'=>NULL)), 0);


        // ======= context_helper::cleanup_instances() ==========================

        $lastcourse = $DB->get_field_sql("SELECT MAX(id) FROM {course}");
        $DB->delete_records('course', array('id'=>$lastcourse));
        $lastcategory = $DB->get_field_sql("SELECT MAX(id) FROM {course_categories}");
        $DB->delete_records('course_categories', array('id'=>$lastcategory));
        $lastuser = $DB->get_field_sql("SELECT MAX(id) FROM {user} WHERE deleted=0");
        $DB->delete_records('user', array('id'=>$lastuser));
        $DB->delete_records('block_instances', array('parentcontextid'=>$frontpagepagecontext->id));
        $DB->delete_records('course_modules', array('id'=>$frontpagepagecontext->instanceid));
        context_helper::cleanup_instances();
        $count = 1; //system
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEqual($DB->count_records('context'), $count);


        // ======= context cache size restrictions ==============================

        $testusers= array();
        for ($i=0; $i<CONTEXT_CACHE_MAX_SIZE + 100; $i++) {
            $user = new stdClass();
            $user->auth         = 'manual';
            $user->firstname    = 'xuser'.$i;
            $user->lastname     = 'xuser'.$i;
            $user->username     = 'xuser'.$i;
            $user->password     = 'doesnotexist';
            $user->email        = "xuser$i@example.com";
            $user->confirmed    = 1;
            $user->mnethostid   = $CFG->mnet_localhost_id;
            $user->lang         = $CFG->lang;
            $user->maildisplay  = 1;
            $user->timemodified = time();
            $user->lastip       = '0.0.0.0';
            $userid = $DB->insert_record('user', $user);
            $testusers[$i] = $userid;
        }
        context_helper::create_instances(null, true);
        context_helper::reset_caches();
        for ($i=0; $i<CONTEXT_CACHE_MAX_SIZE + 100; $i++) {
            context_user::instance($testusers[$i]);
            if ($i == CONTEXT_CACHE_MAX_SIZE - 1) {
                $this->assertEqual(context_inspection::test_context_cache_size(), CONTEXT_CACHE_MAX_SIZE);
            } else if ($i == CONTEXT_CACHE_MAX_SIZE) {
                // once the limit is reached roughly 1/3 of records should be removed from cache
                $this->assertEqual(context_inspection::test_context_cache_size(), (int)(CONTEXT_CACHE_MAX_SIZE * (2/3) +102));
            }
        }
        // We keep the first 100 cached
        $prevsize = context_inspection::test_context_cache_size();
        for ($i=0; $i<100; $i++) {
            context_user::instance($testusers[$i]);
            $this->assertEqual(context_inspection::test_context_cache_size(), $prevsize);
        }
        context_user::instance($testusers[102]);
        $this->assertEqual(context_inspection::test_context_cache_size(), $prevsize+1);
        unset($testusers);



        // =================================================================
        // ======= basic test of legacy functions ==========================
        // =================================================================
        // note: watch out, the fake site might be pretty borked already

        $this->assertIdentical(get_system_context(), context_system::instance());

        foreach ($DB->get_records('context') as $contextid=>$record) {
            $context = context::instance_by_id($contextid);
            $this->assertIdentical(get_context_instance_by_id($contextid), $context);
            $this->assertIdentical(get_context_instance($record->contextlevel, $record->instanceid), $context);
            $this->assertIdentical(get_parent_contexts($context), $context->get_parent_context_ids());
            if ($context->id == SYSCONTEXTID) {
                $this->assertIdentical(get_parent_contextid($context), false);
            } else {
                $this->assertIdentical(get_parent_contextid($context), $context->get_parent_context()->id);
            }
        }

        $children = get_child_contexts($systemcontext);
        $this->assertEqual(count($children), $DB->count_records('context')-1);
        unset($children);

        $DB->delete_records('context', array('contextlevel'=>CONTEXT_BLOCK));
        create_contexts();
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_BLOCK)));

        $DB->set_field('context', 'depth', 0, array('contextlevel'=>CONTEXT_BLOCK));
        build_context_path();
        $this->assertFalse($DB->record_exists('context', array('depth'=>0)));

        $lastcourse = $DB->get_field_sql("SELECT MAX(id) FROM {course}");
        $DB->delete_records('course', array('id'=>$lastcourse));
        $lastcategory = $DB->get_field_sql("SELECT MAX(id) FROM {course_categories}");
        $DB->delete_records('course_categories', array('id'=>$lastcategory));
        $lastuser = $DB->get_field_sql("SELECT MAX(id) FROM {user} WHERE deleted=0");
        $DB->delete_records('user', array('id'=>$lastuser));
        $DB->delete_records('block_instances', array('parentcontextid'=>$frontpagepagecontext->id));
        $DB->delete_records('course_modules', array('id'=>$frontpagepagecontext->instanceid));
        cleanup_contexts();
        $count = 1; //system
        $count += $DB->count_records('user', array('deleted'=>0));
        $count += $DB->count_records('course_categories');
        $count += $DB->count_records('course');
        $count += $DB->count_records('course_modules');
        $count += $DB->count_records('block_instances');
        $this->assertEqual($DB->count_records('context'), $count);

        context_helper::reset_caches();
        preload_course_contexts($SITE->id);
        $this->assertEqual(context_inspection::test_context_cache_size(), 1);

        context_helper::reset_caches();
        list($select, $join) = context_instance_preload_sql('c.id', CONTEXT_COURSECAT, 'ctx');
        $sql = "SELECT c.id $select FROM {course_categories} c $join";
        $records = $DB->get_records_sql($sql);
        foreach ($records as $record) {
            context_instance_preload($record);
            $record = (array)$record;
            $this->assertEqual(1, count($record)); // only id left
        }
        $this->assertEqual(count($records), context_inspection::test_context_cache_size());

        accesslib_clear_all_caches(true);
        $DB->delete_records('cache_flags', array());
        mark_context_dirty($systemcontext->path);
        $dirty = get_cache_flags('accesslib/dirtycontexts', time()-2);
        $this->assertTrue(isset($dirty[$systemcontext->path]));

        accesslib_clear_all_caches(false);
        $DB->delete_records('cache_flags', array());
        $course = $DB->get_record('course', array('id'=>$testcourses[2]));
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $oldpath = $context->path;
        $miscid = $DB->get_field_sql("SELECT MIN(id) FROM {course_categories}");
        $categorycontext = context_coursecat::instance($miscid);
        $course->category = $miscid;
        $DB->update_record('course', $course);
        context_moved($context, $categorycontext);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $this->assertIdentical($context->get_parent_context(), $categorycontext);

        $this->assertTrue($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$testcourses[2])));
        delete_context(CONTEXT_COURSE, $testcourses[2]);
        $this->assertFalse($DB->record_exists('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$testcourses[2])));

        $name = get_contextlevel_name(CONTEXT_COURSE);
        $this->assertFalse(empty($name));

        $context = get_context_instance(CONTEXT_COURSE, $testcourses[2]);
        $name = print_context_name($context);
        $this->assertFalse(empty($name));

        $url = get_context_url($coursecontext);
        $this->assertFalse($url instanceof modole_url);

        $page = $DB->get_record('page', array('id'=>$testpages[7]));
        $context = get_context_instance(CONTEXT_MODULE, $page->id);
        $coursecontext = get_course_context($context);
        $this->assertEqual($coursecontext->contextlevel, CONTEXT_COURSE);
        $this->assertEqual(get_courseid_from_context($context), $page->course);

        $caps = fetch_context_capabilities($systemcontext);
        $this->assertTrue(is_array($caps));
        unset($caps);
    }

    public function tearDown() {
        global $USER, $SITE;
        if (isset($this->accesslibprevuser)) {
            $USER = $this->accesslibprevuser;
        }
        if (isset($this->accesslibprevsite)) {
            $SITE = $this->accesslibprevsite;
        }


        parent::tearDown();
    }
}

