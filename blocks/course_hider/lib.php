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
 * Course Hider Tool
 *
 * @package   block_course_hider
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class course_hider_helpers {

    // Redirects.
    /**
     * Convenience wrapper for redirecting to moodle URLs
     *
     * @param  string  $url
     * @param  array   $urlparams   array of parameters for the given URL
     * @param  int     $delay        delay, in seconds, before redirecting
     * @return (http redirect header)
     */
    public function redirect_to_url($url, $urlparams = [], $delay = 2) {
        $moodleurl = new \moodle_url($url, $urlparams);
        redirect($moodleurl, '', $delay);
    }

    /**
     * Config Converter - config settings that have multiple lines with
     * a key value settings will be broken down and converted into an
     * associative array, for example:
     * Monthly 720,
     * Weekly 168
     * .....etc
     * Becomes (Monthly => 720, Weekly => 168)
     * @param  string $configstring setting
     * @param  string $arraytype by default multi, use mirror to miror key/value
     *
     * @return array
     */
    public function config_to_array($configstring, $arraytype = "multi") {

        $configname = get_config('moodle', $configstring);

        // Strip the line breaks.
        $breakstripped = preg_replace("/\r|\n/", " ", $configname);
        // Make sure there are not double spaces.
        $breakstripped = str_replace("  ", " ", $breakstripped);
        // Remove any spaces or line breaks from start or end.
        $breakstripped = trim($breakstripped);

        $exploded = explode(" ", $breakstripped);
        $explodedcount = count($exploded);
        $final = array();

        if ($arraytype == "multi") {
            // Now convert to array and transform to an assoc. array.
            for ($i = 0; $i < $explodedcount; $i += 2) {
                $final[$exploded[$i + 1]] = $exploded[$i];
            }
        } else if ($arraytype == "mirror") {
            // It's possible there may be an extra line break from user input.
            for ($i = 0; $i < $explodedcount; $i++) {
                $tempval = $exploded[$i];
                $final[$tempval] = $tempval;
            }
        }
        return $final;
    }

    public static function getYears() {
        $years = get_config('moodle', "block_course_hider_form_years");
        $range = explode('-', $years);
        $years = array();
        if (empty($range)) {
            mtrace("There is something wrong with the exploding of years");
        } else {
            $start = (int)$range[0];
            $end = (int)$range[1];
            $years = array();
            for ($i = $start; $i <= $end; $i++) {
                $years[] = $i;
            }
        }
        return $years;
    }
    public static function getSemesterType() {
        $semestertypestr = get_config('moodle', "block_course_hider_form_semester_type");
        $semestertypes = explode(',', $semestertypestr);
        array_unshift($semestertypes, 'Skip');
        return $semestertypes;
    }
    public static function getSemester() {
        $semesterstr = get_config('moodle', "block_course_hider_form_semester");
        $semesters = explode(',', $semesterstr);
        return $semesters;
    }
    public static function getSemesterSection() {
        $semestersectionstr = get_config('moodle', "block_course_hider_form_semester_section");
        $semestersections = explode(',', $semestersectionstr);
        array_unshift($semestersections, 'Skip');
        return $semestersections;
    }
}
