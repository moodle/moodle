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
 * @package   core_form
 * @category  phpunit
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/radio.php');
require_once($CFG->libdir . '/form/select.php');
require_once($CFG->libdir . '/form/text.php');


class formslib_testcase extends advanced_testcase {

    public function test_require_rule() {
        global $CFG;

        $strictformsrequired = null;
        if (isset($CFG->strictformsrequired)) {
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

        if (isset($strictformsrequired)) {
            $CFG->strictformsrequired = $strictformsrequired;
        }
    }

    public function test_generate_id_select() {
        $el = new MoodleQuickForm_select('choose_one', 'Choose one',
            array(1 => 'One', '2' => 'Two'));
        $el->_generateId();
        $this->assertEquals('id_choose_one', $el->getAttribute('id'));
    }

    public function test_generate_id_like_repeat() {
        $el = new MoodleQuickForm_text('text[7]', 'Type something');
        $el->_generateId();
        $this->assertEquals('id_text_7', $el->getAttribute('id'));
    }

    public function test_can_manually_set_id() {
        $el = new MoodleQuickForm_text('elementname', 'Type something',
            array('id' => 'customelementid'));
        $el->_generateId();
        $this->assertEquals('customelementid', $el->getAttribute('id'));
    }

    public function test_generate_id_radio() {
        $el = new MoodleQuickForm_radio('radio', 'Label', 'Choice label', 'choice_value');
        $el->_generateId();
        $this->assertEquals('id_radio_choice_value', $el->getAttribute('id'));
    }

    public function test_radio_can_manually_set_id() {
        $el = new MoodleQuickForm_radio('radio2', 'Label', 'Choice label', 'choice_value',
            array('id' => 'customelementid2'));
        $el->_generateId();
        $this->assertEquals('customelementid2', $el->getAttribute('id'));
    }

    public function test_generate_id_radio_like_repeat() {
        $el = new MoodleQuickForm_radio('repeatradio[2]', 'Label', 'Choice label', 'val');
        $el->_generateId();
        $this->assertEquals('id_repeatradio_2_val', $el->getAttribute('id'));
    }

    public function test_rendering() {
        $form = new formslib_test_form();
        ob_start();
        $form->display();
        $html = ob_get_clean();

        $this->assertTag(array('tag'=>'select', 'id'=>'id_choose_one',
            'attributes'=>array('name'=>'choose_one')), $html);

        $this->assertTag(array('tag'=>'input', 'id'=>'id_text_0',
            'attributes'=>array('type'=>'text', 'name'=>'text[0]')), $html);

        $this->assertTag(array('tag'=>'input', 'id'=>'id_text_1',
            'attributes'=>array('type'=>'text', 'name'=>'text[1]')), $html);

        $this->assertTag(array('tag'=>'input', 'id'=>'id_radio_choice_value',
            'attributes'=>array('type'=>'radio', 'name'=>'radio', 'value'=>'choice_value')), $html);

        $this->assertTag(array('tag'=>'input', 'id'=>'customelementid2',
            'attributes'=>array('type'=>'radio', 'name'=>'radio2')), $html);

        $this->assertTag(array('tag'=>'input', 'id'=>'id_repeatradio_0_2',
            'attributes'=>array('type'=>'radio', 'name'=>'repeatradio[0]', 'value'=>'2')), $html);

        $this->assertTag(array('tag'=>'input', 'id'=>'id_repeatradio_2_1',
            'attributes'=>array('type'=>'radio', 'name'=>'repeatradio[2]', 'value'=>'1')), $html);

        $this->assertTag(array('tag'=>'input', 'id'=>'id_repeatradio_2_2',
            'attributes'=>array('type'=>'radio', 'name'=>'repeatradio[2]', 'value'=>'2')), $html);
    }

    public function test_settype_debugging_text() {
        $mform = new formslib_settype_debugging_text();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'texttest'? Defaulting to PARAM_RAW cleaning.");

        // Check form still there though
        $this->expectOutputRegex('/<input[^>]*name="texttest[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_hidden() {
        $mform = new formslib_settype_debugging_hidden();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'hiddentest'? Defaulting to PARAM_RAW cleaning.");

        // Check form still there though
        $this->expectOutputRegex('/<input[^>]*name="hiddentest[^>]*type="hidden/');
        $mform->display();
    }

    public function test_settype_debugging_url() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $mform = new formslib_settype_debugging_url();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'urltest'? Defaulting to PARAM_RAW cleaning.");

        // Check form still there though
        $this->expectOutputRegex('/<input[^>]*name="urltest"[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_repeat() {
        $mform = new formslib_settype_debugging_repeat();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'repeattest[0]'? Defaulting to PARAM_RAW cleaning.");

        // Check form still there though
        $this->expectOutputRegex('/<input[^>]*name="repeattest[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_repeat_ok() {
        $mform = new formslib_settype_debugging_repeat_ok();
        // No debugging expected here.

        $this->expectOutputRegex('/<input[^>]*name="repeattest[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_group() {
        $mform = new formslib_settype_debugging_group();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'groupel1'? Defaulting to PARAM_RAW cleaning.");
        $this->expectOutputRegex('/<input[^>]*name="groupel1"[^>]*type="text/');
        $this->expectOutputRegex('/<input[^>]*name="groupel2"[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_namedgroup() {
        $mform = new formslib_settype_debugging_namedgroup();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'namedgroup[groupel1]'? Defaulting to PARAM_RAW cleaning.");
        $this->expectOutputRegex('/<input[^>]*name="namedgroup\[groupel1\]"[^>]*type="text/');
        $this->expectOutputRegex('/<input[^>]*name="namedgroup\[groupel2\]"[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_funky_name() {
        $mform = new formslib_settype_debugging_funky_name();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'blah[foo][bar][1]'? Defaulting to PARAM_RAW cleaning.");
        $this->expectOutputRegex('/<input[^>]*name="blah\[foo\]\[bar\]\[0\]"[^>]*type="text/');
        $this->expectOutputRegex('/<input[^>]*name="blah\[foo\]\[bar\]\[1\]"[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_type_inheritance() {
        $mform = new formslib_settype_debugging_type_inheritance();
        $this->expectOutputRegex('/<input[^>]*name="blah\[foo\]\[bar\]\[0\]"[^>]*type="text/');
        $this->expectOutputRegex('/<input[^>]*name="blah\[bar\]\[foo\]\[1\]"[^>]*type="text/');
        $this->expectOutputRegex('/<input[^>]*name="blah\[any\]\[other\]\[2\]"[^>]*type="text/');
        $mform->display();
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
        // TODO: The repeat_elements() is far from perfect. Everything should be
        // repeated auto-magically by default with options only defining exceptions.
        // Surely this is caused because we are storing some element information OUT
        // from the element (type...) at form level. Anyway, the method should do its
        // work better, no matter of that.
        $this->repeat_elements($repeatels, 2, array('text' => array('type' => PARAM_RAW)), 'numtexts', 'addtexts');

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

// Used to test debugging is called when text added without setType.
class formslib_settype_debugging_text extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'texttest', 'test123', 'testing123');
    }
}

// Used to test debugging is called when hidden added without setType.
class formslib_settype_debugging_hidden extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'hiddentest', '1');
    }
}

// Used to test debugging is called when hidden added without setType.
class formslib_settype_debugging_url extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('url', 'urltest', 'urltest');
    }
}

// Used to test debugging is called when repeated text added without setType.
class formslib_settype_debugging_repeat extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $repeatels = array(
            $mform->createElement('text', 'repeattest', 'Type something')
        );

        $this->repeat_elements($repeatels, 1, array(), 'numtexts', 'addtexts');
    }
}

// Used to no debugging is called when correctly tset
class formslib_settype_debugging_repeat_ok extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $repeatels = array(
            $mform->createElement('text', 'repeattest', 'Type something')
        );

       $this->repeat_elements($repeatels, 2, array('repeattest' => array('type' => PARAM_RAW)), 'numtexts', 'addtexts');
    }
}

// Used to test if debugging is called when a group contains elements without type.
class formslib_settype_debugging_group extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $group = array(
            $mform->createElement('text', 'groupel1', 'groupel1'),
            $mform->createElement('text', 'groupel2', 'groupel2')
        );
        $mform->addGroup($group);
        $mform->setType('groupel2', PARAM_INT);
    }
}

// Used to test if debugging is called when a named group contains elements without type.
class formslib_settype_debugging_namedgroup extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $group = array(
            $mform->createElement('text', 'groupel1', 'groupel1'),
            $mform->createElement('text', 'groupel2', 'groupel2')
        );
        $mform->addGroup($group, 'namedgroup');
        $mform->setType('namedgroup[groupel2]', PARAM_INT);
    }
}

// Used to test if debugging is called when has a funky name.
class formslib_settype_debugging_funky_name extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'blah[foo][bar][0]', 'test', 'test');
        $mform->addElement('text', 'blah[foo][bar][1]', 'test', 'test');
        $mform->setType('blah[foo][bar][0]', PARAM_INT);
    }
}

// Used to test if debugging is not called with type inheritance.
class formslib_settype_debugging_type_inheritance extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'blah[foo][bar][0]', 'test1', 'test');
        $mform->addElement('text', 'blah[bar][foo][1]', 'test2', 'test');
        $mform->addElement('text', 'blah[any][other][2]', 'test3', 'test');
        $mform->setType('blah[foo][bar]', PARAM_INT);
        $mform->setType('blah[bar]', PARAM_FLOAT);
        $mform->setType('blah', PARAM_TEXT);
    }
}
