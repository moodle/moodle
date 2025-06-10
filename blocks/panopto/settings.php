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
 * the main config settings for the Panopto block
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../config.php');
}
require_once(dirname(__FILE__) . '/classes/admin/trim_configtext.php');
require_once(dirname(__FILE__) . '/lib/panopto_data.php');

$numservers = get_config('block_panopto', 'server_number');
$numservers = isset($numservers) ? $numservers : 0;

// Increment numservers by 1 to take into account starting at 0.
++$numservers;

$targetserverarray = panopto_get_configured_panopto_servers();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('block_panopto/panopto_server_config',
            get_string('block_global_panopto_server_config', 'block_panopto'),
            ''));

    $settings->add(
        new admin_setting_configselect(
            'block_panopto/server_number',
            get_string('block_panopto_server_number_name', 'block_panopto'),
            get_string('block_panopto_server_number_desc', 'block_panopto'),
            0,
            range(1, 30, 1)
        )
    );
    $settings->add(
        new  admin_setting_configtext_trimmed(
            'block_panopto/instance_name',
            get_string('block_global_instance_name', 'block_panopto'),
            get_string('block_global_instance_desc', 'block_panopto'),
            'moodle',
            PARAM_TEXT
        )
    );

    for ($serverwalker = 1; $serverwalker <= $numservers; ++$serverwalker) {
        $settings->add(
            new admin_setting_configtext_trimmed(
                'block_panopto/server_name' . $serverwalker,
                get_string('block_global_hostname', 'block_panopto') . ' ' . $serverwalker,
                get_string('block_global_hostname_desc', 'block_panopto'),
                '',
                PARAM_TEXT
            )
        );
        $settings->add(
            new admin_setting_configtext_trimmed(
                'block_panopto/application_key' . $serverwalker,
                get_string('block_global_application_key', 'block_panopto') . ' ' . $serverwalker,
                get_string('block_global_application_key_desc', 'block_panopto'),
                '',
                PARAM_TEXT
            )
        );
    }

    // The next setting requires a Panopto server and appkey combo to be properly set.
    if (!isset($targetserverarray) || empty($targetserverarray)) {
        $targetserverarray = [get_string('add_a_panopto_server', 'block_panopto')];
    }

    $settings->add(
        new admin_setting_configselect(
            'block_panopto/automatic_operation_target_server',
            get_string('block_panopto_automatic_operation_target_server', 'block_panopto'),
            get_string('block_panopto_automatic_operation_target_server_desc', 'block_panopto'),
            0,
            $targetserverarray
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/check_server_status',
            get_string('block_panopto_check_server_status', 'block_panopto'),
            get_string('block_panopto_check_server_status_desc', 'block_panopto'),
            0
        )
    );
    $settings->add(
        new admin_setting_configduration('block_panopto/check_server_interval',
            get_string('block_panopto_check_server_interval', 'block_panopto'),
            get_string('block_panopto_check_server_interval_desc', 'block_panopto'),
            30)
    );

    $settings->add(new admin_setting_heading('block_panopto/panopto_syncing_options',
            get_string('block_global_panopto_syncing_options', 'block_panopto'),
            ''));

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/sync_after_login',
            get_string('block_panopto_sync_after_login', 'block_panopto'),
            get_string('block_panopto_sync_after_login_desc', 'block_panopto'),
            0
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/sync_after_provisioning',
            get_string('block_panopto_sync_after_provisioning', 'block_panopto'),
            get_string('block_panopto_sync_after_provisioning_desc', 'block_panopto'),
            0
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/sync_on_enrolment',
            get_string('block_panopto_sync_on_enrolment', 'block_panopto'),
            get_string('block_panopto_sync_on_enrolment_desc', 'block_panopto'),
            0
        )
    );

    $possiblessosynctypes = \panopto_data::getpossiblessosynctypes();
    $settings->add(
        new admin_setting_configselect(
            'block_panopto/sso_sync_type',
            get_string('block_panopto_sso_sync_type', 'block_panopto'),
            get_string('block_panopto_sso_sync_type_desc', 'block_panopto'),
            'nosync', // Default to authentication without sync.
            $possiblessosynctypes
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/async_tasks',
            get_string('block_panopto_async_tasks', 'block_panopto'),
            get_string('block_panopto_async_tasks_desc', 'block_panopto'),
            0
        )
    );

    $settings->add(new admin_setting_heading('block_panopto/panopto_folder_and_category_options',
            get_string('block_global_panopto_folder_and_category_options', 'block_panopto'),
            ''));

    $possiblefoldernamestyles = \panopto_data::getpossiblefoldernamestyles();
    $settings->add(
        new admin_setting_configselect(
            'block_panopto/folder_name_style',
            get_string('block_panopto_folder_name_style', 'block_panopto'),
            get_string('block_panopto_folder_name_style_desc', 'block_panopto'),
            'fullname', // Default to longname only.
            $possiblefoldernamestyles
        )
    );

    $possibleprovisiontypes = \panopto_data::getpossibleprovisiontypes();
    $settings->add(
        new admin_setting_configselect(
            'block_panopto/auto_provision_new_courses',
            get_string('block_panopto_auto_provision', 'block_panopto'),
            get_string('block_panopto_auto_provision_desc', 'block_panopto'),
            'oncoursecreation',
            $possibleprovisiontypes
        )
    );

    $possiblecopyprovisiontypes = \panopto_data::getpossiblecopyprovisiontypes();
    $settings->add(
        new admin_setting_configselect(
            'block_panopto/provisioning_during_copy',
            get_string('block_panopto_copy_provision', 'block_panopto'),
            get_string('block_panopto_copy_provision_desc', 'block_panopto'),
            'both',
            $possiblecopyprovisiontypes
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/auto_insert_lti_link_to_new_courses',
            get_string('block_panopto_auto_insert_lti_link_to_new_courses', 'block_panopto'),
            get_string('block_panopto_auto_insert_lti_link_to_new_courses_desc', 'block_panopto'),
            0
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/auto_add_block_to_new_courses',
            get_string('block_panopto_auto_add_block_to_new_courses', 'block_panopto'),
            get_string('block_panopto_auto_add_block_to_new_courses_desc', 'block_panopto'),
            0
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/auto_sync_imports',
            get_string('block_panopto_auto_sync_imports', 'block_panopto'),
            get_string('block_panopto_auto_sync_imports_desc', 'block_panopto'),
            1
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/anyone_view_recorder_links',
            get_string('block_panopto_anyone_view_recorder_links', 'block_panopto'),
            get_string('block_panopto_anyone_view_recorder_links_desc', 'block_panopto'),
            0
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/any_creator_can_view_folder_settings',
            get_string('block_panopto_any_creator_can_view_folder_settings', 'block_panopto'),
            get_string('block_panopto_any_creator_can_view_folder_settings_desc', 'block_panopto'),
            0
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/enforce_category_structure',
            get_string('block_panopto_enforce_category_structure', 'block_panopto'),
            get_string('block_panopto_enforce_category_structure_desc', 'block_panopto'),
            0
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/sync_category_after_course_provision',
            get_string('block_panopto_enforce_category_after_course_provision', 'block_panopto'),
            get_string('block_panopto_enforce_category_after_course_provision_desc', 'block_panopto'),
            0
        )
    );

    $settings->add(new admin_setting_heading('block_panopto/panopto_role_options',
            get_string('block_global_panopto_role_options', 'block_panopto'),
            ''));

    $systemcontext = context_system::instance();
    $systemrolearray = panopto_get_all_roles_at_context_and_contextlevel($systemcontext);
    $systemrolearray = role_fix_names($systemrolearray, $systemcontext, ROLENAME_ALIAS, true);

    $systempublishersetting = new admin_setting_configmultiselect(
        'block_panopto/publisher_system_role_mapping',
        get_string('block_panopto_publisher_system_role_mapping', 'block_panopto'),
        get_string('block_panopto_publisher_system_role_mapping_desc', 'block_panopto'),
        [],
        $systemrolearray
    );
    $systempublishersetting->set_updatedcallback('panopto_update_system_publishers');
    $settings->add($systempublishersetting);

    $coursecontext = context_course::instance(SITEID);
    $courserolearray = get_all_roles($coursecontext);
    $courserolearray = role_fix_names($courserolearray, $coursecontext, ROLENAME_ALIAS, true);

    $settings->add(
        new admin_setting_configmultiselect(
            'block_panopto/publisher_role_mapping',
            get_string('block_panopto_publisher_mapping', 'block_panopto'),
            get_string('block_panopto_publisher_mapping_desc', 'block_panopto'),
            [],
            $courserolearray
        )
    );

    $settings->add(
        new admin_setting_configmultiselect(
            'block_panopto/creator_role_mapping',
            get_string('block_panopto_creator_mapping', 'block_panopto'),
            get_string('block_panopto_creator_mapping_desc', 'block_panopto'),
            [3, 4],
            $courserolearray
        )
    );

    $settings->add(new admin_setting_heading('block_panopto/panopto_http_and_debug_settings',
            get_string('block_global_panopto_http_and_debug_settings', 'block_panopto'),
            ''));

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/print_log_to_file',
            get_string('block_panopto_print_log_to_file', 'block_panopto'),
            get_string('block_panopto_print_log_to_file_desc', 'block_panopto'),
            0
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/print_verbose_logs',
            get_string('block_panopto_print_verbose_logs', 'block_panopto'),
            get_string('block_panopto_print_verbose_logs_desc', 'block_panopto'),
            0
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'block_panopto/enforce_https_on_wsdl',
            get_string('block_panopto_enforce_https_on_wsdl', 'block_panopto'),
            get_string('block_panopto_enforce_https_on_wsdl_desc', 'block_panopto'),
            1
        )
    );

    $settings->add(
        new admin_setting_configtext_trimmed(
            'block_panopto/wsdl_proxy_host',
            get_string('block_panopto_wsdl_proxy_host', 'block_panopto'),
            get_string('block_panopto_wsdl_proxy_host_desc', 'block_panopto'),
            '',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext_trimmed(
            'block_panopto/wsdl_proxy_port',
            get_string('block_panopto_wsdl_proxy_port', 'block_panopto'),
            get_string('block_panopto_wsdl_proxy_port_desc', 'block_panopto'),
            '',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext_trimmed(
            'block_panopto/panopto_connection_timeout',
            get_string('block_panopto_panopto_connection_timeout', 'block_panopto'),
            get_string('block_panopto_panopto_connection_timeout_desc', 'block_panopto'),
            15,
            PARAM_INT
        )
    );

    $settings->add(
        new admin_setting_configtext_trimmed(
            'block_panopto/panopto_socket_timeout',
            get_string('block_panopto_panopto_socket_timeout', 'block_panopto'),
            get_string('block_panopto_panopto_socket_timeout_desc', 'block_panopto'),
            30,
            PARAM_INT
        )
    );

    $settings->add(new admin_setting_heading('block_panopto/panopto_bulk_and_batch_tools',
            get_string('block_global_panopto_bulk_and_batch_tools', 'block_panopto'),
            ''));

    $categorystructurelink = '<a id="panopto_build_category_structure_btn" href="' . $CFG->wwwroot .
        '/blocks/panopto/build_category_structure.php">' .
        get_string('block_global_build_category_structure', 'block_panopto') . '</a>';

    $settings->add(new admin_setting_heading('block_panopto_build_category_structure', '', $categorystructurelink));

    $link = '<a id="panopto_provision_course_btn" href="' . $CFG->wwwroot . '/blocks/panopto/provision_course.php">' .
        get_string('block_global_add_courses', 'block_panopto') . '</a>';

    $settings->add(new admin_setting_heading('block_panopto_add_courses', '', $link));

    $unprovisionlink = '<a id="panopto_unprovision_course_btn" href="' . $CFG->wwwroot .
        '/blocks/panopto/unprovision_course.php">' . get_string('block_global_unprovision_courses', 'block_panopto') . '</a>';
    $settings->add(new admin_setting_heading('block_panopto_unprovision_courses', '', $unprovisionlink));

    $importlink = '<a id="panopto_reinitialize_imports_btn" href="' . $CFG->wwwroot . '/blocks/panopto/reinitialize_imports.php">' .
        get_string('block_global_reinitialize_all_imports', 'block_panopto') . '</a>';

    $settings->add(new admin_setting_heading('block_panopto_reinitialize_all_imports', '', $importlink));

    $upgradelink = '<a id="panopto_upgrade_folders_btn" href="' . $CFG->wwwroot . '/blocks/panopto/upgrade_all_folders.php">' .
        get_string('block_global_upgrade_all_folders', 'block_panopto') . '</a>';

    $settings->add(new admin_setting_heading('block_panopto_upgrade_all_folders', '', $upgradelink));

    $bulkrenamelink = '<a id="panopto_rename_folders_btn" href="' . $CFG->wwwroot . '/blocks/panopto/rename_all_folders.php">' .
        get_string('block_global_rename_all_folders', 'block_panopto') . '</a>';

    $settings->add(new admin_setting_heading('block_panopto_rename_all_folders', '', $bulkrenamelink));
}
/* End of file settings.php */
