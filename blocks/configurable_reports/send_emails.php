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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Email form added to enable email to selected users.
require_once('../../config.php');

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');

require_login();
global $PAGE, $USER, $DB, $COURSE;
$context = context_course::instance($COURSE->id);
$PAGE->set_context($context);

if (!has_capability('block/configurable_reports:managereports', $context) &&
    !has_capability('block/configurable_reports:manageownreports', $context)) {
    throw new moodle_exception('badpermissions');
}

/**
 * Class sendemail_form
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class sendemail_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        global $COURSE;

        $mform =& $this->_form;
        $context = context_course::instance($COURSE->id);
        $editoroptions = [
            'trusttext' => true,
            'subdirs' => true,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'context' => $context,
        ];

        $mform->addElement('hidden', 'usersids', $this->_customdata['usersids']);
        $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);

        $mform->addElement('text', 'subject', get_string('email_subject', 'block_configurable_reports'));
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', null, 'required');

        $mform->addElement('editor', 'content', get_string('email_message', 'block_configurable_reports'), null, $editoroptions);

        $buttons = [];
        $buttons[] =& $mform->createElement('submit', 'send', get_string('email_send', 'block_configurable_reports'));
        $buttons[] =& $mform->createElement('cancel');

        $mform->addGroup($buttons, 'buttons', get_string('actions'), [' '], false);
    }

}

// TODO _POST?? not Moodle way.
$form = new sendemail_form(null, [
    'usersids' => implode(',', $_POST['userids']),
    'courseid' => (int) $_POST['courseid'],
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php?id=' . $data->courseid));
} else if ($data = $form->get_data()) {
    foreach (explode(',', $data->usersids) as $userid) {
        $abouttosenduser = $DB->get_record('user', ['id' => (int) $userid]);
        email_to_user($abouttosenduser, $USER, $data->subject, format_text($data->content['text']), $data->content['text']);
    }
    // After emails were sent... go back to where you came from.
    redirect(new moodle_url('/course/view.php?id=' . $data->courseid));
}

$PAGE->set_title(get_string('email', 'questionnaire'));
$PAGE->set_heading(format_string($COURSE->fullname));
$PAGE->navbar->add(get_string('email', 'questionnaire'));

echo $OUTPUT->header();

echo html_writer::start_tag('div', ['class' => 'no-overflow']);
$form->display();
echo html_writer::end_tag('div');

echo $OUTPUT->footer();
