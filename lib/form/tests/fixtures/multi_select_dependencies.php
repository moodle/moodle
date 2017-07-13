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
 * Fixture for Behat test for testing multiple select dependencies.
 *
 * @package core_form
 * @copyright 2016 Rajesh Taneja <rajesh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/formslib.php');

// Behat test fixture only.
defined('BEHAT_SITE_RUNNING') || die('Only available on Behat test server');

/**
 * Form for testing multiple select dependencies.
 *
 * @package core_form
 * @copyright 2016 Rajesh Taneja <rajesh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition() {

        $mform = $this->_form;

        $labels = array('North', 'Est', 'South', 'West');
        $select = $mform->addElement('select', 'mselect_name', 'Choose one or more directions', $labels);
        $select->setMultiple(true);

        $mform->addElement('text', 'text_name', 'Enter your name');
        $mform->setType('text_name', PARAM_RAW);

        $mform->disabledIf('text_name', 'mselect_name[]', 'neq', array(2, 3));

        $this->add_action_buttons($cancel = true, $submitlabel = null);
    }
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/form/tests/fixtures/multi_select_dependencies.php');
$PAGE->set_title('multi_select_dependencies');

$mform = new test_form(new moodle_url('/lib/form/tests/fixtures/multi_select_dependencies.php'));

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();