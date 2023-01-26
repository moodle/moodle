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
 * moodlelib.php - Moodle main library
 *
 * Main library file of miscellaneous general-purpose Moodle functions.
 * Other main libraries:
 *  - weblib.php      - functions that produce web output
 *  - datalib.php     - functions that access the database
 *
 * @package    core
 * @subpackage lib
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// CONSTANTS (Encased in phpdoc proper comments).

// Date and time constants.
/**
 * Time constant - the number of seconds in a year
 */
define('YEARSECS', 31536000);

/**
 * Time constant - the number of seconds in a week
 */
define('WEEKSECS', 604800);

/**
 * Time constant - the number of seconds in a day
 */
define('DAYSECS', 86400);

/**
 * Time constant - the number of seconds in an hour
 */
define('HOURSECS', 3600);

/**
 * Time constant - the number of seconds in a minute
 */
define('MINSECS', 60);

/**
 * Time constant - the number of minutes in a day
 */
define('DAYMINS', 1440);

/**
 * Time constant - the number of minutes in an hour
 */
define('HOURMINS', 60);

// Parameter constants - every call to optional_param(), required_param()
// or clean_param() should have a specified type of parameter.

/**
 * PARAM_ALPHA - contains only English ascii letters [a-zA-Z].
 */
define('PARAM_ALPHA',    'alpha');

/**
 * PARAM_ALPHAEXT the same contents as PARAM_ALPHA (English ascii letters [a-zA-Z]) plus the chars in quotes: "_-" allowed
 * NOTE: originally this allowed "/" too, please use PARAM_SAFEPATH if "/" needed
 */
define('PARAM_ALPHAEXT', 'alphaext');

/**
 * PARAM_ALPHANUM - expected numbers 0-9 and English ascii letters [a-zA-Z] only.
 */
define('PARAM_ALPHANUM', 'alphanum');

/**
 * PARAM_ALPHANUMEXT - expected numbers 0-9, letters (English ascii letters [a-zA-Z]) and _- only.
 */
define('PARAM_ALPHANUMEXT', 'alphanumext');

/**
 * PARAM_AUTH - actually checks to make sure the string is a valid auth plugin
 */
define('PARAM_AUTH',  'auth');

/**
 * PARAM_BASE64 - Base 64 encoded format
 */
define('PARAM_BASE64',   'base64');

/**
 * PARAM_BOOL - converts input into 0 or 1, use for switches in forms and urls.
 */
define('PARAM_BOOL',     'bool');

/**
 * PARAM_CAPABILITY - A capability name, like 'moodle/role:manage'. Actually
 * checked against the list of capabilities in the database.
 */
define('PARAM_CAPABILITY',   'capability');

/**
 * PARAM_CLEANHTML - cleans submitted HTML code. Note that you almost never want
 * to use this. The normal mode of operation is to use PARAM_RAW when receiving
 * the input (required/optional_param or formslib) and then sanitise the HTML
 * using format_text on output. This is for the rare cases when you want to
 * sanitise the HTML on input. This cleaning may also fix xhtml strictness.
 */
define('PARAM_CLEANHTML', 'cleanhtml');

/**
 * PARAM_EMAIL - an email address following the RFC
 */
define('PARAM_EMAIL',   'email');

/**
 * PARAM_FILE - safe file name, all dangerous chars are stripped, protects against XSS, SQL injections and directory traversals
 */
define('PARAM_FILE',   'file');

/**
 * PARAM_FLOAT - a real/floating point number.
 *
 * Note that you should not use PARAM_FLOAT for numbers typed in by the user.
 * It does not work for languages that use , as a decimal separator.
 * Use PARAM_LOCALISEDFLOAT instead.
 */
define('PARAM_FLOAT',  'float');

/**
 * PARAM_LOCALISEDFLOAT - a localised real/floating point number.
 * This is preferred over PARAM_FLOAT for numbers typed in by the user.
 * Cleans localised numbers to computer readable numbers; false for invalid numbers.
 */
define('PARAM_LOCALISEDFLOAT',  'localisedfloat');

/**
 * PARAM_HOST - expected fully qualified domain name (FQDN) or an IPv4 dotted quad (IP address)
 */
define('PARAM_HOST',     'host');

/**
 * PARAM_INT - integers only, use when expecting only numbers.
 */
define('PARAM_INT',      'int');

/**
 * PARAM_LANG - checks to see if the string is a valid installed language in the current site.
 */
define('PARAM_LANG',  'lang');

/**
 * PARAM_LOCALURL - expected properly formatted URL as well as one that refers to the local server itself. (NOT orthogonal to the
 * others! Implies PARAM_URL!)
 */
define('PARAM_LOCALURL', 'localurl');

/**
 * PARAM_NOTAGS - all html tags are stripped from the text. Do not abuse this type.
 */
define('PARAM_NOTAGS',   'notags');

/**
 * PARAM_PATH - safe relative path name, all dangerous chars are stripped, protects against XSS, SQL injections and directory
 * traversals note: the leading slash is not removed, window drive letter is not allowed
 */
define('PARAM_PATH',     'path');

/**
 * PARAM_PEM - Privacy Enhanced Mail format
 */
define('PARAM_PEM',      'pem');

/**
 * PARAM_PERMISSION - A permission, one of CAP_INHERIT, CAP_ALLOW, CAP_PREVENT or CAP_PROHIBIT.
 */
define('PARAM_PERMISSION',   'permission');

/**
 * PARAM_RAW specifies a parameter that is not cleaned/processed in any way except the discarding of the invalid utf-8 characters
 */
define('PARAM_RAW', 'raw');

/**
 * PARAM_RAW_TRIMMED like PARAM_RAW but leading and trailing whitespace is stripped.
 */
define('PARAM_RAW_TRIMMED', 'raw_trimmed');

/**
 * PARAM_SAFEDIR - safe directory name, suitable for include() and require()
 */
define('PARAM_SAFEDIR',  'safedir');

/**
 * PARAM_SAFEPATH - several PARAM_SAFEDIR joined by "/", suitable for include() and require(), plugin paths
 * and other references to Moodle code files.
 *
 * This is NOT intended to be used for absolute paths or any user uploaded files.
 */
define('PARAM_SAFEPATH',  'safepath');

/**
 * PARAM_SEQUENCE - expects a sequence of numbers like 8 to 1,5,6,4,6,8,9.  Numbers and comma only.
 */
define('PARAM_SEQUENCE',  'sequence');

/**
 * PARAM_TAG - one tag (interests, blogs, etc.) - mostly international characters and space, <> not supported
 */
define('PARAM_TAG',   'tag');

/**
 * PARAM_TAGLIST - list of tags separated by commas (interests, blogs, etc.)
 */
define('PARAM_TAGLIST',   'taglist');

/**
 * PARAM_TEXT - general plain text compatible with multilang filter, no other html tags. Please note '<', or '>' are allowed here.
 */
define('PARAM_TEXT',  'text');

/**
 * PARAM_THEME - Checks to see if the string is a valid theme name in the current site
 */
define('PARAM_THEME',  'theme');

/**
 * PARAM_URL - expected properly formatted URL. Please note that domain part is required, http://localhost/ is not accepted but
 * http://localhost.localdomain/ is ok.
 */
define('PARAM_URL',      'url');

/**
 * PARAM_USERNAME - Clean username to only contains allowed characters. This is to be used ONLY when manually creating user
 * accounts, do NOT use when syncing with external systems!!
 */
define('PARAM_USERNAME',    'username');

/**
 * PARAM_STRINGID - used to check if the given string is valid string identifier for get_string()
 */
define('PARAM_STRINGID',    'stringid');

// DEPRECATED PARAM TYPES OR ALIASES - DO NOT USE FOR NEW CODE.
/**
 * PARAM_CLEAN - obsoleted, please use a more specific type of parameter.
 * It was one of the first types, that is why it is abused so much ;-)
 * @deprecated since 2.0
 */
define('PARAM_CLEAN',    'clean');

/**
 * PARAM_INTEGER - deprecated alias for PARAM_INT
 * @deprecated since 2.0
 */
define('PARAM_INTEGER',  'int');

/**
 * PARAM_NUMBER - deprecated alias of PARAM_FLOAT
 * @deprecated since 2.0
 */
define('PARAM_NUMBER',  'float');

/**
 * PARAM_ACTION - deprecated alias for PARAM_ALPHANUMEXT, use for various actions in forms and urls
 * NOTE: originally alias for PARAM_APLHA
 * @deprecated since 2.0
 */
define('PARAM_ACTION',   'alphanumext');

/**
 * PARAM_FORMAT - deprecated alias for PARAM_ALPHANUMEXT, use for names of plugins, formats, etc.
 * NOTE: originally alias for PARAM_APLHA
 * @deprecated since 2.0
 */
define('PARAM_FORMAT',   'alphanumext');

/**
 * PARAM_MULTILANG - deprecated alias of PARAM_TEXT.
 * @deprecated since 2.0
 */
define('PARAM_MULTILANG',  'text');

/**
 * PARAM_TIMEZONE - expected timezone. Timezone can be int +-(0-13) or float +-(0.5-12.5) or
 * string separated by '/' and can have '-' &/ '_' (eg. America/North_Dakota/New_Salem
 * America/Port-au-Prince)
 */
define('PARAM_TIMEZONE', 'timezone');

/**
 * PARAM_CLEANFILE - deprecated alias of PARAM_FILE; originally was removing regional chars too
 */
define('PARAM_CLEANFILE', 'file');

/**
 * PARAM_COMPONENT is used for full component names (aka frankenstyle) such as 'mod_forum', 'core_rating', 'auth_ldap'.
 * Short legacy subsystem names and module names are accepted too ex: 'forum', 'rating', 'user'.
 * Only lowercase ascii letters, numbers and underscores are allowed, it has to start with a letter.
 * NOTE: numbers and underscores are strongly discouraged in plugin names!
 */
define('PARAM_COMPONENT', 'component');

/**
 * PARAM_AREA is a name of area used when addressing files, comments, ratings, etc.
 * It is usually used together with context id and component.
 * Only lowercase ascii letters, numbers and underscores are allowed, it has to start with a letter.
 */
define('PARAM_AREA', 'area');

/**
 * PARAM_PLUGIN is used for plugin names such as 'forum', 'glossary', 'ldap', 'paypal', 'completionstatus'.
 * Only lowercase ascii letters, numbers and underscores are allowed, it has to start with a letter.
 * NOTE: numbers and underscores are strongly discouraged in plugin names! Underscores are forbidden in module names.
 */
define('PARAM_PLUGIN', 'plugin');


// Web Services.

/**
 * VALUE_REQUIRED - if the parameter is not supplied, there is an error
 */
define('VALUE_REQUIRED', 1);

/**
 * VALUE_OPTIONAL - if the parameter is not supplied, then the param has no value
 */
define('VALUE_OPTIONAL', 2);

/**
 * VALUE_DEFAULT - if the parameter is not supplied, then the default value is used
 */
define('VALUE_DEFAULT', 0);

/**
 * NULL_NOT_ALLOWED - the parameter can not be set to null in the database
 */
define('NULL_NOT_ALLOWED', false);

/**
 * NULL_ALLOWED - the parameter can be set to null in the database
 */
define('NULL_ALLOWED', true);

// Page types.

/**
 * PAGE_COURSE_VIEW is a definition of a page type. For more information on the page class see moodle/lib/pagelib.php.
 */
define('PAGE_COURSE_VIEW', 'course-view');

/** Get remote addr constant */
define('GETREMOTEADDR_SKIP_HTTP_CLIENT_IP', '1');
/** Get remote addr constant */
define('GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR', '2');
/**
 * GETREMOTEADDR_SKIP_DEFAULT defines the default behavior remote IP address validation.
 */
define('GETREMOTEADDR_SKIP_DEFAULT', GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR|GETREMOTEADDR_SKIP_HTTP_CLIENT_IP);

// Blog access level constant declaration.
define ('BLOG_USER_LEVEL', 1);
define ('BLOG_GROUP_LEVEL', 2);
define ('BLOG_COURSE_LEVEL', 3);
define ('BLOG_SITE_LEVEL', 4);
define ('BLOG_GLOBAL_LEVEL', 5);


// Tag constants.
/**
 * To prevent problems with multibytes strings,Flag updating in nav not working on the review page. this should not exceed the
 * length of "varchar(255) / 3 (bytes / utf-8 character) = 85".
 * TODO: this is not correct, varchar(255) are 255 unicode chars ;-)
 *
 * @todo define(TAG_MAX_LENGTH) this is not correct, varchar(255) are 255 unicode chars ;-)
 */
define('TAG_MAX_LENGTH', 50);

// Password policy constants.
define ('PASSWORD_LOWER', 'abcdefghijklmnopqrstuvwxyz');
define ('PASSWORD_UPPER', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
define ('PASSWORD_DIGITS', '0123456789');
define ('PASSWORD_NONALPHANUM', '.,;:!?_-+/*@#&$');

// Feature constants.
// Used for plugin_supports() to report features that are, or are not, supported by a module.

/** True if module can provide a grade */
define('FEATURE_GRADE_HAS_GRADE', 'grade_has_grade');
/** True if module supports outcomes */
define('FEATURE_GRADE_OUTCOMES', 'outcomes');
/** True if module supports advanced grading methods */
define('FEATURE_ADVANCED_GRADING', 'grade_advanced_grading');
/** True if module controls the grade visibility over the gradebook */
define('FEATURE_CONTROLS_GRADE_VISIBILITY', 'controlsgradevisbility');
/** True if module supports plagiarism plugins */
define('FEATURE_PLAGIARISM', 'plagiarism');

/** True if module has code to track whether somebody viewed it */
define('FEATURE_COMPLETION_TRACKS_VIEWS', 'completion_tracks_views');
/** True if module has custom completion rules */
define('FEATURE_COMPLETION_HAS_RULES', 'completion_has_rules');

/** True if module has no 'view' page (like label) */
define('FEATURE_NO_VIEW_LINK', 'viewlink');
/** True (which is default) if the module wants support for setting the ID number for grade calculation purposes. */
define('FEATURE_IDNUMBER', 'idnumber');
/** True if module supports groups */
define('FEATURE_GROUPS', 'groups');
/** True if module supports groupings */
define('FEATURE_GROUPINGS', 'groupings');
/**
 * True if module supports groupmembersonly (which no longer exists)
 * @deprecated Since Moodle 2.8
 */
define('FEATURE_GROUPMEMBERSONLY', 'groupmembersonly');

/** Type of module */
define('FEATURE_MOD_ARCHETYPE', 'mod_archetype');
/** True if module supports intro editor */
define('FEATURE_MOD_INTRO', 'mod_intro');
/** True if module has default completion */
define('FEATURE_MODEDIT_DEFAULT_COMPLETION', 'modedit_default_completion');

define('FEATURE_COMMENT', 'comment');

define('FEATURE_RATE', 'rate');
/** True if module supports backup/restore of moodle2 format */
define('FEATURE_BACKUP_MOODLE2', 'backup_moodle2');

/** True if module can show description on course main page */
define('FEATURE_SHOW_DESCRIPTION', 'showdescription');

/** True if module uses the question bank */
define('FEATURE_USES_QUESTIONS', 'usesquestions');

/**
 * Maximum filename char size
 */
define('MAX_FILENAME_SIZE', 100);

/** Unspecified module archetype */
define('MOD_ARCHETYPE_OTHER', 0);
/** Resource-like type module */
define('MOD_ARCHETYPE_RESOURCE', 1);
/** Assignment module archetype */
define('MOD_ARCHETYPE_ASSIGNMENT', 2);
/** System (not user-addable) module archetype */
define('MOD_ARCHETYPE_SYSTEM', 3);

/** Type of module */
define('FEATURE_MOD_PURPOSE', 'mod_purpose');
/** Module purpose administration */
define('MOD_PURPOSE_ADMINISTRATION', 'administration');
/** Module purpose assessment */
define('MOD_PURPOSE_ASSESSMENT', 'assessment');
/** Module purpose communication */
define('MOD_PURPOSE_COLLABORATION', 'collaboration');
/** Module purpose communication */
define('MOD_PURPOSE_COMMUNICATION', 'communication');
/** Module purpose content */
define('MOD_PURPOSE_CONTENT', 'content');
/** Module purpose interface */
define('MOD_PURPOSE_INTERFACE', 'interface');
/** Module purpose other */
define('MOD_PURPOSE_OTHER', 'other');

/**
 * Security token used for allowing access
 * from external application such as web services.
 * Scripts do not use any session, performance is relatively
 * low because we need to load access info in each request.
 * Scripts are executed in parallel.
 */
define('EXTERNAL_TOKEN_PERMANENT', 0);

/**
 * Security token used for allowing access
 * of embedded applications, the code is executed in the
 * active user session. Token is invalidated after user logs out.
 * Scripts are executed serially - normal session locking is used.
 */
define('EXTERNAL_TOKEN_EMBEDDED', 1);

/**
 * The home page should be the site home
 */
define('HOMEPAGE_SITE', 0);
/**
 * The home page should be the users my page
 */
define('HOMEPAGE_MY', 1);
/**
 * The home page can be chosen by the user
 */
define('HOMEPAGE_USER', 2);
/**
 * The home page should be the users my courses page
 */
define('HOMEPAGE_MYCOURSES', 3);

/**
 * URL of the Moodle sites registration portal.
 */
defined('HUB_MOODLEORGHUBURL') || define('HUB_MOODLEORGHUBURL', 'https://stats.moodle.org');

/**
 * URL of the statistic server public key.
 */
defined('HUB_STATSPUBLICKEY') || define('HUB_STATSPUBLICKEY', 'https://moodle.org/static/statspubkey.pem');

/**
 * Moodle mobile app service name
 */
define('MOODLE_OFFICIAL_MOBILE_SERVICE', 'moodle_mobile_app');

/**
 * Indicates the user has the capabilities required to ignore activity and course file size restrictions
 */
define('USER_CAN_IGNORE_FILE_SIZE_LIMITS', -1);

/**
 * Course display settings: display all sections on one page.
 */
define('COURSE_DISPLAY_SINGLEPAGE', 0);
/**
 * Course display settings: split pages into a page per section.
 */
define('COURSE_DISPLAY_MULTIPAGE', 1);

/**
 * Authentication constant: String used in password field when password is not stored.
 */
define('AUTH_PASSWORD_NOT_CACHED', 'not cached');

/**
 * Email from header to never include via information.
 */
define('EMAIL_VIA_NEVER', 0);

/**
 * Email from header to always include via information.
 */
define('EMAIL_VIA_ALWAYS', 1);

/**
 * Email from header to only include via information if the address is no-reply.
 */
define('EMAIL_VIA_NO_REPLY_ONLY', 2);

/**
 * Contact site support form/link disabled.
 */
define('CONTACT_SUPPORT_DISABLED', 0);

/**
 * Contact site support form/link only available to authenticated users.
 */
define('CONTACT_SUPPORT_AUTHENTICATED', 1);

/**
 * Contact site support form/link available to anyone visiting the site.
 */
define('CONTACT_SUPPORT_ANYONE', 2);

// PARAMETER HANDLING.

/**
 * Returns a particular value for the named variable, taken from
 * POST or GET.  If the parameter doesn't exist then an error is
 * thrown because we require this variable.
 *
 * This function should be used to initialise all required values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $id = required_param('id', PARAM_INT);
 *
 * Please note the $type parameter is now required and the value can not be array.
 *
 * @param string $parname the name of the page parameter we want
 * @param string $type expected type of parameter
 * @return mixed
 * @throws coding_exception
 */
function required_param($parname, $type) {
    if (func_num_args() != 2 or empty($parname) or empty($type)) {
        throw new coding_exception('required_param() requires $parname and $type to be specified (parameter: '.$parname.')');
    }
    // POST has precedence.
    if (isset($_POST[$parname])) {
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        throw new \moodle_exception('missingparam', '', '', $parname);
    }

    if (is_array($param)) {
        debugging('Invalid array parameter detected in required_param(): '.$parname);
        // TODO: switch to fatal error in Moodle 2.3.
        return required_param_array($parname, $type);
    }

    return clean_param($param, $type);
}

/**
 * Returns a particular array value for the named variable, taken from
 * POST or GET.  If the parameter doesn't exist then an error is
 * thrown because we require this variable.
 *
 * This function should be used to initialise all required values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $ids = required_param_array('ids', PARAM_INT);
 *
 *  Note: arrays of arrays are not supported, only alphanumeric keys with _ and - are supported
 *
 * @param string $parname the name of the page parameter we want
 * @param string $type expected type of parameter
 * @return array
 * @throws coding_exception
 */
function required_param_array($parname, $type) {
    if (func_num_args() != 2 or empty($parname) or empty($type)) {
        throw new coding_exception('required_param_array() requires $parname and $type to be specified (parameter: '.$parname.')');
    }
    // POST has precedence.
    if (isset($_POST[$parname])) {
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        throw new \moodle_exception('missingparam', '', '', $parname);
    }
    if (!is_array($param)) {
        throw new \moodle_exception('missingparam', '', '', $parname);
    }

    $result = array();
    foreach ($param as $key => $value) {
        if (!preg_match('/^[a-z0-9_-]+$/i', $key)) {
            debugging('Invalid key name in required_param_array() detected: '.$key.', parameter: '.$parname);
            continue;
        }
        $result[$key] = clean_param($value, $type);
    }

    return $result;
}

/**
 * Returns a particular value for the named variable, taken from
 * POST or GET, otherwise returning a given default.
 *
 * This function should be used to initialise all optional values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $name = optional_param('name', 'Fred', PARAM_TEXT);
 *
 * Please note the $type parameter is now required and the value can not be array.
 *
 * @param string $parname the name of the page parameter we want
 * @param mixed  $default the default value to return if nothing is found
 * @param string $type expected type of parameter
 * @return mixed
 * @throws coding_exception
 */
function optional_param($parname, $default, $type) {
    if (func_num_args() != 3 or empty($parname) or empty($type)) {
        throw new coding_exception('optional_param requires $parname, $default + $type to be specified (parameter: '.$parname.')');
    }

    // POST has precedence.
    if (isset($_POST[$parname])) {
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        return $default;
    }

    if (is_array($param)) {
        debugging('Invalid array parameter detected in required_param(): '.$parname);
        // TODO: switch to $default in Moodle 2.3.
        return optional_param_array($parname, $default, $type);
    }

    return clean_param($param, $type);
}

/**
 * Returns a particular array value for the named variable, taken from
 * POST or GET, otherwise returning a given default.
 *
 * This function should be used to initialise all optional values
 * in a script that are based on parameters.  Usually it will be
 * used like this:
 *    $ids = optional_param('id', array(), PARAM_INT);
 *
 * Note: arrays of arrays are not supported, only alphanumeric keys with _ and - are supported
 *
 * @param string $parname the name of the page parameter we want
 * @param mixed $default the default value to return if nothing is found
 * @param string $type expected type of parameter
 * @return array
 * @throws coding_exception
 */
function optional_param_array($parname, $default, $type) {
    if (func_num_args() != 3 or empty($parname) or empty($type)) {
        throw new coding_exception('optional_param_array requires $parname, $default + $type to be specified (parameter: '.$parname.')');
    }

    // POST has precedence.
    if (isset($_POST[$parname])) {
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        return $default;
    }
    if (!is_array($param)) {
        debugging('optional_param_array() expects array parameters only: '.$parname);
        return $default;
    }

    $result = array();
    foreach ($param as $key => $value) {
        if (!preg_match('/^[a-z0-9_-]+$/i', $key)) {
            debugging('Invalid key name in optional_param_array() detected: '.$key.', parameter: '.$parname);
            continue;
        }
        $result[$key] = clean_param($value, $type);
    }

    return $result;
}

/**
 * Strict validation of parameter values, the values are only converted
 * to requested PHP type. Internally it is using clean_param, the values
 * before and after cleaning must be equal - otherwise
 * an invalid_parameter_exception is thrown.
 * Objects and classes are not accepted.
 *
 * @param mixed $param
 * @param string $type PARAM_ constant
 * @param bool $allownull are nulls valid value?
 * @param string $debuginfo optional debug information
 * @return mixed the $param value converted to PHP type
 * @throws invalid_parameter_exception if $param is not of given type
 */
function validate_param($param, $type, $allownull=NULL_NOT_ALLOWED, $debuginfo='') {
    if (is_null($param)) {
        if ($allownull == NULL_ALLOWED) {
            return null;
        } else {
            throw new invalid_parameter_exception($debuginfo);
        }
    }
    if (is_array($param) or is_object($param)) {
        throw new invalid_parameter_exception($debuginfo);
    }

    $cleaned = clean_param($param, $type);

    if ($type == PARAM_FLOAT) {
        // Do not detect precision loss here.
        if (is_float($param) or is_int($param)) {
            // These always fit.
        } else if (!is_numeric($param) or !preg_match('/^[\+-]?[0-9]*\.?[0-9]*(e[-+]?[0-9]+)?$/i', (string)$param)) {
            throw new invalid_parameter_exception($debuginfo);
        }
    } else if ((string)$param !== (string)$cleaned) {
        // Conversion to string is usually lossless.
        throw new invalid_parameter_exception($debuginfo);
    }

    return $cleaned;
}

/**
 * Makes sure array contains only the allowed types, this function does not validate array key names!
 *
 * <code>
 * $options = clean_param($options, PARAM_INT);
 * </code>
 *
 * @param array|null $param the variable array we are cleaning
 * @param string $type expected format of param after cleaning.
 * @param bool $recursive clean recursive arrays
 * @return array
 * @throws coding_exception
 */
function clean_param_array(?array $param, $type, $recursive = false) {
    // Convert null to empty array.
    $param = (array)$param;
    foreach ($param as $key => $value) {
        if (is_array($value)) {
            if ($recursive) {
                $param[$key] = clean_param_array($value, $type, true);
            } else {
                throw new coding_exception('clean_param_array can not process multidimensional arrays when $recursive is false.');
            }
        } else {
            $param[$key] = clean_param($value, $type);
        }
    }
    return $param;
}

/**
 * Used by {@link optional_param()} and {@link required_param()} to
 * clean the variables and/or cast to specific types, based on
 * an options field.
 * <code>
 * $course->format = clean_param($course->format, PARAM_ALPHA);
 * $selectedgradeitem = clean_param($selectedgradeitem, PARAM_INT);
 * </code>
 *
 * @param mixed $param the variable we are cleaning
 * @param string $type expected format of param after cleaning.
 * @return mixed
 * @throws coding_exception
 */
function clean_param($param, $type) {
    global $CFG;

    if (is_array($param)) {
        throw new coding_exception('clean_param() can not process arrays, please use clean_param_array() instead.');
    } else if (is_object($param)) {
        if (method_exists($param, '__toString')) {
            $param = $param->__toString();
        } else {
            throw new coding_exception('clean_param() can not process objects, please use clean_param_array() instead.');
        }
    }

    switch ($type) {
        case PARAM_RAW:
            // No cleaning at all.
            $param = fix_utf8($param);
            return $param;

        case PARAM_RAW_TRIMMED:
            // No cleaning, but strip leading and trailing whitespace.
            $param = (string)fix_utf8($param);
            return trim($param);

        case PARAM_CLEAN:
            // General HTML cleaning, try to use more specific type if possible this is deprecated!
            // Please use more specific type instead.
            if (is_numeric($param)) {
                return $param;
            }
            $param = fix_utf8($param);
            // Sweep for scripts, etc.
            return clean_text($param);

        case PARAM_CLEANHTML:
            // Clean html fragment.
            $param = (string)fix_utf8($param);
            // Sweep for scripts, etc.
            $param = clean_text($param, FORMAT_HTML);
            return trim($param);

        case PARAM_INT:
            // Convert to integer.
            return (int)$param;

        case PARAM_FLOAT:
            // Convert to float.
            return (float)$param;

        case PARAM_LOCALISEDFLOAT:
            // Convert to float.
            return unformat_float($param, true);

        case PARAM_ALPHA:
            // Remove everything not `a-z`.
            return preg_replace('/[^a-zA-Z]/i', '', (string)$param);

        case PARAM_ALPHAEXT:
            // Remove everything not `a-zA-Z_-` (originally allowed "/" too).
            return preg_replace('/[^a-zA-Z_-]/i', '', (string)$param);

        case PARAM_ALPHANUM:
            // Remove everything not `a-zA-Z0-9`.
            return preg_replace('/[^A-Za-z0-9]/i', '', (string)$param);

        case PARAM_ALPHANUMEXT:
            // Remove everything not `a-zA-Z0-9_-`.
            return preg_replace('/[^A-Za-z0-9_-]/i', '', (string)$param);

        case PARAM_SEQUENCE:
            // Remove everything not `0-9,`.
            return preg_replace('/[^0-9,]/i', '', (string)$param);

        case PARAM_BOOL:
            // Convert to 1 or 0.
            $tempstr = strtolower((string)$param);
            if ($tempstr === 'on' or $tempstr === 'yes' or $tempstr === 'true') {
                $param = 1;
            } else if ($tempstr === 'off' or $tempstr === 'no'  or $tempstr === 'false') {
                $param = 0;
            } else {
                $param = empty($param) ? 0 : 1;
            }
            return $param;

        case PARAM_NOTAGS:
            // Strip all tags.
            $param = fix_utf8($param);
            return strip_tags((string)$param);

        case PARAM_TEXT:
            // Leave only tags needed for multilang.
            $param = fix_utf8($param);
            // If the multilang syntax is not correct we strip all tags because it would break xhtml strict which is required
            // for accessibility standards please note this cleaning does not strip unbalanced '>' for BC compatibility reasons.
            do {
                if (strpos((string)$param, '</lang>') !== false) {
                    // Old and future mutilang syntax.
                    $param = strip_tags($param, '<lang>');
                    if (!preg_match_all('/<.*>/suU', $param, $matches)) {
                        break;
                    }
                    $open = false;
                    foreach ($matches[0] as $match) {
                        if ($match === '</lang>') {
                            if ($open) {
                                $open = false;
                                continue;
                            } else {
                                break 2;
                            }
                        }
                        if (!preg_match('/^<lang lang="[a-zA-Z0-9_-]+"\s*>$/u', $match)) {
                            break 2;
                        } else {
                            $open = true;
                        }
                    }
                    if ($open) {
                        break;
                    }
                    return $param;

                } else if (strpos((string)$param, '</span>') !== false) {
                    // Current problematic multilang syntax.
                    $param = strip_tags($param, '<span>');
                    if (!preg_match_all('/<.*>/suU', $param, $matches)) {
                        break;
                    }
                    $open = false;
                    foreach ($matches[0] as $match) {
                        if ($match === '</span>') {
                            if ($open) {
                                $open = false;
                                continue;
                            } else {
                                break 2;
                            }
                        }
                        if (!preg_match('/^<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>$/u', $match)) {
                            break 2;
                        } else {
                            $open = true;
                        }
                    }
                    if ($open) {
                        break;
                    }
                    return $param;
                }
            } while (false);
            // Easy, just strip all tags, if we ever want to fix orphaned '&' we have to do that in format_string().
            return strip_tags((string)$param);

        case PARAM_COMPONENT:
            // We do not want any guessing here, either the name is correct or not
            // please note only normalised component names are accepted.
            $param = (string)$param;
            if (!preg_match('/^[a-z][a-z0-9]*(_[a-z][a-z0-9_]*)?[a-z0-9]+$/', $param)) {
                return '';
            }
            if (strpos($param, '__') !== false) {
                return '';
            }
            if (strpos($param, 'mod_') === 0) {
                // Module names must not contain underscores because we need to differentiate them from invalid plugin types.
                if (substr_count($param, '_') != 1) {
                    return '';
                }
            }
            return $param;

        case PARAM_PLUGIN:
        case PARAM_AREA:
            // We do not want any guessing here, either the name is correct or not.
            if (!is_valid_plugin_name($param)) {
                return '';
            }
            return $param;

        case PARAM_SAFEDIR:
            // Remove everything not a-zA-Z0-9_- .
            return preg_replace('/[^a-zA-Z0-9_-]/i', '', (string)$param);

        case PARAM_SAFEPATH:
            // Remove everything not a-zA-Z0-9/_- .
            return preg_replace('/[^a-zA-Z0-9\/_-]/i', '', (string)$param);

        case PARAM_FILE:
            // Strip all suspicious characters from filename.
            $param = (string)fix_utf8($param);
            $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':\\\\/]~u', '', $param);
            if ($param === '.' || $param === '..') {
                $param = '';
            }
            return $param;

        case PARAM_PATH:
            // Strip all suspicious characters from file path.
            $param = (string)fix_utf8($param);
            $param = str_replace('\\', '/', $param);

            // Explode the path and clean each element using the PARAM_FILE rules.
            $breadcrumb = explode('/', $param);
            foreach ($breadcrumb as $key => $crumb) {
                if ($crumb === '.' && $key === 0) {
                    // Special condition to allow for relative current path such as ./currentdirfile.txt.
                } else {
                    $crumb = clean_param($crumb, PARAM_FILE);
                }
                $breadcrumb[$key] = $crumb;
            }
            $param = implode('/', $breadcrumb);

            // Remove multiple current path (./././) and multiple slashes (///).
            $param = preg_replace('~//+~', '/', $param);
            $param = preg_replace('~/(\./)+~', '/', $param);
            return $param;

        case PARAM_HOST:
            // Allow FQDN or IPv4 dotted quad.
            $param = preg_replace('/[^\.\d\w-]/', '', (string)$param );
            // Match ipv4 dotted quad.
            if (preg_match('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/', $param, $match)) {
                // Confirm values are ok.
                if ( $match[0] > 255
                     || $match[1] > 255
                     || $match[3] > 255
                     || $match[4] > 255 ) {
                    // Hmmm, what kind of dotted quad is this?
                    $param = '';
                }
            } else if ( preg_match('/^[\w\d\.-]+$/', $param) // Dots, hyphens, numbers.
                       && !preg_match('/^[\.-]/',  $param) // No leading dots/hyphens.
                       && !preg_match('/[\.-]$/',  $param) // No trailing dots/hyphens.
                       ) {
                // All is ok - $param is respected.
            } else {
                // All is not ok...
                $param='';
            }
            return $param;

        case PARAM_URL:
            // Allow safe urls.
            $param = (string)fix_utf8($param);
            include_once($CFG->dirroot . '/lib/validateurlsyntax.php');
            if (!empty($param) && validateUrlSyntax($param, 's?H?S?F?E-u-P-a?I?p?f?q?r?')) {
                // All is ok, param is respected.
            } else {
                // Not really ok.
                $param ='';
            }
            return $param;

        case PARAM_LOCALURL:
            // Allow http absolute, root relative and relative URLs within wwwroot.
            $param = clean_param($param, PARAM_URL);
            if (!empty($param)) {

                if ($param === $CFG->wwwroot) {
                    // Exact match;
                } else if (preg_match(':^/:', $param)) {
                    // Root-relative, ok!
                } else if (preg_match('/^' . preg_quote($CFG->wwwroot . '/', '/') . '/i', $param)) {
                    // Absolute, and matches our wwwroot.
                } else {

                    // Relative - let's make sure there are no tricks.
                    if (validateUrlSyntax('/' . $param, 's-u-P-a-p-f+q?r?') && !preg_match('/javascript:/i', $param)) {
                        // Looks ok.
                    } else {
                        $param = '';
                    }
                }
            }
            return $param;

        case PARAM_PEM:
            $param = trim((string)$param);
            // PEM formatted strings may contain letters/numbers and the symbols:
            //   forward slash: /
            //   plus sign:     +
            //   equal sign:    =
            //   , surrounded by BEGIN and END CERTIFICATE prefix and suffixes.
            if (preg_match('/^-----BEGIN CERTIFICATE-----([\s\w\/\+=]+)-----END CERTIFICATE-----$/', trim($param), $matches)) {
                list($wholething, $body) = $matches;
                unset($wholething, $matches);
                $b64 = clean_param($body, PARAM_BASE64);
                if (!empty($b64)) {
                    return "-----BEGIN CERTIFICATE-----\n$b64\n-----END CERTIFICATE-----\n";
                } else {
                    return '';
                }
            }
            return '';

        case PARAM_BASE64:
            if (!empty($param)) {
                // PEM formatted strings may contain letters/numbers and the symbols
                //   forward slash: /
                //   plus sign:     +
                //   equal sign:    =.
                if (0 >= preg_match('/^([\s\w\/\+=]+)$/', trim($param))) {
                    return '';
                }
                $lines = preg_split('/[\s]+/', $param, -1, PREG_SPLIT_NO_EMPTY);
                // Each line of base64 encoded data must be 64 characters in length, except for the last line which may be less
                // than (or equal to) 64 characters long.
                for ($i=0, $j=count($lines); $i < $j; $i++) {
                    if ($i + 1 == $j) {
                        if (64 < strlen($lines[$i])) {
                            return '';
                        }
                        continue;
                    }

                    if (64 != strlen($lines[$i])) {
                        return '';
                    }
                }
                return implode("\n", $lines);
            } else {
                return '';
            }

        case PARAM_TAG:
            $param = (string)fix_utf8($param);
            // Please note it is not safe to use the tag name directly anywhere,
            // it must be processed with s(), urlencode() before embedding anywhere.
            // Remove some nasties.
            $param = preg_replace('~[[:cntrl:]]|[<>`]~u', '', $param);
            // Convert many whitespace chars into one.
            $param = preg_replace('/\s+/u', ' ', $param);
            $param = core_text::substr(trim($param), 0, TAG_MAX_LENGTH);
            return $param;

        case PARAM_TAGLIST:
            $param = (string)fix_utf8($param);
            $tags = explode(',', $param);
            $result = array();
            foreach ($tags as $tag) {
                $res = clean_param($tag, PARAM_TAG);
                if ($res !== '') {
                    $result[] = $res;
                }
            }
            if ($result) {
                return implode(',', $result);
            } else {
                return '';
            }

        case PARAM_CAPABILITY:
            if (get_capability_info($param)) {
                return $param;
            } else {
                return '';
            }

        case PARAM_PERMISSION:
            $param = (int)$param;
            if (in_array($param, array(CAP_INHERIT, CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT))) {
                return $param;
            } else {
                return CAP_INHERIT;
            }

        case PARAM_AUTH:
            $param = clean_param($param, PARAM_PLUGIN);
            if (empty($param)) {
                return '';
            } else if (exists_auth_plugin($param)) {
                return $param;
            } else {
                return '';
            }

        case PARAM_LANG:
            $param = clean_param($param, PARAM_SAFEDIR);
            if (get_string_manager()->translation_exists($param)) {
                return $param;
            } else {
                // Specified language is not installed or param malformed.
                return '';
            }

        case PARAM_THEME:
            $param = clean_param($param, PARAM_PLUGIN);
            if (empty($param)) {
                return '';
            } else if (file_exists("$CFG->dirroot/theme/$param/config.php")) {
                return $param;
            } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$param/config.php")) {
                return $param;
            } else {
                // Specified theme is not installed.
                return '';
            }

        case PARAM_USERNAME:
            $param = (string)fix_utf8($param);
            $param = trim($param);
            // Convert uppercase to lowercase MDL-16919.
            $param = core_text::strtolower($param);
            if (empty($CFG->extendedusernamechars)) {
                $param = str_replace(" " , "", $param);
                // Regular expression, eliminate all chars EXCEPT:
                // alphanum, dash (-), underscore (_), at sign (@) and period (.) characters.
                $param = preg_replace('/[^-\.@_a-z0-9]/', '', $param);
            }
            return $param;

        case PARAM_EMAIL:
            $param = fix_utf8($param);
            if (validate_email($param ?? '')) {
                return $param;
            } else {
                return '';
            }

        case PARAM_STRINGID:
            if (preg_match('|^[a-zA-Z][a-zA-Z0-9\.:/_-]*$|', (string)$param)) {
                return $param;
            } else {
                return '';
            }

        case PARAM_TIMEZONE:
            // Can be int, float(with .5 or .0) or string seperated by '/' and can have '-_'.
            $param = (string)fix_utf8($param);
            $timezonepattern = '/^(([+-]?(0?[0-9](\.[5|0])?|1[0-3](\.0)?|1[0-2]\.5))|(99)|[[:alnum:]]+(\/?[[:alpha:]_-])+)$/';
            if (preg_match($timezonepattern, $param)) {
                return $param;
            } else {
                return '';
            }

        default:
            // Doh! throw error, switched parameters in optional_param or another serious problem.
            throw new \moodle_exception("unknownparamtype", '', '', $type);
    }
}

/**
 * Whether the PARAM_* type is compatible in RTL.
 *
 * Being compatible with RTL means that the data they contain can flow
 * from right-to-left or left-to-right without compromising the user experience.
 *
 * Take URLs for example, they are not RTL compatible as they should always
 * flow from the left to the right. This also applies to numbers, email addresses,
 * configuration snippets, base64 strings, etc...
 *
 * This function tries to best guess which parameters can contain localised strings.
 *
 * @param string $paramtype Constant PARAM_*.
 * @return bool
 */
function is_rtl_compatible($paramtype) {
    return $paramtype == PARAM_TEXT || $paramtype == PARAM_NOTAGS;
}

/**
 * Makes sure the data is using valid utf8, invalid characters are discarded.
 *
 * Note: this function is not intended for full objects with methods and private properties.
 *
 * @param mixed $value
 * @return mixed with proper utf-8 encoding
 */
function fix_utf8($value) {
    if (is_null($value) or $value === '') {
        return $value;

    } else if (is_string($value)) {
        if ((string)(int)$value === $value) {
            // Shortcut.
            return $value;
        }
        // No null bytes expected in our data, so let's remove it.
        $value = str_replace("\0", '', $value);

        // Note: this duplicates min_fix_utf8() intentionally.
        static $buggyiconv = null;
        if ($buggyiconv === null) {
            $buggyiconv = (!function_exists('iconv') or @iconv('UTF-8', 'UTF-8//IGNORE', '100'.chr(130).'€') !== '100€');
        }

        if ($buggyiconv) {
            if (function_exists('mb_convert_encoding')) {
                $subst = mb_substitute_character();
                mb_substitute_character('none');
                $result = mb_convert_encoding($value, 'utf-8', 'utf-8');
                mb_substitute_character($subst);

            } else {
                // Warn admins on admin/index.php page.
                $result = $value;
            }

        } else {
            $result = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
        }

        return $result;

    } else if (is_array($value)) {
        foreach ($value as $k => $v) {
            $value[$k] = fix_utf8($v);
        }
        return $value;

    } else if (is_object($value)) {
        // Do not modify original.
        $value = clone($value);
        foreach ($value as $k => $v) {
            $value->$k = fix_utf8($v);
        }
        return $value;

    } else {
        // This is some other type, no utf-8 here.
        return $value;
    }
}

/**
 * Return true if given value is integer or string with integer value
 *
 * @param mixed $value String or Int
 * @return bool true if number, false if not
 */
function is_number($value) {
    if (is_int($value)) {
        return true;
    } else if (is_string($value)) {
        return ((string)(int)$value) === $value;
    } else {
        return false;
    }
}

/**
 * Returns host part from url.
 *
 * @param string $url full url
 * @return string host, null if not found
 */
function get_host_from_url($url) {
    preg_match('|^[a-z]+://([a-zA-Z0-9-.]+)|i', $url, $matches);
    if ($matches) {
        return $matches[1];
    }
    return null;
}

/**
 * Tests whether anything was returned by text editor
 *
 * This function is useful for testing whether something you got back from
 * the HTML editor actually contains anything. Sometimes the HTML editor
 * appear to be empty, but actually you get back a <br> tag or something.
 *
 * @param string $string a string containing HTML.
 * @return boolean does the string contain any actual content - that is text,
 * images, objects, etc.
 */
function html_is_blank($string) {
    return trim(strip_tags((string)$string, '<img><object><applet><input><select><textarea><hr>')) == '';
}

/**
 * Set a key in global configuration
 *
 * Set a key/value pair in both this session's {@link $CFG} global variable
 * and in the 'config' database table for future sessions.
 *
 * Can also be used to update keys for plugin-scoped configs in config_plugin table.
 * In that case it doesn't affect $CFG.
 *
 * A NULL value will delete the entry.
 *
 * NOTE: this function is called from lib/db/upgrade.php
 *
 * @param string $name the key to set
 * @param string $value the value to set (without magic quotes)
 * @param string $plugin (optional) the plugin scope, default null
 * @return bool true or exception
 */
function set_config($name, $value, $plugin = null) {
    global $CFG, $DB;

    // Redirect to appropriate handler when value is null.
    if ($value === null) {
        return unset_config($name, $plugin);
    }

    // Set variables determining conditions and where to store the new config.
    // Plugin config goes to {config_plugins}, core config goes to {config}.
    $iscore = empty($plugin);
    if ($iscore) {
        // If it's for core config.
        $table = 'config';
        $conditions = ['name' => $name];
        $invalidatecachekey = 'core';
    } else {
        // If it's a plugin.
        $table = 'config_plugins';
        $conditions = ['name' => $name, 'plugin' => $plugin];
        $invalidatecachekey = $plugin;
    }

    // DB handling - checks for existing config, updating or inserting only if necessary.
    $invalidatecache = true;
    $inserted = false;
    $record = $DB->get_record($table, $conditions, 'id, value');
    if ($record === false) {
        // Inserts a new config record.
        $config = new stdClass();
        $config->name  = $name;
        $config->value = $value;
        if (!$iscore) {
            $config->plugin = $plugin;
        }
        $inserted = $DB->insert_record($table, $config, false);
    } else if ($invalidatecache = ($record->value !== $value)) {
        // Record exists - Check and only set new value if it has changed.
        $DB->set_field($table, 'value', $value, ['id' => $record->id]);
    }

    if ($iscore && !isset($CFG->config_php_settings[$name])) {
        // So it's defined for this invocation at least.
        // Settings from db are always strings.
        $CFG->$name = (string) $value;
    }

    // When setting config during a Behat test (in the CLI script, not in the web browser
    // requests), remember which ones are set so that we can clear them later.
    if ($iscore && $inserted && defined('BEHAT_TEST')) {
        $CFG->behat_cli_added_config[$name] = true;
    }

    // Update siteidentifier cache, if required.
    if ($iscore && $name === 'siteidentifier') {
        cache_helper::update_site_identifier($value);
    }

    // Invalidate cache, if required.
    if ($invalidatecache) {
        cache_helper::invalidate_by_definition('core', 'config', [], $invalidatecachekey);
    }

    return true;
}

/**
 * Get configuration values from the global config table
 * or the config_plugins table.
 *
 * If called with one parameter, it will load all the config
 * variables for one plugin, and return them as an object.
 *
 * If called with 2 parameters it will return a string single
 * value or false if the value is not found.
 *
 * NOTE: this function is called from lib/db/upgrade.php
 *
 * @param string $plugin full component name
 * @param string $name default null
 * @return mixed hash-like object or single value, return false no config found
 * @throws dml_exception
 */
function get_config($plugin, $name = null) {
    global $CFG, $DB;

    if ($plugin === 'moodle' || $plugin === 'core' || empty($plugin)) {
        $forced =& $CFG->config_php_settings;
        $iscore = true;
        $plugin = 'core';
    } else {
        if (array_key_exists($plugin, $CFG->forced_plugin_settings)) {
            $forced =& $CFG->forced_plugin_settings[$plugin];
        } else {
            $forced = array();
        }
        $iscore = false;
    }

    if (!isset($CFG->siteidentifier)) {
        try {
            // This may throw an exception during installation, which is how we detect the
            // need to install the database. For more details see {@see initialise_cfg()}.
            $CFG->siteidentifier = $DB->get_field('config', 'value', array('name' => 'siteidentifier'));
        } catch (dml_exception $ex) {
            // Set siteidentifier to false. We don't want to trip this continually.
            $siteidentifier = false;
            throw $ex;
        }
    }

    if (!empty($name)) {
        if (array_key_exists($name, $forced)) {
            return (string)$forced[$name];
        } else if ($name === 'siteidentifier' && $plugin == 'core') {
            return $CFG->siteidentifier;
        }
    }

    $cache = cache::make('core', 'config');
    $result = $cache->get($plugin);
    if ($result === false) {
        // The user is after a recordset.
        if (!$iscore) {
            $result = $DB->get_records_menu('config_plugins', array('plugin' => $plugin), '', 'name,value');
        } else {
            // This part is not really used any more, but anyway...
            $result = $DB->get_records_menu('config', array(), '', 'name,value');;
        }
        $cache->set($plugin, $result);
    }

    if (!empty($name)) {
        if (array_key_exists($name, $result)) {
            return $result[$name];
        }
        return false;
    }

    if ($plugin === 'core') {
        $result['siteidentifier'] = $CFG->siteidentifier;
    }

    foreach ($forced as $key => $value) {
        if (is_null($value) or is_array($value) or is_object($value)) {
            // We do not want any extra mess here, just real settings that could be saved in db.
            unset($result[$key]);
        } else {
            // Convert to string as if it went through the DB.
            $result[$key] = (string)$value;
        }
    }

    return (object)$result;
}

/**
 * Removes a key from global configuration.
 *
 * NOTE: this function is called from lib/db/upgrade.php
 *
 * @param string $name the key to set
 * @param string $plugin (optional) the plugin scope
 * @return boolean whether the operation succeeded.
 */
function unset_config($name, $plugin=null) {
    global $CFG, $DB;

    if (empty($plugin)) {
        unset($CFG->$name);
        $DB->delete_records('config', array('name' => $name));
        cache_helper::invalidate_by_definition('core', 'config', array(), 'core');
    } else {
        $DB->delete_records('config_plugins', array('name' => $name, 'plugin' => $plugin));
        cache_helper::invalidate_by_definition('core', 'config', array(), $plugin);
    }

    return true;
}

/**
 * Remove all the config variables for a given plugin.
 *
 * NOTE: this function is called from lib/db/upgrade.php
 *
 * @param string $plugin a plugin, for example 'quiz' or 'qtype_multichoice';
 * @return boolean whether the operation succeeded.
 */
function unset_all_config_for_plugin($plugin) {
    global $DB;
    // Delete from the obvious config_plugins first.
    $DB->delete_records('config_plugins', array('plugin' => $plugin));
    // Next delete any suspect settings from config.
    $like = $DB->sql_like('name', '?', true, true, false, '|');
    $params = array($DB->sql_like_escape($plugin.'_', '|') . '%');
    $DB->delete_records_select('config', $like, $params);
    // Finally clear both the plugin cache and the core cache (suspect settings now removed from core).
    cache_helper::invalidate_by_definition('core', 'config', array(), array('core', $plugin));

    return true;
}

/**
 * Use this function to get a list of users from a config setting of type admin_setting_users_with_capability.
 *
 * All users are verified if they still have the necessary capability.
 *
 * @param string $value the value of the config setting.
 * @param string $capability the capability - must match the one passed to the admin_setting_users_with_capability constructor.
 * @param bool $includeadmins include administrators.
 * @return array of user objects.
 */
function get_users_from_config($value, $capability, $includeadmins = true) {
    if (empty($value) or $value === '$@NONE@$') {
        return array();
    }

    // We have to make sure that users still have the necessary capability,
    // it should be faster to fetch them all first and then test if they are present
    // instead of validating them one-by-one.
    $users = get_users_by_capability(context_system::instance(), $capability);
    if ($includeadmins) {
        $admins = get_admins();
        foreach ($admins as $admin) {
            $users[$admin->id] = $admin;
        }
    }

    if ($value === '$@ALL@$') {
        return $users;
    }

    $result = array(); // Result in correct order.
    $allowed = explode(',', $value);
    foreach ($allowed as $uid) {
        if (isset($users[$uid])) {
            $user = $users[$uid];
            $result[$user->id] = $user;
        }
    }

    return $result;
}


/**
 * Invalidates browser caches and cached data in temp.
 *
 * @return void
 */
function purge_all_caches() {
    purge_caches();
}

/**
 * Selectively invalidate different types of cache.
 *
 * Purges the cache areas specified.  By default, this will purge all caches but can selectively purge specific
 * areas alone or in combination.
 *
 * @param bool[] $options Specific parts of the cache to purge. Valid options are:
 *        'muc'    Purge MUC caches?
 *        'theme'  Purge theme cache?
 *        'lang'   Purge language string cache?
 *        'js'     Purge javascript cache?
 *        'filter' Purge text filter cache?
 *        'other'  Purge all other caches?
 */
function purge_caches($options = []) {
    $defaults = array_fill_keys(['muc', 'theme', 'lang', 'js', 'template', 'filter', 'other'], false);
    if (empty(array_filter($options))) {
        $options = array_fill_keys(array_keys($defaults), true); // Set all options to true.
    } else {
        $options = array_merge($defaults, array_intersect_key($options, $defaults)); // Override defaults with specified options.
    }
    if ($options['muc']) {
        cache_helper::purge_all();
    }
    if ($options['theme']) {
        theme_reset_all_caches();
    }
    if ($options['lang']) {
        get_string_manager()->reset_caches();
    }
    if ($options['js']) {
        js_reset_all_caches();
    }
    if ($options['template']) {
        template_reset_all_caches();
    }
    if ($options['filter']) {
        reset_text_filters_cache();
    }
    if ($options['other']) {
        purge_other_caches();
    }
}

/**
 * Purge all non-MUC caches not otherwise purged in purge_caches.
 *
 * IMPORTANT - If you are adding anything here to do with the cache directory you should also have a look at
 * {@link phpunit_util::reset_dataroot()}
 */
function purge_other_caches() {
    global $DB, $CFG;
    if (class_exists('core_plugin_manager')) {
        core_plugin_manager::reset_caches();
    }

    // Bump up cacherev field for all courses.
    try {
        increment_revision_number('course', 'cacherev', '');
    } catch (moodle_exception $e) {
        // Ignore exception since this function is also called before upgrade script when field course.cacherev does not exist yet.
    }

    $DB->reset_caches();

    // Purge all other caches: rss, simplepie, etc.
    clearstatcache();
    remove_dir($CFG->cachedir.'', true);

    // Make sure cache dir is writable, throws exception if not.
    make_cache_directory('');

    // This is the only place where we purge local caches, we are only adding files there.
    // The $CFG->localcachedirpurged flag forces local directories to be purged on cluster nodes.
    remove_dir($CFG->localcachedir, true);
    set_config('localcachedirpurged', time());
    make_localcache_directory('', true);
    \core\task\manager::clear_static_caches();
}

/**
 * Get volatile flags
 *
 * @param string $type
 * @param int $changedsince default null
 * @return array records array
 */
function get_cache_flags($type, $changedsince = null) {
    global $DB;

    $params = array('type' => $type, 'expiry' => time());
    $sqlwhere = "flagtype = :type AND expiry >= :expiry";
    if ($changedsince !== null) {
        $params['changedsince'] = $changedsince;
        $sqlwhere .= " AND timemodified > :changedsince";
    }
    $cf = array();
    if ($flags = $DB->get_records_select('cache_flags', $sqlwhere, $params, '', 'name,value')) {
        foreach ($flags as $flag) {
            $cf[$flag->name] = $flag->value;
        }
    }
    return $cf;
}

/**
 * Get volatile flags
 *
 * @param string $type
 * @param string $name
 * @param int $changedsince default null
 * @return string|false The cache flag value or false
 */
function get_cache_flag($type, $name, $changedsince=null) {
    global $DB;

    $params = array('type' => $type, 'name' => $name, 'expiry' => time());

    $sqlwhere = "flagtype = :type AND name = :name AND expiry >= :expiry";
    if ($changedsince !== null) {
        $params['changedsince'] = $changedsince;
        $sqlwhere .= " AND timemodified > :changedsince";
    }

    return $DB->get_field_select('cache_flags', 'value', $sqlwhere, $params);
}

/**
 * Set a volatile flag
 *
 * @param string $type the "type" namespace for the key
 * @param string $name the key to set
 * @param string $value the value to set (without magic quotes) - null will remove the flag
 * @param int $expiry (optional) epoch indicating expiry - defaults to now()+ 24hs
 * @return bool Always returns true
 */
function set_cache_flag($type, $name, $value, $expiry = null) {
    global $DB;

    $timemodified = time();
    if ($expiry === null || $expiry < $timemodified) {
        $expiry = $timemodified + 24 * 60 * 60;
    } else {
        $expiry = (int)$expiry;
    }

    if ($value === null) {
        unset_cache_flag($type, $name);
        return true;
    }

    if ($f = $DB->get_record('cache_flags', array('name' => $name, 'flagtype' => $type), '*', IGNORE_MULTIPLE)) {
        // This is a potential problem in DEBUG_DEVELOPER.
        if ($f->value == $value and $f->expiry == $expiry and $f->timemodified == $timemodified) {
            return true; // No need to update.
        }
        $f->value        = $value;
        $f->expiry       = $expiry;
        $f->timemodified = $timemodified;
        $DB->update_record('cache_flags', $f);
    } else {
        $f = new stdClass();
        $f->flagtype     = $type;
        $f->name         = $name;
        $f->value        = $value;
        $f->expiry       = $expiry;
        $f->timemodified = $timemodified;
        $DB->insert_record('cache_flags', $f);
    }
    return true;
}

/**
 * Removes a single volatile flag
 *
 * @param string $type the "type" namespace for the key
 * @param string $name the key to set
 * @return bool
 */
function unset_cache_flag($type, $name) {
    global $DB;
    $DB->delete_records('cache_flags', array('name' => $name, 'flagtype' => $type));
    return true;
}

/**
 * Garbage-collect volatile flags
 *
 * @return bool Always returns true
 */
function gc_cache_flags() {
    global $DB;
    $DB->delete_records_select('cache_flags', 'expiry < ?', array(time()));
    return true;
}

// USER PREFERENCE API.

/**
 * Refresh user preference cache. This is used most often for $USER
 * object that is stored in session, but it also helps with performance in cron script.
 *
 * Preferences for each user are loaded on first use on every page, then again after the timeout expires.
 *
 * @package  core
 * @category preference
 * @access   public
 * @param    stdClass         $user          User object. Preferences are preloaded into 'preference' property
 * @param    int              $cachelifetime Cache life time on the current page (in seconds)
 * @throws   coding_exception
 * @return   null
 */
function check_user_preferences_loaded(stdClass $user, $cachelifetime = 120) {
    global $DB;
    // Static cache, we need to check on each page load, not only every 2 minutes.
    static $loadedusers = array();

    if (!isset($user->id)) {
        throw new coding_exception('Invalid $user parameter in check_user_preferences_loaded() call, missing id field');
    }

    if (empty($user->id) or isguestuser($user->id)) {
        // No permanent storage for not-logged-in users and guest.
        if (!isset($user->preference)) {
            $user->preference = array();
        }
        return;
    }

    $timenow = time();

    if (isset($loadedusers[$user->id]) and isset($user->preference) and isset($user->preference['_lastloaded'])) {
        // Already loaded at least once on this page. Are we up to date?
        if ($user->preference['_lastloaded'] + $cachelifetime > $timenow) {
            // No need to reload - we are on the same page and we loaded prefs just a moment ago.
            return;

        } else if (!get_cache_flag('userpreferenceschanged', $user->id, $user->preference['_lastloaded'])) {
            // No change since the lastcheck on this page.
            $user->preference['_lastloaded'] = $timenow;
            return;
        }
    }

    // OK, so we have to reload all preferences.
    $loadedusers[$user->id] = true;
    $user->preference = $DB->get_records_menu('user_preferences', array('userid' => $user->id), '', 'name,value'); // All values.
    $user->preference['_lastloaded'] = $timenow;
}

/**
 * Called from set/unset_user_preferences, so that the prefs can be correctly reloaded in different sessions.
 *
 * NOTE: internal function, do not call from other code.
 *
 * @package core
 * @access private
 * @param integer $userid the user whose prefs were changed.
 */
function mark_user_preferences_changed($userid) {
    global $CFG;

    if (empty($userid) or isguestuser($userid)) {
        // No cache flags for guest and not-logged-in users.
        return;
    }

    set_cache_flag('userpreferenceschanged', $userid, 1, time() + $CFG->sessiontimeout);
}

/**
 * Sets a preference for the specified user.
 *
 * If a $user object is submitted it's 'preference' property is used for the preferences cache.
 *
 * When additional validation/permission check is needed it is better to use {@see useredit_update_user_preference()}
 *
 * @package  core
 * @category preference
 * @access   public
 * @param    string            $name  The key to set as preference for the specified user
 * @param    string            $value The value to set for the $name key in the specified user's
 *                                    record, null means delete current value.
 * @param    stdClass|int|null $user  A moodle user object or id, null means current user
 * @throws   coding_exception
 * @return   bool                     Always true or exception
 */
function set_user_preference($name, $value, $user = null) {
    global $USER, $DB;

    if (empty($name) or is_numeric($name) or $name === '_lastloaded') {
        throw new coding_exception('Invalid preference name in set_user_preference() call');
    }

    if (is_null($value)) {
        // Null means delete current.
        return unset_user_preference($name, $user);
    } else if (is_object($value)) {
        throw new coding_exception('Invalid value in set_user_preference() call, objects are not allowed');
    } else if (is_array($value)) {
        throw new coding_exception('Invalid value in set_user_preference() call, arrays are not allowed');
    }
    // Value column maximum length is 1333 characters.
    $value = (string)$value;
    if (core_text::strlen($value) > 1333) {
        throw new coding_exception('Invalid value in set_user_preference() call, value is is too long for the value column');
    }

    if (is_null($user)) {
        $user = $USER;
    } else if (isset($user->id)) {
        // It is a valid object.
    } else if (is_numeric($user)) {
        $user = (object)array('id' => (int)$user);
    } else {
        throw new coding_exception('Invalid $user parameter in set_user_preference() call');
    }

    check_user_preferences_loaded($user);

    if (empty($user->id) or isguestuser($user->id)) {
        // No permanent storage for not-logged-in users and guest.
        $user->preference[$name] = $value;
        return true;
    }

    if ($preference = $DB->get_record('user_preferences', array('userid' => $user->id, 'name' => $name))) {
        if ($preference->value === $value and isset($user->preference[$name]) and $user->preference[$name] === $value) {
            // Preference already set to this value.
            return true;
        }
        $DB->set_field('user_preferences', 'value', $value, array('id' => $preference->id));

    } else {
        $preference = new stdClass();
        $preference->userid = $user->id;
        $preference->name   = $name;
        $preference->value  = $value;
        $DB->insert_record('user_preferences', $preference);
    }

    // Update value in cache.
    $user->preference[$name] = $value;
    // Update the $USER in case where we've not a direct reference to $USER.
    if ($user !== $USER && $user->id == $USER->id) {
        $USER->preference[$name] = $value;
    }

    // Set reload flag for other sessions.
    mark_user_preferences_changed($user->id);

    return true;
}

/**
 * Sets a whole array of preferences for the current user
 *
 * If a $user object is submitted it's 'preference' property is used for the preferences cache.
 *
 * @package  core
 * @category preference
 * @access   public
 * @param    array             $prefarray An array of key/value pairs to be set
 * @param    stdClass|int|null $user      A moodle user object or id, null means current user
 * @return   bool                         Always true or exception
 */
function set_user_preferences(array $prefarray, $user = null) {
    foreach ($prefarray as $name => $value) {
        set_user_preference($name, $value, $user);
    }
    return true;
}

/**
 * Unsets a preference completely by deleting it from the database
 *
 * If a $user object is submitted it's 'preference' property is used for the preferences cache.
 *
 * @package  core
 * @category preference
 * @access   public
 * @param    string            $name The key to unset as preference for the specified user
 * @param    stdClass|int|null $user A moodle user object or id, null means current user
 * @throws   coding_exception
 * @return   bool                    Always true or exception
 */
function unset_user_preference($name, $user = null) {
    global $USER, $DB;

    if (empty($name) or is_numeric($name) or $name === '_lastloaded') {
        throw new coding_exception('Invalid preference name in unset_user_preference() call');
    }

    if (is_null($user)) {
        $user = $USER;
    } else if (isset($user->id)) {
        // It is a valid object.
    } else if (is_numeric($user)) {
        $user = (object)array('id' => (int)$user);
    } else {
        throw new coding_exception('Invalid $user parameter in unset_user_preference() call');
    }

    check_user_preferences_loaded($user);

    if (empty($user->id) or isguestuser($user->id)) {
        // No permanent storage for not-logged-in user and guest.
        unset($user->preference[$name]);
        return true;
    }

    // Delete from DB.
    $DB->delete_records('user_preferences', array('userid' => $user->id, 'name' => $name));

    // Delete the preference from cache.
    unset($user->preference[$name]);
    // Update the $USER in case where we've not a direct reference to $USER.
    if ($user !== $USER && $user->id == $USER->id) {
        unset($USER->preference[$name]);
    }

    // Set reload flag for other sessions.
    mark_user_preferences_changed($user->id);

    return true;
}

/**
 * Used to fetch user preference(s)
 *
 * If no arguments are supplied this function will return
 * all of the current user preferences as an array.
 *
 * If a name is specified then this function
 * attempts to return that particular preference value.  If
 * none is found, then the optional value $default is returned,
 * otherwise null.
 *
 * If a $user object is submitted it's 'preference' property is used for the preferences cache.
 *
 * @package  core
 * @category preference
 * @access   public
 * @param    string            $name    Name of the key to use in finding a preference value
 * @param    mixed|null        $default Value to be returned if the $name key is not set in the user preferences
 * @param    stdClass|int|null $user    A moodle user object or id, null means current user
 * @throws   coding_exception
 * @return   string|mixed|null          A string containing the value of a single preference. An
 *                                      array with all of the preferences or null
 */
function get_user_preferences($name = null, $default = null, $user = null) {
    global $USER;

    if (is_null($name)) {
        // All prefs.
    } else if (is_numeric($name) or $name === '_lastloaded') {
        throw new coding_exception('Invalid preference name in get_user_preferences() call');
    }

    if (is_null($user)) {
        $user = $USER;
    } else if (isset($user->id)) {
        // Is a valid object.
    } else if (is_numeric($user)) {
        if ($USER->id == $user) {
            $user = $USER;
        } else {
            $user = (object)array('id' => (int)$user);
        }
    } else {
        throw new coding_exception('Invalid $user parameter in get_user_preferences() call');
    }

    check_user_preferences_loaded($user);

    if (empty($name)) {
        // All values.
        return $user->preference;
    } else if (isset($user->preference[$name])) {
        // The single string value.
        return $user->preference[$name];
    } else {
        // Default value (null if not specified).
        return $default;
    }
}

// FUNCTIONS FOR HANDLING TIME.

/**
 * Given Gregorian date parts in user time produce a GMT timestamp.
 *
 * @package core
 * @category time
 * @param int $year The year part to create timestamp of
 * @param int $month The month part to create timestamp of
 * @param int $day The day part to create timestamp of
 * @param int $hour The hour part to create timestamp of
 * @param int $minute The minute part to create timestamp of
 * @param int $second The second part to create timestamp of
 * @param int|float|string $timezone Timezone modifier, used to calculate GMT time offset.
 *             if 99 then default user's timezone is used {@link http://docs.moodle.org/dev/Time_API#Timezone}
 * @param bool $applydst Toggle Daylight Saving Time, default true, will be
 *             applied only if timezone is 99 or string.
 * @return int GMT timestamp
 */
function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true) {
    $date = new DateTime('now', core_date::get_user_timezone_object($timezone));
    $date->setDate((int)$year, (int)$month, (int)$day);
    $date->setTime((int)$hour, (int)$minute, (int)$second);

    $time = $date->getTimestamp();

    if ($time === false) {
        throw new coding_exception('getTimestamp() returned false, please ensure you have passed correct values.'.
            ' This can fail if year is more than 2038 and OS is 32 bit windows');
    }

    // Moodle BC DST stuff.
    if (!$applydst) {
        $time += dst_offset_on($time, $timezone);
    }

    return $time;

}

/**
 * Format a date/time (seconds) as weeks, days, hours etc as needed
 *
 * Given an amount of time in seconds, returns string
 * formatted nicely as years, days, hours etc as needed
 *
 * @package core
 * @category time
 * @uses MINSECS
 * @uses HOURSECS
 * @uses DAYSECS
 * @uses YEARSECS
 * @param int $totalsecs Time in seconds
 * @param stdClass $str Should be a time object
 * @return string A nicely formatted date/time string
 */
function format_time($totalsecs, $str = null) {

    $totalsecs = abs($totalsecs);

    if (!$str) {
        // Create the str structure the slow way.
        $str = new stdClass();
        $str->day   = get_string('day');
        $str->days  = get_string('days');
        $str->hour  = get_string('hour');
        $str->hours = get_string('hours');
        $str->min   = get_string('min');
        $str->mins  = get_string('mins');
        $str->sec   = get_string('sec');
        $str->secs  = get_string('secs');
        $str->year  = get_string('year');
        $str->years = get_string('years');
    }

    $years     = floor($totalsecs/YEARSECS);
    $remainder = $totalsecs - ($years*YEARSECS);
    $days      = floor($remainder/DAYSECS);
    $remainder = $totalsecs - ($days*DAYSECS);
    $hours     = floor($remainder/HOURSECS);
    $remainder = $remainder - ($hours*HOURSECS);
    $mins      = floor($remainder/MINSECS);
    $secs      = $remainder - ($mins*MINSECS);

    $ss = ($secs == 1)  ? $str->sec  : $str->secs;
    $sm = ($mins == 1)  ? $str->min  : $str->mins;
    $sh = ($hours == 1) ? $str->hour : $str->hours;
    $sd = ($days == 1)  ? $str->day  : $str->days;
    $sy = ($years == 1)  ? $str->year  : $str->years;

    $oyears = '';
    $odays = '';
    $ohours = '';
    $omins = '';
    $osecs = '';

    if ($years) {
        $oyears  = $years .' '. $sy;
    }
    if ($days) {
        $odays  = $days .' '. $sd;
    }
    if ($hours) {
        $ohours = $hours .' '. $sh;
    }
    if ($mins) {
        $omins  = $mins .' '. $sm;
    }
    if ($secs) {
        $osecs  = $secs .' '. $ss;
    }

    if ($years) {
        return trim($oyears .' '. $odays);
    }
    if ($days) {
        return trim($odays .' '. $ohours);
    }
    if ($hours) {
        return trim($ohours .' '. $omins);
    }
    if ($mins) {
        return trim($omins .' '. $osecs);
    }
    if ($secs) {
        return $osecs;
    }
    return get_string('now');
}

/**
 * Returns a formatted string that represents a date in user time.
 *
 * @package core
 * @category time
 * @param int $date the timestamp in UTC, as obtained from the database.
 * @param string $format strftime format. You should probably get this using
 *        get_string('strftime...', 'langconfig');
 * @param int|float|string $timezone by default, uses the user's time zone. if numeric and
 *        not 99 then daylight saving will not be added.
 *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
 * @param bool $fixday If true (default) then the leading zero from %d is removed.
 *        If false then the leading zero is maintained.
 * @param bool $fixhour If true (default) then the leading zero from %I is removed.
 * @return string the formatted date/time.
 */
function userdate($date, $format = '', $timezone = 99, $fixday = true, $fixhour = true) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->timestamp_to_date_string($date, $format, $timezone, $fixday, $fixhour);
}

/**
 * Returns a html "time" tag with both the exact user date with timezone information
 * as a datetime attribute in the W3C format, and the user readable date and time as text.
 *
 * @package core
 * @category time
 * @param int $date the timestamp in UTC, as obtained from the database.
 * @param string $format strftime format. You should probably get this using
 *        get_string('strftime...', 'langconfig');
 * @param int|float|string $timezone by default, uses the user's time zone. if numeric and
 *        not 99 then daylight saving will not be added.
 *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
 * @param bool $fixday If true (default) then the leading zero from %d is removed.
 *        If false then the leading zero is maintained.
 * @param bool $fixhour If true (default) then the leading zero from %I is removed.
 * @return string the formatted date/time.
 */
function userdate_htmltime($date, $format = '', $timezone = 99, $fixday = true, $fixhour = true) {
    $userdatestr = userdate($date, $format, $timezone, $fixday, $fixhour);
    if (CLI_SCRIPT && !PHPUNIT_TEST) {
        return $userdatestr;
    }
    $machinedate = new DateTime();
    $machinedate->setTimestamp(intval($date));
    $machinedate->setTimezone(core_date::get_user_timezone_object());

    return html_writer::tag('time', $userdatestr, ['datetime' => $machinedate->format(DateTime::W3C)]);
}

/**
 * Returns a formatted date ensuring it is UTF-8.
 *
 * If we are running under Windows convert to Windows encoding and then back to UTF-8
 * (because it's impossible to specify UTF-8 to fetch locale info in Win32).
 *
 * @param int $date the timestamp - since Moodle 2.9 this is a real UTC timestamp
 * @param string $format strftime format.
 * @param int|float|string $tz the user timezone
 * @return string the formatted date/time.
 * @since Moodle 2.3.3
 */
function date_format_string($date, $format, $tz = 99) {

    date_default_timezone_set(core_date::get_user_timezone($tz));

    if (date('A', 0) === date('A', HOURSECS * 18)) {
        $datearray = getdate($date);
        $format = str_replace([
            '%P',
            '%p',
        ], [
            $datearray['hours'] < 12 ? get_string('am', 'langconfig') : get_string('pm', 'langconfig'),
            $datearray['hours'] < 12 ? get_string('amcaps', 'langconfig') : get_string('pmcaps', 'langconfig'),
        ], $format);
    }

    $datestring = core_date::strftime($format, $date);
    core_date::set_default_server_timezone();

    return $datestring;
}

/**
 * Given a $time timestamp in GMT (seconds since epoch),
 * returns an array that represents the Gregorian date in user time
 *
 * @package core
 * @category time
 * @param int $time Timestamp in GMT
 * @param float|int|string $timezone user timezone
 * @return array An array that represents the date in user time
 */
function usergetdate($time, $timezone=99) {
    if ($time === null) {
        // PHP8 and PHP7 return different results when getdate(null) is called.
        // Display warning and cast to 0 to make sure the usergetdate() behaves consistently on all versions of PHP.
        // In the future versions of Moodle we may consider adding a strict typehint.
        debugging('usergetdate() expects parameter $time to be int, null given', DEBUG_DEVELOPER);
        $time = 0;
    }

    date_default_timezone_set(core_date::get_user_timezone($timezone));
    $result = getdate($time);
    core_date::set_default_server_timezone();

    return $result;
}

/**
 * Given a GMT timestamp (seconds since epoch), offsets it by
 * the timezone.  eg 3pm in India is 3pm GMT - 7 * 3600 seconds
 *
 * NOTE: this function does not include DST properly,
 *       you should use the PHP date stuff instead!
 *
 * @package core
 * @category time
 * @param int $date Timestamp in GMT
 * @param float|int|string $timezone user timezone
 * @return int
 */
function usertime($date, $timezone=99) {
    $userdate = new DateTime('@' . $date);
    $userdate->setTimezone(core_date::get_user_timezone_object($timezone));
    $dst = dst_offset_on($date, $timezone);

    return $date - $userdate->getOffset() + $dst;
}

/**
 * Get a formatted string representation of an interval between two unix timestamps.
 *
 * E.g.
 * $intervalstring = get_time_interval_string(12345600, 12345660);
 * Will produce the string:
 * '0d 0h 1m'
 *
 * @param int $time1 unix timestamp
 * @param int $time2 unix timestamp
 * @param string $format string (can be lang string) containing format chars: https://www.php.net/manual/en/dateinterval.format.php.
 * @return string the formatted string describing the time difference, e.g. '10d 11h 45m'.
 */
function get_time_interval_string(int $time1, int $time2, string $format = ''): string {
    $dtdate = new DateTime();
    $dtdate->setTimeStamp($time1);
    $dtdate2 = new DateTime();
    $dtdate2->setTimeStamp($time2);
    $interval = $dtdate2->diff($dtdate);
    $format = empty($format) ? get_string('dateintervaldayshoursmins', 'langconfig') : $format;
    return $interval->format($format);
}

/**
 * Given a time, return the GMT timestamp of the most recent midnight
 * for the current user.
 *
 * @package core
 * @category time
 * @param int $date Timestamp in GMT
 * @param float|int|string $timezone user timezone
 * @return int Returns a GMT timestamp
 */
function usergetmidnight($date, $timezone=99) {

    $userdate = usergetdate($date, $timezone);

    // Time of midnight of this user's day, in GMT.
    return make_timestamp($userdate['year'], $userdate['mon'], $userdate['mday'], 0, 0, 0, $timezone);

}

/**
 * Returns a string that prints the user's timezone
 *
 * @package core
 * @category time
 * @param float|int|string $timezone user timezone
 * @return string
 */
function usertimezone($timezone=99) {
    $tz = core_date::get_user_timezone($timezone);
    return core_date::get_localised_timezone($tz);
}

/**
 * Returns a float or a string which denotes the user's timezone
 * A float value means that a simple offset from GMT is used, while a string (it will be the name of a timezone in the database)
 * means that for this timezone there are also DST rules to be taken into account
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @package core
 * @category time
 * @param float|int|string $tz timezone to calculate GMT time offset before
 *        calculating user timezone, 99 is default user timezone
 *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
 * @return float|string
 */
function get_user_timezone($tz = 99) {
    global $USER, $CFG;

    $timezones = array(
        $tz,
        isset($CFG->forcetimezone) ? $CFG->forcetimezone : 99,
        isset($USER->timezone) ? $USER->timezone : 99,
        isset($CFG->timezone) ? $CFG->timezone : 99,
        );

    $tz = 99;

    // Loop while $tz is, empty but not zero, or 99, and there is another timezone is the array.
    foreach ($timezones as $nextvalue) {
        if ((empty($tz) && !is_numeric($tz)) || $tz == 99) {
            $tz = $nextvalue;
        }
    }
    return is_numeric($tz) ? (float) $tz : $tz;
}

/**
 * Calculates the Daylight Saving Offset for a given date/time (timestamp)
 * - Note: Daylight saving only works for string timezones and not for float.
 *
 * @package core
 * @category time
 * @param int $time must NOT be compensated at all, it has to be a pure timestamp
 * @param int|float|string $strtimezone user timezone
 * @return int
 */
function dst_offset_on($time, $strtimezone = null) {
    $tz = core_date::get_user_timezone($strtimezone);
    $date = new DateTime('@' . $time);
    $date->setTimezone(new DateTimeZone($tz));
    if ($date->format('I') == '1') {
        if ($tz === 'Australia/Lord_Howe') {
            return 1800;
        }
        return 3600;
    }
    return 0;
}

/**
 * Calculates when the day appears in specific month
 *
 * @package core
 * @category time
 * @param int $startday starting day of the month
 * @param int $weekday The day when week starts (normally taken from user preferences)
 * @param int $month The month whose day is sought
 * @param int $year The year of the month whose day is sought
 * @return int
 */
function find_day_in_month($startday, $weekday, $month, $year) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();

    $daysinmonth = days_in_month($month, $year);
    $daysinweek = count($calendartype->get_weekdays());

    if ($weekday == -1) {
        // Don't care about weekday, so return:
        //    abs($startday) if $startday != -1
        //    $daysinmonth otherwise.
        return ($startday == -1) ? $daysinmonth : abs($startday);
    }

    // From now on we 're looking for a specific weekday.
    // Give "end of month" its actual value, since we know it.
    if ($startday == -1) {
        $startday = -1 * $daysinmonth;
    }

    // Starting from day $startday, the sign is the direction.
    if ($startday < 1) {
        $startday = abs($startday);
        $lastmonthweekday = dayofweek($daysinmonth, $month, $year);

        // This is the last such weekday of the month.
        $lastinmonth = $daysinmonth + $weekday - $lastmonthweekday;
        if ($lastinmonth > $daysinmonth) {
            $lastinmonth -= $daysinweek;
        }

        // Find the first such weekday <= $startday.
        while ($lastinmonth > $startday) {
            $lastinmonth -= $daysinweek;
        }

        return $lastinmonth;
    } else {
        $indexweekday = dayofweek($startday, $month, $year);

        $diff = $weekday - $indexweekday;
        if ($diff < 0) {
            $diff += $daysinweek;
        }

        // This is the first such weekday of the month equal to or after $startday.
        $firstfromindex = $startday + $diff;

        return $firstfromindex;
    }
}

/**
 * Calculate the number of days in a given month
 *
 * @package core
 * @category time
 * @param int $month The month whose day count is sought
 * @param int $year The year of the month whose day count is sought
 * @return int
 */
function days_in_month($month, $year) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->get_num_days_in_month($year, $month);
}

/**
 * Calculate the position in the week of a specific calendar day
 *
 * @package core
 * @category time
 * @param int $day The day of the date whose position in the week is sought
 * @param int $month The month of the date whose position in the week is sought
 * @param int $year The year of the date whose position in the week is sought
 * @return int
 */
function dayofweek($day, $month, $year) {
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    return $calendartype->get_weekday($year, $month, $day);
}

// USER AUTHENTICATION AND LOGIN.

/**
 * Returns full login url.
 *
 * Any form submissions for authentication to this URL must include username,
 * password as well as a logintoken generated by \core\session\manager::get_login_token().
 *
 * @return string login url
 */
function get_login_url() {
    global $CFG;

    return "$CFG->wwwroot/login/index.php";
}

/**
 * This function checks that the current user is logged in and has the
 * required privileges
 *
 * This function checks that the current user is logged in, and optionally
 * whether they are allowed to be in a particular course and view a particular
 * course module.
 * If they are not logged in, then it redirects them to the site login unless
 * $autologinguest is set and {@link $CFG}->autologinguests is set to 1 in which
 * case they are automatically logged in as guests.
 * If $courseid is given and the user is not enrolled in that course then the
 * user is redirected to the course enrolment page.
 * If $cm is given and the course module is hidden and the user is not a teacher
 * in the course then the user is redirected to the course home page.
 *
 * When $cm parameter specified, this function sets page layout to 'module'.
 * You need to change it manually later if some other layout needed.
 *
 * @package    core_access
 * @category   access
 *
 * @param mixed $courseorid id of the course or course object
 * @param bool $autologinguest default true
 * @param object $cm course module object
 * @param bool $setwantsurltome Define if we want to set $SESSION->wantsurl, defaults to
 *             true. Used to avoid (=false) some scripts (file.php...) to set that variable,
 *             in order to keep redirects working properly. MDL-14495
 * @param bool $preventredirect set to true in scripts that can not redirect (CLI, rss feeds, etc.), throws exceptions
 * @return mixed Void, exit, and die depending on path
 * @throws coding_exception
 * @throws require_login_exception
 * @throws moodle_exception
 */
function require_login($courseorid = null, $autologinguest = true, $cm = null, $setwantsurltome = true, $preventredirect = false) {
    global $CFG, $SESSION, $USER, $PAGE, $SITE, $DB, $OUTPUT;

    // Must not redirect when byteserving already started.
    if (!empty($_SERVER['HTTP_RANGE'])) {
        $preventredirect = true;
    }

    if (AJAX_SCRIPT) {
        // We cannot redirect for AJAX scripts either.
        $preventredirect = true;
    }

    // Setup global $COURSE, themes, language and locale.
    if (!empty($courseorid)) {
        if (is_object($courseorid)) {
            $course = $courseorid;
        } else if ($courseorid == SITEID) {
            $course = clone($SITE);
        } else {
            $course = $DB->get_record('course', array('id' => $courseorid), '*', MUST_EXIST);
        }
        if ($cm) {
            if ($cm->course != $course->id) {
                throw new coding_exception('course and cm parameters in require_login() call do not match!!');
            }
            // Make sure we have a $cm from get_fast_modinfo as this contains activity access details.
            if (!($cm instanceof cm_info)) {
                // Note: nearly all pages call get_fast_modinfo anyway and it does not make any
                // db queries so this is not really a performance concern, however it is obviously
                // better if you use get_fast_modinfo to get the cm before calling this.
                $modinfo = get_fast_modinfo($course);
                $cm = $modinfo->get_cm($cm->id);
            }
        }
    } else {
        // Do not touch global $COURSE via $PAGE->set_course(),
        // the reasons is we need to be able to call require_login() at any time!!
        $course = $SITE;
        if ($cm) {
            throw new coding_exception('cm parameter in require_login() requires valid course parameter!');
        }
    }

    // If this is an AJAX request and $setwantsurltome is true then we need to override it and set it to false.
    // Otherwise the AJAX request URL will be set to $SESSION->wantsurl and events such as self enrolment in the future
    // risk leading the user back to the AJAX request URL.
    if ($setwantsurltome && defined('AJAX_SCRIPT') && AJAX_SCRIPT) {
        $setwantsurltome = false;
    }

    // Redirect to the login page if session has expired, only with dbsessions enabled (MDL-35029) to maintain current behaviour.
    if ((!isloggedin() or isguestuser()) && !empty($SESSION->has_timed_out) && !empty($CFG->dbsessions)) {
        if ($preventredirect) {
            throw new require_login_session_timeout_exception();
        } else {
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            redirect(get_login_url());
        }
    }

    // If the user is not even logged in yet then make sure they are.
    if (!isloggedin()) {
        if ($autologinguest and !empty($CFG->guestloginbutton) and !empty($CFG->autologinguests)) {
            if (!$guest = get_complete_user_data('id', $CFG->siteguest)) {
                // Misconfigured site guest, just redirect to login page.
                redirect(get_login_url());
                exit; // Never reached.
            }
            $lang = isset($SESSION->lang) ? $SESSION->lang : $CFG->lang;
            complete_user_login($guest);
            $USER->autologinguest = true;
            $SESSION->lang = $lang;
        } else {
            // NOTE: $USER->site check was obsoleted by session test cookie, $USER->confirmed test is in login/index.php.
            if ($preventredirect) {
                throw new require_login_exception('You are not logged in');
            }

            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }

            // Give auth plugins an opportunity to authenticate or redirect to an external login page
            $authsequence = get_enabled_auth_plugins(); // Auths, in sequence.
            foreach($authsequence as $authname) {
                $authplugin = get_auth_plugin($authname);
                $authplugin->pre_loginpage_hook();
                if (isloggedin()) {
                    if ($cm) {
                        $modinfo = get_fast_modinfo($course);
                        $cm = $modinfo->get_cm($cm->id);
                    }
                    set_access_log_user();
                    break;
                }
            }

            // If we're still not logged in then go to the login page
            if (!isloggedin()) {
                redirect(get_login_url());
                exit; // Never reached.
            }
        }
    }

    // Loginas as redirection if needed.
    if ($course->id != SITEID and \core\session\manager::is_loggedinas()) {
        if ($USER->loginascontext->contextlevel == CONTEXT_COURSE) {
            if ($USER->loginascontext->instanceid != $course->id) {
                throw new \moodle_exception('loginasonecourse', '',
                    $CFG->wwwroot.'/course/view.php?id='.$USER->loginascontext->instanceid);
            }
        }
    }

    // Check whether the user should be changing password (but only if it is REALLY them).
    if (get_user_preferences('auth_forcepasswordchange') && !\core\session\manager::is_loggedinas()) {
        $userauth = get_auth_plugin($USER->auth);
        if ($userauth->can_change_password() and !$preventredirect) {
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            if ($changeurl = $userauth->change_password_url()) {
                // Use plugin custom url.
                redirect($changeurl);
            } else {
                // Use moodle internal method.
                redirect($CFG->wwwroot .'/login/change_password.php');
            }
        } else if ($userauth->can_change_password()) {
            throw new moodle_exception('forcepasswordchangenotice');
        } else {
            throw new moodle_exception('nopasswordchangeforced', 'auth');
        }
    }

    // Check that the user account is properly set up. If we can't redirect to
    // edit their profile and this is not a WS request, perform just the lax check.
    // It will allow them to use filepicker on the profile edit page.

    if ($preventredirect && !WS_SERVER) {
        $usernotfullysetup = user_not_fully_set_up($USER, false);
    } else {
        $usernotfullysetup = user_not_fully_set_up($USER, true);
    }

    if ($usernotfullysetup) {
        if ($preventredirect) {
            throw new moodle_exception('usernotfullysetup');
        }
        if ($setwantsurltome) {
            $SESSION->wantsurl = qualified_me();
        }
        redirect($CFG->wwwroot .'/user/edit.php?id='. $USER->id .'&amp;course='. SITEID);
    }

    // Make sure the USER has a sesskey set up. Used for CSRF protection.
    sesskey();

    if (\core\session\manager::is_loggedinas()) {
        // During a "logged in as" session we should force all content to be cleaned because the
        // logged in user will be viewing potentially malicious user generated content.
        // See MDL-63786 for more details.
        $CFG->forceclean = true;
    }

    $afterlogins = get_plugins_with_function('after_require_login', 'lib.php');

    // Do not bother admins with any formalities, except for activities pending deletion.
    if (is_siteadmin() && !($cm && $cm->deletioninprogress)) {
        // Set the global $COURSE.
        if ($cm) {
            $PAGE->set_cm($cm, $course);
            $PAGE->set_pagelayout('incourse');
        } else if (!empty($courseorid)) {
            $PAGE->set_course($course);
        }
        // Set accesstime or the user will appear offline which messes up messaging.
        // Do not update access time for webservice or ajax requests.
        if (!WS_SERVER && !AJAX_SCRIPT) {
            user_accesstime_log($course->id);
        }

        foreach ($afterlogins as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);
            }
        }
        return;
    }

    // Scripts have a chance to declare that $USER->policyagreed should not be checked.
    // This is mostly for places where users are actually accepting the policies, to avoid the redirect loop.
    if (!defined('NO_SITEPOLICY_CHECK')) {
        define('NO_SITEPOLICY_CHECK', false);
    }

    // Check that the user has agreed to a site policy if there is one - do not test in case of admins.
    // Do not test if the script explicitly asked for skipping the site policies check.
    // Or if the user auth type is webservice.
    if (!$USER->policyagreed && !is_siteadmin() && !NO_SITEPOLICY_CHECK && $USER->auth !== 'webservice') {
        $manager = new \core_privacy\local\sitepolicy\manager();
        if ($policyurl = $manager->get_redirect_url(isguestuser())) {
            if ($preventredirect) {
                throw new moodle_exception('sitepolicynotagreed', 'error', '', $policyurl->out());
            }
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            redirect($policyurl);
        }
    }

    // Fetch the system context, the course context, and prefetch its child contexts.
    $sysctx = context_system::instance();
    $coursecontext = context_course::instance($course->id, MUST_EXIST);
    if ($cm) {
        $cmcontext = context_module::instance($cm->id, MUST_EXIST);
    } else {
        $cmcontext = null;
    }

    // If the site is currently under maintenance, then print a message.
    if (!empty($CFG->maintenance_enabled) and !has_capability('moodle/site:maintenanceaccess', $sysctx)) {
        if ($preventredirect) {
            throw new require_login_exception('Maintenance in progress');
        }
        $PAGE->set_context(null);
        print_maintenance_message();
    }

    // Make sure the course itself is not hidden.
    if ($course->id == SITEID) {
        // Frontpage can not be hidden.
    } else {
        if (is_role_switched($course->id)) {
            // When switching roles ignore the hidden flag - user had to be in course to do the switch.
        } else {
            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                // Originally there was also test of parent category visibility, BUT is was very slow in complex queries
                // involving "my courses" now it is also possible to simply hide all courses user is not enrolled in :-).
                if ($preventredirect) {
                    throw new require_login_exception('Course is hidden');
                }
                $PAGE->set_context(null);
                // We need to override the navigation URL as the course won't have been added to the navigation and thus
                // the navigation will mess up when trying to find it.
                navigation_node::override_active_url(new moodle_url('/'));
                notice(get_string('coursehidden'), $CFG->wwwroot .'/');
            }
        }
    }

    // Is the user enrolled?
    if ($course->id == SITEID) {
        // Everybody is enrolled on the frontpage.
    } else {
        if (\core\session\manager::is_loggedinas()) {
            // Make sure the REAL person can access this course first.
            $realuser = \core\session\manager::get_realuser();
            if (!is_enrolled($coursecontext, $realuser->id, '', true) and
                !is_viewing($coursecontext, $realuser->id) and !is_siteadmin($realuser->id)) {
                if ($preventredirect) {
                    throw new require_login_exception('Invalid course login-as access');
                }
                $PAGE->set_context(null);
                echo $OUTPUT->header();
                notice(get_string('studentnotallowed', '', fullname($USER, true)), $CFG->wwwroot .'/');
            }
        }

        $access = false;

        if (is_role_switched($course->id)) {
            // Ok, user had to be inside this course before the switch.
            $access = true;

        } else if (is_viewing($coursecontext, $USER)) {
            // Ok, no need to mess with enrol.
            $access = true;

        } else {
            if (isset($USER->enrol['enrolled'][$course->id])) {
                if ($USER->enrol['enrolled'][$course->id] > time()) {
                    $access = true;
                    if (isset($USER->enrol['tempguest'][$course->id])) {
                        unset($USER->enrol['tempguest'][$course->id]);
                        remove_temp_course_roles($coursecontext);
                    }
                } else {
                    // Expired.
                    unset($USER->enrol['enrolled'][$course->id]);
                }
            }
            if (isset($USER->enrol['tempguest'][$course->id])) {
                if ($USER->enrol['tempguest'][$course->id] == 0) {
                    $access = true;
                } else if ($USER->enrol['tempguest'][$course->id] > time()) {
                    $access = true;
                } else {
                    // Expired.
                    unset($USER->enrol['tempguest'][$course->id]);
                    remove_temp_course_roles($coursecontext);
                }
            }

            if (!$access) {
                // Cache not ok.
                $until = enrol_get_enrolment_end($coursecontext->instanceid, $USER->id);
                if ($until !== false) {
                    // Active participants may always access, a timestamp in the future, 0 (always) or false.
                    if ($until == 0) {
                        $until = ENROL_MAX_TIMESTAMP;
                    }
                    $USER->enrol['enrolled'][$course->id] = $until;
                    $access = true;

                } else if (core_course_category::can_view_course_info($course)) {
                    $params = array('courseid' => $course->id, 'status' => ENROL_INSTANCE_ENABLED);
                    $instances = $DB->get_records('enrol', $params, 'sortorder, id ASC');
                    $enrols = enrol_get_plugins(true);
                    // First ask all enabled enrol instances in course if they want to auto enrol user.
                    foreach ($instances as $instance) {
                        if (!isset($enrols[$instance->enrol])) {
                            continue;
                        }
                        // Get a duration for the enrolment, a timestamp in the future, 0 (always) or false.
                        $until = $enrols[$instance->enrol]->try_autoenrol($instance);
                        if ($until !== false) {
                            if ($until == 0) {
                                $until = ENROL_MAX_TIMESTAMP;
                            }
                            $USER->enrol['enrolled'][$course->id] = $until;
                            $access = true;
                            break;
                        }
                    }
                    // If not enrolled yet try to gain temporary guest access.
                    if (!$access) {
                        foreach ($instances as $instance) {
                            if (!isset($enrols[$instance->enrol])) {
                                continue;
                            }
                            // Get a duration for the guest access, a timestamp in the future or false.
                            $until = $enrols[$instance->enrol]->try_guestaccess($instance);
                            if ($until !== false and $until > time()) {
                                $USER->enrol['tempguest'][$course->id] = $until;
                                $access = true;
                                break;
                            }
                        }
                    }
                } else {
                    // User is not enrolled and is not allowed to browse courses here.
                    if ($preventredirect) {
                        throw new require_login_exception('Course is not available');
                    }
                    $PAGE->set_context(null);
                    // We need to override the navigation URL as the course won't have been added to the navigation and thus
                    // the navigation will mess up when trying to find it.
                    navigation_node::override_active_url(new moodle_url('/'));
                    notice(get_string('coursehidden'), $CFG->wwwroot .'/');
                }
            }
        }

        if (!$access) {
            if ($preventredirect) {
                throw new require_login_exception('Not enrolled');
            }
            if ($setwantsurltome) {
                $SESSION->wantsurl = qualified_me();
            }
            redirect($CFG->wwwroot .'/enrol/index.php?id='. $course->id);
        }
    }

    // Check whether the activity has been scheduled for deletion. If so, then deny access, even for admins.
    if ($cm && $cm->deletioninprogress) {
        if ($preventredirect) {
            throw new moodle_exception('activityisscheduledfordeletion');
        }
        require_once($CFG->dirroot . '/course/lib.php');
        redirect(course_get_url($course), get_string('activityisscheduledfordeletion', 'error'));
    }

    // Check visibility of activity to current user; includes visible flag, conditional availability, etc.
    if ($cm && !$cm->uservisible) {
        if ($preventredirect) {
            throw new require_login_exception('Activity is hidden');
        }
        // Get the error message that activity is not available and why (if explanation can be shown to the user).
        $PAGE->set_course($course);
        $renderer = $PAGE->get_renderer('course');
        $message = $renderer->course_section_cm_unavailable_error_message($cm);
        redirect(course_get_url($course), $message, null, \core\output\notification::NOTIFY_ERROR);
    }

    // Set the global $COURSE.
    if ($cm) {
        $PAGE->set_cm($cm, $course);
        $PAGE->set_pagelayout('incourse');
    } else if (!empty($courseorid)) {
        $PAGE->set_course($course);
    }

    foreach ($afterlogins as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginfunction($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);
        }
    }

    // Finally access granted, update lastaccess times.
    // Do not update access time for webservice or ajax requests.
    if (!WS_SERVER && !AJAX_SCRIPT) {
        user_accesstime_log($course->id);
    }
}

/**
 * A convenience function for where we must be logged in as admin
 * @return void
 */
function require_admin() {
    require_login(null, false);
    require_capability('moodle/site:config', context_system::instance());
}

/**
 * This function just makes sure a user is logged out.
 *
 * @package    core_access
 * @category   access
 */
function require_logout() {
    global $USER, $DB;

    if (!isloggedin()) {
        // This should not happen often, no need for hooks or events here.
        \core\session\manager::terminate_current();
        return;
    }

    // Execute hooks before action.
    $authplugins = array();
    $authsequence = get_enabled_auth_plugins();
    foreach ($authsequence as $authname) {
        $authplugins[$authname] = get_auth_plugin($authname);
        $authplugins[$authname]->prelogout_hook();
    }

    // Store info that gets removed during logout.
    $sid = session_id();
    $event = \core\event\user_loggedout::create(
        array(
            'userid' => $USER->id,
            'objectid' => $USER->id,
            'other' => array('sessionid' => $sid),
        )
    );
    if ($session = $DB->get_record('sessions', array('sid'=>$sid))) {
        $event->add_record_snapshot('sessions', $session);
    }

    // Clone of $USER object to be used by auth plugins.
    $user = fullclone($USER);

    // Delete session record and drop $_SESSION content.
    \core\session\manager::terminate_current();

    // Trigger event AFTER action.
    $event->trigger();

    // Hook to execute auth plugins redirection after event trigger.
    foreach ($authplugins as $authplugin) {
        $authplugin->postlogout_hook($user);
    }
}

/**
 * Weaker version of require_login()
 *
 * This is a weaker version of {@link require_login()} which only requires login
 * when called from within a course rather than the site page, unless
 * the forcelogin option is turned on.
 * @see require_login()
 *
 * @package    core_access
 * @category   access
 *
 * @param mixed $courseorid The course object or id in question
 * @param bool $autologinguest Allow autologin guests if that is wanted
 * @param object $cm Course activity module if known
 * @param bool $setwantsurltome Define if we want to set $SESSION->wantsurl, defaults to
 *             true. Used to avoid (=false) some scripts (file.php...) to set that variable,
 *             in order to keep redirects working properly. MDL-14495
 * @param bool $preventredirect set to true in scripts that can not redirect (CLI, rss feeds, etc.), throws exceptions
 * @return void
 * @throws coding_exception
 */
function require_course_login($courseorid, $autologinguest = true, $cm = null, $setwantsurltome = true, $preventredirect = false) {
    global $CFG, $PAGE, $SITE;
    $issite = ((is_object($courseorid) and $courseorid->id == SITEID)
          or (!is_object($courseorid) and $courseorid == SITEID));
    if ($issite && !empty($cm) && !($cm instanceof cm_info)) {
        // Note: nearly all pages call get_fast_modinfo anyway and it does not make any
        // db queries so this is not really a performance concern, however it is obviously
        // better if you use get_fast_modinfo to get the cm before calling this.
        if (is_object($courseorid)) {
            $course = $courseorid;
        } else {
            $course = clone($SITE);
        }
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($cm->id);
    }
    if (!empty($CFG->forcelogin)) {
        // Login required for both SITE and courses.
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);

    } else if ($issite && !empty($cm) and !$cm->uservisible) {
        // Always login for hidden activities.
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);

    } else if (isloggedin() && !isguestuser()) {
        // User is already logged in. Make sure the login is complete (user is fully setup, policies agreed).
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);

    } else if ($issite) {
        // Login for SITE not required.
        // We still need to instatiate PAGE vars properly so that things that rely on it like navigation function correctly.
        if (!empty($courseorid)) {
            if (is_object($courseorid)) {
                $course = $courseorid;
            } else {
                $course = clone $SITE;
            }
            if ($cm) {
                if ($cm->course != $course->id) {
                    throw new coding_exception('course and cm parameters in require_course_login() call do not match!!');
                }
                $PAGE->set_cm($cm, $course);
                $PAGE->set_pagelayout('incourse');
            } else {
                $PAGE->set_course($course);
            }
        } else {
            // If $PAGE->course, and hence $PAGE->context, have not already been set up properly, set them up now.
            $PAGE->set_course($PAGE->course);
        }
        // Do not update access time for webservice or ajax requests.
        if (!WS_SERVER && !AJAX_SCRIPT) {
            user_accesstime_log(SITEID);
        }
        return;

    } else {
        // Course login always required.
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);
    }
}

/**
 * Validates a user key, checking if the key exists, is not expired and the remote ip is correct.
 *
 * @param  string $keyvalue the key value
 * @param  string $script   unique script identifier
 * @param  int $instance    instance id
 * @return stdClass the key entry in the user_private_key table
 * @since Moodle 3.2
 * @throws moodle_exception
 */
function validate_user_key($keyvalue, $script, $instance) {
    global $DB;

    if (!$key = $DB->get_record('user_private_key', array('script' => $script, 'value' => $keyvalue, 'instance' => $instance))) {
        throw new \moodle_exception('invalidkey');
    }

    if (!empty($key->validuntil) and $key->validuntil < time()) {
        throw new \moodle_exception('expiredkey');
    }

    if ($key->iprestriction) {
        $remoteaddr = getremoteaddr(null);
        if (empty($remoteaddr) or !address_in_subnet($remoteaddr, $key->iprestriction)) {
            throw new \moodle_exception('ipmismatch');
        }
    }
    return $key;
}

/**
 * Require key login. Function terminates with error if key not found or incorrect.
 *
 * @uses NO_MOODLE_COOKIES
 * @uses PARAM_ALPHANUM
 * @param string $script unique script identifier
 * @param int $instance optional instance id
 * @param string $keyvalue The key. If not supplied, this will be fetched from the current session.
 * @return int Instance ID
 */
function require_user_key_login($script, $instance = null, $keyvalue = null) {
    global $DB;

    if (!NO_MOODLE_COOKIES) {
        throw new \moodle_exception('sessioncookiesdisable');
    }

    // Extra safety.
    \core\session\manager::write_close();

    if (null === $keyvalue) {
        $keyvalue = required_param('key', PARAM_ALPHANUM);
    }

    $key = validate_user_key($keyvalue, $script, $instance);

    if (!$user = $DB->get_record('user', array('id' => $key->userid))) {
        throw new \moodle_exception('invaliduserid');
    }

    core_user::require_active_user($user, true, true);

    // Emulate normal session.
    enrol_check_plugins($user, false);
    \core\session\manager::set_user($user);

    // Note we are not using normal login.
    if (!defined('USER_KEY_LOGIN')) {
        define('USER_KEY_LOGIN', true);
    }

    // Return instance id - it might be empty.
    return $key->instance;
}

/**
 * Creates a new private user access key.
 *
 * @param string $script unique target identifier
 * @param int $userid
 * @param int $instance optional instance id
 * @param string $iprestriction optional ip restricted access
 * @param int $validuntil key valid only until given data
 * @return string access key value
 */
function create_user_key($script, $userid, $instance=null, $iprestriction=null, $validuntil=null) {
    global $DB;

    $key = new stdClass();
    $key->script        = $script;
    $key->userid        = $userid;
    $key->instance      = $instance;
    $key->iprestriction = $iprestriction;
    $key->validuntil    = $validuntil;
    $key->timecreated   = time();

    // Something long and unique.
    $key->value         = md5($userid.'_'.time().random_string(40));
    while ($DB->record_exists('user_private_key', array('value' => $key->value))) {
        // Must be unique.
        $key->value     = md5($userid.'_'.time().random_string(40));
    }
    $DB->insert_record('user_private_key', $key);
    return $key->value;
}

/**
 * Delete the user's new private user access keys for a particular script.
 *
 * @param string $script unique target identifier
 * @param int $userid
 * @return void
 */
function delete_user_key($script, $userid) {
    global $DB;
    $DB->delete_records('user_private_key', array('script' => $script, 'userid' => $userid));
}

/**
 * Gets a private user access key (and creates one if one doesn't exist).
 *
 * @param string $script unique target identifier
 * @param int $userid
 * @param int $instance optional instance id
 * @param string $iprestriction optional ip restricted access
 * @param int $validuntil key valid only until given date
 * @return string access key value
 */
function get_user_key($script, $userid, $instance=null, $iprestriction=null, $validuntil=null) {
    global $DB;

    if ($key = $DB->get_record('user_private_key', array('script' => $script, 'userid' => $userid,
                                                         'instance' => $instance, 'iprestriction' => $iprestriction,
                                                         'validuntil' => $validuntil))) {
        return $key->value;
    } else {
        return create_user_key($script, $userid, $instance, $iprestriction, $validuntil);
    }
}


/**
 * Modify the user table by setting the currently logged in user's last login to now.
 *
 * @return bool Always returns true
 */
function update_user_login_times() {
    global $USER, $DB, $SESSION;

    if (isguestuser()) {
        // Do not update guest access times/ips for performance.
        return true;
    }

    if (defined('USER_KEY_LOGIN') && USER_KEY_LOGIN === true) {
        // Do not update user login time when using user key login.
        return true;
    }

    $now = time();

    $user = new stdClass();
    $user->id = $USER->id;

    // Make sure all users that logged in have some firstaccess.
    if ($USER->firstaccess == 0) {
        $USER->firstaccess = $user->firstaccess = $now;
    }

    // Store the previous current as lastlogin.
    $USER->lastlogin = $user->lastlogin = $USER->currentlogin;

    $USER->currentlogin = $user->currentlogin = $now;

    // Function user_accesstime_log() may not update immediately, better do it here.
    $USER->lastaccess = $user->lastaccess = $now;
    $SESSION->userpreviousip = $USER->lastip;
    $USER->lastip = $user->lastip = getremoteaddr();

    // Note: do not call user_update_user() here because this is part of the login process,
    //       the login event means that these fields were updated.
    $DB->update_record('user', $user);
    return true;
}

/**
 * Determines if a user has completed setting up their account.
 *
 * The lax mode (with $strict = false) has been introduced for special cases
 * only where we want to skip certain checks intentionally. This is valid in
 * certain mnet or ajax scenarios when the user cannot / should not be
 * redirected to edit their profile. In most cases, you should perform the
 * strict check.
 *
 * @param stdClass $user A {@link $USER} object to test for the existence of a valid name and email
 * @param bool $strict Be more strict and assert id and custom profile fields set, too
 * @return bool
 */
function user_not_fully_set_up($user, $strict = true) {
    global $CFG, $SESSION, $USER;
    require_once($CFG->dirroot.'/user/profile/lib.php');

    // If the user is setup then store this in the session to avoid re-checking.
    // Some edge cases are when the users email starts to bounce or the
    // configuration for custom fields has changed while they are logged in so
    // we re-check this fully every hour for the rare cases it has changed.
    if (isset($USER->id) && isset($user->id) && $USER->id === $user->id &&
         isset($SESSION->fullysetupstrict) && (time() - $SESSION->fullysetupstrict) < HOURSECS) {
        return false;
    }

    if (isguestuser($user)) {
        return false;
    }

    if (empty($user->firstname) or empty($user->lastname) or empty($user->email) or over_bounce_threshold($user)) {
        return true;
    }

    if ($strict) {
        if (empty($user->id)) {
            // Strict mode can be used with existing accounts only.
            return true;
        }
        if (!profile_has_required_custom_fields_set($user->id)) {
            return true;
        }
        if (isset($USER->id) && isset($user->id) && $USER->id === $user->id) {
            $SESSION->fullysetupstrict = time();
        }
    }

    return false;
}

/**
 * Check whether the user has exceeded the bounce threshold
 *
 * @param stdClass $user A {@link $USER} object
 * @return bool true => User has exceeded bounce threshold
 */
function over_bounce_threshold($user) {
    global $CFG, $DB;

    if (empty($CFG->handlebounces)) {
        return false;
    }

    if (empty($user->id)) {
        // No real (DB) user, nothing to do here.
        return false;
    }

    // Set sensible defaults.
    if (empty($CFG->minbounces)) {
        $CFG->minbounces = 10;
    }
    if (empty($CFG->bounceratio)) {
        $CFG->bounceratio = .20;
    }
    $bouncecount = 0;
    $sendcount = 0;
    if ($bounce = $DB->get_record('user_preferences', array ('userid' => $user->id, 'name' => 'email_bounce_count'))) {
        $bouncecount = $bounce->value;
    }
    if ($send = $DB->get_record('user_preferences', array('userid' => $user->id, 'name' => 'email_send_count'))) {
        $sendcount = $send->value;
    }
    return ($bouncecount >= $CFG->minbounces && $bouncecount/$sendcount >= $CFG->bounceratio);
}

/**
 * Used to increment or reset email sent count
 *
 * @param stdClass $user object containing an id
 * @param bool $reset will reset the count to 0
 * @return void
 */
function set_send_count($user, $reset=false) {
    global $DB;

    if (empty($user->id)) {
        // No real (DB) user, nothing to do here.
        return;
    }

    if ($pref = $DB->get_record('user_preferences', array('userid' => $user->id, 'name' => 'email_send_count'))) {
        $pref->value = (!empty($reset)) ? 0 : $pref->value+1;
        $DB->update_record('user_preferences', $pref);
    } else if (!empty($reset)) {
        // If it's not there and we're resetting, don't bother. Make a new one.
        $pref = new stdClass();
        $pref->name   = 'email_send_count';
        $pref->value  = 1;
        $pref->userid = $user->id;
        $DB->insert_record('user_preferences', $pref, false);
    }
}

/**
 * Increment or reset user's email bounce count
 *
 * @param stdClass $user object containing an id
 * @param bool $reset will reset the count to 0
 */
function set_bounce_count($user, $reset=false) {
    global $DB;

    if ($pref = $DB->get_record('user_preferences', array('userid' => $user->id, 'name' => 'email_bounce_count'))) {
        $pref->value = (!empty($reset)) ? 0 : $pref->value+1;
        $DB->update_record('user_preferences', $pref);
    } else if (!empty($reset)) {
        // If it's not there and we're resetting, don't bother. Make a new one.
        $pref = new stdClass();
        $pref->name   = 'email_bounce_count';
        $pref->value  = 1;
        $pref->userid = $user->id;
        $DB->insert_record('user_preferences', $pref, false);
    }
}

/**
 * Determines if the logged in user is currently moving an activity
 *
 * @param int $courseid The id of the course being tested
 * @return bool
 */
function ismoving($courseid) {
    global $USER;

    if (!empty($USER->activitycopy)) {
        return ($USER->activitycopycourse == $courseid);
    }
    return false;
}

/**
 * Returns a persons full name
 *
 * Given an object containing all of the users name values, this function returns a string with the full name of the person.
 * The result may depend on system settings or language. 'override' will force the alternativefullnameformat to be used. In
 * English, fullname as well as alternativefullnameformat is set to 'firstname lastname' by default. But you could have
 * fullname set to 'firstname lastname' and alternativefullnameformat set to 'firstname middlename alternatename lastname'.
 *
 * @param stdClass $user A {@link $USER} object to get full name of.
 * @param bool $override If true then the alternativefullnameformat format rather than fullnamedisplay format will be used.
 * @return string
 */
function fullname($user, $override=false) {
    global $CFG, $SESSION;

    if (!isset($user->firstname) and !isset($user->lastname)) {
        return '';
    }

    // Get all of the name fields.
    $allnames = \core_user\fields::get_name_fields();
    if ($CFG->debugdeveloper) {
        foreach ($allnames as $allname) {
            if (!property_exists($user, $allname)) {
                // If all the user name fields are not set in the user object, then notify the programmer that it needs to be fixed.
                debugging('You need to update your sql to include additional name fields in the user object.', DEBUG_DEVELOPER);
                // Message has been sent, no point in sending the message multiple times.
                break;
            }
        }
    }

    if (!$override) {
        if (!empty($CFG->forcefirstname)) {
            $user->firstname = $CFG->forcefirstname;
        }
        if (!empty($CFG->forcelastname)) {
            $user->lastname = $CFG->forcelastname;
        }
    }

    if (!empty($SESSION->fullnamedisplay)) {
        $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
    }

    $template = null;
    // If the fullnamedisplay setting is available, set the template to that.
    if (isset($CFG->fullnamedisplay)) {
        $template = $CFG->fullnamedisplay;
    }
    // If the template is empty, or set to language, return the language string.
    if ((empty($template) || $template == 'language') && !$override) {
        return get_string('fullnamedisplay', null, $user);
    }

    // Check to see if we are displaying according to the alternative full name format.
    if ($override) {
        if (empty($CFG->alternativefullnameformat) || $CFG->alternativefullnameformat == 'language') {
            // Default to show just the user names according to the fullnamedisplay string.
            return get_string('fullnamedisplay', null, $user);
        } else {
            // If the override is true, then change the template to use the complete name.
            $template = $CFG->alternativefullnameformat;
        }
    }

    $requirednames = array();
    // With each name, see if it is in the display name template, and add it to the required names array if it is.
    foreach ($allnames as $allname) {
        if (strpos($template, $allname) !== false) {
            $requirednames[] = $allname;
        }
    }

    $displayname = $template;
    // Switch in the actual data into the template.
    foreach ($requirednames as $altname) {
        if (isset($user->$altname)) {
            // Using empty() on the below if statement causes breakages.
            if ((string)$user->$altname == '') {
                $displayname = str_replace($altname, 'EMPTY', $displayname);
            } else {
                $displayname = str_replace($altname, $user->$altname, $displayname);
            }
        } else {
            $displayname = str_replace($altname, 'EMPTY', $displayname);
        }
    }
    // Tidy up any misc. characters (Not perfect, but gets most characters).
    // Don't remove the "u" at the end of the first expression unless you want garbled characters when combining hiragana or
    // katakana and parenthesis.
    $patterns = array();
    // This regular expression replacement is to fix problems such as 'James () Kirk' Where 'Tiberius' (middlename) has not been
    // filled in by a user.
    // The special characters are Japanese brackets that are common enough to make allowances for them (not covered by :punct:).
    $patterns[] = '/[[:punct:]「」]*EMPTY[[:punct:]「」]*/u';
    // This regular expression is to remove any double spaces in the display name.
    $patterns[] = '/\s{2,}/u';
    foreach ($patterns as $pattern) {
        $displayname = preg_replace($pattern, ' ', $displayname);
    }

    // Trimming $displayname will help the next check to ensure that we don't have a display name with spaces.
    $displayname = trim($displayname);
    if (empty($displayname)) {
        // Going with just the first name if no alternate fields are filled out. May be changed later depending on what
        // people in general feel is a good setting to fall back on.
        $displayname = $user->firstname;
    }
    return $displayname;
}

/**
 * Reduces lines of duplicated code for getting user name fields.
 *
 * See also {@link user_picture::unalias()}
 *
 * @param object $addtoobject Object to add user name fields to.
 * @param object $secondobject Object that contains user name field information.
 * @param string $prefix prefix to be added to all fields (including $additionalfields) e.g. authorfirstname.
 * @param array $additionalfields Additional fields to be matched with data in the second object.
 * The key can be set to the user table field name.
 * @return object User name fields.
 */
function username_load_fields_from_object($addtoobject, $secondobject, $prefix = null, $additionalfields = null) {
    $fields = [];
    foreach (\core_user\fields::get_name_fields() as $field) {
        $fields[$field] = $prefix . $field;
    }
    if ($additionalfields) {
        // Additional fields can specify their own 'alias' such as 'id' => 'userid'. This checks to see if
        // the key is a number and then sets the key to the array value.
        foreach ($additionalfields as $key => $value) {
            if (is_numeric($key)) {
                $additionalfields[$value] = $prefix . $value;
                unset($additionalfields[$key]);
            } else {
                $additionalfields[$key] = $prefix . $value;
            }
        }
        $fields = array_merge($fields, $additionalfields);
    }
    foreach ($fields as $key => $field) {
        // Important that we have all of the user name fields present in the object that we are sending back.
        $addtoobject->$key = '';
        if (isset($secondobject->$field)) {
            $addtoobject->$key = $secondobject->$field;
        }
    }
    return $addtoobject;
}

/**
 * Returns an array of values in order of occurance in a provided string.
 * The key in the result is the character postion in the string.
 *
 * @param array $values Values to be found in the string format
 * @param string $stringformat The string which may contain values being searched for.
 * @return array An array of values in order according to placement in the string format.
 */
function order_in_string($values, $stringformat) {
    $valuearray = array();
    foreach ($values as $value) {
        $pattern = "/$value\b/";
        // Using preg_match as strpos() may match values that are similar e.g. firstname and firstnamephonetic.
        if (preg_match($pattern, $stringformat)) {
            $replacement = "thing";
            // Replace the value with something more unique to ensure we get the right position when using strpos().
            $newformat = preg_replace($pattern, $replacement, $stringformat);
            $position = strpos($newformat, $replacement);
            $valuearray[$position] = $value;
        }
    }
    ksort($valuearray);
    return $valuearray;
}

/**
 * Returns whether a given authentication plugin exists.
 *
 * @param string $auth Form of authentication to check for. Defaults to the global setting in {@link $CFG}.
 * @return boolean Whether the plugin is available.
 */
function exists_auth_plugin($auth) {
    global $CFG;

    if (file_exists("{$CFG->dirroot}/auth/$auth/auth.php")) {
        return is_readable("{$CFG->dirroot}/auth/$auth/auth.php");
    }
    return false;
}

/**
 * Checks if a given plugin is in the list of enabled authentication plugins.
 *
 * @param string $auth Authentication plugin.
 * @return boolean Whether the plugin is enabled.
 */
function is_enabled_auth($auth) {
    if (empty($auth)) {
        return false;
    }

    $enabled = get_enabled_auth_plugins();

    return in_array($auth, $enabled);
}

/**
 * Returns an authentication plugin instance.
 *
 * @param string $auth name of authentication plugin
 * @return auth_plugin_base An instance of the required authentication plugin.
 */
function get_auth_plugin($auth) {
    global $CFG;

    // Check the plugin exists first.
    if (! exists_auth_plugin($auth)) {
        throw new \moodle_exception('authpluginnotfound', 'debug', '', $auth);
    }

    // Return auth plugin instance.
    require_once("{$CFG->dirroot}/auth/$auth/auth.php");
    $class = "auth_plugin_$auth";
    return new $class;
}

/**
 * Returns array of active auth plugins.
 *
 * @param bool $fix fix $CFG->auth if needed. Only set if logged in as admin.
 * @return array
 */
function get_enabled_auth_plugins($fix=false) {
    global $CFG;

    $default = array('manual', 'nologin');

    if (empty($CFG->auth)) {
        $auths = array();
    } else {
        $auths = explode(',', $CFG->auth);
    }

    $auths = array_unique($auths);
    $oldauthconfig = implode(',', $auths);
    foreach ($auths as $k => $authname) {
        if (in_array($authname, $default)) {
            // The manual and nologin plugin never need to be stored.
            unset($auths[$k]);
        } else if (!exists_auth_plugin($authname)) {
            debugging(get_string('authpluginnotfound', 'debug', $authname));
            unset($auths[$k]);
        }
    }

    // Ideally only explicit interaction from a human admin should trigger a
    // change in auth config, see MDL-70424 for details.
    if ($fix) {
        $newconfig = implode(',', $auths);
        if (!isset($CFG->auth) or $newconfig != $CFG->auth) {
            add_to_config_log('auth', $oldauthconfig, $newconfig, 'core');
            set_config('auth', $newconfig);
        }
    }

    return (array_merge($default, $auths));
}

/**
 * Returns true if an internal authentication method is being used.
 * if method not specified then, global default is assumed
 *
 * @param string $auth Form of authentication required
 * @return bool
 */
function is_internal_auth($auth) {
    // Throws error if bad $auth.
    $authplugin = get_auth_plugin($auth);
    return $authplugin->is_internal();
}

/**
 * Returns true if the user is a 'restored' one.
 *
 * Used in the login process to inform the user and allow him/her to reset the password
 *
 * @param string $username username to be checked
 * @return bool
 */
function is_restored_user($username) {
    global $CFG, $DB;

    return $DB->record_exists('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'password' => 'restored'));
}

/**
 * Returns an array of user fields
 *
 * @return array User field/column names
 */
function get_user_fieldnames() {
    global $DB;

    $fieldarray = $DB->get_columns('user');
    unset($fieldarray['id']);
    $fieldarray = array_keys($fieldarray);

    return $fieldarray;
}

/**
 * Returns the string of the language for the new user.
 *
 * @return string language for the new user
 */
function get_newuser_language() {
    global $CFG, $SESSION;
    return (!empty($CFG->autolangusercreation) && !empty($SESSION->lang)) ? $SESSION->lang : $CFG->lang;
}

/**
 * Creates a bare-bones user record
 *
 * @todo Outline auth types and provide code example
 *
 * @param string $username New user's username to add to record
 * @param string $password New user's password to add to record
 * @param string $auth Form of authentication required
 * @return stdClass A complete user object
 */
function create_user_record($username, $password, $auth = 'manual') {
    global $CFG, $DB, $SESSION;
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');

    // Just in case check text case.
    $username = trim(core_text::strtolower($username));

    $authplugin = get_auth_plugin($auth);
    $customfields = $authplugin->get_custom_user_profile_fields();
    $newuser = new stdClass();
    if ($newinfo = $authplugin->get_userinfo($username)) {
        $newinfo = truncate_userinfo($newinfo);
        foreach ($newinfo as $key => $value) {
            if (in_array($key, $authplugin->userfields) || (in_array($key, $customfields))) {
                $newuser->$key = $value;
            }
        }
    }

    if (!empty($newuser->email)) {
        if (email_is_not_allowed($newuser->email)) {
            unset($newuser->email);
        }
    }

    $newuser->auth = $auth;
    $newuser->username = $username;

    // Fix for MDL-8480
    // user CFG lang for user if $newuser->lang is empty
    // or $user->lang is not an installed language.
    if (empty($newuser->lang) || !get_string_manager()->translation_exists($newuser->lang)) {
        $newuser->lang = get_newuser_language();
    }
    $newuser->confirmed = 1;
    $newuser->lastip = getremoteaddr();
    $newuser->timecreated = time();
    $newuser->timemodified = $newuser->timecreated;
    $newuser->mnethostid = $CFG->mnet_localhost_id;

    $newuser->id = user_create_user($newuser, false, false);

    // Save user profile data.
    profile_save_data($newuser);

    $user = get_complete_user_data('id', $newuser->id);
    if (!empty($CFG->{'auth_'.$newuser->auth.'_forcechangepassword'})) {
        set_user_preference('auth_forcepasswordchange', 1, $user);
    }
    // Set the password.
    update_internal_user_password($user, $password);

    // Trigger event.
    \core\event\user_created::create_from_userid($newuser->id)->trigger();

    return $user;
}

/**
 * Will update a local user record from an external source (MNET users can not be updated using this method!).
 *
 * @param string $username user's username to update the record
 * @return stdClass A complete user object
 */
function update_user_record($username) {
    global $DB, $CFG;
    // Just in case check text case.
    $username = trim(core_text::strtolower($username));

    $oldinfo = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id), '*', MUST_EXIST);
    return update_user_record_by_id($oldinfo->id);
}

/**
 * Will update a local user record from an external source (MNET users can not be updated using this method!).
 *
 * @param int $id user id
 * @return stdClass A complete user object
 */
function update_user_record_by_id($id) {
    global $DB, $CFG;
    require_once($CFG->dirroot."/user/profile/lib.php");
    require_once($CFG->dirroot.'/user/lib.php');

    $params = array('mnethostid' => $CFG->mnet_localhost_id, 'id' => $id, 'deleted' => 0);
    $oldinfo = $DB->get_record('user', $params, '*', MUST_EXIST);

    $newuser = array();
    $userauth = get_auth_plugin($oldinfo->auth);

    if ($newinfo = $userauth->get_userinfo($oldinfo->username)) {
        $newinfo = truncate_userinfo($newinfo);
        $customfields = $userauth->get_custom_user_profile_fields();

        foreach ($newinfo as $key => $value) {
            $iscustom = in_array($key, $customfields);
            if (!$iscustom) {
                $key = strtolower($key);
            }
            if ((!property_exists($oldinfo, $key) && !$iscustom) or $key === 'username' or $key === 'id'
                    or $key === 'auth' or $key === 'mnethostid' or $key === 'deleted') {
                // Unknown or must not be changed.
                continue;
            }
            if (empty($userauth->config->{'field_updatelocal_' . $key}) || empty($userauth->config->{'field_lock_' . $key})) {
                continue;
            }
            $confval = $userauth->config->{'field_updatelocal_' . $key};
            $lockval = $userauth->config->{'field_lock_' . $key};
            if ($confval === 'onlogin') {
                // MDL-4207 Don't overwrite modified user profile values with
                // empty LDAP values when 'unlocked if empty' is set. The purpose
                // of the setting 'unlocked if empty' is to allow the user to fill
                // in a value for the selected field _if LDAP is giving
                // nothing_ for this field. Thus it makes sense to let this value
                // stand in until LDAP is giving a value for this field.
                if (!(empty($value) && $lockval === 'unlockedifempty')) {
                    if ($iscustom || (in_array($key, $userauth->userfields) &&
                            ((string)$oldinfo->$key !== (string)$value))) {
                        $newuser[$key] = (string)$value;
                    }
                }
            }
        }
        if ($newuser) {
            $newuser['id'] = $oldinfo->id;
            $newuser['timemodified'] = time();
            user_update_user((object) $newuser, false, false);

            // Save user profile data.
            profile_save_data((object) $newuser);

            // Trigger event.
            \core\event\user_updated::create_from_userid($newuser['id'])->trigger();
        }
    }

    return get_complete_user_data('id', $oldinfo->id);
}

/**
 * Will truncate userinfo as it comes from auth_get_userinfo (from external auth) which may have large fields.
 *
 * @param array $info Array of user properties to truncate if needed
 * @return array The now truncated information that was passed in
 */
function truncate_userinfo(array $info) {
    // Define the limits.
    $limit = array(
        'username'    => 100,
        'idnumber'    => 255,
        'firstname'   => 100,
        'lastname'    => 100,
        'email'       => 100,
        'phone1'      =>  20,
        'phone2'      =>  20,
        'institution' => 255,
        'department'  => 255,
        'address'     => 255,
        'city'        => 120,
        'country'     =>   2,
    );

    // Apply where needed.
    foreach (array_keys($info) as $key) {
        if (!empty($limit[$key])) {
            $info[$key] = trim(core_text::substr($info[$key], 0, $limit[$key]));
        }
    }

    return $info;
}

/**
 * Marks user deleted in internal user database and notifies the auth plugin.
 * Also unenrols user from all roles and does other cleanup.
 *
 * Any plugin that needs to purge user data should register the 'user_deleted' event.
 *
 * @param stdClass $user full user object before delete
 * @return boolean success
 * @throws coding_exception if invalid $user parameter detected
 */
function delete_user(stdClass $user) {
    global $CFG, $DB, $SESSION;
    require_once($CFG->libdir.'/grouplib.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/message/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');

    // Make sure nobody sends bogus record type as parameter.
    if (!property_exists($user, 'id') or !property_exists($user, 'username')) {
        throw new coding_exception('Invalid $user parameter in delete_user() detected');
    }

    // Better not trust the parameter and fetch the latest info this will be very expensive anyway.
    if (!$user = $DB->get_record('user', array('id' => $user->id))) {
        debugging('Attempt to delete unknown user account.');
        return false;
    }

    // There must be always exactly one guest record, originally the guest account was identified by username only,
    // now we use $CFG->siteguest for performance reasons.
    if ($user->username === 'guest' or isguestuser($user)) {
        debugging('Guest user account can not be deleted.');
        return false;
    }

    // Admin can be theoretically from different auth plugin, but we want to prevent deletion of internal accoutns only,
    // if anything goes wrong ppl may force somebody to be admin via config.php setting $CFG->siteadmins.
    if ($user->auth === 'manual' and is_siteadmin($user)) {
        debugging('Local administrator accounts can not be deleted.');
        return false;
    }

    // Allow plugins to use this user object before we completely delete it.
    if ($pluginsfunction = get_plugins_with_function('pre_user_delete')) {
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($user);
            }
        }
    }

    // Keep user record before updating it, as we have to pass this to user_deleted event.
    $olduser = clone $user;

    // Keep a copy of user context, we need it for event.
    $usercontext = context_user::instance($user->id);

    // Delete all grades - backup is kept in grade_grades_history table.
    grade_user_delete($user->id);

    // TODO: remove from cohorts using standard API here.

    // Remove user tags.
    core_tag_tag::remove_all_item_tags('core', 'user', $user->id);

    // Unconditionally unenrol from all courses.
    enrol_user_delete($user);

    // Unenrol from all roles in all contexts.
    // This might be slow but it is really needed - modules might do some extra cleanup!
    role_unassign_all(array('userid' => $user->id));

    // Notify the competency subsystem.
    \core_competency\api::hook_user_deleted($user->id);

    // Now do a brute force cleanup.

    // Delete all user events and subscription events.
    $DB->delete_records_select('event', 'userid = :userid AND subscriptionid IS NOT NULL', ['userid' => $user->id]);

    // Now, delete all calendar subscription from the user.
    $DB->delete_records('event_subscriptions', ['userid' => $user->id]);

    // Remove from all cohorts.
    $DB->delete_records('cohort_members', array('userid' => $user->id));

    // Remove from all groups.
    $DB->delete_records('groups_members', array('userid' => $user->id));

    // Brute force unenrol from all courses.
    $DB->delete_records('user_enrolments', array('userid' => $user->id));

    // Purge user preferences.
    $DB->delete_records('user_preferences', array('userid' => $user->id));

    // Purge user extra profile info.
    $DB->delete_records('user_info_data', array('userid' => $user->id));

    // Purge log of previous password hashes.
    $DB->delete_records('user_password_history', array('userid' => $user->id));

    // Last course access not necessary either.
    $DB->delete_records('user_lastaccess', array('userid' => $user->id));
    // Remove all user tokens.
    $DB->delete_records('external_tokens', array('userid' => $user->id));

    // Unauthorise the user for all services.
    $DB->delete_records('external_services_users', array('userid' => $user->id));

    // Remove users private keys.
    $DB->delete_records('user_private_key', array('userid' => $user->id));

    // Remove users customised pages.
    $DB->delete_records('my_pages', array('userid' => $user->id, 'private' => 1));

    // Remove user's oauth2 refresh tokens, if present.
    $DB->delete_records('oauth2_refresh_token', array('userid' => $user->id));

    // Delete user from $SESSION->bulk_users.
    if (isset($SESSION->bulk_users[$user->id])) {
        unset($SESSION->bulk_users[$user->id]);
    }

    // Force logout - may fail if file based sessions used, sorry.
    \core\session\manager::kill_user_sessions($user->id);

    // Generate username from email address, or a fake email.
    $delemail = !empty($user->email) ? $user->email : $user->username . '.' . $user->id . '@unknownemail.invalid';

    $deltime = time();
    $deltimelength = core_text::strlen((string) $deltime);

    // Max username length is 100 chars. Select up to limit - (length of current time + 1 [period character]) from users email.
    $delname = clean_param($delemail, PARAM_USERNAME);
    $delname = core_text::substr($delname, 0, 100 - ($deltimelength + 1)) . ".{$deltime}";

    // Workaround for bulk deletes of users with the same email address.
    while ($DB->record_exists('user', array('username' => $delname))) { // No need to use mnethostid here.
        $delname++;
    }

    // Mark internal user record as "deleted".
    $updateuser = new stdClass();
    $updateuser->id           = $user->id;
    $updateuser->deleted      = 1;
    $updateuser->username     = $delname;            // Remember it just in case.
    $updateuser->email        = md5($user->username);// Store hash of username, useful importing/restoring users.
    $updateuser->idnumber     = '';                  // Clear this field to free it up.
    $updateuser->picture      = 0;
    $updateuser->timemodified = $deltime;

    // Don't trigger update event, as user is being deleted.
    user_update_user($updateuser, false, false);

    // Delete all content associated with the user context, but not the context itself.
    $usercontext->delete_content();

    // Delete any search data.
    \core_search\manager::context_deleted($usercontext);

    // Any plugin that needs to cleanup should register this event.
    // Trigger event.
    $event = \core\event\user_deleted::create(
            array(
                'objectid' => $user->id,
                'relateduserid' => $user->id,
                'context' => $usercontext,
                'other' => array(
                    'username' => $user->username,
                    'email' => $user->email,
                    'idnumber' => $user->idnumber,
                    'picture' => $user->picture,
                    'mnethostid' => $user->mnethostid
                    )
                )
            );
    $event->add_record_snapshot('user', $olduser);
    $event->trigger();

    // We will update the user's timemodified, as it will be passed to the user_deleted event, which
    // should know about this updated property persisted to the user's table.
    $user->timemodified = $updateuser->timemodified;

    // Notify auth plugin - do not block the delete even when plugin fails.
    $authplugin = get_auth_plugin($user->auth);
    $authplugin->user_delete($user);

    return true;
}

/**
 * Retrieve the guest user object.
 *
 * @return stdClass A {@link $USER} object
 */
function guest_user() {
    global $CFG, $DB;

    if ($newuser = $DB->get_record('user', array('id' => $CFG->siteguest))) {
        $newuser->confirmed = 1;
        $newuser->lang = get_newuser_language();
        $newuser->lastip = getremoteaddr();
    }

    return $newuser;
}

/**
 * Authenticates a user against the chosen authentication mechanism
 *
 * Given a username and password, this function looks them
 * up using the currently selected authentication mechanism,
 * and if the authentication is successful, it returns a
 * valid $user object from the 'user' table.
 *
 * Uses auth_ functions from the currently active auth module
 *
 * After authenticate_user_login() returns success, you will need to
 * log that the user has logged in, and call complete_user_login() to set
 * the session up.
 *
 * Note: this function works only with non-mnet accounts!
 *
 * @param string $username  User's username (or also email if $CFG->authloginviaemail enabled)
 * @param string $password  User's password
 * @param bool $ignorelockout useful when guessing is prevented by other mechanism such as captcha or SSO
 * @param int $failurereason login failure reason, can be used in renderers (it may disclose if account exists)
 * @param mixed logintoken If this is set to a string it is validated against the login token for the session.
 * @return stdClass|false A {@link $USER} object or false if error
 */
function authenticate_user_login($username, $password, $ignorelockout=false, &$failurereason=null, $logintoken=false) {
    global $CFG, $DB, $PAGE;
    require_once("$CFG->libdir/authlib.php");

    if ($user = get_complete_user_data('username', $username, $CFG->mnet_localhost_id)) {
        // we have found the user

    } else if (!empty($CFG->authloginviaemail)) {
        if ($email = clean_param($username, PARAM_EMAIL)) {
            $select = "mnethostid = :mnethostid AND LOWER(email) = LOWER(:email) AND deleted = 0";
            $params = array('mnethostid' => $CFG->mnet_localhost_id, 'email' => $email);
            $users = $DB->get_records_select('user', $select, $params, 'id', 'id', 0, 2);
            if (count($users) === 1) {
                // Use email for login only if unique.
                $user = reset($users);
                $user = get_complete_user_data('id', $user->id);
                $username = $user->username;
            }
            unset($users);
        }
    }

    // Make sure this request came from the login form.
    if (!\core\session\manager::validate_login_token($logintoken)) {
        $failurereason = AUTH_LOGIN_FAILED;

        // Trigger login failed event (specifying the ID of the found user, if available).
        \core\event\user_login_failed::create([
            'userid' => ($user->id ?? 0),
            'other' => [
                'username' => $username,
                'reason' => $failurereason,
            ],
        ])->trigger();

        error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Invalid Login Token:  $username  ".$_SERVER['HTTP_USER_AGENT']);
        return false;
    }

    $authsenabled = get_enabled_auth_plugins();

    if ($user) {
        // Use manual if auth not set.
        $auth = empty($user->auth) ? 'manual' : $user->auth;

        if (in_array($user->auth, $authsenabled)) {
            $authplugin = get_auth_plugin($user->auth);
            $authplugin->pre_user_login_hook($user);
        }

        if (!empty($user->suspended)) {
            $failurereason = AUTH_LOGIN_SUSPENDED;

            // Trigger login failed event.
            $event = \core\event\user_login_failed::create(array('userid' => $user->id,
                    'other' => array('username' => $username, 'reason' => $failurereason)));
            $event->trigger();
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Suspended Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }
        if ($auth=='nologin' or !is_enabled_auth($auth)) {
            // Legacy way to suspend user.
            $failurereason = AUTH_LOGIN_SUSPENDED;

            // Trigger login failed event.
            $event = \core\event\user_login_failed::create(array('userid' => $user->id,
                    'other' => array('username' => $username, 'reason' => $failurereason)));
            $event->trigger();
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Disabled Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }
        $auths = array($auth);

    } else {
        // Check if there's a deleted record (cheaply), this should not happen because we mangle usernames in delete_user().
        if ($DB->get_field('user', 'id', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id,  'deleted' => 1))) {
            $failurereason = AUTH_LOGIN_NOUSER;

            // Trigger login failed event.
            $event = \core\event\user_login_failed::create(array('other' => array('username' => $username,
                    'reason' => $failurereason)));
            $event->trigger();
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Deleted Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }

        // User does not exist.
        $auths = $authsenabled;
        $user = new stdClass();
        $user->id = 0;
    }

    if ($ignorelockout) {
        // Some other mechanism protects against brute force password guessing, for example login form might include reCAPTCHA
        // or this function is called from a SSO script.
    } else if ($user->id) {
        // Verify login lockout after other ways that may prevent user login.
        if (login_is_lockedout($user)) {
            $failurereason = AUTH_LOGIN_LOCKOUT;

            // Trigger login failed event.
            $event = \core\event\user_login_failed::create(array('userid' => $user->id,
                    'other' => array('username' => $username, 'reason' => $failurereason)));
            $event->trigger();

            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Login lockout:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }
    } else {
        // We can not lockout non-existing accounts.
    }

    foreach ($auths as $auth) {
        $authplugin = get_auth_plugin($auth);

        // On auth fail fall through to the next plugin.
        if (!$authplugin->user_login($username, $password)) {
            continue;
        }

        // Before performing login actions, check if user still passes password policy, if admin setting is enabled.
        if (!empty($CFG->passwordpolicycheckonlogin)) {
            $errmsg = '';
            $passed = check_password_policy($password, $errmsg, $user);
            if (!$passed) {
                // First trigger event for failure.
                $failedevent = \core\event\user_password_policy_failed::create_from_user($user);
                $failedevent->trigger();

                // If able to change password, set flag and move on.
                if ($authplugin->can_change_password()) {
                    // Check if we are on internal change password page, or service is external, don't show notification.
                    $internalchangeurl = new moodle_url('/login/change_password.php');
                    if (!($PAGE->has_set_url() && $internalchangeurl->compare($PAGE->url)) && $authplugin->is_internal()) {
                        \core\notification::error(get_string('passwordpolicynomatch', '', $errmsg));
                    }
                    set_user_preference('auth_forcepasswordchange', 1, $user);
                } else if ($authplugin->can_reset_password()) {
                    // Else force a reset if possible.
                    \core\notification::error(get_string('forcepasswordresetnotice', '', $errmsg));
                    redirect(new moodle_url('/login/forgot_password.php'));
                } else {
                    $notifymsg = get_string('forcepasswordresetfailurenotice', '', $errmsg);
                    // If support page is set, add link for help.
                    if (!empty($CFG->supportpage)) {
                        $link = \html_writer::link($CFG->supportpage, $CFG->supportpage);
                        $link = \html_writer::tag('p', $link);
                        $notifymsg .= $link;
                    }

                    // If no change or reset is possible, add a notification for user.
                    \core\notification::error($notifymsg);
                }
            }
        }

        // Successful authentication.
        if ($user->id) {
            // User already exists in database.
            if (empty($user->auth)) {
                // For some reason auth isn't set yet.
                $DB->set_field('user', 'auth', $auth, array('id' => $user->id));
                $user->auth = $auth;
            }

            // If the existing hash is using an out-of-date algorithm (or the legacy md5 algorithm), then we should update to
            // the current hash algorithm while we have access to the user's password.
            update_internal_user_password($user, $password);

            if ($authplugin->is_synchronised_with_external()) {
                // Update user record from external DB.
                $user = update_user_record_by_id($user->id);
            }
        } else {
            // The user is authenticated but user creation may be disabled.
            if (!empty($CFG->authpreventaccountcreation)) {
                $failurereason = AUTH_LOGIN_UNAUTHORISED;

                // Trigger login failed event.
                $event = \core\event\user_login_failed::create(array('other' => array('username' => $username,
                        'reason' => $failurereason)));
                $event->trigger();

                error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Unknown user, can not create new accounts:  $username  ".
                        $_SERVER['HTTP_USER_AGENT']);
                return false;
            } else {
                $user = create_user_record($username, $password, $auth);
            }
        }

        $authplugin->sync_roles($user);

        foreach ($authsenabled as $hau) {
            $hauth = get_auth_plugin($hau);
            $hauth->user_authenticated_hook($user, $username, $password);
        }

        if (empty($user->id)) {
            $failurereason = AUTH_LOGIN_NOUSER;
            // Trigger login failed event.
            $event = \core\event\user_login_failed::create(array('other' => array('username' => $username,
                    'reason' => $failurereason)));
            $event->trigger();
            return false;
        }

        if (!empty($user->suspended)) {
            // Just in case some auth plugin suspended account.
            $failurereason = AUTH_LOGIN_SUSPENDED;
            // Trigger login failed event.
            $event = \core\event\user_login_failed::create(array('userid' => $user->id,
                    'other' => array('username' => $username, 'reason' => $failurereason)));
            $event->trigger();
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Suspended Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }

        login_attempt_valid($user);
        $failurereason = AUTH_LOGIN_OK;
        return $user;
    }

    // Failed if all the plugins have failed.
    if (debugging('', DEBUG_ALL)) {
        error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Failed Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
    }

    if ($user->id) {
        login_attempt_failed($user);
        $failurereason = AUTH_LOGIN_FAILED;
        // Trigger login failed event.
        $event = \core\event\user_login_failed::create(array('userid' => $user->id,
                'other' => array('username' => $username, 'reason' => $failurereason)));
        $event->trigger();
    } else {
        $failurereason = AUTH_LOGIN_NOUSER;
        // Trigger login failed event.
        $event = \core\event\user_login_failed::create(array('other' => array('username' => $username,
                'reason' => $failurereason)));
        $event->trigger();
    }

    return false;
}

/**
 * Call to complete the user login process after authenticate_user_login()
 * has succeeded. It will setup the $USER variable and other required bits
 * and pieces.
 *
 * NOTE:
 * - It will NOT log anything -- up to the caller to decide what to log.
 * - this function does not set any cookies any more!
 *
 * @param stdClass $user
 * @param array $extrauserinfo
 * @return stdClass A {@link $USER} object - BC only, do not use
 */
function complete_user_login($user, array $extrauserinfo = []) {
    global $CFG, $DB, $USER, $SESSION;

    \core\session\manager::login_user($user);

    // Reload preferences from DB.
    unset($USER->preference);
    check_user_preferences_loaded($USER);

    // Update login times.
    update_user_login_times();

    // Extra session prefs init.
    set_login_session_preferences();

    // Trigger login event.
    $event = \core\event\user_loggedin::create(
        array(
            'userid' => $USER->id,
            'objectid' => $USER->id,
            'other' => [
                'username' => $USER->username,
                'extrauserinfo' => $extrauserinfo
            ]
        )
    );
    $event->trigger();

    // Check if the user is using a new browser or session (a new MoodleSession cookie is set in that case).
    // If the user is accessing from the same IP, ignore everything (most of the time will be a new session in the same browser).
    // Skip Web Service requests, CLI scripts, AJAX scripts, and request from the mobile app itself.
    $loginip = getremoteaddr();
    $isnewip = isset($SESSION->userpreviousip) && $SESSION->userpreviousip != $loginip;
    $isvalidenv = (!WS_SERVER && !CLI_SCRIPT && !NO_MOODLE_COOKIES) || PHPUNIT_TEST;

    if (!empty($SESSION->isnewsessioncookie) && $isnewip && $isvalidenv && !\core_useragent::is_moodle_app()) {

        $logintime = time();
        $ismoodleapp = false;
        $useragent = \core_useragent::get_user_agent_string();

        // Schedule adhoc task to sent a login notification to the user.
        $task = new \core\task\send_login_notifications();
        $task->set_userid($USER->id);
        $task->set_custom_data(compact('ismoodleapp', 'useragent', 'loginip', 'logintime'));
        $task->set_component('core');
        \core\task\manager::queue_adhoc_task($task);
    }

    // Queue migrating the messaging data, if we need to.
    if (!get_user_preferences('core_message_migrate_data', false, $USER->id)) {
        // Check if there are any legacy messages to migrate.
        if (\core_message\helper::legacy_messages_exist($USER->id)) {
            \core_message\task\migrate_message_data::queue_task($USER->id);
        } else {
            set_user_preference('core_message_migrate_data', true, $USER->id);
        }
    }

    if (isguestuser()) {
        // No need to continue when user is THE guest.
        return $USER;
    }

    if (CLI_SCRIPT) {
        // We can redirect to password change URL only in browser.
        return $USER;
    }

    // Select password change url.
    $userauth = get_auth_plugin($USER->auth);

    // Check whether the user should be changing password.
    if (get_user_preferences('auth_forcepasswordchange', false)) {
        if ($userauth->can_change_password()) {
            if ($changeurl = $userauth->change_password_url()) {
                redirect($changeurl);
            } else {
                require_once($CFG->dirroot . '/login/lib.php');
                $SESSION->wantsurl = core_login_get_return_url();
                redirect($CFG->wwwroot.'/login/change_password.php');
            }
        } else {
            throw new \moodle_exception('nopasswordchangeforced', 'auth');
        }
    }
    return $USER;
}

/**
 * Check a password hash to see if it was hashed using the legacy hash algorithm (md5).
 *
 * @param string $password String to check.
 * @return boolean True if the $password matches the format of an md5 sum.
 */
function password_is_legacy_hash($password) {
    return (bool) preg_match('/^[0-9a-f]{32}$/', $password);
}

/**
 * Compare password against hash stored in user object to determine if it is valid.
 *
 * If necessary it also updates the stored hash to the current format.
 *
 * @param stdClass $user (Password property may be updated).
 * @param string $password Plain text password.
 * @return bool True if password is valid.
 */
function validate_internal_user_password($user, $password) {
    global $CFG;

    if ($user->password === AUTH_PASSWORD_NOT_CACHED) {
        // Internal password is not used at all, it can not validate.
        return false;
    }

    // If hash isn't a legacy (md5) hash, validate using the library function.
    if (!password_is_legacy_hash($user->password)) {
        return password_verify($password, $user->password);
    }

    // Otherwise we need to check for a legacy (md5) hash instead. If the hash
    // is valid we can then update it to the new algorithm.

    $sitesalt = isset($CFG->passwordsaltmain) ? $CFG->passwordsaltmain : '';
    $validated = false;

    if ($user->password === md5($password.$sitesalt)
            or $user->password === md5($password)
            or $user->password === md5(addslashes($password).$sitesalt)
            or $user->password === md5(addslashes($password))) {
        // Note: we are intentionally using the addslashes() here because we
        //       need to accept old password hashes of passwords with magic quotes.
        $validated = true;

    } else {
        for ($i=1; $i<=20; $i++) { // 20 alternative salts should be enough, right?
            $alt = 'passwordsaltalt'.$i;
            if (!empty($CFG->$alt)) {
                if ($user->password === md5($password.$CFG->$alt) or $user->password === md5(addslashes($password).$CFG->$alt)) {
                    $validated = true;
                    break;
                }
            }
        }
    }

    if ($validated) {
        // If the password matches the existing md5 hash, update to the
        // current hash algorithm while we have access to the user's password.
        update_internal_user_password($user, $password);
    }

    return $validated;
}

/**
 * Calculate hash for a plain text password.
 *
 * @param string $password Plain text password to be hashed.
 * @param bool $fasthash If true, use a low cost factor when generating the hash
 *                       This is much faster to generate but makes the hash
 *                       less secure. It is used when lots of hashes need to
 *                       be generated quickly.
 * @return string The hashed password.
 *
 * @throws moodle_exception If a problem occurs while generating the hash.
 */
function hash_internal_user_password($password, $fasthash = false) {
    global $CFG;

    // Set the cost factor to 4 for fast hashing, otherwise use default cost.
    $options = ($fasthash) ? array('cost' => 4) : array();

    $generatedhash = password_hash($password, PASSWORD_DEFAULT, $options);

    if ($generatedhash === false || $generatedhash === null) {
        throw new moodle_exception('Failed to generate password hash.');
    }

    return $generatedhash;
}

/**
 * Update password hash in user object (if necessary).
 *
 * The password is updated if:
 * 1. The password has changed (the hash of $user->password is different
 *    to the hash of $password).
 * 2. The existing hash is using an out-of-date algorithm (or the legacy
 *    md5 algorithm).
 *
 * Updating the password will modify the $user object and the database
 * record to use the current hashing algorithm.
 * It will remove Web Services user tokens too.
 *
 * @param stdClass $user User object (password property may be updated).
 * @param string $password Plain text password.
 * @param bool $fasthash If true, use a low cost factor when generating the hash
 *                       This is much faster to generate but makes the hash
 *                       less secure. It is used when lots of hashes need to
 *                       be generated quickly.
 * @return bool Always returns true.
 */
function update_internal_user_password($user, $password, $fasthash = false) {
    global $CFG, $DB;

    // Figure out what the hashed password should be.
    if (!isset($user->auth)) {
        debugging('User record in update_internal_user_password() must include field auth',
                DEBUG_DEVELOPER);
        $user->auth = $DB->get_field('user', 'auth', array('id' => $user->id));
    }
    $authplugin = get_auth_plugin($user->auth);
    if ($authplugin->prevent_local_passwords()) {
        $hashedpassword = AUTH_PASSWORD_NOT_CACHED;
    } else {
        $hashedpassword = hash_internal_user_password($password, $fasthash);
    }

    $algorithmchanged = false;

    if ($hashedpassword === AUTH_PASSWORD_NOT_CACHED) {
        // Password is not cached, update it if not set to AUTH_PASSWORD_NOT_CACHED.
        $passwordchanged = ($user->password !== $hashedpassword);

    } else if (isset($user->password)) {
        // If verification fails then it means the password has changed.
        $passwordchanged = !password_verify($password, $user->password);
        $algorithmchanged = password_needs_rehash($user->password, PASSWORD_DEFAULT);
    } else {
        // While creating new user, password in unset in $user object, to avoid
        // saving it with user_create()
        $passwordchanged = true;
    }

    if ($passwordchanged || $algorithmchanged) {
        $DB->set_field('user', 'password',  $hashedpassword, array('id' => $user->id));
        $user->password = $hashedpassword;

        // Trigger event.
        $user = $DB->get_record('user', array('id' => $user->id));
        \core\event\user_password_updated::create_from_user($user)->trigger();

        // Remove WS user tokens.
        if (!empty($CFG->passwordchangetokendeletion)) {
            require_once($CFG->dirroot.'/webservice/lib.php');
            webservice::delete_user_ws_tokens($user->id);
        }
    }

    return true;
}

/**
 * Get a complete user record, which includes all the info in the user record.
 *
 * Intended for setting as $USER session variable
 *
 * @param string $field The user field to be checked for a given value.
 * @param string $value The value to match for $field.
 * @param int $mnethostid
 * @param bool $throwexception If true, it will throw an exception when there's no record found or when there are multiple records
 *                              found. Otherwise, it will just return false.
 * @return mixed False, or A {@link $USER} object.
 */
function get_complete_user_data($field, $value, $mnethostid = null, $throwexception = false) {
    global $CFG, $DB;

    if (!$field || !$value) {
        return false;
    }

    // Change the field to lowercase.
    $field = core_text::strtolower($field);

    // List of case insensitive fields.
    $caseinsensitivefields = ['email'];

    // Username input is forced to lowercase and should be case sensitive.
    if ($field == 'username') {
        $value = core_text::strtolower($value);
    }

    // Build the WHERE clause for an SQL query.
    $params = array('fieldval' => $value);

    // Do a case-insensitive query, if necessary. These are generally very expensive. The performance can be improved on some DBs
    // such as MySQL by pre-filtering users with accent-insensitive subselect.
    if (in_array($field, $caseinsensitivefields)) {
        $fieldselect = $DB->sql_equal($field, ':fieldval', false);
        $idsubselect = $DB->sql_equal($field, ':fieldval2', false, false);
        $params['fieldval2'] = $value;
    } else {
        $fieldselect = "$field = :fieldval";
        $idsubselect = '';
    }
    $constraints = "$fieldselect AND deleted <> 1";

    // If we are loading user data based on anything other than id,
    // we must also restrict our search based on mnet host.
    if ($field != 'id') {
        if (empty($mnethostid)) {
            // If empty, we restrict to local users.
            $mnethostid = $CFG->mnet_localhost_id;
        }
    }
    if (!empty($mnethostid)) {
        $params['mnethostid'] = $mnethostid;
        $constraints .= " AND mnethostid = :mnethostid";
    }

    if ($idsubselect) {
        $constraints .= " AND id IN (SELECT id FROM {user} WHERE {$idsubselect})";
    }

    // Get all the basic user data.
    try {
        // Make sure that there's only a single record that matches our query.
        // For example, when fetching by email, multiple records might match the query as there's no guarantee that email addresses
        // are unique. Therefore we can't reliably tell whether the user profile data that we're fetching is the correct one.
        $user = $DB->get_record_select('user', $constraints, $params, '*', MUST_EXIST);
    } catch (dml_exception $exception) {
        if ($throwexception) {
            throw $exception;
        } else {
            // Return false when no records or multiple records were found.
            return false;
        }
    }

    // Get various settings and preferences.

    // Preload preference cache.
    check_user_preferences_loaded($user);

    // Load course enrolment related stuff.
    $user->lastcourseaccess    = array(); // During last session.
    $user->currentcourseaccess = array(); // During current session.
    if ($lastaccesses = $DB->get_records('user_lastaccess', array('userid' => $user->id))) {
        foreach ($lastaccesses as $lastaccess) {
            $user->lastcourseaccess[$lastaccess->courseid] = $lastaccess->timeaccess;
        }
    }

    // Add cohort theme.
    if (!empty($CFG->allowcohortthemes)) {
        require_once($CFG->dirroot . '/cohort/lib.php');
        if ($cohorttheme = cohort_get_user_cohort_theme($user->id)) {
            $user->cohorttheme = $cohorttheme;
        }
    }

    // Add the custom profile fields to the user record.
    $user->profile = array();
    if (!isguestuser($user)) {
        require_once($CFG->dirroot.'/user/profile/lib.php');
        profile_load_custom_fields($user);
    }

    // Rewrite some variables if necessary.
    if (!empty($user->description)) {
        // No need to cart all of it around.
        $user->description = true;
    }
    if (isguestuser($user)) {
        // Guest language always same as site.
        $user->lang = get_newuser_language();
        // Name always in current language.
        $user->firstname = get_string('guestuser');
        $user->lastname = ' ';
    }

    return $user;
}

/**
 * Validate a password against the configured password policy
 *
 * @param string $password the password to be checked against the password policy
 * @param string $errmsg the error message to display when the password doesn't comply with the policy.
 * @param stdClass $user the user object to perform password validation against. Defaults to null if not provided.
 *
 * @return bool true if the password is valid according to the policy. false otherwise.
 */
function check_password_policy($password, &$errmsg, $user = null) {
    global $CFG;

    if (!empty($CFG->passwordpolicy)) {
        $errmsg = '';
        if (core_text::strlen($password) < $CFG->minpasswordlength) {
            $errmsg .= '<div>'. get_string('errorminpasswordlength', 'auth', $CFG->minpasswordlength) .'</div>';
        }
        if (preg_match_all('/[[:digit:]]/u', $password, $matches) < $CFG->minpassworddigits) {
            $errmsg .= '<div>'. get_string('errorminpassworddigits', 'auth', $CFG->minpassworddigits) .'</div>';
        }
        if (preg_match_all('/[[:lower:]]/u', $password, $matches) < $CFG->minpasswordlower) {
            $errmsg .= '<div>'. get_string('errorminpasswordlower', 'auth', $CFG->minpasswordlower) .'</div>';
        }
        if (preg_match_all('/[[:upper:]]/u', $password, $matches) < $CFG->minpasswordupper) {
            $errmsg .= '<div>'. get_string('errorminpasswordupper', 'auth', $CFG->minpasswordupper) .'</div>';
        }
        if (preg_match_all('/[^[:upper:][:lower:][:digit:]]/u', $password, $matches) < $CFG->minpasswordnonalphanum) {
            $errmsg .= '<div>'. get_string('errorminpasswordnonalphanum', 'auth', $CFG->minpasswordnonalphanum) .'</div>';
        }
        if (!check_consecutive_identical_characters($password, $CFG->maxconsecutiveidentchars)) {
            $errmsg .= '<div>'. get_string('errormaxconsecutiveidentchars', 'auth', $CFG->maxconsecutiveidentchars) .'</div>';
        }

        // Fire any additional password policy functions from plugins.
        // Plugin functions should output an error message string or empty string for success.
        $pluginsfunction = get_plugins_with_function('check_password_policy');
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginerr = $pluginfunction($password, $user);
                if ($pluginerr) {
                    $errmsg .= '<div>'. $pluginerr .'</div>';
                }
            }
        }
    }

    if ($errmsg == '') {
        return true;
    } else {
        return false;
    }
}


/**
 * When logging in, this function is run to set certain preferences for the current SESSION.
 */
function set_login_session_preferences() {
    global $SESSION;

    $SESSION->justloggedin = true;

    unset($SESSION->lang);
    unset($SESSION->forcelang);
    unset($SESSION->load_navigation_admin);
}


/**
 * Delete a course, including all related data from the database, and any associated files.
 *
 * @param mixed $courseorid The id of the course or course object to delete.
 * @param bool $showfeedback Whether to display notifications of each action the function performs.
 * @return bool true if all the removals succeeded. false if there were any failures. If this
 *             method returns false, some of the removals will probably have succeeded, and others
 *             failed, but you have no way of knowing which.
 */
function delete_course($courseorid, $showfeedback = true) {
    global $DB;

    if (is_object($courseorid)) {
        $courseid = $courseorid->id;
        $course   = $courseorid;
    } else {
        $courseid = $courseorid;
        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            return false;
        }
    }
    $context = context_course::instance($courseid);

    // Frontpage course can not be deleted!!
    if ($courseid == SITEID) {
        return false;
    }

    // Allow plugins to use this course before we completely delete it.
    if ($pluginsfunction = get_plugins_with_function('pre_course_delete')) {
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($course);
            }
        }
    }

    // Tell the search manager we are about to delete a course. This prevents us sending updates
    // for each individual context being deleted.
    \core_search\manager::course_deleting_start($courseid);

    $handler = core_course\customfield\course_handler::create();
    $handler->delete_instance($courseid);

    // Make the course completely empty.
    remove_course_contents($courseid, $showfeedback);

    // Delete the course and related context instance.
    context_helper::delete_instance(CONTEXT_COURSE, $courseid);

    $DB->delete_records("course", array("id" => $courseid));
    $DB->delete_records("course_format_options", array("courseid" => $courseid));

    // Reset all course related caches here.
    core_courseformat\base::reset_course_cache($courseid);

    // Tell search that we have deleted the course so it can delete course data from the index.
    \core_search\manager::course_deleting_finish($courseid);

    // Trigger a course deleted event.
    $event = \core\event\course_deleted::create(array(
        'objectid' => $course->id,
        'context' => $context,
        'other' => array(
            'shortname' => $course->shortname,
            'fullname' => $course->fullname,
            'idnumber' => $course->idnumber
            )
    ));
    $event->add_record_snapshot('course', $course);
    $event->trigger();

    return true;
}

/**
 * Clear a course out completely, deleting all content but don't delete the course itself.
 *
 * This function does not verify any permissions.
 *
 * Please note this function also deletes all user enrolments,
 * enrolment instances and role assignments by default.
 *
 * $options:
 *  - 'keep_roles_and_enrolments' - false by default
 *  - 'keep_groups_and_groupings' - false by default
 *
 * @param int $courseid The id of the course that is being deleted
 * @param bool $showfeedback Whether to display notifications of each action the function performs.
 * @param array $options extra options
 * @return bool true if all the removals succeeded. false if there were any failures. If this
 *             method returns false, some of the removals will probably have succeeded, and others
 *             failed, but you have no way of knowing which.
 */
function remove_course_contents($courseid, $showfeedback = true, array $options = null) {
    global $CFG, $DB, $OUTPUT;

    require_once($CFG->libdir.'/badgeslib.php');
    require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->libdir.'/questionlib.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/group/lib.php');
    require_once($CFG->dirroot.'/comment/lib.php');
    require_once($CFG->dirroot.'/rating/lib.php');
    require_once($CFG->dirroot.'/notes/lib.php');

    // Handle course badges.
    badges_handle_course_deletion($courseid);

    // NOTE: these concatenated strings are suboptimal, but it is just extra info...
    $strdeleted = get_string('deleted').' - ';

    // Some crazy wishlist of stuff we should skip during purging of course content.
    $options = (array)$options;

    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $coursecontext = context_course::instance($courseid);
    $fs = get_file_storage();

    // Delete course completion information, this has to be done before grades and enrols.
    $cc = new completion_info($course);
    $cc->clear_criteria();
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.get_string('completion', 'completion'), 'notifysuccess');
    }

    // Remove all data from gradebook - this needs to be done before course modules
    // because while deleting this information, the system may need to reference
    // the course modules that own the grades.
    remove_course_grades($courseid, $showfeedback);
    remove_grade_letters($coursecontext, $showfeedback);

    // Delete course blocks in any all child contexts,
    // they may depend on modules so delete them first.
    $childcontexts = $coursecontext->get_child_contexts(); // Returns all subcontexts since 2.2.
    foreach ($childcontexts as $childcontext) {
        blocks_delete_all_for_context($childcontext->id);
    }
    unset($childcontexts);
    blocks_delete_all_for_context($coursecontext->id);
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.get_string('type_block_plural', 'plugin'), 'notifysuccess');
    }

    $DB->set_field('course_modules', 'deletioninprogress', '1', ['course' => $courseid]);
    rebuild_course_cache($courseid, true);

    // Get the list of all modules that are properly installed.
    $allmodules = $DB->get_records_menu('modules', array(), '', 'name, id');

    // Delete every instance of every module,
    // this has to be done before deleting of course level stuff.
    $locations = core_component::get_plugin_list('mod');
    foreach ($locations as $modname => $moddir) {
        if ($modname === 'NEWMODULE') {
            continue;
        }
        if (array_key_exists($modname, $allmodules)) {
            $sql = "SELECT cm.*, m.id AS modinstance, m.name, '$modname' AS modname
              FROM {".$modname."} m
                   LEFT JOIN {course_modules} cm ON cm.instance = m.id AND cm.module = :moduleid
             WHERE m.course = :courseid";
            $instances = $DB->get_records_sql($sql, array('courseid' => $course->id,
                'modulename' => $modname, 'moduleid' => $allmodules[$modname]));

            include_once("$moddir/lib.php");                 // Shows php warning only if plugin defective.
            $moddelete = $modname .'_delete_instance';       // Delete everything connected to an instance.

            if ($instances) {
                foreach ($instances as $cm) {
                    if ($cm->id) {
                        // Delete activity context questions and question categories.
                        question_delete_activity($cm);
                        // Notify the competency subsystem.
                        \core_competency\api::hook_course_module_deleted($cm);

                        // Delete all tag instances associated with the instance of this module.
                        core_tag_tag::delete_instances("mod_{$modname}", null, context_module::instance($cm->id)->id);
                        core_tag_tag::remove_all_item_tags('core', 'course_modules', $cm->id);
                    }
                    if (function_exists($moddelete)) {
                        // This purges all module data in related tables, extra user prefs, settings, etc.
                        $moddelete($cm->modinstance);
                    } else {
                        // NOTE: we should not allow installation of modules with missing delete support!
                        debugging("Defective module '$modname' detected when deleting course contents: missing function $moddelete()!");
                        $DB->delete_records($modname, array('id' => $cm->modinstance));
                    }

                    if ($cm->id) {
                        // Delete cm and its context - orphaned contexts are purged in cron in case of any race condition.
                        context_helper::delete_instance(CONTEXT_MODULE, $cm->id);
                        $DB->delete_records('course_modules_completion', ['coursemoduleid' => $cm->id]);
                        $DB->delete_records('course_modules_viewed', ['coursemoduleid' => $cm->id]);
                        $DB->delete_records('course_modules', array('id' => $cm->id));
                        rebuild_course_cache($cm->course, true);
                    }
                }
            }
            if ($instances and $showfeedback) {
                echo $OUTPUT->notification($strdeleted.get_string('pluginname', $modname), 'notifysuccess');
            }
        } else {
            // Ooops, this module is not properly installed, force-delete it in the next block.
        }
    }

    // We have tried to delete everything the nice way - now let's force-delete any remaining module data.

    // Delete completion defaults.
    $DB->delete_records("course_completion_defaults", array("course" => $courseid));

    // Remove all data from availability and completion tables that is associated
    // with course-modules belonging to this course. Note this is done even if the
    // features are not enabled now, in case they were enabled previously.
    $DB->delete_records_subquery('course_modules_completion', 'coursemoduleid', 'id',
            'SELECT id from {course_modules} WHERE course = ?', [$courseid]);
    $DB->delete_records_subquery('course_modules_viewed', 'coursemoduleid', 'id',
        'SELECT id from {course_modules} WHERE course = ?', [$courseid]);

    // Remove course-module data that has not been removed in modules' _delete_instance callbacks.
    $cms = $DB->get_records('course_modules', array('course' => $course->id));
    $allmodulesbyid = array_flip($allmodules);
    foreach ($cms as $cm) {
        if (array_key_exists($cm->module, $allmodulesbyid)) {
            try {
                $DB->delete_records($allmodulesbyid[$cm->module], array('id' => $cm->instance));
            } catch (Exception $e) {
                // Ignore weird or missing table problems.
            }
        }
        context_helper::delete_instance(CONTEXT_MODULE, $cm->id);
        $DB->delete_records('course_modules', array('id' => $cm->id));
        rebuild_course_cache($cm->course, true);
    }

    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.get_string('type_mod_plural', 'plugin'), 'notifysuccess');
    }

    // Delete questions and question categories.
    question_delete_course($course);
    if ($showfeedback) {
        echo $OUTPUT->notification($strdeleted.get_string('questions', 'question'), 'notifysuccess');
    }

    // Delete content bank contents.
    $cb = new \core_contentbank\contentbank();
    $cbdeleted = $cb->delete_contents($coursecontext);
    if ($showfeedback && $cbdeleted) {
        echo $OUTPUT->notification($strdeleted.get_string('contentbank', 'contentbank'), 'notifysuccess');
    }

    // Make sure there are no subcontexts left - all valid blocks and modules should be already gone.
    $childcontexts = $coursecontext->get_child_contexts(); // Returns all subcontexts since 2.2.
    foreach ($childcontexts as $childcontext) {
        $childcontext->delete();
    }
    unset($childcontexts);

    // Remove roles and enrolments by default.
    if (empty($options['keep_roles_and_enrolments'])) {
        // This hack is used in restore when deleting contents of existing course.
        // During restore, we should remove only enrolment related data that the user performing the restore has a
        // permission to remove.
        $userid = $options['userid'] ?? null;
        enrol_course_delete($course, $userid);
        role_unassign_all(array('contextid' => $coursecontext->id, 'component' => ''), true);
        if ($showfeedback) {
            echo $OUTPUT->notification($strdeleted.get_string('type_enrol_plural', 'plugin'), 'notifysuccess');
        }
    }

    // Delete any groups, removing members and grouping/course links first.
    if (empty($options['keep_groups_and_groupings'])) {
        groups_delete_groupings($course->id, $showfeedback);
        groups_delete_groups($course->id, $showfeedback);
    }

    // Filters be gone!
    filter_delete_all_for_context($coursecontext->id);

    // Notes, you shall not pass!
    note_delete_all($course->id);

    // Die comments!
    comment::delete_comments($coursecontext->id);

    // Ratings are history too.
    $delopt = new stdclass();
    $delopt->contextid = $coursecontext->id;
    $rm = new rating_manager();
    $rm->delete_ratings($delopt);

    // Delete course tags.
    core_tag_tag::remove_all_item_tags('core', 'course', $course->id);

    // Give the course format the opportunity to remove its obscure data.
    $format = course_get_format($course);
    $format->delete_format_data();

    // Notify the competency subsystem.
    \core_competency\api::hook_course_deleted($course);

    // Delete calendar events.
    $DB->delete_records('event', array('courseid' => $course->id));
    $fs->delete_area_files($coursecontext->id, 'calendar');

    // Delete all related records in other core tables that may have a courseid
    // This array stores the tables that need to be cleared, as
    // table_name => column_name that contains the course id.
    $tablestoclear = array(
        'backup_courses' => 'courseid',  // Scheduled backup stuff.
        'user_lastaccess' => 'courseid', // User access info.
    );
    foreach ($tablestoclear as $table => $col) {
        $DB->delete_records($table, array($col => $course->id));
    }

    // Delete all course backup files.
    $fs->delete_area_files($coursecontext->id, 'backup');

    // Cleanup course record - remove links to deleted stuff.
    $oldcourse = new stdClass();
    $oldcourse->id               = $course->id;
    $oldcourse->summary          = '';
    $oldcourse->cacherev         = 0;
    $oldcourse->legacyfiles      = 0;
    if (!empty($options['keep_groups_and_groupings'])) {
        $oldcourse->defaultgroupingid = 0;
    }
    $DB->update_record('course', $oldcourse);

    // Delete course sections.
    $DB->delete_records('course_sections', array('course' => $course->id));

    // Delete legacy, section and any other course files.
    $fs->delete_area_files($coursecontext->id, 'course'); // Files from summary and section.

    // Delete all remaining stuff linked to context such as files, comments, ratings, etc.
    if (empty($options['keep_roles_and_enrolments']) and empty($options['keep_groups_and_groupings'])) {
        // Easy, do not delete the context itself...
        $coursecontext->delete_content();
    } else {
        // Hack alert!!!!
        // We can not drop all context stuff because it would bork enrolments and roles,
        // there might be also files used by enrol plugins...
    }

    // Delete legacy files - just in case some files are still left there after conversion to new file api,
    // also some non-standard unsupported plugins may try to store something there.
    fulldelete($CFG->dataroot.'/'.$course->id);

    // Delete from cache to reduce the cache size especially makes sense in case of bulk course deletion.
    course_modinfo::purge_course_cache($courseid);

    // Trigger a course content deleted event.
    $event = \core\event\course_content_deleted::create(array(
        'objectid' => $course->id,
        'context' => $coursecontext,
        'other' => array('shortname' => $course->shortname,
                         'fullname' => $course->fullname,
                         'options' => $options) // Passing this for legacy reasons.
    ));
    $event->add_record_snapshot('course', $course);
    $event->trigger();

    return true;
}

/**
 * Change dates in module - used from course reset.
 *
 * @param string $modname forum, assignment, etc
 * @param array $fields array of date fields from mod table
 * @param int $timeshift time difference
 * @param int $courseid
 * @param int $modid (Optional) passed if specific mod instance in course needs to be updated.
 * @return bool success
 */
function shift_course_mod_dates($modname, $fields, $timeshift, $courseid, $modid = 0) {
    global $CFG, $DB;
    include_once($CFG->dirroot.'/mod/'.$modname.'/lib.php');

    $return = true;
    $params = array($timeshift, $courseid);
    foreach ($fields as $field) {
        $updatesql = "UPDATE {".$modname."}
                          SET $field = $field + ?
                        WHERE course=? AND $field<>0";
        if ($modid) {
            $updatesql .= ' AND id=?';
            $params[] = $modid;
        }
        $return = $DB->execute($updatesql, $params) && $return;
    }

    return $return;
}

/**
 * This function will empty a course of user data.
 * It will retain the activities and the structure of the course.
 *
 * @param object $data an object containing all the settings including courseid (without magic quotes)
 * @return array status array of array component, item, error
 */
function reset_course_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->dirroot.'/completion/criteria/completion_criteria_date.php');
    require_once($CFG->dirroot.'/group/lib.php');

    $data->courseid = $data->id;
    $context = context_course::instance($data->courseid);

    $eventparams = array(
        'context' => $context,
        'courseid' => $data->id,
        'other' => array(
            'reset_options' => (array) $data
        )
    );
    $event = \core\event\course_reset_started::create($eventparams);
    $event->trigger();

    // Calculate the time shift of dates.
    if (!empty($data->reset_start_date)) {
        // Time part of course startdate should be zero.
        $data->timeshift = $data->reset_start_date - usergetmidnight($data->reset_start_date_old);
    } else {
        $data->timeshift = 0;
    }

    // Result array: component, item, error.
    $status = array();

    // Start the resetting.
    $componentstr = get_string('general');

    // Move the course start time.
    if (!empty($data->reset_start_date) and $data->timeshift) {
        // Change course start data.
        $DB->set_field('course', 'startdate', $data->reset_start_date, array('id' => $data->courseid));
        // Update all course and group events - do not move activity events.
        $updatesql = "UPDATE {event}
                         SET timestart = timestart + ?
                       WHERE courseid=? AND instance=0";
        $DB->execute($updatesql, array($data->timeshift, $data->courseid));

        // Update any date activity restrictions.
        if ($CFG->enableavailability) {
            \availability_date\condition::update_all_dates($data->courseid, $data->timeshift);
        }

        // Update completion expected dates.
        if ($CFG->enablecompletion) {
            $modinfo = get_fast_modinfo($data->courseid);
            $changed = false;
            foreach ($modinfo->get_cms() as $cm) {
                if ($cm->completion && !empty($cm->completionexpected)) {
                    $DB->set_field('course_modules', 'completionexpected', $cm->completionexpected + $data->timeshift,
                        array('id' => $cm->id));
                    $changed = true;
                }
            }

            // Clear course cache if changes made.
            if ($changed) {
                rebuild_course_cache($data->courseid, true);
            }

            // Update course date completion criteria.
            \completion_criteria_date::update_date($data->courseid, $data->timeshift);
        }

        $status[] = array('component' => $componentstr, 'item' => get_string('datechanged'), 'error' => false);
    }

    if (!empty($data->reset_end_date)) {
        // If the user set a end date value respect it.
        $DB->set_field('course', 'enddate', $data->reset_end_date, array('id' => $data->courseid));
    } else if ($data->timeshift > 0 && $data->reset_end_date_old) {
        // If there is a time shift apply it to the end date as well.
        $enddate = $data->reset_end_date_old + $data->timeshift;
        $DB->set_field('course', 'enddate', $enddate, array('id' => $data->courseid));
    }

    if (!empty($data->reset_events)) {
        $DB->delete_records('event', array('courseid' => $data->courseid));
        $status[] = array('component' => $componentstr, 'item' => get_string('deleteevents', 'calendar'), 'error' => false);
    }

    if (!empty($data->reset_notes)) {
        require_once($CFG->dirroot.'/notes/lib.php');
        note_delete_all($data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('deletenotes', 'notes'), 'error' => false);
    }

    if (!empty($data->delete_blog_associations)) {
        require_once($CFG->dirroot.'/blog/lib.php');
        blog_remove_associations_for_course($data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('deleteblogassociations', 'blog'), 'error' => false);
    }

    if (!empty($data->reset_completion)) {
        // Delete course and activity completion information.
        $course = $DB->get_record('course', array('id' => $data->courseid));
        $cc = new completion_info($course);
        $cc->delete_all_completion_data();
        $status[] = array('component' => $componentstr,
                'item' => get_string('deletecompletiondata', 'completion'), 'error' => false);
    }

    if (!empty($data->reset_competency_ratings)) {
        \core_competency\api::hook_course_reset_competency_ratings($data->courseid);
        $status[] = array('component' => $componentstr,
            'item' => get_string('deletecompetencyratings', 'core_competency'), 'error' => false);
    }

    $componentstr = get_string('roles');

    if (!empty($data->reset_roles_overrides)) {
        $children = $context->get_child_contexts();
        foreach ($children as $child) {
            $child->delete_capabilities();
        }
        $context->delete_capabilities();
        $status[] = array('component' => $componentstr, 'item' => get_string('deletecourseoverrides', 'role'), 'error' => false);
    }

    if (!empty($data->reset_roles_local)) {
        $children = $context->get_child_contexts();
        foreach ($children as $child) {
            role_unassign_all(array('contextid' => $child->id));
        }
        $status[] = array('component' => $componentstr, 'item' => get_string('deletelocalroles', 'role'), 'error' => false);
    }

    // First unenrol users - this cleans some of related user data too, such as forum subscriptions, tracking, etc.
    $data->unenrolled = array();
    if (!empty($data->unenrol_users)) {
        $plugins = enrol_get_plugins(true);
        $instances = enrol_get_instances($data->courseid, true);
        foreach ($instances as $key => $instance) {
            if (!isset($plugins[$instance->enrol])) {
                unset($instances[$key]);
                continue;
            }
        }

        $usersroles = enrol_get_course_users_roles($data->courseid);
        foreach ($data->unenrol_users as $withroleid) {
            if ($withroleid) {
                $sql = "SELECT ue.*
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                          JOIN {context} c ON (c.contextlevel = :courselevel AND c.instanceid = e.courseid)
                          JOIN {role_assignments} ra ON (ra.contextid = c.id AND ra.roleid = :roleid AND ra.userid = ue.userid)";
                $params = array('courseid' => $data->courseid, 'roleid' => $withroleid, 'courselevel' => CONTEXT_COURSE);

            } else {
                // Without any role assigned at course context.
                $sql = "SELECT ue.*
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                          JOIN {context} c ON (c.contextlevel = :courselevel AND c.instanceid = e.courseid)
                     LEFT JOIN {role_assignments} ra ON (ra.contextid = c.id AND ra.userid = ue.userid)
                         WHERE ra.id IS null";
                $params = array('courseid' => $data->courseid, 'courselevel' => CONTEXT_COURSE);
            }

            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (!isset($instances[$ue->enrolid])) {
                    continue;
                }
                $instance = $instances[$ue->enrolid];
                $plugin = $plugins[$instance->enrol];
                if (!$plugin->allow_unenrol($instance) and !$plugin->allow_unenrol_user($instance, $ue)) {
                    continue;
                }

                if ($withroleid && count($usersroles[$ue->userid]) > 1) {
                    // If we don't remove all roles and user has more than one role, just remove this role.
                    role_unassign($withroleid, $ue->userid, $context->id);

                    unset($usersroles[$ue->userid][$withroleid]);
                } else {
                    // If we remove all roles or user has only one role, unenrol user from course.
                    $plugin->unenrol_user($instance, $ue->userid);
                }
                $data->unenrolled[$ue->userid] = $ue->userid;
            }
            $rs->close();
        }
    }
    if (!empty($data->unenrolled)) {
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('unenrol', 'enrol').' ('.count($data->unenrolled).')',
            'error' => false
        );
    }

    $componentstr = get_string('groups');

    // Remove all group members.
    if (!empty($data->reset_groups_members)) {
        groups_delete_group_members($data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('removegroupsmembers', 'group'), 'error' => false);
    }

    // Remove all groups.
    if (!empty($data->reset_groups_remove)) {
        groups_delete_groups($data->courseid, false);
        $status[] = array('component' => $componentstr, 'item' => get_string('deleteallgroups', 'group'), 'error' => false);
    }

    // Remove all grouping members.
    if (!empty($data->reset_groupings_members)) {
        groups_delete_groupings_groups($data->courseid, false);
        $status[] = array('component' => $componentstr, 'item' => get_string('removegroupingsmembers', 'group'), 'error' => false);
    }

    // Remove all groupings.
    if (!empty($data->reset_groupings_remove)) {
        groups_delete_groupings($data->courseid, false);
        $status[] = array('component' => $componentstr, 'item' => get_string('deleteallgroupings', 'group'), 'error' => false);
    }

    // Look in every instance of every module for data to delete.
    $unsupportedmods = array();
    if ($allmods = $DB->get_records('modules') ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = $CFG->dirroot.'/mod/'. $modname.'/lib.php';
            $moddeleteuserdata = $modname.'_reset_userdata';   // Function to delete user data.
            if (file_exists($modfile)) {
                if (!$DB->count_records($modname, array('course' => $data->courseid))) {
                    continue; // Skip mods with no instances.
                }
                include_once($modfile);
                if (function_exists($moddeleteuserdata)) {
                    $modstatus = $moddeleteuserdata($data);
                    if (is_array($modstatus)) {
                        $status = array_merge($status, $modstatus);
                    } else {
                        debugging('Module '.$modname.' returned incorrect staus - must be an array!');
                    }
                } else {
                    $unsupportedmods[] = $mod;
                }
            } else {
                debugging('Missing lib.php in '.$modname.' module!');
            }
            // Update calendar events for all modules.
            course_module_bulk_update_calendar_events($modname, $data->courseid);
        }
    }

    // Mention unsupported mods.
    if (!empty($unsupportedmods)) {
        foreach ($unsupportedmods as $mod) {
            $status[] = array(
                'component' => get_string('modulenameplural', $mod->name),
                'item' => '',
                'error' => get_string('resetnotimplemented')
            );
        }
    }

    $componentstr = get_string('gradebook', 'grades');
    // Reset gradebook,.
    if (!empty($data->reset_gradebook_items)) {
        remove_course_grades($data->courseid, false);
        grade_grab_course_grades($data->courseid);
        grade_regrade_final_grades($data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('removeallcourseitems', 'grades'), 'error' => false);

    } else if (!empty($data->reset_gradebook_grades)) {
        grade_course_reset($data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('removeallcoursegrades', 'grades'), 'error' => false);
    }
    // Reset comments.
    if (!empty($data->reset_comments)) {
        require_once($CFG->dirroot.'/comment/lib.php');
        comment::reset_course_page_comments($context);
    }

    $event = \core\event\course_reset_ended::create($eventparams);
    $event->trigger();

    return $status;
}

/**
 * Generate an email processing address.
 *
 * @param int $modid
 * @param string $modargs
 * @return string Returns email processing address
 */
function generate_email_processing_address($modid, $modargs) {
    global $CFG;

    $header = $CFG->mailprefix . substr(base64_encode(pack('C', $modid)), 0, 2).$modargs;
    return $header . substr(md5($header.get_site_identifier()), 0, 16).'@'.$CFG->maildomain;
}

/**
 * ?
 *
 * @todo Finish documenting this function
 *
 * @param string $modargs
 * @param string $body Currently unused
 */
function moodle_process_email($modargs, $body) {
    global $DB;

    // The first char should be an unencoded letter. We'll take this as an action.
    switch ($modargs[0]) {
        case 'B': { // Bounce.
            list(, $userid) = unpack('V', base64_decode(substr($modargs, 1, 8)));
            if ($user = $DB->get_record("user", array('id' => $userid), "id,email")) {
                // Check the half md5 of their email.
                $md5check = substr(md5($user->email), 0, 16);
                if ($md5check == substr($modargs, -16)) {
                    set_bounce_count($user);
                }
                // Else maybe they've already changed it?
            }
        }
        break;
        // Maybe more later?
    }
}

// CORRESPONDENCE.

/**
 * Get mailer instance, enable buffering, flush buffer or disable buffering.
 *
 * @param string $action 'get', 'buffer', 'close' or 'flush'
 * @return moodle_phpmailer|null mailer instance if 'get' used or nothing
 */
function get_mailer($action='get') {
    global $CFG;

    /** @var moodle_phpmailer $mailer */
    static $mailer  = null;
    static $counter = 0;

    if (!isset($CFG->smtpmaxbulk)) {
        $CFG->smtpmaxbulk = 1;
    }

    if ($action == 'get') {
        $prevkeepalive = false;

        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            if ($counter < $CFG->smtpmaxbulk and !$mailer->isError()) {
                $counter++;
                // Reset the mailer.
                $mailer->Priority         = 3;
                $mailer->CharSet          = 'UTF-8'; // Our default.
                $mailer->ContentType      = "text/plain";
                $mailer->Encoding         = "8bit";
                $mailer->From             = "root@localhost";
                $mailer->FromName         = "Root User";
                $mailer->Sender           = "";
                $mailer->Subject          = "";
                $mailer->Body             = "";
                $mailer->AltBody          = "";
                $mailer->ConfirmReadingTo = "";

                $mailer->clearAllRecipients();
                $mailer->clearReplyTos();
                $mailer->clearAttachments();
                $mailer->clearCustomHeaders();
                return $mailer;
            }

            $prevkeepalive = $mailer->SMTPKeepAlive;
            get_mailer('flush');
        }

        require_once($CFG->libdir.'/phpmailer/moodle_phpmailer.php');
        $mailer = new moodle_phpmailer();

        $counter = 1;

        if ($CFG->smtphosts == 'qmail') {
            // Use Qmail system.
            $mailer->isQmail();

        } else if (empty($CFG->smtphosts)) {
            // Use PHP mail() = sendmail.
            $mailer->isMail();

        } else {
            // Use SMTP directly.
            $mailer->isSMTP();
            if (!empty($CFG->debugsmtp) && (!empty($CFG->debugdeveloper))) {
                $mailer->SMTPDebug = 3;
            }
            // Specify main and backup servers.
            $mailer->Host          = $CFG->smtphosts;
            // Specify secure connection protocol.
            $mailer->SMTPSecure    = $CFG->smtpsecure;
            // Use previous keepalive.
            $mailer->SMTPKeepAlive = $prevkeepalive;

            if ($CFG->smtpuser) {
                // Use SMTP authentication.
                $mailer->SMTPAuth = true;
                $mailer->Username = $CFG->smtpuser;
                $mailer->Password = $CFG->smtppass;
            }
        }

        return $mailer;
    }

    $nothing = null;

    // Keep smtp session open after sending.
    if ($action == 'buffer') {
        if (!empty($CFG->smtpmaxbulk)) {
            get_mailer('flush');
            $m = get_mailer();
            if ($m->Mailer == 'smtp') {
                $m->SMTPKeepAlive = true;
            }
        }
        return $nothing;
    }

    // Close smtp session, but continue buffering.
    if ($action == 'flush') {
        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            if (!empty($mailer->SMTPDebug)) {
                echo '<pre>'."\n";
            }
            $mailer->SmtpClose();
            if (!empty($mailer->SMTPDebug)) {
                echo '</pre>';
            }
        }
        return $nothing;
    }

    // Close smtp session, do not buffer anymore.
    if ($action == 'close') {
        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            get_mailer('flush');
            $mailer->SMTPKeepAlive = false;
        }
        $mailer = null; // Better force new instance.
        return $nothing;
    }
}

/**
 * A helper function to test for email diversion
 *
 * @param string $email
 * @return bool Returns true if the email should be diverted
 */
function email_should_be_diverted($email) {
    global $CFG;

    if (empty($CFG->divertallemailsto)) {
        return false;
    }

    if (empty($CFG->divertallemailsexcept)) {
        return true;
    }

    $patterns = array_map('trim', preg_split("/[\s,]+/", $CFG->divertallemailsexcept, -1, PREG_SPLIT_NO_EMPTY));
    foreach ($patterns as $pattern) {
        if (preg_match("/$pattern/", $email)) {
            return false;
        }
    }

    return true;
}

/**
 * Generate a unique email Message-ID using the moodle domain and install path
 *
 * @param string $localpart An optional unique message id prefix.
 * @return string The formatted ID ready for appending to the email headers.
 */
function generate_email_messageid($localpart = null) {
    global $CFG;

    $urlinfo = parse_url($CFG->wwwroot);
    $base = '@' . $urlinfo['host'];

    // If multiple moodles are on the same domain we want to tell them
    // apart so we add the install path to the local part. This means
    // that the id local part should never contain a / character so
    // we can correctly parse the id to reassemble the wwwroot.
    if (isset($urlinfo['path'])) {
        $base = $urlinfo['path'] . $base;
    }

    if (empty($localpart)) {
        $localpart = uniqid('', true);
    }

    // Because we may have an option /installpath suffix to the local part
    // of the id we need to escape any / chars which are in the $localpart.
    $localpart = str_replace('/', '%2F', $localpart);

    return '<' . $localpart . $base . '>';
}

/**
 * Send an email to a specified user
 *
 * @param stdClass $user  A {@link $USER} object
 * @param stdClass $from A {@link $USER} object
 * @param string $subject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml complete html version of the message (optional)
 * @param string $attachment a file on the filesystem, either relative to $CFG->dataroot or a full path to a file in one of
 *          the following directories: $CFG->cachedir, $CFG->dataroot, $CFG->dirroot, $CFG->localcachedir, $CFG->tempdir
 * @param string $attachname the name of the file (extension indicates MIME)
 * @param bool $usetrueaddress determines whether $from email address should
 *          be sent out. Will be overruled by user profile setting for maildisplay
 * @param string $replyto Email address to reply to
 * @param string $replytoname Name of reply to recipient
 * @param int $wordwrapwidth custom word wrap width, default 79
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function email_to_user($user, $from, $subject, $messagetext, $messagehtml = '', $attachment = '', $attachname = '',
                       $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79) {

    global $CFG, $PAGE, $SITE;

    if (empty($user) or empty($user->id)) {
        debugging('Can not send email to null user', DEBUG_DEVELOPER);
        return false;
    }

    if (empty($user->email)) {
        debugging('Can not send email to user without email: '.$user->id, DEBUG_DEVELOPER);
        return false;
    }

    if (!empty($user->deleted)) {
        debugging('Can not send email to deleted user: '.$user->id, DEBUG_DEVELOPER);
        return false;
    }

    if (defined('BEHAT_SITE_RUNNING')) {
        // Fake email sending in behat.
        return true;
    }

    if (!empty($CFG->noemailever)) {
        // Hidden setting for development sites, set in config.php if needed.
        debugging('Not sending email due to $CFG->noemailever config setting', DEBUG_NORMAL);
        return true;
    }

    if (email_should_be_diverted($user->email)) {
        $subject = "[DIVERTED {$user->email}] $subject";
        $user = clone($user);
        $user->email = $CFG->divertallemailsto;
    }

    // Skip mail to suspended users.
    if ((isset($user->auth) && $user->auth=='nologin') or (isset($user->suspended) && $user->suspended)) {
        return true;
    }

    if (!validate_email($user->email)) {
        // We can not send emails to invalid addresses - it might create security issue or confuse the mailer.
        debugging("email_to_user: User $user->id (".fullname($user).") email ($user->email) is invalid! Not sending.");
        return false;
    }

    if (over_bounce_threshold($user)) {
        debugging("email_to_user: User $user->id (".fullname($user).") is over bounce threshold! Not sending.");
        return false;
    }

    // TLD .invalid  is specifically reserved for invalid domain names.
    // For More information, see {@link http://tools.ietf.org/html/rfc2606#section-2}.
    if (substr($user->email, -8) == '.invalid') {
        debugging("email_to_user: User $user->id (".fullname($user).") email domain ($user->email) is invalid! Not sending.");
        return true; // This is not an error.
    }

    // If the user is a remote mnet user, parse the email text for URL to the
    // wwwroot and modify the url to direct the user's browser to login at their
    // home site (identity provider - idp) before hitting the link itself.
    if (is_mnet_remote_user($user)) {
        require_once($CFG->dirroot.'/mnet/lib.php');

        $jumpurl = mnet_get_idp_jump_url($user);
        $callback = partial('mnet_sso_apply_indirection', $jumpurl);

        $messagetext = preg_replace_callback("%($CFG->wwwroot[^[:space:]]*)%",
                $callback,
                $messagetext);
        $messagehtml = preg_replace_callback("%href=[\"'`]($CFG->wwwroot[\w_:\?=#&@/;.~-]*)[\"'`]%",
                $callback,
                $messagehtml);
    }
    $mail = get_mailer();

    if (!empty($mail->SMTPDebug)) {
        echo '<pre>' . "\n";
    }

    $temprecipients = array();
    $tempreplyto = array();

    // Make sure that we fall back onto some reasonable no-reply address.
    $noreplyaddressdefault = 'noreply@' . get_host_from_url($CFG->wwwroot);
    $noreplyaddress = empty($CFG->noreplyaddress) ? $noreplyaddressdefault : $CFG->noreplyaddress;

    if (!validate_email($noreplyaddress)) {
        debugging('email_to_user: Invalid noreply-email '.s($noreplyaddress));
        $noreplyaddress = $noreplyaddressdefault;
    }

    // Make up an email address for handling bounces.
    if (!empty($CFG->handlebounces)) {
        $modargs = 'B'.base64_encode(pack('V', $user->id)).substr(md5($user->email), 0, 16);
        $mail->Sender = generate_email_processing_address(0, $modargs);
    } else {
        $mail->Sender = $noreplyaddress;
    }

    // Make sure that the explicit replyto is valid, fall back to the implicit one.
    if (!empty($replyto) && !validate_email($replyto)) {
        debugging('email_to_user: Invalid replyto-email '.s($replyto));
        $replyto = $noreplyaddress;
    }

    if (is_string($from)) { // So we can pass whatever we want if there is need.
        $mail->From     = $noreplyaddress;
        $mail->FromName = $from;
    // Check if using the true address is true, and the email is in the list of allowed domains for sending email,
    // and that the senders email setting is either displayed to everyone, or display to only other users that are enrolled
    // in a course with the sender.
    } else if ($usetrueaddress && can_send_from_real_email_address($from, $user)) {
        if (!validate_email($from->email)) {
            debugging('email_to_user: Invalid from-email '.s($from->email).' - not sending');
            // Better not to use $noreplyaddress in this case.
            return false;
        }
        $mail->From = $from->email;
        $fromdetails = new stdClass();
        $fromdetails->name = fullname($from);
        $fromdetails->url = preg_replace('#^https?://#', '', $CFG->wwwroot);
        $fromdetails->siteshortname = format_string($SITE->shortname);
        $fromstring = $fromdetails->name;
        if ($CFG->emailfromvia == EMAIL_VIA_ALWAYS) {
            $fromstring = get_string('emailvia', 'core', $fromdetails);
        }
        $mail->FromName = $fromstring;
        if (empty($replyto)) {
            $tempreplyto[] = array($from->email, fullname($from));
        }
    } else {
        $mail->From = $noreplyaddress;
        $fromdetails = new stdClass();
        $fromdetails->name = fullname($from);
        $fromdetails->url = preg_replace('#^https?://#', '', $CFG->wwwroot);
        $fromdetails->siteshortname = format_string($SITE->shortname);
        $fromstring = $fromdetails->name;
        if ($CFG->emailfromvia != EMAIL_VIA_NEVER) {
            $fromstring = get_string('emailvia', 'core', $fromdetails);
        }
        $mail->FromName = $fromstring;
        if (empty($replyto)) {
            $tempreplyto[] = array($noreplyaddress, get_string('noreplyname'));
        }
    }

    if (!empty($replyto)) {
        $tempreplyto[] = array($replyto, $replytoname);
    }

    $temprecipients[] = array($user->email, fullname($user));

    // Set word wrap.
    $mail->WordWrap = $wordwrapwidth;

    if (!empty($from->customheaders)) {
        // Add custom headers.
        if (is_array($from->customheaders)) {
            foreach ($from->customheaders as $customheader) {
                $mail->addCustomHeader($customheader);
            }
        } else {
            $mail->addCustomHeader($from->customheaders);
        }
    }

    // If the X-PHP-Originating-Script email header is on then also add an additional
    // header with details of where exactly in moodle the email was triggered from,
    // either a call to message_send() or to email_to_user().
    if (ini_get('mail.add_x_header')) {

        $stack = debug_backtrace(false);
        $origin = $stack[0];

        foreach ($stack as $depth => $call) {
            if ($call['function'] == 'message_send') {
                $origin = $call;
            }
        }

        $originheader = $CFG->wwwroot . ' => ' . gethostname() . ':'
             . str_replace($CFG->dirroot . '/', '', $origin['file']) . ':' . $origin['line'];
        $mail->addCustomHeader('X-Moodle-Originating-Script: ' . $originheader);
    }

    if (!empty($CFG->emailheaders)) {
        $headers = array_map('trim', explode("\n", $CFG->emailheaders));
        foreach ($headers as $header) {
            if (!empty($header)) {
                $mail->addCustomHeader($header);
            }
        }
    }

    if (!empty($from->priority)) {
        $mail->Priority = $from->priority;
    }

    $renderer = $PAGE->get_renderer('core');
    $context = array(
        'sitefullname' => $SITE->fullname,
        'siteshortname' => $SITE->shortname,
        'sitewwwroot' => $CFG->wwwroot,
        'subject' => $subject,
        'prefix' => $CFG->emailsubjectprefix,
        'to' => $user->email,
        'toname' => fullname($user),
        'from' => $mail->From,
        'fromname' => $mail->FromName,
    );
    if (!empty($tempreplyto[0])) {
        $context['replyto'] = $tempreplyto[0][0];
        $context['replytoname'] = $tempreplyto[0][1];
    }
    if ($user->id > 0) {
        $context['touserid'] = $user->id;
        $context['tousername'] = $user->username;
    }

    if (!empty($user->mailformat) && $user->mailformat == 1) {
        // Only process html templates if the user preferences allow html email.

        if (!$messagehtml) {
            // If no html has been given, BUT there is an html wrapping template then
            // auto convert the text to html and then wrap it.
            $messagehtml = trim(text_to_html($messagetext));
        }
        $context['body'] = $messagehtml;
        $messagehtml = $renderer->render_from_template('core/email_html', $context);
    }

    $context['body'] = html_to_text(nl2br($messagetext));
    $mail->Subject = $renderer->render_from_template('core/email_subject', $context);
    $mail->FromName = $renderer->render_from_template('core/email_fromname', $context);
    $messagetext = $renderer->render_from_template('core/email_text', $context);

    // Autogenerate a MessageID if it's missing.
    if (empty($mail->MessageID)) {
        $mail->MessageID = generate_email_messageid();
    }

    if ($messagehtml && !empty($user->mailformat) && $user->mailformat == 1) {
        // Don't ever send HTML to users who don't want it.
        $mail->isHTML(true);
        $mail->Encoding = 'quoted-printable';
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
        if (preg_match( "~\\.\\.~" , $attachment )) {
            // Security check for ".." in dir path.
            $supportuser = core_user::get_support_user();
            $temprecipients[] = array($supportuser->email, fullname($supportuser, true));
            $mail->addStringAttachment('Error in attachment.  User attempted to attach a filename with a unsafe name.', 'error.txt', '8bit', 'text/plain');
        } else {
            require_once($CFG->libdir.'/filelib.php');
            $mimetype = mimeinfo('type', $attachname);

            // Before doing the comparison, make sure that the paths are correct (Windows uses slashes in the other direction).
            // The absolute (real) path is also fetched to ensure that comparisons to allowed paths are compared equally.
            $attachpath = str_replace('\\', '/', realpath($attachment));

            // Build an array of all filepaths from which attachments can be added (normalised slashes, absolute/real path).
            $allowedpaths = array_map(function(string $path): string {
                return str_replace('\\', '/', realpath($path));
            }, [
                $CFG->cachedir,
                $CFG->dataroot,
                $CFG->dirroot,
                $CFG->localcachedir,
                $CFG->tempdir,
                $CFG->localrequestdir,
            ]);

            // Set addpath to true.
            $addpath = true;

            // Check if attachment includes one of the allowed paths.
            foreach (array_filter($allowedpaths) as $allowedpath) {
                // Set addpath to false if the attachment includes one of the allowed paths.
                if (strpos($attachpath, $allowedpath) === 0) {
                    $addpath = false;
                    break;
                }
            }

            // If the attachment is a full path to a file in the multiple allowed paths, use it as is,
            // otherwise assume it is a relative path from the dataroot (for backwards compatibility reasons).
            if ($addpath == true) {
                $attachment = $CFG->dataroot . '/' . $attachment;
            }

            $mail->addAttachment($attachment, $attachname, 'base64', $mimetype);
        }
    }

    // Check if the email should be sent in an other charset then the default UTF-8.
    if ((!empty($CFG->sitemailcharset) || !empty($CFG->allowusermailcharset))) {

        // Use the defined site mail charset or eventually the one preferred by the recipient.
        $charset = $CFG->sitemailcharset;
        if (!empty($CFG->allowusermailcharset)) {
            if ($useremailcharset = get_user_preferences('mailcharset', '0', $user->id)) {
                $charset = $useremailcharset;
            }
        }

        // Convert all the necessary strings if the charset is supported.
        $charsets = get_list_of_charsets();
        unset($charsets['UTF-8']);
        if (in_array($charset, $charsets)) {
            $mail->CharSet  = $charset;
            $mail->FromName = core_text::convert($mail->FromName, 'utf-8', strtolower($charset));
            $mail->Subject  = core_text::convert($mail->Subject, 'utf-8', strtolower($charset));
            $mail->Body     = core_text::convert($mail->Body, 'utf-8', strtolower($charset));
            $mail->AltBody  = core_text::convert($mail->AltBody, 'utf-8', strtolower($charset));

            foreach ($temprecipients as $key => $values) {
                $temprecipients[$key][1] = core_text::convert($values[1], 'utf-8', strtolower($charset));
            }
            foreach ($tempreplyto as $key => $values) {
                $tempreplyto[$key][1] = core_text::convert($values[1], 'utf-8', strtolower($charset));
            }
        }
    }

    foreach ($temprecipients as $values) {
        $mail->addAddress($values[0], $values[1]);
    }
    foreach ($tempreplyto as $values) {
        $mail->addReplyTo($values[0], $values[1]);
    }

    if (!empty($CFG->emaildkimselector)) {
        $domain = substr(strrchr($mail->From, "@"), 1);
        $pempath = "{$CFG->dataroot}/dkim/{$domain}/{$CFG->emaildkimselector}.private";
        if (file_exists($pempath)) {
            $mail->DKIM_domain      = $domain;
            $mail->DKIM_private     = $pempath;
            $mail->DKIM_selector    = $CFG->emaildkimselector;
            $mail->DKIM_identity    = $mail->From;
        } else {
            debugging("Email DKIM selector chosen due to {$mail->From} but no certificate found at $pempath", DEBUG_DEVELOPER);
        }
    }

    if ($mail->send()) {
        set_send_count($user);
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        return true;
    } else {
        // Trigger event for failing to send email.
        $event = \core\event\email_failed::create(array(
            'context' => context_system::instance(),
            'userid' => $from->id,
            'relateduserid' => $user->id,
            'other' => array(
                'subject' => $subject,
                'message' => $messagetext,
                'errorinfo' => $mail->ErrorInfo
            )
        ));
        $event->trigger();
        if (CLI_SCRIPT) {
            mtrace('Error: lib/moodlelib.php email_to_user(): '.$mail->ErrorInfo);
        }
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        return false;
    }
}

/**
 * Check to see if a user's real email address should be used for the "From" field.
 *
 * @param  object $from The user object for the user we are sending the email from.
 * @param  object $user The user object that we are sending the email to.
 * @param  array $unused No longer used.
 * @return bool Returns true if we can use the from user's email adress in the "From" field.
 */
function can_send_from_real_email_address($from, $user, $unused = null) {
    global $CFG;
    if (!isset($CFG->allowedemaildomains) || empty(trim($CFG->allowedemaildomains))) {
        return false;
    }
    $alloweddomains = array_map('trim', explode("\n", $CFG->allowedemaildomains));
    // Email is in the list of allowed domains for sending email,
    // and the senders email setting is either displayed to everyone, or display to only other users that are enrolled
    // in a course with the sender.
    if (\core\ip_utils::is_domain_in_allowed_list(substr($from->email, strpos($from->email, '@') + 1), $alloweddomains)
                && ($from->maildisplay == core_user::MAILDISPLAY_EVERYONE
                || ($from->maildisplay == core_user::MAILDISPLAY_COURSE_MEMBERS_ONLY
                && enrol_get_shared_courses($user, $from, false, true)))) {
        return true;
    }
    return false;
}

/**
 * Generate a signoff for emails based on support settings
 *
 * @return string
 */
function generate_email_signoff() {
    global $CFG, $OUTPUT;

    $signoff = "\n";
    if (!empty($CFG->supportname)) {
        $signoff .= $CFG->supportname."\n";
    }

    $supportemail = $OUTPUT->supportemail(['class' => 'font-weight-bold']);

    if ($supportemail) {
        $signoff .= "\n" . $supportemail . "\n";
    }

    return $signoff;
}

/**
 * Sets specified user's password and send the new password to the user via email.
 *
 * @param stdClass $user A {@link $USER} object
 * @param bool $fasthash If true, use a low cost factor when generating the hash for speed.
 * @return bool|string Returns "true" if mail was sent OK and "false" if there was an error
 */
function setnew_password_and_mail($user, $fasthash = false) {
    global $CFG, $DB;

    // We try to send the mail in language the user understands,
    // unfortunately the filter_string() does not support alternative langs yet
    // so multilang will not work properly for site->fullname.
    $lang = empty($user->lang) ? get_newuser_language() : $user->lang;

    $site  = get_site();

    $supportuser = core_user::get_support_user();

    $newpassword = generate_password();

    update_internal_user_password($user, $newpassword, $fasthash);

    $a = new stdClass();
    $a->firstname   = fullname($user, true);
    $a->sitename    = format_string($site->fullname);
    $a->username    = $user->username;
    $a->newpassword = $newpassword;
    $a->link        = $CFG->wwwroot .'/login/?lang='.$lang;
    $a->signoff     = generate_email_signoff();

    $message = (string)new lang_string('newusernewpasswordtext', '', $a, $lang);

    $subject = format_string($site->fullname) .': '. (string)new lang_string('newusernewpasswordsubj', '', $a, $lang);

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message);

}

/**
 * Resets specified user's password and send the new password to the user via email.
 *
 * @param stdClass $user A {@link $USER} object
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function reset_password_and_mail($user) {
    global $CFG;

    $site  = get_site();
    $supportuser = core_user::get_support_user();

    $userauth = get_auth_plugin($user->auth);
    if (!$userauth->can_reset_password() or !is_enabled_auth($user->auth)) {
        trigger_error("Attempt to reset user password for user $user->username with Auth $user->auth.");
        return false;
    }

    $newpassword = generate_password();

    if (!$userauth->user_update_password($user, $newpassword)) {
        throw new \moodle_exception("cannotsetpassword");
    }

    $a = new stdClass();
    $a->firstname   = $user->firstname;
    $a->lastname    = $user->lastname;
    $a->sitename    = format_string($site->fullname);
    $a->username    = $user->username;
    $a->newpassword = $newpassword;
    $a->link        = $CFG->wwwroot .'/login/change_password.php';
    $a->signoff     = generate_email_signoff();

    $message = get_string('newpasswordtext', '', $a);

    $subject  = format_string($site->fullname) .': '. get_string('changedpassword');

    unset_user_preference('create_password', $user); // Prevent cron from generating the password.

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message);
}

/**
 * Send email to specified user with confirmation text and activation link.
 *
 * @param stdClass $user A {@link $USER} object
 * @param string $confirmationurl user confirmation URL
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function send_confirmation_email($user, $confirmationurl = null) {
    global $CFG;

    $site = get_site();
    $supportuser = core_user::get_support_user();

    $data = new stdClass();
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();

    $subject = get_string('emailconfirmationsubject', '', format_string($site->fullname));

    if (empty($confirmationurl)) {
        $confirmationurl = '/login/confirm.php';
    }

    $confirmationurl = new moodle_url($confirmationurl);
    // Remove data parameter just in case it was included in the confirmation so we can add it manually later.
    $confirmationurl->remove_params('data');
    $confirmationpath = $confirmationurl->out(false);

    // We need to custom encode the username to include trailing dots in the link.
    // Because of this custom encoding we can't use moodle_url directly.
    // Determine if a query string is present in the confirmation url.
    $hasquerystring = strpos($confirmationpath, '?') !== false;
    // Perform normal url encoding of the username first.
    $username = urlencode($user->username);
    // Prevent problems with trailing dots not being included as part of link in some mail clients.
    $username = str_replace('.', '%2E', $username);

    $data->link = $confirmationpath . ( $hasquerystring ? '&' : '?') . 'data='. $user->secret .'/'. $username;

    $message     = get_string('emailconfirmation', '', $data);
    $messagehtml = text_to_html(get_string('emailconfirmation', '', $data), false, false, true);

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
}

/**
 * Sends a password change confirmation email.
 *
 * @param stdClass $user A {@link $USER} object
 * @param stdClass $resetrecord An object tracking metadata regarding password reset request
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function send_password_change_confirmation_email($user, $resetrecord) {
    global $CFG;

    $site = get_site();
    $supportuser = core_user::get_support_user();
    $pwresetmins = isset($CFG->pwresettime) ? floor($CFG->pwresettime / MINSECS) : 30;

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->username  = $user->username;
    $data->sitename  = format_string($site->fullname);
    $data->link      = $CFG->wwwroot .'/login/forgot_password.php?token='. $resetrecord->token;
    $data->admin     = generate_email_signoff();
    $data->resetminutes = $pwresetmins;

    $message = get_string('emailresetconfirmation', '', $data);
    $subject = get_string('emailresetconfirmationsubject', '', format_string($site->fullname));

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message);

}

/**
 * Sends an email containing information on how to change your password.
 *
 * @param stdClass $user A {@link $USER} object
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function send_password_change_info($user) {
    $site = get_site();
    $supportuser = core_user::get_support_user();

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->username  = $user->username;
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();

    if (!is_enabled_auth($user->auth)) {
        $message = get_string('emailpasswordchangeinfodisabled', '', $data);
        $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));
        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
        return email_to_user($user, $supportuser, $subject, $message);
    }

    $userauth = get_auth_plugin($user->auth);
    ['subject' => $subject, 'message' => $message] = $userauth->get_password_change_info($user);

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message);
}

/**
 * Check that an email is allowed.  It returns an error message if there was a problem.
 *
 * @param string $email Content of email
 * @return string|false
 */
function email_is_not_allowed($email) {
    global $CFG;

    // Comparing lowercase domains.
    $email = strtolower($email);
    if (!empty($CFG->allowemailaddresses)) {
        $allowed = explode(' ', strtolower($CFG->allowemailaddresses));
        foreach ($allowed as $allowedpattern) {
            $allowedpattern = trim($allowedpattern);
            if (!$allowedpattern) {
                continue;
            }
            if (strpos($allowedpattern, '.') === 0) {
                if (strpos(strrev($email), strrev($allowedpattern)) === 0) {
                    // Subdomains are in a form ".example.com" - matches "xxx@anything.example.com".
                    return false;
                }

            } else if (strpos(strrev($email), strrev('@'.$allowedpattern)) === 0) {
                return false;
            }
        }
        return get_string('emailonlyallowed', '', $CFG->allowemailaddresses);

    } else if (!empty($CFG->denyemailaddresses)) {
        $denied = explode(' ', strtolower($CFG->denyemailaddresses));
        foreach ($denied as $deniedpattern) {
            $deniedpattern = trim($deniedpattern);
            if (!$deniedpattern) {
                continue;
            }
            if (strpos($deniedpattern, '.') === 0) {
                if (strpos(strrev($email), strrev($deniedpattern)) === 0) {
                    // Subdomains are in a form ".example.com" - matches "xxx@anything.example.com".
                    return get_string('emailnotallowed', '', $CFG->denyemailaddresses);
                }

            } else if (strpos(strrev($email), strrev('@'.$deniedpattern)) === 0) {
                return get_string('emailnotallowed', '', $CFG->denyemailaddresses);
            }
        }
    }

    return false;
}

// FILE HANDLING.

/**
 * Returns local file storage instance
 *
 * @return file_storage
 */
function get_file_storage($reset = false) {
    global $CFG;

    static $fs = null;

    if ($reset) {
        $fs = null;
        return;
    }

    if ($fs) {
        return $fs;
    }

    require_once("$CFG->libdir/filelib.php");

    $fs = new file_storage();

    return $fs;
}

/**
 * Returns local file storage instance
 *
 * @return file_browser
 */
function get_file_browser() {
    global $CFG;

    static $fb = null;

    if ($fb) {
        return $fb;
    }

    require_once("$CFG->libdir/filelib.php");

    $fb = new file_browser();

    return $fb;
}

/**
 * Returns file packer
 *
 * @param string $mimetype default application/zip
 * @return file_packer
 */
function get_file_packer($mimetype='application/zip') {
    global $CFG;

    static $fp = array();

    if (isset($fp[$mimetype])) {
        return $fp[$mimetype];
    }

    switch ($mimetype) {
        case 'application/zip':
        case 'application/vnd.moodle.profiling':
            $classname = 'zip_packer';
            break;

        case 'application/x-gzip' :
            $classname = 'tgz_packer';
            break;

        case 'application/vnd.moodle.backup':
            $classname = 'mbz_packer';
            break;

        default:
            return false;
    }

    require_once("$CFG->libdir/filestorage/$classname.php");
    $fp[$mimetype] = new $classname();

    return $fp[$mimetype];
}

/**
 * Returns current name of file on disk if it exists.
 *
 * @param string $newfile File to be verified
 * @return string Current name of file on disk if true
 */
function valid_uploaded_file($newfile) {
    if (empty($newfile)) {
        return '';
    }
    if (is_uploaded_file($newfile['tmp_name']) and $newfile['size'] > 0) {
        return $newfile['tmp_name'];
    } else {
        return '';
    }
}

/**
 * Returns the maximum size for uploading files.
 *
 * There are seven possible upload limits:
 * 1. in Apache using LimitRequestBody (no way of checking or changing this)
 * 2. in php.ini for 'upload_max_filesize' (can not be changed inside PHP)
 * 3. in .htaccess for 'upload_max_filesize' (can not be changed inside PHP)
 * 4. in php.ini for 'post_max_size' (can not be changed inside PHP)
 * 5. by the Moodle admin in $CFG->maxbytes
 * 6. by the teacher in the current course $course->maxbytes
 * 7. by the teacher for the current module, eg $assignment->maxbytes
 *
 * These last two are passed to this function as arguments (in bytes).
 * Anything defined as 0 is ignored.
 * The smallest of all the non-zero numbers is returned.
 *
 * @todo Finish documenting this function
 *
 * @param int $sitebytes Set maximum size
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @param bool $unused This parameter has been deprecated and is not used any more.
 * @return int The maximum size for uploading files.
 */
function get_max_upload_file_size($sitebytes=0, $coursebytes=0, $modulebytes=0, $unused = false) {

    if (! $filesize = ini_get('upload_max_filesize')) {
        $filesize = '5M';
    }
    $minimumsize = get_real_size($filesize);

    if ($postsize = ini_get('post_max_size')) {
        $postsize = get_real_size($postsize);
        if ($postsize < $minimumsize) {
            $minimumsize = $postsize;
        }
    }

    if (($sitebytes > 0) and ($sitebytes < $minimumsize)) {
        $minimumsize = $sitebytes;
    }

    if (($coursebytes > 0) and ($coursebytes < $minimumsize)) {
        $minimumsize = $coursebytes;
    }

    if (($modulebytes > 0) and ($modulebytes < $minimumsize)) {
        $minimumsize = $modulebytes;
    }

    return $minimumsize;
}

/**
 * Returns the maximum size for uploading files for the current user
 *
 * This function takes in account {@link get_max_upload_file_size()} the user's capabilities
 *
 * @param context $context The context in which to check user capabilities
 * @param int $sitebytes Set maximum size
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @param stdClass $user The user
 * @param bool $unused This parameter has been deprecated and is not used any more.
 * @return int The maximum size for uploading files.
 */
function get_user_max_upload_file_size($context, $sitebytes = 0, $coursebytes = 0, $modulebytes = 0, $user = null,
        $unused = false) {
    global $USER;

    if (empty($user)) {
        $user = $USER;
    }

    if (has_capability('moodle/course:ignorefilesizelimits', $context, $user)) {
        return USER_CAN_IGNORE_FILE_SIZE_LIMITS;
    }

    return get_max_upload_file_size($sitebytes, $coursebytes, $modulebytes);
}

/**
 * Returns an array of possible sizes in local language
 *
 * Related to {@link get_max_upload_file_size()} - this function returns an
 * array of possible sizes in an array, translated to the
 * local language.
 *
 * The list of options will go up to the minimum of $sitebytes, $coursebytes or $modulebytes.
 *
 * If $coursebytes or $sitebytes is not 0, an option will be included for "Course/Site upload limit (X)"
 * with the value set to 0. This option will be the first in the list.
 *
 * @uses SORT_NUMERIC
 * @param int $sitebytes Set maximum size
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @param int|array $custombytes custom upload size/s which will be added to list,
 *        Only value/s smaller then maxsize will be added to list.
 * @return array
 */
function get_max_upload_sizes($sitebytes = 0, $coursebytes = 0, $modulebytes = 0, $custombytes = null) {
    global $CFG;

    if (!$maxsize = get_max_upload_file_size($sitebytes, $coursebytes, $modulebytes)) {
        return array();
    }

    if ($sitebytes == 0) {
        // Will get the minimum of upload_max_filesize or post_max_size.
        $sitebytes = get_max_upload_file_size();
    }

    $filesize = array();
    $sizelist = array(10240, 51200, 102400, 512000, 1048576, 2097152,
                      5242880, 10485760, 20971520, 52428800, 104857600,
                      262144000, 524288000, 786432000, 1073741824,
                      2147483648, 4294967296, 8589934592);

    // If custombytes is given and is valid then add it to the list.
    if (is_number($custombytes) and $custombytes > 0) {
        $custombytes = (int)$custombytes;
        if (!in_array($custombytes, $sizelist)) {
            $sizelist[] = $custombytes;
        }
    } else if (is_array($custombytes)) {
        $sizelist = array_unique(array_merge($sizelist, $custombytes));
    }

    // Allow maxbytes to be selected if it falls outside the above boundaries.
    if (isset($CFG->maxbytes) && !in_array(get_real_size($CFG->maxbytes), $sizelist)) {
        // Note: get_real_size() is used in order to prevent problems with invalid values.
        $sizelist[] = get_real_size($CFG->maxbytes);
    }

    foreach ($sizelist as $sizebytes) {
        if ($sizebytes < $maxsize && $sizebytes > 0) {
            $filesize[(string)intval($sizebytes)] = display_size($sizebytes, 0);
        }
    }

    $limitlevel = '';
    $displaysize = '';
    if ($modulebytes &&
        (($modulebytes < $coursebytes || $coursebytes == 0) &&
         ($modulebytes < $sitebytes || $sitebytes == 0))) {
        $limitlevel = get_string('activity', 'core');
        $displaysize = display_size($modulebytes, 0);
        $filesize[$modulebytes] = $displaysize; // Make sure the limit is also included in the list.

    } else if ($coursebytes && ($coursebytes < $sitebytes || $sitebytes == 0)) {
        $limitlevel = get_string('course', 'core');
        $displaysize = display_size($coursebytes, 0);
        $filesize[$coursebytes] = $displaysize; // Make sure the limit is also included in the list.

    } else if ($sitebytes) {
        $limitlevel = get_string('site', 'core');
        $displaysize = display_size($sitebytes, 0);
        $filesize[$sitebytes] = $displaysize; // Make sure the limit is also included in the list.
    }

    krsort($filesize, SORT_NUMERIC);
    if ($limitlevel) {
        $params = (object) array('contextname' => $limitlevel, 'displaysize' => $displaysize);
        $filesize  = array('0' => get_string('uploadlimitwithsize', 'core', $params)) + $filesize;
    }

    return $filesize;
}

/**
 * Returns an array with all the filenames in all subdirectories, relative to the given rootdir.
 *
 * If excludefiles is defined, then that file/directory is ignored
 * If getdirs is true, then (sub)directories are included in the output
 * If getfiles is true, then files are included in the output
 * (at least one of these must be true!)
 *
 * @todo Finish documenting this function. Add examples of $excludefile usage.
 *
 * @param string $rootdir A given root directory to start from
 * @param string|array $excludefiles If defined then the specified file/directory is ignored
 * @param bool $descend If true then subdirectories are recursed as well
 * @param bool $getdirs If true then (sub)directories are included in the output
 * @param bool $getfiles  If true then files are included in the output
 * @return array An array with all the filenames in all subdirectories, relative to the given rootdir
 */
function get_directory_list($rootdir, $excludefiles='', $descend=true, $getdirs=false, $getfiles=true) {

    $dirs = array();

    if (!$getdirs and !$getfiles) {   // Nothing to show.
        return $dirs;
    }

    if (!is_dir($rootdir)) {          // Must be a directory.
        return $dirs;
    }

    if (!$dir = opendir($rootdir)) {  // Can't open it for some reason.
        return $dirs;
    }

    if (!is_array($excludefiles)) {
        $excludefiles = array($excludefiles);
    }

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == '.' or $file == 'CVS' or in_array($file, $excludefiles)) {
            continue;
        }
        $fullfile = $rootdir .'/'. $file;
        if (filetype($fullfile) == 'dir') {
            if ($getdirs) {
                $dirs[] = $file;
            }
            if ($descend) {
                $subdirs = get_directory_list($fullfile, $excludefiles, $descend, $getdirs, $getfiles);
                foreach ($subdirs as $subdir) {
                    $dirs[] = $file .'/'. $subdir;
                }
            }
        } else if ($getfiles) {
            $dirs[] = $file;
        }
    }
    closedir($dir);

    asort($dirs);

    return $dirs;
}


/**
 * Adds up all the files in a directory and works out the size.
 *
 * @param string $rootdir  The directory to start from
 * @param string $excludefile A file to exclude when summing directory size
 * @return int The summed size of all files and subfiles within the root directory
 */
function get_directory_size($rootdir, $excludefile='') {
    global $CFG;

    // Do it this way if we can, it's much faster.
    if (!empty($CFG->pathtodu) && is_executable(trim($CFG->pathtodu))) {
        $command = trim($CFG->pathtodu).' -sk '.escapeshellarg($rootdir);
        $output = null;
        $return = null;
        exec($command, $output, $return);
        if (is_array($output)) {
            // We told it to return k.
            return get_real_size(intval($output[0]).'k');
        }
    }

    if (!is_dir($rootdir)) {
        // Must be a directory.
        return 0;
    }

    if (!$dir = @opendir($rootdir)) {
        // Can't open it for some reason.
        return 0;
    }

    $size = 0;

    while (false !== ($file = readdir($dir))) {
        $firstchar = substr($file, 0, 1);
        if ($firstchar == '.' or $file == 'CVS' or $file == $excludefile) {
            continue;
        }
        $fullfile = $rootdir .'/'. $file;
        if (filetype($fullfile) == 'dir') {
            $size += get_directory_size($fullfile, $excludefile);
        } else {
            $size += filesize($fullfile);
        }
    }
    closedir($dir);

    return $size;
}

/**
 * Converts bytes into display form
 *
 * @param int $size  The size to convert to human readable form
 * @param int $decimalplaces If specified, uses fixed number of decimal places
 * @param string $fixedunits If specified, uses fixed units (e.g. 'KB')
 * @return string Display version of size
 */
function display_size($size, int $decimalplaces = 1, string $fixedunits = ''): string {

    static $units;

    if ($size === USER_CAN_IGNORE_FILE_SIZE_LIMITS) {
        return get_string('unlimited');
    }

    if (empty($units)) {
        $units[] = get_string('sizeb');
        $units[] = get_string('sizekb');
        $units[] = get_string('sizemb');
        $units[] = get_string('sizegb');
        $units[] = get_string('sizetb');
        $units[] = get_string('sizepb');
    }

    switch ($fixedunits) {
        case 'PB' :
            $magnitude = 5;
            break;
        case 'TB' :
            $magnitude = 4;
            break;
        case 'GB' :
            $magnitude = 3;
            break;
        case 'MB' :
            $magnitude = 2;
            break;
        case 'KB' :
            $magnitude = 1;
            break;
        case 'B' :
            $magnitude = 0;
            break;
        case '':
            $magnitude = floor(log($size, 1024));
            $magnitude = max(0, min(5, $magnitude));
            break;
        default:
            throw new coding_exception('Unknown fixed units value: ' . $fixedunits);
    }

    // Special case for magnitude 0 (bytes) - never use decimal places.
    $nbsp = "\xc2\xa0";
    if ($magnitude === 0) {
        return round($size) . $nbsp . $units[$magnitude];
    }

    // Convert to specified units.
    $sizeinunit = $size / 1024 ** $magnitude;

    // Fixed decimal places.
    return sprintf('%.' . $decimalplaces . 'f', $sizeinunit) . $nbsp . $units[$magnitude];
}

/**
 * Cleans a given filename by removing suspicious or troublesome characters
 *
 * @see clean_param()
 * @param string $string file name
 * @return string cleaned file name
 */
function clean_filename($string) {
    return clean_param($string, PARAM_FILE);
}

// STRING TRANSLATION.

/**
 * Returns the code for the current language
 *
 * @category string
 * @return string
 */
function current_language() {
    global $CFG, $PAGE, $SESSION, $USER;

    if (!empty($SESSION->forcelang)) {
        // Allows overriding course-forced language (useful for admins to check
        // issues in courses whose language they don't understand).
        // Also used by some code to temporarily get language-related information in a
        // specific language (see force_current_language()).
        $return = $SESSION->forcelang;

    } else if (!empty($PAGE->cm->lang)) {
        // Activity language, if set.
        $return = $PAGE->cm->lang;

    } else if (!empty($PAGE->course->id) && $PAGE->course->id != SITEID && !empty($PAGE->course->lang)) {
        // Course language can override all other settings for this page.
        $return = $PAGE->course->lang;

    } else if (!empty($SESSION->lang)) {
        // Session language can override other settings.
        $return = $SESSION->lang;

    } else if (!empty($USER->lang)) {
        $return = $USER->lang;

    } else if (isset($CFG->lang)) {
        $return = $CFG->lang;

    } else {
        $return = 'en';
    }

    // Just in case this slipped in from somewhere by accident.
    $return = str_replace('_utf8', '', $return);

    return $return;
}

/**
 * Fix the current language to the given language code.
 *
 * @param string $lang The language code to use.
 * @return void
 */
function fix_current_language(string $lang): void {
    global $CFG, $COURSE, $SESSION, $USER;

    if (!get_string_manager()->translation_exists($lang)) {
        throw new coding_exception("The language pack for $lang is not available");
    }

    $fixglobal = '';
    $fixlang = 'lang';
    if (!empty($SESSION->forcelang)) {
        $fixglobal = $SESSION;
        $fixlang = 'forcelang';
    } else if (!empty($COURSE->id) && $COURSE->id != SITEID && !empty($COURSE->lang)) {
        $fixglobal = $COURSE;
    } else if (!empty($SESSION->lang)) {
        $fixglobal = $SESSION;
    } else if (!empty($USER->lang)) {
        $fixglobal = $USER;
    } else if (isset($CFG->lang)) {
        set_config('lang', $lang);
    }

    if ($fixglobal) {
        $fixglobal->$fixlang = $lang;
    }
}

/**
 * Returns parent language of current active language if defined
 *
 * @category string
 * @param string $lang null means current language
 * @return string
 */
function get_parent_language($lang=null) {

    $parentlang = get_string_manager()->get_string('parentlanguage', 'langconfig', null, $lang);

    if ($parentlang === 'en') {
        $parentlang = '';
    }

    return $parentlang;
}

/**
 * Force the current language to get strings and dates localised in the given language.
 *
 * After calling this function, all strings will be provided in the given language
 * until this function is called again, or equivalent code is run.
 *
 * @param string $language
 * @return string previous $SESSION->forcelang value
 */
function force_current_language($language) {
    global $SESSION;
    $sessionforcelang = isset($SESSION->forcelang) ? $SESSION->forcelang : '';
    if ($language !== $sessionforcelang) {
        // Setting forcelang to null or an empty string disables its effect.
        if (empty($language) || get_string_manager()->translation_exists($language, false)) {
            $SESSION->forcelang = $language;
            moodle_setlocale();
        }
    }
    return $sessionforcelang;
}

/**
 * Returns current string_manager instance.
 *
 * The param $forcereload is needed for CLI installer only where the string_manager instance
 * must be replaced during the install.php script life time.
 *
 * @category string
 * @param bool $forcereload shall the singleton be released and new instance created instead?
 * @return core_string_manager
 */
function get_string_manager($forcereload=false) {
    global $CFG;

    static $singleton = null;

    if ($forcereload) {
        $singleton = null;
    }
    if ($singleton === null) {
        if (empty($CFG->early_install_lang)) {

            $transaliases = array();
            if (empty($CFG->langlist)) {
                 $translist = array();
            } else {
                $translist = explode(',', $CFG->langlist);
                $translist = array_map('trim', $translist);
                // Each language in the $CFG->langlist can has an "alias" that would substitute the default language name.
                foreach ($translist as $i => $value) {
                    $parts = preg_split('/\s*\|\s*/', $value, 2);
                    if (count($parts) == 2) {
                        $transaliases[$parts[0]] = $parts[1];
                        $translist[$i] = $parts[0];
                    }
                }
            }

            if (!empty($CFG->config_php_settings['customstringmanager'])) {
                $classname = $CFG->config_php_settings['customstringmanager'];

                if (class_exists($classname)) {
                    $implements = class_implements($classname);

                    if (isset($implements['core_string_manager'])) {
                        $singleton = new $classname($CFG->langotherroot, $CFG->langlocalroot, $translist, $transaliases);
                        return $singleton;

                    } else {
                        debugging('Unable to instantiate custom string manager: class '.$classname.
                            ' does not implement the core_string_manager interface.');
                    }

                } else {
                    debugging('Unable to instantiate custom string manager: class '.$classname.' can not be found.');
                }
            }

            $singleton = new core_string_manager_standard($CFG->langotherroot, $CFG->langlocalroot, $translist, $transaliases);

        } else {
            $singleton = new core_string_manager_install();
        }
    }

    return $singleton;
}

/**
 * Returns a localized string.
 *
 * Returns the translated string specified by $identifier as
 * for $module.  Uses the same format files as STphp.
 * $a is an object, string or number that can be used
 * within translation strings
 *
 * eg 'hello {$a->firstname} {$a->lastname}'
 * or 'hello {$a}'
 *
 * If you would like to directly echo the localized string use
 * the function {@link print_string()}
 *
 * Example usage of this function involves finding the string you would
 * like a local equivalent of and using its identifier and module information
 * to retrieve it.<br/>
 * If you open moodle/lang/en/moodle.php and look near line 278
 * you will find a string to prompt a user for their word for 'course'
 * <code>
 * $string['course'] = 'Course';
 * </code>
 * So if you want to display the string 'Course'
 * in any language that supports it on your site
 * you just need to use the identifier 'course'
 * <code>
 * $mystring = '<strong>'. get_string('course') .'</strong>';
 * or
 * </code>
 * If the string you want is in another file you'd take a slightly
 * different approach. Looking in moodle/lang/en/calendar.php you find
 * around line 75:
 * <code>
 * $string['typecourse'] = 'Course event';
 * </code>
 * If you want to display the string "Course event" in any language
 * supported you would use the identifier 'typecourse' and the module 'calendar'
 * (because it is in the file calendar.php):
 * <code>
 * $mystring = '<h1>'. get_string('typecourse', 'calendar') .'</h1>';
 * </code>
 *
 * As a last resort, should the identifier fail to map to a string
 * the returned string will be [[ $identifier ]]
 *
 * In Moodle 2.3 there is a new argument to this function $lazyload.
 * Setting $lazyload to true causes get_string to return a lang_string object
 * rather than the string itself. The fetching of the string is then put off until
 * the string object is first used. The object can be used by calling it's out
 * method or by casting the object to a string, either directly e.g.
 *     (string)$stringobject
 * or indirectly by using the string within another string or echoing it out e.g.
 *     echo $stringobject
 *     return "<p>{$stringobject}</p>";
 * It is worth noting that using $lazyload and attempting to use the string as an
 * array key will cause a fatal error as objects cannot be used as array keys.
 * But you should never do that anyway!
 * For more information {@link lang_string}
 *
 * @category string
 * @param string $identifier The key identifier for the localized string
 * @param string $component The module where the key identifier is stored,
 *      usually expressed as the filename in the language pack without the
 *      .php on the end but can also be written as mod/forum or grade/export/xls.
 *      If none is specified then moodle.php is used.
 * @param string|object|array $a An object, string or number that can be used
 *      within translation strings
 * @param bool $lazyload If set to true a string object is returned instead of
 *      the string itself. The string then isn't calculated until it is first used.
 * @return string The localized string.
 * @throws coding_exception
 */
function get_string($identifier, $component = '', $a = null, $lazyload = false) {
    global $CFG;

    // If the lazy load argument has been supplied return a lang_string object
    // instead.
    // We need to make sure it is true (and a bool) as you will see below there
    // used to be a forth argument at one point.
    if ($lazyload === true) {
        return new lang_string($identifier, $component, $a);
    }

    if ($CFG->debugdeveloper && clean_param($identifier, PARAM_STRINGID) === '') {
        throw new coding_exception('Invalid string identifier. The identifier cannot be empty. Please fix your get_string() call.', DEBUG_DEVELOPER);
    }

    // There is now a forth argument again, this time it is a boolean however so
    // we can still check for the old extralocations parameter.
    if (!is_bool($lazyload) && !empty($lazyload)) {
        debugging('extralocations parameter in get_string() is not supported any more, please use standard lang locations only.');
    }

    if (strpos((string)$component, '/') !== false) {
        debugging('The module name you passed to get_string is the deprecated format ' .
                'like mod/mymod or block/myblock. The correct form looks like mymod, or block_myblock.' , DEBUG_DEVELOPER);
        $componentpath = explode('/', $component);

        switch ($componentpath[0]) {
            case 'mod':
                $component = $componentpath[1];
                break;
            case 'blocks':
            case 'block':
                $component = 'block_'.$componentpath[1];
                break;
            case 'enrol':
                $component = 'enrol_'.$componentpath[1];
                break;
            case 'format':
                $component = 'format_'.$componentpath[1];
                break;
            case 'grade':
                $component = 'grade'.$componentpath[1].'_'.$componentpath[2];
                break;
        }
    }

    $result = get_string_manager()->get_string($identifier, $component, $a);

    // Debugging feature lets you display string identifier and component.
    if (isset($CFG->debugstringids) && $CFG->debugstringids && optional_param('strings', 0, PARAM_INT)) {
        $result .= ' {' . $identifier . '/' . $component . '}';
    }
    return $result;
}

/**
 * Converts an array of strings to their localized value.
 *
 * @param array $array An array of strings
 * @param string $component The language module that these strings can be found in.
 * @return stdClass translated strings.
 */
function get_strings($array, $component = '') {
    $string = new stdClass;
    foreach ($array as $item) {
        $string->$item = get_string($item, $component);
    }
    return $string;
}

/**
 * Prints out a translated string.
 *
 * Prints out a translated string using the return value from the {@link get_string()} function.
 *
 * Example usage of this function when the string is in the moodle.php file:<br/>
 * <code>
 * echo '<strong>';
 * print_string('course');
 * echo '</strong>';
 * </code>
 *
 * Example usage of this function when the string is not in the moodle.php file:<br/>
 * <code>
 * echo '<h1>';
 * print_string('typecourse', 'calendar');
 * echo '</h1>';
 * </code>
 *
 * @category string
 * @param string $identifier The key identifier for the localized string
 * @param string $component The module where the key identifier is stored. If none is specified then moodle.php is used.
 * @param string|object|array $a An object, string or number that can be used within translation strings
 */
function print_string($identifier, $component = '', $a = null) {
    echo get_string($identifier, $component, $a);
}

/**
 * Returns a list of charset codes
 *
 * Returns a list of charset codes. It's hardcoded, so they should be added manually
 * (checking that such charset is supported by the texlib library!)
 *
 * @return array And associative array with contents in the form of charset => charset
 */
function get_list_of_charsets() {

    $charsets = array(
        'EUC-JP'     => 'EUC-JP',
        'ISO-2022-JP'=> 'ISO-2022-JP',
        'ISO-8859-1' => 'ISO-8859-1',
        'SHIFT-JIS'  => 'SHIFT-JIS',
        'GB2312'     => 'GB2312',
        'GB18030'    => 'GB18030', // GB18030 not supported by typo and mbstring.
        'UTF-8'      => 'UTF-8');

    asort($charsets);

    return $charsets;
}

/**
 * Returns a list of valid and compatible themes
 *
 * @return array
 */
function get_list_of_themes() {
    global $CFG;

    $themes = array();

    if (!empty($CFG->themelist)) {       // Use admin's list of themes.
        $themelist = explode(',', $CFG->themelist);
    } else {
        $themelist = array_keys(core_component::get_plugin_list("theme"));
    }

    foreach ($themelist as $key => $themename) {
        $theme = theme_config::load($themename);
        $themes[$themename] = $theme;
    }

    core_collator::asort_objects_by_method($themes, 'get_theme_name');

    return $themes;
}

/**
 * Factory function for emoticon_manager
 *
 * @return emoticon_manager singleton
 */
function get_emoticon_manager() {
    static $singleton = null;

    if (is_null($singleton)) {
        $singleton = new emoticon_manager();
    }

    return $singleton;
}

/**
 * Provides core support for plugins that have to deal with emoticons (like HTML editor or emoticon filter).
 *
 * Whenever this manager mentiones 'emoticon object', the following data
 * structure is expected: stdClass with properties text, imagename, imagecomponent,
 * altidentifier and altcomponent
 *
 * @see admin_setting_emoticons
 *
 * @copyright 2010 David Mudrak
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emoticon_manager {

    /**
     * Returns the currently enabled emoticons
     *
     * @param boolean $selectable - If true, only return emoticons that should be selectable from a list.
     * @return array of emoticon objects
     */
    public function get_emoticons($selectable = false) {
        global $CFG;
        $notselectable = ['martin', 'egg'];

        if (empty($CFG->emoticons)) {
            return array();
        }

        $emoticons = $this->decode_stored_config($CFG->emoticons);

        if (!is_array($emoticons)) {
            // Something is wrong with the format of stored setting.
            debugging('Invalid format of emoticons setting, please resave the emoticons settings form', DEBUG_NORMAL);
            return array();
        }
        if ($selectable) {
            foreach ($emoticons as $index => $emote) {
                if (in_array($emote->altidentifier, $notselectable)) {
                    // Skip this one.
                    unset($emoticons[$index]);
                }
            }
        }

        return $emoticons;
    }

    /**
     * Converts emoticon object into renderable pix_emoticon object
     *
     * @param stdClass $emoticon emoticon object
     * @param array $attributes explicit HTML attributes to set
     * @return pix_emoticon
     */
    public function prepare_renderable_emoticon(stdClass $emoticon, array $attributes = array()) {
        $stringmanager = get_string_manager();
        if ($stringmanager->string_exists($emoticon->altidentifier, $emoticon->altcomponent)) {
            $alt = get_string($emoticon->altidentifier, $emoticon->altcomponent);
        } else {
            $alt = s($emoticon->text);
        }
        return new pix_emoticon($emoticon->imagename, $alt, $emoticon->imagecomponent, $attributes);
    }

    /**
     * Encodes the array of emoticon objects into a string storable in config table
     *
     * @see self::decode_stored_config()
     * @param array $emoticons array of emtocion objects
     * @return string
     */
    public function encode_stored_config(array $emoticons) {
        return json_encode($emoticons);
    }

    /**
     * Decodes the string into an array of emoticon objects
     *
     * @see self::encode_stored_config()
     * @param string $encoded
     * @return string|null
     */
    public function decode_stored_config($encoded) {
        $decoded = json_decode($encoded);
        if (!is_array($decoded)) {
            return null;
        }
        return $decoded;
    }

    /**
     * Returns default set of emoticons supported by Moodle
     *
     * @return array of sdtClasses
     */
    public function default_emoticons() {
        return array(
            $this->prepare_emoticon_object(":-)", 's/smiley', 'smiley'),
            $this->prepare_emoticon_object(":)", 's/smiley', 'smiley'),
            $this->prepare_emoticon_object(":-D", 's/biggrin', 'biggrin'),
            $this->prepare_emoticon_object(";-)", 's/wink', 'wink'),
            $this->prepare_emoticon_object(":-/", 's/mixed', 'mixed'),
            $this->prepare_emoticon_object("V-.", 's/thoughtful', 'thoughtful'),
            $this->prepare_emoticon_object(":-P", 's/tongueout', 'tongueout'),
            $this->prepare_emoticon_object(":-p", 's/tongueout', 'tongueout'),
            $this->prepare_emoticon_object("B-)", 's/cool', 'cool'),
            $this->prepare_emoticon_object("^-)", 's/approve', 'approve'),
            $this->prepare_emoticon_object("8-)", 's/wideeyes', 'wideeyes'),
            $this->prepare_emoticon_object(":o)", 's/clown', 'clown'),
            $this->prepare_emoticon_object(":-(", 's/sad', 'sad'),
            $this->prepare_emoticon_object(":(", 's/sad', 'sad'),
            $this->prepare_emoticon_object("8-.", 's/shy', 'shy'),
            $this->prepare_emoticon_object(":-I", 's/blush', 'blush'),
            $this->prepare_emoticon_object(":-X", 's/kiss', 'kiss'),
            $this->prepare_emoticon_object("8-o", 's/surprise', 'surprise'),
            $this->prepare_emoticon_object("P-|", 's/blackeye', 'blackeye'),
            $this->prepare_emoticon_object("8-[", 's/angry', 'angry'),
            $this->prepare_emoticon_object("(grr)", 's/angry', 'angry'),
            $this->prepare_emoticon_object("xx-P", 's/dead', 'dead'),
            $this->prepare_emoticon_object("|-.", 's/sleepy', 'sleepy'),
            $this->prepare_emoticon_object("}-]", 's/evil', 'evil'),
            $this->prepare_emoticon_object("(h)", 's/heart', 'heart'),
            $this->prepare_emoticon_object("(heart)", 's/heart', 'heart'),
            $this->prepare_emoticon_object("(y)", 's/yes', 'yes', 'core'),
            $this->prepare_emoticon_object("(n)", 's/no', 'no', 'core'),
            $this->prepare_emoticon_object("(martin)", 's/martin', 'martin'),
            $this->prepare_emoticon_object("( )", 's/egg', 'egg'),
        );
    }

    /**
     * Helper method preparing the stdClass with the emoticon properties
     *
     * @param string|array $text or array of strings
     * @param string $imagename to be used by {@link pix_emoticon}
     * @param string $altidentifier alternative string identifier, null for no alt
     * @param string $altcomponent where the alternative string is defined
     * @param string $imagecomponent to be used by {@link pix_emoticon}
     * @return stdClass
     */
    protected function prepare_emoticon_object($text, $imagename, $altidentifier = null,
                                               $altcomponent = 'core_pix', $imagecomponent = 'core') {
        return (object)array(
            'text'           => $text,
            'imagename'      => $imagename,
            'imagecomponent' => $imagecomponent,
            'altidentifier'  => $altidentifier,
            'altcomponent'   => $altcomponent,
        );
    }
}

// ENCRYPTION.

/**
 * rc4encrypt
 *
 * @param string $data        Data to encrypt.
 * @return string             The now encrypted data.
 */
function rc4encrypt($data) {
    return endecrypt(get_site_identifier(), $data, '');
}

/**
 * rc4decrypt
 *
 * @param string $data        Data to decrypt.
 * @return string             The now decrypted data.
 */
function rc4decrypt($data) {
    return endecrypt(get_site_identifier(), $data, 'de');
}

/**
 * Based on a class by Mukul Sabharwal [mukulsabharwal @ yahoo.com]
 *
 * @todo Finish documenting this function
 *
 * @param string $pwd The password to use when encrypting or decrypting
 * @param string $data The data to be decrypted/encrypted
 * @param string $case Either 'de' for decrypt or '' for encrypt
 * @return string
 */
function endecrypt ($pwd, $data, $case) {

    if ($case == 'de') {
        $data = urldecode($data);
    }

    $key[] = '';
    $box[] = '';
    $pwdlength = strlen($pwd);

    for ($i = 0; $i <= 255; $i++) {
        $key[$i] = ord(substr($pwd, ($i % $pwdlength), 1));
        $box[$i] = $i;
    }

    $x = 0;

    for ($i = 0; $i <= 255; $i++) {
        $x = ($x + $box[$i] + $key[$i]) % 256;
        $tempswap = $box[$i];
        $box[$i] = $box[$x];
        $box[$x] = $tempswap;
    }

    $cipher = '';

    $a = 0;
    $j = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $temp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $temp;
        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipherby = ord(substr($data, $i, 1)) ^ $k;
        $cipher .= chr($cipherby);
    }

    if ($case == 'de') {
        $cipher = urldecode(urlencode($cipher));
    } else {
        $cipher = urlencode($cipher);
    }

    return $cipher;
}

// ENVIRONMENT CHECKING.

/**
 * This method validates a plug name. It is much faster than calling clean_param.
 *
 * @param string $name a string that might be a plugin name.
 * @return bool if this string is a valid plugin name.
 */
function is_valid_plugin_name($name) {
    // This does not work for 'mod', bad luck, use any other type.
    return core_component::is_valid_plugin_name('tool', $name);
}

/**
 * Get a list of all the plugins of a given type that define a certain API function
 * in a certain file. The plugin component names and function names are returned.
 *
 * @param string $plugintype the type of plugin, e.g. 'mod' or 'report'.
 * @param string $function the part of the name of the function after the
 *      frankenstyle prefix. e.g 'hook' if you are looking for functions with
 *      names like report_courselist_hook.
 * @param string $file the name of file within the plugin that defines the
 *      function. Defaults to lib.php.
 * @return array with frankenstyle plugin names as keys (e.g. 'report_courselist', 'mod_forum')
 *      and the function names as values (e.g. 'report_courselist_hook', 'forum_hook').
 */
function get_plugin_list_with_function($plugintype, $function, $file = 'lib.php') {
    global $CFG;

    // We don't include here as all plugin types files would be included.
    $plugins = get_plugins_with_function($function, $file, false);

    if (empty($plugins[$plugintype])) {
        return array();
    }

    $allplugins = core_component::get_plugin_list($plugintype);

    // Reformat the array and include the files.
    $pluginfunctions = array();
    foreach ($plugins[$plugintype] as $pluginname => $functionname) {

        // Check that it has not been removed and the file is still available.
        if (!empty($allplugins[$pluginname])) {

            $filepath = $allplugins[$pluginname] . DIRECTORY_SEPARATOR . $file;
            if (file_exists($filepath)) {
                include_once($filepath);

                // Now that the file is loaded, we must verify the function still exists.
                if (function_exists($functionname)) {
                    $pluginfunctions[$plugintype . '_' . $pluginname] = $functionname;
                } else {
                    // Invalidate the cache for next run.
                    \cache_helper::invalidate_by_definition('core', 'plugin_functions');
                }
            }
        }
    }

    return $pluginfunctions;
}

/**
 * Get a list of all the plugins that define a certain API function in a certain file.
 *
 * @param string $function the part of the name of the function after the
 *      frankenstyle prefix. e.g 'hook' if you are looking for functions with
 *      names like report_courselist_hook.
 * @param string $file the name of file within the plugin that defines the
 *      function. Defaults to lib.php.
 * @param bool $include Whether to include the files that contain the functions or not.
 * @return array with [plugintype][plugin] = functionname
 */
function get_plugins_with_function($function, $file = 'lib.php', $include = true) {
    global $CFG;

    if (during_initial_install() || isset($CFG->upgraderunning)) {
        // API functions _must not_ be called during an installation or upgrade.
        return [];
    }

    $cache = \cache::make('core', 'plugin_functions');

    // Including both although I doubt that we will find two functions definitions with the same name.
    // Clearning the filename as cache_helper::hash_key only allows a-zA-Z0-9_.
    $key = $function . '_' . clean_param($file, PARAM_ALPHA);
    $pluginfunctions = $cache->get($key);
    $dirty = false;

    // Use the plugin manager to check that plugins are currently installed.
    $pluginmanager = \core_plugin_manager::instance();

    if ($pluginfunctions !== false) {

        // Checking that the files are still available.
        foreach ($pluginfunctions as $plugintype => $plugins) {

            $allplugins = \core_component::get_plugin_list($plugintype);
            $installedplugins = $pluginmanager->get_installed_plugins($plugintype);
            foreach ($plugins as $plugin => $function) {
                if (!isset($installedplugins[$plugin])) {
                    // Plugin code is still present on disk but it is not installed.
                    $dirty = true;
                    break 2;
                }

                // Cache might be out of sync with the codebase, skip the plugin if it is not available.
                if (empty($allplugins[$plugin])) {
                    $dirty = true;
                    break 2;
                }

                $fileexists = file_exists($allplugins[$plugin] . DIRECTORY_SEPARATOR . $file);
                if ($include && $fileexists) {
                    // Include the files if it was requested.
                    include_once($allplugins[$plugin] . DIRECTORY_SEPARATOR . $file);
                } else if (!$fileexists) {
                    // If the file is not available any more it should not be returned.
                    $dirty = true;
                    break 2;
                }

                // Check if the function still exists in the file.
                if ($include && !function_exists($function)) {
                    $dirty = true;
                    break 2;
                }
            }
        }

        // If the cache is dirty, we should fall through and let it rebuild.
        if (!$dirty) {
            return $pluginfunctions;
        }
    }

    $pluginfunctions = array();

    // To fill the cached. Also, everything should continue working with cache disabled.
    $plugintypes = \core_component::get_plugin_types();
    foreach ($plugintypes as $plugintype => $unused) {

        // We need to include files here.
        $pluginswithfile = \core_component::get_plugin_list_with_file($plugintype, $file, true);
        $installedplugins = $pluginmanager->get_installed_plugins($plugintype);
        foreach ($pluginswithfile as $plugin => $notused) {

            if (!isset($installedplugins[$plugin])) {
                continue;
            }

            $fullfunction = $plugintype . '_' . $plugin . '_' . $function;

            $pluginfunction = false;
            if (function_exists($fullfunction)) {
                // Function exists with standard name. Store, indexed by frankenstyle name of plugin.
                $pluginfunction = $fullfunction;

            } else if ($plugintype === 'mod') {
                // For modules, we also allow plugin without full frankenstyle but just starting with the module name.
                $shortfunction = $plugin . '_' . $function;
                if (function_exists($shortfunction)) {
                    $pluginfunction = $shortfunction;
                }
            }

            if ($pluginfunction) {
                if (empty($pluginfunctions[$plugintype])) {
                    $pluginfunctions[$plugintype] = array();
                }
                $pluginfunctions[$plugintype][$plugin] = $pluginfunction;
            }

        }
    }
    $cache->set($key, $pluginfunctions);

    return $pluginfunctions;

}

/**
 * Lists plugin-like directories within specified directory
 *
 * This function was originally used for standard Moodle plugins, please use
 * new core_component::get_plugin_list() now.
 *
 * This function is used for general directory listing and backwards compatility.
 *
 * @param string $directory relative directory from root
 * @param string $exclude dir name to exclude from the list (defaults to none)
 * @param string $basedir full path to the base dir where $plugin resides (defaults to $CFG->dirroot)
 * @return array Sorted array of directory names found under the requested parameters
 */
function get_list_of_plugins($directory='mod', $exclude='', $basedir='') {
    global $CFG;

    $plugins = array();

    if (empty($basedir)) {
        $basedir = $CFG->dirroot .'/'. $directory;

    } else {
        $basedir = $basedir .'/'. $directory;
    }

    if ($CFG->debugdeveloper and empty($exclude)) {
        // Make sure devs do not use this to list normal plugins,
        // this is intended for general directories that are not plugins!

        $subtypes = core_component::get_plugin_types();
        if (in_array($basedir, $subtypes)) {
            debugging('get_list_of_plugins() should not be used to list real plugins, use core_component::get_plugin_list() instead!', DEBUG_DEVELOPER);
        }
        unset($subtypes);
    }

    $ignorelist = array_flip(array_filter([
        'CVS',
        '_vti_cnf',
        'amd',
        'classes',
        'simpletest',
        'tests',
        'templates',
        'yui',
        $exclude,
    ]));

    if (file_exists($basedir) && filetype($basedir) == 'dir') {
        if (!$dirhandle = opendir($basedir)) {
            debugging("Directory permission error for plugin ({$directory}). Directory exists but cannot be read.", DEBUG_DEVELOPER);
            return array();
        }
        while (false !== ($dir = readdir($dirhandle))) {
            if (strpos($dir, '.') === 0) {
                // Ignore directories starting with .
                // These are treated as hidden directories.
                continue;
            }
            if (array_key_exists($dir, $ignorelist)) {
                // This directory features on the ignore list.
                continue;
            }
            if (filetype($basedir .'/'. $dir) != 'dir') {
                continue;
            }
            $plugins[] = $dir;
        }
        closedir($dirhandle);
    }
    if ($plugins) {
        asort($plugins);
    }
    return $plugins;
}

/**
 * Invoke plugin's callback functions
 *
 * @param string $type plugin type e.g. 'mod'
 * @param string $name plugin name
 * @param string $feature feature name
 * @param string $action feature's action
 * @param array $params parameters of callback function, should be an array
 * @param mixed $default default value if callback function hasn't been defined, or if it retursn null.
 * @return mixed
 *
 * @todo Decide about to deprecate and drop plugin_callback() - MDL-30743
 */
function plugin_callback($type, $name, $feature, $action, $params = null, $default = null) {
    return component_callback($type . '_' . $name, $feature . '_' . $action, (array) $params, $default);
}

/**
 * Invoke component's callback functions
 *
 * @param string $component frankenstyle component name, e.g. 'mod_quiz'
 * @param string $function the rest of the function name, e.g. 'cron' will end up calling 'mod_quiz_cron'
 * @param array $params parameters of callback function
 * @param mixed $default default value if callback function hasn't been defined, or if it retursn null.
 * @return mixed
 */
function component_callback($component, $function, array $params = array(), $default = null) {

    $functionname = component_callback_exists($component, $function);

    if ($params && (array_keys($params) !== range(0, count($params) - 1))) {
        // PHP 8 allows to have associative arrays in the call_user_func_array() parameters but
        // PHP 7 does not. Using associative arrays can result in different behavior in different PHP versions.
        // See https://php.watch/versions/8.0/named-parameters#named-params-call_user_func_array
        // This check can be removed when minimum PHP version for Moodle is raised to 8.
        debugging('Parameters array can not be an associative array while Moodle supports both PHP 7 and PHP 8.',
            DEBUG_DEVELOPER);
        $params = array_values($params);
    }

    if ($functionname) {
        // Function exists, so just return function result.
        $ret = call_user_func_array($functionname, $params);
        if (is_null($ret)) {
            return $default;
        } else {
            return $ret;
        }
    }
    return $default;
}

/**
 * Determine if a component callback exists and return the function name to call. Note that this
 * function will include the required library files so that the functioname returned can be
 * called directly.
 *
 * @param string $component frankenstyle component name, e.g. 'mod_quiz'
 * @param string $function the rest of the function name, e.g. 'cron' will end up calling 'mod_quiz_cron'
 * @return mixed Complete function name to call if the callback exists or false if it doesn't.
 * @throws coding_exception if invalid component specfied
 */
function component_callback_exists($component, $function) {
    global $CFG; // This is needed for the inclusions.

    $cleancomponent = clean_param($component, PARAM_COMPONENT);
    if (empty($cleancomponent)) {
        throw new coding_exception('Invalid component used in plugin/component_callback():' . $component);
    }
    $component = $cleancomponent;

    list($type, $name) = core_component::normalize_component($component);
    $component = $type . '_' . $name;

    $oldfunction = $name.'_'.$function;
    $function = $component.'_'.$function;

    $dir = core_component::get_component_directory($component);
    if (empty($dir)) {
        throw new coding_exception('Invalid component used in plugin/component_callback():' . $component);
    }

    // Load library and look for function.
    if (file_exists($dir.'/lib.php')) {
        require_once($dir.'/lib.php');
    }

    if (!function_exists($function) and function_exists($oldfunction)) {
        if ($type !== 'mod' and $type !== 'core') {
            debugging("Please use new function name $function instead of legacy $oldfunction", DEBUG_DEVELOPER);
        }
        $function = $oldfunction;
    }

    if (function_exists($function)) {
        return $function;
    }
    return false;
}

/**
 * Call the specified callback method on the provided class.
 *
 * If the callback returns null, then the default value is returned instead.
 * If the class does not exist, then the default value is returned.
 *
 * @param   string      $classname The name of the class to call upon.
 * @param   string      $methodname The name of the staticically defined method on the class.
 * @param   array       $params The arguments to pass into the method.
 * @param   mixed       $default The default value.
 * @return  mixed       The return value.
 */
function component_class_callback($classname, $methodname, array $params, $default = null) {
    if (!class_exists($classname)) {
        return $default;
    }

    if (!method_exists($classname, $methodname)) {
        return $default;
    }

    $fullfunction = $classname . '::' . $methodname;
    $result = call_user_func_array($fullfunction, $params);

    if (null === $result) {
        return $default;
    } else {
        return $result;
    }
}

/**
 * Checks whether a plugin supports a specified feature.
 *
 * @param string $type Plugin type e.g. 'mod'
 * @param string $name Plugin name e.g. 'forum'
 * @param string $feature Feature code (FEATURE_xx constant)
 * @param mixed $default default value if feature support unknown
 * @return mixed Feature result (false if not supported, null if feature is unknown,
 *         otherwise usually true but may have other feature-specific value such as array)
 * @throws coding_exception
 */
function plugin_supports($type, $name, $feature, $default = null) {
    global $CFG;

    if ($type === 'mod' and $name === 'NEWMODULE') {
        // Somebody forgot to rename the module template.
        return false;
    }

    $component = clean_param($type . '_' . $name, PARAM_COMPONENT);
    if (empty($component)) {
        throw new coding_exception('Invalid component used in plugin_supports():' . $type . '_' . $name);
    }

    $function = null;

    if ($type === 'mod') {
        // We need this special case because we support subplugins in modules,
        // otherwise it would end up in infinite loop.
        if (file_exists("$CFG->dirroot/mod/$name/lib.php")) {
            include_once("$CFG->dirroot/mod/$name/lib.php");
            $function = $component.'_supports';
            if (!function_exists($function)) {
                // Legacy non-frankenstyle function name.
                $function = $name.'_supports';
            }
        }

    } else {
        if (!$path = core_component::get_plugin_directory($type, $name)) {
            // Non existent plugin type.
            return false;
        }
        if (file_exists("$path/lib.php")) {
            include_once("$path/lib.php");
            $function = $component.'_supports';
        }
    }

    if ($function and function_exists($function)) {
        $supports = $function($feature);
        if (is_null($supports)) {
            // Plugin does not know - use default.
            return $default;
        } else {
            return $supports;
        }
    }

    // Plugin does not care, so use default.
    return $default;
}

/**
 * Returns true if the current version of PHP is greater that the specified one.
 *
 * @todo Check PHP version being required here is it too low?
 *
 * @param string $version The version of php being tested.
 * @return bool
 */
function check_php_version($version='5.2.4') {
    return (version_compare(phpversion(), $version) >= 0);
}

/**
 * Determine if moodle installation requires update.
 *
 * Checks version numbers of main code and all plugins to see
 * if there are any mismatches.
 *
 * @return bool
 */
function moodle_needs_upgrading() {
    global $CFG;

    if (empty($CFG->version)) {
        return true;
    }

    // There is no need to purge plugininfo caches here because
    // these caches are not used during upgrade and they are purged after
    // every upgrade.

    if (empty($CFG->allversionshash)) {
        return true;
    }

    $hash = core_component::get_all_versions_hash();

    return ($hash !== $CFG->allversionshash);
}

/**
 * Returns the major version of this site
 *
 * Moodle version numbers consist of three numbers separated by a dot, for
 * example 1.9.11 or 2.0.2. The first two numbers, like 1.9 or 2.0, represent so
 * called major version. This function extracts the major version from either
 * $CFG->release (default) or eventually from the $release variable defined in
 * the main version.php.
 *
 * @param bool $fromdisk should the version if source code files be used
 * @return string|false the major version like '2.3', false if could not be determined
 */
function moodle_major_version($fromdisk = false) {
    global $CFG;

    if ($fromdisk) {
        $release = null;
        require($CFG->dirroot.'/version.php');
        if (empty($release)) {
            return false;
        }

    } else {
        if (empty($CFG->release)) {
            return false;
        }
        $release = $CFG->release;
    }

    if (preg_match('/^[0-9]+\.[0-9]+/', $release, $matches)) {
        return $matches[0];
    } else {
        return false;
    }
}

// MISCELLANEOUS.

/**
 * Gets the system locale
 *
 * @return string Retuns the current locale.
 */
function moodle_getlocale() {
    global $CFG;

    // Fetch the correct locale based on ostype.
    if ($CFG->ostype == 'WINDOWS') {
        $stringtofetch = 'localewin';
    } else {
        $stringtofetch = 'locale';
    }

    if (!empty($CFG->locale)) { // Override locale for all language packs.
        return $CFG->locale;
    }

    return get_string($stringtofetch, 'langconfig');
}

/**
 * Sets the system locale
 *
 * @category string
 * @param string $locale Can be used to force a locale
 */
function moodle_setlocale($locale='') {
    global $CFG;

    static $currentlocale = ''; // Last locale caching.

    $oldlocale = $currentlocale;

    // The priority is the same as in get_string() - parameter, config, course, session, user, global language.
    if (!empty($locale)) {
        $currentlocale = $locale;
    } else {
        $currentlocale = moodle_getlocale();
    }

    // Do nothing if locale already set up.
    if ($oldlocale == $currentlocale) {
        return;
    }

    // Due to some strange BUG we cannot set the LC_TIME directly, so we fetch current values,
    // set LC_ALL and then set values again. Just wondering why we cannot set LC_ALL only??? - stronk7
    // Some day, numeric, monetary and other categories should be set too, I think. :-/.

    // Get current values.
    $monetary= setlocale (LC_MONETARY, 0);
    $numeric = setlocale (LC_NUMERIC, 0);
    $ctype   = setlocale (LC_CTYPE, 0);
    if ($CFG->ostype != 'WINDOWS') {
        $messages= setlocale (LC_MESSAGES, 0);
    }
    // Set locale to all.
    $result = setlocale (LC_ALL, $currentlocale);
    // If setting of locale fails try the other utf8 or utf-8 variant,
    // some operating systems support both (Debian), others just one (OSX).
    if ($result === false) {
        if (stripos($currentlocale, '.UTF-8') !== false) {
            $newlocale = str_ireplace('.UTF-8', '.UTF8', $currentlocale);
            setlocale (LC_ALL, $newlocale);
        } else if (stripos($currentlocale, '.UTF8') !== false) {
            $newlocale = str_ireplace('.UTF8', '.UTF-8', $currentlocale);
            setlocale (LC_ALL, $newlocale);
        }
    }
    // Set old values.
    setlocale (LC_MONETARY, $monetary);
    setlocale (LC_NUMERIC, $numeric);
    if ($CFG->ostype != 'WINDOWS') {
        setlocale (LC_MESSAGES, $messages);
    }
    if ($currentlocale == 'tr_TR' or $currentlocale == 'tr_TR.UTF-8') {
        // To workaround a well-known PHP problem with Turkish letter Ii.
        setlocale (LC_CTYPE, $ctype);
    }
}

/**
 * Count words in a string.
 *
 * Words are defined as things between whitespace.
 *
 * @category string
 * @param string $string The text to be searched for words. May be HTML.
 * @return int The count of words in the specified string
 */
function count_words($string) {
    // Before stripping tags, add a space after the close tag of anything that is not obviously inline.
    // Also, br is a special case because it definitely delimits a word, but has no close tag.
    $string = preg_replace('~
            (                                   # Capture the tag we match.
                </                              # Start of close tag.
                (?!                             # Do not match any of these specific close tag names.
                    a> | b> | del> | em> | i> |
                    ins> | s> | small> |
                    strong> | sub> | sup> | u>
                )
                \w+                             # But, apart from those execptions, match any tag name.
                >                               # End of close tag.
            |
                <br> | <br\s*/>                 # Special cases that are not close tags.
            )
            ~x', '$1 ', $string); // Add a space after the close tag.
    // Now remove HTML tags.
    $string = strip_tags($string);
    // Decode HTML entities.
    $string = html_entity_decode($string, ENT_COMPAT);

    // Now, the word count is the number of blocks of characters separated
    // by any sort of space. That seems to be the definition used by all other systems.
    // To be precise about what is considered to separate words:
    // * Anything that Unicode considers a 'Separator'
    // * Anything that Unicode considers a 'Control character'
    // * An em- or en- dash.
    return count(preg_split('~[\p{Z}\p{Cc}—–]+~u', $string, -1, PREG_SPLIT_NO_EMPTY));
}

/**
 * Count letters in a string.
 *
 * Letters are defined as chars not in tags and different from whitespace.
 *
 * @category string
 * @param string $string The text to be searched for letters. May be HTML.
 * @return int The count of letters in the specified text.
 */
function count_letters($string) {
    $string = strip_tags($string); // Tags are out now.
    $string = html_entity_decode($string, ENT_COMPAT);
    $string = preg_replace('/[[:space:]]*/', '', $string); // Whitespace are out now.

    return core_text::strlen($string);
}

/**
 * Generate and return a random string of the specified length.
 *
 * @param int $length The length of the string to be created.
 * @return string
 */
function random_string($length=15) {
    $randombytes = random_bytes_emulate($length);
    $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pool .= 'abcdefghijklmnopqrstuvwxyz';
    $pool .= '0123456789';
    $poollen = strlen($pool);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $rand = ord($randombytes[$i]);
        $string .= substr($pool, ($rand%($poollen)), 1);
    }
    return $string;
}

/**
 * Generate a complex random string (useful for md5 salts)
 *
 * This function is based on the above {@link random_string()} however it uses a
 * larger pool of characters and generates a string between 24 and 32 characters
 *
 * @param int $length Optional if set generates a string to exactly this length
 * @return string
 */
function complex_random_string($length=null) {
    $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $pool .= '`~!@#%^&*()_+-=[];,./<>?:{} ';
    $poollen = strlen($pool);
    if ($length===null) {
        $length = floor(rand(24, 32));
    }
    $randombytes = random_bytes_emulate($length);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $rand = ord($randombytes[$i]);
        $string .= $pool[($rand%$poollen)];
    }
    return $string;
}

/**
 * Try to generates cryptographically secure pseudo-random bytes.
 *
 * Note this is achieved by fallbacking between:
 *  - PHP 7 random_bytes().
 *  - OpenSSL openssl_random_pseudo_bytes().
 *  - In house random generator getting its entropy from various, hard to guess, pseudo-random sources.
 *
 * @param int $length requested length in bytes
 * @return string binary data
 */
function random_bytes_emulate($length) {
    global $CFG;
    if ($length <= 0) {
        debugging('Invalid random bytes length', DEBUG_DEVELOPER);
        return '';
    }
    if (function_exists('random_bytes')) {
        // Use PHP 7 goodness.
        $hash = @random_bytes($length);
        if ($hash !== false) {
            return $hash;
        }
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        // If you have the openssl extension enabled.
        $hash = openssl_random_pseudo_bytes($length);
        if ($hash !== false) {
            return $hash;
        }
    }

    // Bad luck, there is no reliable random generator, let's just slowly hash some unique stuff that is hard to guess.
    $staticdata = serialize($CFG) . serialize($_SERVER);
    $hash = '';
    do {
        $hash .= sha1($staticdata . microtime(true) . uniqid('', true), true);
    } while (strlen($hash) < $length);

    return substr($hash, 0, $length);
}

/**
 * Given some text (which may contain HTML) and an ideal length,
 * this function truncates the text neatly on a word boundary if possible
 *
 * @category string
 * @param string $text text to be shortened
 * @param int $ideal ideal string length
 * @param boolean $exact if false, $text will not be cut mid-word
 * @param string $ending The string to append if the passed string is truncated
 * @return string $truncate shortened string
 */
function shorten_text($text, $ideal=30, $exact = false, $ending='...') {
    // If the plain text is shorter than the maximum length, return the whole text.
    if (core_text::strlen(preg_replace('/<.*?>/', '', $text)) <= $ideal) {
        return $text;
    }

    // Splits on HTML tags. Each open/close/empty tag will be the first thing
    // and only tag in its 'line'.
    preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

    $totallength = core_text::strlen($ending);
    $truncate = '';

    // This array stores information about open and close tags and their position
    // in the truncated string. Each item in the array is an object with fields
    // ->open (true if open), ->tag (tag name in lower case), and ->pos
    // (byte position in truncated text).
    $tagdetails = array();

    foreach ($lines as $linematchings) {
        // If there is any html-tag in this line, handle it and add it (uncounted) to the output.
        if (!empty($linematchings[1])) {
            // If it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>).
            if (!preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $linematchings[1])) {
                if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $linematchings[1], $tagmatchings)) {
                    // Record closing tag.
                    $tagdetails[] = (object) array(
                            'open' => false,
                            'tag'  => core_text::strtolower($tagmatchings[1]),
                            'pos'  => core_text::strlen($truncate),
                        );

                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $linematchings[1], $tagmatchings)) {
                    // Record opening tag.
                    $tagdetails[] = (object) array(
                            'open' => true,
                            'tag'  => core_text::strtolower($tagmatchings[1]),
                            'pos'  => core_text::strlen($truncate),
                        );
                } else if (preg_match('/^<!--\[if\s.*?\]>$/s', $linematchings[1], $tagmatchings)) {
                    $tagdetails[] = (object) array(
                            'open' => true,
                            'tag'  => core_text::strtolower('if'),
                            'pos'  => core_text::strlen($truncate),
                    );
                } else if (preg_match('/^<!--<!\[endif\]-->$/s', $linematchings[1], $tagmatchings)) {
                    $tagdetails[] = (object) array(
                            'open' => false,
                            'tag'  => core_text::strtolower('if'),
                            'pos'  => core_text::strlen($truncate),
                    );
                }
            }
            // Add html-tag to $truncate'd text.
            $truncate .= $linematchings[1];
        }

        // Calculate the length of the plain text part of the line; handle entities as one character.
        $contentlength = core_text::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $linematchings[2]));
        if ($totallength + $contentlength > $ideal) {
            // The number of characters which are left.
            $left = $ideal - $totallength;
            $entitieslength = 0;
            // Search for html entities.
            if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $linematchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                // Calculate the real length of all entities in the legal range.
                foreach ($entities[0] as $entity) {
                    if ($entity[1]+1-$entitieslength <= $left) {
                        $left--;
                        $entitieslength += core_text::strlen($entity[0]);
                    } else {
                        // No more characters left.
                        break;
                    }
                }
            }
            $breakpos = $left + $entitieslength;

            // If the words shouldn't be cut in the middle...
            if (!$exact) {
                // Search the last occurence of a space.
                for (; $breakpos > 0; $breakpos--) {
                    if ($char = core_text::substr($linematchings[2], $breakpos, 1)) {
                        if ($char === '.' or $char === ' ') {
                            $breakpos += 1;
                            break;
                        } else if (strlen($char) > 2) {
                            // Chinese/Japanese/Korean text can be truncated at any UTF-8 character boundary.
                            $breakpos += 1;
                            break;
                        }
                    }
                }
            }
            if ($breakpos == 0) {
                // This deals with the test_shorten_text_no_spaces case.
                $breakpos = $left + $entitieslength;
            } else if ($breakpos > $left + $entitieslength) {
                // This deals with the previous for loop breaking on the first char.
                $breakpos = $left + $entitieslength;
            }

            $truncate .= core_text::substr($linematchings[2], 0, $breakpos);
            // Maximum length is reached, so get off the loop.
            break;
        } else {
            $truncate .= $linematchings[2];
            $totallength += $contentlength;
        }

        // If the maximum length is reached, get off the loop.
        if ($totallength >= $ideal) {
            break;
        }
    }

    // Add the defined ending to the text.
    $truncate .= $ending;

    // Now calculate the list of open html tags based on the truncate position.
    $opentags = array();
    foreach ($tagdetails as $taginfo) {
        if ($taginfo->open) {
            // Add tag to the beginning of $opentags list.
            array_unshift($opentags, $taginfo->tag);
        } else {
            // Can have multiple exact same open tags, close the last one.
            $pos = array_search($taginfo->tag, array_reverse($opentags, true));
            if ($pos !== false) {
                unset($opentags[$pos]);
            }
        }
    }

    // Close all unclosed html-tags.
    foreach ($opentags as $tag) {
        if ($tag === 'if') {
            $truncate .= '<!--<![endif]-->';
        } else {
            $truncate .= '</' . $tag . '>';
        }
    }

    return $truncate;
}

/**
 * Shortens a given filename by removing characters positioned after the ideal string length.
 * When the filename is too long, the file cannot be created on the filesystem due to exceeding max byte size.
 * Limiting the filename to a certain size (considering multibyte characters) will prevent this.
 *
 * @param string $filename file name
 * @param int $length ideal string length
 * @param bool $includehash Whether to include a file hash in the shortened version. This ensures uniqueness.
 * @return string $shortened shortened file name
 */
function shorten_filename($filename, $length = MAX_FILENAME_SIZE, $includehash = false) {
    $shortened = $filename;
    // Extract a part of the filename if it's char size exceeds the ideal string length.
    if (core_text::strlen($filename) > $length) {
        // Exclude extension if present in filename.
        $mimetypes = get_mimetypes_array();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if ($extension && !empty($mimetypes[$extension])) {
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $hash = empty($includehash) ? '' : ' - ' . substr(sha1($basename), 0, 10);
            $shortened = core_text::substr($basename, 0, $length - strlen($hash)) . $hash;
            $shortened .= '.' . $extension;
        } else {
            $hash = empty($includehash) ? '' : ' - ' . substr(sha1($filename), 0, 10);
            $shortened = core_text::substr($filename, 0, $length - strlen($hash)) . $hash;
        }
    }
    return $shortened;
}

/**
 * Shortens a given array of filenames by removing characters positioned after the ideal string length.
 *
 * @param array $path The paths to reduce the length.
 * @param int $length Ideal string length
 * @param bool $includehash Whether to include a file hash in the shortened version. This ensures uniqueness.
 * @return array $result Shortened paths in array.
 */
function shorten_filenames(array $path, $length = MAX_FILENAME_SIZE, $includehash = false) {
    $result = null;

    $result = array_reduce($path, function($carry, $singlepath) use ($length, $includehash) {
        $carry[] = shorten_filename($singlepath, $length, $includehash);
        return $carry;
    }, []);

    return $result;
}

/**
 * Given dates in seconds, how many weeks is the date from startdate
 * The first week is 1, the second 2 etc ...
 *
 * @param int $startdate Timestamp for the start date
 * @param int $thedate Timestamp for the end date
 * @return string
 */
function getweek ($startdate, $thedate) {
    if ($thedate < $startdate) {
        return 0;
    }

    return floor(($thedate - $startdate) / WEEKSECS) + 1;
}

/**
 * Returns a randomly generated password of length $maxlen.  inspired by
 *
 * {@link http://www.phpbuilder.com/columns/jesus19990502.php3} and
 * {@link http://es2.php.net/manual/en/function.str-shuffle.php#73254}
 *
 * @param int $maxlen  The maximum size of the password being generated.
 * @return string
 */
function generate_password($maxlen=10) {
    global $CFG;

    if (empty($CFG->passwordpolicy)) {
        $fillers = PASSWORD_DIGITS;
        $wordlist = file($CFG->wordlist);
        $word1 = trim($wordlist[rand(0, count($wordlist) - 1)]);
        $word2 = trim($wordlist[rand(0, count($wordlist) - 1)]);
        $filler1 = $fillers[rand(0, strlen($fillers) - 1)];
        $password = $word1 . $filler1 . $word2;
    } else {
        $minlen = !empty($CFG->minpasswordlength) ? $CFG->minpasswordlength : 0;
        $digits = $CFG->minpassworddigits;
        $lower = $CFG->minpasswordlower;
        $upper = $CFG->minpasswordupper;
        $nonalphanum = $CFG->minpasswordnonalphanum;
        $total = $lower + $upper + $digits + $nonalphanum;
        // Var minlength should be the greater one of the two ( $minlen and $total ).
        $minlen = $minlen < $total ? $total : $minlen;
        // Var maxlen can never be smaller than minlen.
        $maxlen = $minlen > $maxlen ? $minlen : $maxlen;
        $additional = $maxlen - $total;

        // Make sure we have enough characters to fulfill
        // complexity requirements.
        $passworddigits = PASSWORD_DIGITS;
        while ($digits > strlen($passworddigits)) {
            $passworddigits .= PASSWORD_DIGITS;
        }
        $passwordlower = PASSWORD_LOWER;
        while ($lower > strlen($passwordlower)) {
            $passwordlower .= PASSWORD_LOWER;
        }
        $passwordupper = PASSWORD_UPPER;
        while ($upper > strlen($passwordupper)) {
            $passwordupper .= PASSWORD_UPPER;
        }
        $passwordnonalphanum = PASSWORD_NONALPHANUM;
        while ($nonalphanum > strlen($passwordnonalphanum)) {
            $passwordnonalphanum .= PASSWORD_NONALPHANUM;
        }

        // Now mix and shuffle it all.
        $password = str_shuffle (substr(str_shuffle ($passwordlower), 0, $lower) .
                                 substr(str_shuffle ($passwordupper), 0, $upper) .
                                 substr(str_shuffle ($passworddigits), 0, $digits) .
                                 substr(str_shuffle ($passwordnonalphanum), 0 , $nonalphanum) .
                                 substr(str_shuffle ($passwordlower .
                                                     $passwordupper .
                                                     $passworddigits .
                                                     $passwordnonalphanum), 0 , $additional));
    }

    return substr ($password, 0, $maxlen);
}

/**
 * Given a float, prints it nicely.
 * Localized floats must not be used in calculations!
 *
 * The stripzeros feature is intended for making numbers look nicer in small
 * areas where it is not necessary to indicate the degree of accuracy by showing
 * ending zeros. If you turn it on with $decimalpoints set to 3, for example,
 * then it will display '5.4' instead of '5.400' or '5' instead of '5.000'.
 *
 * @param float $float The float to print
 * @param int $decimalpoints The number of decimal places to print. -1 is a special value for auto detect (full precision).
 * @param bool $localized use localized decimal separator
 * @param bool $stripzeros If true, removes final zeros after decimal point. It will be ignored and the trailing zeros after
 *                         the decimal point are always striped if $decimalpoints is -1.
 * @return string locale float
 */
function format_float($float, $decimalpoints=1, $localized=true, $stripzeros=false) {
    if (is_null($float)) {
        return '';
    }
    if ($localized) {
        $separator = get_string('decsep', 'langconfig');
    } else {
        $separator = '.';
    }
    if ($decimalpoints == -1) {
        // The following counts the number of decimals.
        // It is safe as both floatval() and round() functions have same behaviour when non-numeric values are provided.
        $floatval = floatval($float);
        for ($decimalpoints = 0; $floatval != round($float, $decimalpoints); $decimalpoints++);
    }

    $result = number_format($float, $decimalpoints, $separator, '');
    if ($stripzeros && $decimalpoints > 0) {
        // Remove zeros and final dot if not needed.
        // However, only do this if there is a decimal point!
        $result = preg_replace('~(' . preg_quote($separator, '~') . ')?0+$~', '', $result);
    }
    return $result;
}

/**
 * Converts locale specific floating point/comma number back to standard PHP float value
 * Do NOT try to do any math operations before this conversion on any user submitted floats!
 *
 * @param string $localefloat locale aware float representation
 * @param bool $strict If true, then check the input and return false if it is not a valid number.
 * @return mixed float|bool - false or the parsed float.
 */
function unformat_float($localefloat, $strict = false) {
    $localefloat = trim((string)$localefloat);

    if ($localefloat == '') {
        return null;
    }

    $localefloat = str_replace(' ', '', $localefloat); // No spaces - those might be used as thousand separators.
    $localefloat = str_replace(get_string('decsep', 'langconfig'), '.', $localefloat);

    if ($strict && !is_numeric($localefloat)) {
        return false;
    }

    return (float)$localefloat;
}

/**
 * Given a simple array, this shuffles it up just like shuffle()
 * Unlike PHP's shuffle() this function works on any machine.
 *
 * @param array $array The array to be rearranged
 * @return array
 */
function swapshuffle($array) {

    $last = count($array) - 1;
    for ($i = 0; $i <= $last; $i++) {
        $from = rand(0, $last);
        $curr = $array[$i];
        $array[$i] = $array[$from];
        $array[$from] = $curr;
    }
    return $array;
}

/**
 * Like {@link swapshuffle()}, but works on associative arrays
 *
 * @param array $array The associative array to be rearranged
 * @return array
 */
function swapshuffle_assoc($array) {

    $newarray = array();
    $newkeys = swapshuffle(array_keys($array));

    foreach ($newkeys as $newkey) {
        $newarray[$newkey] = $array[$newkey];
    }
    return $newarray;
}

/**
 * Given an arbitrary array, and a number of draws,
 * this function returns an array with that amount
 * of items.  The indexes are retained.
 *
 * @todo Finish documenting this function
 *
 * @param array $array
 * @param int $draws
 * @return array
 */
function draw_rand_array($array, $draws) {

    $return = array();

    $last = count($array);

    if ($draws > $last) {
        $draws = $last;
    }

    while ($draws > 0) {
        $last--;

        $keys = array_keys($array);
        $rand = rand(0, $last);

        $return[$keys[$rand]] = $array[$keys[$rand]];
        unset($array[$keys[$rand]]);

        $draws--;
    }

    return $return;
}

/**
 * Calculate the difference between two microtimes
 *
 * @param string $a The first Microtime
 * @param string $b The second Microtime
 * @return string
 */
function microtime_diff($a, $b) {
    list($adec, $asec) = explode(' ', $a);
    list($bdec, $bsec) = explode(' ', $b);
    return $bsec - $asec + $bdec - $adec;
}

/**
 * Given a list (eg a,b,c,d,e) this function returns
 * an array of 1->a, 2->b, 3->c etc
 *
 * @param string $list The string to explode into array bits
 * @param string $separator The separator used within the list string
 * @return array The now assembled array
 */
function make_menu_from_list($list, $separator=',') {

    $array = array_reverse(explode($separator, $list), true);
    foreach ($array as $key => $item) {
        $outarray[$key+1] = trim($item);
    }
    return $outarray;
}

/**
 * Creates an array that represents all the current grades that
 * can be chosen using the given grading type.
 *
 * Negative numbers
 * are scales, zero is no grade, and positive numbers are maximum
 * grades.
 *
 * @todo Finish documenting this function or better deprecated this completely!
 *
 * @param int $gradingtype
 * @return array
 */
function make_grades_menu($gradingtype) {
    global $DB;

    $grades = array();
    if ($gradingtype < 0) {
        if ($scale = $DB->get_record('scale', array('id'=> (-$gradingtype)))) {
            return make_menu_from_list($scale->scale);
        }
    } else if ($gradingtype > 0) {
        for ($i=$gradingtype; $i>=0; $i--) {
            $grades[$i] = $i .' / '. $gradingtype;
        }
        return $grades;
    }
    return $grades;
}

/**
 * make_unique_id_code
 *
 * @todo Finish documenting this function
 *
 * @uses $_SERVER
 * @param string $extra Extra string to append to the end of the code
 * @return string
 */
function make_unique_id_code($extra = '') {

    $hostname = 'unknownhost';
    if (!empty($_SERVER['HTTP_HOST'])) {
        $hostname = $_SERVER['HTTP_HOST'];
    } else if (!empty($_ENV['HTTP_HOST'])) {
        $hostname = $_ENV['HTTP_HOST'];
    } else if (!empty($_SERVER['SERVER_NAME'])) {
        $hostname = $_SERVER['SERVER_NAME'];
    } else if (!empty($_ENV['SERVER_NAME'])) {
        $hostname = $_ENV['SERVER_NAME'];
    }

    $date = gmdate("ymdHis");

    $random =  random_string(6);

    if ($extra) {
        return $hostname .'+'. $date .'+'. $random .'+'. $extra;
    } else {
        return $hostname .'+'. $date .'+'. $random;
    }
}


/**
 * Function to check the passed address is within the passed subnet
 *
 * The parameter is a comma separated string of subnet definitions.
 * Subnet strings can be in one of three formats:
 *   1: xxx.xxx.xxx.xxx/nn or xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx/nnn          (number of bits in net mask)
 *   2: xxx.xxx.xxx.xxx-yyy or  xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx::xxxx-yyyy (a range of IP addresses in the last group)
 *   3: xxx.xxx or xxx.xxx. or xxx:xxx:xxxx or xxx:xxx:xxxx.                  (incomplete address, a bit non-technical ;-)
 * Code for type 1 modified from user posted comments by mediator at
 * {@link http://au.php.net/manual/en/function.ip2long.php}
 *
 * @param string $addr    The address you are checking
 * @param string $subnetstr    The string of subnet addresses
 * @return bool
 */
function address_in_subnet($addr, $subnetstr) {

    if ($addr == '0.0.0.0') {
        return false;
    }
    $subnets = explode(',', $subnetstr);
    $found = false;
    $addr = trim($addr);
    $addr = cleanremoteaddr($addr, false); // Normalise.
    if ($addr === null) {
        return false;
    }
    $addrparts = explode(':', $addr);

    $ipv6 = strpos($addr, ':');

    foreach ($subnets as $subnet) {
        $subnet = trim($subnet);
        if ($subnet === '') {
            continue;
        }

        if (strpos($subnet, '/') !== false) {
            // 1: xxx.xxx.xxx.xxx/nn or xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx/nnn.
            list($ip, $mask) = explode('/', $subnet);
            $mask = trim($mask);
            if (!is_number($mask)) {
                continue; // Incorect mask number, eh?
            }
            $ip = cleanremoteaddr($ip, false); // Normalise.
            if ($ip === null) {
                continue;
            }
            if (strpos($ip, ':') !== false) {
                // IPv6.
                if (!$ipv6) {
                    continue;
                }
                if ($mask > 128 or $mask < 0) {
                    continue; // Nonsense.
                }
                if ($mask == 0) {
                    return true; // Any address.
                }
                if ($mask == 128) {
                    if ($ip === $addr) {
                        return true;
                    }
                    continue;
                }
                $ipparts = explode(':', $ip);
                $modulo  = $mask % 16;
                $ipnet   = array_slice($ipparts, 0, ($mask-$modulo)/16);
                $addrnet = array_slice($addrparts, 0, ($mask-$modulo)/16);
                if (implode(':', $ipnet) === implode(':', $addrnet)) {
                    if ($modulo == 0) {
                        return true;
                    }
                    $pos     = ($mask-$modulo)/16;
                    $ipnet   = hexdec($ipparts[$pos]);
                    $addrnet = hexdec($addrparts[$pos]);
                    $mask    = 0xffff << (16 - $modulo);
                    if (($addrnet & $mask) == ($ipnet & $mask)) {
                        return true;
                    }
                }

            } else {
                // IPv4.
                if ($ipv6) {
                    continue;
                }
                if ($mask > 32 or $mask < 0) {
                    continue; // Nonsense.
                }
                if ($mask == 0) {
                    return true;
                }
                if ($mask == 32) {
                    if ($ip === $addr) {
                        return true;
                    }
                    continue;
                }
                $mask = 0xffffffff << (32 - $mask);
                if (((ip2long($addr) & $mask) == (ip2long($ip) & $mask))) {
                    return true;
                }
            }

        } else if (strpos($subnet, '-') !== false) {
            // 2: xxx.xxx.xxx.xxx-yyy or  xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx::xxxx-yyyy. A range of IP addresses in the last group.
            $parts = explode('-', $subnet);
            if (count($parts) != 2) {
                continue;
            }

            if (strpos($subnet, ':') !== false) {
                // IPv6.
                if (!$ipv6) {
                    continue;
                }
                $ipstart = cleanremoteaddr(trim($parts[0]), false); // Normalise.
                if ($ipstart === null) {
                    continue;
                }
                $ipparts = explode(':', $ipstart);
                $start = hexdec(array_pop($ipparts));
                $ipparts[] = trim($parts[1]);
                $ipend = cleanremoteaddr(implode(':', $ipparts), false); // Normalise.
                if ($ipend === null) {
                    continue;
                }
                $ipparts[7] = '';
                $ipnet = implode(':', $ipparts);
                if (strpos($addr, $ipnet) !== 0) {
                    continue;
                }
                $ipparts = explode(':', $ipend);
                $end = hexdec($ipparts[7]);

                $addrend = hexdec($addrparts[7]);

                if (($addrend >= $start) and ($addrend <= $end)) {
                    return true;
                }

            } else {
                // IPv4.
                if ($ipv6) {
                    continue;
                }
                $ipstart = cleanremoteaddr(trim($parts[0]), false); // Normalise.
                if ($ipstart === null) {
                    continue;
                }
                $ipparts = explode('.', $ipstart);
                $ipparts[3] = trim($parts[1]);
                $ipend = cleanremoteaddr(implode('.', $ipparts), false); // Normalise.
                if ($ipend === null) {
                    continue;
                }

                if ((ip2long($addr) >= ip2long($ipstart)) and (ip2long($addr) <= ip2long($ipend))) {
                    return true;
                }
            }

        } else {
            // 3: xxx.xxx or xxx.xxx. or xxx:xxx:xxxx or xxx:xxx:xxxx.
            if (strpos($subnet, ':') !== false) {
                // IPv6.
                if (!$ipv6) {
                    continue;
                }
                $parts = explode(':', $subnet);
                $count = count($parts);
                if ($parts[$count-1] === '') {
                    unset($parts[$count-1]); // Trim trailing :'s.
                    $count--;
                    $subnet = implode('.', $parts);
                }
                $isip = cleanremoteaddr($subnet, false); // Normalise.
                if ($isip !== null) {
                    if ($isip === $addr) {
                        return true;
                    }
                    continue;
                } else if ($count > 8) {
                    continue;
                }
                $zeros = array_fill(0, 8-$count, '0');
                $subnet = $subnet.':'.implode(':', $zeros).'/'.($count*16);
                if (address_in_subnet($addr, $subnet)) {
                    return true;
                }

            } else {
                // IPv4.
                if ($ipv6) {
                    continue;
                }
                $parts = explode('.', $subnet);
                $count = count($parts);
                if ($parts[$count-1] === '') {
                    unset($parts[$count-1]); // Trim trailing .
                    $count--;
                    $subnet = implode('.', $parts);
                }
                if ($count == 4) {
                    $subnet = cleanremoteaddr($subnet, false); // Normalise.
                    if ($subnet === $addr) {
                        return true;
                    }
                    continue;
                } else if ($count > 4) {
                    continue;
                }
                $zeros = array_fill(0, 4-$count, '0');
                $subnet = $subnet.'.'.implode('.', $zeros).'/'.($count*8);
                if (address_in_subnet($addr, $subnet)) {
                    return true;
                }
            }
        }
    }

    return false;
}

/**
 * For outputting debugging info
 *
 * @param string $string The string to write
 * @param string $eol The end of line char(s) to use
 * @param string $sleep Period to make the application sleep
 *                      This ensures any messages have time to display before redirect
 */
function mtrace($string, $eol="\n", $sleep=0) {
    global $CFG;

    if (isset($CFG->mtrace_wrapper) && function_exists($CFG->mtrace_wrapper)) {
        $fn = $CFG->mtrace_wrapper;
        $fn($string, $eol);
        return;
    } else if (defined('STDOUT') && !PHPUNIT_TEST && !defined('BEHAT_TEST')) {
        // We must explicitly call the add_line function here.
        // Uses of fwrite to STDOUT are not picked up by ob_start.
        if ($output = \core\task\logmanager::add_line("{$string}{$eol}")) {
            fwrite(STDOUT, $output);
        }
    } else {
        echo $string . $eol;
    }

    // Flush again.
    flush();

    // Delay to keep message on user's screen in case of subsequent redirect.
    if ($sleep) {
        sleep($sleep);
    }
}

/**
 * Helper to {@see mtrace()} an exception or throwable, including all relevant information.
 *
 * @param Throwable $e the error to ouptput.
 */
function mtrace_exception(Throwable $e): void {
    $info = get_exception_info($e);

    $message = $info->message;
    if ($info->debuginfo) {
        $message .= "\n\n" . $info->debuginfo;
    }
    if ($info->backtrace) {
        $message .= "\n\n" . format_backtrace($info->backtrace, true);
    }

    mtrace($message);
}

/**
 * Replace 1 or more slashes or backslashes to 1 slash
 *
 * @param string $path The path to strip
 * @return string the path with double slashes removed
 */
function cleardoubleslashes ($path) {
    return preg_replace('/(\/|\\\){1,}/', '/', $path);
}

/**
 * Is the current ip in a given list?
 *
 * @param string $list
 * @return bool
 */
function remoteip_in_list($list) {
    $clientip = getremoteaddr(null);

    if (!$clientip) {
        // Ensure access on cli.
        return true;
    }
    return \core\ip_utils::is_ip_in_subnet_list($clientip, $list);
}

/**
 * Returns most reliable client address
 *
 * @param string $default If an address can't be determined, then return this
 * @return string The remote IP address
 */
function getremoteaddr($default='0.0.0.0') {
    global $CFG;

    if (!isset($CFG->getremoteaddrconf)) {
        // This will happen, for example, before just after the upgrade, as the
        // user is redirected to the admin screen.
        $variablestoskip = GETREMOTEADDR_SKIP_DEFAULT;
    } else {
        $variablestoskip = $CFG->getremoteaddrconf;
    }
    if (!($variablestoskip & GETREMOTEADDR_SKIP_HTTP_CLIENT_IP)) {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $address = cleanremoteaddr($_SERVER['HTTP_CLIENT_IP']);
            return $address ? $address : $default;
        }
    }
    if (!($variablestoskip & GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR)) {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwardedaddresses = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);

            $forwardedaddresses = array_filter($forwardedaddresses, function($ip) {
                global $CFG;
                return !\core\ip_utils::is_ip_in_subnet_list($ip, $CFG->reverseproxyignore ?? '', ',');
            });

            // Multiple proxies can append values to this header including an
            // untrusted original request header so we must only trust the last ip.
            $address = end($forwardedaddresses);

            if (substr_count($address, ":") > 1) {
                // Remove port and brackets from IPv6.
                if (preg_match("/\[(.*)\]:/", $address, $matches)) {
                    $address = $matches[1];
                }
            } else {
                // Remove port from IPv4.
                if (substr_count($address, ":") == 1) {
                    $parts = explode(":", $address);
                    $address = $parts[0];
                }
            }

            $address = cleanremoteaddr($address);
            return $address ? $address : $default;
        }
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $address = cleanremoteaddr($_SERVER['REMOTE_ADDR']);
        return $address ? $address : $default;
    } else {
        return $default;
    }
}

/**
 * Cleans an ip address. Internal addresses are now allowed.
 * (Originally local addresses were not allowed.)
 *
 * @param string $addr IPv4 or IPv6 address
 * @param bool $compress use IPv6 address compression
 * @return string normalised ip address string, null if error
 */
function cleanremoteaddr($addr, $compress=false) {
    $addr = trim($addr);

    if (strpos($addr, ':') !== false) {
        // Can be only IPv6.
        $parts = explode(':', $addr);
        $count = count($parts);

        if (strpos($parts[$count-1], '.') !== false) {
            // Legacy ipv4 notation.
            $last = array_pop($parts);
            $ipv4 = cleanremoteaddr($last, true);
            if ($ipv4 === null) {
                return null;
            }
            $bits = explode('.', $ipv4);
            $parts[] = dechex($bits[0]).dechex($bits[1]);
            $parts[] = dechex($bits[2]).dechex($bits[3]);
            $count = count($parts);
            $addr = implode(':', $parts);
        }

        if ($count < 3 or $count > 8) {
            return null; // Severly malformed.
        }

        if ($count != 8) {
            if (strpos($addr, '::') === false) {
                return null; // Malformed.
            }
            // Uncompress.
            $insertat = array_search('', $parts, true);
            $missing = array_fill(0, 1 + 8 - $count, '0');
            array_splice($parts, $insertat, 1, $missing);
            foreach ($parts as $key => $part) {
                if ($part === '') {
                    $parts[$key] = '0';
                }
            }
        }

        $adr = implode(':', $parts);
        if (!preg_match('/^([0-9a-f]{1,4})(:[0-9a-f]{1,4})*$/i', $adr)) {
            return null; // Incorrect format - sorry.
        }

        // Normalise 0s and case.
        $parts = array_map('hexdec', $parts);
        $parts = array_map('dechex', $parts);

        $result = implode(':', $parts);

        if (!$compress) {
            return $result;
        }

        if ($result === '0:0:0:0:0:0:0:0') {
            return '::'; // All addresses.
        }

        $compressed = preg_replace('/(:0)+:0$/', '::', $result, 1);
        if ($compressed !== $result) {
            return $compressed;
        }

        $compressed = preg_replace('/^(0:){2,7}/', '::', $result, 1);
        if ($compressed !== $result) {
            return $compressed;
        }

        $compressed = preg_replace('/(:0){2,6}:/', '::', $result, 1);
        if ($compressed !== $result) {
            return $compressed;
        }

        return $result;
    }

    // First get all things that look like IPv4 addresses.
    $parts = array();
    if (!preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $addr, $parts)) {
        return null;
    }
    unset($parts[0]);

    foreach ($parts as $key => $match) {
        if ($match > 255) {
            return null;
        }
        $parts[$key] = (int)$match; // Normalise 0s.
    }

    return implode('.', $parts);
}


/**
 * Is IP address a public address?
 *
 * @param string $ip The ip to check
 * @return bool true if the ip is public
 */
function ip_is_public($ip) {
    return (bool) filter_var($ip, FILTER_VALIDATE_IP, (FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
}

/**
 * This function will make a complete copy of anything it's given,
 * regardless of whether it's an object or not.
 *
 * @param mixed $thing Something you want cloned
 * @return mixed What ever it is you passed it
 */
function fullclone($thing) {
    return unserialize(serialize($thing));
}

/**
 * Used to make sure that $min <= $value <= $max
 *
 * Make sure that value is between min, and max
 *
 * @param int $min The minimum value
 * @param int $value The value to check
 * @param int $max The maximum value
 * @return int
 */
function bounded_number($min, $value, $max) {
    if ($value < $min) {
        return $min;
    }
    if ($value > $max) {
        return $max;
    }
    return $value;
}

/**
 * Check if there is a nested array within the passed array
 *
 * @param array $array
 * @return bool true if there is a nested array false otherwise
 */
function array_is_nested($array) {
    foreach ($array as $value) {
        if (is_array($value)) {
            return true;
        }
    }
    return false;
}

/**
 * get_performance_info() pairs up with init_performance_info()
 * loaded in setup.php. Returns an array with 'html' and 'txt'
 * values ready for use, and each of the individual stats provided
 * separately as well.
 *
 * @return array
 */
function get_performance_info() {
    global $CFG, $PERF, $DB, $PAGE;

    $info = array();
    $info['txt']  = me() . ' '; // Holds log-friendly representation.

    $info['html'] = '';
    if (!empty($CFG->themedesignermode)) {
        // Attempt to avoid devs debugging peformance issues, when its caused by css building and so on.
        $info['html'] .= '<p><strong>Warning: Theme designer mode is enabled.</strong></p>';
    }
    $info['html'] .= '<ul class="list-unstyled row mx-md-0">';         // Holds userfriendly HTML representation.

    $info['realtime'] = microtime_diff($PERF->starttime, microtime());

    $info['html'] .= '<li class="timeused col-sm-4">'.$info['realtime'].' secs</li> ';
    $info['txt'] .= 'time: '.$info['realtime'].'s ';

    // GET/POST (or NULL if $_SERVER['REQUEST_METHOD'] is undefined) is useful for txt logged information.
    $info['txt'] .= 'method: ' . ($_SERVER['REQUEST_METHOD'] ?? "NULL") . ' ';

    if (function_exists('memory_get_usage')) {
        $info['memory_total'] = memory_get_usage();
        $info['memory_growth'] = memory_get_usage() - $PERF->startmemory;
        $info['html'] .= '<li class="memoryused col-sm-4">RAM: '.display_size($info['memory_total']).'</li> ';
        $info['txt']  .= 'memory_total: '.$info['memory_total'].'B (' . display_size($info['memory_total']).') memory_growth: '.
            $info['memory_growth'].'B ('.display_size($info['memory_growth']).') ';
    }

    if (function_exists('memory_get_peak_usage')) {
        $info['memory_peak'] = memory_get_peak_usage();
        $info['html'] .= '<li class="memoryused col-sm-4">RAM peak: '.display_size($info['memory_peak']).'</li> ';
        $info['txt']  .= 'memory_peak: '.$info['memory_peak'].'B (' . display_size($info['memory_peak']).') ';
    }

    $info['html'] .= '</ul><ul class="list-unstyled row mx-md-0">';
    $inc = get_included_files();
    $info['includecount'] = count($inc);
    $info['html'] .= '<li class="included col-sm-4">Included '.$info['includecount'].' files</li> ';
    $info['txt']  .= 'includecount: '.$info['includecount'].' ';

    if (!empty($CFG->early_install_lang) or empty($PAGE)) {
        // We can not track more performance before installation or before PAGE init, sorry.
        return $info;
    }

    $filtermanager = filter_manager::instance();
    if (method_exists($filtermanager, 'get_performance_summary')) {
        list($filterinfo, $nicenames) = $filtermanager->get_performance_summary();
        $info = array_merge($filterinfo, $info);
        foreach ($filterinfo as $key => $value) {
            $info['html'] .= "<li class='$key col-sm-4'>$nicenames[$key]: $value </li> ";
            $info['txt'] .= "$key: $value ";
        }
    }

    $stringmanager = get_string_manager();
    if (method_exists($stringmanager, 'get_performance_summary')) {
        list($filterinfo, $nicenames) = $stringmanager->get_performance_summary();
        $info = array_merge($filterinfo, $info);
        foreach ($filterinfo as $key => $value) {
            $info['html'] .= "<li class='$key col-sm-4'>$nicenames[$key]: $value </li> ";
            $info['txt'] .= "$key: $value ";
        }
    }

    if (!empty($PERF->logwrites)) {
        $info['logwrites'] = $PERF->logwrites;
        $info['html'] .= '<li class="logwrites col-sm-4">Log DB writes '.$info['logwrites'].'</li> ';
        $info['txt'] .= 'logwrites: '.$info['logwrites'].' ';
    }

    $info['dbqueries'] = $DB->perf_get_reads().'/'.($DB->perf_get_writes() - $PERF->logwrites);
    $info['html'] .= '<li class="dbqueries col-sm-4">DB reads/writes: '.$info['dbqueries'].'</li> ';
    $info['txt'] .= 'db reads/writes: '.$info['dbqueries'].' ';

    if ($DB->want_read_slave()) {
        $info['dbreads_slave'] = $DB->perf_get_reads_slave();
        $info['html'] .= '<li class="dbqueries col-sm-4">DB reads from slave: '.$info['dbreads_slave'].'</li> ';
        $info['txt'] .= 'db reads from slave: '.$info['dbreads_slave'].' ';
    }

    $info['dbtime'] = round($DB->perf_get_queries_time(), 5);
    $info['html'] .= '<li class="dbtime col-sm-4">DB queries time: '.$info['dbtime'].' secs</li> ';
    $info['txt'] .= 'db queries time: ' . $info['dbtime'] . 's ';

    if (function_exists('posix_times')) {
        $ptimes = posix_times();
        if (is_array($ptimes)) {
            foreach ($ptimes as $key => $val) {
                $info[$key] = $ptimes[$key] -  $PERF->startposixtimes[$key];
            }
            $info['html'] .= "<li class=\"posixtimes col-sm-4\">ticks: $info[ticks] user: $info[utime]";
            $info['html'] .= "sys: $info[stime] cuser: $info[cutime] csys: $info[cstime]</li> ";
            $info['txt'] .= "ticks: $info[ticks] user: $info[utime] sys: $info[stime] cuser: $info[cutime] csys: $info[cstime] ";
        }
    }

    // Grab the load average for the last minute.
    // /proc will only work under some linux configurations
    // while uptime is there under MacOSX/Darwin and other unices.
    if (is_readable('/proc/loadavg') && $loadavg = @file('/proc/loadavg')) {
        list($serverload) = explode(' ', $loadavg[0]);
        unset($loadavg);
    } else if ( function_exists('is_executable') && is_executable('/usr/bin/uptime') && $loadavg = `/usr/bin/uptime` ) {
        if (preg_match('/load averages?: (\d+[\.,:]\d+)/', $loadavg, $matches)) {
            $serverload = $matches[1];
        } else {
            trigger_error('Could not parse uptime output!');
        }
    }
    if (!empty($serverload)) {
        $info['serverload'] = $serverload;
        $info['html'] .= '<li class="serverload col-sm-4">Load average: '.$info['serverload'].'</li> ';
        $info['txt'] .= "serverload: {$info['serverload']} ";
    }

    // Display size of session if session started.
    if ($si = \core\session\manager::get_performance_info()) {
        $info['sessionsize'] = $si['size'];
        $info['html'] .= "<li class=\"serverload col-sm-4\">" . $si['html'] . "</li>";
        $info['txt'] .= $si['txt'];
    }

    // Display time waiting for session if applicable.
    if (!empty($PERF->sessionlock['wait'])) {
        $sessionwait = number_format($PERF->sessionlock['wait'], 3) . ' secs';
        $info['html'] .= html_writer::tag('li', 'Session wait: ' . $sessionwait, [
            'class' => 'sessionwait col-sm-4'
        ]);
        $info['txt'] .= 'sessionwait: ' . $sessionwait . ' ';
    }

    $info['html'] .= '</ul>';
    $html = '';
    if ($stats = cache_helper::get_stats()) {

        $table = new html_table();
        $table->attributes['class'] = 'cachesused table table-dark table-sm w-auto table-bordered';
        $table->head = ['Mode', 'Cache item', 'Static', 'H', 'M', get_string('mappingprimary', 'cache'), 'H', 'M', 'S', 'I/O'];
        $table->data = [];
        $table->align = ['left', 'left', 'left', 'right', 'right', 'left', 'right', 'right', 'right', 'right'];

        $text = 'Caches used (hits/misses/sets): ';
        $hits = 0;
        $misses = 0;
        $sets = 0;
        $maxstores = 0;

        // We want to align static caches into their own column.
        $hasstatic = false;
        foreach ($stats as $definition => $details) {
            $numstores = count($details['stores']);
            $first = key($details['stores']);
            if ($first !== cache_store::STATIC_ACCEL) {
                $numstores++; // Add a blank space for the missing static store.
            }
            $maxstores = max($maxstores, $numstores);
        }

        $storec = 0;

        while ($storec++ < ($maxstores - 2)) {
            if ($storec == ($maxstores - 2)) {
                $table->head[] = get_string('mappingfinal', 'cache');
            } else {
                $table->head[] = "Store $storec";
            }
            $table->align[] = 'left';
            $table->align[] = 'right';
            $table->align[] = 'right';
            $table->align[] = 'right';
            $table->align[] = 'right';
            $table->head[] = 'H';
            $table->head[] = 'M';
            $table->head[] = 'S';
            $table->head[] = 'I/O';
        }

        ksort($stats);

        foreach ($stats as $definition => $details) {
            switch ($details['mode']) {
                case cache_store::MODE_APPLICATION:
                    $modeclass = 'application';
                    $mode = ' <span title="application cache">App</span>';
                    break;
                case cache_store::MODE_SESSION:
                    $modeclass = 'session';
                    $mode = ' <span title="session cache">Ses</span>';
                    break;
                case cache_store::MODE_REQUEST:
                    $modeclass = 'request';
                    $mode = ' <span title="request cache">Req</span>';
                    break;
            }
            $row = [$mode, $definition];

            $text .= "$definition {";

            $storec = 0;
            foreach ($details['stores'] as $store => $data) {

                if ($storec == 0 && $store !== cache_store::STATIC_ACCEL) {
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $storec++;
                }

                $hits   += $data['hits'];
                $misses += $data['misses'];
                $sets   += $data['sets'];
                if ($data['hits'] == 0 and $data['misses'] > 0) {
                    $cachestoreclass = 'nohits bg-danger';
                } else if ($data['hits'] < $data['misses']) {
                    $cachestoreclass = 'lowhits bg-warning text-dark';
                } else {
                    $cachestoreclass = 'hihits';
                }
                $text .= "$store($data[hits]/$data[misses]/$data[sets]) ";
                $cell = new html_table_cell($store);
                $cell->attributes = ['class' => $cachestoreclass];
                $row[] = $cell;
                $cell = new html_table_cell($data['hits']);
                $cell->attributes = ['class' => $cachestoreclass];
                $row[] = $cell;
                $cell = new html_table_cell($data['misses']);
                $cell->attributes = ['class' => $cachestoreclass];
                $row[] = $cell;

                if ($store !== cache_store::STATIC_ACCEL) {
                    // The static cache is never set.
                    $cell = new html_table_cell($data['sets']);
                    $cell->attributes = ['class' => $cachestoreclass];
                    $row[] = $cell;

                    if ($data['hits'] || $data['sets']) {
                        if ($data['iobytes'] === cache_store::IO_BYTES_NOT_SUPPORTED) {
                            $size = '-';
                        } else {
                            $size = display_size($data['iobytes'], 1, 'KB');
                            if ($data['iobytes'] >= 10 * 1024) {
                                $cachestoreclass = ' bg-warning text-dark';
                            }
                        }
                    } else {
                        $size = '';
                    }
                    $cell = new html_table_cell($size);
                    $cell->attributes = ['class' => $cachestoreclass];
                    $row[] = $cell;
                }
                $storec++;
            }
            while ($storec++ < $maxstores) {
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
            }
            $text .= '} ';

            $table->data[] = $row;
        }

        $html .= html_writer::table($table);

        // Now lets also show sub totals for each cache store.
        $storetotals = [];
        $storetotal = ['hits' => 0, 'misses' => 0, 'sets' => 0, 'iobytes' => 0];
        foreach ($stats as $definition => $details) {
            foreach ($details['stores'] as $store => $data) {
                if (!array_key_exists($store, $storetotals)) {
                    $storetotals[$store] = ['hits' => 0, 'misses' => 0, 'sets' => 0, 'iobytes' => 0];
                }
                $storetotals[$store]['class']   = $data['class'];
                $storetotals[$store]['hits']   += $data['hits'];
                $storetotals[$store]['misses'] += $data['misses'];
                $storetotals[$store]['sets']   += $data['sets'];
                $storetotal['hits']   += $data['hits'];
                $storetotal['misses'] += $data['misses'];
                $storetotal['sets']   += $data['sets'];
                if ($data['iobytes'] !== cache_store::IO_BYTES_NOT_SUPPORTED) {
                    $storetotals[$store]['iobytes'] += $data['iobytes'];
                    $storetotal['iobytes'] += $data['iobytes'];
                }
            }
        }

        $table = new html_table();
        $table->attributes['class'] = 'cachesused table table-dark table-sm w-auto table-bordered';
        $table->head = [get_string('storename', 'cache'), get_string('type_cachestore', 'plugin'), 'H', 'M', 'S', 'I/O'];
        $table->data = [];
        $table->align = ['left', 'left', 'right', 'right', 'right', 'right'];

        ksort($storetotals);

        foreach ($storetotals as $store => $data) {
            $row = [];
            if ($data['hits'] == 0 and $data['misses'] > 0) {
                $cachestoreclass = 'nohits bg-danger';
            } else if ($data['hits'] < $data['misses']) {
                $cachestoreclass = 'lowhits bg-warning text-dark';
            } else {
                $cachestoreclass = 'hihits';
            }
            $cell = new html_table_cell($store);
            $cell->attributes = ['class' => $cachestoreclass];
            $row[] = $cell;
            $cell = new html_table_cell($data['class']);
            $cell->attributes = ['class' => $cachestoreclass];
            $row[] = $cell;
            $cell = new html_table_cell($data['hits']);
            $cell->attributes = ['class' => $cachestoreclass];
            $row[] = $cell;
            $cell = new html_table_cell($data['misses']);
            $cell->attributes = ['class' => $cachestoreclass];
            $row[] = $cell;
            $cell = new html_table_cell($data['sets']);
            $cell->attributes = ['class' => $cachestoreclass];
            $row[] = $cell;
            if ($data['hits'] || $data['sets']) {
                if ($data['iobytes']) {
                    $size = display_size($data['iobytes'], 1, 'KB');
                } else {
                    $size = '-';
                }
            } else {
                $size = '';
            }
            $cell = new html_table_cell($size);
            $cell->attributes = ['class' => $cachestoreclass];
            $row[] = $cell;
            $table->data[] = $row;
        }
        if (!empty($storetotal['iobytes'])) {
            $size = display_size($storetotal['iobytes'], 1, 'KB');
        } else if (!empty($storetotal['hits']) || !empty($storetotal['sets'])) {
            $size = '-';
        } else {
            $size = '';
        }
        $row = [
            get_string('total'),
            '',
            $storetotal['hits'],
            $storetotal['misses'],
            $storetotal['sets'],
            $size,
        ];
        $table->data[] = $row;

        $html .= html_writer::table($table);

        $info['cachesused'] = "$hits / $misses / $sets";
        $info['html'] .= $html;
        $info['txt'] .= $text.'. ';
    } else {
        $info['cachesused'] = '0 / 0 / 0';
        $info['html'] .= '<div class="cachesused">Caches used (hits/misses/sets): 0/0/0</div>';
        $info['txt'] .= 'Caches used (hits/misses/sets): 0/0/0 ';
    }

    // Display lock information if any.
    if (!empty($PERF->locks)) {
        $table = new html_table();
        $table->attributes['class'] = 'locktimings table table-dark table-sm w-auto table-bordered';
        $table->head = ['Lock', 'Waited (s)', 'Obtained', 'Held for (s)'];
        $table->align = ['left', 'right', 'center', 'right'];
        $table->data = [];
        $text = 'Locks (waited/obtained/held):';
        foreach ($PERF->locks as $locktiming) {
            $row = [];
            $row[] = s($locktiming->type . '/' . $locktiming->resource);
            $text .= ' ' . $locktiming->type . '/' . $locktiming->resource . ' (';

            // The time we had to wait to get the lock.
            $roundedtime = number_format($locktiming->wait, 1);
            $cell = new html_table_cell($roundedtime);
            if ($locktiming->wait > 0.5) {
                $cell->attributes = ['class' => 'bg-warning text-dark'];
            }
            $row[] = $cell;
            $text .= $roundedtime . '/';

            // Show a tick or cross for success.
            $row[] = $locktiming->success ? '&#x2713;' : '&#x274c;';
            $text .= ($locktiming->success ? 'y' : 'n') . '/';

            // If applicable, show how long we held the lock before releasing it.
            if (property_exists($locktiming, 'held')) {
                $roundedtime = number_format($locktiming->held, 1);
                $cell = new html_table_cell($roundedtime);
                if ($locktiming->held > 0.5) {
                    $cell->attributes = ['class' => 'bg-warning text-dark'];
                }
                $row[] = $cell;
                $text .= $roundedtime;
            } else {
                $row[] = '-';
                $text .= '-';
            }
            $text .= ')';

            $table->data[] = $row;
        }
        $info['html'] .= html_writer::table($table);
        $info['txt'] .= $text . '. ';
    }

    $info['html'] = '<div class="performanceinfo siteinfo container-fluid px-md-0 overflow-auto pt-3">'.$info['html'].'</div>';
    return $info;
}

/**
 * Renames a file or directory to a unique name within the same directory.
 *
 * This function is designed to avoid any potential race conditions, and select an unused name.
 *
 * @param string $filepath Original filepath
 * @param string $prefix Prefix to use for the temporary name
 * @return string|bool New file path or false if failed
 * @since Moodle 3.10
 */
function rename_to_unused_name(string $filepath, string $prefix = '_temp_') {
    $dir = dirname($filepath);
    $basename = $dir . '/' . $prefix;
    $limit = 0;
    while ($limit < 100) {
        // Select a new name based on a random number.
        $newfilepath = $basename . md5(mt_rand());

        // Attempt a rename to that new name.
        if (@rename($filepath, $newfilepath)) {
            return $newfilepath;
        }

        // The first time, do some sanity checks, maybe it is failing for a good reason and there
        // is no point trying 100 times if so.
        if ($limit === 0 && (!file_exists($filepath) || !is_writable($dir))) {
            return false;
        }
        $limit++;
    }
    return false;
}

/**
 * Delete directory or only its content
 *
 * @param string $dir directory path
 * @param bool $contentonly
 * @return bool success, true also if dir does not exist
 */
function remove_dir($dir, $contentonly=false) {
    if (!is_dir($dir)) {
        // Nothing to do.
        return true;
    }

    if (!$contentonly) {
        // Start by renaming the directory; this will guarantee that other processes don't write to it
        // while it is in the process of being deleted.
        $tempdir = rename_to_unused_name($dir);
        if ($tempdir) {
            // If the rename was successful then delete the $tempdir instead.
            $dir = $tempdir;
        }
        // If the rename fails, we will continue through and attempt to delete the directory
        // without renaming it since that is likely to at least delete most of the files.
    }

    if (!$handle = opendir($dir)) {
        return false;
    }
    $result = true;
    while (false!==($item = readdir($handle))) {
        if ($item != '.' && $item != '..') {
            if (is_dir($dir.'/'.$item)) {
                $result = remove_dir($dir.'/'.$item) && $result;
            } else {
                $result = unlink($dir.'/'.$item) && $result;
            }
        }
    }
    closedir($handle);
    if ($contentonly) {
        clearstatcache(); // Make sure file stat cache is properly invalidated.
        return $result;
    }
    $result = rmdir($dir); // If anything left the result will be false, no need for && $result.
    clearstatcache(); // Make sure file stat cache is properly invalidated.
    return $result;
}

/**
 * Detect if an object or a class contains a given property
 * will take an actual object or the name of a class
 *
 * @param mix $obj Name of class or real object to test
 * @param string $property name of property to find
 * @return bool true if property exists
 */
function object_property_exists( $obj, $property ) {
    if (is_string( $obj )) {
        $properties = get_class_vars( $obj );
    } else {
        $properties = get_object_vars( $obj );
    }
    return array_key_exists( $property, $properties );
}

/**
 * Converts an object into an associative array
 *
 * This function converts an object into an associative array by iterating
 * over its public properties. Because this function uses the foreach
 * construct, Iterators are respected. It works recursively on arrays of objects.
 * Arrays and simple values are returned as is.
 *
 * If class has magic properties, it can implement IteratorAggregate
 * and return all available properties in getIterator()
 *
 * @param mixed $var
 * @return array
 */
function convert_to_array($var) {
    $result = array();

    // Loop over elements/properties.
    foreach ($var as $key => $value) {
        // Recursively convert objects.
        if (is_object($value) || is_array($value)) {
            $result[$key] = convert_to_array($value);
        } else {
            // Simple values are untouched.
            $result[$key] = $value;
        }
    }
    return $result;
}

/**
 * Detect a custom script replacement in the data directory that will
 * replace an existing moodle script
 *
 * @return string|bool full path name if a custom script exists, false if no custom script exists
 */
function custom_script_path() {
    global $CFG, $SCRIPT;

    if ($SCRIPT === null) {
        // Probably some weird external script.
        return false;
    }

    $scriptpath = $CFG->customscripts . $SCRIPT;

    // Check the custom script exists.
    if (file_exists($scriptpath) and is_file($scriptpath)) {
        return $scriptpath;
    } else {
        return false;
    }
}

/**
 * Returns whether or not the user object is a remote MNET user. This function
 * is in moodlelib because it does not rely on loading any of the MNET code.
 *
 * @param object $user A valid user object
 * @return bool        True if the user is from a remote Moodle.
 */
function is_mnet_remote_user($user) {
    global $CFG;

    if (!isset($CFG->mnet_localhost_id)) {
        include_once($CFG->dirroot . '/mnet/lib.php');
        $env = new mnet_environment();
        $env->init();
        unset($env);
    }

    return (!empty($user->mnethostid) && $user->mnethostid != $CFG->mnet_localhost_id);
}

/**
 * This function will search for browser prefereed languages, setting Moodle
 * to use the best one available if $SESSION->lang is undefined
 */
function setup_lang_from_browser() {
    global $CFG, $SESSION, $USER;

    if (!empty($SESSION->lang) or !empty($USER->lang) or empty($CFG->autolang)) {
        // Lang is defined in session or user profile, nothing to do.
        return;
    }

    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { // There isn't list of browser langs, nothing to do.
        return;
    }

    // Extract and clean langs from headers.
    $rawlangs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $rawlangs = str_replace('-', '_', $rawlangs);         // We are using underscores.
    $rawlangs = explode(',', $rawlangs);                  // Convert to array.
    $langs = array();

    $order = 1.0;
    foreach ($rawlangs as $lang) {
        if (strpos($lang, ';') === false) {
            $langs[(string)$order] = $lang;
            $order = $order-0.01;
        } else {
            $parts = explode(';', $lang);
            $pos = strpos($parts[1], '=');
            $langs[substr($parts[1], $pos+1)] = $parts[0];
        }
    }
    krsort($langs, SORT_NUMERIC);

    // Look for such langs under standard locations.
    foreach ($langs as $lang) {
        // Clean it properly for include.
        $lang = strtolower(clean_param($lang, PARAM_SAFEDIR));
        if (get_string_manager()->translation_exists($lang, false)) {
            // Lang exists, set it in session.
            $SESSION->lang = $lang;
            // We have finished. Go out.
            break;
        }
    }
    return;
}

/**
 * Check if $url matches anything in proxybypass list
 *
 * Any errors just result in the proxy being used (least bad)
 *
 * @param string $url url to check
 * @return boolean true if we should bypass the proxy
 */
function is_proxybypass( $url ) {
    global $CFG;

    // Sanity check.
    if (empty($CFG->proxyhost) or empty($CFG->proxybypass)) {
        return false;
    }

    // Get the host part out of the url.
    if (!$host = parse_url( $url, PHP_URL_HOST )) {
        return false;
    }

    // Get the possible bypass hosts into an array.
    $matches = explode( ',', $CFG->proxybypass );

    // Check for a match.
    // (IPs need to match the left hand side and hosts the right of the url,
    // but we can recklessly check both as there can't be a false +ve).
    foreach ($matches as $match) {
        $match = trim($match);

        // Try for IP match (Left side).
        $lhs = substr($host, 0, strlen($match));
        if (strcasecmp($match, $lhs)==0) {
            return true;
        }

        // Try for host match (Right side).
        $rhs = substr($host, -strlen($match));
        if (strcasecmp($match, $rhs)==0) {
            return true;
        }
    }

    // Nothing matched.
    return false;
}

/**
 * Check if the passed navigation is of the new style
 *
 * @param mixed $navigation
 * @return bool true for yes false for no
 */
function is_newnav($navigation) {
    if (is_array($navigation) && !empty($navigation['newnav'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks whether the given variable name is defined as a variable within the given object.
 *
 * This will NOT work with stdClass objects, which have no class variables.
 *
 * @param string $var The variable name
 * @param object $object The object to check
 * @return boolean
 */
function in_object_vars($var, $object) {
    $classvars = get_class_vars(get_class($object));
    $classvars = array_keys($classvars);
    return in_array($var, $classvars);
}

/**
 * Returns an array without repeated objects.
 * This function is similar to array_unique, but for arrays that have objects as values
 *
 * @param array $array
 * @param bool $keepkeyassoc
 * @return array
 */
function object_array_unique($array, $keepkeyassoc = true) {
    $duplicatekeys = array();
    $tmp         = array();

    foreach ($array as $key => $val) {
        // Convert objects to arrays, in_array() does not support objects.
        if (is_object($val)) {
            $val = (array)$val;
        }

        if (!in_array($val, $tmp)) {
            $tmp[] = $val;
        } else {
            $duplicatekeys[] = $key;
        }
    }

    foreach ($duplicatekeys as $key) {
        unset($array[$key]);
    }

    return $keepkeyassoc ? $array : array_values($array);
}

/**
 * Is a userid the primary administrator?
 *
 * @param int $userid int id of user to check
 * @return boolean
 */
function is_primary_admin($userid) {
    $primaryadmin =  get_admin();

    if ($userid == $primaryadmin->id) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns the site identifier
 *
 * @return string $CFG->siteidentifier, first making sure it is properly initialised.
 */
function get_site_identifier() {
    global $CFG;
    // Check to see if it is missing. If so, initialise it.
    if (empty($CFG->siteidentifier)) {
        set_config('siteidentifier', random_string(32) . $_SERVER['HTTP_HOST']);
    }
    // Return it.
    return $CFG->siteidentifier;
}

/**
 * Check whether the given password has no more than the specified
 * number of consecutive identical characters.
 *
 * @param string $password   password to be checked against the password policy
 * @param integer $maxchars  maximum number of consecutive identical characters
 * @return bool
 */
function check_consecutive_identical_characters($password, $maxchars) {

    if ($maxchars < 1) {
        return true; // Zero 0 is to disable this check.
    }
    if (strlen($password) <= $maxchars) {
        return true; // Too short to fail this test.
    }

    $previouschar = '';
    $consecutivecount = 1;
    foreach (str_split($password) as $char) {
        if ($char != $previouschar) {
            $consecutivecount = 1;
        } else {
            $consecutivecount++;
            if ($consecutivecount > $maxchars) {
                return false; // Check failed already.
            }
        }

        $previouschar = $char;
    }

    return true;
}

/**
 * Helper function to do partial function binding.
 * so we can use it for preg_replace_callback, for example
 * this works with php functions, user functions, static methods and class methods
 * it returns you a callback that you can pass on like so:
 *
 * $callback = partial('somefunction', $arg1, $arg2);
 *     or
 * $callback = partial(array('someclass', 'somestaticmethod'), $arg1, $arg2);
 *     or even
 * $obj = new someclass();
 * $callback = partial(array($obj, 'somemethod'), $arg1, $arg2);
 *
 * and then the arguments that are passed through at calltime are appended to the argument list.
 *
 * @param mixed $function a php callback
 * @param mixed $arg1,... $argv arguments to partially bind with
 * @return array Array callback
 */
function partial() {
    if (!class_exists('partial')) {
        /**
         * Used to manage function binding.
         * @copyright  2009 Penny Leach
         * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
         */
        class partial{
            /** @var array */
            public $values = array();
            /** @var string The function to call as a callback. */
            public $func;
            /**
             * Constructor
             * @param string $func
             * @param array $args
             */
            public function __construct($func, $args) {
                $this->values = $args;
                $this->func = $func;
            }
            /**
             * Calls the callback function.
             * @return mixed
             */
            public function method() {
                $args = func_get_args();
                return call_user_func_array($this->func, array_merge($this->values, $args));
            }
        }
    }
    $args = func_get_args();
    $func = array_shift($args);
    $p = new partial($func, $args);
    return array($p, 'method');
}

/**
 * helper function to load up and initialise the mnet environment
 * this must be called before you use mnet functions.
 *
 * @return mnet_environment the equivalent of old $MNET global
 */
function get_mnet_environment() {
    global $CFG;
    require_once($CFG->dirroot . '/mnet/lib.php');
    static $instance = null;
    if (empty($instance)) {
        $instance = new mnet_environment();
        $instance->init();
    }
    return $instance;
}

/**
 * during xmlrpc server code execution, any code wishing to access
 * information about the remote peer must use this to get it.
 *
 * @return mnet_remote_client the equivalent of old $MNETREMOTE_CLIENT global
 */
function get_mnet_remote_client() {
    if (!defined('MNET_SERVER')) {
        debugging(get_string('notinxmlrpcserver', 'mnet'));
        return false;
    }
    global $MNET_REMOTE_CLIENT;
    if (isset($MNET_REMOTE_CLIENT)) {
        return $MNET_REMOTE_CLIENT;
    }
    return false;
}

/**
 * during the xmlrpc server code execution, this will be called
 * to setup the object returned by {@link get_mnet_remote_client}
 *
 * @param mnet_remote_client $client the client to set up
 * @throws moodle_exception
 */
function set_mnet_remote_client($client) {
    if (!defined('MNET_SERVER')) {
        throw new moodle_exception('notinxmlrpcserver', 'mnet');
    }
    global $MNET_REMOTE_CLIENT;
    $MNET_REMOTE_CLIENT = $client;
}

/**
 * return the jump url for a given remote user
 * this is used for rewriting forum post links in emails, etc
 *
 * @param stdclass $user the user to get the idp url for
 */
function mnet_get_idp_jump_url($user) {
    global $CFG;

    static $mnetjumps = array();
    if (!array_key_exists($user->mnethostid, $mnetjumps)) {
        $idp = mnet_get_peer_host($user->mnethostid);
        $idpjumppath = mnet_get_app_jumppath($idp->applicationid);
        $mnetjumps[$user->mnethostid] = $idp->wwwroot . $idpjumppath . '?hostwwwroot=' . $CFG->wwwroot . '&wantsurl=';
    }
    return $mnetjumps[$user->mnethostid];
}

/**
 * Gets the homepage to use for the current user
 *
 * @return int One of HOMEPAGE_*
 */
function get_home_page() {
    global $CFG;

    if (isloggedin() && !isguestuser() && !empty($CFG->defaulthomepage)) {
        // If dashboard is disabled, home will be set to default page.
        $defaultpage = get_default_home_page();
        if ($CFG->defaulthomepage == HOMEPAGE_MY) {
            if (!empty($CFG->enabledashboard)) {
                return HOMEPAGE_MY;
            } else {
                return $defaultpage;
            }
        } else if ($CFG->defaulthomepage == HOMEPAGE_MYCOURSES) {
            return HOMEPAGE_MYCOURSES;
        } else {
            $userhomepage = (int) get_user_preferences('user_home_page_preference', $defaultpage);
            if (empty($CFG->enabledashboard) && $userhomepage == HOMEPAGE_MY) {
                // If the user was using the dashboard but it's disabled, return the default home page.
                $userhomepage = $defaultpage;
            }
            return $userhomepage;
        }
    }
    return HOMEPAGE_SITE;
}

/**
 * Returns the default home page to display if current one is not defined or can't be applied.
 * The default behaviour is to return Dashboard if it's enabled or My courses page if it isn't.
 *
 * @return int The default home page.
 */
function get_default_home_page(): int {
    global $CFG;

    return !empty($CFG->enabledashboard) ? HOMEPAGE_MY : HOMEPAGE_MYCOURSES;
}

/**
 * Gets the name of a course to be displayed when showing a list of courses.
 * By default this is just $course->fullname but user can configure it. The
 * result of this function should be passed through print_string.
 * @param stdClass|core_course_list_element $course Moodle course object
 * @return string Display name of course (either fullname or short + fullname)
 */
function get_course_display_name_for_list($course) {
    global $CFG;
    if (!empty($CFG->courselistshortnames)) {
        if (!($course instanceof stdClass)) {
            $course = (object)convert_to_array($course);
        }
        return get_string('courseextendednamedisplay', '', $course);
    } else {
        return $course->fullname;
    }
}

/**
 * Safe analogue of unserialize() that can only parse arrays
 *
 * Arrays may contain only integers or strings as both keys and values. Nested arrays are allowed.
 * Note: If any string (key or value) has semicolon (;) as part of the string parsing will fail.
 * This is a simple method to substitute unnecessary unserialize() in code and not intended to cover all possible cases.
 *
 * @param string $expression
 * @return array|bool either parsed array or false if parsing was impossible.
 */
function unserialize_array($expression) {
    $subs = [];
    // Find nested arrays, parse them and store in $subs , substitute with special string.
    while (preg_match('/([\^;\}])(a:\d+:\{[^\{\}]*\})/', $expression, $matches) && strlen($matches[2]) < strlen($expression)) {
        $key = '--SUB' . count($subs) . '--';
        $subs[$key] = unserialize_array($matches[2]);
        if ($subs[$key] === false) {
            return false;
        }
        $expression = str_replace($matches[2], $key . ';', $expression);
    }

    // Check the expression is an array.
    if (!preg_match('/^a:(\d+):\{([^\}]*)\}$/', $expression, $matches1)) {
        return false;
    }
    // Get the size and elements of an array (key;value;key;value;....).
    $parts = explode(';', $matches1[2]);
    $size = intval($matches1[1]);
    if (count($parts) < $size * 2 + 1) {
        return false;
    }
    // Analyze each part and make sure it is an integer or string or a substitute.
    $value = [];
    for ($i = 0; $i < $size * 2; $i++) {
        if (preg_match('/^i:(\d+)$/', $parts[$i], $matches2)) {
            $parts[$i] = (int)$matches2[1];
        } else if (preg_match('/^s:(\d+):"(.*)"$/', $parts[$i], $matches3) && strlen($matches3[2]) == (int)$matches3[1]) {
            $parts[$i] = $matches3[2];
        } else if (preg_match('/^--SUB\d+--$/', $parts[$i])) {
            $parts[$i] = $subs[$parts[$i]];
        } else {
            return false;
        }
    }
    // Combine keys and values.
    for ($i = 0; $i < $size * 2; $i += 2) {
        $value[$parts[$i]] = $parts[$i+1];
    }
    return $value;
}

/**
 * Safe method for unserializing given input that is expected to contain only a serialized instance of an stdClass object
 *
 * If any class type other than stdClass is included in the input string, it will not be instantiated and will be cast to an
 * stdClass object. The initial cast to array, then back to object is to ensure we are always returning the correct type,
 * otherwise we would return an instances of {@see __PHP_Incomplete_class} for malformed strings
 *
 * @param string $input
 * @return stdClass
 */
function unserialize_object(string $input): stdClass {
    $instance = (array) unserialize($input, ['allowed_classes' => [stdClass::class]]);
    return (object) $instance;
}

/**
 * The lang_string class
 *
 * This special class is used to create an object representation of a string request.
 * It is special because processing doesn't occur until the object is first used.
 * The class was created especially to aid performance in areas where strings were
 * required to be generated but were not necessarily used.
 * As an example the admin tree when generated uses over 1500 strings, of which
 * normally only 1/3 are ever actually printed at any time.
 * The performance advantage is achieved by not actually processing strings that
 * arn't being used, as such reducing the processing required for the page.
 *
 * How to use the lang_string class?
 *     There are two methods of using the lang_string class, first through the
 *     forth argument of the get_string function, and secondly directly.
 *     The following are examples of both.
 * 1. Through get_string calls e.g.
 *     $string = get_string($identifier, $component, $a, true);
 *     $string = get_string('yes', 'moodle', null, true);
 * 2. Direct instantiation
 *     $string = new lang_string($identifier, $component, $a, $lang);
 *     $string = new lang_string('yes');
 *
 * How do I use a lang_string object?
 *     The lang_string object makes use of a magic __toString method so that you
 *     are able to use the object exactly as you would use a string in most cases.
 *     This means you are able to collect it into a variable and then directly
 *     echo it, or concatenate it into another string, or similar.
 *     The other thing you can do is manually get the string by calling the
 *     lang_strings out method e.g.
 *         $string = new lang_string('yes');
 *         $string->out();
 *     Also worth noting is that the out method can take one argument, $lang which
 *     allows the developer to change the language on the fly.
 *
 * When should I use a lang_string object?
 *     The lang_string object is designed to be used in any situation where a
 *     string may not be needed, but needs to be generated.
 *     The admin tree is a good example of where lang_string objects should be
 *     used.
 *     A more practical example would be any class that requries strings that may
 *     not be printed (after all classes get renderer by renderers and who knows
 *     what they will do ;))
 *
 * When should I not use a lang_string object?
 *     Don't use lang_strings when you are going to use a string immediately.
 *     There is no need as it will be processed immediately and there will be no
 *     advantage, and in fact perhaps a negative hit as a class has to be
 *     instantiated for a lang_string object, however get_string won't require
 *     that.
 *
 * Limitations:
 * 1. You cannot use a lang_string object as an array offset. Doing so will
 *     result in PHP throwing an error. (You can use it as an object property!)
 *
 * @package    core
 * @category   string
 * @copyright  2011 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang_string {

    /** @var string The strings identifier */
    protected $identifier;
    /** @var string The strings component. Default '' */
    protected $component = '';
    /** @var array|stdClass Any arguments required for the string. Default null */
    protected $a = null;
    /** @var string The language to use when processing the string. Default null */
    protected $lang = null;

    /** @var string The processed string (once processed) */
    protected $string = null;

    /**
     * A special boolean. If set to true then the object has been woken up and
     * cannot be regenerated. If this is set then $this->string MUST be used.
     * @var bool
     */
    protected $forcedstring = false;

    /**
     * Constructs a lang_string object
     *
     * This function should do as little processing as possible to ensure the best
     * performance for strings that won't be used.
     *
     * @param string $identifier The strings identifier
     * @param string $component The strings component
     * @param stdClass|array $a Any arguments the string requires
     * @param string $lang The language to use when processing the string.
     * @throws coding_exception
     */
    public function __construct($identifier, $component = '', $a = null, $lang = null) {
        if (empty($component)) {
            $component = 'moodle';
        }

        $this->identifier = $identifier;
        $this->component = $component;
        $this->lang = $lang;

        // We MUST duplicate $a to ensure that it if it changes by reference those
        // changes are not carried across.
        // To do this we always ensure $a or its properties/values are strings
        // and that any properties/values that arn't convertable are forgotten.
        if ($a !== null) {
            if (is_scalar($a)) {
                $this->a = $a;
            } else if ($a instanceof lang_string) {
                $this->a = $a->out();
            } else if (is_object($a) or is_array($a)) {
                $a = (array)$a;
                $this->a = array();
                foreach ($a as $key => $value) {
                    // Make sure conversion errors don't get displayed (results in '').
                    if (is_array($value)) {
                        $this->a[$key] = '';
                    } else if (is_object($value)) {
                        if (method_exists($value, '__toString')) {
                            $this->a[$key] = $value->__toString();
                        } else {
                            $this->a[$key] = '';
                        }
                    } else {
                        $this->a[$key] = (string)$value;
                    }
                }
            }
        }

        if (debugging(false, DEBUG_DEVELOPER)) {
            if (clean_param($this->identifier, PARAM_STRINGID) == '') {
                throw new coding_exception('Invalid string identifier. Most probably some illegal character is part of the string identifier. Please check your string definition');
            }
            if (!empty($this->component) && clean_param($this->component, PARAM_COMPONENT) == '') {
                throw new coding_exception('Invalid string compontent. Please check your string definition');
            }
            if (!get_string_manager()->string_exists($this->identifier, $this->component)) {
                debugging('String does not exist. Please check your string definition for '.$this->identifier.'/'.$this->component, DEBUG_DEVELOPER);
            }
        }
    }

    /**
     * Processes the string.
     *
     * This function actually processes the string, stores it in the string property
     * and then returns it.
     * You will notice that this function is VERY similar to the get_string method.
     * That is because it is pretty much doing the same thing.
     * However as this function is an upgrade it isn't as tolerant to backwards
     * compatibility.
     *
     * @return string
     * @throws coding_exception
     */
    protected function get_string() {
        global $CFG;

        // Check if we need to process the string.
        if ($this->string === null) {
            // Check the quality of the identifier.
            if ($CFG->debugdeveloper && clean_param($this->identifier, PARAM_STRINGID) === '') {
                throw new coding_exception('Invalid string identifier. Most probably some illegal character is part of the string identifier. Please check your string definition', DEBUG_DEVELOPER);
            }

            // Process the string.
            $this->string = get_string_manager()->get_string($this->identifier, $this->component, $this->a, $this->lang);
            // Debugging feature lets you display string identifier and component.
            if (isset($CFG->debugstringids) && $CFG->debugstringids && optional_param('strings', 0, PARAM_INT)) {
                $this->string .= ' {' . $this->identifier . '/' . $this->component . '}';
            }
        }
        // Return the string.
        return $this->string;
    }

    /**
     * Returns the string
     *
     * @param string $lang The langauge to use when processing the string
     * @return string
     */
    public function out($lang = null) {
        if ($lang !== null && $lang != $this->lang && ($this->lang == null && $lang != current_language())) {
            if ($this->forcedstring) {
                debugging('lang_string objects that have been used cannot be printed in another language. ('.$this->lang.' used)', DEBUG_DEVELOPER);
                return $this->get_string();
            }
            $translatedstring = new lang_string($this->identifier, $this->component, $this->a, $lang);
            return $translatedstring->out();
        }
        return $this->get_string();
    }

    /**
     * Magic __toString method for printing a string
     *
     * @return string
     */
    public function __toString() {
        return $this->get_string();
    }

    /**
     * Magic __set_state method used for var_export
     *
     * @param array $array
     * @return self
     */
    public static function __set_state(array $array): self {
        $tmp = new lang_string($array['identifier'], $array['component'], $array['a'], $array['lang']);
        $tmp->string = $array['string'];
        $tmp->forcedstring = $array['forcedstring'];
        return $tmp;
    }

    /**
     * Prepares the lang_string for sleep and stores only the forcedstring and
     * string properties... the string cannot be regenerated so we need to ensure
     * it is generated for this.
     *
     * @return string
     */
    public function __sleep() {
        $this->get_string();
        $this->forcedstring = true;
        return array('forcedstring', 'string', 'lang');
    }

    /**
     * Returns the identifier.
     *
     * @return string
     */
    public function get_identifier() {
        return $this->identifier;
    }

    /**
     * Returns the component.
     *
     * @return string
     */
    public function get_component() {
        return $this->component;
    }
}

/**
 * Get human readable name describing the given callable.
 *
 * This performs syntax check only to see if the given param looks like a valid function, method or closure.
 * It does not check if the callable actually exists.
 *
 * @param callable|string|array $callable
 * @return string|bool Human readable name of callable, or false if not a valid callable.
 */
function get_callable_name($callable) {

    if (!is_callable($callable, true, $name)) {
        return false;

    } else {
        return $name;
    }
}

/**
 * Tries to guess if $CFG->wwwroot is publicly accessible or not.
 * Never put your faith on this function and rely on its accuracy as there might be false positives.
 * It just performs some simple checks, and mainly is used for places where we want to hide some options
 * such as site registration when $CFG->wwwroot is not publicly accessible.
 * Good thing is there is no false negative.
 * Note that it's possible to force the result of this check by specifying $CFG->site_is_public in config.php
 *
 * @return bool
 */
function site_is_public() {
    global $CFG;

    // Return early if site admin has forced this setting.
    if (isset($CFG->site_is_public)) {
        return (bool)$CFG->site_is_public;
    }

    $host = parse_url($CFG->wwwroot, PHP_URL_HOST);

    if ($host === 'localhost' || preg_match('|^127\.\d+\.\d+\.\d+$|', $host)) {
        $ispublic = false;
    } else if (\core\ip_utils::is_ip_address($host) && !ip_is_public($host)) {
        $ispublic = false;
    } else if (($address = \core\ip_utils::get_ip_address($host)) && !ip_is_public($address)) {
        $ispublic = false;
    } else {
        $ispublic = true;
    }

    return $ispublic;
}
