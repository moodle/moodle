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
 * Performs the custom lang export.
 *
 * @package    tool_customlang
 * @subpackage customlang
 * @copyright  2020 Thomas Wedekind <thomas.wedekind@univie.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/customlang/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

global $PAGE, $CFG;

require_login(SITEID, false);
require_capability('tool/customlang:export', context_system::instance());

$lng = required_param('lng', PARAM_LANG);

admin_externalpage_setup('toolcustomlang', '', null,
    new moodle_url('/admin/tool/customlang/import.php', ['lng' => $lng]));

$form = new \tool_customlang\form\export(null, ['lng' => $lng]);

if ($form->is_cancelled()) {
    redirect('index.php');
    die();
} else if ($formdata = $form->get_data()) {
    $tempzip = tempnam($CFG->tempdir . '/', 'tool_customlang_export');
    $filelist = [];
    foreach ($formdata->files as $file) {
        $filepath = tool_customlang_utils::get_localpack_location($lng). '/' . $file;
        if (file_exists($filepath)) {
            $filelist[$file] = $filepath;
        }
    }
    $zipper = new zip_packer();

    if (!empty($filelist) && $zipper->archive_to_pathname($filelist, $tempzip)) {
        // Filename include the lang name so the file can be imported with automatic language detection.
        send_temp_file($tempzip, "customlang_$lng.zip");
        die();
    }
}

$output = $PAGE->get_renderer('tool_customlang');

echo $output->header();
echo $output->heading(get_string('pluginname', 'tool_customlang'));
$form->display();
echo $OUTPUT->footer();
