<?php

// This file is part of the certificateuv module for Moodle - http://moodle.org/
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
 * Deprecated certificateuv functions.
 *
 * @package    mod_certificateuv
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Prepare to be print the date -- defaults to time.
 *
 * @deprecated since certificateuv version 2012052501
 * @param stdClass $certificateuv
 * @param stdClass $course
 * @return string the date
 */
function certificateuv_generate_date($certificate, $course) {
    debugging('certificate_generate_date is deprecated, please use certificate_get_date instead which will
               return a date in a human readable format.', DEBUG_DEVELOPER);

    global $DB, $USER;

    // Set certificate date to current time, can be overwritten later
    $date = time();

    if ($certificate->printdate == '2') {
        // Get the enrolment end date
        $sql = "SELECT MAX(c.timecompleted) as timecompleted
                FROM {course_completions} c
                WHERE c.userid = :userid
                AND c.course = :courseid";
        if ($timecompleted = $DB->get_record_sql($sql, array('userid' => $USER->id, 'courseid' => $course->id))) {
            if (!empty($timecompleted->timecompleted)) {
                $date = $timecompleted->timecompleted;
            }
        }
    } else if ($certificate->printdate > 2) {
        if ($modinfo = certificateuv_get_mod_grade($course, $certificate->printdate, $USER->id)) {
            $date = $modinfo->dategraded;
        }
    }

    return $date;
}

/**
 * Prepare to print the course grade.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $course
 * @return mixed
 */
function certificateuv_print_course_grade($course){
    debugging('certificate_print_course_grade is deprecated, please use certificate_get_grade instead. Ideally
               you should be using certificate_get_grade in your certificate type which will either get the course
               or module grade depending on your certificate settings.', DEBUG_DEVELOPER);

    global $USER, $DB;

    if ($course_item = grade_item::fetch_course_item($course->id)) {
        $grade = new grade_grade(array('itemid'=>$course_item->id, 'userid'=>$USER->id));
        $course_item->gradetype = GRADE_TYPE_VALUE;
        $coursegrade = new stdClass;
        $coursegrade->points = grade_format_gradevalue($grade->finalgrade, $course_item, true, GRADE_DISPLAY_TYPE_REAL, $decimals=2);
        $coursegrade->percentage = grade_format_gradevalue($grade->finalgrade, $course_item, true, GRADE_DISPLAY_TYPE_PERCENTAGE, $decimals=2);
        $coursegrade->letter = grade_format_gradevalue($grade->finalgrade, $course_item, true, GRADE_DISPLAY_TYPE_LETTER, $decimals=0);

        return $coursegrade;
    }

    return false;
}

/**
 * Prepare to print an activity grade.
 *
 * @deprecated since certificateuv version 2012052501
 * @param stdClass $course
 * @param int $moduleid
 * @return mixed
 */
function certificateuv_print_mod_grade($course, $moduleid){
    debugging('certificate_print_mod_grade is deprecated, please use certificate_get_mod_grade instead. Ideally
               you should be using certificate_get_grade in your certificate type which will either get the course
               or module grade depending on your certificate settings.', DEBUG_DEVELOPER);

    global $USER;

    return certificateuv_get_mod_grade($course, $moduleid, $USER->id);
}


/**
* Prepare to print an outcome.
*
* @deprecated since certificateuv version 2012052501
* @param stdClass $course
* @param int $moduleid
* @return mixed
*/
function certificateuv_print_outcome($course, $id) {
    debugging('certificate_print_outcome is deprecated, please use certificate_get_outcome instead', DEBUG_DEVELOPER);

    return certificateuv_get_outcome($certificate, $course);
}

/**
 * Creates rectangles for line border for A4 size paper.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $pdf
 * @param stdClass $certificate
 * @return null
 */
function draw_frame($pdf, $certificate) {
    debugging('draw_frame is deprecated, please use certificate_draw_frame instead', DEBUG_DEVELOPER);

    certificateuv_draw_frame($pdf, $certificate);
}

/**
 * Creates rectangles for line border for letter size paper.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $pdf
 * @param stdClass $certificate
 * @return null
 */
function draw_frame_letter($pdf, $certificate) {
    debugging('draw_frame_letter is deprecated, please use certificate_draw_frame_letter instead', DEBUG_DEVELOPER);

    certificateuv_draw_frame_letter($pdf, $certificate);
}

/**
 * Prints border images from the borders folder in PNG or JPG formats.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $pdf;
 * @param stdClass $certificate
 * @param int $x x position
 * @param int $y y position
 * @param int $w the width
 * @param int $h the height
 * @return null
 */
function print_border($pdf, $certificate, $x, $y, $w, $h) {
    debugging('print_watermark is deprecated, please use certificate_print_image instead', DEBUG_DEVELOPER);

    certificateuv_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $x, $y, $w, $h);
}

/**
 * Prints watermark images.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $pdf;
 * @param stdClass $certificate
 * @param int $x x position
 * @param int $y y position
 * @param int $w the width
 * @param int $h the height
 * @return null
 */
function print_watermark($pdf, $certificate, $x, $y, $w, $h) {
    debugging('print_watermark is deprecated, please use certificate_print_image instead', DEBUG_DEVELOPER);

    certificateuv_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $x, $y, $w, $h);
}

/**
 * Prints signature images or a line.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $pdf
 * @param stdClass $certificate
 * @param int $x x position
 * @param int $y y position
 * @param int $w the width
 * @param int $h the height
 * @return null
 */
function print_signature($pdf, $certificate, $x, $y, $w, $h) {
    debugging('print_signature is deprecated, please use certificate_print_image instead', DEBUG_DEVELOPER);

    certificateuv_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $x, $y, $w, $h);
}

/**
 * Prints seal images.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $pdf;
 * @param stdClass $certificate
 * @param int $x x position
 * @param int $y y position
 * @param int $w the width
 * @param int $h the height
 * @return null
 */
function print_seal($pdf, $certificate, $x, $y, $w, $h) {
    debugging('print_seal is deprecated, please use certificate_print_image instead', DEBUG_DEVELOPER);

    certificateuv_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $x, $y, $w, $h);
}

/**
 * Sends text to output given the following params.
 *
 * @deprecated since certificate version 2012052501
 * @param stdClass $pdf
 * @param int $x horizontal position
 * @param int $y vertical position
 * @param char $align L=left, C=center, R=right
 * @param string $font any available font in font directory
 * @param char $style ''=normal, B=bold, I=italic, U=underline
 * @param int $size font size in points
 * @param string $text the text to print
 * @return null
 */
function cert_printtext($pdf, $x, $y, $align, $font, $style, $size, $text) {
    static $hasbeenwarned = false;

    if (!$hasbeenwarned) {
        debugging('cert_printtext is deprecated, please use certificate_print_text instead', DEBUG_DEVELOPER);
    }

    $hasbeenwarned = true;
    certificateuv_print_text($pdf, $x, $y, $align, $font, $style, $size, $text);
}

