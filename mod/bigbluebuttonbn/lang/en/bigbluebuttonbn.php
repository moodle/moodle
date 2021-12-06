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
 * Language File.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 */
defined('MOODLE_INTERNAL') || die();

$string['activityoverview'] = 'You have upcoming bigbluebuttonbn sessions';
$string['bbbduetimeoverstartingtime'] = 'The due time for this activity must be greater than the starting time';
$string['bbbdurationwarning'] = 'The maximum duration for this session is %duration% minutes.';
$string['bbbrecordwarning'] = 'This session may be recorded.';
$string['bbbrecordallfromstartwarning'] = 'This session is being recorded from start.';
$string['bigbluebuttonbn:addinstance'] = 'Add a new bigbluebuttonbn room/activity';
$string['bigbluebuttonbn:join'] = 'Join a bigbluebuttonbn meeting';
$string['bigbluebuttonbn:view'] = 'View a room/activity';
$string['bigbluebuttonbn:addinstancewithmeeting'] = 'Create instances with live meeting capabilities';
$string['bigbluebuttonbn:addinstancewithrecording'] = 'Create instances with recording capabilities';
$string['bigbluebuttonbn:managerecordings'] = 'Manage recordings';
$string['bigbluebuttonbn:publishrecordings'] = 'Publish recordings';
$string['bigbluebuttonbn:unpublishrecordings'] = 'Unpublish recordings';
$string['bigbluebuttonbn:protectrecordings'] = 'Protect recordings';
$string['bigbluebuttonbn:unprotectrecordings'] = 'Unprotect recordings';
$string['bigbluebuttonbn:deleterecordings'] = 'Delete recordings';
$string['bigbluebuttonbn:importrecordings'] = 'Import recordings';
$string['bigbluebuttonbn'] = 'BigBlueButton';
$string['cannotperformaction'] = 'Cannot perform action {$a} on this recording';
$string['indicator:cognitivedepth'] = 'BigBlueButton cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in a BigBlueButton activity.';
$string['indicator:socialbreadth'] = 'BigBlueButton social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in a BigBlueButton activity.';
$string['modulename'] = 'BigBlueButton';
$string['modulenameplural'] = 'BigBlueButton';
$string['modulename_help'] = 'BigBlueButton lets you create from within Moodle links to real-time on-line classrooms using BigBlueButton, an open source web conferencing system for distance education.

Using BigBlueButton you can specify for the title, description, calendar entry (which gives a date range for joining the session), groups, and details about the recording of the on-line session.';
$string['modulename_link'] = 'BigBlueButton/view';
$string['nosuchinstance'] = 'No such an instance {$a->entity} with id: {$a->id} ';
$string['pluginadministration'] = 'BigBlueButton administration';
$string['pluginname'] = 'BigBlueButton';

$string['removedevents'] = 'Deleted events';
$string['removedtags'] = 'Deleted tags';
$string['removedlogs'] = 'Deleted custom logs';
$string['removedrecordings'] = 'Deleted recordings';
$string['resetevents'] = 'Delete events';
$string['resettags'] = 'Delete tags';
$string['resetlogs'] = 'Delete custom logs';
$string['resetrecordings'] = 'Delete recordings';
$string['resetlogs_help'] = 'Deleting the logs will cause the lost of references to recordings';
$string['resetrecordings_help'] = 'Deleting the recordings will make them inaccessible from anywhere and it can not be undone';

$string['search:activity'] = 'BigBlueButton - activity information';
$string['search:tags'] = 'BigBlueButton - tags information';
$string['settings'] = 'BigBlueButton settings';
$string['privacy:metadata:bigbluebuttonbn'] = 'Stores the configuration for the room or activity that defines the features and general behaviour of the BigBlueButton session.';
$string['privacy:metadata:bigbluebuttonbn:participants'] = 'A list of rules that define the role users will in the live meeting. A user ID may be stored as permissions can be granted per role or user.';
$string['privacy:metadata:bigbluebuttonbn_logs'] = 'Stores events triggered when using the plugin.';
$string['privacy:metadata:bigbluebuttonbn_logs:userid'] = 'The user ID of the user who triggered the event.';
$string['privacy:metadata:bigbluebuttonbn_logs:timecreated'] = 'The time at which the log was created.';
$string['privacy:metadata:bigbluebuttonbn_logs:meetingid'] = 'The meeting ID the user had access to.';
$string['privacy:metadata:bigbluebuttonbn_logs:log'] = 'The type of event triggered by the user.';
$string['privacy:metadata:bigbluebuttonbn_logs:meta'] = 'May include extra information related to the meeting or the recording afected by the event.';
$string['privacy:metadata:bigbluebutton'] = 'In order to create and join BigBlueButton sessions, user data needs to be exchanged with the server.';
$string['privacy:metadata:bigbluebutton:userid'] = 'The userid of the user accessing the BigBlueButton server.';
$string['privacy:metadata:bigbluebutton:fullname'] = 'The fullname of the user accessing the BigBlueButton server.';
$string['privacy:metadata:bigbluebuttonbn_recordings'] = 'Stores metadata about recordings.';
$string['privacy:metadata:bigbluebuttonbn_recordings:userid'] = 'The user ID of the user who last changed a recording.';

$string['completionattendance'] = 'Student must attend the meeting for:';
$string['completionattendance_desc'] = 'Student must attend the meeting and remain in the session for at least {$a} minute(s)';
$string['completionattendance_event_desc'] = 'Student has attended the meeting or remained in the session for at least {$a} minute(s)';
$string['completionattendancegroup'] = 'Require attendance';
$string['completionattendancegroup_help'] = 'Attending the meeting for (n) minutes is required for completion';

$string['completionengagementchats'] = 'Require chats events';
$string['completionengagementchats_desc'] = 'Student must take participate into {$a} chat(s) events to complete it';
$string['completionengagementchats_event_desc'] = 'Has raised {$a} Chat(s) event(s)';
$string['completionengagementtalks'] = 'Talk events';
$string['completionengagementtalks_desc'] = 'Student must talk {$a} time(s) to complete it';
$string['completionengagementtalks_event_desc'] = 'Has raised {$a} Talk(s) event(s)';
$string['completionengagementraisehand'] = 'Require raise hand events';
$string['completionengagementraisehand_desc'] = 'Student must raise hand {$a} time(s) to complete it';
$string['completionengagementraisehand_event_desc'] = 'Has raised {$a} Raise Hand event(s)';
$string['completionengagementpollvotes'] = 'Poll votes';
$string['completionengagementpollvotes_desc'] = 'Student must vote into polls {$a} time(s) to complete it';
$string['completionengagementpollvotes_event_desc'] = 'Has raised {$a} Poll vote(s) events';
$string['completionengagementemojis'] = 'Emojis';
$string['completionengagementemojis_desc'] = 'Student must send {$a} emoji(s) into polls to complete it';
$string['completionengagementemojis_event_desc'] = 'Has raised {$a} Emoji(s) event(s)';

$string['completionengagement_desc'] = 'Student must engage in activities during the meeting';
$string['completionengagementgroup'] = 'Require engagement';
$string['completionengagementgroup_help'] = 'Active participation during the session is required for completion';

$string['completionupdatestate'] = 'Completion update state';
$string['completionvalidatestate'] = 'Validate completion';
$string['completionvalidatestatetriggered'] = 'Validate completion has been triggered.';

$string['completionview'] = 'Require view';
$string['completionview_desc'] = 'Student must join a meeting or play a recording to complete it.';
$string['completionview_event_desc'] = 'Has joined the meeting or played a recording {$a} time(s).';
$string['sendnotification'] = 'Send notification';

$string['minute'] = 'minute';
$string['minutes'] = 'minutes';

$string['config_general'] = 'General configuration';
$string['config_general_description'] = 'These settings are <b>always</b> used';
$string['config_server_url'] = 'BigBlueButton Server URL';
$string['config_server_url_description'] = 'The URL of your BigBlueButton server must end with /bigbluebutton/. (This default URL is for a BigBlueButton server provided by Blindside Networks that you can use for testing.)';
$string['config_shared_secret'] = 'BigBlueButton Shared Secret';
$string['config_shared_secret_description'] = 'The security secret of your BigBlueButton server. (This default secret is for a BigBlueButton server provided by Blindside Networks that you can use for testing.)';

$string['config_recording'] = 'Configuration for "Record meeting" feature';
$string['config_recording_description'] = 'These settings are feature specific';
$string['config_recording_default'] = 'Recording feature enabled by default';
$string['config_recording_default_description'] = 'If enabled the sessions created in BigBlueButton will have recording capabilities.';
$string['config_recording_editable'] = 'Recording feature can be edited';
$string['config_recording_editable_description'] = 'If checked the interface includes an option for enable and disable the recording feature.';
$string['config_recording_protect_editable'] = 'Protected recordings state can be edited';
$string['config_recording_protect_editable_description'] = 'If checked the interface includes an option for protecting/unprotecting recordings.';
$string['config_recording_icons_enabled'] = 'Icons for recording management';
$string['config_recording_icons_enabled_description'] = 'When enabled, the recording management panel shows icons for the publish/unpublish and delete actions.';
$string['config_recording_all_from_start_default'] = 'Record all from start';
$string['config_recording_all_from_start_default_description'] = 'If checked the meeting will record to start';
$string['config_recording_all_from_start_editable'] = 'Record all from start can be edited';
$string['config_recording_all_from_start_editable_description'] = 'If checked the interface includes an option for enable and disable the record all from start feature.';
$string['config_recording_hide_button_default'] = 'Hide recording button';
$string['config_recording_hide_button_default_description'] = 'If checked the button for record will be hide';
$string['config_recording_hide_button_editable'] = 'Hide recording button can be edited';
$string['config_recording_hide_button_editable_description'] = 'If checked the interface includes an option for enable and disable the hide recording button feature.';
$string['config_recording_refresh_period'] = 'Recording refresh period (in seconds)';
$string['config_recording_refresh_period_description'] = 'To avoid querying the Bigbluebutton server too often we cache information
 for recording. This is the refresh period in seconds which will decide how often we can/would refresh remote information for a given recording. Defaults to 300s (5mins).';
$string['config_recordings'] = 'Configuration for "Show recordings" feature';
$string['config_recordings_description'] = 'These settings are feature specific';
$string['config_recordings_general'] = 'Show recording settings';
$string['config_recordings_general_description'] = 'These settings are used only when showing recordings';
$string['config_recordings_html_default'] = 'UI as html is enabled by default';
$string['config_recordings_html_default_description'] = 'If enabled the recording table is shown in plain HTML by default.';
$string['config_recordings_html_editable'] = 'UI as html feature can be edited';
$string['config_recordings_html_editable_description'] = 'UI as html value by default can be edited when the instance is added or updated.';
$string['config_recordings_deleted_default'] = 'Include recordings from deleted activities enabled by default';
$string['config_recordings_deleted_default_description'] = 'If enabled the recording table will include the recordings belonging to deleted activities if there is any.';
$string['config_recordings_deleted_editable'] = 'Include recordings from deleted activities feature can be edited';
$string['config_recordings_deleted_editable_description'] = 'Include recordings from deleted activities by default can be edited when the instance is added or updated.';
$string['config_recordings_imported_default'] = 'Show only imported links enabled by default';
$string['config_recordings_imported_default_description'] = 'If enabled the recording table will include only the imported links to recordings.';
$string['config_recordings_imported_editable'] = 'Show only imported links feature can be edited';
$string['config_recordings_imported_editable_description'] = 'Show only imported links by default can be edited when the instance is added or updated.';
$string['config_recordings_preview_default'] = 'Preview is enabled by default';
$string['config_recordings_preview_default_description'] = 'If enabled the table includes a preview of the presentation.';
$string['config_recordings_preview_editable'] = 'Preview feature can be edited';
$string['config_recordings_preview_editable_description'] = 'Preview feature can be edited when the instance is added or updated.';
$string['config_recordings_sortorder'] = 'Order the recordings in ascending order.';
$string['config_recordings_sortorder_description'] = 'By default recordings are displayed in descending order. When checked they will be sorted in ascending order.';
$string['config_recordings_validate_url'] = 'Validate URL';
$string['config_recordings_validate_url_description'] = 'If checked the playback URL will be validated before the user access it.';

$string['config_importrecordings'] = 'Configuration for "Import recordings" feature';
$string['config_importrecordings_description'] = 'These settings are feature specific';
$string['config_importrecordings_enabled'] = 'Import recordings enabled';
$string['config_importrecordings_enabled_description'] = 'When this and the recording feature are enabled, it is possible to import recordings from different courses into an activity.';
$string['config_importrecordings_from_deleted_enabled'] = 'Import recordings from deleted activities enabled';
$string['config_importrecordings_from_deleted_enabled_description'] = 'When this and the import recording feature are enabled, it is possible to import recordings from activities that are no longer in the course.';

$string['config_waitformoderator'] = 'Configuration for "Wait for moderator" feature';
$string['config_waitformoderator_description'] = 'These settings are feature specific';
$string['config_waitformoderator_default'] = 'Wait for moderator enabled by default';
$string['config_waitformoderator_default_description'] = 'Wait for moderator feature is enabled by default when a new room or conference is added.';
$string['config_waitformoderator_editable'] = 'Wait for moderator feature can be edited';
$string['config_waitformoderator_editable_description'] = 'Wait for moderator value by default can be edited when the room or conference is added or updated.';
$string['config_waitformoderator_ping_interval'] = 'Wait for moderator ping (seconds)';
$string['config_waitformoderator_ping_interval_description'] = 'When the wait for moderator feature is enabled, the client pings for the status of the session each [number] seconds. This parameter defines the interval for requests made to the Moodle server';
$string['config_waitformoderator_cache_ttl'] = 'Wait for moderator cache TTL (seconds)';
$string['config_waitformoderator_cache_ttl_description'] = 'To support a heavy load of clients this plugin makes use of a cache. This parameter defines the time the cache will be kept before the next request is sent to the BigBlueButton server.';

$string['config_voicebridge'] = 'Configuration for "Voice bridge" feature';
$string['config_voicebridge_description'] = 'These settings enable or disable options in the UI and also define default values for these options.';
$string['config_voicebridge_editable'] = 'Conference voice bridge can be edited';
$string['config_voicebridge_editable_description'] = 'Conference voice bridge number can be permanently assigned to a room conference. When assigned, the number can not be used by any other room or conference';

$string['config_preuploadpresentation'] = 'Configuration for "Pre-upload presentation" feature';
$string['config_preuploadpresentation_description'] = 'These settings enable or disable options in the UI and also define default values for these options. The feature works only if the Moodle server is accessible to BigBlueButton..';
$string['config_preuploadpresentation_enabled'] = 'Pre-uploading presentation enabled';
$string['config_preuploadpresentation_enabled_description'] = 'Preupload presentation feature is enabled in the UI when the room or conference is added or updated.';

$string['config_presentation_default'] = 'Default file for "Pre-upload presentation" feature';
$string['config_presentation_default_description'] = 'This setting allow to select a file to use as default in all BBB instances if "Pre-upload presentation" is enabled.';

$string['config_participant'] = 'Participant configuration';
$string['config_participant_description'] = 'These settings define the role by default for participants in a conference.';
$string['config_participant_moderator_default'] = 'Moderator by default';
$string['config_participant_moderator_default_description'] = 'This rule is used by default when a new room is added.';

$string['config_userlimit'] = 'Configuration for "User limit" feature';
$string['config_userlimit_description'] = 'These settings enable or disable options in the UI and also define default values for these options.';
$string['config_userlimit_default'] = 'User limit enabled by default';
$string['config_userlimit_default_description'] = 'The number of users allowed in a session by default when a new room or conference is added. If the number is set to 0, no limit is established';
$string['config_userlimit_editable'] = 'User limit feature can be edited';
$string['config_userlimit_editable_description'] = 'User limit value by default can be edited when the room or conference is added or updated.';

$string['config_scheduled'] = 'Configuration for "Scheduled sessions"';
$string['config_scheduled_description'] = 'These settings define some of the behaviour by default for scheduled sessions.';
$string['config_scheduled_pre_opening'] = 'Accessible before opening time (minutes)';
$string['config_scheduled_pre_opening_description'] = 'The time in minutes for the session to be acceessible before the schedules opening time is due.';

$string['config_sendnotifications'] = 'Configuration for "Send notifications" feature';
$string['config_sendnotifications_description'] = 'These settings enable or disable options in the UI and also define default values for these options.';
$string['config_sendnotifications_enabled'] = 'Send notifications enabled';
$string['config_sendnotifications_enabled_description'] = 'If enabled the UI for editing the activity includes an option for sending a notification to enrolled user when the activity is added or updated.';

$string['config_extended_capabilities'] = 'Configuration for extended capabilities';
$string['config_extended_capabilities_description'] = 'Configuration for extended capabilities when the BigBlueButton server offers them.';
$string['config_uidelegation_enabled'] = 'UI delegation is enabled';
$string['config_uidelegation_enabled_description'] = 'These settings enable or disable the UI delegation to the BigBlueButton server.';
$string['config_recordingready_enabled'] = 'Send notifications when a recording is ready';
$string['config_recordingready_enabled_description'] = 'Enable the plugin for sending notifications when the recording is ready. (It will only work if the script post_publish_recording_ready_callback is enabled in the BigBlueButton server)';
$string['config_meetingevents_enabled'] = 'Register live events';
$string['config_meetingevents_enabled_description'] = 'Enable the plugin for accepting and processing live events after the session ends. (It must be enabled for "Activity completion" and will only work if the BigBlueButton server is capable of processing post_events scripts)';

$string['config_warning_curl_not_installed'] = 'This feature requires the CURL extension for php installed and enabled. The settings will be accessible only if this condition is fulfilled.';
$string['config_warning_bigbluebuttonbn_cfg_deprecated'] = 'BigBlueButton is making use of config.php with a global variable that has been deprecated. Please convert the file as it will not be supported in future versions';

$string['config_muteonstart'] = 'Configuration for "Mute on Start" feature';
$string['config_muteonstart_description'] = 'These settings enable or disable options in the UI and also define default values for these options.';
$string['config_muteonstart_default'] = 'Mute on start enabled by default';
$string['config_muteonstart_default_description'] = 'If enabled the session will be muted on start.';
$string['config_muteonstart_editable'] = 'Mute on start can be edited';
$string['config_muteonstart_editable_description'] = 'Mute on start by default can be edited when the instance is added or updated.';
$string['config_welcome_default'] = 'Default welcome message';
$string['config_welcome_default_description'] = 'Replaces the default message setted up for the BigBlueButton server. The message can includes keywords  (%%CONFNAME%%, %%DIALNUM%%, %%CONFNUM%%) which will be substituted automatically, and also html tags like <b>...</b> or <i></i> ';
$string['config_default_messages'] = 'Default messages';
$string['config_default_messages_description'] = 'Set message defaults for activities';

$string['config_locksettings'] = 'Configuration for locking settings';
$string['config_locksettings_description'] = 'These setttings enable or disable options in the UI for locking settings, and also define default values for these options.';

$string['config_disablecam_default'] = 'Disable cam enabled by default';
$string['config_disablecam_default_description'] = 'If enabled the webcams will be disabled.';
$string['config_disablecam_editable'] = 'Disable cam can be edited';
$string['config_disablecam_editable_description'] = 'Disable cam by default can be edited when the instance is added or updated.';

$string['config_disablemic_default'] = 'Disable mic enabled by default';
$string['config_disablemic_default_description'] = 'If enabled the microphones will be disabled.';
$string['config_disablemic_editable'] = 'Disable mic can be edited';
$string['config_disablemic_editable_description'] = 'Disable mic by default can be edited when the instance is added or updated.';

$string['config_disableprivatechat_default'] = 'Disable private chat enabled by default';
$string['config_disableprivatechat_default_description'] = 'If enabled the private chat will be disabled.';
$string['config_disableprivatechat_editable'] = 'Disable private chat can be edited';
$string['config_disableprivatechat_editable_description'] = 'Disable private chat by default can be edited when the instance is added or updated.';

$string['config_disablepublicchat_default'] = 'Disable public chat enabled by default';
$string['config_disablepublicchat_default_description'] = 'If enabled the public chat will be disabled.';
$string['config_disablepublicchat_editable'] = 'Disable public chat can be edited';
$string['config_disablepublicchat_editable_description'] = 'Disable public chat by default can be edited when the instance is added or updated.';

$string['config_disablenote_default'] = 'Disable shared notes enabled by default';
$string['config_disablenote_default_description'] = 'If enabled the shared notes will be disabled.';
$string['config_disablenote_editable'] = 'Disable shared notes can be edited';
$string['config_disablenote_editable_description'] = 'Disable shared notes by default can be edited when the instance is added or updated.';

$string['config_hideuserlist_default'] = 'Hide user list enabled by default';
$string['config_hideuserlist_default_description'] = 'If enabled the session user list will be hidden.';
$string['config_hideuserlist_editable'] = 'Hide user list can be edited';
$string['config_hideuserlist_editable_description'] = 'Hide user list by default can be edited when the instance is added or updated.';

$string['config_lockedlayout_default'] = 'Locked layout enabled by default';
$string['config_lockedlayout_default_description'] = 'If enabled the session layout will be locked.';
$string['config_lockedlayout_editable'] = 'Locked layout can be edited';
$string['config_lockedlayout_editable_description'] = 'Locked layout by default can be edited when the instance is added or updated.';

$string['config_lockonjoin_default'] = 'Ignore lock on join enabled by default';
$string['config_lockonjoin_default_description'] = 'If enabled the lock settings will be ignored. Lock configuration must be enabled for this to apply.';
$string['config_lockonjoin_editable'] = 'Ignore lock on join can be edited';
$string['config_lockonjoin_editable_description'] = 'Ignore lock on join by default can be edited when the instance is added or updated.';

$string['config_lockonjoinconfigurable_default'] = 'Lock configuration enabled by default';
$string['config_lockonjoinconfigurable_default_description'] = 'If enabled the session lock settings can be enabled or disabled from the above control.';
$string['config_lockonjoinconfigurable_editable'] = 'Lock configuration can be edited';
$string['config_lockonjoinconfigurable_editable_description'] = 'Lock configuration by default can be edited when the instance is added or updated.';

$string['config_experimental_features'] = 'Configuration for experimental features';
$string['config_experimental_features_description'] = 'Configuration for experimental features.';

$string['general_error_unable_connect'] = 'Unable to connect. Please check the url of the BigBlueButton server AND check to see if the BigBlueButton server is running.
Details : {$a}';
$string['general_error_no_answer'] = 'Empty response. Please check the url of the BigBlueButton server AND check to see if the BigBlueButton server is running.';
$string['general_error_not_allowed_to_create_instances'] = 'User is not allowed to create any type of instances.';
$string['general_error_not_found'] = 'Entity not found : {$a}.';
$string['general_error_cannot_create_meeting'] = 'Cannot create meeting.';
$string['general_error_cannot_get_recordings'] = 'Cannot get recordings.';
$string['index_confirm_end'] = 'Do you wish to end the virtual class?';
$string['index_disabled'] = 'disabled';
$string['index_enabled'] = 'enabled';
$string['index_ending'] = 'Ending the virtual classroom ... please wait';
$string['index_error_checksum'] = 'A checksum error occurred. Make sure you entered the correct secret.';
$string['index_error_forciblyended'] = 'Unable to join this meeting because it has been manually ended.';
$string['index_error_unable_display'] = 'Unable to display the meetings. Please check the url of the BigBlueButton server AND check to see if the BigBlueButton server is running.';
$string['index_heading_actions'] = 'Actions';
$string['index_heading_group'] = 'Group';
$string['index_heading_moderator'] = 'Moderators';
$string['index_heading_name'] = 'Room';
$string['index_heading_recording'] = 'Recording';
$string['index_heading_users'] = 'Users';
$string['index_heading_viewer'] = 'Viewers';
$string['index_heading'] = 'BigBlueButton Rooms';
$string['instanceprofilewithoutrecordings'] = 'This instance profile cannot display recordings';
$string['mod_form_block_general'] = 'General settings';
$string['mod_form_block_room'] = 'Activity/Room settings';
$string['mod_form_block_recordings'] = 'View for recording';
$string['mod_form_block_presentation'] = 'Presentation content';
$string['mod_form_block_presentation_default'] = 'Presentation default content';
$string['mod_form_block_participants'] = 'Role assigned during live session';
$string['mod_form_block_schedule'] = 'Schedule for session';
$string['mod_form_block_record'] = 'Record settings';
$string['mod_form_field_openingtime'] = 'Join open';
$string['mod_form_field_closingtime'] = 'Join closed';
$string['mod_form_field_intro'] = 'Description';
$string['mod_form_field_intro_help'] = 'A short description for the room or conference.';
$string['mod_form_field_duration_help'] = 'Setting the duration for a meeting will establish the maximum time for a meeting to keep alive before the recording finish';
$string['mod_form_field_duration'] = 'Duration';
$string['mod_form_field_userlimit'] = 'User limit';
$string['mod_form_field_userlimit_help'] = 'Maximum limit of users allowed in a meeting. If the limit is set to 0 the number of users will be unlimited.';
$string['mod_form_field_name'] = 'Virtual classroom name';
$string['mod_form_field_room_name'] = 'Room name';
$string['mod_form_field_conference_name'] = 'Conference name';
$string['mod_form_field_record'] = 'Session can be recorded';
$string['mod_form_field_voicebridge'] = 'Voice bridge [####]';
$string['mod_form_field_voicebridge_help'] = 'Voice conference number that participants enter to join the voice conference when using dial-in. A number between 1 and 9999 must be typed. If the value is 0 the static voicebridge number will be ignored and a random number will be generated by BigBlueButton. A number 7 will preced to the four digits typed';
$string['mod_form_field_voicebridge_format_error'] = 'Format error. You should input a number between 1 and 9999.';
$string['mod_form_field_voicebridge_notunique_error'] = 'Not a unique value. This number is being used by another room or conference.';
$string['mod_form_field_wait'] = 'Wait for moderator';
$string['mod_form_field_wait_help'] = 'Viewers must wait until a moderator enters the session before they can do so';
$string['mod_form_field_welcome'] = 'Welcome message';
$string['mod_form_field_welcome_help'] = 'Replaces the default message setted up for the BigBlueButton server. The message can includes keywords  (%%CONFNAME%%, %%DIALNUM%%, %%CONFNUM%%) which will be substituted automatically, and also html tags like &lt;b>...&lt;/b>, &lt;br />, &lt;u>&lt;/u> or &lt;i>&lt;/i> ';
$string['mod_form_field_welcome_default'] = '<br>Welcome to <b>%%CONFNAME%%</b>!<br><br>For help on using BigBlueButton see these (short)  <a href="event:http://www.bigbluebutton.org/content/videos"><u>tutorial videos</u></a>.<br><br>To join the audio bridge click the phone icon (top center). <b>Please use a headset to avoid causing background noise for others.</b>';
$string['mod_form_field_participant_add'] = 'Add assignation';
$string['mod_form_field_participant_list'] = 'Assignation list';
$string['mod_form_field_participant_list_type_all'] = 'All users enrolled';
$string['mod_form_field_participant_list_type_role'] = 'Role';
$string['mod_form_field_participant_list_type_user'] = 'User';
$string['mod_form_field_participant_list_type_owner'] = 'Owner';
$string['mod_form_field_participant_list_text_as'] = 'joins session as';
$string['mod_form_field_participant_list_action_add'] = 'Add';
$string['mod_form_field_participant_list_action_remove'] = 'Remove';
$string['mod_form_field_participant_bbb_role_moderator'] = 'Moderator';
$string['mod_form_field_participant_bbb_role_viewer'] = 'Viewer';
$string['mod_form_field_instanceprofiles'] = 'Instance type';
$string['mod_form_field_instanceprofiles_help'] = 'Select the type for this BigBlueButton instance.';
$string['mod_form_field_muteonstart'] = 'Mute on start';
$string['mod_form_field_notification'] = 'Notify this change to users enrolled';
$string['mod_form_field_notification_help'] = 'Send a notification to all users enrolled to let them know that this activity has been added or updated';
$string['mod_form_field_notification_created_help'] = 'Send a notification to all users enrolled to let them know that this activity has been created';
$string['mod_form_field_notification_modified_help'] = 'Send a notification to all users enrolled to let them know that this activity has been updated';
$string['mod_form_field_notification_msg_at'] = 'at';
$string['mod_form_field_recordings_html'] = 'Show the table in plain html';
$string['mod_form_field_recordings_deleted'] = 'Include recordings from deleted activities';
$string['mod_form_field_recordings_imported'] = 'Show only imported links';
$string['mod_form_field_recordings_preview'] = 'Show recording preview';
$string['mod_form_field_recordallfromstart'] = 'Record all from start';
$string['mod_form_field_recordhidebutton'] = 'Hide recording button';
$string['mod_form_field_nosettings'] = 'No settings can be edited';
$string['mod_form_field_disablecam'] = 'Disable webcams';
$string['mod_form_field_disablemic'] = 'Disable microphones';
$string['mod_form_field_disableprivatechat'] = 'Disable private chat';
$string['mod_form_field_disablepublicchat'] = 'Disable public chat';
$string['mod_form_field_disablenote'] = 'Disable shared notes';
$string['mod_form_field_hideuserlist'] = 'Hide user list';
$string['mod_form_field_lockedlayout'] = 'Lock room layout';
$string['mod_form_field_lockonjoin'] = 'Ignore lock settings';
$string['mod_form_field_lockonjoinconfigurable'] = 'Allow ignore locking settings';
$string['mod_form_locksettings'] = 'Lock settings';


$string['starts_at'] = 'Starts';
$string['started_at'] = 'Started';
$string['ends_at'] = 'Ends';
$string['calendarstarts'] = '{$a} is scheduled for';
$string['recordings_from_deleted_activities'] = 'Recordings from deleted activities';
$string['view_error_no_group_student'] = 'You have not been enrolled in a group. Please contact your Teacher or the Administrator.';
$string['view_error_no_group_teacher'] = 'There are no groups configured yet. Please set up groups or contact the Administrator.';
$string['view_error_no_group'] = 'There are no groups configured yet. Please set up groups before trying to join the meeting.';
$string['view_error_unable_join_student'] = 'Unable to connect to the BigBlueButton server. Please contact your Teacher or the Administrator.';
$string['view_error_unable_join_teacher'] = 'Unable to connect to the BigBlueButton server. Please contact the Administrator.';
$string['view_error_unable_join'] = 'Unable to join the meeting. Please check the url of the BigBlueButton server AND check to see if the BigBlueButton server is running.';
$string['view_error_bigbluebutton'] = 'BigBlueButton responded with errors. {$a}';
$string['view_error_create'] = 'The BigBlueButton server responded with an error message, the meeting could not be created.';
$string['view_error_max_concurrent'] = 'Number of concurrent meetings allowed has been reached.';
$string['view_error_userlimit_reached'] = 'The number of users allowed in a meeting has been reached.';
$string['view_error_url_missing_parameters'] = 'There are parameters missing in this URL';
$string['view_error_import_no_courses'] = 'No courses to look up for recordings';
$string['view_error_import_no_recordings'] = 'No recordings in this course for importing';
$string['view_error_invalid_session'] = 'The session is expired. Go back to the activity main page.';
$string['view_groups_selection_join'] = 'Join';
$string['view_groups_selection'] = 'Select the group you want to join and confirm the action';
$string['view_login_moderator'] = 'Logging in as moderator ...';
$string['view_login_viewer'] = 'Logging in as viewer ...';
$string['view_noguests'] = 'The BigBlueButton is not open to guests';
$string['view_nojoin'] = 'You are not in a role allowed to join this session.';
$string['view_recording_list_actionbar_edit'] = 'Edit';
$string['view_recording_list_actionbar_delete'] = 'Delete';
$string['view_recording_list_actionbar_import'] = 'Import';
$string['view_recording_list_actionbar_hide'] = 'Hide';
$string['view_recording_list_actionbar_show'] = 'Show';
$string['view_recording_list_actionbar_publish'] = 'Publish';
$string['view_recording_list_actionbar_unpublish'] = 'Unpublish';
$string['view_recording_list_actionbar_protect'] = 'Make it private';
$string['view_recording_list_actionbar_unprotect'] = 'Make it public';
$string['view_recording_list_action_publish'] = 'Publishing';
$string['view_recording_list_action_unpublish'] = 'Unpublishing';
$string['view_recording_list_action_process'] = 'Processing';
$string['view_recording_list_action_delete'] = 'Deleting';
$string['view_recording_list_action_protect'] = 'Protecting';
$string['view_recording_list_action_unprotect'] = 'Unprotecting';
$string['view_recording_list_action_update'] = 'Updating';
$string['view_recording_list_action_edit'] = 'Updating';
$string['view_recording_list_action_play'] = 'Play';
$string['view_recording_list_actionbar'] = 'Toolbar';
$string['view_recording_list_activity'] = 'Activity';
$string['view_recording_list_course'] = 'Course';
$string['view_recording_list_date'] = 'Date';
$string['view_recording_list_description'] = 'Description';
$string['view_recording_list_duration'] = 'Duration';
$string['view_recording_list_recording'] = 'Recording';
$string['view_recording_button_import'] = 'Import recording links';
$string['view_recording_button_return'] = 'Go back';
$string['view_recording_format_notes'] = 'Notes';
$string['view_recording_format_podcast'] = 'Podcast';
$string['view_recording_format_presentation'] = 'Presentation';
$string['view_recording_format_screenshare'] = 'Screenshare';
$string['view_recording_format_statistics'] = 'Statistics';
$string['view_recording_format_video'] = 'Video';
$string['view_recording_format_errror_unreachable'] = 'The URL for this recording format is unreachable.';
$string['view_section_title_presentation'] = 'Presentation file';
$string['view_section_title_recordings'] = 'Recordings';
$string['view_message_norecordings'] = 'There are no recording to show.';
$string['view_message_finished'] = 'This activity is over.';
$string['view_message_notavailableyet'] = 'This session is not yet available.';
$string['view_recording_select_course'] = 'Select a course first in the drop down menu';


$string['view_message_session_started_at'] = 'This session started at';
$string['view_message_session_running_for'] = 'This session has been running for';
$string['view_message_hour'] = 'hour';
$string['view_message_hours'] = 'hours';
$string['view_message_minute'] = 'minute';
$string['view_message_minutes'] = 'minutes';
$string['view_message_moderator'] = 'moderator';
$string['view_message_moderators'] = 'moderators';
$string['view_message_viewer'] = 'viewer';
$string['view_message_viewers'] = 'viewers';
$string['view_message_user'] = 'user';
$string['view_message_users'] = 'users';
$string['view_message_has_joined'] = 'has joined';
$string['view_message_have_joined'] = 'have joined';
$string['view_message_session_no_users'] = 'There are no users in this session';
$string['view_message_session_has_user'] = 'There is';
$string['view_message_session_has_users'] = 'There are';
$string['view_message_session_for'] = 'the session for';
$string['view_message_times'] = 'times';
$string['view_message_and'] = 'and';

$string['view_message_room_closed'] = 'This room is closed.';
$string['view_message_room_ready'] = 'This room is ready.';
$string['view_message_room_open'] = 'This room is open.';
$string['view_message_conference_room_ready'] = 'This conference room is ready. You can join the session now.';
$string['view_message_conference_not_started'] = 'This conference has not started yet.';
$string['view_message_conference_wait_for_moderator'] = 'Waiting for a moderator to join.';
$string['view_message_conference_in_progress'] = 'This conference is in progress.';
$string['view_message_conference_has_ended'] = 'This conference has ended.';
$string['view_message_tab_close'] = 'This tab/window must be closed manually';
$string['view_message_recordings_disabled'] = 'Recordings were disabled on this server. BigBlueButton instances for recordings only can not be used.';
$string['view_message_importrecordings_disabled'] = 'Feature for import recording links is disabled on this server.';

$string['view_groups_selection_warning'] = 'There is a conference room for each group and you have access to more than one. Be sure to select the correct one.';
$string['view_groups_nogroups_warning'] = 'The room was configured for using groups but the course does not have groups defined.';
$string['view_groups_notenrolled_warning'] = 'The room was configured for using groups but you are not enrolled in any of them.';
$string['view_conference_action_join'] = 'Join session';
$string['view_conference_action_end'] = 'End session';

$string['view_recording'] = 'recording';
$string['view_recording_link'] = 'imported link';
$string['view_recording_link_warning'] = 'This is a link pointing to a recording that was created in a different course or activity';
$string['view_recording_delete_confirmation'] = 'Are you sure to delete this {$a}?';
$string['view_recording_delete_confirmation_warning_s'] = 'This recording has {$a} link associated that was imported in a different course or activity. If the recording is deleted that link will also be removed';
$string['view_recording_delete_confirmation_warning_p'] = 'This recording has {$a} links associated that were imported in different courses or activities. If the recording is deleted those links will also be removed';
$string['view_recording_publish_confirmation'] = 'Are you sure to publish this {$a}?';
$string['view_recording_publish_confirmation_warning_s'] = 'This recording has {$a} link associated that was imported in a different course or activity. If the recording is published that link will also be published';
$string['view_recording_publish_confirmation_warning_p'] = 'This recording has {$a} links associated that were imported in different courses or activities. If the recording is published those links will also be published';
$string['view_recording_publish_link_deleted'] = 'This link can not be re-published because the actual recording does not exist in the current BigBlueButton server. The link should be removed.';
$string['view_recording_publish_link_not_published'] = 'This link can not be re-published because the actual recording is unpublished';
$string['view_recording_unpublish_confirmation'] = 'Are you sure to unpublish this {$a}?';
$string['view_recording_unpublish_confirmation_warning_s'] = 'This recording has {$a} link associated that was imported in a different course or activity. If the recording is unpublished that link will also be unpublished';
$string['view_recording_unpublish_confirmation_warning_p'] = 'This recording has {$a} links associated that were imported in different courses or activities. If the recording is unpublished those links will also be unpublished';
$string['view_recording_protect_confirmation'] = 'Are you sure to protect this {$a}?';
$string['view_recording_protect_confirmation_warning_s'] = 'This recording has {$a} link associated that was imported in a different course or activity. If the recording is protected it will also affect the imported links';
$string['view_recording_protect_confirmation_warning_p'] = 'This recording has {$a} links associated that were imported in different courses or activities. If the recording is protected it will also affect the imported links';
$string['view_recording_unprotect_confirmation'] = 'Are you sure to unprotect this {$a}?';
$string['view_recording_unprotect_confirmation_warning_s'] = 'This recording has {$a} link associated that was imported in a different course or activity. If the recording is unprotected it will also affect the imported links';
$string['view_recording_unprotect_confirmation_warning_p'] = 'This recording has {$a} links associated that were imported in different courses or activities. If the recording is unprotected it will also affect the imported links';
$string['view_recording_import_confirmation'] = 'Are you sure to import this recording?';
$string['view_recording_unprotect_link_deleted'] = 'This link can not be un-protected because the actual recording does not exist in the current BigBlueButton server. The link should be removed.';
$string['view_recording_unprotect_link_not_unprotected'] = 'This link can not be un-protected because the actual recording is protected';
$string['view_recording_actionbar'] = 'Toolbar';
$string['view_recording_activity'] = 'Activity';
$string['view_recording_course'] = 'Course';
$string['view_recording_date'] = 'Date';
$string['view_recording_description'] = 'Description';
$string['view_recording_description_editlabel'] = 'Edit Description';
$string['view_recording_description_edithint'] = 'Edit the description. It will help to find the recording later';
$string['view_recording_length'] = 'Length';
$string['view_recording_meeting'] = 'Meeting';
$string['view_recording_duration'] = 'Duration';
$string['view_recording_recording'] = 'Recording';
$string['view_recording_duration_min'] = 'min';
$string['view_recording_name'] = 'Name';
$string['view_recording_name_editlabel'] = 'Edit Name';
$string['view_recording_name_edithint'] = 'Edit the name. It will help to find the recording later';
$string['view_recording_tags'] = 'Tags';
$string['view_recording_playback'] = 'Playback';
$string['view_recording_preview'] = 'Preview';
$string['view_recording_preview_help'] = 'Hover over an image to view it in full size';
$string['view_recording_modal_button'] = 'Apply';
$string['view_recording_modal_title'] = 'Set values for recording';
$string['view_recording_yui_first'] = 'First';
$string['view_recording_yui_prev'] = 'Previous';
$string['view_recording_yui_next'] = 'Next';
$string['view_recording_yui_last'] = 'Last';
$string['view_recording_yui_page'] = 'Page';
$string['view_recording_yui_go'] = 'Go';
$string['view_recording_yui_rows'] = 'Rows';
$string['view_recording_yui_show_all'] = 'Show all';

$string['event_activity_created'] = 'Activity created';
$string['event_activity_deleted'] = 'Activity deleted';
$string['event_activity_updated'] = 'Activity updated';
$string['event_meeting_created'] = 'Meeting created';
$string['event_meeting_ended'] = 'Meeting forcibly ended';
$string['event_meeting_joined'] = 'Meeting joined';
$string['event_meeting_left'] = 'Meeting left';
$string['event_recording_viewed'] = 'Recording viewed';
$string['event_recording_edited'] = 'Recording edited';
$string['event_recording_deleted'] = 'Recording deleted';
$string['event_recording_imported'] = 'Recording imported';
$string['event_recording_published'] = 'Recording published';
$string['event_recording_unpublished'] = 'Recording unpublished';
$string['event_recording_protected'] = 'Recording protected';
$string['event_recording_unprotected'] = 'Recording unprotected';
$string['event_live_session'] = 'Live session event';

$string['instance_type_default'] = 'Room/Activity with recordings';
$string['instance_type_room_only'] = 'Room/Activity only';
$string['instance_type_recording_only'] = 'Recordings only';

$string['messageprovider:instance_updated'] = 'BigBlueButton meeting updated';
$string['messageprovider:recording_ready'] = 'BigBlueButton recording ready to view';
$string['notification_instance_created_intro'] = 'The <a href="{$a->link}">{$a->name}</a> BigBlueButton activity has been created.';
$string['notification_instance_created_small'] = 'A new BigBlueButton meeting named {$a->name} was created';
$string['notification_instance_created_subject'] = 'A new BigBlueButton meeting activity has been created';
$string['notification_instance_description'] = 'Description';
$string['notification_instance_end_date'] = 'End date';
$string['notification_instance_name'] = 'Title';
$string['notification_instance_start_date'] = 'Start date';
$string['notification_instance_updated_intro'] = 'The <a href="{$a->link}">{$a->name}</a> BigBlueButton activity has been updated.';
$string['notification_instance_updated_small'] = 'The {$a->name} BigBlueButton meeting was updated';
$string['notification_instance_updated_subject'] = 'Your BigBlueButton meeting activity has been updated';
$string['notification_recording_ready_small'] = 'A new recording is available for the {$a->name} BigBlueButton meeting';
$string['notification_recording_ready_html'] = 'A recording is now available for a recent meeting in <b><a href="{$a->link}">{$a->name}</a></b>.';
$string['notification_recording_ready_plain'] = 'A recording is now available for a recent meeting in {$a->name}. See {$a->link} to view the meeting.';
$string['notification_recording_ready_subject'] = 'Recording ready';

$string['view_error_meeting_not_running'] = 'Something went wrong, the meeting is not running.';
$string['view_error_current_state_not_found'] = 'Current state was not found. The recording may have been deleted or the BigBlueButton server is not compatible with the action performed.';
$string['view_error_action_not_completed'] = 'Action could not be completed';
$string['view_warning_default_server'] = 'This Moodle server is making use of the BigBlueButton testing server that comes pre-configured by default. It should be replaced for production.';

$string['view_room'] = 'View room';
$string['index_error_noinstances'] = 'There are no instances of bigbluebuttonbn';
$string['index_error_bbtn'] = 'BigBlueButton ID {$a} is incorrect';

$string['view_mobile_message_reload_page_creation_time_meeting'] = 'You exceeded the 45 seconds in this page, please reload the page to join correctly to the meeting.';
$string['view_mobile_message_groups_not_supported'] = 'This instance is enable to work with groups but the mobile app has not support for this. Please open in desktop if you want to use the group support.';

$string['end_session_confirm_title'] = 'Really end session?';
$string['end_session_confirm'] = 'Are you sure you want to end the virtual classroom session?';
$string['end_session_notification'] = 'The session has now been closed.';
$string['cachedef_currentfetch'] = 'Data to list any recording fetched recently.';
$string['cachedef_serverinfo'] = 'Remote server information';
$string['cachedef_recordings'] = 'Recording metadata';
$string['cachedef_validatedurls'] = 'Cache of validated URL checks';
$string['taskname:check_pending_recordings'] = 'Fetch pending recordings';
$string['userlimitreached'] = 'The number of users allowed in a meeting has been reached.';
$string['waitformoderator'] = 'Waiting for a moderator to join.';

$string['recordingurlnotfound'] = 'The recording URL is invalid.';
