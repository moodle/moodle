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
 * Running task admin page.
 *
 * @package    tool_task
 * @copyright  2019 The Open University
 * @copyright  2020 Mikhail Golenkov <golenkovm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$pageurl = new \moodle_url('/admin/tool/task/runningtasks.php');
$heading = get_string('runningtasks', 'tool_task');
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

admin_externalpage_setup('runningtasks');

echo $OUTPUT->header();

if (!get_config('core', 'cron_enabled')) {
    $renderer = $PAGE->get_renderer('tool_task');
    echo $renderer->cron_disabled();
}

$table = new \tool_task\running_tasks_table();
$table->baseurl = $pageurl;
$table->out(100, false);

echo $OUTPUT->footer();
