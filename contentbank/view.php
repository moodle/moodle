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
$deletecontent = optional_param('deletecontent', null, PARAM_INT);

$PAGE->requires->js_call_amd('core_contentbank/actions', 'init');

$record = $DB->get_record('contentbank_content', ['id' => $id], '*', MUST_EXIST);
$context = context::instance_by_id($record->contextid, MUST_EXIST);
require_capability('moodle/contentbank:access', $context);

$statusmsg = optional_param('statusmsg', '', PARAM_ALPHANUMEXT);
$errormsg = optional_param('errormsg', '', PARAM_ALPHANUMEXT);

$returnurl = new \moodle_url('/contentbank/index.php', ['contextid' => $context->id]);
$plugin = core_plugin_manager::instance()->get_plugin_info($record->contenttype);
if (!$plugin || !$plugin->is_enabled()) {
    print_error('unsupported', 'core_contentbank', $returnurl);
}

$title = get_string('contentbank');
\core_contentbank\helper::get_page_ready($context, $title, true);
if ($PAGE->course) {
    require_login($PAGE->course->id);
}

$PAGE->set_url(new \moodle_url('/contentbank/view.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->navbar->add($record->name);
$PAGE->set_heading($record->name);
$title .= ": ".$record->name;
$PAGE->set_title($title);
$PAGE->set_pagetype('contentbank');

$contenttypeclass = "\\$record->contenttype\\contenttype";
$contentclass = "\\$record->contenttype\\content";
if (!class_exists($contenttypeclass) || !class_exists($contentclass)) {
    print_error('contenttypenotfound', 'error', $returnurl, $record->contenttype);
}
$contenttype = new $contenttypeclass($context);
$content = new $contentclass($record);

// Create the cog menu with all the secondary actions, such as delete, rename...
$actionmenu = new action_menu();
$actionmenu->set_alignment(action_menu::TR, action_menu::BR);
if ($contenttype->can_manage($content)) {
    // Add the rename content item to the menu.
    $attributes = [
        'data-action' => 'renamecontent',
        'data-contentname' => $content->get_name(),
        'data-contentid' => $content->get_id(),
    ];
    $actionmenu->add_secondary_action(new action_menu_link(
        new moodle_url('#'),
        new pix_icon('e/styleparagraph', get_string('rename')),
        get_string('rename'),
        false,
        $attributes
    ));
}
if ($contenttype->can_delete($content)) {
    // Add the delete content item to the menu.
    $attributes = [
                'data-action' => 'deletecontent',
                'data-contentname' => $content->get_name(),
                'data-contentid' => $content->get_id(),
                'data-contextid' => $context->id,
            ];
    $actionmenu->add_secondary_action(new action_menu_link(
        new moodle_url('#'),
        new pix_icon('t/delete', get_string('delete')),
        get_string('delete'),
        false,
        $attributes
    ));
}

// Add the cog menu to the header.
$PAGE->add_header_action(html_writer::div(
    $OUTPUT->render($actionmenu),
    'd-print-none',
    ['id' => 'region-main-settings-menu']
));

echo $OUTPUT->header();

// If needed, display notifications.
if ($errormsg !== '' && get_string_manager()->string_exists($errormsg, 'core_contentbank')) {
    $errormsg = get_string($errormsg, 'core_contentbank');
    echo $OUTPUT->notification($errormsg);
} else if ($statusmsg !== '' && get_string_manager()->string_exists($statusmsg, 'core_contentbank')) {
    $statusmsg = get_string($statusmsg, 'core_contentbank');
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}
if ($contenttype->can_access()) {
    $viewcontent = new core_contentbank\output\viewcontent($contenttype, $content);
    echo $OUTPUT->render($viewcontent);
} else {
    $message = get_string('contenttypenoaccess', 'core_contentbank', $record->contenttype);
    echo $OUTPUT->notification($message, 'error');
}

echo $OUTPUT->footer();
