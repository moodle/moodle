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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
global $CFG;

require_once($CFG->dirroot . '/blocks/simple_restore/lib.php');

// Check permissions.
require_login();

$courseid = required_param('id', PARAM_INT);
$restoreto = optional_param('restore_to', 0, PARAM_INT);

$name = optional_param('name', null, PARAM_RAW);
$action = optional_param('action', null, PARAM_TEXT);
$file = optional_param('fileid', null, PARAM_RAW);

// Needed for admins, as they need to query the courses.
$shortname = optional_param('shortname', null, PARAM_TEXT);

// Determine whether archive mode.
$archivemode = $courseid == SITEID && get_config('simple_restore', 'is_archive_server');

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('no_course', 'block_simple_restore', '', $courseid);
}

$blockname = get_string('pluginname', 'block_simple_restore');
$heading = simple_restore_utils::heading($restoreto);

$baseurl = new moodle_url('/blocks/simple_restore/list.php', array(
    'id' => $courseid, 'restore_to' => $restoreto
));


// Set context and require capabilities depending on archive_mode.
if ($archivemode) {
    $context = context_system::instance();
    require_capability('block/simple_restore:canrestorearchive', $context);
} else {
    $context = context_course::instance($courseid);
    require_capability('block/simple_restore:canrestore', $context);
}

// Return the number of grades.
$sql = "SELECT COUNT(*) as count
        FROM mdl_course AS c
        JOIN mdl_grade_items AS gi ON gi.courseid = c.id
        JOIN mdl_grade_grades AS gg ON gi.id = gg.itemid 
        WHERE NOT gg.finalgrade <=> NULL 
        AND gi.courseid = :courseid";
$count = $DB->count_records_sql($sql, array("courseid" => $courseid));

if ($count > 0) {
    $PAGE->set_url($baseurl);
    $warn = $OUTPUT->notification(simple_restore_utils::_s('have_grades'));

    $PAGE->set_context($context);
    $PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php?id='.$course->id));
    $PAGE->set_title($blockname.': '.$heading);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(simple_restore_utils::_s('restore_stopped'));
    echo $warn;
    echo $OUTPUT->continue_button(
        new moodle_url('/course/view.php', array('id' => $course->id))
    );
    echo $OUTPUT->footer();
    die;
}

// The user has chosen a file.
if ($file and $action and $name) {

    // We need to get the course name, etc differently when in archive mode.
    if ($archivemode) {
        simple_restore_utils::includes();

        // Parse the filename for course fullname and category.
        list($fullname, $category) = archive_restore_utils::coursedata_from_filename($file);

        // Get a category object.
        if (!$DB->record_exists('course_categories', array('name' => $category))) {
            // Create the category if it doesn't exits.
            $category = core_course_category::create(array('name' => $category));
        } else {
            // Otherwise, just fetch it.
            $category = $DB->get_record('course_categories', array('name' => $category));
        }

        // Prep_restore needs a course and a context.
        $courseid = restore_dbops::create_new_course($fullname, $fullname, $category->id);
        $context = context_course::instance($courseid);
    }

    // Move the backup file into place.
    $filename = simple_restore_utils::prep_restore($file, $name, $courseid);
    redirect(new moodle_url('/blocks/simple_restore/restore.php', array(
        'contextid' => $context->id,
        'filename' => $filename,
        'restore_to' => $restoreto
    )));
}

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->navbar->add($blockname);
$PAGE->set_title($blockname.': '.$heading);
$PAGE->set_heading($blockname.': '.$heading);
$PAGE->set_url($baseurl);

$system = context_system::instance();

$isadmin = has_capability('moodle/course:create', $system);

if (empty($shortname) and $isadmin) {
    require_once('list_form.php');

    $form = new list_form();

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
    } else if ($data = $form->get_data()) {
        $warn = $OUTPUT->notification(simple_restore_utils::_s('no_filter'));
    }

    $form->set_data(array('id' => $courseid, 'restore_to' => $restoreto));

    echo $OUTPUT->header();
    echo $OUTPUT->heading(simple_restore_utils::_s('adminfilter'));

    if (!empty($warn)) {
        echo $warn;
    }

    echo $OUTPUT->box_start();
    $form->display();
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();

$data = new stdClass;
$data->restore_to = $restoreto;
$data->courseid = $courseid;
// Admins can filter by shortname.
if ($isadmin) {
    $data->shortname = $shortname;
}
$data->lists = array();

simple_restore_utils::backup_list($data);

$displaylist = function($in, $list) {
    echo $list->html;
    return $in || !empty($list->backups);
};

// Obey handled order.
usort($data->lists, function($a, $b) {
    if ($a->order == $b->order) {
        return 0;
    }
    return $a->order < $b->order ? -1 : 1;
});

$successful = array_reduce($data->lists, $displaylist, false);

if (!$successful) {
    echo $OUTPUT->notification(simple_restore_utils::_s('empty_backups'));
    echo $OUTPUT->continue_button(
        new moodle_url('/course/view.php', array('id' => $courseid))
    );
}

echo $OUTPUT->footer();
