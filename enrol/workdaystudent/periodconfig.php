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
 * @copyright 2024 onwards LSUOnline & Continuing Education
 * @copyright 2024 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');

global $DB, $PAGE, $OUTPUT;

// Require admin permissions
require_login();
admin_externalpage_setup('period_config');

class enrol_workdaystudent_periodconfig_form extends moodleform {
    public function definition() {
        global $DB;

        // Date format.
        $format = '%b %d %Y';

        // Instantiate the form.
        $mform = $this->_form;

        // Fetch all records from the enrol_wds_periods table.
        $records = $DB->get_records('enrol_wds_periods', null, 'academic_year ASC, start_date ASC');

        // Build the table structure in an ugly manner.
        $tablehtml = '<table class="generaltable">';
        $tablehtml .= '<tr>
                        <th>' . get_string('wds:academic_period_id', 'enrol_workdaystudent') . '</th>
                        <th>' . get_string('wds:academic_period', 'enrol_workdaystudent') . '</th>
                        <th>' . get_string('wds:academic_year', 'enrol_workdaystudent') . '</th>
                        <th>' . get_string('wds:period_year', 'enrol_workdaystudent') . '</th>
                        <th>' . get_string('wds:period_type', 'enrol_workdaystudent') . '</th>
                        <th>' . get_string('wds:start_date', 'enrol_workdaystudent') . '</th>
                        <th>' . get_string('wds:end_date', 'enrol_workdaystudent') . '</th>
                        <th>' . get_string('wds:enabled', 'enrol_workdaystudent') . '</th>
                      </tr>';

        // Loop through the records to build out the table.
        foreach ($records as $record) {

            // Get the record.
            $enabledname = 'enabled_' . $record->id;

            // Check if we're checked….
            $checked = ($record->enabled) ? 'checked="checked"' : '';

            // Add a row to the table.
            $tablehtml .= '<tr>
                            <td>' . s($record->academic_period_id) . '</td>
                            <td>' . s($record->academic_period) . '</td>
                            <td>' . s($record->academic_year) . '</td>
                            <td>' . s($record->period_year) . '</td>
                            <td>' . s($record->period_type) . '</td>
                            <td>' . userdate($record->start_date, $format) . '</td>
                            <td>' . userdate($record->end_date, $format) . '</td>
                            <td><input type="checkbox" name="' .
                             $enabledname . '" value="1" ' . $checked . '></td>
                           </tr>';
        }

        // Finish the table.
        $tablehtml .= '</table>';

        // Add table as raw HTML inside the form.
        $mform->addElement('html', $tablehtml);

        // Add a submit button.
        $mform->addElement('submit', 'submitbutton', get_string('savechanges', 'admin'));
    }

    // Form validation?
    public function validation($data, $files) {
        return [];
    }
}

// Instantiate the form.
$mform = new enrol_workdaystudent_periodconfig_form();

// Process form submission.
if ($mform->is_submitted() && $mform->is_validated()) {
    $data = $mform->get_data();

    // Fetch existing records.
    $records = $DB->get_records('enrol_wds_periods', null, 'academic_year ASC, start_date ASC');

    // Loop through the records in the post.
    foreach ($records as $record) {
        $enabledname = 'enabled_' . $record->id;
        $enabledvalue = isset($_POST[$enabledname]) ? 1 : 0;

        // Build out the updated record.
        $updatedrecord = new stdClass();
        $updatedrecord->id = $record->id;
        $updatedrecord->enabled = $enabledvalue;

        // Update the database record.
        $DB->update_record('enrol_wds_periods', $updatedrecord);
    }

    // Redirect to avoid form resubmission issues.
    redirect(
        new moodle_url('/enrol/workdaystudent/periodconfig.php'),
        get_string('changessaved', 'moodle'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Page setup.
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/enrol/workdaystudent/periodconfig.php'));
$PAGE->set_title(get_string('workdaystudent:periodconfig', 'enrol_workdaystudent'));
$PAGE->set_heading(get_string('pluginname', 'enrol_workdaystudent'));

// Output the page.
echo $OUTPUT->header();
echo $OUTPUT->heading(
    get_string('workdaystudent:periodconfig', 'enrol_workdaystudent')
);
$mform->display();
echo $OUTPUT->footer();
