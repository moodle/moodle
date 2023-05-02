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
 * English language strings.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Microsoft 365 Integration';

// Settings - tabs.
$string['settings_header_setup'] = 'Setup';
$string['settings_header_syncsettings'] = 'Sync Settings';
$string['settings_header_advanced'] = 'Advanced';
$string['settings_header_sds'] = 'School Data Sync';
$string['settings_header_teams'] = 'Teams Settings';
$string['settings_header_moodle_app'] = 'Teams Moodle app';

// Setting sections in the "Setup" tab.
$string['settings_setup_step1'] = 'Step 1/3: Register Moodle with Azure AD';
$string['settings_setup_step1_desc'] = 'Register a new AzureAD Application for your Microsoft 365 tenant by using Windows PowerShell:

<a href="{$a}/local/o365/scripts/Moodle-AzureAD-Powershell.zip" class="btn btn-primary" target="_blank">Download Windows PowerShell Script</a>

<p style="margin-top:10px"><a href="https://aka.ms/MoodleTeamsPowerShellReadMe" target="_blank">Click here</a> to read instructions on running the script.</p>
<p>When prompted, use the following link as the Moodle URL:</p><h5><b>{$a}</b></h5>';
$string['settings_setup_step1clientcreds'] = '<br />Once the script is successfully executed, copy the Application ID and Secret returned by the script into the <a href="{$a}">OpenID Connect authentication plugin configuration page</a>.';
$string['settings_setup_step1_credentials_end'] = 'If you are unable to set up the AzureAD app via PowerShell, <a href="https://aka.ms/MoodleTeamsManualSetup" target="_blank">click here</a> for manual setup instructions.';
$string['settings_setup_step1_continue'] = '<b>Once you have entered your Application ID and Key, click "Save changes" at the bottom of the page to continue.</b><br /><br /><br /><br /><br />';
$string['settings_setup_step1_existing_settings'] = '<h5>Existing settings</h5>';
$string['settings_setup_step2'] = 'Step 2/3: Choose connection method';
$string['settings_setup_step2_desc'] = 'This section allows you to choose how the Microsoft 365 integration suite connects to Microsoft 365 services.<br />
Historically the integration can connect to the Microsoft 365 services using "Application Access", or on behalf of a user you have dedicated as the "system" user.<br />
<b>From March 2022, only "Application Access" is supported. All future new functions will only be implemented and tested using the "Application Access" connection method.</b>';
$string['settings_setup_step2_desc_additional'] = '<br /><br />
<span class="error">You are currently using "System API user" connection method, which is not supported from March 2022. Please change to "Application Access" connection method.</span>';
$string['settings_setup_step2_continue'] = '<b>Choose a connection method, then click "Save changes" to continue.</b><br /><br /><br /><br /><br />';
$string['settings_setup_step3'] = 'Step 3/3: Admin consent &amp; additional information';
$string['settings_setup_step3_desc'] = 'This last step allows you to give administrator consent to use some Azure permissions, and gathers some additional information about your Microsoft 365 environment.<br /><br />';
$string['settings_setup_step4'] = 'Verify setup';
$string['settings_setup_step4_desc'] = 'Setup is complete. Click the "Update" button below to verify your setup.';

// Settings in "Step 2/3" of the "Setup" tab.
$string['settings_enableapponlyaccess'] = 'Application access';
$string['settings_enableapponlyaccess_details'] = '<b>Recommended</b><br />
Using this method, the integration accesses Microsoft 365 directly using "Application Permissions", which is the easiest and the only recommended way to connect to Microsoft 365.<br />
It requires you enable a few extra permissions in the Azure app.<br />';

// Settings in "Step 3/3" section of the "Setup" tab.
$string['settings_adminconsent'] = 'Admin Consent';
$string['settings_adminconsent_btn'] = 'Provide Admin Consent';
$string['settings_adminconsent_details'] = 'To allow access to some of the permissions needed, an administrator will need to provide admin consent. Click this button, then log in with an Azure administrator account to provide consent. This will need to be done whenever you change "Admin" permissions in Azure.';
$string['settings_aadtenant'] = 'Azure AD Tenant';
$string['settings_aadtenant_details'] = 'Used to Identify your organization within Azure AD. For example: "contoso.onmicrosoft.com".';
$string['settings_aadtenant_error'] = 'We could not detect your Azure AD tenant.<br />Please ensure "Windows Azure Active Directory" has been added to your registered Azure AD application, and that the "Read directory data" permission is enabled.';
$string['settings_odburl'] = 'OneDrive for Business URL';
$string['settings_odburl_details'] = 'The URL used to access OneDrive for Business. This can usually be determined by your Azure AD tenant. For example, if your Azure AD tenant is "contoso.onmicrosoft.com", this is most likely "contoso-my.sharepoint.com". Enter only the domain name, do not include http:// or https://';
$string['settings_odburl_error'] = 'We could not determine your OneDrive for Business URL.<br />Please make sure "Microsoft 365 SharePoint Online" has been added to your registered application in Azure AD.';
$string['settings_odburl_error_graph'] = 'We could not determine your OneDrive for Business URL, please enter manually. This can usually be determined by using the URL you use to access OneDrive.';
$string['settings_serviceresourceabstract_detect'] = 'Detect';
$string['settings_serviceresourceabstract_detecting'] = 'Detecting...';
$string['settings_serviceresourceabstract_error'] = 'An error occurred detecting setting. Please set manually.';
$string['settings_serviceresourceabstract_noperms'] = 'We experienced a problem detecting this setting.<br />Please ensure "Windows Azure Active Directory" has been added to your registered Azure AD application, and that the "Read directory data" permission is enabled.';
$string['settings_serviceresourceabstract_valid'] = '{$a} is usable.';
$string['settings_serviceresourceabstract_invalid'] = 'This value doesn\'t seem to be usable.';
$string['settings_serviceresourceabstract_nocreds'] = 'Please set application credentials first.';
$string['settings_serviceresourceabstract_empty'] = 'Please enter a value or click "Detect" to attempt to detect correct value.';

// Settings in "Verify setup" section of the "Setup" tab.
$string['settings_azuresetup'] = 'Azure AD setup';
$string['settings_azuresetup_appdataheader'] = 'Azure AD Application Registration';
$string['settings_azuresetup_appdatadesc'] = 'Verifies the correct parameters are set up in Azure AD.';
$string['settings_azuresetup_appdatareplyurlcorrect'] = 'Reply URL Correct';
$string['settings_azuresetup_appdatareplyurlincorrect'] = 'Reply URL Incorrect';
$string['settings_azuresetup_appdatareplyurlgeneralerror'] = 'Could not check reply url.';
$string['settings_azuresetup_appdatasignonurlcorrect'] = 'Sign-on URL Correct.';
$string['settings_azuresetup_appdatasignonurlincorrect'] = 'Sign-on URL Incorrect';
$string['settings_azuresetup_appdatasignonurlgeneralerror'] = 'Could not check sign-on url.';
$string['settings_azuresetup_apppermscorrect'] = 'Application Permissions are correct.';
$string['settings_azuresetup_details'] = 'This tool checks with Azure AD to make sure everything is set up correctly. <br /><b>Note:</b> Changes in Azure AD can take a moment to appear here. If you have made a change in Azure AD and do not see it reflected here, wait a moment and try again.';
$string['settings_azuresetup_correctval'] = 'Correct Value:';
$string['settings_azuresetup_detectedval'] = 'Detected Value:';
$string['settings_azuresetup_update'] = 'Update';
$string['settings_azuresetup_checking'] = 'Checking...';
$string['settings_azuresetup_missingappperms'] = 'Missing Application Permissions:';
$string['settings_azuresetup_missingperms'] = 'Missing Delegated Permissions:';
$string['settings_azuresetup_permscorrect'] = 'Delegated Permissions are correct.';
$string['settings_azuresetup_errorcheck'] = 'An error occurred trying to check Azure AD setup.';
$string['settings_azuresetup_noinfo'] = 'We don\'t have any information about your Azure AD setup yet. Please click the Update button to check.';
$string['settings_azuresetup_strunifiedpermerror'] = 'There was an error checking Microsoft Graph API permissions.';
$string['settings_azuresetup_strtenanterror'] = 'Please use the dectect button to set your Azure AD Tenant before updating Azure AD setup.';
$string['settings_azuresetup_unifiedheader'] = 'Microsoft Graph API';
$string['settings_azuresetup_unifieddesc'] = 'The Microsoft Graph API allows communication between Moodle and Microsoft 365.';
$string['settings_azuresetup_unifiederror'] = 'There was an error checking for Microsoft Graph API support.';
$string['settings_azuresetup_unifiedactive'] = 'Microsoft Graph API active.';
$string['settings_azuresetup_unifiedmissing'] = 'The Microsoft Graph API was not found in this application.';

// Additional settings in the "Verify setup" section of the "Setup" tab.
$string['settings_detectoidc'] = 'Application Credentials';
$string['settings_detectoidc_details'] = 'To communicate with Microsoft 365, Moodle needs credentials to identify itself. These are set in the "OpenID Connect" authentication plugin.';
$string['settings_detectoidc_credsvalid'] = 'Credentials have been set.';
$string['settings_detectoidc_credsvalid_link'] = 'Change';
$string['settings_detectoidc_credsinvalid'] = 'Credentials have not been set or are incomplete.';
$string['settings_detectoidc_credsinvalid_link'] = 'Set Credentials';
$string['settings_detectperms'] = 'Application Permissions';
$string['settings_detectperms_details'] = 'The use the plugin features, correct permissions must be set up for the application in Azure AD.';
$string['settings_detectperms_nocreds'] = 'Application credentials need to be set first. See above setting.';
$string['settings_detectperms_missing'] = 'Missing:';
$string['settings_detectperms_errorfix'] = 'An error occurred trying to fix permissions. Please set manually in Azure AD.';
$string['settings_detectperms_fixperms'] = 'Fix permissions';
$string['settings_detectperms_fixprereq'] = 'To fix this automatically, your system API user must be an administrator, and the "Access your organization\'s directory" permission must be enabled in Azure AD for the "Windows Azure Active Directory" application.';
$string['settings_detectperms_nounified'] = 'Microsoft Graph API not present, some new features may not work.';
$string['settings_detectperms_unifiednomissing'] = 'All unified permissions present.';
$string['settings_detectperms_update'] = 'Update';
$string['settings_detectperms_valid'] = 'Permissions have been set up.';
$string['settings_detectperms_invalid'] = 'Check permissions in Azure AD';

// Settings in "User sync" section of the "Sync settings" tab.
$string['settings_options_usersync'] = 'User Sync';
$string['settings_options_usersync_desc'] = 'The following settings control user synchronization between Microsoft 365 and Moodle.';
$string['settings_aadsync'] = 'Sync users with Azure AD';
$string['settings_aadsync_details'] = 'When enabled, Moodle and Azure AD users are synced according to the above options.<br /><br /><b>Note: </b>The sync job runs in the Moodle cron, and syncs 1000 users at a time. By default, this runs once per day at 1:00 AM in the time zone local to your server. To sync large sets of users more quickly, you can increase the frequency of the <b>Sync users with Azure AD</b> task using the <a href="{$a}">Scheduled tasks management page.</a><br /><br />';
$string['settings_aadsync_create'] = 'Create accounts in Moodle for users in Azure AD';
$string['settings_aadsync_update'] = 'Update all accounts in Moodle for users in Azure AD';
$string['settings_aadsync_suspend'] = 'Suspend previously synced accounts in Moodle when they are deleted from Azure AD';
$string['settings_aadsync_delete'] = 'Delete previously synced accounts in Moodle when they are deleted from Azure AD (requires "suspend" option above)';
$string['settings_aadsync_reenable'] = 'Re-enable suspended accounts for users in Azure AD';
$string['settings_aadsync_disabledsync'] = 'Sync disabled status';
$string['settings_aadsync_match'] = 'Match preexisting Moodle users with same-named accounts in Azure AD';
$string['settings_aadsync_matchswitchauth'] = 'Switch matched users to Microsoft 365 (OpenID Connect) authentication';
$string['settings_aadsync_appassign'] = 'Assign users to application during sync';
$string['settings_aadsync_photosync'] = 'Sync Microsoft 365 profile photos to Moodle in cron job';
$string['settings_aadsync_photosynconlogin'] = 'Sync Microsoft 365 profile photos to Moodle on login';
$string['settings_aadsync_nodelta'] = 'Perform a full sync each run';
$string['settings_aadsync_emailsync'] = 'Match Azure usernames to moodle emails instead of moodle usernames during the sync';
$string['settings_addsync_tzsync'] = 'Sync Outlook timezone to Moodle in cronjob';
$string['settings_addsync_tzsynconlogin'] = 'Sync Outlook timezone to Moodle on login';
$string['settings_aadsync_guestsync'] = 'Sync guest users';
$string['settings_suspend_delete_running_time'] = 'User suspension/deletion running time';
$string['settings_suspend_delete_running_time_desc'] = 'If the option is enabled, suspension/delete feature of user sync function will run once a day, at the time configured in the Moodle instance default time zone.';

// User field mapping.
$string['settings_fieldmap'] = 'User field mapping';
$string['settings_fieldmap_details'] = 'Available in <a href="{$a}">Open ID Connect authentication plugin</a>.';

// Settings in the "Course sync" section of the "Sync settings" tab.
$string['settings_secthead_coursesync'] = 'Course Sync';
$string['settings_secthead_coursesync_desc'] = 'These following settings control course synchronization between Moodle and Microsoft Teams.';
$string['settings_coursesync'] = 'Course sync';
$string['settings_coursesync_details'] = 'If enabled, this will create and maintain Teams for courses on the Moodle site (Default: Disabled). This will create any needed Teams each cron run, and add all current enrolled users as Team owners or members, depending on capability check. After that, Team membership will be maintained as users are enrolled or unenrolled from Moodle courses.';
$string['acp_coursesynccustom_off'] = 'Disabled<br />Disable Teams creation for all courses.';
$string['acp_coursesynccustom_oncustom'] = 'Customize<br />Allows authorized users to select which courses to create Teams for.<br> <span id="adminsetting_coursesync" style="font-weight: bold"><a href="{$a}">Customize course sync</a></span>';
$string['acp_coursesynccustom_onall'] = 'All Features Enabled<br />Enables Teams creation for all courses.';
$string['settings_coursesync_delete_group_on_course_deletion'] = 'Delete Microsoft 365 groups when connected Moodle course is deleted';
$string['settings_coursesync_delete_group_on_course_deletion_details'] = 'If enabled, Moodle will try to delete the Microsoft 365 Group (and associated Team) when the connected course is deleted from Moodle. Note this does not apply to courses created from SDS sync.';
$string['settings_coursesync_delete_group_on_course_sync_disabled'] = 'Delete Microsoft 365 Groups when course sync is disabled';
$string['settings_coursesync_delete_group_on_course_sync_disabled_details'] = 'If enabled, Moodle will try to delete the connected Microsoft 365 Group (and associated Team) when course sync is turned off for a Moodle course.';
$string['settings_coursesync_courses_per_task'] = 'Courses to sync per task run';
$string['settings_coursesync_courses_per_task_details'] = 'The number of courses whose Team/group sync status are to be processed at each task run.';

// Settings in the "Course sync customization" page in the "Course sync" section of the "Sync settings" tab.
$string['acp_coursesynccustom'] = 'Course sync customization';
$string['acp_coursesynccustom_enabled'] = 'Enabled';
$string['acp_coursesynccustom_enable_all'] = 'Enable course sync on all courses';
$string['acp_coursesynccustom_disable_all'] = 'Disable course sync on all courses';
$string['acp_coursesynccustom_bulk'] = 'Bulk Operations';
$string['acp_coursesynccustom_bulk_enable'] = 'Enable course sync on courses on this page';
$string['acp_coursesynccustom_bulk_disable'] = 'Disable course sync on courses on this page';
$string['acp_coursesynccustom_settings_header'] = 'Sync options';
$string['acp_coursesynccustom_new_course'] = 'Enabled by default for new course';
$string['acp_coursesynccustom_new_course_desc'] = 'If enabled, all newly created courses will have sync enabled by default.';
$string['acp_coursesynccustom_controlled_per_course'] = 'Allow configure course sync in course';
$string['acp_coursesynccustom_controlled_per_course_desc'] = 'If enabled, enrolled users in Moodle courses who are eligible to act as Team owners (having "Team owner" [local/o365:teamowner] capability) will be able to control course sync from the Microsoft block in the course.';
$string['acp_coursesynccustom_savemessage'] = 'Your changes have been saved.';
$string['acp_coursesynccustom_searchwarning'] = 'Note: Searches will lose any unsaved progress. Press save changes to ensure your changes are saved.';
$string['acp_coursesynccustom_confirm_all_action'] = 'Are you sure you want to change sync status of all courses? The action cannot be undone.';
$string['acp_coursesynccustom_sds_course'] = 'Locked for SDS course';

// Settings in the "Team / group names" section of the "Sync settings" tab.
$string['settings_secthead_team_group_name'] = 'Team / group names';
$string['settings_secthead_team_group_name_desc'] = 'If a course is configured to be synced, the name of the Team and group will be constructed as follows.<br/>
<ul>
<li>Group mail alias and team display names can be defined.</li>
<li>Microsoft 365 group will be created first using the group mailNickname and team display name configured first, and a Team will be created from the group.</li>
<li>If Azure AD group naming policies are used, groups will not be created if the display name created according to the configuration does not match the policies.</li>
<li>Changes made here will only affect future Team creation, and not existing ones.</li>
<li>All spaces will be removed from the group mail alias.</li>
<li>Only upper and lower case letters, numbers, - and _ are allowed in the group mail alias.</li>
<li>Group mail alias, including prefix and suffix cannot exceed 64 characters.</li>
<li>Mail alias of the group needs to be unique, otherwise a random 4-digit number will be appended to ensure uniqueness.</li>
</ul>';
$string['settings_team_name_prefix'] = 'Teams name prefix';
$string['settings_team_name_prefix_desc'] = '';
$string['settings_team_name_course'] = 'Course part of the Teams name';
$string['settings_team_name_course_desc'] = '';
$string['settings_team_name_suffix'] = 'Teams name suffix';
$string['settings_team_name_suffix_desc'] = '';
$string['settings_group_mail_alias_prefix'] = 'Group mail alias prefix';
$string['settings_group_mail_alias_prefix_desc'] = '';
$string['settings_group_mail_alias_course'] = 'Course part of the group mail alias';
$string['settings_group_mail_alias_course_desc'] = '';
$string['settings_group_mail_alias_suffix'] = 'Group mail alias suffix';
$string['settings_group_mail_alias_suffix_desc'] = '';
$string['settings_team_name_sample'] = 'Assume a course has:
<ul>
<li>Full name: <b>Sample course</b></li>
<li>Short name: <b>sample 15</b></li>
<li>Moodle created ID: <b>2</b></li>
<li>ID number: <b>Sample ID 15</b></li>
</ul>
Your current setting will create group using mail alias "<b>{$a->mailalias}</b>" and Team using name "<b>{$a->teamname}</b>".<br/>
Click "Save changes" button below to see how your settings will change this.';
$string['settings_main_name_option_full_name'] = 'Full name';
$string['settings_main_name_option_short_name'] = 'Short name';
$string['settings_main_name_option_id'] = 'Moodle created ID';
$string['settings_main_name_option_id_number'] = 'ID number';
$string['settings_team_name_sync'] = 'Update Teams name on course update';
$string['settings_team_name_sync_desc'] = 'If enabled, when Moodle course is updated, the name of the Team will be updated according to the latest Teams name settings.';

// Settings section headings of the "Advanced" tab.
$string['settings_header_tools'] = 'Tools';
$string['settings_secthead_advanced'] = 'Advanced Settings';
$string['settings_secthead_advanced_desc'] = 'These settings control other features of the plugin suite. Be careful! These may cause unintended effects.';

// Settings in the "Tools" section of the "Advanced" tab.
$string['settings_tools_tenants'] = 'Tenants';
$string['settings_tools_tenants_linktext'] = 'Configure additional tenants';
$string['settings_tools_tenants_details'] = 'Manage access to additional Microsoft 365 tenants.';
$string['settings_healthcheck'] = 'Health Check';
$string['settings_healthcheck_details'] = 'If something isn\'t working correctly, performing a health check can usually identify the problem and propose solutions';
$string['settings_healthcheck_linktext'] = 'Perform health check';
$string['settings_userconnections'] = 'User connections';
$string['settings_userconnections_linktext'] = 'Manage User Connections';
$string['settings_userconnections_details'] = 'Review and manage connections between Moodle and Microsoft 365 users.';
$string['settings_teamconnections'] = 'Team connections';
$string['settings_teamconnections_linktext'] = 'Manage Team Connections';
$string['settings_teamconnections_details'] = 'Review and manage connections between Moodle course and Microsoft Teams.';
$string['settings_usermatch'] = 'User Matching';
$string['settings_usermatch_details'] = 'This tool allows you to match Moodle users with Microsoft 365 users based on a custom uploaded data file.';
$string['settings_usersynccreationrestriction'] = 'User creation restriction';
$string['settings_usersynccreationrestriction_details'] = 'If enabled, only users that have the specified value for the specified Azure AD field will be created during user sync.';
$string['settings_usersynccreationrestriction_fieldval'] = 'Field value';
$string['settings_usersynccreationrestriction_o365group'] = 'Microsoft 365 Group Membership';
$string['settings_usersynccreationrestriction_regex'] = 'Value is a regular expression';
$string['settings_maintenance'] = 'Maintenance';
$string['settings_maintenance_details'] = 'Various maintenance tasks are available to resolve some common issues.';
$string['settings_maintenance_linktext'] = 'View maintenance tools';
$string['multi_tenants_settings_needs_update'] = 'Multi tenants settings needs to be updated. Please go to <a href="{$a}">multi tenants configuration page</a>.';

// Settings in "Configure additional tenants" feature of the "Advanced" tab.
$string['acp_tenants_title'] = 'Multitenancy';
$string['acp_tenants_title_desc'] = 'This page helps you set up multitenant access to Moodle from Microsoft 365.';
$string['acp_tenants_add'] = 'Add New Tenant';
$string['acp_tenants_errornotsetup'] = 'Please complete the plugin setup process before adding additional tenants.';
$string['acp_tenants_hosttenant'] = 'Host Tenant: {$a}';
$string['acp_tenants_intro'] = '<b>How Multitenancy Works:</b><br />Multitenancy allows multiple Microsoft 365 tenants to access your Moodle site. <br /><br />
    Here\'s how to get set up:
    <ol>
        <li>Log in to Moodle as a site administrator user that is not using the OpenID Connect authentication plugin.</li>
        <li>Ensure the <b>Authorization Endpoint</b> and <b>Token Endpoint</b> settings of the OpenID Connect authentication plugin are using the default non-tenant specific settings.</li>
        <li>Disable the OpenID Connect authentication plugin in Moodle. (Use <a href="{$a}/admin/settings.php?section=manageauths">the authentication plugins administration page</a>.)</li>
        <li>Navigate to Azure AD, and find the application you configured for Moodle.</li>
        <li>Enable multitenancy in the Azure AD application and save changes.</li>
        <li>Give at least one of the following Graph Delegated permissions to the app: <b>Directory.Read.All</b>, <b>Domain.Read.All</b>, or <b>Domain.ReadWrite.All</b>. Admin consent for your organisation is not required.</li>
        <li>If you are already signed in using your Microsoft account from the additional tenant, log out completely and log back in.</li>
        <li>For each tenant you want to enable, click "Add New Tenant" and log in with an administrator account from the tenant you want to enable.</li>
        <li>Once you have added all the tenants you want, re-enable the OpenID Connect authentication plugin in Moodle.</li>
        <li>You\'re done! To add additional tenants in the future, just click the "Add New Tenant" button and log in with an administrator account from that tenant.</li>
    </ol>
    <b>Important Note:</b> Azure AD multitenancy allows all Microsoft 365 tenants to access your application when enabled. Adding the tenants here allows us to restrict Moodle access to tenants you configure. <b>If you remove all the tenants from this list before disabling multitenancy in Azure AD, or enable OpenID Connect authentication in Moodle with an empty list, your Moodle site will be open to all Microsoft 365 tenants.</b>';
$string['acp_tenants_none'] = 'You have not configured any tenants. If you have enabled multitenancy in Azure AD, you\'re Moodle site may be open to all Microsoft 365 users.';
$string['acp_tenants_revokeaccess'] = 'Revoke Access';
$string['acp_tenants_tenant'] = 'Tenant';
$string['acp_tenants_actions'] = 'Actions';
$string['acp_tenants_delete'] = 'Delete';
$string['acp_tenantsadd_desc'] = 'To grant access to an additional tenant, click the button below and log in to Microsoft 365 using an adminitrator account of the new tenant. You will be returned to the list of additional tenants where the new tenant will be listed. You will then be able to use Moodle with the new tenant.';
$string['acp_tenantsadd_linktext'] = 'Proceed to Microsoft 365 login page';
$string['acp_tenants_additional_tenants'] = 'Additional tenants';
$string['acp_tenants_legacy_tenants'] = 'Legacy tenants (Action required)';
$string['acp_tenants_legacy_tenants_help'] = 'Additional tenants below added previously may not work in the updated multitenancy workflow, and need to be fixed.<br/>
Please use the "Add New Tenant" button above to add again.';

// Settings in the "Health check" feature of the "Advanced" tab.
$string['acp_healthcheck'] = 'Health Check';
$string['healthcheck_fixlink'] = 'Click here to fix it.';
$string['healthcheck_systemapiuser_title'] = 'System API User';
$string['healthcheck_systemtoken_result_notoken'] = 'Moodle does not have a token to communicate with Microsoft 365 as the system API user. This can usually be resolved by resetting the system API user.';
$string['healthcheck_systemtoken_result_noclientcreds'] = 'There are not application credentials present in the OpenID Connect plugin. Without these credentials, Moodle cannot perform any communication with Microsoft 365. Click here to visit the settings page and enter your credentials.';
$string['healthcheck_systemtoken_result_badtoken'] = 'There was a problem communicating with Microsoft 365 as the system API user. This can usually be resolved by resetting the system API user.';
$string['healthcheck_systemtoken_result_passed'] = 'Moodle can communicate with Microsoft 365 as the system API user.';
$string['healthcheck_ratelimit_title'] = 'API Throttling';
$string['healthcheck_ratelimit_result_notice'] = 'Slight throttling has been enabled to handle increased Moodle site load. <br /><br />All Microsoft 365 features are functional, this just spaces out requests slightly to prevent interruption of Microsoft 365 services. Once Moodle activity decreases, everything will return to normal. <br />(Level {$a->level} / started {$a->timestart})';
$string['healthcheck_ratelimit_result_warning'] = 'Increased throttling has been enabled to handle significant Moodle site load. <br /><br />All Microsoft 365 features are still functional, but Microsoft 365 requests may take longer to complete. Once Moodle site activity has decreased, everything will return to normal. <br />(Level {$a->level} / started {$a->timestart})';
$string['healthcheck_ratelimit_result_disabled'] = 'Rate limiting features have been disabled.';
$string['healthcheck_ratelimit_result_passed'] = 'Microsoft 365 API calls are executing at full speed.';

// Settings in the "Manage User Connections" feature of the "Advanced" tab.
$string['acp_userconnections'] = 'Manage User Connections';
$string['acp_userconnections_column_muser'] = 'Moodle User';
$string['acp_userconnections_column_o365user'] = 'Microsoft 365 User';
$string['acp_userconnections_column_status'] = 'Connection Status';
$string['acp_userconnections_column_actions'] = 'Actions';
$string['acp_userconnections_column_usinglogin'] = 'Using Login';
$string['acp_userconnections_filtering_muserfullname'] = 'Moodle user full name';
$string['acp_userconnections_filtering_musername'] = 'Moodle username';
$string['acp_userconnections_filtering_o365username'] = 'Microsoft 365 username';
$string['acp_userconnections_manualmatch_title'] = 'Manual user match';
$string['acp_userconnections_manualmatch_details'] = 'This page allows you to match a single Moodle user with a single Microsoft 365 user.';
$string['acp_userconnections_manualmatch_musername'] = 'Moodle user';
$string['acp_userconnections_manualmatch_uselogin'] = 'Log in with Microsoft 365';
$string['acp_userconnections_manualmatch_o365username'] = 'Microsoft 365 username';
$string['acp_userconnections_manualmatch_error_muserconnected'] = 'The Moodle user is already connected to a Microsoft 365 user';
$string['acp_userconnections_manualmatch_error_muserconnected2'] = 'The Moodle user is already connected to a Microsoft 365 user (2)';
$string['acp_userconnections_manualmatch_error_musermatched'] = 'The Moodle user is already matched to a Microsoft 365 user';
$string['acp_userconnections_manualmatch_error_o365usermatched'] = 'The Microsoft 365 user is already matched to another Moodle user';
$string['acp_userconnections_manualmatch_error_o365userconnected'] = 'The Microsoft 365 user is already connected to another Moodle user';
$string['acp_userconnections_resync_notconnected'] = 'This user is not connected to Microsoft 365';
$string['acp_userconnections_resync_nodata'] = 'Could not find stored Microsoft 365 information for this user.';
$string['acp_userconnections_table_connected'] = 'Connected';
$string['acp_userconnections_table_disconnect'] = 'Disconnect';
$string['acp_userconnections_table_disconnect_confirmmsg'] = 'This will disconnect the Moodle user "{$a}" from Microsoft 365. Click the link below to proceed.';
$string['acp_userconnections_table_match'] = 'Match';
$string['acp_userconnections_table_matched'] = 'Matched with existing user.<br />Awaiting completion.';
$string['acp_userconnections_table_noconnection'] = 'No Connection';
$string['acp_userconnections_table_resync'] = 'Resync';
$string['acp_userconnections_table_synced'] = 'Synced from Azure AD.<br />Awaiting initial login.';
$string['acp_userconnections_table_unmatch'] = 'Unmatch';
$string['acp_userconnections_table_unmatch_confirmmsg'] = 'This will unmatch the Moodle user "{$a}" from Microsoft 365. Click the link below to proceed.';

// Settings in the "Manage Team Connections" feature of the "Advanced" tab.
$string['acp_teamconnections'] = 'Manage Team connections';
$string['acp_teamconnection'] = 'Manage Team connection';
$string['acp_teamconnections_actions'] = 'Actions';
$string['acp_teamconnections_cache_last_updated'] = 'Teams cache was last updated at {$a->lastupdated}. Click <a href="{$a->updateurl}">here</a> to update cache.';
$string['acp_teamconnections_cache_never_updated'] = 'Teams cache was never updated. Click <a href="{$a->updateurl}">here</a> to update.';
$string['acp_teamconnections_connected_team'] = 'Connected Team';
$string['acp_teamconnections_connection_completed'] = 'The course has been successfully connected to the Team';
$string['acp_teamconnections_course_connected'] = 'The course has been successfully connected with the Team.';
$string['acp_teamconnections_current_connection'] = 'CURRENT CONNECTION';
$string['acp_teamconnections_exception_course_not_exist'] = 'The course to be connected does not exist.';
$string['acp_teamconnections_exception_invalid_team_id'] = 'Invalid Team ID';
$string['acp_teamconnections_exception_no_unified_token'] = 'Failed to get unified token to make API calls.';
$string['acp_teamconnections_exception_not_configured'] = 'Microsoft 365 is not fully configured.';
$string['acp_teamconnections_exception_team_already_connected'] = 'The Team is already connected to another course';
$string['acp_teamconnections_exception_team_creation'] = 'Error occurred when trying to create Team. Details: {$a}';
$string['acp_teamconnections_exception_team_no_owner'] = 'Cannot find suitable Team owner.';
$string['acp_teamconnections_form_connect_course'] = 'Manage Team connection for course {$a}';
$string['acp_teamconnections_form_sds_warning'] = 'NOTE: Manually connect a Moodle course with a Team created by Student Data Sync (SDS) may result in unexpected behaviour in both Moodle course sync and SDS sync, therefore should be prohibited.';
$string['acp_teamconnections_form_team'] = 'Select Team';
$string['acp_teamconnections_group_only'] = ' (Group only)';
$string['acp_teamconnections_team_missing'] = '(Team missing)';
$string['acp_teamconnections_invalid_connection'] = 'Invalid connection';
$string['acp_teamconnections_not_connected'] = 'Not connected';
$string['acp_teamconnections_sync_disabled'] = 'Course sync needs to be enabled first before managing Teams connections.';
$string['acp_teamconnections_table_connect'] = 'Connect';
$string['acp_teamconnections_table_connect_to_different_team'] = 'Connect to a different Team';
$string['acp_teamconnections_table_cannot_create_team_from_group'] = 'Cannot create Team from group - no owner';
$string['acp_teamconnections_table_missing_team'] = 'The course is connected to a non-existing team. Try update Teams cache.';
$string['acp_teamconnections_table_update'] = 'Update';
$string['acp_teamconnections_team_already_connected'] = 'The course is already connected to a Team.';
$string['acp_teamconnections_team_created'] = 'Team was created successfully for the course.';
$string['acp_teamconnections_team_exists_but_not_connected'] = 'The course is configured to be connected to a Group only, however a Team connected to the Group exists.';
$string['acp_teamconnections_teams_cache_updated'] = 'Teams cache updated successfully.';
$string['acp_teamconnections_no_owner'] = 'Add owner to connect to Teams.';

// Settings in the "User matching" feature of the "Advanced" tab.
$string['acp_usermatch'] = 'User Matching';
$string['acp_usermatch_desc'] = 'This tool allows you to match Moodle users to Microsoft 365 users. You will upload a file containing Moodle users and associated Microsoft 365 users, and a cron task will verify the data and set up the match.';
$string['acp_usermatch_upload'] = 'Step 1: Upload New Matches';
$string['acp_usermatch_upload_desc'] = 'Upload a data file containing Moodle and Microsoft 365 usernames to match Moodle users to Microsoft 365 users.<br />
<br />
This file should be a simple plain-text CSV file containing three items per line:
<ul>
<li>the Moodle username,</li>
<li>the Microsoft 365 username,</li>
<li>1 or 0, whereas 1 means changing the user\'s authentication method to OpenID Connect, and 0 means keeping the existing authentication method and use it as a linked account.</li>
</ul>
Do not include any headers or additional data.<br />
For example: <pre>moodleuser1,bob.smith@example.onmicrosoft.com,1<br />moodleuser2,john.doe@example.onmicrosoft.com,0</pre>';
$string['acp_usermatch_upload_err_badmime'] = 'Type {$a} is not supported. Please upload a plain-text CSV.';
$string['acp_usermatch_upload_err_data'] = 'Line #{$a} contained invalid data. Each line in the CSV file should have two items: the Moodle username and the Microsoft 365 username.';
$string['acp_usermatch_upload_err_fileopen'] = 'Could not open file for processing. Are the permissions correct in your Moodledata directory?';
$string['acp_usermatch_upload_err_nofile'] = 'No file was received to add to the queue.';
$string['acp_usermatch_upload_submit'] = 'Add Data File To Match Queue';
$string['acp_usermatch_matchqueue'] = 'Step 2: Match Queue';
$string['acp_usermatch_matchqueue_clearall'] = 'Clear All';
$string['acp_usermatch_matchqueue_clearerrors'] = 'Clear Errors';
$string['acp_usermatch_matchqueue_clearqueued'] = 'Clear Queued';
$string['acp_usermatch_matchqueue_clearsuccess'] = 'Clear Successful';
$string['acp_usermatch_matchqueue_column_muser'] = 'Moodle Username';
$string['acp_usermatch_matchqueue_column_o365user'] = 'Microsoft 365 Username';
$string['acp_usermatch_matchqueue_column_openidconnect'] = 'OpenID Connect';
$string['acp_usermatch_matchqueue_column_status'] = 'Status';
$string['acp_usermatch_matchqueue_desc'] = 'This table shows the current status of the match operation. Every time the <b>Process Match Queue</b> scheduled task runs, a batch of the following users will be processed.<br /><b>Note:</b> This page will not update dynamically, refresh this page to view the current status.';
$string['acp_usermatch_matchqueue_empty'] = 'The match queue is currently empty. Upload a data file using the file picker above to add users to the queue.';
$string['acp_usermatch_matchqueue_status_error'] = 'Error: {$a}';
$string['acp_usermatch_matchqueue_status_queued'] = 'Queued';
$string['acp_usermatch_matchqueue_status_success'] = 'Successful';

// Settings in the "Maintenance Tools" feature of the "Advanced" tab.
$string['acp_maintenance'] = 'Maintenance Tools';
$string['acp_maintenance_desc'] = 'These tools can help you resolve some common issues.';
$string['acp_maintenance_warning'] = 'Warning: These are advanced tools. Please use them only if you understand what you are doing.';
$string['acp_maintenance_resyncgroupusers'] = 'Resync users in Microsoft 365 groups for courses';
$string['acp_maintenance_resyncgroupusers_desc'] = 'This will resync the user membership for all Microsoft 365 groups created for all Moodle courses. This will ensure all, and only, users enrolled in the Moodle course are in the Microsoft 365 group. <br /><b>Note:</b> If you have added any additional users to a Microsoft 365 Group that are not enrolled in the associated Moodle course, they will be removed.';
$string['acp_maintenance_recreatedeletedgroups'] = 'Recreate deleted Microsoft 365 groups';
$string['acp_maintenance_recreatedeletedgroups_desc'] = 'This will check for any Microsoft Teams that may have been manually deleted and recreate them.';
$string['acp_maintenance_debugdata'] = 'Generate debug data package';
$string['acp_maintenance_debugdata_desc'] = 'This will generate a package containing various pieces of information about your Moodle and Microsoft 365 environment to assist developers in solving any issues you may have. If requested by a developer, run this tool and send the resulting file download. Note: Although this package does not contain sensitive token data, we ask that you do not post this file publicly or send it to an untrusted party.';
$string['acp_maintenance_cleandeltatoken'] = 'Cleanup User Sync Delta Tokens';
$string['acp_maintenance_cleandeltatoken_desc'] = 'If user synchronisation is not fully working after updating it user sync settings, it may be caused by an old delta sync token. Cleaning up the token will remove force a complete re-sync the next time when the user sync is run.';

// Settings in the "Resync users in Microsoft 365 groups for courses" feature in the "Maintenance Tools" feature of the
// "Advanced" tab.
$string['acp_maintenance_resyncgroupusers_course_output'] = 'Resync output';
$string['acp_maintenance_resyncgroupusers_no_course'] = 'No course connected to Microsoft 365';

// Settings in the "Recreate deleted Microsoft 365 groups" feature in the "Maintenance Tools" feature of the "Advanced" tab.
$string['acp_maintenance_recreatedeletedgroups_group_type'] = 'Moodle object type';
$string['acp_maintenance_recreatedeletedgroups_group_type_course'] = 'Course';
$string['acp_maintenance_recreatedeletedgroups_group_type_course_group'] = 'Course group';
$string['acp_maintenance_recreatedeletedgroups_course'] = 'Course';
$string['acp_maintenance_recreatedeletedgroups_course_group'] = 'Course group';
$string['acp_maintenance_recreatedeletedgroups_status'] = 'Status';
$string['acp_maintenance_recreatedeletedgroups_status_sync_disabled'] = 'Course sync is disabled';
$string['acp_maintenance_recreatedeletedgroups_status_created_success'] = 'Group was recreated';
$string['acp_maintenance_recreatedeletedgroups_status_created_fail'] = 'Group recreation failed';
$string['acp_maintenance_recreatedeletedgroups_all_groups_exist'] = 'All groups connected to Moodle course and course groups exist.';

// Settings in the "Cleanup User Sync Delta Tokens" feature in the "Maintenance Tools" feature of the "Advanced" tab.
$string['acp_maintenance_cleandeltatoken_completed'] = 'User sync delta token and skip delta token were cleaned up.';

// Settings "Advanced settings" section of the "Advanced" tab.
$string['settings_course_reset_teams'] = 'Course reset Team/group actions';
$string['settings_course_reset_teams_details'] = 'Actions to be performed on a Team or group connected to a course when the course is reset.';
$string['settings_course_reset_teams_option_do_nothing'] = 'Do nothing<br/>The Team or group is still connected to the course. User unenrolments will result in user Team or group membership removal.';
$string['settings_course_reset_teams_option_per_course'] = 'Allow settings per course<br/>This requires the Microsoft block to be added to the course. Users with capability to reset the course can choose what to do during course reset in the block.';
$string['settings_course_reset_teams_option_force_archive'] = 'Disconnect the course with the Team or group and create a new one<br/>The existing Team or group connected to the course will be renamed as configured. If a Team is connected, it will be archived. A new Team or group will be created and connected to the course.';
$string['settings_course_reset_teams_option_archive_only'] = 'Disconnect the course with the Team or group only<br />The existing Team or group connected to the course will be renamed as configured. If a Team is connected, it will be archived. No new Team or group will be created.';
$string['settings_reset_team_name_prefix'] = 'Reset Team name prefix';
$string['settings_reset_team_name_prefix_details'] = 'When resetting a course that is connected to a Team, the name of the existing connected Team will be prefixed with this.';
$string['settings_reset_group_name_prefix'] = 'Reset group name prefix';
$string['settings_reset_group_name_prefix_details'] = 'When resetting a course that is connected to a group, the name of the existing group will be prefixed with this.';
$string['settings_o365china'] = 'Microsoft 365 for China';
$string['settings_o365china_details'] = 'Check this if you are using Microsoft 365 for China.';
$string['settings_debugmode'] = 'Record debug messages';
$string['settings_debugmode_details'] = 'If enabled, information will be logged to the Moodle log that can help in identifying problems. <a href="{$a}">View recorded log messages.</a>';
$string['settings_switchauthminupnsplit0'] = 'Minimum inexact username length to switch to Microsoft 365';
$string['settings_switchauthminupnsplit0_details'] = 'If you enable the "Switch matched users to Microsoft 365 authentication" setting, this sets the minimum length for usernames without a tenant (the @example.onmicrosoft.com part) which will be switched. This helps to avoid switching accounts with generic names, like "admin", which aren\'t necessarily same in Moodle and Azure AD.';
$string['settings_photoexpire'] = 'Profile photo refresh time';
$string['settings_photoexpire_details'] = 'The number of hours to wait before refreshing profile photos. Longer times can increase performance.';
$string['settings_customtheme'] = 'Custom theme (Advanced)';
$string['settings_customtheme_desc'] = 'Recommended theme is "boost_o365teams". However, you can select different theme if you have a custom theme which is adapted to be used in the Teams tab.<br/>
Please note that a custom theme set at either course or category level would take precedence over settings here, i.e. a course would use course or category theme in Moodle app in Teams by default. This can be fixed by updating $CFG->themeorder in config.php to be "array(\'session\', \'course\', \'category\', \'user\', \'cohort\', \'site\');".';

// Settings in the "School Data Sync" tab.
$string['settings_sds_intro'] = '';
$string['settings_sds_intro_previewwarning'] = '<div class="alert"><b>This is a preview feature</b><br />Preview features may not work as intended or may break without warning. Please proceed with caution.</div>';
$string['settings_sds_intro_desc'] = 'The school data sync ("SDS") tool allows you to sync information imported into Azure AD from external SIS systems into Moodle. <a href="https://sis.microsoft.com/" target="_blank">Learn More</a><br/>
<br/>
SDS sync feature requires <b>"Application access"</b> connection method to work.<br/>
Please also ensure the Azure app used for the integration has <b>EduRoster.Read.All</b> and <b>Member.Read.Hidden</b> Microsoft Graph application permissions, which are not automatically added by the default set up. Admin consent needs to be granted for them too.<br/>
<br/>
By default, the school data sync process happens in the Moodle cron, at 3am local server time. To change this schedule, please visit the <a href="{$a}">Scheduled tasks management page.</a><br /><br />';
$string['settings_sds_coursecreation'] = 'Course Creation';
$string['settings_sds_coursecreation_desc'] = 'These options control course creation in Moodle based on information in SDS.';
$string['settings_sds_coursecreation_enabled'] = 'Synced schools';
$string['settings_sds_coursecreation_enabled_desc'] = 'Create courses for these schools.';
$string['settings_sds_teams_enabled'] = 'Teams creation enabled';
$string['settings_sds_teams_enabled_desc'] = 'This controls if Moodle courses created from syncing SDS classes are automatically connected to the Microsoft Teams of the SDS class. This should be enabled only if Teams are automatically created from the SDS classes.';
$string['settings_sds_enrolment_enabled'] = 'Enrol users';
$string['settings_sds_enrolment_enabled_desc'] = 'Enrol SDS class teachers and members into Moodle courses created from the classes.<br />
Note in order to sync SDS class teacher/member role changes to Moodle classes, <b>Advanced enrolments sync with SDS classes</b> option has to be enabled, and <b>Teacher role</b> and <b>Member role</b> settings have to be confgured.';
$string['settings_sds_sync_enrolment_to_sds'] = 'Advanced enrolments sync with SDS classes';
$string['settings_sds_sync_enrolment_to_sds_desc'] = 'This option requires <b>Enrol users</b> option to be enabled to work.<br />
If this setting is enabled, the SDS class sync will do the following:
<ul>
<li>Changes in SDS class ownership / membership status will be synced to Moodle course and reflected in Moodle user role changes.</li>
<li>User enrolment changes, such as enrolments and unenrolments, that are made in Moodle course connected to SDS classes will be synchronised back to SDS classes. The ownership / membership status of the user will depend on the "local/o365:teamowner" and "local/o365:teammember" capabilities in the course context.</li>
</ul>';
$string['settings_sds_enrolment_teacher_role'] = 'Teacher role';
$string['settings_sds_enrolment_teacher_role_desc'] = 'If the "Enrol users" option is enabled, teachers in SDS class will be enrolled in connected Moodle course with this role.';
$string['settings_sds_enrolment_student_role'] = 'Member role';
$string['settings_sds_enrolment_student_role_desc'] = 'If the "Enrol users" option is enabled, students in SDS class will be enrolled in connected Moodle course with this role.';
$string['settings_sds_profilesync_header'] = 'Profile Data Sync';
$string['settings_sds_profilesync_header_desc'] = 'These options control profile data syncing between SDS data and Moodle.';
$string['settings_sds_profilesync_disabled'] = 'Disabled';
$string['settings_sds_profilesync'] = 'Sync profile data from school';
$string['settings_sds_profilesync_desc'] = 'Select the SDS school from which Moodle synchronises SDS specific profile data.<br/>
Note synchronisation of SDS fields will only happen when running the "Sync with SDS" scheduled task, and will not happen when running the "Sync users with Azure AD" scheduled task, nor when user logs in.<br/>
Note there is a known issue in Microsoft Graph API used by this feature that certain student and teacher school profile fields are not returned, therefore are unavilable to sync even when configured.';
$string['settings_sds_noschools'] = '<div class="alert alert-info">You do not have any schools available in School data sync.</div>';
$string['settings_sds_get_schools_error'] = '<div class="alert alert-info error">Failed to get SDS schools. Check the Azure app has required permission.</div>';
$string['settings_sds_school_disabled_action'] = 'School sync disabled action';
$string['settings_sds_school_disabled_action_desc'] = 'Action to the already connected Moodle courses when sync is disabled on an SDS school.';
$string['settings_sds_school_disabled_action_keep_connected'] = 'Keep the Moodle course connected to the Team';
$string['settings_sds_school_disabled_action_disconnect'] = 'Disconnect the Moodle course with the Team';

// Settings in the "Teams Settings" tab.
$string['settings_teams_banner_1'] = 'The Moodle app for <a href="https://aka.ms/MoodleLearnTeams" target="_blank">Microsoft Teams</a> allows you to easily access and collaborate around your Moodle courses in Teams. The Moodle app also consists of a Moodle Assistant bot, which will send Moodle notifications to students and teachers and answer questions about their courses, assignments, grades and students -- right within Teams!';
$string['settings_teams_banner_2'] = 'To provision the Moodle Assistant Bot for your Microsoft 365 tenant, you need to deploy it to <a href="https://aka.ms/MoodleLearnAzure" target="_blank">Microsoft Azure</a>. If you don\'t have an active Azure subscription, you can <a href="https://aka.ms/MoodleTeamsAzureFree" target="_blank">get one for free</a> today!';
$string['settings_teams_moodle_setup_heading'] = '<h4 class="local_o365_settings_teams_h4_spacer">Setup your Moodle app for Microsoft Teams</h4>';
$string['settings_moodlesettingssetup'] = 'Configure Moodle';
$string['settings_check_moodle_settings'] = 'Check Moodle settings';
$string['settings_moodlesetup_checking'] = 'Checking...';
$string['settings_notice_oidcenabled'] = 'Open ID Connect enabled successfully';
$string['settings_notice_oidcnotenabled'] = 'Open ID Connect could not be enabled';
$string['settings_notice_oidcalreadyenabled'] = 'Open ID Connect was already enabled';
$string['settings_notice_webservicesframealreadyenabled'] = 'Webservices were already enabled and frame embedding is also allowed';
$string['settings_notice_webservicesframeenabled'] = 'Webservices enabled successfully and frame embedding is also allowed now';
$string['settings_notice_restenabled'] = 'REST Protocol enabled successfully';
$string['settings_notice_restnotenabled'] = 'REST Protocol could not be enabled';
$string['settings_notice_restalreadyenabled'] = 'REST Protocol was already enabled';
$string['settings_notice_o365serviceenabled'] = 'O365 Webservices enabled successfully';
$string['settings_notice_o365servicealreadyenabled'] = 'O365 Webservices were already enabled';
$string['settings_notice_createtokenallowed'] = 'Permission to create a web service token granted';
$string['settings_notice_createtokenalreadyallowed'] = 'Permission to create a web service token was already granted';
$string['settings_notice_createtokennotallowed'] = 'There was an issue giving permission to create a web service token';
$string['settings_notice_restusageallowed'] = 'Permission to use REST Protocol granted';
$string['settings_notice_restusagealreadyallowed'] = 'Permission to use REST Protocol was already granted';
$string['settings_notice_restusagenotallowed'] = 'There was an issue giving permission to use REST Protocol';
$string['settings_moodlesettingssetup_details'] = 'This will make sure that:
<ul class="local_o365_settings_teams_horizontal_spacer">
<li>Open ID is enabled.</li>
<li>Frame Embedding is enabled.</li>
<li>Web Services is enabled.</li>
<li>Rest Protocol is enabled.</li>
<li>Microsoft 365 Webservices is enabled.</li>
<li>Authenticated user has permission to create a web service token.</li>
<li>Authenticated user has permission to use Rest Protocol.</li>
</ul>';
$string['settings_teams_additional_instructions'] = '<p class="local_o365_settings_teams_horizontal_spacer">
Go to the <a href="https://aka.ms/MoodleBotRegistration" target="_blank">App registrations section of Azure Portal</a> and register a new app. Enter the application ID and client secret below:
</p>';
$string['settings_teams_deploy_bot_1'] = 'Once you have completed the above steps and have an active Azure subscription, click here to deploy the bot:';
$string['settings_teams_deploy_bot_2'] = 'Need help?';
$string['settings_bot_feature_enabled'] = 'Bot feature enabled';
$string['settings_bot_feature_enabled_desc'] = '<span class="warning">NOTE: There is a known issue in which if the bot feature is enabled in the Teams app, the Moodle Teams app cannot be provisioned to class teams unless the team is manually activated.</span>';
$string['settings_bot_app_id'] = 'Application ID';
$string['settings_bot_app_id_desc'] = '';
$string['settings_bot_app_password'] = 'Client Secret';
$string['settings_bot_app_password_desc'] = 'Go to \'Certificates & secrets\' section under \'Manage\' in application settings, and click \'New client secret\', and paste the one-time secret';
$string['settings_teams_download_json_desc'] = 'After entering client id and secret above, click on the button below to download JSON file for deployment.';
$string['settings_teams_download_json'] = 'Download JSON';
$string['settings_bot_webhook_endpoint'] = 'Bot webhook end point';
$string['settings_bot_webhook_endpoint_desc'] = 'Format: https://<moodlebotname\>.azurewebsites.net/api/webhook';
$string['settings_teams_moodle_app_external_id'] = 'Microsoft app ID for the Moodle Teams app';
$string['settings_teams_moodle_app_external_id_desc'] = 'This should be set to the default value, unless multiple Moodle Teams apps are required in your tenant to connect to different Moodle sites.';
$string['settings_teams_moodle_app_short_name'] = 'Teams app name';
$string['settings_teams_moodle_app_short_name_desc'] = 'This can be set as default, unless multiple Moodle Teams apps are required in your tenant to connect to different Moodle sites.';
$string['settings_bot_sharedsecret'] = 'Shared Moodle Secret';
$string['settings_bot_sharedsecret_desc'] = 'This shared secret will be also added to the \'Shared Moodle Secret\' field in the Azure Bot template to secure communication between Moodle and Bot.';
$string['settings_download_teams_tab_app_manifest'] = 'Download manifest file';
$string['settings_download_teams_tab_app_manifest_reminder'] = 'Please save all your changes before downloading the manifest.';
$string['settings_publish_manifest_instruction'] = '<a href="https://docs.microsoft.com/en-us/microsoftteams/platform/concepts/apps/apps-upload" target="_blank">Click here</a> to learn how to publish your downloaded Moodle app manifest file to all users in Teams.';
$string['settings_deploy_bot'] = 'Deploy bot to Azure';

// Settings in the "Teams Moodle app" tab.
$string['settings_moodle_app_id'] = 'Moodle app ID';
$string['settings_moodle_app_id_desc'] = 'ID of the uploaded Moodle app in Teams app catalogs.<br/>
If configured, Moodle will try to create a Moodle tab linking to the Moodle course in the "General" channel of the created/connected Team.';
$string['settings_moodle_app_id_desc_auto_id'] = '<br/>
Automatically detected value is "<span class="local_o365_settings_moodle_app_id">{$a}</span>".';
$string['settings_set_moodle_app_id_instruction'] = 'To find the Moodle app ID manually, follow these steps:
<ol>
<li>Upload the downloaded manifest file to Teams app catalog of your tenant.</li>
<li>In Teams app catalog, find the app.</li>
<li>Click the option icon of the app, which is located at the top right corner of the app image.</li>
<li>Click "Copy link".</li>
<li>In a text editor, paste the copied content. It should contain a URL such as https://teams.microsoft.com/l/app/00112233-4455-6677-8899-aabbccddeeff.</li>
</ol>
The last part of the URL, i.e. <span class="local_o365_settings_moodle_app_id">00112233-4455-6677-8899-aabbccddeeff</span>, is the app ID.';

// Settings for sharepoint features.
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Site for shared Moodle course data.';

// Settings for calendar subscriptions.
$string['calendar_setting'] = 'Enable Outlook Calendar Sync';
$string['calendar_user'] = 'Personal (User) Calendar';
$string['calendar_site'] = 'Sitewide Calendar';
$string['personal_calendar'] = 'Personal';
$string['calendar_event'] = 'View details';
$string['eventcalendarsubscribed'] = 'User subscribed to a calendar';
$string['eventcalendarunsubscribed'] = 'User unsubscribed from a calendar';

// Errors.
$string['erroracpauthoidcnotconfig'] = 'Please set application credentials in auth_oidc first.';
$string['erroracplocalo365notconfig'] = 'Please configure local_o365 first.';
$string['errorhttpclientbadtempfileloc'] = 'Could not open temporary location to store file.';
$string['errorhttpclientnofileinput'] = 'No file parameter in httpclient::put';
$string['errorcouldnotrefreshtoken'] = 'Could not refresh token';
$string['errorchecksystemapiuser'] = 'Could not get a system API user token, please run the health check, ensure that your Moodle cron is running, and refresh the system API user if necessary.';
$string['erroracpapcantgettenant'] = 'Could not get Azure AD tenant, please enter manually.';
$string['erroracpcantgettenant'] = 'Could not get OneDrive URL, please enter manually.';
$string['errorprovisioningapp'] = 'Could not provision the Moodle app in the Team.';
$string['erroro365apibadcall'] = 'Error in API call.';
$string['erroro365apibadcall_message'] = 'Error in API call: {$a}';
$string['erroro365apibadpermission'] = 'Permission not found';
$string['erroro365apicouldnotcreatesite'] = 'Problem creating site.';
$string['erroro365apicoursenotfound'] = 'Course not found.';
$string['erroro365apiinvalidtoken'] = 'Invalid or expired token.';
$string['erroro365apiinvalidmethod'] = 'Invalid httpmethod passed to apicall';
$string['erroro365apinoparentinfo'] = 'Could not find parent folder information';
$string['erroro365apinotimplemented'] = 'This should be overridden.';
$string['erroro365apinotoken'] = 'Did not have a token for the given resource and user, and could not get one. Is the user\'s refresh token expired?';
$string['erroro365apisiteexistsnolocal'] = 'Site already exists, but could not find local record.';
$string['errorusermatched'] = 'The Microsoft 365 account "{$a->aadupn}" is already matched with Moodle user "{$a->username}". To complete the connection, please log in as that Moodle user first and follow the instructions in the Microsoft block.';
$string['eventapifail'] = 'API failure';

// Privacy API.
$string['privacy:metadata:local_o365'] = 'Microsoft 365 Local Plugin';
$string['privacy:metadata:local_o365_calidmap'] = 'Information about links between Microsoft 365 calendar events and Moodle calendar events.';
$string['privacy:metadata:local_o365_calidmap:userid'] = 'The ID of the user who owns the event.';
$string['privacy:metadata:local_o365_calidmap:origin'] = 'Where the event originated. Either Moodle or Microsoft 365.';
$string['privacy:metadata:local_o365_calidmap:outlookeventid'] = 'The ID of the event in Outlook.';
$string['privacy:metadata:local_o365_calidmap:eventid'] = 'The ID of the event in Moodle.';
$string['privacy:metadata:local_o365_calsub'] = 'Information about sync subscriptions between Moodle and Outlook calendars';
$string['privacy:metadata:local_o365_calsub:user_id'] = 'The ID of the Moodle user the subscription is for';
$string['privacy:metadata:local_o365_calsub:caltype'] = 'The type of Moodle calendar (site,course,user)';
$string['privacy:metadata:local_o365_calsub:caltypeid'] = 'The associated ID of the Moodle calendar';
$string['privacy:metadata:local_o365_calsub:o365calid'] = 'The ID of the Microsoft 365 calendar';
$string['privacy:metadata:local_o365_calsub:isprimary'] = 'Whether this is the primary calendar';
$string['privacy:metadata:local_o365_calsub:syncbehav'] = 'The sync behaviour (i.e. Moodle to Outlook or Outlook to Moodle)';
$string['privacy:metadata:local_o365_calsub:timecreated'] = 'The time the subscription was created.';
$string['privacy:metadata:local_o365_connections'] = 'Information about connections between Moodle and Microsoft 365 users that have not yet been confirmed';
$string['privacy:metadata:local_o365_connections:muserid'] = 'The ID of the Moodle user';
$string['privacy:metadata:local_o365_connections:aadupn'] = 'The UPN of the Microsoft 365 user.';
$string['privacy:metadata:local_o365_connections:uselogin'] = 'Whether to switch the user\'s authentication method when completed.';
$string['privacy:metadata:local_o365_token'] = 'Information about Microsoft 365 API tokens for users';
$string['privacy:metadata:local_o365_token:user_id'] = 'The ID of the Moodle user';
$string['privacy:metadata:local_o365_token:scope'] = 'The token scope.';
$string['privacy:metadata:local_o365_token:tokenresource'] = 'The token resource.';
$string['privacy:metadata:local_o365_token:token'] = 'The token.';
$string['privacy:metadata:local_o365_token:expiry'] = 'The token\'s expiry time.';
$string['privacy:metadata:local_o365_token:refreshtoken'] = 'The refresh token.';
$string['privacy:metadata:local_o365_objects'] = 'Information about the relationship between Moodle and Microsoft 365 objects';
$string['privacy:metadata:local_o365_objects:type'] = 'The type of object (group, user, course, etc)';
$string['privacy:metadata:local_o365_objects:subtype'] = 'The subtype of object.';
$string['privacy:metadata:local_o365_objects:objectid'] = 'The Microsoft 365 object id';
$string['privacy:metadata:local_o365_objects:moodleid'] = 'The ID of the object in Moodle';
$string['privacy:metadata:local_o365_objects:o365name'] = 'The human-readable name of the object in Microsoft 365';
$string['privacy:metadata:local_o365_objects:tenant'] = 'The tenant the object belongs to (in multi-tenancy environments)';
$string['privacy:metadata:local_o365_objects:metadata'] = 'Any associated metadata';
$string['privacy:metadata:local_o365_objects:timecreated'] = 'The time the record was created.';
$string['privacy:metadata:local_o365_objects:timemodified'] = 'The time the record was modified.';
$string['privacy:metadata:local_o365_appassign'] = 'Information about Microsoft 365 app role assignments';
$string['privacy:metadata:local_o365_appassign:muserid'] = 'The ID of the Moodle user';
$string['privacy:metadata:local_o365_appassign:assigned'] = 'Whether the user has been assigned to the app';
$string['privacy:metadata:local_o365_appassign:photoid'] = 'The ID of the user\'s photo in Microsoft 365';
$string['privacy:metadata:local_o365_appassign:photoupdated'] = 'When the user\'s photo was last updated from Microsoft 365';
$string['privacy:metadata:local_o365_matchqueue'] = 'Information about Moodle user to Microsoft 365 user matching';
$string['privacy:metadata:local_o365_matchqueue:musername'] = 'The username of the Moodle user.';
$string['privacy:metadata:local_o365_matchqueue:o365username'] = 'The username of the Microsoft 365 user.';
$string['privacy:metadata:local_o365_matchqueue:openidconnect'] = 'Whether to switch the user to OpenID Connect authentication when the match is made';
$string['privacy:metadata:local_o365_matchqueue:completed'] = 'Whether the record has been processed';
$string['privacy:metadata:local_o365_matchqueue:errormessage'] = 'The error message (if any)';
$string['privacy:metadata:local_o365_calsettings'] = 'Information about calendar sync settings';
$string['privacy:metadata:local_o365_calsettings:user_id'] = 'The ID of the Moodle user';
$string['privacy:metadata:local_o365_calsettings:o365calid'] = 'The ID of the calendar in Microsoft 365';
$string['privacy:metadata:local_o365_calsettings:timecreated'] = 'The time the record was created.';

// Page "Microsoft 365 / Moodle Control Panel" in the Microsoft block.
$string['ucp_title'] = 'Microsoft 365 / Moodle Control Panel';
$string['ucp_general_intro'] = 'Here you can manage your connection to Microsoft 365.';
$string['ucp_connectionstatus'] = 'Connection Status';
$string['ucp_calsync_availcal'] = 'Available Moodle Calendars';
$string['ucp_calsync_title'] = 'Outlook Calendar sync settings';
$string['ucp_calsync_desc'] = 'Checked calendars will be synced from Moodle to your Outlook calendar.';
$string['ucp_connection_status'] = 'Microsoft 365 connection is:';
$string['ucp_connection_start'] = 'Connect to Microsoft 365';
$string['ucp_connection_stop'] = 'Disconnect from Microsoft 365';
$string['ucp_connection_options'] = 'Connection Options:';
$string['ucp_connection_desc'] = 'Here you can configure how you connect to Microsoft 365. To use Microsoft 365 features, you must be connected to a Microsoft 365 account. This can be accomplished as outlined below.';
$string['ucp_connection_aadlogin'] = 'Use your Microsoft 365 credentials to log in to Moodle<br />';
$string['ucp_connection_aadlogin_desc_rocreds'] = 'Instead of your Moodle username and password, you will enter your Microsoft 365 username and password on the Moodle login page.';
$string['ucp_connection_aadlogin_desc_authcode'] = 'Instead of entering a username and password on the Moodle login page, you will see a section that says "Login using your account on {$a}" on the login page. You will click the link and be redirected to Microsoft 365 to log in. After you have logged in to Microsoft 365 successfully, you will be returned to Moodle and logged in to your account.';
$string['ucp_connection_aadlogin_start'] = 'Start using Microsoft 365 to log in to Moodle.';
$string['ucp_connection_aadlogin_stop'] = 'Stop using Microsoft 365 to log in to Moodle.';
$string['ucp_connection_aadlogin_active'] = 'You are using the Microsoft 365 account "{$a}" to log in to Moodle.';
$string['ucp_connection_linked'] = 'Link your Moodle and Microsoft 365 accounts';
$string['ucp_connection_linked_desc'] = 'Linking your Moodle and Microsoft 365 accounts allows you to use Microsoft 365 Moodle features without changing how you log in to Moodle. <br />Clicking the link below will send you to Microsoft 365 to perform a one-time login, after which you will be returned here. You will be able to use all the Microsoft 365 features without making any other changes to your Moodle account - you will log in to Moodle as you always have.';
$string['ucp_connection_linked_active'] = 'You are linked to Microsoft 365 account "{$a}".';
$string['ucp_connection_linked_start'] = 'Link your Moodle account to a Microsoft 365 account.';
$string['ucp_connection_linked_migrate'] = 'Switch to linked account.';
$string['ucp_connection_linked_stop'] = 'Unlink your Moodle account from the Microsoft 365 account.';
$string['ucp_connection_disconnected'] = 'You are not connected to Microsoft 365.';
$string['ucp_features'] = 'Microsoft 365 Features';
$string['ucp_features_intro'] = 'Below is a list of the features you can use to enhance Moodle with Microsoft 365.';
$string['ucp_features_intro_notconnected'] = ' Some of these may not be available until you are connected to Microsoft 365.';
$string['ucp_general_intro_notconnected_nopermissions'] = 'To connect to Microsoft 365 you will need to contact your site administrator.';
$string['ucp_index_aadlogin_title'] = 'Microsoft 365 Login';
$string['ucp_index_aadlogin_desc'] = 'You can use your Microsoft 365 credentials to log in to Moodle. ';
$string['ucp_index_aadlogin_active'] = 'You are currently using Microsoft 365 to log in to Moodle';
$string['ucp_index_aadlogin_inactive'] = 'You are not currently using Microsoft 365 to log in to Moodle';
$string['ucp_index_calendar_title'] = 'Outlook Calendar sync settings';
$string['ucp_index_calendar_desc'] = 'Here you can set up syncing between your Moodle and Outlook calendars. You can export Moodle calendar events to Outlook, and bring Outlook events into Moodle.';
$string['ucp_index_connection_title'] = 'Microsoft 365 connection settings';
$string['ucp_index_connection_desc'] = 'Configure how you connect to Microsoft 365.';
$string['ucp_index_connectionstatus_title'] = 'Connection Status';
$string['ucp_index_connectionstatus_login'] = 'Click here to log in.';
$string['ucp_index_connectionstatus_usinglogin'] = 'You are currently using Microsoft 365 to log in to Moodle.';
$string['ucp_index_connectionstatus_usinglinked'] = 'You are linked to a Microsoft 365 account.';
$string['ucp_index_connectionstatus_connect'] = 'Click here to connect.';
$string['ucp_index_connectionstatus_manage'] = 'Manage Connection';
$string['ucp_index_connectionstatus_disconnect'] = 'Disconnect';
$string['ucp_index_connectionstatus_reconnect'] = 'Refresh Connection';
$string['ucp_index_connectionstatus_connected'] = 'You are currently connected to Microsoft 365';
$string['ucp_index_connectionstatus_matched'] = 'You have been matched with Microsoft 365 user <small>"{$a}"</small>. To complete this connection, please click the link below and log in to Microsoft 365.';
$string['ucp_index_connectionstatus_notconnected'] = 'You are not currently connected to Microsoft 365';
$string['ucp_index_onenote_title'] = 'OneNote';
$string['ucp_index_onenote_desc'] = 'OneNote integration allows you to use Microsoft 365 OneNote with Moodle. You can complete assignments using OneNote and easily take notes for your courses.';
$string['ucp_notconnected'] = 'Please connect to Microsoft 365 before visiting here.';
$string['ucp_status_enabled'] = 'Active';
$string['ucp_status_disabled'] = 'Not Connected';
$string['ucp_syncwith_title'] = 'Name of Outlook calendar to sync with:';
$string['ucp_syncdir_title'] = 'Sync Behavior:';
$string['ucp_syncdir_out'] = 'From Moodle to Outlook';
$string['ucp_syncdir_in'] = 'From Outlook To Moodle';
$string['ucp_syncdir_both'] = 'Update both Outlook and Moodle';
$string['ucp_options'] = 'Options';
$string['ucp_o365accountconnected'] = 'This Microsoft 365 account is already connected with another Moodle account.';

// Tasks.
$string['task_bot'] = 'Bot message task';
$string['task_calendarsyncin'] = 'Sync Microsoft 365 events in to Moodle';
$string['task_coursesync'] = 'Sync Moodle courses to Microsoft Teams';
$string['task_refreshsystemrefreshtoken'] = 'Refresh system API user refresh token';
$string['task_sds_sync'] = 'Sync with SDS';
$string['task_syncusers'] = 'Sync users with Azure AD';
$string['task_processmatchqueue'] = 'Process Match Queue';
$string['task_notifysecretexpiry'] = 'Notify site admin about Azure app secret expiry';
$string['task_processmatchqueue_err_museralreadymatched'] = 'Moodle user is already matched to a Microsoft 365 user.';
$string['task_processmatchqueue_err_museralreadyo365'] = 'Moodle user is already connected to Microsoft 365.';
$string['task_processmatchqueue_err_nomuser'] = 'No Moodle user found with this username.';
$string['task_processmatchqueue_err_noo365user'] = 'No Microsoft 365 user found with this username.';
$string['task_processmatchqueue_err_o365useralreadymatched'] = 'Microsoft 365 user is already matched to a Moodle user.';
$string['task_processmatchqueue_err_o365useralreadyconnected'] = 'Microsoft 365 user is already connected to a Moodle user.';

// Capabilities.
$string['o365:accessbotstudentdata'] = 'Access student bot data';
$string['o365:accessbotteacherdata'] = 'Access teacher bot data';
$string['o365:manageconnectionlink'] = 'Manage Connection Link';
$string['o365:manageconnectionunlink'] = 'Manage Connection Unlink';
$string['o365:viewgroups'] = 'View links to Microsoft 365 services in the Microsoft block in courses with sync enabled';
$string['o365:managegroups'] = 'View links to Microsoft 365 services and management options in the Microsoft block in courses with sync enabled';
$string['o365:teammember'] = 'Team member';
$string['o365:teamowner'] = 'Team owner';

// Cache Stores.
$string['cachedef_groups'] = 'Stores Microsoft 365 group data';

// Web service errors.
$string['webservices_error_assignnotfound'] = 'The received module\'s assignment record could not be found.';
$string['webservices_error_invalidassignment'] = 'The received assignment ID cannot be used with this webservices function.';
$string['webservices_error_modulenotfound'] = 'The received module ID could not be found.';
$string['webservices_error_sectionnotfound'] = 'The course section could not be found.';
$string['webservices_error_couldnotsavegrade'] = 'Could not save grade.';

// User sync task help text.
$string['help_user_create'] = 'Create Accounts Help';
$string['help_user_create_help'] = 'This will create users in Moodle from each user in the linked Azure Active Directory. Only users which do not currently have Moodle accounts will have accounts created. New accounts will be set up to use their Microsoft 365 credentials to log in to Moodle (using the OpenID Connect authentication plugin), and will be able to use all Microsoft 365/Moodle integration features.';
$string['help_user_update'] = 'Update All Accounts Help';
$string['help_user_update_help'] = 'This will update all users in Moodle from each user in the linked Azure Active Directory.';
$string['help_user_suspend'] = 'Suspend Accounts Help';
$string['help_user_suspend_help'] = 'This will suspend users from Moodle if they are marked as deleted in Azure Active Directory.';
$string['help_user_delete'] = 'Delete Accounts Help';
$string['help_user_delete_help'] = 'This will delete users from Moodle if they are marked as deleted in Azure Active Directory. This will only work if the suspend user option is enabled. The Moodle account will be deleted and all associated user information will be removed from Moodle. Be careful!';
$string['help_user_reenable'] = 'Re-enable Accounts Help';
$string['help_user_reenable_help'] = 'This will re-enable suspended Moodle accounts if they are returned from Azure Active Directory.';
$string['help_user_disabledsync'] = 'Sync disabled status help';
$string['help_user_disabledsync_help'] = 'This will suspend/unsuspend users in Moodle if their connected accounts in Azure Active Directory are marked prevented/allowed from login.';
$string['help_user_match'] = 'Match Accounts Help';
$string['help_user_match_help'] = 'This will look at the each user in the linked Azure Active Directory and try to match them with a user in Moodle. This match is based on usernames in Azure AD and Moodle. Matches are case-insentitive and ignore the Microsoft 365 tenant. For example, "BoB.SmiTh" in Moodle would match "bob.smith@example.onmicrosoft.com". Users who are matched will have their Moodle and Microsoft 365 accounts connected and will be able to use all Microsoft 365/Moodle integration features. The user\'s authentication method will not change unless the setting below is enabled.';
$string['help_user_matchswitchauth'] = 'Switch Matched Accounts Help';
$string['help_user_matchswitchauth_help'] = 'This requires the "Match preexisting Moodle users" setting above to be enabled. When a user is matched, enabling this setting will switch their authentication method to OpenID Connect. They will then be able to log in to Moodle with their Microsoft 365 credentials. Note: Please ensure that the OpenID Connect authentication plugin is enabled if you want to use this setting.';
$string['help_user_appassign'] = 'Assign Users To Application Help';
$string['help_user_appassign_help'] = 'This will cause all the Azure AD accounts with matching Moodle accounts to be assigned to the Azure application created for this Moodle installation, if not already assigned.';
$string['help_user_photosync'] = 'Sync Microsoft 365 Profile Photos (Cron) Help';
$string['help_user_photosync_help'] = 'This will cause all users\' Moodle profile photos to get synced with their Microsoft 365 profile photos.';
$string['help_user_photosynconlogin'] = 'Sync Microsoft 365 Profile Photos (Login) Help';
$string['help_user_photosynconlogin_help'] = 'This will cause a user\'s Moodle profile photo to get synced with their Microsoft 365 profile photo when that user logs in. Note this requires user visiting a page containing the Microsoft block in Moodle.';
$string['help_user_nodelta'] = 'Perform a full sync help';
$string['help_user_nodelta_help'] = 'By default, user sync will only sync changes from Azure AD. Checking this option will force a full user sync each time.';
$string['help_user_emailsync'] = 'Sync azure usernames to moodle emails Help';
$string['help_user_emailsync_help'] = 'Enabling this option will match azure usernames to moodle emails, instead of the default behaviour which is azure usernames to moodle usernames.';
$string['help_user_tzsync'] = 'Sync Outlook timezone (Cron) Help';
$string['help_user_tzsync_help'] = 'This will cause all users\' Moodle timezone to get synced with their Outlook timezone preference.';
$string['help_user_tzsynconlogin'] = 'Sync Outlook timezone (Login) Help';
$string['help_user_tzsynconlogin_help'] = 'This will cause a user\'s Moodle timezone to get synced with their Outlook timezone preference. Note this requires user visiting a page containing the Microsoft block in Moodle.';
$string['help_user_guestsync'] = 'Sync guest users Help';
$string['help_user_guestsync_help'] = 'If enabled, guest users in Azure AD will be synced to Moodle in the user sync task.';

// Bot feature.
$string['list_of_absent_students'] = 'This is the list of students that were absent this month:';
$string['list_of_assignments_grades_compared'] = 'This is the list of your assignments grades compared with class average:';
$string['list_of_assignments_needs_grading'] = 'This is the list of the assignments that need grading:';
$string['list_of_due_assignments'] = 'This is the list of due assignments';
$string['list_of_incomplete_assignments'] = 'This is the list of the assignments that are incomplete:';
$string['list_of_last_logged_students'] = 'This is the list of last logged students:';
$string['list_of_late_submissions'] = 'This is the list of students who made late submissions:';
$string['list_of_latest_logged_students'] = 'This is the list of latest logged students:';
$string['list_of_recent_grades'] = 'This is the list of your recent grades:';
$string['list_of_students_with_least_score'] = 'This is the list of students with least score in the latest assignment:';
$string['list_of_students_with_name'] = 'These are the students with the name {$a}:';
$string['assignment'] = 'Assignment';
$string['course_assignment_submitted_due'] = 'Course - {$a->course} &nbsp; |  &nbsp; Assignment -{$a->assignment} <br />
                        Submitted on - {$a->submittedon} &nbsp; |  &nbsp; Due date - {$a->duedate}';
$string['due_date'] = 'Due date - {$a}';
$string['grade_date'] = 'Grade - {$a->grade} &nbsp; | &nbsp; Date - {$a->date}';
$string['help_message'] = 'Hi there! I am your Moodle assistant. You can ask me the following questions:';
$string['last_login_date'] = 'Last login date - {$a}';
$string['never'] = 'Never';
$string['no_absent_users_found'] = 'No absent users found';
$string['no_assignments_for_grading_found'] = 'No assignments for grading found';
$string['no_assignments_found'] = 'No assignments found';
$string['no_due_assignments_found'] = 'No due assignments found';
$string['no_due_incomplete_assignments_found'] = 'No due and incomplete assignments found';
$string['no_graded_assignments_found'] = 'No graded assignments found';
$string['no_grades_found'] = 'No grades found';
$string['no_late_submissions_found'] = 'No late submissions found';
$string['no_users_found'] = 'No users found';
$string['no_user_with_name_found'] = 'No user with such name found';
$string['participants_submitted_needs_grading'] = 'Participants - {$a->participants}  &nbsp; |  &nbsp; Submitted - {$a->submitted}  &nbsp; |  &nbsp;
                        Needs grading - {$a->needsgrading}';
$string['pending_submissions_due_date'] = 'Pending submissions - {$a->incomplete} / {$a->total} &nbsp; |  &nbsp; Due - {$a->duedate}';
$string['sorry_do_not_understand'] = 'Sorry, I do not understand';
$string['question_student_assignments_compared'] = "How did I do in my latest assignments compared to the class?";
$string['question_student_assignments_due'] = "Which assignments are due next?";
$string['question_student_latest_grades'] = "What are the latest grades I've received?";
$string['question_teacher_absent_students'] = "Which students have been absent this month?";
$string['question_teacher_assignments_incomplete_submissions'] = "How many assignments have incomplete submissions?";
$string['question_teacher_assignments_for_grading'] = "Which assignments are yet to be graded?";
$string['question_teacher_last_logged_students'] = "Which students have logged into Moodle (most recent first)?";
$string['question_teacher_late_submissions'] = "Which students have made late submissions?";
$string['question_teacher_latest_logged_students'] = "Which students have logged into Moodle (oldest first)?";
$string['question_teacher_least_scored_in_assignment'] = "Which students scored the least in the last assignment?";
$string['question_teacher_student_last_logged'] = "When did Firstname Lastname last log into moodle?";
$string['your_grade'] = 'Your grade - {$a}';
$string['your_grade_class_grade'] = 'Your grade - {$a->usergrade} &nbsp; |  &nbsp; Class average grade - {$a->classgrade}';
$string['error_missing_app_id'] = 'Missing Application ID setting.';
$string['error_missing_bot_settings'] = 'Bot feature is enabled, but bot settings are missing.';
$string['errornodirectaccess'] = 'Direct access to the page is prohibited';

// Teams page.
$string['teams_no_course'] = 'You don\'t have any course to add.';
$string['tab_name'] = 'Tab name';
$string['tab_moodle'] = 'Moodle';
$string['sso_login'] = 'Login to Microsoft 365';
$string['other_login'] = 'Login manually';
$string['course_selector_label'] = "Select existing course";

// Notifications to site admin about Azure app secret.
$string['notification_subject_secret_expired'] = 'Action required: Azure app secret expired';
$string['notification_content_secret_expired'] = 'Dear site administrator,

The Azure app secret used in your Moodle and Microsoft 365 integration has expired.
Please create a new secret in Azure portal and update it in the integration configuration in order to restore the integration.';
$string['notification_subject_secret_almost_expired'] = 'Action required: Azure app secret expiring soon';
$string['notification_content_secret_almost_expired'] = 'Dear site administrator,

The Azure app secret used in your Moodle and Microsoft 365 integration will expire in {$a}.
Please create a new secret in Azure portal and update it in the integration configuration in order to avoid integration disruption.';
$string['notification_days_less_than_one_day'] = 'less than 1 day';
$string['notification_days_one_day'] = '1 day';
$string['notification_days_days'] = '{$a} days';
$string['notification_subject_invalid_secret'] = 'Action required: invalid Azure app secret found';
$string['notification_content_invalid_secret'] = 'Dear site administrator,

The Azure app secret used in your Moodle and Microsoft 365 integration seems to be invalid. This can either be caused by the secret expired, or it has been deleted.  
Please review the secret to ensure the integration works as expected.';

// Misc.
$string['spsite_group_contributors_desc'] = 'All users who have access to manage files for course {$a}';
