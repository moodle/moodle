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
 * Strings for plugin 'reminders', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package   local_reminders
 * @copyright 2012 Isuru Madushanka Weerarathna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activityconfduein'] = 'Due In';
$string['activityconfexplicitenable'] = 'Explicit Reminder Activation';
$string['activityconfexplicitenabledesc'] = 'If checked, teachers or relevant authorities must <strong>explicitly</strong> enable reminders for each activity under course reminders settings page. Because of that, all activity reminders will be by default disabled regardless of the schedule defined in below. This configuration will not be impact to the overdue reminders anyway.';
$string['activityconfexplicitenablehint'] = 'Site administrator(s) have disabled sending activity reminders by default. That means, teachers must <em>explictly</em> enable reminders for activities in this course which they want to sent.';
$string['activityconfupcomingactivities'] = 'Upcoming Activities';
$string['activityconfupcomingactivitiesdesc'] = 'Reminders will not be sent for unchecked activities.';
$string['activityconfnoupcomingactivities'] = 'No upcoming activities for this course.';
$string['activitydueopenahead'] = 'Activity Openings Send Before:';
$string['activitydueopenaheaddesc'] = 'Days ahead need to send reminders for activity openings. This setting is valid only if activity openings are enabled to send reminders from above setting.';
$string['activityopeningseparation'] = 'Separate Activity Openings:';
$string['activityopeningseparationdesc'] = 'Show activity openings as separate entry in course reminders settings page.';
$string['activityremindersboth'] = 'For both openings and closings';
$string['activityremindersonlyopenings'] = 'Only for activity openings';
$string['activityremindersonlyclosings'] = 'Only for activity closings';
$string['activityignoreincompletes'] = 'No reminders once completed:';
$string['activityignoreincompletesdetails'] = 'If checked, then no reminders will be sent if the activity is already completed by user, <strong>before</strong> the activity ends.';
$string['admintreelabel'] = 'Reminders';
$string['calendareventupdatedprefix'] = 'UPDATED';
$string['calendareventremovedprefix'] = 'REMOVED';
$string['calendareventcreatedprefix'] = 'ADDED';
$string['calendareventoverdueprefix'] = 'OVERDUE';
$string['caleventchangedheading'] = 'Calendar Events Change Reminders';
$string['caleventchangedheadingdetails'] = 'These settings will be checked <strong>before</strong> considering the individual event type.';
$string['categoryheading'] = 'Course Category Event Reminders';
$string['categorynosendforended'] = 'No Reminders for Completed Courses:';
$string['categorynosendforendeddescription'] = 'If checked, reminders will not be sending for completed courses.';
$string['contentdescription'] = 'Description';
$string['contenttypecategory'] = 'Category';
$string['contenttypecourse'] = 'Course';
$string['contenttypeactivity'] = 'Activity';
$string['contenttypegroup'] = 'Group';
$string['contenttypeuser'] = 'User';
$string['contenttypelocation'] = 'Where';
$string['contentwhen'] = 'When';
$string['courseheading'] = 'Course Event Reminders';
$string['custom'] = 'Custom';
$string['customschedulefallback'] = 'Fallback Custom Schedule';
$string['customschedulefallbackdesc'] = 'If checked, custom schedules will fallback to the value specified in activities for <strong>unknown event types</strong>.';
$string['days7'] = '7 Days';
$string['days3'] = '3 Days';
$string['days1'] = '1 Day';
$string['dueheading'] = 'Activity Event Reminders';
$string['emailconfigsheading'] = 'Reminder Email Customization';
$string['emailfootercustomname'] = 'Custom Email Footer';
$string['emailfootercustomnamedesc'] = 'Specify the footer content to be embedded in every reminder email message. If this content is empty and default footer is disabled, then footer will remove completely from reminders.';
$string['emailfooterdefaultname'] = 'Use Default Email Footer';
$string['emailfooterdefaultnamedesc'] = 'If checked, then default reminder email footer will contain a link to the Moodle calendar. Otherwise, the content provided in customized footer will be used.';
$string['emailheadercustomname'] = 'Custom Email Header';
$string['emailheadercustomnamedesc'] = 'Specify the header content to be embedded in every reminder email message. This can be use to brand these email messages to your site.';
$string['enabled'] = 'Enabled';
$string['enabledoverdue'] = 'Overdue Enabled';
$string['enableddescription'] = 'Enable/disable reminder plugin';
$string['enabledchangedevents'] = 'Send when Event Updated:';
$string['enabledremovedevents'] = 'Send when Event Removed:';
$string['enabledaddedevents'] = 'Send when Event Created:';
$string['enabledchangedeventsdescription'] = 'Indicates whether to send reminders when a calendar event is being updated.';
$string['enabledremovedeventsdescription'] = 'Indicates whether to send reminders when a calendar event is being removed.';
$string['enabledaddedeventsdescription'] = 'Indicates whether to send reminders when a calendar event is being created.';
$string['enabledforcalevents'] = 'Enable for Calendar Change Events:';
$string['enabledforcaleventsdescription'] = 'Enable sending reminders for this type when a calendar event created, deleted, or updated.';
$string['eventtypegradingdue'] = 'Grading Due';
$string['eventtypeexpectcompletionon'] = 'Expected Completion';
$string['eventtypeopen'] = 'Activity Opens';
$string['eventtypeclose'] = 'Activity Closes';
$string['explaincategoryheading'] = 'Reminder settings for course category events.';
$string['explaincourseheading'] = 'Reminder settings for course events. These events are coming from a course.';
$string['explaindueheading'] = 'Reminder settings for activity events. These events are coming from activities/modules within a course.';
$string['explaingroupheading'] = 'Reminder settings for group events. These events are based only for a specific group.';
$string['explaingroupshowname'] = 'Indicates whether group name should be included to the message being sent, or not.';
$string['explainrolesallowedfor'] = 'Choose which users having above roles can receive reminders.';
$string['explainsendactivityreminders'] = 'Indicates in which activity state the reminders should be sent.';
$string['explainsiteheading'] = 'Reminder settings for site events. These events are relevant to all the users in the site.';
$string['explainuserheading'] = 'Reminder settings for user events. These events are individual to each user.';
$string['excludedmodules'] = 'Excluded Modules:';
$string['excludedmodulesdesc'] = 'Reminders will not be sending if an event is generated from above selected modules. This setting is global and applicable for any type of events.';
$string['filterevents'] = 'Filter calendar events:';
$string['filtereventsdescription'] = 'Which calendar events should be filtered and send reminders for them.';
$string['filtereventsonlyhidden'] = 'Only hidden events in calendar';
$string['filtereventsonlyvisible'] = 'Only visible events in calendar';
$string['filtereventssendall'] = 'All events';
$string['groupheading'] = 'Group Event Reminders';
$string['groupshowname'] = 'Show group name in message:';
$string['messageprovider:reminders_course'] = 'Reminder notifications for Course events';
$string['messageprovider:reminders_coursecategory'] = 'Reminder notifications for Course Category events';
$string['messageprovider:reminders_due'] = 'Reminder notifications for Activity events';
$string['messageprovider:reminders_group'] = 'Reminder notifications for Group events';
$string['messageprovider:reminders_site'] = 'Reminder notifications for Site events';
$string['messageprovider:reminders_user'] = 'Reminder notifications for User events';
$string['messagetitleprefix'] = 'Message Title Prefix:';
$string['messagetitleprefixdescription'] = 'This text will be inserted as a prefix (within square brackets) to the title of every reminder message is being sent.';
$string['moodlecalendarname'] = 'Moodle Calendar';
$string['overduemessage'] = 'This activity is overdue!';
$string['plugindisabled'] = 'The plugin is disabled by admin.';
$string['pluginname'] = 'Event Reminders';
$string['privacy:metadata'] = 'The Event Reminders plugin does not store any personal data.';
$string['overdueactivityreminders'] = 'Activity Overdue Reminders:';
$string['overdueactivityremindersdescription'] = 'If checked, then reminders will be sent to users who are overdue the activity.';
$string['overduewarnmessage'] = 'Overdue Warn Message:';
$string['overduewarnmessagedescription'] = 'Enter a <strong>simple text</strong> to be embedded inside overdue emails in red color. If this is empty, then no message will be shown. Also this will be enabled only if the overdue emails are enabled.';
$string['overduewarnprefix'] = 'Overdue Title Prefix:';
$string['overduewarnprefixdescription'] = 'Enter a <strong>simple prefix</strong> to be embedded for overdue emails title. If this is empty, then nothing will be prepended. Also this will be enabled only if the overdue emails are enabled.';
$string['reminderdaysahead'] = 'Send before:';
$string['reminderdaysaheadcustom'] = 'Custom schedule:';
$string['reminderdaysaheadschedule'] = 'Schedule';
$string['reminderdaysaheadcustomdetails'] = 'Additionally specify desired schedule to send reminders in ahead of time for an event.';
$string['reminderfrom'] = 'Reminder from';
$string['reminderstask'] = 'Local Reminders';
$string['reminderstaskclean'] = 'Clean Local Reminders Logs';
$string['rolesallowedfor'] = 'Allowed Roles:';
$string['sendactivityreminders'] = 'Activity reminders:';
$string['sendas'] = 'Send As:';
$string['sendasadmin'] = 'As Site Admin';
$string['sendasdescription'] = 'Specify as who these reminder mails should be sent.';
$string['sendasnametitle'] = 'No Reply Name:';
$string['sendasnamedescription'] = 'Specify display user name for reminder mails when them are sent as No Reply user.';
$string['sendasnoreply'] = 'No Reply Address';
$string['showmodnameintitle'] = 'Show Module name in email subject';
$string['showmodnameintitledesc'] = 'If checked, then corresponding module name will be appended to reminder email subject.';
$string['siteheading'] = 'Site Event Reminders';
$string['taskreminder'] = 'Reminders Task';
$string['titlesubjectprefix'] = 'Reminder';
$string['userheading'] = 'User Event Reminders';
$string['useservertimezone'] = "Use Server Timezone";
