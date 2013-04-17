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
 * Allows the admin to enable, disable and uninstall course formats
 *
 * @package    core_admin
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/pluginlib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$formatname   = required_param('format', PARAM_PLUGIN);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$syscontext = context_system::instance();
$PAGE->set_url('/admin/courseformats.php');
$PAGE->set_context($syscontext);

require_login();
require_capability('moodle/site:config', $syscontext);
require_sesskey();

$return = new moodle_url('/admin/settings.php', array('section' => 'manageformats'));

$formatplugins = plugin_manager::instance()->get_plugins_of_type('format');
$sortorder = array_flip(array_keys($formatplugins));

if (!isset($formatplugins[$formatname])) {
    print_error('courseformatnotfound', 'error', $return, $formatname);
}

switch ($action) {
    case 'disable':
        if ($formatplugins[$formatname]->is_enabled()) {
            if (get_config('moodlecourse', 'format') === $formatname) {
                print_error('cannotdisableformat', 'error', $return);
            }
            set_config('disabled', 1, 'format_'. $formatname);
        }
        break;
    case 'enable':
        if (!$formatplugins[$formatname]->is_enabled()) {
            unset_config('disabled', 'format_'. $formatname);
        }
        break;
    case 'up':
        if ($sortorder[$formatname]) {
            $currentindex = $sortorder[$formatname];
            $seq = array_keys($formatplugins);
            $seq[$currentindex] = $seq[$currentindex-1];
            $seq[$currentindex-1] = $formatname;
            set_config('format_plugins_sortorder', implode(',', $seq));
        }
        break;
    case 'down':
        if ($sortorder[$formatname] < count($sortorder)-1) {
            $currentindex = $sortorder[$formatname];
            $seq = array_keys($formatplugins);
            $seq[$currentindex] = $seq[$currentindex+1];
            $seq[$currentindex+1] = $formatname;
            set_config('format_plugins_sortorder', implode(',', $seq));
        }
        break;
    case 'uninstall':
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('courseformats', 'moodle'));

        $coursecount = $DB->count_records('course', array('format' => $formatname));
        if ($coursecount) {
            // Check that default format is set. It will be used to convert courses
            // using this format
            $defaultformat = get_config('moodlecourse', 'format');
            $defaultformat = $formatplugins[get_config('moodlecourse', 'format')];
            if (!$defaultformat) {
                echo $OUTPUT->error_text(get_string('defaultformatnotset', 'admin'));
                echo $OUTPUT->footer();
                exit;
            }
        }

        $format = $formatplugins[$formatname];
        $deleteurl = $format->get_uninstall_url();
        if (!$deleteurl) {
            // somebody was trying to cheat and type non-existing link
            echo $OUTPUT->error_text(get_string('cannotuninstall', 'admin', $format->displayname));
            echo $OUTPUT->footer();
            exit;
        }

        if (!$confirm) {
            if ($coursecount) {
                $message = get_string('formatuninstallwithcourses', 'admin',
                        (object)array('count' => $coursecount, 'format' => $format->displayname,
                            'defaultformat' => $defaultformat->displayname));
            } else {
                $message = get_string('formatuninstallconfirm', 'admin', $format->displayname);
            }
            $deleteurl->param('confirm', 1);
            echo $OUTPUT->confirm($message, $deleteurl, $return);
        } else {
            $a = new stdClass();
            $a->plugin = $format->displayname;
            $a->directory = $format->rootdir;
            uninstall_plugin('format', $formatname);
            echo $OUTPUT->notification(get_string('formatuninstalled', 'admin', $a), 'notifysuccess');
            echo $OUTPUT->continue_button($return);
        }

        echo $OUTPUT->footer();
        exit;
}
redirect($return);
