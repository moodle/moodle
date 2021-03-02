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
 * @package   local_iomad_track
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to import completion information.
 */

require_once(dirname(__FILE__) . '/../../config.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$completions = optional_param('completions', 0, PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);
$submit = optional_param('submitbutton', '', PARAM_ALPHANUM);

$context = context_system::instance();
require_login();

iomad::require_capability('local/iomad_track:importfrommoodle', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

$linktext = get_string('importcompletionrecords', 'local_iomad_track');

// Set the url.
$linkurl = new moodle_url('/local/iomad_track/import.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Process current completions.
if (!empty($completions)) {
    if (confirm_sesskey() && $confirm == md5($completions)) {
        $task = new local_iomad_track\task\importmoodlecompletioninformation();
        \core\task\manager::queue_adhoc_task($task, true);
        redirect($linkurl);
    } else {
        echo $OUTPUT->header();
        $optionsyes = array('completions' => $completions, 'confirm' => md5($completions), 'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('importcompletionsfrommoodlefull', 'local_iomad_track'),
                              new moodle_url('/local/iomad_track/import.php', $optionsyes), $linkurl);
        echo $OUTPUT->footer();
        die;
    }
}

// Display the page.
echo $OUTPUT->header();

echo html_writer::tag('a',
                      get_string('importcompletionsfrommoodle', 'local_iomad_track'),
                      array('class' => 'btn-primary',
                            'href' => new moodle_url('/local/iomad_track/import.php',
                                                     array('completions' => true,
                                                           'sesskey' => sesskey()))));

echo $OUTPUT->footer();
