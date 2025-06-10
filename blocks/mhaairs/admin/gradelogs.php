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
 * @package block_mhaairs
 * @category admin
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once("$CFG->libdir/adminlib.php");
require_once("$CFG->dirroot/blocks/mhaairs/block_mhaairs_util.php");

$delete = optional_param('delete', 0, PARAM_INT);

admin_externalpage_setup('blockmhaairs_gradelogs');
$baseurl = '/blocks/mhaairs/admin/gradelogs.php';

$logger = MHLog::instance();

// DATA PROCESSING.
if ($delete) {
    $logger->delete_all();
    redirect(new \moodle_url($baseurl));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('gradelogs', 'block_mhaairs'));

if ($logs = $logger->logs) {
    // Print delete option.
    $url = new \moodle_url($baseurl, array('delete' => 1));
    $link = html_writer::link($url, get_string('deleteall'));
    echo html_writer::tag('div', $link);

    // Print summary.
    foreach ($logs as $logfile) {
        echo html_writer::tag('div', $logfile);
    }

} else {
    // No logs found.
    echo get_string('nogradelogs', 'block_mhaairs');
}

echo $OUTPUT->footer();
