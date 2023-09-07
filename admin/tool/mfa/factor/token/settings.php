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
 * Settings
 *
 * @package     factor_token
 * @subpackage  tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$enabled = new admin_setting_configcheckbox('factor_token/enabled',
    new lang_string('settings:enablefactor', 'tool_mfa'),
    new lang_string('settings:enablefactor_help', 'tool_mfa'), 0);
$enabled->set_updatedcallback(function () {
    \tool_mfa\manager::do_factor_action('token', get_config('factor_token', 'enabled') ? 'enable' : 'disable');
});
$settings->add($enabled);

$settings->add(new admin_setting_configtext('factor_token/weight',
    new lang_string('settings:weight', 'tool_mfa'),
    new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));

$settings->add(new admin_setting_configduration('factor_token/expiry',
    new lang_string('settings:expiry', 'factor_token'),
    new lang_string('settings:expiry_help', 'factor_token'), DAYSECS));

$settings->add(new admin_setting_configcheckbox('factor_token/expireovernight',
    new lang_string('settings:expireovernight', 'factor_token'),
    new lang_string('settings:expireovernight_help', 'factor_token'), 1));
