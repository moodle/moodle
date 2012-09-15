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
 * TinyMCE subplugin management.
 *
 * @package   editor_tinymce
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$delete  = optional_param('delete', '', PARAM_PLUGIN);
$confirm = optional_param('confirm', '', PARAM_BOOL);
$disable = optional_param('disable', '', PARAM_PLUGIN);
$enable  = optional_param('enable', '', PARAM_PLUGIN);
$return  = optional_param('return', 'overview', PARAM_ALPHA);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/editor/tinymce/subplugins.php', array('delete'=>$delete));

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

if ($return === 'settings') {
    $returnurl = new moodle_url('/admin/settings.php', array('section'=>'editorsettingstinymce'));
} else {
    $returnurl = new moodle_url('/admin/plugins.php');
}

if ($delete) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'editor_tinymce'));

    if (!$confirm) {
        if (get_string_manager()->string_exists('pluginname', 'tinymce_' . $delete)) {
            $strpluginname = get_string('pluginname', 'tinymce_' . $delete);
        } else {
            $strpluginname = $delete;
        }
        echo $OUTPUT->confirm(get_string('subplugindeleteconfirm', 'editor_tinymce', $strpluginname),
            new moodle_url($PAGE->url, array('delete' => $delete, 'confirm' => 1, 'return'=>$return)),
            $returnurl);
        echo $OUTPUT->footer();
        die();

    } else {
        uninstall_plugin('tinymce', $delete);
        $a = new stdclass();
        $a->name = $delete;
        $pluginlocation = get_plugin_types();
        $a->directory = $pluginlocation['tinymce'] . '/' . $delete;
        echo $OUTPUT->notification(get_string('plugindeletefiles', '', $a), 'notifysuccess');
        echo $OUTPUT->continue_button($returnurl);
        echo $OUTPUT->footer();
        die();
    }

} else {
    $disabled = array();
    $disabledsubplugins = get_config('editor_tinymce', 'disabledsubplugins');
    if ($disabledsubplugins) {
        $disabledsubplugins = explode(',', $disabledsubplugins);
        foreach ($disabledsubplugins as $sp) {
            $sp = trim($sp);
            if ($sp !== '') {
                $disabled[$sp] = $sp;
            }
        }
    }

    if ($disable) {
        $disabled[$disable] = $disable;
    } else if ($enable) {
        unset($disabled[$enable]);
    }

    set_config('disabledsubplugins', implode(',', $disabled), 'editor_tinymce');
}

redirect($returnurl);
