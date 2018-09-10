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
 * Supporting class for calculating autoupdate
 *
 * @package   mod_checklist
 * @copyright 2015 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_checklist\local;

defined('MOODLE_INTERNAL') || die();

class autoupdate {
    protected static $uselegacy = null;
    /** @var \core\log\sql_select_reader */
    protected static $reader = null;

    public static function get_log_actions_legacy($modname) {
        switch ($modname) {
            case 'survey':
                return array('submit');
                break;
            case 'quiz':
                return array('close attempt');
                break;
            case 'forum':
                return array('add post', 'add discussion');
                break;
            case 'resource':
                return array('view');
                break;
            case 'hotpot':
                return array('submit');
                break;
            case 'wiki':
                return array('edit');
                break;
            case 'checklist':
                return array('complete');
                break;
            case 'choice':
                return array('choose');
                break;
            case 'lams':
                return array('view');
                break;
            case 'scorm':
                return array('view');
                break;
            case 'assignment':
                return array('upload');
                break;
            case 'journal':
                return array('add entry');
                break;
            case 'lesson':
                return array('end');
                break;
            case 'realtimequiz':
                return array('submit');
                break;
            case 'workshop':
                return array('submit');
                break;
            case 'glossary':
                return array('add entry');
                break;
            case 'data':
                return array('add');
                break;
            case 'chat':
                return array('talk');
                break;
            case 'feedback':
                return array('submit');
                break;
        }
        return null;
    }

    public static function get_log_action_new($modname) {
        switch ($modname) {
            case 'assign':
                return array('submission', 'created');
                break;
            case 'book':
                return array('course_module', 'viewed');
                break;
            case 'chat':
                return array('message', 'sent');
                break;
            case 'checklist':
                return array('checklist', 'completed');
                break;
            case 'choice':
                return array('answer', 'submitted');
                break;
            case 'choicegroup':
                return array('choice', 'updated');
                break;
            case 'data':
                return array('record', 'created');
                break;
            case 'feedback':
                return array('response', 'submitted');
                break;
            case 'folder':
                return array('course_module', 'viewed');
                break;
            case 'forum':
                return array(
                    array('post', 'created'),
                    array('discussion', 'created'),
                );
                break;
            case 'glossary':
                return array('entry', 'created');
                break;
            case 'hotpot':
                return array('attempt', 'submitted');
                break;
            case 'imscp':
                return array('course_module', 'viewed');
                break;
            case 'lesson':
                return array('lesson', 'ended');
                break;
            case 'lti':
                return array('course_module', 'viewed');
                break;
            case 'page':
                return array('course_module', 'viewed');
                break;
            case 'quiz':
                return array('attempt', 'submitted');
                break;
            case 'resource':
                return array('course_module', 'viewed');
                break;
            case 'scorm':
                return array('sco', 'launched');
                break;
            case 'survey':
                return array('response', 'submitted');
                break;
            case 'url':
                return array('course_module', 'viewed');
                break;
            case 'wiki':
                return array('page', 'updated');
                break;
            case 'workshop':
                return array('submission', 'created');
                break;
        }
        return null;
    }

    /**
     * Get a list of all userids where the user has a log entry with the given cmid and action(s).
     *
     * @param string $modname
     * @param int $cmid
     * @param int[] $checklistuserids limit the search to the given users
     * @return int[]
     */
    public static function get_logged_userids($modname, $cmid, $checklistuserids) {
        self::init_log_status();

        $userids = array();
        if (self::$uselegacy) {
            $userids = array_merge($userids, self::get_logged_userids_legacy($modname, $cmid, $checklistuserids));
        }
        if (self::$reader) {
            $userids = array_merge($userids, self::get_logged_userids_new($modname, $cmid, $checklistuserids));
        }
        return array_unique($userids);
    }

    protected static function init_log_status() {
        global $CFG;
        if (self::$uselegacy !== null) {
            return;
        }

        $manager = get_log_manager();
        $allreaders = $manager->get_readers();
        if (isset($allreaders['logstore_legacy'])) {
            self::$uselegacy = true;
        } else {
            self::$uselegacy = false;
        }

        if ($CFG->branch < 29) {
            $selectreaders = $manager->get_readers('\core\log\sql_select_reader');
        } else {
            $selectreaders = $manager->get_readers('\core\log\sql_reader');
        }
        if ($selectreaders) {
            self::$reader = reset($selectreaders);
        }
    }

    protected static function get_logged_userids_legacy($modname, $cmid, $userids) {
        global $DB;

        $logactions = self::get_log_actions_legacy($modname);
        if (!$logactions) {
            return array();
        }

        list($usql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['cmid'] = $cmid;
        $params['action1'] = $logactions[0];

        $action2 = '';
        if (isset($logactions[1])) {
            $action2 = ' OR action = :action2 ';
            $params['action2'] = $logactions[1];
        }

        $sql = "SELECT DISTINCT userid
                  FROM {log}
                 WHERE cmid = :cmid AND (action = :action1 $action2)
                   AND userid $usql ";
        $userids = $DB->get_fieldset_sql($sql, $params);

        return $userids;
    }

    protected static function get_logged_userids_new($modname, $cmid, $userids) {
        global $DB;

        $action = self::get_log_action_new($modname);
        if (!$action) {
            return array();
        }

        list($usql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params['contextinstanceid'] = $cmid;
        $params['contextlevel'] = CONTEXT_MODULE;
        $params['target'] = $action[0];
        $params['action'] = $action[1];
        $select = "contextinstanceid = :contextinstanceid AND contextlevel = :contextlevel
                   AND target = :target AND action = :action AND userid $usql";

        $userids = array();
        $entries = self::$reader->get_events_select($select, $params, '', 0, 0);
        foreach ($entries as $entry) {
            $userids[$entry->userid] = $entry->userid;
        }
        return array_values($userids);
    }

    /**
     * Get details of the logs for the given courses, since the given timestamp.
     *
     * @param int[] $courseids
     * @param int $lastlogtime
     * @return object[]
     */
    public static function get_logs($courseids, $lastlogtime) {
        self::init_log_status();

        $logs = array();
        if (self::$uselegacy) {
            $logs = array_merge($logs, self::get_logs_legacy($courseids, $lastlogtime));
        }
        if (self::$reader) {
            $logs = array_merge($logs, self::get_logs_new($courseids, $lastlogtime));
        }
        return $logs;
    }

    protected static function get_logs_legacy($courseids, $lastlogtime) {
        global $DB;

        list($csql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params['time'] = $lastlogtime;
        $logs = get_logs("l.time >= :time AND l.course $csql AND cmid > 0", $params, 'l.time ASC', '', '', $totalcount);
        $ret = array();
        foreach ($logs as $log) {
            $wantedactions = self::get_log_actions_legacy($log->module);
            if (in_array($log->action, $wantedactions)) {
                $ret[] = $log;
            }
        }
        return $ret;
    }

    protected static function get_module_from_component($component) {
        list($type, $name) = \core_component::normalize_component($component);
        if ($type == 'mod') {
            return $name;
        }
        if ($type == 'assignsubmission') {
            return 'assign';
        }
        return null;
    }

    protected static function get_logs_new($courseids, $lastlogtime) {
        global $DB;

        list($csql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params['time'] = $lastlogtime;
        $select = "courseid $csql AND timecreated > :time";
        $entries = self::$reader->get_events_select($select, $params, 'timecreated ASC', 0, 0);
        $ret = array();
        foreach ($entries as $entry) {
            $info = self::get_entry_info($entry);
            if ($info) {
                $ret[] = $info;
            }
        }

        return $ret;
    }

    protected static function get_entry_info($entry) {
        $module = self::get_module_from_component($entry->component);
        if ($module) {
            $wantedaction = self::get_log_action_new($module);
            if ($wantedaction) {
                if (!is_array($wantedaction[0])) {
                    // Most activities only have a single 'complete' action, but to support those with more
                    // than one (forum!), wrap those with just one action in an array.
                    $wantedaction = array($wantedaction);
                }
                foreach ($wantedaction as $candidate) {
                    list($target, $action) = $candidate;
                    if ($entry->target == $target && $entry->action == $action) {
                        return (object)array(
                            'course' => $entry->courseid,
                            'module' => $module,
                            'cmid' => $entry->contextinstanceid,
                            'userid' => $entry->userid,
                        );
                    }
                }
            }
        }
        return null;
    }

    public static function update_from_event(\core\event\base $event) {
        global $CFG;
        require_once($CFG->dirroot.'/mod/checklist/autoupdate.php');
        if ($event->target == 'course_module_completion' && $event->action == 'updated') {
            // Update from a completion change event.
            $comp = $event->get_record_snapshot('course_modules_completion', $event->objectid);
            // Update any relevant checklists.
            checklist_completion_autoupdate($comp->coursemoduleid, $comp->userid, $comp->completionstate);
        } else if ($event->target == 'course' && $event->action == 'completed') {

            // Update from a course completion event.
            checklist_course_completion_autoupdate($event->courseid, $event->relateduserid);

        } else {
            // Check if this is an action that counts as 'completing' an activity (when completion is off).
            $info = self::get_entry_info($event);
            if (!$info) {
                return;
            }
            // Update any relevant checklists.
            checklist_autoupdate_internal($info->course, $info->module, $info->cmid, $info->userid);
        }
    }
}