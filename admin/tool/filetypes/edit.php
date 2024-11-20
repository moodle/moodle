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
 * Display the file type updating page.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('edit_form.php');

admin_externalpage_setup('tool_filetypes');

$oldextension = optional_param('oldextension', '', PARAM_ALPHANUMEXT);
$mform = new tool_filetypes_form('edit.php', array('oldextension' => $oldextension));
$title = get_string('addfiletypes', 'tool_filetypes');

if ($oldextension) {
    // This is editing an existing filetype, load data to the form.
    $mimetypes = get_mimetypes_array();
    if (!array_key_exists($oldextension, $mimetypes)) {
        throw new moodle_exception('error_notfound', 'tool_filetypes');
    }
    $typeinfo = $mimetypes[$oldextension];
    $formdata = array(
        'extension' => $oldextension,
        'mimetype' => $typeinfo['type'],
        'icon' => $typeinfo['icon'],
        'oldextension' => $oldextension,
        'description' => '',
        'groups' => '',
        'corestring' => '',
        'defaulticon' => 0
    );
    if (!empty($typeinfo['customdescription'])) {
        $formdata['description'] = $typeinfo['customdescription'];
    }
    if (!empty($typeinfo['groups'])) {
        $formdata['groups'] = implode(', ', $typeinfo['groups']);
    }
    if (!empty($typeinfo['string'])) {
        $formdata['corestring'] = $typeinfo['string'];
    }
    if (!empty($typeinfo['defaulticon'])) {
        $formdata['defaulticon'] = 1;
    }

    $mform->set_data($formdata);
    $title = get_string('editfiletypes', 'tool_filetypes');
}

$backurl = new \moodle_url('/admin/tool/filetypes/index.php');
if ($mform->is_cancelled()) {
    redirect($backurl);
} else if ($data = $mform->get_data()) {
    // Convert the groups value back into an array.
    $data->groups = trim($data->groups);
    if ($data->groups) {
        $data->groups = preg_split('~,\s*~', $data->groups);
    } else {
        $data->groups = array();
    }
    if (empty($data->defaulticon)) {
        $data->defaulticon = 0;
    }
    if (empty($data->corestring)) {
        $data->corestring = '';
    }
    if (empty($data->description)) {
        $data->description = '';
    }
    if ($data->oldextension) {
        // Update an existing file type.
        core_filetypes::update_type($data->oldextension, $data->extension, $data->mimetype, $data->icon,
            $data->groups, $data->corestring, $data->description, (bool)$data->defaulticon);
    } else {
        // Add a new file type entry.
        core_filetypes::add_type($data->extension, $data->mimetype, $data->icon,
            $data->groups, $data->corestring, $data->description, (bool)$data->defaulticon);
    }
    redirect($backurl);
}

// Page settings.
$context = context_system::instance();
$PAGE->set_url(new \moodle_url('/admin/tool/filetypes/edit.php', array('oldextension' => $oldextension)));

$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->set_secondary_active_tab('server');

$PAGE->navbar->add($oldextension ? s($oldextension) : $title);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);

// Display the page.
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
