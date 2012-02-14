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

/// CONSTANTS (Encased in phpdoc proper comments)/////////////////////////

/// Date and time constants ///
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

/// Parameter constants - every call to optional_param(), required_param()  ///
/// or clean_param() should have a specified type of parameter.  //////////////



/**
 * PARAM_ALPHA - contains only english ascii letters a-zA-Z.
 */
define('PARAM_ALPHA',    'alpha');

/**
 * PARAM_ALPHAEXT the same contents as PARAM_ALPHA plus the chars in quotes: "_-" allowed
 * NOTE: originally this allowed "/" too, please use PARAM_SAFEPATH if "/" needed
 */
define('PARAM_ALPHAEXT', 'alphaext');

/**
 * PARAM_ALPHANUM - expected numbers and letters only.
 */
define('PARAM_ALPHANUM', 'alphanum');

/**
 * PARAM_ALPHANUMEXT - expected numbers, letters only and _-.
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
 * PARAM_CLEANHTML - cleans submitted HTML code. use only for text in HTML format. This cleaning may fix xhtml strictness too.
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
 */
define('PARAM_FLOAT',  'float');

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
 * PARAM_LOCALURL - expected properly formatted URL as well as one that refers to the local server itself. (NOT orthogonal to the others! Implies PARAM_URL!)
 */
define('PARAM_LOCALURL', 'localurl');

/**
 * PARAM_NOTAGS - all html tags are stripped from the text. Do not abuse this type.
 */
define('PARAM_NOTAGS',   'notags');

/**
 * PARAM_PATH - safe relative path name, all dangerous chars are stripped, protects against XSS, SQL injections and directory traversals
 * note: the leading slash is not removed, window drive letter is not allowed
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
 * PARAM_RAW specifies a parameter that is not cleaned/processed in any way
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
 * PARAM_SAFEPATH - several PARAM_SAFEDIR joined by "/", suitable for include() and require(), plugin paths, etc.
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
 * PARAM_URL - expected properly formatted URL. Please note that domain part is required, http://localhost/ is not accepted but http://localhost.localdomain/ is ok.
 */
define('PARAM_URL',      'url');

/**
 * PARAM_USERNAME - Clean username to only contains allowed characters. This is to be used ONLY when manually creating user accounts, do NOT use when syncing with external systems!!
 */
define('PARAM_USERNAME',    'username');

/**
 * PARAM_STRINGID - used to check if the given string is valid string identifier for get_string()
 */
define('PARAM_STRINGID',    'stringid');

///// DEPRECATED PARAM TYPES OR ALIASES - DO NOT USE FOR NEW CODE  /////
/**
 * PARAM_CLEAN - obsoleted, please use a more specific type of parameter.
 * It was one of the first types, that is why it is abused so much ;-)
 * @deprecated since 2.0
 */
define('PARAM_CLEAN',    'clean');

/**
 * PARAM_INTEGER - deprecated alias for PARAM_INT
 */
define('PARAM_INTEGER',  'int');

/**
 * PARAM_NUMBER - deprecated alias of PARAM_FLOAT
 */
define('PARAM_NUMBER',  'float');

/**
 * PARAM_ACTION - deprecated alias for PARAM_ALPHANUMEXT, use for various actions in forms and urls
 * NOTE: originally alias for PARAM_APLHA
 */
define('PARAM_ACTION',   'alphanumext');

/**
 * PARAM_FORMAT - deprecated alias for PARAM_ALPHANUMEXT, use for names of plugins, formats, etc.
 * NOTE: originally alias for PARAM_APLHA
 */
define('PARAM_FORMAT',   'alphanumext');

/**
 * PARAM_MULTILANG - deprecated alias of PARAM_TEXT.
 */
define('PARAM_MULTILANG',  'text');

/**
 * PARAM_TIMEZONE - expected timezone. Timezone can be int +-(0-13) or float +-(0.5-12.5) or
 * string seperated by '/' and can have '-' &/ '_' (eg. America/North_Dakota/New_Salem
 * America/Port-au-Prince)
 */
define('PARAM_TIMEZONE', 'timezone');

/**
 * PARAM_CLEANFILE - deprecated alias of PARAM_FILE; originally was removing regional chars too
 */
define('PARAM_CLEANFILE', 'file');

/// Web Services ///

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

/// Page types ///
/**
 * PAGE_COURSE_VIEW is a definition of a page type. For more information on the page class see moodle/lib/pagelib.php.
 */
define('PAGE_COURSE_VIEW', 'course-view');

/** Get remote addr constant */
define('GETREMOTEADDR_SKIP_HTTP_CLIENT_IP', '1');
/** Get remote addr constant */
define('GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR', '2');

/// Blog access level constant declaration ///
define ('BLOG_USER_LEVEL', 1);
define ('BLOG_GROUP_LEVEL', 2);
define ('BLOG_COURSE_LEVEL', 3);
define ('BLOG_SITE_LEVEL', 4);
define ('BLOG_GLOBAL_LEVEL', 5);


///Tag constants///
/**
 * To prevent problems with multibytes strings,Flag updating in nav not working on the review page. this should not exceed the
 * length of "varchar(255) / 3 (bytes / utf-8 character) = 85".
 * TODO: this is not correct, varchar(255) are 255 unicode chars ;-)
 *
 * @todo define(TAG_MAX_LENGTH) this is not correct, varchar(255) are 255 unicode chars ;-)
 */
define('TAG_MAX_LENGTH', 50);

/// Password policy constants ///
define ('PASSWORD_LOWER', 'abcdefghijklmnopqrstuvwxyz');
define ('PASSWORD_UPPER', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
define ('PASSWORD_DIGITS', '0123456789');
define ('PASSWORD_NONALPHANUM', '.,;:!?_-+/*@#&$');

/// Feature constants ///
// Used for plugin_supports() to report features that are, or are not, supported by a module.

/** True if module can provide a grade */
define('FEATURE_GRADE_HAS_GRADE', 'grade_has_grade');
/** True if module supports outcomes */
define('FEATURE_GRADE_OUTCOMES', 'outcomes');

/** True if module has code to track whether somebody viewed it */
define('FEATURE_COMPLETION_TRACKS_VIEWS', 'completion_tracks_views');
/** True if module has custom completion rules */
define('FEATURE_COMPLETION_HAS_RULES', 'completion_has_rules');

/** True if module has no 'view' page (like label) */
define('FEATURE_NO_VIEW_LINK', 'viewlink');
/** True if module supports outcomes */
define('FEATURE_IDNUMBER', 'idnumber');
/** True if module supports groups */
define('FEATURE_GROUPS', 'groups');
/** True if module supports groupings */
define('FEATURE_GROUPINGS', 'groupings');
/** True if module supports groupmembersonly */
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

/** Unspecified module archetype */
define('MOD_ARCHETYPE_OTHER', 0);
/** Resource-like type module */
define('MOD_ARCHETYPE_RESOURCE', 1);
/** Assignment module archetype */
define('MOD_ARCHETYPE_ASSIGNMENT', 2);

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
 * Hub directory url (should be moodle.org)
 */
define('HUB_HUBDIRECTORYURL', "http://hubdirectory.moodle.org");


/**
 * Moodle.org url (should be moodle.org)
 */
define('HUB_MOODLEORGHUBURL', "http://hub.moodle.org");

/**
 * Moodle mobile app service name
 */
define('MOODLE_OFFICIAL_MOBILE_SERVICE', 'moodle_mobile_app');

/// PARAMETER HANDLING ////////////////////////////////////////////////////

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
 * Please note the $type parameter is now required,
 * for now PARAM_CLEAN is used for backwards compatibility only.
 *
 * @param string $parname the name of the page parameter we want
 * @param string $type expected type of parameter
 * @return mixed
 */
function required_param($parname, $type) {
    if (!isset($type)) {
        debugging('required_param() requires $type to be specified.');
        $type = PARAM_CLEAN; // for now let's use this deprecated type
    }
    if (isset($_POST[$parname])) {       // POST has precedence
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        print_error('missingparam', '', '', $parname);
    }

    return clean_param($param, $type);
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
 * Please note $default and $type parameters are now required,
 * for now PARAM_CLEAN is used for backwards compatibility only.
 *
 * @param string $parname the name of the page parameter we want
 * @param mixed  $default the default value to return if nothing is found
 * @param string $type expected type of parameter
 * @return mixed
 */
function optional_param($parname, $default, $type) {
    if (!isset($type)) {
        debugging('optional_param() requires $default and $type to be specified.');
        $type = PARAM_CLEAN; // for now let's use this deprecated type
    }
    if (!isset($default)) {
        $default = null;
    }

    if (isset($_POST[$parname])) {       // POST has precedence
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) {
        $param = $_GET[$parname];
    } else {
        return $default;
    }

    return clean_param($param, $type);
}

/**
 * Strict validation of parameter values, the values are only converted
 * to requested PHP type. Internally it is using clean_param, the values
 * before and after cleaning must be equal - otherwise
 * an invalid_parameter_exception is thrown.
 * Objects and classes are not accepted.
 *
 * @param mixed $param
 * @param int $type PARAM_ constant
 * @param bool $allownull are nulls valid value?
 * @param string $debuginfo optional debug information
 * @return mixed the $param value converted to PHP type or invalid_parameter_exception
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
    if ((string)$param !== (string)$cleaned) {
        // conversion to string is usually lossless
        throw new invalid_parameter_exception($debuginfo);
    }

    return $cleaned;
}

/**
 * Used by {@link optional_param()} and {@link required_param()} to
 * clean the variables and/or cast to specific types, based on
 * an options field.
 * <code>
 * $course->format = clean_param($course->format, PARAM_ALPHA);
 * $selectedgrade_item = clean_param($selectedgrade_item, PARAM_INT);
 * </code>
 *
 * @param mixed $param the variable we are cleaning
 * @param string $type expected format of param after cleaning.
 * @return mixed
 */
function clean_param($param, $type) {

    global $CFG;

    if (is_array($param)) {              // Let's loop
        $newparam = array();
        foreach ($param as $key => $value) {
            $newparam[$key] = clean_param($value, $type);
        }
        return $newparam;
    }

    switch ($type) {
        case PARAM_RAW:          // no cleaning at all
            return $param;

        case PARAM_RAW_TRIMMED:         // no cleaning, but strip leading and trailing whitespace.
            return trim($param);

        case PARAM_CLEAN:        // General HTML cleaning, try to use more specific type if possible
            // this is deprecated!, please use more specific type instead
            if (is_numeric($param)) {
                return $param;
            }
            return clean_text($param);     // Sweep for scripts, etc

        case PARAM_CLEANHTML:    // clean html fragment
            $param = clean_text($param, FORMAT_HTML);     // Sweep for scripts, etc
            return trim($param);

        case PARAM_INT:
            return (int)$param;  // Convert to integer

        case PARAM_FLOAT:
        case PARAM_NUMBER:
            return (float)$param;  // Convert to float

        case PARAM_ALPHA:        // Remove everything not a-z
            return preg_replace('/[^a-zA-Z]/i', '', $param);

        case PARAM_ALPHAEXT:     // Remove everything not a-zA-Z_- (originally allowed "/" too)
            return preg_replace('/[^a-zA-Z_-]/i', '', $param);

        case PARAM_ALPHANUM:     // Remove everything not a-zA-Z0-9
            return preg_replace('/[^A-Za-z0-9]/i', '', $param);

        case PARAM_ALPHANUMEXT:     // Remove everything not a-zA-Z0-9_-
            return preg_replace('/[^A-Za-z0-9_-]/i', '', $param);

        case PARAM_SEQUENCE:     // Remove everything not 0-9,
            return preg_replace('/[^0-9,]/i', '', $param);

        case PARAM_BOOL:         // Convert to 1 or 0
            $tempstr = strtolower($param);
            if ($tempstr === 'on' or $tempstr === 'yes' or $tempstr === 'true') {
                $param = 1;
            } else if ($tempstr === 'off' or $tempstr === 'no'  or $tempstr === 'false') {
                $param = 0;
            } else {
                $param = empty($param) ? 0 : 1;
            }
            return $param;

        case PARAM_NOTAGS:       // Strip all tags
            return strip_tags($param);

        case PARAM_TEXT:    // leave only tags needed for multilang
            // if the multilang syntax is not correct we strip all tags
            // because it would break xhtml strict which is required for accessibility standards
            // please note this cleaning does not strip unbalanced '>' for BC compatibility reasons
            do {
                if (strpos($param, '</lang>') !== false) {
                    // old and future mutilang syntax
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

                } else if (strpos($param, '</span>') !== false) {
                    // current problematic multilang syntax
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
            // easy, just strip all tags, if we ever want to fix orphaned '&' we have to do that in format_string()
            return strip_tags($param);

        case PARAM_SAFEDIR:      // Remove everything not a-zA-Z0-9_-
            return preg_replace('/[^a-zA-Z0-9_-]/i', '', $param);

        case PARAM_SAFEPATH:     // Remove everything not a-zA-Z0-9/_-
            return preg_replace('/[^a-zA-Z0-9\/_-]/i', '', $param);

        case PARAM_FILE:         // Strip all suspicious characters from filename
            $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':\\\\/]~u', '', $param);
            $param = preg_replace('~\.\.+~', '', $param);
            if ($param === '.') {
                $param = '';
            }
            return $param;

        case PARAM_PATH:         // Strip all suspicious characters from file path
            $param = str_replace('\\', '/', $param);
            $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':]~u', '', $param);
            $param = preg_replace('~\.\.+~', '', $param);
            $param = preg_replace('~//+~', '/', $param);
            return preg_replace('~/(\./)+~', '/', $param);

        case PARAM_HOST:         // allow FQDN or IPv4 dotted quad
            $param = preg_replace('/[^\.\d\w-]/','', $param ); // only allowed chars
            // match ipv4 dotted quad
            if (preg_match('/(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/',$param, $match)){
                // confirm values are ok
                if ( $match[0] > 255
                     || $match[1] > 255
                     || $match[3] > 255
                     || $match[4] > 255 ) {
                    // hmmm, what kind of dotted quad is this?
                    $param = '';
                }
            } elseif ( preg_match('/^[\w\d\.-]+$/', $param) // dots, hyphens, numbers
                       && !preg_match('/^[\.-]/',  $param) // no leading dots/hyphens
                       && !preg_match('/[\.-]$/',  $param) // no trailing dots/hyphens
                       ) {
                // all is ok - $param is respected
            } else {
                // all is not ok...
                $param='';
            }
            return $param;

        case PARAM_URL:          // allow safe ftp, http, mailto urls
            include_once($CFG->dirroot . '/lib/validateurlsyntax.php');
            if (!empty($param) && validateUrlSyntax($param, 's?H?S?F?E?u-P-a?I?p?f?q?r?')) {
                // all is ok, param is respected
            } else {
                $param =''; // not really ok
            }
            return $param;

        case PARAM_LOCALURL:     // allow http absolute, root relative and relative URLs within wwwroot
            $param = clean_param($param, PARAM_URL);
            if (!empty($param)) {
                if (preg_match(':^/:', $param)) {
                    // root-relative, ok!
                } elseif (preg_match('/^'.preg_quote($CFG->wwwroot, '/').'/i',$param)) {
                    // absolute, and matches our wwwroot
                } else {
                    // relative - let's make sure there are no tricks
                    if (validateUrlSyntax('/' . $param, 's-u-P-a-p-f+q?r?')) {
                        // looks ok.
                    } else {
                        $param = '';
                    }
                }
            }
            return $param;

        case PARAM_PEM:
            $param = trim($param);
            // PEM formatted strings may contain letters/numbers and the symbols
            // forward slash: /
            // plus sign:     +
            // equal sign:    =
            // , surrounded by BEGIN and END CERTIFICATE prefix and suffixes
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
                // forward slash: /
                // plus sign:     +
                // equal sign:    =
                if (0 >= preg_match('/^([\s\w\/\+=]+)$/', trim($param))) {
                    return '';
                }
                $lines = preg_split('/[\s]+/', $param, -1, PREG_SPLIT_NO_EMPTY);
                // Each line of base64 encoded data must be 64 characters in
                // length, except for the last line which may be less than (or
                // equal to) 64 characters long.
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
                return implode("\n",$lines);
            } else {
                return '';
            }

        case PARAM_TAG:
            // Please note it is not safe to use the tag name directly anywhere,
            // it must be processed with s(), urlencode() before embedding anywhere.
            // remove some nasties
            $param = preg_replace('~[[:cntrl:]]|[<>`]~u', '', $param);
            //convert many whitespace chars into one
            $param = preg_replace('/\s+/', ' ', $param);
            $textlib = textlib_get_instance();
            $param = $textlib->substr(trim($param), 0, TAG_MAX_LENGTH);
            return $param;

        case PARAM_TAGLIST:
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
            $param = clean_param($param, PARAM_SAFEDIR);
            if (exists_auth_plugin($param)) {
                return $param;
            } else {
                return '';
            }

        case PARAM_LANG:
            $param = clean_param($param, PARAM_SAFEDIR);
            if (get_string_manager()->translation_exists($param)) {
                return $param;
            } else {
                return ''; // Specified language is not installed or param malformed
            }

        case PARAM_THEME:
            $param = clean_param($param, PARAM_SAFEDIR);
            if (file_exists("$CFG->dirroot/theme/$param/config.php")) {
                return $param;
            } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$param/config.php")) {
                return $param;
            } else {
                return '';  // Specified theme is not installed
            }

        case PARAM_USERNAME:
            $param = str_replace(" " , "", $param);
            $param = moodle_strtolower($param);  // Convert uppercase to lowercase MDL-16919
            if (empty($CFG->extendedusernamechars)) {
                // regular expression, eliminate all chars EXCEPT:
                // alphanum, dash (-), underscore (_), at sign (@) and period (.) characters.
                $param = preg_replace('/[^-\.@_a-z0-9]/', '', $param);
            }
            return $param;

        case PARAM_EMAIL:
            if (validate_email($param)) {
                return $param;
            } else {
                return '';
            }

        case PARAM_STRINGID:
            if (preg_match('|^[a-zA-Z][a-zA-Z0-9\.:/_-]*$|', $param)) {
                return $param;
            } else {
                return '';
            }

        case PARAM_TIMEZONE:    //can be int, float(with .5 or .0) or string seperated by '/' and can have '-_'
            $timezonepattern = '/^(([+-]?(0?[0-9](\.[5|0])?|1[0-3]|1[0-2]\.5))|(99)|[[:alnum:]]+(\/?[[:alpha:]_-])+)$/';
            if (preg_match($timezonepattern, $param)) {
                return $param;
            } else {
                return '';
            }

        default:                 // throw error, switched parameters in optional_param or another serious problem
            print_error("unknownparamtype", '', '', $type);
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
 * Returns host part from url
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
    return trim(strip_tags($string, '<img><object><applet><input><select><textarea><hr>')) == '';
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
 * @global object
 * @global object
 * @param string $name the key to set
 * @param string $value the value to set (without magic quotes)
 * @param string $plugin (optional) the plugin scope, default NULL
 * @return bool true or exception
 */
function set_config($name, $value, $plugin=NULL) {
    global $CFG, $DB;

    if (empty($plugin)) {
        if (!array_key_exists($name, $CFG->config_php_settings)) {
            // So it's defined for this invocation at least
            if (is_null($value)) {
                unset($CFG->$name);
            } else {
                $CFG->$name = (string)$value; // settings from db are always strings
            }
        }

        if ($DB->get_field('config', 'name', array('name'=>$name))) {
            if ($value === null) {
                $DB->delete_records('config', array('name'=>$name));
            } else {
                $DB->set_field('config', 'value', $value, array('name'=>$name));
            }
        } else {
            if ($value !== null) {
                $config = new stdClass();
                $config->name  = $name;
                $config->value = $value;
                $DB->insert_record('config', $config, false);
            }
        }

    } else { // plugin scope
        if ($id = $DB->get_field('config_plugins', 'id', array('name'=>$name, 'plugin'=>$plugin))) {
            if ($value===null) {
                $DB->delete_records('config_plugins', array('name'=>$name, 'plugin'=>$plugin));
            } else {
                $DB->set_field('config_plugins', 'value', $value, array('id'=>$id));
            }
        } else {
            if ($value !== null) {
                $config = new stdClass();
                $config->plugin = $plugin;
                $config->name   = $name;
                $config->value  = $value;
                $DB->insert_record('config_plugins', $config, false);
            }
        }
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
 * @param string $plugin full component name
 * @param string $name default NULL
 * @return mixed hash-like object or single value, return false no config found
 */
function get_config($plugin, $name = NULL) {
    global $CFG, $DB;

    // normalise component name
    if ($plugin === 'moodle' or $plugin === 'core') {
        $plugin = NULL;
    }

    if (!empty($name)) { // the user is asking for a specific value
        if (!empty($plugin)) {
            if (isset($CFG->forced_plugin_settings[$plugin]) and array_key_exists($name, $CFG->forced_plugin_settings[$plugin])) {
                // setting forced in config file
                return $CFG->forced_plugin_settings[$plugin][$name];
            } else {
                return $DB->get_field('config_plugins', 'value', array('plugin'=>$plugin, 'name'=>$name));
            }
        } else {
            if (array_key_exists($name, $CFG->config_php_settings)) {
                // setting force in config file
                return $CFG->config_php_settings[$name];
            } else {
                return $DB->get_field('config', 'value', array('name'=>$name));
            }
        }
    }

    // the user is after a recordset
    if ($plugin) {
        $localcfg = $DB->get_records_menu('config_plugins', array('plugin'=>$plugin), '', 'name,value');
        if (isset($CFG->forced_plugin_settings[$plugin])) {
            foreach($CFG->forced_plugin_settings[$plugin] as $n=>$v) {
                if (is_null($v) or is_array($v) or is_object($v)) {
                    // we do not want any extra mess here, just real settings that could be saved in db
                    unset($localcfg[$n]);
                } else {
                    //convert to string as if it went through the DB
                    $localcfg[$n] = (string)$v;
                }
            }
        }
        if ($localcfg) {
            return (object)$localcfg;
        } else {
            return new stdClass();
        }

    } else {
        // this part is not really used any more, but anyway...
        $localcfg = $DB->get_records_menu('config', array(), '', 'name,value');
        foreach($CFG->config_php_settings as $n=>$v) {
            if (is_null($v) or is_array($v) or is_object($v)) {
                // we do not want any extra mess here, just real settings that could be saved in db
                unset($localcfg[$n]);
            } else {
                //convert to string as if it went through the DB
                $localcfg[$n] = (string)$v;
            }
        }
        return (object)$localcfg;
    }
}

/**
 * Removes a key from global configuration
 *
 * @param string $name the key to set
 * @param string $plugin (optional) the plugin scope
 * @global object
 * @return boolean whether the operation succeeded.
 */
function unset_config($name, $plugin=NULL) {
    global $CFG, $DB;

    if (empty($plugin)) {
        unset($CFG->$name);
        $DB->delete_records('config', array('name'=>$name));
    } else {
        $DB->delete_records('config_plugins', array('name'=>$name, 'plugin'=>$plugin));
    }

    return true;
}

/**
 * Remove all the config variables for a given plugin.
 *
 * @param string $plugin a plugin, for example 'quiz' or 'qtype_multichoice';
 * @return boolean whether the operation succeeded.
 */
function unset_all_config_for_plugin($plugin) {
    global $DB;
    $DB->delete_records('config_plugins', array('plugin' => $plugin));
    $like = $DB->sql_like('name', '?', true, true, false, '|');
    $params = array($DB->sql_like_escape($plugin.'_', '|') . '%');
    $DB->delete_records_select('config', $like, $params);
    return true;
}

/**
 * Use this function to get a list of users from a config setting of type admin_setting_users_with_capability.
 *
 * All users are verified if they still have the necessary capability.
 *
 * @param string $value the value of the config setting.
 * @param string $capability the capability - must match the one passed to the admin_setting_users_with_capability constructor.
 * @param bool $include admins, include administrators
 * @return array of user objects.
 */
function get_users_from_config($value, $capability, $includeadmins = true) {
    global $CFG, $DB;

    if (empty($value) or $value === '$@NONE@$') {
        return array();
    }

    // we have to make sure that users still have the necessary capability,
    // it should be faster to fetch them all first and then test if they are present
    // instead of validating them one-by-one
    $users = get_users_by_capability(get_context_instance(CONTEXT_SYSTEM), $capability);
    if ($includeadmins) {
        $admins = get_admins();
        foreach ($admins as $admin) {
            $users[$admin->id] = $admin;
        }
    }

    if ($value === '$@ALL@$') {
        return $users;
    }

    $result = array(); // result in correct order
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
 * Invalidates browser caches and cached data in temp
 * @return void
 */
function purge_all_caches() {
    global $CFG;

    reset_text_filters_cache();
    js_reset_all_caches();
    theme_reset_all_caches();
    get_string_manager()->reset_caches();

    // purge all other caches: rss, simplepie, etc.
    remove_dir($CFG->dataroot.'/cache', true);

    // make sure cache dir is writable, throws exception if not
    make_upload_directory('cache');

    // hack: this script may get called after the purifier was initialised,
    // but we do not want to verify repeatedly this exists in each call
    make_upload_directory('cache/htmlpurifier');

    clearstatcache();
}

/**
 * Get volatile flags
 *
 * @param string $type
 * @param int    $changedsince default null
 * @return records array
 */
function get_cache_flags($type, $changedsince=NULL) {
    global $DB;

    $params = array('type'=>$type, 'expiry'=>time());
    $sqlwhere = "flagtype = :type AND expiry >= :expiry";
    if ($changedsince !== NULL) {
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
 * @param int    $changedsince default null
 * @return records array
 */
function get_cache_flag($type, $name, $changedsince=NULL) {
    global $DB;

    $params = array('type'=>$type, 'name'=>$name, 'expiry'=>time());

    $sqlwhere = "flagtype = :type AND name = :name AND expiry >= :expiry";
    if ($changedsince !== NULL) {
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
 * @param string $value the value to set (without magic quotes) - NULL will remove the flag
 * @param int $expiry (optional) epoch indicating expiry - defaults to now()+ 24hs
 * @return bool Always returns true
 */
function set_cache_flag($type, $name, $value, $expiry=NULL) {
    global $DB;

    $timemodified = time();
    if ($expiry===NULL || $expiry < $timemodified) {
        $expiry = $timemodified + 24 * 60 * 60;
    } else {
        $expiry = (int)$expiry;
    }

    if ($value === NULL) {
        unset_cache_flag($type,$name);
        return true;
    }

    if ($f = $DB->get_record('cache_flags', array('name'=>$name, 'flagtype'=>$type), '*', IGNORE_MULTIPLE)) { // this is a potential problem in DEBUG_DEVELOPER
        if ($f->value == $value and $f->expiry == $expiry and $f->timemodified == $timemodified) {
            return true; //no need to update; helps rcache too
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
 * @global object
 * @param string $type the "type" namespace for the key
 * @param string $name the key to set
 * @return bool
 */
function unset_cache_flag($type, $name) {
    global $DB;
    $DB->delete_records('cache_flags', array('name'=>$name, 'flagtype'=>$type));
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

/// FUNCTIONS FOR HANDLING USER PREFERENCES ////////////////////////////////////

/**
 * Refresh user preference cache. This is used most often for $USER
 * object that is stored in session, but it also helps with performance in cron script.
 *
 * Preferences for each user are loaded on first use on every page, then again after the timeout expires.
 *
 * @param stdClass $user user object, preferences are preloaded into ->preference property
 * @param int $cachelifetime cache life time on the current page (ins seconds)
 * @return void
 */
function check_user_preferences_loaded(stdClass $user, $cachelifetime = 120) {
    global $DB;
    static $loadedusers = array(); // Static cache, we need to check on each page load, not only every 2 minutes.

    if (!isset($user->id)) {
        throw new coding_exception('Invalid $user parameter in check_user_preferences_loaded() call, missing id field');
    }

    if (empty($user->id) or isguestuser($user->id)) {
        // No permanent storage for not-logged-in users and guest
        if (!isset($user->preference)) {
            $user->preference = array();
        }
        return;
    }

    $timenow = time();

    if (isset($loadedusers[$user->id]) and isset($user->preference) and isset($user->preference['_lastloaded'])) {
        // Already loaded at least once on this page. Are we up to date?
        if ($user->preference['_lastloaded'] + $cachelifetime > $timenow) {
            // no need to reload - we are on the same page and we loaded prefs just a moment ago
            return;

        } else if (!get_cache_flag('userpreferenceschanged', $user->id, $user->preference['_lastloaded'])) {
            // no change since the lastcheck on this page
            $user->preference['_lastloaded'] = $timenow;
            return;
        }
    }

    // OK, so we have to reload all preferences
    $loadedusers[$user->id] = true;
    $user->preference = $DB->get_records_menu('user_preferences', array('userid'=>$user->id), '', 'name,value'); // All values
    $user->preference['_lastloaded'] = $timenow;
}

/**
 * Called from set/delete_user_preferences, so that the prefs can
 * be correctly reloaded in different sessions.
 *
 * NOTE: internal function, do not call from other code.
 *
 * @param integer $userid the user whose prefs were changed.
 * @return void
 */
function mark_user_preferences_changed($userid) {
    global $CFG;

    if (empty($userid) or isguestuser($userid)) {
        // no cache flags for guest and not-logged-in users
        return;
    }

    set_cache_flag('userpreferenceschanged', $userid, 1, time() + $CFG->sessiontimeout);
}

/**
 * Sets a preference for the specified user.
 *
 * If user object submitted, 'preference' property contains the preferences cache.
 *
 * @param string $name The key to set as preference for the specified user
 * @param string $value The value to set for the $name key in the specified user's record,
 *                      null means delete current value
 * @param stdClass|int $user A moodle user object or id, null means current user
 * @return bool always true or exception
 */
function set_user_preference($name, $value, $user = null) {
    global $USER, $DB;

    if (empty($name) or is_numeric($name) or $name === '_lastloaded') {
        throw new coding_exception('Invalid preference name in set_user_preference() call');
    }

    if (is_null($value)) {
        // null means delete current
        return unset_user_preference($name, $user);
    } else if (is_object($value)) {
        throw new coding_exception('Invalid value in set_user_preference() call, objects are not allowed');
    } else if (is_array($value)) {
        throw new coding_exception('Invalid value in set_user_preference() call, arrays are not allowed');
    }
    $value = (string)$value;

    if (is_null($user)) {
        $user = $USER;
    } else if (isset($user->id)) {
        // $user is valid object
    } else if (is_numeric($user)) {
        $user = (object)array('id'=>(int)$user);
    } else {
        throw new coding_exception('Invalid $user parameter in set_user_preference() call');
    }

    check_user_preferences_loaded($user);

    if (empty($user->id) or isguestuser($user->id)) {
        // no permanent storage for not-logged-in users and guest
        $user->preference[$name] = $value;
        return true;
    }

    if ($preference = $DB->get_record('user_preferences', array('userid'=>$user->id, 'name'=>$name))) {
        if ($preference->value === $value and isset($user->preference[$name]) and $user->preference[$name] === $value) {
            // preference already set to this value
            return true;
        }
        $DB->set_field('user_preferences', 'value', $value, array('id'=>$preference->id));

    } else {
        $preference = new stdClass();
        $preference->userid = $user->id;
        $preference->name   = $name;
        $preference->value  = $value;
        $DB->insert_record('user_preferences', $preference);
    }

    // update value in cache
    $user->preference[$name] = $value;

    // set reload flag for other sessions
    mark_user_preferences_changed($user->id);

    return true;
}

/**
 * Sets a whole array of preferences for the current user
 *
 * If user object submitted, 'preference' property contains the preferences cache.
 *
 * @param array $prefarray An array of key/value pairs to be set
 * @param stdClass|int $user A moodle user object or id, null means current user
 * @return bool always true or exception
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
 * If user object submitted, 'preference' property contains the preferences cache.
 *
 * @param string  $name The key to unset as preference for the specified user
 * @param stdClass|int $user A moodle user object or id, null means current user
 * @return bool always true or exception
 */
function unset_user_preference($name, $user = null) {
    global $USER, $DB;

    if (empty($name) or is_numeric($name) or $name === '_lastloaded') {
        throw new coding_exception('Invalid preference name in unset_user_preference() call');
    }

    if (is_null($user)) {
        $user = $USER;
    } else if (isset($user->id)) {
        // $user is valid object
    } else if (is_numeric($user)) {
        $user = (object)array('id'=>(int)$user);
    } else {
        throw new coding_exception('Invalid $user parameter in unset_user_preference() call');
    }

    check_user_preferences_loaded($user);

    if (empty($user->id) or isguestuser($user->id)) {
        // no permanent storage for not-logged-in user and guest
        unset($user->preference[$name]);
        return true;
    }

    // delete from DB
    $DB->delete_records('user_preferences', array('userid'=>$user->id, 'name'=>$name));

    // delete the preference from cache
    unset($user->preference[$name]);

    // set reload flag for other sessions
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
 * otherwise NULL.
 *
 * If user object submitted, 'preference' property contains the preferences cache.
 *
 * @param string $name Name of the key to use in finding a preference value
 * @param mixed $default Value to be returned if the $name key is not set in the user preferences
 * @param stdClass|int $user A moodle user object or id, null means current user
 * @return mixed string value or default
 */
function get_user_preferences($name = null, $default = null, $user = null) {
    global $USER;

    if (is_null($name)) {
        // all prefs
    } else if (is_numeric($name) or $name === '_lastloaded') {
        throw new coding_exception('Invalid preference name in get_user_preferences() call');
    }

    if (is_null($user)) {
        $user = $USER;
    } else if (isset($user->id)) {
        // $user is valid object
    } else if (is_numeric($user)) {
        $user = (object)array('id'=>(int)$user);
    } else {
        throw new coding_exception('Invalid $user parameter in get_user_preferences() call');
    }

    check_user_preferences_loaded($user);

    if (empty($name)) {
        return $user->preference; // All values
    } else if (isset($user->preference[$name])) {
        return $user->preference[$name]; // The single string value
    } else {
        return $default; // Default value (null if not specified)
    }
}

/// FUNCTIONS FOR HANDLING TIME ////////////////////////////////////////////

/**
 * Given date parts in user time produce a GMT timestamp.
 *
 * @todo Finish documenting this function
 * @param int $year The year part to create timestamp of
 * @param int $month The month part to create timestamp of
 * @param int $day The day part to create timestamp of
 * @param int $hour The hour part to create timestamp of
 * @param int $minute The minute part to create timestamp of
 * @param int $second The second part to create timestamp of
 * @param mixed $timezone Timezone modifier, if 99 then use default user's timezone
 * @param bool $applydst Toggle Daylight Saving Time, default true, will be
 *             applied only if timezone is 99 or string.
 * @return int timestamp
 */
function make_timestamp($year, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timezone=99, $applydst=true) {

    //save input timezone, required for dst offset check.
    $passedtimezone = $timezone;

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {  //server time
        $time = mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
    } else {
        $time = gmmktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
        $time = usertime($time, $timezone);

        //Apply dst for string timezones or if 99 then try dst offset with user's default timezone
        if ($applydst && ((99 == $passedtimezone) || !is_numeric($passedtimezone))) {
            $time -= dst_offset_on($time, $passedtimezone);
        }
    }

    return $time;

}

/**
 * Format a date/time (seconds) as weeks, days, hours etc as needed
 *
 * Given an amount of time in seconds, returns string
 * formatted nicely as weeks, days, hours etc as needed
 *
 * @uses MINSECS
 * @uses HOURSECS
 * @uses DAYSECS
 * @uses YEARSECS
 * @param int $totalsecs Time in seconds
 * @param object $str Should be a time object
 * @return string A nicely formatted date/time string
 */
 function format_time($totalsecs, $str=NULL) {

    $totalsecs = abs($totalsecs);

    if (!$str) {  // Create the str structure the slow way
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

    if ($years)  $oyears  = $years .' '. $sy;
    if ($days)  $odays  = $days .' '. $sd;
    if ($hours) $ohours = $hours .' '. $sh;
    if ($mins)  $omins  = $mins .' '. $sm;
    if ($secs)  $osecs  = $secs .' '. $ss;

    if ($years) return trim($oyears .' '. $odays);
    if ($days)  return trim($odays .' '. $ohours);
    if ($hours) return trim($ohours .' '. $omins);
    if ($mins)  return trim($omins .' '. $osecs);
    if ($secs)  return $osecs;
    return get_string('now');
}

/**
 * Returns a formatted string that represents a date in user time
 *
 * Returns a formatted string that represents a date in user time
 * <b>WARNING: note that the format is for strftime(), not date().</b>
 * Because of a bug in most Windows time libraries, we can't use
 * the nicer %e, so we have to use %d which has leading zeroes.
 * A lot of the fuss in the function is just getting rid of these leading
 * zeroes as efficiently as possible.
 *
 * If parameter fixday = true (default), then take off leading
 * zero from %d, else maintain it.
 *
 * @param int $date the timestamp in UTC, as obtained from the database.
 * @param string $format strftime format. You should probably get this using
 *      get_string('strftime...', 'langconfig');
 * @param mixed $timezone by default, uses the user's time zone. if numeric and
 *      not 99 then daylight saving will not be added.
 * @param bool $fixday If true (default) then the leading zero from %d is removed.
 *      If false then the leading zero is maintained.
 * @return string the formatted date/time.
 */
function userdate($date, $format = '', $timezone = 99, $fixday = true) {

    global $CFG;

    if (empty($format)) {
        $format = get_string('strftimedaydatetime', 'langconfig');
    }

    if (!empty($CFG->nofixday)) {  // Config.php can force %d not to be fixed.
        $fixday = false;
    } else if ($fixday) {
        $formatnoday = str_replace('%d', 'DD', $format);
        $fixday = ($formatnoday != $format);
    }

    //add daylight saving offset for string timezones only, as we can't get dst for
    //float values. if timezone is 99 (user default timezone), then try update dst.
    if ((99 == $timezone) || !is_numeric($timezone)) {
        $date += dst_offset_on($date, $timezone);
    }

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {   /// Server time
        if ($fixday) {
            $datestring = strftime($formatnoday, $date);
            $daystring  = ltrim(str_replace(array(' 0', ' '), '', strftime(' %d', $date)));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
            $datestring = strftime($format, $date);
        }
    } else {
        $date += (int)($timezone * 3600);
        if ($fixday) {
            $datestring = gmstrftime($formatnoday, $date);
            $daystring  = ltrim(str_replace(array(' 0', ' '), '', gmstrftime(' %d', $date)));
            $datestring = str_replace('DD', $daystring, $datestring);
        } else {
            $datestring = gmstrftime($format, $date);
        }
    }

/// If we are running under Windows convert from windows encoding to UTF-8
/// (because it's impossible to specify UTF-8 to fetch locale info in Win32)

   if ($CFG->ostype == 'WINDOWS') {
       if ($localewincharset = get_string('localewincharset', 'langconfig')) {
           $textlib = textlib_get_instance();
           $datestring = $textlib->convert($datestring, $localewincharset, 'utf-8');
       }
   }

    return $datestring;
}

/**
 * Given a $time timestamp in GMT (seconds since epoch),
 * returns an array that represents the date in user time
 *
 * @todo Finish documenting this function
 * @uses HOURSECS
 * @param int $time Timestamp in GMT
 * @param mixed $timezone offset time with timezone, if float and not 99, then no
 *        dst offset is applyed
 * @return array An array that represents the date in user time
 */
function usergetdate($time, $timezone=99) {

    //save input timezone, required for dst offset check.
    $passedtimezone = $timezone;

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {    // Server time
        return getdate($time);
    }

    //add daylight saving offset for string timezones only, as we can't get dst for
    //float values. if timezone is 99 (user default timezone), then try update dst.
    if ($passedtimezone == 99 || !is_numeric($passedtimezone)) {
        $time += dst_offset_on($time, $passedtimezone);
    }

    $time += intval((float)$timezone * HOURSECS);

    $datestring = gmstrftime('%B_%A_%j_%Y_%m_%w_%d_%H_%M_%S', $time);

    //be careful to ensure the returned array matches that produced by getdate() above
    list(
        $getdate['month'],
        $getdate['weekday'],
        $getdate['yday'],
        $getdate['year'],
        $getdate['mon'],
        $getdate['wday'],
        $getdate['mday'],
        $getdate['hours'],
        $getdate['minutes'],
        $getdate['seconds']
    ) = explode('_', $datestring);

    return $getdate;
}

/**
 * Given a GMT timestamp (seconds since epoch), offsets it by
 * the timezone.  eg 3pm in India is 3pm GMT - 7 * 3600 seconds
 *
 * @uses HOURSECS
 * @param  int $date Timestamp in GMT
 * @param float $timezone
 * @return int
 */
function usertime($date, $timezone=99) {

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {
        return $date;
    }
    return $date - (int)($timezone * HOURSECS);
}

/**
 * Given a time, return the GMT timestamp of the most recent midnight
 * for the current user.
 *
 * @param int $date Timestamp in GMT
 * @param float $timezone Defaults to user's timezone
 * @return int Returns a GMT timestamp
 */
function usergetmidnight($date, $timezone=99) {

    $userdate = usergetdate($date, $timezone);

    // Time of midnight of this user's day, in GMT
    return make_timestamp($userdate['year'], $userdate['mon'], $userdate['mday'], 0, 0, 0, $timezone);

}

/**
 * Returns a string that prints the user's timezone
 *
 * @param float $timezone The user's timezone
 * @return string
 */
function usertimezone($timezone=99) {

    $tz = get_user_timezone($timezone);

    if (!is_float($tz)) {
        return $tz;
    }

    if(abs($tz) > 13) { // Server time
        return get_string('serverlocaltime');
    }

    if($tz == intval($tz)) {
        // Don't show .0 for whole hours
        $tz = intval($tz);
    }

    if($tz == 0) {
        return 'UTC';
    }
    else if($tz > 0) {
        return 'UTC+'.$tz;
    }
    else {
        return 'UTC'.$tz;
    }

}

/**
 * Returns a float which represents the user's timezone difference from GMT in hours
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @global object
 * @global object
 * @param float $tz If this value is provided and not equal to 99, it will be returned as is and no other settings will be checked
 * @return float
 */
function get_user_timezone_offset($tz = 99) {

    global $USER, $CFG;

    $tz = get_user_timezone($tz);

    if (is_float($tz)) {
        return $tz;
    } else {
        $tzrecord = get_timezone_record($tz);
        if (empty($tzrecord)) {
            return 99.0;
        }
        return (float)$tzrecord->gmtoff / HOURMINS;
    }
}

/**
 * Returns an int which represents the systems's timezone difference from GMT in seconds
 *
 * @global object
 * @param mixed $tz timezone
 * @return int if found, false is timezone 99 or error
 */
function get_timezone_offset($tz) {
    global $CFG;

    if ($tz == 99) {
        return false;
    }

    if (is_numeric($tz)) {
        return intval($tz * 60*60);
    }

    if (!$tzrecord = get_timezone_record($tz)) {
        return false;
    }
    return intval($tzrecord->gmtoff * 60);
}

/**
 * Returns a float or a string which denotes the user's timezone
 * A float value means that a simple offset from GMT is used, while a string (it will be the name of a timezone in the database)
 * means that for this timezone there are also DST rules to be taken into account
 * Checks various settings and picks the most dominant of those which have a value
 *
 * @global object
 * @global object
 * @param mixed $tz If this value is provided and not equal to 99, it will be returned as is and no other settings will be checked
 * @return mixed
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

    while(($tz == '' || $tz == 99 || $tz == NULL) && $next = each($timezones)) {
        $tz = $next['value'];
    }

    return is_numeric($tz) ? (float) $tz : $tz;
}

/**
 * Returns cached timezone record for given $timezonename
 *
 * @global object
 * @global object
 * @param string $timezonename
 * @return mixed timezonerecord object or false
 */
function get_timezone_record($timezonename) {
    global $CFG, $DB;
    static $cache = NULL;

    if ($cache === NULL) {
        $cache = array();
    }

    if (isset($cache[$timezonename])) {
        return $cache[$timezonename];
    }

    return $cache[$timezonename] = $DB->get_record_sql('SELECT * FROM {timezone}
                                                        WHERE name = ? ORDER BY year DESC', array($timezonename), IGNORE_MULTIPLE);
}

/**
 * Build and store the users Daylight Saving Time (DST) table
 *
 * @global object
 * @global object
 * @global object
 * @param mixed $from_year Start year for the table, defaults to 1971
 * @param mixed $to_year End year for the table, defaults to 2035
 * @param mixed $strtimezone, if null or 99 then user's default timezone is used
 * @return bool
 */
function calculate_user_dst_table($from_year = NULL, $to_year = NULL, $strtimezone = NULL) {
    global $CFG, $SESSION, $DB;

    $usertz = get_user_timezone($strtimezone);

    if (is_float($usertz)) {
        // Trivial timezone, no DST
        return false;
    }

    if (!empty($SESSION->dst_offsettz) && $SESSION->dst_offsettz != $usertz) {
        // We have precalculated values, but the user's effective TZ has changed in the meantime, so reset
        unset($SESSION->dst_offsets);
        unset($SESSION->dst_range);
    }

    if (!empty($SESSION->dst_offsets) && empty($from_year) && empty($to_year)) {
        // Repeat calls which do not request specific year ranges stop here, we have already calculated the table
        // This will be the return path most of the time, pretty light computationally
        return true;
    }

    // Reaching here means we either need to extend our table or create it from scratch

    // Remember which TZ we calculated these changes for
    $SESSION->dst_offsettz = $usertz;

    if(empty($SESSION->dst_offsets)) {
        // If we 're creating from scratch, put the two guard elements in there
        $SESSION->dst_offsets = array(1 => NULL, 0 => NULL);
    }
    if(empty($SESSION->dst_range)) {
        // If creating from scratch
        $from = max((empty($from_year) ? intval(date('Y')) - 3 : $from_year), 1971);
        $to   = min((empty($to_year)   ? intval(date('Y')) + 3 : $to_year),   2035);

        // Fill in the array with the extra years we need to process
        $yearstoprocess = array();
        for($i = $from; $i <= $to; ++$i) {
            $yearstoprocess[] = $i;
        }

        // Take note of which years we have processed for future calls
        $SESSION->dst_range = array($from, $to);
    }
    else {
        // If needing to extend the table, do the same
        $yearstoprocess = array();

        $from = max((empty($from_year) ? $SESSION->dst_range[0] : $from_year), 1971);
        $to   = min((empty($to_year)   ? $SESSION->dst_range[1] : $to_year),   2035);

        if($from < $SESSION->dst_range[0]) {
            // Take note of which years we need to process and then note that we have processed them for future calls
            for($i = $from; $i < $SESSION->dst_range[0]; ++$i) {
                $yearstoprocess[] = $i;
            }
            $SESSION->dst_range[0] = $from;
        }
        if($to > $SESSION->dst_range[1]) {
            // Take note of which years we need to process and then note that we have processed them for future calls
            for($i = $SESSION->dst_range[1] + 1; $i <= $to; ++$i) {
                $yearstoprocess[] = $i;
            }
            $SESSION->dst_range[1] = $to;
        }
    }

    if(empty($yearstoprocess)) {
        // This means that there was a call requesting a SMALLER range than we have already calculated
        return true;
    }

    // From now on, we know that the array has at least the two guard elements, and $yearstoprocess has the years we need
    // Also, the array is sorted in descending timestamp order!

    // Get DB data

    static $presets_cache = array();
    if (!isset($presets_cache[$usertz])) {
        $presets_cache[$usertz] = $DB->get_records('timezone', array('name'=>$usertz), 'year DESC', 'year, gmtoff, dstoff, dst_month, dst_startday, dst_weekday, dst_skipweeks, dst_time, std_month, std_startday, std_weekday, std_skipweeks, std_time');
    }
    if(empty($presets_cache[$usertz])) {
        return false;
    }

    // Remove ending guard (first element of the array)
    reset($SESSION->dst_offsets);
    unset($SESSION->dst_offsets[key($SESSION->dst_offsets)]);

    // Add all required change timestamps
    foreach($yearstoprocess as $y) {
        // Find the record which is in effect for the year $y
        foreach($presets_cache[$usertz] as $year => $preset) {
            if($year <= $y) {
                break;
            }
        }

        $changes = dst_changes_for_year($y, $preset);

        if($changes === NULL) {
            continue;
        }
        if($changes['dst'] != 0) {
            $SESSION->dst_offsets[$changes['dst']] = $preset->dstoff * MINSECS;
        }
        if($changes['std'] != 0) {
            $SESSION->dst_offsets[$changes['std']] = 0;
        }
    }

    // Put in a guard element at the top
    $maxtimestamp = max(array_keys($SESSION->dst_offsets));
    $SESSION->dst_offsets[($maxtimestamp + DAYSECS)] = NULL; // DAYSECS is arbitrary, any "small" number will do

    // Sort again
    krsort($SESSION->dst_offsets);

    return true;
}

/**
 * Calculates the required DST change and returns a Timestamp Array
 *
 * @uses HOURSECS
 * @uses MINSECS
 * @param mixed $year Int or String Year to focus on
 * @param object $timezone Instatiated Timezone object
 * @return mixed Null, or Array dst=>xx, 0=>xx, std=>yy, 1=>yy
 */
function dst_changes_for_year($year, $timezone) {

    if($timezone->dst_startday == 0 && $timezone->dst_weekday == 0 && $timezone->std_startday == 0 && $timezone->std_weekday == 0) {
        return NULL;
    }

    $monthdaydst = find_day_in_month($timezone->dst_startday, $timezone->dst_weekday, $timezone->dst_month, $year);
    $monthdaystd = find_day_in_month($timezone->std_startday, $timezone->std_weekday, $timezone->std_month, $year);

    list($dst_hour, $dst_min) = explode(':', $timezone->dst_time);
    list($std_hour, $std_min) = explode(':', $timezone->std_time);

    $timedst = make_timestamp($year, $timezone->dst_month, $monthdaydst, 0, 0, 0, 99, false);
    $timestd = make_timestamp($year, $timezone->std_month, $monthdaystd, 0, 0, 0, 99, false);

    // Instead of putting hour and minute in make_timestamp(), we add them afterwards.
    // This has the advantage of being able to have negative values for hour, i.e. for timezones
    // where GMT time would be in the PREVIOUS day than the local one on which DST changes.

    $timedst += $dst_hour * HOURSECS + $dst_min * MINSECS;
    $timestd += $std_hour * HOURSECS + $std_min * MINSECS;

    return array('dst' => $timedst, 0 => $timedst, 'std' => $timestd, 1 => $timestd);
}

/**
 * Calculates the Daylight Saving Offset for a given date/time (timestamp)
 * - Note: Daylight saving only works for string timezones and not for float.
 *
 * @global object
 * @param int $time must NOT be compensated at all, it has to be a pure timestamp
 * @param mixed $strtimezone timezone for which offset is expected, if 99 or null
 *        then user's default timezone is used.
 * @return int
 */
function dst_offset_on($time, $strtimezone = NULL) {
    global $SESSION;

    if(!calculate_user_dst_table(NULL, NULL, $strtimezone) || empty($SESSION->dst_offsets)) {
        return 0;
    }

    reset($SESSION->dst_offsets);
    while(list($from, $offset) = each($SESSION->dst_offsets)) {
        if($from <= $time) {
            break;
        }
    }

    // This is the normal return path
    if($offset !== NULL) {
        return $offset;
    }

    // Reaching this point means we haven't calculated far enough, do it now:
    // Calculate extra DST changes if needed and recurse. The recursion always
    // moves toward the stopping condition, so will always end.

    if($from == 0) {
        // We need a year smaller than $SESSION->dst_range[0]
        if($SESSION->dst_range[0] == 1971) {
            return 0;
        }
        calculate_user_dst_table($SESSION->dst_range[0] - 5, NULL, $strtimezone);
        return dst_offset_on($time, $strtimezone);
    }
    else {
        // We need a year larger than $SESSION->dst_range[1]
        if($SESSION->dst_range[1] == 2035) {
            return 0;
        }
        calculate_user_dst_table(NULL, $SESSION->dst_range[1] + 5, $strtimezone);
        return dst_offset_on($time, $strtimezone);
    }
}

/**
 * ?
 *
 * @todo Document what this function does
 * @param int $startday
 * @param int $weekday
 * @param int $month
 * @param int $year
 * @return int
 */
function find_day_in_month($startday, $weekday, $month, $year) {

    $daysinmonth = days_in_month($month, $year);

    if($weekday == -1) {
        // Don't care about weekday, so return:
        //    abs($startday) if $startday != -1
        //    $daysinmonth otherwise
        return ($startday == -1) ? $daysinmonth : abs($startday);
    }

    // From now on we 're looking for a specific weekday

    // Give "end of month" its actual value, since we know it
    if($startday == -1) {
        $startday = -1 * $daysinmonth;
    }

    // Starting from day $startday, the sign is the direction

    if($startday < 1) {

        $startday = abs($startday);
        $lastmonthweekday  = strftime('%w', mktime(12, 0, 0, $month, $daysinmonth, $year));

        // This is the last such weekday of the month
        $lastinmonth = $daysinmonth + $weekday - $lastmonthweekday;
        if($lastinmonth > $daysinmonth) {
            $lastinmonth -= 7;
        }

        // Find the first such weekday <= $startday
        while($lastinmonth > $startday) {
            $lastinmonth -= 7;
        }

        return $lastinmonth;

    }
    else {

        $indexweekday = strftime('%w', mktime(12, 0, 0, $month, $startday, $year));

        $diff = $weekday - $indexweekday;
        if($diff < 0) {
            $diff += 7;
        }

        // This is the first such weekday of the month equal to or after $startday
        $firstfromindex = $startday + $diff;

        return $firstfromindex;

    }
}

/**
 * Calculate the number of days in a given month
 *
 * @param int $month The month whose day count is sought
 * @param int $year The year of the month whose day count is sought
 * @return int
 */
function days_in_month($month, $year) {
   return intval(date('t', mktime(12, 0, 0, $month, 1, $year)));
}

/**
 * Calculate the position in the week of a specific calendar day
 *
 * @param int $day The day of the date whose position in the week is sought
 * @param int $month The month of the date whose position in the week is sought
 * @param int $year The year of the date whose position in the week is sought
 * @return int
 */
function dayofweek($day, $month, $year) {
    // I wonder if this is any different from
    // strftime('%w', mktime(12, 0, 0, $month, $daysinmonth, $year, 0));
    return intval(date('w', mktime(12, 0, 0, $month, $day, $year)));
}

/// USER AUTHENTICATION AND LOGIN ////////////////////////////////////////

/**
 * Returns full login url.
 *
 * @return string login url
 */
function get_login_url() {
    global $CFG;

    $url = "$CFG->wwwroot/login/index.php";

    if (!empty($CFG->loginhttps)) {
        $url = str_replace('http:', 'https:', $url);
    }

    return $url;
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
 * @param mixed $courseorid id of the course or course object
 * @param bool $autologinguest default true
 * @param object $cm course module object
 * @param bool $setwantsurltome Define if we want to set $SESSION->wantsurl, defaults to
 *             true. Used to avoid (=false) some scripts (file.php...) to set that variable,
 *             in order to keep redirects working properly. MDL-14495
 * @param bool $preventredirect set to true in scripts that can not redirect (CLI, rss feeds, etc.), throws exceptions
 * @return mixed Void, exit, and die depending on path
 */
function require_login($courseorid = NULL, $autologinguest = true, $cm = NULL, $setwantsurltome = true, $preventredirect = false) {
    global $CFG, $SESSION, $USER, $FULLME, $PAGE, $SITE, $DB, $OUTPUT;

    // setup global $COURSE, themes, language and locale
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
            // make sure we have a $cm from get_fast_modinfo as this contains activity access details
            if (!($cm instanceof cm_info)) {
                // note: nearly all pages call get_fast_modinfo anyway and it does not make any
                // db queries so this is not really a performance concern, however it is obviously
                // better if you use get_fast_modinfo to get the cm before calling this.
                $modinfo = get_fast_modinfo($course);
                $cm = $modinfo->get_cm($cm->id);
            }
            $PAGE->set_cm($cm, $course); // set's up global $COURSE
            $PAGE->set_pagelayout('incourse');
        } else {
            $PAGE->set_course($course); // set's up global $COURSE
        }
    } else {
        // do not touch global $COURSE via $PAGE->set_course(),
        // the reasons is we need to be able to call require_login() at any time!!
        $course = $SITE;
        if ($cm) {
            throw new coding_exception('cm parameter in require_login() requires valid course parameter!');
        }
    }

    // If the user is not even logged in yet then make sure they are
    if (!isloggedin()) {
        if ($autologinguest and !empty($CFG->guestloginbutton) and !empty($CFG->autologinguests)) {
            if (!$guest = get_complete_user_data('id', $CFG->siteguest)) {
                // misconfigured site guest, just redirect to login page
                redirect(get_login_url());
                exit; // never reached
            }
            $lang = isset($SESSION->lang) ? $SESSION->lang : $CFG->lang;
            complete_user_login($guest, false);
            $USER->autologinguest = true;
            $SESSION->lang = $lang;
        } else {
            //NOTE: $USER->site check was obsoleted by session test cookie,
            //      $USER->confirmed test is in login/index.php
            if ($preventredirect) {
                throw new require_login_exception('You are not logged in');
            }

            if ($setwantsurltome) {
                // TODO: switch to PAGE->url
                $SESSION->wantsurl = $FULLME;
            }
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $SESSION->fromurl  = $_SERVER['HTTP_REFERER'];
            }
            redirect(get_login_url());
            exit; // never reached
        }
    }

    // loginas as redirection if needed
    if ($course->id != SITEID and session_is_loggedinas()) {
        if ($USER->loginascontext->contextlevel == CONTEXT_COURSE) {
            if ($USER->loginascontext->instanceid != $course->id) {
                print_error('loginasonecourse', '', $CFG->wwwroot.'/course/view.php?id='.$USER->loginascontext->instanceid);
            }
        }
    }

    // check whether the user should be changing password (but only if it is REALLY them)
    if (get_user_preferences('auth_forcepasswordchange') && !session_is_loggedinas()) {
        $userauth = get_auth_plugin($USER->auth);
        if ($userauth->can_change_password() and !$preventredirect) {
            $SESSION->wantsurl = $FULLME;
            if ($changeurl = $userauth->change_password_url()) {
                //use plugin custom url
                redirect($changeurl);
            } else {
                //use moodle internal method
                if (empty($CFG->loginhttps)) {
                    redirect($CFG->wwwroot .'/login/change_password.php');
                } else {
                    $wwwroot = str_replace('http:','https:', $CFG->wwwroot);
                    redirect($wwwroot .'/login/change_password.php');
                }
            }
        } else {
            print_error('nopasswordchangeforced', 'auth');
        }
    }

    // Check that the user account is properly set up
    if (user_not_fully_set_up($USER)) {
        if ($preventredirect) {
            throw new require_login_exception('User not fully set-up');
        }
        $SESSION->wantsurl = $FULLME;
        redirect($CFG->wwwroot .'/user/edit.php?id='. $USER->id .'&amp;course='. SITEID);
    }

    // Make sure the USER has a sesskey set up. Used for CSRF protection.
    sesskey();

    // Do not bother admins with any formalities
    if (is_siteadmin()) {
        //set accesstime or the user will appear offline which messes up messaging
        user_accesstime_log($course->id);
        return;
    }

    // Check that the user has agreed to a site policy if there is one - do not test in case of admins
    if (!$USER->policyagreed and !is_siteadmin()) {
        if (!empty($CFG->sitepolicy) and !isguestuser()) {
            if ($preventredirect) {
                throw new require_login_exception('Policy not agreed');
            }
            $SESSION->wantsurl = $FULLME;
            redirect($CFG->wwwroot .'/user/policy.php');
        } else if (!empty($CFG->sitepolicyguest) and isguestuser()) {
            if ($preventredirect) {
                throw new require_login_exception('Policy not agreed');
            }
            $SESSION->wantsurl = $FULLME;
            redirect($CFG->wwwroot .'/user/policy.php');
        }
    }

    // Fetch the system context, the course context, and prefetch its child contexts
    $sysctx = get_context_instance(CONTEXT_SYSTEM);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
    if ($cm) {
        $cmcontext = get_context_instance(CONTEXT_MODULE, $cm->id, MUST_EXIST);
    } else {
        $cmcontext = null;
    }

    // If the site is currently under maintenance, then print a message
    if (!empty($CFG->maintenance_enabled) and !has_capability('moodle/site:config', $sysctx)) {
        if ($preventredirect) {
            throw new require_login_exception('Maintenance in progress');
        }

        print_maintenance_message();
    }

    // make sure the course itself is not hidden
    if ($course->id == SITEID) {
        // frontpage can not be hidden
    } else {
        if (is_role_switched($course->id)) {
            // when switching roles ignore the hidden flag - user had to be in course to do the switch
        } else {
            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                // originally there was also test of parent category visibility,
                // BUT is was very slow in complex queries involving "my courses"
                // now it is also possible to simply hide all courses user is not enrolled in :-)
                if ($preventredirect) {
                    throw new require_login_exception('Course is hidden');
                }
                // We need to override the navigation URL as the course won't have
                // been added to the navigation and thus the navigation will mess up
                // when trying to find it.
                navigation_node::override_active_url(new moodle_url('/'));
                notice(get_string('coursehidden'), $CFG->wwwroot .'/');
            }
        }
    }

    // is the user enrolled?
    if ($course->id == SITEID) {
        // everybody is enrolled on the frontpage

    } else {
        if (session_is_loggedinas()) {
            // Make sure the REAL person can access this course first
            $realuser = session_get_realuser();
            if (!is_enrolled($coursecontext, $realuser->id, '', true) and !is_viewing($coursecontext, $realuser->id) and !is_siteadmin($realuser->id)) {
                if ($preventredirect) {
                    throw new require_login_exception('Invalid course login-as access');
                }
                echo $OUTPUT->header();
                notice(get_string('studentnotallowed', '', fullname($USER, true)), $CFG->wwwroot .'/');
            }
        }

        // very simple enrolment caching - changes in course setting are not reflected immediately
        if (!isset($USER->enrol)) {
            $USER->enrol = array();
            $USER->enrol['enrolled'] = array();
            $USER->enrol['tempguest'] = array();
        }

        $access = false;

        if (is_role_switched($course->id)) {
            // ok, user had to be inside this course before the switch
            $access = true;

        } else if (is_viewing($coursecontext, $USER)) {
            // ok, no need to mess with enrol
            $access = true;

        } else {
            if (isset($USER->enrol['enrolled'][$course->id])) {
                if ($USER->enrol['enrolled'][$course->id] == 0) {
                    $access = true;
                } else if ($USER->enrol['enrolled'][$course->id] > time()) {
                    $access = true;
                } else {
                    //expired
                    unset($USER->enrol['enrolled'][$course->id]);
                }
            }
            if (isset($USER->enrol['tempguest'][$course->id])) {
                if ($USER->enrol['tempguest'][$course->id] == 0) {
                    $access = true;
                } else if ($USER->enrol['tempguest'][$course->id] > time()) {
                    $access = true;
                } else {
                    //expired
                    unset($USER->enrol['tempguest'][$course->id]);
                    $USER->access = remove_temp_roles($coursecontext, $USER->access);
                }
            }

            if ($access) {
                // cache ok
            } else if (is_enrolled($coursecontext, $USER, '', true)) {
                // active participants may always access
                // TODO: refactor this into some new function
                $now = time();
                $sql = "SELECT MAX(ue.timeend)
                          FROM {user_enrolments} ue
                          JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                          JOIN {user} u ON u.id = ue.userid
                         WHERE ue.userid = :userid AND ue.status = :active AND e.status = :enabled AND u.deleted = 0
                               AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)";
                $params = array('enabled'=>ENROL_INSTANCE_ENABLED, 'active'=>ENROL_USER_ACTIVE,
                                'userid'=>$USER->id, 'courseid'=>$coursecontext->instanceid, 'now1'=>$now, 'now2'=>$now);
                $until = $DB->get_field_sql($sql, $params);
                if (!$until or $until > time() + ENROL_REQUIRE_LOGIN_CACHE_PERIOD) {
                    $until = time() + ENROL_REQUIRE_LOGIN_CACHE_PERIOD;
                }

                $USER->enrol['enrolled'][$course->id] = $until;
                $access = true;

                // remove traces of previous temp guest access
                $USER->access = remove_temp_roles($coursecontext, $USER->access);

            } else {
                $instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'status'=>ENROL_INSTANCE_ENABLED), 'sortorder, id ASC');
                $enrols = enrol_get_plugins(true);
                // first ask all enabled enrol instances in course if they want to auto enrol user
                foreach($instances as $instance) {
                    if (!isset($enrols[$instance->enrol])) {
                        continue;
                    }
                    // Get a duration for the guestaccess, a timestamp in the future or false.
                    $until = $enrols[$instance->enrol]->try_autoenrol($instance);
                    if ($until !== false) {
                        $USER->enrol['enrolled'][$course->id] = $until;
                        $USER->access = remove_temp_roles($coursecontext, $USER->access);
                        $access = true;
                        break;
                    }
                }
                // if not enrolled yet try to gain temporary guest access
                if (!$access) {
                    foreach($instances as $instance) {
                        if (!isset($enrols[$instance->enrol])) {
                            continue;
                        }
                        // Get a duration for the guestaccess, a timestamp in the future or false.
                        $until = $enrols[$instance->enrol]->try_guestaccess($instance);
                        if ($until !== false) {
                            $USER->enrol['tempguest'][$course->id] = $until;
                            $access = true;
                            break;
                        }
                    }
                }
            }
        }

        if (!$access) {
            if ($preventredirect) {
                throw new require_login_exception('Not enrolled');
            }
            $SESSION->wantsurl = $FULLME;
            redirect($CFG->wwwroot .'/enrol/index.php?id='. $course->id);
        }
    }

    // Check visibility of activity to current user; includes visible flag, groupmembersonly,
    // conditional availability, etc
    if ($cm && !$cm->uservisible) {
        if ($preventredirect) {
            throw new require_login_exception('Activity is hidden');
        }
        redirect($CFG->wwwroot, get_string('activityiscurrentlyhidden'));
    }

    // Finally access granted, update lastaccess times
    user_accesstime_log($course->id);
}


/**
 * This function just makes sure a user is logged out.
 *
 * @global object
 */
function require_logout() {
    global $USER;

    $params = $USER;

    if (isloggedin()) {
        add_to_log(SITEID, "user", "logout", "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);

        $authsequence = get_enabled_auth_plugins(); // auths, in sequence
        foreach($authsequence as $authname) {
            $authplugin = get_auth_plugin($authname);
            $authplugin->prelogout_hook();
        }
    }

    events_trigger('user_logout', $params);
    session_get_instance()->terminate_current();
    unset($params);
}

/**
 * Weaker version of require_login()
 *
 * This is a weaker version of {@link require_login()} which only requires login
 * when called from within a course rather than the site page, unless
 * the forcelogin option is turned on.
 * @see require_login()
 *
 * @global object
 * @param mixed $courseorid The course object or id in question
 * @param bool $autologinguest Allow autologin guests if that is wanted
 * @param object $cm Course activity module if known
 * @param bool $setwantsurltome Define if we want to set $SESSION->wantsurl, defaults to
 *             true. Used to avoid (=false) some scripts (file.php...) to set that variable,
 *             in order to keep redirects working properly. MDL-14495
 * @param bool $preventredirect set to true in scripts that can not redirect (CLI, rss feeds, etc.), throws exceptions
 * @return void
 */
function require_course_login($courseorid, $autologinguest = true, $cm = NULL, $setwantsurltome = true, $preventredirect = false) {
    global $CFG, $PAGE, $SITE;
    $issite = (is_object($courseorid) and $courseorid->id == SITEID)
          or (!is_object($courseorid) and $courseorid == SITEID);
    if ($issite && !empty($cm) && !($cm instanceof cm_info)) {
        // note: nearly all pages call get_fast_modinfo anyway and it does not make any
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
        // login required for both SITE and courses
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);

    } else if ($issite && !empty($cm) and !$cm->uservisible) {
        // always login for hidden activities
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);

    } else if ($issite) {
              //login for SITE not required
        if ($cm and empty($cm->visible)) {
            // hidden activities are not accessible without login
            require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);
        } else if ($cm and !empty($CFG->enablegroupmembersonly) and $cm->groupmembersonly) {
            // not-logged-in users do not have any group membership
            require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);
        } else {
            // We still need to instatiate PAGE vars properly so that things
            // that rely on it like navigation function correctly.
            if (!empty($courseorid)) {
                if (is_object($courseorid)) {
                    $course = $courseorid;
                } else {
                    $course = clone($SITE);
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
                // If $PAGE->course, and hence $PAGE->context, have not already been set
                // up properly, set them up now.
                $PAGE->set_course($PAGE->course);
            }
            //TODO: verify conditional activities here
            user_accesstime_log(SITEID);
            return;
        }

    } else {
        // course login always required
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);
    }
}

/**
 * Require key login. Function terminates with error if key not found or incorrect.
 *
 * @global object
 * @global object
 * @global object
 * @global object
 * @uses NO_MOODLE_COOKIES
 * @uses PARAM_ALPHANUM
 * @param string $script unique script identifier
 * @param int $instance optional instance id
 * @return int Instance ID
 */
function require_user_key_login($script, $instance=null) {
    global $USER, $SESSION, $CFG, $DB;

    if (!NO_MOODLE_COOKIES) {
        print_error('sessioncookiesdisable');
    }

/// extra safety
    @session_write_close();

    $keyvalue = required_param('key', PARAM_ALPHANUM);

    if (!$key = $DB->get_record('user_private_key', array('script'=>$script, 'value'=>$keyvalue, 'instance'=>$instance))) {
        print_error('invalidkey');
    }

    if (!empty($key->validuntil) and $key->validuntil < time()) {
        print_error('expiredkey');
    }

    if ($key->iprestriction) {
        $remoteaddr = getremoteaddr(null);
        if (empty($remoteaddr) or !address_in_subnet($remoteaddr, $key->iprestriction)) {
            print_error('ipmismatch');
        }
    }

    if (!$user = $DB->get_record('user', array('id'=>$key->userid))) {
        print_error('invaliduserid');
    }

/// emulate normal session
    session_set_user($user);

/// note we are not using normal login
    if (!defined('USER_KEY_LOGIN')) {
        define('USER_KEY_LOGIN', true);
    }

/// return instance id - it might be empty
    return $key->instance;
}

/**
 * Creates a new private user access key.
 *
 * @global object
 * @param string $script unique target identifier
 * @param int $userid
 * @param int $instance optional instance id
 * @param string $iprestriction optional ip restricted access
 * @param timestamp $validuntil key valid only until given data
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

    $key->value         = md5($userid.'_'.time().random_string(40)); // something long and unique
    while ($DB->record_exists('user_private_key', array('value'=>$key->value))) {
        // must be unique
        $key->value     = md5($userid.'_'.time().random_string(40));
    }
    $DB->insert_record('user_private_key', $key);
    return $key->value;
}

/**
 * Delete the user's new private user access keys for a particular script.
 *
 * @global object
 * @param string $script unique target identifier
 * @param int $userid
 * @return void
 */
function delete_user_key($script,$userid) {
    global $DB;
    $DB->delete_records('user_private_key', array('script'=>$script, 'userid'=>$userid));
}

/**
 * Gets a private user access key (and creates one if one doesn't exist).
 *
 * @global object
 * @param string $script unique target identifier
 * @param int $userid
 * @param int $instance optional instance id
 * @param string $iprestriction optional ip restricted access
 * @param timestamp $validuntil key valid only until given data
 * @return string access key value
 */
function get_user_key($script, $userid, $instance=null, $iprestriction=null, $validuntil=null) {
    global $DB;

    if ($key = $DB->get_record('user_private_key', array('script'=>$script, 'userid'=>$userid,
                                                         'instance'=>$instance, 'iprestriction'=>$iprestriction,
                                                         'validuntil'=>$validuntil))) {
        return $key->value;
    } else {
        return create_user_key($script, $userid, $instance, $iprestriction, $validuntil);
    }
}


/**
 * Modify the user table by setting the currently logged in user's
 * last login to now.
 *
 * @global object
 * @global object
 * @return bool Always returns true
 */
function update_user_login_times() {
    global $USER, $DB;

    $user = new stdClass();
    $USER->lastlogin = $user->lastlogin = $USER->currentlogin;
    $USER->currentlogin = $user->lastaccess = $user->currentlogin = time();

    $user->id = $USER->id;

    $DB->update_record('user', $user);
    return true;
}

/**
 * Determines if a user has completed setting up their account.
 *
 * @param user $user A {@link $USER} object to test for the existence of a valid name and email
 * @return bool
 */
function user_not_fully_set_up($user) {
    if (isguestuser($user)) {
        return false;
    }
    return (empty($user->firstname) or empty($user->lastname) or empty($user->email) or over_bounce_threshold($user));
}

/**
 * Check whether the user has exceeded the bounce threshold
 *
 * @global object
 * @global object
 * @param user $user A {@link $USER} object
 * @return bool true=>User has exceeded bounce threshold
 */
function over_bounce_threshold($user) {
    global $CFG, $DB;

    if (empty($CFG->handlebounces)) {
        return false;
    }

    if (empty($user->id)) { /// No real (DB) user, nothing to do here.
        return false;
    }

    // set sensible defaults
    if (empty($CFG->minbounces)) {
        $CFG->minbounces = 10;
    }
    if (empty($CFG->bounceratio)) {
        $CFG->bounceratio = .20;
    }
    $bouncecount = 0;
    $sendcount = 0;
    if ($bounce = $DB->get_record('user_preferences', array ('userid'=>$user->id, 'name'=>'email_bounce_count'))) {
        $bouncecount = $bounce->value;
    }
    if ($send = $DB->get_record('user_preferences', array('userid'=>$user->id, 'name'=>'email_send_count'))) {
        $sendcount = $send->value;
    }
    return ($bouncecount >= $CFG->minbounces && $bouncecount/$sendcount >= $CFG->bounceratio);
}

/**
 * Used to increment or reset email sent count
 *
 * @global object
 * @param user $user object containing an id
 * @param bool $reset will reset the count to 0
 * @return void
 */
function set_send_count($user,$reset=false) {
    global $DB;

    if (empty($user->id)) { /// No real (DB) user, nothing to do here.
        return;
    }

    if ($pref = $DB->get_record('user_preferences', array('userid'=>$user->id, 'name'=>'email_send_count'))) {
        $pref->value = (!empty($reset)) ? 0 : $pref->value+1;
        $DB->update_record('user_preferences', $pref);
    }
    else if (!empty($reset)) { // if it's not there and we're resetting, don't bother.
        // make a new one
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
 * @global object
 * @param user $user object containing an id
 * @param bool $reset will reset the count to 0
 */
function set_bounce_count($user,$reset=false) {
    global $DB;

    if ($pref = $DB->get_record('user_preferences', array('userid'=>$user->id, 'name'=>'email_bounce_count'))) {
        $pref->value = (!empty($reset)) ? 0 : $pref->value+1;
        $DB->update_record('user_preferences', $pref);
    }
    else if (!empty($reset)) { // if it's not there and we're resetting, don't bother.
        // make a new one
        $pref = new stdClass();
        $pref->name   = 'email_bounce_count';
        $pref->value  = 1;
        $pref->userid = $user->id;
        $DB->insert_record('user_preferences', $pref, false);
    }
}

/**
 * Keeps track of login attempts
 *
 * @global object
 */
function update_login_count() {
    global $SESSION;

    $max_logins = 10;

    if (empty($SESSION->logincount)) {
        $SESSION->logincount = 1;
    } else {
        $SESSION->logincount++;
    }

    if ($SESSION->logincount > $max_logins) {
        unset($SESSION->wantsurl);
        print_error('errortoomanylogins');
    }
}

/**
 * Resets login attempts
 *
 * @global object
 */
function reset_login_count() {
    global $SESSION;

    $SESSION->logincount = 0;
}

/**
 * Determines if the currently logged in user is in editing mode.
 * Note: originally this function had $userid parameter - it was not usable anyway
 *
 * @deprecated since Moodle 2.0 - use $PAGE->user_is_editing() instead.
 * @todo Deprecated function remove when ready
 *
 * @global object
 * @uses DEBUG_DEVELOPER
 * @return bool
 */
function isediting() {
    global $PAGE;
    debugging('call to deprecated function isediting(). Please use $PAGE->user_is_editing() instead', DEBUG_DEVELOPER);
    return $PAGE->user_is_editing();
}

/**
 * Determines if the logged in user is currently moving an activity
 *
 * @global object
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
 * Given an object containing firstname and lastname
 * values, this function returns a string with the
 * full name of the person.
 * The result may depend on system settings
 * or language.  'override' will force both names
 * to be used even if system settings specify one.
 *
 * @global object
 * @global object
 * @param object $user A {@link $USER} object to get full name of
 * @param bool $override If true then the name will be first name followed by last name rather than adhering to fullnamedisplay setting.
 * @return string
 */
function fullname($user, $override=false) {
    global $CFG, $SESSION;

    if (!isset($user->firstname) and !isset($user->lastname)) {
        return '';
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

    if (!isset($CFG->fullnamedisplay) or $CFG->fullnamedisplay === 'firstname lastname') {
        return $user->firstname .' '. $user->lastname;

    } else if ($CFG->fullnamedisplay == 'lastname firstname') {
        return $user->lastname .' '. $user->firstname;

    } else if ($CFG->fullnamedisplay == 'firstname') {
        if ($override) {
            return get_string('fullnamedisplay', '', $user);
        } else {
            return $user->firstname;
        }
    }

    return get_string('fullnamedisplay', '', $user);
}

/**
 * Returns whether a given authentication plugin exists.
 *
 * @global object
 * @param string $auth Form of authentication to check for. Defaults to the
 *        global setting in {@link $CFG}.
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
 * @global object
 * @param string $auth name of authentication plugin
 * @return auth_plugin_base An instance of the required authentication plugin.
 */
function get_auth_plugin($auth) {
    global $CFG;

    // check the plugin exists first
    if (! exists_auth_plugin($auth)) {
        print_error('authpluginnotfound', 'debug', '', $auth);
    }

    // return auth plugin instance
    require_once "{$CFG->dirroot}/auth/$auth/auth.php";
    $class = "auth_plugin_$auth";
    return new $class;
}

/**
 * Returns array of active auth plugins.
 *
 * @param bool $fix fix $CFG->auth if needed
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

    if ($fix) {
        $auths = array_unique($auths);
        foreach($auths as $k=>$authname) {
            if (!exists_auth_plugin($authname) or in_array($authname, $default)) {
                unset($auths[$k]);
            }
        }
        $newconfig = implode(',', $auths);
        if (!isset($CFG->auth) or $newconfig != $CFG->auth) {
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
    $authplugin = get_auth_plugin($auth); // throws error if bad $auth
    return $authplugin->is_internal();
}

/**
 * Returns true if the user is a 'restored' one
 *
 * Used in the login process to inform the user
 * and allow him/her to reset the password
 *
 * @uses $CFG
 * @uses $DB
 * @param string $username username to be checked
 * @return bool
 */
function is_restored_user($username) {
    global $CFG, $DB;

    return $DB->record_exists('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id, 'password'=>'restored'));
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
    global $CFG, $DB;

    //just in case check text case
    $username = trim(moodle_strtolower($username));

    $authplugin = get_auth_plugin($auth);

    $newuser = new stdClass();

    if ($newinfo = $authplugin->get_userinfo($username)) {
        $newinfo = truncate_userinfo($newinfo);
        foreach ($newinfo as $key => $value){
            $newuser->$key = $value;
        }
    }

    if (!empty($newuser->email)) {
        if (email_is_not_allowed($newuser->email)) {
            unset($newuser->email);
        }
    }

    if (!isset($newuser->city)) {
        $newuser->city = '';
    }

    $newuser->auth = $auth;
    $newuser->username = $username;

    // fix for MDL-8480
    // user CFG lang for user if $newuser->lang is empty
    // or $user->lang is not an installed language
    if (empty($newuser->lang) || !get_string_manager()->translation_exists($newuser->lang)) {
        $newuser->lang = $CFG->lang;
    }
    $newuser->confirmed = 1;
    $newuser->lastip = getremoteaddr();
    $newuser->timecreated = time();
    $newuser->timemodified = $newuser->timecreated;
    $newuser->mnethostid = $CFG->mnet_localhost_id;

    $newuser->id = $DB->insert_record('user', $newuser);
    $user = get_complete_user_data('id', $newuser->id);
    if (!empty($CFG->{'auth_'.$newuser->auth.'_forcechangepassword'})){
        set_user_preference('auth_forcepasswordchange', 1, $user);
    }
    update_internal_user_password($user, $password);

    // fetch full user record for the event, the complete user data contains too much info
    // and we want to be consistent with other places that trigger this event
    events_trigger('user_created', $DB->get_record('user', array('id'=>$user->id)));

    return $user;
}

/**
 * Will update a local user record from an external source.
 * (MNET users can not be updated using this method!)
 *
 * @param string $username user's username to update the record
 * @return stdClass A complete user object
 */
function update_user_record($username) {
    global $DB, $CFG;

    $username = trim(moodle_strtolower($username)); /// just in case check text case

    $oldinfo = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id), '*', MUST_EXIST);
    $newuser = array();
    $userauth = get_auth_plugin($oldinfo->auth);

    if ($newinfo = $userauth->get_userinfo($username)) {
        $newinfo = truncate_userinfo($newinfo);
        foreach ($newinfo as $key => $value){
            $key = strtolower($key);
            if (!property_exists($oldinfo, $key) or $key === 'username' or $key === 'id'
                    or $key === 'auth' or $key === 'mnethostid' or $key === 'deleted') {
                // unknown or must not be changed
                continue;
            }
            $confval = $userauth->config->{'field_updatelocal_' . $key};
            $lockval = $userauth->config->{'field_lock_' . $key};
            if (empty($confval) || empty($lockval)) {
                continue;
            }
            if ($confval === 'onlogin') {
                // MDL-4207 Don't overwrite modified user profile values with
                // empty LDAP values when 'unlocked if empty' is set. The purpose
                // of the setting 'unlocked if empty' is to allow the user to fill
                // in a value for the selected field _if LDAP is giving
                // nothing_ for this field. Thus it makes sense to let this value
                // stand in until LDAP is giving a value for this field.
                if (!(empty($value) && $lockval === 'unlockedifempty')) {
                    if ((string)$oldinfo->$key !== (string)$value) {
                        $newuser[$key] = (string)$value;
                    }
                }
            }
        }
        if ($newuser) {
            $newuser['id'] = $oldinfo->id;
            $newuser['timemodified'] = time();
            $DB->update_record('user', $newuser);
            // fetch full user record for the event, the complete user data contains too much info
            // and we want to be consistent with other places that trigger this event
            events_trigger('user_updated', $DB->get_record('user', array('id'=>$oldinfo->id)));
        }
    }

    return get_complete_user_data('id', $oldinfo->id);
}

/**
 * Will truncate userinfo as it comes from auth_get_userinfo (from external auth)
 * which may have large fields
 *
 * @todo Add vartype handling to ensure $info is an array
 *
 * @param array $info Array of user properties to truncate if needed
 * @return array The now truncated information that was passed in
 */
function truncate_userinfo($info) {
    // define the limits
    $limit = array(
                    'username'    => 100,
                    'idnumber'    => 255,
                    'firstname'   => 100,
                    'lastname'    => 100,
                    'email'       => 100,
                    'icq'         =>  15,
                    'phone1'      =>  20,
                    'phone2'      =>  20,
                    'institution' =>  40,
                    'department'  =>  30,
                    'address'     =>  70,
                    'city'        => 120,
                    'country'     =>   2,
                    'url'         => 255,
                    );

    $textlib = textlib_get_instance();
    // apply where needed
    foreach (array_keys($info) as $key) {
        if (!empty($limit[$key])) {
            $info[$key] = trim($textlib->substr($info[$key],0, $limit[$key]));
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
 * @return boolean always true
 */
function delete_user($user) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/grouplib.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/message/lib.php');
    require_once($CFG->dirroot.'/tag/lib.php');

    // delete all grades - backup is kept in grade_grades_history table
    grade_user_delete($user->id);

    //move unread messages from this user to read
    message_move_userfrom_unread2read($user->id);

    // TODO: remove from cohorts using standard API here

    // remove user tags
    tag_set('user', $user->id, array());

    // unconditionally unenrol from all courses
    enrol_user_delete($user);

    // unenrol from all roles in all contexts
    role_unassign_all(array('userid'=>$user->id)); // this might be slow but it is really needed - modules might do some extra cleanup!

    //now do a brute force cleanup

    // remove from all cohorts
    $DB->delete_records('cohort_members', array('userid'=>$user->id));

    // remove from all groups
    $DB->delete_records('groups_members', array('userid'=>$user->id));

    // brute force unenrol from all courses
    $DB->delete_records('user_enrolments', array('userid'=>$user->id));

    // purge user preferences
    $DB->delete_records('user_preferences', array('userid'=>$user->id));

    // purge user extra profile info
    $DB->delete_records('user_info_data', array('userid'=>$user->id));

    // last course access not necessary either
    $DB->delete_records('user_lastaccess', array('userid'=>$user->id));

    // remove all user tokens
    $DB->delete_records('external_tokens', array('userid'=>$user->id));

    // unauthorise the user for all services
    $DB->delete_records('external_services_users', array('userid'=>$user->id));

    // now do a final accesslib cleanup - removes all role assignments in user context and context itself
    delete_context(CONTEXT_USER, $user->id);

    // workaround for bulk deletes of users with the same email address
    $delname = "$user->email.".time();
    while ($DB->record_exists('user', array('username'=>$delname))) { // no need to use mnethostid here
        $delname++;
    }

    // mark internal user record as "deleted"
    $updateuser = new stdClass();
    $updateuser->id           = $user->id;
    $updateuser->deleted      = 1;
    $updateuser->username     = $delname;            // Remember it just in case
    $updateuser->email        = md5($user->username);// Store hash of username, useful importing/restoring users
    $updateuser->idnumber     = '';                  // Clear this field to free it up
    $updateuser->timemodified = time();

    $DB->update_record('user', $updateuser);

    // notify auth plugin - do not block the delete even when plugin fails
    $authplugin = get_auth_plugin($user->auth);
    $authplugin->user_delete($user);

    // any plugin that needs to cleanup should register this event
    events_trigger('user_deleted', $user);

    return true;
}

/**
 * Retrieve the guest user object
 *
 * @global object
 * @global object
 * @return user A {@link $USER} object
 */
function guest_user() {
    global $CFG, $DB;

    if ($newuser = $DB->get_record('user', array('id'=>$CFG->siteguest))) {
        $newuser->confirmed = 1;
        $newuser->lang = $CFG->lang;
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
 * @param string $username  User's username
 * @param string $password  User's password
 * @return user|flase A {@link $USER} object or false if error
 */
function authenticate_user_login($username, $password) {
    global $CFG, $DB;

    $authsenabled = get_enabled_auth_plugins();

    if ($user = get_complete_user_data('username', $username, $CFG->mnet_localhost_id)) {
        $auth = empty($user->auth) ? 'manual' : $user->auth;  // use manual if auth not set
        if (!empty($user->suspended)) {
            add_to_log(SITEID, 'login', 'error', 'index.php', $username);
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Suspended Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }
        if ($auth=='nologin' or !is_enabled_auth($auth)) {
            add_to_log(SITEID, 'login', 'error', 'index.php', $username);
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Disabled Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }
        $auths = array($auth);

    } else {
        // check if there's a deleted record (cheaply)
        if ($DB->get_field('user', 'id', array('username'=>$username, 'deleted'=>1))) {
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Deleted Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }

        // User does not exist
        $auths = $authsenabled;
        $user = new stdClass();
        $user->id = 0;
    }

    foreach ($auths as $auth) {
        $authplugin = get_auth_plugin($auth);

        // on auth fail fall through to the next plugin
        if (!$authplugin->user_login($username, $password)) {
            continue;
        }

        // successful authentication
        if ($user->id) {                          // User already exists in database
            if (empty($user->auth)) {             // For some reason auth isn't set yet
                $DB->set_field('user', 'auth', $auth, array('username'=>$username));
                $user->auth = $auth;
            }
            if (empty($user->firstaccess)) { //prevent firstaccess from remaining 0 for manual account that never required confirmation
                $DB->set_field('user','firstaccess', $user->timemodified, array('id' => $user->id));
                $user->firstaccess = $user->timemodified;
            }

            update_internal_user_password($user, $password); // just in case salt or encoding were changed (magic quotes too one day)

            if ($authplugin->is_synchronised_with_external()) { // update user record from external DB
                $user = update_user_record($username);
            }
        } else {
            // if user not found and user creation is not disabled, create it
            if (empty($CFG->authpreventaccountcreation)) {
                $user = create_user_record($username, $password, $auth);
            } else {
                continue;
            }
        }

        $authplugin->sync_roles($user);

        foreach ($authsenabled as $hau) {
            $hauth = get_auth_plugin($hau);
            $hauth->user_authenticated_hook($user, $username, $password);
        }

        if (empty($user->id)) {
            return false;
        }

        if (!empty($user->suspended)) {
            // just in case some auth plugin suspended account
            add_to_log(SITEID, 'login', 'error', 'index.php', $username);
            error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Suspended Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
            return false;
        }

        return $user;
    }

    // failed if all the plugins have failed
    add_to_log(SITEID, 'login', 'error', 'index.php', $username);
    if (debugging('', DEBUG_ALL)) {
        error_log('[client '.getremoteaddr()."]  $CFG->wwwroot  Failed Login:  $username  ".$_SERVER['HTTP_USER_AGENT']);
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
 *
 * @param object $user
 * @param bool $setcookie
 * @return object A {@link $USER} object - BC only, do not use
 */
function complete_user_login($user, $setcookie=true) {
    global $CFG, $USER;

    // regenerate session id and delete old session,
    // this helps prevent session fixation attacks from the same domain
    session_regenerate_id(true);

    // check enrolments, load caps and setup $USER object
    session_set_user($user);

    // reload preferences from DB
    unset($user->preference);
    check_user_preferences_loaded($user);

    // update login times
    update_user_login_times();

    // extra session prefs init
    set_login_session_preferences();

    if (isguestuser()) {
        // no need to continue when user is THE guest
        return $USER;
    }

    if ($setcookie) {
        if (empty($CFG->nolastloggedin)) {
            set_moodle_cookie($USER->username);
        } else {
            // do not store last logged in user in cookie
            // auth plugins can temporarily override this from loginpage_hook()
            // do not save $CFG->nolastloggedin in database!
            set_moodle_cookie('');
        }
    }

    /// Select password change url
    $userauth = get_auth_plugin($USER->auth);

    /// check whether the user should be changing password
    if (get_user_preferences('auth_forcepasswordchange', false)){
        if ($userauth->can_change_password()) {
            if ($changeurl = $userauth->change_password_url()) {
                redirect($changeurl);
            } else {
                redirect($CFG->httpswwwroot.'/login/change_password.php');
            }
        } else {
            print_error('nopasswordchangeforced', 'auth');
        }
    }
    return $USER;
}

/**
 * Compare password against hash stored in internal user table.
 * If necessary it also updates the stored hash to new format.
 *
 * @param stdClass $user (password property may be updated)
 * @param string $password plain text password
 * @return bool is password valid?
 */
function validate_internal_user_password($user, $password) {
    global $CFG;

    if (!isset($CFG->passwordsaltmain)) {
        $CFG->passwordsaltmain = '';
    }

    $validated = false;

    if ($user->password === 'not cached') {
        // internal password is not used at all, it can not validate

    } else if ($user->password === md5($password.$CFG->passwordsaltmain)
            or $user->password === md5($password)
            or $user->password === md5(addslashes($password).$CFG->passwordsaltmain)
            or $user->password === md5(addslashes($password))) {
        // note: we are intentionally using the addslashes() here because we
        //       need to accept old password hashes of passwords with magic quotes
        $validated = true;

    } else {
        for ($i=1; $i<=20; $i++) { //20 alternative salts should be enough, right?
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
        // force update of password hash using latest main password salt and encoding if needed
        update_internal_user_password($user, $password);
    }

    return $validated;
}

/**
 * Calculate hashed value from password using current hash mechanism.
 *
 * @param string $password
 * @return string password hash
 */
function hash_internal_user_password($password) {
    global $CFG;

    if (isset($CFG->passwordsaltmain)) {
        return md5($password.$CFG->passwordsaltmain);
    } else {
        return md5($password);
    }
}

/**
 * Update password hash in user object.
 *
 * @param stdClass $user (password property may be updated)
 * @param string $password plain text password
 * @return bool always returns true
 */
function update_internal_user_password($user, $password) {
    global $DB;

    $authplugin = get_auth_plugin($user->auth);
    if ($authplugin->prevent_local_passwords()) {
        $hashedpassword = 'not cached';
    } else {
        $hashedpassword = hash_internal_user_password($password);
    }

    if ($user->password !== $hashedpassword) {
        $DB->set_field('user', 'password',  $hashedpassword, array('id'=>$user->id));
        $user->password = $hashedpassword;
    }

    return true;
}

/**
 * Get a complete user record, which includes all the info
 * in the user record.
 *
 * Intended for setting as $USER session variable
 *
 * @param string $field The user field to be checked for a given value.
 * @param string $value The value to match for $field.
 * @param int $mnethostid
 * @return mixed False, or A {@link $USER} object.
 */
function get_complete_user_data($field, $value, $mnethostid = null) {
    global $CFG, $DB;

    if (!$field || !$value) {
        return false;
    }

/// Build the WHERE clause for an SQL query
    $params = array('fieldval'=>$value);
    $constraints = "$field = :fieldval AND deleted <> 1";

    // If we are loading user data based on anything other than id,
    // we must also restrict our search based on mnet host.
    if ($field != 'id') {
        if (empty($mnethostid)) {
            // if empty, we restrict to local users
            $mnethostid = $CFG->mnet_localhost_id;
        }
    }
    if (!empty($mnethostid)) {
        $params['mnethostid'] = $mnethostid;
        $constraints .= " AND mnethostid = :mnethostid";
    }

/// Get all the basic user data

    if (! $user = $DB->get_record_select('user', $constraints, $params)) {
        return false;
    }

/// Get various settings and preferences

    // preload preference cache
    check_user_preferences_loaded($user);

    // load course enrolment related stuff
    $user->lastcourseaccess    = array(); // during last session
    $user->currentcourseaccess = array(); // during current session
    if ($lastaccesses = $DB->get_records('user_lastaccess', array('userid'=>$user->id))) {
        foreach ($lastaccesses as $lastaccess) {
            $user->lastcourseaccess[$lastaccess->courseid] = $lastaccess->timeaccess;
        }
    }

    $sql = "SELECT g.id, g.courseid
              FROM {groups} g, {groups_members} gm
             WHERE gm.groupid=g.id AND gm.userid=?";

    // this is a special hack to speedup calendar display
    $user->groupmember = array();
    if (!isguestuser($user)) {
        if ($groups = $DB->get_records_sql($sql, array($user->id))) {
            foreach ($groups as $group) {
                if (!array_key_exists($group->courseid, $user->groupmember)) {
                    $user->groupmember[$group->courseid] = array();
                }
                $user->groupmember[$group->courseid][$group->id] = $group->id;
            }
        }
    }

/// Add the custom profile fields to the user record
    $user->profile = array();
    if (!isguestuser($user)) {
        require_once($CFG->dirroot.'/user/profile/lib.php');
        profile_load_custom_fields($user);
    }

/// Rewrite some variables if necessary
    if (!empty($user->description)) {
        $user->description = true;   // No need to cart all of it around
    }
    if (isguestuser($user)) {
        $user->lang       = $CFG->lang;               // Guest language always same as site
        $user->firstname  = get_string('guestuser');  // Name always in current language
        $user->lastname   = ' ';
    }

    return $user;
}

/**
 * Validate a password against the configured password policy
 *
 * @global object
 * @param string $password the password to be checked against the password policy
 * @param string $errmsg the error message to display when the password doesn't comply with the policy.
 * @return bool true if the password is valid according to the policy. false otherwise.
 */
function check_password_policy($password, &$errmsg) {
    global $CFG;

    if (empty($CFG->passwordpolicy)) {
        return true;
    }

    $textlib = textlib_get_instance();
    $errmsg = '';
    if ($textlib->strlen($password) < $CFG->minpasswordlength) {
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

    if ($errmsg == '') {
        return true;
    } else {
        return false;
    }
}


/**
 * When logging in, this function is run to set certain preferences
 * for the current SESSION
 *
 * @global object
 * @global object
 */
function set_login_session_preferences() {
    global $SESSION, $CFG;

    $SESSION->justloggedin = true;

    unset($SESSION->lang);
}


/**
 * Delete a course, including all related data from the database,
 * and any associated files.
 *
 * @global object
 * @global object
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
        if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
            return false;
        }
    }
    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    // frontpage course can not be deleted!!
    if ($courseid == SITEID) {
        return false;
    }

    // make the course completely empty
    remove_course_contents($courseid, $showfeedback);

    // delete the course and related context instance
    delete_context(CONTEXT_COURSE, $courseid);
    $DB->delete_records("course", array("id"=>$courseid));

    //trigger events
    $course->context = $context; // you can not fetch context in the event because it was already deleted
    events_trigger('course_deleted', $course);

    return true;
}

/**
 * Clear a course out completely, deleting all content
 * but don't delete the course itself.
 * This function does not verify any permissions.
 *
 * Please note this function also deletes all user enrolments,
 * enrolment instances and role assignments.
 *
 * @param int $courseid The id of the course that is being deleted
 * @param bool $showfeedback Whether to display notifications of each action the function performs.
 * @return bool true if all the removals succeeded. false if there were any failures. If this
 *             method returns false, some of the removals will probably have succeeded, and others
 *             failed, but you have no way of knowing which.
 */
function remove_course_contents($courseid, $showfeedback = true) {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->libdir.'/questionlib.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/group/lib.php');
    require_once($CFG->dirroot.'/tag/coursetagslib.php');

    $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
    $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);

    $strdeleted = get_string('deleted');

    // Delete course completion information,
    // this has to be done before grades and enrols
    $cc = new completion_info($course);
    $cc->clear_criteria();

    // remove roles and enrolments
    role_unassign_all(array('contextid'=>$context->id), true);
    enrol_course_delete($course);

    // Clean up course formats (iterate through all formats in the even the course format was ever changed)
    $formats = get_plugin_list('format');
    foreach ($formats as $format=>$formatdir) {
        $formatdelete = 'format_'.$format.'_delete_course';
        $formatlib    = "$formatdir/lib.php";
        if (file_exists($formatlib)) {
            include_once($formatlib);
            if (function_exists($formatdelete)) {
                if ($showfeedback) {
                    echo $OUTPUT->notification($strdeleted.' '.$format);
                }
                $formatdelete($course->id);
            }
        }
    }

    // Remove all data from gradebook - this needs to be done before course modules
    // because while deleting this information, the system may need to reference
    // the course modules that own the grades.
    remove_course_grades($courseid, $showfeedback);
    remove_grade_letters($context, $showfeedback);

    // Remove all data from availability and completion tables that is associated
    // with course-modules belonging to this course. Note this is done even if the
    // features are not enabled now, in case they were enabled previously
    $DB->delete_records_select('course_modules_completion',
           'coursemoduleid IN (SELECT id from {course_modules} WHERE course=?)',
           array($courseid));
    $DB->delete_records_select('course_modules_availability',
           'coursemoduleid IN (SELECT id from {course_modules} WHERE course=?)',
           array($courseid));

    // Delete course blocks - they may depend on modules so delete them first
    blocks_delete_all_for_context($context->id);

    // Delete every instance of every module
    if ($allmods = $DB->get_records('modules') ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = $CFG->dirroot .'/mod/'. $modname .'/lib.php';
            $moddelete = $modname .'_delete_instance';       // Delete everything connected to an instance
            $moddeletecourse = $modname .'_delete_course';   // Delete other stray stuff (uncommon)
            $count=0;
            if (file_exists($modfile)) {
                include_once($modfile);
                if (function_exists($moddelete)) {
                    if ($instances = $DB->get_records($modname, array('course'=>$course->id))) {
                        foreach ($instances as $instance) {
                            if ($cm = get_coursemodule_from_instance($modname, $instance->id, $course->id)) {
                                /// Delete activity context questions and question categories
                                question_delete_activity($cm,  $showfeedback);
                            }
                            if ($moddelete($instance->id)) {
                                $count++;

                            } else {
                                echo $OUTPUT->notification('Could not delete '. $modname .' instance '. $instance->id .' ('. format_string($instance->name) .')');
                            }
                            if ($cm) {
                                // delete cm and its context in correct order
                                delete_context(CONTEXT_MODULE, $cm->id); // some callbacks may try to fetch context, better delete first
                                $DB->delete_records('course_modules', array('id'=>$cm->id));
                            }
                        }
                    }
                } else {
                    //note: we should probably delete these anyway
                    echo $OUTPUT->notification('Function '.$moddelete.'() doesn\'t exist!');
                }

                if (function_exists($moddeletecourse)) {
                    $moddeletecourse($course, $showfeedback);
                }
            }
            if ($showfeedback) {
                echo $OUTPUT->notification($strdeleted .' '. $count .' x '. $modname);
            }
        }
    }

    // Delete any groups, removing members and grouping/course links first.
    groups_delete_groupings($course->id, $showfeedback);
    groups_delete_groups($course->id, $showfeedback);

    // Delete questions and question categories
    question_delete_course($course, $showfeedback);

    // Delete course tags
    coursetag_delete_course_tags($course->id, $showfeedback);

    // Delete legacy files (just in case some files are still left there after conversion to new file api)
    fulldelete($CFG->dataroot.'/'.$course->id);

    // cleanup course record - remove links to delted stuff
    $oldcourse = new stdClass();
    $oldcourse->id                = $course->id;
    $oldcourse->summary           = '';
    $oldcourse->modinfo           = NULL;
    $oldcourse->legacyfiles       = 0;
    $oldcourse->defaultgroupingid = 0;
    $oldcourse->enablecompletion  = 0;
    $DB->update_record('course', $oldcourse);

    // Delete all related records in other tables that may have a courseid
    // This array stores the tables that need to be cleared, as
    // table_name => column_name that contains the course id.
    $tablestoclear = array(
        'event' => 'courseid', // Delete events
        'log' => 'course', // Delete logs
        'course_sections' => 'course', // Delete any course stuff
        'course_modules' => 'course',
        'course_display' => 'course',
        'backup_courses' => 'courseid', // Delete scheduled backup stuff
        'user_lastaccess' => 'courseid',
        'backup_log' => 'courseid'
    );
    foreach ($tablestoclear as $table => $col) {
        $DB->delete_records($table, array($col=>$course->id));
    }

    // Delete all remaining stuff linked to context,
    // such as remaining roles, files, comments, etc.
    // Keep the context record for now.
    delete_context(CONTEXT_COURSE, $course->id, false);

    //trigger events
    $course->context = $context; // you can not access context in cron event later after course is deleted
    events_trigger('course_content_removed', $course);

    return true;
}

/**
 * Change dates in module - used from course reset.
 *
 * @global object
 * @global object
 * @param string $modname forum, assignment, etc
 * @param array $fields array of date fields from mod table
 * @param int $timeshift time difference
 * @param int $courseid
 * @return bool success
 */
function shift_course_mod_dates($modname, $fields, $timeshift, $courseid) {
    global $CFG, $DB;
    include_once($CFG->dirroot.'/mod/'.$modname.'/lib.php');

    $return = true;
    foreach ($fields as $field) {
        $updatesql = "UPDATE {".$modname."}
                          SET $field = $field + ?
                        WHERE course=? AND $field<>0 AND $field<>0";
        $return = $DB->execute($updatesql, array($timeshift, $courseid)) && $return;
    }

    $refreshfunction = $modname.'_refresh_events';
    if (function_exists($refreshfunction)) {
        $refreshfunction($courseid);
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
    global $CFG, $USER, $DB;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->dirroot.'/group/lib.php');

    $data->courseid = $data->id;
    $context = get_context_instance(CONTEXT_COURSE, $data->courseid);

    // calculate the time shift of dates
    if (!empty($data->reset_start_date)) {
        // time part of course startdate should be zero
        $data->timeshift = $data->reset_start_date - usergetmidnight($data->reset_start_date_old);
    } else {
        $data->timeshift = 0;
    }

    // result array: component, item, error
    $status = array();

    // start the resetting
    $componentstr = get_string('general');

    // move the course start time
    if (!empty($data->reset_start_date) and $data->timeshift) {
        // change course start data
        $DB->set_field('course', 'startdate', $data->reset_start_date, array('id'=>$data->courseid));
        // update all course and group events - do not move activity events
        $updatesql = "UPDATE {event}
                         SET timestart = timestart + ?
                       WHERE courseid=? AND instance=0";
        $DB->execute($updatesql, array($data->timeshift, $data->courseid));

        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    if (!empty($data->reset_logs)) {
        $DB->delete_records('log', array('course'=>$data->courseid));
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deletelogs'), 'error'=>false);
    }

    if (!empty($data->reset_events)) {
        $DB->delete_records('event', array('courseid'=>$data->courseid));
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteevents', 'calendar'), 'error'=>false);
    }

    if (!empty($data->reset_notes)) {
        require_once($CFG->dirroot.'/notes/lib.php');
        note_delete_all($data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deletenotes', 'notes'), 'error'=>false);
    }

    if (!empty($data->delete_blog_associations)) {
        require_once($CFG->dirroot.'/blog/lib.php');
        blog_remove_associations_for_course($data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteblogassociations', 'blog'), 'error'=>false);
    }

    if (!empty($data->reset_course_completion)) {
        // Delete course completion information
        $course = $DB->get_record('course', array('id'=>$data->courseid));
        $cc = new completion_info($course);
        $cc->delete_course_completion_data();
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deletecoursecompletiondata', 'completion'), 'error'=>false);
    }

    $componentstr = get_string('roles');

    if (!empty($data->reset_roles_overrides)) {
        $children = get_child_contexts($context);
        foreach ($children as $child) {
            $DB->delete_records('role_capabilities', array('contextid'=>$child->id));
        }
        $DB->delete_records('role_capabilities', array('contextid'=>$context->id));
        //force refresh for logged in users
        mark_context_dirty($context->path);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deletecourseoverrides', 'role'), 'error'=>false);
    }

    if (!empty($data->reset_roles_local)) {
        $children = get_child_contexts($context);
        foreach ($children as $child) {
            role_unassign_all(array('contextid'=>$child->id));
        }
        //force refresh for logged in users
        mark_context_dirty($context->path);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deletelocalroles', 'role'), 'error'=>false);
    }

    // First unenrol users - this cleans some of related user data too, such as forum subscriptions, tracking, etc.
    $data->unenrolled = array();
    if (!empty($data->unenrol_users)) {
        $plugins = enrol_get_plugins(true);
        $instances = enrol_get_instances($data->courseid, true);
        foreach ($instances as $key=>$instance) {
            if (!isset($plugins[$instance->enrol])) {
                unset($instances[$key]);
                continue;
            }
            if (!$plugins[$instance->enrol]->allow_unenrol($instance)) {
                unset($instances[$key]);
            }
        }

        $sqlempty = $DB->sql_empty();
        foreach($data->unenrol_users as $withroleid) {
            $sql = "SELECT DISTINCT ue.userid, ue.enrolid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                      JOIN {context} c ON (c.contextlevel = :courselevel AND c.instanceid = e.courseid)
                      JOIN {role_assignments} ra ON (ra.contextid = c.id AND ra.roleid = :roleid AND ra.userid = ue.userid)";
            $params = array('courseid'=>$data->courseid, 'roleid'=>$withroleid, 'courselevel'=>CONTEXT_COURSE);

            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (!isset($instances[$ue->enrolid])) {
                    continue;
                }
                $plugins[$instances[$ue->enrolid]->enrol]->unenrol_user($instances[$ue->enrolid], $ue->userid);
                $data->unenrolled[$ue->userid] = $ue->userid;
            }
        }
    }
    if (!empty($data->unenrolled)) {
        $status[] = array('component'=>$componentstr, 'item'=>get_string('unenrol', 'enrol').' ('.count($data->unenrolled).')', 'error'=>false);
    }


    $componentstr = get_string('groups');

    // remove all group members
    if (!empty($data->reset_groups_members)) {
        groups_delete_group_members($data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('removegroupsmembers', 'group'), 'error'=>false);
    }

    // remove all groups
    if (!empty($data->reset_groups_remove)) {
        groups_delete_groups($data->courseid, false);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallgroups', 'group'), 'error'=>false);
    }

    // remove all grouping members
    if (!empty($data->reset_groupings_members)) {
        groups_delete_groupings_groups($data->courseid, false);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('removegroupingsmembers', 'group'), 'error'=>false);
    }

    // remove all groupings
    if (!empty($data->reset_groupings_remove)) {
        groups_delete_groupings($data->courseid, false);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallgroupings', 'group'), 'error'=>false);
    }

    // Look in every instance of every module for data to delete
    $unsupported_mods = array();
    if ($allmods = $DB->get_records('modules') ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            if (!$DB->count_records($modname, array('course'=>$data->courseid))) {
                continue; // skip mods with no instances
            }
            $modfile = $CFG->dirroot.'/mod/'. $modname.'/lib.php';
            $moddeleteuserdata = $modname.'_reset_userdata';   // Function to delete user data
            if (file_exists($modfile)) {
                include_once($modfile);
                if (function_exists($moddeleteuserdata)) {
                    $modstatus = $moddeleteuserdata($data);
                    if (is_array($modstatus)) {
                        $status = array_merge($status, $modstatus);
                    } else {
                        debugging('Module '.$modname.' returned incorrect staus - must be an array!');
                    }
                } else {
                    $unsupported_mods[] = $mod;
                }
            } else {
                debugging('Missing lib.php in '.$modname.' module!');
            }
        }
    }

    // mention unsupported mods
    if (!empty($unsupported_mods)) {
        foreach($unsupported_mods as $mod) {
            $status[] = array('component'=>get_string('modulenameplural', $mod->name), 'item'=>'', 'error'=>get_string('resetnotimplemented'));
        }
    }


    $componentstr = get_string('gradebook', 'grades');
    // reset gradebook
    if (!empty($data->reset_gradebook_items)) {
        remove_course_grades($data->courseid, false);
        grade_grab_course_grades($data->courseid);
        grade_regrade_final_grades($data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('removeallcourseitems', 'grades'), 'error'=>false);

    } else if (!empty($data->reset_gradebook_grades)) {
        grade_course_reset($data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('removeallcoursegrades', 'grades'), 'error'=>false);
    }
    // reset comments
    if (!empty($data->reset_comments)) {
        require_once($CFG->dirroot.'/comment/lib.php');
        comment::reset_course_page_comments($context);
    }

    return $status;
}

/**
 * Generate an email processing address
 *
 * @param int $modid
 * @param string $modargs
 * @return string Returns email processing address
 */
function generate_email_processing_address($modid,$modargs) {
    global $CFG;

    $header = $CFG->mailprefix . substr(base64_encode(pack('C',$modid)),0,2).$modargs;
    return $header . substr(md5($header.get_site_identifier()),0,16).'@'.$CFG->maildomain;
}

/**
 * ?
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @param string $modargs
 * @param string $body Currently unused
 */
function moodle_process_email($modargs,$body) {
    global $DB;

    // the first char should be an unencoded letter. We'll take this as an action
    switch ($modargs{0}) {
        case 'B': { // bounce
            list(,$userid) = unpack('V',base64_decode(substr($modargs,1,8)));
            if ($user = $DB->get_record("user", array('id'=>$userid), "id,email")) {
                // check the half md5 of their email
                $md5check = substr(md5($user->email),0,16);
                if ($md5check == substr($modargs, -16)) {
                    set_bounce_count($user);
                }
                // else maybe they've already changed it?
            }
        }
        break;
        // maybe more later?
    }
}

/// CORRESPONDENCE  ////////////////////////////////////////////////

/**
 * Get mailer instance, enable buffering, flush buffer or disable buffering.
 *
 * @global object
 * @param string $action 'get', 'buffer', 'close' or 'flush'
 * @return object|null mailer instance if 'get' used or nothing
 */
function get_mailer($action='get') {
    global $CFG;

    static $mailer  = null;
    static $counter = 0;

    if (!isset($CFG->smtpmaxbulk)) {
        $CFG->smtpmaxbulk = 1;
    }

    if ($action == 'get') {
        $prevkeepalive = false;

        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            if ($counter < $CFG->smtpmaxbulk and !$mailer->IsError()) {
                $counter++;
                // reset the mailer
                $mailer->Priority         = 3;
                $mailer->CharSet          = 'UTF-8'; // our default
                $mailer->ContentType      = "text/plain";
                $mailer->Encoding         = "8bit";
                $mailer->From             = "root@localhost";
                $mailer->FromName         = "Root User";
                $mailer->Sender           = "";
                $mailer->Subject          = "";
                $mailer->Body             = "";
                $mailer->AltBody          = "";
                $mailer->ConfirmReadingTo = "";

                $mailer->ClearAllRecipients();
                $mailer->ClearReplyTos();
                $mailer->ClearAttachments();
                $mailer->ClearCustomHeaders();
                return $mailer;
            }

            $prevkeepalive = $mailer->SMTPKeepAlive;
            get_mailer('flush');
        }

        include_once($CFG->libdir.'/phpmailer/moodle_phpmailer.php');
        $mailer = new moodle_phpmailer();

        $counter = 1;

        $mailer->Version   = 'Moodle '.$CFG->version;         // mailer version
        $mailer->PluginDir = $CFG->libdir.'/phpmailer/';      // plugin directory (eg smtp plugin)
        $mailer->CharSet   = 'UTF-8';

        // some MTAs may do double conversion of LF if CRLF used, CRLF is required line ending in RFC 822bis
        if (isset($CFG->mailnewline) and $CFG->mailnewline == 'CRLF') {
            $mailer->LE = "\r\n";
        } else {
            $mailer->LE = "\n";
        }

        if ($CFG->smtphosts == 'qmail') {
            $mailer->IsQmail();                              // use Qmail system

        } else if (empty($CFG->smtphosts)) {
            $mailer->IsMail();                               // use PHP mail() = sendmail

        } else {
            $mailer->IsSMTP();                               // use SMTP directly
            if (!empty($CFG->debugsmtp)) {
                $mailer->SMTPDebug = true;
            }
            $mailer->Host          = $CFG->smtphosts;        // specify main and backup servers
            $mailer->SMTPKeepAlive = $prevkeepalive;         // use previous keepalive

            if ($CFG->smtpuser) {                            // Use SMTP authentication
                $mailer->SMTPAuth = true;
                $mailer->Username = $CFG->smtpuser;
                $mailer->Password = $CFG->smtppass;
            }
        }

        return $mailer;
    }

    $nothing = null;

    // keep smtp session open after sending
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

    // close smtp session, but continue buffering
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

    // close smtp session, do not buffer anymore
    if ($action == 'close') {
        if (isset($mailer) and $mailer->Mailer == 'smtp') {
            get_mailer('flush');
            $mailer->SMTPKeepAlive = false;
        }
        $mailer = null; // better force new instance
        return $nothing;
    }
}

/**
 * Send an email to a specified user
 *
 * @global object
 * @global string
 * @global string IdentityProvider(IDP) URL user hits to jump to mnet peer.
 * @uses SITEID
 * @param stdClass $user  A {@link $USER} object
 * @param stdClass $from A {@link $USER} object
 * @param string $subject plain text subject line of the email
 * @param string $messagetext plain text version of the message
 * @param string $messagehtml complete html version of the message (optional)
 * @param string $attachment a file on the filesystem, relative to $CFG->dataroot
 * @param string $attachname the name of the file (extension indicates MIME)
 * @param bool $usetrueaddress determines whether $from email address should
 *          be sent out. Will be overruled by user profile setting for maildisplay
 * @param string $replyto Email address to reply to
 * @param string $replytoname Name of reply to recipient
 * @param int $wordwrapwidth custom word wrap width, default 79
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function email_to_user($user, $from, $subject, $messagetext, $messagehtml='', $attachment='', $attachname='', $usetrueaddress=true, $replyto='', $replytoname='', $wordwrapwidth=79) {

    global $CFG, $FULLME;

    if (empty($user) || empty($user->email)) {
        mtrace('Error: lib/moodlelib.php email_to_user(): User is null or has no email');
        return false;
    }

    if (!empty($user->deleted)) {
        // do not mail delted users
        mtrace('Error: lib/moodlelib.php email_to_user(): User is deleted');
        return false;
    }

    if (!empty($CFG->noemailever)) {
        // hidden setting for development sites, set in config.php if needed
        mtrace('Error: lib/moodlelib.php email_to_user(): Not sending email due to noemailever config setting');
        return true;
    }

    if (!empty($CFG->divertallemailsto)) {
        $subject = "[DIVERTED {$user->email}] $subject";
        $user = clone($user);
        $user->email = $CFG->divertallemailsto;
    }

    // skip mail to suspended users
    if ((isset($user->auth) && $user->auth=='nologin') or (isset($user->suspended) && $user->suspended)) {
        return true;
    }

    if (!validate_email($user->email)) {
        // we can not send emails to invalid addresses - it might create security issue or confuse the mailer
        $invalidemail = "User $user->id (".fullname($user).") email ($user->email) is invalid! Not sending.";
        error_log($invalidemail);
        if (CLI_SCRIPT) {
            // do not print this in standard web pages
            mtrace($invalidemail);
        }
        return false;
    }

    if (over_bounce_threshold($user)) {
        $bouncemsg = "User $user->id (".fullname($user).") is over bounce threshold! Not sending.";
        error_log($bouncemsg);
        mtrace('Error: lib/moodlelib.php email_to_user(): '.$bouncemsg);
        return false;
    }

    // If the user is a remote mnet user, parse the email text for URL to the
    // wwwroot and modify the url to direct the user's browser to login at their
    // home site (identity provider - idp) before hitting the link itself
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

    $supportuser = generate_email_supportuser();

    // make up an email address for handling bounces
    if (!empty($CFG->handlebounces)) {
        $modargs = 'B'.base64_encode(pack('V',$user->id)).substr(md5($user->email),0,16);
        $mail->Sender = generate_email_processing_address(0,$modargs);
    } else {
        $mail->Sender = $supportuser->email;
    }

    if (is_string($from)) { // So we can pass whatever we want if there is need
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = $from;
    } else if ($usetrueaddress and $from->maildisplay) {
        $mail->From     = $from->email;
        $mail->FromName = fullname($from);
    } else {
        $mail->From     = $CFG->noreplyaddress;
        $mail->FromName = fullname($from);
        if (empty($replyto)) {
            $tempreplyto[] = array($CFG->noreplyaddress, get_string('noreplyname'));
        }
    }

    if (!empty($replyto)) {
        $tempreplyto[] = array($replyto, $replytoname);
    }

    $mail->Subject = substr($subject, 0, 900);

    $temprecipients[] = array($user->email, fullname($user));

    $mail->WordWrap = $wordwrapwidth;                   // set word wrap

    if (!empty($from->customheaders)) {                 // Add custom headers
        if (is_array($from->customheaders)) {
            foreach ($from->customheaders as $customheader) {
                $mail->AddCustomHeader($customheader);
            }
        } else {
            $mail->AddCustomHeader($from->customheaders);
        }
    }

    if (!empty($from->priority)) {
        $mail->Priority = $from->priority;
    }

    if ($messagehtml && !empty($user->mailformat) && $user->mailformat == 1) { // Don't ever send HTML to users who don't want it
        $mail->IsHTML(true);
        $mail->Encoding = 'quoted-printable';           // Encoding to use
        $mail->Body    =  $messagehtml;
        $mail->AltBody =  "\n$messagetext\n";
    } else {
        $mail->IsHTML(false);
        $mail->Body =  "\n$messagetext\n";
    }

    if ($attachment && $attachname) {
        if (preg_match( "~\\.\\.~" ,$attachment )) {    // Security check for ".." in dir path
            $temprecipients[] = array($supportuser->email, fullname($supportuser, true));
            $mail->AddStringAttachment('Error in attachment.  User attempted to attach a filename with a unsafe name.', 'error.txt', '8bit', 'text/plain');
        } else {
            require_once($CFG->libdir.'/filelib.php');
            $mimetype = mimeinfo('type', $attachname);
            $mail->AddAttachment($CFG->dataroot .'/'. $attachment, $attachname, 'base64', $mimetype);
        }
    }

    // Check if the email should be sent in an other charset then the default UTF-8
    if ((!empty($CFG->sitemailcharset) || !empty($CFG->allowusermailcharset))) {

        // use the defined site mail charset or eventually the one preferred by the recipient
        $charset = $CFG->sitemailcharset;
        if (!empty($CFG->allowusermailcharset)) {
            if ($useremailcharset = get_user_preferences('mailcharset', '0', $user->id)) {
                $charset = $useremailcharset;
            }
        }

        // convert all the necessary strings if the charset is supported
        $charsets = get_list_of_charsets();
        unset($charsets['UTF-8']);
        if (in_array($charset, $charsets)) {
            $textlib = textlib_get_instance();
            $mail->CharSet  = $charset;
            $mail->FromName = $textlib->convert($mail->FromName, 'utf-8', strtolower($charset));
            $mail->Subject  = $textlib->convert($mail->Subject, 'utf-8', strtolower($charset));
            $mail->Body     = $textlib->convert($mail->Body, 'utf-8', strtolower($charset));
            $mail->AltBody  = $textlib->convert($mail->AltBody, 'utf-8', strtolower($charset));

            foreach ($temprecipients as $key => $values) {
                $temprecipients[$key][1] = $textlib->convert($values[1], 'utf-8', strtolower($charset));
            }
            foreach ($tempreplyto as $key => $values) {
                $tempreplyto[$key][1] = $textlib->convert($values[1], 'utf-8', strtolower($charset));
            }
        }
    }

    foreach ($temprecipients as $values) {
        $mail->AddAddress($values[0], $values[1]);
    }
    foreach ($tempreplyto as $values) {
        $mail->AddReplyTo($values[0], $values[1]);
    }

    if ($mail->Send()) {
        set_send_count($user);
        $mail->IsSMTP();                               // use SMTP directly
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        return true;
    } else {
        mtrace('ERROR: '. $mail->ErrorInfo);
        add_to_log(SITEID, 'library', 'mailer', $FULLME, 'ERROR: '. $mail->ErrorInfo);
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        return false;
    }
}

/**
 * Generate a signoff for emails based on support settings
 *
 * @global object
 * @return string
 */
function generate_email_signoff() {
    global $CFG;

    $signoff = "\n";
    if (!empty($CFG->supportname)) {
        $signoff .= $CFG->supportname."\n";
    }
    if (!empty($CFG->supportemail)) {
        $signoff .= $CFG->supportemail."\n";
    }
    if (!empty($CFG->supportpage)) {
        $signoff .= $CFG->supportpage."\n";
    }
    return $signoff;
}

/**
 * Generate a fake user for emails based on support settings
 * @global object
 * @return object user info
 */
function generate_email_supportuser() {
    global $CFG;

    static $supportuser;

    if (!empty($supportuser)) {
        return $supportuser;
    }

    $supportuser = new stdClass();
    $supportuser->email = $CFG->supportemail ? $CFG->supportemail : $CFG->noreplyaddress;
    $supportuser->firstname = $CFG->supportname ? $CFG->supportname : get_string('noreplyname');
    $supportuser->lastname = '';
    $supportuser->maildisplay = true;

    return $supportuser;
}


/**
 * Sets specified user's password and send the new password to the user via email.
 *
 * @global object
 * @global object
 * @param user $user A {@link $USER} object
 * @return boolean|string Returns "true" if mail was sent OK and "false" if there was an error
 */
function setnew_password_and_mail($user) {
    global $CFG, $DB;

    $site  = get_site();

    $supportuser = generate_email_supportuser();

    $newpassword = generate_password();

    $DB->set_field('user', 'password', hash_internal_user_password($newpassword), array('id'=>$user->id));

    $a = new stdClass();
    $a->firstname   = fullname($user, true);
    $a->sitename    = format_string($site->fullname);
    $a->username    = $user->username;
    $a->newpassword = $newpassword;
    $a->link        = $CFG->wwwroot .'/login/';
    $a->signoff     = generate_email_signoff();

    $message = get_string('newusernewpasswordtext', '', $a);

    $subject = format_string($site->fullname) .': '. get_string('newusernewpasswordsubj');

    //directly email rather than using the messaging system to ensure its not routed to a popup or jabber
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
    $supportuser = generate_email_supportuser();

    $userauth = get_auth_plugin($user->auth);
    if (!$userauth->can_reset_password() or !is_enabled_auth($user->auth)) {
        trigger_error("Attempt to reset user password for user $user->username with Auth $user->auth.");
        return false;
    }

    $newpassword = generate_password();

    if (!$userauth->user_update_password($user, $newpassword)) {
        print_error("cannotsetpassword");
    }

    $a = new stdClass();
    $a->firstname   = $user->firstname;
    $a->lastname    = $user->lastname;
    $a->sitename    = format_string($site->fullname);
    $a->username    = $user->username;
    $a->newpassword = $newpassword;
    $a->link        = $CFG->httpswwwroot .'/login/change_password.php';
    $a->signoff     = generate_email_signoff();

    $message = get_string('newpasswordtext', '', $a);

    $subject  = format_string($site->fullname) .': '. get_string('changedpassword');

    unset_user_preference('create_password', $user); // prevent cron from generating the password

    //directly email rather than using the messaging system to ensure its not routed to a popup or jabber
    return email_to_user($user, $supportuser, $subject, $message);

}

/**
 * Send email to specified user with confirmation text and activation link.
 *
 * @global object
 * @param user $user A {@link $USER} object
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
 function send_confirmation_email($user) {
    global $CFG;

    $site = get_site();
    $supportuser = generate_email_supportuser();

    $data = new stdClass();
    $data->firstname = fullname($user);
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();

    $subject = get_string('emailconfirmationsubject', '', format_string($site->fullname));

    $data->link  = $CFG->wwwroot .'/login/confirm.php?data='. $user->secret .'/'. urlencode($user->username);
    $message     = get_string('emailconfirmation', '', $data);
    $messagehtml = text_to_html(get_string('emailconfirmation', '', $data), false, false, true);

    $user->mailformat = 1;  // Always send HTML version as well

    //directly email rather than using the messaging system to ensure its not routed to a popup or jabber
    return email_to_user($user, $supportuser, $subject, $message, $messagehtml);

}

/**
 * send_password_change_confirmation_email.
 *
 * @global object
 * @param user $user A {@link $USER} object
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function send_password_change_confirmation_email($user) {
    global $CFG;

    $site = get_site();
    $supportuser = generate_email_supportuser();

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->sitename  = format_string($site->fullname);
    $data->link      = $CFG->httpswwwroot .'/login/forgot_password.php?p='. $user->secret .'&s='. urlencode($user->username);
    $data->admin     = generate_email_signoff();

    $message = get_string('emailpasswordconfirmation', '', $data);
    $subject = get_string('emailpasswordconfirmationsubject', '', format_string($site->fullname));

    //directly email rather than using the messaging system to ensure its not routed to a popup or jabber
    return email_to_user($user, $supportuser, $subject, $message);

}

/**
 * send_password_change_info.
 *
 * @global object
 * @param user $user A {@link $USER} object
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function send_password_change_info($user) {
    global $CFG;

    $site = get_site();
    $supportuser = generate_email_supportuser();
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();

    $userauth = get_auth_plugin($user->auth);

    if (!is_enabled_auth($user->auth) or $user->auth == 'nologin') {
        $message = get_string('emailpasswordchangeinfodisabled', '', $data);
        $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));
        //directly email rather than using the messaging system to ensure its not routed to a popup or jabber
        return email_to_user($user, $supportuser, $subject, $message);
    }

    if ($userauth->can_change_password() and $userauth->change_password_url()) {
        // we have some external url for password changing
        $data->link .= $userauth->change_password_url();

    } else {
        //no way to change password, sorry
        $data->link = '';
    }

    if (!empty($data->link) and has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
        $message = get_string('emailpasswordchangeinfo', '', $data);
        $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));
    } else {
        $message = get_string('emailpasswordchangeinfofail', '', $data);
        $subject = get_string('emailpasswordchangeinfosubject', '', format_string($site->fullname));
    }

    //directly email rather than using the messaging system to ensure its not routed to a popup or jabber
    return email_to_user($user, $supportuser, $subject, $message);

}

/**
 * Check that an email is allowed.  It returns an error message if there
 * was a problem.
 *
 * @global object
 * @param  string $email Content of email
 * @return string|false
 */
function email_is_not_allowed($email) {
    global $CFG;

    if (!empty($CFG->allowemailaddresses)) {
        $allowed = explode(' ', $CFG->allowemailaddresses);
        foreach ($allowed as $allowedpattern) {
            $allowedpattern = trim($allowedpattern);
            if (!$allowedpattern) {
                continue;
            }
            if (strpos($allowedpattern, '.') === 0) {
                if (strpos(strrev($email), strrev($allowedpattern)) === 0) {
                    // subdomains are in a form ".example.com" - matches "xxx@anything.example.com"
                    return false;
                }

            } else if (strpos(strrev($email), strrev('@'.$allowedpattern)) === 0) { // Match!   (bug 5250)
                return false;
            }
        }
        return get_string('emailonlyallowed', '', $CFG->allowemailaddresses);

    } else if (!empty($CFG->denyemailaddresses)) {
        $denied = explode(' ', $CFG->denyemailaddresses);
        foreach ($denied as $deniedpattern) {
            $deniedpattern = trim($deniedpattern);
            if (!$deniedpattern) {
                continue;
            }
            if (strpos($deniedpattern, '.') === 0) {
                if (strpos(strrev($email), strrev($deniedpattern)) === 0) {
                    // subdomains are in a form ".example.com" - matches "xxx@anything.example.com"
                    return get_string('emailnotallowed', '', $CFG->denyemailaddresses);
                }

            } else if (strpos(strrev($email), strrev('@'.$deniedpattern)) === 0) { // Match!   (bug 5250)
                return get_string('emailnotallowed', '', $CFG->denyemailaddresses);
            }
        }
    }

    return false;
}

/// FILE HANDLING  /////////////////////////////////////////////

/**
 * Returns local file storage instance
 *
 * @return file_storage
 */
function get_file_storage() {
    global $CFG;

    static $fs = null;

    if ($fs) {
        return $fs;
    }

    require_once("$CFG->libdir/filelib.php");

    if (isset($CFG->filedir)) {
        $filedir = $CFG->filedir;
    } else {
        $filedir = $CFG->dataroot.'/filedir';
    }

    if (isset($CFG->trashdir)) {
        $trashdirdir = $CFG->trashdir;
    } else {
        $trashdirdir = $CFG->dataroot.'/trashdir';
    }

    $fs = new file_storage($filedir, $trashdirdir, "$CFG->dataroot/temp/filestorage", $CFG->directorypermissions, $CFG->filepermissions);

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

    static $fp = array();;

    if (isset($fp[$mimetype])) {
        return $fp[$mimetype];
    }

    switch ($mimetype) {
        case 'application/zip':
        case 'application/vnd.moodle.backup':
            $classname = 'zip_packer';
            break;
        case 'application/x-tar':
//            $classname = 'tar_packer';
//            break;
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
 * @param int $sizebytes Set maximum size
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @return int The maximum size for uploading files.
 */
function get_max_upload_file_size($sitebytes=0, $coursebytes=0, $modulebytes=0) {

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

    if ($sitebytes and $sitebytes < $minimumsize) {
        $minimumsize = $sitebytes;
    }

    if ($coursebytes and $coursebytes < $minimumsize) {
        $minimumsize = $coursebytes;
    }

    if ($modulebytes and $modulebytes < $minimumsize) {
        $minimumsize = $modulebytes;
    }

    return $minimumsize;
}

/**
 * Returns an array of possible sizes in local language
 *
 * Related to {@link get_max_upload_file_size()} - this function returns an
 * array of possible sizes in an array, translated to the
 * local language.
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @uses SORT_NUMERIC
 * @param int $sizebytes Set maximum size
 * @param int $coursebytes Current course $course->maxbytes (in bytes)
 * @param int $modulebytes Current module ->maxbytes (in bytes)
 * @return array
 */
function get_max_upload_sizes($sitebytes=0, $coursebytes=0, $modulebytes=0) {
    global $CFG;

    if (!$maxsize = get_max_upload_file_size($sitebytes, $coursebytes, $modulebytes)) {
        return array();
    }

    $filesize[intval($maxsize)] = display_size($maxsize);

    $sizelist = array(10240, 51200, 102400, 512000, 1048576, 2097152,
                      5242880, 10485760, 20971520, 52428800, 104857600);

    // Allow maxbytes to be selected if it falls outside the above boundaries
    if (isset($CFG->maxbytes) && !in_array(get_real_size($CFG->maxbytes), $sizelist)) {
        // note: get_real_size() is used in order to prevent problems with invalid values
        $sizelist[] = get_real_size($CFG->maxbytes);
    }

    foreach ($sizelist as $sizebytes) {
       if ($sizebytes < $maxsize) {
           $filesize[intval($sizebytes)] = display_size($sizebytes);
       }
    }

    krsort($filesize, SORT_NUMERIC);

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
 * @param string|array $excludefile If defined then the specified file/directory is ignored
 * @param bool $descend If true then subdirectories are recursed as well
 * @param bool $getdirs If true then (sub)directories are included in the output
 * @param bool $getfiles  If true then files are included in the output
 * @return array An array with all the filenames in
 * all subdirectories, relative to the given rootdir
 */
function get_directory_list($rootdir, $excludefiles='', $descend=true, $getdirs=false, $getfiles=true) {

    $dirs = array();

    if (!$getdirs and !$getfiles) {   // Nothing to show
        return $dirs;
    }

    if (!is_dir($rootdir)) {          // Must be a directory
        return $dirs;
    }

    if (!$dir = opendir($rootdir)) {  // Can't open it for some reason
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
 * @todo Finish documenting this function
 *
 * @param string $rootdir  The directory to start from
 * @param string $excludefile A file to exclude when summing directory size
 * @return int The summed size of all files and subfiles within the root directory
 */
function get_directory_size($rootdir, $excludefile='') {
    global $CFG;

    // do it this way if we can, it's much faster
    if (!empty($CFG->pathtodu) && is_executable(trim($CFG->pathtodu))) {
        $command = trim($CFG->pathtodu).' -sk '.escapeshellarg($rootdir);
        $output = null;
        $return = null;
        exec($command,$output,$return);
        if (is_array($output)) {
            return get_real_size(intval($output[0]).'k'); // we told it to return k.
        }
    }

    if (!is_dir($rootdir)) {          // Must be a directory
        return 0;
    }

    if (!$dir = @opendir($rootdir)) {  // Can't open it for some reason
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
 * @todo Finish documenting this function. Verify return type.
 *
 * @staticvar string $gb Localized string for size in gigabytes
 * @staticvar string $mb Localized string for size in megabytes
 * @staticvar string $kb Localized string for size in kilobytes
 * @staticvar string $b Localized string for size in bytes
 * @param int $size  The size to convert to human readable form
 * @return string
 */
function display_size($size) {

    static $gb, $mb, $kb, $b;

    if (empty($gb)) {
        $gb = get_string('sizegb');
        $mb = get_string('sizemb');
        $kb = get_string('sizekb');
        $b  = get_string('sizeb');
    }

    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 10) / 10 . $gb;
    } else if ($size >= 1048576) {
        $size = round($size / 1048576 * 10) / 10 . $mb;
    } else if ($size >= 1024) {
        $size = round($size / 1024 * 10) / 10 . $kb;
    } else {
        $size = intval($size) .' '. $b; // file sizes over 2GB can not work in 32bit PHP anyway
    }
    return $size;
}

/**
 * Cleans a given filename by removing suspicious or troublesome characters
 * @see clean_param()
 *
 * @uses PARAM_FILE
 * @param string $string  file name
 * @return string cleaned file name
 */
function clean_filename($string) {
    return clean_param($string, PARAM_FILE);
}


/// STRING TRANSLATION  ////////////////////////////////////////

/**
 * Returns the code for the current language
 *
 * @return string
 */
function current_language() {
    global $CFG, $USER, $SESSION, $COURSE;

    if (!empty($COURSE->id) and $COURSE->id != SITEID and !empty($COURSE->lang)) {    // Course language can override all other settings for this page
        $return = $COURSE->lang;

    } else if (!empty($SESSION->lang)) {    // Session language can override other settings
        $return = $SESSION->lang;

    } else if (!empty($USER->lang)) {
        $return = $USER->lang;

    } else if (isset($CFG->lang)) {
        $return = $CFG->lang;

    } else {
        $return = 'en';
    }

    $return = str_replace('_utf8', '', $return);  // Just in case this slipped in from somewhere by accident

    return $return;
}

/**
 * Returns parent language of current active language if defined
 *
 * @uses COURSE
 * @uses SESSION
 * @param string $lang null means current language
 * @return string
 */
function get_parent_language($lang=null) {
    global $COURSE, $SESSION;

    //let's hack around the current language
    if (!empty($lang)) {
        $old_course_lang  = empty($COURSE->lang) ? '' : $COURSE->lang;
        $old_session_lang = empty($SESSION->lang) ? '' : $SESSION->lang;
        $COURSE->lang  = '';
        $SESSION->lang = $lang;
    }

    $parentlang = get_string('parentlanguage', 'langconfig');
    if ($parentlang === 'en') {
        $parentlang = '';
    }

    //let's hack around the current language
    if (!empty($lang)) {
        $COURSE->lang  = $old_course_lang;
        $SESSION->lang = $old_session_lang;
    }

    return $parentlang;
}

/**
 * Returns current string_manager instance.
 *
 * The param $forcereload is needed for CLI installer only where the string_manager instance
 * must be replaced during the install.php script life time.
 *
 * @param bool $forcereload shall the singleton be released and new instance created instead?
 * @return string_manager
 */
function get_string_manager($forcereload=false) {
    global $CFG;

    static $singleton = null;

    if ($forcereload) {
        $singleton = null;
    }
    if ($singleton === null) {
        if (empty($CFG->early_install_lang)) {

            if (empty($CFG->langcacheroot)) {
                $langcacheroot = $CFG->dataroot . '/cache/lang';
            } else {
                $langcacheroot = $CFG->langcacheroot;
            }

            if (empty($CFG->langlist)) {
                 $translist = array();
            } else {
                $translist = explode(',', $CFG->langlist);
            }

            if (empty($CFG->langmenucachefile)) {
                $langmenucache = $CFG->dataroot . '/cache/languages';
            } else {
                $langmenucache = $CFG->langmenucachefile;
            }

            $singleton = new core_string_manager($CFG->langotherroot, $CFG->langlocalroot, $langcacheroot,
                                                 !empty($CFG->langstringcache), $translist, $langmenucache);

        } else {
            $singleton = new install_string_manager();
        }
    }

    return $singleton;
}


/**
 * Interface describing class which is responsible for getting
 * of localised strings from language packs.
 *
 * @package    moodlecore
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface string_manager {
    /**
     * Get String returns a requested string
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @param string|object|array $a An object, string or number that can be used
     *      within translation strings
     * @param string $lang moodle translation language, NULL means use current
     * @return string The String !
     */
    public function get_string($identifier, $component = '', $a = NULL, $lang = NULL);

    /**
     * Does the string actually exist?
     *
     * get_string() is throwing debug warnings, sometimes we do not want them
     * or we want to display better explanation of the problem.
     *
     * Use with care!
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @return boot true if exists
     */
    public function string_exists($identifier, $component);

    /**
     * Returns a localised list of all country names, sorted by country keys.
     * @param bool $returnall return all or just enabled
     * @param string $lang moodle translation language, NULL means use current
     * @return array two-letter country code => translated name.
     */
    public function get_list_of_countries($returnall = false, $lang = NULL);

    /**
     * Returns a localised list of languages, sorted by code keys.
     *
     * @param string $lang moodle translation language, NULL means use current
     * @param string $standard language list standard
     *                     iso6392: three-letter language code (ISO 639-2/T) => translated name.
     * @return array language code => translated name
     */
    public function get_list_of_languages($lang = NULL, $standard = 'iso6392');

    /**
     * Does the translation exist?
     *
     * @param string $lang moodle translation language code
     * @param bool include also disabled translations?
     * @return boot true if exists
     */
    public function translation_exists($lang, $includeall = true);

    /**
     * Returns localised list of installed translations
     * @param bool $returnall return all or just enabled
     * @return array moodle translation code => localised translation name
     */
    public function get_list_of_translations($returnall = false);

    /**
     * Returns localised list of currencies.
     *
     * @param string $lang moodle translation language, NULL means use current
     * @return array currency code => localised currency name
     */
    public function get_list_of_currencies($lang = NULL);

    /**
     * Load all strings for one component
     * @param string $component The module the string is associated with
     * @param string $lang
     * @param bool $disablecache Do not use caches, force fetching the strings from sources
     * @param bool $disablelocal Do not use customized strings in xx_local language packs
     * @return array of all string for given component and lang
     */
    public function load_component_strings($component, $lang, $disablecache=false, $disablelocal=false);

    /**
     * Invalidates all caches, should the implementation use any
     */
    public function reset_caches();
}


/**
 * Standard string_manager implementation
 *
 * @package    moodlecore
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_string_manager implements string_manager {
    /** @var string location of all packs except 'en' */
    protected $otherroot;
    /** @var string location of all lang pack local modifications */
    protected $localroot;
    /** @var string location of on-disk cache of merged strings */
    protected $cacheroot;
    /** @var array lang string cache - it will be optimised more later */
    protected $cache = array();
    /** @var int get_string() counter */
    protected $countgetstring = 0;
    /** @var int in-memory cache hits counter */
    protected $countmemcache = 0;
    /** @var int on-disk cache hits counter */
    protected $countdiskcache = 0;
    /** @var bool use disk cache */
    protected $usediskcache;
    /* @var array limit list of translations */
    protected $translist;
    /** @var string location of a file that caches the list of available translations */
    protected $menucache;

    /**
     * Create new instance of string manager
     *
     * @param string $otherroot location of downlaoded lang packs - usually $CFG->dataroot/lang
     * @param string $localroot usually the same as $otherroot
     * @param string $cacheroot usually lang dir in cache folder
     * @param bool $usediskcache use disk cache
     * @param array $translist limit list of visible translations
     * @param string $menucache the location of a file that caches the list of available translations
     */
    public function __construct($otherroot, $localroot, $cacheroot, $usediskcache, $translist, $menucache) {
        $this->otherroot    = $otherroot;
        $this->localroot    = $localroot;
        $this->cacheroot    = $cacheroot;
        $this->usediskcache = $usediskcache;
        $this->translist    = $translist;
        $this->menucache    = $menucache;
    }

    /**
     * Returns dependencies of current language, en is not included.
     * @param string $lang
     * @return array all parents, the lang itself is last
     */
    public function get_language_dependencies($lang) {
        if ($lang === 'en') {
            return array();
        }
        if (!file_exists("$this->otherroot/$lang/langconfig.php")) {
            return array();
        }
        $string = array();
        include("$this->otherroot/$lang/langconfig.php");

        if (empty($string['parentlanguage'])) {
            return array($lang);
        } else {
            $parentlang = $string['parentlanguage'];
            unset($string);
            return array_merge($this->get_language_dependencies($parentlang), array($lang));
        }
    }

    /**
     * Load all strings for one component
     * @param string $component The module the string is associated with
     * @param string $lang
     * @param bool $disablecache Do not use caches, force fetching the strings from sources
     * @param bool $disablelocal Do not use customized strings in xx_local language packs
     * @return array of all string for given component and lang
     */
    public function load_component_strings($component, $lang, $disablecache=false, $disablelocal=false) {
        global $CFG;

        list($plugintype, $pluginname) = normalize_component($component);
        if ($plugintype == 'core' and is_null($pluginname)) {
            $component = 'core';
        } else {
            $component = $plugintype . '_' . $pluginname;
        }

        if (!$disablecache) {
            // try in-memory cache first
            if (isset($this->cache[$lang][$component])) {
                $this->countmemcache++;
                return $this->cache[$lang][$component];
            }

            // try on-disk cache then
            if ($this->usediskcache and file_exists($this->cacheroot . "/$lang/$component.php")) {
                $this->countdiskcache++;
                include($this->cacheroot . "/$lang/$component.php");
                return $this->cache[$lang][$component];
            }
        }

        // no cache found - let us merge all possible sources of the strings
        if ($plugintype === 'core') {
            $file = $pluginname;
            if ($file === null) {
                $file = 'moodle';
            }
            $string = array();
            // first load english pack
            if (!file_exists("$CFG->dirroot/lang/en/$file.php")) {
                return array();
            }
            include("$CFG->dirroot/lang/en/$file.php");
            $originalkeys = array_keys($string);
            $originalkeys = array_flip($originalkeys);

            // and then corresponding local if present and allowed
            if (!$disablelocal and file_exists("$this->localroot/en_local/$file.php")) {
                include("$this->localroot/en_local/$file.php");
            }
            // now loop through all langs in correct order
            $deps = $this->get_language_dependencies($lang);
            foreach ($deps as $dep) {
                // the main lang string location
                if (file_exists("$this->otherroot/$dep/$file.php")) {
                    include("$this->otherroot/$dep/$file.php");
                }
                if (!$disablelocal and file_exists("$this->localroot/{$dep}_local/$file.php")) {
                    include("$this->localroot/{$dep}_local/$file.php");
                }
            }

        } else {
            if (!$location = get_plugin_directory($plugintype, $pluginname) or !is_dir($location)) {
                return array();
            }
            if ($plugintype === 'mod') {
                // bloody mod hack
                $file = $pluginname;
            } else {
                $file = $plugintype . '_' . $pluginname;
            }
            $string = array();
            // first load English pack
            if (!file_exists("$location/lang/en/$file.php")) {
                //English pack does not exist, so do not try to load anything else
                return array();
            }
            include("$location/lang/en/$file.php");
            $originalkeys = array_keys($string);
            $originalkeys = array_flip($originalkeys);
            // and then corresponding local english if present
            if (!$disablelocal and file_exists("$this->localroot/en_local/$file.php")) {
                include("$this->localroot/en_local/$file.php");
            }

            // now loop through all langs in correct order
            $deps = $this->get_language_dependencies($lang);
            foreach ($deps as $dep) {
                // legacy location - used by contrib only
                if (file_exists("$location/lang/$dep/$file.php")) {
                    include("$location/lang/$dep/$file.php");
                }
                // the main lang string location
                if (file_exists("$this->otherroot/$dep/$file.php")) {
                    include("$this->otherroot/$dep/$file.php");
                }
                // local customisations
                if (!$disablelocal and file_exists("$this->localroot/{$dep}_local/$file.php")) {
                    include("$this->localroot/{$dep}_local/$file.php");
                }
            }
        }

        // we do not want any extra strings from other languages - everything must be in en lang pack
        $string = array_intersect_key($string, $originalkeys);

        // now we have a list of strings from all possible sources. put it into both in-memory and on-disk
        // caches so we do not need to do all this merging and dependencies resolving again
        $this->cache[$lang][$component] = $string;
        if ($this->usediskcache) {
            check_dir_exists("$this->cacheroot/$lang");
            file_put_contents("$this->cacheroot/$lang/$component.php", "<?php \$this->cache['$lang']['$component'] = ".var_export($string, true).";");
        }
        return $string;
    }

    /**
     * Does the string actually exist?
     *
     * get_string() is throwing debug warnings, sometimes we do not want them
     * or we want to display better explanation of the problem.
     *
     * Use with care!
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @return boot true if exists
     */
    public function string_exists($identifier, $component) {
       $identifier = clean_param($identifier, PARAM_STRINGID);
        if (empty($identifier)) {
            return false;
        }
        $lang = current_language();
        $string = $this->load_component_strings($component, $lang);
        return isset($string[$identifier]);
    }

    /**
     * Get String returns a requested string
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @param string|object|array $a An object, string or number that can be used
     *      within translation strings
     * @param string $lang moodle translation language, NULL means use current
     * @return string The String !
     */
    public function get_string($identifier, $component = '', $a = NULL, $lang = NULL) {
        $this->countgetstring++;
        // there are very many uses of these time formating strings without the 'langconfig' component,
        // it would not be reasonable to expect that all of them would be converted during 2.0 migration
        static $langconfigstrs = array(
                'strftimedate' => 1,
                'strftimedatefullshort' => 1,
                'strftimedateshort' => 1,
                'strftimedatetime' => 1,
                'strftimedatetimeshort' => 1,
                'strftimedaydate' => 1,
                'strftimedaydatetime' => 1,
                'strftimedayshort' => 1,
                'strftimedaytime' => 1,
                'strftimemonthyear' => 1,
                'strftimerecent' => 1,
                'strftimerecentfull' => 1,
                'strftimetime' => 1);

        if (empty($component)) {
            if (isset($langconfigstrs[$identifier])) {
                $component = 'langconfig';
            } else {
                $component = 'moodle';
            }
        }

        if ($lang === NULL) {
            $lang = current_language();
        }

        $string = $this->load_component_strings($component, $lang);

        if (!isset($string[$identifier])) {
            if ($component === 'pix' or $component === 'core_pix') {
                // this component contains only alt tags for emoticons,
                // not all of them are supposed to be defined
                return '';
            }
            if ($identifier === 'parentlanguage' and ($component === 'langconfig' or $component === 'core_langconfig')) {
                // parentlanguage is a special string, undefined means use English if not defined
                return 'en';
            }
            if ($this->usediskcache) {
                // maybe the on-disk cache is dirty - let the last attempt be to find the string in original sources
                $string = $this->load_component_strings($component, $lang, true);
            }
            if (!isset($string[$identifier])) {
                // the string is still missing - should be fixed by developer
                debugging("Invalid get_string() identifier: '$identifier' or component '$component'", DEBUG_DEVELOPER);
                return "[[$identifier]]";
            }
        }

        $string = $string[$identifier];

        if ($a !== NULL) {
            if (is_object($a) or is_array($a)) {
                $a = (array)$a;
                $search = array();
                $replace = array();
                foreach ($a as $key=>$value) {
                    if (is_int($key)) {
                        // we do not support numeric keys - sorry!
                        continue;
                    }
                    if (is_object($value) or is_array($value)) {
                        // we support just string as value
                        continue;
                    }
                    $search[]  = '{$a->'.$key.'}';
                    $replace[] = (string)$value;
                }
                if ($search) {
                    $string = str_replace($search, $replace, $string);
                }
            } else {
                $string = str_replace('{$a}', (string)$a, $string);
            }
        }

        return $string;
    }

    /**
     * Returns information about the string_manager performance
     * @return array
     */
    public function get_performance_summary() {
        return array(array(
            'langcountgetstring' => $this->countgetstring,
            'langcountmemcache' => $this->countmemcache,
            'langcountdiskcache' => $this->countdiskcache,
        ), array(
            'langcountgetstring' => 'get_string calls',
            'langcountmemcache' => 'strings mem cache hits',
            'langcountdiskcache' => 'strings disk cache hits',
        ));
    }

    /**
     * Returns a localised list of all country names, sorted by localised name.
     *
     * @param bool $returnall return all or just enabled
     * @param string $lang moodle translation language, NULL means use current
     * @return array two-letter country code => translated name.
     */
    public function get_list_of_countries($returnall = false, $lang = NULL) {
        global $CFG;

        if ($lang === NULL) {
            $lang = current_language();
        }

        $countries = $this->load_component_strings('core_countries', $lang);
        textlib_get_instance()->asort($countries);
        if (!$returnall and !empty($CFG->allcountrycodes)) {
            $enabled = explode(',', $CFG->allcountrycodes);
            $return = array();
            foreach ($enabled as $c) {
                if (isset($countries[$c])) {
                    $return[$c] = $countries[$c];
                }
            }
            return $return;
        }

        return $countries;
    }

    /**
     * Returns a localised list of languages, sorted by code keys.
     *
     * @param string $lang moodle translation language, NULL means use current
     * @param string $standard language list standard
     *    - iso6392: three-letter language code (ISO 639-2/T) => translated name
     *    - iso6391: two-letter langauge code (ISO 639-1) => translated name
     * @return array language code => translated name
     */
    public function get_list_of_languages($lang = NULL, $standard = 'iso6391') {
        if ($lang === NULL) {
            $lang = current_language();
        }

        if ($standard === 'iso6392') {
            $langs = $this->load_component_strings('core_iso6392', $lang);
            ksort($langs);
            return $langs;

        } else if ($standard === 'iso6391') {
            $langs2 = $this->load_component_strings('core_iso6392', $lang);
            static $mapping = array('aar' => 'aa', 'abk' => 'ab', 'afr' => 'af', 'aka' => 'ak', 'sqi' => 'sq', 'amh' => 'am', 'ara' => 'ar', 'arg' => 'an', 'hye' => 'hy',
                'asm' => 'as', 'ava' => 'av', 'ave' => 'ae', 'aym' => 'ay', 'aze' => 'az', 'bak' => 'ba', 'bam' => 'bm', 'eus' => 'eu', 'bel' => 'be', 'ben' => 'bn', 'bih' => 'bh',
                'bis' => 'bi', 'bos' => 'bs', 'bre' => 'br', 'bul' => 'bg', 'mya' => 'my', 'cat' => 'ca', 'cha' => 'ch', 'che' => 'ce', 'zho' => 'zh', 'chu' => 'cu', 'chv' => 'cv',
                'cor' => 'kw', 'cos' => 'co', 'cre' => 'cr', 'ces' => 'cs', 'dan' => 'da', 'div' => 'dv', 'nld' => 'nl', 'dzo' => 'dz', 'eng' => 'en', 'epo' => 'eo', 'est' => 'et',
                'ewe' => 'ee', 'fao' => 'fo', 'fij' => 'fj', 'fin' => 'fi', 'fra' => 'fr', 'fry' => 'fy', 'ful' => 'ff', 'kat' => 'ka', 'deu' => 'de', 'gla' => 'gd', 'gle' => 'ga',
                'glg' => 'gl', 'glv' => 'gv', 'ell' => 'el', 'grn' => 'gn', 'guj' => 'gu', 'hat' => 'ht', 'hau' => 'ha', 'heb' => 'he', 'her' => 'hz', 'hin' => 'hi', 'hmo' => 'ho',
                'hrv' => 'hr', 'hun' => 'hu', 'ibo' => 'ig', 'isl' => 'is', 'ido' => 'io', 'iii' => 'ii', 'iku' => 'iu', 'ile' => 'ie', 'ina' => 'ia', 'ind' => 'id', 'ipk' => 'ik',
                'ita' => 'it', 'jav' => 'jv', 'jpn' => 'ja', 'kal' => 'kl', 'kan' => 'kn', 'kas' => 'ks', 'kau' => 'kr', 'kaz' => 'kk', 'khm' => 'km', 'kik' => 'ki', 'kin' => 'rw',
                'kir' => 'ky', 'kom' => 'kv', 'kon' => 'kg', 'kor' => 'ko', 'kua' => 'kj', 'kur' => 'ku', 'lao' => 'lo', 'lat' => 'la', 'lav' => 'lv', 'lim' => 'li', 'lin' => 'ln',
                'lit' => 'lt', 'ltz' => 'lb', 'lub' => 'lu', 'lug' => 'lg', 'mkd' => 'mk', 'mah' => 'mh', 'mal' => 'ml', 'mri' => 'mi', 'mar' => 'mr', 'msa' => 'ms', 'mlg' => 'mg',
                'mlt' => 'mt', 'mon' => 'mn', 'nau' => 'na', 'nav' => 'nv', 'nbl' => 'nr', 'nde' => 'nd', 'ndo' => 'ng', 'nep' => 'ne', 'nno' => 'nn', 'nob' => 'nb', 'nor' => 'no',
                'nya' => 'ny', 'oci' => 'oc', 'oji' => 'oj', 'ori' => 'or', 'orm' => 'om', 'oss' => 'os', 'pan' => 'pa', 'fas' => 'fa', 'pli' => 'pi', 'pol' => 'pl', 'por' => 'pt',
                'pus' => 'ps', 'que' => 'qu', 'roh' => 'rm', 'ron' => 'ro', 'run' => 'rn', 'rus' => 'ru', 'sag' => 'sg', 'san' => 'sa', 'sin' => 'si', 'slk' => 'sk', 'slv' => 'sl',
                'sme' => 'se', 'smo' => 'sm', 'sna' => 'sn', 'snd' => 'sd', 'som' => 'so', 'sot' => 'st', 'spa' => 'es', 'srd' => 'sc', 'srp' => 'sr', 'ssw' => 'ss', 'sun' => 'su',
                'swa' => 'sw', 'swe' => 'sv', 'tah' => 'ty', 'tam' => 'ta', 'tat' => 'tt', 'tel' => 'te', 'tgk' => 'tg', 'tgl' => 'tl', 'tha' => 'th', 'bod' => 'bo', 'tir' => 'ti',
                'ton' => 'to', 'tsn' => 'tn', 'tso' => 'ts', 'tuk' => 'tk', 'tur' => 'tr', 'twi' => 'tw', 'uig' => 'ug', 'ukr' => 'uk', 'urd' => 'ur', 'uzb' => 'uz', 'ven' => 've',
                'vie' => 'vi', 'vol' => 'vo', 'cym' => 'cy', 'wln' => 'wa', 'wol' => 'wo', 'xho' => 'xh', 'yid' => 'yi', 'yor' => 'yo', 'zha' => 'za', 'zul' => 'zu');
            $langs1 = array();
            foreach ($mapping as $c2=>$c1) {
                $langs1[$c1] = $langs2[$c2];
            }
            ksort($langs1);
            return $langs1;

        } else {
            debugging('Unsupported $standard parameter in get_list_of_languages() method: '.$standard);
        }

        return array();
    }

    /**
     * Does the translation exist?
     *
     * @param string $lang moodle translation language code
     * @param bool include also disabled translations?
     * @return boot true if exists
     */
    public function translation_exists($lang, $includeall = true) {

        if (strpos($lang, '_local') !== false) {
            // _local packs are not real translations
            return false;
        }
        if (!$includeall and !empty($this->translist)) {
            if (!in_array($lang, $this->translist)) {
                return false;
            }
        }
        if ($lang === 'en') {
            // part of distribution
            return true;
        }
        return file_exists("$this->otherroot/$lang/langconfig.php");
    }

    /**
     * Returns localised list of installed translations
     * @param bool $returnall return all or just enabled
     * @return array moodle translation code => localised translation name
     */
    public function get_list_of_translations($returnall = false) {
        global $CFG;

        $languages = array();

        if (!empty($CFG->langcache) and is_readable($this->menucache)) {
            // try to re-use the cached list of all available languages
            $cachedlist = json_decode(file_get_contents($this->menucache), true);

            if (is_array($cachedlist) and !empty($cachedlist)) {
                // the cache file is restored correctly

                if (!$returnall and !empty($this->translist)) {
                    // return just enabled translations
                    foreach ($cachedlist as $langcode => $langname) {
                        if (in_array($langcode, $this->translist)) {
                            $languages[$langcode] = $langname;
                        }
                    }
                    return $languages;

                } else {
                    // return all translations
                    return $cachedlist;
                }
            }
        }

        // the cached list of languages is not available, let us populate the list

        if (!$returnall and !empty($this->translist)) {
            // return only some translations
            foreach ($this->translist as $lang) {
                $lang = trim($lang);   //Just trim spaces to be a bit more permissive
                if (strstr($lang, '_local') !== false) {
                    continue;
                }
                if (strstr($lang, '_utf8') !== false) {
                    continue;
                }
                if ($lang !== 'en' and !file_exists("$this->otherroot/$lang/langconfig.php")) {
                    // some broken or missing lang - can not switch to it anyway
                    continue;
                }
                $string = $this->load_component_strings('langconfig', $lang);
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'].' ('. $lang .')';
                }
                unset($string);
            }

        } else {
            // return all languages available in system
            $langdirs = get_list_of_plugins('', '', $this->otherroot);

            $langdirs = array_merge($langdirs, array("$CFG->dirroot/lang/en"=>'en'));
            // Sort all

            // Loop through all langs and get info
            foreach ($langdirs as $lang) {
                if (strstr($lang, '_local') !== false) {
                    continue;
                }
                if (strstr($lang, '_utf8') !== false) {
                    continue;
                }
                $string = $this->load_component_strings('langconfig', $lang);
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'].' ('. $lang .')';
                }
                unset($string);
            }

            if (!empty($CFG->langcache) and !empty($this->menucache)) {
                // cache the list so that it can be used next time
                textlib_get_instance()->asort($languages);
                file_put_contents($this->menucache, json_encode($languages));
            }
        }

        textlib_get_instance()->asort($languages);

        return $languages;
    }

    /**
     * Returns localised list of currencies.
     *
     * @param string $lang moodle translation language, NULL means use current
     * @return array currency code => localised currency name
     */
    public function get_list_of_currencies($lang = NULL) {
        if ($lang === NULL) {
            $lang = current_language();
        }

        $currencies = $this->load_component_strings('core_currencies', $lang);
        asort($currencies);

        return $currencies;
    }

    /**
     * Clears both in-memory and on-disk caches
     */
    public function reset_caches() {
        global $CFG;
        require_once("$CFG->libdir/filelib.php");

        // clear the on-disk disk with aggregated string files
        fulldelete($this->cacheroot);

        // clear the in-memory cache of loaded strings
        $this->cache = array();

        // clear the cache containing the list of available translations
        // and re-populate it again
        fulldelete($this->menucache);
        $this->get_list_of_translations(true);
    }
}


/**
 * Minimalistic string fetching implementation
 * that is used in installer before we fetch the wanted
 * language pack from moodle.org lang download site.
 *
 * @package    moodlecore
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class install_string_manager implements string_manager {
    /** @var string location of pre-install packs for all langs */
    protected $installroot;

    /**
     * Crate new instance of install string manager
     */
    public function __construct() {
        global $CFG;
        $this->installroot = "$CFG->dirroot/install/lang";
    }

    /**
     * Load all strings for one component
     * @param string $component The module the string is associated with
     * @param string $lang
     * @param bool $disablecache Do not use caches, force fetching the strings from sources
     * @param bool $disablelocal Do not use customized strings in xx_local language packs
     * @return array of all string for given component and lang
     */
    public function load_component_strings($component, $lang, $disablecache=false, $disablelocal=false) {
        // not needed in installer
        return array();
    }

    /**
     * Does the string actually exist?
     *
     * get_string() is throwing debug warnings, sometimes we do not want them
     * or we want to display better explanation of the problem.
     *
     * Use with care!
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @return boot true if exists
     */
    public function string_exists($identifier, $component) {
        $identifier = clean_param($identifier, PARAM_STRINGID);
        if (empty($identifier)) {
            return false;
        }
        // simple old style hack ;)
        $str = get_string($identifier, $component);
        return (strpos($str, '[[') === false);
    }

    /**
     * Get String returns a requested string
     *
     * @param string $identifier The identifier of the string to search for
     * @param string $component The module the string is associated with
     * @param string|object|array $a An object, string or number that can be used
     *      within translation strings
     * @param string $lang moodle translation language, NULL means use current
     * @return string The String !
     */
    public function get_string($identifier, $component = '', $a = NULL, $lang = NULL) {
        if (!$component) {
            $component = 'moodle';
        }

        if ($lang === NULL) {
            $lang = current_language();
        }

        //get parent lang
        $parent = '';
        if ($lang !== 'en' and $identifier !== 'parentlanguage' and $component !== 'langconfig') {
            if (file_exists("$this->installroot/$lang/langconfig.php")) {
                $string = array();
                include("$this->installroot/$lang/langconfig.php");
                if (isset($string['parentlanguage'])) {
                    $parent = $string['parentlanguage'];
                }
                unset($string);
            }
        }

        // include en string first
        if (!file_exists("$this->installroot/en/$component.php")) {
            return "[[$identifier]]";
        }
        $string = array();
        include("$this->installroot/en/$component.php");

        // now override en with parent if defined
        if ($parent and $parent !== 'en' and file_exists("$this->installroot/$parent/$component.php")) {
            include("$this->installroot/$parent/$component.php");
        }

        // finally override with requested language
        if ($lang !== 'en' and file_exists("$this->installroot/$lang/$component.php")) {
            include("$this->installroot/$lang/$component.php");
        }

        if (!isset($string[$identifier])) {
            return "[[$identifier]]";
        }

        $string = $string[$identifier];

        if ($a !== NULL) {
            if (is_object($a) or is_array($a)) {
                $a = (array)$a;
                $search = array();
                $replace = array();
                foreach ($a as $key=>$value) {
                    if (is_int($key)) {
                        // we do not support numeric keys - sorry!
                        continue;
                    }
                    $search[]  = '{$a->'.$key.'}';
                    $replace[] = (string)$value;
                }
                if ($search) {
                    $string = str_replace($search, $replace, $string);
                }
            } else {
                $string = str_replace('{$a}', (string)$a, $string);
            }
        }

        return $string;
    }

    /**
     * Returns a localised list of all country names, sorted by country keys.
     *
     * @param bool $returnall return all or just enabled
     * @param string $lang moodle translation language, NULL means use current
     * @return array two-letter country code => translated name.
     */
    public function get_list_of_countries($returnall = false, $lang = NULL) {
        //not used in installer
        return array();
    }

    /**
     * Returns a localised list of languages, sorted by code keys.
     *
     * @param string $lang moodle translation language, NULL means use current
     * @param string $standard language list standard
     *                     iso6392: three-letter language code (ISO 639-2/T) => translated name.
     * @return array language code => translated name
     */
    public function get_list_of_languages($lang = NULL, $standard = 'iso6392') {
        //not used in installer
        return array();
    }

    /**
     * Does the translation exist?
     *
     * @param string $lang moodle translation language code
     * @param bool include also disabled translations?
     * @return boot true if exists
     */
    public function translation_exists($lang, $includeall = true) {
        return file_exists($this->installroot.'/'.$lang.'/langconfig.php');
    }

    /**
     * Returns localised list of installed translations
     * @param bool $returnall return all or just enabled
     * @return array moodle translation code => localised translation name
     */
    public function get_list_of_translations($returnall = false) {
        // return all is ignored here - we need to know all langs in installer
        $languages = array();
        // Get raw list of lang directories
        $langdirs = get_list_of_plugins('install/lang');
        asort($langdirs);
        // Get some info from each lang
        foreach ($langdirs as $lang) {
            if (file_exists($this->installroot.'/'.$lang.'/langconfig.php')) {
                $string = array();
                include($this->installroot.'/'.$lang.'/langconfig.php');
                if (!empty($string['thislanguage'])) {
                    $languages[$lang] = $string['thislanguage'].' ('.$lang.')';
                }
            }
        }
        // Return array
        return $languages;
    }

    /**
     * Returns localised list of currencies.
     *
     * @param string $lang moodle translation language, NULL means use current
     * @return array currency code => localised currency name
     */
    public function get_list_of_currencies($lang = NULL) {
        // not used in installer
        return array();
    }

    /**
     * This implementation does not use any caches
     */
    public function reset_caches() {}
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
 * @param string $identifier The key identifier for the localized string
 * @param string $component The module where the key identifier is stored,
 *      usually expressed as the filename in the language pack without the
 *      .php on the end but can also be written as mod/forum or grade/export/xls.
 *      If none is specified then moodle.php is used.
 * @param string|object|array $a An object, string or number that can be used
 *      within translation strings
 * @return string The localized string.
 */
function get_string($identifier, $component = '', $a = NULL) {

    $identifier = clean_param($identifier, PARAM_STRINGID);
    if (empty($identifier)) {
        throw new coding_exception('Invalid string identifier. Most probably some illegal character is part of the string identifier. Please fix your get_string() call and string definition');
    }

    if (func_num_args() > 3) {
        debugging('extralocations parameter in get_string() is not supported any more, please use standard lang locations only.');
    }

    if (strpos($component, '/') !== false) {
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

    return get_string_manager()->get_string($identifier, $component, $a);
}

/**
 * Converts an array of strings to their localized value.
 *
 * @param array $array An array of strings
 * @param string $module The language module that these strings can be found in.
 * @return array and array of translated strings.
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
 * @param string $identifier The key identifier for the localized string
 * @param string $component The module where the key identifier is stored. If none is specified then moodle.php is used.
 * @param mixed $a An object, string or number that can be used within translation strings
 */
function print_string($identifier, $component = '', $a = NULL) {
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
        'GB18030'    => 'GB18030', // gb18030 not supported by typo and mbstring
        'UTF-8'      => 'UTF-8');

    asort($charsets);

    return $charsets;
}

/**
 * Returns a list of valid and compatible themes
 *
 * @global object
 * @return array
 */
function get_list_of_themes() {
    global $CFG;

    $themes = array();

    if (!empty($CFG->themelist)) {       // use admin's list of themes
        $themelist = explode(',', $CFG->themelist);
    } else {
        $themelist = array_keys(get_plugin_list("theme"));
    }

    foreach ($themelist as $key => $themename) {
        $theme = theme_config::load($themename);
        $themes[$themename] = $theme;
    }
    asort($themes);

    return $themes;
}

/**
 * Returns a list of timezones in the current language
 *
 * @global object
 * @global object
 * @return array
 */
function get_list_of_timezones() {
    global $CFG, $DB;

    static $timezones;

    if (!empty($timezones)) {    // This function has been called recently
        return $timezones;
    }

    $timezones = array();

    if ($rawtimezones = $DB->get_records_sql("SELECT MAX(id), name FROM {timezone} GROUP BY name")) {
        foreach($rawtimezones as $timezone) {
            if (!empty($timezone->name)) {
                if (get_string_manager()->string_exists(strtolower($timezone->name), 'timezones')) {
                    $timezones[$timezone->name] = get_string(strtolower($timezone->name), 'timezones');
                } else {
                    $timezones[$timezone->name] = $timezone->name;
                }
                if (substr($timezones[$timezone->name], 0, 1) == '[') {  // No translation found
                    $timezones[$timezone->name] = $timezone->name;
                }
            }
        }
    }

    asort($timezones);

    for ($i = -13; $i <= 13; $i += .5) {
        $tzstring = 'UTC';
        if ($i < 0) {
            $timezones[sprintf("%.1f", $i)] = $tzstring . $i;
        } else if ($i > 0) {
            $timezones[sprintf("%.1f", $i)] = $tzstring . '+' . $i;
        } else {
            $timezones[sprintf("%.1f", $i)] = $tzstring;
        }
    }

    return $timezones;
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
 * Provides core support for plugins that have to deal with
 * emoticons (like HTML editor or emoticon filter).
 *
 * Whenever this manager mentiones 'emoticon object', the following data
 * structure is expected: stdClass with properties text, imagename, imagecomponent,
 * altidentifier and altcomponent
 *
 * @see admin_setting_emoticons
 */
class emoticon_manager {

    /**
     * Returns the currently enabled emoticons
     *
     * @return array of emoticon objects
     */
    public function get_emoticons() {
        global $CFG;

        if (empty($CFG->emoticons)) {
            return array();
        }

        $emoticons = $this->decode_stored_config($CFG->emoticons);

        if (!is_array($emoticons)) {
            // something is wrong with the format of stored setting
            debugging('Invalid format of emoticons setting, please resave the emoticons settings form', DEBUG_NORMAL);
            return array();
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
     * @param string $imagename to be used by {@see pix_emoticon}
     * @param string $altidentifier alternative string identifier, null for no alt
     * @param array $altcomponent where the alternative string is defined
     * @param string $imagecomponent to be used by {@see pix_emoticon}
     * @return stdClass
     */
    protected function prepare_emoticon_object($text, $imagename, $altidentifier = null, $altcomponent = 'core_pix', $imagecomponent = 'core') {
        return (object)array(
            'text'           => $text,
            'imagename'      => $imagename,
            'imagecomponent' => $imagecomponent,
            'altidentifier'  => $altidentifier,
            'altcomponent'   => $altcomponent,
        );
    }
}

/// ENCRYPTION  ////////////////////////////////////////////////

/**
 * rc4encrypt
 *
 * @todo Finish documenting this function
 *
 * @param string $data Data to encrypt
 * @return string The now encrypted data
 */
function rc4encrypt($data) {
    $password = get_site_identifier();
    return endecrypt($password, $data, '');
}

/**
 * rc4decrypt
 *
 * @todo Finish documenting this function
 *
 * @param string $data Data to decrypt
 * @return string The now decrypted data
 */
function rc4decrypt($data) {
    $password = get_site_identifier();
    return endecrypt($password, $data, 'de');
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
    $temp_swap = '';
    $pwd_length = 0;

    $pwd_length = strlen($pwd);

    for ($i = 0; $i <= 255; $i++) {
        $key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
        $box[$i] = $i;
    }

    $x = 0;

    for ($i = 0; $i <= 255; $i++) {
        $x = ($x + $box[$i] + $key[$i]) % 256;
        $temp_swap = $box[$i];
        $box[$i] = $box[$x];
        $box[$x] = $temp_swap;
    }

    $temp = '';
    $k = '';

    $cipherby = '';
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

/// ENVIRONMENT CHECKING  ////////////////////////////////////////////////////////////

/**
 * Returns the exact absolute path to plugin directory.
 *
 * @param string $plugintype type of plugin
 * @param string $name name of the plugin
 * @return string full path to plugin directory; NULL if not found
 */
function get_plugin_directory($plugintype, $name) {
    global $CFG;

    if ($plugintype === '') {
        $plugintype = 'mod';
    }

    $types = get_plugin_types(true);
    if (!array_key_exists($plugintype, $types)) {
        return NULL;
    }
    $name = clean_param($name, PARAM_SAFEDIR); // just in case ;-)

    if (!empty($CFG->themedir) and $plugintype === 'theme') {
        if (!is_dir($types['theme'] . '/' . $name)) {
            // ok, so the theme is supposed to be in the $CFG->themedir
            return $CFG->themedir . '/' . $name;
        }
    }

    return $types[$plugintype].'/'.$name;
}

/**
 * Return exact absolute path to a plugin directory,
 * this method support "simpletest_" prefix designed for unit testing.
 *
 * @param string $component name such as 'moodle', 'mod_forum' or special simpletest value
 * @return string full path to component directory; NULL if not found
 */
function get_component_directory($component) {
    global $CFG;
/*
    $simpletest = false;
    if (strpos($component, 'simpletest_') === 0) {
        $subdir = substr($component, strlen('simpletest_'));
        //TODO: this looks borked, where is it used actually?
        return $subdir;
    }
*/
    list($type, $plugin) = normalize_component($component);

    if ($type === 'core') {
        if ($plugin === NULL ) {
            $path = $CFG->libdir;
        } else {
            $subsystems = get_core_subsystems();
            if (isset($subsystems[$plugin])) {
                $path = $CFG->dirroot.'/'.$subsystems[$plugin];
            } else {
                $path = NULL;
            }
        }

    } else {
        $path = get_plugin_directory($type, $plugin);
    }

    return $path;
}

/**
 * Normalize the component name using the "frankenstyle" names.
 * @param string $component
 * @return array $type+$plugin elements
 */
function normalize_component($component) {
    if ($component === 'moodle' or $component === 'core') {
        $type = 'core';
        $plugin = NULL;

    } else if (strpos($component, '_') === false) {
        $subsystems = get_core_subsystems();
        if (array_key_exists($component, $subsystems)) {
            $type   = 'core';
            $plugin = $component;
        } else {
            // everything else is a module
            $type   = 'mod';
            $plugin = $component;
        }

    } else {
        list($type, $plugin) = explode('_', $component, 2);
        $plugintypes = get_plugin_types(false);
        if ($type !== 'core' and !array_key_exists($type, $plugintypes)) {
            $type   = 'mod';
            $plugin = $component;
        }
    }

    return array($type, $plugin);
}

/**
 * List all core subsystems and their location
 *
 * This is a whitelist of components that are part of the core and their
 * language strings are defined in /lang/en/<<subsystem>>.php. If a given
 * plugin is not listed here and it does not have proper plugintype prefix,
 * then it is considered as course activity module.
 *
 * The location is dirroot relative path. NULL means there is no special
 * directory for this subsystem. If the location is set, the subsystem's
 * renderer.php is expected to be there.
 *
 * @return array of (string)name => (string|null)location
 */
function get_core_subsystems() {
    global $CFG;

    static $info = null;

    if (!$info) {
        $info = array(
            'access'      => NULL,
            'admin'       => $CFG->admin,
            'auth'        => 'auth',
            'backup'      => 'backup/util/ui',
            'block'       => 'blocks',
            'blog'        => 'blog',
            'bulkusers'   => NULL,
            'calendar'    => 'calendar',
            'cohort'      => 'cohort',
            'condition'   => NULL,
            'completion'  => NULL,
            'countries'   => NULL,
            'course'      => 'course',
            'currencies'  => NULL,
            'dbtransfer'  => NULL,
            'debug'       => NULL,
            'dock'        => NULL,
            'editor'      => 'lib/editor',
            'edufields'   => NULL,
            'enrol'       => 'enrol',
            'error'       => NULL,
            'filepicker'  => NULL,
            'files'       => 'files',
            'filters'     => NULL,
            'flashdetect' => NULL,
            'fonts'       => NULL,
            'form'        => 'lib/form',
            'grades'      => 'grade',
            'group'       => 'group',
            'help'        => NULL,
            'hub'         => NULL,
            'imscc'       => NULL,
            'install'     => NULL,
            'iso6392'     => NULL,
            'langconfig'  => NULL,
            'license'     => NULL,
            'mathslib'    => NULL,
            'message'     => 'message',
            'mimetypes'   => NULL,
            'mnet'        => 'mnet',
            'moodle.org'  => NULL, // the dot is nasty, watch out! should be renamed to moodleorg
            'my'          => 'my',
            'notes'       => 'notes',
            'pagetype'    => NULL,
            'pix'         => NULL,
            'plagiarism'  => 'plagiarism',
            'plugin'      => NULL,
            'portfolio'   => 'portfolio',
            'publish'     => 'course/publish',
            'question'    => 'question',
            'rating'      => 'rating',
            'register'    => 'admin/registration',
            'repository'  => 'repository',
            'rss'         => 'rss',
            'role'        => $CFG->admin.'/role',
            'simpletest'  => NULL,
            'search'      => 'search',
            'table'       => NULL,
            'tag'         => 'tag',
            'timezones'   => NULL,
            'user'        => 'user',
            'userkey'     => NULL,
            'webservice'  => 'webservice',
            'xmldb'       => NULL,
        );
    }

    return $info;
}

/**
 * Lists all plugin types
 * @param bool $fullpaths false means relative paths from dirroot
 * @return array Array of strings - name=>location
 */
function get_plugin_types($fullpaths=true) {
    global $CFG;

    static $info     = null;
    static $fullinfo = null;

    if (!$info) {
        $info = array('qtype'         => 'question/type',
                      'mod'           => 'mod',
                      'auth'          => 'auth',
                      'enrol'         => 'enrol',
                      'message'       => 'message/output',
                      'block'         => 'blocks',
                      'filter'        => 'filter',
                      'editor'        => 'lib/editor',
                      'format'        => 'course/format',
                      'profilefield'  => 'user/profile/field',
                      'report'        => $CFG->admin.'/report',
                      'coursereport'  => 'course/report', // must be after system reports
                      'gradeexport'   => 'grade/export',
                      'gradeimport'   => 'grade/import',
                      'gradereport'   => 'grade/report',
                      'mnetservice'   => 'mnet/service',
                      'webservice'    => 'webservice',
                      'repository'    => 'repository',
                      'portfolio'     => 'portfolio',
                      'qbehaviour'    => 'question/behaviour',
                      'qformat'       => 'question/format',
                      'plagiarism'    => 'plagiarism',
                      'theme'         => 'theme'); // this is a bit hacky, themes may be in $CFG->themedir too

        $mods = get_plugin_list('mod');
        foreach ($mods as $mod => $moddir) {
            if (file_exists("$moddir/db/subplugins.php")) {
                $subplugins = array();
                include("$moddir/db/subplugins.php");
                foreach ($subplugins as $subtype=>$dir) {
                    $info[$subtype] = $dir;
                }
            }
        }

        // local is always last!
        $info['local'] = 'local';

        $fullinfo = array();
        foreach ($info as $type => $dir) {
            $fullinfo[$type] = $CFG->dirroot.'/'.$dir;
        }
    }

    return ($fullpaths ? $fullinfo : $info);
}

/**
 * Simplified version of get_list_of_plugins()
 * @param string $plugintype type of plugin
 * @return array name=>fulllocation pairs of plugins of given type
 */
function get_plugin_list($plugintype) {
    global $CFG;

    $ignored = array('CVS', '_vti_cnf', 'simpletest', 'db', 'yui', 'phpunit');
    if ($plugintype == 'auth') {
        // Historically we have had an auth plugin called 'db', so allow a special case.
        $key = array_search('db', $ignored);
        if ($key !== false) {
            unset($ignored[$key]);
        }
    }

    if ($plugintype === '') {
        $plugintype = 'mod';
    }

    $fulldirs = array();

    if ($plugintype === 'mod') {
        // mod is an exception because we have to call this function from get_plugin_types()
        $fulldirs[] = $CFG->dirroot.'/mod';

    } else if ($plugintype === 'theme') {
        $fulldirs[] = $CFG->dirroot.'/theme';
        // themes are special because they may be stored also in separate directory
        if (!empty($CFG->themedir) and file_exists($CFG->themedir) and is_dir($CFG->themedir) ) {
            $fulldirs[] = $CFG->themedir;
        }

    } else {
        $types = get_plugin_types(true);
        if (!array_key_exists($plugintype, $types)) {
            return array();
        }
        $fulldir = $types[$plugintype];
        if (!file_exists($fulldir)) {
            return array();
        }
        $fulldirs[] = $fulldir;
    }

    $result = array();

    foreach ($fulldirs as $fulldir) {
        if (!is_dir($fulldir)) {
            continue;
        }
        $items = new DirectoryIterator($fulldir);
        foreach ($items as $item) {
            if ($item->isDot() or !$item->isDir()) {
                continue;
            }
            $pluginname = $item->getFilename();
            if (in_array($pluginname, $ignored)) {
                continue;
            }
            if ($pluginname !== clean_param($pluginname, PARAM_SAFEDIR)) {
                // better ignore plugins with problematic names here
                continue;
            }
            $result[$pluginname] = $fulldir.'/'.$pluginname;
            unset($item);
        }
        unset($items);
    }

    //TODO: implement better sorting once we migrated all plugin names to 'pluginname', ksort does not work for unicode, that is why we have to sort by the dir name, not the strings!
    ksort($result);
    return $result;
}

/**
 * Gets a list of all plugin API functions for given plugin type, function
 * name, and filename.
 * @param string $plugintype Plugin type, e.g. 'mod' or 'report'
 * @param string $function Name of function after the frankenstyle prefix;
 *   e.g. if the function is called report_courselist_hook then this value
 *   would be 'hook'
 * @param string $file Name of file that includes function within plugin,
 *   default 'lib.php'
 * @return Array of plugin frankenstyle (e.g. 'report_courselist', 'mod_forum')
 *   to valid, existing plugin function name (e.g. 'report_courselist_hook',
 *   'forum_hook')
 */
function get_plugin_list_with_function($plugintype, $function, $file='lib.php') {
    global $CFG; // mandatory in case it is referenced by include()d PHP script

    $result = array();
    // Loop through list of plugins with given type
    $list = get_plugin_list($plugintype);
    foreach($list as $plugin => $dir) {
        $path = $dir . '/' . $file;
        // If file exists, require it and look for function
        if (file_exists($path)) {
            include_once($path);
            $fullfunction = $plugintype . '_' . $plugin . '_' . $function;
            if (function_exists($fullfunction)) {
                // Function exists with standard name. Store, indexed by
                // frankenstyle name of plugin
                $result[$plugintype . '_' . $plugin] = $fullfunction;
            } else if ($plugintype === 'mod') {
                // For modules, we also allow plugin without full frankenstyle
                // but just starting with the module name
                $shortfunction = $plugin . '_' . $function;
                if (function_exists($shortfunction)) {
                    $result[$plugintype . '_' . $plugin] = $shortfunction;
                }
            }
        }
    }
    return $result;
}

/**
 * Lists plugin-like directories within specified directory
 *
 * This function was originally used for standard Moodle plugins, please use
 * new get_plugin_list() now.
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

    if (file_exists($basedir) && filetype($basedir) == 'dir') {
        $dirhandle = opendir($basedir);
        while (false !== ($dir = readdir($dirhandle))) {
            $firstchar = substr($dir, 0, 1);
            if ($firstchar === '.' or $dir === 'CVS' or $dir === '_vti_cnf' or $dir === 'simpletest' or $dir === 'yui' or $dir === 'phpunit' or $dir === $exclude) {
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
    global $CFG; // this is needed for require_once() below

    $cleancomponent = clean_param($component, PARAM_SAFEDIR);
    if (empty($cleancomponent)) {
        throw new coding_exception('Invalid component used in plugin/component_callback():' . $component);
    }
    $component = $cleancomponent;

    list($type, $name) = normalize_component($component);
    $component = $type . '_' . $name;

    $oldfunction = $name.'_'.$function;
    $function = $component.'_'.$function;

    $dir = get_component_directory($component);
    if (empty($dir)) {
        throw new coding_exception('Invalid component used in plugin/component_callback():' . $component);
    }

    // Load library and look for function
    if (file_exists($dir.'/lib.php')) {
        require_once($dir.'/lib.php');
    }

    if (!function_exists($function) and function_exists($oldfunction)) {
        // In Moodle 2.2 and greater this will result in a debugging notice however
        // for 2.1+ we tolerate it.
        $function = $oldfunction;
    }

    if (function_exists($function)) {
        // Function exists, so just return function result
        $ret = call_user_func_array($function, $params);
        if (is_null($ret)) {
            return $default;
        } else {
            return $ret;
        }
    }
    return $default;
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
 */
function plugin_supports($type, $name, $feature, $default = NULL) {
    global $CFG;

    $name = clean_param($name, PARAM_SAFEDIR); //bit of extra security

    $function = null;

    if ($type === 'mod') {
        // we need this special case because we support subplugins in modules,
        // otherwise it would end up in infinite loop
        if ($name === 'NEWMODULE') {
            //somebody forgot to rename the module template
            return false;
        }
        if (file_exists("$CFG->dirroot/mod/$name/lib.php")) {
            include_once("$CFG->dirroot/mod/$name/lib.php");
            $function = $type.'_'.$name.'_supports';
            if (!function_exists($function)) {
                // legacy non-frankenstyle function name
                $function = $name.'_supports';
            }
        }

    } else {
        if (!$path = get_plugin_directory($type, $name)) {
            // non existent plugin type
            return false;
        }
        if (file_exists("$path/lib.php")) {
            include_once("$path/lib.php");
            $function = $type.'_'.$name.'_supports';
        }
    }

    if ($function and function_exists($function)) {
        $supports = $function($feature);
        if (is_null($supports)) {
            // plugin does not know - use default
            return $default;
        } else {
            return $supports;
        }
    }

    //plugin does not care, so use default
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
 * Checks to see if is the browser operating system matches the specified
 * brand.
 *
 * Known brand: 'Windows','Linux','Macintosh','SGI','SunOS','HP-UX'
 *
 * @uses $_SERVER
 * @param string $brand The operating system identifier being tested
 * @return bool true if the given brand below to the detected operating system
 */
 function check_browser_operating_system($brand) {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    if (preg_match("/$brand/i", $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }

    return false;
 }

/**
 * Checks to see if is a browser matches the specified
 * brand and is equal or better version.
 *
 * @uses $_SERVER
 * @param string $brand The browser identifier being tested
 * @param int $version The version of the browser, if not specified any version (except 5.5 for IE for BC reasons)
 * @return bool true if the given version is below that of the detected browser
 */
 function check_browser_version($brand, $version = null) {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $agent = $_SERVER['HTTP_USER_AGENT'];

    switch ($brand) {

      case 'Camino':   /// OSX browser using Gecke engine
          if (strpos($agent, 'Camino') === false) {
              return false;
          }
          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/Camino\/([0-9\.]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'Firefox':   /// Mozilla Firefox browsers
          if (strpos($agent, 'Iceweasel') === false and strpos($agent, 'Firefox') === false) {
              return false;
          }
          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/(Iceweasel|Firefox)\/([0-9\.]+)/i", $agent, $match)) {
              if (version_compare($match[2], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'Gecko':   /// Gecko based browsers
          if (empty($version) and substr_count($agent, 'Camino')) {
              // MacOS X Camino support
              $version = 20041110;
          }

          // the proper string - Gecko/CCYYMMDD Vendor/Version
          // Faster version and work-a-round No IDN problem.
          if (preg_match("/Gecko\/([0-9]+)/i", $agent, $match)) {
              if ($match[1] > $version) {
                      return true;
                  }
              }
          break;


      case 'MSIE':   /// Internet Explorer
          if (strpos($agent, 'Opera') !== false) {     // Reject Opera
              return false;
          }
          // in case of IE we have to deal with BC of the version parameter
          if (is_null($version)) {
              $version = 5.5; // anything older is not considered a browser at all!
          }

          //see: http://www.useragentstring.com/pages/Internet%20Explorer/
          if (preg_match("/MSIE ([0-9\.]+)/", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'Opera':  /// Opera
          if (strpos($agent, 'Opera') === false) {
              return false;
          }
          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/Opera\/([0-9\.]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'WebKit':  /// WebKit based browser - everything derived from it (Safari, Chrome, iOS, Android and other mobiles)
          if (strpos($agent, 'AppleWebKit') === false) {
              return false;
          }
          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/AppleWebKit\/([0-9]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'Safari':  /// Desktop version of Apple Safari browser - no mobile or touch devices
          if (strpos($agent, 'AppleWebKit') === false) {
              return false;
          }
          // Look for AppleWebKit, excluding strings with OmniWeb, Shiira and SymbianOS and any other mobile devices
          if (strpos($agent, 'OmniWeb')) { // Reject OmniWeb
              return false;
          }
          if (strpos($agent, 'Shiira')) { // Reject Shiira
              return false;
          }
          if (strpos($agent, 'SymbianOS')) { // Reject SymbianOS
              return false;
          }
          if (strpos($agent, 'Android')) { // Reject Androids too
              return false;
          }
          if (strpos($agent, 'iPhone') or strpos($agent, 'iPad') or strpos($agent, 'iPod')) {
              // No Apple mobile devices here - editor does not work, course ajax is not touch compatible, etc.
              return false;
          }
          if (strpos($agent, 'Chrome')) { // Reject chrome browsers - it needs to be tested explicitly
              return false;
          }

          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/AppleWebKit\/([0-9]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'Chrome':
          if (strpos($agent, 'Chrome') === false) {
              return false;
          }
          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/Chrome\/(.*)[ ]+/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'Safari iOS':  /// Safari on iPhone, iPad and iPod touch
          if (strpos($agent, 'AppleWebKit') === false or strpos($agent, 'Safari') === false) {
              return false;
          }
          if (!strpos($agent, 'iPhone') and !strpos($agent, 'iPad') and !strpos($agent, 'iPod')) {
              return false;
          }
          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/AppleWebKit\/([0-9]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;


      case 'WebKit Android':  /// WebKit browser on Android
          if (strpos($agent, 'Linux; U; Android') === false) {
              return false;
          }
          if (empty($version)) {
              return true; // no version specified
          }
          if (preg_match("/AppleWebKit\/([0-9]+)/i", $agent, $match)) {
              if (version_compare($match[1], $version) >= 0) {
                  return true;
              }
          }
          break;

    }

    return false;
}

/**
 * Returns whether a device/browser combination is mobile, tablet, legacy, default or the result of
 * an optional admin specified regular expression.  If enabledevicedetection is set to no or not set
 * it returns default
 *
 * @return string device type
 */
function get_device_type() {
    global $CFG;

    if (empty($CFG->enabledevicedetection) || empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'default';
    }

    $useragent = $_SERVER['HTTP_USER_AGENT'];

    if (!empty($CFG->devicedetectregex)) {
        $regexes = json_decode($CFG->devicedetectregex);

        foreach ($regexes as $value=>$regex) {
            if (preg_match($regex, $useragent)) {
                return $value;
            }
        }
    }

    //mobile detection PHP direct copy from open source detectmobilebrowser.com
    $phonesregex = '/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i';
    $modelsregex = '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i';
    if (preg_match($phonesregex,$useragent) || preg_match($modelsregex,substr($useragent, 0, 4))){
        return 'mobile';
    }

    $tabletregex = '/Tablet browser|iPad|iProd|GT-P1000|GT-I9000|SHW-M180S|SGH-T849|SCH-I800|Build\/ERE27|sholest/i';
    if (preg_match($tabletregex, $useragent)) {
         return 'tablet';
    }

    // Safe way to check for IE6 and not get false positives for some IE 7/8 users
    if (substr($_SERVER['HTTP_USER_AGENT'], 0, 34) === 'Mozilla/4.0 (compatible; MSIE 6.0;') {
        return 'legacy';
    }

    return 'default';
}

/**
 * Returns a list of the device types supporting by Moodle
 *
 * @param boolean $incusertypes includes types specified using the devicedetectregex admin setting
 * @return array $types
 */
function get_device_type_list($incusertypes = true) {
    global $CFG;

    $types = array('default', 'legacy', 'mobile', 'tablet');

    if ($incusertypes && !empty($CFG->devicedetectregex)) {
        $regexes = json_decode($CFG->devicedetectregex);

        foreach ($regexes as $value => $regex) {
            $types[] = $value;
        }
    }

    return $types;
}

/**
 * Returns the theme selected for a particular device or false if none selected.
 *
 * @param string $devicetype
 * @return string|false The name of the theme to use for the device or the false if not set
 */
function get_selected_theme_for_device_type($devicetype = null) {
    global $CFG;

    if (empty($devicetype)) {
        $devicetype = get_user_device_type();
    }

    $themevarname = get_device_cfg_var_name($devicetype);
    if (empty($CFG->$themevarname)) {
        return false;
    }

    return $CFG->$themevarname;
}

/**
 * Returns the name of the device type theme var in $CFG (because there is not a standard convention to allow backwards compatability
 *
 * @param string $devicetype
 * @return string The config variable to use to determine the theme
 */
function get_device_cfg_var_name($devicetype = null) {
    if ($devicetype == 'default' || empty($devicetype)) {
        return 'theme';
    }

    return 'theme' . $devicetype;
}

/**
 * Allows the user to switch the device they are seeing the theme for.
 * This allows mobile users to switch back to the default theme, or theme for any other device.
 *
 * @param string $newdevice The device the user is currently using.
 * @return string The device the user has switched to
 */
function set_user_device_type($newdevice) {
    global $USER;

    $devicetype = get_device_type();
    $devicetypes = get_device_type_list();

    if ($newdevice == $devicetype) {
        unset_user_preference('switchdevice'.$devicetype);
    } else if (in_array($newdevice, $devicetypes)) {
        set_user_preference('switchdevice'.$devicetype, $newdevice);
    }
}

/**
 * Returns the device the user is currently using, or if the user has chosen to switch devices
 * for the current device type the type they have switched to.
 *
 * @return string The device the user is currently using or wishes to use
 */
function get_user_device_type() {
    $device = get_device_type();
    $switched = get_user_preferences('switchdevice'.$device, false);
    if ($switched != false) {
        return $switched;
    }
    return $device;
}

/**
 * Returns one or several CSS class names that match the user's browser. These can be put
 * in the body tag of the page to apply browser-specific rules without relying on CSS hacks
 *
 * @return array An array of browser version classes
 */
function get_browser_version_classes() {
    $classes = array();

    if (check_browser_version("MSIE", "0")) {
        $classes[] = 'ie';
        if (check_browser_version("MSIE", 9)) {
            $classes[] = 'ie9';
        } else if (check_browser_version("MSIE", 8)) {
            $classes[] = 'ie8';
        } elseif (check_browser_version("MSIE", 7)) {
            $classes[] = 'ie7';
        } elseif (check_browser_version("MSIE", 6)) {
            $classes[] = 'ie6';
        }

    } else if (check_browser_version("Firefox") || check_browser_version("Gecko") || check_browser_version("Camino")) {
        $classes[] = 'gecko';
        if (preg_match('/rv\:([1-2])\.([0-9])/', $_SERVER['HTTP_USER_AGENT'], $matches)) {
            $classes[] = "gecko{$matches[1]}{$matches[2]}";
        }

    } else if (check_browser_version("WebKit")) {
        $classes[] = 'safari';
        if (check_browser_version("Safari iOS")) {
            $classes[] = 'ios';

        } else if (check_browser_version("WebKit Android")) {
            $classes[] = 'android';
        }

    } else if (check_browser_version("Opera")) {
        $classes[] = 'opera';

    }

    return $classes;
}

/**
 * Can handle rotated text. Whether it is safe to use the trickery in textrotate.js.
 *
 * @return bool True for yes, false for no
 */
function can_use_rotated_text() {
    global $USER;
    return ajaxenabled(array('Firefox' => 2.0)) && !$USER->screenreader;;
}

/**
 * Hack to find out the GD version by parsing phpinfo output
 *
 * @return int GD version (1, 2, or 0)
 */
function check_gd_version() {
    $gdversion = 0;

    if (function_exists('gd_info')){
        $gd_info = gd_info();
        if (substr_count($gd_info['GD Version'], '2.')) {
            $gdversion = 2;
        } else if (substr_count($gd_info['GD Version'], '1.')) {
            $gdversion = 1;
        }

    } else {
        ob_start();
        phpinfo(INFO_MODULES);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = explode("\n", $phpinfo);


        foreach ($phpinfo as $text) {
            $parts = explode('</td>', $text);
            foreach ($parts as $key => $val) {
                $parts[$key] = trim(strip_tags($val));
            }
            if ($parts[0] == 'GD Version') {
                if (substr_count($parts[1], '2.0')) {
                    $parts[1] = '2.0';
                }
                $gdversion = intval($parts[1]);
            }
        }
    }

    return $gdversion;   // 1, 2 or 0
}

/**
 * Determine if moodle installation requires update
 *
 * Checks version numbers of main code and all modules to see
 * if there are any mismatches
 *
 * @global object
 * @global object
 * @return bool
 */
function moodle_needs_upgrading() {
    global $CFG, $DB, $OUTPUT;

    if (empty($CFG->version)) {
        return true;
    }

    // main versio nfirst
    $version = null;
    include($CFG->dirroot.'/version.php');  // defines $version and upgrades
    if ($version > $CFG->version) {
        return true;
    }

    // modules
    $mods = get_plugin_list('mod');
    $installed = $DB->get_records('modules', array(), '', 'name, version');
    foreach ($mods as $mod => $fullmod) {
        if ($mod === 'NEWMODULE') {   // Someone has unzipped the template, ignore it
            continue;
        }
        $module = new stdClass();
        if (!is_readable($fullmod.'/version.php')) {
            continue;
        }
        include($fullmod.'/version.php');  // defines $module with version etc
        if (empty($installed[$mod])) {
            return true;
        } else if ($module->version > $installed[$mod]->version) {
            return true;
        }
    }
    unset($installed);

    // blocks
    $blocks = get_plugin_list('block');
    $installed = $DB->get_records('block', array(), '', 'name, version');
    require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    foreach ($blocks as $blockname=>$fullblock) {
        if ($blockname === 'NEWBLOCK') {   // Someone has unzipped the template, ignore it
            continue;
        }
        if (!is_readable($fullblock.'/version.php')) {
            continue;
        }
        $plugin = new stdClass();
        $plugin->version = NULL;
        include($fullblock.'/version.php');
        if (empty($installed[$blockname])) {
            return true;
        } else if ($plugin->version > $installed[$blockname]->version) {
            return true;
        }
    }
    unset($installed);

    // now the rest of plugins
    $plugintypes = get_plugin_types();
    unset($plugintypes['mod']);
    unset($plugintypes['block']);
    foreach ($plugintypes as $type=>$unused) {
        $plugs = get_plugin_list($type);
        foreach ($plugs as $plug=>$fullplug) {
            $component = $type.'_'.$plug;
            if (!is_readable($fullplug.'/version.php')) {
                continue;
            }
            $plugin = new stdClass();
            include($fullplug.'/version.php');  // defines $plugin with version etc
            $installedversion = get_config($component, 'version');
            if (empty($installedversion)) { // new installation
                return true;
            } else if ($installedversion < $plugin->version) { // upgrade
                return true;
            }
        }
    }

    return false;
}

/**
 * Sets maximum expected time needed for upgrade task.
 * Please always make sure that upgrade will not run longer!
 *
 * The script may be automatically aborted if upgrade times out.
 *
 * @global object
 * @param int $max_execution_time in seconds (can not be less than 60 s)
 */
function upgrade_set_timeout($max_execution_time=300) {
    global $CFG;

    if (!isset($CFG->upgraderunning) or $CFG->upgraderunning < time()) {
        $upgraderunning = get_config(null, 'upgraderunning');
    } else {
        $upgraderunning = $CFG->upgraderunning;
    }

    if (!$upgraderunning) {
        // upgrade not running or aborted
        print_error('upgradetimedout', 'admin', "$CFG->wwwroot/$CFG->admin/");
        die;
    }

    if ($max_execution_time < 60) {
        // protection against 0 here
        $max_execution_time = 60;
    }

    $expected_end = time() + $max_execution_time;

    if ($expected_end < $upgraderunning + 10 and $expected_end > $upgraderunning - 10) {
        // no need to store new end, it is nearly the same ;-)
        return;
    }

    set_time_limit($max_execution_time);
    set_config('upgraderunning', $expected_end); // keep upgrade locked until this time
}

/// MISCELLANEOUS ////////////////////////////////////////////////////////////////////

/**
 * Notify admin users or admin user of any failed logins (since last notification).
 *
 * Note that this function must be only executed from the cron script
 * It uses the cache_flags system to store temporary records, deleting them
 * by name before finishing
 *
 * @global object
 * @global object
 * @uses HOURSECS
 */
function notify_login_failures() {
    global $CFG, $DB, $OUTPUT;

    $recip = get_users_from_config($CFG->notifyloginfailures, 'moodle/site:config');

    if (empty($CFG->lastnotifyfailure)) {
        $CFG->lastnotifyfailure=0;
    }

    // we need to deal with the threshold stuff first.
    if (empty($CFG->notifyloginthreshold)) {
        $CFG->notifyloginthreshold = 10; // default to something sensible.
    }

/// Get all the IPs with more than notifyloginthreshold failures since lastnotifyfailure
/// and insert them into the cache_flags temp table
    $sql = "SELECT ip, COUNT(*)
              FROM {log}
             WHERE module = 'login' AND action = 'error'
                   AND time > ?
          GROUP BY ip
            HAVING COUNT(*) >= ?";
    $params = array($CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $iprec) {
        if (!empty($iprec->ip)) {
            set_cache_flag('login_failure_by_ip', $iprec->ip, '1', 0);
        }
    }
    $rs->close();

/// Get all the INFOs with more than notifyloginthreshold failures since lastnotifyfailure
/// and insert them into the cache_flags temp table
    $sql = "SELECT info, count(*)
              FROM {log}
             WHERE module = 'login' AND action = 'error'
                   AND time > ?
          GROUP BY info
            HAVING count(*) >= ?";
    $params = array($CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $inforec) {
        if (!empty($inforec->info)) {
            set_cache_flag('login_failure_by_info', $inforec->info, '1', 0);
        }
    }
    $rs->close();

/// Now, select all the login error logged records belonging to the ips and infos
/// since lastnotifyfailure, that we have stored in the cache_flags table
    $sql = "SELECT l.*, u.firstname, u.lastname
              FROM {log} l
              JOIN {cache_flags} cf ON l.ip = cf.name
         LEFT JOIN {user} u         ON l.userid = u.id
             WHERE l.module = 'login' AND l.action = 'error'
                   AND l.time > ?
                   AND cf.flagtype = 'login_failure_by_ip'
        UNION ALL
            SELECT l.*, u.firstname, u.lastname
              FROM {log} l
              JOIN {cache_flags} cf ON l.info = cf.name
         LEFT JOIN {user} u         ON l.userid = u.id
             WHERE l.module = 'login' AND l.action = 'error'
                   AND l.time > ?
                   AND cf.flagtype = 'login_failure_by_info'
          ORDER BY time DESC";
    $params = array($CFG->lastnotifyfailure, $CFG->lastnotifyfailure);

/// Init some variables
    $count = 0;
    $messages = '';
/// Iterate over the logs recordset
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $log) {
        $log->time = userdate($log->time);
        $messages .= get_string('notifyloginfailuresmessage','',$log)."\n";
        $count++;
    }
    $rs->close();

/// If we haven't run in the last hour and
/// we have something useful to report and we
/// are actually supposed to be reporting to somebody
    if ((time() - HOURSECS) > $CFG->lastnotifyfailure && $count > 0 && is_array($recip) && count($recip) > 0) {
        $site = get_site();
        $subject = get_string('notifyloginfailuressubject', '', format_string($site->fullname));
    /// Calculate the complete body of notification (start + messages + end)
        $body = get_string('notifyloginfailuresmessagestart', '', $CFG->wwwroot) .
                (($CFG->lastnotifyfailure != 0) ? '('.userdate($CFG->lastnotifyfailure).')' : '')."\n\n" .
                $messages .
                "\n\n".get_string('notifyloginfailuresmessageend','',$CFG->wwwroot)."\n\n";

    /// For each destination, send mail
        mtrace('Emailing admins about '. $count .' failed login attempts');
        foreach ($recip as $admin) {
            //emailing the admins directly rather than putting these through the messaging system
            email_to_user($admin,get_admin(), $subject, $body);
        }

    /// Update lastnotifyfailure with current time
        set_config('lastnotifyfailure', time());
    }

/// Finally, delete all the temp records we have created in cache_flags
    $DB->delete_records_select('cache_flags', "flagtype IN ('login_failure_by_ip', 'login_failure_by_info')");
}

/**
 * Sets the system locale
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @param string $locale Can be used to force a locale
 */
function moodle_setlocale($locale='') {
    global $CFG;

    static $currentlocale = ''; // last locale caching

    $oldlocale = $currentlocale;

/// Fetch the correct locale based on ostype
    if ($CFG->ostype == 'WINDOWS') {
        $stringtofetch = 'localewin';
    } else {
        $stringtofetch = 'locale';
    }

/// the priority is the same as in get_string() - parameter, config, course, session, user, global language
    if (!empty($locale)) {
        $currentlocale = $locale;
    } else if (!empty($CFG->locale)) { // override locale for all language packs
        $currentlocale = $CFG->locale;
    } else {
        $currentlocale = get_string($stringtofetch, 'langconfig');
    }

/// do nothing if locale already set up
    if ($oldlocale == $currentlocale) {
        return;
    }

/// Due to some strange BUG we cannot set the LC_TIME directly, so we fetch current values,
/// set LC_ALL and then set values again. Just wondering why we cannot set LC_ALL only??? - stronk7
/// Some day, numeric, monetary and other categories should be set too, I think. :-/

/// Get current values
    $monetary= setlocale (LC_MONETARY, 0);
    $numeric = setlocale (LC_NUMERIC, 0);
    $ctype   = setlocale (LC_CTYPE, 0);
    if ($CFG->ostype != 'WINDOWS') {
        $messages= setlocale (LC_MESSAGES, 0);
    }
/// Set locale to all
    setlocale (LC_ALL, $currentlocale);
/// Set old values
    setlocale (LC_MONETARY, $monetary);
    setlocale (LC_NUMERIC, $numeric);
    if ($CFG->ostype != 'WINDOWS') {
        setlocale (LC_MESSAGES, $messages);
    }
    if ($currentlocale == 'tr_TR' or $currentlocale == 'tr_TR.UTF-8') { // To workaround a well-known PHP problem with Turkish letter Ii
        setlocale (LC_CTYPE, $ctype);
    }
}

/**
 * Converts string to lowercase using most compatible function available.
 *
 * @todo Remove this function when no longer in use
 * @deprecated Use textlib->strtolower($text) instead.
 *
 * @param string $string The string to convert to all lowercase characters.
 * @param string $encoding The encoding on the string.
 * @return string
 */
function moodle_strtolower ($string, $encoding='') {

    //If not specified use utf8
    if (empty($encoding)) {
        $encoding = 'UTF-8';
    }
    //Use text services
    $textlib = textlib_get_instance();

    return $textlib->strtolower($string, $encoding);
}

/**
 * Count words in a string.
 *
 * Words are defined as things between whitespace.
 *
 * @param string $string The text to be searched for words.
 * @return int The count of words in the specified string
 */
function count_words($string) {
    $string = strip_tags($string);
    return count(preg_split("/\w\b/", $string)) - 1;
}

/** Count letters in a string.
 *
 * Letters are defined as chars not in tags and different from whitespace.
 *
 * @param string $string The text to be searched for letters.
 * @return int The count of letters in the specified text.
 */
function count_letters($string) {
/// Loading the textlib singleton instance. We are going to need it.
    $textlib = textlib_get_instance();

    $string = strip_tags($string); // Tags are out now
    $string = preg_replace('/[[:space:]]*/','',$string); //Whitespace are out now

    return $textlib->strlen($string);
}

/**
 * Generate and return a random string of the specified length.
 *
 * @param int $length The length of the string to be created.
 * @return string
 */
function random_string ($length=15) {
    $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pool .= 'abcdefghijklmnopqrstuvwxyz';
    $pool .= '0123456789';
    $poollen = strlen($pool);
    mt_srand ((double) microtime() * 1000000);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($pool, (mt_rand()%($poollen)), 1);
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
    mt_srand ((double) microtime() * 1000000);
    if ($length===null) {
        $length = floor(rand(24,32));
    }
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $pool[(mt_rand()%$poollen)];
    }
    return $string;
}

/**
 * Given some text (which may contain HTML) and an ideal length,
 * this function truncates the text neatly on a word boundary if possible
 *
 * @global object
 * @param string $text - text to be shortened
 * @param int $ideal - ideal string length
 * @param boolean $exact if false, $text will not be cut mid-word
 * @param string $ending The string to append if the passed string is truncated
 * @return string $truncate - shortened string
 */
function shorten_text($text, $ideal=30, $exact = false, $ending='...') {

    global $CFG;

    // if the plain text is shorter than the maximum length, return the whole text
    if (strlen(preg_replace('/<.*?>/', '', $text)) <= $ideal) {
        return $text;
    }

    // Splits on HTML tags. Each open/close/empty tag will be the first thing
    // and only tag in its 'line'
    preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

    $total_length = strlen($ending);
    $truncate = '';

    // This array stores information about open and close tags and their position
    // in the truncated string. Each item in the array is an object with fields
    // ->open (true if open), ->tag (tag name in lower case), and ->pos
    // (byte position in truncated text)
    $tagdetails = array();

    foreach ($lines as $line_matchings) {
        // if there is any html-tag in this line, handle it and add it (uncounted) to the output
        if (!empty($line_matchings[1])) {
            // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
            if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    // do nothing
            // if tag is a closing tag (f.e. </b>)
            } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                // record closing tag
                $tagdetails[] = (object)array('open'=>false,
                    'tag'=>strtolower($tag_matchings[1]), 'pos'=>strlen($truncate));
            // if tag is an opening tag (f.e. <b>)
            } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                // record opening tag
                $tagdetails[] = (object)array('open'=>true,
                    'tag'=>strtolower($tag_matchings[1]), 'pos'=>strlen($truncate));
            }
            // add html-tag to $truncate'd text
            $truncate .= $line_matchings[1];
        }

        // calculate the length of the plain text part of the line; handle entities as one character
        $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
        if ($total_length+$content_length > $ideal) {
            // the number of characters which are left
            $left = $ideal - $total_length;
            $entities_length = 0;
            // search for html entities
            if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                // calculate the real length of all entities in the legal range
                foreach ($entities[0] as $entity) {
                    if ($entity[1]+1-$entities_length <= $left) {
                        $left--;
                        $entities_length += strlen($entity[0]);
                    } else {
                        // no more characters left
                        break;
                    }
                }
            }
            $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
            // maximum length is reached, so get off the loop
            break;
        } else {
            $truncate .= $line_matchings[2];
            $total_length += $content_length;
        }

        // if the maximum length is reached, get off the loop
        if($total_length >= $ideal) {
            break;
        }
    }

    // if the words shouldn't be cut in the middle...
    if (!$exact) {
        // ...search the last occurence of a space...
        for ($k=strlen($truncate);$k>0;$k--) {
            if (!empty($truncate[$k]) && ($char = $truncate[$k])) {
                if ($char == '.' or $char == ' ') {
                    $breakpos = $k+1;
                    break;
                } else if (ord($char) >= 0xE0) {  // Chinese/Japanese/Korean text
                    $breakpos = $k;               // can be truncated at any UTF-8
                    break;                        // character boundary.
                }
            }
        }

        if (isset($breakpos)) {
            // ...and cut the text in this position
            $truncate = substr($truncate, 0, $breakpos);
        }
    }

    // add the defined ending to the text
    $truncate .= $ending;

    // Now calculate the list of open html tags based on the truncate position
    $open_tags = array();
    foreach ($tagdetails as $taginfo) {
        if(isset($breakpos) && $taginfo->pos >= $breakpos) {
            // Don't include tags after we made the break!
            break;
        }
        if($taginfo->open) {
            // add tag to the beginning of $open_tags list
            array_unshift($open_tags, $taginfo->tag);
        } else {
            $pos = array_search($taginfo->tag, array_reverse($open_tags, true)); // can have multiple exact same open tags, close the last one
            if ($pos !== false) {
                unset($open_tags[$pos]);
            }
        }
    }

    // close all unclosed html-tags
    foreach ($open_tags as $tag) {
        $truncate .= '</' . $tag . '>';
    }

    return $truncate;
}


/**
 * Given dates in seconds, how many weeks is the date from startdate
 * The first week is 1, the second 2 etc ...
 *
 * @todo Finish documenting this function
 *
 * @uses WEEKSECS
 * @param int $startdate Timestamp for the start date
 * @param int $thedate Timestamp for the end date
 * @return string
 */
function getweek ($startdate, $thedate) {
    if ($thedate < $startdate) {   // error
        return 0;
    }

    return floor(($thedate - $startdate) / WEEKSECS) + 1;
}

/**
 * returns a randomly generated password of length $maxlen.  inspired by
 *
 * {@link http://www.phpbuilder.com/columns/jesus19990502.php3} and
 * {@link http://es2.php.net/manual/en/function.str-shuffle.php#73254}
 *
 * @global object
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
        // minlength should be the greater one of the two ( $minlen and $total )
        $minlen = $minlen < $total ? $total : $minlen;
        // maxlen can never be smaller than minlen
        $maxlen = $minlen > $maxlen ? $minlen : $maxlen;
        $additional = $maxlen - $total;

        // Make sure we have enough characters to fulfill
        // complexity requirements
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

        // Now mix and shuffle it all
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
 * @param float $float The float to print
 * @param int $places The number of decimal places to print.
 * @param bool $localized use localized decimal separator
 * @return string locale float
 */
function format_float($float, $decimalpoints=1, $localized=true) {
    if (is_null($float)) {
        return '';
    }
    if ($localized) {
        return number_format($float, $decimalpoints, get_string('decsep', 'langconfig'), '');
    } else {
        return number_format($float, $decimalpoints, '.', '');
    }
}

/**
 * Converts locale specific floating point/comma number back to standard PHP float value
 * Do NOT try to do any math operations before this conversion on any user submitted floats!
 *
 * @param  string $locale_float locale aware float representation
 * @return float
 */
function unformat_float($locale_float) {
    $locale_float = trim($locale_float);

    if ($locale_float == '') {
        return null;
    }

    $locale_float = str_replace(' ', '', $locale_float); // no spaces - those might be used as thousand separators

    return (float)str_replace(get_string('decsep', 'langconfig'), '.', $locale_float);
}

/**
 * Given a simple array, this shuffles it up just like shuffle()
 * Unlike PHP's shuffle() this function works on any machine.
 *
 * @param array $array The array to be rearranged
 * @return array
 */
function swapshuffle($array) {

    srand ((double) microtime() * 10000000);
    $last = count($array) - 1;
    for ($i=0;$i<=$last;$i++) {
        $from = rand(0,$last);
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
    srand ((double) microtime() * 10000000);

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
    list($a_dec, $a_sec) = explode(' ', $a);
    list($b_dec, $b_sec) = explode(' ', $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
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
 * This function returns the number of activities
 * using scaleid in a courseid
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @global object
 * @param int $courseid ?
 * @param int $scaleid ?
 * @return int
 */
function course_scale_used($courseid, $scaleid) {
    global $CFG, $DB;

    $return = 0;

    if (!empty($scaleid)) {
        if ($cms = get_course_mods($courseid)) {
            foreach ($cms as $cm) {
                //Check cm->name/lib.php exists
                if (file_exists($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php')) {
                    include_once($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php');
                    $function_name = $cm->modname.'_scale_used';
                    if (function_exists($function_name)) {
                        if ($function_name($cm->instance,$scaleid)) {
                            $return++;
                        }
                    }
                }
            }
        }

        // check if any course grade item makes use of the scale
        $return += $DB->count_records('grade_items', array('courseid'=>$courseid, 'scaleid'=>$scaleid));

        // check if any outcome in the course makes use of the scale
        $return += $DB->count_records_sql("SELECT COUNT('x')
                                             FROM {grade_outcomes_courses} goc,
                                                  {grade_outcomes} go
                                            WHERE go.id = goc.outcomeid
                                                  AND go.scaleid = ? AND goc.courseid = ?",
                                          array($scaleid, $courseid));
    }
    return $return;
}

/**
 * This function returns the number of activities
 * using scaleid in the entire site
 *
 * @param int $scaleid
 * @param array $courses
 * @return int
 */
function site_scale_used($scaleid, &$courses) {
    $return = 0;

    if (!is_array($courses) || count($courses) == 0) {
        $courses = get_courses("all",false,"c.id,c.shortname");
    }

    if (!empty($scaleid)) {
        if (is_array($courses) && count($courses) > 0) {
            foreach ($courses as $course) {
                $return += course_scale_used($course->id,$scaleid);
            }
        }
    }
    return $return;
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
function make_unique_id_code($extra='') {

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
    $addr = cleanremoteaddr($addr, false); // normalise
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
        ///1: xxx.xxx.xxx.xxx/nn or xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx/nnn
            list($ip, $mask) = explode('/', $subnet);
            $mask = trim($mask);
            if (!is_number($mask)) {
                continue; // incorect mask number, eh?
            }
            $ip = cleanremoteaddr($ip, false); // normalise
            if ($ip === null) {
                continue;
            }
            if (strpos($ip, ':') !== false) {
                // IPv6
                if (!$ipv6) {
                    continue;
                }
                if ($mask > 128 or $mask < 0) {
                    continue; // nonsense
                }
                if ($mask == 0) {
                    return true; // any address
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
                // IPv4
                if ($ipv6) {
                    continue;
                }
                if ($mask > 32 or $mask < 0) {
                    continue; // nonsense
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

        } else if (strpos($subnet, '-') !== false)  {
        /// 2: xxx.xxx.xxx.xxx-yyy or  xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx::xxxx-yyyy ...a range of IP addresses in the last group.
            $parts = explode('-', $subnet);
            if (count($parts) != 2) {
                continue;
            }

            if (strpos($subnet, ':') !== false) {
                // IPv6
                if (!$ipv6) {
                    continue;
                }
                $ipstart = cleanremoteaddr(trim($parts[0]), false); // normalise
                if ($ipstart === null) {
                    continue;
                }
                $ipparts = explode(':', $ipstart);
                $start = hexdec(array_pop($ipparts));
                $ipparts[] = trim($parts[1]);
                $ipend = cleanremoteaddr(implode(':', $ipparts), false); // normalise
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
                // IPv4
                if ($ipv6) {
                    continue;
                }
                $ipstart = cleanremoteaddr(trim($parts[0]), false); // normalise
                if ($ipstart === null) {
                    continue;
                }
                $ipparts = explode('.', $ipstart);
                $ipparts[3] = trim($parts[1]);
                $ipend = cleanremoteaddr(implode('.', $ipparts), false); // normalise
                if ($ipend === null) {
                    continue;
                }

                if ((ip2long($addr) >= ip2long($ipstart)) and (ip2long($addr) <= ip2long($ipend))) {
                    return true;
                }
            }

        } else {
        /// 3: xxx.xxx or xxx.xxx. or xxx:xxx:xxxx or xxx:xxx:xxxx.
            if (strpos($subnet, ':') !== false) {
                // IPv6
                if (!$ipv6) {
                    continue;
                }
                $parts = explode(':', $subnet);
                $count = count($parts);
                if ($parts[$count-1] === '') {
                    unset($parts[$count-1]); // trim trailing :
                    $count--;
                    $subnet = implode('.', $parts);
                }
                $isip = cleanremoteaddr($subnet, false); // normalise
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
                // IPv4
                if ($ipv6) {
                    continue;
                }
                $parts = explode('.', $subnet);
                $count = count($parts);
                if ($parts[$count-1] === '') {
                    unset($parts[$count-1]); // trim trailing .
                    $count--;
                    $subnet = implode('.', $parts);
                }
                if ($count == 4) {
                    $subnet = cleanremoteaddr($subnet, false); // normalise
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
 * @uses STDOUT
 * @param string $string The string to write
 * @param string $eol The end of line char(s) to use
 * @param string $sleep Period to make the application sleep
 *                      This ensures any messages have time to display before redirect
 */
function mtrace($string, $eol="\n", $sleep=0) {

    if (defined('STDOUT')) {
        fwrite(STDOUT, $string.$eol);
    } else {
        echo $string . $eol;
    }

    flush();

    //delay to keep message on user's screen in case of subsequent redirect
    if ($sleep) {
        sleep($sleep);
    }
}

/**
 * Replace 1 or more slashes or backslashes to 1 slash
 *
 * @param string $path The path to strip
 * @return string the path with double slashes removed
 */
function cleardoubleslashes ($path) {
    return preg_replace('/(\/|\\\){1,}/','/',$path);
}

/**
 * Is current ip in give list?
 *
 * @param string $list
 * @return bool
 */
function remoteip_in_list($list){
    $inlist = false;
    $client_ip = getremoteaddr(null);

    if(!$client_ip){
        // ensure access on cli
        return true;
    }

    $list = explode("\n", $list);
    foreach($list as $subnet) {
        $subnet = trim($subnet);
        if (address_in_subnet($client_ip, $subnet)) {
            $inlist = true;
            break;
        }
    }
    return $inlist;
}

/**
 * Returns most reliable client address
 *
 * @global object
 * @param string $default If an address can't be determined, then return this
 * @return string The remote IP address
 */
function getremoteaddr($default='0.0.0.0') {
    global $CFG;

    if (empty($CFG->getremoteaddrconf)) {
        // This will happen, for example, before just after the upgrade, as the
        // user is redirected to the admin screen.
        $variablestoskip = 0;
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
            $address = cleanremoteaddr($_SERVER['HTTP_X_FORWARDED_FOR']);
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

    //TODO: maybe add a separate function is_addr_public() or something like this

    if (strpos($addr, ':') !== false) {
        // can be only IPv6
        $parts = explode(':', $addr);
        $count = count($parts);

        if (strpos($parts[$count-1], '.') !== false) {
            //legacy ipv4 notation
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
            return null; // severly malformed
        }

        if ($count != 8) {
            if (strpos($addr, '::') === false) {
                return null; // malformed
            }
            // uncompress ::
            $insertat = array_search('', $parts, true);
            $missing = array_fill(0, 1 + 8 - $count, '0');
            array_splice($parts, $insertat, 1, $missing);
            foreach ($parts as $key=>$part) {
                if ($part === '') {
                    $parts[$key] = '0';
                }
            }
        }

        $adr = implode(':', $parts);
        if (!preg_match('/^([0-9a-f]{1,4})(:[0-9a-f]{1,4})*$/i', $adr)) {
            return null; // incorrect format - sorry
        }

        // normalise 0s and case
        $parts = array_map('hexdec', $parts);
        $parts = array_map('dechex', $parts);

        $result = implode(':', $parts);

        if (!$compress) {
            return $result;
        }

        if ($result === '0:0:0:0:0:0:0:0') {
            return '::'; // all addresses
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

    // first get all things that look like IPv4 addresses
    $parts = array();
    if (!preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $addr, $parts)) {
        return null;
    }
    unset($parts[0]);

    foreach ($parts as $key=>$match) {
        if ($match > 255) {
            return null;
        }
        $parts[$key] = (int)$match; // normalise 0s
    }

    return implode('.', $parts);
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
 * This function expects to called during shutdown
 * should be set via register_shutdown_function()
 * in lib/setup.php .
 *
 * @return void
 */
function moodle_request_shutdown() {
    global $CFG;

    // help apache server if possible
    $apachereleasemem = false;
    if (function_exists('apache_child_terminate') && function_exists('memory_get_usage')
            && ini_get_bool('child_terminate')) {

        $limit = (empty($CFG->apachemaxmem) ? 64*1024*1024 : $CFG->apachemaxmem); //64MB default
        if (memory_get_usage() > get_real_size($limit)) {
            $apachereleasemem = $limit;
            @apache_child_terminate();
        }
    }

    // deal with perf logging
    if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
        if ($apachereleasemem) {
            error_log('Mem usage over '.$apachereleasemem.': marking Apache child for reaping.');
        }
        if (defined('MDL_PERFTOLOG')) {
            $perf = get_performance_info();
            error_log("PERF: " . $perf['txt']);
        }
        if (defined('MDL_PERFINC')) {
            $inc = get_included_files();
            $ts  = 0;
            foreach($inc as $f) {
                if (preg_match(':^/:', $f)) {
                    $fs  =  filesize($f);
                    $ts  += $fs;
                    $hfs =  display_size($fs);
                    error_log(substr($f,strlen($CFG->dirroot)) . " size: $fs ($hfs)"
                              , NULL, NULL, 0);
                } else {
                    error_log($f , NULL, NULL, 0);
                }
            }
            if ($ts > 0 ) {
                $hts = display_size($ts);
                error_log("Total size of files included: $ts ($hts)");
            }
        }
    }
}

 /**
  * If new messages are waiting for the current user, then insert
  * JavaScript to pop up the messaging window into the page
  *
  * @global moodle_page $PAGE
  * @return void
  */
function message_popup_window() {
    global $USER, $DB, $PAGE, $CFG, $SITE;

    if (!$PAGE->get_popup_notification_allowed() || empty($CFG->messaging)) {
        return;
    }

    if (!isloggedin() || isguestuser()) {
        return;
    }

    if (!isset($USER->message_lastpopup)) {
        $USER->message_lastpopup = 0;
    } else if ($USER->message_lastpopup > (time()-120)) {
        //dont run the query to check whether to display a popup if its been run in the last 2 minutes
        return;
    }

    //a quick query to check whether the user has new messages
    $messagecount = $DB->count_records('message', array('useridto' => $USER->id));
    if ($messagecount<1) {
        return;
    }

    //got unread messages so now do another query that joins with the user table
    $messagesql = "SELECT m.id, m.smallmessage, m.fullmessageformat, m.notification, u.firstname, u.lastname
                     FROM {message} m
                     JOIN {message_working} mw ON m.id=mw.unreadmessageid
                     JOIN {message_processors} p ON mw.processorid=p.id
                     JOIN {user} u ON m.useridfrom=u.id
                    WHERE m.useridto = :userid
                      AND p.name='popup'";

    //if the user was last notified over an hour ago we can renotify them of old messages
    //so don't worry about when the new message was sent
    $lastnotifiedlongago = $USER->message_lastpopup < (time()-3600);
    if (!$lastnotifiedlongago) {
        $messagesql .= 'AND m.timecreated > :lastpopuptime';
    }

    $message_users = $DB->get_records_sql($messagesql, array('userid'=>$USER->id, 'lastpopuptime'=>$USER->message_lastpopup));

    //if we have new messages to notify the user about
    if (!empty($message_users)) {

        $strmessages = '';
        if (count($message_users)>1) {
            $strmessages = get_string('unreadnewmessages', 'message', count($message_users));
        } else {
            $message_users = reset($message_users);

            //show who the message is from if its not a notification
            if (!$message_users->notification) {
                $strmessages = get_string('unreadnewmessage', 'message', fullname($message_users) );
            }

            //try to display the small version of the message
            $smallmessage = null;
            if (!empty($message_users->smallmessage)) {
                //display the first 200 chars of the message in the popup
                $textlib = textlib_get_instance();
                $smallmessage = null;
                if ($textlib->strlen($message_users->smallmessage) > 200) {
                    $smallmessage = $textlib->substr($message_users->smallmessage,0,200).'...';
                } else {
                    $smallmessage = $message_users->smallmessage;
                }

                //prevent html symbols being displayed
                if ($message_users->fullmessageformat == FORMAT_HTML) {
                    $smallmessage = html_to_text($smallmessage);
                } else {
                    $smallmessage = s($smallmessage);
                }
            } else if ($message_users->notification) {
                //its a notification with no smallmessage so just say they have a notification
                $smallmessage = get_string('unreadnewnotification', 'message');
            }
            if (!empty($smallmessage)) {
                $strmessages .= '<div id="usermessage">'.s($smallmessage).'</div>';
            }
        }

        $strgomessage = get_string('gotomessages', 'message');
        $strstaymessage = get_string('ignore','admin');

        $url = $CFG->wwwroot.'/message/index.php';
        $content =  html_writer::start_tag('div', array('id'=>'newmessageoverlay','class'=>'mdl-align')).
                        html_writer::start_tag('div', array('id'=>'newmessagetext')).
                            $strmessages.
                        html_writer::end_tag('div').

                        html_writer::start_tag('div', array('id'=>'newmessagelinks')).
                            html_writer::link($url, $strgomessage, array('id'=>'notificationyes')).'&nbsp;&nbsp;&nbsp;'.
                            html_writer::link('', $strstaymessage, array('id'=>'notificationno')).
                        html_writer::end_tag('div');
                    html_writer::end_tag('div');

        $PAGE->requires->js_init_call('M.core_message.init_notification', array('', $content, $url));

        $USER->message_lastpopup = time();
    }
}

/**
 * Used to make sure that $min <= $value <= $max
 *
 * Make sure that value is between min, and max
 *
 * @param int $min The minimum value
 * @param int $value The value to check
 * @param int $max The maximum value
 */
function bounded_number($min, $value, $max) {
    if($value < $min) {
        return $min;
    }
    if($value > $max) {
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
 * @global object
 * @global object
 * @global object
 * @return array
 */
function get_performance_info() {
    global $CFG, $PERF, $DB, $PAGE;

    $info = array();
    $info['html'] = '';         // holds userfriendly HTML representation
    $info['txt']  = me() . ' '; // holds log-friendly representation

    $info['realtime'] = microtime_diff($PERF->starttime, microtime());

    $info['html'] .= '<span class="timeused">'.$info['realtime'].' secs</span> ';
    $info['txt'] .= 'time: '.$info['realtime'].'s ';

    if (function_exists('memory_get_usage')) {
        $info['memory_total'] = memory_get_usage();
        $info['memory_growth'] = memory_get_usage() - $PERF->startmemory;
        $info['html'] .= '<span class="memoryused">RAM: '.display_size($info['memory_total']).'</span> ';
        $info['txt']  .= 'memory_total: '.$info['memory_total'].'B (' . display_size($info['memory_total']).') memory_growth: '.$info['memory_growth'].'B ('.display_size($info['memory_growth']).') ';
    }

    if (function_exists('memory_get_peak_usage')) {
        $info['memory_peak'] = memory_get_peak_usage();
        $info['html'] .= '<span class="memoryused">RAM peak: '.display_size($info['memory_peak']).'</span> ';
        $info['txt']  .= 'memory_peak: '.$info['memory_peak'].'B (' . display_size($info['memory_peak']).') ';
    }

    $inc = get_included_files();
    //error_log(print_r($inc,1));
    $info['includecount'] = count($inc);
    $info['html'] .= '<span class="included">Included '.$info['includecount'].' files</span> ';
    $info['txt']  .= 'includecount: '.$info['includecount'].' ';

    $filtermanager = filter_manager::instance();
    if (method_exists($filtermanager, 'get_performance_summary')) {
        list($filterinfo, $nicenames) = $filtermanager->get_performance_summary();
        $info = array_merge($filterinfo, $info);
        foreach ($filterinfo as $key => $value) {
            $info['html'] .= "<span class='$key'>$nicenames[$key]: $value </span> ";
            $info['txt'] .= "$key: $value ";
        }
    }

    $stringmanager = get_string_manager();
    if (method_exists($stringmanager, 'get_performance_summary')) {
        list($filterinfo, $nicenames) = $stringmanager->get_performance_summary();
        $info = array_merge($filterinfo, $info);
        foreach ($filterinfo as $key => $value) {
            $info['html'] .= "<span class='$key'>$nicenames[$key]: $value </span> ";
            $info['txt'] .= "$key: $value ";
        }
    }

     $jsmodules = $PAGE->requires->get_loaded_modules();
     if ($jsmodules) {
         $yuicount = 0;
         $othercount = 0;
         $details = '';
         foreach ($jsmodules as $module => $backtraces) {
             if (strpos($module, 'yui') === 0) {
                 $yuicount += 1;
             } else {
                 $othercount += 1;
             }
             $details .= "<div class='yui-module'><p>$module</p>";
             foreach ($backtraces as $backtrace) {
                 $details .= "<div class='backtrace'>$backtrace</div>";
             }
             $details .= '</div>';
         }
         $info['html'] .= "<span class='includedyuimodules'>Included YUI modules: $yuicount</span> ";
         $info['txt'] .= "includedyuimodules: $yuicount ";
         $info['html'] .= "<span class='includedjsmodules'>Other JavaScript modules: $othercount</span> ";
         $info['txt'] .= "includedjsmodules: $othercount ";
         // Slightly odd to output the details in a display: none div. The point
         // Is that it takes a lot of space, and if you care you can reveal it
         // using firebug.
         $info['html'] .= '<div id="yui-module-debug" class="notifytiny">'.$details.'</div>';
     }

    if (!empty($PERF->logwrites)) {
        $info['logwrites'] = $PERF->logwrites;
        $info['html'] .= '<span class="logwrites">Log DB writes '.$info['logwrites'].'</span> ';
        $info['txt'] .= 'logwrites: '.$info['logwrites'].' ';
    }

    $info['dbqueries'] = $DB->perf_get_reads().'/'.($DB->perf_get_writes() - $PERF->logwrites);
    $info['html'] .= '<span class="dbqueries">DB reads/writes: '.$info['dbqueries'].'</span> ';
    $info['txt'] .= 'db reads/writes: '.$info['dbqueries'].' ';

    if (!empty($PERF->profiling) && $PERF->profiling) {
        require_once($CFG->dirroot .'/lib/profilerlib.php');
        $info['html'] .= '<span class="profilinginfo">'.Profiler::get_profiling(array('-R')).'</span>';
    }

    if (function_exists('posix_times')) {
        $ptimes = posix_times();
        if (is_array($ptimes)) {
            foreach ($ptimes as $key => $val) {
                $info[$key] = $ptimes[$key] -  $PERF->startposixtimes[$key];
            }
            $info['html'] .= "<span class=\"posixtimes\">ticks: $info[ticks] user: $info[utime] sys: $info[stime] cuser: $info[cutime] csys: $info[cstime]</span> ";
            $info['txt'] .= "ticks: $info[ticks] user: $info[utime] sys: $info[stime] cuser: $info[cutime] csys: $info[cstime] ";
        }
    }

    // Grab the load average for the last minute
    // /proc will only work under some linux configurations
    // while uptime is there under MacOSX/Darwin and other unices
    if (is_readable('/proc/loadavg') && $loadavg = @file('/proc/loadavg')) {
        list($server_load) = explode(' ', $loadavg[0]);
        unset($loadavg);
    } else if ( function_exists('is_executable') && is_executable('/usr/bin/uptime') && $loadavg = `/usr/bin/uptime` ) {
        if (preg_match('/load averages?: (\d+[\.,:]\d+)/', $loadavg, $matches)) {
            $server_load = $matches[1];
        } else {
            trigger_error('Could not parse uptime output!');
        }
    }
    if (!empty($server_load)) {
        $info['serverload'] = $server_load;
        $info['html'] .= '<span class="serverload">Load average: '.$info['serverload'].'</span> ';
        $info['txt'] .= "serverload: {$info['serverload']} ";
    }

    // Display size of session if session started
    if (session_id()) {
        $info['sessionsize'] = display_size(strlen(session_encode()));
        $info['html'] .= '<span class="sessionsize">Session: ' . $info['sessionsize'] . '</span> ';
        $info['txt'] .= "Session: {$info['sessionsize']} ";
    }

/*    if (isset($rcache->hits) && isset($rcache->misses)) {
        $info['rcachehits'] = $rcache->hits;
        $info['rcachemisses'] = $rcache->misses;
        $info['html'] .= '<span class="rcache">Record cache hit/miss ratio : '.
            "{$rcache->hits}/{$rcache->misses}</span> ";
        $info['txt'] .= 'rcache: '.
            "{$rcache->hits}/{$rcache->misses} ";
    }*/
    $info['html'] = '<div class="performanceinfo siteinfo">'.$info['html'].'</div>';
    return $info;
}

/**
 * @todo Document this function linux people
 */
function apd_get_profiling() {
    return shell_exec('pprofp -u ' . ini_get('apd.dumpdir') . '/pprof.' . getmypid() . '.*');
}

/**
 * Delete directory or only it's content
 *
 * @param string $dir directory path
 * @param bool $content_only
 * @return bool success, true also if dir does not exist
 */
function remove_dir($dir, $content_only=false) {
    if (!file_exists($dir)) {
        // nothing to do
        return true;
    }
    $handle = opendir($dir);
    $result = true;
    while (false!==($item = readdir($handle))) {
        if($item != '.' && $item != '..') {
            if(is_dir($dir.'/'.$item)) {
                $result = remove_dir($dir.'/'.$item) && $result;
            }else{
                $result = unlink($dir.'/'.$item) && $result;
            }
        }
    }
    closedir($handle);
    if ($content_only) {
        return $result;
    }
    return rmdir($dir); // if anything left the result will be false, no need for && $result
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
    }
    else {
        $properties = get_object_vars( $obj );
    }
    return array_key_exists( $property, $properties );
}


/**
 * Detect a custom script replacement in the data directory that will
 * replace an existing moodle script
 *
 * @param string $urlpath path to the original script
 * @return string|bool full path name if a custom script exists, false if no custom script exists
 */
function custom_script_path($urlpath='') {
    global $CFG;

    // set default $urlpath, if necessary
    if (empty($urlpath)) {
        $urlpath = qualified_me(); // e.g. http://www.this-server.com/moodle/this-script.php
    }

    // $urlpath is invalid if it is empty or does not start with the Moodle wwwroot
    if (empty($urlpath) or (strpos($urlpath, $CFG->wwwroot) === false )) {
        return false;
    }

    // replace wwwroot with the path to the customscripts folder and clean path
    $scriptpath = $CFG->customscripts . clean_param(substr($urlpath, strlen($CFG->wwwroot)), PARAM_PATH);

    // remove the query string, if any
    if (($strpos = strpos($scriptpath, '?')) !== false) {
        $scriptpath = substr($scriptpath, 0, $strpos);
    }

    // remove trailing slashes, if any
    $scriptpath = rtrim($scriptpath, '/\\');

    // append index.php, if necessary
    if (is_dir($scriptpath)) {
        $scriptpath .= '/index.php';
    }

    // check the custom script exists
    if (file_exists($scriptpath)) {
        return $scriptpath;
    } else {
        return false;
    }
}

/**
 * Returns whether or not the user object is a remote MNET user. This function
 * is in moodlelib because it does not rely on loading any of the MNET code.
 *
 * @global object
 * @param object $user A valid user object
 * @return bool        True if the user is from a remote Moodle.
 */
function is_mnet_remote_user($user) {
    global $CFG;

    if (!isset($CFG->mnet_localhost_id)) {
        include_once $CFG->dirroot . '/mnet/lib.php';
        $env = new mnet_environment();
        $env->init();
        unset($env);
    }

    return (!empty($user->mnethostid) && $user->mnethostid != $CFG->mnet_localhost_id);
}

/**
 * This function will search for browser prefereed languages, setting Moodle
 * to use the best one available if $SESSION->lang is undefined
 *
 * @global object
 * @global object
 * @global object
 */
function setup_lang_from_browser() {

    global $CFG, $SESSION, $USER;

    if (!empty($SESSION->lang) or !empty($USER->lang) or empty($CFG->autolang)) {
        // Lang is defined in session or user profile, nothing to do
        return;
    }

    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { // There isn't list of browser langs, nothing to do
        return;
    }

/// Extract and clean langs from headers
    $rawlangs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $rawlangs = str_replace('-', '_', $rawlangs);         // we are using underscores
    $rawlangs = explode(',', $rawlangs);                  // Convert to array
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

/// Look for such langs under standard locations
    foreach ($langs as $lang) {
        $lang = strtolower(clean_param($lang, PARAM_SAFEDIR)); // clean it properly for include
        if (get_string_manager()->translation_exists($lang, false)) {
            $SESSION->lang = $lang; /// Lang exists, set it in session
            break; /// We have finished. Go out
        }
    }
    return;
}

/**
 * check if $url matches anything in proxybypass list
 *
 * any errors just result in the proxy being used (least bad)
 *
 * @global object
 * @param string $url url to check
 * @return boolean true if we should bypass the proxy
 */
function is_proxybypass( $url ) {
    global $CFG;

    // sanity check
    if (empty($CFG->proxyhost) or empty($CFG->proxybypass)) {
        return false;
    }

    // get the host part out of the url
    if (!$host = parse_url( $url, PHP_URL_HOST )) {
        return false;
    }

    // get the possible bypass hosts into an array
    $matches = explode( ',', $CFG->proxybypass );

    // check for a match
    // (IPs need to match the left hand side and hosts the right of the url,
    // but we can recklessly check both as there can't be a false +ve)
    $bypass = false;
    foreach ($matches as $match) {
        $match = trim($match);

        // try for IP match (Left side)
        $lhs = substr($host,0,strlen($match));
        if (strcasecmp($match,$lhs)==0) {
            return true;
        }

        // try for host match (Right side)
        $rhs = substr($host,-strlen($match));
        if (strcasecmp($match,$rhs)==0) {
            return true;
        }
    }

    // nothing matched.
    return false;
}


////////////////////////////////////////////////////////////////////////////////

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
    $class_vars = get_class_vars(get_class($object));
    $class_vars = array_keys($class_vars);
    return in_array($var, $class_vars);
}

/**
 * Returns an array without repeated objects.
 * This function is similar to array_unique, but for arrays that have objects as values
 *
 * @param array $array
 * @param bool $keep_key_assoc
 * @return array
 */
function object_array_unique($array, $keep_key_assoc = true) {
    $duplicate_keys = array();
    $tmp         = array();

    foreach ($array as $key=>$val) {
        // convert objects to arrays, in_array() does not support objects
        if (is_object($val)) {
            $val = (array)$val;
        }

        if (!in_array($val, $tmp)) {
            $tmp[] = $val;
        } else {
            $duplicate_keys[] = $key;
        }
    }

    foreach ($duplicate_keys as $key) {
        unset($array[$key]);
    }

    return $keep_key_assoc ? $array : array_values($array);
}

/**
 * Is a userid the primary administrator?
 *
 * @param int $userid int id of user to check
 * @return boolean
 */
function is_primary_admin($userid){
    $primaryadmin =  get_admin();

    if($userid == $primaryadmin->id){
        return true;
    }else{
        return false;
    }
}

/**
 * Returns the site identifier
 *
 * @global object
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
 */
function check_consecutive_identical_characters($password, $maxchars) {

    if ($maxchars < 1) {
        return true; // 0 is to disable this check
    }
    if (strlen($password) <= $maxchars) {
        return true; // too short to fail this test
    }

    $previouschar = '';
    $consecutivecount = 1;
    foreach (str_split($password) as $char) {
        if ($char != $previouschar) {
            $consecutivecount = 1;
        }
        else {
            $consecutivecount++;
            if ($consecutivecount > $maxchars) {
                return false; // check failed already
            }
        }

        $previouschar = $char;
    }

    return true;
}

/**
 * helper function to do partial function binding
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
 * $param mixed $arg1.. $argv arguments to partially bind with
 *
 * @return callback
 */
function partial() {
    if (!class_exists('partial')) {
        class partial{
            var $values = array();
            var $func;

            function __construct($func, $args) {
                $this->values = $args;
                $this->func = $func;
            }

            function method() {
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
 * @return mnet_remote_client the equivalent of old $MNET_REMOTE_CLIENT global
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
 * to setup the object returned by {@see get_mnet_remote_client}
 *
 * @param mnet_remote_client $client the client to set up
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
        if ($CFG->defaulthomepage == HOMEPAGE_MY) {
            return HOMEPAGE_MY;
        } else {
            return (int)get_user_preferences('user_home_page_preference', HOMEPAGE_MY);
        }
    }
    return HOMEPAGE_SITE;
}
