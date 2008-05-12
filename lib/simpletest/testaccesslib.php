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

class accesslib_test extends UnitTestCase {

    function setUp() {
    }

    function tearDown() {
    }

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
}
?>
