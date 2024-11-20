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
 * Moodle Component Library
 *
 * A sample of different singlebuttons
 *
 * @package    tool_componentlibrary
 * @copyright  2022 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/lib/formslib.php');

require_login();
require_capability('moodle/site:configview', context_system::instance());

$repeatcount = optional_param('test_repeat', 1, PARAM_INT);

$PAGE->set_pagelayout('embedded');

$url = new moodle_url('/admin/tool/componentlibrary/examples/singlebuttons.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading('Moodle single buttons');
$PAGE->set_title('Moodle single buttons');

$buttondefinitions = [
    ['label' => 'Standard'],
    ['label' => 'Primary', 'type' => single_button::BUTTON_PRIMARY],
    ['label' => 'Danger', 'type' => single_button::BUTTON_DANGER],
    ['label' => 'Warning', 'type' => single_button::BUTTON_WARNING],
    ['label' => 'Success', 'type' => single_button::BUTTON_SUCCESS],
    ['label' => 'Info', 'type' => single_button::BUTTON_INFO],

];
echo $OUTPUT->header();
foreach ($buttondefinitions as $def) {
    $button = new single_button($url, $def['label'], 'post', $def['type'] ??
        single_button::BUTTON_SECONDARY, $def['attributes'] ?? []);
    echo $OUTPUT->render($button);
}
echo $OUTPUT->footer();
