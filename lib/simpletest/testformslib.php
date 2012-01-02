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
 * Unit tests for /lib/formslib.php.
 *
 * @package   file
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/radio.php');
require_once($CFG->libdir . '/form/select.php');
require_once($CFG->libdir . '/form/text.php');

class formslib_test extends UnitTestCase {

    public function test_require_rule() {
        global $CFG;

        $strictformsrequired = false;
        if (!empty($CFG->strictformsrequired)) {
            $strictformsrequired = $CFG->strictformsrequired;
        }

        $rule = new MoodleQuickForm_Rule_Required();

        // First run the tests with strictformsrequired off
        $CFG->strictformsrequired = false;
        // Passes
        $this->assertTrue($rule->validate('Something'));
        $this->assertTrue($rule->validate("Something\nmore"));
        $this->assertTrue($rule->validate("\nmore"));
        $this->assertTrue($rule->validate(" more "));
        $this->assertTrue($rule->validate("0"));
        $this->assertTrue($rule->validate(0));
        $this->assertTrue($rule->validate(true));
        $this->assertTrue($rule->validate(' '));
        $this->assertTrue($rule->validate('      '));
        $this->assertTrue($rule->validate("\t"));
        $this->assertTrue($rule->validate("\n"));
        $this->assertTrue($rule->validate("\r"));
        $this->assertTrue($rule->validate("\r\n"));
        $this->assertTrue($rule->validate(" \t  \n  \r "));
        $this->assertTrue($rule->validate('<p></p>'));
        $this->assertTrue($rule->validate('<p> </p>'));
        $this->assertTrue($rule->validate('<p>x</p>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile" />'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"/>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"></img>'));
        $this->assertTrue($rule->validate('<hr />'));
        $this->assertTrue($rule->validate('<hr/>'));
        $this->assertTrue($rule->validate('<hr>'));
        $this->assertTrue($rule->validate('<hr></hr>'));
        $this->assertTrue($rule->validate('<br />'));
        $this->assertTrue($rule->validate('<br/>'));
        $this->assertTrue($rule->validate('<br>'));
        $this->assertTrue($rule->validate('&nbsp;'));
        // Fails
        $this->assertFalse($rule->validate(''));
        $this->assertFalse($rule->validate(false));
        $this->assertFalse($rule->validate(null));

        // Now run the same tests with it on to make sure things work as expected
        $CFG->strictformsrequired = true;
        // Passes
        $this->assertTrue($rule->validate('Something'));
        $this->assertTrue($rule->validate("Something\nmore"));
        $this->assertTrue($rule->validate("\nmore"));
        $this->assertTrue($rule->validate(" more "));
        $this->assertTrue($rule->validate("0"));
        $this->assertTrue($rule->validate(0));
        $this->assertTrue($rule->validate(true));
        $this->assertTrue($rule->validate('<p>x</p>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile" />'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"/>'));
        $this->assertTrue($rule->validate('<img src="smile.jpg" alt="smile"></img>'));
        $this->assertTrue($rule->validate('<hr />'));
        $this->assertTrue($rule->validate('<hr/>'));
        $this->assertTrue($rule->validate('<hr>'));
        $this->assertTrue($rule->validate('<hr></hr>'));
        // Fails
        $this->assertFalse($rule->validate(' '));
        $this->assertFalse($rule->validate('      '));
        $this->assertFalse($rule->validate("\t"));
        $this->assertFalse($rule->validate("\n"));
        $this->assertFalse($rule->validate("\r"));
        $this->assertFalse($rule->validate("\r\n"));
        $this->assertFalse($rule->validate(" \t  \n  \r "));
        $this->assertFalse($rule->validate('<p></p>'));
        $this->assertFalse($rule->validate('<p> </p>'));
        $this->assertFalse($rule->validate('<br />'));
        $this->assertFalse($rule->validate('<br/>'));
        $this->assertFalse($rule->validate('<br>'));
        $this->assertFalse($rule->validate('&nbsp;'));
        $this->assertFalse($rule->validate(''));
        $this->assertFalse($rule->validate(false));
        $this->assertFalse($rule->validate(null));

        $CFG->strictformsrequired = $strictformsrequired;
    }

    public function test_generate_id_select() {
        $el = new MoodleQuickForm_select('choose_one', 'Choose one',
                array(1 => 'One', '2' => 'Two'));
        $el->_generateId();
        $this->assertEqual('id_choose_one', $el->getAttribute('id'));
    }

    public function test_generate_id_like_repeat() {
        $el = new MoodleQuickForm_text('text[7]', 'Type something');
        $el->_generateId();
        $this->assertEqual('id_text_7', $el->getAttribute('id'));
    }

    public function test_can_manually_set_id() {
        $el = new MoodleQuickForm_text('elementname', 'Type something',
                array('id' => 'customelementid'));
        $el->_generateId();
        $this->assertEqual('customelementid', $el->getAttribute('id'));
    }

    public function test_generate_id_radio() {
        $el = new MoodleQuickForm_radio('radio', 'Label', 'Choice label', 'choice_value');
        $el->_generateId();
        $this->assertEqual('id_radio_choice_value', $el->getAttribute('id'));
    }

    public function test_radio_can_manually_set_id() {
        $el = new MoodleQuickForm_radio('radio2', 'Label', 'Choice label', 'choice_value',
                array('id' => 'customelementid2'));
        $el->_generateId();
        $this->assertEqual('customelementid2', $el->getAttribute('id'));
    }

    public function test_generate_id_radio_like_repeat() {
        $el = new MoodleQuickForm_radio('repeatradio[2]', 'Label', 'Choice label', 'val');
        $el->_generateId();
        $this->assertEqual('id_repeatradio_2_val', $el->getAttribute('id'));
    }

    public function test_rendering() {
        $form = new formslib_test_form();
        ob_start();
        $form->display();
        $html = ob_get_clean();

        $this->assert(new ContainsTagWithAttributes('select', array(
                'id' => 'id_choose_one', 'name' => 'choose_one')), $html);

        $this->assert(new ContainsTagWithAttributes('input', array(
                'type' => 'text', 'id' => 'id_text_0', 'name' => 'text[0]')), $html);

        $this->assert(new ContainsTagWithAttributes('input', array(
                'type' => 'text', 'id' => 'id_text_1', 'name' => 'text[1]')), $html);

        $this->assert(new ContainsTagWithAttributes('input', array(
                'type' => 'radio', 'id' => 'id_radio_choice_value',
                'name' => 'radio', 'value' => 'choice_value')), $html);

        $this->assert(new ContainsTagWithAttributes('input', array(
                'type' => 'radio', 'id' => 'customelementid2', 'name' => 'radio2')), $html);

        $this->assert(new ContainsTagWithAttributes('input', array(
        'type' => 'radio', 'id' => 'id_repeatradio_0_2',
                        'name' => 'repeatradio[0]', 'value' => '2')), $html);

        $this->assert(new ContainsTagWithAttributes('input', array(
                'type' => 'radio', 'id' => 'id_repeatradio_2_1',
                'name' => 'repeatradio[2]', 'value' => '1')), $html);

        $this->assert(new ContainsTagWithAttributes('input', array(
                'type' => 'radio', 'id' => 'id_repeatradio_2_2',
                'name' => 'repeatradio[2]', 'value' => '2')), $html);
    }
}


/**
 * Test form to be used by {@link formslib_test::test_rendering()}.
 */
class formslib_test_form extends moodleform {
    public function definition() {
        $this->_form->addElement('select', 'choose_one', 'Choose one',
                array(1 => 'One', '2' => 'Two'));

        $repeatels = array(
            $this->_form->createElement('text', 'text', 'Type something')
        );
        $this->repeat_elements($repeatels, 2, array(), 'numtexts', 'addtexts');

        $this->_form->addElement('radio', 'radio', 'Label', 'Choice label', 'choice_value');

        $this->_form->addElement('radio', 'radio2', 'Label', 'Choice label', 'choice_value',
                array('id' => 'customelementid2'));

        $repeatels = array(
            $this->_form->createElement('radio', 'repeatradio', 'Choose {no}', 'One', 1),
            $this->_form->createElement('radio', 'repeatradio', 'Choose {no}', 'Two', 2),
        );
        $this->repeat_elements($repeatels, 3, array(), 'numradios', 'addradios');
    }
}