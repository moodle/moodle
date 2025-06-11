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
 * @package    block_backadel
 * @copyright  2016 Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

// Require login.
require_login();

// Ensure the site admin is the page user.
if (!is_siteadmin($USER->id)) {
    moodle_exception('need_permission', 'block_backadel');
}

// Page Setup.
$blockname = get_string('pluginname', 'block_backadel');
$header = get_string('delete_header', 'block_backadel');

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->navbar->add($header);
$PAGE->set_title($blockname);
$PAGE->set_heading($SITE->shortname . ': ' . $blockname);
$PAGE->set_url('/blocks/backadel/delete.php');

$PAGE->requires->js('/blocks/backadel/js/jquery.js');
$PAGE->requires->js('/blocks/backadel/js/toggle.js');

// Output the page.
echo $OUTPUT->header();
echo $OUTPUT->heading($header);

// Populate deletedids from the post.
if (isset($_POST['delete'])) {
    $deleteids = $_POST['delete'];
} else {
    $deleteids = null;
}

// Dont bother if there are none.
if ($deleteids) {
    $todelete = array();

    // Loop through the courses to delete.
    foreach ($deleteids as $id) {
        $id = clean_param($id, PARAM_INT);
        // Grab the course fullname.
        $fullname = $DB->get_field('course', 'fullname', array('id' => $id));

        // Print out the status of the successful deletion.
        if (backadel_delete_course($id)) {
            mtrace(get_string('deleted', 'block_backadel', $fullname));
            $todelete[] = $id;
            // Otherwise print the error.
        } else {
            mtrace(get_string('delete_error', 'block_backadel'));
        }
        mtrace('<br />');
    }

    // Set the limits on the query.
    $where = 'coursesid IN (' . implode(', ', $todelete) . ')';

    // Grab the list of courses to delete.
    $DB->delete_records_select('block_backadel_statuses', $where);

    // Output the footer.
    echo $OUTPUT->footer();
    return true;
}

// List completed backups.
$completedids = $DB->get_fieldset_select('block_backadel_statuses',
    'coursesid', 'status = "SUCCESS"');

// Output a different string if there are none completed.
if (!$completedids) {
    echo '<div>' . get_string('none_completed', 'block_backadel') . '</div>';

    // Output the footer.
    echo $OUTPUT->footer();
    return true;
}

// Set the limits on the query.
$where = 'id IN (' . implode(', ', $completedids) . ')';

// Grab the list of completed courses.
$courses = $DB->get_records_select('course', $where);

// Set up a new table.
$table = new html_table();
$table->head = array(get_string('shortname'), get_string('fullname'), get_string('delete', 'block_backadel'));
$table->data = array();

// Loop through the list of courses.
foreach ($courses as $c) {

    // Build links.
    $url = new moodle_url('/course/view.php?id=' . $c->id);
    $link = html_writer::link($url, $c->shortname);

    // Add checkboxes.
    $checkbox = html_writer::checkbox('delete[]', $c->id);

    // Populate the table with links and checkboxes.
    $table->data[] = array($link, $c->fullname, $checkbox);
}

// Output the form.
echo '<form action = "delete.php" method = "POST">';
echo html_writer::table($table);
echo html_writer::link('#', get_string('toggle_all', 'block_backadel'), array('class' => 'toggle_link'));
echo '    <input type = "submit" value = "' . get_string('delete_button', 'block_backadel') . '"/>';
echo '</form>';

// Output the footer.
echo $OUTPUT->footer();
