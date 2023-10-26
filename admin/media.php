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
 * @subpackage media
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$media   = required_param('media', PARAM_PLUGIN);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/media.php');
$PAGE->set_context(context_system::instance());

require_admin();
require_sesskey();

$plugins = core_plugin_manager::instance()->get_plugins_of_type('media');
$sortorder = array_values(\core\plugininfo\media::get_enabled_plugins());

$return = new moodle_url('/admin/settings.php', array('section' => 'managemediaplayers'));

if (!array_key_exists($media, $plugins)) {
    redirect($return);
}

switch ($action) {
    case 'disable':
        $class = \core_plugin_manager::resolve_plugininfo_class('media');
        $class::enable_plugin($media, false);
        break;

    case 'enable':
        $class = \core_plugin_manager::resolve_plugininfo_class('media');
        $class::enable_plugin($media, true);
        break;

    case 'up':
        if (($pos = array_search($media, $sortorder)) > 0) {
            $tmp = $sortorder[$pos - 1];
            $sortorder[$pos - 1] = $sortorder[$pos];
            $sortorder[$pos] = $tmp;
            \core\plugininfo\media::set_enabled_plugins($sortorder);
        }
        break;

    case 'down':
        if ((($pos = array_search($media, $sortorder)) !== false) && ($pos < count($sortorder) - 1)) {
            $tmp = $sortorder[$pos + 1];
            $sortorder[$pos + 1] = $sortorder[$pos];
            $sortorder[$pos] = $tmp;
            \core\plugininfo\media::set_enabled_plugins($sortorder);
        }
        break;
}

redirect($return);
