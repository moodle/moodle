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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG, $DB;

if (block_quickmail\migrator\migrator::old_tables_exist()) {
    $msp = get_string('pluginname', 'block_quickmail') . ' ' . get_string('migrate', 'block_quickmail');
    $ADMIN->add('blocksettings', new admin_externalpage('blockquickmail', $msp, new moodle_url('/blocks/quickmail/migrate.php')));
}

if ($ADMIN->fulltree) {
    $nevernooryesoptions = [
        -1 => get_string('never'),
        0 => get_string('no'),
        1 => get_string('yes')
    ];

    $nooryesoptions = [
        0 => get_string('no'),
        1 => get_string('yes')
    ];

    $noyesorforceoptions = [
        0 => get_string('no'),
        1 => get_string('yes'),
        2 => get_string('force'),
    ];

    // Allow students to send messages?
    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_allowstudents',
            block_quickmail_string::get('allowstudents'),
            block_quickmail_string::get('allowstudents_desc'),
            0, // Default.
            $nevernooryesoptions
        )
    );

    // Role selection.
    // Get all roles.
    $roles = $DB->get_records('role', null, 'sortorder ASC');

    // Set default role selections by shortname.
    $defaultrolenames = [
        'editingteacher',
        'teacher',
        'student'
    ];

    // Get actual default roles.
    $defaultroleskeys = array_keys(array_filter($roles, function ($role) use ($defaultrolenames) {
        return in_array($role->shortname, $defaultrolenames);
    }));

    // Build a $value=>$label array of options.
    $blockquickmailroleselectionoptions = array_map(function ($role) {
        if ($role->name == '') {
            return $role->shortname;
        } else {
            return $role->name;
        }
    }, $roles);

    $settings->add(
        new admin_setting_configmultiselect(
            'block_quickmail_roleselection',
            block_quickmail_string::get('selectable_roles'),
            block_quickmail_string::get('selectable_roles_desc'),
            $defaultroleskeys, // Default.
            $blockquickmailroleselectionoptions
        )
    );

    // Send messages as background tasks.
    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_send_as_tasks',
            block_quickmail_string::get('send_as_tasks'),
            block_quickmail_string::get('send_as_tasks_help'),
            1,  // Default.
            $nooryesoptions
        )
    );

    // Send now recipient threshold.
    $settings->add(
        new admin_setting_configtext(
            'block_quickmail_send_now_threshold',
            block_quickmail_string::get('send_now_threshold'),
            block_quickmail_string::get('send_now_threshold_desc'),
            50 // Default.
        )
    );

    // Sender receives a copy?
    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_receipt',
            block_quickmail_string::get('receipt'),
            block_quickmail_string::get('receipt_help'),
            0,  // Default.
            $nooryesoptions
        )
    );

    // Allow sender to CC mentors of recipients?
    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_allow_mentor_copy',
            block_quickmail_string::get('allow_mentor_copy'),
            block_quickmail_string::get('allow_mentor_copy_help'),
            0,  // Default.
            $noyesorforceoptions
        )
    );

    // Email profile fields.
    if (block_quickmail_plugin::get_user_profile_field_array()) {
        $settings->add(
            new admin_setting_configmultiselect(
                'block_quickmail_email_profile_fields',
                block_quickmail_string::get('email_profile_fields'),
                block_quickmail_string::get('email_profile_fields_desc'),
                [], // Default.
                block_quickmail_plugin::get_user_profile_field_array()
            )
        );
    }

    // Subject prepend options.
    $blockquickmailprependclassoptions = [
        0 => get_string('none'),
        'idnumber' => get_string('idnumber'),
        'shortname' => get_string('shortnamecourse'),
        'fullname' => get_string('fullname')
    ];

    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_prepend_class',
            block_quickmail_string::get('prepend_class'),
            block_quickmail_string::get('prepend_class_desc'),
            0,  // Default.
            $blockquickmailprependclassoptions
        )
    );

    // FERPA options.
    $blockquickmailferpaoptions = [
        'strictferpa' => block_quickmail_string::get('strictferpa'),
        'courseferpa' => block_quickmail_string::get('courseferpa'),
        'noferpa' => block_quickmail_string::get('noferpa')
    ];

    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_ferpa',
            block_quickmail_string::get('ferpa'),
            block_quickmail_string::get('ferpa_desc'),
            'strictferpa',  // Default.
            $blockquickmailferpaoptions
        )
    );

    // Attachment download options.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_quickmail_downloads',
            block_quickmail_string::get('downloads'),
            block_quickmail_string::get('downloads_desc'),
            1  // Default.
        )
    );

    // Allow additional external emails to be sent to?
    $settings->add(
        new admin_setting_configcheckbox(
            'block_quickmail_additionalemail',
            block_quickmail_string::get('additionalemail'),
            block_quickmail_string::get('additionalemail_desc'),
            0   // Default.
        )
    );

    // Messaging channel options.
    $blockquickmailmessagetypesavailableoptions = [
        'all' => block_quickmail_string::get('message_type_available_all'),
        'email' => block_quickmail_string::get('message_type_available_email')
    ];

    // Allow messaging as an option only if messaging is enabled.
    if ( ! empty($CFG->messaging)) {
        $blockquickmailmessagetypesavailableoptions['message'] = block_quickmail_string::get('message_type_available_message');
    }

    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_message_types_available',
            block_quickmail_string::get('message_types_available'),
            block_quickmail_string::get('message_types_available_desc'),
            'all',  // Default.
            $blockquickmailmessagetypesavailableoptions
        )
    );

    // Enable notifications?
    $settings->add(
        new admin_setting_configselect(
            'block_quickmail_notifications_enabled',
            block_quickmail_string::get('notifications_enabled'),
            block_quickmail_string::get('notifications_enabled_desc'),
            0,  // Default.
            $nooryesoptions
        )
    );

    // Migration chunk size.
    $settings->add(
        new admin_setting_configtext(
            'block_quickmail_migration_chunk_size',
            block_quickmail_string::get('migration_chunk_size'),
            block_quickmail_string::get('migration_chunk_size_desc'),
            1000 // Default.
        )
    );

    // Allow additional external emaili addresses to be used.
    $settings->add(
        new admin_setting_configcheckbox(
            'altsendfrom',
            block_quickmail_string::get('altsendfrom'),
            block_quickmail_string::get('altsendfrom_desc'),
            0   // Default.
        )
    );

    // Frozen Context Settings.
    $settings->add(
        new admin_setting_heading(
            'block_quickmail_context_freezing_readonly_access',
            block_quickmail_string::get('block_quickmail_context_freezing_readonly_access_title'),
            ''
        )
    );
    $settings->add(
        new admin_setting_configmultiselect(
            'block_quickmail_frozen_readonly',
            block_quickmail_string::get('selectable_roles_readonly'),
            block_quickmail_string::get('selectable_roles_readonly_desc'),
            $defaultroleskeys, // Default.
            $blockquickmailroleselectionoptions
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'block_quickmail_frozen_readonly_pages',
            block_quickmail_string::get('frozen_readonly_pages'),
            block_quickmail_string::get('frozen_readonly_pages_desc'),
            'qm,sent,notifications,signatures'
        )
    );

    // Miscellaneous Stuffs.
    $settings->add(
        new admin_setting_heading(
            'misc_settings_heading',
            block_quickmail_string::get('misc_settings_heading_title'),
            ''
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'block_quickmail_misc_allow_student_sendall',
            block_quickmail_string::get('misc_settings_allow_student_sendall_title'),
            block_quickmail_string::get('misc_settings_allow_student_sendall_desc'),
            0   // Default.
        )
    );
}
