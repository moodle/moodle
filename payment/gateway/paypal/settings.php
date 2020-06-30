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
 * Settings for the PayPal payment gateway
 *
 * @package    pg_paypal
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('pg_paypal_settings', '', get_string('pluginname_desc', 'pg_paypal')));

    $settings->add(new admin_setting_configtext('pg_paypal/brandname', get_string('brandname', 'pg_paypal'),
            get_string('brandname', 'pg_paypal'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('pg_paypal/clientid', get_string('clientid', 'pg_paypal'),
            get_string('clientid_desc', 'pg_paypal'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('pg_paypal/secret', get_string('secret', 'pg_paypal'),
            get_string('secret_desc', 'pg_paypal'), '', PARAM_TEXT));

    $options = [
        'live' => get_string('live', 'pg_paypal'),
        'sandbox'  => get_string('sandbox', 'pg_paypal'),
    ];
    $settings->add(new admin_setting_configselect('pg_paypal/environment', get_string('environment', 'pg_paypal'),
            get_string('environment_desc', 'pg_paypal'), 'live', $options));

    \core_payment\helper::add_common_gateway_settings($settings, 'pg_paypal');
}
