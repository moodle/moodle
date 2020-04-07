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
 * Security overview report
 *
 * @package    report_security
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

use core\check\check;
use core\check\result;

// Print the header.
admin_externalpage_setup('reportsecurity', '', null, '', ['pagelayout' => 'report']);

// We may need a bit more memory and this may take a long time to process.
raise_memory_limit(MEMORY_EXTRA);
core_php_time_limit::raise();

$checks = \core\check\manager::get_security_checks();

$detail = optional_param('detail', '', PARAM_TEXT); // Show detailed info about one check only.
if ($detail) {
    $checks = array_filter($checks, function($check) use ($detail) {
        return $detail == $check->get_ref();
    });
    $checks = array_values($checks);
    if (!empty($checks)) {
        $PAGE->set_docs_path('report/security/index.php?detail=' . $detail);
        $PAGE->navbar->add($checks[0]->get_name());
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_security'));

echo '<div id="timewarning">' . get_string('timewarning', 'report_security') . '</div>';

$url = "$CFG->wwwroot/report/security/index.php";

$PAGE->requires->js_init_code("Y.one('#timewarning').addClass('timewarninghidden')");
$table = new html_table();
$table->data = [];
$table->head  = [
    get_string('status'),
    get_string('check'),
    get_string('summary'),
    get_string('action'),
];
$table->colclasses = [
    'rightalign status',
    'leftalign check',
    'leftalign summary',
    'leftalign action',
];
$table->id = 'securityreporttable';
$table->attributes = ['class' => 'admintable securityreport generaltable'];

$manager = core_plugin_manager::instance();

foreach ($checks as $check) {
    $ref = $check->get_ref();
    $result = $check->get_result();
    $component = $check->get_component();
    $actionlink = $check->get_action_link();

    $link = new \moodle_url('/report/security/index.php', ['detail' => $ref]);

    $row = [];
    $row[] = $OUTPUT->check_result($result);
    $row[] = $OUTPUT->action_link($link, $check->get_name());

    $row[] = $result->get_summary();
    if ($actionlink) {
        $row[] = $OUTPUT->render($actionlink);
    } else {
        $row[] = '';
    }

    $table->data[] = $row;
}
echo html_writer::table($table);

if ($detail && $result) {
    echo $OUTPUT->heading(get_string('description'), 3);
    echo $OUTPUT->box($result->get_details(), 'generalbox boxwidthnormal boxaligncenter');
    echo $OUTPUT->continue_button($url);
}

echo $OUTPUT->footer();
$event = \report_security\event\report_viewed::create(['context' => context_system::instance()]);
$event->trigger();

