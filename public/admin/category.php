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
 * This file is used to display a categories sub categories, external pages, and settings.
 *
 * @since      Moodle 2.3
 * @package    admin
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\exception\moodle_exception;
use core\output\html_writer;
use core\url;

require_once('../config.php');
require_once($CFG->libdir . '/adminlib.php');

$category = required_param('category', PARAM_SAFEDIR);
$return = optional_param('return', '', PARAM_ALPHA);
$adminediting = optional_param('adminedit', -1, PARAM_BOOL);

require_login(0, false);
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/admin/category.php', ['category' => $category]);
$PAGE->set_pagetype("admin-setting-{$category}");
$PAGE->set_pagelayout('admin');
$PAGE->navigation->clear_cache();

$adminroot = admin_get_root(); // We need all settings.
$settingspage = $adminroot->locate($category, true);

if (empty($settingspage) || !($settingspage instanceof \core_admin\setting\tree\category)) {
    throw new moodle_exception('categoryerror', 'error', "$CFG->wwwroot/admin/");
}

if (!($settingspage->check_access())) {
    throw new moodle_exception('accessdenied', 'admin');
}

$hassiteconfig = has_capability('moodle/site:config', $PAGE->context);
if ($hassiteconfig) {
    $PAGE->add_header_action($OUTPUT->render_from_template('core_admin/header_search_input', [
        'action' => new url('/admin/search.php'),
    ]));
}

$statusmsg = '';
$errormsg  = '';

if (($data = data_submitted()) && confirm_sesskey()) {
    $count = admin_write_settings($data);
    if (empty($adminroot->errors)) {
        // No errors. Did we change any setting?  If so, then indicate success.
        if ($count) {
            $statusmsg = get_string('changessaved');
        } else {
            switch ($return) {
                case 'site':
                    redirect("{$CFG->wwwroot}/");
                    break;
                case 'admin':
                    redirect("{$CFG->wwwroot}/admin/");
                    break;
            }
        }
    } else {
        $errormsg = get_string('errorwithsettings', 'admin');
        $firsterror = reset($adminroot->errors);
    }
    $settingspage = $adminroot->locate($category, true);
}

if ($PAGE->user_allowed_editing() && $adminediting != -1) {
    $USER->editing = $adminediting;
}
$buttons = null;
if ($PAGE->user_allowed_editing() && !$PAGE->theme->haseditswitch) {
    $url = clone($PAGE->url);
    if ($PAGE->user_is_editing()) {
        $caption = get_string('blockseditoff');
        $url->param('adminedit', 'off');
    } else {
        $caption = get_string('blocksediton');
        $url->param('adminedit', 'on');
    }
    $buttons = $OUTPUT->single_button($url, $caption, 'get');
}

$savebutton = false;
$outputhtml = '';
foreach ($settingspage->children as $childpage) {
    if ($childpage->is_hidden() || !$childpage->check_access()) {
        continue;
    }
    if ($childpage instanceof \core_admin\setting\tree\externalpage) {
        $outputhtml .= $OUTPUT->heading(html_writer::link($childpage->url, $childpage->visiblename), 3);
    } else if ($childpage instanceof \core_admin\setting\settingpage\settingpage) {
        $outputhtml .= $OUTPUT->heading(html_writer::link(
            new url(
                '/admin/settings.php',
                [
                    'section' => $childpage->name,
                ],
            ),
            $childpage->visiblename
        ), 3);
        // If its a settings page and has settings lets display them.
        if (!empty($childpage->settings)) {
            $outputhtml .= html_writer::start_tag('fieldset', ['class' => 'adminsettings']);
            foreach ($childpage->settings as $setting) {
                if (empty($setting->nosave)) {
                    $savebutton = true;
                }
                $fullname = $setting->get_full_name();
                if (array_key_exists($fullname, $adminroot->errors)) {
                    $data = $adminroot->errors[$fullname]->data;
                } else {
                    $data = $setting->get_setting();
                }
                $outputhtml .= html_writer::tag('div', '<!-- -->', ['class' => 'clearer']);
                $outputhtml .= $setting->output_html($data);
                if ($childpage->has_dependencies()) {
                    $context = ['dependencies' => json_encode($childpage->get_dependencies_for_javascript())];
                    echo $OUTPUT->render_from_template('core_admin/settings_showhide', $context);
                }
            }
            $outputhtml .= html_writer::end_tag('fieldset');
        }
    } else if ($childpage instanceof \core_admin\setting\tree\category) {
        $outputhtml .= $OUTPUT->heading(html_writer::link(
            new url(
                '/admin/category.php',
                [
                    'category' => $childpage->name,
                ],
            ),
            get_string('admincategory', 'admin', $childpage->visiblename),
        ), 3);
    }
}
if ($savebutton) {
    $outputhtml .= html_writer::start_tag('div', ['class' => 'form-buttons']);
    $outputhtml .= html_writer::empty_tag('input', [
        'class' => 'btn btn-primary form-submit',
        'type' => 'submit',
        'value' => get_string('savechanges', 'admin'),
    ]);
    $outputhtml .= html_writer::end_tag('div');
}

$PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $settingspage->visiblepath));
$PAGE->set_heading($SITE->fullname);
if ($buttons) {
    $PAGE->set_button($buttons);
}

echo $OUTPUT->header();

if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);
} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

echo $OUTPUT->heading(get_string('admincategory', 'admin', $settingspage->visiblename), 2);

echo html_writer::start_tag('form', ['action' => '', 'method' => 'post', 'id' => 'adminsettings']);
echo html_writer::start_tag('div');
echo html_writer::input_hidden_params(new url($PAGE->url, ['sesskey' => sesskey(), 'return' => $return]));
echo html_writer::end_tag('div');
echo html_writer::start_tag('fieldset');
echo html_writer::tag('div', '<!-- -->', ['class' => 'clearer']);
echo $outputhtml;
echo html_writer::end_tag('fieldset');
echo html_writer::end_tag('form');

// Add the form change checker.
$PAGE->requires->js_call_amd('core_form/changechecker', 'watchFormById', ['adminsettings']);

echo $OUTPUT->footer();
