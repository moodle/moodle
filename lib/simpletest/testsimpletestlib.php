<?php
/**
 * Unit tests for (some of) ../simpletestlib.php.
 *
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package SimpleTestEx
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class simpletestlib_test extends UnitTestCaseUsingDatabase {

    function test_table_creation_and_data() {
        global $DB;

        $this->switch_to_test_db(); // All operations until end of test method will happen in test DB
        $dbman = $DB->get_manager();

        // Create table and test
        $this->create_test_table('context', 'lib');
        $this->assertTrue($dbman->table_exists('context'));

        $sampledata = array(
                          array('contextlevel' => 10, 'instanceid' => 666, 'path' => '', 'depth' => 1),
                          array('contextlevel' => 40, 'instanceid' => 666, 'path' => '', 'depth' => 2),
                          array('contextlevel' => 50, 'instanceid' => 666, 'path' => '', 'depth' => 3));

        foreach($sampledata as $key => $record) {
            $sampledata[$key]['id'] = $DB->insert_record('context', $record);
        }

        // Just test added data and delete later
        $this->assertEqual($DB->count_records('context'), 3);
        $this->assertTrue($DB->record_exists('context', array('id' => $sampledata[0]['id'])));
        $this->assertTrue($DB->get_field('context', 'contextlevel', array('id' => $sampledata[2]['id'])), $sampledata[2]['contextlevel']);
        $DB->delete_records('context');
        $this->assertFalse($DB->record_exists('context', array('id' => $sampledata[1]['id'])));
    }

    function test_tables_are_dropped() {
        global $DB;

        $this->switch_to_test_db(); // All operations until end of test method will happen in test DB
        $dbman = $DB->get_manager();
        // Previous method tearDown *must* delete all created tables, so here 'context' must not exist anymore
        $this->assertFalse($dbman->table_exists('context'));
    }
}


/**
 * Unit tests for the ContainsTagWithAttribute_test class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsTagWithAttribute_test extends UnitTestCase {
    function test_simple() {
        $expectation = new ContainsTagWithAttribute('span', 'class', 'error');
        $this->assertTrue($expectation->test('<span class="error">message</span>'));
    }

    function test_other_attrs() {
        $expectation = new ContainsTagWithAttribute('span', 'class', 'error');
        $this->assertTrue($expectation->test('<span     oneattr="thingy"   class  =  "error"  otherattr="thingy">message</span>'));
    }

    function test_fails() {
        $expectation = new ContainsTagWithAttribute('span', 'class', 'error');
        $this->assertFalse($expectation->test('<span class="mismatch">message</span>'));
    }

    function test_link() {
        $html = '<a href="http://www.test.com">Click Here</a>';
        $expectation = new ContainsTagWithAttribute('a', 'href', 'http://www.test.com');
        $this->assertTrue($expectation->test($html));
        $this->assert(new ContainsTagWithContents('a', 'Click Here'), $html);
    }

    function test_garbage() {
        $expectation = new ContainsTagWithAttribute('a', 'href', '!#@*%@_-)(*#0-735\\fdf//fdfg235-0970}$@}{#:~');
        $this->assertTrue($expectation->test('<a href="!#@*%@_-)(*#0-735\\fdf//fdfg235-0970}$@}{#:~">Click Here</a>'));

    }

    function test_inline_js() {
        $html = '<a title="Popup window" href="http://otheraddress.com" class="link" onclick="this.target=\'my_popup\';">Click here</a>';
        $this->assert(new ContainsTagWithAttribute('a', 'href', 'http://otheraddress.com'), $html);
    }

    function test_real_regression1() {
        $expectation = new ContainsTagWithAttribute('label', 'for', 'html_select4ac387224bf9d');
        $html = '<label for="html_select4ac387224bf9d">Cool menu</label><select name="mymenu" id="html_select4ac387224bf9d" class="menumymenu select"> <option value="0">Choose...</option><option value="10">ten</option><option value="c2">two</option></select>';
        $this->assert($expectation, $html);
    }
}

/**
 * Unit tests for the ContainsTagWithAttribute_test class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsTagWithContents_test extends UnitTestCase {
    function test_simple() {
        $expectation = new ContainsTagWithContents('span', 'message');
        $this->assertTrue($expectation->test('<span class="error">message</span>'));
    }

    function test_no_end() {
        $expectation = new ContainsTagWithContents('span', 'message');
        $this->assertFalse($expectation->test('<span class="error">message'));
    }
}


