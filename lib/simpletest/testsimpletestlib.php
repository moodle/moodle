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

    function test_zero_attr() {
        $expectation = new ContainsTagWithAttribute('span', 'class', 0);
        $this->assertTrue($expectation->test('<span class="0">message</span>'));
    }

    function test_zero_attr_does_not_match_blank() {
        $expectation = new ContainsTagWithAttribute('span', 'class', 0);
        $this->assertFalse($expectation->test('<span class="">message</span>'));
    }

    function test_blank_attr() {
        $expectation = new ContainsTagWithAttribute('span', 'class', '');
        $this->assertTrue($expectation->test('<span class="">message</span>'));
    }

    function test_blank_attr_does_not_match_zero() {
        $expectation = new ContainsTagWithAttribute('span', 'class', '');
        $this->assertFalse($expectation->test('<span class="0">message</span>'));
    }
}


/**
 * Unit tests for the ContainsTagWithAttribute class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsTagWithAttributes_test extends UnitTestCase {
    function test_simple() {
        $content = <<<END
<input id="qIhr6wWLTt3,1_omact_gen_14" name="qIhr6wWLTt3,1_omact_gen_14" onclick="if(this.hasSubmitted) { return false; } this.hasSubmitted=true; preSubmit(this.form); return true;" type="submit" value="Check" />
END;
        $expectation = new ContainsTagWithAttributes('input',
                array('type' => 'submit', 'name' => 'qIhr6wWLTt3,1_omact_gen_14', 'value' => 'Check'));
        $this->assert($expectation, $content);
    }

    function test_zero_attr() {
        $expectation = new ContainsTagWithAttributes('span', array('class' => 0));
        $this->assertTrue($expectation->test('<span class="0">message</span>'));
    }

    function test_zero_attr_does_not_match_blank() {
        $expectation = new ContainsTagWithAttributes('span', array('class' => 0));
        $this->assertFalse($expectation->test('<span class="">message</span>'));
    }

    function test_blank_attr() {
        $expectation = new ContainsTagWithAttributes('span', array('class' => ''));
        $this->assertTrue($expectation->test('<span class="">message</span>'));
    }

    function test_blank_attr_does_not_match_zero() {
        $expectation = new ContainsTagWithAttributes('span', array('class' => ''));
        $this->assertFalse($expectation->test('<span class="0">message</span>'));
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


/**
 * Unit tests for the {@link ContainsSelectExpectation} class.
 *
 * @copyright 2010 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ContainsSelectExpectation_test extends UnitTestCase {
    function test_matching_select_passes() {
        $expectation = new ContainsSelectExpectation('selectname', array('Choice1', 'Choice2'));
        $this->assertTrue($expectation->test('
                <select name="selectname">
                    <option value="0">Choice1</option>
                    <option value="1">Choice2</option>
                </select>'));
    }

    function test_fails_if_no_select() {
        $expectation = new ContainsSelectExpectation('selectname', array('Choice1', 'Choice2'));
        $this->assertFalse($expectation->test('<span>should not match</span>'));
    }

    function test_select_with_missing_choices_fails() {
        $expectation = new ContainsSelectExpectation('selectname', array('Choice1', 'Choice2'));
        $this->assertFalse($expectation->test('
                <select name="selectname">
                    <option value="0">Choice1</option>
                </select>'));
    }

    function test_select_with_extra_choices_fails() {
        $expectation = new ContainsSelectExpectation('selectname', array('Choice1'));
        $this->assertFalse($expectation->test('
                <select name="selectname">
                    <option value="0">Choice1</option>
                    <option value="1">Choice2</option>
                </select>'));
    }

    function test_select_with_wrong_order_choices_fails() {
        $expectation = new ContainsSelectExpectation('selectname', array('Choice1'));
        $this->assertFalse($expectation->test('
                <select name="selectname">
                    <option value="1">Choice2</option>
                    <option value="0">Choice1</option>
                </select>'));
    }

    function test_select_check_selected_pass() {
        $expectation = new ContainsSelectExpectation('selectname',
                array('key1' => 'Choice1', 'key2' => 'Choice2'), 'key2');
        $this->assertTrue($expectation->test('
                <select name="selectname">
                    <option value="key1">Choice1</option>
                    <option value="key2" selected="selected">Choice2</option>
                </select>'));
    }

    function test_select_check_wrong_one_selected_fail() {
        $expectation = new ContainsSelectExpectation('selectname',
                array('key1' => 'Choice1', 'key2' => 'Choice2'), 'key2');
        $this->assertFalse($expectation->test('
                <select name="selectname">
                    <option value="key1" selected="selected">Choice1</option>
                    <option value="key2">Choice2</option>
                </select>'));
    }

    function test_select_check_nothing_selected_fail() {
        $expectation = new ContainsSelectExpectation('selectname',
                array('key1' => 'Choice1', 'key2' => 'Choice2'), 'key2');
        $this->assertFalse($expectation->test('
                <select name="selectname">
                    <option value="key1">Choice1</option>
                    <option value="key2">Choice2</option>
                </select>'));
    }

    function test_select_disabled_pass() {
        $expectation = new ContainsSelectExpectation('selectname',
                array('key1' => 'Choice1', 'key2' => 'Choice2'), null, false);
        $this->assertTrue($expectation->test('
                <select name="selectname" disabled="disabled">
                    <option value="key1">Choice1</option>
                    <option value="key2">Choice2</option>
                </select>'));
    }

    function test_select_disabled_fail1() {
        $expectation = new ContainsSelectExpectation('selectname',
                array('key1' => 'Choice1', 'key2' => 'Choice2'), null, true);
        $this->assertFalse($expectation->test('
                <select name="selectname" disabled="disabled">
                    <option value="key1">Choice1</option>
                    <option value="key2">Choice2</option>
                </select>'));
    }

    function test_select_disabled_fail2() {
        $expectation = new ContainsSelectExpectation('selectname',
                array('key1' => 'Choice1', 'key2' => 'Choice2'), null, false);
        $this->assertFalse($expectation->test('
                <select name="selectname">
                    <option value="key1">Choice1</option>
                    <option value="key2">Choice2</option>
                </select>'));
    }
}


/**
 * Unit tests for the {@link DoesNotContainTagWithAttributes} class.
 *
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class DoesNotContainTagWithAttributes_test extends UnitTestCase {
    function test_simple() {
        $content = <<<END
<input id="qIhr6wWLTt3,1_omact_gen_14" name="qIhr6wWLTt3,1_omact_gen_14" onclick="if(this.hasSubmitted) { return false; } this.hasSubmitted=true; preSubmit(this.form); return true;" type="submit" value="Check" />
END;
        $expectation = new DoesNotContainTagWithAttributes('input',
                array('type' => 'submit', 'name' => 'qIhr6wWLTt3,1_omact_gen_14', 'value' => 'Check'));
        $this->assertFalse($expectation->test($content));
    }

    function test_zero_attr() {
        $expectation = new DoesNotContainTagWithAttributes('span', array('class' => 0));
        $this->assertFalse($expectation->test('<span class="0">message</span>'));
    }

    function test_zero_different_attr_ok() {
        $expectation = new DoesNotContainTagWithAttributes('span', array('class' => 'shrub'));
        $this->assertTrue($expectation->test('<span class="tree">message</span>'));
    }

    function test_blank_attr() {
        $expectation = new DoesNotContainTagWithAttributes('span', array('class' => ''));
        $this->assertFalse($expectation->test('<span class="">message</span>'));
    }

    function test_blank_attr_does_not_match_zero() {
        $expectation = new ContainsTagWithAttributes('span', array('class' => ''));
        $this->assertFalse($expectation->test('<span class="0">message</span>'));
    }
}
