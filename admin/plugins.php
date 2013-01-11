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

admin_externalpage_setup('pluginsoverview');
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$fetchremote = optional_param('fetchremote', false, PARAM_BOOL);

$pluginman = plugin_manager::instance();
$checker = available_update_checker::instance();

if ($fetchremote) {
    require_sesskey();
    $checker->fetch();
    redirect($PAGE->url);
}

$output = $PAGE->get_renderer('core', 'admin');
echo $output->plugin_management_page($pluginman, $checker);
