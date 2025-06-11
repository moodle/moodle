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
 * @package    block_ce_enrollinfo
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Nashid Hasan (nashid@outlook.com), Robert Russo, Steven Jackson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_ce_enrollinfo extends block_base {
    public $course;
    public $user;
    public $content;
    public $coursecontext;

    public function init() {
        $this->title = $this->get_title();
        $this->set_course();
        $this->set_user();
        $this->set_course_context();
    }

    public function get_title() {
        return get_string('pluginname', 'block_ce_enrollinfo');
    }

    public function set_course() {
        global $COURSE;
        $this->course = $COURSE;
    }

    public function set_user() {
        global $USER;
        $this->user = $USER;
    }

    /**
     * Returns this course's context
     *
     * @return context
     */
    private function set_course_context() {
        $this->coursecontext = \context_course::instance($this->course->id);
    }

    /**
     * Indicates which pages types this block may be added to
     *
     * @return array
     */
    public function applicable_formats() {
        return array('course-view' => true, 'my' => false, 'site' => false);
    }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the content to be rendered when displaying this block
     *
     * @return object
     */
    public function get_content() {
        global $CFG, $DB;

        if (!empty($this->content)) {
            return $this->content;
        }

        $fieldid = $CFG->block_ce_enrollinfo_field;

         // Query Moodle to get the user's idnumber.
         $sqlidnumber  = "
             SELECT uid.data AS lsuid
                 FROM {user} u
                 INNER JOIN {user_info_data} uid ON uid.userid = u.id
                 INNER JOIN {user_info_field} uif ON uif.id = uid.fieldid AND uif.id = " . $fieldid . "
             WHERE u.id = ?";

        $return = $DB->get_record_sql($sqlidnumber, array($this->user->id));

        // Query Moodle to get the user's $startdate and $enddate.
        $sql  = "
            SELECT ue.timestart AS creationtime, ue.timeend AS terminationtime
                FROM {user} u
                INNER JOIN {user_enrolments} ue ON ue.userid = u.id
                INNER JOIN {enrol} e ON e.id = ue.enrolid
            WHERE e.enrol = 'd1'
                AND u.id = ?
                AND ue.status = 0
                AND e.courseid = ?";

        $result = $DB->get_records_sql($sql, array($this->user->id, $this->course->id));

        foreach ($result as $item) {
            $startdate = $item->creationtime;
            $enddate   = $item->terminationtime;
        }

        if ((!isset($enddate) || !$return) && $CFG->block_ce_enrollinfo_empty == 0) {
            $this->content = new stdClass;
            return $this->content;
        }

        // Add 56 days (8 weeks) to $startdate to find $eightweekdate.
        // Changed to 21 days (3 weeks) -- strtotime becomes +21, not +56 20190104 pvz.
        if (isset($startdate) != null) {
            $threeweekdate = date( 'l, F j, Y', strtotime ( '+21 day', $startdate ));
        } else {
            $threeweekdate = "";
        }

        // Format $startdate for easy reading.
        if (isset($startdate) != null) {
            $formattedstartdate = date( 'l, F j, Y (g:ia)', $startdate);
        } else {
            $formattedstartdate = "";
        }

        // Format $enddate for easy reading.
        if (isset($enddate) != null) {
            $formattedenddate = date( 'l, F j, Y (g:ia)', $enddate);
        } else {
            $formattedenddate = "";
        }

        if (!isset($enddate)) {
            $enddate = 0;
        }

        // Calculate time left in enrollment. Subtract current date from $enddate.
        $now = date ( 'F j, Y, g:i a' );
        if (isset($startdate) != null OR isset($enddate) != null) {
            $timeleft = ($enddate - strtotime($now));
        } else {
            $timeleft = "0";
        }

        function seconds2human($secs) {
            $date1 = new DateTime("@0");
            $date2 = new DateTime("@$secs");
            $interval = date_diff($date1, $date2);
            $parts = ['years' => 'y', 'months' => 'm', 'days' => 'd', 'hours' => 'h', 'minutes' => 'i', 'seconds' => 's'];
            $formatted = [];
            foreach ($parts as $i => $part) {
                $value = $interval->$part;
                if ($value !== 0) {
                    if ($value == 1) {
                        $i = substr($i, 0, -1);
                    }
                    $formatted[] = "$value $i";
                }
            }
            if (count($formatted) == 1) {
                return $formatted[0];
            } else {
                $str = implode(', ', array_slice($formatted, 0, -1));
                $str .= $str ? ' and ' . $formatted[count($formatted) - 1] : '';
                return $str;
            }
        }

        $formattedtimeleft = seconds2human($timeleft);
        $missingenddate = get_string('ce_missing_enddate', 'block_ce_enrollinfo');
        $formattedtimeleft = $enddate <> 0 ? $formattedtimeleft : $missingenddate;
        $formattedenddate = $enddate <> 0 ? $formattedenddate : $missingenddate;
        $lsuid = ($return && $return->lsuid <> 0) ? $return->lsuid : get_string('ce_missing_lsuid', 'block_ce_enrollinfo');

        $this->content = new stdClass;
        $this->content->text = '';
        if ($return && $return->lsuid <> 0) {
            $this->content->text .= get_string('cestring_lsuid', 'block_ce_enrollinfo', $lsuid);
            $this->content->text .= '<br /><br />';
        }
        if ($formattedstartdate <> "") {
            $this->content->text .= get_string('cestring_formattedstartdate', 'block_ce_enrollinfo', $formattedstartdate);
            $this->content->text .= '<br /><br />';
        }
        if ($threeweekdate <> "") {
            $this->content->text .= get_string('cestring_threeweekdate', 'block_ce_enrollinfo', $threeweekdate);
            $this->content->text .= '<br /><br />';
        }
        if ($enddate <> 0) {
            $this->content->text .= get_string('cestring_formattedenddate', 'block_ce_enrollinfo', $formattedenddate);
            $this->content->text .= '<br /><br />';
            $this->content->text .= get_string('cestring_formattedtimeleft', 'block_ce_enrollinfo', $formattedtimeleft);
            $this->content->text .= '<br /><br />';
        }
        if ($enddate <> 0 && $threeweekdate <> "" && $formattedstartdate <> "") {
            $this->content->text .= get_string('ce_timezone', 'block_ce_enrollinfo');
        }
        return $this->content;
    }
}
