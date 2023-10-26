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

use core_contentbank\content;

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
    throw new \moodle_exception('unsupported', 'core_contentbank', $returnurl);
}

$title = get_string('contentbank');
\core_contentbank\helper::get_page_ready($context, $title, true);
if ($PAGE->course) {
    require_login($PAGE->course->id);
}

$cb = new \core_contentbank\contentbank();
$content = $cb->get_content_from_id($record->id);
$contenttype = $content->get_content_type_instance();

if (!$content->is_view_allowed()) {
    $cburl = new \moodle_url('/contentbank/index.php', ['contextid' => $context->id, 'errormsg' => 'notavailable']);
    redirect($cburl);
}

if ($context->contextlevel == CONTEXT_COURSECAT) {
    $PAGE->set_primary_active_tab('home');
}

$PAGE->set_url(new \moodle_url('/contentbank/view.php', ['id' => $id]));
if ($context->id == \context_system::instance()->id) {
    $PAGE->set_context(context_course::instance($context->id));
} else {
    $PAGE->set_context($context);
}
$PAGE->navbar->add($record->name);
$title .= ": ".$record->name;
$PAGE->set_title($title);
$PAGE->set_pagetype('contentbank');
$PAGE->set_pagelayout('incourse');
$PAGE->set_secondary_active_tab('contentbank');

echo $OUTPUT->header();

// If needed, display notifications.
if ($errormsg !== '' && get_string_manager()->string_exists($errormsg, 'core_contentbank')) {
    $errormsg = get_string($errormsg, 'core_contentbank');
    echo $OUTPUT->notification($errormsg);
} else if ($statusmsg !== '' && get_string_manager()->string_exists($statusmsg, 'core_contentbank')) {
    if ($statusmsg == 'contentvisibilitychanged') {
        switch ($content->get_visibility()) {
            case content::VISIBILITY_PUBLIC:
                $visibilitymsg = get_string('public', 'core_contentbank');
                break;
            case content::VISIBILITY_UNLISTED:
                $visibilitymsg = get_string('unlisted', 'core_contentbank');
                break;
            default:
                throw new \moodle_exception('contentvisibilitynotfound', 'error', $returnurl, $content->get_visibility());
                break;
        }
        $statusmsg = get_string($statusmsg, 'core_contentbank', $visibilitymsg);
    } else {
        $statusmsg = get_string($statusmsg, 'core_contentbank');
    }
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
