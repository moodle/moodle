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


class core_formslib_testcase extends advanced_testcase {

    public function test_require_rule() {
        global $CFG;

        $strictformsrequired = null;
        if (isset($CFG->strictformsrequired)) {
            $strictformsrequired = $CFG->strictformsrequired;
        }

        $rule = new MoodleQuickForm_Rule_Required();

        // First run the tests with strictformsrequired off.
        $CFG->strictformsrequired = false;
        // Passes.
        $this->assertTrue($rule->validate('Something'));
        $this->assertTrue($rule->validate("Something\nmore"));
        $this->assertTrue($rule->validate("\nmore"));
        $this->assertTrue($rule->validate(" more "));
        $this->assertTrue($rule->validate('ш'));
        $this->assertTrue($rule->validate("の"));
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
        // Fails.
        $this->assertFalse($rule->validate(''));
        $this->assertFalse($rule->validate(false));
        $this->assertFalse($rule->validate(null));

        // Now run the same tests with it on to make sure things work as expected.
        $CFG->strictformsrequired = true;
        // Passes.
        $this->assertTrue($rule->validate('Something'));
        $this->assertTrue($rule->validate("Something\nmore"));
        $this->assertTrue($rule->validate("\nmore"));
        $this->assertTrue($rule->validate(" more "));
        $this->assertTrue($rule->validate("0"));
        $this->assertTrue($rule->validate('ш'));
        $this->assertTrue($rule->validate("の"));
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
        // Fails.
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

    public function test_range_rule() {
        global $CFG;

        require_once('HTML/QuickForm/Rule/Range.php'); // Requires this pear stuff.

        $strictformsrequired = null;
        if (isset($CFG->strictformsrequired)) {
            $strictformsrequired = $CFG->strictformsrequired;
        }

        $rule = new HTML_QuickForm_Rule_Range();

        // First run the tests with strictformsrequired off.
        $CFG->strictformsrequired = false;
        // Passes.
        $rule->setName('minlength'); // Let's verify some min lengths.
        $this->assertTrue($rule->validate('12', 2));
        $this->assertTrue($rule->validate('123', 2));
        $this->assertTrue($rule->validate('áé', 2));
        $this->assertTrue($rule->validate('áéí', 2));
        $rule->setName('maxlength'); // Let's verify some max lengths.
        $this->assertTrue($rule->validate('1', 2));
        $this->assertTrue($rule->validate('12', 2));
        $this->assertTrue($rule->validate('á', 2));
        $this->assertTrue($rule->validate('áé', 2));
        $rule->setName('----'); // Let's verify some ranges.
        $this->assertTrue($rule->validate('', array(0, 2)));
        $this->assertTrue($rule->validate('1', array(0, 2)));
        $this->assertTrue($rule->validate('12', array(0, 2)));
        $this->assertTrue($rule->validate('á', array(0, 2)));
        $this->assertTrue($rule->validate('áé', array(0, 2)));

        // Fail.
        $rule->setName('minlength'); // Let's verify some min lengths.
        $this->assertFalse($rule->validate('', 2));
        $this->assertFalse($rule->validate('1', 2));
        $this->assertFalse($rule->validate('á', 2));
        $rule->setName('maxlength'); // Let's verify some max lengths.
        $this->assertFalse($rule->validate('123', 2));
        $this->assertFalse($rule->validate('áéí', 2));
        $rule->setName('----'); // Let's verify some ranges.
        $this->assertFalse($rule->validate('', array(1, 2)));
        $this->assertFalse($rule->validate('123', array(1, 2)));
        $this->assertFalse($rule->validate('áéí', array(1, 2)));

        // Now run the same tests with it on to make sure things work as expected.
        $CFG->strictformsrequired = true;
        // Passes.
        $rule->setName('minlength'); // Let's verify some min lengths.
        $this->assertTrue($rule->validate('12', 2));
        $this->assertTrue($rule->validate('123', 2));
        $this->assertTrue($rule->validate('áé', 2));
        $this->assertTrue($rule->validate('áéí', 2));
        $rule->setName('maxlength'); // Let's verify some min lengths.
        $this->assertTrue($rule->validate('1', 2));
        $this->assertTrue($rule->validate('12', 2));
        $this->assertTrue($rule->validate('á', 2));
        $this->assertTrue($rule->validate('áé', 2));
        $rule->setName('----'); // Let's verify some ranges.
        $this->assertTrue($rule->validate('', array(0, 2)));
        $this->assertTrue($rule->validate('1', array(0, 2)));
        $this->assertTrue($rule->validate('12', array(0, 2)));
        $this->assertTrue($rule->validate('á', array(0, 2)));
        $this->assertTrue($rule->validate('áé', array(0, 2)));

        // Fail.
        $rule->setName('minlength'); // Let's verify some min lengths.
        $this->assertFalse($rule->validate('', 2));
        $this->assertFalse($rule->validate('1', 2));
        $this->assertFalse($rule->validate('á', 2));
        $rule->setName('maxlength'); // Let's verify some min lengths.
        $this->assertFalse($rule->validate('123', 2));
        $this->assertFalse($rule->validate('áéí', 2));
        $rule->setName('----'); // Let's verify some ranges.
        $this->assertFalse($rule->validate('', array(1, 2)));
        $this->assertFalse($rule->validate('123', array(1, 2)));
        $this->assertFalse($rule->validate('áéí', array(1, 2)));

        if (isset($strictformsrequired)) {
            $CFG->strictformsrequired = $strictformsrequired;
        }
    }

    public function test_generate_id_select() {
        $el = new MoodleQuickForm_select('choose_one', 'Choose one',
            array(1 => 'One', '2' => 'Two'));
        $el->_generateId();
        $this->assertSame('id_choose_one', $el->getAttribute('id'));
    }

    public function test_generate_id_like_repeat() {
        $el = new MoodleQuickForm_text('text[7]', 'Type something');
        $el->_generateId();
        $this->assertSame('id_text_7', $el->getAttribute('id'));
    }

    public function test_can_manually_set_id() {
        $el = new MoodleQuickForm_text('elementname', 'Type something',
            array('id' => 'customelementid'));
        $el->_generateId();
        $this->assertSame('customelementid', $el->getAttribute('id'));
    }

    public function test_generate_id_radio() {
        $el = new MoodleQuickForm_radio('radio', 'Label', 'Choice label', 'choice_value');
        $el->_generateId();
        $this->assertSame('id_radio_choice_value', $el->getAttribute('id'));
    }

    public function test_radio_can_manually_set_id() {
        $el = new MoodleQuickForm_radio('radio2', 'Label', 'Choice label', 'choice_value',
            array('id' => 'customelementid2'));
        $el->_generateId();
        $this->assertSame('customelementid2', $el->getAttribute('id'));
    }

    public function test_generate_id_radio_like_repeat() {
        $el = new MoodleQuickForm_radio('repeatradio[2]', 'Label', 'Choice label', 'val');
        $el->_generateId();
        $this->assertSame('id_repeatradio_2_val', $el->getAttribute('id'));
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

        // Check form still there though.
        $this->expectOutputRegex('/<input[^>]*name="texttest[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_hidden() {
        $mform = new formslib_settype_debugging_hidden();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'hiddentest'? Defaulting to PARAM_RAW cleaning.");

        // Check form still there though.
        $this->expectOutputRegex('/<input[^>]*name="hiddentest[^>]*type="hidden/');
        $mform->display();
    }

    public function test_settype_debugging_url() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $mform = new formslib_settype_debugging_url();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'urltest'? Defaulting to PARAM_RAW cleaning.");

        // Check form still there though.
        $this->expectOutputRegex('/<input[^>]*name="urltest"[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_repeat() {
        $mform = new formslib_settype_debugging_repeat();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'repeattest[0]'? Defaulting to PARAM_RAW cleaning.");

        // Check form still there though.
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

    public function test_settype_debugging_type_group_in_repeat() {
        $mform = new formslib_settype_debugging_type_group_in_repeat();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'test2[0]'? Defaulting to PARAM_RAW cleaning.");
        $this->expectOutputRegex('/<input[^>]*name="test1\[0\]"[^>]*type="text/');
        $this->expectOutputRegex('/<input[^>]*name="test2\[0\]"[^>]*type="text/');
        $mform->display();
    }

    public function test_settype_debugging_type_namedgroup_in_repeat() {
        $mform = new formslib_settype_debugging_type_namedgroup_in_repeat();
        $this->assertDebuggingCalled("Did you remember to call setType() for 'namedgroup[0][test2]'? Defaulting to PARAM_RAW cleaning.");
        $this->expectOutputRegex('/<input[^>]*name="namedgroup\[0\]\[test1\]"[^>]*type="text/');
        $this->expectOutputRegex('/<input[^>]*name="namedgroup\[0\]\[test2\]"[^>]*type="text/');
        $mform->display();
    }

    public function test_type_cleaning() {
        $expectedtypes = array(
            'simpleel' => PARAM_INT,
            'groupel1' => PARAM_INT,
            'groupel2' => PARAM_FLOAT,
            'groupel3' => PARAM_INT,
            'namedgroup' => array(
                'sndgroupel1' => PARAM_INT,
                'sndgroupel2' => PARAM_FLOAT,
                'sndgroupel3' => PARAM_INT
            ),
            'namedgroupinherit' => array(
                'thdgroupel1' => PARAM_INT,
                'thdgroupel2' => PARAM_INT
            ),
            'repeatedel' => array(
                0 => PARAM_INT,
                1 => PARAM_INT
            ),
            'repeatedelinherit' => array(
                0 => PARAM_INT,
                1 => PARAM_INT
            ),
            'squaretest' => array(
                0 => PARAM_INT
            ),
            'nested' => array(
                0 => array(
                    'bob' => array(
                        123 => PARAM_INT,
                        'foo' => PARAM_FLOAT
                    ),
                    'xyz' => PARAM_RAW
                ),
                1 => PARAM_INT
            ),
            'repeatgroupel1' => array(
                0 => PARAM_INT,
                1 => PARAM_INT
            ),
            'repeatgroupel2' => array(
                0 => PARAM_INT,
                1 => PARAM_INT
            ),
            'repeatnamedgroup' => array(
                0 => array(
                    'repeatnamedgroupel1' => PARAM_INT,
                    'repeatnamedgroupel2' => PARAM_INT
                ),
                1 => array(
                    'repeatnamedgroupel1' => PARAM_INT,
                    'repeatnamedgroupel2' => PARAM_INT
                )
            )
        );
        $valuessubmitted = array(
            'simpleel' => '11.01',
            'groupel1' => '11.01',
            'groupel2' => '11.01',
            'groupel3' => '11.01',
            'namedgroup' => array(
                'sndgroupel1' => '11.01',
                'sndgroupel2' => '11.01',
                'sndgroupel3' => '11.01'
            ),
            'namedgroupinherit' => array(
                'thdgroupel1' => '11.01',
                'thdgroupel2' => '11.01'
            ),
            'repeatedel' => array(
                0 => '11.01',
                1 => '11.01'
            ),
            'repeatedelinherit' => array(
                0 => '11.01',
                1 => '11.01'
            ),
            'squaretest' => array(
                0 => '11.01'
            ),
            'nested' => array(
                0 => array(
                    'bob' => array(
                        123 => '11.01',
                        'foo' => '11.01'
                    ),
                    'xyz' => '11.01'
                ),
                1 => '11.01'
            ),
            'repeatgroupel1' => array(
                0 => '11.01',
                1 => '11.01'
            ),
            'repeatgroupel2' => array(
                0 => '11.01',
                1 => '11.01'
            ),
            'repeatnamedgroup' => array(
                0 => array(
                    'repeatnamedgroupel1' => '11.01',
                    'repeatnamedgroupel2' => '11.01'
                ),
                1 => array(
                    'repeatnamedgroupel1' => '11.01',
                    'repeatnamedgroupel2' => '11.01'
                )
            )
        );
        $expectedvalues = array(
            'simpleel' => 11,
            'groupel1' => 11,
            'groupel2' => 11.01,
            'groupel3' => 11,
            'namedgroup' => array(
                'sndgroupel1' => 11,
                'sndgroupel2' => 11.01,
                'sndgroupel3' => 11
            ),
            'namedgroupinherit' => array(
                'thdgroupel1' => 11,
                'thdgroupel2' => 11
            ),
            'repeatable' => 2,
            'repeatedel' => array(
                0 => 11,
                1 => 11
            ),
            'repeatableinherit' => 2,
            'repeatedelinherit' => array(
                0 => 11,
                1 => 11
            ),
            'squaretest' => array(
                0 => 11
            ),
            'nested' => array(
                0 => array(
                    'bob' => array(
                        123 => 11,
                        'foo' => 11.01
                    ),
                    'xyz' => '11.01'
                ),
                1 => 11
            ),
            'repeatablegroup' => 2,
            'repeatgroupel1' => array(
                0 => 11,
                1 => 11
            ),
            'repeatgroupel2' => array(
                0 => 11,
                1 => 11
            ),
            'repeatablenamedgroup' => 2,
            'repeatnamedgroup' => array(
                0 => array(
                    'repeatnamedgroupel1' => 11,
                    'repeatnamedgroupel2' => 11
                ),
                1 => array(
                    'repeatnamedgroupel1' => 11,
                    'repeatnamedgroupel2' => 11
                )
            )
        );

        $mform = new formslib_clean_value();
        $mform->get_form()->updateSubmission($valuessubmitted, null);
        foreach ($expectedtypes as $elementname => $expected) {
            $actual = $mform->get_form()->getCleanType($elementname, $valuessubmitted[$elementname]);
            $this->assertSame($expected, $actual, "Failed validating clean type of '$elementname'");
        }

        $data = $mform->get_data();
        $this->assertSame($expectedvalues, (array) $data);
    }

    /**
     * MDL-52873
     */
    public function test_multiple_modgrade_fields() {
        $this->resetAfterTest(true);

        $form = new formslib_multiple_modgrade_form();
        ob_start();
        $form->display();
        $html = ob_get_clean();

        $this->assertTag(array('id' => 'fgroup_id_grade1'), $html);
        $this->assertTag(array('id' => 'id_grade1_modgrade_type'), $html);
        $this->assertTag(array('id' => 'id_grade1_modgrade_point'), $html);
        $this->assertTag(array('id' => 'id_grade1_modgrade_scale'), $html);

        $this->assertTag(array('id' => 'fgroup_id_grade2'), $html);
        $this->assertTag(array('id' => 'id_grade2_modgrade_type'), $html);
        $this->assertTag(array('id' => 'id_grade2_modgrade_point'), $html);
        $this->assertTag(array('id' => 'id_grade2_modgrade_scale'), $html);

        $this->assertTag(array('id' => 'fgroup_id_grade_3'), $html);
        $this->assertTag(array('id' => 'id_grade_3_modgrade_type'), $html);
        $this->assertTag(array('id' => 'id_grade_3_modgrade_point'), $html);
        $this->assertTag(array('id' => 'id_grade_3_modgrade_scale'), $html);
    }

    /**
     * Ensure a validation can run at least once per object. See MDL-56259.
     */
    public function test_multiple_validation() {
        $this->resetAfterTest(true);

        // It should be valid.
        formslib_multiple_validation_form::mock_submit(['somenumber' => '10']);
        $form = new formslib_multiple_validation_form();
        $this->assertTrue($form->is_validated());
        $this->assertEquals(10, $form->get_data()->somenumber);

        // It should not validate.
        formslib_multiple_validation_form::mock_submit(['somenumber' => '-5']);
        $form = new formslib_multiple_validation_form();
        $this->assertFalse($form->is_validated());
        $this->assertNull($form->get_data());
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

/**
 * Used to test debugging is called when text added without setType.
 */
class formslib_settype_debugging_text extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'texttest', 'test123', 'testing123');
    }
}

/**
 * Used to test debugging is called when hidden added without setType.
 */
class formslib_settype_debugging_hidden extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'hiddentest', '1');
    }
}

/**
 * Used to test debugging is called when hidden added without setType.
 */
class formslib_settype_debugging_url extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('url', 'urltest', 'urltest');
    }
}

/**
 * Used to test debugging is called when repeated text added without setType.
 */
class formslib_settype_debugging_repeat extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $repeatels = array(
            $mform->createElement('text', 'repeattest', 'Type something')
        );

        $this->repeat_elements($repeatels, 1, array(), 'numtexts', 'addtexts');
    }
}

/**
 * Used to no debugging is called when correctly test.
 */
class formslib_settype_debugging_repeat_ok extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $repeatels = array(
            $mform->createElement('text', 'repeattest', 'Type something')
        );

        $this->repeat_elements($repeatels, 2, array('repeattest' => array('type' => PARAM_RAW)), 'numtexts', 'addtexts');
    }
}

/**
 * Used to test if debugging is called when a group contains elements without type.
 */
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

/**
 * Used to test if debugging is called when a named group contains elements without type.
 */
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

/**
 * Used to test if debugging is called when has a funky name.
 */
class formslib_settype_debugging_funky_name extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'blah[foo][bar][0]', 'test', 'test');
        $mform->addElement('text', 'blah[foo][bar][1]', 'test', 'test');
        $mform->setType('blah[foo][bar][0]', PARAM_INT);
    }
}

/**
 * Used to test that debugging is not called with type inheritance.
 */
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

/**
 * Used to test the debugging when using groups in repeated elements.
 */
class formslib_settype_debugging_type_group_in_repeat extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $groupelements = array(
            $mform->createElement('text', 'test1', 'test1', 'test'),
            $mform->createElement('text', 'test2', 'test2', 'test')
        );
        $group = $mform->createElement('group', null, 'group1', $groupelements, null, false);
        $this->repeat_elements(array($group), 1, array('test1' => array('type' => PARAM_INT)), 'hidden', 'button');
    }
}

/**
 * Used to test the debugging when using named groups in repeated elements.
 */
class formslib_settype_debugging_type_namedgroup_in_repeat extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $groupelements = array(
            $mform->createElement('text', 'test1', 'test1', 'test'),
            $mform->createElement('text', 'test2', 'test2', 'test')
        );
        $group = $mform->createElement('group', 'namedgroup', 'group1', $groupelements, null, true);
        $this->repeat_elements(array($group), 1, array('namedgroup[test1]' => array('type' => PARAM_INT)), 'hidden', 'button');
    }
}

/**
 * Used to check value cleaning.
 */
class formslib_clean_value extends moodleform {
    public function get_form() {
        return $this->_form;
    }
    public function definition() {
        $mform = $this->_form;

        // Add a simple int.
        $mform->addElement('text', 'simpleel', 'simpleel');
        $mform->setType('simpleel', PARAM_INT);

        // Add a non-named group.
        $group = array(
            $mform->createElement('text', 'groupel1', 'groupel1'),
            $mform->createElement('text', 'groupel2', 'groupel2'),
            $mform->createElement('text', 'groupel3', 'groupel3')
        );
        $mform->setType('groupel1', PARAM_INT);
        $mform->setType('groupel2', PARAM_FLOAT);
        $mform->setType('groupel3', PARAM_INT);
        $mform->addGroup($group);

        // Add a named group.
        $group = array(
            $mform->createElement('text', 'sndgroupel1', 'sndgroupel1'),
            $mform->createElement('text', 'sndgroupel2', 'sndgroupel2'),
            $mform->createElement('text', 'sndgroupel3', 'sndgroupel3')
        );
        $mform->addGroup($group, 'namedgroup');
        $mform->setType('namedgroup[sndgroupel1]', PARAM_INT);
        $mform->setType('namedgroup[sndgroupel2]', PARAM_FLOAT);
        $mform->setType('namedgroup[sndgroupel3]', PARAM_INT);

        // Add a named group, with inheritance.
        $group = array(
            $mform->createElement('text', 'thdgroupel1', 'thdgroupel1'),
            $mform->createElement('text', 'thdgroupel2', 'thdgroupel2')
        );
        $mform->addGroup($group, 'namedgroupinherit');
        $mform->setType('namedgroupinherit', PARAM_INT);

        // Add a repetition.
        $repeat = $mform->createElement('text', 'repeatedel', 'repeatedel');
        $this->repeat_elements(array($repeat), 2, array('repeatedel' => array('type' => PARAM_INT)), 'repeatable', 'add', 0);

        // Add a repetition, with inheritance.
        $repeat = $mform->createElement('text', 'repeatedelinherit', 'repeatedelinherit');
        $this->repeat_elements(array($repeat), 2, array(), 'repeatableinherit', 'add', 0);
        $mform->setType('repeatedelinherit', PARAM_INT);

        // Add an arbitrary named element.
        $mform->addElement('text', 'squaretest[0]', 'squaretest[0]');
        $mform->setType('squaretest[0]', PARAM_INT);

        // Add an arbitrary nested array named element.
        $mform->addElement('text', 'nested[0][bob][123]', 'nested[0][bob][123]');
        $mform->setType('nested[0][bob][123]', PARAM_INT);

        // Add inheritance test cases.
        $mform->setType('nested', PARAM_INT);
        $mform->setType('nested[0]', PARAM_RAW);
        $mform->setType('nested[0][bob]', PARAM_FLOAT);
        $mform->addElement('text', 'nested[1]', 'nested[1]');
        $mform->addElement('text', 'nested[0][xyz]', 'nested[0][xyz]');
        $mform->addElement('text', 'nested[0][bob][foo]', 'nested[0][bob][foo]');

        // Add group in repeated element (with extra inheritance).
        $groupelements = array(
            $mform->createElement('text', 'repeatgroupel1', 'repeatgroupel1'),
            $mform->createElement('text', 'repeatgroupel2', 'repeatgroupel2')
        );
        $group = $mform->createElement('group', 'repeatgroup', 'repeatgroup', $groupelements, null, false);
        $this->repeat_elements(array($group), 2, array('repeatgroupel1' => array('type' => PARAM_INT),
            'repeatgroupel2' => array('type' => PARAM_INT)), 'repeatablegroup', 'add', 0);

        // Add named group in repeated element.
        $groupelements = array(
            $mform->createElement('text', 'repeatnamedgroupel1', 'repeatnamedgroupel1'),
            $mform->createElement('text', 'repeatnamedgroupel2', 'repeatnamedgroupel2')
        );
        $group = $mform->createElement('group', 'repeatnamedgroup', 'repeatnamedgroup', $groupelements, null, true);
        $this->repeat_elements(array($group), 2, array('repeatnamedgroup[repeatnamedgroupel1]' => array('type' => PARAM_INT),
            'repeatnamedgroup[repeatnamedgroupel2]' => array('type' => PARAM_INT)), 'repeatablenamedgroup', 'add', 0);
    }
}

/**
 * Used to test that modgrade fields get unique id attributes.
 */
class formslib_multiple_modgrade_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('modgrade', 'grade1', 'Grade 1');
        $mform->addElement('modgrade', 'grade2', 'Grade 2');
        $mform->addElement('modgrade', 'grade[3]', 'Grade 3');
    }
}

/**
 * Used to test that you can validate a form more than once. See MDL-56250.
 * @package    core_form
 * @author     Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright  2016 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later */
class formslib_multiple_validation_form extends moodleform {
    /**
     * Simple definition, one text field which can have a number.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'somenumber');
        $mform->setType('somenumber', PARAM_INT);
    }

    /**
     * The number cannot be negative.
     * @param array $data An array of form data
     * @param array $files An array of form files
     * @return array Error messages
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['somenumber'] < 0) {
            $errors['somenumber'] = 'The number cannot be negative.';
        }
        return $errors;
    }
}
