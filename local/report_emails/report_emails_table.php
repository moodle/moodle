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
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @package   local_report_emails
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

/**
 * Base class for the table used by local_report_users_login
 *
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_report_emails_table extends table_sql {

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_sender($row) {
        global $CFG, $DB;

        if ($sender = $DB->get_record('user', array('id' => $row->senderid))) {
            return fullname($sender);
        } else {
            return $CFG->supportname;
        }
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_created($row) {
        global $CFG;

        return date($CFG->iomad_date_format, $row->created);
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_due($row) {
        global $CFG;

        return date($CFG->iomad_date_format, $row->due);
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_sent($row) {
        global $CFG;

        if (empty($row->sent)) {
            return get_string('never');
        } else {
            return date($CFG->iomad_date_format, $row->sent);
        }
    }

    /**
     * Generate the display of the user's created timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_controls($row) {
        global $CFG, $output;

        $context = context_system::instance();
        if (iomad::has_capability('local/report_emails:resend', $context) && !empty($row->sent)) {
            $resendlink = new moodle_url('/local/report_emails/index.php',
                                                array('emailid' => $row->emailid));
            return $output->single_button($resendlink, get_string('resend', 'local_report_emails'));
        } else {
            return;
        }
    }
}