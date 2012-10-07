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
 * UI for general plugins management
 *
 * @package    core
 * @subpackage admin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/pluginlib.php');

require_capability('moodle/site:config', context_system::instance());
admin_externalpage_setup('pluginsoverview');

$fetchremote = optional_param('fetchremote', false, PARAM_BOOL);
$updatesonly = optional_param('updatesonly', false, PARAM_BOOL);
$contribonly = optional_param('contribonly', false, PARAM_BOOL);

$pluginman = plugin_manager::instance();
$checker = available_update_checker::instance();

// Filtering options.
$options = array(
    'updatesonly' => $updatesonly,
    'contribonly' => $contribonly,
);

if ($fetchremote) {
    require_sesskey();
    $checker->fetch();
    redirect(new moodle_url($PAGE->url, $options));
}

$output = $PAGE->get_renderer('core', 'admin');

$deployer = available_update_deployer::instance();
if ($deployer->enabled()) {
    $myurl = new moodle_url($PAGE->url, array('updatesonly' => $updatesonly, 'contribonly' => $contribonly));
    $deployer->initialize($myurl, $myurl);

    $deploydata = $deployer->submitted_data();
    if (!empty($deploydata)) {
        echo $output->upgrade_plugin_confirm_deploy_page($deployer, $deploydata);
        die();
    }
}

echo $output->plugin_management_page($pluginman, $checker, $options);
