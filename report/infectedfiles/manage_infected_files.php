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
 * Manage infected  files.
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
use \core\antivirus\quarantine;

require_admin();
require_sesskey();

$action = optional_param('action', '', PARAM_TEXT);
$reportpage = new moodle_url('/report/infectedfiles/index.php');
$thispage = new moodle_url('/report/infectedfiles/manage_infected_files.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_url($thispage);
$PAGE->navbar->add(get_string('infectedfiles', 'report_infectedfiles'), $reportpage);

switch ($action) {
    case 'download':
        $filename = required_param('filename', PARAM_TEXT);
        quarantine::download_quarantined_file($filename);
    case 'downloadall':
        quarantine::download_all_quarantined_files();
    case 'confirmdelete':
        $filename = required_param('filename', PARAM_TEXT);
        $deleteparams = ['filename' => $filename, 'action' => 'delete', 'sesskey' => sesskey()];
        $confirmeddelete = new single_button(new moodle_url($thispage, $deleteparams), get_string('delete'), 'post');
        $cancel = new single_button(new moodle_url($reportpage), get_string('cancel'), 'post');
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('infectedfiles', 'report_infectedfiles'));
        echo $OUTPUT->confirm(get_string('confirmdelete', 'report_infectedfiles'), $confirmeddelete, $cancel);
        echo $OUTPUT->footer();
        die;
    case 'delete':
        $filename = required_param('filename', PARAM_TEXT);
        quarantine::delete_quarantined_file($filename);
        redirect($reportpage);
    case 'confirmdeleteall':
        require_sesskey();
        $deleteallparams = ['action' => 'deleteall', 'sesskey' => sesskey()];
        $confirmeddeleteall = new single_button(new moodle_url($thispage, $deleteallparams), get_string('delete'), 'post');
        $cancel = new single_button(new moodle_url($reportpage), get_string('cancel'), 'post');
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('infectedfiles', 'report_infectedfiles'));
        echo $OUTPUT->confirm(get_string('confirmdeleteall', 'report_infectedfiles'), $confirmeddeleteall, $cancel);
        echo $OUTPUT->footer();
        die;
    case 'deleteall':
        require_sesskey();
        // Remove file until current time.
        quarantine::clean_up_quarantine_folder(time());
        redirect($reportpage);
    default:
        break;
}
