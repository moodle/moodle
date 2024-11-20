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
 * English log store lang strings.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['endpoint'] = 'Your LRS endpoint for the xAPI';
$string['settings'] = 'General Settings';
$string['xapifieldset'] = 'Custom example fieldset';
$string['xapi'] = 'xAPI';
$string['password'] = 'Your LRS basic auth secret/password for the xAPI';
$string['pluginadministration'] = 'Logstore xAPI administration';
$string['pluginname'] = 'Logstore xAPI';
$string['submit'] = 'Submit';
$string['username'] = 'Your LRS basic auth key/username for the xAPI';
$string['xapisettingstitle'] = 'Logstore xAPI Settings';
$string['backgroundmode'] = 'Send statements by scheduled task?';
$string['backgroundmode_desc'] = 'This will force Moodle to send the statements to the LRS in the background,
        via a cron task to avoid blocking page responses. This will make the process less close to real time, but will help to prevent unpredictable
        Moodle performance linked to the performance of the LRS.';
$string['maxbatchsize'] = 'Maximum batch size';
$string['maxbatchsize_desc'] = 'Statements are sent to the LRS in batches. This setting controls the maximum number of
        statements that will be sent in a single operation. Setting this to zero will cause all available statements to
        be sent at once, although this is not recommended.';
$string['maxbatchsizeforfailed'] = 'Maximum batch size for failed requests';
$string['maxbatchsizeforfailed_desc'] = 'Statements are sent to the LRS in batches. This setting controls the maximum number of
        statements that will be sent in a single operation for failed requests. Setting this to zero will cause all available statements to
        be sent at once, although this is not recommended.';
$string['maxbatchsizeforhistorical'] = 'Maximum batch size for historical requests';
$string['maxbatchsizeforhistorical_desc'] = 'Statements are sent to the LRS in batches. This setting controls the maximum number of
        statements that will be sent in a single operation for historical requests. Setting this to zero will cause all available statements to
        be sent at once, although this is not recommended.';
$string['taskemit'] = 'Emit records to LRS';
$string['taskfailed'] = 'Emit failed records to LRS';
$string['taskhistorical'] = 'Emit historical records to LRS';
$string['tasksendfailednotifications'] = 'Send failed notifications';
$string['enablesendingnotifications'] = 'Send notifications?';
$string['enablesendingnotifications_desc'] = 'Control if notifications should be sent to configured recipients.';
$string['errornotificationtrigger'] = 'Error notification trigger';
$string['errornotificationtrigger_desc'] = 'Threshold value at which point notifications will be triggered. When a number of errors greater than this value have been generated, the notification is sent.';
$string['cohorts'] = 'Cohorts';
$string['cohorts_help'] = 'Add cohort(s) to notifications';
$string['includecohorts'] = 'Include these cohorts in notifications';
$string['send_additional_email_addresses'] = 'Additional email addresses';
$string['send_additional_email_addresses_desc'] = 'Send notifications to list of email addresses. Comma separated values.';
$string['routes'] = 'Include actions with these routes';
$string['failed_events'] = 'event(s) have failed to send to the LRS.';
$string['successful_events'] = 'event(s) have been successfully processed.';
$string['filters'] = 'Filter logs';
$string['logguests'] = 'Log guest actions';
$string['filters_help'] = 'Enable filters that INCLUDE some actions to be logged.';
$string['mbox'] = 'Identify users by email';
$string['mbox_desc'] = 'Statements will identify users with their email (mbox) when this box is ticked.';
$string['send_username'] = 'Identify users by username';
$string['send_username_desc'] = 'Statements will identify users with their username when this box is ticked, but only if identifying users by email is disabled.';
$string['send_jisc_data'] = 'Adds JISC data to statements';
$string['send_jisc_data_desc'] = 'Statements will contain data required by JISC.';
$string['shortcourseid'] = 'Send short course name';
$string['shortcourseid_desc'] = 'Statements will contain the shortname for a course as a short course id extension';
$string['sendidnumber'] = 'Send course and activity ID number';
$string['sendidnumber_desc'] = 'Statements will include the ID number (admin defined) for courses and activities in the object extensions';
$string['send_response_choices'] = 'Send response choices';
$string['send_response_choices_desc'] = 'Statements for multiple choice and sequencing question answers will be sent to the LRS with the correct response and potential choices';
$string['resendfailedbatches'] = 'Resend failed batches';
$string['resendfailedbatches_desc'] = 'When processing events in batches, try re-sending events in smaller batches if a batch fails. If not selected, the whole batch will not be sent in the event of a failed event.';
$string['type'] = 'Type';
$string['eventname'] = 'Event Name';
$string['username'] = 'Username';
$string['eventcontext'] = 'Event Context';
$string['response'] = 'Response';
$string['errortype'] = 'Error Type';
$string['info'] = 'Info';
$string['datetimegmt'] = 'Date/Time (GMT)';
$string['logstorexapierrorlog'] = 'Logstore xAPI Error Log';
$string['logstorexapihistoriclog'] = 'Logstore xAPI Historic Log';
$string['noerrorsfound'] = 'No errors found';
$string['datetovalidation'] = 'The To date cannot be before the From date';
$string['failedtransformresponse'] = 'Event: "{$a}" was not transformed successfully';
$string['privacy:metadata:logstore_xapi_log'] = 'xAPI holding table for cron processing';
$string['privacy:metadata:logstore_xapi_log:userid'] = 'User Id of xAPI holding table for cron processing';
$string['privacy:metadata:logstore_xapi_failed_log'] = 'xAPI holding table for failed events';
$string['privacy:metadata:logstore_xapi_failed_log:userid'] = 'User Id of xAPI holding table for failed events';
$string['failedtosend'] = "The following statements have failed to be sent to the LDH.";
$string['errorlogpage'] = "Error log page";
$string['failurelog'] = "Failure log";
$string['failedsubject'] = "XAPI Logstore: failed to send messages report";
$string['insendfailednotificationstask'] = 'In send failed notifications task execute';
$string['norows'] = "No rows to report";
$string['notificationsnotenabled'] = "Notifications not enabled";
$string['notificationtriggerlimitnotreached'] = "Notification trigger limit not reached";
$string['user'] = 'User';
$string['user_help'] = 'Searches the users fullname';
$string['contextidnolongerexists'] = 'Context ID {$a} no longer exists';
$string['count'] = 'Count';
$string['unknownverb'] = 'Unknown verb was requested, It should be set by developer.';

// Capabilities.
$string['xapi:viewerrorlog'] = 'View xAPI error log';
$string['xapi:manageerrors'] = 'Replay failed statements';
$string['xapi:managehistoric'] = 'Manage historic data';

// Info strings from xAPI errors.
$string['networkerror'] = 'There was a network error sending the response.';
$string['recipeerror'] = 'The LDH responded with a 400 error, this can be due to an issue with the recipe.';
$string['autherror'] = 'The server is returning a 401 error. Please ensure the endpoint, username and auth secret/password for
    the xAPI is correct in the Logstore xAPI settings.';
$string['unknownerror'] = 'Error code: "{$a}"';
$string['lrserror'] = 'There is a problem with the LDH. The LDH has responded with a 500 error.';

$string['failed'] = 'Failed';
$string['resendevents'] = 'Resend ({$a->count})';
$string['sendevents'] = 'Send ({$a->count})';
$string['replayevent'] = 'Replay event';
$string['confirmresendeventsheader'] = 'Resend events';
$string['confirmsendeventsheader'] = 'Send events';
$string['confirmresendevents'] = 'You are about to send {$a->count} record(s) to the queue for reprocessing.<br>Do you wish to continue?';
$string['confirmsendevents'] = 'You are about to send {$a->count} record(s) to the queue for reprocessing.<br>Do you wish to continue?';
$string['resendevents:success'] = 'Events successfully sent for reprocessing.';
$string['resendevents:failed'] = 'Events failed to be sent for reprocessing.';
$string['lmsinstance'] = 'LMS instance on which the error(s) occurred';
