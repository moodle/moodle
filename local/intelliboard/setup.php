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

defined('MOODLE_INTERNAL') || die;
/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

require_once("../../config.php");
require_once($CFG->libdir."/adminlib.php");
require_once($CFG->dirroot."/local/intelliboard/locallib.php");

require_login();
require_capability("moodle/site:config", context_system::instance());

if (!is_siteadmin()) {
    throw new moodle_exception('invalidaccess', 'error');
}
if (isset($CFG->intelliboardsetup) and $CFG->intelliboardsetup == false) {
    throw new moodle_exception('invalidaccess', 'error');
}

$PAGE->set_url("/local/intelliboard/setup.php");
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_intelliboard'));
$PAGE->set_heading(get_string('pluginname', 'local_intelliboard'));
$PAGE->set_pagetype('home');

$intelliboard = intelliboard(["task" => "setup"]);
$renderer = $PAGE->get_renderer("local_intelliboard");

$PAGE->requires->css("/local/intelliboard/assets/css/ionicons.min.css");
$PAGE->requires->js_call_amd(
    'local_intelliboard/setup', 'init', []
);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("setup", "local_intelliboard"));
echo $renderer->render(new \local_intelliboard\output\setup([
    "intelliboard" => $intelliboard
]));
echo $OUTPUT->footer();