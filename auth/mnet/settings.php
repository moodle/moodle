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
 * Admin settings and defaults.
 *
 * @package    auth_mnet
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/lib/outputlib.php');

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_mnet/pluginname', '',
            new lang_string('auth_mnetdescription', 'auth_mnet')));

    // RPC Timeout.
    $settings->add(new admin_setting_configtext('auth_mnet/rpc_negotiation_timeout',
            get_string('rpc_negotiation_timeout', 'auth_mnet'),
            get_string('auth_mnet_rpc_negotiation_timeout', 'auth_mnet'), '30', PARAM_INT));

}
