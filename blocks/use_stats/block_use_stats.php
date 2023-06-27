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
 * Master block class for use_stats compiler
 *
 * @package    block_use_stats
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/blocklib.php');
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/blocks/use_stats/lib.php');
if (block_use_stats_supports_feature('data/multidimensionnal')) {
    // Only in "pro" distributions.
    include_once($CFG->dirroot.'/blocks/use_stats/pro/lib.php');
}

class block_use_stats extends block_base {

    public function init() {
        $this->title = get_string('blockname', 'block_use_stats');
        $this->content_type = BLOCK_TYPE_TEXT;
    }

    /**
     * is the bloc configurable ?
     */
    public function has_config() {
        return true;
    }

    /**
     * do we have local config
     */
    public function instance_allow_config() {
        global $COURSE;

        return false;
    }

    /**
     * In which course format can we see and add the block.
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Produce content for the bloc
     */
    public function get_content() {
        global $USER, $CFG, $COURSE, $PAGE, $OUTPUT, $SESSION;

        $config = get_config('block_use_stats');

        $renderer = $PAGE->get_renderer('block_use_stats');

        if (!isset($this->config->studentscansee)) {
            if (!isset($this->config)) {
                $this->config = new StdClass;
            }
            $this->config->studentscansee = 1;
            $this->instance_config_save($this->config);
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        // Know which reader we are working with.
        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers(use_stats_get_reader());
        $reader = reset($readers);

        if (empty($reader)) {
            return $this->content; // No log reader found.
        }

        // Get context so we can check capabilities.
        $context = context_block::instance($this->instance->id);
        $systemcontext = context_system::instance();

        // Check global per role config.
        if (!has_capability('block/use_stats:view', $context)) {
            return $this->content;
        }

        // Check student access on instance.
        if (!$this->_seeother()) {
            if (empty($this->config->studentscansee)) {
                return $this->content;
            }
        }

        $id = optional_param('id', 0, PARAM_INT);

        list($from, $to) = $this->get_range();

        $capabilities = array('block/use_stats:seesitedetails',
                              'block/use_stats:seecoursedetails',
                              'block/use_stats:seegroupdetails');
        if (has_any_capability($capabilities, $context, $USER->id)) {
            $userid = optional_param('uid', $USER->id, PARAM_INT);
        } else {
            $userid = $USER->id;
        }

        $cache = cache::make('block_use_stats', 'aggregate');

        /*
         * We want to know the effective logrange against required period to
         * query the cache
         */
        $logrange = block_use_stats_get_log_range($userid, $from, $to);

        $cachekey = $userid.'_'.$logrange->min.'_'.$logrange->max;
        $userkeys = unserialize($cache->get('user'.$userid));

        $cachestate = '';
        if (!$aggregate = unserialize($cache->get($cachekey))) {
            if (debugging()) {
                $cachestate = 'missed';
            }
            if (($COURSE->id > SITEID) && !empty($config->displayothertime)) {
                $logs = use_stats_extract_logs($from, $to, $userid, $COURSE->id);
            } else {
                $logs = use_stats_extract_logs($from, $to, $userid);
            }
            if ($logs) {
                // Call without session storage for speed.
                $aggregate = use_stats_aggregate_logs($logs, 'module', 0, $from, $to, '', true);
            }
            $cache->set($cachekey, serialize($aggregate));

            // Update keys for this user.
            if (empty($userkeys)) {
                $userkeys = array();
            }
            if (!in_array($cachekey, $userkeys)) {
                $userkeys[] = $cachekey;
                $cache->set('user'.$userid, serialize($userkeys));
            }
        } else {
            if (debugging()) {
                $cachestate = 'hit';
            }
        }

        if ($aggregate) {

            $shadowclass = ($this->config->studentscansee) ? '' : 'usestats-shadow';

            $this->content->text .= "<!-- $from / $to -->";
            $this->content->text .= '<div class="usestats-message '.$cachestate.' '.$shadowclass.'">';

            $this->content->text .= $renderer->change_params_form($context, $id, $from, $to, $userid);

            $strbuffer = $renderer->per_course($aggregate, $fulltotal);

            $this->content->text .= get_string('youspent', 'block_use_stats');
            $this->content->text .= ' '.block_use_stats_format_time($fulltotal);
            if ($config->backtrackmode == 'sliding') {
                $this->content->text .= get_string('onthismoodlefrom', 'block_use_stats');
                $this->content->text .= userdate($from);
            } else {
                $this->content->text .= '&ensp;'.core_text::strtolower(get_string('fromrange', 'block_use_stats'));
                $this->content->text .= userdate($from);
                $this->content->text .= get_string('to', 'block_use_stats');
                $this->content->text .= userdate($to);
            }
            if (empty($this->config->hidecourselist)) {
                $this->content->text .= $strbuffer;
            }

            $this->content->text .= '</div>';

            $capabilities = array('block/use_stats:seeowndetails',
                                  'block/use_stats:seesitedetails',
                                  'block/use_stats:seecoursedetails',
                                  'block/use_stats:seegroupdetails');
            if (has_any_capability($capabilities, $context, $USER->id)) {
                $showdetailstr = get_string('showdetails', 'block_use_stats');
                $params = array('id' => $this->instance->id, 'userid' => $userid, 'course' => $COURSE->id);
                if (!empty($fromwhen)) {
                     $params['ts_from'] = $fromwhen;
                }
                $viewurl = new moodle_url('/blocks/use_stats/detail.php', $params);
                $this->content->text .= '<a href="'.$viewurl.'">'.$showdetailstr.'</a>';
            }

            if (has_capability('block/use_stats:export', $context)) {
                if (is_dir($CFG->dirroot.'/report/trainingsessions')) {
                    $button = $renderer->button_pdf($userid, $from, $to, $context);
                    $this->content->text .= '<div class="usestats-pdf">'.$button.'</div>';
                }
            }
        } else {
            $this->content->text = '<div class="message">';
            $this->content->text .= $OUTPUT->notification(get_string('noavailablelogs', 'block_use_stats'));
            $this->content->text .= '<br/>';
            $this->content->text .= $renderer->change_params_form($context, $id, $from, $to, $userid);
            $this->content->text .= '</div>';
        }

        return $this->content;
    }

    protected function get_range() {
        global $COURSE, $SESSION, $USER;

        $config = get_config('block_use_stats');
        $context = context_block::instance($this->instance->id);

        if ($config->backtrackmode == 'fixeddate') {

            // Choose default.
            if ($COURSE->id == SITEID) {
                $from = $USER->firstaccess;
            } else {
                $from = $COURSE->startdate;
            }
            $to = time();

            // Memorize in session for tracking changes.
            if (!isset($SESSION->usestatsfromwhen)) {
                $SESSION->usestatsfrom = $from;
            }

            // Memorize in session for tracking changes.
            if (!isset($SESSION->usestatsto)) {
                $SESSION->usestatsto = $to;
            }

            if ($config->backtracksource == 'studentchoice') {
                $htmlkey = 'ts_from'.$context->id;
                if ($tsfrom = optional_param($htmlkey, '', PARAM_TEXT)) {
                    $from = strtotime($tsfrom);
                }

                $htmlkey = 'ts_to'.$context->id;
                if ($tsto = optional_param($htmlkey, '', PARAM_TEXT)) {
                    // When coming from calendar, time is 00h00 of the given day.
                    $to = strtotime($tsto) + DAYSECS - 5; // Push up to 23:59:55.
                    $SESSION->usestatsto = $to;
                }
            }

            $SESSION->usestatstoenable = optional_param('usestatstoenable', 0, PARAM_BOOL);
            if (empty($SESSION->usestatstoenable)) {
                // Force to to track until latest moves.
                $to = time() + 120;
            }

        } else {
            $to = time();

            // This config only for slidingrange.
            if (empty($config->fromwhen)) {
                $config->fromwhen = 60;
                set_config('fromwhen', 60, 'block_use_stats');
            }

            // Memorize in session for tracking changes.
            if (!isset($SESSION->usestatsfromwhen)) {
                $SESSION->usestatsfromwhen = $config->fromwhen;
            }

            $fromwhen = $config->fromwhen;
            $daystocompilelogs = $fromwhen * DAYSECS;
            $now = time();
            if ($fromwhen = optional_param('ts_from', $SESSION->usestatsfromwhen, PARAM_INT)) {
                $daystocompilelogs = $fromwhen * DAYSECS;
                $to = $now;
            }
            $from = $now - $daystocompilelogs;
        }
        return array($from, $to);
    }

    /**
     * Used by the component associated task.
     */
    public static function cron_task() {
        global $DB;

        $config = get_config('block_use_stats');

        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers(use_stats_get_reader());
        $reader = reset($readers);

        if (empty($reader)) {
            mtrace('No log reader.');
            return false; // No log reader found.
        }

        if ($reader instanceof \logstore_standard\log\store) {
            $courseparm = 'courseid';
        } else if ($reader instanceof \logstore_legacy\log\store) {
            $courseparm = 'course';
        } else {
            mtrace('Unsupported log reader.');
            return;
        }

        if (!isset($config->lastcompiled)) {
            set_config('lastcompiled', '0', 'block_use_stats');
            $config->lastcompiled = 0;
        }

        mtrace("\n".'... Compiling gaps from : '.$config->lastcompiled);

        // Feed the table with log gaps.
        $previouslog = array();
        if ($reader instanceof \logstore_standard\log\store) {
            $sql = "
                SELECT
                    id,
                    courseid as course,
                    action,
                    timecreated as time,
                    target as module,
                    userid,
                    objectid as cmid
                FROM
                    {logstore_standard_log}
                WHERE
                    timecreated > ?
                ORDER BY
                    timecreated
            ";
            $rs = $DB->get_recordset_sql($sql, array($config->lastcompiled));
        } else if ($reader instanceof \logstore_legacy\log\store) {
            $params = array($config->lastcompiled);
            $fields = 'id,time,userid,course,cmid';
            $rs = $DB->get_recordset_select('log', " time > ? ", $params, 'time', $fields);
        } else {
            mtrace("this logstore is not supported");
            return;
        }

        if ($rs) {

            $r = 0;

            $starttime = time();

            while ($rs->valid()) {
                $log = $rs->current();
                $gaprec = new StdClass;
                $gaprec->logid = $log->id;
                $gaprec->userid = $log->userid;
                $gaprec->time = $log->time;
                $gaprec->course = $log->course;

                if (block_use_stats_supports_feature('data/multidimensionnal')) {
                    // This is an advanced feature only in pro distribution.
                    block_use_stats_get_cube_info($log, $gaprec, $config);
                }

                $gaprec->gap = 0;
                if (!$DB->record_exists('block_use_stats_log', array('logid' => $log->id))) {
                    $DB->insert_record('block_use_stats_log', $gaprec);
                }
                // Is there a last log found before actual compilation session ?
                if (!array_key_exists($log->userid, $previouslog)) {
                    if ($reader instanceof \logstore_standard\log\store) {
                        $select = ' timecreated < ? ';
                        $params = array($config->lastcompiled);
                        $maxlasttime = $DB->get_field_select('logstore_standard_log', 'MAX(timecreated)', $select, $params);
                        $params = array('timecreated' => $maxlasttime);
                        $lastlog = $DB->get_records('logstore_standard_log', $params, 'id DESC', '*', 0, 1);
                    } else if ($reader instanceof \logstore_legacy\log\store) {
                        $maxlasttime = $DB->get_field_select('log', 'MAX(time)', ' time < ? ', array($config->lastcompiled));
                        $lastlog = $DB->get_records('log', array('time' => $maxlasttime), 'id DESC', '*', 0, 1);
                    }
                    $lastlogs = array_values($lastlog);
                    $previouslog[$log->userid] = array_shift($lastlogs);
                }
                $value = $log->time - (0 + @$previouslog[$log->userid]->time);
                $DB->set_field('block_use_stats_log', 'gap', $value, array('logid' => @$previouslog[$log->userid]->id));
                $previouslog[$log->userid] = $log;
                $lasttime = $log->time;
                $r++;

                if ($r % 10 == 0) {
                    echo '.';
                    $processtime = time();
                    if (($processtime > $starttime + 60 * 15) || ($r > 100000)) {
                        break; // Do not process more than 15 minutes.
                    }
                }
                if ($r % 1000 == 0) {
                    // Store intermediary track points.
                    if (!empty($lasttime)) {
                        set_config('lastcompiled', $lasttime, 'block_use_stats');
                    }
                }
                $rs->next();
            }
            $rs->close();

            mtrace("\n... $r logs gapped");
            // Register last log time for cron further updates.
            if (!empty($lasttime)) {
                set_config('lastcompiled', $lasttime, 'block_use_stats');
            }
        }
    }

    /**
     * Purges selectively caches of online users every x minutes
     */
    public static function cache_ttl_task() {
        global $DB;

        $timeminusthirty = time() - 30 * MINSECS;

        $cache = cache::make('block_use_stats', 'aggregate');

        $sql = "
            SELECT
                id,
                id
            FROM
                {user}
            WHERE
                lastaccess < ?
        ";

        $onlineusers = $DB->get_records_sql($sql, array($timeminusthirty));

        foreach (array_keys($onlineusers) as $userid) {
            $userkeys = unserialize($cache->get('user'.$userid));
            if (!empty($userkeys)) {
                foreach ($userkeys as $cachekey) {
                    $cache->delete($cachekey);
                }
            }
        }
    }

    /**
     * to cleanup some logs to delete.
     */
    public static function cleanup_task() {
        global $DB;

        $logmanager = get_log_manager();
        $readers = $logmanager->get_readers('\core\log\sql_select_reader');
        $reader = reset($readers);

        if (empty($reader)) {
            mtrace('No log reader.');
            return false; // No log reader found.
        }

        if ($reader instanceof \logstore_standard\log\store) {
            $sql = "
                DELETE FROM
                    {block_use_stats_log}
                WHERE
                    logid NOT IN(
                        SELECT
                            id
                        FROM
                            {log})
            ";
        } else if ($reader instanceof \logstore_legacy\log\store) {
            $sql = "
                DELETE FROM
                    {block_use_stats_log}
                WHERE
                    logid NOT IN(
                        SELECT
                            id
                        FROM
                            {logstore_standard_log})
            ";
        } else {
            mtrace('Unsupported log reader.');
            return;
        }

        $DB->execute($sql);
    }

    public static function prepare_coursetable(&$aggregate, &$fulltotal, &$fullevents, $order = 'name') {
        global $DB, $COURSE;

        $config = get_config('block_use_stats');

        $courseelapsed = array();
        $courseshort = array();
        $coursefull = array();
        $courseevents = array();

        $fulltotal = 0;
        $fullevents = 0;

        // Prepare per course table.
        if (!empty($aggregate['coursetotal'])) {
            foreach ($aggregate['coursetotal'] as $courseid => $coursestats) {

                if ($courseid) {
                    $fields = 'id,shortname,idnumber,fullname';
                    $course = $DB->get_record('course', array('id' => $courseid), $fields);
                } else {
                    $course = new StdClass();
                    $course->shortname = get_string('othershort', 'block_use_stats');
                    $course->fullname = get_string('other', 'block_use_stats');
                    $course->idnumber = '';
                }

                if (!$config->displayothertime) {
                    if (!$courseid || (($COURSE->id > SITEID) && ($courseid == 1))) {
                        continue;
                    }
                }

                if ($course) {
                    // Count total even if not shown (D NOT loose time).
                    if (@$config->displayactivitytimeonly == DISPLAY_FULL_COURSE) {
                        $reftime = 0 + @$aggregate['coursetotal'][$courseid]->elapsed;
                        $refevents = 0 + @$aggregate['coursetotal'][$courseid]->events;
                    } else {
                        $reftime = 0 + @$aggregate['activities'][$courseid]->elapsed;
                        $refevents = 0 + @$aggregate['coursetotal'][$courseid]->events;
                    }
                    $fulltotal += $reftime;
                    $fullevents += $refevents;

                    if (!empty($config->filterdisplayunder)) {
                        if ($reftime < $config->filterdisplayunder) {
                            continue;
                        }
                    }

                    $courseshort[$courseid] = $course->shortname;
                    $coursefull[$courseid] = $course->fullname;
                    $courseelapsed[$courseid] = $reftime;
                    $courseevents[$courseid] = $refevents;
                }
            }
        }

        if ($order == 'name') {
            $displaycourses = $courseshort;
            asort($displaycourses);
        } else {
            $displaycourses = $courseelapsed;
            asort($displaycourses);
            $displaycourses = array_reverse($displaycourses, true);
        }

        return array($displaycourses, $courseshort, $coursefull, $courseelapsed, $courseevents);
    }

    private function _seeother() {
        $context = context_block::instance($this->instance->id);
        $capabilities = array('block/use_stats:seesitedetails',
                              'block/use_stats:seecoursedetails',
                              'block/use_stats:seegroupdetails');
        return has_any_capability($capabilities, $context);
    }

    public function get_required_javascript() {
        global $CFG, $PAGE;

        $config = get_config('block_use_stats');

        parent::get_required_javascript();

        $PAGE->requires->jquery();

        $PAGE->requires->js('/blocks/use_stats/js/usestats.js', true);
        $PAGE->requires->js('/blocks/use_stats/js/dhtmlxCalendar/codebase/dhtmlxcalendar.js', true);
        $PAGE->requires->js('/blocks/use_stats/js/dhtmlxCalendar/codebase/dhtmlxcalendar_locales.js', true);
        $PAGE->requires->css('/blocks/use_stats/js/dhtmlxCalendar/codebase/dhtmlxcalendar.css', true);
        $PAGE->requires->css('/blocks/use_stats/js/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_'.$config->calendarskin.'.css', true);
    }
}
