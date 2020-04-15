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
 * Generic content bank visualizer.
 *
 * @package   core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$record = $DB->get_record('contentbank_content', ['id' => $id], '*', MUST_EXIST);
$context = context::instance_by_id($record->contextid, MUST_EXIST);
require_capability('moodle/contentbank:access', $context);

$returnurl = new \moodle_url('/contentbank/index.php');
$plugin = core_plugin_manager::instance()->get_plugin_info($record->contenttype);
if (!$plugin || !$plugin->is_enabled()) {
    print_error('unsupported', 'core_contentbank', $returnurl);
}

$title = get_string('contentbank');
\core_contentbank\helper::get_page_ready($context, $title, true);
if ($PAGE->course) {
    require_login($PAGE->course->id);
}
$returnurl = new \moodle_url('/contentbank/index.php', ['contextid' => $context->id]);

$PAGE->set_url(new \moodle_url('/contentbank/view.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->navbar->add($record->name);
$PAGE->set_heading($title);
$title .= ": ".$record->name;
$PAGE->set_title($title);
$PAGE->set_pagetype('contenbank');

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

$managerlass = "\\$record->contenttype\\contenttype";
if (class_exists($managerlass)) {
    $manager = new $managerlass($context);
    if ($manager->can_access()) {
        echo $manager->get_view_content($record);
    }
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
