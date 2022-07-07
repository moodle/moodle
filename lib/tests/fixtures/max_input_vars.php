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
 * Fixture for Behat test of the max_input_vars handling for large forms.
 *
 * @package core
 * @copyright 2015 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/formslib.php');

// Behat test fixture only.
defined('BEHAT_SITE_RUNNING') || die('Only available on Behat test server');

/**
 * Form for testing max_input_vars.
 *
 * @copyright 2015 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_max_input_vars_form extends moodleform {
    /**
     * Form definition.
     */
    public function definition() {
        global $CFG, $PAGE;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', '');
        $mform->addElement('hidden', 'type', $this->_customdata['type']);
        $mform->setType('type', PARAM_ALPHA);

        // This is similar to how the selects are created for the role tables,
        // without using a Moodle form element.
        $select = html_writer::select(array(13 => 'ArrayOpt13', 42 => 'ArrayOpt4', 666 => 'ArrayOpt666'),
                'arraytest[]', array(13, 42), false, array('multiple' => 'multiple', 'size' => 10));
        $mform->addElement('static', 'arraybit', $select);

        switch ($this->_customdata['control']) {
            case 'c' :
                // Create a whole stack of checkboxes.
                for ($i = 0; $i < $this->_customdata['fieldcount']; $i++) {
                    $mform->addElement('advcheckbox', 'test_c' . $i, 'Checkbox ' . $i);
                }
                break;

            case 'a' :
                // Create a very large array input type field.
                $options = array();
                $values = array();
                for ($i = 0; $i < $this->_customdata['fieldcount']; $i++) {
                    $options[$i] = 'BigArray ' . $i;
                    if ($i !== 3) {
                        $values[] = $i;
                    }
                }
                $select = html_writer::select($options,
                        'test_a[]', $values, false, array('multiple' => 'multiple', 'size' => 50));
                $mform->addElement('static', 'bigarraybit', $select);
                break;
        }

        // For the sake of it, let's have a second array.
        $select = html_writer::select(array(13 => 'Array2Opt13', 42 => 'Array2Opt4', 666 => 'Array2Opt666'),
                'array2test[]', array(13, 42), false, array('multiple' => 'multiple', 'size' => 10));
        $mform->addElement('static', 'array2bit', $select);

        $mform->addElement('submit', 'submitbutton', 'Submit here!');
    }
}

require_login();

$context = context_system::instance();

$type = optional_param('type', '', PARAM_ALPHA);

// Set up the page details.
$PAGE->set_url(new moodle_url('/lib/tests/fixtures/max_input_vars.php'));
$PAGE->set_context($context);

if ($type) {
    // Make it work regardless of max_input_vars setting on server, within reason.
    if ($type[1] === 's') {
        // Small enough to definitely fit in the area.
        $fieldcount = 10;
    } else if ($type[1] === 'm') {
        // Just under the limit (will go over for advancedcheckbox).
        $fieldcount = (int)ini_get('max_input_vars') - 100;
    } else if ($type[1] === 'e') {
        // Exactly on the PHP limit, taking into account extra form fields
        // and the double fields for checkboxes.
        if ($type[0] === 'c') {
            $fieldcount = (int)ini_get('max_input_vars') / 2 - 2;
        } else {
            $fieldcount = (int)ini_get('max_input_vars') - 11;
        }
    } else if ($type[1] === 'l') {
        // Just over the limit.
        $fieldcount = (int)ini_get('max_input_vars') + 100;
    }

    $mform = new core_max_input_vars_form('max_input_vars.php',
            array('type' => $type, 'fieldcount' => $fieldcount, 'control' => $type[0]));
    if ($type[0] === 'c') {
        $data = array();
        for ($i = 0; $i < $fieldcount; $i++) {
            if ($i === 3) {
                // Everything is set except number 3.
                continue;
            }
            $data['test_c' . $i] = 1;
        }
        $mform->set_data($data);
    }
}

echo $OUTPUT->header();

if ($type && ($result = $mform->get_data())) {
    $testc = array();
    $testa = array();
    foreach ($_POST as $key => $value) {
        $matches = array();
        // Handle the 'bulk' ones separately so we can show success/fail rather
        // than outputting a thousand items; also makes it possible to Behat-test
        // without depending on specific value of max_input_vars.
        if (preg_match('~^test_c([0-9]+)$~', $key, $matches)) {
            $testc[(int)$matches[1]] = $value;
        } else if ($key === 'test_a') {
            $testa = $value;
        } else {
            // Other fields are output straight off.
            if (is_array($value)) {
                echo html_writer::div(s($key) . '=[' . s(implode(',', $value)) . ']');
            } else {
                echo html_writer::div(s($key) . '=' . s($value));
            }
        }
    }

    // Confirm that the bulk results are correct.
    switch ($type[0]) {
        case 'c' :
            $success = true;
            for ($i = 0; $i < $fieldcount; $i++) {
                if (!array_key_exists($i, $testc)) {
                    $success = false;
                    break;
                }
                if ($testc[$i] != ($i == 3 ? 0 : 1)) {
                    $success = false;
                    break;
                }
            }
            if (array_key_exists($fieldcount, $testc)) {
                $success = false;
            }
            // Check using Moodle form and _param functions too.
            $key = 'test_c' . ($fieldcount - 1);
            if (empty($result->{$key})) {
                $success = false;
            }
            if (optional_param($key, 0, PARAM_INT) !== 1) {
                $success = false;
            }
            echo html_writer::div('Bulk checkbox success: ' . ($success ? 'true' : 'false'));
            break;

        case 'a' :
            $success = true;
            for ($i = 0; $i < $fieldcount; $i++) {
                if ($i === 3) {
                    if (in_array($i, $testa)) {
                        $success = false;
                        break;
                    }
                } else {
                    if (!in_array($i, $testa)) {
                        $success = false;
                        break;
                    }
                }
            }
            if (in_array($fieldcount, $testa)) {
                $success = false;
            }
            // Check using Moodle _param function. The form does not include these
            // fields so it won't be in the form result.
            $array = optional_param_array('test_a', array(), PARAM_INT);
            if ($array != $testa) {
                $success = false;
            }
            echo html_writer::div('Bulk array success: ' . ($success ? 'true' : 'false'));
            break;
    }

} else if ($type) {
    $mform->display();
}

// Show links to each available type of test.
echo html_writer::start_tag('ul');
foreach (array('c' => 'Advanced checkboxes',
        'a' => 'Select options') as $control => $controlname) {
    foreach (array('s' => 'Small', 'm' => 'Below limit', 'e' => 'Exact PHP limit',
            'l' => 'Above limit') as $size => $sizename) {
        echo html_writer::tag('li', html_writer::link('max_input_vars.php?type=' .
                $control . $size, $controlname . ' / ' . $sizename));
    }
}
echo html_writer::end_tag('ul');

echo $OUTPUT->footer();
