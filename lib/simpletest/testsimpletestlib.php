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
        $this->assertTrue($expectation->test('<span     otherattr="thingy"   class  =  "error"  otherattr="thingy">message</span>'));
    }

    function test_fails() {
        $expectation = new ContainsTagWithAttribute('span', 'class', 'error');
        $this->assertFalse($expectation->test('<span class="mismatch">message</span>'));
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

?>
