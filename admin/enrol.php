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
 * Enrol config manipulation script.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$enrol   = required_param('enrol', PARAM_PLUGIN);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$migrate = optional_param('migrate', 0, PARAM_BOOL);

$PAGE->set_url('/admin/enrol.php');
$PAGE->set_context(context_system::instance());

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

$enabled = enrol_get_plugins(true);
$all     = enrol_get_plugins(false);

$return = new moodle_url('/admin/settings.php', array('section'=>'manageenrols'));

$syscontext = context_system::instance();

switch ($action) {
    case 'disable':
        unset($enabled[$enrol]);
        set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));
        $syscontext->mark_dirty(); // resets all enrol caches
        break;

    case 'enable':
        if (!isset($all[$enrol])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled[] = $enrol;
        set_config('enrol_plugins_enabled', implode(',', $enabled));
        $syscontext->mark_dirty(); // resets all enrol caches
        break;

    case 'up':
        if (!isset($enabled[$enrol])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled = array_flip($enabled);
        $current = $enabled[$enrol];
        if ($current == 0) {
            break; //already at the top
        }
        $enabled = array_flip($enabled);
        $enabled[$current] = $enabled[$current - 1];
        $enabled[$current - 1] = $enrol;
        set_config('enrol_plugins_enabled', implode(',', $enabled));
        break;

    case 'down':
        if (!isset($enabled[$enrol])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled = array_flip($enabled);
        $current = $enabled[$enrol];
        if ($current == count($enabled) - 1) {
            break; //already at the end
        }
        $enabled = array_flip($enabled);
        $enabled[$current] = $enabled[$current + 1];
        $enabled[$current + 1] = $enrol;
        set_config('enrol_plugins_enabled', implode(',', $enabled));
        break;

    case 'uninstall':
        if (get_string_manager()->string_exists('pluginname', 'enrol_'.$enrol)) {
            $strplugin = get_string('pluginname', 'enrol_'.$enrol);
        } else {
            $strplugin = $enrol;
        }

        $PAGE->set_title($strplugin);
        echo $OUTPUT->header();

        if (!$confirm) {
            echo $OUTPUT->heading(get_string('enrolments', 'enrol'));

            $deleteurl = new moodle_url('/admin/enrol.php', array('action'=>'uninstall', 'enrol'=>$enrol, 'sesskey'=>sesskey(), 'confirm'=>1, 'migrate'=>0));
            $migrateurl = new moodle_url('/admin/enrol.php', array('action'=>'uninstall', 'enrol'=>$enrol, 'sesskey'=>sesskey(), 'confirm'=>1, 'migrate'=>1));

            $migrate = new single_button($migrateurl, get_string('uninstallmigrate', 'enrol'));
            $delete = new single_button($deleteurl, get_string('uninstalldelete', 'enrol'));
            $cancel = new single_button($return, get_string('cancel'), 'get');

            $buttons = $OUTPUT->render($delete) . $OUTPUT->render($cancel);
            if ($enrol !== 'manual') {
                $buttons = $OUTPUT->render($migrate) . $buttons;
            }

            echo $OUTPUT->box_start('generalbox', 'notice');
            echo html_writer::tag('p', markdown_to_html(get_string('uninstallconfirm', 'enrol', $strplugin)));
            echo html_writer::tag('div', $buttons, array('class' => 'buttons'));
            echo $OUTPUT->box_end();

            echo $OUTPUT->footer();
            exit;

        } else {
            // This may take a long time.
            set_time_limit(0);

            // Disable plugin to prevent concurrent cron execution.
            unset($enabled[$enrol]);
            set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));

            if ($migrate) {
                echo $OUTPUT->heading(get_string('uninstallmigrating', 'enrol', 'enrol_'.$enrol));

                require_once("$CFG->dirroot/enrol/manual/locallib.php");
                enrol_manual_migrate_plugin_enrolments($enrol);

                echo $OUTPUT->notification(get_string('success'), 'notifysuccess');
            }

            // Delete everything!!
            uninstall_plugin('enrol', $enrol);
            $syscontext->mark_dirty(); // Resets all enrol caches.

            $a = new stdClass();
            $a->plugin = $strplugin;
            $a->directory = "$CFG->dirroot/enrol/$enrol";
            echo $OUTPUT->notification(get_string('uninstalldeletefiles', 'enrol', $a), 'notifysuccess');
            echo $OUTPUT->continue_button($return);
            echo $OUTPUT->footer();
            exit;
        }
}


redirect($return);