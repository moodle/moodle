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
 * Period configuration page for WDS Post Grades block.
 *
 * @package    block_wds_postgrades
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/blocks/wds_postgrades/classes/period_settings.php');

// Ensure user has admin access.
require_login();
require_capability('moodle/site:config', context_system::instance());

// Go nuts here.
if (!is_siteadmin()) {
    redirect(new moodle_url('/'));
}

// Set up the page.
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/blocks/wds_postgrades/period_config.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('periodconfig', 'block_wds_postgrades'));
$PAGE->set_heading(get_string('periodconfig', 'block_wds_postgrades'));

// Define the form for period configuration.
class block_wds_postgrades_period_config_form extends moodleform {

    protected function definition() {
        global $DB;

        $mform = $this->_form;
        $periods = $this->_customdata['periods'];

        if (empty($periods)) {
            $mform->addElement('static', 'noperiods', '', get_string('noperiods', 'block_wds_postgrades'));
            $mform->addElement('static', 'noperiodsdesc', '', get_string('noperiodsdesc', 'block_wds_postgrades'));
            return;
        }

        foreach ($periods as $period) {
            $periodid = $period->academic_period_id;

            // Add heading for each period.
            $mform->addElement('header', 'period_' . $periodid, get_string('periodheading', 'block_wds_postgrades', $periodid));
            $mform->addElement('static', 'period_desc_' . $periodid, '', get_string('perioddescription', 'block_wds_postgrades'));

            // Add start date/time selector.
            $mform->addElement('date_time_selector', 'period_' . $periodid . '_start',
                               get_string('periodstartdate', 'block_wds_postgrades'));

            // Add end date/time selector.
            $mform->addElement('date_time_selector', 'period_' . $periodid . '_end',
                               get_string('periodenddate', 'block_wds_postgrades'));

            // Set defaults if they exist in our custom table.
            $periodrecord = $DB->get_record('block_wds_postgrades_periods', ['academic_period_id' => $periodid]);

            if ($periodrecord) {
                $mform->setDefault('period_' . $periodid . '_start', $periodrecord->start_time);
                $mform->setDefault('period_' . $periodid . '_end', $periodrecord->end_time);
            }

            // Add a hidden field to store the DB record ID if it exists.
            if ($periodrecord) {
                $mform->addElement('hidden', 'period_' . $periodid . '_id', $periodrecord->id);
                $mform->setType('period_' . $periodid . '_id', PARAM_INT);
            }
        }

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $periods = $this->_customdata['periods'];

        foreach ($periods as $period) {
            $periodid = $period->academic_period_id;
            $startfieldname = 'period_' . $periodid . '_start';
            $endfieldname = 'period_' . $periodid . '_end';

            if (!empty($data[$startfieldname]) && !empty($data[$endfieldname])) {
                if ($data[$startfieldname] >= $data[$endfieldname]) {
                    $errors[$endfieldname] = get_string('endbeforestart', 'block_wds_postgrades');
                }
            }
        }

        return $errors;
    }
}

// Get active periods.
$periods = \block_wds_postgrades\period_settings::get_active_periods();

// Create and process the form.
$form = new block_wds_postgrades_period_config_form(null, ['periods' => $periods]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'blocksettingwds_postgrades']));
} else if ($data = $form->get_data()) {
    global $DB;
    $now = time();

    // Process the form data.
    foreach ($periods as $period) {
        $periodid = $period->academic_period_id;
        $startfieldname = 'period_' . $periodid . '_start';
        $endfieldname = 'period_' . $periodid . '_end';
        $idfieldname = 'period_' . $periodid . '_id';

        // Only process if both start and end times are provided.
        if (isset($data->$startfieldname) && isset($data->$endfieldname)) {

            // Prepare the record.
            $record = new stdClass();
            $record->academic_period_id = $periodid;
            $record->start_time = $data->$startfieldname;
            $record->end_time = $data->$endfieldname;
            $record->timemodified = $now;

            // Check if record exists to update or insert.
            if (isset($data->$idfieldname)) {

                // Update existing record.
                $record->id = $data->$idfieldname;
                $DB->update_record('block_wds_postgrades_periods', $record);
            } else {

                // Insert new record.
                $record->timecreated = $now;
                $DB->insert_record('block_wds_postgrades_periods', $record);
            }
        }
    }

    redirect(
        new moodle_url('/blocks/wds_postgrades/period_config.php'),
        get_string('changessaved', 'block_wds_postgrades'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Output page.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('settings', 'block_wds_postgrades'));

if (empty($periods)) {
    echo $OUTPUT->notification(get_string('noperiods', 'block_wds_postgrades'), 'warning');
}

$form->display();
echo $OUTPUT->footer();
