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
 * This file contains utility functions for the mhaairs-moodle integration.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class for the mhaairs util api.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MHUtil {

    const TOKEN_VALIDITY_INTERVAL = 300;

    /**
     * Returns formatted GMT/UTC data/time.
     *
     * @return string
     */
    public static function get_time_stamp() {
        return gmdate("Y-m-d\TH:i:sP");
    }

    /**
     * Returns a token string with of user id, formatted time and course id (optional).
     *
     * @param int $userid
     * @param int $courseid
     * @return string
     */
    public static function create_token($userid, $courseid = 0) {
        $result = 'userid='.$userid.';time='. self::get_time_stamp();
        if ($courseid) {
            $result = 'courseid='.$courseid.';'.$result;
        }
        return $result;
    }

    /**
     * Returns a token string consisting of key=value pairs.
     *
     * @param string $params Associative array of key value pairs.
     * @return string
     */
    public static function create_token2(array $params) {
        $result = '';

        $params['time'] = self::get_time_stamp();

        foreach ($params as $name => $value) {
            if (!empty($result)) {
                $result .= '&';
            }
            $result .= "$name=$value";
        }

        return $result;
    }

    /**
     *
     */
    public static function encode_token($token, $secret, $alg = 'md5') {
        return self::hex_encode(''.md5($token.$secret).';'.$token);
    }

    /**
     *
     */
    public static function encode_token2($token, $secret, $alg = 'md5') {
        return self::hex_encode(''.md5($token.$secret).';'.$token);
    }

    /**
     *
     */
    public static function get_token($token) {
        list( , $value) = array_pad(explode(';', $token, 2), 2, '');
        return $value;
    }

    /**
     * Returns the hash part of the token.
     *
     * @return string
     */
    public static function get_hash($token) {
        list($value, ) = array_pad(explode(';', $token, 2), 2, '');
        return $value;
    }

    /**
     * Returns the token value for the given name.
     *
     * @return string
     */
    public static function get_token_value($token, $name) {
        try {
            $parts = explode(';', $token);
            foreach ($parts as $part) {
                $pair = explode('=', $part);
                if (count($pair) > 0) {
                    if (0 === strcasecmp($pair[0], $name)) {
                        return $pair[1];
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore.
        }
        return false;
    }

    /**
     * Returns true if the given token is valid, and false otherwise.
     *
     * @return bool
     */
    public static function is_token_valid($tokentext, $secret, $delay = 25200, $alg = 'md5', &$trace = '') {
        $trace = $trace.";token validation";
        try {
            $decodedtoken = self::hex_decode($tokentext);
            $trace = $trace.";decoded_token=".$decodedtoken;
            $token = self::get_token($decodedtoken);
            $hash = self::get_hash($decodedtoken);
            $trace = $trace.";hash=".$hash;
            $truehash = md5($token.$secret);
            if ($truehash === $hash) {
                $trace = $trace."the hash is good;";
                // Calculate the interval.
                $tokentimetext = self::get_token_value($token, "time");
                $tokentime = (int) strtotime($tokentimetext);
                $currenttime = time();
                $interval = $currenttime - $tokentime;
                $trace = $trace. ";interval=". $interval;
                // Is the token within the allowed timeframe?
                $intime = ($interval < $delay && $interval >= -$delay);

                return $intime;
            } else {
                $trace = $trace."the hash is bad;";
            }
        } catch (Exception $e) {
            $trace = $trace.'Exception in is_token_valid:'.$e->getMessage();
        }
        return false;
    }

    /**
     * Returns hexadecimal representation of the given string.
     *
     * @param string $data
     * @return bool|string
     */
    public static function hex_encode($data) {
        try {
            $result = bin2hex($data);
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * Returns the binary representation of the given data or FALSE on failure.
     * Alias for {@link MHUtil::hex2bin()}.
     *
     * @param string $data
     * @return string|bool
     */
    public static function hex_decode($data) {
        try {
            return self::hex2bin($data);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns the json representation of the given variable.
     *
     * @param mix $var
     * @return string
     */
    public static function var2json($var) {
        if (function_exists('json_encode')) {
            return json_encode($var);
        } else {
            // Handling primitive types.
            if (is_int($var) || is_float($var)) {
                return $var;
            }
            if (is_bool($var)) {
                return ($var) ? "true" : "false";
            }
            if (is_null($var)) {
                return "null";
            }
            if (is_string($var)) {
                return '"'.addcslashes($var, '"').'"';
            }

            if (is_object($var)) {
                $construct = array();
                foreach ($var as $key => $value) {
                    $propname = addslashes($key);
                    $propvalue = self::var2json( $value );
                    // Add to staging array.
                    $construct[] = "\"$propname\":$propvalue";
                }
                // Format JSON 'object'.
                $result = "{" . implode( ",", $construct ) . "}";
                return $result;
            }
            $associative = count( array_diff( array_keys($var), array_keys( array_keys( $var )) ));
            if ( $associative) {
                // If the array is a vector (not associative) format JSON 'object'.
                $construct = array();
                foreach ($var as $key => $value) {
                    $keyname = '';
                    if ( is_int($key)) {
                        $keyname = "key_$key";
                    } else {
                        $keyname = addslashes("$key");
                    }
                    $keyvalue = self::var2json( $value );
                    $construct[] = '"'.$keyname.'":'.$keyvalue;
                }
                $result = "{" . implode( ",", $construct ) . "}";
            } else {
                // If the array is a vector (not associative) format JSON 'array'.
                $construct = array();
                foreach ($var as $value) {
                    $construct[] = self::var2json( $value );
                }
                $result = "[" . implode( ",", $construct ) . "]";
            }
            return $result;
        }
    }

    /**
     * Returns the hexadecimal representation of the given string.
     *
     * @param string
     * @return string
     */
    public static function hex2bin($str) {
        $bin = "";
        $i = 0;
        do {
            $bin .= chr(hexdec($str[$i].$str[($i + 1)]));
            $i += 2;
        } while ($i < strlen($str));
        return $bin;
    }

    /**
     * Validates that a user that corresponds to the token, secret, username and password,
     * can log in. Returns the authentication result.
     *
     * @return MHAuthenticationResult
     */
    public static function validate_login($token, $secret, $username, $password) {
        $trace = '';
        $result = new MHAuthenticationResult(MHAuthenticationResult::FAILURE, '', '');
        if (self::is_token_valid($token, $secret, self::TOKEN_VALIDITY_INTERVAL, 'md5', $trace) || empty($secret)) {
            $user = authenticate_user_login($username, $password);
            if ($user != false) {
                $result = new MHAuthenticationResult(MHAuthenticationResult::SUCCESS, $user->username, '');
            } else {
                $result = new MHAuthenticationResult(MHAuthenticationResult::FAILURE, '', $trace.'User Authentication Failed');
            }
        }
        return $result;
    }

    /**
     * Returns the user info for the given token and secret.
     *
     * @param string $token
     * @param string $secret
     * @param string $identitytype
     * @return MHUserInfo
     */
    public static function get_user_info($token, $secret, $identitytype = null) {
        global $DB, $CFG;

        $trace = '';
        $userinfo = new MHUserInfo(MHUserInfo::FAILURE);
        $userid = null;
        $uservar = self::get_user_var($identitytype);

        // With secret the token must be valid.
        if ($secret) {
            if (!self::is_token_valid($token, $secret, self::TOKEN_VALIDITY_INTERVAL, 'md5', $trace)) {
                $userinfo->message = 'error: token is invalid';
                return $userinfo;
            }
        }

        // We must have token user id.
        if (!$tokenuserid = self::get_token_value(self::hex_decode($token), 'userid')) {
            $userinfo->message = 'error: token is invalid';
            return $userinfo;
        }

        // We have token user id and we can try to fetch the user info.
        try {
            require_once("$CFG->libdir/adminlib.php");

            // Get the user.
            $fields = 'id,deleted,suspended,username,idnumber,firstname,lastname,email,timezone';
            if (!$user = $DB->get_record('user', array($uservar => $tokenuserid), $fields)) {
                $userinfo->message = "error: user with $uservar '$tokenuserid' not found";
                return $userinfo;
            };

            // We have a user so let's construct userinfo.
            $userinfo = new MHUserInfo(MHUserInfo::SUCCESS);
            $userinfo->set_user($user);
            $trace = $trace.'; user is set';

            // Get the user courses.
            $userid = $user->id;
            $fields = 'id,category,fullname,shortname,idnumber,visible';
            $courses = enrol_get_users_courses($userid, true, $fields);

            // Get all roles from DB.
            $roles = $DB->get_records('role', null, '', 'shortname,id,archetype');

            // Get equivalent roles to Tegrity student and instructor roles.
            $studentroles = array();
            $instructorroles = array();
            if ($roles) {
                // Student roles.
                if ($shortnames = self::get_role_shortnames_from_setting('block_mhaairs_student_roles')) {
                    // Get role ids for configured short name.
                    foreach ($shortnames as $name) {
                        if (!empty($roles[$name])) {
                            $studentroles[$roles[$name]->id] = $name;
                        }
                    }
                } else {
                    // Get role ids for default archetypes.
                    foreach ($roles as $role) {
                        if ($role->archetype == 'student') {
                            $studentroles[$role->id] = $role->shortname;
                        }
                    }
                }

                // Instructor roles.
                if ($shortnames = self::get_role_shortnames_from_setting('block_mhaairs_instructor_roles')) {
                    // Get role ids for configured short name.
                    foreach ($shortnames as $name) {
                        if (!empty($roles[$name])) {
                            $instructorroles[$roles[$name]->id] = $name;
                        }
                    }
                } else {
                    // Get role ids for default archetypes.
                    foreach ($roles as $role) {
                        if ($role->archetype == 'editingteacher' or $role->archetype == 'teacher') {
                            $instructorroles[$role->id] = $role->shortname;
                        }
                    }
                }
            }

            foreach ($courses as $course) {
                $context = context_course::instance($course->id);
                // Has instrutor role in course?
                foreach ($instructorroles as $roleid => $unused) {
                    $conds = array('roleid' => $roleid, 'contextid' => $context->id, 'userid' => $userid);
                    if ($DB->record_exists('role_assignments', $conds)) {
                        $userinfo->add_course($course, 'instructor');
                        break;
                    }
                }
                // Has student role in course?
                foreach ($studentroles as $roleid => $unused) {
                    $conds = array('roleid' => $roleid, 'contextid' => $context->id, 'userid' => $userid);
                    if ($DB->record_exists('role_assignments', $conds)) {
                        $userinfo->add_course($course, 'student');
                        break;
                    }
                }
            }
            $trace = $trace. '; courses are set';
            $userinfo->message = '';

        } catch (Exception $e) {
            $userinfo = new MHUserInfo(MHUserInfo::FAILURE);
            $userinfo->message = 'ex:'. $e->getMessage(). ' trace:'. $trace;
        }

        return $userinfo;
    }

    /**
     * Returns the user var by means of which the user should be looked up,
     * according to the given identity type, 'id' if identity type is internal,
     * and 'username' otherwise.
     *
     * @param string $identitytype
     * @return string
     */
    public static function get_user_var($identitytype = null) {
        return ($identitytype == 'internal' ? 'id' : 'username');
    }

    /**
     * Returns environment info: php version, db vendor, db version,
     * moodle version and plugin version.
     *
     * @return array
     */
    public static function get_environment_info() {
        global $DB, $CFG;

        require_once("$CFG->libdir/environmentlib.php");
        require_once("$CFG->libdir/adminlib.php");

        $envinfo = array(
            'system' => php_uname('s'),
            'server' => php_sapi_name(),
            'phpversion' => normalize_version(phpversion()),
            'dbvendor' => $DB->get_dbvendor(),
            'dbversion' => '',
            'moodleversion' => '',
            'pluginversion' => '',
        );

        $dbinfo = $DB->get_server_info();
        if (!empty($dbinfo['version'])) {
            $envinfo['dbversion'] = normalize_version($dbinfo['version']);
        }

        if ($version = get_component_version('moodle')) {
            $envinfo['moodleversion'] = normalize_version($version);
        }

        if ($version = get_component_version('block_mhaairs')) {
            $envinfo['pluginversion'] = normalize_version($version);
        }

        return (object) $envinfo;
    }

    /**
     * Returns url for sso.
     *
     * @return moodle_url
     */
    public static function get_sso_url($serviceurl, $serviceid) {
        global $COURSE, $USER, $CFG;

        // Service url.
        $serviceurl = self::hex_decode($serviceurl);

        // Service id.
        $serviceid = self::hex_decode($serviceid);

        $course = $COURSE;
        $courseid = empty($course->idnumber) ? $course->id : $course->idnumber;
        $context = context_course::instance($course->id);
        $rolename = null;

        // Normalize the user's role name to insructor or student.
        if ($roles = get_user_roles($context, $USER->id)) {
            foreach ($roles as $role) {
                $rolename = empty($role->name) ? $role->shortname : $role->name;
                if ($rolename == 'teacher' || $rolename == 'editingteacher') {
                    $rolename = 'instructor';
                    break;
                }
            }
            if ($rolename != null && $rolename != 'instructor') {
                $rolename = 'student';
            }
        }

        // Get the environment info.
        $envinfo = self::get_environment_info();

        // Create the user token.
        $params = array(
            'customer' => $CFG->block_mhaairs_customer_number,
            'moodleversion' => $envinfo->moodleversion,
            'pluginversion' => $envinfo->pluginversion,
            'userid' => $USER->username,
            'username' => urlencode($USER->firstname.' '.$USER->lastname),
            'useremail' => $USER->email,
            'courseid' => $courseid,
            'courseinternalid' => $course->id,
            'coursename' => urlencode($course->shortname),
            'role' => $rolename,
            'linktype' => $serviceid,
        );
        $token = self::create_token2($params);

        // Encode the token.
        $encodedtoken = self::encode_token2($token, $CFG->block_mhaairs_shared_secret);

        $url = new moodle_url($serviceurl, array('token' => $encodedtoken));
        return $url;
    }

    /**
     * Returns a list of roles short names from a setting.
     * @return array|null
     */
    protected static function get_role_shortnames_from_setting($settingname) {
        global $CFG;

        if (!empty($CFG->{$settingname})) {
            $roleshortnames = [];
            $shortnames = explode(',', $CFG->{$settingname});
            foreach ($shortnames as $name) {
                $name = s($name);
                $roleshortnames[] = trim($name);
            }
            return array_unique($roleshortnames);
        }

        return null;
    }

}

/**
 *
 */
class MHUserInfo {
    const SUCCESS = 0;
    const FAILURE = 1;

    /* @var int $status SUCCESS|FAILURE. */
    public $status;
    /* @var stdClass $user The user object. */
    public $user;
    /* @var array $courses A list of course objects. */
    public $courses;
    /* @var string $message Unused. */
    public $message;

    public function __construct($status) {
        $this->status = $status;
        $this->user = array();
        $this->environment = array();
        $this->courses = array();
    }

    public function add_courses($courses, $rolename) {
        foreach ($courses as $course) {
            $lcourse = clone $course;
            $lcourse->rolename = $rolename;
            array_push($this->courses, $lcourse);
        }
    }

    public function add_course($course, $rolename) {
        $lcourse = clone $course;
        $lcourse->rolename = $rolename;
        array_push($this->courses, $lcourse);
    }

    public function set_user($user) {
        $this->user = $user;
    }
}

/**
 *
 */
class MHAuthenticationResult {
    const SUCCESS = 0;
    const FAILURE = 1;

    public $status;
    public $effectiveuserid;
    public $redirecturl;
    public $attributes;
    public $message;

    public function __construct($status, $effectiveuserid, $errordetails) {
        $this->status = $status;
        $this->effectiveuserid = $effectiveuserid;
        $this->attributes = array();
        $this->message = $errordetails;
    }
}

/**
 * Class block_mhaairs_log
 */
class MHLog {
    /* @var string */
    protected $_filepath = null;

    /* @var string */
    protected $_dirpath = null;

    /* @var null|bool */
    protected $_logenabled = null;

    /**
     * @return block_mhaairs_log
     */
    public static function instance() {
        return new self;
    }

    /**
     * Private constructor.
     */
    private function __construct() {
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        return null;
    }

    /**
     * Writes the specified data into the current log file.
     *
     * @param string $data
     * @return int|bool
     */
    public function log($data, $rename = false) {
        if ($this->logenabled) {
            if ($this->filepath) {
                // If we need to rename, we add an X in the file name.
                // This is meant to allow identifying failed requests
                // In the file names.
                if ($rename) {
                    $currentpath = $this->filepath;
                    $this->_filepath = preg_replace('/(mhaairs_\d{8}_\d{6}_)/', '$1X_', $currentpath);
                    if (file_exists($currentpath)) {
                        $currentcontent = file_get_contents($currentpath);
                        file_put_contents($this->filepath, $currentcontent, FILE_APPEND);
                        unlink($currentpath);
                    }
                }
                $line = $this->time_stamp. ': '. $data;
                return file_put_contents($this->filepath, $line.PHP_EOL, FILE_APPEND);
            }
        }
        return false;
    }

    /**
     * @return string|bool
     */
    public function get_dirpath() {
        global $CFG;

        if ($this->_dirpath === null) {
            $sep = DIRECTORY_SEPARATOR;
            $this->_dirpath = make_writable_directory("$CFG->dataroot{$sep}mhaairs", false);
        }
        return $this->_dirpath;
    }

    /**
     * @return bool
     */
    public function get_filepath() {
        if ($this->_filepath === null) {
            if ($dir = $this->dirpath) {
                $sep = DIRECTORY_SEPARATOR;
                $fileprefix = userdate(time(), 'mhaairs_%Y%m%d_%H%M%S_');
                while (empty($this->_filepath)) {
                    $name = uniqid($fileprefix, true);
                    $fullname = "{$dir}{$sep}{$name}.log";
                    if (!file_exists($fullname)) {
                        $this->_filepath = $fullname;
                    }
                }
            }
        }
        return $this->_filepath;
    }

    /**
     * Return formatted time stamp.
     *
     * @return string
     */
    public function get_time_stamp() {
        return userdate(time(), '%Y%m%d %H%M%S');
    }

    /**
     * @return bool
     */
    public function get_logenabled() {
        if ($this->_logenabled === null) {
            $this->_logenabled = get_config('core', 'block_mhaairs_gradelog') ? true : false;
        }
        return $this->_logenabled;
    }

    /**
     * Delete current log file
     */
    public function delete() {
        if ($this->_filepath and is_writable($this->_filepath)) {
            unlink($this->_filepath);
        }
    }

    /**
     * Delete entire log directory
     */
    public function delete_all() {
        if (!$dirpath = $this->dirpath) {
            return;
        }

        if (is_dir($dirpath) && is_writable($dirpath)) {
            $fileleft = false;
            $it = new RecursiveDirectoryIterator($dirpath, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                    continue;
                }
                $rpath = $file->getRealPath();
                if (is_writable($rpath)) {
                    if ($file->isDir()) {
                        rmdir($rpath);
                    } else {
                        unlink($rpath);
                    }
                } else {
                    $fileleft = true;
                }
            }
            if (!$fileleft) {
                rmdir($dirpath);
            }
        }
    }

    /**
     * Returns a list of existing logs.
     *
     * @return array
     */
    public function get_logs() {
        $logs = array();
        $dirpath = $this->dirpath;
        if (is_dir($this->dirpath)) {
            $it = new RecursiveDirectoryIterator($dirpath, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                if ($filename === '.' || $filename === '..') {
                    continue;
                }
                $logs[] = $filename;
            }
        }
        return $logs;
    }
}
