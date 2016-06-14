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
 * Profiling tool export utility.
 *
 * @package    tool_profiling
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');

// Page parameters.
$runid = required_param('runid', PARAM_ALPHANUM);
$listurl = required_param('listurl', PARAM_PATH);

admin_externalpage_setup('toolprofiling');

$PAGE->navbar->add(get_string('export', 'tool_profiling'));

// Calculate export variables.
$tempdir = 'profiling';
make_temp_directory($tempdir);
$runids = array($runid);
$filename = $runid . '.mpr';
$filepath = $CFG->tempdir . '/' . $tempdir . '/' . $filename;

// Generate the mpr file and send it.
if (profiling_export_runs($runids, $filepath)) {
    send_file($filepath, $filename, 0, 0, false, false, '', true);
    unlink($filepath); // Delete once sent.
    die;
}

// Something wrong happened, notice it and done.
$urlparams = array(
        'runid' => $runid,
        'listurl' => $listurl);
$url = new moodle_url('/admin/tool/profiling/index.php', $urlparams);
notice(get_string('exportproblem', 'tool_profiling', $urlparams), $url);
