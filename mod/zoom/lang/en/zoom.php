<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * English strings for zoom.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['addtocalendar'] = 'Add to calendar';
$string['alternative_hosts'] = 'Alternative Hosts';
$string['alternative_hosts_help'] = 'The alternative host option allows you to schedule meetings and designate another Pro user on the same account to start the meeting or webinar if you are unable to. This user will receive an email notifying them that they\'ve been added as an alternative host, with a link to start the meeting. Separate multiple emails by comma (without spaces).';
$string['allmeetings'] = 'All meetings';
$string['apikey'] = 'Zoom API key';
$string['apikey_desc'] = '';
$string['apisecret'] = 'Zoom API secret';
$string['apisecret_desc'] = '';
$string['apiurl'] = 'Zoom API url';
$string['apiurl_desc'] = '';
$string['audio_both'] = 'VoIP and Telephony';
$string['audio_telephony'] = 'Telephony only';
$string['audio_voip'] = 'VoIP only';
$string['cachedef_zoomid'] = 'The zoom user id of the user';
$string['cachedef_sessions'] = 'Information from the zoom get user report request';
$string['calendardescriptionURL'] = 'Meeting join URL: {$a}.';
$string['calendardescriptionintro'] = "\nDescription:\n{\$a}";
$string['calendariconalt'] = 'Calendar icon';
$string['changehost'] = 'Change host';
$string['clickjoin'] = 'Clicked join meeting button';
$string['connectionok'] = 'Connection working.';
$string['connectionfailed'] = 'Connection failed: ';
$string['connectionstatus'] = 'Connection status';
$string['defaultsettings'] = 'Default Zoom settings';
$string['defaultsettings_help'] = 'These settings define the defaults for all new Zoom meetings and webinars.';
$string['displaypassword'] = 'Display passcode';
$string['displaypassword_help'] = 'If enabled the meeting passcode will always be displayed to non-hosts.';
$string['downloadical'] = 'Download iCal';
$string['duration'] = 'Duration (minutes)';
$string['endtime'] = 'End time';
$string['err_duration_nonpositive'] = 'The duration must be positive.';
$string['err_duration_too_long'] = 'The duration cannot exceed 150 hours.';
$string['err_long_timeframe'] = 'Requested time frame too long, showing results of latest month in range.';
$string['err_invalid_password'] = 'Passcode contains invalid characters.';
$string['err_password'] = 'Passcode may only contain the following characters: [a-z A-Z 0-9 @ - _ *]. Max of 10 characters.';
$string['err_password_required'] = 'Passcode is required.';
$string['err_start_time_past'] = 'The start date cannot be in the past.';
$string['errorwebservice_badrequest'] = 'Zoom received a bad request: {$a}';
$string['errorwebservice_notfound'] = 'The resource does not exists: {$a}';
$string['errorwebservice'] = 'Zoom webservice error: {$a}.';
$string['export'] = 'Export';
$string['firstjoin'] = 'First able to join';
$string['firstjoin_desc'] = 'The earliest a user can join a scheduled meeting (minutes before start).';
$string['getmeetingreports'] = 'Get meeting report from Zoom';
$string['host'] = 'Host';
$string['indicator:cognitivedepth'] = 'Zoom cognitive';
$string['indicator:cognitivedepth_help'] = 'This indicator is based on the cognitive depth reached by the student in a Zoom activity.';
$string['indicator:socialbreadth'] = 'Zoom social';
$string['indicator:socialbreadth_help'] = 'This indicator is based on the social breadth reached by the student in a Zoom activity.';
$string['invalidscheduleuser'] = 'You cannot schedule for the specified user.';
$string['invalid_status'] = 'Status invalid, check the database.';
$string['join'] = 'Join';
$string['joinbeforehost'] = 'Join meeting before host';
$string['join_link'] = 'Join link';
$string['join_meeting'] = 'Join Meeting';
$string['jointime'] = 'Join time';
$string['leavetime'] = 'Leave time';
$string['licensesnumber'] = 'Number of licenses';
$string['redefinelicenses'] = 'Redefine licenses';
$string['lowlicenses'] = 'If the number of your licenses exceeds those required, then when you create each new activity by the user, it will be assigned a PRO license by lowering the status of another user. The option is effective when the number of active PRO-licenses is more than 5.';
$string['maskparticipantdata'] = 'Mask participant data';
$string['maskparticipantdata_help'] = 'Prevents participant data from appearing in reports (useful for sites that mask participant data, e.g., for HIPAA).';
$string['meeting_nonexistent_on_zoom'] = 'Nonexistent on Zoom';
$string['meeting_invite'] = 'Phone/Dial-In info';
$string['meeting_invite_show'] = 'Show meeting invitation';
$string['meeting_invite_hide'] = 'Hide meeting invitation';
$string['meeting_finished'] = 'Finished';
$string['meeting_not_started'] = 'Not started';
$string['meetingoptions'] = 'Meeting option';
$string['meetingoptions_help'] = "*Join before host* allows attendees to join the meeting before the host joins or when the host cannot attend the meeting.\n\n*Waiting room* allows the host to control when a participant joins the meeting.\n\nThese two options are mutually exclusive, so selecting one will deselect the other. It is also possible to deselect both of them.\n\n*Authenticated users* requires all attendees to sign in with their authorized zoom account to be able to join.";
$string['meeting_started'] = 'In progress';
$string['meeting_time'] = 'Start Time';
$string['modulename'] = 'Zoom meeting';
$string['modulenameplural'] = 'Zoom Meetings';
$string['modulename_help'] = 'Zoom is a video and web conferencing platform that gives authorized users the ability to host online meetings.';
$string['newmeetings'] = 'New Meetings';
$string['nomeetinginstances'] = 'No sessions found for this meeting.';
$string['noparticipants'] = 'No participants found for this session at this time.';
$string['nosessions'] = 'No sessions found for specified range.';
$string['nozooms'] = 'No meetings';
$string['off'] = 'Off';
$string['oldmeetings'] = 'Concluded Meetings';
$string['on'] = 'On';
$string['option_audio'] = 'Audio options';
$string['option_authenticated_users'] = 'Only authenticated users';
$string['option_host_video'] = 'Host video';
$string['option_jbh'] = 'Enable join before host';
$string['option_mute_upon_entry'] = 'Mute upon entry';
$string['option_mute_upon_entry_help'] = 'Automatically mute all participants when they join the meeting. The host controls whether participants can unmute themselves.';
$string['option_participants_video'] = 'Participants video';
$string['option_proxyhost'] = 'Use proxy';
$string['option_proxyhost_desc'] = 'The proxy set here as \'<code>&lt;hostname&gt;:&lt;port&gt;</code>\' is used only for communicating with Zoom. Leave empty to use the Moodle default proxy settings. You only need to set this if you do not want to set a global proxy in Moodle.';
$string['option_waiting_room'] = 'Enable waiting room';
$string['participantdatanotavailable'] = 'Details not available';
$string['participantdatanotavailable_help'] = 'Participant data is not available for this Zoom session (e.g., due to HIPAA-compliance).';
$string['participants'] = 'Participants';
$string['password'] = 'Passcode';
$string['passwordprotected'] = 'Passcode Protected';
$string['pluginadministration'] = 'Manage Zoom meeting';
$string['pluginname'] = 'Zoom meeting';
$string['privacy:metadata:zoom_meeting_details'] = 'The database table that stores information about each meeting instance.';
$string['privacy:metadata:zoom_meeting_details:topic'] = 'The name of the meeting that the user attended.';
$string['privacy:metadata:zoom_meeting_participants'] = 'The database table that stores information about meeting participants.';
$string['privacy:metadata:zoom_meeting_participants:duration'] = 'How long the participant was in the meeting';
$string['privacy:metadata:zoom_meeting_participants:join_time'] = 'The time that the participant joined the meeting';
$string['privacy:metadata:zoom_meeting_participants:leave_time'] = 'The time that the participant left the meeting';
$string['privacy:metadata:zoom_meeting_participants:name'] = 'The name of the participant';
$string['privacy:metadata:zoom_meeting_participants:user_email'] = 'The email of the participant';
$string['recurringmeeting'] = 'Recurring';
$string['recurringmeeting_help'] = 'Has no end date';
$string['recurringmeetinglong'] = 'Recurring meeting (meeting with no end date or time)';
$string['recycleonjoin'] = 'Recycle license upon join';
$string['licenseonjoin'] = 'Select this option if you would like the host to receive a license upon starting the meeting, <i>as well as</i> upon creation.';
$string['report'] = 'Reports';
$string['reportapicalls'] = 'Report API calls exhausted';
$string['requirepasscode'] = 'Require meeting passcode';
$string['requirepasscode_help'] = 'If enabled, passcodes are required by default for meetings.';
$string['resetapicalls'] = 'Reset the number of available API calls';
$string['schedulefor'] = 'Schedule meeting for';
$string['scheduleforself'] = 'Schedule for yourself';
$string['search:activity'] = 'Zoom - activity information';
$string['sessions'] = 'Sessions';
$string['start'] = 'Start';
$string['starthostjoins'] = 'Start video when host joins';
$string['start_meeting'] = 'Start Meeting';
$string['startpartjoins'] = 'Start video when participant joins';
$string['start_time'] = 'When';
$string['starttime'] = 'Start time';
$string['status'] = 'Status';
$string['title'] = 'Title';
$string['topic'] = 'Topic';
$string['unavailable'] = 'Unable to join at this time';
$string['updatemeetings'] = 'Update meeting settings from Zoom';
$string['usepersonalmeeting'] = 'Use personal meeting ID {$a}';
$string['waitingroom'] = 'Waiting room enabled';
$string['webinar'] = 'Webinar';
$string['webinar_help'] = 'This option is only available to pre-authorized Zoom accounts.';
$string['webinar_already_true'] = '<p><b>This module was already set as a webinar, not meeting. You cannot toggle this setting after creating the webinar.</b></p>';
$string['webinar_already_false'] = '<p><b>This module was already set as a meeting, not webinar. You cannot toggle this setting after creating the meeting.</b></p>';
$string['zoom:addinstance'] = 'Add a new Zoom meeting';
$string['zoomerr'] = 'An error occured with Zoom.'; // Generic error.
$string['zoomerr_apikey_missing'] = 'Zoom API key not found';
$string['zoomerr_apilimit'] = 'Reached the maximum daily rate limit for this API. Retry at {$a}';
$string['zoomerr_apisecret_missing'] = 'Zoom API secret not found';
$string['zoomerr_id_missing'] = 'You must specify a course_module ID or an instance ID';
$string['zoomerr_licensesnumber_missing'] = 'Zoom utmost setting found but, licensesnumber setting not found';
$string['zoomerr_maxretries'] = 'Retried {$a->maxretries} times to make the call, but failed: {$a->response}';
$string['zoomerr_meetingnotfound'] = 'This meeting cannot be found on Zoom. You can <a href="{$a->recreate}">recreate it here</a> or <a href="{$a->delete}">delete it completely</a>.';
$string['zoomerr_meetingnotfound_info'] = 'This meeting cannot be found on Zoom. Please contact the meeting host if you have questions.';
$string['zoomerr_usernotfound'] = 'Unable to find your account on Zoom. If you are using Zoom for the first time, you must activate your Zoom account by logging into <a href="{$a}" target="_blank">{$a}</a>. Once you\'ve activated your Zoom account, reload this page and continue setting up your meeting. Else make sure your email on Zoom matches your email on this system.';
$string['zoomurl'] = 'Zoom home page URL';
$string['zoomurl_desc'] = '';
$string['zoom:view'] = 'View Zoom meetings';
