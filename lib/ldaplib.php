<?php

/**
 * ldaplib.php - LDAP functions & data library
 *
 * Library file of miscellaneous general-purpose LDAP functions and
 * data structures, useful for both ldap authentication (or ldap based
 * authentication like CAS) and enrolment plugins.
 *
 * @author     Iñaki Arenaza
 * @package    core
 * @subpackage lib
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @copyright  2010 onwards Iñaki Arenaza
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// rootDSE is defined as the root of the directory data tree on a directory server.
if (!defined('ROOTDSE')) {
    define ('ROOTDSE', '');
}

// Default page size when using LDAP paged results
if (!defined('LDAP_DEFAULT_PAGESIZE')) {
    define('LDAP_DEFAULT_PAGESIZE', 250);
}

/**
 * Returns predefined user types
 *
 * @return array of predefined user types
 */
function ldap_supported_usertypes() {
    $types = array();
    $types['edir'] = 'Novell Edirectory';
    $types['rfc2307'] = 'posixAccount (rfc2307)';
    $types['rfc2307bis'] = 'posixAccount (rfc2307bis)';
    $types['samba'] = 'sambaSamAccount (v.3.0.7)';
    $types['ad'] = 'MS ActiveDirectory';
    $types['default'] = get_string('default');
    return $types;
}

/**
 * Initializes needed variables for ldap-module
 *
 * Uses names defined in ldap_supported_usertypes.
 * $default is first defined as:
 * $default['pseudoname'] = array(
 *                      'typename1' => 'value',
 *                      'typename2' => 'value'
 *                      ....
 *                      );
 *
 * @return array of default values
 */
function ldap_getdefaults() {
    // All the values have to be written in lowercase, even if the
    // standard LDAP attributes are mixed-case
    $default['objectclass'] = array(
                        'edir' => 'user',
                        'rfc2307' => 'posixaccount',
                        'rfc2307bis' => 'posixaccount',
                        'samba' => 'sambasamaccount',
                        'ad' => 'user',
                        'default' => '*'
                        );
    $default['user_attribute'] = array(
                        'edir' => 'cn',
                        'rfc2307' => 'uid',
                        'rfc2307bis' => 'uid',
                        'samba' => 'uid',
                        'ad' => 'cn',
                        'default' => 'cn'
                        );
    $default['memberattribute'] = array(
                        'edir' => 'member',
                        'rfc2307' => 'member',
                        'rfc2307bis' => 'member',
                        'samba' => 'member',
                        'ad' => 'member',
                        'default' => 'member'
                        );
    $default['memberattribute_isdn'] = array(
                        'edir' => '1',
                        'rfc2307' => '0',
                        'rfc2307bis' => '1',
                        'samba' => '0', // is this right?
                        'ad' => '1',
                        'default' => '0'
                        );
    $default['expireattr'] = array (
                        'edir' => 'passwordexpirationtime',
                        'rfc2307' => 'shadowexpire',
                        'rfc2307bis' => 'shadowexpire',
                        'samba' => '', // No support yet
                        'ad' => 'pwdlastset',
                        'default' => ''
                        );
    return $default;
}

/**
 * Checks if user belongs to specific group(s) or is in a subtree.
 *
 * Returns true if user belongs to a group in grupdns string OR if the
 * DN of the user is in a subtree of the DN provided as "group"
 *
 * @param mixed $ldapconnection A valid LDAP connection.
 * @param string $userid LDAP user id (dn/cn/uid/...) to test membership for.
 * @param array $group_dns arrary of group dn
 * @param string $member_attrib the name of the membership attribute.
 * @return boolean
 *
 */
function ldap_isgroupmember($ldapconnection, $userid, $group_dns, $member_attrib) {
    if (empty($ldapconnection) || empty($userid) || empty($group_dns) || empty($member_attrib)) {
        return false;
    }

    $result = false;
    foreach ($group_dns as $group) {
        $group = trim($group);
        if (empty($group)) {
            continue;
        }

        // Check cheaply if the user's DN sits in a subtree of the
        // "group" DN provided. Granted, this isn't a proper LDAP
        // group, but it's a popular usage.
        if (stripos(strrev(strtolower($userid)), strrev(strtolower($group))) === 0) {
            $result = true;
            break;
        }

        $search = ldap_read($ldapconnection, $group,
                            '('.$member_attrib.'='.ldap_filter_addslashes($userid).')',
                            array($member_attrib));

        if (!empty($search) && ldap_count_entries($ldapconnection, $search)) {
            $info = ldap_get_entries_moodle($ldapconnection, $search);
            if (count($info) > 0 ) {
                // User is member of group
                $result = true;
                break;
            }
        }
    }

    return $result;
}

/**
 * Tries connect to specified ldap servers. Returns a valid LDAP
 * connection or false.
 *
 * @param string $host_url
 * @param integer $ldap_version either 2 (LDAPv2) or 3 (LDAPv3).
 * @param string $user_type the configured user type for this connection.
 * @param string $bind_dn the binding user dn. If an emtpy string, anonymous binding is used.
 * @param string $bind_pw the password for the binding user. Ignored for anonymous bindings.
 * @param boolean $opt_deref whether to set LDAP_OPT_DEREF on this connection or not.
 * @param string &$debuginfo the debugging information in case the connection fails.
 * @param boolean $start_tls whether to use LDAP with TLS (not to be confused with LDAP+SSL)
 * @return mixed connection result or false.
 */
function ldap_connect_moodle($host_url, $ldap_version, $user_type, $bind_dn, $bind_pw, $opt_deref, &$debuginfo, $start_tls=false) {
    if (empty($host_url) || empty($ldap_version) || empty($user_type)) {
        $debuginfo = 'No LDAP Host URL, Version or User Type specified in your LDAP settings';
        return false;
    }

    $debuginfo = '';
    $urls = explode(';', $host_url);
    foreach ($urls as $server) {
        $server = trim($server);
        if (empty($server)) {
            continue;
        }

        $connresult = ldap_connect($server); // ldap_connect returns ALWAYS true

        if (!empty($ldap_version)) {
            ldap_set_option($connresult, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);
        }

        // Fix MDL-10921
        if ($user_type === 'ad') {
            ldap_set_option($connresult, LDAP_OPT_REFERRALS, 0);
        }

        if (!empty($opt_deref)) {
            ldap_set_option($connresult, LDAP_OPT_DEREF, $opt_deref);
        }

        if ($start_tls && (!ldap_start_tls($connresult))) {
            $debuginfo .= "Server: '$server', Connection: '$connresult', STARTTLS failed.\n";
            continue;
        }

        if (!empty($bind_dn)) {
            $bindresult = @ldap_bind($connresult, $bind_dn, $bind_pw);
        } else {
            // Bind anonymously
            $bindresult = @ldap_bind($connresult);
        }

        if ($bindresult) {
            return $connresult;
        }

        $debuginfo .= "Server: '$server', Connection: '$connresult', Bind result: '$bindresult'\n";
    }

    // If any of servers were alive we have already returned connection.
    return false;
}

/**
 * Search specified contexts for username and return the user dn like:
 * cn=username,ou=suborg,o=org
 *
 * @param mixed $ldapconnection a valid LDAP connection.
 * @param mixed $username username (external LDAP encoding, no db slashes).
 * @param array $contexts contexts to look for the user.
 * @param string $objectclass objectlass of the user (in LDAP filter syntax).
 * @param string $search_attrib the attribute use to look for the user.
 * @param boolean $search_sub whether to search subcontexts or not.
 * @return mixed the user dn (external LDAP encoding, no db slashes) or false
 *
 */
function ldap_find_userdn($ldapconnection, $username, $contexts, $objectclass, $search_attrib, $search_sub) {
    if (empty($ldapconnection) || empty($username) || empty($contexts) || empty($objectclass) || empty($search_attrib)) {
        return false;
    }

    // Default return value
    $ldap_user_dn = false;

    // Get all contexts and look for first matching user
    foreach ($contexts as $context) {
        $context = trim($context);
        if (empty($context)) {
            continue;
        }

        if ($search_sub) {
            $ldap_result = @ldap_search($ldapconnection, $context,
                                        '(&'.$objectclass.'('.$search_attrib.'='.ldap_filter_addslashes($username).'))',
                                        array($search_attrib));
        } else {
            $ldap_result = @ldap_list($ldapconnection, $context,
                                      '(&'.$objectclass.'('.$search_attrib.'='.ldap_filter_addslashes($username).'))',
                                      array($search_attrib));
        }

        if (!$ldap_result) {
            continue; // Not found in this context.
        }

        $entry = ldap_first_entry($ldapconnection, $ldap_result);
        if ($entry) {
            $ldap_user_dn = ldap_get_dn($ldapconnection, $entry);
            break;
        }
    }

    return $ldap_user_dn;
}

/**
 * Returns values like ldap_get_entries but is binary compatible and
 * returns all attributes as array.
 *
 * @param mixed $ldapconnection A valid LDAP connection
 * @param mixed $searchresult A search result from ldap_search, ldap_list, etc.
 * @return array ldap-entries with lower-cased attributes as indexes
 */
function ldap_get_entries_moodle($ldapconnection, $searchresult) {
    if (empty($ldapconnection) || empty($searchresult)) {
        return array();
    }

    $i = 0;
    $result = array();
    $entry = ldap_first_entry($ldapconnection, $searchresult);
    if (!$entry) {
        return array();
    }
    do {
        $attributes = array_change_key_case(ldap_get_attributes($ldapconnection, $entry), CASE_LOWER);
        for ($j = 0; $j < $attributes['count']; $j++) {
            $values = ldap_get_values_len($ldapconnection, $entry, $attributes[$j]);
            if (is_array($values)) {
                $result[$i][$attributes[$j]] = $values;
            } else {
                $result[$i][$attributes[$j]] = array($values);
            }
        }
        $i++;
    } while ($entry = ldap_next_entry($ldapconnection, $entry));

    return ($result);
}

/**
 * Quote control characters in texts used in LDAP filters - see RFC 4515/2254
 *
 * @param string filter string to quote
 * @return string the filter string quoted
 */
function ldap_filter_addslashes($text) {
    $text = str_replace('\\', '\\5c', $text);
    $text = str_replace(array('*',    '(',    ')',    "\0"),
                        array('\\2a', '\\28', '\\29', '\\00'), $text);
    return $text;
}

if(!defined('LDAP_DN_SPECIAL_CHARS')) {
    define('LDAP_DN_SPECIAL_CHARS', 0);
}
if(!defined('LDAP_DN_SPECIAL_CHARS_QUOTED_NUM')) {
    define('LDAP_DN_SPECIAL_CHARS_QUOTED_NUM', 1);
}
if(!defined('LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA')) {
    define('LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA', 2);
}

/**
 * The order of the special characters in these arrays _IS IMPORTANT_.
 * Make sure '\\5C' (and '\\') are the first elements of the arrays.
 * Otherwise we'll double replace '\' with '\5C' which is Bad(tm)
 */
function ldap_get_dn_special_chars() {
    return array (
        LDAP_DN_SPECIAL_CHARS              => array('\\',  ' ',   '"',   '#',   '+',   ',',   ';',   '<',   '=',   '>',   "\0"),
        LDAP_DN_SPECIAL_CHARS_QUOTED_NUM   => array('\\5c','\\20','\\22','\\23','\\2b','\\2c','\\3b','\\3c','\\3d','\\3e','\\00'),
        LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA => array('\\\\','\\ ', '\\"', '\\#', '\\+', '\\,', '\\;', '\\<', '\\>', '\\=', '\\00'),
        );
}

/**
 * Quote control characters in distinguished names used in LDAP - See RFC 4514/2253
 *
 * @param string The text to quote
 * @return string The text quoted
 */
function ldap_addslashes($text) {
    $special_dn_chars = ldap_get_dn_special_chars();

    $text = str_replace ($special_dn_chars[LDAP_DN_SPECIAL_CHARS],
                         $special_dn_chars[LDAP_DN_SPECIAL_CHARS_QUOTED_NUM],
                         $text);
    return $text;
}

/**
 * Unquote control characters in distinguished names used in LDAP - See RFC 4514/2253
 *
 * @param string The text quoted
 * @return string The text unquoted
 */
function ldap_stripslashes($text) {
    $special_dn_chars = ldap_get_dn_special_chars();

    // First unquote the simply backslashed special characters. If we
    // do it the other way, we remove too many slashes.
    $text = str_replace($special_dn_chars[LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA],
                        $special_dn_chars[LDAP_DN_SPECIAL_CHARS],
                        $text);

    // Next unquote the 'numerically' quoted characters. We don't use
    // LDAP_DN_SPECIAL_CHARS_QUOTED_NUM because the standard allows us
    // to quote any character with this encoding, not just the special
    // ones.
    $text = preg_replace('/\\\([0-9A-Fa-f]{2})/e', "chr(hexdec('\\1'))", $text);

    return $text;
}


/**
 * Check if we use LDAP version 3, otherwise the server cannot use them.
 *
 * @param ldapversion integer The LDAP protocol version we use.
 *
 * @return boolean true is paged results can be used, false otherwise.
 */
function ldap_paged_results_supported($ldapversion) {
    if ((int)$ldapversion === 3) {
        return true;
    }

    return false;
}
