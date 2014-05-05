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
 * Table log for displaying logs.
 *
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Table log class for displaying logs.
 *
 * @since      Moodle 2.7
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_loglive_table_log extends table_sql {

    /** @var array list of user fullnames shown in report */
    protected $userfullnames = array();

    /** @var array list of course short names shown in report */
    protected $courseshortnames = array();

    /** @var array list of context name shown in report */
    protected $contextname = array();

    /** @var stdClass filters parameters */
    protected $filterparams;

    /**
     * Sets up the table_log parameters.
     *
     * @param string $uniqueid unique id of form.
     * @param stdClass $filterparams (optional) filter params.
     *     - int courseid: id of course
     *     - int userid: user id
     *     - int|string modid: Module id or "site_errors" to view site errors
     *     - int groupid: Group id
     *     - \core\log\sql_select_reader logreader: reader from which data will be fetched.
     *     - int edulevel: educational level.
     *     - string action: view action
     *     - int date: Date from which logs to be viewed.
     */
    public function __construct($uniqueid, $filterparams = null) {
        parent::__construct($uniqueid);

        $this->set_attribute('class', 'reportloglive generaltable generalbox');
        $this->set_attribute('aria-live', 'polite');
        $this->filterparams = $filterparams;
        // Add course column if logs are displayed for site.
        $cols = array();
        $headers = array();
        if (empty($filterparams->courseid)) {
            $cols = array('course');
            $headers = array(get_string('course'));
        }

        $this->define_columns(array_merge($cols, array('time', 'fullnameuser', 'relatedfullnameuser', 'context', 'component',
                'eventname', 'description', 'origin', 'ip')));
        $this->define_headers(array_merge($headers, array(
                get_string('time'),
                get_string('fullnameuser'),
                get_string('eventrelatedfullnameuser', 'report_loglive'),
                get_string('eventcontext', 'report_loglive'),
                get_string('eventcomponent', 'report_loglive'),
                get_string('eventname'),
                get_string('description'),
                get_string('eventorigin', 'report_loglive'),
                get_string('ip_address')
                )
            ));
        $this->collapsible(false);
        $this->sortable(false);
        $this->pageable(true);
        $this->is_downloadable(false);
    }

    /**
     * Generate the course column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the course column.
     */
    public function col_course($event) {
        if (empty($event->courseid) || empty($this->courseshortnames[$event->courseid])) {
            return '-';
        } else {
            return $this->courseshortnames[$event->courseid];
        }
    }

    /**
     * Generate the time column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the time column
     */
    public function col_time($event) {
        $recenttimestr = get_string('strftimerecent', 'core_langconfig');
        return userdate($event->timecreated, $recenttimestr);
    }

    /**
     * Generate the username column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the username column
     */
    public function col_fullnameuser($event) {
        // Get extra event data for origin and realuserid.
        $logextra = $event->get_logextra();

        // Add username who did the action.
        if (!empty($logextra['realuserid'])) {
            $a = new stdClass();
            $params = array('id' => $logextra['realuserid']);
            if ($event->courseid) {
                $params['course'] = $event->courseid;
            }
            $a->realusername = html_writer::link(new moodle_url("/user/view.php", $params),
                $this->userfullnames[$logextra['realuserid']]);
            $params['id'] = $event->userid;
            $a->asusername = html_writer::link(new moodle_url("/user/view.php", $params),
                $this->userfullnames[$event->userid]);
            $username = get_string('eventloggedas', 'report_loglive', $a);
        } else if (!empty($event->userid) && !empty($this->userfullnames[$event->userid])) {
            $params = array('id' => $event->userid);
            if ($event->courseid) {
                $params['course'] = $event->courseid;
            }
            $username = html_writer::link(new moodle_url("/user/view.php", $params), $this->userfullnames[$event->userid]);
        } else {
            $username = '-';
        }
        return $username;
    }

    /**
     * Generate the related username column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the related username column
     */
    public function col_relatedfullnameuser($event) {
        // Add affected user.
        if (!empty($event->relateduserid) && isset($this->userfullnames[$event->relateduserid])) {
            $params = array('id' => $event->relateduserid);
            if ($event->courseid) {
                $params['course'] = $event->courseid;
            }
            return html_writer::link(new moodle_url("/user/view.php", $params), $this->userfullnames[$event->relateduserid]);
        } else {
            return '-';
        }
    }

    /**
     * Generate the context column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the context column
     */
    public function col_context($event) {
        // Add context name.
        if ($event->contextid) {
            // If context name was fetched before then return, else get one.
            if (isset($this->contextname[$event->contextid])) {
                return $this->contextname[$event->contextid];
            } else {
                $context = context::instance_by_id($event->contextid, IGNORE_MISSING);
                if ($context) {
                    $contextname = $context->get_context_name(true);
                    if ($url = $context->get_url()) {
                        $contextname = html_writer::link($url, $contextname);
                    }
                } else {
                    $contextname = get_string('other');
                }
            }
        } else {
            $contextname = get_string('other');
        }

        $this->contextname[$event->contextid] = $contextname;
        return $contextname;
    }

    /**
     * Generate the component column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the component column
     */
    public function col_component($event) {
        // Component.
        $componentname = $event->component;
        if (($event->component === 'core') || ($event->component === 'legacy')) {
            return  get_string('coresystem');
        } else if (get_string_manager()->string_exists('pluginname', $event->component)) {
            return get_string('pluginname', $event->component);
        } else {
            return $componentname;
        }
    }

    /**
     * Generate the event name column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the event name column
     */
    public function col_eventname($event) {
        // Event name.
        if ($this->filterparams->logreader instanceof logstore_legacy\log\store) {
            // Hack for support of logstore_legacy.
            $eventname = $event->eventname;
        } else {
            $eventname = $event->get_name();
        }
        if ($url = $event->get_url()) {
            $eventname = $this->action_link($url, $eventname, 'action');
        }
        return $eventname;
    }

    /**
     * Generate the description column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the description column
     */
    public function col_description($event) {
        // Description.
        return $event->get_description();
    }

    /**
     * Generate the origin column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the origin column
     */
    public function col_origin($event) {
        // Get extra event data for origin and realuserid.
        $logextra = $event->get_logextra();

        // Add event origin, normally IP/cron.
        return $logextra['origin'];
    }

    /**
     * Generate the ip column.
     *
     * @param stdClass $event event data.
     * @return string HTML for the ip column
     */
    public function col_ip($event) {
        // Get extra event data for origin and realuserid.
        $logextra = $event->get_logextra();

        $url = new moodle_url("/iplookup/index.php?ip={$logextra['ip']}&user=$event->userid");
        return $this->action_link($url, $logextra['ip'], 'ip');
    }

    /**
     * Method to create a link with popup action.
     *
     * @param moodle_url $url The url to open.
     * @param string $text Anchor text for the link.
     * @param string $name Name of the popup window.
     *
     * @return string html to use.
     */
    protected function action_link(moodle_url $url, $text, $name = 'popup') {
        global $OUTPUT;
        $link = new action_link($url, $text, new popup_action('click', $url, $name, array('height' => 440, 'width' => 700)));
        return $OUTPUT->render($link);
    }

    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {

        $joins = array();
        $params = array();

        // Set up filtering.
        if (!empty($this->filterparams->courseid)) {
            $joins[] = "courseid = :courseid";
            $params['courseid'] = $this->filterparams->courseid;
        }

        if (!empty($this->filterparams->date)) {
            $joins[] = "timecreated > :date";
            $params['date'] = $this->filterparams->date;
        }

        if (isset($this->filterparams->anonymous)) {
            $joins[] = "anonymous = :anon";
            $params['anon'] = $this->filterparams->anonymous;
        }

        $selector = implode(' AND ', $joins);

        $total = $this->filterparams->logreader->get_events_select_count($selector, $params);
        $this->pagesize($pagesize, $total);
        $this->rawdata = $this->filterparams->logreader->get_events_select($selector, $params, $this->filterparams->orderby,
                $this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }

        // Update list of users and courses list which will be displayed on log page.
        $this->update_users_and_courses_used();
    }

    /**
     * Helper function to create list of course shortname and user fullname shown in log report.
     * This will update $this->userfullnames and $this->courseshortnames array with userfullname and courseshortname (with link),
     * which will be used to render logs in table.
     */
    public function update_users_and_courses_used() {
        global $SITE, $DB;

        $this->userfullnames = array();
        $this->courseshortnames = array($SITE->id => $SITE->shortname);
        $userids = array();
        $courseids = array();
        // For each event cache full username and course.
        // Get list of userids and courseids which will be shown in log report.
        foreach ($this->rawdata as $event) {
            $logextra = $event->get_logextra();
            if (!empty($event->userid) && !in_array($event->userid, $userids)) {
                $userids[] = $event->userid;
            }
            if (!empty($logextra['realuserid']) && !in_array($logextra['realuserid'], $userids)) {
                $userids[] = $logextra['realuserid'];
            }
            if (!empty($event->relateduserid) && !in_array($event->relateduserid, $userids)) {
                $userids[] = $event->relateduserid;
            }

            if (!empty($event->courseid) && ($event->courseid != $SITE->id) && !in_array($event->courseid, $courseids)) {
                $courseids[] = $event->courseid;
            }
        }

        // Get user fullname and put that in return list.
        if (!empty($userids)) {
            list($usql, $uparams) = $DB->get_in_or_equal($userids);
            $users = $DB->get_records_sql("SELECT id," . get_all_user_name_fields(true) . " FROM {user} WHERE id " . $usql,
                    $uparams);
            foreach ($users as $userid => $user) {
                $this->userfullnames[$userid] = fullname($user);
            }
        }

        // Get course shortname and put that in return list.
        if (!empty($courseids)) { // If all logs don't belog to site level then get course info.
            list($coursesql, $courseparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
            $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
            $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
            $courseparams['contextlevel'] = CONTEXT_COURSE;
            $sql = "SELECT c.id,c.shortname $ccselect FROM {course} c
                   $ccjoin
                     WHERE c.id " . $coursesql;

            $courses = $DB->get_records_sql($sql, $courseparams);
            foreach ($courses as $courseid => $course) {
                $url = new moodle_url("/course/view.php", array('id' => $courseid));
                context_helper::preload_from_record($course);
                $context = context_course::instance($courseid, IGNORE_MISSING);
                // Method format_string() takes care of missing contexts.
                $this->courseshortnames[$courseid] = html_writer::link($url, format_string($course->shortname, true,
                        array('context' => $context)));
            }
        }
    }
}
