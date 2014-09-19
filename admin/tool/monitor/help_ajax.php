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
 * Displays help via AJAX call.
 *
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @package tool_monitor
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
define('AJAX_SCRIPT', true);

require_once('../../../config.php');

$type = required_param('type', PARAM_ALPHA);
$id = required_param('id', PARAM_INT);
$lang = optional_param('lang', 'en', PARAM_LANG);

// We don't actually modify the session here as we have NO_MOODLE_COOKIES set.
$SESSION->lang = $lang;
$PAGE->set_url('/admin/tool/monitor/help_ajax.php');

if ($type == 'rule') {
    $item = \tool_monitor\rule_manager::get_rule($id);
} else { // Must be a subscription.
    $item = \tool_monitor\subscription_manager::get_subscription($id);
}

if ($item->courseid) {
    $PAGE->set_context(context_course::instance($item->courseid));
} else { // Must be system context.
    $PAGE->set_context(context_system::instance());
}

echo json_encode(tool_monitor\output\helpicon\renderable::get_help_string_parameters($type, $id, true));
