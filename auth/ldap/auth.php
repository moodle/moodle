<?php

/**
 * @author Martin Dougiamas
 * @author Iñaki Arenaza
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: LDAP Authentication
 *
 * Authentication using LDAP (Lightweight Directory Access Protocol).
 *
 * 2006-08-28  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

// See http://support.microsoft.com/kb/305144 to interprete these values.
if (!defined('AUTH_AD_ACCOUNTDISABLE')) {
    define('AUTH_AD_ACCOUNTDISABLE', 0x0002);
}
if (!defined('AUTH_AD_NORMAL_ACCOUNT')) {
    define('AUTH_AD_NORMAL_ACCOUNT', 0x0200);
}
if (!defined('AUTH_NTLMTIMEOUT')) {  // timewindow for the NTLM SSO process, in secs...
    define('AUTH_NTLMTIMEOUT', 10);
}

// UF_DONT_EXPIRE_PASSWD value taken from MSDN directly
if (!defined('UF_DONT_EXPIRE_PASSWD')) {
    define ('UF_DONT_EXPIRE_PASSWD', 0x00010000);
}

// The Posix uid and gid of the 'nobody' account and 'nogroup' group.
if (!defined('AUTH_UID_NOBODY')) {
    define('AUTH_UID_NOBODY', -2);
}
if (!defined('AUTH_GID_NOGROUP')) {
    define('AUTH_GID_NOGROUP', -2);
}

// Regular expressions for a valid NTLM username and domain name.
if (!defined('AUTH_NTLM_VALID_USERNAME')) {
    define('AUTH_NTLM_VALID_USERNAME', '[^/\\\\\\\\\[\]:;|=,+*?<>@"]+');
}
if (!defined('AUTH_NTLM_VALID_DOMAINNAME')) {
    define('AUTH_NTLM_VALID_DOMAINNAME', '[^\\\\\\\\\/:*?"<>|]+');
}
// Default format for remote users if using NTLM SSO
if (!defined('AUTH_NTLM_DEFAULT_FORMAT')) {
    define('AUTH_NTLM_DEFAULT_FORMAT', '%domain%\\%username%');
}

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->libdir.'/ldaplib.php');

/**
 * LDAP authentication plugin.
 */
class auth_plugin_ldap extends auth_plugin_base {

    /**
     * Init plugin config from database settings depending on the plugin auth type.
     */
    function init_plugin($authtype) {
        $this->pluginconfig = 'auth/'.$authtype;
        $this->config = get_config($this->pluginconfig);
        if (empty($this->config->ldapencoding)) {
            $this->config->ldapencoding = 'utf-8';
        }
        if (empty($this->config->user_type)) {
            $this->config->user_type = 'default';
        }

        $ldap_usertypes = ldap_supported_usertypes();
        $this->config->user_type_name = $ldap_usertypes[$this->config->user_type];
        unset($ldap_usertypes);

        $default = ldap_getdefaults();

        // Use defaults if values not given
        foreach ($default as $key => $value) {
            // watch out - 0, false are correct values too
            if (!isset($this->config->{$key}) or $this->config->{$key} == '') {
                $this->config->{$key} = $value[$this->config->user_type];
            }
        }

        // Hack prefix to objectclass
        if (empty($this->config->objectclass)) {
            // Can't send empty filter
            $this->config->objectclass = '(objectClass=*)';
        } else if (stripos($this->config->objectclass, 'objectClass=') === 0) {
            // Value is 'objectClass=some-string-here', so just add ()
            // around the value (filter _must_ have them).
            $this->config->objectclass = '('.$this->config->objectclass.')';
        } else if (strpos($this->config->objectclass, '(') !== 0) {
            // Value is 'some-string-not-starting-with-left-parentheses',
            // which is assumed to be the objectClass matching value.
            // So build a valid filter with it.
            $this->config->objectclass = '(objectClass='.$this->config->objectclass.')';
        } else {
            // There is an additional possible value
            // '(some-string-here)', that can be used to specify any
            // valid filter string, to select subsets of users based
            // on any criteria. For example, we could select the users
            // whose objectClass is 'user' and have the
            // 'enabledMoodleUser' attribute, with something like:
            //
            //   (&(objectClass=user)(enabledMoodleUser=1))
            //
            // In this particular case we don't need to do anything,
            // so leave $this->config->objectclass as is.
        }
    }

    /**
     * Constructor with initialisation.
     */
    function auth_plugin_ldap() {
        $this->authtype = 'ldap';
        $this->roleauth = 'auth_ldap';
        $this->errorlogtag = '[AUTH LDAP] ';
        $this->init_plugin($this->authtype);
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (without system magic quotes)
     * @param string $password The password (without system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        if (! function_exists('ldap_bind')) {
            print_error('auth_ldapnotinstalled', 'auth_ldap');
            return false;
        }

        if (!$username or !$password) {    // Don't allow blank usernames or passwords
            return false;
        }

        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);
        $extpassword = textlib::convert($password, 'utf-8', $this->config->ldapencoding);

        // Before we connect to LDAP, check if this is an AD SSO login
        // if we succeed in this block, we'll return success early.
        //
        $key = sesskey();
        if (!empty($this->config->ntlmsso_enabled) && $key === $password) {
            $cf = get_cache_flags($this->pluginconfig.'/ntlmsess');
            // We only get the cache flag if we retrieve it before
            // it expires (AUTH_NTLMTIMEOUT seconds).
            if (!isset($cf[$key]) || $cf[$key] === '') {
                return false;
            }

            $sessusername = $cf[$key];
            if ($username === $sessusername) {
                unset($sessusername);
                unset($cf);

                // Check that the user is inside one of the configured LDAP contexts
                $validuser = false;
                $ldapconnection = $this->ldap_connect();
                // if the user is not inside the configured contexts,
                // ldap_find_userdn returns false.
                if ($this->ldap_find_userdn($ldapconnection, $extusername)) {
                    $validuser = true;
                }
                $this->ldap_close();

                // Shortcut here - SSO confirmed
                return $validuser;
            }
        } // End SSO processing
        unset($key);

        $ldapconnection = $this->ldap_connect();
        $ldap_user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);

        // If ldap_user_dn is empty, user does not exist
        if (!$ldap_user_dn) {
            $this->ldap_close();
            return false;
        }

        // Try to bind with current username and password
        $ldap_login = @ldap_bind($ldapconnection, $ldap_user_dn, $extpassword);
        $this->ldap_close();
        if ($ldap_login) {
            return true;
        }
        return false;
    }

    /**
     * Reads user information from ldap and returns it in array()
     *
     * Function should return all information available. If you are saving
     * this information to moodle user-table you should honor syncronization flags
     *
     * @param string $username username
     *
     * @return mixed array with no magic quotes or false on error
     */
    function get_userinfo($username) {
        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();
        if(!($user_dn = $this->ldap_find_userdn($ldapconnection, $extusername))) {
            $this->ldap_close();
            return false;
        }

        $search_attribs = array();
        $attrmap = $this->ldap_attributes();
        foreach ($attrmap as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                if (!in_array($value, $search_attribs)) {
                    array_push($search_attribs, $value);
                }
            }
        }

        if (!$user_info_result = ldap_read($ldapconnection, $user_dn, '(objectClass=*)', $search_attribs)) {
            $this->ldap_close();
            return false; // error!
        }

        $user_entry = ldap_get_entries_moodle($ldapconnection, $user_info_result);
        if (empty($user_entry)) {
            $this->ldap_close();
            return false; // entry not found
        }

        $result = array();
        foreach ($attrmap as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            $ldapval = NULL;
            foreach ($values as $value) {
                $entry = array_change_key_case($user_entry[0], CASE_LOWER);
                if (($value == 'dn') || ($value == 'distinguishedname')) {
                    $result[$key] = $user_dn;
                    continue;
                }
                if (!array_key_exists($value, $entry)) {
                    continue; // wrong data mapping!
                }
                if (is_array($entry[$value])) {
                    $newval = textlib::convert($entry[$value][0], $this->config->ldapencoding, 'utf-8');
                } else {
                    $newval = textlib::convert($entry[$value], $this->config->ldapencoding, 'utf-8');
                }
                if (!empty($newval)) { // favour ldap entries that are set
                    $ldapval = $newval;
                }
            }
            if (!is_null($ldapval)) {
                $result[$key] = $ldapval;
            }
        }

        $this->ldap_close();
        return $result;
    }

    /**
     * Reads user information from ldap and returns it in an object
     *
     * @param string $username username (with system magic quotes)
     * @return mixed object or false on error
     */
    function get_userinfo_asobj($username) {
        $user_array = $this->get_userinfo($username);
        if ($user_array == false) {
            return false; //error or not found
        }
        $user_array = truncate_userinfo($user_array);
        $user = new stdClass();
        foreach ($user_array as $key=>$value) {
            $user->{$key} = $value;
        }
        return $user;
    }

    /**
     * Returns all usernames from LDAP
     *
     * get_userlist returns all usernames from LDAP
     *
     * @return array
     */
    function get_userlist() {
        return $this->ldap_get_userlist("({$this->config->user_attribute}=*)");
    }

    /**
     * Checks if user exists on LDAP
     *
     * @param string $username
     */
    function user_exists($username) {
        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);

        // Returns true if given username exists on ldap
        $users = $this->ldap_get_userlist('('.$this->config->user_attribute.'='.ldap_filter_addslashes($extusername).')');
        return count($users);
    }

    /**
     * Creates a new user on LDAP.
     * By using information in userobject
     * Use user_exists to prevent duplicate usernames
     *
     * @param mixed $userobject  Moodle userobject
     * @param mixed $plainpass   Plaintext password
     */
    function user_create($userobject, $plainpass) {
        $extusername = textlib::convert($userobject->username, 'utf-8', $this->config->ldapencoding);
        $extpassword = textlib::convert($plainpass, 'utf-8', $this->config->ldapencoding);

        switch ($this->config->passtype) {
            case 'md5':
                $extpassword = '{MD5}' . base64_encode(pack('H*', md5($extpassword)));
                break;
            case 'sha1':
                $extpassword = '{SHA}' . base64_encode(pack('H*', sha1($extpassword)));
                break;
            case 'plaintext':
            default:
                break; // plaintext
        }

        $ldapconnection = $this->ldap_connect();
        $attrmap = $this->ldap_attributes();

        $newuser = array();

        foreach ($attrmap as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                if (!empty($userobject->$key) ) {
                    $newuser[$value] = textlib::convert($userobject->$key, 'utf-8', $this->config->ldapencoding);
                }
            }
        }

        //Following sets all mandatory and other forced attribute values
        //User should be creted as login disabled untill email confirmation is processed
        //Feel free to add your user type and send patches to paca@sci.fi to add them
        //Moodle distribution

        switch ($this->config->user_type)  {
            case 'edir':
                $newuser['objectClass']   = array('inetOrgPerson', 'organizationalPerson', 'person', 'top');
                $newuser['uniqueId']      = $extusername;
                $newuser['logindisabled'] = 'TRUE';
                $newuser['userpassword']  = $extpassword;
                $uadd = ldap_add($ldapconnection, $this->config->user_attribute.'='.ldap_addslashes($extusername).','.$this->config->create_context, $newuser);
                break;
            case 'rfc2307':
            case 'rfc2307bis':
                // posixAccount object class forces us to specify a uidNumber
                // and a gidNumber. That is quite complicated to generate from
                // Moodle without colliding with existing numbers and without
                // race conditions. As this user is supposed to be only used
                // with Moodle (otherwise the user would exist beforehand) and
                // doesn't need to login into a operating system, we assign the
                // user the uid of user 'nobody' and gid of group 'nogroup'. In
                // addition to that, we need to specify a home directory. We
                // use the root directory ('/') as the home directory, as this
                // is the only one can always be sure exists. Finally, even if
                // it's not mandatory, we specify '/bin/false' as the login
                // shell, to prevent the user from login in at the operating
                // system level (Moodle ignores this).

                $newuser['objectClass']   = array('posixAccount', 'inetOrgPerson', 'organizationalPerson', 'person', 'top');
                $newuser['cn']            = $extusername;
                $newuser['uid']           = $extusername;
                $newuser['uidNumber']     = AUTH_UID_NOBODY;
                $newuser['gidNumber']     = AUTH_GID_NOGROUP;
                $newuser['homeDirectory'] = '/';
                $newuser['loginShell']    = '/bin/false';

                // IMPORTANT:
                // We have to create the account locked, but posixAccount has
                // no attribute to achive this reliably. So we are going to
                // modify the password in a reversable way that we can later
                // revert in user_activate().
                //
                // Beware that this can be defeated by the user if we are not
                // using MD5 or SHA-1 passwords. After all, the source code of
                // Moodle is available, and the user can see the kind of
                // modification we are doing and 'undo' it by hand (but only
                // if we are using plain text passwords).
                //
                // Also bear in mind that you need to use a binding user that
                // can create accounts and has read/write privileges on the
                // 'userPassword' attribute for this to work.

                $newuser['userPassword']  = '*'.$extpassword;
                $uadd = ldap_add($ldapconnection, $this->config->user_attribute.'='.ldap_addslashes($extusername).','.$this->config->create_context, $newuser);
                break;
            case 'ad':
                // User account creation is a two step process with AD. First you
                // create the user object, then you set the password. If you try
                // to set the password while creating the user, the operation
                // fails.

                // Passwords in Active Directory must be encoded as Unicode
                // strings (UCS-2 Little Endian format) and surrounded with
                // double quotes. See http://support.microsoft.com/?kbid=269190
                if (!function_exists('mb_convert_encoding')) {
                    print_error('auth_ldap_no_mbstring', 'auth_ldap');
                }

                // Check for invalid sAMAccountName characters.
                if (preg_match('#[/\\[\]:;|=,+*?<>@"]#', $extusername)) {
                    print_error ('auth_ldap_ad_invalidchars', 'auth_ldap');
                }

                // First create the user account, and mark it as disabled.
                $newuser['objectClass'] = array('top', 'person', 'user', 'organizationalPerson');
                $newuser['sAMAccountName'] = $extusername;
                $newuser['userAccountControl'] = AUTH_AD_NORMAL_ACCOUNT |
                                                 AUTH_AD_ACCOUNTDISABLE;
                $userdn = 'cn='.ldap_addslashes($extusername).','.$this->config->create_context;
                if (!ldap_add($ldapconnection, $userdn, $newuser)) {
                    print_error('auth_ldap_ad_create_req', 'auth_ldap');
                }

                // Now set the password
                unset($newuser);
                $newuser['unicodePwd'] = mb_convert_encoding('"' . $extpassword . '"',
                                                             'UCS-2LE', 'UTF-8');
                if(!ldap_modify($ldapconnection, $userdn, $newuser)) {
                    // Something went wrong: delete the user account and error out
                    ldap_delete ($ldapconnection, $userdn);
                    print_error('auth_ldap_ad_create_req', 'auth_ldap');
                }
                $uadd = true;
                break;
            default:
               print_error('auth_ldap_unsupportedusertype', 'auth_ldap', '', $this->config->user_type_name);
        }
        $this->ldap_close();
        return $uadd;
    }

    /**
     * Returns true if plugin allows resetting of password from moodle.
     *
     * @return bool
     */
    function can_reset_password() {
        return !empty($this->config->stdchangepassword);
    }

    /**
     * Returns true if plugin allows signup and user creation.
     *
     * @return bool
     */
    function can_signup() {
        return (!empty($this->config->auth_user_create) and !empty($this->config->create_context));
    }

    /**
     * Sign up a new user ready for confirmation.
     * Password is passed in plaintext.
     *
     * @param object $user new user object
     * @param boolean $notify print notice with link and terminate
     */
    function user_signup($user, $notify=true) {
        global $CFG, $DB, $PAGE, $OUTPUT;

        require_once($CFG->dirroot.'/user/profile/lib.php');

        if ($this->user_exists($user->username)) {
            print_error('auth_ldap_user_exists', 'auth_ldap');
        }

        $plainslashedpassword = $user->password;
        unset($user->password);

        if (! $this->user_create($user, $plainslashedpassword)) {
            print_error('auth_ldap_create_error', 'auth_ldap');
        }

        $user->id = $DB->insert_record('user', $user);

        // Save any custom profile field information
        profile_save_data($user);

        $this->update_user_record($user->username);
        update_internal_user_password($user, $plainslashedpassword);

        $user = $DB->get_record('user', array('id'=>$user->id));
        events_trigger('user_created', $user);

        if (! send_confirmation_email($user)) {
            print_error('noemail', 'auth_ldap');
        }

        if ($notify) {
            $emailconfirm = get_string('emailconfirm');
            $PAGE->set_url('/auth/ldap/auth.php');
            $PAGE->navbar->add($emailconfirm);
            $PAGE->set_title($emailconfirm);
            $PAGE->set_heading($emailconfirm);
            echo $OUTPUT->header();
            notice(get_string('emailconfirmsent', '', $user->email), "{$CFG->wwwroot}/index.php");
        } else {
            return true;
        }
    }

    /**
     * Returns true if plugin allows confirming of new users.
     *
     * @return bool
     */
    function can_confirm() {
        return $this->can_signup();
    }

    /**
     * Confirm the new user as registered.
     *
     * @param string $username
     * @param string $confirmsecret
     */
    function user_confirm($username, $confirmsecret) {
        global $DB;

        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->confirmed) {
                return AUTH_CONFIRM_ALREADY;

            } else if ($user->auth != $this->authtype) {
                return AUTH_CONFIRM_ERROR;

            } else if ($user->secret == $confirmsecret) {   // They have provided the secret key to get in
                if (!$this->user_activate($username)) {
                    return AUTH_CONFIRM_FAIL;
                }
                $DB->set_field('user', 'confirmed', 1, array('id'=>$user->id));
                if ($user->firstaccess == 0) {
                    $DB->set_field('user', 'firstaccess', time(), array('id'=>$user->id));
                }
                return AUTH_CONFIRM_OK;
            }
        } else {
            return AUTH_CONFIRM_ERROR;
        }
    }

    /**
     * Return number of days to user password expires
     *
     * If userpassword does not expire it should return 0. If password is already expired
     * it should return negative value.
     *
     * @param mixed $username username
     * @return integer
     */
    function password_expire($username) {
        $result = 0;

        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();
        $user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);
        $search_attribs = array($this->config->expireattr);
        $sr = ldap_read($ldapconnection, $user_dn, '(objectClass=*)', $search_attribs);
        if ($sr)  {
            $info = ldap_get_entries_moodle($ldapconnection, $sr);
            if (!empty ($info)) {
                $info = array_change_key_case($info[0], CASE_LOWER);
                if (!empty($info[$this->config->expireattr][0])) {
                    $expiretime = $this->ldap_expirationtime2unix($info[$this->config->expireattr][0], $ldapconnection, $user_dn);
                    if ($expiretime != 0) {
                        $now = time();
                        if ($expiretime > $now) {
                            $result = ceil(($expiretime - $now) / DAYSECS);
                        } else {
                            $result = floor(($expiretime - $now) / DAYSECS);
                        }
                    }
                }
            }
        } else {
            error_log($this->errorlogtag.get_string('didtfindexpiretime', 'auth_ldap'));
        }

        return $result;
    }

    /**
     * Syncronizes user fron external LDAP server to moodle user table
     *
     * Sync is now using username attribute.
     *
     * Syncing users removes or suspends users that dont exists anymore in external LDAP.
     * Creates new users and updates coursecreator status of users.
     *
     * @param bool $do_updates will do pull in data updates from LDAP if relevant
     */
    function sync_users($do_updates=true) {
        global $CFG, $DB;

        print_string('connectingldap', 'auth_ldap');
        $ldapconnection = $this->ldap_connect();

        $dbman = $DB->get_manager();

    /// Define table user to be created
        $table = new xmldb_table('tmp_extuser');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('username', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('mnethostid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('username', XMLDB_INDEX_UNIQUE, array('mnethostid', 'username'));

        print_string('creatingtemptable', 'auth_ldap', 'tmp_extuser');
        $dbman->create_temp_table($table);

        ////
        //// get user's list from ldap to sql in a scalable fashion
        ////
        // prepare some data we'll need
        $filter = '(&('.$this->config->user_attribute.'=*)'.$this->config->objectclass.')';

        $contexts = explode(';', $this->config->contexts);

        if (!empty($this->config->create_context)) {
            array_push($contexts, $this->config->create_context);
        }

        $ldap_pagedresults = ldap_paged_results_supported($this->config->ldap_version);
        $ldap_cookie = '';
        foreach ($contexts as $context) {
            $context = trim($context);
            if (empty($context)) {
                continue;
            }

            do {
                if ($ldap_pagedresults) {
                    ldap_control_paged_result($ldapconnection, $this->config->pagesize, true, $ldap_cookie);
                }
                if ($this->config->search_sub) {
                    // Use ldap_search to find first user from subtree.
                    $ldap_result = ldap_search($ldapconnection, $context, $filter, array($this->config->user_attribute));
                } else {
                    // Search only in this context.
                    $ldap_result = ldap_list($ldapconnection, $context, $filter, array($this->config->user_attribute));
                }
                if(!$ldap_result) {
                    continue;
                }
                if ($ldap_pagedresults) {
                    ldap_control_paged_result_response($ldapconnection, $ldap_result, $ldap_cookie);
                }
                if ($entry = @ldap_first_entry($ldapconnection, $ldap_result)) {
                    do {
                        $value = ldap_get_values_len($ldapconnection, $entry, $this->config->user_attribute);
                        $value = textlib::convert($value[0], $this->config->ldapencoding, 'utf-8');
                        $this->ldap_bulk_insert($value);
                    } while ($entry = ldap_next_entry($ldapconnection, $entry));
                }
                unset($ldap_result); // Free mem.
            } while ($ldap_pagedresults && !empty($ldap_cookie));
        }

        // If LDAP paged results were used, the current connection must be completely
        // closed and a new one created, to work without paged results from here on.
        if ($ldap_pagedresults) {
            $this->ldap_close(true);
            $ldapconnection = $this->ldap_connect();
        }

        /// preserve our user database
        /// if the temp table is empty, it probably means that something went wrong, exit
        /// so as to avoid mass deletion of users; which is hard to undo
        $count = $DB->count_records_sql('SELECT COUNT(username) AS count, 1 FROM {tmp_extuser}');
        if ($count < 1) {
            print_string('didntgetusersfromldap', 'auth_ldap');
            exit;
        } else {
            print_string('gotcountrecordsfromldap', 'auth_ldap', $count);
        }


/// User removal
        // Find users in DB that aren't in ldap -- to be removed!
        // this is still not as scalable (but how often do we mass delete?)
        if ($this->config->removeuser != AUTH_REMOVEUSER_KEEP) {
            $sql = 'SELECT u.*
                      FROM {user} u
                      LEFT JOIN {tmp_extuser} e ON (u.username = e.username AND u.mnethostid = e.mnethostid)
                     WHERE u.auth = ?
                           AND u.deleted = 0
                           AND e.username IS NULL';
            $remove_users = $DB->get_records_sql($sql, array($this->authtype));

            if (!empty($remove_users)) {
                print_string('userentriestoremove', 'auth_ldap', count($remove_users));

                foreach ($remove_users as $user) {
                    if ($this->config->removeuser == AUTH_REMOVEUSER_FULLDELETE) {
                        if (delete_user($user)) {
                            echo "\t"; print_string('auth_dbdeleteuser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id)); echo "\n";
                        } else {
                            echo "\t"; print_string('auth_dbdeleteusererror', 'auth_db', $user->username); echo "\n";
                        }
                    } else if ($this->config->removeuser == AUTH_REMOVEUSER_SUSPEND) {
                        $updateuser = new stdClass();
                        $updateuser->id = $user->id;
                        $updateuser->auth = 'nologin';
                        $DB->update_record('user', $updateuser);
                        echo "\t"; print_string('auth_dbsuspenduser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id)); echo "\n";
                    }
                }
            } else {
                print_string('nouserentriestoremove', 'auth_ldap');
            }
            unset($remove_users); // free mem!
        }

/// Revive suspended users
        if (!empty($this->config->removeuser) and $this->config->removeuser == AUTH_REMOVEUSER_SUSPEND) {
            $sql = "SELECT u.id, u.username
                      FROM {user} u
                      JOIN {tmp_extuser} e ON (u.username = e.username AND u.mnethostid = e.mnethostid)
                     WHERE u.auth = 'nologin' AND u.deleted = 0";
            $revive_users = $DB->get_records_sql($sql);

            if (!empty($revive_users)) {
                print_string('userentriestorevive', 'auth_ldap', count($revive_users));

                foreach ($revive_users as $user) {
                    $updateuser = new stdClass();
                    $updateuser->id = $user->id;
                    $updateuser->auth = $this->authtype;
                    $DB->update_record('user', $updateuser);
                    echo "\t"; print_string('auth_dbreviveduser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id)); echo "\n";
                }
            } else {
                print_string('nouserentriestorevive', 'auth_ldap');
            }

            unset($revive_users);
        }


/// User Updates - time-consuming (optional)
        if ($do_updates) {
            // Narrow down what fields we need to update
            $all_keys = array_keys(get_object_vars($this->config));
            $updatekeys = array();
            foreach ($all_keys as $key) {
                if (preg_match('/^field_updatelocal_(.+)$/', $key, $match)) {
                    // If we have a field to update it from
                    // and it must be updated 'onlogin' we
                    // update it on cron
                    if (!empty($this->config->{'field_map_'.$match[1]})
                         and $this->config->{$match[0]} === 'onlogin') {
                        array_push($updatekeys, $match[1]); // the actual key name
                    }
                }
            }
            unset($all_keys); unset($key);

        } else {
            print_string('noupdatestobedone', 'auth_ldap');
        }
        if ($do_updates and !empty($updatekeys)) { // run updates only if relevant
            $users = $DB->get_records_sql('SELECT u.username, u.id
                                             FROM {user} u
                                            WHERE u.deleted = 0 AND u.auth = ? AND u.mnethostid = ?',
                                          array($this->authtype, $CFG->mnet_localhost_id));
            if (!empty($users)) {
                print_string('userentriestoupdate', 'auth_ldap', count($users));

                $sitecontext = context_system::instance();
                if (!empty($this->config->creators) and !empty($this->config->memberattribute)
                  and $roles = get_archetype_roles('coursecreator')) {
                    $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
                } else {
                    $creatorrole = false;
                }

                $transaction = $DB->start_delegated_transaction();
                $xcount = 0;
                $maxxcount = 100;

                foreach ($users as $user) {
                    echo "\t"; print_string('auth_dbupdatinguser', 'auth_db', array('name'=>$user->username, 'id'=>$user->id));
                    if (!$this->update_user_record($user->username, $updatekeys)) {
                        echo ' - '.get_string('skipped');
                    }
                    echo "\n";
                    $xcount++;

                    // Update course creators if needed
                    if ($creatorrole !== false) {
                        if ($this->iscreator($user->username)) {
                            role_assign($creatorrole->id, $user->id, $sitecontext->id, $this->roleauth);
                        } else {
                            role_unassign($creatorrole->id, $user->id, $sitecontext->id, $this->roleauth);
                        }
                    }
                }
                $transaction->allow_commit();
                unset($users); // free mem
            }
        } else { // end do updates
            print_string('noupdatestobedone', 'auth_ldap');
        }

/// User Additions
        // Find users missing in DB that are in LDAP
        // and gives me a nifty object I don't want.
        // note: we do not care about deleted accounts anymore, this feature was replaced by suspending to nologin auth plugin
        $sql = 'SELECT e.id, e.username
                  FROM {tmp_extuser} e
                  LEFT JOIN {user} u ON (e.username = u.username AND e.mnethostid = u.mnethostid)
                 WHERE u.id IS NULL';
        $add_users = $DB->get_records_sql($sql);

        if (!empty($add_users)) {
            print_string('userentriestoadd', 'auth_ldap', count($add_users));

            $sitecontext = context_system::instance();
            if (!empty($this->config->creators) and !empty($this->config->memberattribute)
              and $roles = get_archetype_roles('coursecreator')) {
                $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
            } else {
                $creatorrole = false;
            }

            $transaction = $DB->start_delegated_transaction();
            foreach ($add_users as $user) {
                $user = $this->get_userinfo_asobj($user->username);

                // Prep a few params
                $user->modified   = time();
                $user->confirmed  = 1;
                $user->auth       = $this->authtype;
                $user->mnethostid = $CFG->mnet_localhost_id;
                // get_userinfo_asobj() might have replaced $user->username with the value
                // from the LDAP server (which can be mixed-case). Make sure it's lowercase
                $user->username = trim(textlib::strtolower($user->username));
                if (empty($user->lang)) {
                    $user->lang = $CFG->lang;
                }

                $id = $DB->insert_record('user', $user);
                echo "\t"; print_string('auth_dbinsertuser', 'auth_db', array('name'=>$user->username, 'id'=>$id)); echo "\n";
                if (!empty($this->config->forcechangepassword)) {
                    set_user_preference('auth_forcepasswordchange', 1, $id);
                }

                // Add course creators if needed
                if ($creatorrole !== false and $this->iscreator($user->username)) {
                    role_assign($creatorrole->id, $id, $sitecontext->id, $this->roleauth);
                }

            }
            $transaction->allow_commit();
            unset($add_users); // free mem
        } else {
            print_string('nouserstobeadded', 'auth_ldap');
        }

        $dbman->drop_table($table);
        $this->ldap_close();

        return true;
    }

    /**
     * Update a local user record from an external source.
     * This is a lighter version of the one in moodlelib -- won't do
     * expensive ops such as enrolment.
     *
     * If you don't pass $updatekeys, there is a performance hit and
     * values removed from LDAP won't be removed from moodle.
     *
     * @param string $username username
     * @param boolean $updatekeys true to update the local record with the external LDAP values.
     */
    function update_user_record($username, $updatekeys = false) {
        global $CFG, $DB;

        // Just in case check text case
        $username = trim(textlib::strtolower($username));

        // Get the current user record
        $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id));
        if (empty($user)) { // trouble
            error_log($this->errorlogtag.get_string('auth_dbusernotexist', 'auth_db', '', $username));
            print_error('auth_dbusernotexist', 'auth_db', '', $username);
            die;
        }

        // Protect the userid from being overwritten
        $userid = $user->id;

        if ($newinfo = $this->get_userinfo($username)) {
            $newinfo = truncate_userinfo($newinfo);

            if (empty($updatekeys)) { // all keys? this does not support removing values
                $updatekeys = array_keys($newinfo);
            }

            foreach ($updatekeys as $key) {
                if (isset($newinfo[$key])) {
                    $value = $newinfo[$key];
                } else {
                    $value = '';
                }

                if (!empty($this->config->{'field_updatelocal_' . $key})) {
                    if ($user->{$key} != $value) { // only update if it's changed
                        $DB->set_field('user', $key, $value, array('id'=>$userid));
                    }
                }
            }
        } else {
            return false;
        }
        return $DB->get_record('user', array('id'=>$userid, 'deleted'=>0));
    }

    /**
     * Bulk insert in SQL's temp table
     */
    function ldap_bulk_insert($username) {
        global $DB, $CFG;

        $username = textlib::strtolower($username); // usernames are __always__ lowercase.
        $DB->insert_record_raw('tmp_extuser', array('username'=>$username,
                                                    'mnethostid'=>$CFG->mnet_localhost_id), false, true);
        echo '.';
    }

    /**
     * Activates (enables) user in external LDAP so user can login
     *
     * @param mixed $username
     * @return boolean result
     */
    function user_activate($username) {
        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        $userdn = $this->ldap_find_userdn($ldapconnection, $extusername);
        switch ($this->config->user_type)  {
            case 'edir':
                $newinfo['loginDisabled'] = 'FALSE';
                break;
            case 'rfc2307':
            case 'rfc2307bis':
                // Remember that we add a '*' character in front of the
                // external password string to 'disable' the account. We just
                // need to remove it.
                $sr = ldap_read($ldapconnection, $userdn, '(objectClass=*)',
                                array('userPassword'));
                $info = ldap_get_entries($ldapconnection, $sr);
                $info[0] = array_change_key_case($info[0], CASE_LOWER);
                $newinfo['userPassword'] = ltrim($info[0]['userpassword'][0], '*');
                break;
            case 'ad':
                // We need to unset the ACCOUNTDISABLE bit in the
                // userAccountControl attribute ( see
                // http://support.microsoft.com/kb/305144 )
                $sr = ldap_read($ldapconnection, $userdn, '(objectClass=*)',
                                array('userAccountControl'));
                $info = ldap_get_entries($ldapconnection, $sr);
                $info[0] = array_change_key_case($info[0], CASE_LOWER);
                $newinfo['userAccountControl'] = $info[0]['useraccountcontrol'][0]
                                                 & (~AUTH_AD_ACCOUNTDISABLE);
                break;
            default:
                print_error('user_activatenotsupportusertype', 'auth_ldap', '', $this->config->user_type_name);
        }
        $result = ldap_modify($ldapconnection, $userdn, $newinfo);
        $this->ldap_close();
        return $result;
    }

    /**
     * Returns true if user should be coursecreator.
     *
     * @param mixed $username    username (without system magic quotes)
     * @return mixed result      null if course creators is not configured, boolean otherwise.
     */
    function iscreator($username) {
        if (empty($this->config->creators) or empty($this->config->memberattribute)) {
            return null;
        }

        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        if ($this->config->memberattribute_isdn) {
            if(!($userid = $this->ldap_find_userdn($ldapconnection, $extusername))) {
                return false;
            }
        } else {
            $userid = $extusername;
        }

        $group_dns = explode(';', $this->config->creators);
        $creator = ldap_isgroupmember($ldapconnection, $userid, $group_dns, $this->config->memberattribute);

        $this->ldap_close();

        return $creator;
    }

    /**
     * Called when the user record is updated.
     *
     * Modifies user in external LDAP server. It takes olduser (before
     * changes) and newuser (after changes) compares information and
     * saves modified information to external LDAP server.
     *
     * @param mixed $olduser     Userobject before modifications    (without system magic quotes)
     * @param mixed $newuser     Userobject new modified userobject (without system magic quotes)
     * @return boolean result
     *
     */
    function user_update($olduser, $newuser) {
        global $USER;

        if (isset($olduser->username) and isset($newuser->username) and $olduser->username != $newuser->username) {
            error_log($this->errorlogtag.get_string('renamingnotallowed', 'auth_ldap'));
            return false;
        }

        if (isset($olduser->auth) and $olduser->auth != $this->authtype) {
            return true; // just change auth and skip update
        }

        $attrmap = $this->ldap_attributes();
        // Before doing anything else, make sure we really need to update anything
        // in the external LDAP server.
        $update_external = false;
        foreach ($attrmap as $key => $ldapkeys) {
            if (!empty($this->config->{'field_updateremote_'.$key})) {
                $update_external = true;
                break;
            }
        }
        if (!$update_external) {
            return true;
        }

        $extoldusername = textlib::convert($olduser->username, 'utf-8', $this->config->ldapencoding);

        $ldapconnection = $this->ldap_connect();

        $search_attribs = array();
        foreach ($attrmap as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                if (!in_array($value, $search_attribs)) {
                    array_push($search_attribs, $value);
                }
            }
        }

        if(!($user_dn = $this->ldap_find_userdn($ldapconnection, $extoldusername))) {
            return false;
        }

        $user_info_result = ldap_read($ldapconnection, $user_dn, '(objectClass=*)', $search_attribs);
        if ($user_info_result) {
            $user_entry = ldap_get_entries_moodle($ldapconnection, $user_info_result);
            if (empty($user_entry)) {
                $attribs = join (', ', $search_attribs);
                error_log($this->errorlogtag.get_string('updateusernotfound', 'auth_ldap',
                                                          array('userdn'=>$user_dn,
                                                                'attribs'=>$attribs)));
                return false; // old user not found!
            } else if (count($user_entry) > 1) {
                error_log($this->errorlogtag.get_string('morethanoneuser', 'auth_ldap'));
                return false;
            }

            $user_entry = array_change_key_case($user_entry[0], CASE_LOWER);

            foreach ($attrmap as $key => $ldapkeys) {
                // Only process if the moodle field ($key) has changed and we
                // are set to update LDAP with it
                if (isset($olduser->$key) and isset($newuser->$key)
                  and $olduser->$key !== $newuser->$key
                  and !empty($this->config->{'field_updateremote_'. $key})) {
                    // For ldap values that could be in more than one
                    // ldap key, we will do our best to match
                    // where they came from
                    $ambiguous = true;
                    $changed   = false;
                    if (!is_array($ldapkeys)) {
                        $ldapkeys = array($ldapkeys);
                    }
                    if (count($ldapkeys) < 2) {
                        $ambiguous = false;
                    }

                    $nuvalue = textlib::convert($newuser->$key, 'utf-8', $this->config->ldapencoding);
                    empty($nuvalue) ? $nuvalue = array() : $nuvalue;
                    $ouvalue = textlib::convert($olduser->$key, 'utf-8', $this->config->ldapencoding);

                    foreach ($ldapkeys as $ldapkey) {
                        $ldapkey   = $ldapkey;
                        $ldapvalue = $user_entry[$ldapkey][0];
                        if (!$ambiguous) {
                            // Skip update if the values already match
                            if ($nuvalue !== $ldapvalue) {
                                // This might fail due to schema validation
                                if (@ldap_modify($ldapconnection, $user_dn, array($ldapkey => $nuvalue))) {
                                    continue;
                                } else {
                                    error_log($this->errorlogtag.get_string ('updateremfail', 'auth_ldap',
                                                                             array('errno'=>ldap_errno($ldapconnection),
                                                                                   'errstring'=>ldap_err2str(ldap_errno($ldapconnection)),
                                                                                   'key'=>$key,
                                                                                   'ouvalue'=>$ouvalue,
                                                                                   'nuvalue'=>$nuvalue)));
                                    continue;
                                }
                            }
                        } else {
                            // Ambiguous. Value empty before in Moodle (and LDAP) - use
                            // 1st ldap candidate field, no need to guess
                            if ($ouvalue === '') { // value empty before - use 1st ldap candidate
                                // This might fail due to schema validation
                                if (@ldap_modify($ldapconnection, $user_dn, array($ldapkey => $nuvalue))) {
                                    $changed = true;
                                    continue;
                                } else {
                                    error_log($this->errorlogtag.get_string ('updateremfail', 'auth_ldap',
                                                                             array('errno'=>ldap_errno($ldapconnection),
                                                                                   'errstring'=>ldap_err2str(ldap_errno($ldapconnection)),
                                                                                   'key'=>$key,
                                                                                   'ouvalue'=>$ouvalue,
                                                                                   'nuvalue'=>$nuvalue)));
                                    continue;
                                }
                            }

                            // We found which ldap key to update!
                            if ($ouvalue !== '' and $ouvalue === $ldapvalue ) {
                                // This might fail due to schema validation
                                if (@ldap_modify($ldapconnection, $user_dn, array($ldapkey => $nuvalue))) {
                                    $changed = true;
                                    continue;
                                } else {
                                    error_log($this->errorlogtag.get_string ('updateremfail', 'auth_ldap',
                                                                             array('errno'=>ldap_errno($ldapconnection),
                                                                                   'errstring'=>ldap_err2str(ldap_errno($ldapconnection)),
                                                                                   'key'=>$key,
                                                                                   'ouvalue'=>$ouvalue,
                                                                                   'nuvalue'=>$nuvalue)));
                                    continue;
                                }
                            }
                        }
                    }

                    if ($ambiguous and !$changed) {
                        error_log($this->errorlogtag.get_string ('updateremfailamb', 'auth_ldap',
                                                                 array('key'=>$key,
                                                                       'ouvalue'=>$ouvalue,
                                                                       'nuvalue'=>$nuvalue)));
                    }
                }
            }
        } else {
            error_log($this->errorlogtag.get_string ('usernotfound', 'auth_ldap'));
            $this->ldap_close();
            return false;
        }

        $this->ldap_close();
        return true;

    }

    /**
     * Changes userpassword in LDAP
     *
     * Called when the user password is updated. It assumes it is
     * called by an admin or that you've otherwise checked the user's
     * credentials
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password (not crypted/md5'ed)
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword) {
        global $USER;

        $result = false;
        $username = $user->username;

        $extusername = textlib::convert($username, 'utf-8', $this->config->ldapencoding);
        $extpassword = textlib::convert($newpassword, 'utf-8', $this->config->ldapencoding);

        switch ($this->config->passtype) {
            case 'md5':
                $extpassword = '{MD5}' . base64_encode(pack('H*', md5($extpassword)));
                break;
            case 'sha1':
                $extpassword = '{SHA}' . base64_encode(pack('H*', sha1($extpassword)));
                break;
            case 'plaintext':
            default:
                break; // plaintext
        }

        $ldapconnection = $this->ldap_connect();

        $user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);

        if (!$user_dn) {
            error_log($this->errorlogtag.get_string ('nodnforusername', 'auth_ldap', $user->username));
            return false;
        }

        switch ($this->config->user_type) {
            case 'edir':
                // Change password
                $result = ldap_modify($ldapconnection, $user_dn, array('userPassword' => $extpassword));
                if (!$result) {
                    error_log($this->errorlogtag.get_string ('updatepasserror', 'auth_ldap',
                                                               array('errno'=>ldap_errno($ldapconnection),
                                                                     'errstring'=>ldap_err2str(ldap_errno($ldapconnection)))));
                }
                // Update password expiration time, grace logins count
                $search_attribs = array($this->config->expireattr, 'passwordExpirationInterval', 'loginGraceLimit');
                $sr = ldap_read($ldapconnection, $user_dn, '(objectClass=*)', $search_attribs);
                if ($sr) {
                    $entry = ldap_get_entries_moodle($ldapconnection, $sr);
                    $info = array_change_key_case($entry[0], CASE_LOWER);
                    $newattrs = array();
                    if (!empty($info[$this->config->expireattr][0])) {
                        // Set expiration time only if passwordExpirationInterval is defined
                        if (!empty($info['passwordexpirationinterval'][0])) {
                           $expirationtime = time() + $info['passwordexpirationinterval'][0];
                           $ldapexpirationtime = $this->ldap_unix2expirationtime($expirationtime);
                           $newattrs['passwordExpirationTime'] = $ldapexpirationtime;
                        }

                        // Set gracelogin count
                        if (!empty($info['logingracelimit'][0])) {
                           $newattrs['loginGraceRemaining']= $info['logingracelimit'][0];
                        }

                        // Store attribute changes in LDAP
                        $result = ldap_modify($ldapconnection, $user_dn, $newattrs);
                        if (!$result) {
                            error_log($this->errorlogtag.get_string ('updatepasserrorexpiregrace', 'auth_ldap',
                                                                       array('errno'=>ldap_errno($ldapconnection),
                                                                             'errstring'=>ldap_err2str(ldap_errno($ldapconnection)))));
                        }
                    }
                }
                else {
                    error_log($this->errorlogtag.get_string ('updatepasserrorexpire', 'auth_ldap',
                                                             array('errno'=>ldap_errno($ldapconnection),
                                                                   'errstring'=>ldap_err2str(ldap_errno($ldapconnection)))));
                }
                break;

            case 'ad':
                // Passwords in Active Directory must be encoded as Unicode
                // strings (UCS-2 Little Endian format) and surrounded with
                // double quotes. See http://support.microsoft.com/?kbid=269190
                if (!function_exists('mb_convert_encoding')) {
                    error_log($this->errorlogtag.get_string ('needmbstring', 'auth_ldap'));
                    return false;
                }
                $extpassword = mb_convert_encoding('"'.$extpassword.'"', "UCS-2LE", $this->config->ldapencoding);
                $result = ldap_modify($ldapconnection, $user_dn, array('unicodePwd' => $extpassword));
                if (!$result) {
                    error_log($this->errorlogtag.get_string ('updatepasserror', 'auth_ldap',
                                                             array('errno'=>ldap_errno($ldapconnection),
                                                                   'errstring'=>ldap_err2str(ldap_errno($ldapconnection)))));
                }
                break;

            default:
                // Send LDAP the password in cleartext, it will md5 it itself
                $result = ldap_modify($ldapconnection, $user_dn, array('userPassword' => $extpassword));
                if (!$result) {
                    error_log($this->errorlogtag.get_string ('updatepasserror', 'auth_ldap',
                                                             array('errno'=>ldap_errno($ldapconnection),
                                                                   'errstring'=>ldap_err2str(ldap_errno($ldapconnection)))));
                }

        }

        $this->ldap_close();
        return $result;
    }

    /**
     * Take expirationtime and return it as unix timestamp in seconds
     *
     * Takes expiration timestamp as read from LDAP and returns it as unix timestamp in seconds
     * Depends on $this->config->user_type variable
     *
     * @param mixed time   Time stamp read from LDAP as it is.
     * @param string $ldapconnection Only needed for Active Directory.
     * @param string $user_dn User distinguished name for the user we are checking password expiration (only needed for Active Directory).
     * @return timestamp
     */
    function ldap_expirationtime2unix ($time, $ldapconnection, $user_dn) {
        $result = false;
        switch ($this->config->user_type) {
            case 'edir':
                $yr=substr($time, 0, 4);
                $mo=substr($time, 4, 2);
                $dt=substr($time, 6, 2);
                $hr=substr($time, 8, 2);
                $min=substr($time, 10, 2);
                $sec=substr($time, 12, 2);
                $result = mktime($hr, $min, $sec, $mo, $dt, $yr);
                break;
            case 'rfc2307':
            case 'rfc2307bis':
                $result = $time * DAYSECS; // The shadowExpire contains the number of DAYS between 01/01/1970 and the actual expiration date
                break;
            case 'ad':
                $result = $this->ldap_get_ad_pwdexpire($time, $ldapconnection, $user_dn);
                break;
            default:
                print_error('auth_ldap_usertypeundefined', 'auth_ldap');
        }
        return $result;
    }

    /**
     * Takes unix timestamp and returns it formated for storing in LDAP
     *
     * @param integer unix time stamp
     */
    function ldap_unix2expirationtime($time) {
        $result = false;
        switch ($this->config->user_type) {
            case 'edir':
                $result=date('YmdHis', $time).'Z';
                break;
            case 'rfc2307':
            case 'rfc2307bis':
                $result = $time ; // Already in correct format
                break;
            default:
                print_error('auth_ldap_usertypeundefined2', 'auth_ldap');
        }
        return $result;

    }

    /**
     * Returns user attribute mappings between moodle and LDAP
     *
     * @return array
     */

    function ldap_attributes () {
        $moodleattributes = array();
        foreach ($this->userfields as $field) {
            if (!empty($this->config->{"field_map_$field"})) {
                $moodleattributes[$field] = textlib::strtolower(trim($this->config->{"field_map_$field"}));
                if (preg_match('/,/', $moodleattributes[$field])) {
                    $moodleattributes[$field] = explode(',', $moodleattributes[$field]); // split ?
                }
            }
        }
        $moodleattributes['username'] = textlib::strtolower(trim($this->config->user_attribute));
        return $moodleattributes;
    }

    /**
     * Returns all usernames from LDAP
     *
     * @param $filter An LDAP search filter to select desired users
     * @return array of LDAP user names converted to UTF-8
     */
    function ldap_get_userlist($filter='*') {
        $fresult = array();

        $ldapconnection = $this->ldap_connect();

        if ($filter == '*') {
           $filter = '(&('.$this->config->user_attribute.'=*)'.$this->config->objectclass.')';
        }

        $contexts = explode(';', $this->config->contexts);
        if (!empty($this->config->create_context)) {
            array_push($contexts, $this->config->create_context);
        }

        $ldap_pagedresults = ldap_paged_results_supported($this->config->ldap_version);
        foreach ($contexts as $context) {
            $context = trim($context);
            if (empty($context)) {
                continue;
            }

            do {
                if ($ldap_pagedresults) {
                    ldap_control_paged_result($ldapconnection, $this->config->pagesize, true, $ldap_cookie);
                }
                if ($this->config->search_sub) {
                    // Use ldap_search to find first user from subtree.
                    $ldap_result = ldap_search($ldapconnection, $context, $filter, array($this->config->user_attribute));
                } else {
                    // Search only in this context.
                    $ldap_result = ldap_list($ldapconnection, $context, $filter, array($this->config->user_attribute));
                }
                if(!$ldap_result) {
                    continue;
                }
                if ($ldap_pagedresults) {
                    ldap_control_paged_result_response($ldapconnection, $ldap_result, $ldap_cookie);
                }
                $users = ldap_get_entries_moodle($ldapconnection, $ldap_result);
                // Add found users to list.
                for ($i = 0; $i < count($users); $i++) {
                    $extuser = textlib::convert($users[$i][$this->config->user_attribute][0],
                                                $this->config->ldapencoding, 'utf-8');
                    array_push($fresult, $extuser);
                }
                unset($ldap_result); // Free mem.
            } while ($ldap_pagedresults && !empty($ldap_cookie));
        }

        // If paged results were used, make sure the current connection is completely closed
        $this->ldap_close($ldap_pagedresults);
        return $fresult;
    }

    /**
     * Indicates if password hashes should be stored in local moodle database.
     *
     * @return bool true means flag 'not_cached' stored instead of password hash
     */
    function prevent_local_passwords() {
        return !empty($this->config->preventpassindb);
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return false;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return !empty($this->config->stdchangepassword) or !empty($this->config->changepasswordurl);
    }

    /**
     * Returns the URL for changing the user's password, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        if (empty($this->config->stdchangepassword)) {
            return new moodle_url($this->config->changepasswordurl);
        } else {
            return null;
        }
    }

    /**
     * Will get called before the login page is shownr. Ff NTLM SSO
     * is enabled, and the user is in the right network, we'll redirect
     * to the magic NTLM page for SSO...
     *
     */
    function loginpage_hook() {
        global $CFG, $SESSION;

        // HTTPS is potentially required
        //httpsrequired(); - this must be used before setting the URL, it is already done on the login/index.php

        if (($_SERVER['REQUEST_METHOD'] === 'GET'         // Only on initial GET of loginpage
             || ($_SERVER['REQUEST_METHOD'] === 'POST'
                 && (get_referer() != strip_querystring(qualified_me()))))
                                                          // Or when POSTed from another place
                                                          // See MDL-14071
            && !empty($this->config->ntlmsso_enabled)     // SSO enabled
            && !empty($this->config->ntlmsso_subnet)      // have a subnet to test for
            && empty($_GET['authldap_skipntlmsso'])       // haven't failed it yet
            && (isguestuser() || !isloggedin())           // guestuser or not-logged-in users
            && address_in_subnet(getremoteaddr(), $this->config->ntlmsso_subnet)) {

            // First, let's remember where we were trying to get to before we got here
            if (empty($SESSION->wantsurl)) {
                $SESSION->wantsurl = (array_key_exists('HTTP_REFERER', $_SERVER) &&
                                      $_SERVER['HTTP_REFERER'] != $CFG->wwwroot &&
                                      $_SERVER['HTTP_REFERER'] != $CFG->wwwroot.'/' &&
                                      $_SERVER['HTTP_REFERER'] != $CFG->httpswwwroot.'/login/' &&
                                      $_SERVER['HTTP_REFERER'] != $CFG->httpswwwroot.'/login/index.php')
                    ? $_SERVER['HTTP_REFERER'] : NULL;
            }

            // Now start the whole NTLM machinery.
            if(!empty($this->config->ntlmsso_ie_fastpath)) {
                // Shortcut for IE browsers: skip the attempt page
                if(check_browser_version('MSIE')) {
                    $sesskey = sesskey();
                    redirect($CFG->wwwroot.'/auth/ldap/ntlmsso_magic.php?sesskey='.$sesskey);
                } else {
                    redirect($CFG->httpswwwroot.'/login/index.php?authldap_skipntlmsso=1');
                }
            } else {
                redirect($CFG->wwwroot.'/auth/ldap/ntlmsso_attempt.php');
            }
        }

        // No NTLM SSO, Use the normal login page instead.

        // If $SESSION->wantsurl is empty and we have a 'Referer:' header, the login
        // page insists on redirecting us to that page after user validation. If
        // we clicked on the redirect link at the ntlmsso_finish.php page (instead
        // of waiting for the redirection to happen) then we have a 'Referer:' header
        // we don't want to use at all. As we can't get rid of it, just point
        // $SESSION->wantsurl to $CFG->wwwroot (after all, we came from there).
        if (empty($SESSION->wantsurl)
            && (get_referer() == $CFG->httpswwwroot.'/auth/ldap/ntlmsso_finish.php')) {

            $SESSION->wantsurl = $CFG->wwwroot;
        }
    }

    /**
     * To be called from a page running under NTLM's
     * "Integrated Windows Authentication".
     *
     * If successful, it will set a special "cookie" (not an HTTP cookie!)
     * in cache_flags under the $this->pluginconfig/ntlmsess "plugin" and return true.
     * The "cookie" will be picked up by ntlmsso_finish() to complete the
     * process.
     *
     * On failure it will return false for the caller to display an appropriate
     * error message (probably saying that Integrated Windows Auth isn't enabled!)
     *
     * NOTE that this code will execute under the OS user credentials,
     * so we MUST avoid dealing with files -- such as session files.
     * (The caller should define('NO_MOODLE_COOKIES', true) before including config.php)
     *
     */
    function ntlmsso_magic($sesskey) {
        if (isset($_SERVER['REMOTE_USER']) && !empty($_SERVER['REMOTE_USER'])) {

            // HTTP __headers__ seem to be sent in ISO-8859-1 encoding
            // (according to my reading of RFC-1945, RFC-2616 and RFC-2617 and
            // my local tests), so we need to convert the REMOTE_USER value
            // (i.e., what we got from the HTTP WWW-Authenticate header) into UTF-8
            $username = textlib::convert($_SERVER['REMOTE_USER'], 'iso-8859-1', 'utf-8');

            switch ($this->config->ntlmsso_type) {
                case 'ntlm':
                    // The format is now configurable, so try to extract the username
                    $username = $this->get_ntlm_remote_user($username);
                    if (empty($username)) {
                        return false;
                    }
                    break;
                case 'kerberos':
                    // Format is username@DOMAIN
                    $username = substr($username, 0, strpos($username, '@'));
                    break;
                default:
                    error_log($this->errorlogtag.get_string ('ntlmsso_unknowntype', 'auth_ldap'));
                    return false; // Should never happen!
            }

            $username = textlib::strtolower($username); // Compatibility hack
            set_cache_flag($this->pluginconfig.'/ntlmsess', $sesskey, $username, AUTH_NTLMTIMEOUT);
            return true;
        }
        return false;
    }

    /**
     * Find the session set by ntlmsso_magic(), validate it and
     * call authenticate_user_login() to authenticate the user through
     * the auth machinery.
     *
     * It is complemented by a similar check in user_login().
     *
     * If it succeeds, it never returns.
     *
     */
    function ntlmsso_finish() {
        global $CFG, $USER, $SESSION;

        $key = sesskey();
        $cf = get_cache_flags($this->pluginconfig.'/ntlmsess');
        if (!isset($cf[$key]) || $cf[$key] === '') {
            return false;
        }
        $username   = $cf[$key];
        // Here we want to trigger the whole authentication machinery
        // to make sure no step is bypassed...
        $user = authenticate_user_login($username, $key);
        if ($user) {
            add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID,
                       $user->id, 0, $user->id);
            complete_user_login($user);

            // Cleanup the key to prevent reuse...
            // and to allow re-logins with normal credentials
            unset_cache_flag($this->pluginconfig.'/ntlmsess', $key);

            // Redirection
            if (user_not_fully_set_up($USER)) {
                $urltogo = $CFG->wwwroot.'/user/edit.php';
                // We don't delete $SESSION->wantsurl yet, so we get there later
            } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
                $urltogo = $SESSION->wantsurl;    // Because it's an address in this site
                unset($SESSION->wantsurl);
            } else {
                // No wantsurl stored or external - go to homepage
                $urltogo = $CFG->wwwroot.'/';
                unset($SESSION->wantsurl);
            }
            redirect($urltogo);
        }
        // Should never reach here.
        return false;
    }

    /**
     * Sync roles for this user
     *
     * @param $user object user object (without system magic quotes)
     */
    function sync_roles($user) {
        $iscreator = $this->iscreator($user->username);
        if ($iscreator === null) {
            return; // Nothing to sync - creators not configured
        }

        if ($roles = get_archetype_roles('coursecreator')) {
            $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
            $systemcontext = context_system::instance();

            if ($iscreator) { // Following calls will not create duplicates
                role_assign($creatorrole->id, $user->id, $systemcontext->id, $this->roleauth);
            } else {
                // Unassign only if previously assigned by this plugin!
                role_unassign($creatorrole->id, $user->id, $systemcontext->id, $this->roleauth);
            }
        }
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        global $CFG, $OUTPUT;

        if (!function_exists('ldap_connect')) { // Is php-ldap really there?
            echo $OUTPUT->notification(get_string('auth_ldap_noextension', 'auth_ldap'));
            return;
        }

        if (!ldap_paged_results_supported($this->config->ldap_version)) {
            echo $OUTPUT->notification(get_string('pagedresultsnotsupp', 'auth_ldap'));
        }

        include($CFG->dirroot.'/auth/ldap/config.html');
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // Set to defaults if undefined
        if (!isset($config->host_url)) {
             $config->host_url = '';
        }
        if (empty($config->ldapencoding)) {
         $config->ldapencoding = 'utf-8';
        }
        if (!isset($config->pagesize)) {
            $config->pagesize = LDAP_DEFAULT_PAGESIZE;
        }
        if (!isset($config->contexts)) {
             $config->contexts = '';
        }
        if (!isset($config->user_type)) {
             $config->user_type = 'default';
        }
        if (!isset($config->user_attribute)) {
             $config->user_attribute = '';
        }
        if (!isset($config->search_sub)) {
             $config->search_sub = '';
        }
        if (!isset($config->opt_deref)) {
             $config->opt_deref = LDAP_DEREF_NEVER;
        }
        if (!isset($config->preventpassindb)) {
             $config->preventpassindb = 0;
        }
        if (!isset($config->bind_dn)) {
            $config->bind_dn = '';
        }
        if (!isset($config->bind_pw)) {
            $config->bind_pw = '';
        }
        if (!isset($config->ldap_version)) {
            $config->ldap_version = '3';
        }
        if (!isset($config->objectclass)) {
            $config->objectclass = '';
        }
        if (!isset($config->memberattribute)) {
            $config->memberattribute = '';
        }
        if (!isset($config->memberattribute_isdn)) {
            $config->memberattribute_isdn = '';
        }
        if (!isset($config->creators)) {
            $config->creators = '';
        }
        if (!isset($config->create_context)) {
            $config->create_context = '';
        }
        if (!isset($config->expiration)) {
            $config->expiration = '';
        }
        if (!isset($config->expiration_warning)) {
            $config->expiration_warning = '10';
        }
        if (!isset($config->expireattr)) {
            $config->expireattr = '';
        }
        if (!isset($config->gracelogins)) {
            $config->gracelogins = '';
        }
        if (!isset($config->graceattr)) {
            $config->graceattr = '';
        }
        if (!isset($config->auth_user_create)) {
            $config->auth_user_create = '';
        }
        if (!isset($config->forcechangepassword)) {
            $config->forcechangepassword = 0;
        }
        if (!isset($config->stdchangepassword)) {
            $config->stdchangepassword = 0;
        }
        if (!isset($config->passtype)) {
            $config->passtype = 'plaintext';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }
        if (!isset($config->removeuser)) {
            $config->removeuser = AUTH_REMOVEUSER_KEEP;
        }
        if (!isset($config->ntlmsso_enabled)) {
            $config->ntlmsso_enabled = 0;
        }
        if (!isset($config->ntlmsso_subnet)) {
            $config->ntlmsso_subnet = '';
        }
        if (!isset($config->ntlmsso_ie_fastpath)) {
            $config->ntlmsso_ie_fastpath = 0;
        }
        if (!isset($config->ntlmsso_type)) {
            $config->ntlmsso_type = 'ntlm';
        }
        if (!isset($config->ntlmsso_remoteuserformat)) {
            $config->ntlmsso_remoteuserformat = '';
        }

        // Try to remove duplicates before storing the contexts (to avoid problems in sync_users()).
        $config->contexts = explode(';', $config->contexts);
        $config->contexts = array_map(create_function('$x', 'return textlib::strtolower(trim($x));'),
                                      $config->contexts);
        $config->contexts = implode(';', array_unique($config->contexts));

        // Save settings
        set_config('host_url', trim($config->host_url), $this->pluginconfig);
        set_config('ldapencoding', trim($config->ldapencoding), $this->pluginconfig);
        set_config('pagesize', (int)trim($config->pagesize), $this->pluginconfig);
        set_config('contexts', $config->contexts, $this->pluginconfig);
        set_config('user_type', textlib::strtolower(trim($config->user_type)), $this->pluginconfig);
        set_config('user_attribute', textlib::strtolower(trim($config->user_attribute)), $this->pluginconfig);
        set_config('search_sub', $config->search_sub, $this->pluginconfig);
        set_config('opt_deref', $config->opt_deref, $this->pluginconfig);
        set_config('preventpassindb', $config->preventpassindb, $this->pluginconfig);
        set_config('bind_dn', trim($config->bind_dn), $this->pluginconfig);
        set_config('bind_pw', $config->bind_pw, $this->pluginconfig);
        set_config('ldap_version', $config->ldap_version, $this->pluginconfig);
        set_config('objectclass', trim($config->objectclass), $this->pluginconfig);
        set_config('memberattribute', textlib::strtolower(trim($config->memberattribute)), $this->pluginconfig);
        set_config('memberattribute_isdn', $config->memberattribute_isdn, $this->pluginconfig);
        set_config('creators', trim($config->creators), $this->pluginconfig);
        set_config('create_context', trim($config->create_context), $this->pluginconfig);
        set_config('expiration', $config->expiration, $this->pluginconfig);
        set_config('expiration_warning', trim($config->expiration_warning), $this->pluginconfig);
        set_config('expireattr', textlib::strtolower(trim($config->expireattr)), $this->pluginconfig);
        set_config('gracelogins', $config->gracelogins, $this->pluginconfig);
        set_config('graceattr', textlib::strtolower(trim($config->graceattr)), $this->pluginconfig);
        set_config('auth_user_create', $config->auth_user_create, $this->pluginconfig);
        set_config('forcechangepassword', $config->forcechangepassword, $this->pluginconfig);
        set_config('stdchangepassword', $config->stdchangepassword, $this->pluginconfig);
        set_config('passtype', $config->passtype, $this->pluginconfig);
        set_config('changepasswordurl', trim($config->changepasswordurl), $this->pluginconfig);
        set_config('removeuser', $config->removeuser, $this->pluginconfig);
        set_config('ntlmsso_enabled', (int)$config->ntlmsso_enabled, $this->pluginconfig);
        set_config('ntlmsso_subnet', trim($config->ntlmsso_subnet), $this->pluginconfig);
        set_config('ntlmsso_ie_fastpath', (int)$config->ntlmsso_ie_fastpath, $this->pluginconfig);
        set_config('ntlmsso_type', $config->ntlmsso_type, 'auth/ldap');
        set_config('ntlmsso_remoteuserformat', trim($config->ntlmsso_remoteuserformat), 'auth/ldap');

        return true;
    }

    /**
     * Get password expiration time for a given user from Active Directory
     *
     * @param string $pwdlastset The time last time we changed the password.
     * @param resource $lcapconn The open LDAP connection.
     * @param string $user_dn The distinguished name of the user we are checking.
     *
     * @return string $unixtime
     */
    function ldap_get_ad_pwdexpire($pwdlastset, $ldapconn, $user_dn){
        global $CFG;

        if (!function_exists('bcsub')) {
            error_log($this->errorlogtag.get_string ('needbcmath', 'auth_ldap'));
            return 0;
        }

        // If UF_DONT_EXPIRE_PASSWD flag is set in user's
        // userAccountControl attribute, the password doesn't expire.
        $sr = ldap_read($ldapconn, $user_dn, '(objectClass=*)',
                        array('userAccountControl'));
        if (!$sr) {
            error_log($this->errorlogtag.get_string ('useracctctrlerror', 'auth_ldap', $user_dn));
            // Don't expire password, as we are not sure if it has to be
            // expired or not.
            return 0;
        }

        $entry = ldap_get_entries_moodle($ldapconn, $sr);
        $info = array_change_key_case($entry[0], CASE_LOWER);
        $useraccountcontrol = $info['useraccountcontrol'][0];
        if ($useraccountcontrol & UF_DONT_EXPIRE_PASSWD) {
            // Password doesn't expire.
            return 0;
        }

        // If pwdLastSet is zero, the user must change his/her password now
        // (unless UF_DONT_EXPIRE_PASSWD flag is set, but we already
        // tested this above)
        if ($pwdlastset === '0') {
            // Password has expired
            return -1;
        }

        // ----------------------------------------------------------------
        // Password expiration time in Active Directory is the composition of
        // two values:
        //
        //   - User's pwdLastSet attribute, that stores the last time
        //     the password was changed.
        //
        //   - Domain's maxPwdAge attribute, that sets how long
        //     passwords last in this domain.
        //
        // We already have the first value (passed in as a parameter). We
        // need to get the second one. As we don't know the domain DN, we
        // have to query rootDSE's defaultNamingContext attribute to get
        // it. Then we have to query that DN's maxPwdAge attribute to get
        // the real value.
        //
        // Once we have both values, we just need to combine them. But MS
        // chose to use a different base and unit for time measurements.
        // So we need to convert the values to Unix timestamps (see
        // details below).
        // ----------------------------------------------------------------

        $sr = ldap_read($ldapconn, ROOTDSE, '(objectClass=*)',
                        array('defaultNamingContext'));
        if (!$sr) {
            error_log($this->errorlogtag.get_string ('rootdseerror', 'auth_ldap'));
            return 0;
        }

        $entry = ldap_get_entries_moodle($ldapconn, $sr);
        $info = array_change_key_case($entry[0], CASE_LOWER);
        $domaindn = $info['defaultnamingcontext'][0];

        $sr = ldap_read ($ldapconn, $domaindn, '(objectClass=*)',
                         array('maxPwdAge'));
        $entry = ldap_get_entries_moodle($ldapconn, $sr);
        $info = array_change_key_case($entry[0], CASE_LOWER);
        $maxpwdage = $info['maxpwdage'][0];

        // ----------------------------------------------------------------
        // MSDN says that "pwdLastSet contains the number of 100 nanosecond
        // intervals since January 1, 1601 (UTC), stored in a 64 bit integer".
        //
        // According to Perl's Date::Manip, the number of seconds between
        // this date and Unix epoch is 11644473600. So we have to
        // substract this value to calculate a Unix time, once we have
        // scaled pwdLastSet to seconds. This is the script used to
        // calculate the value shown above:
        //
        //    #!/usr/bin/perl -w
        //
        //    use Date::Manip;
        //
        //    $date1 = ParseDate ("160101010000 UTC");
        //    $date2 = ParseDate ("197001010000 UTC");
        //    $delta = DateCalc($date1, $date2, \$err);
        //    $secs = Delta_Format($delta, 0, "%st");
        //    print "$secs \n";
        //
        // MSDN also says that "maxPwdAge is stored as a large integer that
        // represents the number of 100 nanosecond intervals from the time
        // the password was set before the password expires." We also need
        // to scale this to seconds. Bear in mind that this value is stored
        // as a _negative_ quantity (at least in my AD domain).
        //
        // As a last remark, if the low 32 bits of maxPwdAge are equal to 0,
        // the maximum password age in the domain is set to 0, which means
        // passwords do not expire (see
        // http://msdn2.microsoft.com/en-us/library/ms974598.aspx)
        //
        // As the quantities involved are too big for PHP integers, we
        // need to use BCMath functions to work with arbitrary precision
        // numbers.
        // ----------------------------------------------------------------

        // If the low order 32 bits are 0, then passwords do not expire in
        // the domain. Just do '$maxpwdage mod 2^32' and check the result
        // (2^32 = 4294967296)
        if (bcmod ($maxpwdage, 4294967296) === '0') {
            return 0;
        }

        // Add up pwdLastSet and maxPwdAge to get password expiration
        // time, in MS time units. Remember maxPwdAge is stored as a
        // _negative_ quantity, so we need to substract it in fact.
        $pwdexpire = bcsub ($pwdlastset, $maxpwdage);

        // Scale the result to convert it to Unix time units and return
        // that value.
        return bcsub( bcdiv($pwdexpire, '10000000'), '11644473600');
    }

    /**
     * Connect to the LDAP server, using the plugin configured
     * settings. It's actually a wrapper around ldap_connect_moodle()
     *
     * @return resource A valid LDAP connection (or dies if it can't connect)
     */
    function ldap_connect() {
        // Cache ldap connections. They are expensive to set up
        // and can drain the TCP/IP ressources on the server if we
        // are syncing a lot of users (as we try to open a new connection
        // to get the user details). This is the least invasive way
        // to reuse existing connections without greater code surgery.
        if(!empty($this->ldapconnection)) {
            $this->ldapconns++;
            return $this->ldapconnection;
        }

        if($ldapconnection = ldap_connect_moodle($this->config->host_url, $this->config->ldap_version,
                                                 $this->config->user_type, $this->config->bind_dn,
                                                 $this->config->bind_pw, $this->config->opt_deref,
                                                 $debuginfo)) {
            $this->ldapconns = 1;
            $this->ldapconnection = $ldapconnection;
            return $ldapconnection;
        }

        print_error('auth_ldap_noconnect_all', 'auth_ldap', '', $debuginfo);
    }

    /**
     * Disconnects from a LDAP server
     *
     * @param force boolean Forces closing the real connection to the LDAP server, ignoring any
     *                      cached connections. This is needed when we've used paged results
     *                      and want to use normal results again.
     */
    function ldap_close($force=false) {
        $this->ldapconns--;
        if (($this->ldapconns == 0) || ($force)) {
            $this->ldapconns = 0;
            @ldap_close($this->ldapconnection);
            unset($this->ldapconnection);
        }
    }

    /**
     * Search specified contexts for username and return the user dn
     * like: cn=username,ou=suborg,o=org. It's actually a wrapper
     * around ldap_find_userdn().
     *
     * @param resource $ldapconnection a valid LDAP connection
     * @param string $extusername the username to search (in external LDAP encoding, no db slashes)
     * @return mixed the user dn (external LDAP encoding) or false
     */
    function ldap_find_userdn($ldapconnection, $extusername) {
        $ldap_contexts = explode(';', $this->config->contexts);
        if (!empty($this->config->create_context)) {
            array_push($ldap_contexts, $this->config->create_context);
        }

        return ldap_find_userdn($ldapconnection, $extusername, $ldap_contexts, $this->config->objectclass,
                                $this->config->user_attribute, $this->config->search_sub);
    }


    /**
     * A chance to validate form data, and last chance to do stuff
     * before it is inserted in config_plugin
     *
     * @param object object with submitted configuration settings (without system magic quotes)
     * @param array $err array of error messages (passed by reference)
     */
    function validate_form($form, &$err) {
        if ($form->ntlmsso_type == 'ntlm') {
            $format = trim($form->ntlmsso_remoteuserformat);
            if (!empty($format) && !preg_match('/%username%/i', $format)) {
                $err['ntlmsso_remoteuserformat'] = get_string('auth_ntlmsso_missing_username', 'auth_ldap');
            }
        }
    }


    /**
     * When using NTLM SSO, the format of the remote username we get in
     * $_SERVER['REMOTE_USER'] may vary, depending on where from and how the web
     * server gets the data. So we let the admin configure the format using two
     * place holders (%domain% and %username%). This function tries to extract
     * the username (stripping the domain part and any separators if they are
     * present) from the value present in $_SERVER['REMOTE_USER'], using the
     * configured format.
     *
     * @param string $remoteuser The value from $_SERVER['REMOTE_USER'] (converted to UTF-8)
     *
     * @return string The remote username (without domain part or
     *                separators). Empty string if we can't extract the username.
     */
    protected function get_ntlm_remote_user($remoteuser) {
        if (empty($this->config->ntlmsso_remoteuserformat)) {
            $format = AUTH_NTLM_DEFAULT_FORMAT;
        } else {
            $format = $this->config->ntlmsso_remoteuserformat;
        }

        $format = preg_quote($format);
        $formatregex = preg_replace(array('#%domain%#', '#%username%#'),
                                    array('('.AUTH_NTLM_VALID_DOMAINNAME.')', '('.AUTH_NTLM_VALID_USERNAME.')'),
                                    $format);
        if (preg_match('#^'.$formatregex.'$#', $remoteuser, $matches)) {
            $user = end($matches);
            return $user;
        }

        /* We are unable to extract the username with the configured format. Probably
         * the format specified is wrong, so log a warning for the admin and return
         * an empty username.
         */
        error_log($this->errorlogtag.get_string ('auth_ntlmsso_maybeinvalidformat', 'auth_ldap'));
        return '';
    }

} // End of the class
