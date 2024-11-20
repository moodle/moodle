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
 * Strings for component 'tool_task', language 'en'
 *
 * @package    tool_task
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['adhoc'] = 'Ad hoc';
$string['adhoctaskid'] = 'Ad hoc task ID: {$a}';
$string['adhoctaskrun'] = 'Ad hoc task run initiated';
$string['adhoctasks'] = 'Ad hoc tasks';
$string['adhoctasksdue'] = 'Ad hoc tasks due';
$string['adhoctasksfailed'] = 'Ad hoc tasks failed';
$string['adhoctasksfuture'] = 'Future ad hoc tasks';
$string['adhoctasksrunning'] = 'Ad hoc tasks running';
$string['asap'] = 'ASAP';
$string['adhocempty'] = 'Ad hoc task queue is empty';
$string['adhocqueuesize'] = 'Ad hoc task queue has {$a} tasks';
$string['adhocqueueold'] = 'Oldest unprocessed task is {$a->age}, which is more than {$a->max}';
$string['backtoadhoctasks'] = 'Back to ad hoc tasks';
$string['backtoscheduledtasks'] = 'Back to scheduled tasks';
$string['blocking'] = 'Blocking';
$string['cannotfindthepathtothecli'] = 'Cannot find the path to the PHP CLI executable so task execution aborted. Set the \'Path to PHP CLI\' setting in Site administration / Server / System paths.';
$string['checkadhocqueue'] = 'Ad hoc task queue';
$string['checkcronrunning'] = 'Cron running';
$string['checkmaxfaildelay'] = 'Tasks max fail delay';
$string['classname'] = 'Class name';
$string['checklongrunningtasks'] = 'Long running tasks';
$string['checklongrunningtaskcount'] = 'Long running tasks: {$a}';
$string['clearfaildelay_confirm'] = 'Are you sure you want to clear the fail delay for task \'{$a}\'? After clearing the delay, the task will run according to its normal schedule.';
$string['component'] = 'Component';
$string['corecomponent'] = 'Core';
$string['crondisabled'] = 'Cron is disabled. No new tasks will be started. The system will not operate properly until it is enabled again.';
$string['cronok'] = 'Cron is running frequently';
$string['default'] = 'Default';
$string['defaultx'] = 'Default: {$a}';
$string['disabled'] = 'Disabled';
$string['disabled_help'] = 'Disabled scheduled tasks are not executed from cron, however they can still be executed manually via the CLI tool.';
$string['edittaskschedule'] = 'Edit task schedule: {$a}';
$string['enablerunnow'] = 'Allow \'Run now\' for scheduled tasks';
$string['enablerunnow_desc'] = 'Allows administrators to run a single scheduled task immediately, rather than waiting for it to run as scheduled. The feature requires \'Path to PHP CLI\' (pathtophp) to be set in System paths. The task runs on the web server, so you may wish to disable this feature to avoid potential performance issues.';
$string['faildelay'] = 'Fail delay';
$string['failed'] = 'Failed';
$string['fromcomponent'] = 'From component: {$a}';
$string['hostname'] = 'Host name';
$string['lastcronstart'] = 'Time since last cron run: {$a}';
$string['lastruntime'] = 'Last run';
$string['lastupdated'] = 'Last updated {$a}.';
$string['nextruntime'] = 'Next run';
$string['noclassname'] = 'Class name not specified';
$string['notasks'] = 'No tasks to run';
$string['payload'] = 'Payload';
$string['pid'] = 'PID';
$string['plugindisabled'] = 'Plugin disabled';
$string['pluginname'] = 'Scheduled task configuration';
$string['resettasktodefaults'] = 'Reset task schedule to defaults';
$string['resettasktodefaults_help'] = 'This will discard any local changes and revert the schedule for this task back to its original settings.';
$string['run_adhoctasks'] = 'Run ad hoc tasks';
$string['runningalltasks'] = 'Running all tasks';
$string['runningfailedtasks'] = 'Running failed tasks';
$string['runningtasks'] = 'Tasks running now';
$string['runnow'] = 'Run now';
$string['runagain'] = 'Run again';
$string['runadhoc_confirm'] = 'Tasks will run on the web server and may take some time to complete.';
$string['runadhoc'] = 'Run ad hoc tasks now?';
$string['runadhoctask'] = 'Run \'{$a->task}\' task ID {$a->taskid}';
$string['runadhoctasks'] = 'Run all \'{$a}\' tasks';
$string['runadhoctasksfailed'] = 'Run failed \'{$a}\' tasks';
$string['runnow_confirm'] = 'Are you sure you want to run this task \'{$a}\' now? The task will run on the web server and may take some time to complete.';
$string['runclassname'] = 'Run all';
$string['runclassnamefailedonly'] = 'Run all failed';
$string['runpattern'] = 'Run pattern';
$string['scheduled'] = 'Scheduled';
$string['scheduledtasks'] = 'Scheduled tasks';
$string['scheduledtaskchangesdisabled'] = 'Modifications to the list of scheduled tasks have been prevented in Moodle configuration';
$string['slowtask'] = 'Task has run for longer than {$a}';
$string['showall'] = 'Show all';
$string['showfailedonly'] = 'Show failed only';
$string['showsummary'] = 'Show ad hoc tasks summary';
$string['started'] = 'Started';
$string['taskage'] = 'Run time';
$string['taskdetails'] = 'Tasks running for more than {$a->time} (max {$a->maxtime}): {$a->count}';
$string['taskdisabled'] = 'Task disabled';
$string['taskfailures'] = '{$a} task(s) failing';
$string['taskid'] = 'Task ID';
$string['tasklogs'] = 'Task logs';
$string['tasknofailures'] = 'There are no tasks failing';
$string['taskrunningtime'] = 'Task has run for {$a}';
$string['taskscheduleday'] = 'Day';
$string['taskscheduleday_help'] = 'Day of month field for task schedule. The field uses the same format as unix cron. Some examples are:

* <strong>*</strong> Every day
* <strong>*/2</strong> Every 2nd day
* <strong>1</strong> The first of every month
* <strong>1,15</strong> The first and fifteenth of every month';
$string['taskscheduledayofweek'] = 'Day of week';
$string['taskscheduledayofweek_help'] = 'Day of week field for task schedule. The field uses the same format as unix cron. Some examples are:

* <strong>*</strong> Every day
* <strong>0</strong> Every Sunday
* <strong>6</strong> Every Saturday
* <strong>1,5</strong> Every Monday and Friday';
$string['taskschedulehour'] = 'Hour';
$string['taskschedulehour_help'] = 'Hour field for task schedule. The field uses the same format as unix cron. Some examples are:

* <strong>*</strong> Every hour
* <strong>*/2</strong> Every 2 hours
* <strong>2-10</strong> Every hour from 2am until 10am (inclusive)
* <strong>2,6,9</strong> 2am, 6am and 9am';
$string['taskscheduleminute'] = 'Minute';
$string['taskscheduleminute_help'] = 'Minute field for task schedule. The field uses the same format as unix cron. Some examples are:

* <strong>*</strong> Every minute
* <strong>*/5</strong> Every 5 minutes
* <strong>2-10</strong> Every minute between 2 and 10 past the hour (inclusive)
* <strong>2,6,9</strong> 2, 6 and 9 minutes past the hour';
$string['taskschedulemonth'] = 'Month';
$string['taskschedulemonth_help'] = 'Month field for task schedule. The field uses the same format as unix cron. Some examples are:

* <strong>*</strong> Every month
* <strong>*/2</strong> Every second month
* <strong>1</strong> Every January
* <strong>1,5</strong> Every January and May';
$string['privacy:metadata'] = 'The Scheduled task configuration plugin does not store any personal data.';
$string['viewlogs'] = 'View logs for {$a}';
