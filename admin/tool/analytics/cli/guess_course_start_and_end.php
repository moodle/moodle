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
 * Guesses course start and end dates based on activity logs.
 *
 * @package    tool_analytics
 * @copyright  2016 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/format/weeks/lib.php');

$help = "Guesses course start and end dates based on activity logs.

IMPORTANT: Don't use this script if you keep previous academic years users enrolled in courses. Guesses would not be accurate.

Options:
--guessstart           Guess the course start date (default to true)
--guessend             Guess the course end date (default to true)
--guessall             Guess all start and end dates, even if they are already set (default to false)
--update               Update the db or just notify the guess (default to false)
--filter               Analyser dependant. e.g. A courseid would evaluate the model using a single course (Optional)
-h, --help             Print out this help

Example:
\$ php admin/tool/analytics/cli/guess_course_start_and_end_dates.php --update=1 --filter=123,321
";

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help'        => false,
        'guessstart'  => true,
        'guessend'    => true,
        'guessall'    => false,
        'update'      => false,
        'filter'      => false
    ),
    array(
        'h' => 'help',
    )
);

if ($options['help']) {
    echo $help;
    exit(0);
}

if ($options['guessstart'] === false && $options['guessend'] === false && $options['guessall'] === false) {
    echo $help;
    exit(0);
}

// Reformat them as an array.
if ($options['filter'] !== false) {
    $options['filter'] = explode(',', clean_param($options['filter'], PARAM_SEQUENCE));
}

// We need admin permissions.
\core\session\manager::set_user(get_admin());

$conditions = array('id != 1');
if (!$options['guessall']) {
    if ($options['guessstart']) {
        $conditions[] = '(startdate is null or startdate = 0)';
    }
    if ($options['guessend']) {
        $conditions[] = '(enddate is null or enddate = 0)';
    }
}

$coursessql = '';
$params = null;
if ($options['filter']) {
    list($coursessql, $params) = $DB->get_in_or_equal($options['filter'], SQL_PARAMS_NAMED);
    $conditions[] = 'id ' . $coursessql;
}

$courses = $DB->get_recordset_select('course', implode(' AND ', $conditions), $params, 'sortorder ASC');
foreach ($courses as $course) {
    tool_analytics_calculate_course_dates($course, $options);
}
$courses->close();


/**
 * tool_analytics_calculate_course_dates
 *
 * @param stdClass $course
 * @param array $options CLI options
 * @return void
 */
function tool_analytics_calculate_course_dates($course, $options) {
    global $DB, $OUTPUT;

    $courseman = new \core_analytics\course($course);

    $notification = $course->shortname . ' (id = ' . $course->id . '): ';

    $originalenddate = null;
    $guessedstartdate = null;
    $guessedenddate = null;
    $samestartdate = null;
    $lowerenddate = null;

    if ($options['guessstart'] || $options['guessall']) {

        $originalstartdate = $course->startdate;

        $guessedstartdate = $courseman->guess_start();
        $samestartdate = ($guessedstartdate == $originalstartdate);
        $lowerenddate = ($course->enddate && ($course->enddate < $guessedstartdate));

        if ($samestartdate) {
            if (!$guessedstartdate) {
                $notification .= PHP_EOL . '  ' . get_string('cantguessstartdate', 'tool_analytics');
            } else {
                // No need to update.
                $notification .= PHP_EOL . '  ' . get_string('samestartdate', 'tool_analytics') . ': ' . userdate($guessedstartdate);
            }
        } else if (!$guessedstartdate) {
            $notification .= PHP_EOL . '  ' . get_string('cantguessstartdate', 'tool_analytics');
        } else if ($lowerenddate) {
            $notification .= PHP_EOL . '  ' . get_string('cantguessstartdate', 'tool_analytics') . ': ' .
                get_string('enddatebeforestartdate', 'error') . ' - ' . userdate($guessedstartdate);
        } else {
            // Update it to something we guess.

            // We set it to $course even if we don't update because may be needed to guess the end one.
            $course->startdate = $guessedstartdate;
            $notification .= PHP_EOL . '  ' . get_string('startdate') . ': ' . userdate($guessedstartdate);

            // Two different course updates because week's end date may be recalculated after setting the start date.
            if ($options['update']) {
                update_course($course);

                // Refresh course data as end date may have been updated.
                $course = $DB->get_record('course', array('id' => $course->id));
                $courseman = new \core_analytics\course($course);
            }
        }
    }

    if ($options['guessend'] || $options['guessall']) {

        if (!empty($lowerenddate) && !empty($guessedstartdate)) {
            $course->startdate = $guessedstartdate;
        }

        $originalenddate = $course->enddate;

        $format = course_get_format($course);
        $formatoptions = $format->get_format_options();

        // Change this for a course formats API level call in MDL-60702.
        if (method_exists($format, 'update_end_date') && $formatoptions['automaticenddate']) {
            // Special treatment for weeks-based formats with automatic end date.

            if ($options['update']) {
                $format::update_end_date($course->id);
                $course->enddate = $DB->get_field('course', 'enddate', array('id' => $course->id));
                $notification .= PHP_EOL . '  ' . get_string('weeksenddateautomaticallyset', 'tool_analytics') . ': ' .
                    userdate($course->enddate);
            } else {
                // We can't provide more info without actually updating it in db.
                $notification .= PHP_EOL . '  ' . get_string('weeksenddatedefault', 'tool_analytics');
            }
        } else {
            $guessedenddate = $courseman->guess_end();

            if ($guessedenddate == $originalenddate) {
                if (!$guessedenddate) {
                    $notification .= PHP_EOL . '  ' . get_string('cantguessenddate', 'tool_analytics');
                } else {
                    // No need to update.
                    $notification .= PHP_EOL . '  ' . get_string('sameenddate', 'tool_analytics') . ': ' . userdate($guessedenddate);
                }
            } else if (!$guessedenddate) {
                $notification .= PHP_EOL . '  ' . get_string('cantguessenddate', 'tool_analytics');
            } else {
                // Update it to something we guess.

                $course->enddate = $guessedenddate;

                $updateit = false;
                if ($course->enddate < $course->startdate) {
                    $notification .= PHP_EOL . '  ' . get_string('errorendbeforestart', 'analytics', userdate($course->enddate));
                } else if ($course->startdate + (YEARSECS + (WEEKSECS * 4)) > $course->enddate) {
                    $notification .= PHP_EOL . '  ' . get_string('coursetoolong', 'analytics');
                } else {
                    $notification .= PHP_EOL . '  ' . get_string('enddate') . ': ' . userdate($course->enddate);
                    $updateit = true;
                }

                if ($options['update'] && $updateit) {
                    update_course($course);
                }
            }
        }

    }

    mtrace($notification);
}

mtrace(get_string('success'));

exit(0);
