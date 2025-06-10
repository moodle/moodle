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

namespace block_course_hider\controllers;

// use block_course_hider\persistents\course_hider;

class form_controller {

    private $partial;
    /**
     * Let's process the form by getting the results and showing it.
     * @param  object - the form data.
     * @return array - list of courses.
     */
    public function process_form($params = false) {
        global $DB;

        $showhidden = (isset($params->hiddenonly) && $params->hiddenonly == 2)
            ? ''
            : ' c.visible = ' . $params->hiddenonly . ' AND ';
            
        $showlocked = (isset($params->lockedonly) && $params->lockedonly == 2)
            ? ''
            : ' ctx.locked = ' . $params->lockedonly . ' AND ';

        // Check raw input field and use if there's stuff.
        if ($params->raw_input != "") {
            // Cleanse it.
            $stripped = preg_replace('/;/', '', $params->raw_input);
            $stripped = trim($stripped, ';');
            $stripped = trim($stripped);

            // Store the partial for later use.
            $this->partial = $stripped;
            $snippet = "SELECT c.*, ctx.locked
                FROM {course} c
                INNER JOIN {context} ctx ON c.id = ctx.instanceid 
                    AND" . $showlocked . $showhidden. " ctx.contextlevel = '50'
                WHERE c.shortname LIKE '%" . $stripped . "%'
                OR c.fullname LIKE '%" . $stripped . "%'";

        } else {

            $years = \course_hider_helpers::getYears()[$params->ch_years] . " ";
            $semester = \course_hider_helpers::getSemester()[$params->ch_semester];
            $semtype = "";
            $section = "";
            
            if ($params->ch_semester_type != "0") {
                $semtype = \course_hider_helpers::getSemesterType()[$params->ch_semester_type];
                $semtype .= " ";
            }
            if ($params->ch_semester_section != "0") {
                $section = " AND c.shortname LIKE '%" . \course_hider_helpers::getSemesterSection()[$params->ch_semester_section] . "%'";
            } else {
                $section = "";
            }

            // Store the partial for later use.
            $this->partial = $years.$semtype.$semester;
            $snippet = "SELECT c.*, ctx.locked
                FROM {course} c
                INNER JOIN {context} ctx ON c.id = ctx.instanceid 
                    AND" . $showlocked . $showhidden. " ctx.contextlevel = '50'
                WHERE shortname LIKE '" . $this->partial . " %'".$section;
        }

        $courses = $DB->get_records_sql($snippet);
        $courses["lockme"] = $params->lockcourses;
        $courses["hideme"] = $params->hidecourses;

        return $courses;
    }

    /**
     * Execute the form to make the courses either hidden or visible.
     * @param  array - list of courses to process.
     * @param  array - the form data.
     * @return null
     */
    public function execute_hider($fdata = array(), $formdata = array()) {
        global $DB, $CFG;
        $updatecount = 0;
        $time_start = microtime(true);

        // Show/Hide Courses
        // 2 - leave
        // 0 - hide
        // 1 - show

        // Lock/Unlock Courses
        // 2 - leave
        // 0 - unlock
        // 1 - lock
        
        $lockme = $fdata->lock;
        $hideme = $fdata->hide;
        echo('<div class="block_course_hider_container">');
        $courses = explode(",", $fdata->courses);
        foreach($courses as $course) {
            // Update the course to be hidden.
            $dis_one = $DB->get_record('course', array('id' => $course));
            if (isset($hideme) && $hideme < 2) {
                $hideobject = [
                    'id' => $course,
                    'visible' => $hideme,
                ];
                $result = $DB->update_record('course', $hideobject, $bulk = false);
                $hidetask = $hideme == 0 ? 'to be hidden' :  'to be visible';
            } else {
                $hidetask = '';
            }

            if (isset($lockme) && $lockme < 2) {
                $sql =  'UPDATE {context} 
                        SET locked = '.$lockme.'
                        WHERE instanceid = '.$course.' AND contextlevel = "50"';

                $locked = $DB->execute($sql);
                $locktask = $lockme == 1 ? ' and was locked' : ' and was unlocked';
            } else {
                $locktask = '';
            }

            if ((isset($hideme) && $hideme < 2) || (isset($lockme) && $lockme < 2)) {
                $updatecount++;
                mtrace("Course (" . $course . "):
                    <a href='" . $CFG->wwwroot . "/course/view.php?id=" . $course . "' target='_blank'>" . $dis_one->shortname . "</a>
                    was updated " . $hidetask . $locktask . ".<br>");
            } else {
                mtrace("Course (" . $course . "):
                    <a href='" . $CFG->wwwroot . "/course/view.php?id=" . $course . "' target='_blank'>" . $dis_one->shortname . "</a>
                    has been left alone.<br>");

            }
        }
        $time_end = microtime(true);
        if ($updatecount == 0) {
            mtrace("<br><br>Ummmm......nothing was updated.<br>");
        } else {
            $execution_time = $time_end - $time_start;
            mtrace("A total of ". $updatecount. " courses have been updated and took ". number_format($execution_time, 2). " seconds.<br>");
        }
        
        mtrace("<br>--- Process Complete ---<br>");
        echo('</div>');
    }    
}
