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
 * Session manager class.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * Session manager, this is the public Moodle API for sessions.
 *
 * Following PHP functions MUST NOT be used directly:
 * - session_start() - not necessary, lib/setup.php starts session automatically,
 *   use define('NO_MOODLE_COOKIE', true) if session not necessary.
 * - session_write_close() - use \core\session\manager::write_close() instead.
 * - session_destroy() - use require_logout() instead.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** @var handler $handler active session handler instance */
    protected static $handler;

    /** @var bool $sessionactive Is the session active? */
    protected static $sessionactive = null;

    /**
     * Start user session.
     *
     * Note: This is intended to be called only from lib/setup.php!
     */
    public static function start() {
        global $CFG, $DB;

        if (isset(self::$sessionactive)) {
            debugging('Session was already started!', DEBUG_DEVELOPER);
            return;
        }

        self::load_handler();

        // Init the session handler only if everything initialised properly in lib/setup.php file
        // and the session is actually required.
        if (empty($DB) or empty($CFG->version) or !defined('NO_MOODLE_COOKIES') or NO_MOODLE_COOKIES or CLI_SCRIPT) {
            self::$sessionactive = false;
            self::init_empty_session();
            return;
        }

        try {
            self::$handler->init();
            self::prepare_cookies();
            $isnewsession = empty($_COOKIE[session_name()]);

            if (!self::$handler->start()) {
                // Could not successfully start/recover session.
                throw new \core\session\exception(get_string('servererror'));
            }

            self::initialise_user_session($isnewsession);
            self::check_security();

            // Link global $USER and $SESSION,
            // this is tricky because PHP does not allow references to references
            // and global keyword uses internally once reference to the $GLOBALS array.
            // The solution is to use the $GLOBALS['USER'] and $GLOBALS['$SESSION']
            // as the main storage of data and put references to $_SESSION.
            $GLOBALS['USER'] = $_SESSION['USER'];
            $_SESSION['USER'] =& $GLOBALS['USER'];
            $GLOBALS['SESSION'] = $_SESSION['SESSION'];
            $_SESSION['SESSION'] =& $GLOBALS['SESSION'];

        } catch (\Exception $ex) {
            self::init_empty_session();
            self::$sessionactive = false;
            throw $ex;
        }

        self::$sessionactive = true;
    }

    /**
     * Returns current page performance info.
     *
     * @return array perf info
     */
    public static function get_performance_info() {
        if (!session_id()) {
            return array();
        }

        self::load_handler();
        $size = display_size(strlen(session_encode()));
        $handler = get_class(self::$handler);

        $info = array();
        $info['size'] = $size;
        $info['html'] = "<span class=\"sessionsize\">Session ($handler): $size</span> ";
        $info['txt'] = "Session ($handler): $size ";

        return $info;
    }

    /**
     * Create handler instance.
     */
    protected static function load_handler() {
        global $CFG, $DB;

        if (self::$handler) {
            return;
        }

        // Find out which handler to use.
        if (PHPUNIT_TEST) {
            $class = '\core\session\file';

        } else if (!empty($CFG->session_handler_class)) {
            $class = $CFG->session_handler_class;

        } else if (!empty($CFG->dbsessions) and $DB->session_lock_supported()) {
            $class = '\core\session\database';

        } else {
            $class = '\core\session\file';
        }
        self::$handler = new $class();
    }

    /**
     * Empty current session, fill it with not-logged-in user info.
     *
     * This is intended for installation scripts, unit tests and other
     * special areas. Do NOT use for logout and session termination
     * in normal requests!
     */
    public static function init_empty_session() {
        global $CFG;

        $GLOBALS['SESSION'] = new \stdClass();

        $GLOBALS['USER'] = new \stdClass();
        $GLOBALS['USER']->id = 0;
        if (isset($CFG->mnet_localhost_id)) {
            $GLOBALS['USER']->mnethostid = $CFG->mnet_localhost_id;
        } else {
            // Not installed yet, the future host id will be most probably 1.
            $GLOBALS['USER']->mnethostid = 1;
        }

        // Link global $USER and $SESSION.
        $_SESSION = array();
        $_SESSION['USER'] =& $GLOBALS['USER'];
        $_SESSION['SESSION'] =& $GLOBALS['SESSION'];
    }

    /**
     * Make sure all cookie and session related stuff is configured properly before session start.
     */
    protected static function prepare_cookies() {
        global $CFG;

        if (!isset($CFG->cookiesecure) or (!is_https() and empty($CFG->sslproxy))) {
            $CFG->cookiesecure = 0;
        }

        if (!isset($CFG->cookiehttponly)) {
            $CFG->cookiehttponly = 0;
        }

        // Set sessioncookie variable if it isn't already.
        if (!isset($CFG->sessioncookie)) {
            $CFG->sessioncookie = '';
        }
        $sessionname = 'MoodleSession'.$CFG->sessioncookie;

        // Make sure cookie domain makes sense for this wwwroot.
        if (!isset($CFG->sessioncookiedomain)) {
            $CFG->sessioncookiedomain = '';
        } else if ($CFG->sessioncookiedomain !== '') {
            $host = parse_url($CFG->wwwroot, PHP_URL_HOST);
            if ($CFG->sessioncookiedomain !== $host) {
                if (substr($CFG->sessioncookiedomain, 0, 1) === '.') {
                    if (!preg_match('|^.*'.preg_quote($CFG->sessioncookiedomain, '|').'$|', $host)) {
                        // Invalid domain - it must be end part of host.
                        $CFG->sessioncookiedomain = '';
                    }
                } else {
                    if (!preg_match('|^.*\.'.preg_quote($CFG->sessioncookiedomain, '|').'$|', $host)) {
                        // Invalid domain - it must be end part of host.
                        $CFG->sessioncookiedomain = '';
                    }
                }
            }
        }

        // Make sure the cookiepath is valid for this wwwroot or autodetect if not specified.
        if (!isset($CFG->sessioncookiepath)) {
            $CFG->sessioncookiepath = '';
        }
        if ($CFG->sessioncookiepath !== '/') {
            $path = parse_url($CFG->wwwroot, PHP_URL_PATH).'/';
            if ($CFG->sessioncookiepath === '') {
                $CFG->sessioncookiepath = $path;
            } else {
                if (strpos($path, $CFG->sessioncookiepath) !== 0 or substr($CFG->sessioncookiepath, -1) !== '/') {
                    $CFG->sessioncookiepath = $path;
                }
            }
        }

        // Discard session ID from POST, GET and globals to tighten security,
        // this is session fixation prevention.
        unset($GLOBALS[$sessionname]);
        unset($_GET[$sessionname]);
        unset($_POST[$sessionname]);
        unset($_REQUEST[$sessionname]);

        // Compatibility hack for non-browser access to our web interface.
        if (!empty($_COOKIE[$sessionname]) && $_COOKIE[$sessionname] == "deleted") {
            unset($_COOKIE[$sessionname]);
        }

        // Set configuration.
        session_name($sessionname);
        session_set_cookie_params(0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.hash_function', '0');        // For now MD5 - we do not have room for sha-1 in sessions table.
        ini_set('session.use_strict_mode', '0');      // We have custom protection in session init.
        ini_set('session.serialize_handler', 'php');  // We can move to 'php_serialize' after we require PHP 5.5.4 form Moodle.

        // Moodle does normal session timeouts, this is for leftovers only.
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1000);
        ini_set('session.gc_maxlifetime', 60*60*24*4);
    }

    /**
     * Initialise $_SESSION, handles google access
     * and sets up not-logged-in user properly.
     *
     * WARNING: $USER and $SESSION are set up later, do not use them yet!
     *
     * @param bool $newsid is this a new session in first http request?
     */
    protected static function initialise_user_session($newsid) {
        global $CFG, $DB;

        $sid = session_id();
        if (!$sid) {
            // No session, very weird.
            error_log('Missing session ID, session not started!');
            self::init_empty_session();
            return;
        }

        if (!$record = $DB->get_record('sessions', array('sid'=>$sid), 'id, sid, state, userid, lastip, timecreated, timemodified')) {
            if (!$newsid) {
                if (!empty($_SESSION['USER']->id)) {
                    // This should not happen, just log it, we MUST not produce any output here!
                    error_log("Cannot find session record $sid for user ".$_SESSION['USER']->id.", creating new session.");
                }
                // Prevent session fixation attacks.
                session_regenerate_id(true);
            }
            $_SESSION = array();
        }
        unset($sid);

        if (isset($_SESSION['USER']->id)) {
            if (!empty($_SESSION['USER']->realuser)) {
                $userid = $_SESSION['USER']->realuser;
            } else {
                $userid = $_SESSION['USER']->id;
            }

            // Verify timeout first.
            $maxlifetime = $CFG->sessiontimeout;
            $timeout = false;
            if (isguestuser($userid) or empty($userid)) {
                // Ignore guest and not-logged in timeouts, there is very little risk here.
                $timeout = false;

            } else if ($record->timemodified < time() - $maxlifetime) {
                $timeout = true;
                $authsequence = get_enabled_auth_plugins(); // Auths, in sequence.
                foreach ($authsequence as $authname) {
                    $authplugin = get_auth_plugin($authname);
                    if ($authplugin->ignore_timeout_hook($_SESSION['USER'], $record->sid, $record->timecreated, $record->timemodified)) {
                        $timeout = false;
                        break;
                    }
                }
            }

            if ($timeout) {
                session_regenerate_id(true);
                $_SESSION = array();
                $DB->delete_records('sessions', array('id'=>$record->id));

            } else {
                // Update session tracking record.

                $update = new \stdClass();
                $updated = false;

                if ($record->userid != $userid) {
                    $update->userid = $record->userid = $userid;
                    $updated = true;
                }

                $ip = getremoteaddr();
                if ($record->lastip != $ip) {
                    $update->lastip = $record->lastip = $ip;
                    $updated = true;
                }

                $updatefreq = empty($CFG->session_update_timemodified_frequency) ? 20 : $CFG->session_update_timemodified_frequency;

                if ($record->timemodified == $record->timecreated) {
                    // Always do first update of existing record.
                    $update->timemodified = $record->timemodified = time();
                    $updated = true;

                } else if ($record->timemodified < time() - $updatefreq) {
                    // Update the session modified flag only once every 20 seconds.
                    $update->timemodified = $record->timemodified = time();
                    $updated = true;
                }

                if ($updated) {
                    $update->id = $record->id;
                    $DB->update_record('sessions', $update);
                }

                return;
            }
        } else {
            if ($record) {
                // This happens when people switch session handlers...
                session_regenerate_id(true);
                $_SESSION = array();
                $DB->delete_records('sessions', array('id'=>$record->id));
            }
        }
        unset($record);

        $timedout = false;
        if (!isset($_SESSION['SESSION'])) {
            $_SESSION['SESSION'] = new \stdClass();
            if (!$newsid) {
                $timedout = true;
            }
        }

        $user = null;

        if (!empty($CFG->opentogoogle)) {
            if (\core_useragent::is_web_crawler()) {
                $user = guest_user();
            }
            $referer = get_local_referer(false);
            if (!empty($CFG->guestloginbutton) and !$user and !empty($referer)) {
                // Automatically log in users coming from search engine results.
                if (strpos($referer, 'google') !== false ) {
                    $user = guest_user();
                } else if (strpos($referer, 'altavista') !== false ) {
                    $user = guest_user();
                }
            }
        }

        // Setup $USER and insert the session tracking record.
        if ($user) {
            self::set_user($user);
            self::add_session_record($user->id);
        } else {
            self::init_empty_session();
            self::add_session_record(0);
        }

        if ($timedout) {
            $_SESSION['SESSION']->has_timed_out = true;
        }
    }

    /**
     * Insert new empty session record.
     * @param int $userid
     * @return \stdClass the new record
     */
    protected static function add_session_record($userid) {
        global $DB;
        $record = new \stdClass();
        $record->state       = 0;
        $record->sid         = session_id();
        $record->sessdata    = null;
        $record->userid      = $userid;
        $record->timecreated = $record->timemodified = time();
        $record->firstip     = $record->lastip = getremoteaddr();

        $record->id = $DB->insert_record('sessions', $record);

        return $record;
    }

    /**
     * Do various session security checks.
     *
     * WARNING: $USER and $SESSION are set up later, do not use them yet!
     * @throws \core\session\exception
     */
    protected static function check_security() {
        global $CFG;

        if (!empty($_SESSION['USER']->id) and !empty($CFG->tracksessionip)) {
            // Make sure current IP matches the one for this session.
            $remoteaddr = getremoteaddr();

            if (empty($_SESSION['USER']->sessionip)) {
                $_SESSION['USER']->sessionip = $remoteaddr;
            }

            if ($_SESSION['USER']->sessionip != $remoteaddr) {
                // This is a security feature - terminate the session in case of any doubt.
                self::terminate_current();
                throw new exception('sessionipnomatch2', 'error');
            }
        }
    }

    /**
     * Login user, to be called from complete_user_login() only.
     * @param \stdClass $user
     */
    public static function login_user(\stdClass $user) {
        global $DB;

        // Regenerate session id and delete old session,
        // this helps prevent session fixation attacks from the same domain.

        $sid = session_id();
        session_regenerate_id(true);
        $DB->delete_records('sessions', array('sid'=>$sid));
        self::add_session_record($user->id);

        // Let enrol plugins deal with new enrolments if necessary.
        enrol_check_plugins($user);

        // Setup $USER object.
        self::set_user($user);
    }

    /**
     * Terminate current user session.
     * @return void
     */
    public static function terminate_current() {
        global $DB;

        if (!self::$sessionactive) {
            self::init_empty_session();
            self::$sessionactive = false;
            return;
        }

        try {
            $DB->delete_records('external_tokens', array('sid'=>session_id(), 'tokentype'=>EXTERNAL_TOKEN_EMBEDDED));
        } catch (\Exception $ignored) {
            // Probably install/upgrade - ignore this problem.
        }

        // Initialize variable to pass-by-reference to headers_sent(&$file, &$line).
        $file = null;
        $line = null;
        if (headers_sent($file, $line)) {
            error_log('Cannot terminate session properly - headers were already sent in file: '.$file.' on line '.$line);
        }

        // Write new empty session and make sure the old one is deleted.
        $sid = session_id();
        session_regenerate_id(true);
        $DB->delete_records('sessions', array('sid'=>$sid));
        self::init_empty_session();
        self::add_session_record($_SESSION['USER']->id); // Do not use $USER here because it may not be set up yet.
        session_write_close();
        self::$sessionactive = false;
    }

    /**
     * No more changes in session expected.
     * Unblocks the sessions, other scripts may start executing in parallel.
     */
    public static function write_close() {
        if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
            // More control over whether session data
            // is persisted or not.
            if (self::$sessionactive && session_id()) {
                // Write session and release lock only if
                // indication session start was clean.
                session_write_close();
            } else {
                // Otherwise, if possibile lock exists want
                // to clear it, but do not write session.
                @session_abort();
            }
        } else {
            // Any indication session was started, attempt
            // to close it.
            if (self::$sessionactive || session_id()) {
                session_write_close();
            }
        }
        self::$sessionactive = false;
    }

    /**
     * Does the PHP session with given id exist?
     *
     * The session must exist both in session table and actual
     * session backend and the session must not be timed out.
     *
     * Timeout evaluation is simplified, the auth hooks are not executed.
     *
     * @param string $sid
     * @return bool
     */
    public static function session_exists($sid) {
        global $DB, $CFG;

        if (empty($CFG->version)) {
            // Not installed yet, do not try to access database.
            return false;
        }

        // Note: add sessions->state checking here if it gets implemented.
        if (!$record = $DB->get_record('sessions', array('sid' => $sid), 'id, userid, timemodified')) {
            return false;
        }

        if (empty($record->userid) or isguestuser($record->userid)) {
            // Ignore guest and not-logged-in timeouts, there is very little risk here.
        } else if ($record->timemodified < time() - $CFG->sessiontimeout) {
            return false;
        }

        // There is no need the existence of handler storage in public API.
        self::load_handler();
        return self::$handler->session_exists($sid);
    }

    /**
     * Fake last access for given session, this prevents session timeout.
     * @param string $sid
     */
    public static function touch_session($sid) {
        global $DB;

        // Timeouts depend on core sessions table only, no need to update anything in external stores.

        $sql = "UPDATE {sessions} SET timemodified = :now WHERE sid = :sid";
        $DB->execute($sql, array('now'=>time(), 'sid'=>$sid));
    }

    /**
     * Terminate all sessions unconditionally.
     */
    public static function kill_all_sessions() {
        global $DB;

        self::terminate_current();

        self::load_handler();
        self::$handler->kill_all_sessions();

        try {
            $DB->delete_records('sessions');
        } catch (\dml_exception $ignored) {
            // Do not show any warnings - might be during upgrade/installation.
        }
    }

    /**
     * Terminate give session unconditionally.
     * @param string $sid
     */
    public static function kill_session($sid) {
        global $DB;

        self::load_handler();

        if ($sid === session_id()) {
            self::write_close();
        }

        self::$handler->kill_session($sid);

        $DB->delete_records('sessions', array('sid'=>$sid));
    }

    /**
     * Terminate all sessions of given user unconditionally.
     * @param int $userid
     * @param string $keepsid keep this sid if present
     */
    public static function kill_user_sessions($userid, $keepsid = null) {
        global $DB;

        $sessions = $DB->get_records('sessions', array('userid'=>$userid), 'id DESC', 'id, sid');
        foreach ($sessions as $session) {
            if ($keepsid and $keepsid === $session->sid) {
                continue;
            }
            self::kill_session($session->sid);
        }
    }

    /**
     * Terminate other sessions of current user depending
     * on $CFG->limitconcurrentlogins restriction.
     *
     * This is expected to be called right after complete_user_login().
     *
     * NOTE:
     *  * Do not use from SSO auth plugins, this would not work.
     *  * Do not use from web services because they do not have sessions.
     *
     * @param int $userid
     * @param string $sid session id to be always keep, usually the current one
     * @return void
     */
    public static function apply_concurrent_login_limit($userid, $sid = null) {
        global $CFG, $DB;

        // NOTE: the $sid parameter is here mainly to allow testing,
        //       in most cases it should be current session id.

        if (isguestuser($userid) or empty($userid)) {
            // This applies to real users only!
            return;
        }

        if (empty($CFG->limitconcurrentlogins) or $CFG->limitconcurrentlogins < 0) {
            return;
        }

        $count = $DB->count_records('sessions', array('userid' => $userid));

        if ($count <= $CFG->limitconcurrentlogins) {
            return;
        }

        $i = 0;
        $select = "userid = :userid";
        $params = array('userid' => $userid);
        if ($sid) {
            if ($DB->record_exists('sessions', array('sid' => $sid, 'userid' => $userid))) {
                $select .= " AND sid <> :sid";
                $params['sid'] = $sid;
                $i = 1;
            }
        }

        $sessions = $DB->get_records_select('sessions', $select, $params, 'timecreated DESC', 'id, sid');
        foreach ($sessions as $session) {
            $i++;
            if ($i <= $CFG->limitconcurrentlogins) {
                continue;
            }
            self::kill_session($session->sid);
        }
    }

    /**
     * Set current user.
     *
     * @param \stdClass $user record
     */
    public static function set_user(\stdClass $user) {
        $GLOBALS['USER'] = $user;
        unset($GLOBALS['USER']->description); // Conserve memory.
        unset($GLOBALS['USER']->password);    // Improve security.
        if (isset($GLOBALS['USER']->lang)) {
            // Make sure it is a valid lang pack name.
            $GLOBALS['USER']->lang = clean_param($GLOBALS['USER']->lang, PARAM_LANG);
        }

        // Relink session with global $USER just in case it got unlinked somehow.
        $_SESSION['USER'] =& $GLOBALS['USER'];

        // Init session key.
        sesskey();
    }

    /**
     * Periodic timed-out session cleanup.
     */
    public static function gc() {
        global $CFG, $DB;

        // This may take a long time...
        \core_php_time_limit::raise();

        $maxlifetime = $CFG->sessiontimeout;

        try {
            // Kill all sessions of deleted and suspended users without any hesitation.
            $rs = $DB->get_recordset_select('sessions', "userid IN (SELECT id FROM {user} WHERE deleted <> 0 OR suspended <> 0)", array(), 'id DESC', 'id, sid');
            foreach ($rs as $session) {
                self::kill_session($session->sid);
            }
            $rs->close();

            // Kill sessions of users with disabled plugins.
            $auth_sequence = get_enabled_auth_plugins(true);
            $auth_sequence = array_flip($auth_sequence);
            unset($auth_sequence['nologin']); // No login means user cannot login.
            $auth_sequence = array_flip($auth_sequence);

            list($notplugins, $params) = $DB->get_in_or_equal($auth_sequence, SQL_PARAMS_QM, '', false);
            $rs = $DB->get_recordset_select('sessions', "userid IN (SELECT id FROM {user} WHERE auth $notplugins)", $params, 'id DESC', 'id, sid');
            foreach ($rs as $session) {
                self::kill_session($session->sid);
            }
            $rs->close();

            // Now get a list of time-out candidates - real users only.
            $sql = "SELECT u.*, s.sid, s.timecreated AS s_timecreated, s.timemodified AS s_timemodified
                      FROM {user} u
                      JOIN {sessions} s ON s.userid = u.id
                     WHERE s.timemodified < :purgebefore AND u.id <> :guestid";
            $params = array('purgebefore' => (time() - $maxlifetime), 'guestid'=>$CFG->siteguest);

            $authplugins = array();
            foreach ($auth_sequence as $authname) {
                $authplugins[$authname] = get_auth_plugin($authname);
            }
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $user) {
                foreach ($authplugins as $authplugin) {
                    /** @var \auth_plugin_base $authplugin*/
                    if ($authplugin->ignore_timeout_hook($user, $user->sid, $user->s_timecreated, $user->s_timemodified)) {
                        continue;
                    }
                }
                self::kill_session($user->sid);
            }
            $rs->close();

            // Delete expired sessions for guest user account, give them larger timeout, there is no security risk here.
            $params = array('purgebefore' => (time() - ($maxlifetime * 5)), 'guestid'=>$CFG->siteguest);
            $rs = $DB->get_recordset_select('sessions', 'userid = :guestid AND timemodified < :purgebefore', $params, 'id DESC', 'id, sid');
            foreach ($rs as $session) {
                self::kill_session($session->sid);
            }
            $rs->close();

            // Delete expired sessions for userid = 0 (not logged in), better kill them asap to release memory.
            $params = array('purgebefore' => (time() - $maxlifetime));
            $rs = $DB->get_recordset_select('sessions', 'userid = 0 AND timemodified < :purgebefore', $params, 'id DESC', 'id, sid');
            foreach ($rs as $session) {
                self::kill_session($session->sid);
            }
            $rs->close();

            // Cleanup letfovers from the first browser access because it may set multiple cookies and then use only one.
            $params = array('purgebefore' => (time() - 60*3));
            $rs = $DB->get_recordset_select('sessions', 'userid = 0 AND timemodified = timecreated AND timemodified < :purgebefore', $params, 'id ASC', 'id, sid');
            foreach ($rs as $session) {
                self::kill_session($session->sid);
            }
            $rs->close();

        } catch (\Exception $ex) {
            debugging('Error gc-ing sessions: '.$ex->getMessage(), DEBUG_NORMAL, $ex->getTrace());
        }
    }

    /**
     * Is current $USER logged-in-as somebody else?
     * @return bool
     */
    public static function is_loggedinas() {
        return !empty($GLOBALS['USER']->realuser);
    }

    /**
     * Returns the $USER object ignoring current login-as session
     * @return \stdClass user object
     */
    public static function get_realuser() {
        if (self::is_loggedinas()) {
            return $_SESSION['REALUSER'];
        } else {
            return $GLOBALS['USER'];
        }
    }

    /**
     * Login as another user - no security checks here.
     * @param int $userid
     * @param \context $context
     * @return void
     */
    public static function loginas($userid, \context $context) {
        global $USER;

        if (self::is_loggedinas()) {
            return;
        }

        // Switch to fresh new $_SESSION.
        $_SESSION = array();
        $_SESSION['REALSESSION'] = clone($GLOBALS['SESSION']);
        $GLOBALS['SESSION'] = new \stdClass();
        $_SESSION['SESSION'] =& $GLOBALS['SESSION'];

        // Create the new $USER object with all details and reload needed capabilities.
        $_SESSION['REALUSER'] = clone($GLOBALS['USER']);
        $user = get_complete_user_data('id', $userid);
        $user->realuser       = $_SESSION['REALUSER']->id;
        $user->loginascontext = $context;

        // Let enrol plugins deal with new enrolments if necessary.
        enrol_check_plugins($user);

        // Create event before $USER is updated.
        $event = \core\event\user_loggedinas::create(
            array(
                'objectid' => $USER->id,
                'context' => $context,
                'relateduserid' => $userid,
                'other' => array(
                    'originalusername' => fullname($USER, true),
                    'loggedinasusername' => fullname($user, true)
                )
            )
        );
        // Set up global $USER.
        \core\session\manager::set_user($user);
        $event->trigger();
    }

    /**
     * Add a JS session keepalive to the page.
     *
     * A JS session keepalive script will be called to update the session modification time every $frequency seconds.
     *
     * Upon failure, the specified error message will be shown to the user.
     *
     * @param string $identifier The string identifier for the message to show on failure.
     * @param string $component The string component for the message to show on failure.
     * @param int $frequency The update frequency in seconds.
     * @throws coding_exception IF the frequency is longer than the session lifetime.
     */
    public static function keepalive($identifier = 'sessionerroruser', $component = 'error', $frequency = null) {
        global $CFG, $PAGE;

        if ($frequency) {
            if ($frequency > $CFG->sessiontimeout) {
                // Sanity check the frequency.
                throw new \coding_exception('Keepalive frequency is longer than the session lifespan.');
            }
        } else {
            // A frequency of sessiontimeout / 3 allows for one missed request whilst still preserving the session.
            $frequency = $CFG->sessiontimeout / 3;
        }

        // Add the session keepalive script to the list of page output requirements.
        $sessionkeepaliveurl = new \moodle_url('/lib/sessionkeepalive_ajax.php');
        $PAGE->requires->string_for_js($identifier, $component);
        $PAGE->requires->yui_module('moodle-core-checknet', 'M.core.checknet.init', array(array(
            // The JS config takes this is milliseconds rather than seconds.
            'frequency' => $frequency * 1000,
            'message' => array($identifier, $component),
            'uri' => $sessionkeepaliveurl->out(),
        )));
    }

}
