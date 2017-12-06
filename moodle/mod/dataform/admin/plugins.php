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
 * Dataform sub-plugins management.
 *
 * @package    mod_dataform
 * @copyright  2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$action = required_param('action', PARAM_ALPHANUMEXT);
$plugin = required_param('plugin', PARAM_PLUGIN);

$PAGE->set_url('/mod/dataform/admin/plugins.php');
$PAGE->set_context(context_system::instance());

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

list($type, $name) = explode('_', $plugin, 2);

$all = \core_component::get_plugin_list($type);
$enabled = get_config('mod_dataform', "enabled_$type");
if (!$enabled) {
    $enabled = array();
} else {
    $enabled = array_flip(explode(',', $enabled));
}

$return = new moodle_url('/admin/settings.php', array('section' => "manage$type"));

$syscontext = context_system::instance();

switch ($action) {
    case 'disable':
        unset($enabled[$name]);
        set_config("enabled_$type", implode(',', array_keys($enabled)), 'mod_dataform');
        break;

    case 'enable':
        if (!isset($all[$name])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled[] = $name;
        set_config("enabled_$type", implode(',', $enabled), 'mod_dataform');
        break;

    case 'up':
        if (!isset($enabled[$name])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled = array_flip($enabled);
        $current = $enabled[$name];
        if ($current == 0) {
            break; // Already at the top.
        }
        $enabled = array_flip($enabled);
        $enabled[$current] = $enabled[$current - 1];
        $enabled[$current - 1] = $name;
        set_config("enabled_$type", implode(',', $enabled), 'mod_dataform');
        break;

    case 'down':
        if (!isset($enabled[$name])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled = array_flip($enabled);
        $current = $enabled[$name];
        if ($current == count($enabled) - 1) {
            break; // Already at the end.
        }
        $enabled = array_flip($enabled);
        $enabled[$current] = $enabled[$current + 1];
        $enabled[$current + 1] = $name;
        set_config("enabled_$type", implode(',', $enabled), 'mod_dataform');
        break;
}

redirect($return);
