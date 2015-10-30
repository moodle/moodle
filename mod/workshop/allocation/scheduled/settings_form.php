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
 * Scheduled allocator's settings
 *
 * @package     workshopallocation_scheduled
 * @subpackage  mod_workshop
 * @copyright   2012 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');
require_once(dirname(dirname(__FILE__)) . '/random/settings_form.php'); // parent form

/**
 * Allocator settings form
 *
 * This is used by {@see workshop_scheduled_allocator::ui()} to set up allocation parameters.
 */
class workshop_scheduled_allocator_form extends workshop_random_allocator_form {

    /**
     * Definition of the setting form elements
     */
    public function definition() {
        global $OUTPUT;

        $mform = $this->_form;
        $workshop = $this->_customdata['workshop'];
        $current = $this->_customdata['current'];

        if (!empty($workshop->submissionend)) {
            $strtimeexpected = workshop::timestamp_formats($workshop->submissionend);
        }

        if (!empty($current->timeallocated)) {
            $strtimeexecuted = workshop::timestamp_formats($current->timeallocated);
        }

        $mform->addElement('header', 'scheduledallocationsettings', get_string('scheduledallocationsettings', 'workshopallocation_scheduled'));
        $mform->addHelpButton('scheduledallocationsettings', 'scheduledallocationsettings', 'workshopallocation_scheduled');

        $mform->addElement('checkbox', 'enablescheduled', get_string('enablescheduled', 'workshopallocation_scheduled'), get_string('enablescheduledinfo', 'workshopallocation_scheduled'), 1);

        $mform->addElement('header', 'scheduledallocationinfo', get_string('currentstatus', 'workshopallocation_scheduled'));

        if ($current === false) {
            $mform->addElement('static', 'infostatus', get_string('currentstatusexecution', 'workshopallocation_scheduled'),
                get_string('resultdisabled', 'workshopallocation_scheduled').' '.
                html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/invalid'))));

        } else {
            if (!empty($current->timeallocated)) {
                $mform->addElement('static', 'infostatus', get_string('currentstatusexecution', 'workshopallocation_scheduled'),
                    get_string('currentstatusexecution1', 'workshopallocation_scheduled', $strtimeexecuted).' '.
                    html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/valid'))));

                if ($current->resultstatus == workshop_allocation_result::STATUS_EXECUTED) {
                    $strstatus = get_string('resultexecuted', 'workshopallocation_scheduled').' '.
                        html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/valid')));
                } else if ($current->resultstatus == workshop_allocation_result::STATUS_FAILED) {
                    $strstatus = get_string('resultfailed', 'workshopallocation_scheduled').' '.
                        html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/invalid')));
                } else {
                    $strstatus = get_string('resultvoid', 'workshopallocation_scheduled').' '.
                        html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/invalid')));
                }

                if (!empty($current->resultmessage)) {
                    $strstatus .= html_writer::empty_tag('br').$current->resultmessage; // yes, this is ugly. better solution suggestions are welcome.
                }
                $mform->addElement('static', 'inforesult', get_string('currentstatusresult', 'workshopallocation_scheduled'), $strstatus);

                if ($current->timeallocated < $workshop->submissionend) {
                    $mform->addElement('static', 'infoexpected', get_string('currentstatusnext', 'workshopallocation_scheduled'),
                        get_string('currentstatusexecution2', 'workshopallocation_scheduled', $strtimeexpected).' '.
                        html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/caution'))));
                    $mform->addHelpButton('infoexpected', 'currentstatusnext', 'workshopallocation_scheduled');
                } else {
                    $mform->addElement('checkbox', 'reenablescheduled', get_string('currentstatusreset', 'workshopallocation_scheduled'),
                       get_string('currentstatusresetinfo', 'workshopallocation_scheduled'));
                    $mform->addHelpButton('reenablescheduled', 'currentstatusreset', 'workshopallocation_scheduled');
                }

            } else if (empty($current->enabled)) {
                $mform->addElement('static', 'infostatus', get_string('currentstatusexecution', 'workshopallocation_scheduled'),
                    get_string('resultdisabled', 'workshopallocation_scheduled').' '.
                    html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/invalid'))));

            } else if ($workshop->phase != workshop::PHASE_SUBMISSION) {
                $mform->addElement('static', 'infostatus', get_string('currentstatusexecution', 'workshopallocation_scheduled'),
                    get_string('resultfailed', 'workshopallocation_scheduled').' '.
                    html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/invalid'))).
                    html_writer::empty_tag('br').
                    get_string('resultfailedphase', 'workshopallocation_scheduled'));

            } else if (empty($workshop->submissionend)) {
                $mform->addElement('static', 'infostatus', get_string('currentstatusexecution', 'workshopallocation_scheduled'),
                    get_string('resultfailed', 'workshopallocation_scheduled').' '.
                    html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/invalid'))).
                    html_writer::empty_tag('br').
                    get_string('resultfaileddeadline', 'workshopallocation_scheduled'));

            } else if ($workshop->submissionend < time()) {
                // next cron will execute it
                $mform->addElement('static', 'infostatus', get_string('currentstatusexecution', 'workshopallocation_scheduled'),
                    get_string('currentstatusexecution4', 'workshopallocation_scheduled').' '.
                    html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/caution'))));

            } else {
                $mform->addElement('static', 'infostatus', get_string('currentstatusexecution', 'workshopallocation_scheduled'),
                    get_string('currentstatusexecution3', 'workshopallocation_scheduled', $strtimeexpected).' '.
                    html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/caution'))));
            }
        }

        parent::definition();

        $mform->addHelpButton('randomallocationsettings', 'randomallocationsettings', 'workshopallocation_scheduled');
    }
}
