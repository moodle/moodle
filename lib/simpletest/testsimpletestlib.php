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

class accesslib_test extends MoodleUnitTestCase {

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
        // Create three nested contexts. instanceid does not matter for this. Just
        // ensure we don't violate any unique keys by using an unlikely number.
        // We will fix paths in a second.
        $contexts = $this->load_test_data('context',
                array('contextlevel', 'instanceid', 'path', 'depth'), array(
                array(10, 666, '', 1),
                array(40, 666, '', 2),
                array(50, 666, '', 3),
        ));
        $contexts[0]->path = '/' . $contexts[0]->id;
        $DB->set_field('context', 'path', $contexts[0]->path, array('id' => $contexts[0]->id));
        $contexts[1]->path = $contexts[0]->path . '/' . $contexts[1]->id;
        $DB->set_field('context', 'path', $contexts[1]->path, array('id' => $contexts[1]->id));
        $contexts[2]->path = $contexts[1]->path . '/' . $contexts[2]->id;
        $DB->set_field('context', 'path', $contexts[2]->path, array('id' => $contexts[2]->id));

        // Just test load_test_data and delete_test_data for now.
        $this->assertTrue($DB->record_exists('context', array('id' => $contexts[1]->id)));
        $this->assertTrue($DB->get_field('context', 'path', array('id' => $contexts[2]->id)), $contexts[2]->path);
        $this->delete_test_data('context', $contexts);
        $this->assertFalse($DB->record_exists('context', array('id' => $contexts[1]->id)));
    }
}
?>
