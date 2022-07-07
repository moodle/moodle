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
 * Legacy log reader.
 * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
 * @todo  MDL-52805 This is to be removed in Moodle 3.10
 *
 * @package    logstore_legacy
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_legacy\log;

defined('MOODLE_INTERNAL') || die();

class store implements \tool_log\log\store, \core\log\sql_reader {
    use \tool_log\helper\store,
        \tool_log\helper\reader;

    /**
     * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
     * @todo  MDL-52805 This is to be removed in Moodle 3.10
     *
     * @param \tool_log\log\manager $manager
     */
    public function __construct(\tool_log\log\manager $manager) {
        $this->helper_setup($manager);
    }

    /** @var array list of db fields which needs to be replaced for legacy log query */
    protected static $standardtolegacyfields = array(
        'timecreated'       => 'time',
        'courseid'          => 'course',
        'contextinstanceid' => 'cmid',
        'origin'            => 'ip',
        'anonymous'         => 0,
    );

    /** @var string Regex to replace the crud params */
    const CRUD_REGEX = "/(crud).*?(<>|=|!=).*?'(.*?)'/s";

    /**
     * This method contains mapping required for Moodle core to make legacy store compatible with other sql_reader based
     * queries.
     *
     * @param string $selectwhere Select statment
     * @param array $params params for the sql
     * @param string $sort sort fields
     *
     * @return array returns an array containing the sql predicate, an array of params and sorting parameter.
     */
    protected static function replace_sql_legacy($selectwhere, array $params, $sort = '') {
        // Following mapping is done to make can_delete_course() compatible with legacy store.
        if ($selectwhere == "userid = :userid AND courseid = :courseid AND eventname = :eventname AND timecreated > :since" and
                empty($sort)) {
            $replace = "module = 'course' AND action = 'new' AND userid = :userid AND url = :url AND time > :since";
            $params += array('url' => "view.php?id={$params['courseid']}");
            return array($replace, $params, $sort);
        }

        // Replace db field names to make it compatible with legacy log.
        foreach (self::$standardtolegacyfields as $from => $to) {
            $selectwhere = str_replace($from, $to, $selectwhere);
            if (!empty($sort)) {
                $sort = str_replace($from, $to, $sort);
            }
            if (isset($params[$from])) {
                $params[$to] = $params[$from];
                unset($params[$from]);
            }
        }

        // Replace crud fields.
        $selectwhere = preg_replace_callback("/(crud).*?(<>|=|!=).*?'(.*?)'/s", 'self::replace_crud', $selectwhere);

        return array($selectwhere, $params, $sort);
    }

    /**
     * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
     * @todo MDL-52805 This will be removed in Moodle 3.10
     *
     * @param  string $selectwhere
     * @param  array  $params
     * @param  string $sort
     * @param  int    $limitfrom
     * @param  int    $limitnum
     * @return array
     */
    public function get_events_select($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        global $DB;

        $sort = self::tweak_sort_by_id($sort);

        // Replace the query with hardcoded mappings required for core.
        list($selectwhere, $params, $sort) = self::replace_sql_legacy($selectwhere, $params, $sort);

        $records = array();

        try {
            // A custom report + on the fly SQL rewriting = a possible exception.
            $records = $DB->get_recordset_select('log', $selectwhere, $params, $sort, '*', $limitfrom, $limitnum);
        } catch (\moodle_exception $ex) {
            debugging("error converting legacy event data " . $ex->getMessage() . $ex->debuginfo, DEBUG_DEVELOPER);
            return array();
        }

        $events = array();

        foreach ($records as $data) {
            $events[$data->id] = $this->get_log_event($data);
        }

        $records->close();

        return $events;
    }

    /**
     * Fetch records using given criteria returning a Traversable object.
     * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
     * @todo MDL-52805 This will be removed in Moodle 3.10
     *
     * Note that the traversable object contains a moodle_recordset, so
     * remember that is important that you call close() once you finish
     * using it.
     *
     * @param string $selectwhere
     * @param array $params
     * @param string $sort
     * @param int $limitfrom
     * @param int $limitnum
     * @return \Traversable|\core\event\base[]
     */
    public function get_events_select_iterator($selectwhere, array $params, $sort, $limitfrom, $limitnum) {
        global $DB;

        $sort = self::tweak_sort_by_id($sort);

        // Replace the query with hardcoded mappings required for core.
        list($selectwhere, $params, $sort) = self::replace_sql_legacy($selectwhere, $params, $sort);

        try {
            $recordset = $DB->get_recordset_select('log', $selectwhere, $params, $sort, '*', $limitfrom, $limitnum);
        } catch (\moodle_exception $ex) {
            debugging("error converting legacy event data " . $ex->getMessage() . $ex->debuginfo, DEBUG_DEVELOPER);
            return new \EmptyIterator;
        }

        return new \core\dml\recordset_walk($recordset, array($this, 'get_log_event'));
    }

    /**
     * Returns an event from the log data.
     * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
     * @todo MDL-52805 This will be removed in Moodle 3.10
     *
     * @param stdClass $data Log data
     * @return \core\event\base
     */
    public function get_log_event($data) {
        return \logstore_legacy\event\legacy_logged::restore_legacy($data);
    }

    /**
     * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
     * @todo MDL-52805 This will be removed in Moodle 3.10
     *
     * @param  string $selectwhere
     * @param  array  $params
     * @return int
     */
    public function get_events_select_count($selectwhere, array $params) {
        global $DB;

        // Replace the query with hardcoded mappings required for core.
        list($selectwhere, $params) = self::replace_sql_legacy($selectwhere, $params);

        try {
            return $DB->count_records_select('log', $selectwhere, $params);
        } catch (\moodle_exception $ex) {
            debugging("error converting legacy event data " . $ex->getMessage() . $ex->debuginfo, DEBUG_DEVELOPER);
            return 0;
        }
    }

    /**
     * Are the new events appearing in the reader?
     * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
     * @todo MDL-52805 This will be removed in Moodle 3.10
     *
     * @return bool true means new log events are being added, false means no new data will be added
     */
    public function is_logging() {
        return (bool)$this->get_config('loglegacy', true);
    }

    /**
     * @deprecated since Moodle 3.6 MDL-52953 - Please use supported log stores such as "standard" or "external" instead.
     * @todo MDL-52805 This will be removed in Moodle 3.10
     */
    public function dispose() {
    }

    /**
     * Legacy add_to_log() code.
     * @deprecated since Moodle 3.1 MDL-45104 - Please use supported log stores such as "standard" or "external" instead.
     * @todo MDL-52805 This will be removed in Moodle 3.3
     *
     * @param    int $courseid The course id
     * @param    string $module The module name  e.g. forum, journal, resource, course, user etc
     * @param    string $action 'view', 'update', 'add' or 'delete', possibly followed by another word to clarify.
     * @param    string $url The file and parameters used to see the results of the action
     * @param    string $info Additional description information
     * @param    int $cm The course_module->id if there is one
     * @param    int|\stdClass $user If log regards $user other than $USER
     * @param    string $ip Override the IP, should only be used for restore.
     * @param    int $time Override the log time, should only be used for restore.
     */
    public function legacy_add_to_log($courseid, $module, $action, $url, $info, $cm, $user, $ip = null, $time = null) {
        // Note that this function intentionally does not follow the normal Moodle DB access idioms.
        // This is for a good reason: it is the most frequently used DB update function,
        // so it has been optimised for speed.
        global $DB, $CFG, $USER;
        if (!$this->is_logging()) {
            return;
        }

        if ($cm === '' || is_null($cm)) { // Postgres won't translate empty string to its default.
            $cm = 0;
        }

        if ($user) {
            $userid = $user;
        } else {
            if (\core\session\manager::is_loggedinas()) { // Don't log.
                return;
            }
            $userid = empty($USER->id) ? '0' : $USER->id;
        }

        if (isset($CFG->logguests) and !$CFG->logguests) {
            if (!$userid or isguestuser($userid)) {
                return;
            }
        }

        $remoteaddr = (is_null($ip)) ? getremoteaddr() : $ip;

        $timenow = (is_null($time)) ? time() : $time;
        if (!empty($url)) { // Could break doing html_entity_decode on an empty var.
            $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');
        } else {
            $url = '';
        }

        // Restrict length of log lines to the space actually available in the
        // database so that it doesn't cause a DB error. Log a warning so that
        // developers can avoid doing things which are likely to cause this on a
        // routine basis.
        if (\core_text::strlen($action) > 40) {
            $action = \core_text::substr($action, 0, 37) . '...';
            debugging('Warning: logged very long action', DEBUG_DEVELOPER);
        }

        if (!empty($info) && \core_text::strlen($info) > 255) {
            $info = \core_text::substr($info, 0, 252) . '...';
            debugging('Warning: logged very long info', DEBUG_DEVELOPER);
        }

        // If the 100 field size is changed, also need to alter print_log in course/lib.php.
        if (!empty($url) && \core_text::strlen($url) > 100) {
            $url = \core_text::substr($url, 0, 97) . '...';
            debugging('Warning: logged very long URL', DEBUG_DEVELOPER);
        }

        if (defined('MDL_PERFDB')) {
            global $PERF;
            $PERF->logwrites++;
        };

        $log = array('time' => $timenow, 'userid' => $userid, 'course' => $courseid, 'ip' => $remoteaddr,
                     'module' => $module, 'cmid' => $cm, 'action' => $action, 'url' => $url, 'info' => $info);

        try {
            $DB->insert_record_raw('log', $log, false);
        } catch (\dml_exception $e) {
            debugging('Error: Could not insert a new entry to the Moodle log. ' . $e->errorcode, DEBUG_ALL);

            // MDL-11893, alert $CFG->supportemail if insert into log failed.
            if ($CFG->supportemail and empty($CFG->noemailever)) {
                // Function email_to_user is not usable because email_to_user tries to write to the logs table,
                // and this will get caught in an infinite loop, if disk is full.
                $site = get_site();
                $subject = 'Insert into log failed at your moodle site ' . $site->fullname;
                $message = "Insert into log table failed at " . date('l dS \of F Y h:i:s A') .
                    ".\n It is possible that your disk is full.\n\n";
                $message .= "The failed query parameters are:\n\n" . var_export($log, true);

                $lasttime = get_config('admin', 'lastloginserterrormail');
                if (empty($lasttime) || time() - $lasttime > 60 * 60 * 24) { // Limit to 1 email per day.
                    // Using email directly rather than messaging as they may not be able to log in to access a message.
                    mail($CFG->supportemail, $subject, $message);
                    set_config('lastloginserterrormail', time(), 'admin');
                }
            }
        }
    }

    /**
     * Generate a replace string for crud related sql conditions. This function is called as callback to preg_replace_callback()
     * on the actual sql.
     *
     * @param array $match matched string for the passed pattern
     *
     * @return string The sql string to use instead of original
     */
    protected static function replace_crud($match) {
        $return = '';
        unset($match[0]); // The first entry is the whole string.
        foreach ($match as $m) {
            // We hard code LIKE here because we are not worried about case sensitivity and want this to be fast.
            switch ($m) {
                case 'crud' :
                    $replace = 'action';
                    break;
                case 'c' :
                    switch ($match[2]) {
                        case '=' :
                            $replace = " LIKE '%add%'";
                            break;
                        case '!=' :
                        case '<>' :
                            $replace = " NOT LIKE '%add%'";
                            break;
                        default:
                            $replace = '';
                    }
                    break;
                case 'r' :
                    switch ($match[2]) {
                        case '=' :
                            $replace = " LIKE '%view%' OR action LIKE '%report%'";
                            break;
                        case '!=' :
                        case '<>' :
                            $replace = " NOT LIKE '%view%' AND action NOT LIKE '%report%'";
                            break;
                        default:
                            $replace = '';
                    }
                    break;
                case 'u' :
                    switch ($match[2]) {
                        case '=' :
                            $replace = " LIKE '%update%'";
                            break;
                        case '!=' :
                        case '<>' :
                            $replace = " NOT LIKE '%update%'";
                            break;
                        default:
                            $replace = '';
                    }
                    break;
                case 'd' :
                    switch ($match[2]) {
                        case '=' :
                            $replace = " LIKE '%delete%'";
                            break;
                        case '!=' :
                        case '<>' :
                            $replace = " NOT LIKE '%delete%'";
                            break;
                        default:
                            $replace = '';
                    }
                    break;
                default :
                    $replace = '';
            }
            $return .= $replace;
        }
        return $return;
    }
}
