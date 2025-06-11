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
 * Date condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_relativedate;

use context_course;
use core_availability\info;
use stdClass;

/**
 * relativedate from course start condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var int relativenumber (how many relative) for condition. */
    private $relativenumber;

    /** @var int relativedwm (what does the date relates to) for condition.
     *
     * 0 => minutes
     * 1 => hours
     * 2 => days
     * 3 => weeks
     * 4 => months
     */
    private $relativedwm;

    /** @var int relativestart (what date relates to) for condition.
     *
     * 1 => After Course start date
     * 2 => Before Course end date
     * 3 => After User enrolment date
     * 4 => After Enrolment method end date
     * 5 => After Course End date
     * 6 => Before Course start date
     * 7 => After completion of an activity

     */
    private $relativestart;

    /**
     * @var int Course module id of the activity used by type 6
     */
    private $relativecoursemodule;

    /**
     * Constructor.
     *
     * @param stdClass $structure Data structure from JSON decode.
     */
    public function __construct($structure) {
        $this->relativenumber = property_exists($structure, 'n') ? intval($structure->n) : 1;
        $this->relativedwm = property_exists($structure, 'd') ? intval($structure->d) : 2;
        $this->relativestart = property_exists($structure, 's') ? intval($structure->s) : 1;
        $this->relativecoursemodule = property_exists($structure, 'm') ? intval($structure->m) : 0;
    }

    /**
     * Saves the data.
     *
     * @return object data structure.
     */
    public function save() {
        return (object)[
            'type' => 'relativedate',
            'n' => intval($this->relativenumber),
            'd' => intval($this->relativedwm),
            's' => intval($this->relativestart),
            'm' => intval($this->relativecoursemodule),
        ];
    }

    /**
     * Determines whether this particular item is currently available.
     *
     * @param bool $not
     * @param info $info
     * @param bool $grabthelot
     * @param int $userid If set, specifies a different user ID to check availability for
     * @return bool True if this item is available to the user, false otherwise
     */
    public function is_available($not, info $info, $grabthelot, $userid) {
        $calc = $this->calc($info->get_course(), $userid);
        if ($calc === 0) {
            // Always not available if for some reason the value could not be calculated.
            return false;
        }
        $allow = time() > $calc;
        if ($not) {
            $allow = !$allow;
        }
        return $allow;
    }

    /**
     * Obtains a string describing this restriction (whether or not it actually applies).
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on this item
     */
    public function get_description($full, $not, info $info): string {
        global $USER;
        $course = $info->get_course();
        $capability = has_capability('moodle/course:manageactivities', context_course::instance($course->id));
        $relative = $this->relativestart;
        if ($relative === 2 || $relative === 5) {
            if ((!isset($course->enddate) || (int)$course->enddate === 0) && $capability) {
                return get_string('noenddate', 'availability_relativedate');
            }
        }
        if ($relative === 2 || $relative === 6) {
            $frut = $not ? 'from' : 'until';
        } else {
            $frut = $not ? 'until' : 'from';
        }
        $calc = $this->calc($course, $USER->id);
        if ($calc === 0) {
            return '(' . trim($this->get_debug_string()) . ')';
        }
        $a = new stdClass();
        $a->rnumber = userdate($calc, get_string('strftimedatetime', 'langconfig'));
        $a->rtime = ($capability && $full) ? '(' . trim($this->get_debug_string()) . ')' : '';
        $a->rela = '';
        return trim(get_string($frut, 'availability_relativedate', $a));
    }

    /**
     * Obtains a representation of the options of this condition as a string for debugging.
     *
     * @return string Text representation of parameters
     */
    protected function get_debug_string() {
        // TODO: remove concat.
        $modname = '';
        if ($this->relativestart === 7) {
            $modname = ' ';
            if (get_coursemodule_from_id('', $this->relativecoursemodule)) {
                $modname .= \core_availability\condition::description_cm_name($this->relativecoursemodule);
            } else {
                $modname .= \html_writer::span(get_string('missing', 'availability_relativedate'), 'alert alert-danger');
            }
        }
        return ' ' . $this->relativenumber . ' ' . self::options_dwm($this->relativenumber)[$this->relativedwm] . ' ' .
               self::options_start($this->relativestart) . $modname;
    }

    /**
     * Obtains a the options for days week months.
     *
     * @param int $i index
     * @return string
     */
    public static function options_start(int $i) {
        switch ($i) {
            case 1:
                return get_string('datestart', 'availability_relativedate');
            case 2:
                return get_string('dateend', 'availability_relativedate');
            case 3:
                return get_string('dateenrol', 'availability_relativedate');
            case 4:
                return get_string('dateendenrol', 'availability_relativedate');
            case 5:
                return get_string('dateendafter', 'availability_relativedate');
            case 6:
                return get_string('datestartbefore', 'availability_relativedate');
            case 7:
                return get_string('datecompletion', 'availability_relativedate');
        }
        return '';
    }

    /**
     * Obtains a the options for hours days weeks months.
     *
     * @param int $number
     * @return array
     */
    public static function options_dwm($number = 1) {
        $s = $number === 1 ? '' : 's';
        return [
            0 => get_string('minute' . $s, 'availability_relativedate'),
            1 => get_string('hour' . $s, 'availability_relativedate'),
            2 => get_string('day' . $s, 'availability_relativedate'),
            3 => get_string('week' . $s, 'availability_relativedate'),
            4 => get_string('month' . $s, 'availability_relativedate'),
        ];
    }

    /**
     * Obtains a the options for hour day week month.
     *
     * @param int $i
     * @return string
     */
    public static function option_dwm(int $i): string {
        switch ($i) {
            case 0:
                return 'minute';
            case 1:
                return 'hour';
            case 2:
                return 'day';
            case 3:
                return 'week';
            case 4:
                return 'month';
        }
        return '';
    }

    /**
     * Perform the calculation.
     *
     * @param stdClass $course
     * @param int $userid
     * @return int relative date.
     */
    private function calc($course, $userid): int {
        $a = $this->relativenumber;
        $b = $this->option_dwm($this->relativedwm);
        $x = "$a $b";
        switch ($this->relativestart) {
            case 6:
                // Before course start date.
                return $this->fixdate("-$x", $course->startdate);
            case 2:
                // Before course end date.
                return $this->fixdate("-$x", $course->enddate);
            case 5:
                // After course end date.
                return $this->fixdate("+$x", $course->enddate);
            case 3:
                // After latest enrolment start date.
                $sql = 'SELECT ue.timestart
                        FROM {user_enrolments} ue
                        JOIN {enrol} e on ue.enrolid = e.id
                        WHERE e.courseid = :courseid AND ue.userid = :userid AND ue.timestart > 0
                        ORDER by ue.timestart DESC';
                $lowest = $this->getlowest($sql, ['courseid' => $course->id, 'userid' => $userid]);
                if ($lowest === 0) {
                    // A teacher or admin without restriction - or a student with no limit set?
                    $sql = 'SELECT ue.timecreated
                            FROM {user_enrolments} ue
                            JOIN {enrol} e on (e.id = ue.enrolid AND e.courseid = :courseid)
                            WHERE ue.userid = :userid
                            ORDER by ue.timecreated DESC';
                    $lowest = $this->getlowest($sql, ['courseid' => $course->id, 'userid' => $userid]);
                }
                return $this->fixdate("+$x", $lowest);
            case 4:
                // After latest enrolment end date.
                $sql = 'SELECT e.enrolenddate
                        FROM {user_enrolments} ue
                        JOIN {enrol} e on ue.enrolid = e.id
                        WHERE e.courseid = :courseid AND ue.userid = :userid
                        ORDER by e.enrolenddate DESC';
                $lowest = $this->getlowest($sql, ['courseid' => $course->id, 'userid' => $userid]);
                return $this->fixdate("+$x", $lowest);
            case 7:
                // Since completion of a module.

                if ($this->relativecoursemodule < 1) {
                    return 0;
                }

                $cm = new stdClass();
                $cm->id = $this->relativecoursemodule;
                $cm->course = $course->id;
                try {
                    $completion = new \completion_info($course);
                    $data = $completion->get_data($cm, false, $userid);
                    return $this->fixdate("+$x", $data->timemodified);
                } catch (\Exception $e) {
                    return 0;
                }
        }
        // After course start date.
        return $this->fixdate("+$x", $course->startdate);
    }

    /**
     * Get the record with the lowest value.
     *
     * @param string $sql
     * @param array $parameters
     * @return int lowest value.
     */
    private function getlowest($sql, $parameters): int {
        global $DB;
        if ($lowestrec = $DB->get_record_sql($sql, $parameters, IGNORE_MULTIPLE)) {
            $recs = get_object_vars($lowestrec);
            foreach ($recs as $value) {
                return $value;
            }
        }
        return 0;
    }


    /**
     * Keep the original hour.
     *
     * @param string $calc
     * @param int $newdate
     * @return int relative date.
     */
    private function fixdate($calc, $newdate): int {
        if ($newdate > 0) {
            $olddate = strtotime($calc, $newdate);
            if ($this->relativedwm > 1) {
                $arr1 = getdate($olddate);
                $arr2 = getdate($newdate);
                return mktime($arr2['hours'], $arr2['minutes'], $arr2['seconds'], $arr1['mon'], $arr1['mday'], $arr1['year']);
            }
            return $olddate;
        }
        return 0;
    }

    /**
     * Used in course/lib.php because we need to disable the completion JS if
     * a completion value affects a conditional activity.
     * @param int|stdClass $course Moodle course object
     * @param int $cmid Course-module id
     * @return bool True if this is used in a condition, false otherwise
     */
    public static function completion_value_used($course, $cmid): bool {
        global $DB;
        $courseobj = (is_object($course)) ? $course : get_course($course);
        $modinfo = get_fast_modinfo($courseobj);
        foreach ($modinfo->cms as $othercm) {
            if (is_null($othercm->availability)) {
                continue;
            }
            $ci = new \core_availability\info_module($othercm);
            $tree = $ci->get_availability_tree();
            foreach ($tree->get_all_children('availability_relativedate\condition') as $cond) {
                if ($cond->relativestart === 7 && $cond->relativecoursemodule === $cmid) {
                    return true;
                }
            }
        }
        // Availability of sections (get_section_info_all) is always null.
        $sqllike = $DB->sql_like('availability', ':availability');
        $params = ['course' => $courseobj->id, 'availability' => '%"s":7,"m":' . $cmid . '%'];
        return count($DB->get_records_sql("SELECT id FROM {course_sections} WHERE course = :course AND $sqllike", $params)) > 0;
    }

    /**
     * Helper for updating ids, implemented for course modules and sections
     *
     * @param string $table
     * @param int $oldid
     * @param int $newid
     * @return bool
     */
    public function update_dependency_id($table, $oldid, $newid) {
        if ($this->relativestart === 7) {
            if (in_array($table, ['course_modules', 'course_sections'])) {
                if ($this->relativecoursemodule === $oldid) {
                    $this->relativecoursemodule = $newid;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Updates this node after restore, returning true if anything changed.
     *
     * @param string $restoreid Restore ID
     * @param int $courseid ID of target course
     * @param \base_logger $logger Logger for any warnings
     * @param string $name Name of this item (for use in warning messages)
     * @return bool True if there was any change
     */
    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name): bool {
        $rec = \restore_dbops::get_backup_ids_record($restoreid, 'course_module', $this->relativecoursemodule);
        if (!($rec && $rec->newitemid)) {
            // If we are on the same course (e.g. duplicate) then we can just use the existing one.
            if (!get_coursemodule_from_id('', $this->relativecoursemodule, $courseid)) {
                $this->relativecoursemodule = 0;
                $logger->process(
                    "Restored item ($name has availability condition on module that was not restored",
                    \backup::LOG_WARNING
                );
                return false;
            }
        } else {
            $this->relativecoursemodule = $rec->newitemid;
        }
        return true;
    }
}
