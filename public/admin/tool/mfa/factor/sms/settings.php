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
 * Settings for SMS MFA factor.
 *
 * @package     factor_sms
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Get the gateway records.
    $manager = \core\di::get(\core_sms\manager::class);
    $gatewayrecords = $manager->get_gateway_records(['enabled' => 1]);
    $smsconfigureurl = new moodle_url(
        '/sms/configure.php',
        [
            'returnurl' => new moodle_url(
                '/admin/settings.php',
                ['section' => 'factor_sms'],
            ),
        ],
    );
    $smsconfigureurl = $smsconfigureurl->out();

    $settings->add(
        new admin_setting_heading(
            'factor_sms/heading',
            '',
            new lang_string(
                'settings:heading',
                'factor_sms',
            ),
        ),
    );
    $settings->add(new admin_setting_heading('factor_sms/settings', new lang_string('settings', 'moodle'), ''));

    // Get available gateways, or link to gateway creation.
    $gateways = [0 => new lang_string('none')];
    if (count($gatewayrecords) > 0) {
        foreach ($gatewayrecords as $record) {
            $values = explode('\\', $record->gateway);
            $gatewayname = new lang_string('pluginname', $values[0]);
            $gateways[$record->id] = $record->name . ' (' . $gatewayname . ')';
        }
    } else {
        $notify = new \core\output\notification(
            get_string('settings:setupdesc', 'factor_sms', $smsconfigureurl),
            \core\output\notification::NOTIFY_WARNING
        );
        $settings->add(new admin_setting_heading('factor_sms/setupdesc', '', $OUTPUT->render($notify)));
    }

    $settings->add(
        new admin_setting_configselect(
            'factor_sms/smsgateway',
            new lang_string('settings:smsgateway', 'factor_sms'),
            new lang_string('settings:smsgateway_help', 'factor_sms', $smsconfigureurl),
            0,
            $gateways,
        ),
    );

    $enabled = new admin_setting_configcheckbox(
        'factor_sms/enabled',
        new lang_string('settings:enablefactor', 'tool_mfa'),
        new lang_string('settings:enablefactor_help', 'tool_mfa'),
        0,
    );
    $enabled->set_updatedcallback(function () {
        \tool_mfa\manager::do_factor_action(
            'sms',
            get_config('factor_sms', 'enabled') ? 'enable' : 'disable',
        );
    });
    $settings->add($enabled);

    $settings->add(
        new admin_setting_configtext(
            'factor_sms/weight',
            new lang_string('settings:weight', 'tool_mfa'),
            new lang_string('settings:weight_help', 'tool_mfa'),
            100,
            PARAM_INT,
        ),
    );

    $settings->add(
        new admin_setting_configduration(
            'factor_sms/duration',
            new lang_string('settings:duration', 'tool_mfa'),
            new lang_string('settings:duration_help', 'tool_mfa'),
            30 * MINSECS,
            MINSECS,
        ),
    );
}
