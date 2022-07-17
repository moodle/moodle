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
 * Provides the plugin strings.
 *
 * @package     tool_pluginskel
 * @category    string
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['observers_internal'] = 'Internal';
$string['observers_internal_help'] = 'Non-internal observers are not called during database transactions, but instead after a successful commit of the transaction.';
$string['observers_internal_link'] = 'https://docs.moodle.org/dev/Event_2';

$string['message_providers_title'] = 'Title';
$string['message_providers_title_help'] = 'A short, one-line, description of the message provider. It represents the text value for the language string "messageprovider:name".';
$string['message_providers_title_link'] = 'https://docs.moodle.org/dev/Messaging_2.0';

$string['upgradelib'] = 'Upgradelib';
$string['upgradelib_help'] = 'Create the file db/upgradelib.php where upgrade code can be grouped under some helper functions to be used in the upgrade.php file.';
$string['upgradelib_link'] = 'https://docs.moodle.org/dev/Upgrade_API';

$string['addmore_applicable_formats'] = 'Add more applicable formats';
$string['addmore_archetypes'] = 'Add more archetypes';
$string['addmore_backup_elements'] = 'Add more backup elements';
$string['addmore_capabilities'] = 'Add more capabilities';
$string['addmore_cli_scripts'] = 'Add more filenames';
$string['addmore_custom_layouts'] = 'Add more custom layouts';
$string['addmore_dependencies'] = 'Add more dependencies';
$string['addmore_events'] = 'Add more events';
$string['addmore_lang_strings'] = 'Add more lang strings';
$string['addmore_message_providers'] = 'Add more message providers';
$string['addmore_mobile_addons'] = 'Add more mobile addons';
$string['addmore_observers'] = 'Add more observers';
$string['addmore_params_for_js'] = 'Add more JS params';
$string['addmore_parents'] = 'Add more parents';
$string['addmore_phpunit_tests'] = 'Add more test classes';
$string['addmore_restore_elements'] = 'Add more restore elements';
$string['addmore_strings_for_js'] = 'Add more JS strings';
$string['addmore_stylesheets'] = 'Add more stylesheets';

$string['atto_features_params_for_js'] = 'JS params';
$string['atto_features_params_for_js_default'] = 'Default';
$string['atto_features_params_for_js_default_help'] = 'The default value for the parameter, defined in the JavaScript source file.';
$string['atto_features_params_for_js_default_link'] = 'https://docs.moodle.org/dev/Atto#Atto_subplugin_Php_API';
$string['atto_features_params_for_js_name'] = 'Name';
$string['atto_features_params_for_js_name_help'] = 'The name of the parameter.';
$string['atto_features_params_for_js_name_link'] = 'https://docs.moodle.org/dev/Atto#Atto_subplugin_Php_API';
$string['atto_features_params_for_js_value'] = 'Value';
$string['atto_features_params_for_js_value_help'] = 'The value of the JavaScript parameter.';
$string['atto_features_params_for_js_value_link'] = 'https://docs.moodle.org/dev/Atto#Atto_subplugin_Php_API';
$string['atto_features_strings_for_js'] = 'JS strings';
$string['atto_features_strings_for_js_id'] = 'Id';
$string['atto_features_strings_for_js_id_help'] = 'The string id.';
$string['atto_features_strings_for_js_id_link'] = 'https://docs.moodle.org/dev/Atto#Atto_subplugin_Php_API';
$string['atto_features_strings_for_js_text'] = 'Text';
$string['atto_features_strings_for_js_text_help'] = 'The value of the string id.';
$string['atto_features_strings_for_js_text_link'] = 'https://docs.moodle.org/dev/Atto#Atto_subplugin_Php_API';

$string['auth_features_can_be_manually_set'] = 'can_be_manually_set';
$string['auth_features_can_be_manually_set_help'] = 'True if the authentication plugin will be able to be manually set for users.';
$string['auth_features_can_be_manually_set_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_can_change_password'] = 'can_change_password';
$string['auth_features_can_change_password_help'] = 'True if the authentication plugin can change the user\'s password.';
$string['auth_features_can_change_password_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_can_confirm'] = 'can_confirm';
$string['auth_features_can_confirm_help'] = 'True if the authentication plugin allows confirmation of new users.';
$string['auth_features_can_confirm_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_can_edit_profile'] = 'can_edit_profile';
$string['auth_features_can_edit_profile_help'] = 'True if the authentication plugin can edit the user\'s profile.';
$string['auth_features_can_edit_profile_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_can_reset_password'] = 'can_reset_password';
$string['auth_features_can_reset_password_help'] = 'True if the plugin allows resetting of the internal password.';
$string['auth_features_can_reset_password_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_can_signup'] = 'can_signup';
$string['auth_features_can_signup_help'] = 'True if the authentication plugin allows signup and user creation.';
$string['auth_features_can_signup_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_config_ui'] = 'Config UI';
$string['auth_features_config_ui_help'] = 'Enable the generation of a configuration interface. A web form must be defined in the function config_form() from auth.php.';
$string['auth_features_config_ui_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_description'] = 'Description';
$string['auth_features_description_help'] = 'A short, one-line, description of the authentication plugin. It represents the text value of the language string "auth_description".';
$string['auth_features_description_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_is_internal'] = 'is_internal';
$string['auth_features_is_internal_help'] = 'True if the authentication plugin is "internal". Internal plugins use password hashes from Moodle user table for authentication.';
$string['auth_features_is_internal_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_is_synchronised_with_external'] = 'is_synchronised_with_external';
$string['auth_features_is_synchronised_with_external_help'] = 'True if Moodle should automatically update internal user records with data from external sources using the information from get_userinfo() method.';
$string['auth_features_is_synchronised_with_external_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';
$string['auth_features_prevent_local_passwords'] = 'prevent_local_passwords';
$string['auth_features_prevent_local_passwords_help'] = 'True if password hashes should be stored in local Moodle database.';
$string['auth_features_prevent_local_passwords_link'] = 'https://docs.moodle.org/dev/Authentication_plugins';

$string['back'] = 'Back';

$string['block_features_edit_form'] = 'Edit form';
$string['block_features_edit_form_help'] = 'Create the file edit_form.php which will be used for instance configuration.';
$string['block_features_edit_form_link'] = 'https://docs.moodle.org/dev/Blocks';
$string['block_features_instance_allow_multiple'] = 'Allow multiple instances';
$string['block_features_instance_allow_multiple_help'] = 'Allow multiple instances of the block plugin in the same course.';
$string['block_features_instance_allow_multiple_link'] = 'https://docs.moodle.org/dev/Blocks';
$string['block_features_applicable_formats'] = 'Applicable formats';
$string['block_features_applicable_formats_page'] = 'Page';
$string['block_features_applicable_formats_page_help'] = 'Declares on which page the plugin is available.';
$string['block_features_applicable_formats_page_link'] = 'https://docs.moodle.org/dev/Blocks';
$string['block_features_applicable_formats_allowed'] = 'Allowed';
$string['block_features_applicable_formats_allowed_help'] = 'True if the block plugin is allowed on the page.';
$string['block_features_applicable_formats_allowed_link'] = 'https://docs.moodle.org/dev/Blocks';
$string['block_features_backup_moodle2'] = 'Backup moodle2';
$string['block_features_backup_moodle2_backup_elements'] = 'Backup elements';
$string['block_features_backup_moodle2_backup_elements_name'] = 'Backup element name';
$string['block_features_backup_moodle2_backup_elements_name_help'] = 'Name for the backup element.';
$string['block_features_backup_moodle2_backup_elements_name_link'] = 'https://docs.moodle.org/dev/Backup_API#API_for_blocks';
$string['block_features_backup_moodle2_backup_stepslib'] = 'Backup stepslib';
$string['block_features_backup_moodle2_backup_stepslib_help'] = 'Create a backup stepslib file.';
$string['block_features_backup_moodle2_backup_stepslib_link'] = 'https://docs.moodle.org/dev/Backup_API#API_for_blocks';
$string['block_features_backup_moodle2_restore_elements'] = 'Restore elements';
$string['block_features_backup_moodle2_restore_elements_name'] = 'Restore element name';
$string['block_features_backup_moodle2_restore_elements_name_help'] = 'Name for the restore element.';
$string['block_features_backup_moodle2_restore_elements_name_link'] = 'https://docs.moodle.org/dev/Restore_2.0_for_developers';
$string['block_features_backup_moodle2_restore_elements_path'] = 'Restore element path';
$string['block_features_backup_moodle2_restore_elements_path_help'] = 'The path for the restore element.';
$string['block_features_backup_moodle2_restore_elements_path_link'] = 'https://docs.moodle.org/dev/Restore_2.0_for_developers';
$string['block_features_backup_moodle2_restore_stepslib'] = 'Restore stepslib';
$string['block_features_backup_moodle2_restore_stepslib_help'] = 'Create a restore stepslib file.';
$string['block_features_backup_moodle2_restore_stepslib_link'] = 'https://docs.moodle.org/dev/Restore_2.0_for_developers';
$string['block_features_backup_moodle2_restore_task'] = 'Restore task';
$string['block_features_backup_moodle2_restore_task_help'] = 'Create a restore task file.';
$string['block_features_backup_moodle2_restore_task_link'] = 'https://docs.moodle.org/dev/Restore_2.0_for_developers';
$string['block_features_backup_moodle2_settingslib'] = 'Settingslib';
$string['block_features_backup_moodle2_settingslib_help'] = 'Create a backup settingslib file.';
$string['block_features_backup_moodle2_settingslib_link'] = 'https://docs.moodle.org/dev/Backup_API#API_for_blocks';

$string['capabilities'] = 'Capabilities';
$string['capabilities_archetypes'] = 'Archetypes';
$string['capabilities_archetypes_role'] = 'Role';
$string['capabilities_archetypes_role_help'] = 'Standard archetype.';
$string['capabilities_archetypes_role_link'] = 'https://docs.moodle.org/dev/Access_API';
$string['capabilities_archetypes_permission'] = 'Permission';
$string['capabilities_archetypes_permission_help'] = 'The permission associated with the role.';
$string['capabilities_archetypes_permission_link'] = 'https://docs.moodle.org/dev/Access_API';
$string['capabilities_captype'] = 'Captype';
$string['capabilities_captype_help'] = 'All capabilities are "read" or "write", for security reasons all write capabilities for guests and non-logged in users are prevented.';
$string['capabilities_captype_link'] = 'https://docs.moodle.org/dev/Access_API';
$string['capabilities_clonepermissionsfrom'] = 'Clone permissions from';
$string['capabilities_clonepermissionsfrom_help'] = 'Copy the permissions for each role from the current settings of another capability.';
$string['capabilities_clonepermissionsfrom_link'] = 'https://docs.moodle.org/dev/Access_API';
$string['capabilities_contextlevel'] = 'Contextlevel';
$string['capabilities_contextlevel_help'] = 'The typical contextlevel where the capability is checked. It is the lowest level where this capability can be overriden by the permissions UI.';
$string['capabilities_contextlevel_link'] = 'https://docs.moodle.org/dev/Access_API';
$string['capabilities_name'] = 'Name';
$string['capabilities_name_help'] = 'The name of the capability. The capability "componenttype/componentname:name" will be generated.';
$string['capabilities_name_link'] = 'https://docs.moodle.org/dev/Access_API';
$string['capabilities_riskbitmask'] = 'Riskbit mask';
$string['capabilities_riskbitmask_help'] = 'The risk associated with the capability. Multiple risk types can be specified by using the "|" separator.';
$string['capabilities_riskbitmask_link'] = 'https://docs.moodle.org/dev/Hardening_new_Roles_system';
$string['capabilities_title'] = 'Title';
$string['capabilities_title_help'] = 'A short, one-line, description of the capability. It represents the text value for the language string "componentname:name".';
$string['capabilities_title_link'] = 'https://docs.moodle.org/dev/Access_API';

$string['cli_scripts'] = 'CLI scripts';
$string['cli_scripts_filename'] = 'Filename';
$string['cli_scripts_filename_help'] = 'The name of the CLI script file. All files will be generated in the plugin\'s "cli" directory.';

$string['component'] = 'Component';
$string['component_help'] = 'The full frankenstyle component name in the form of componenttype_componentname.

If you want to change the component type please restart the plugin generation process from the beginning.';
$string['component_link'] = 'https://docs.moodle.org/dev/version.php';
$string['componenthdr'] = 'Component';
$string['componenttype'] = 'Component type';
$string['componenttype_help'] = 'The type of plugin. More information about the different plugin types can be found in the official Moodle documentation by clicking the "More help" button.';
$string['componenttype_link'] = 'https://docs.moodle.org/dev/Plugin_types';
$string['componentnameinvalid'] = 'Invalid component name';
$string['componentname'] = 'Component name';
$string['componentname_help'] = 'The name of the plugin. This will become the name of the plugin\'s root folder.

The name must start with a letter and ideally should contain letters only. Numbers and underscores are allowed, but discouraged. Activity modules must not have underscores in their name.';
$string['copyright'] = 'Copyright';
$string['copyright_desc'] = 'Default value of the Copyright field when manually generating the plugin skeleton.';
$string['copyright_help'] = 'This field should contain the year the plugin was created, the name(s) of the copyright holder(s) as well as the email addresse(s).

The copyright tag will be present in every file of the plugin as part of the standard Moodle boilerplate.

For more information about the standard Moodle boilerplate click on the "More help" button.';
$string['copyright_link'] = 'https://docs.moodle.org/dev/Coding_style#Files';

$string['delete_applicable_formats'] = 'Delete applicable format';
$string['delete_capabilities'] = 'Delete capability';
$string['delete_cli_scripts'] = 'Delete filename';
$string['delete_custom_layouts'] = 'Delete custom layouts';
$string['delete_dependencies'] = 'Delete dependency';
$string['delete_events'] = 'Delete event';
$string['delete_lang_strings'] = 'Delete lang string';
$string['delete_message_providers'] = 'Delete message provider';
$string['delete_mobile_addons'] = 'Delete mobile addon';
$string['delete_observers'] = 'Delete observer';
$string['delete_params_for_js'] = 'Delete JS param';
$string['delete_parents'] = 'Delete parent';
$string['delete_phpunit_tests'] = 'Delete test class';
$string['delete_restore_elements'] = 'Delete restore element';
$string['delete_strings_for_js'] = 'Delete JS string';
$string['delete_stylesheets'] = 'Delete stylesheet';

$string['dependencies'] = 'Dependencies';
$string['dependencies_plugin'] = 'Plugin';
$string['dependencies_plugin_help'] = 'The full frankenstyle component name for the plugin dependency.';
$string['dependencies_version'] = 'Version';
$string['dependencies_version_help'] = 'The version number for the plugin dependency.

A value ANY_VERSION means that any version of the plugin will satisfy the dependency';

$string['downloadskel'] = 'Download plugin skeleton';
$string['downloadrecipe'] = 'Download recipe';
$string['emptypluginname'] = 'Plugin name not specified';
$string['emptyrecipecontent'] = 'Empty recipe';

$string['enrol_features_allow_enrol'] = 'Allow enrol';
$string['enrol_features_allow_enrol_help'] = 'Allow user enrolment from other plugins by calling the function enrol_user(). A corresponding "enrol" capability must also be defined.';
$string['enrol_features_allow_enrol_link'] = 'https://docs.moodle.org/dev/Enrolment_plugins';
$string['enrol_features_allow_unenrol'] = 'Allow unenrol';
$string['enrol_features_allow_unenrol_help'] = 'Allow other plugins to unenrol everybody. A corresponding "unenrol" capability must also be defined.';
$string['enrol_features_allow_unenrol_link'] = 'https://docs.moodle.org/dev/Enrolment_plugins';
$string['enrol_features_allow_unenrol_user'] = 'Allow unenrol user';
$string['enrol_features_allow_unenrol_user_help'] = 'Allow other plugins to unenrol a specific user. A corresponding "unenrol" capability must also be defined.';
$string['enrol_features_allow_unenrol_user_link'] = 'https://docs.moodle.org/dev/Enrolment_plugins';
$string['enrol_features_allow_manage'] = 'Allow manage';
$string['enrol_features_allow_manage_help'] = 'Allow other plugins to manually modify the user enrolments.';
$string['enrol_features_allow_manage_link'] = 'https://docs.moodle.org/dev/Enrolment_plugins';

$string['events'] = 'Events';
$string['events_eventname'] = 'Event name';
$string['events_eventname_help'] = 'The name of the created event.';
$string['events_eventname_link'] = 'https://docs.moodle.org/dev/Event_2';
$string['events_extends'] = 'Extends';
$string['events_extends_help'] = 'The name of the base event that the event extends.';
$string['events_extends_link'] = 'https://docs.moodle.org/dev/Event_2';

$string['features_install'] = 'Install';
$string['features_install_help'] = 'Generate the file db/install.php';
$string['features_license'] = 'License';
$string['features_license_help'] = 'Generate the LICENSE.md file with the text of the GPL3 license.';
$string['features_readme'] = 'Readme';
$string['features_readme_help'] = 'Generate the README.md file.';
$string['features_settings'] = 'Settings';
$string['features_settings_help'] = 'Generate the settings.php file.';
$string['features_uninstall'] = 'Uninstall';
$string['features_uninstall_help'] = 'Generate the file db/uninstall.php.';
$string['features_upgrade'] = 'Upgrade';
$string['features_upgrade_help'] = 'Generate the file db/upgrade.php.';
$string['features_upgrade_link'] = 'https://docs.moodle.org/dev/Upgrade_API';
$string['features_upgradelib'] = 'Upgrade library';
$string['features_upgradelib_help'] = 'Generate the file db/upgradelib.php.';
$string['features_upgradelib_link'] = 'https://docs.moodle.org/dev/Upgrade_API';

$string['generateskel'] = 'Generate plugin skeleton';
$string['generalhdr'] = 'General';

$string['lang_strings'] = 'Lang strings';
$string['lang_strings_id'] = 'Id';
$string['lang_strings_id_help'] = 'The id of the language string.';
$string['lang_strings_id_link'] = 'https://docs.moodle.org/dev/String_API';
$string['lang_strings_text'] = 'Text';
$string['lang_strings_text_help'] = 'The value of the language string.';
$string['lang_strings_text_link'] = 'https://docs.moodle.org/dev/String_API';

$string['manualhdr'] = 'Generate the plugin manually';
$string['maturity'] = 'Maturity';
$string['maturity_help'] = 'Plugin maturity.';
$string['maturity_link'] = 'https://docs.moodle.org/dev/version.php';

$string['message_providers'] = 'Message providers';
$string['message_providers_capability'] = 'Required capability';
$string['message_providers_capability_help'] = 'The capability that the user requires in order to receive the message produced by the provider.';
$string['message_providers_capability_link'] = 'https://docs.moodle.org/dev/Messaging_2.0';
$string['message_providers_name'] = 'Name';
$string['message_providers_name_help'] = 'The name for the message provider. The message provider is defined in the file db/messages.php.';
$string['message_providers_name_link'] = 'https://docs.moodle.org/dev/Messaging_2.0';

$string['mobile_addons'] = 'Mobile addons';
$string['mobile_addons_dependencies'] = 'Dependencies';
$string['mobile_addons_dependencies_name'] = 'Name';
$string['mobile_addons_dependencies_name_help'] = 'The name of the dependency.';
$string['mobile_addons_dependencies_name_link'] = 'https://docs.moodle.org/dev/Moodle_Mobile_Remote_add-ons';
$string['mobile_addons_name'] = 'Name';
$string['mobile_addons_name_help'] = 'The name of the mobile remote addon that will be loaded when the user access the plugin on the mobile app.';
$string['mobile_addons_name_link'] = 'https://docs.moodle.org/dev/Moodle_Mobile_Remote_add-ons';

$string['mod_features_backup_moodle2'] = 'Backup moodle2';
$string['mod_features_backup_moodle2_settingslib'] = 'Settingslib';
$string['mod_features_backup_moodle2_settingslib_help'] = 'Create a backup settingslib file.';
$string['mod_features_backup_moodle2_settingslib_link'] = 'https://docs.moodle.org/dev/Backup_2.0_for_developers';
$string['mod_features_backup_moodle2_backup_elements'] = 'Backup elements';
$string['mod_features_backup_moodle2_backup_elements_name'] = 'Backup element name';
$string['mod_features_backup_moodle2_backup_elements_name_help'] = 'Name for the backup element.';
$string['mod_features_backup_moodle2_backup_elements_name_link'] = 'https://docs.moodle.org/dev/Backup_2.0_for_developers';
$string['mod_features_backup_moodle2_restore_elements'] = 'Restore elements';
$string['mod_features_backup_moodle2_restore_elements_name'] = 'Restore element name';
$string['mod_features_backup_moodle2_restore_elements_name_help'] = 'Name for the restore element.';
$string['mod_features_backup_moodle2_restore_elements_name_link'] = 'https://docs.moodle.org/dev/Restore_2.0_for_developers';
$string['mod_features_backup_moodle2_restore_elements_path'] = 'Restore element path';
$string['mod_features_backup_moodle2_restore_elements_path_help'] = 'The path for the restore element.';
$string['mod_features_backup_moodle2_restore_elements_path_link'] = 'https://docs.moodle.org/dev/Restore_2.0_for_developers';
$string['mod_features_file_area'] = 'File area';
$string['mod_features_file_area_help'] = 'Generates the Files API related functions.';
$string['mod_features_file_area_link'] = 'https://docs.moodle.org/dev/Activity_modules';
$string['mod_features_gradebook'] = 'Gradebook';
$string['mod_features_gradebook_help'] = 'True if the plugin implements a gradebook.';
$string['mod_features_gradebook_link'] = 'https://docs.moodle.org/dev/Activity_modules';
$string['mod_features_navigation'] = 'Navigation';
$string['mod_features_navigation_help'] = 'Create the function extend_navigation() and extend_settings_navigation() in lib.php.';
$string['mod_features_navigation_link'] = 'https://docs.moodle.org/dev/Activity_modules';

$string['name'] = 'Name';
$string['name_help'] = 'Human readable name for the plugin. This represents the text value for the language string "pluginname".';

$string['observers'] = 'Observers';
$string['observers_callback'] = 'Callback';
$string['observers_callback_help'] = 'Callback function name.';
$string['observers_callback_link'] = 'https://docs.moodle.org/dev/Event_2';
$string['observers_eventname'] = 'Event name';
$string['observers_eventname_help'] = 'Fully qualified event class name.';
$string['observers_eventname_link'] = 'https://docs.moodle.org/dev/Event_2';
$string['observers_includefile'] = 'Includefile';
$string['observers_includefile_help'] = 'File to be included before calling the observer. The path of the file should be relative to the Moodle root directory.';
$string['observers_includefile_link'] = 'https://docs.moodle.org/dev/Event_2';
$string['observers_priority'] = 'Priority';
$string['observers_priority_help'] = 'The priority of the observer. Observers with higher priority are called first. If not specified, it will default to 0.';
$string['observers_priority_link'] = 'https://docs.moodle.org/dev/Event_2';

$string['phpunit_tests'] = 'PHPUnit tests';
$string['phpunit_tests_classname'] = 'Class name';
$string['phpunit_tests_classname_help'] = 'The test class name. It can be either the full frankenstyle name, or just the name of the class to be tested. All PHPUnit test files will be generated in the "tests" directory.';
$string['phpunit_tests_classname_link'] = 'https://docs.moodle.org/dev/PHPUnit';

$string['privacy:metadata'] = 'Plugin skeleton generator does not store any personal data';

$string['proceedmanually'] = 'Proceed with manual generation';
$string['proceedrecipefile'] = 'Proceed with recipe file';
$string['proceedrecipe'] = 'Proceed with recipe';

$string['qtype_features_base_class'] = 'Base class';
$string['qtype_features_base_class_help'] = 'Base class for the question class located in question.php.';
$string['qtype_features_base_class_link'] = 'https://docs.moodle.org/dev/Question_types';

$string['pluginname'] = 'Moodle plugin skeleton generator';
$string['recipe'] = 'Recipe';
$string['recipe_help'] = 'The recipe should be written using the YAML serialization format. A recipe template is located in the plugin installation directory at cli/example.yaml.

More information about the YAML syntax can be found by clicking the "More help" button which will take you the official YAML web page.';
$string['recipe_link'] = 'http://yaml.org/';
$string['recipefile'] = 'Recipe file';

$string['recipefile_help'] = 'The recipe should be written using the YAML serialization format. A recipe template is located in the plugin installation directory at cli/example.yaml.

More information about the YAML syntax can be found by clicking the "More help" button which will take you the official YAML web page.';
$string['recipefile_link'] = 'http://yaml.org/';
$string['recipefilehdr'] = 'Generate the plugin from recipe file';
$string['recipehdr'] = 'Generate the plugin from recipe';
$string['release'] = 'Release';
$string['release_help'] = 'Human readable version name that should help to identify each release of the plugin.';
$string['release_link'] = 'https://docs.moodle.org/dev/version.php';
$string['requires'] = 'Required Moodle version';
$string['requires_help'] = 'The minimum required Moodle version for the plugin to install and function correctly.';
$string['showrecipe'] = 'Show recipe';
$string['showrecipehdr'] = 'Recipe';

$string['theme_features_all_layouts'] = 'All layouts';
$string['theme_features_all_layouts_help'] = 'Apply the theme to all layouts.';
$string['theme_features_all_layouts_link'] = 'https://docs.moodle.org/dev/Themes';
$string['theme_features_custom_layouts'] = 'Custom layouts';
$string['theme_features_custom_layouts_name'] = 'Custom layout name';
$string['theme_features_custom_layouts_name_help'] = 'The name for the custom layout that the plugin will create. The layout will be located in the "layouts" directory.';
$string['theme_features_custom_layouts_name_link'] = 'https://docs.moodle.org/dev/Themes';
$string['theme_features_doctype'] = 'Doctype';
$string['theme_features_doctype_help'] = 'The doctype for the web page. It usually is "html5".';
$string['theme_features_doctype_link'] = 'https://docs.moodle.org/dev/Themes';
$string['theme_features_parents'] = 'Parent themes';
$string['theme_features_parents_base_theme'] = 'Base theme';
$string['theme_features_parents_base_theme_help'] = 'The base theme that this theme will extend.';
$string['theme_features_parents_base_theme_link'] = 'https://docs.moodle.org/dev/Themes';
$string['theme_features_stylesheets'] = 'Stylesheets';
$string['theme_features_stylesheets_name'] = 'Stylesheet name';
$string['theme_features_stylesheets_name_help'] = 'The name of the stylesheet defined by the plugin. The stylesheet will be created in the "styles" directory.';
$string['theme_features_stylesheets_name_link'] = 'https://docs.moodle.org/dev/Themes';

$string['undefined'] = 'Undefined';
$string['version'] = 'Version';
$string['version_help'] = 'The version number of the plugin. The format is partially date based with the form YYYYMMDDXX where XX is an incremental counter for the given year (YYYY), month (MM) and date (DD) of the plugin version\'s release.';
$string['version_link'] = 'https://docs.moodle.org/dev/version.php';
