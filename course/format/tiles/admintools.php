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
 * Page called by administrator to carry out admin functions from plugin settings page.
 *
 * @package format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

require_once('../../../config.php');

global $PAGE, $DB, $OUTPUT;

require_login();
$systemcontext = context_system::instance();

// Admins only for this page.
if (!has_capability('moodle/site:config', $systemcontext)) {
    throw new moodle_exception('You do not have permission to perform this action.');
}

$action = required_param('action', PARAM_TEXT);
$pageurl = new moodle_url('/course/format/tiles/admintools.php', array('action' => $action));
$settingsurl = new moodle_url('/admin/settings.php', array('section' => 'formatsettingtiles'));

$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_heading(get_string('admintools', 'format_tiles'));
$PAGE->navbar->add(get_string('administrationsite'), new moodle_url('/admin/search.php'));
$PAGE->navbar->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', array('category' => 'modules')));
$PAGE->navbar->add(get_string('courseformats'), new moodle_url('/admin/category.php', array('category' => 'formatsettings')));
$PAGE->navbar->add(get_string('pluginname', 'format_tiles'), $settingsurl);
$PAGE->navbar->add(get_string('admintools', 'format_tiles'));

$o = '';

switch ($action) {
    case 'resetcolours':
        $o = reset_colours($settingsurl, $pageurl);
        break;
    case 'deleteemptysections':
        schedule_delete_empty_sections();
        break;
    case 'reordersections':
        resolve_section_misnumbering();
        break;
    case 'canceldeleteemptysections':
        cancel_delete_empty_sections();
        break;
    case 'listproblemcourses':
        $o = list_problem_courses();
        break;
    default:
        break;
}

echo $OUTPUT->header();
echo $o;
echo $OUTPUT->footer();

/**
 * Get an array of all the permitted colour hex values allowed by site admin in plugin settings.
 * @package format_tiles
 * @return array
 * @throws dml_exception
 */
function permitted_colours() {
    global $DB;
    $records = $DB->get_records_select(
        'config_plugins',
        "plugin = 'format_tiles' AND " . $DB->sql_like('name', '?', false), array("tilecolour%")
    );
    $permittedcolours = [];
    foreach ($records as $record) {
        if (hexdec($record->value) !== 0) {
            // If the colour is #000 or #000000 we ignore as this means admin has disabled the colour.
            $permittedcolours[] = $record->value;
        }
    }
    return $permittedcolours;
}

/**
 * Function to allow site admin to reset course colours to allowed settings from Site Admin > Plugins page.
 * @param moodle_url $settingsurl
 * @param moodle_url $pageurl
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function reset_colours($settingsurl, $pageurl) {
    global $DB;
    require_sesskey();
    $permittedcolours = permitted_colours();
    if (count($permittedcolours) === 0) {
        redirect(
            $settingsurl,
            get_string('novaliddefaultcolour', 'format_tiles'), null, \core\output\notification::NOTIFY_ERROR
        );
    }
    // Prepare a "NOT IN" statement for the permitted colours, to find records which have other colours.
    list($permittedcolourssql, $params) = $DB->get_in_or_equal($permittedcolours, SQL_PARAMS_NAMED, 'param', false);

    if (!optional_param('sure', 0, PARAM_INT)) {
        // User has not said they are sure yet so count how many courses are affected and offer user chance to confirm.
        $requiredchangecount = $DB->count_records_sql(
            "SELECT COUNT(courseid) FROM {course_format_options}
                WHERE format = 'tiles' AND name = 'basecolour'
                AND value " . $permittedcolourssql,
            $params
        );

        if ($requiredchangecount === 0) {
            redirect($settingsurl, get_string('allcoursescomplypalette', 'format_tiles'));
        } else {
            $pageurl->param('sure', '1');
            $pageurl->param('sesskey', sesskey());
            $o = html_writer::div(get_string('sureresetcolours', 'format_tiles', $requiredchangecount), 'mb-3 mt-3');
            $o .= html_writer::link($pageurl, get_string('resetcolours', 'format_tiles'), array('class' => 'btn btn-danger'));
            $o .= html_writer::link($settingsurl, get_string('cancel'), array('class' => 'btn btn-secondary'));
            return $o;
        }
    } else {
        // User has said they are sure so go ahead and reset.
        $defaultvalue = get_config('format_tiles', 'tilecolour1');

        // Validate our default value before we apply it to multiple courses.
        if (!$defaultvalue || strlen($defaultvalue) > 7 || substr($defaultvalue, 0, 1) !== "#"
            || !ctype_xdigit(substr($defaultvalue, 1)) || hexdec($defaultvalue) === 0) {
            redirect(
                $settingsurl,
                get_string('novaliddefaultcolour', 'format_tiles'), null, \core\output\notification::NOTIFY_ERROR
            );
        }
        // We don't want to trawl through and update each course record individually as it may take a while.
        // Better to just reset the 'illegal' colours in the DB in one query, given that we know what the permitted colours are.
        $sql = "UPDATE {course_format_options} SET value = :defaultvalue
            WHERE format = 'tiles' AND name = 'basecolour' AND sectionid = '0'
            AND value " . $permittedcolourssql;
        $params['defaultvalue'] = $defaultvalue;
        $DB->execute($sql, $params);

        redirect(
            $settingsurl,
            get_string('tilecolourschanged', 'format_tiles'), null, \core\output\notification::NOTIFY_SUCCESS
        );
    }
    return '';
}

/**
 * Allow site admin to schedule a deletion of empty sections from courses (from admintools.php).
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function schedule_delete_empty_sections() {
    require_sesskey();
    $courseid = required_param('courseid', PARAM_INT);
    $course = get_course($courseid);
    format_tiles\course_section_manager::schedule_empty_sec_deletion($course->id);
    redirect(
        \format_tiles\course_section_manager::get_list_problem_courses_url(),
        get_string('scheduleddeleteemptysections', 'format_tiles'),
        null,
        core\output\notification::NOTIFY_SUCCESS
    );
}

/**
 * Allow site admin to cancel scheduled deletion
 * @see schedule_delete_empty_sections()
 * @throws coding_exception
 * @throws moodle_exception
 */
function cancel_delete_empty_sections() {
    $courseid = required_param('courseid', PARAM_INT);
    format_tiles\course_section_manager::cancel_empty_sec_deletion($courseid);
    redirect(
        \format_tiles\course_section_manager::get_list_problem_courses_url(),
        get_string('cancelled'),
        null,
        core\output\notification::NOTIFY_SUCCESS
    );
}

/**
 * Get a HTML table of problem courses (too many / badly numbered sections) for display to admin.
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function list_problem_courses() {
    $maxsections = \format_tiles\course_section_manager::get_max_sections();

    // Find the courses which have section numbers we would not expect (too high).
    $problemcourses = \format_tiles\course_section_manager::get_problem_courses($maxsections);

    $o = html_writer::tag(
        'h2',
        get_string('problemcourses', 'format_tiles')
        . ' (' . get_string('experimentalfeature', 'format_tiles') . ')'
    );

    if (count($problemcourses)) {
        $displaycourses = [];
        foreach ($problemcourses as $problemcourse) {
            $courseurl = new moodle_url(
                '/course/view.php',
                ['id' => $problemcourse->id, 'edit' => 'on', 'sesskey' => sesskey()]
            );
            $displaycourse = new \stdClass();
            $displaycourse->link = html_writer::link(
                $courseurl,
                $problemcourse->fullname,
                ['target' => '_blank']
            );
            $displaycourse->count_sections = $problemcourse->count_sections;
            $displaycourse->max_section_number = $problemcourse->max_section_number;
            if ($problemcourse->count_sections > $maxsections) {
                $displaycourse->action = \format_tiles\course_section_manager::get_schedule_button($problemcourse->id);
            } else {
                $url = new moodle_url(
                    '/course/format/tiles/admintools.php',
                    ['action' => 'reordersections', 'courseid' => $problemcourse->id, 'sesskey' => sesskey()]
                );
                $displaycourse->action = html_writer::link(
                    $url,
                    get_string('fixproblems', 'format_tiles'),
                    ['target' => '_blank', 'class' => 'btn btn-secondary ml-2']
                );
            }
            $displaycourses[] = $displaycourse;
        }
        $table = new html_table();
        $table->caption = get_string('problemcourses', 'format_tiles');
        $table->head = array(
            get_string('course'),
            get_string('numberofsections', 'format_tiles'),
            get_string('highestsectionnum', 'format_tiles'),
            get_string('action')
        );
        $table->data = $displaycourses;

        $o .= html_writer::div(get_string('problemcoursesintro', 'format_tiles'));
        $o .= html_writer::div(get_string('maxcoursesectionsallowed', 'format_tiles', $maxsections));
        $o .= html_writer::table($table);
    } else {
        $o .= html_writer::div(
            get_string('noproblemsfound', 'format_tiles'),
            'alert alert-success'
        );
    }
    return $o;
}

/**
 * Allow site admin to fix a section with mis-numbering.
 * @throws coding_exception
 * @throws moodle_exception
 */
function resolve_section_misnumbering() {
    $courseid = required_param('courseid', PARAM_INT);
    require_sesskey();
    \format_tiles\course_section_manager::resolve_section_misnumbering($courseid);
    redirect(\format_tiles\course_section_manager::get_list_problem_courses_url());
}
