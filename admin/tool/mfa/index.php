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
 * MFA configuration page.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

require_login(null, false);
require_capability('moodle/site:config', context_system::instance());

$returnurl = get_local_referer(false);

$PAGE->set_url('/admin/tool/mfa/index.php');

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$factor = optional_param('factor', '', PARAM_ALPHANUMEXT);

if (empty($factor) || !\tool_mfa\plugininfo\factor::factor_exists($factor)) {
    throw new moodle_exception('factornotfound', 'tool_mfa', $returnurl, $factor);
}

if (empty($action) || !in_array($action, \tool_mfa\plugininfo\factor::get_factor_actions())) {
    throw new moodle_exception('actionnotfound', 'tool_mfa', $returnurl, $action);
}

require_sesskey();

$enabledfactors = [];
foreach (\tool_mfa\plugininfo\factor::get_enabled_factors() as $enabledfactor) {
    $enabledfactors[] = $enabledfactor->name;
}


switch ($action) {
    case 'disable':
        if (in_array($factor, $enabledfactors)) {
            \tool_mfa\manager::set_factor_config(['enabled' => 0], 'factor_' . $factor);
            \tool_mfa\manager::do_factor_action($factor, $action);

            \core\session\manager::gc(); // Remove stale sessions.
            core_plugin_manager::reset_caches();
        }
        break;

    case 'enable':
        if (!in_array($factor, $enabledfactors)) {
            \tool_mfa\manager::set_factor_config(['enabled' => 1], 'factor_' . $factor);
            \tool_mfa\manager::do_factor_action($factor, $action);

            \core\session\manager::gc(); // Remove stale sessions.
            core_plugin_manager::reset_caches();
        }
        break;

    case 'up':
    case 'down':
        \tool_mfa\manager::do_factor_action($factor, $action);

        \core\session\manager::gc(); // Remove stale sessions.
        core_plugin_manager::reset_caches();
        break;

    default:
        break;
}

redirect($returnurl);
