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
 * Moodle file tree viewer based on YUI2 Treeview
 *
 * @package    core
 * @subpackage file
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

$contextid  = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
$filepath   = optional_param('filepath', '', PARAM_PATH);
$filename   = optional_param('filename', '', PARAM_FILE);
// hard-coded to course legacy area
$component = 'course';
$filearea  = 'legacy';
$itemid    = 0;

$PAGE->set_url('/files/index.php', array('contextid'=>$contextid, 'filepath'=>$filepath, 'filename'=>$filename));

if ($filepath === '') {
    $filepath = null;
}

if ($filename === '') {
    $filename = null;
}

list($context, $course, $cm) = get_context_info_array($contextid);
$PAGE->set_context($context);

require_login($course, false, $cm);
require_capability('moodle/course:managefiles', $context);

$browser = get_file_browser();

$file_info = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);

$strfiles = get_string("files");
if ($node = $PAGE->settingsnav->find('coursefiles', navigation_node::TYPE_SETTING)) {
    $node->make_active();
} else {
    $PAGE->navbar->add($strfiles);
}

$PAGE->set_title("$course->shortname: $strfiles");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('course');

$output = $PAGE->get_renderer('core', 'files');

echo $output->header();
echo $output->box_start();

if ($file_info) {
    $options = array();
    $options['context'] = $context;
    //$options['visible_areas'] = array('backup'=>array('section', 'course'), 'course'=>array('legacy'), 'user'=>array('backup'));
    echo $output->files_tree_viewer($file_info, $options);
} else {
    echo $output->notification(get_string('nofilesavailable', 'repository'));
}

echo $output->box_end();
echo $output->footer();
