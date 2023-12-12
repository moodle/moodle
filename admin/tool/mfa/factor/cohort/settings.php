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
 * @package     factor_cohort
 * @author      Chris Pratt <tonyyeb@gmail.com>
 * @copyright   Chris Pratt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../../../../cohort/lib.php');

$enabled = new admin_setting_configcheckbox('factor_cohort/enabled',
    new lang_string('settings:enablefactor', 'tool_mfa'),
    new lang_string('settings:enablefactor_help', 'tool_mfa'), 0);
$enabled->set_updatedcallback(function () {
    \tool_mfa\manager::do_factor_action('cohort', get_config('factor_cohort', 'enabled') ? 'enable' : 'disable');
});
$settings->add($enabled);

$settings->add(new admin_setting_configtext('factor_cohort/weight',
    new lang_string('settings:weight', 'tool_mfa'),
    new lang_string('settings:weight_help', 'tool_mfa'), 100, PARAM_INT));

$cohorts = cohort_get_all_cohorts();
$choices = [];

foreach ($cohorts['cohorts'] as $cohort) {
    $choices[$cohort->id] = $cohort->name;
}

if (!empty($choices)) {
    $settings->add(new admin_setting_configmultiselect('factor_cohort/cohorts',
    new lang_string('settings:cohort', 'factor_cohort'),
    new lang_string('settings:cohort_help', 'factor_cohort'), [], $choices));
}
