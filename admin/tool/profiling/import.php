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
 * Profiling tool import utility.
 *
 * @package    tool_profiling
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');
require_once(__DIR__ . '/import_form.php');

admin_externalpage_setup('toolprofiling');

$PAGE->navbar->add(get_string('import', 'tool_profiling'));

// Calculate export variables.
$tempdir = 'profiling';
make_temp_directory($tempdir);

// URL where we'll end, both on success and failure.
$url = new moodle_url('/admin/tool/profiling/index.php');

// Instantiate the upload profiling runs form.
$mform = new profiling_import_form();

// If there is any file to import.
if ($data = $mform->get_data()) {
    $filename = $mform->get_new_filename('mprfile');
    $file = $CFG->tempdir . '/' . $tempdir . '/' . $filename;
    $status = $mform->save_file('mprfile', $file);
    if ($status) {
        // File saved properly, let's import it.
        $status = profiling_import_runs($file, $data->importprefix);
    }
    // Delete the temp file, not needed anymore.
    if (file_exists($file)) {
        unlink($file);
    }
    if ($status) {
        // Import ended ok, let's redirect to main profiling page.
        redirect($url, get_string('importok', 'tool_profiling', $filename));
    }
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('import', 'tool_profiling'));
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

// Something wrong happened, notice it and done.
notice(get_string('importproblem', 'tool_profiling', $filename), $url);
