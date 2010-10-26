<?php
/**
 * Unit tests for (some of) ../accesslib.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class accesslib_test extends UnitTestCaseUsingDatabase {

    public static $includecoverage = array('lib/accesslib.php');

    function test_get_parent_contexts() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $this->assertEqual(get_parent_contexts($context), array());

        $context = new stdClass;
        $context->path = '/1/25';
        $this->assertEqual(get_parent_contexts($context), array(1));

        $context = new stdClass;
        $context->path = '/1/123/234/345/456';
        $this->assertEqual(get_parent_contexts($context), array(345, 234, 123, 1));
    }

    function test_get_parent_contextid() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $this->assertFalse(get_parent_contextid($context));

        $context = new stdClass;
        $context->path = '/1/25';
        $this->assertEqual(get_parent_contextid($context), 1);

        $context = new stdClass;
        $context->path = '/1/123/234/345/456';
        $this->assertEqual(get_parent_contextid($context), 345);
    }

    function test_get_users_by_capability() {
        global $CFG;

        $tablenames = array('capabilities', 'context', 'role', 'role_capabilities',
                'role_allow_assign', 'role_allow_override', 'role_assignments', 'role_context_levels',
                'user', 'groups_members', 'cache_flags', 'events_handlers', 'user_lastaccess', 'course');
        $this->create_test_tables($tablenames, 'lib');

        accesslib_clear_all_caches_for_unit_testing();
        $this->switch_to_test_db();
        $this->switch_to_test_cfg();

        $course = new stdClass();
        $course->category = 0;
        $this->testdb->insert_record('course', $course);
        $syscontext = get_system_context(false);

    /// Install the roles system.
        $coursecreatorrole  = create_role(get_string('coursecreators'), 'coursecreator',
                                          get_string('coursecreatorsdescription'), 'coursecreator');
        $editteacherrole    = create_role(get_string('defaultcourseteacher'), 'editingteacher',
                                          get_string('defaultcourseteacherdescription'), 'editingteacher');
        $noneditteacherrole = create_role(get_string('noneditingteacher'), 'teacher',
                                          get_string('noneditingteacherdescription'), 'teacher');
        $studentrole        = create_role(get_string('defaultcoursestudent'), 'student',
                                          get_string('defaultcoursestudentdescription'), 'student');
        $guestrole          = create_role(get_string('guest'), 'guest',
                                          get_string('guestdescription'), 'guest');
        $userrole           = create_role(get_string('authenticateduser'), 'user',
                                          get_string('authenticateduserdescription'), 'user');

        /// Now is the correct moment to install capabilities - after creation of legacy roles, but before assigning of roles
        update_capabilities('moodle');
        update_capabilities('mod_forum');
        update_capabilities('mod_quiz');

        // Create some nested contexts. instanceid does not matter for this. Just
        // ensure we don't violate any unique keys by using an unlikely number.
        // We will fix paths in a second.
        $contexts = $this->load_test_data('context',
                array('contextlevel', 'instanceid', 'path', 'depth'), array(
           1 => array(40, 666, '', 2),
           2 => array(50, 666, '', 3),
           3 => array(70, 666, '', 4),
        ));
        $contexts[0] = $syscontext;
        $contexts[1]->path = $contexts[0]->path . '/' . $contexts[1]->id;
        $this->testdb->set_field('context', 'path', $contexts[1]->path, array('id' => $contexts[1]->id));
        $contexts[2]->path = $contexts[1]->path . '/' . $contexts[2]->id;
        $this->testdb->set_field('context', 'path', $contexts[2]->path, array('id' => $contexts[2]->id));
        $contexts[3]->path = $contexts[2]->path . '/' . $contexts[3]->id;
        $this->testdb->set_field('context', 'path', $contexts[3]->path, array('id' => $contexts[3]->id));

        // Now make some test users.
        $users = $this->load_test_data('user',
                 array('username', 'confirmed', 'deleted'), array(
        'a' =>   array('a',         1,           0),
        'cc' =>  array('cc',        1,           0),
        't1' =>  array('t1',        1,           0),
        's1' =>  array('s1',        1,           0),
        's2' =>  array('s2',        1,           0),
        'del' => array('del',       1,           1),
        'unc' => array('unc',       0,           0),
        ));

        // Get some of the standard roles.
        $creator = $this->testdb->get_record('role', array('shortname' => 'coursecreator'));
        $teacher = $this->testdb->get_record('role', array('shortname' => 'editingteacher'));
        $student = $this->testdb->get_record('role', array('shortname' => 'student'));
        $authuser = $this->testdb->get_record('role', array('shortname' => 'user'));

        // And some role assignments.
        $ras = $this->load_test_data('role_assignments',
                array('userid', 'roleid', 'contextid'), array(
        'cc' => array($users['cc']->id, $creator->id, $contexts[1]->id),
        't1' => array($users['t1']->id, $teacher->id, $contexts[2]->id),
        's1' => array($users['s1']->id, $student->id, $contexts[2]->id),
        's2' => array($users['s2']->id, $student->id, $contexts[2]->id),
        ));

        // And make user a into admin
        $CFG->siteadmins = $users['a']->id;
        $CFG->defaultuserroleid = $userrole;

        // And some group memebership.
        $gms = $this->load_test_data('groups_members',
                array('userid', 'groupid'), array(
                array($users['t1']->id, 666),
                array($users['s1']->id, 666),
                array($users['s2']->id, 667),
        ));

        // Test some simple cases - check that looking in coruse and module contextlevel gives the same answer.
        foreach (array(2, 3) as $conindex) {
            $results = get_users_by_capability($contexts[$conindex], 'mod/forum:replypost');
            // note: admin accounts are never returned, so no admin return here
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['t1']->id, $users['s1']->id, $users['s2']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    $results));
            // Paging.
            $firstuser = reset($results);
            $this->assertEqual(array($firstuser->id => $firstuser), get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', 0, 1));
            $seconduser = next($results);
            $this->assertEqual(array($seconduser->id => $seconduser), get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', 1, 1));
            // $doanything = false (ignored now)
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['t1']->id, $users['s1']->id, $users['s2']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', '', '', '', '', false)));
            // group
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['t1']->id, $users['s1']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', '', '', 666)));
            // exceptions
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['s1']->id, $users['s2']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', '', '', '', array($users['t1']->id))));
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['s1']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', '', '', 666, array($users['t1']->id))));
            // $useviewallgroups
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['t1']->id, $users['s2']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', '', '', 667, '', false, false, true)));
            // More than one capability.
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['s1']->id, $users['s2']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    get_users_by_capability($contexts[$conindex], array('mod/quiz:attempt', 'mod/quiz:reviewmyattempts'))));
        }

// For reference: get_users_by_capability argument order:
// $context, $capability, $fields='', $sort='', $limitfrom='', $limitnum='',
// $groups='', $exceptions='', $doanything=true, $view=false, $useviewallgroups=false

        // Now add some role overrides.
        $rcs = $this->load_test_data('role_capabilities',
                array('capability',                 'roleid',      'contextid',      'permission'), array(
                array('mod/forum:replypost',        $student->id,  $contexts[1]->id, CAP_PREVENT),
                array('mod/forum:replypost',        $student->id,  $contexts[3]->id, CAP_ALLOW),
                array('mod/quiz:attempt',           $student->id,  $contexts[2]->id, CAP_PREVENT),
                array('mod/forum:startdiscussion',  $student->id,  $contexts[1]->id, CAP_PROHIBIT),
                array('mod/forum:startdiscussion',  $student->id,  $contexts[3]->id, CAP_ALLOW),
                array('mod/forum:viewrating',       $authuser->id, $contexts[1]->id, CAP_PROHIBIT),
                array('mod/forum:createattachment', $authuser->id, $contexts[3]->id, CAP_PREVENT),
        ));

        // Now test the overridden cases.
        // Students prevented at category level, with and without doanything.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['t1']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[2], 'mod/forum:replypost')));
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['t1']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[2], 'mod/forum:replypost', '', '', '', '', '', '', false)));
        // Students prevented at category level, but re-allowed at module level, with and without doanything.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['t1']->id, $users['s1']->id, $users['s2']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:replypost', '', '', '', '', '', '', false)));
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['t1']->id, $users['s1']->id, $users['s2']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:replypost')));
        // Students prohibited at category level, re-allowed at module level should have no effect.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['t1']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[2], 'mod/forum:startdiscussion')));
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['t1']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:startdiscussion')));
        // Prevent on logged-in user should be overridden by student allow.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['t1']->id, $users['s1']->id, $users['s2']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:createattachment')));

        // Prohibit on logged-in user should trump student/teacher allow.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array()),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:viewrating')));

        // More than one capability, where students have one, but not the other.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['s1']->id, $users['s2']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], array('mod/quiz:attempt', 'mod/quiz:reviewmyattempts'), '', '', '', '', '', '', false)));
    }

    function test_get_switchable_roles() {
        global $USER;

        $tablenames = array('role' , 'role_capabilities', 'role_assignments', 'role_allow_switch',
                'capabilities', 'context', 'role_names');
        $this->create_test_tables($tablenames, 'lib');

        $this->switch_to_test_db();
        $this->switch_to_test_cfg();

        // Ensure SYSCONTEXTID is set.
        get_context_instance(CONTEXT_SYSTEM);

        $contexts = $this->load_test_data('context',
                 array('contextlevel', 'instanceid', 'path', 'depth'), array(
        'sys' => array(CONTEXT_SYSTEM,     0, '/' . SYSCONTEXTID, 1),
        'cat' => array(CONTEXT_COURSECAT, 66, '/' . SYSCONTEXTID . '/' . (SYSCONTEXTID + 1), 2),
        'cou' => array(CONTEXT_COURSE,   666, '/' . SYSCONTEXTID . '/' . (SYSCONTEXTID + 1) . '/' . (SYSCONTEXTID + 2), 3),
        'fp'  => array(CONTEXT_COURSE,   SITEID, '/' . SYSCONTEXTID . '/' . SITEID, 2)));
        $this->testdb->set_field('context', 'id', SYSCONTEXTID, array('id' => $contexts['sys']->id));
        $this->testdb->set_field('context', 'id', SYSCONTEXTID + 1, array('id' => $contexts['cat']->id));
        $this->testdb->set_field('context', 'id', SYSCONTEXTID + 2, array('id' => $contexts['cou']->id));
        $syscontext = $contexts['sys'];
        $syscontext->id = SYSCONTEXTID;
        $context = $contexts['cou'];
        $context->id = SYSCONTEXTID + 2;

        $roles = $this->load_test_data('role',
                   array( 'name', 'shortname', 'description', 'sortorder'), array(
        'r1' =>    array(   'r1',        'r1',    'not null',          2),
        'r2' =>    array(   'r2',        'r2',    'not null',          3),
        'funny' => array('funny',     'funny',    'not null',          4)));
        $r1id = $roles['r1']->id;
        $r2id = $roles['r2']->id;
        $funnyid = $roles['funny']->id; // strange role

        // Note that get_switchable_roles requires at least one capability for
        // each role. I am not really clear why it was implemented that way
        // but this makes the test work.
        $roles = $this->load_test_data('role_capabilities',
                array('roleid', 'capability'), array(
                    array($r1id, 'moodle/say:hello'),
                    array($r2id, 'moodle/say:hello'),
                    array($funnyid, 'moodle/say:hello'),
        ));

        $this->load_test_data('role_assignments',
                array('userid', 'contextid',   'roleid'), array(
                array(      2, SYSCONTEXTID + 1 , $r1id),
                array(      3, SYSCONTEXTID + 2 , $r2id)));

        $this->load_test_data('role_allow_switch',
                array('roleid', 'allowswitch'), array(
                array(  $r1id ,        $r2id),
                array(  $r2id ,        $r1id),
                array(  $r2id ,        $r2id),
                array(  $r2id ,     $funnyid)));

        // r1 should be able to switch to r2, but this user only has r1 in $context, not $syscontext.
        $this->switch_global_user_id(2);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assert(new ArraysHaveSameValuesExpectation(array()), array_keys(get_switchable_roles($syscontext)));
        $this->assert(new ArraysHaveSameValuesExpectation(array($r2id)), array_keys(get_switchable_roles($context)));
        $this->revert_global_user_id();

        // The table says r2 should be able to switch to all of r1, r2 and funny;
        // this used to be restricted further beyond the switch table (for example
        // to prevent you switching to roles with doanything) but is not any more
        // (for example because doanything does not exist).
        $this->switch_global_user_id(3);
        accesslib_clear_all_caches_for_unit_testing();
        $this->assert(new ArraysHaveSameValuesExpectation(array()), array_keys(get_switchable_roles($syscontext)));
        $this->assert(new ArraysHaveSameValuesExpectation(array($r2id, $r1id, $funnyid)), array_keys(get_switchable_roles($context)));
    }

    function test_context_cache() {
        // Create cache, empty
        $cache = new context_cache();
        $this->assertEqual(0, $cache->get_approx_count());

        // Put context in cache
        $context = (object)array('id'=>37,'contextlevel'=>50,'instanceid'=>13);
        $cache->add($context);
        $this->assertEqual(1, $cache->get_approx_count());

        // Get context out of cache
        $this->assertEqual($context, $cache->get(50, 13));
        $this->assertEqual($context, $cache->get_by_id(37));

        // Try to get context that isn't there
        $this->assertEqual(false, $cache->get(50, 99));
        $this->assertEqual(false, $cache->get(99, 13));
        $this->assertEqual(false, $cache->get_by_id(99));

        // Remove context from cache
        $cache->remove($context);
        $this->assertEqual(0, $cache->get_approx_count());
        $this->assertEqual(false, $cache->get(50, 13));
        $this->assertEqual(false, $cache->get_by_id(37));

        // Add a stack of contexts to cache
        for($i=0; $i<context_cache::MAX_SIZE; $i++) {
            $context = (object)array('id'=>$i, 'contextlevel'=>50,
                    'instanceid'=>$i+1);
            $cache->add($context);
        }
        $this->assertEqual(context_cache::MAX_SIZE, $cache->get_approx_count());

        // Get a random sample from the middle
        $sample = (object)array(
                'id'=>174, 'contextlevel'=>50, 'instanceid'=>175);
        $this->assertEqual($sample, $cache->get(50, 175));
        $this->assertEqual($sample, $cache->get_by_id(174));

        // Now add one more; should result in size being reduced
        $context = (object)array('id'=>99999, 'contextlevel'=>50,
                'instanceid'=>99999);
        $cache->add($context);
        $this->assertEqual(context_cache::MAX_SIZE - context_cache::REDUCE_SIZE + 1,
                $cache->get_approx_count());

        // Check that the first ones are no longer there
        $this->assertEqual(false, $cache->get(50, 175));
        $this->assertEqual(false, $cache->get_by_id(174));

        // Check that the last ones are still there
        $bigid = context_cache::MAX_SIZE - 10;
        $sample = (object)array(
                'id'=>$bigid, 'contextlevel'=>50, 'instanceid'=>$bigid+1);
        $this->assertEqual($sample, $cache->get(50, $bigid+1));
        $this->assertEqual($sample, $cache->get_by_id($bigid));

        // Reset the cache
        $cache->reset();
        $this->assertEqual(0, $cache->get_approx_count());
        $this->assertEqual(false, $cache->get(50, $bigid+1));
    }
}

