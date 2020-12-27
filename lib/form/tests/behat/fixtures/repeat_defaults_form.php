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
 * Test form repeat elements + defaults
 *
 * @copyright 2020 Davo Smith, Synergy Learning
 * @package   core_form
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir.'/formslib.php');
$PAGE->set_url('/lib/form/tests/behat/fixtures/repeat_defaults_form.php');
require_login();
$PAGE->set_context(context_system::instance());

/**
 * Class repeat_defaults_form
 * @package core_form
 */
class repeat_defaults_form extends moodleform {
    /**
     * Form definition
     */
    public function definition() {
        $mform = $this->_form;
        $repeatcount = $this->_customdata['repeatcount'];

        $repeat = array();
        $repeatopts = array();

        $repeat[] = $mform->createElement('header', 'testheading', 'Heading {no}');

        $repeat[] = $mform->createElement('checkbox', 'testcheckbox', 'Test checkbox (default checked)');
        $repeatopts['testcheckbox']['default'] = 1;

        $repeat[] = $mform->createElement('advcheckbox', 'testadvcheckbox', 'Test advcheckbox (default checked)');
        $repeatopts['testadvcheckbox']['default'] = 1;

        $repeat[] = $mform->createElement('date_selector', 'testdate', 'Test date (default 8th Sept 2013)');
        $repeatopts['testdate']['default'] = mktime(0, 0, 0, 9, 8, 2013);

        $repeat[] = $mform->createElement('date_time_selector', 'testdatetime', 'Test datetime (default 8th Sept 2013, 10:30am)');
        $repeatopts['testdatetime']['default'] = mktime(10, 30, 0, 9, 8, 2013);

        $repeat[] = $mform->createElement('duration', 'testduration', 'Test duration (default 3 hours)');
        $repeatopts['testduration']['default'] = 3 * HOURSECS;

        $repeat[] = $mform->createElement('select', 'testselect', 'Test select (default B)', array(1 => 'A', 2 => 'B', 3 => 'C'));
        $repeatopts['testselect']['default'] = 2;

        $repeat[] = $mform->createElement('selectyesno', 'testselectyes', 'Test selectyesno (default yes)');
        $repeatopts['testselectyes']['default'] = 1;

        $repeat[] = $mform->createElement('selectyesno', 'testselectno', 'Test selectyesno (default no)');
        $repeatopts['testselectno']['default'] = 0;

        $repeat[] = $mform->createElement('text', 'testtext', 'Test text (default \'Testing 123\')');
        $repeatopts['testtext']['default'] = 'Testing 123';
        $repeatopts['testtext']['type'] = PARAM_TEXT;

        $this->repeat_elements($repeat, $repeatcount, $repeatopts, 'test_repeat', 'test_repeat_add', 1, 'Add repeats', true);

        $this->add_action_buttons();
    }
}

$repeatcount = optional_param('test_repeat', 1, PARAM_INT);
$form = new repeat_defaults_form(null, array('repeatcount' => $repeatcount));

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
