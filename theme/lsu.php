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
 * General LSU Theme Functions.
 *
 * @package   theme_lsu
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * General LSU Class with various functions.
 */
class lsu_theme_snippets {

    /**
     * Little snippet to display the size of the course using the bootstrap
     * progressbar.
     * @param int $isadmin - Is the current user admin or no?
     * @return string - The html to display.
     */
    public function show_course_size($isallowed = 0) {
        global $OUTPUT, $COURSE, $CFG, $USER;

        $coursesize = $this->get_file_size($COURSE->id);

        $sizesetting = (int)get_config('theme_snap', 'course_size_limit');
        if ($sizesetting == 0) {
            return '';
        }
        $coursesnippet = '<div id="snap-show-course-size">';
        $percentage = number_format(((($coursesize / 1048576) * 100) / $sizesetting), 0);

        $percent = round((($coursesize / 1048576) * 100) / $sizesetting, 0);

        // number_format( $myNumber, 2, '.', '' );
        // Let's format this number so it's readable.
        $size = $this->formatBytes($coursesize);

        // What is the percentage of it being full.
        $displayclass = $this->get_bootstrap_barlevel($percent);
        $show_course_size_link = "";

        if ($isallowed) {
            $show_course_size_link = ' <a href="' . $CFG->wwwroot .
                '/report/coursesize/course.php?id='
                . $COURSE->id .
                '" target="_blank">' .
                '<i class="fa fa-question-circle-o" aria-hidden="true"></i>' .
                '</a>';
        }

        // Do not show percentage if below 10%.
        $percentsnippet = $percent < 10 ? '' : '<span class="fg-' . $displayclass . '">' . $percentage . '%</span>';

        $coursesnippet .= 'Course File Size: '
            . $size
            . $show_course_size_link .
            '<div class="progress" ' .
            'role="progressbar" ' .
            'aria-label="Success example" ' .
            'aria-valuenow="' . $percent . '" ' .
            'aria-valuemin="0" ' .
            'aria-valuemax="100"> ' .
            '<div class="progress-bar bg-' . $displayclass .
            '" style="width: ' . $percent . '%">' .
            $percentsnippet .
            '</div></div></div>';
        return $coursesnippet;
    }

    /**
     * Based on the percentage show the type of bar to use.
     * @param  [int]        $percentage number ranging from 0-100
     * @return [string]     partial string used in the div-class.
     */
    private function get_bootstrap_barlevel($percentage) {

        if ($percentage > 0 && $percentage < 50) {
            return "success";
        } else if ($percentage >= 50 && $percentage < 75) {
            return "info";
        } else if ($percentage >= 75 && $percentage < 90) {
            return "warning";
        } else if ($percentage >= 90 && $percentage < 100) {
            return "danger";
        } else if ($percentage >= 100) {
            return "excessive";
        }
    }

    /**
     * Get the total file size of a course.
     * @param int $courseid - The course id.
     * @return int - Total size.
     */
    private function get_file_size($courseid = 0) {

        global $COURSE, $DB;
        if ($courseid == 0) {
            $courseid = $COURSE->id;
        }

        // Are we using cron or no?
        if (get_config('report_coursesize', 'calcmethod') == 'cron') {

            // Search the report_coursesize table first.
            $found = $DB->get_record_sql(
                "SELECT filesize, timestamp
                FROM {report_coursesize}
                WHERE course = ?
                AND timestamp = (SELECT MAX(timestamp) FROM {report_coursesize} WHERE course = ?)",
                array($courseid, $courseid)
            );

            if ($found) {
                return $found->filesize;
            }
        }

        // No records found for this course so let's find the size.
        $sql = "SELECT c.id, c.shortname, c.category, ca.name, rc.filesize
            FROM {course} c
            JOIN (
                SELECT id AS course, SUM(filesize) AS filesize
                    FROM (
                        SELECT c.id, f.filesize
                        FROM {course} c
                        JOIN {context} cx ON cx.contextlevel = 50 AND cx.instanceid = c.id
                        JOIN {files} f ON f.contextid = cx.id
                        WHERE c.id = " . $courseid . "

                        UNION ALL

                        SELECT c.id, f.filesize
                        FROM {block_instances} bi
                        JOIN {context} cx1 ON cx1.contextlevel = 80 AND cx1.instanceid = bi.id
                        JOIN {context} cx2 ON cx2.contextlevel = 50 AND cx2.id = bi.parentcontextid
                        JOIN {course} c ON c.id = cx2.instanceid
                        JOIN {files} f ON f.contextid = cx1.id
                        WHERE c.id = " . $courseid . "

                        UNION ALL

                        SELECT c.id, f.filesize
                        FROM {course_modules} cm
                        JOIN {context} cx ON cx.contextlevel = 70 AND cx.instanceid = cm.id
                        JOIN {course} c ON c.id = cm.course
                        JOIN {files} f ON f.contextid = cx.id
                        WHERE c.id = " . $courseid . "
                    ) x
                    GROUP BY id
            ) rc on rc.course = c.id
            JOIN {course_categories} ca on c.category = ca.id AND c.id=". $courseid. "
            ORDER BY rc.filesize DESC";

        $csize = $DB->get_record_sql($sql);

        // Make sure we are returning something regardless of data returned.
        if ($csize == false) {
            return '0';
        } else {
            return $csize->filesize;
        }
    }

    /**
     * Format a data size number to make it human readable.
     * @param int $bytes - The size in bytes.
     * @param int $precision - The number of decimal places.
     * @return bool
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes = $bytes / pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Check to see if this user is a student.
     * @return bool
     */
    public function are_you_student() {
        global $DB, $COURSE, $USER;

        // Don't try and check for invalid courses.
        if ($COURSE->id == 0 || $COURSE->id == 1) {
            return;
        }

        // Set the context.
        $context = context_course::instance($COURSE->id);

        // If user can edit grades then let them see how big it is.
        $hotforteacher = has_capability('moodle/grade:edit', $context, $USER);

        if ($hotforteacher == true) {
            // They are NOT a student, return false.
            return false;
        } else {
            // They ARE a student, return true.
            return true;
        }
    }
}

/**
 * General LSU Class with various functions.
 */
class lsu_snippets {

    /**
     * A simple function to remove double spaces in between words and trim the edges.
     * @param  string $word The string to clean.
     * @return string $word The cleaned string.
     */
    private static function quick_string_clean($word) {
        // Make sure there are not double spaces.
        $word = str_replace("  ", " ", $word);
        // Remove any spaces or line breaks from start or end.
        $word = trim($word);
        return $word;
    }

    /**
     * Config Converter - config settings that have multiple lines with
     * a key value settings will be broken down and converted into an
     * associative array.
     * Example1 : multi
     * Monthly 720
     * Weekly 168
     * Becomes (Monthly => 720, Weekly => 168)
     *
     * Example2: comma
     * LXD, true
     * IT, false
     * Becomes (LXD => true, IT => false)
     
     * Example3: index
     * LXD
     * IT
     * Becomes (0 => LXD, 1 => IT)
     *
     * @param  string $configstring setting
     * @param  string $arraytype by default multi, comma, use mirror to miror key/value
     * @param  bool $booly is the second param using bool? (see example2)
     *
     * @return array
     */
    public static function config_to_array($configstring, $arraytype = "multi", $booly = false) {

        $configname = get_config('moodle', $configstring);
        if ($configname == false) {
            return false;
        }

        $final = array();

        if ($arraytype == "multi") {
            $breakstripped = preg_replace("/\r|\n/", " ", $configname);
            
            self::quick_string_clean($breakstripped);
            $exploded = explode(" ", $breakstripped);
            $explodedcount = count($exploded);
            // Now convert to array and transform to an assoc. array.
            for ($i = 0; $i < $explodedcount; $i += 2) {
                $final[$exploded[$i + 1]] = $exploded[$i];
            }
        
        } else if ($arraytype == "comma") {
            // Replace the line breaks with tilde.
            $breakstripped = preg_replace("/\r|\n/", "~", $configname);
            // Clean the string.
            $breakstripped = self::quick_string_clean($breakstripped);
            // You might get a double tilde depending on OS
            $breakstripped = str_replace("~~", "~", $breakstripped);
            // Now break into chunks
            $exploded = explode("~", $breakstripped);
            $explodedcount = count($exploded);
            // Now convert to array and transform to an assoc. array.
            for ($i = 0; $i < $explodedcount; $i++) {
                $temp = explode(",", $exploded[$i]);
                $temp[1] = $booly ? (bool)$temp[1] : $temp[1]; 
                $final[$temp[0]] = $temp[1];
            }
        } else if ($arraytype == "index") {

            $final = $exploded;
        }
        return $final;
    }

    /**
     * This grabs the rols from a plugins settings (manual entries) and checks to
     * see if they have access.
     * @param  integer $cid course id number.
     * @param  string $plugin the name of the setting to get.
     * @return [array] Array showing if it was found and if they have access.
     */
    public static function role_check_course_size($cid = 0, $plugin = "") {
        global $OUTPUT, $COURSE, $CFG, $USER;
        
        $found = false;
        $access = false;

        $results = array(
            "found" => false,
            "access" => false
        );
        $context = context_course::instance($cid);
        $roles = get_user_roles($context, $USER->id, true);

        if (empty($roles)) {
            return $results;
        }
        $role = key($roles);
        $rolename = $roles[$role]->shortname;

        // Get the list of roles in course size settings that allows specific roles access to view.
        if ($customroles = self::config_to_array($plugin, "comma", true)) {
            foreach ($customroles as $k => $v) {
                if (strtolower($k) == strtolower($rolename)) {
                    $found = true;
                    $access = $v;
                }
            }
        } else {
            return $results;
        }
        return array(
            "found" => $found,
            "access" => $access
        );
    }
}
