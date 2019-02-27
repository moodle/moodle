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
 * @package   local_report_user_license_allocations
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

/**
 * Base class for the table used by local_report_user_license_allocations
 *
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_report_user_completion_table extends table_sql {

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_coursename($row) {
        global $output;

        if (!$this->is_downloading()) {
            $completionurl = '/local/report_completion/index.php';
            return $output->single_button(new moodle_url($completionurl, array('courseid' => $row->courseid)), $row->coursename);
        } else {
            return $row->coursename;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_licenseallocated($row) {
        global $CFG;

        if (!empty($row->licenseallocated)) {
            return date($CFG->iomad_date_format, $row->licenseallocated);
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timeenrolled($row) {
        global $CFG;

        if (!empty($row->timeenrolled)) {
            return date($CFG->iomad_date_format, $row->timeenrolled);
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timecompleted($row) {
        global $CFG;

        if (!empty($row->timecompleted)) {
            return date($CFG->iomad_date_format, $row->timecompleted);
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's course expiration timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timeexpires($row) {
        global $CFG;

        if (!empty($row->timeexpires)) {
            if ($icourserec = $DB->get_record_sql("SELECT * FROM {iomad_courses} WHERE courseid =: courseid AND expireafter !=0", array('courseid' => $row->courseid))) {
                $expiredate = $row->timecompleted + $icourserec->timeexpires * 24 * 60 * 60;
                return date($CFG->iomad_date_format, $expiredate);
            } else {
                return;
            }
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_finalscore($row) {
        global $CFG;

        if (!empty($row->finalscore) && !empty($row->timeenrolled)) {
            return round($row->finalscore, 0)."%";
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $DB;

        // Do nothing if downloading.
        if ($this->is_downloading()) {
            return;
        }

        // Get the buttons.
        if (!empty($row->action) || (!empty($row->licenseallocated) && !empty($row->timeenrolled) && empty($row->timecompleted))) {
            // Link for user delete
            $dellink = new moodle_url('/local/report_users/userdisplay.php', array(
                    'userid' => $row->userid,
                    'delete' => $row->userid,
                    'courseid' => $row->courseid,
                    'action' => 'delete'
                ));
            $clearlink = new moodle_url('/local/report_users/userdisplay.php', array(
                    'userid' => $row->userid,
                    'delete' => $row->userid,
                    'courseid' => $row->courseid,
                    'action' => 'clear'
                ));
            if (has_capability('block/iomad_company_admin:editusers', context_system::instance())) {
                // Its from the course_completions table.  Check the license type.
                if ($DB->get_record_sql("SELECT cl.* FROM {companylicense} cl
                                         JOIN {companylicense_users} clu
                                         ON (cl.id = clu.licenseid)
                                         WHERE cl.program = 1
                                         AND clu.userid = :userid
                                         AND clu.licensecourseid = :courseid",
                                         array('userid' => $row->userid,
                                               'courseid' => $row->courseid))) {
                    $delaction = '<a class="btn btn-danger" href="'.$clearlink.'">' . get_string('clear', 'local_report_users') . '</a>';
                } else {
                    $delaction = '<a class="btn btn-danger" href="'.$dellink.'">' . get_string('delete', 'local_report_users') . '</a>' .
                                 '<a class="btn btn-danger" href="'.$clearlink.'">' . get_string('clear', 'local_report_users') . '</a>';
                }
            } else {
                $delaction = '';
            }

            return $delaction;
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_certificate($row) {
        global $DB, $output;

        if ($this->is_downloading()) {
            return;
        }

        if (!empty($row->timecompleted) && $certmodule = $DB->get_record('modules', array('name' => 'iomadcertificate'))) {
            if ($certificateinfo = $DB->get_record('iomadcertificate', array('course' => $row->courseid))) {
                if ($certificatemodinstance = $DB->get_record('course_modules', array('course' => $row->courseid,
                                                                                      'module' => $certmodule->id,
                                                                                      'instance' => $certificateinfo->id))) {
                    return $output->single_button(new moodle_url('/mod/iomadcertificate/view.php',
                                                                 array('id' => $certificatemodinstance->id,
                                                                       'action' => 'get',
                                                                       'userid' => $row->id,
                                                                       'sesskey' => sesskey())),
                                                   get_string('downloadcert', 'local_report_users'));
                } else {
                    return;
                }
            } else {
                return;
            }
        } else {
            return;
        }
    }

    /**
     * Generate the display of the user's course status
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_status($row) {
        global $DB;

        if (!empty($row->timecompleted)) {
            $progress = 100;
        } else {
            $total = $DB->count_records('course_completion_criteria', array('course' => $row->courseid));
            if ($total != 0) {
                $usercount = $DB->count_records('course_completion_crit_compl', array('course' => $row->courseid, 'userid' => $row->userid));
                $progress = round($usercount * 100 / $total, 0);
            } else {
                $progress = -1;
            }
        }
        if ($progress == -1) {
            return get_string('notstarted', 'local_report_users');
        } else {
            if (!$this->is_downloading()) {
                return '<div class="progress" style="height:20px">
                        <div class="progress-bar" style="width:' . $progress . '%;height:20px">' . $progress . '%</div>
                        </div>';
            } else {
                return "$progress%";
            }
        }
    }
}