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
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Adam Zapletal, Philip Cali, Jason Peak, Chad Mazilly, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/report/lib.php');

ues::require_daos();

abstract class student_gradeviewer {
    public static function grade_gen($userid) {
        return function($course) use ($userid) {
            $name = $course->fullname;

            $courseitem = grade_item::fetch_course_item($course->id);

            if (empty($courseitem)) {
                return "$name -";
            }

            $grade = $courseitem->get_grade($userid);
            if (empty($grade->id)) {
                $grade->finalgrade = null;
            }

            $finalsggrade = sg_get_grade_for_course($course->id, $userid);

            $display = $finalsggrade[0] ?
            $finalsggrade[0] :
            grade_format_gradevalue($grade->finalgrade, $courseitem);

            return "$name $display";
        };
    }

    public static function rank($context, $grade, $totalusers) {
        $ids = array_keys($totalusers);
        $count = count($ids);

        if (empty($grade->finalgrade)) {
            return "-/$count";
        }

        global $DB;

        $sql = 'SELECT COUNT(DISTINCT(g.userid))
            FROM {grade_grades} g
            WHERE g.itemid = :itemid
              AND g.finalgrade IS NOT NULL
              AND g.finalgrade > :final
              AND g.userid IN (' . implode(',', $ids) . ')';

        $params = array(
            'itemid' => $grade->grade_item->id,
            'final' => $grade->finalgrade
        );

        $rank = $DB->count_records_sql($sql, $params) + 1;

        return "$rank/$count";
    }
}
