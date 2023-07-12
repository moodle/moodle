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
 * Course trainingsessions report
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');

if (!function_exists('debug_trace')) {
    function debug_trace($str) {
        // Simulates the local/advancedperfs library.
    }
}

/**
 * This special report allows wrapping to course report crons
 * function that otherwise would not be considered by cron task.
 *
 * for repetitive tasks, we will not delete the task record and push the batchdate ahead to the next date.
 */
function report_trainingsessions_crontask() {
    global $CFG;

    mtrace("Starting trainingsession cron.");

    if (!$tasks = unserialize(@$CFG->trainingreporttasks)) {
        mtrace('empty task stack...');
        return;
    }

    foreach ($tasks as $taskid => $task) {
        mtrace("\tStarting registering $task->taskname...");
        if (time() < $task->batchdate && !optional_param('force', false, PARAM_BOOL)) {
            mtrace("\t\tnot yet.");
            debug_trace(time().": task $task->id not in time ($task->batchdate) to run");
            continue;
        }

        switch ($task->reportformat) {
            case 'pdf':
                switch ($task->reportlayout) {
                    case 'onefulluserpersheet':
                        $reporttype = 'peruser';
                        $range = 'group';
                        break;

                    case 'onefulluserperfile':
                        $reporttype = 'peruser';
                        $range = 'user';
                        break;

                    case 'oneuserperrow':
                        $reporttype = 'summary';
                        $range = 'group';
                        break;

                    case 'allusersessionssinglefile':
                        $reporttype = 'sessions';
                        $range = 'group';
                        break;

                    default:
                        $reporttype = 'sessions';
                        $range = 'user';
                }
                break;

            case 'xls':
                switch($task->reportlayout) {
                    case 'onefulluserpersheet':
                        $reporttype = 'peruser';
                        $range = 'user';
                        break;

                    case 'oneuserperrow':
                        $reporttype = 'summary';
                        $range = 'group';
                        break;

                    case 'allusersessionssinglefile':
                        $reporttype = 'sessions';
                        $range = 'group';
                        break;

                    default:
                        $reporttype = 'sessions';
                        $range = 'user';
                }
                break;

            case 'csv':
                switch ($task->reportlayout) {
                    case 'allusersessionssinglefile':
                    case 'onefulluserpersheet':
                        // Silently unseupported.
                        break;
                    case 'oneuserperrow':
                        $reporttype = 'summary';
                        $range = 'group';
                        break;
                    default:
                        $reporttype = 'sessions';
                        $range = 'user';
                }
                break;

            default:
        }

        $tf = $task->reportformat;
        $version = report_trainingsessions_supports_feature('format/'.$tf);
        $versionpath = ($version == 'pro') ? 'pro/' : '';

        if ($range == 'group') {
            $url = '/report/trainingsessions/'.$versionpath.'tasks/group'.$tf.'report'.$reporttype.'_batch_task.php';
            $uri = new moodle_url($url);
        } else {
            $url = '/report/trainingsessions/'.$versionpath.'batchs/group'.$tf.'report'.'_batch.php';
            $uri = new moodle_url($url);
        }

        $taskarr = (array)$task;
        $rqarr = array();
        $taskarr['id'] = $taskarr['courseid']; // Add the course reference of the batch.
        $taskarr['timesession'] = time(); // Add the time.

        /*
         * Setup the back office security. This ticket is used all along the batch chain
         * to allow cron or bounce processes to run.
         */
        if (file_exists($CFG->dirroot.'/auth/ticket/lib.php')) {
            $user = new StdClass();
            $user->username = 'admin';
            include_once($CFG->dirroot.'/auth/ticket/lib.php');
            $taskarr['ticket'] = ticket_generate($user, 'batch web distribution', '');
        }

        foreach ($taskarr as $key => $value) {
            $rqarr[] = $key.'='.urlencode($value);
        }
        $rq = implode('&', $rqarr);

        $ch = curl_init($uri.'?'.$rq);

        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle Report Batch');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rq);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);

        if ($task->replay > TASK_SINGLE) {
            // Replaydelay in seconds.
            $task->batchdate = $task->batchdate + $task->replaydelay * MINSECS;
            mtrace('Bouncing task '.$taskid.' to '.userdate($task->batchdate));
        } else {
            unset($tasks[$task->id]);
            mtrace('Removing task '.$taskid);
        }
    }

    // Update in config.
    set_config('trainingreporttasks', serialize($tasks));

    mtrace("\tdone.");
}
