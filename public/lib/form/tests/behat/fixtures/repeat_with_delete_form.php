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
 * Test form repeat elements and delete button
 *
 * @copyright 2021 Marina Glancy
 * @package   core_form
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir.'/formslib.php');
$PAGE->set_url('/lib/form/tests/behat/fixtures/repeat_with_delete_form.php');
require_login();
$PAGE->set_context(context_system::instance());

/**
 * Class repeat_with_delete_form
 *
 * @copyright 2021 Marina Glancy
 * @package   core_form
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repeat_with_delete_form extends moodleform {
    /**
     * Form definition
     */
    public function definition() {
        $mform = $this->_form;
        $repeatcount = $this->_customdata['repeatcount'];

        $repeat = array();
        $repeatopts = array();

        $repeat[] = $mform->createElement('header', 'testheading', 'Heading {no}');

        $repeat[] = $mform->createElement('text', 'testtext', 'Test text {no}');
        $repeatopts['testtext']['default'] = 'Testing';
        $repeatopts['testtext']['type'] = PARAM_TEXT;

        $repeat[] = $mform->createElement('submit', 'deleteel', 'Delete option {no}', [], false);

        $this->repeat_elements($repeat, $repeatcount, $repeatopts, 'test_repeat',
            'test_repeat_add', 1, 'Add repeats', true, 'deleteel');

        $this->add_action_buttons();
    }
}

$repeatcount = optional_param('test_repeat', 1, PARAM_INT);
$form = new repeat_with_delete_form(null, array('repeatcount' => $repeatcount));

echo $OUTPUT->header();
if ($data = $form->get_data()) {
    echo "<pre>".json_encode($data->testtext)."</pre>";
} else {
    $form->display();
}
echo $OUTPUT->footer();
