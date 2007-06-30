<?php
/**
 * Unit tests for (some of) ../questionlib.php.
 *
 * @copyright &copy; Jamie Pratt
 * @author Jamie Pratt
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
global $CFG;
require_once($CFG->libdir.'/questionlib.php');
class questionlib_test extends UnitTestCase {

    function setUp() {
    }

    function tearDown() {
    }

    function test_find_file_links_from_html() {
        global $CFG;
        $this->assertEqual(array_keys(find_file_links_from_html("hello <a href='{$CFG->wwwroot}/file.php/2/gg/go.php'>hello</a>", 2)),
                                    array("gg/go.php"));
        $this->assertEqual(array_keys(find_file_links_from_html("hello <a href=\"{$CFG->wwwroot}/file.php/2/gg/go.php\">hello</a>", 2)),
                                     array("gg/go.php"));
        $this->assertEqual(array_keys(find_file_links_from_html('hello <a href=\''.$CFG->wwwroot.'/file.php/1/ggo/fghfgh/sdfsdf/sdf/go.php\'>hello</a>', 1)),
                array('ggo/fghfgh/sdfsdf/sdf/go.php'));
        $this->assertEqual(array_keys(find_file_links_from_html('hello <a href=\''.$CFG->wwwroot.'/file.php?file=/1/ggo/fghfgh/sdfsdf/sdf/go.php\'>hello</a>'.
                                "hello <a href='{$CFG->wwwroot}/file.php/2/gg/go.php'>hello</a>" . "hello <a href='{$CFG->wwwroot}/file.php/2/gg/go.php'>hello</a>" .
                                "hello <a href='{$CFG->wwwroot}/file.php/2/gg/go.php'>hello</a>" ."hello <a href='{$CFG->wwwroot}/file.php/2/gg/go.php'>hello</a>",1)),
                array('ggo/fghfgh/sdfsdf/sdf/go.php'));
        $this->assertEqual(array_keys(find_file_links_from_html('hello <img src=\''.$CFG->wwwroot.'/file.php/1/ggo/fghfgh/sdfsdf/sdf/go.php\' />', 1)),
                array('ggo/fghfgh/sdfsdf/sdf/go.php'));
    }
}
?>
