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
 * @author    Guy Thomas <citricity@gmail.com>
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
global $PAGE, $CFG, $USER, $DB;
require_login();
require_capability('tool/ally:viewlogs', context_system::instance());


$PAGE->set_url('/admin/tool/ally/logs.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('logs', 'tool_ally'));
$PAGE->set_pagelayout('report');
$PAGE->set_heading(get_string('logs', 'tool_ally'));

$PAGE->requires->jquery();

echo $OUTPUT->header();

$PAGE->requires->js_call_amd('tool_ally/logviewer', 'init');

$template = <<<TEMP
<div id="app">
 <router-view></router-view>
</div>
TEMP;

echo ($template);

echo $OUTPUT->footer();
