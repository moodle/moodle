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
 * View page for WDS Post Grades block.
 *
 * @package    block_wds_postgrades
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/wds_postgrades/classes/wdspg.php');

// Get parameters.
$courseid = required_param('courseid', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$gradetype = required_param('gradetype', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_ALPHA);

// Get course.
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

// Set the table.
$stable = 'enrol_wds_sections';
$ctable = 'enrol_wds_courses';

// Build out the section parms.
$sparms = ['id' => $sectionid, 'moodle_status' => $courseid];

// Get section details.
$section = $DB->get_record($stable, $sparms, '*', MUST_EXIST);

// Build out the course parms.
$cparms = ['course_listing_id' => $section->course_listing_id];

// Get course details.
$wdscourse = $DB->get_record($ctable, $cparms, '*', MUST_EXIST);

$sectiontitle = $section->course_subject_abbreviation .
    ' ' .
    $wdscourse->course_number .
    ' ' .
    $section->section_number;

// Build out the typeword for the lang string.
if ($gradetype == 'interim') {
    $typeword = 'Interim';
} else {
    $typeword = 'Final';
}

// We need this.
$section->gradetype = $typeword;

$stringvar = [
    'coursename' => $course->fullname,
    'sectiontitle' => $sectiontitle,
    'typeword' => $typeword
];

// Setup page.
$PAGE->set_url(new moodle_url('/blocks/wds_postgrades/view.php',
    ['courseid' => $courseid, 'sectionid' => $sectionid, 'gradetype' => $gradetype]));
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');

// Set appropriate title for a specific section.
$PAGE->set_title(get_string('viewgradesfor', 'block_wds_postgrades', $stringvar));

$PAGE->navbar->add(get_string('pluginname', 'block_wds_postgrades'));
$PAGE->navbar->add($sectiontitle);

// Check permissions.
require_login($course);
require_capability('block/wds_postgrades:view', $PAGE->context);

// Get enrolled students data - filtered by section if section ID is provided.
$enrolledstudents = \block_wds_postgrades\wdspg::get_enrolled_students($courseid, $sectionid);

// Process form submission if the post grades action is triggered.
if ($action === 'postgrades' && confirm_sesskey()) {

    // Check if user has capability to post grades.
    require_capability('block/wds_postgrades:post', $PAGE->context);

    // Array to store the grade objects.
    $grades = array();

    // Properly set the section listing id.
    $sectionlistingid = $section->section_listing_id;

    // Handle selective posting (for final grades).
    $studentstopost = optional_param_array('students_to_post', [], PARAM_TEXT);

    // Get the raw student_data array and process it manually since it's a nested array.
    $rawstudentdata = [];
    if (isset($_POST['student_data']) && is_array($_POST['student_data'])) {
        foreach ($_POST['student_data'] as $universalid => $fields) {
            if (is_array($fields)) {
                $cleanfields = [];
                foreach ($fields as $fieldname => $fieldvalue) {

                    // Clean each field individually.
                    $cleanfields[clean_param($fieldname, PARAM_TEXT)] = clean_param($fieldvalue, PARAM_TEXT);
                }
                $rawstudentdata[clean_param($universalid, PARAM_TEXT)] = $cleanfields;
            }
        }
    }
    $studentdata = $rawstudentdata;

    // Get last attendance dates from form.
    $lastattendancedates = optional_param_array('last_attendance_date', [], PARAM_TEXT);

    // Process each student's grade.
    foreach ($enrolledstudents as $student) {

        // For final grades, only process students that haven't been posted yet.
        if ($gradetype === 'final' && empty($studentstopost)) {

            // No students to post, form was likely submitted without any available students.
            continue;
        } else if ($gradetype === 'final' && !in_array($student->universal_id, $studentstopost)) {

            // Skip students not in the post list (already posted or invalid grades).
            continue;
        }

        // Get the student's formatted grade.
        $finalgrade = \block_wds_postgrades\wdspg::get_formatted_grade(
            $student->coursegradeitem,
            $student->userid,
            $courseid
        );

        // Get the grade code.
        $gradecode = \block_wds_postgrades\wdspg::get_graded_wds_gradecode($student, $finalgrade);

        // Create grade object.
        $gradeobj = new stdClass();
        $gradeobj->section_listing_id = $student->section_listing_id;
        $gradeobj->universal_id = $student->universal_id;
        $gradeobj->student_fullname = $student->firstname . ' ' . $student->lastname;
        $gradeobj->grade_id = $gradecode->grade_id;
        $gradeobj->grade_display = $gradecode->grade_display;
        $gradeobj->userid = $student->userid;

        // If we're required to post a note.
        if (isset($gradecode->grade_note_required) && $gradecode->grade_note_required == "1") {

            // Set this so we can use isset later.
            $gradeobj->grade_note_required = $gradecode->grade_note_required;
        }

        // If this is a failing grade or the grade type explicitly requires last attendance.
        if (isset($gradecode->requires_last_attendance) &&
            $gradecode->requires_last_attendance == "1" &&
            $gradetype == "final"
        ) {

            // Check if a manual date was provided in the form.
            if (isset($lastattendancedates[$student->universal_id]) && !empty($lastattendancedates[$student->universal_id])) {

                // Convert date string to timestamp.
                $dateobj = \DateTime::createFromFormat('Y-m-d', $lastattendancedates[$student->universal_id]);
                if ($dateobj !== false) {

                    // Use the manually entered date.
                    $gradeobj->last_attendance_date = $dateobj->getTimestamp();
                    $gradeobj->wdladate = $lastattendancedates[$student->universal_id];
                } else {

                    // If invalid date provided, fallback to last course access.
                    $gradeobj->last_attendance_date = \block_wds_postgrades\wdspg::get_wds_sla(
                        $student->userid, $courseid
                    );
                    $gradeobj->wdladate = date('Y-m-d', $gradeobj->last_attendance_date);
                }
            } else {

                // If no date provided, use the last course access date.
                $gradeobj->last_attendance_date = \block_wds_postgrades\wdspg::get_wds_sla(
                    $student->userid, $courseid
                );
                $gradeobj->wdladate = date('Y-m-d', $gradeobj->last_attendance_date);
            }
        }

        // Add to grades array.
        $grades[] = $gradeobj;
    }

    // For final grades, use extended method to track succesful postings.
    if ($gradetype === 'final') {
        $resultdata = \block_wds_postgrades\wdspg::post_grades_with_method_extended(
            $grades, $gradetype, $sectionlistingid, $courseid, $sectionid);
    } else {
        $resultdata = \block_wds_postgrades\wdspg::post_grades_with_method(
            $grades, $gradetype, $sectionlistingid);
    }

    // Create results URL with appropriate parameters.
    $resultsurl = new moodle_url(
        '/blocks/wds_postgrades/results.php',
        ['courseid' => $courseid, 'sectionid' => $section->id, 'gradetype' => $gradetype]);

    // Add section title for context in results page.
    $resultsurl->param('sectiontitle', $sectiontitle);
    $resultsurl->param('typeword', $typeword);

    // Determine overall result type.
    if (empty($resultdata->failures) && !empty($resultdata->successes)) {
        $resultsurl->param('resulttype', 'success');
    } else if (!empty($resultdata->failures) && !empty($resultdata->successes)) {
        $resultsurl->param('resulttype', 'partial');
    } else {
        $resultsurl->param('resulttype', 'error');
    }

    // Add section listing ID for reference.
    $resultsurl->param('sectionlistingid', $sectionlistingid);

    // Store result data in session for the results page.
    $SESSION->wds_postgrades_results = $resultdata;

    // Redirect to the results page.
    redirect($resultsurl);
}

// Start output.
echo $OUTPUT->header();

// Modal for loading spinner.
echo '<div id="loadingModal" class="modal" style="display:none;">';
echo '  <div class="modal-content">';
echo '    <h4>Posting Grades</h4>';
echo '    <p>Please wait while grades are being posted...</p>';
echo '    <div class="spinner"></div>';
echo '  </div>';
echo '</div>';

echo $OUTPUT->heading(get_string('gradesfor', 'block_wds_postgrades', $stringvar));

// Check if grades posting is allowed for this period.
$isopen = \block_wds_postgrades\period_settings::is_grading_open($section);
$openstatus = \block_wds_postgrades\period_settings::get_grading_status($section);

// Display status message about grades availability.
echo $OUTPUT->notification($openstatus, $isopen ? 'info' : 'warning');

// Only show the form if grades are available for posting.
if ($isopen) {

    // Check if all final grades have already been posted.
    $allposted = false;
    if ($gradetype === 'final') {
        $allposted = \block_wds_postgrades\wdspg::all_final_grades_posted($sectionid, $enrolledstudents, $courseid);
    }

    if ($gradetype === 'final' && $allposted) {

        // All final grades have been posted, show a success message.
        echo $OUTPUT->notification(get_string('allgradesposted', 'block_wds_postgrades'), 'success');
    } else {

        // Add jQuery date picker inclusion (only for final grades).
        if ($gradetype === 'final') {
            $PAGE->requires->js_init_code('
                require(["jquery"], function($) {
                    $(".attendance-date-picker").attr("type", "date");
                });
            ');

            // JavaScript for loading modal.
            $PAGE->requires->js_init_code('
                require(["jquery"], function($) {
                    $(document).ready(function() {
                        const loadingModal = $("#loadingModal");

                        // Function to show the modal
                        window.showLoadingModal = function() {
                            if (loadingModal.length) {
                                loadingModal.show();
                            }
                        };

                        // Function to hide the modal
                        window.hideLoadingModal = function() {
                            if (loadingModal.length) {
                                loadingModal.hide();
                            }
                        };

                        // Hide modal on page load, just in case.
                        // It is initially hidden by CSS, but this is an extra precaution.
                        if (loadingModal.length) {
                             hideLoadingModal();
                        }
                    });
                });
            ');
        } else {

            // JavaScript for loading modal.
            $PAGE->requires->js_init_code('
                require(["jquery"], function($) {
                    $(document).ready(function() {
                        const loadingModal = $("#loadingModal");

                        // Function to show the modal
                        window.showLoadingModal = function() {
                            if (loadingModal.length) {
                                loadingModal.show();
                            }
                        };

                        // Function to hide the modal
                        window.hideLoadingModal = function() {
                            if (loadingModal.length) {
                                loadingModal.hide();
                            }
                        };

                        // Hide modal on page load, just in case.
                        // It is initially hidden by CSS, but this is an extra precaution.
                        if (loadingModal.length) {
                             hideLoadingModal();
                        }
                    });
                });
            ');
        }

        // Start form.
        $formaction = new moodle_url('/blocks/wds_postgrades/view.php');
        echo html_writer::start_tag('form', ['method' => 'post', 'action' => $formaction]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'courseid', 'value' => $courseid]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sectionid', 'value' => $sectionid]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'gradetype', 'value' => $gradetype]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'postgrades']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

        if ($gradetype === 'final') {

            // Generate and display the table for final grades with date pickers.
            generateFinalGradesTableWithDatePickers($enrolledstudents, $courseid, $sectionid);
        } else {

            // For interim grades, use the original method.
            $tablehtml = \block_wds_postgrades\wdspg::generate_grades_table($enrolledstudents, $courseid);
            echo $tablehtml;
        }

        // Add a container for buttons.
        echo html_writer::start_div('buttons');

        // Post Grades button (only visible if user has the capability to post grades).
        if (has_capability('block/wds_postgrades:post', $PAGE->context) && !empty($enrolledstudents)) {

            // For final grades, check if there are any available to post.
            $canpost = true;
            if ($gradetype === 'final') {
                $result = calculatePostableGrades($enrolledstudents, $courseid, $sectionid);
                $canpost = ($result['available'] > 0);

                // Display remaining grades message.
                if ($result['available'] > 0) {
                    echo $OUTPUT->notification(
                        get_string('remaininggrades', 'block_wds_postgrades', $result['available']),
                        'info'
                    );
                } else if ($result['posted'] > 0 && $result['available'] == 0) {

                    // All grades posted.
                    echo $OUTPUT->notification(
                        get_string('allgradesposted', 'block_wds_postgrades'),
                        'success'
                    );
                } else if ($result['posted'] == 0 && $result['available'] == 0) {

                    // No grades to post.
                    echo $OUTPUT->notification(
                        get_string('nopostablegrades', 'block_wds_postgrades'),
                        'warning'
                    );
                }
            }

            if ($canpost) {
                echo html_writer::tag('button', get_string('postgrades', 'block_wds_postgrades'),
                    ['type' => 'submit', 'class' => 'btn btn-primary', 'onclick' => 'showLoadingModal();']);
                echo ' ';
            }
        }

        // End the form.
        echo html_writer::end_tag('form');
    }
} else {
    echo $OUTPUT->notification(get_string('gradesnotavailable', 'block_wds_postgrades', $typeword), 'error');
}

// Back to course button (outside the form).
$courseurl = new moodle_url('/course/view.php', ['id' => $courseid]);
echo $OUTPUT->single_button($courseurl, get_string('backtocourse', 'block_wds_postgrades'), 'get');

echo html_writer::end_div();

// Complete output.
echo $OUTPUT->footer();

/**
 * Helper function to generate table for final grades with date pickers for failing students
 *
 * @param array $enrolledstudents Array of enrolled students.
 * @param int $courseid The course ID.
 * @param int $sectionid The section ID.
 */
function generateFinalGradesTableWithDatePickers($enrolledstudents, $courseid, $sectionid) {
    global $OUTPUT, $DB;

    if (empty($enrolledstudents)) {
        echo get_string('nostudents', 'block_wds_postgrades');
        return;
    }

    $table = new html_table();
    $table->attributes['class'] = 'wdspgrades generaltable';
    $table->head = [
        get_string('firstname', 'block_wds_postgrades'),
        get_string('lastname', 'block_wds_postgrades'),
        get_string('universalid', 'block_wds_postgrades'),
        get_string('gradingbasis', 'block_wds_postgrades'),
        get_string('letter', 'block_wds_postgrades'),
        get_string('grade', 'block_wds_postgrades'),
        get_string('status', 'block_wds_postgrades')
    ];

    // Add attendance date column (only for final grades).
    $table->head[] = 'Last Attendance Date';

    // Get course grade item from first student.
    $firststudent = reset($enrolledstudents);
    $coursegradeitemid = $firststudent->coursegradeitem;

    // Check if we have a valid grade item.
    $gradeitem = \block_wds_postgrades\wdspg::get_course_grade_item($coursegradeitemid);
    if ($gradeitem === false) {
        echo get_string('nocoursegrade', 'block_wds_postgrades');
        return;
    }

    // Get all previously posted grades.
    $postedgrades = [];
    $postedgradelookup = [];
    $postedgrades = \block_wds_postgrades\wdspg::get_posted_grades($sectionid);

    // Convert to lookup by universal_id.
    foreach ($postedgrades as $pg) {
        $postedgradelookup[$pg->universal_id] = $pg;
    }
    $postedgrades = $postedgradelookup;

    // Build the table rows.
    $stats = [
        'total' => count($enrolledstudents),
        'posted' => 0,
        'available' => 0
    ];

    foreach ($enrolledstudents as $student) {
        // Get formatted grade.
        $finalgrade = \block_wds_postgrades\wdspg::get_formatted_grade($student->coursegradeitem, $student->userid, $courseid);

        // Get grade code.
        $gradecode = \block_wds_postgrades\wdspg::get_graded_wds_gradecode($student, $finalgrade);

        // Skip invalid grades.
        if (!$gradecode) {
            continue;
        } else if ($gradecode->grade_display == 'No Grade') {
            continue;
        }

        // Check if this is a failing grade.
        $isfailinggrade = (in_array($gradecode->grade_display, ['F', 'Fail', 'No Credit (HNR)']) ||
                          substr($gradecode->grade_id, -1) === 'F');

        // Count valid grades.
        $stats['available']++;

        // Create table row.
        $tablerow = [
            $student->firstname,
            $student->lastname,
            $student->universal_id,
            $student->grading_basis,
            $finalgrade->letter,
            $gradecode->grade_display
        ];

        // Status column.
        $status = 'Not posted';

        // Check if already posted.
        if (isset($postedgrades[$student->universal_id])) {
            $postedgrade = $postedgrades[$student->universal_id];
            $stats['posted']++;
            $stats['available']--;

            // Format the date.
            $postdate = userdate($postedgrade->timecreated, get_string('strftimedatetime', 'core_langconfig'));
            $poster = $postedgrade->poster_firstname . ' ' . $postedgrade->poster_lastname;

            // Create status with icon.
            $status = $OUTPUT->pix_icon('i/checkedcircle',
                get_string('alreadyposted', 'block_wds_postgrades'),
                'moodle',
                ['class' => 'text-success']);

            $status .= ' ' . get_string('alreadyposted', 'block_wds_postgrades');
            $status .= html_writer::tag('div',
                get_string('dateposted', 'block_wds_postgrades', $postdate),
                ['class' => 'small text-muted']);

            $status .= html_writer::tag('div',
                get_string('postedby', 'block_wds_postgrades', $poster),
                ['class' => 'small text-muted']);

            // Add hidden field to exclude this grade.
            $status .= html_writer::empty_tag('input', [
                'type' => 'hidden',
                'name' => 'already_posted[]',
                'value' => $student->universal_id
            ]);
        } else {
            // Not posted yet, include for posting.
            $status = html_writer::empty_tag('input', [
                'type' => 'hidden',
                'name' => 'students_to_post[]',
                'value' => $student->universal_id
            ]);

            // Prepare student data for posting.
            foreach (['userid', 'section_listing_id', 'grading_scheme', 'grading_basis'] as $field) {
                if (isset($student->$field)) {
                    $status .= html_writer::empty_tag('input', [
                        'type' => 'hidden',
                        'name' => "student_data[{$student->universal_id}][{$field}]",
                        'value' => $student->$field
                    ]);
                }
            }
        }

        $tablerow[] = $status;

        // Last Attendance Date column.
        $attendancedatefield = '';

        // Only show date picker for unposted failing grades.
        if (!isset($postedgrades[$student->universal_id])) {
            if ($isfailinggrade) {

                // Get student period start date.
                $sps = $student->periodstart - (86400 * 10);

                // Get default last access date.
                $lastaccess = \block_wds_postgrades\wdspg::get_wds_sla($student->userid, $courseid);

                // If we don't have any last access dates, it returns false, so check for that.
                if ($lastaccess) {

                   // Set this to the last access date.
                   $lata = (int)$lastaccess->timeaccess;
                } else {

                   // Set this to 0.
                   $lata = 0;
                }

                // Build out the default date for the picker.
                $defaultdate = ($lata > 0) ?
                    date('Y-m-d', $lata) :
                    date('Y-m-d', $sps);

                // Create date picker.
                $attendancedatefield = html_writer::empty_tag('input', [
                    'type' => 'text',
                    'class' => 'attendance-date-picker',
                    'name' => "last_attendance_date[{$student->universal_id}]",
                    'value' => $defaultdate,
                    'required' => 'required'
                ]);

                // Add explanation label.
                $attendancedatefield .= html_writer::tag('div',
                    'Required for this grade value',
                    ['class' => 'small text-danger']);
            } else {
                $attendancedatefield = get_string('lastattendancedatenotapplicable', 'block_wds_postgrades');
            }
        } else {

            // For already posted grades, show the date if it exists.
            if (isset($postedgrade->last_attendance_date)) {
                $attendancedatefield = userdate($postedgrade->last_attendance_date, get_string('strfdateshortmonth', 'langconfig'));
            } else {
                $attendancedatefield = get_string('lastattendancedatenotapplicable', 'block_wds_postgrades');
            }
        }

        $tablerow[] = $attendancedatefield;
        $table->data[] = $tablerow;
    }

    // Output the table if we have any data.
    if (!empty($table->data)) {
        echo html_writer::table($table);
    } else {
        echo get_string('nostudents', 'block_wds_postgrades');
    }
}

/**
 * Helper function to calculate statistics for postable grades
 *
 * @param array $enrolledstudents Array of enrolled students.
 * @param int $courseid The course ID.
 * @param int $sectionid The section ID.
 * @return array Statistics about postable grades
 */
function calculatePostableGrades($enrolledstudents, $courseid, $sectionid) {
    global $DB;

    $stats = [
        'total' => count($enrolledstudents),
        'posted' => 0,
        'available' => 0
    ];

    // Get all posted grades for this section.
    $posted = \block_wds_postgrades\wdspg::get_posted_grades($sectionid);
    $postedids = [];

    foreach ($posted as $p) {
        $postedids[$p->universal_id] = true;
    }

    // Check each student.
    foreach ($enrolledstudents as $student) {
        // Get the formatted grade
        $finalgrade = \block_wds_postgrades\wdspg::get_formatted_grade($student->coursegradeitem, $student->userid, $courseid);

        // Get the grade code.
        $gradecode = \block_wds_postgrades\wdspg::get_graded_wds_gradecode($student, $finalgrade);

        // Skip invalid grades.
        if (!$gradecode || $gradecode->grade_display == 'No Grade') {
            continue;
        }

        // Count valid grades.
        if (isset($postedids[$student->universal_id])) {
            $stats['posted']++;
        } else {
            $stats['available']++;
        }
    }

    return $stats;
}
