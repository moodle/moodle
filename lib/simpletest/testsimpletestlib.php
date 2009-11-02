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

class simpletestlib_test extends FakeDBUnitTestCase {

    function test_load_delete_test_data() {
        global $DB;
        $contexts = $this->load_test_data('context',
                array('contextlevel', 'instanceid', 'path', 'depth'), array(
                array(10, 666, '', 1),
                array(40, 666, '', 2),
                array(50, 666, '', 3),
        ));

        // Just test load_test_data and delete_test_data for now.
        $this->assertTrue($DB->record_exists('context', array('id' => $contexts[1]->id)));
        $this->assertTrue($DB->get_field('context', 'contextlevel', array('id' => $contexts[2]->id)), $contexts[2]->contextlevel);
        $this->delete_test_data('context', $contexts);
        $this->assertFalse($DB->record_exists('context', array('id' => $contexts[1]->id)));
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


