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
 * Strings for component 'tool_checklearningrecords', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    tool
 * @subpackage checklearningrecords
 * @copyright  2020 E-Learn Design https://www.e-learndesign
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/local/iomad_track/db/install.php');
require_once($CFG->dirroot.'/admin/tool/checklearningrecords/lib.php');

admin_externalpage_setup('toolchecklearningrecords');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_checklearningrecords'));

// Get the incomplete license records
$brokenlicenses = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                        WHERE
                                        (licenseid > 0
                                         AND licenseallocated IS NULL)
                                        OR
                                        (licenseid = 0
                                         AND licenseallocated > 0
                                         AND licensename != 'HISTORIC')");

// Get the incomplete completion records
$brokencompletions = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                           WHERE
                                           (timecompleted > 0
                                            AND timestarted IS NULL)
                                           OR
                                           (timecompleted > 0
                                            AND timeenrolled IS NULL)
                                           OR
                                           (timestarted > 0
                                            AND timeenrolled IS NULL)");

// Get the incomplete completion records
$missingcompletions = $DB->get_records_sql("SELECT lit.*,cc.id as ccid,cc.timeenrolled AS cctimeenrolled, cc.timestarted AS cctimestarted, cc.timecompleted AS cctimecompleted
                                            FROM {local_iomad_track} lit
                                            JOIN {course_completions} cc
                                            ON (lit.userid = cc.userid AND lit.courseid = cc.course)
                                            WHERE
                                            cc.timecompleted > 0
                                            AND lit.timecompleted IS NULL
                                            AND lit.timestarted > 0");

$form = new tool_checklearningrecords_form($PAGE->url, count($brokenlicenses), count($brokencompletions), count($missingcompletions));

if (!$data = $form->get_data()) {
    $form->display();
    echo $OUTPUT->footer();
    die();
}

// Scroll to the end when finished.
$PAGE->requires->js_init_code("window.scrollTo(0, 5000000);");
if (!empty($brokencompletions)) {
    echo $OUTPUT->box_start();
    do_fixbrokencompletions($brokencompletions);
    echo $OUTPUT->box_end();
}

if (!empty($brokenlicenses)) {
    // Get the incomplete completion records
    $brokenlicenses = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                            WHERE
                                            (licenseid > 0
                                             AND licenseallocated IS NULL)
                                            OR
                                            (licenseid = 0
                                             AND licenseallocated > 0
                                             AND licensename != 'HISTORIC')");
        echo $OUTPUT->box_start();
        do_fixbrokenlicenses($brokenlicenses);
        echo $OUTPUT->box_end();
}

if (!empty($missingcompletions)) {
    // Get the incomplete completion records
    $missingcompletions = $DB->get_records_sql("SELECT lit.*,cc.id as ccid,cc.timeenrolled AS cctimeenrolled, cc.timestarted AS cctimestarted, cc.timecompleted AS cctimecompleted
                                                FROM {local_iomad_track} lit
                                                JOIN {course_completions} cc
                                                ON (lit.userid = cc.userid AND lit.courseid = cc.course)
                                                WHERE
                                                cc.timecompleted > 0
                                                AND lit.timecompleted IS NULL
                                                AND lit.timestarted > 0");

    echo $OUTPUT->box_start();
    do_fixmissingcompletions($missingcompletions);
    echo $OUTPUT->box_end();
}

// Sort out all expiry.
// Calculate the timeexpired for all users.
// Get the courses where there is a expired value.
$expirycourses = $DB->get_records_sql("SELECT courseid,validlength FROM {iomad_courses}
                                       WHERE validlength > 0");
foreach ($expirycourses as $expirycourse) {
    $offset = $expirycourse->validlength * 24 * 60 * 60;
    $DB->execute("UPDATE {local_iomad_track}
                  SET timeexpires = timecompleted + :offset
                  WHERE courseid = :courseid
                  AND timecompleted > 0",
                  array('courseid' => $expirycourse->courseid,
                        'offset' => $offset));
}


// Course caches are now rebuilt on the fly.

echo $OUTPUT->continue_button(new moodle_url('/admin/index.php'));

echo $OUTPUT->footer();
