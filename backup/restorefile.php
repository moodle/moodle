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
 * Import backup file or select existing backup file from moodle
 * @package   moodlecore
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once(dirname(__FILE__) . '/restorefile_form.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

// current context
$contextid = required_param('contextid', PARAM_INT);
// action
$action = optional_param('action', '', PARAM_ALPHA);
// file parameters
// non js interface may require these parameters
$component  = optional_param('component', null, PARAM_ALPHAEXT);
$filearea   = optional_param('filearea', null, PARAM_ALPHAEXT);
$itemid     = optional_param('itemid', null, PARAM_INT);
$filepath   = optional_param('filepath', null, PARAM_PATH);
$filename   = optional_param('filename', null, PARAM_FILE);

list($context, $course, $cm) = get_context_info_array($contextid);

$filecontext = $context;
// files in user context
if ($filearea == 'backup' && $component == 'user') {
    // XXX: hacky, to get user context
    $filecontext = get_context_instance(CONTEXT_USER, $USER->id);
}

$url = new moodle_url('/backup/restorefile.php', array('contextid'=>$contextid));

switch ($context->contextlevel) {
    case CONTEXT_COURSE:
        $heading = get_string('restorecourse', 'backup');
        break;
    case CONTEXT_MODULE:
        $heading = get_string('restoreactivity', 'backup');
        break;
    default:
        print_error('invalidcontext');
        die;
}


require_login($course);
require_capability('moodle/restore:restorecourse', $context);

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($heading);
$PAGE->set_pagelayout('admin');
$browser = get_file_browser();

// choose the backup file from backup files tree
if ($action == 'choosebackupfile') {
    if ($fileinfo = $browser->get_file_info($filecontext, $component, $filearea, $itemid, $filepath, $filename)) {
        $filename = restore_controller::get_tempdir_name($course->id, $USER->id);
        $pathname = "$CFG->dataroot/temp/backup/".$filename;
        $fileinfo->copy_to_pathname($pathname);
        $restore_url = new moodle_url('/backup/restore.php', array('contextid'=>$contextid, 'filename'=>$filename));
        redirect($restore_url);
    } else {
        redirect($url, get_string('filenotfound', 'error'));
    }
    die;
}

$form = new course_restore_form(null, array('contextid'=>$contextid));
$data = $form->get_data();
if ($data && has_capability('moodle/restore:uploadfile', $context)) {
    $filename = restore_controller::get_tempdir_name($course->id, $USER->id);
    $pathname = "$CFG->dataroot/temp/backup/".$filename;
    $form->save_file('backupfile', $pathname);
    $restore_url = new moodle_url('/backup/restore.php', array('contextid'=>$contextid, 'filename'=>$filename));
    redirect($restore_url);
    die;
}

$treeview_options = array();
$treeview_options['context'] = $context;
$treeview_options['filecontext'] = $filecontext;
$treeview_options['component'] = $component;
$treeview_options['filearea'] = $filearea;
$treeview_options['itemid'] = $itemid;
$treeview_options['filepath'] = $filepath;
$treeview_options['filename'] = $filename;
if (empty($component) && empty($filearea)) {
    $treeview_options['show_user_backup'] = true;
}


echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('choosefile', 'backup'));
echo $OUTPUT->container_start();
$renderer = $PAGE->get_renderer('core', 'backup');

echo $renderer->backup_files_viewer($treeview_options);
echo $OUTPUT->container_end();

// require uploadfile cap to use file picker
if (has_capability('moodle/restore:uploadfile', $context)) {
    echo $OUTPUT->heading(get_string('importfile', 'backup'));
    echo $OUTPUT->container_start();
    $form->display();
    echo $OUTPUT->container_end();
}

echo $OUTPUT->footer();
