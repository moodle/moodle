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
 * Lang strings.
 *
 * This files lists lang strings related to tool_monitor.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addrule'] = 'Add a new rule';
$string['allevents'] = 'All events';
$string['allmodules'] = 'All instances';
$string['area'] = 'Area';
$string['areatomonitor'] = 'Area to monitor';
$string['cachedef_eventsubscriptions'] = 'This stores the list of event subscriptions for individual courses';
$string['contactadmin'] = 'Contact your administrator to enable it.';
$string['core'] = 'Core';
$string['coresubsystem'] = 'Subsystem ({$a})';
$string['currentsubscriptions'] = 'Your current subscriptions';
$string['defaultmessagetemplate'] = 'Rule name: {rulename}<br />Description: {description}<br />Event name: {eventname}';
$string['deleterule'] = 'Delete rule';
$string['deletesubscription'] = 'Delete subscription';
$string['description'] = 'Description:';
$string['disablefieldswarning'] = 'Some fields can not be edited as this rule already has subscriptions.';
$string['duplicaterule'] = 'Duplicate rule';
$string['editrule'] = 'Edit rule';
$string['enablehelp'] = 'Enable/disable event monitoring';
$string['enablehelp_help'] = 'Event monitoring must be enabled before you can create and subscribe to rules. Note that enabling Event monitoring may affect the performance of your site.';
$string['event'] = 'Event';
$string['eventnotfound'] = 'Event not found';
$string['eventrulecreated'] = 'Rule created';
$string['eventruledeleted'] = 'Rule deleted';
$string['eventruleupdated'] = 'Rule updated';
$string['eventsubcreated'] = 'Subscription created';
$string['eventsubcriteriamet'] = 'Subscription criteria met';
$string['eventsubdeleted'] = 'Subscription deleted';
$string['errorincorrectevent'] = 'Please select an event related to the selected plugin';
$string['freqdesc'] = '{$a->freq} time(s) in {$a->mins} minute(s)';
$string['frequency'] = 'Notification threshold';
$string['frequency_help'] = 'The number of events within a specified time period required for a notification message to be sent.';
$string['inminutes'] = 'in minutes';
$string['invalidmodule'] = 'Invalid module';
$string['manageruleslink'] = 'You can manage rules from the {$a} page.';
$string['managesubscriptionslink'] = 'You can subscribe to rules from the {$a} page.';
$string['manage'] = 'Manage';
$string['managesubscriptions'] = 'Event monitoring';
$string['managerules'] = 'Event monitoring rules';
$string['messageprovider:notification'] = 'Notifications of rule subscriptions';
$string['messagetemplate'] = 'Notification message';
$string['messagetemplate_help'] = 'A notification message is sent to subscribers once the notification threshold has been reached. It can include any or all of the following placeholders:

* Link to the location of the event {link}
* Link to the area monitored {modulelink}
* Name of the course module {modulename}
* Rule name {rulename}
* Description {description}
* Event {eventname}
* Full name of the course {coursefullname}
* Short name of the course {courseshortname}';
$string['messagetemplate_link'] = 'admin/tool/monitor/managerules';
$string['moduleinstance'] = 'Instance';
$string['monitorenabled'] = 'Event monitoring is currently enabled. ';
$string['monitordisabled'] = 'Event monitoring is currently disabled.';
$string['monitor:managerules'] = 'Manage event monitor rules';
$string['monitor:managetool'] = 'Enable/disable event monitoring';
$string['monitor:subscribe'] = 'Subscribe to event monitor rules';
$string['norules'] = 'There are no event monitoring rules.';
$string['pluginname'] = 'Event monitor';
$string['privacy:createdrules'] = 'Event monitor rules I created';
$string['privacy:metadata:description'] = 'Description of the rule';
$string['privacy:metadata:eventname'] = 'Fully qualified name of the event';
$string['privacy:metadata:frequency'] = 'Frequency of notifications';
$string['privacy:metadata:historysummary'] = 'Stores the history of the message notifications sent';
$string['privacy:metadata:inactivedate'] = 'Period of time, in days, after which an inactive subscription will be removed completely';
$string['privacy:metadata:lastnotificationsent'] = 'When a notification was last sent for this subscription.';
$string['privacy:metadata:messagesummary'] = 'Notifications are sent to the message system.';
$string['privacy:metadata:name'] = 'Name of the rule';
$string['privacy:metadata:plugin'] = 'Frankenstlye name of the plugin';
$string['privacy:metadata:rulessummary'] = 'This stores monitor rules.';
$string['privacy:metadata:subscriptionssummary'] = 'Stores user subscriptions to various rules';
$string['privacy:metadata:template'] = 'Message template';
$string['privacy:metadata:timecreatedrule'] = 'When this rule was created';
$string['privacy:metadata:timecreatedsub'] = 'When this subscription was created';
$string['privacy:metadata:timemodifiedrule'] = 'When this rule was last modified';
$string['privacy:metadata:timesent'] = 'When the message was sent';
$string['privacy:metadata:timewindow'] = 'Time window in seconds';
$string['privacy:metadata:userid'] = 'The ID of user who created the rule.';
$string['privacy:metadata:useridhistory'] = 'The ID of the user to whom this notification was sent';
$string['privacy:metadata:useridsub'] = 'The ID of the subscriber.';
$string['privacy:subscriptions'] = 'My event monitor subscriptions';
$string['processevents'] = 'Process events';
$string['rulename'] = 'Rule name';
$string['ruleareyousure'] = 'Are you sure you want to delete the rule "{$a}"?';
$string['ruleareyousureextra'] = 'There are {$a} subscription(s) to this rule that will also be deleted.';
$string['rulecopysuccess'] = 'Rule successfully duplicated';
$string['ruledeletesuccess'] = 'Rule successfully deleted';
$string['rulehelp'] = 'Rule details';
$string['rulehelp_help'] = 'This rule listens for when the event \'{$a->eventname}\' in \'{$a->eventcomponent}\' has been triggered {$a->frequency} time(s) in {$a->minutes} minute(s).';
$string['rulenopermission'] = 'You do not have permission to subscribe to any events.';
$string['rulenopermissions'] = 'You do not have permissions to "{$a} a rule"';
$string['rulescansubscribe'] = 'Rules you can subscribe to';
$string['selectacourse'] = 'Select a course';
$string['selectcourse'] = 'Visit this report at course level to get a list of possible modules';
$string['subareyousure'] = 'Are you sure you want to delete the subscription to the rule "{$a}"?';
$string['subcreatesuccess'] = 'Subscription successfully created';
$string['subdeletesuccess'] = 'Subscription successfully removed';
$string['subhelp'] = 'Subscription details';
$string['subhelp_help'] = 'This subscription listens for when the event \'{$a->eventname}\' has been triggered in \'{$a->moduleinstance}\' {$a->frequency} time(s) in {$a->minutes} minute(s).';
$string['subscribeto'] = 'Subscribe to rule "{$a}"';
$string['taskcleanevents'] = 'Cleanup event monitor events';
$string['taskchecksubscriptions'] = 'Activate/deactivate invalid rule subscriptions';
$string['unsubscribe'] = 'Unsubscribe';
