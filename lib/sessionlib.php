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
 * @package    core
 * @subpackage session
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @copyright  2008, 2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!defined('SESSION_ACQUIRE_LOCK_TIMEOUT')) {
    /**
     * How much time to wait for session lock before displaying error (in seconds),
     * 2 minutes by default should be a reasonable time before telling users to wait and refresh browser.
     */
    define('SESSION_ACQUIRE_LOCK_TIMEOUT', 60*2);
}

/**
  * Factory method returning moodle_session object.
  * @return moodle_session
  */
function session_get_instance() {
    global $CFG, $DB;

    static $session = null;

    if (is_null($session)) {
        if (empty($CFG->sessiontimeout)) {
            $CFG->sessiontimeout = 7200;
        }

        try {
            if (defined('SESSION_CUSTOM_CLASS')) {
                // this is a hook for webservices, key based login, etc.
                if (defined('SESSION_CUSTOM_FILE')) {
                    require_once($CFG->dirroot.SESSION_CUSTOM_FILE);
                }
                $session_class = SESSION_CUSTOM_CLASS;
                $session = new $session_class();

            } else if ((!isset($CFG->dbsessions) or $CFG->dbsessions) and $DB->session_lock_supported()) {
                // default recommended session type
                $session = new database_session();

            } else {
                // legacy limited file based storage - some features and auth plugins will not work, sorry
                $session = new legacy_file_session();
            }
        } catch (Exception $ex) {
            // prevent repeated inits
            $session = new emergency_session();
            throw $ex;
        }
    }

    return $session;
}


/**
 * Moodle session abstraction
 *
 * @package    core
 * @subpackage session
 * @copyright  2008 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface moodle_session {
    /**
     * Terminate current session
     * @return void
     */
    public function terminate_current();

    /**
     * No more changes in session expected.
     * Unblocks the sessions, other scripts may start executing in parallel.
     * @return void
     */
    public function write_close();

    /**
     * Check for existing session with id $sid
     * @param unknown_type $sid
     * @return boolean true if session found.
     */
    public function session_exists($sid);
}


/**
 * Fallback session handler when standard session init fails.
 * This prevents repeated attempts to init faulty handler.
 *
 * @package    core
 * @subpackage session
 * @copyright  2011 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emergency_session implements moodle_session {

    public function __construct() {
        // session not used at all
        $_SESSION = array();
        $_SESSION['SESSION'] = new stdClass();
        $_SESSION['USER']    = new stdClass();
    }

    /**
     * Terminate current session
     * @return void
     */
    public function terminate_current() {
        return;
    }

    /**
     * No more changes in session expected.
     * Unblocks the sessions, other scripts may start executing in parallel.
     * @return void
     */
    public function write_close() {
        return;
    }

    /**
     * Check for existing session with id $sid
     * @param unknown_type $sid
     * @return boolean true if session found.
     */
    public function session_exists($sid) {
        return false;
    }
}


/**
 * Class handling all session and cookies related stuff.
 *
 * @package    core
 * @subpackage session
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class session_stub implements moodle_session {
    protected $justloggedout;

    public function __construct() {
        global $CFG;

        if (NO_MOODLE_COOKIES) {
            // session not used at all
            $CFG->usesid = false;

            $_SESSION = array();
            $_SESSION['SESSION'] = new stdClass();
            $_SESSION['USER']    = new stdClass();

        } else {
            $this->prepare_cookies();
            $this->init_session_storage();

            $newsession = empty($_COOKIE['MoodleSession'.$CFG->sessioncookie]);

            // cookieless mode is prevented for security reasons
            $CFG->usesid = false;
            ini_set('session.use_trans_sid', '0');

            session_name('MoodleSession'.$CFG->sessioncookie);
            session_set_cookie_params(0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
            session_start();
            if (!isset($_SESSION['SESSION'])) {
                $_SESSION['SESSION'] = new stdClass();
                if (!$newsession and !$this->justloggedout) {
                    $_SESSION['SESSION']->has_timed_out = true;
                }
            }
            if (!isset($_SESSION['USER'])) {
                $_SESSION['USER'] = new stdClass();
            }
        }

        $this->check_user_initialised();

        $this->check_security();
    }

    /**
     * Terminate current session
     * @return void
     */
    public function terminate_current() {
        global $CFG, $SESSION, $USER, $DB;

        try {
            $DB->delete_records('external_tokens', array('sid'=>session_id(), 'tokentype'=>EXTERNAL_TOKEN_EMBEDDED));
        } catch (Exception $ignored) {
            // probably install/upgrade - ignore this problem
        }

        if (NO_MOODLE_COOKIES) {
            return;
        }

        // Initialize variable to pass-by-reference to headers_sent(&$file, &$line)
        $_SESSION = array();
        $_SESSION['SESSION'] = new stdClass();
        $_SESSION['USER']    = new stdClass();
        $_SESSION['USER']->id = 0;
        if (isset($CFG->mnet_localhost_id)) {
            $_SESSION['USER']->mnethostid = $CFG->mnet_localhost_id;
        }
        $SESSION = $_SESSION['SESSION']; // this may not work properly
        $USER    = $_SESSION['USER'];    // this may not work properly

        $file = null;
        $line = null;
        if (headers_sent($file, $line)) {
            error_log('Can not terminate session properly - headers were already sent in file: '.$file.' on line '.$line);
        }

        // now let's try to get a new session id and delete the old one
        $this->justloggedout = true;
        session_regenerate_id(true);
        $this->justloggedout = false;

        // write the new session
        session_write_close();
    }

    /**
     * No more changes in session expected.
     * Unblocks the sessions, other scripts may start executing in parallel.
     * @return void
     */
    public function write_close() {
        if (NO_MOODLE_COOKIES) {
            return;
        }

        session_write_close();
    }

    /**
     * Initialise $USER object, handles google access
     * and sets up not logged in user properly.
     *
     * @return void
     */
    protected function check_user_initialised() {
        global $CFG;

        if (isset($_SESSION['USER']->id)) {
            // already set up $USER
            return;
        }

        $user = null;

        if (!empty($CFG->opentogoogle) and !NO_MOODLE_COOKIES) {
            if (is_web_crawler()) {
                $user = guest_user();
            }
            if (!empty($CFG->guestloginbutton) and !$user and !empty($_SERVER['HTTP_REFERER'])) {
                // automaticaly log in users coming from search engine results
                if (strpos($_SERVER['HTTP_REFERER'], 'google') !== false ) {
                    $user = guest_user();
                } else if (strpos($_SERVER['HTTP_REFERER'], 'altavista') !== false ) {
                    $user = guest_user();
                }
            }
        }

        if (!$user) {
            $user = new stdClass();
            $user->id = 0; // to enable proper function of $CFG->notloggedinroleid hack
            if (isset($CFG->mnet_localhost_id)) {
                $user->mnethostid = $CFG->mnet_localhost_id;
            } else {
                $user->mnethostid = 1;
            }
        }
        session_set_user($user);
    }

    /**
     * Does various session security checks
     * @global void
     */
    protected function check_security() {
        global $CFG;

        if (NO_MOODLE_COOKIES) {
            return;
        }

        if (!empty($_SESSION['USER']->id) and !empty($CFG->tracksessionip)) {
            /// Make sure current IP matches the one for this session
            $remoteaddr = getremoteaddr();

            if (empty($_SESSION['USER']->sessionip)) {
                $_SESSION['USER']->sessionip = $remoteaddr;
            }

            if ($_SESSION['USER']->sessionip != $remoteaddr) {
                // this is a security feature - terminate the session in case of any doubt
                $this->terminate_current();
                print_error('sessionipnomatch2', 'error');
            }
        }
    }

    /**
     * Prepare cookies and various system settings
     */
    protected function prepare_cookies() {
        global $CFG;

        if (!isset($CFG->cookiesecure) or (strpos($CFG->wwwroot, 'https://') !== 0 and empty($CFG->sslproxy))) {
            $CFG->cookiesecure = 0;
        }

        if (!isset($CFG->cookiehttponly)) {
            $CFG->cookiehttponly = 0;
        }

    /// Set sessioncookie and sessioncookiepath variable if it isn't already
        if (!isset($CFG->sessioncookie)) {
            $CFG->sessioncookie = '';
        }
        if (!isset($CFG->sessioncookiedomain)) {
            $CFG->sessioncookiedomain = '';
        }
        if (!isset($CFG->sessioncookiepath)) {
            $CFG->sessioncookiepath = '/';
        }

        //discard session ID from POST, GET and globals to tighten security,
        //this session fixation prevention can not be used in cookieless mode
        if (empty($CFG->usesid)) {
            unset(${'MoodleSession'.$CFG->sessioncookie});
            unset($_GET['MoodleSession'.$CFG->sessioncookie]);
            unset($_POST['MoodleSession'.$CFG->sessioncookie]);
            unset($_REQUEST['MoodleSession'.$CFG->sessioncookie]);
        }
        //compatibility hack for Moodle Cron, cookies not deleted, but set to "deleted" - should not be needed with NO_MOODLE_COOKIES in cron.php now
        if (!empty($_COOKIE['MoodleSession'.$CFG->sessioncookie]) && $_COOKIE['MoodleSession'.$CFG->sessioncookie] == "deleted") {
            unset($_COOKIE['MoodleSession'.$CFG->sessioncookie]);
        }
    }

    /**
     * Init session storage.
     */
    protected abstract function init_session_storage();
}


/**
 * Legacy moodle sessions stored in files, not recommended any more.
 *
 * @package    core
 * @subpackage session
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class legacy_file_session extends session_stub {
    /**
     * Init session storage.
     */
    protected function init_session_storage() {
        global $CFG;

        ini_set('session.save_handler', 'files');

        // Some distros disable GC by setting probability to 0
        // overriding the PHP default of 1
        // (gc_probability is divided by gc_divisor, which defaults to 1000)
        if (ini_get('session.gc_probability') == 0) {
            ini_set('session.gc_probability', 1);
        }

        ini_set('session.gc_maxlifetime', $CFG->sessiontimeout);

        // make sure sessions dir exists and is writable, throws exception if not
        make_upload_directory('sessions');

        // Need to disable debugging since disk_free_space()
        // will fail on very large partitions (see MDL-19222)
        $freespace = @disk_free_space($CFG->dataroot.'/sessions');
        if (!($freespace > 2048) and $freespace !== false) {
            print_error('sessiondiskfull', 'error');
        }
        ini_set('session.save_path', $CFG->dataroot .'/sessions');
    }
    /**
     * Check for existing session with id $sid
     * @param unknown_type $sid
     * @return boolean true if session found.
     */
    public function session_exists($sid){
        global $CFG;

        $sid = clean_param($sid, PARAM_FILE);
        $sessionfile = "$CFG->dataroot/sessions/sess_$sid";
        return file_exists($sessionfile);
    }
}


/**
 * Recommended moodle session storage.
 *
 * @package    core
 * @subpackage session
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_session extends session_stub {
    /** @var stdClass $record session record */
    protected $record   = null;

    /** @var moodle_database $database session database */
    protected $database = null;

    /** @var bool $failed session read/init failed, do not write back to DB */
    protected $failed   = false;

    public function __construct() {
        global $DB;
        $this->database = $DB;
        parent::__construct();

        if (!empty($this->record->state)) {
            // something is very wrong
            session_kill($this->record->sid);

            if ($this->record->state == 9) {
                print_error('dbsessionmysqlpacketsize', 'error');
            }
        }
    }

    /**
     * Check for existing session with id $sid
     * @param unknown_type $sid
     * @return boolean true if session found.
     */
    public function session_exists($sid){
        global $CFG;
        try {
            $sql = "SELECT * FROM {sessions} WHERE timemodified < ? AND sid=? AND state=?";
            $params = array(time() + $CFG->sessiontimeout, $sid, 0);

            return $this->database->record_exists_sql($sql, $params);
        } catch (dml_exception $ex) {
            error_log('Error checking existance of database session');
            return false;
        }
    }

    /**
     * Init session storage.
     */
    protected function init_session_storage() {
        global $CFG;

        // gc only from CRON - individual user timeouts now checked during each access
        ini_set('session.gc_probability', 0);

        ini_set('session.gc_maxlifetime', $CFG->sessiontimeout);

        $result = session_set_save_handler(array($this, 'handler_open'),
                                           array($this, 'handler_close'),
                                           array($this, 'handler_read'),
                                           array($this, 'handler_write'),
                                           array($this, 'handler_destroy'),
                                           array($this, 'handler_gc'));
        if (!$result) {
            print_error('dbsessionhandlerproblem', 'error');
        }
    }

    /**
     * Open session handler
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param string $save_path
     * @param string $session_name
     * @return bool success
     */
    public function handler_open($save_path, $session_name) {
        return true;
    }

    /**
     * Close session handler
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @return bool success
     */
    public function handler_close() {
        if (isset($this->record->id)) {
            try {
                $this->database->release_session_lock($this->record->id);
            } catch (Exception $ex) {
                // ignore any problems
            }
        }
        $this->record = null;
        return true;
    }

    /**
     * Read session handler
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param string $sid
     * @return string
     */
    public function handler_read($sid) {
        global $CFG;

        if ($this->record and $this->record->sid != $sid) {
            error_log('Weird error reading database session - mismatched sid');
            $this->failed = true;
            return '';
        }

        try {
            if (!$record = $this->database->get_record('sessions', array('sid'=>$sid))) {
                $record = new stdClass();
                $record->state        = 0;
                $record->sid          = $sid;
                $record->sessdata     = null;
                $record->userid       = 0;
                $record->timecreated  = $record->timemodified = time();
                $record->firstip      = $record->lastip = getremoteaddr();
                $record->id           = $this->database->insert_record_raw('sessions', $record);
            }
        } catch (Exception $ex) {
            // do not rethrow exceptions here, we need this to work somehow before 1.9.x upgrade and during install
            error_log('Can not read or insert database sessions');
            $this->failed = true;
            return '';
        }

        try {
            $this->database->get_session_lock($record->id, SESSION_ACQUIRE_LOCK_TIMEOUT);
        } catch (Exception $ex) {
            // This is a fatal error, better inform users.
            // It should not happen very often - all pages that need long time to execute
            // should close session soon after access control checks
            error_log('Can not obtain session lock');
            $this->failed = true;
            throw $ex;
        }

        // verify timeout
        if ($record->timemodified + $CFG->sessiontimeout < time()) {
            $ignoretimeout = false;
            if (!empty($record->userid)) { // skips not logged in
                if ($user = $this->database->get_record('user', array('id'=>$record->userid))) {
                    if (!isguestuser($user)) {
                        $authsequence = get_enabled_auth_plugins(); // auths, in sequence
                        foreach($authsequence as $authname) {
                            $authplugin = get_auth_plugin($authname);
                            if ($authplugin->ignore_timeout_hook($user, $record->sid, $record->timecreated, $record->timemodified)) {
                                $ignoretimeout = true;
                                break;
                            }
                        }
                    }
                }
            }
            if ($ignoretimeout) {
                //refresh session
                $record->timemodified = time();
                try {
                    $this->database->update_record('sessions', $record);
                } catch (Exception $ex) {
                    // very unlikely error
                    error_log('Can not refresh database session');
                    $this->failed = true;
                    throw $ex;
                }
            } else {
                //time out session
                $record->state        = 0;
                $record->sessdata     = null;
                $record->userid       = 0;
                $record->timecreated  = $record->timemodified = time();
                $record->firstip      = $record->lastip = getremoteaddr();
                try {
                    $this->database->update_record('sessions', $record);
                } catch (Exception $ex) {
                    // very unlikely error
                    error_log('Can not time out database session');
                    $this->failed = true;
                    throw $ex;
                }
            }
        }

        $data = is_null($record->sessdata) ? '' : base64_decode($record->sessdata);

        unset($record->sessdata); // conserve memory
        $this->record = $record;

        return $data;
    }

    /**
     * Write session handler.
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * NOTE: Do not write to output or throw any exceptions!
     *       Hopefully the next page is going to display nice error or it recovers...
     *
     * @param string $sid
     * @param string $session_data
     * @return bool success
     */
    public function handler_write($sid, $session_data) {
        global $USER;

        // TODO: MDL-20625 we need to rollback all active transactions and log error if any open needed

        if ($this->failed) {
            // do not write anything back - we failed to start the session properly
            return false;
        }

        $userid = 0;
        if (!empty($USER->realuser)) {
            $userid = $USER->realuser;
        } else if (!empty($USER->id)) {
            $userid = $USER->id;
        }

        if (isset($this->record->id)) {
            $record = new stdClass();
            $record->state              = 0;
            $record->sid                = $sid;                         // might be regenerating sid
            $this->record->sessdata     = base64_encode($session_data); // there might be some binary mess :-(
            $this->record->userid       = $userid;
            $this->record->timemodified = time();
            $this->record->lastip       = getremoteaddr();

            // TODO: verify session changed before doing update,
            //       also make sure the timemodified field is changed only every 10s if nothing else changes  MDL-20462

            try {
                $this->database->update_record_raw('sessions', $this->record);
            } catch (dml_exception $ex) {
                if ($this->database->get_dbfamily() === 'mysql') {
                    try {
                        $this->database->set_field('sessions', 'state', 9, array('id'=>$this->record->id));
                    } catch (Exception $ignored) {
                    }
                    error_log('Can not write database session - please verify max_allowed_packet is at least 4M!');
                } else {
                    error_log('Can not write database session');
                }
                return false;
            } catch (Exception $ex) {
                error_log('Can not write database session');
                return false;
            }

        } else {
            // fresh new session
            try {
                $record = new stdClass();
                $record->state        = 0;
                $record->sid          = $sid;
                $record->sessdata     = base64_encode($session_data); // there might be some binary mess :-(
                $record->userid       = $userid;
                $record->timecreated  = $record->timemodified = time();
                $record->firstip      = $record->lastip = getremoteaddr();
                $record->id           = $this->database->insert_record_raw('sessions', $record);
                $this->record = $record;

                $this->database->get_session_lock($this->record->id, SESSION_ACQUIRE_LOCK_TIMEOUT);
            } catch (Exception $ex) {
                // this should not happen
                error_log('Can not write new database session or acquire session lock');
                $this->failed = true;
                return false;
            }
        }

        return true;
    }

    /**
     * Destroy session handler
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param string $sid
     * @return bool success
     */
    public function handler_destroy($sid) {
        session_kill($sid);

        if (isset($this->record->id) and $this->record->sid === $sid) {
            try {
                $this->database->release_session_lock($this->record->id);
            } catch (Exception $ex) {
                // ignore problems
            }
            $this->record = null;
        }

        return true;
    }

    /**
     * GC session handler
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param int $ignored_maxlifetime moodle uses special timeout rules
     * @return bool success
     */
    public function handler_gc($ignored_maxlifetime) {
        session_gc();
        return true;
    }
}


/**
 * returns true if legacy session used.
 * @return bool true if legacy(==file) based session used
 */
function session_is_legacy() {
    global $CFG, $DB;
    return ((isset($CFG->dbsessions) and !$CFG->dbsessions) or !$DB->session_lock_supported());
}

/**
 * Terminates all sessions, auth hooks are not executed.
 * Useful in upgrade scripts.
 */
function session_kill_all() {
    global $CFG, $DB;

    // always check db table - custom session classes use sessions table
    try {
        $DB->delete_records('sessions');
    } catch (dml_exception $ignored) {
        // do not show any warnings - might be during upgrade/installation
    }

    if (session_is_legacy()) {
        $sessiondir = "$CFG->dataroot/sessions";
        if (is_dir($sessiondir)) {
            foreach (glob("$sessiondir/sess_*") as $filename) {
                @unlink($filename);
            }
        }
    }
}

/**
 * Mark session as accessed, prevents timeouts.
 * @param string $sid
 */
function session_touch($sid) {
    global $CFG, $DB;

    // always check db table - custom session classes use sessions table
    try {
        $sql = "UPDATE {sessions} SET timemodified=? WHERE sid=?";
        $params = array(time(), $sid);
        $DB->execute($sql, $params);
    } catch (dml_exception $ignored) {
        // do not show any warnings - might be during upgrade/installation
    }

    if (session_is_legacy()) {
        $sid = clean_param($sid, PARAM_FILE);
        $sessionfile = clean_param("$CFG->dataroot/sessions/sess_$sid", PARAM_FILE);
        if (file_exists($sessionfile)) {
            // if the file is locked it means that it will be updated anyway
            @touch($sessionfile);
        }
    }
}

/**
 * Terminates one sessions, auth hooks are not executed.
 *
 * @param string $sid session id
 */
function session_kill($sid) {
    global $CFG, $DB;

    // always check db table - custom session classes use sessions table
    try {
        $DB->delete_records('sessions', array('sid'=>$sid));
    } catch (dml_exception $ignored) {
        // do not show any warnings - might be during upgrade/installation
    }

    if (session_is_legacy()) {
        $sid = clean_param($sid, PARAM_FILE);
        $sessionfile = "$CFG->dataroot/sessions/sess_$sid";
        if (file_exists($sessionfile)) {
            @unlink($sessionfile);
        }
    }
}

/**
 * Terminates all sessions of one user, auth hooks are not executed.
 * NOTE: This can not work for file based sessions!
 *
 * @param int $userid user id
 */
function session_kill_user($userid) {
    global $CFG, $DB;

    // always check db table - custom session classes use sessions table
    try {
        $DB->delete_records('sessions', array('userid'=>$userid));
    } catch (dml_exception $ignored) {
        // do not show any warnings - might be during upgrade/installation
    }

    if (session_is_legacy()) {
        // log error?
    }
}

/**
 * Session garbage collection
 * - verify timeout for all users
 * - kill sessions of all deleted users
 * - kill sessions of users with disabled plugins or 'nologin' plugin
 *
 * NOTE: this can not work when legacy file sessions used!
 */
function session_gc() {
    global $CFG, $DB;

    $maxlifetime = $CFG->sessiontimeout;

    try {
        /// kill all sessions of deleted users
        $DB->delete_records_select('sessions', "userid IN (SELECT id FROM {user} WHERE deleted <> 0)");

        /// kill sessions of users with disabled plugins
        $auth_sequence = get_enabled_auth_plugins(true);
        $auth_sequence = array_flip($auth_sequence);
        unset($auth_sequence['nologin']); // no login allowed
        $auth_sequence = array_flip($auth_sequence);
        $notplugins = null;
        list($notplugins, $params) = $DB->get_in_or_equal($auth_sequence, SQL_PARAMS_QM, '', false);
        $DB->delete_records_select('sessions', "userid IN (SELECT id FROM {user} WHERE auth $notplugins)", $params);

        /// now get a list of time-out candidates
        $sql = "SELECT u.*, s.sid, s.timecreated AS s_timecreated, s.timemodified AS s_timemodified
                  FROM {user} u
                  JOIN {sessions} s ON s.userid = u.id
                 WHERE s.timemodified + ? < ? AND u.id <> ?";
        $params = array($maxlifetime, time(), $CFG->siteguest);

        $authplugins = array();
        foreach($auth_sequence as $authname) {
            $authplugins[$authname] = get_auth_plugin($authname);
        }
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $user) {
            foreach ($authplugins as $authplugin) {
                if ($authplugin->ignore_timeout_hook($user, $user->sid, $user->s_timecreated, $user->s_timemodified)) {
                    continue;
                }
            }
            $DB->delete_records('sessions', array('sid'=>$user->sid));
        }
        $rs->close();

        $purgebefore = time() - $maxlifetime;
        // delete expired sessions for guest user account
        $DB->delete_records_select('sessions', 'userid = ? AND timemodified < ?', array($CFG->siteguest, $purgebefore));
        // delete expired sessions for userid = 0 (not logged in)
        $DB->delete_records_select('sessions', 'userid = 0 AND timemodified < ?', array($purgebefore));
    } catch (dml_exception $ex) {
        error_log('Error gc-ing sessions');
    }
}

/**
 * Makes sure that $USER->sesskey exists, if $USER itself exists. It sets a new sesskey
 * if one does not already exist, but does not overwrite existing sesskeys. Returns the
 * sesskey string if $USER exists, or boolean false if not.
 *
 * @uses $USER
 * @return string
 */
function sesskey() {
    // note: do not use $USER because it may not be initialised yet
    if (empty($_SESSION['USER']->sesskey)) {
        $_SESSION['USER']->sesskey = random_string(10);
    }

    return $_SESSION['USER']->sesskey;
}


/**
 * Check the sesskey and return true of false for whether it is valid.
 * (You might like to imagine this function is called sesskey_is_valid().)
 *
 * Every script that lets the user perform a significant action (that is,
 * changes data in the database) should check the sesskey before doing the action.
 * Depending on your code flow, you may want to use the {@link require_sesskey()}
 * helper function.
 *
 * @param string $sesskey The sesskey value to check (optional). Normally leave this blank
 *      and this function will do required_param('sesskey', ...).
 * @return bool whether the sesskey sent in the request matches the one stored in the session.
 */
function confirm_sesskey($sesskey=NULL) {
    global $USER;

    if (!empty($USER->ignoresesskey)) {
        return true;
    }

    if (empty($sesskey)) {
        $sesskey = required_param('sesskey', PARAM_RAW);  // Check script parameters
    }

    return (sesskey() === $sesskey);
}

/**
 * Check the session key using {@link confirm_sesskey()},
 * and cause a fatal error if it does not match.
 */
function require_sesskey() {
    if (!confirm_sesskey()) {
        print_error('invalidsesskey');
    }
}

/**
 * Sets a moodle cookie with a weakly encrypted username
 *
 * @param string $username to encrypt and place in a cookie, '' means delete current cookie
 * @return void
 */
function set_moodle_cookie($username) {
    global $CFG;

    if (NO_MOODLE_COOKIES) {
        return;
    }

    if ($username === 'guest') {
        // keep previous cookie in case of guest account login
        return;
    }

    $cookiename = 'MOODLEID1_'.$CFG->sessioncookie;

    // delete old cookie
    setcookie($cookiename, '', time() - HOURSECS, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);

    if ($username !== '') {
        // set username cookie for 60 days
        setcookie($cookiename, rc4encrypt($username, true), time()+(DAYSECS*60), $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
    }
}

/**
 * Gets a moodle cookie with a weakly encrypted username
 *
 * @return string username
 */
function get_moodle_cookie() {
    global $CFG;

    if (NO_MOODLE_COOKIES) {
        return '';
    }

    $cookiename = 'MOODLEID1_'.$CFG->sessioncookie;

    if (empty($_COOKIE[$cookiename])) {
        return '';
    } else {
        $username = rc4decrypt($_COOKIE[$cookiename], true);
        if ($username === 'guest' or $username === 'nobody') {
            // backwards compatibility - we do not set these cookies any more
            $username = '';
        }
        return $username;
    }
}


/**
 * Setup $USER object - called during login, loginas, etc.
 * Preloads capabilities and checks enrolment plugins
 *
 * @param stdClass $user full user record object
 * @return void
 */
function session_set_user($user) {
    $_SESSION['USER'] = $user;
    unset($_SESSION['USER']->description); // conserve memory
    if (!isset($_SESSION['USER']->access)) {
        // check enrolments and load caps only once
        enrol_check_plugins($_SESSION['USER']);
        load_all_capabilities();
    }
    sesskey(); // init session key
}

/**
 * Is current $USER logged-in-as somebody else?
 * @return bool
 */
function session_is_loggedinas() {
    return !empty($_SESSION['USER']->realuser);
}

/**
 * Returns the $USER object ignoring current login-as session
 * @return stdClass user object
 */
function session_get_realuser() {
    if (session_is_loggedinas()) {
        return $_SESSION['REALUSER'];
    } else {
        return $_SESSION['USER'];
    }
}

/**
 * Login as another user - no security checks here.
 * @param int $userid
 * @param stdClass $context
 * @return void
 */
function session_loginas($userid, $context) {
    if (session_is_loggedinas()) {
        return;
    }

    // switch to fresh new $SESSION
    $_SESSION['REALSESSION'] = $_SESSION['SESSION'];
    $_SESSION['SESSION']     = new stdClass();

    /// Create the new $USER object with all details and reload needed capabilities
    $_SESSION['REALUSER'] = $_SESSION['USER'];
    $user = get_complete_user_data('id', $userid);
    $user->realuser       = $_SESSION['REALUSER']->id;
    $user->loginascontext = $context;
    session_set_user($user);
}

/**
 * Sets up current user and course environment (lang, etc.) in cron.
 * Do not use outside of cron script!
 *
 * @param stdClass $user full user object, null means default cron user (admin)
 * @param $course full course record, null means $SITE
 * @return void
 */
function cron_setup_user($user = NULL, $course = NULL) {
    global $CFG, $SITE, $PAGE;

    static $cronuser    = NULL;
    static $cronsession = NULL;

    if (empty($cronuser)) {
        /// ignore admins timezone, language and locale - use site default instead!
        $cronuser = get_admin();
        $cronuser->timezone = $CFG->timezone;
        $cronuser->lang     = '';
        $cronuser->theme    = '';
        unset($cronuser->description);

        $cronsession = new stdClass();
    }

    if (!$user) {
        // cached default cron user (==modified admin for now)
        session_set_user($cronuser);
        $_SESSION['SESSION'] = $cronsession;

    } else {
        // emulate real user session - needed for caps in cron
        if ($_SESSION['USER']->id != $user->id) {
            session_set_user($user);
            $_SESSION['SESSION'] = new stdClass();
        }
    }

    // TODO MDL-19774 relying on global $PAGE in cron is a bad idea.
    // Temporary hack so that cron does not give fatal errors.
    $PAGE = new moodle_page();
    if ($course) {
        $PAGE->set_course($course);
    } else {
        $PAGE->set_course($SITE);
    }

    // TODO: it should be possible to improve perf by caching some limited number of users here ;-)

}

/**
* Enable cookieless sessions by including $CFG->usesid=true;
* in config.php.
* Based on code from php manual by Richard at postamble.co.uk
* Attempts to use cookies if cookies not present then uses session ids attached to all urls and forms to pass session id from page to page.
* If site is open to google, google is given guest access as usual and there are no sessions. No session ids will be attached to urls for googlebot.
* This doesn't require trans_sid to be turned on but this is recommended for better performance
* you should put :
* session.use_trans_sid = 1
* in your php.ini file and make sure that you don't have a line like this in your php.ini
* session.use_only_cookies = 1
* @author Richard at postamble.co.uk and Jamie Pratt
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*/
/**
* You won't call this function directly. This function is used to process
* text buffered by php in an output buffer. All output is run through this function
* before it is ouput.
* @param string $buffer is the output sent from php
* @return string the output sent to the browser
*/
function sid_ob_rewrite($buffer){
    $replacements = array(
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*")([^"]*)(")/i',
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*\')([^\']*)(\')/i');

    $buffer = preg_replace_callback($replacements, 'sid_rewrite_link_tag', $buffer);
    $buffer = preg_replace('/<form\s[^>]*>/i',
        '\0<input type="hidden" name="' . session_name() . '" value="' . session_id() . '"/>', $buffer);

      return $buffer;
}
/**
* You won't call this function directly. This function is used to process
* text buffered by php in an output buffer. All output is run through this function
* before it is ouput.
* This function only processes absolute urls, it is used when we decide that
* php is processing other urls itself but needs some help with internal absolute urls still.
* @param string $buffer is the output sent from php
* @return string the output sent to the browser
*/
function sid_ob_rewrite_absolute($buffer){
    $replacements = array(
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*")((?:http|https)[^"]*)(")/i',
        '/(<\s*(a|link|script|frame|area)\s[^>]*(href|src)\s*=\s*\')((?:http|https)[^\']*)(\')/i');

    $buffer = preg_replace_callback($replacements, 'sid_rewrite_link_tag', $buffer);
    $buffer = preg_replace('/<form\s[^>]*>/i',
        '\0<input type="hidden" name="' . session_name() . '" value="' . session_id() . '"/>', $buffer);
    return $buffer;
}

/**
* A function to process link, a and script tags found
* by preg_replace_callback in {@link sid_ob_rewrite($buffer)}.
*/
function sid_rewrite_link_tag($matches){
    $url = $matches[4];
    $url = sid_process_url($url);
    return $matches[1].$url.$matches[5];
}

/**
* You can call this function directly. This function is used to process
* urls to add a moodle session id to the url for internal links.
* @param string $url is a url
* @return string the processed url
*/
function sid_process_url($url) {
    global $CFG;

    if ((preg_match('/^(http|https):/i', $url)) // absolute url
        &&  ((stripos($url, $CFG->wwwroot)!==0) && stripos($url, $CFG->httpswwwroot)!==0)) { // and not local one
        return $url; //don't attach sessid to non local urls
    }
    if ($url[0]=='#' || (stripos($url, 'javascript:')===0)) {
        return $url; //don't attach sessid to anchors
    }
    if (strpos($url, session_name())!==FALSE) {
        return $url; //don't attach sessid to url that already has one sessid
    }
    if (strpos($url, "?")===FALSE) {
        $append = "?".strip_tags(session_name() . '=' . session_id());
    }    else {
        $append = "&amp;".strip_tags(session_name() . '=' . session_id());
    }
    //put sessid before any anchor
    $p = strpos($url, "#");
    if ($p!==FALSE){
        $anch = substr($url, $p);
        $url = substr($url, 0, $p).$append.$anch ;
    } else  {
        $url .= $append ;
    }
    return $url;
}

/**
* Call this function before there has been any output to the browser to
* buffer output and add session ids to all internal links.
*/
function sid_start_ob(){
    global $CFG;
    //don't attach sess id for bots

    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        if (!empty($CFG->opentogoogle)) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false) {
                @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
                $CFG->usesid=false;
                return;
            }
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false) {
                @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
                $CFG->usesid=false;
                return;
            }
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false) {
            @ini_set('session.use_trans_sid', '0'); // try and turn off trans_sid
            $CFG->usesid=false;
            return;
        }
    }

    @ini_set('session.use_trans_sid', '1'); // try and turn on trans_sid

    if (ini_get('session.use_trans_sid') != 0) {
        // use trans sid as its available
        ini_set('url_rewriter.tags', 'a=href,area=href,script=src,link=href,frame=src,form=fakeentry');
        ob_start('sid_ob_rewrite_absolute');
    } else {
        //rewrite all links ourselves
        ob_start('sid_ob_rewrite');
    }
}

