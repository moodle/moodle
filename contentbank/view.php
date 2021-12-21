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
    print_error('unsupported', 'core_contentbank', $returnurl);
}

$title = get_string('contentbank');
\core_contentbank\helper::get_page_ready($context, $title, true);
if ($PAGE->course) {
    require_login($PAGE->course->id);
}

$cb = new \core_contentbank\contentbank();
$content = $cb->get_content_from_id($record->id);
$contenttype = $content->get_content_type_instance();
$pageheading = $record->name;

if (!$content->is_view_allowed()) {
    $cburl = new \moodle_url('/contentbank/index.php', ['contextid' => $context->id, 'errormsg' => 'notavailable']);
    redirect($cburl);
}

if ($content->get_visibility() == content::VISIBILITY_UNLISTED) {
    $pageheading = get_string('visibilitytitleunlisted', 'contentbank', $record->name);
}

$PAGE->set_url(new \moodle_url('/contentbank/view.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->navbar->add($record->name);
$PAGE->set_heading($pageheading);
$title .= ": ".$record->name;
$PAGE->set_title($title);
$PAGE->set_pagetype('contentbank');

// Create the cog menu with all the secondary actions, such as delete, rename...
$actionmenu = new action_menu();
$actionmenu->set_alignment(action_menu::TR, action_menu::BR);
if ($contenttype->can_manage($content)) {
    // Add the visibility item to the menu.
    switch($content->get_visibility()) {
        case content::VISIBILITY_UNLISTED:
            $visibilitylabel = get_string('visibilitysetpublic', 'core_contentbank');
            $newvisibility = content::VISIBILITY_PUBLIC;
            $visibilityicon = 't/hide';
            break;
        case content::VISIBILITY_PUBLIC:
            $visibilitylabel = get_string('visibilitysetunlisted', 'core_contentbank');
            $newvisibility = content::VISIBILITY_UNLISTED;
            $visibilityicon = 't/show';
            break;
        default:
            print_error('contentvisibilitynotfound', 'error', $returnurl, $content->get_visibility());
            break;
    }

    $attributes = [
        'data-action' => 'setcontentvisibility',
        'data-visibility' => $newvisibility,
        'data-contentid' => $content->get_id(),
    ];
    $actionmenu->add_secondary_action(new action_menu_link(
        new moodle_url('#'),
        new pix_icon($visibilityicon, $visibilitylabel),
        $visibilitylabel,
        false,
        $attributes
    ));

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

    if ($contenttype->can_upload()) {
        $actionmenu->add_secondary_action(new action_menu_link(
            new moodle_url('/contentbank/view.php', ['contextid' => $context->id, 'id' => $content->get_id()]),
            new pix_icon('i/upload', get_string('upload')),
            get_string('replacecontent', 'contentbank'),
            false,
            ['data-action' => 'upload']
        ));
        $PAGE->requires->js_call_amd(
            'core_contentbank/upload',
            'initModal',
            ['[data-action=upload]', \core_contentbank\form\upload_files::class, $context->id, $content->get_id()]
        );
    }
}
if ($contenttype->can_download($content)) {
    // Add the download content item to the menu.
    $actionmenu->add_secondary_action(new action_menu_link(
        new moodle_url($contenttype->get_download_url($content)),
        new pix_icon('t/download', get_string('download')),
        get_string('download'),
        false
    ));
}
if ($contenttype->can_delete($content)) {
    // Add the delete content item to the menu.
    $attributes = [
                'data-action' => 'deletecontent',
                'data-contentname' => $content->get_name(),
                'data-uses' => count($content->get_uses()),
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
    if ($statusmsg == 'contentvisibilitychanged') {
        switch ($content->get_visibility()) {
            case content::VISIBILITY_PUBLIC:
                $visibilitymsg = get_string('public', 'core_contentbank');
                break;
            case content::VISIBILITY_UNLISTED:
                $visibilitymsg = get_string('unlisted', 'core_contentbank');
                break;
            default:
                print_error('contentvisibilitynotfound', 'error', $returnurl, $content->get_visibility());
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
