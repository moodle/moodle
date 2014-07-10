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

$string['allevents'] = 'All events';
$string['allmodules'] = 'All modules';
$string['core'] = 'Core';
$string['customizefilters'] = 'Select the frequency of the events';
$string['customizemessage'] = 'Cutomize the notification message';
$string['description'] = 'Description:';
$string['description_help'] = "Description is displayed to users when they want to subscribe to this rule. This helps them understand what the rule is about.";
$string['defaultmessagetpl'] = 'Rule "{rulename}" has happened. You can find further details at {link}';
$string['eventnotfound'] = 'Event not found';
$string['errorincorrectevent'] = 'Please select an event related to the selected plugin';
$string['freqdesc'] = '{$a->freq} times in {$a->mins} minutes';
$string['managesubscriptions'] = 'Manage subscriptions';
$string['managerules'] = 'Manage rules';
$string['messageheader'] = 'Customize your notification message';
$string['messagetemplate'] = 'Message template';
$string['messagetemplate_help'] = 'This is the content of the message that will be sent to users, when the given conditions of the rule are met. You are allowed to use following templates in this.
<br /> {link} - Link to the location where the event happened.
<br /> {modulelink} - Link to the module where the event has happened.
<br /> {rulename} - Name of this rule.
<br /> {description} - Rule description.
<br /> {eventname} - Name of the event associated with the rule.';
$string['minutes'] = 'in minutes:';
$string['name'] = 'Name of the rule: ';
$string['name_help'] = "Choose a name for the rule.";
$string['norules'] = 'There are no rules you can subscribe to.';
$string['manageruleslink'] = 'You can manage rules from {$a} page.';
$string['pluginname'] = 'Event monitor';
$string['processevents'] = 'Process events';
$string['selectcourse'] = 'Visit this report at course level to get a list of possible modules';
$string['selectevent'] = 'Select an event:';
$string['selectevent_help'] = "Select an event to monitor.";
$string['selectfrequency'] = 'Frequency of events:';
$string['selectfrequency_help'] = "Frequency defines the denisty of the event occurance. Select criterias to define how frequently the event should happen to trigger the notification.";
$string['selectminutes'] = 'in minutes:';
$string['selectplugin'] = 'Select the plugin type:';
$string['selectplugin_help'] = "Select a plugin that you are interested in monitoring.";
$string['title'] = '{$a->coursename} : {$a->reportname}';
$string['tool/monitor:managerules'] = 'Manage event monitor rules';
$string['tool/monitor:subscribe'] = 'Subscribe to event monitor rules';

