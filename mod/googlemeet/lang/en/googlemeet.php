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
 * Plugin strings are defined here.
 *
 * @package     mod_googlemeet
 * @category    string
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apikey'] = 'API key';
$string['checkweekdays'] = 'Select the days of the week that fall within the selected date range.';
$string['clientid'] = 'OAuth client ID';
$string['clientid_desc'] = '<a href="https://github.com/ronefel/moodle-mod_googlemeet/wiki/How-to-create-the-API-key-and-OAuth-client-ID" target="_blank">How to create the API key and OAuth client ID</a>';
$string['date'] = 'Date';
$string['duration'] = 'Duration';
$string['earlierto'] = 'The event date cannot be earlier than the course start date ({$a}).';
$string['emailcontent'] = 'Email content';
$string['emailcontent_default'] = '<p>Hi %userfirstname%,</p>
<p>This reminder is to remind you that there will be a Google meet event in %coursename%</p>
<p><b>%googlemeetname%</b></p>
<p>When: %eventdate% %duration% %timezone%</p>
<p>Access link: %url%</p>';
$string['emailcontent_help'] = 'When a notification is sent to a student, it takes the email content from this field. The following wildcards can be used:
<ul>
<li>%userfirstname%</li>
<li>%userlastname%</li>
<li>%coursename%</li>
<li>%googlemeetname%</li>
<li>%eventdate%</li>
<li>%duration%</li>
<li>%timezone%</li>
<li>%url%</li>
<li>%cmid%</li>
</ul>';
$string['entertheroom'] = 'Enter the room';
$string['eventdate'] = 'Event date';
$string['from'] = 'from';
$string['generateurlroom'] = 'Generate Room URL';
$string['googlemeet:addinstance'] = 'Add a new Google Meet™ for Moodle';
$string['googlemeet:editrecording'] = 'Edit recordings';
$string['googlemeet:removerecording'] = 'Remove recordings';
$string['googlemeet:syncgoogledrive'] = 'Sync with Google Drive';
$string['googlemeet:view'] = 'View Google Meet™ for Moodle content';
$string['hide'] = 'Hide';
$string['invalideventenddate'] = 'This date can not be earlier than the "Event date"';
$string['invalideventendtime'] = 'The end time must be greater than start time';
$string['invalidstoredurl'] = 'Cannot display this resource, Google Meet URL is invalid.';
$string['jstableinfo'] = 'Showing {start} to {end} of {rows} recordings';
$string['jstableinfofiltered'] = 'Showing {start} to {end} of {rows} recordings (filtered from {rowsTotal} recordings)';
$string['jstableloading'] = 'Loading...';
$string['jstablenorows'] = 'No recording found';
$string['jstableperpage'] = '{select} recordings per page';
$string['jstablesearch'] = 'Search...';
$string['lastsync'] = 'Last sync:';
$string['loading'] = 'Loading';
$string['messageprovider:notification'] = 'Google Meet event start reminder';
$string['minutesbefore'] = 'Minutes before';
$string['minutesbefore_help'] = 'Number of minutes before the start of the event when the notification should be send.';
$string['modulename'] = 'Google Meet™ for Moodle';
$string['modulename_help'] = 'The Google Meet™ module for Moodle allows the teacher to create a Google Meet room as a course resource and after the meetings make available to the students the recordings, saved in Google Drive.
<p>©2018 Google LLC All rights reserved.<br/>
Google Meet and the Google Meet logo are registered trademarks of Google LLC.</p>
';
$string['modulenameplural'] = 'Google Meet™ for Moodle instances';
$string['multieventdateexpanded'] = 'Recurrence of the event date expanded';
$string['multieventdateexpanded_desc'] = 'Show the "Recurrence of the event date" settings as expanded by default when creating new Room.';
$string['name'] = 'Name';
$string['never'] = 'Never';
$string['notfoundrecordingname'] = 'No recording found with the name';
$string['notfoundrecordingsfolder'] = 'The "Meet Recordings" folder was not found in Google Drive.';
$string['notification'] = 'Notification';
$string['notificationexpanded'] = 'Notification expanded';
$string['notify'] = 'Send notification to the student';
$string['notify_help'] = 'If checked, a notification will be sent to the student about the start date of the event.';
$string['notifycationexpanded_desc'] = 'Show the "Notification" settings as expanded by default when creating new Room.';
$string['notifytask'] = 'Google Meet™ for Moodle notification task';
$string['notpossiblesync'] = 'Is not possible to synchronize with an account other than the one that created the room.';
$string['or'] = 'or';
$string['play'] = 'Play';
$string['pluginadministration'] = 'Google Meet™ for Moodle administration';
$string['pluginname'] = 'Google Meet™ for Moodle';
$string['privacy:metadata:googlemeet_notify_done'] = 'Records notifications sent to users about the start of events. This data is temporary and is deleted after the event start date.';
$string['privacy:metadata:googlemeet_notify_done:eventid'] = 'The event ID';
$string['privacy:metadata:googlemeet_notify_done:userid'] = 'The user ID';
$string['privacy:metadata:googlemeet_notify_done:timesent'] = 'The timestamp indicating when the user received a notification';
$string['earlierto'] = 'The event date cannot be earlier than the course start date ({$a}).';
$string['recording'] = 'Recording';
$string['recordings'] = 'Recordings';
$string['recordingswiththename'] = 'Recordings with the name:';
$string['recurrenceeventdate'] = 'Recurrence of the event date';
$string['recurrenceeventdate_help'] = 'This function makes it possible to create multiple recurrences from the event date.
<br>* <strong>Repeat on</strong>: Select the days of the week that your class will meet (for example, Monday / Wednesday / Friday).
<br>* <strong>Repeat every</strong>: This allows for a frequency setting. If your class will meet every week, select 1; will meet every two weeks, select 2; every 3 weeks, select 3, etc.
<br>* <strong>Repeat until</strong>: Select the last day of the meeting (the last day you want to take the recurring date of the event).
';
$string['repeatasfollows'] = 'Repeat the event date above as follows';
$string['repeatevery'] = 'Repeat every';
$string['repeaton'] = 'Repeat on';
$string['repeatuntil'] = 'Repeat until';
$string['requirednamefield'] = 'Enter the name of the room to create automatically.';
$string['roomcreator'] = 'Room creator:';
$string['roomname'] = 'Room name';
$string['roomurl'] = 'Room url';
$string['roomurlexpanded'] = 'Room url expanded';
$string['roomurlexpanded_desc'] = 'Show the "Room url" settings as expanded by default when creating new Room.';
$string['show'] = 'Show';
$string['strftimedm'] = '%a. %d %b.';
$string['strftimedmy'] = '%a. %d %b. %Y';
$string['strftimedmyhm'] = '%a. %d %b. %Y %H:%M';
$string['strftimehm'] = '%H:%M';
$string['syncwithgoogledrive'] = 'Sync with Google Drive';
$string['thereisnorecordingtoshow'] = 'There is no recording to show.';
$string['timeahead'] = 'Is not possible to create multiple recurrences of the event date that exceed one year, adjust the start and end dates.';
$string['timedate'] = '%d/%m/%Y %H:%M';
$string['to'] = 'to';
$string['today'] = 'Today';
$string['upcomingevents'] = 'Upcoming events';
$string['url_failed'] = 'A valid Google Meet URL is required';
$string['visible'] = 'Visible';
$string['week'] = 'Week(s)';
