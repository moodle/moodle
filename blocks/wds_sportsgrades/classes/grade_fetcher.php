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
 * Grade fetcher for the Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_wds_sportsgrades;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Class for fetching student grades
 */
class grade_fetcher {

    /**
     * Get course grades for a student
     *
     * @param int $studentid User ID of the student
     * @return array Course grades
     */
    public static function get_course_grades($studentid) {
        global $DB, $USER;

        // Check access first.
        $search = new search();
        $access = $search::get_user_access($USER->id);

        if (empty($access)) {
            return ['error' => get_string('noaccess', 'block_wds_sportsgrades')];
        }

        $student = $DB->get_record('enrol_wds_students', ['id' => $studentid]);

        // Check if user has access to this specific student.
        if (!$access['all_students'] &&
            !in_array($student->userid, $access['student_ids']) &&
            !self::is_student_in_accessible_sports($studentid, $access['sports'])) {
            return ['error' => get_string('noaccess', 'block_wds_sportsgrades')];
        }

        /* TODO: Do I want or need to cache this?
        // Check cache first.
        $cached_data = self::get_cached_data($studentid);
        if (!empty($cached_data)) {
            return $cached_data;
        }
        */

        // Build generic SQL for grabbing ALL (not just WDS) enrollment.
        $sql = "SELECT c.*
            FROM {course} c
            INNER JOIN {enrol} e ON e.courseid = c.id
            INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
            WHERE ue.status = 0
                AND c.visible = 1
                AND ue.userid = :userid
            ORDER BY c.startdate DESC, c.fullname ASC";

        $courses = $DB->get_records_sql($sql, ['userid' => $student->userid]);

        /*
        // Get courses the student is enrolled in.
        $sql = "SELECT DISTINCT c.id AS courseid, stu.userid, c.fullname, c.shortname,
                sec.academic_period_id as term, sec.section_number, c.startdate
                FROM {course} c
                INNER JOIN {enrol_wds_sections} sec
                    ON sec.moodle_status = c.id
                INNER JOIN {enrol_wds_student_enroll} stuenr
                    ON stuenr.section_listing_id = sec.section_listing_id
                INNER JOIN {enrol_wds_students} stu ON stuenr.universal_id = stu.universal_id
                WHERE stu.id = :studentid
                ORDER BY c.startdate DESC, c.fullname ASC";

        $courses = $DB->get_records_sql($sql, ['studentid' => $studentid]);
        */

        if (empty($courses)) {
            return ['courses' => []];
        }

        $results = [];
        foreach ($courses as $course) {

            // Get the section info.
            $sectioninfo = $DB->get_records('enrol_wds_sections', ['moodle_status' => $course->id]);
            $sectioninfo = reset($sectioninfo);

            if (!isset($sectioninfo->academic_period_id)) {
                if (preg_match('/^(\d{4}\s+\w+(?:\s+\d+)?)(?=\s+[A-Z]{2,}\s+\d+)/', $course->fullname, $matches)) {
                    $derivedterm = $matches[1];
                }
            }

            $groupinfo = groups_get_all_groups($course->id, $student->userid);
            $groupinfo = reset($groupinfo);

            // Set this for later.
            $course->userid = $student->userid;
            $course->term = isset($sectioninfo->academic_period_id) ? $sectioninfo->academic_period_id : $derivedterm;
            $course->section_number = isset($sectioninfo->section_number) ? $sectioninfo->section_number : $groupinfo->name;

            // Get the course total grade item.
            $grade_item = \grade_item::fetch_course_item($course->id);

            // Get the student's final grade object for the course.
            $grade_info = \grade_grade::fetch([
                'itemid' => $grade_item->id,
                'userid' => $course->userid,
            ]);

            if ($grade_info) {
                $finalgrade = $grade_info->finalgrade;
                $grademax = $grade_info->rawgrademax;
                $grade_item->grademax = $grade_info->rawgrademax;
            } else {
                $grade_info = \grade_get_course_grade($course->userid, $course->id);
                $finalgrade = $grade_info->grade;
                $grademax = $grade_item->grademax;
            }

            // Format the numeric grade as a letter.
            $lettergrade = \grade_format_gradevalue(
                $finalgrade,
                $grade_item,
                true,
                GRADE_DISPLAY_TYPE_LETTER,
                $grade_item->get_decimals()
            );

            // Format the numeric grade as real.
            $realgrade = \grade_format_gradevalue(
                $finalgrade,
                $grade_item,
                true,
                GRADE_DISPLAY_TYPE_REAL,
                $grade_item->get_decimals()
            );

            $course_data = [
                'id' => $course->id,
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'section' => $course->section_number,
                'term' => $course->term,
                'startdate' => $course->startdate,
                'final_grade' => isset($grade_info->grade) || isset($grade_info->finalgrade) ? $realgrade : null,
                'final_grade_formatted' => $realgrade,
                'grademax' => number_format($grademax, $grade_item->get_decimals()),
                'letter_grade' => isset($grade_info->grade) || isset($grade_info->finalgrade) ? $lettergrade : 'N/A',
                'grade_items' => self::get_grade_items($course->userid, $course->id)
            ];

            $results[] = $course_data;
        }

        // Sort by date (newest first) and then alphabetically
        usort($results, function($a, $b) {
            if ($a['startdate'] == $b['startdate']) {
                return strcasecmp($a['fullname'], $b['fullname']);
            }
            return $b['startdate'] - $a['startdate'];
        });

        /* TODO: Do I want or need to cache this?
        // Cache the results
        self::cache_data($studentid, $results);
        */

        return ['courses' => $results];
    }

    public static function sort_grade_items(array $grade_items): array {

        // Convert objects to array if keys are not numeric.
        $grade_items = array_values($grade_items);

        // Sort by sortorder.
        usort($grade_items, fn($a, $b) => (int)$a->sortorder <=> (int)$b->sortorder);

        // Partition into course item, categories, and others.
        $course_item = null;
        $categories = [];
        $children = [];
        $uncategorized = [];

        foreach ($grade_items as $item) {
            $id = (int)$item->id;

            if ($item->itemtype === 'course') {
                $course_item = $item;
            }

            $categoryid = isset($item->categoryid) &&
                (int)$item->categoryid != (int)$course_item->iteminstance ?
                (int)$item->categoryid :
                null;

            if ($item->itemtype === 'course') {
                $item->iteminfo = 'course';
                $course_item = $item;
            } elseif ($item->itemtype === 'category') {
                $item->iteminfo = 'category';
                $categories[$item->iteminstance] = $item;
            } elseif ($categoryid) {
                $item->iteminfo = 'categorized';
                $children[$categoryid][] = $item;
            } else {
                $item->iteminfo = 'uncategorized';
                $uncategorized[] = $item;
            }
        }

        // Flatten in desired order.
        $final = [];

        foreach ($categories as $catid => $catitem) {
            $final[] = $catitem;
            if (isset($children[$catid])) {
                foreach ($children[$catid] as $child) {
                    $final[] = $child;
                }
            }
        }

        // Append uncategorized.
        foreach ($uncategorized as $item) {
            $final[] = $item;
        }

        // Append course item last.
        if ($course_item) {
            $final[] = $course_item;
        }

        return $final;
    }


    /**
     * Get grade items for a course
     *
     * @param int $studentid User ID of the student
     * @param int $courseid Course ID
     * @return array Grade items
     */
    private static function get_grade_items($studentid, $courseid) {
        global $CFG, $DB;

        // Get required grade stuffs.
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->dirroot . '/grade/lib.php');

        // Get grade items for the course
        $grade_items = \grade_item::fetch_all(['courseid' => $courseid]);

        if (empty($grade_items)) {
            return [];
        }

        // Sort em.
        $grade_items = self::sort_grade_items($grade_items);

        $results = [];
        foreach ($grade_items as $item) {


            /* This is to try to get some due dates
            if ($item->itemtype == 'mod') {

                // Add an empty array to the item.
                $item->duedates = [];

                // Get the module name from the item.
                $modulename = $item->itemmodule;

                // Get the module object.
                $moduleinstance = $DB->get_record($modulename, ['id' => $item->iteminstance], '*', MUST_EXIST);

                // These are some date fields.
                $datefields = ['duedate', 'cutoffdate', 'timeclose', 'submissionend'];

                // Loop through the date fields and get them.
                foreach ($datefields as $field) {

                    if (isset($moduleinstance->{$field}) && (!empty($moduleinstance->{$field}) || $moduleinstance->{$field} > 0)) {
                        // echo "$item->itemname $field: " . userdate($moduleinstance->{$field}) . "<br>";
                    }
                }
            }
            */

            if ($item->itemtype == 'course') {
                  continue;
            }

            if ($item->itemtype == 'category') {
                  $category = $DB->get_record('grade_categories', ['id' => $item->iteminstance]);
            }

            // Get the grade for this item
            $grade = new \grade_grade([
                'itemid' => $item->id,
                'userid' => $studentid
            ]);

            // Get calculation info if available
            $weight = null;
            $contribution = null;

            $weight = number_format($grade->aggregationweight * 100, $item->get_decimals());

            $finalgrade = !is_null($grade->finalgrade) ? number_format($grade->finalgrade, $item->get_decimals()) : '-';
            $grademax = number_format($grade->rawgrademax, $item->get_decimals());

            // Format the numeric grade as a letter.
            $lettergrade = \grade_format_gradevalue(
                $grade->finalgrade,
                $item,
                true,
                GRADE_DISPLAY_TYPE_LETTER
            );

            if ($weight == 0) {
                $weight = '-';
                $lettergrade = '-';
            } else if ($weight < 0) {
                $weight = 'Extra Credit';
                $lettergrade = '-';
            } else {
                $weight = $weight . '%';
            }

            if ($item->itemtype == 'category') {
                $results[] = [
                    'id' => $item->id,
                    'name' => $category->fullname,
                    'type' => $item->itemtype,
                    'module' => $item->itemmodule,
                    'weight' => $weight,
                    'weight_formatted' => $weight,
                    'iteminfo' => $item->iteminfo,
                    //'grade' => null,
                    //'grade_formatted' => null,
                    //'grademax' => null,
                    //'letter' => null,
                    //'percentage' => null,
                    //'percentage_formatted' => null
                    'grade' => $finalgrade,
                    'grade_formatted' => $finalgrade . ' / ' . $grademax,
                    'grademax' => $grademax,
                    'letter' => $lettergrade,
                    'percentage' => empty($grade->finalgrade) || $grademax == 0 ? null :
                        ($finalgrade / $grademax) * 100,
                    'percentage_formatted' => empty($grade->finalgrade) || $grademax == 0 ? '-' :
                        number_format(($finalgrade / $grademax) * 100, $item->get_decimals()) . '%'
                ];
            } else if ($item->iteminfo == 'categorized') {
                $results[] = [
                    'id' => $item->id,
                    'name' => $item->itemname,
                    'type' => $item->itemtype,
                    'module' => $item->itemmodule,
                    'weight' => $weight,
                    'weight_formatted' => $weight,
                    'iteminfo' => $item->iteminfo,
                    'grade' => $finalgrade,
                    'grade_formatted' => $finalgrade . ' / ' . $grademax,
                    'grademax' => $grademax,
                    'letter' => $lettergrade,
                    'percentage' => empty($grade->finalgrade) || $grademax == 0 ? null :
                        ($finalgrade / $grademax) * 100,
                    'percentage_formatted' => empty($grade->finalgrade) || $grademax == 0 ? '-' :
                        number_format(($finalgrade / $grademax) * 100, $item->get_decimals()) . '%'
                ];
            } else {
                $results[] = [
                    'id' => $item->id,
                    'name' => $item->itemname,
                    'type' => $item->itemtype,
                    'module' => $item->itemmodule,
                    'weight' => $weight,
                    'weight_formatted' => $weight,
                    'iteminfo' => $item->iteminfo,
                    'grade' => $finalgrade,
                    'grade_formatted' => $finalgrade . ' / ' . $grademax,
                    'grademax' => $grademax,
                    'letter' => $lettergrade,
                    'percentage' => empty($grade->finalgrade) || $grademax == 0 ? null :
                        ($finalgrade / $grademax) * 100,
                    'percentage_formatted' => empty($grade->finalgrade) || $grademax == 0 ? '-' :
                        number_format(($finalgrade / $grademax) * 100, $item->get_decimals()) . '%'
                ];
            }
        }

        return $results;
    }

    /**
     * Check if a student is in a sport that the user has access to
     *
     * @param int $studentid Student ID
     * @param array $sports Array of sport codes
     * @return bool True if student is in an accessible sport
     */
    private static function is_student_in_accessible_sports($studentid, $sports) {
        global $DB;

        if (empty($sports)) {
            return false;
        }

        list($in_sql, $params) = $DB->get_in_or_equal($sports);
        $params[] = $studentid;

        $sql = "SELECT COUNT(*)
                FROM {enrol_wds_students_meta}
                WHERE datatype = 'Athletic_Team_ID'
                AND data $in_sql
                AND studentid = ?";

        return $DB->count_records_sql($sql, $params) > 0;
    }

    /**
     * Get cached grade data for a student
     *
     * @param int $studentid Student ID
     * @return array|false Cached data or false if not found/expired
     */
    private static function get_cached_data($studentid) {
        global $DB;

        $now = time();

        $sql = "SELECT data
                FROM {block_wds_sportsgrades_cache}
                WHERE studentid = :studentid
                AND timeexpires > :now
                ORDER BY timecreated DESC
                LIMIT 1";

        $cached = $DB->get_field_sql($sql, ['studentid' => $studentid, 'now' => $now]);

        if (!empty($cached)) {
            return json_decode($cached, true);
        }

        return false;
    }

    /**
     * Cache grade data for a student
     *
     * @param int $studentid Student ID
     * @param array $data Data to cache
     * @return bool Success
     */
    private static function cache_data($studentid, $data) {
        global $DB;

        // Cache for 1 hour
        $expiry = time() + (60 * 60);

        $cache_record = new \stdClass();
        $cache_record->studentid = $studentid;
        $cache_record->data = json_encode(['courses' => $data]);
        $cache_record->timecreated = time();
        $cache_record->timeexpires = $expiry;

        return $DB->insert_record('block_wds_sportsgrades_cache', $cache_record);
    }
}
