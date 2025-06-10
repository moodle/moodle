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
 * English Language Strings.
 *
 * @package block_microsoft
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'Microsoft block';
$string['contactadmin'] = 'Contact administrator for more information.';
$string['error_nomoodlenotebook'] = 'Could not find your Moodle notebook.';
$string['linkonedrive'] = 'My OneDrive';
$string['linkonenote'] = 'My OneNote Notebook';
$string['linksways'] = 'My Sways';
$string['linkmsstream'] = 'Microsoft Stream';
$string['linkmsteams'] = 'Microsoft Teams';
$string['linkemail'] = 'My Email';
$string['linkonenote_unavailable'] = 'OneNote unavailable';
$string['linksharepoint'] = 'Course SharePoint site';
$string['linkoutlook'] = 'Outlook Calendar sync settings';
$string['linkprefs'] = 'Edit settings';
$string['linkconnection'] = 'Microsoft 365 connection settings';
$string['microsoft'] = 'Microsoft';
$string['microsoft:addinstance'] = 'Add a new Microsoft block';
$string['microsoft:myaddinstance'] = 'Add a New Microsoft block to the My Moodle page';
$string['notebookname'] = 'Moodle Notebook';
$string['opennotebook'] = 'Open your notebook';
$string['workonthis'] = 'Work on this';
$string['o365matched_title'] = 'You are <span style="color: #960">almost</span> connected to Microsoft 365';
$string['o365matched_desc'] = 'You have been matched with the Microsoft 365 user <b>"{$a}"</b>';
$string['o365matched_complete_userpass'] = 'To complete the connection, please enter the password for this Microsoft 365 user and click "Connect"';
$string['o365matched_complete_authreq'] = 'To complete the connection, please click the link below and log in to this Microsoft 365 account.';
$string['o365connected'] = '{$a->firstname} you are currently <span class="notifysuccess">connected</span> to Microsoft 365';
$string['notconnected'] = 'You are <span class="notifyproblem">not connected</span> to any Microsoft services.';
$string['course_connected_to_team'] = 'The course is <span class="notifysuccess">connected</span> to a <b>Team</b>.';
$string['course_connected_to_team_pending'] = 'The course is configured to be connected to a <b>Team</b>, but the Team creation is <span class="notifyproblem">pending</span>.';
$string['course_connected_to_group_pending'] = 'The course is configured to be connected to a <b>Team</b>, but the group creation is <span class="notifyproblem">pending</span>.';
$string['course_not_connected'] = 'The course is <span class="notifyproblem">not connected</span> to any Microsoft services.';
$string['cachedef_onenotenotebook'] = 'Stores OneNote notebook.';
$string['msalogin'] = 'Log in with Microsoft Account';
$string['logintoo365'] = 'Log in to Microsoft 365';
$string['connecttoo365'] = 'Connect to Microsoft 365';
$string['install_office'] = 'Install Office';

// Configuration settings.
$string['settings_showemail'] = 'Show "My Email"';
$string['settings_showemail_desc'] = 'Enable or disable the "My Email" link in the block.';
$string['settings_showmydelve'] = 'Show "My Delve"';
$string['settings_showmydelve_desc'] = 'Enable or disable the "My Delve" link in the block.';
$string['settings_showmyforms'] = 'Show "My Forms"';
$string['settings_showmyforms_desc'] = 'Enable or disable the "My Forms" link in the block.';
$string['settings_showmyforms_default'] = 'https://forms.office.com/Pages/DesignPage.aspx#';
$string['settings_showo365download'] = 'Show "Install Office"';
$string['settings_showo365download_desc'] = 'Enable or disable the "Install Office" link in the block.';
$string['settings_showdocsdotcom'] = 'Show "My Docs.com"';
$string['settings_showdocsdotcom_desc'] = 'Enable or disable the "My Docs.com" link in the block.';
$string['settings_showsways'] = 'Show "My Sways"';
$string['settings_showsways_desc'] = 'Enable or disable the "My Sways" link in the block.';
$string['settings_showmsstream'] = 'Show "Microsoft Stream"';
$string['settings_showmsstream_desc'] = 'Enable or disable the "Microsoft Stream" link in the block.';
$string['settings_showmsteams'] = 'Show "Microsoft Teams"';
$string['settings_showmsteams_desc'] = 'Enable or disable the "Microsoft Teams" link in the block.';
$string['settings_showonedrive'] = 'Show "My OneDrive"';
$string['settings_showonedrive_desc'] = 'Enable or disable the "My OneDrive" link in the block.';
$string['settings_showonenotenotebook'] = 'Show "My OneNote Notebook"';
$string['settings_showonenotenotebook_desc'] = 'Enable or disable the "My OneNote Notebook" link in the block.';
$string['settings_showoutlooksync'] = 'Show "Outlook Calendar sync settings"';
$string['settings_showoutlooksync_desc'] = 'Enable or disable the "Outlook Calendar sync settings" link in the block.';
$string['settings_showpreferences'] = 'Show "Edit Settings"';
$string['settings_showpreferences_desc'] = 'Enable or disable the "Edit Settings" link in the block.';
$string['settings_showo365connect'] = 'Show "Connect to Microsoft 365"';
$string['settings_showo365connect_desc'] = 'Enable or disable the "Connect to Microsoft 365" link in the block. <br /><b>Note:</b> This is shown to users who are not connected to Microsoft 365 and takes them to the page that allows them to set up a connection.';
$string['settings_showmanageo365conection'] = 'Show "Microsoft 365 connection settings"';
$string['settings_showmanageo365conection_desc'] = 'Enable or disable the "Microsoft 365 connection settings" link in the block. <br /><b>Note:</b> This is shown to Microsoft 365 connected users and takes them to connection management page.';
$string['settings_showcoursespsite'] = 'Show "Course SharePoint site"';
$string['settings_showcoursespsite_desc'] = 'Enable or disable the "Course SharePoint site" link in the block. <br /><b>Note:</b> This is shown in the block when viewing a course that has an associated SharePoint site.';
$string['defaultprofile'] = 'Profile image';
$string['settings_geto365link'] = 'Install Office download URL';
$string['settings_geto365link_desc'] = 'The URL to use for the "Install Office" link.';
$string['settings_geto365link_default'] = 'https://portal.office.com/OLS/MySoftware.aspx';

$string['linkmydelve'] = 'My Delve';
$string['linkmyforms'] = 'My Forms';
$string['privacy:metadata'] = 'The Microsoft block only displays links to various features and services.';

// Course sync features.
$string['course_feature_team'] = 'Team';
$string['course_feature_conversations'] = 'Outlook conversations';
$string['course_feature_onedrive'] = 'OneDrive files';
$string['course_feature_calendar'] = 'Outlook calendar';
$string['course_feature_notebook'] = 'Class notebook';

// Course sync settings.
$string['configure_sync'] = 'Configure course sync';
$string['error_course_sync_not_configurable_per_course'] = 'Course sync cannot be configured per course.';
$string['sync_page_heading'] = 'Sync course {$a} to Microsoft Teams';
$string['configure_course_sync'] = 'Configure course sync to Microsoft Team';
$string['course_sync_option'] = 'Sync option';
$string['course_sync_option_disabled'] = 'Disabled';
$string['course_sync_option_enabled'] = 'Enabled';
$string['sync_setting_saved'] = 'Course sync option is saved';

// Course reset status.
$string['course_reset_disconnect_and_create_new'] = 'When the course is reset, the Team  connected to the course will be renamed as configured, and archived. A new Team will be created and connected to the course.';
$string['course_reset_do_nothing'] = 'When the course is reset, the connection between the course and the Team will remain. All user changes made to the course will be synced to the Team.';
$string['course_reset_disconnect_only'] = 'When the course is reset, the Team  connected to the course will be renamed as configured, and archived. No new Team will be created.';
$string['configure_reset'] = 'Choose Microsoft Team action on course reset';

// Course reset settings page.
$string['reset_page_heading'] = 'Course reset action for {$a}';
$string['configure_course_reset'] = 'Configure course reset setting';
$string['course_reset_option'] = 'Action to the Team when resetting the course';
$string['reset_option_do_nothing'] = 'Do nothing.<br/>The Team will still be connected to the course. User unenrolments will result in user Team ownership/membership removal.';
$string['reset_option_disconnect_and_create_new'] = 'Disconnect and create a new Team.<br/>The existing Team connected to the course will be renamed as configured, and archived. A new Team will be created and connected to the course.';
$string['reset_option_disconnect_only'] = 'Disconnect only.<br/>The existing Team connected to the course will be renamed as configured, and archived. No new Team will be created.';
$string['reset_setting_saved'] = 'Course reset setting has been saved.';
$string['error_site_course_sync_disabled'] = 'Course sync is not enabled';
$string['error_reset_setting_not_managed_per_course'] = 'Reset action configuration is centrally managed by site administrators.';
$string['error_connected_team_missing'] = 'The course is configured to be synced, but the Team cannot be found.';
$string['error_course_sync_disabled'] = 'The course is not configured to be synced.';
