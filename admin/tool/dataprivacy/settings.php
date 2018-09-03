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
 * Adds Data privacy-related settings.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 onwards Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $privacysettings = $ADMIN->locate('privacysettings');

    if ($ADMIN->fulltree) {
        // Contact data protection officer. Disabled by default.
        $privacysettings->add(new admin_setting_configcheckbox('tool_dataprivacy/contactdataprotectionofficer',
                new lang_string('contactdataprotectionofficer', 'tool_dataprivacy'),
                new lang_string('contactdataprotectionofficer_desc', 'tool_dataprivacy'), 0)
        );

        // Set days approved data requests will be accessible. 1 week default.
        $privacysettings->add(new admin_setting_configduration('tool_dataprivacy/privacyrequestexpiry',
                new lang_string('privacyrequestexpiry', 'tool_dataprivacy'),
                new lang_string('privacyrequestexpiry_desc', 'tool_dataprivacy'),
                WEEKSECS, 1));

        // Fetch roles that are assignable.
        $assignableroles = get_assignable_roles(context_system::instance());

        // Fetch roles that have the capability to manage data requests.
        $capableroles = get_roles_with_capability('tool/dataprivacy:managedatarequests');

        // Role(s) that map to the Data Protection Officer role. These are assignable roles with the capability to
        // manage data requests.
        $roles = [];
        foreach ($capableroles as $key => $role) {
            if (array_key_exists($key, $assignableroles)) {
                $roles[$key] = $assignableroles[$key];
            }
        }
        if (!empty($roles)) {
            $privacysettings->add(new admin_setting_configmulticheckbox('tool_dataprivacy/dporoles',
                    new lang_string('dporolemapping', 'tool_dataprivacy'),
                    new lang_string('dporolemapping_desc', 'tool_dataprivacy'), null, $roles)
            );
        }
    }
}

// Restrict config links to the DPO.
if (tool_dataprivacy\api::is_site_dpo($USER->id)) {
    // Link that leads to the data requests management page.
    $ADMIN->add('privacy', new admin_externalpage('datarequests', get_string('datarequests', 'tool_dataprivacy'),
        new moodle_url('/admin/tool/dataprivacy/datarequests.php'), 'tool/dataprivacy:managedatarequests')
    );

    // Link that leads to the data registry management page.
    $ADMIN->add('privacy', new admin_externalpage('dataregistry', get_string('dataregistry', 'tool_dataprivacy'),
        new moodle_url('/admin/tool/dataprivacy/dataregistry.php'), 'tool/dataprivacy:managedataregistry')
    );

    // Link that leads to the review page of expired contexts that are up for deletion.
    $ADMIN->add('privacy', new admin_externalpage('datadeletion', get_string('datadeletion', 'tool_dataprivacy'),
            new moodle_url('/admin/tool/dataprivacy/datadeletion.php'), 'tool/dataprivacy:managedataregistry')
    );

    // Link that leads to the other data registry management page.
    $ADMIN->add('privacy', new admin_externalpage('pluginregistry', get_string('pluginregistry', 'tool_dataprivacy'),
        new moodle_url('/admin/tool/dataprivacy/pluginregistry.php'), 'tool/dataprivacy:managedataregistry')
    );
}
