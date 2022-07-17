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
 * Main interface to Moodle PHP code check
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot. '/local/moodlecheck/locallib.php');

// Include all files from rules directory.
if ($dh = opendir($CFG->dirroot. '/local/moodlecheck/rules')) {
    while (($file = readdir($dh)) !== false) {
        if ($file != '.' && $file != '..') {
            $pathinfo = pathinfo($file);
            if (isset($pathinfo['extension']) && $pathinfo['extension'] == 'php') {
                require_once($CFG->dirroot. '/local/moodlecheck/rules/'. $file);
            }
        }
    }
    closedir($dh);
}

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($SITE->fullname . ': ' . get_string('pluginname', 'local_moodlecheck'));
$PAGE->set_url(new moodle_url('/local/moodlecheck/index.php'));
$output = $PAGE->get_renderer('local_moodlecheck');

echo $output->header();

$form = new local_moodlecheck_form();
$form->display();

if ($form->is_submitted() && $form->is_validated()) {
    $data = $form->get_data();
    $paths = preg_split('/\s*\n\s*/', trim($data->path), null, PREG_SPLIT_NO_EMPTY);
    $ignorepaths = preg_split('/\s*\n\s*/', trim($data->ignorepath), null, PREG_SPLIT_NO_EMPTY);
    if (isset($data->checkall) && $data->checkall == 'selected' && isset($data->rule)) {
        foreach ($data->rule as $code => $value) {
            local_moodlecheck_registry::enable_rule($code);
        }
    } else {
        local_moodlecheck_registry::enable_all_rules();
    }

    // Store result for later output.
    $result = [];

    foreach ($paths as $filename) {
        $path = new local_moodlecheck_path($filename, $ignorepaths);
        $result[] = $output->display_path($path);
    }

    echo $output->display_summary();

    foreach ($result as $line) {
        echo $line;
    }
}

echo $output->footer();
