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

class accesslib_test extends FakeDBUnitTestCase {

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
        global $DB;
        // Warning, this method assumes that the standard roles are set up with
        // the standard definitions.

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);

        // Create some nested contexts. instanceid does not matter for this. Just
        // ensure we don't violate any unique keys by using an unlikely number.
        // We will fix paths in a second.
        $contexts = $this->load_test_data('context',
                array('contextlevel', 'instanceid', 'path', 'depth'), array(
           1 => array(40, 666, '', 2),
           2 => array(50, 666, '', 3),
           3 => array(70, 666, '', 4),
        ));
        $contexts[0] = $systemcontext;
        $contexts[1]->path = $contexts[0]->path . '/' . $contexts[1]->id;
        $DB->set_field('context', 'path', $contexts[1]->path, array('id' => $contexts[1]->id));
        $contexts[2]->path = $contexts[1]->path . '/' . $contexts[2]->id;
        $DB->set_field('context', 'path', $contexts[2]->path, array('id' => $contexts[2]->id));
        $contexts[3]->path = $contexts[2]->path . '/' . $contexts[3]->id;
        $DB->set_field('context', 'path', $contexts[3]->path, array('id' => $contexts[3]->id));

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
        $admin = $DB->get_record('role', array('shortname' => 'admin'));
        $creator = $DB->get_record('role', array('shortname' => 'coursecreator'));
        $teacher = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $student = $DB->get_record('role', array('shortname' => 'student'));
        $authuser = $DB->get_record('role', array('shortname' => 'user'));

        // And some role assignments.
        $ras = $this->load_test_data('role_assignments',
                array('userid', 'roleid', 'contextid'), array(
        'a' =>  array($users['a']->id, $admin->id, $contexts[0]->id),
        'cc' => array($users['cc']->id, $creator->id, $contexts[1]->id),
        't1' => array($users['t1']->id, $teacher->id, $contexts[2]->id),
        's1' => array($users['s1']->id, $student->id, $contexts[2]->id),
        's2' => array($users['s2']->id, $student->id, $contexts[2]->id),
        ));

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
            $this->assert(new ArraysHaveSameValuesExpectation(
                    array($users['a']->id, $users['t1']->id, $users['s1']->id, $users['s2']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    $results));
            // Paging.
            $firstuser = reset($results);
            $this->assertEqual(array($firstuser->id => $firstuser), get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', 0, 1));
            $seconduser = next($results);
            $this->assertEqual(array($seconduser->id => $seconduser), get_users_by_capability($contexts[$conindex], 'mod/forum:replypost', '', '', 1, 1));
            // $doanything = false
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
                    array($users['a']->id, $users['s1']->id, $users['s2']->id)),
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
                    array($users['a']->id, $users['s1']->id, $users['s2']->id)),
                    array_map(create_function('$o', 'return $o->id;'),
                    get_users_by_capability($contexts[$conindex], array('mod/quiz:attempt', 'mod/quiz:reviewmyattempts'))));
        }
        // System context, specifically checking doanythign.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['a']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[0], 'moodle/site:doanything')));

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
//                array('mod/forum:startdiscussion', $student->id, $contexts[1]->id, CAP_PROHIBIT),
//                array('mod/forum:startdiscussion', $student->id, $contexts[3]->id, CAP_ALLOW),
        ));

        // Now test the overridden cases.
        // Students prevented at category level, with and without doanything.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['a']->id, $users['t1']->id)),
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
                array($users['a']->id, $users['t1']->id, $users['s1']->id, $users['s2']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:replypost')));
        // Students prohibited at category level, re-allowed at module level should have no effect.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['a']->id, $users['t1']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[2], 'mod/forum:startdiscussion')));
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['a']->id, $users['t1']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:startdiscussion')));
        // Prevent on logged-in user should be overridden by student allow.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['a']->id, $users['t1']->id, $users['s1']->id, $users['s2']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:createattachment')));

        // Prohibit on logged-in user should trump student/teacher allow.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['a']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], 'mod/forum:viewrating')));

        // More than one capability, where students have one, but not the other.
        $this->assert(new ArraysHaveSameValuesExpectation(
                array($users['s1']->id, $users['s2']->id)),
                array_map(create_function('$o', 'return $o->id;'),
                get_users_by_capability($contexts[3], array('mod/quiz:attempt', 'mod/quiz:reviewmyattempts'), '', '', '', '', '', '', false)));

        // Clean up everything we added.
        unset($contexts[0]);
        $this->delete_test_data('role_capabilities', $rcs);
        $this->delete_test_data('groups_members', $gms);
        $this->delete_test_data('role_assignments', $ras);
        $this->delete_test_data('user', $users);
        $this->delete_test_data('context', $contexts);
    }
}
?>
