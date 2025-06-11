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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// We need the config.
require_once('../../config.php');

// Require login.
require_login();

// Globals.
global $DB, $USER, $OUTPUT, $PAGE;

// Set up the page.
$context = context_system::instance();
$PAGE->set_context($context);

// Build the url.
$PAGE->set_url(new moodle_url('/blocks/wdsprefs/scheduleview.php'));

// Set title and heading.
$PAGE->set_title(get_string('wdsprefs:scheduleview', 'block_wdsprefs'));
$PAGE->set_heading(get_string('wdsprefs:courseschedule', 'block_wdsprefs'));

// Get current user's ID for later.
$userid = $USER->id;

// SQL query to fetch schedule details.
$sql = "SELECT
    COALESCE(c.fullname,
        CONCAT(per.period_year, ' ',
            per.period_type, ' ',
            cou.course_subject_abbreviation, ' ',
            cou.course_number, ' (', sec.class_type, ')'
        )
    ) AS course,
    per.academic_period_id,
    sec.section_number AS section,
    IF(sec.controls_grading=0, 'Course not taught in Moodle',
        IF(sec.idnumber IS NULL, 'Not created yet',
            IF(c.visible=0, 'Hidden', c.id)
        )
    ) AS moodlecourse,
    count(secm.start_time) as timecount,
    COALESCE(
        CONCAT(
            COALESCE(tea.preferred_firstname, tea.firstname),
            ' ',
            COALESCE(tea.preferred_lastname, tea.lastname)
        ),
        'None assigned yet'
    ) AS instructor,
    COALESCE(
        GROUP_CONCAT(secm.short_day ORDER BY secm.day ASC SEPARATOR '<br>'),
        'Not provided'
    ) AS days,
    COALESCE(
        GROUP_CONCAT(CONCAT(secm.start_time, ' - ', secm.end_time) ORDER BY secm.day ASC SEPARATOR '<br>'),
        'Not provided'
    ) AS times,
    sec.wd_status AS workdaystatus,
    sec.delivery_mode AS delivery
    FROM {user} u
        INNER JOIN {enrol_wds_students} stu
            ON stu.userid = u.id
        INNER JOIN {enrol_wds_student_enroll} stuenr
            ON stuenr.universal_id = stu.universal_id
        INNER JOIN {enrol_wds_sections} sec
            ON sec.section_listing_id = stuenr.section_listing_id
        INNER JOIN {enrol_wds_courses} cou
            ON cou.course_listing_id = sec.course_listing_id
        INNER JOIN {enrol_wds_periods} per
            ON per.academic_period_id = sec.academic_period_id
        LEFT JOIN {enrol_wds_teacher_enroll} tenr
            ON sec.section_listing_id = tenr.section_listing_id
            AND tenr.role = 'Primary'
        LEFT JOIN {enrol_wds_teachers} tea
            ON tea.universal_id = tenr.universal_id
        LEFT JOIN {course} c
            ON c.idnumber = sec.idnumber
            AND sec.idnumber IS NOT NULL
            AND c.idnumber != ''
        LEFT JOIN {enrol_wds_section_meta} secm
            ON secm.section_listing_id = sec.section_listing_id
    WHERE per.start_date < UNIX_TIMESTAMP() + (60 * 86400)
#        AND per.end_date > UNIX_TIMESTAMP()
        AND u.id = :userid
    GROUP BY sec.id, stuenr.id
    ORDER BY per.start_date DESC,
         cou.course_subject_abbreviation ASC,
         cou.course_number ASC,
         sec.section_number ASC";

// Some parms.
$params = ['userid' => $userid];

// Do the nasty.
$records = $DB->get_records_sql($sql, $params);

// Output page header.
echo $OUTPUT->header();

// Make sure something is here.
if (empty($records)) {
    echo $OUTPUT->notification(get_string('wdsprefs:nocourses', 'block_wdsprefs'));

// We have a schedule. group and deal with it.
} else {
    // Group records by academic_period_id.
    $grouped = [];

    // loop through records and group them.
    foreach ($records as $rec) {
        $grouped[$rec->academic_period_id][] = $rec;
    }

    // Loop through the groups.
    foreach ($grouped as $periodid => $periodrecords) {

        // Format the heading: remove underscores.
        $prettyperiod = str_replace('_', ' ', $periodid);

        // Display the period heading.
        echo html_writer::tag('h3', "{$prettyperiod}");

        // Build the table.
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->head = [
            get_string('wdsprefs:courseheading','block_wdsprefs'),
            get_string('wdsprefs:sectionheading','block_wdsprefs'),
            get_string('wdsprefs:statusheading','block_wdsprefs'),
            get_string('wdsprefs:instructorheading','block_wdsprefs'),
            get_string('wdsprefs:daysheading','block_wdsprefs'),
            get_string('wdsprefs:timesheading','block_wdsprefs'),
            get_string('wdsprefs:wdstatusheading','block_wdsprefs'),
            get_string('wdsprefs:deliverymodeheading','block_wdsprefs')
        ];

        // Define the correct order of the days of the week.
        $dayorder = ['M', 'Tu', 'W', 'Th', 'F', 'Sa', 'Su'];

        // Loop through the records to build the table data.
        foreach ($periodrecords as $records) {

            // We'll use this in case we don't have a course id.
            $courselink = $records->moodlecourse;

            // Simple way to check if we have a course id.
            if (is_numeric($courselink)) {

                // Build the url.
                $url = new moodle_url('/course/view.php', ['id' => $courselink]);

                // Overwrite courselink with the link.
                $courselink = html_writer::link(
                    $url,
                    get_string('wdsprefs:courselink', 'block_wdsprefs')
                );
            }

            // Convert days to array.
            $daysarray = explode('<br>', $records->days);

            // Convert times to array.
            $timesarray = explode('<br>', $records->times);

            // Sort the days based on the correct order with a fancy anonymous function.
            usort($daysarray, function($a, $b) use ($dayorder) {
                return array_search($a, $dayorder) - array_search($b, $dayorder);
            });

            // Handle the case where the times are identical across all days.
            if (count(array_unique($timesarray)) == 1) {

                // If all times are the same, show the time once and then list the days.
                $timesdisplay = $timesarray[0];
                $daysdisplay = implode(', ', $daysarray);
            } else {

                // Otherwise, use the original values (one time per day).
                $timesdisplay = format_text($records->times, FORMAT_HTML);
                $daysdisplay = format_text($records->days, FORMAT_HTML);
            }

            // Build the table.
            $table->data[] = [
                s($records->course),
                s($records->section),
                $courselink,
                s($records->instructor),

                // Show the cleaned-up days.
                $daysdisplay,

                // Show the cleaned-up times.
                $timesdisplay,
                s($records->workdaystatus),
                s($records->delivery),
            ];
        }

        // Output the table.
        echo html_writer::table($table);
    }
}

// Output the footer.
echo $OUTPUT->footer();
