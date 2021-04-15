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
 * Manage H5P libraries settings page.
 *
 * @package    core_h5p
 * @copyright  2019 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');

require_login(null, false);

$deletelibrary = optional_param('deletelibrary', null, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$action = optional_param('action', null, PARAM_ALPHANUMEXT);

$context = context_system::instance();
require_capability('moodle/h5p:updatelibraries', $context);

$pagetitle = get_string('h5pmanage', 'core_h5p');
$url = new \moodle_url("/h5p/libraries.php");

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title("$SITE->shortname: " . $pagetitle);
$PAGE->set_heading($SITE->fullname);

$h5pfactory = new \core_h5p\factory();
if ($deletelibrary) {
    $library = \core_h5p\api::get_library($deletelibrary);
    if ($confirm) {
        require_sesskey();
        \core_h5p\api::delete_library($h5pfactory, $library);
        redirect(new moodle_url('/h5p/libraries.php'));
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('deleting', 'core_h5p'));
    echo $OUTPUT->confirm(
        get_string('deletelibraryconfirm', 'core_h5p', [
            'name' => format_string($library->title),
            'version' => format_string($library->majorversion . '.' . $library->minorversion . '.' . $library->patchversion),
        ]),
        new moodle_url($PAGE->url, ['deletelibrary' => $deletelibrary, 'confirm' => 1]),
        new moodle_url('/h5p/libraries.php')
    );
    echo $OUTPUT->footer();
    die();
}

if (!is_null($action)) {
    if ($action == 'enable' || $action == 'disable') {
        // If action is enable or disable, library id is required too.
        $libraryid = required_param('id', PARAM_INT);

        \core_h5p\api::set_library_enabled($libraryid, ($action == 'enable'));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
echo $OUTPUT->box(get_string('librariesmanagerdescription', 'core_h5p'));

$form = new \core_h5p\form\uploadlibraries_form();
if ($data = $form->get_data()) {
    require_sesskey();

    // Get the file from the users draft area.
    $usercontext = context_user::instance($USER->id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data->h5ppackage, 'id',
        false);
    $file = reset($files);

    // Validate and save the H5P package.
    // Because we are passing skipcontent = true to save_h5p function, the returning value is false if an error
    // is encountered, null when successfully saving the package without creating the content.
    if (\core_h5p\helper::save_h5p($h5pfactory, $file, new stdClass(), false, true) === false) {
        echo $OUTPUT->notification(get_string('invalidpackage', 'core_h5p'), 'error');
    } else {
        echo $OUTPUT->notification(get_string('uploadsuccess', 'core_h5p'), 'success');
    }
}
$form->display();

// Load installed Libraries.
$framework = $h5pfactory->get_framework();
$libraries = $framework->loadLibraries();

if (!empty($libraries)) {
    $libs = new \core_h5p\output\libraries($h5pfactory, $libraries);
    echo $OUTPUT->render_from_template('core_h5p/h5plibraries', $libs->export_for_template($OUTPUT));
}

echo $OUTPUT->footer();
