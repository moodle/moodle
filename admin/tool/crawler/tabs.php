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
 * Quick access tabs
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$adminrows = [
    new tabobject('settings', new moodle_url('/admin/settings.php?section=tool_crawler'),
        get_string('settings', 'tool_crawler')),
    new tabobject('index',    new moodle_url('/admin/tool/crawler/index.php'),
        get_string('status',   'tool_crawler'))
];

$courseid = optional_param('course', 0, PARAM_INT);
$courseparam = $courseid ? ['course' => $courseid] : [];
$courserows = [
    new tabobject('queued',   new moodle_url('/admin/tool/crawler/report.php', ['report' => 'queued'] + $courseparam),
        get_string('queued',   'tool_crawler')),
    new tabobject('recent',   new moodle_url('/admin/tool/crawler/report.php', ['report' => 'recent'] + $courseparam),
        get_string('recent',   'tool_crawler')),
    new tabobject('broken',   new moodle_url('/admin/tool/crawler/report.php', ['report' => 'broken'] + $courseparam),
        get_string('broken',   'tool_crawler')),
    new tabobject('oversize', new moodle_url('/admin/tool/crawler/report.php', ['report' => 'oversize'] + $courseparam),
        get_string('oversize', 'tool_crawler'))
];

$rows = array_merge(
    has_capability('moodle/site:config', context_system::instance()) ? $adminrows : [],
    $courserows
);

$section = optional_param('section', '', PARAM_RAW);
if ($section == 'tool_crawler') {
    $report = 'settings';
}
if (empty($report)) {
    $report = '';
}
$tabs = $OUTPUT->tabtree($rows, $report);

