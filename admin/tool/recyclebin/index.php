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
 * This page shows the contents of a recyclebin for a given course.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/tablelib.php');

$contextid = required_param('contextid', PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);

$context = context::instance_by_id($contextid, MUST_EXIST);
$PAGE->set_context($context);

// We could be a course or a category.
switch ($context->contextlevel) {
    case CONTEXT_COURSE:
        require_login($context->instanceid);

        $recyclebin = new \tool_recyclebin\course_bin($context->instanceid);
        if (!$recyclebin->can_view()) {
            throw new required_capability_exception($context, 'tool/recyclebin:viewitems', 'nopermissions', '');
        }

        $PAGE->set_pagelayout('incourse');
        // Set the $PAGE heading - this is also the same as the h2 heading.
        $heading = format_string($COURSE->fullname, true, array('context' => $context)) . ': ' .
            get_string('pluginname', 'tool_recyclebin');
        $PAGE->set_heading($heading);

        // Get the expiry to use later.
        $expiry = get_config('tool_recyclebin', 'coursebinexpiry');
    break;

    case CONTEXT_COURSECAT:
        require_login(null, false);

        $recyclebin = new \tool_recyclebin\category_bin($context->instanceid);
        if (!$recyclebin->can_view()) {
            throw new required_capability_exception($context, 'tool/recyclebin:viewitems', 'nopermissions', '');
        }

        $PAGE->set_pagelayout('admin');
        // Set the $PAGE heading.
        $PAGE->set_heading($COURSE->fullname);
        // The h2 heading on the page is going to be different than the $PAGE heading.
        $heading = $context->get_context_name() . ': ' . get_string('pluginname', 'tool_recyclebin');

        // Get the expiry to use later.
        $expiry = get_config('tool_recyclebin', 'categorybinexpiry');
    break;

    default:
        print_error('invalidcontext', 'tool_recyclebin');
    break;
}

if (!$recyclebin::is_enabled()) {
    print_error('notenabled', 'tool_recyclebin');
}

$PAGE->set_url('/admin/tool/recyclebin/index.php', array(
    'contextid' => $contextid
));
$PAGE->set_title(get_string('pluginname', 'tool_recyclebin'));

// If we are doing anything, we need a sesskey!
if (!empty($action)) {
    raise_memory_limit(MEMORY_EXTRA);
    require_sesskey();

    $item = null;
    if ($action == 'restore' || $action == 'delete') {
        $itemid = required_param('itemid', PARAM_INT);
        $item = $recyclebin->get_item($itemid);
    }

    switch ($action) {
        // Restore it.
        case 'restore':
            if ($recyclebin->can_restore()) {
                $recyclebin->restore_item($item);
                redirect($PAGE->url, get_string('alertrestored', 'tool_recyclebin', $item), 2);
            } else {
                print_error('nopermissions', 'error');
            }
        break;

        // Delete it.
        case 'delete':
            if ($recyclebin->can_delete()) {
                $recyclebin->delete_item($item);
                redirect($PAGE->url, get_string('alertdeleted', 'tool_recyclebin', $item), 2);
            } else {
                print_error('nopermissions', 'error');
            }
        break;

        // Empty it.
        case 'empty':
            $recyclebin->delete_all_items();
            redirect($PAGE->url, get_string('alertemptied', 'tool_recyclebin'), 2);
        break;
    }
}

// Add a "Go Back" button.
$goback = html_writer::start_tag('div', array('class' => 'backlink'));
$goback .= html_writer::link($context->get_url(), get_string('backto', '', $context->get_context_name()));
$goback .= html_writer::end_tag('div');

// Output header.
echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

// Grab our items, check there is actually something to display.
$items = $recyclebin->get_items();

// Nothing to show? Bail out early.
if (empty($items)) {
    echo $OUTPUT->box(get_string('noitemsinbin', 'tool_recyclebin'));
    echo $goback;
    echo $OUTPUT->footer();
    die;
}

// Start with a description.
if ($expiry > 0) {
    $expirydisplay = format_time($expiry);
    echo '<div class=\'alert\'>' . get_string('deleteexpirywarning', 'tool_recyclebin', $expirydisplay) . '</div>';
}

// Define columns and headers.
$firstcolstr = $context->contextlevel == CONTEXT_COURSE ? 'activity' : 'course';
$columns = array($firstcolstr, 'date', 'restore', 'delete');
$headers = array(
    get_string($firstcolstr),
    get_string('datedeleted', 'tool_recyclebin'),
    get_string('restore'),
    get_string('delete')
);

// Define a table.
$table = new flexible_table('recyclebin');
$table->define_columns($columns);
$table->column_style('restore', 'text-align', 'center');
$table->column_style('delete', 'text-align', 'center');
$table->define_headers($headers);
$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'recycle-bin-table');
$table->setup();

// Cache a list of modules.
$modules = null;
if ($context->contextlevel == CONTEXT_COURSE) {
    $modules = $DB->get_records('modules');
}

// Add all the items to the table.
$showempty = false;
$canrestore = $recyclebin->can_restore();
foreach ($items as $item) {
    $row = array();

    // Build item name.
    $name = $item->name;
    if ($context->contextlevel == CONTEXT_COURSE) {
        if (isset($modules[$item->module])) {
            $mod = $modules[$item->module];
            $modname = get_string('modulename', $mod->name);
            $name = $OUTPUT->image_icon('monologo', $modname, $mod->name) . $name;
        }
    }

    $row[] = $name;
    $row[] = userdate($item->timecreated);

    // Build restore link.
    if ($canrestore && ($context->contextlevel == CONTEXT_COURSECAT || isset($modules[$item->module]))) {
        $restoreurl = new moodle_url($PAGE->url, array(
            'contextid' => $contextid,
            'itemid' => $item->id,
            'action' => 'restore',
            'sesskey' => sesskey()
        ));
        $row[] = $OUTPUT->action_icon($restoreurl, new pix_icon('t/restore', get_string('restore'), '', array(
            'class' => 'iconsmall'
        )));
    } else {
        // Show padlock.
        $row[] = $OUTPUT->pix_icon('t/locked', get_string('locked', 'admin'), '', array('class' => 'iconsmall'));
    }

    // Build delete link.
    if ($recyclebin->can_delete()) {
        $showempty = true;
        $delete = new moodle_url($PAGE->url, array(
            'contextid' => $contextid,
            'itemid' => $item->id,
            'action' => 'delete',
            'sesskey' => sesskey()
        ));
        $deleteaction = new confirm_action(get_string('deleteconfirm', 'tool_recyclebin'));
        $delete = $OUTPUT->action_icon($delete, new pix_icon('t/delete', get_string('delete')), $deleteaction);

        $row[] = $delete;
    } else {
        // Show padlock.
        $row[] = $OUTPUT->pix_icon('t/locked', get_string('locked', 'admin'), '', array('class' => 'iconsmall'));
    }

    $table->add_data($row);
}

// Display the table now.
$table->finish_output();

// Empty recyclebin link.
if ($showempty) {
    $emptylink = new moodle_url($PAGE->url, array(
        'contextid' => $contextid,
        'action' => 'empty',
        'sesskey' => sesskey()
    ));
    $emptyaction = new confirm_action(get_string('deleteallconfirm', 'tool_recyclebin'));
    echo $OUTPUT->action_link($emptylink, get_string('deleteall', 'tool_recyclebin'), $emptyaction);
}

echo $goback;

// Confirmation JS.
$PAGE->requires->strings_for_js(array('deleteallconfirm', 'deleteconfirm'), 'tool_recyclebin');

// Output footer.
echo $OUTPUT->footer();
