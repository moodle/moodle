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
 * Allows admin to configure antiviruses.
 *
 * @package    core_antivirus
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$antivirus  = required_param('antivirus', PARAM_PLUGIN);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/antiviruses.php', array('action' => $action, 'antivirus' => $antivirus));
$PAGE->set_context(context_system::instance());

require_admin();

$returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=manageantiviruses";

// Get currently installed and enabled antivirus plugins.
$availableantiviruses = \core\antivirus\manager::get_available();
if (!empty($antivirus) and empty($availableantiviruses[$antivirus])) {
    redirect ($returnurl);
}

$activeantiviruses = explode(',', $CFG->antiviruses);
foreach ($activeantiviruses as $key => $active) {
    if (empty($availableantiviruses[$active])) {
        unset($activeantiviruses[$key]);
    }
}

if (!confirm_sesskey()) {
    redirect($returnurl);
}

switch ($action) {
    case 'disable':
        // Remove from enabled list.
        $key = array_search($antivirus, $activeantiviruses);
        unset($activeantiviruses[$key]);
        break;

    case 'enable':
        // Add to enabled list.
        if (!in_array($antivirus, $activeantiviruses)) {
            $activeantiviruses[] = $antivirus;
            $activeantiviruses = array_unique($activeantiviruses);
        }
        break;

    case 'down':
        $key = array_search($antivirus, $activeantiviruses);
        // Check auth plugin is valid.
        if ($key !== false) {
            // Move down the list.
            if ($key < (count($activeantiviruses) - 1)) {
                $fsave = $activeantiviruses[$key];
                $activeantiviruses[$key] = $activeantiviruses[$key + 1];
                $activeantiviruses[$key + 1] = $fsave;
            }
        }
        break;

    case 'up':
        $key = array_search($antivirus, $activeantiviruses);
        // Check auth is valid.
        if ($key !== false) {
            // Move up the list.
            if ($key >= 1) {
                $fsave = $activeantiviruses[$key];
                $activeantiviruses[$key] = $activeantiviruses[$key - 1];
                $activeantiviruses[$key - 1] = $fsave;
            }
        }
        break;

    default:
        break;
}

set_config('antiviruses', implode(',', $activeantiviruses));
core_plugin_manager::reset_caches();

redirect ($returnurl);