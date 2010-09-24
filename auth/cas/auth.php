<?php
/**
 * @author Martin Dougiamas
 * @authro Jerome GUTIERREZ
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: CAS Authentication
 *
 * Authentication using CAS (Central Authentication Server).
 *
 * 2006-08-28  File created.
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/cas/CAS/CAS.php');
/**
 * CAS authentication plugin.
 */
class auth_plugin_cas extends auth_plugin_base {
    /**
     * Constructor.
     */
    function auth_plugin_cas() {
        $this->authtype = 'cas';
        $this->config = get_config('auth/cas');
        if (empty($this->config->ldapencoding)) {
            $this->config->ldapencoding = 'utf-8';
        }
        if (empty($this->config->user_type)) {
            $this->config->user_type = 'default';
        }
        $default = $this->ldap_getdefaults();
        //use defaults if values not given
        foreach ($default as $key => $value) {
            // watch out - 0, false are correct values too
            if (!isset($this->config->{$key}) or $this->config->{$key} == '') {
                $this->config->{$key} = $value[$this->config->user_type];
            }
        }
        //hack prefix to objectclass
        if (empty($this->config->objectclass)) {        // Can't send empty filter
            $this->config->objectclass='objectClass=*';
        } else if (strpos($this->config->objectclass, 'objectClass=') !== 0) {
            $this->config->objectclass = 'objectClass='.$this->config->objectclass;
        }
    }
    /**
     * Authenticates user againt CAS
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
		$this->connectCAS();
		return phpCAS::isAuthenticated() && (trim(moodle_strtolower(phpCAS::getUser())) == $username);
    }

    function prevent_local_passwords() {
        return true;
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
        return false;
    }
    /**
     * authentication choice (CAS or other)
     * redirection to the CAS form or to login/index.php
     * for other authentication
     */
    function loginpage_hook() {
      global $frm;
      global $CFG;
	  global $SESSION;

      $site = get_site();
      $CASform = get_string("CASform","auth");
      $username = optional_param("username");

      if (!empty($username)) {
          if (isset($SESSION->wantsurl) && (strstr($SESSION->wantsurl, 'ticket') ||
                                            strstr($SESSION->wantsurl, 'NOCAS'))) {
              unset($SESSION->wantsurl);
          }
          return;		
        }


		
		// Test si cas activ� et param�tres non remplis
	  if (empty($this->config->hostname)) {
		  return;
		  }

// Connection to CAS server
	 $this->connectCAS();

         // Don't try to validate the server SSL credentials
         phpCAS::setNoCasServerValidation();

	  // Gestion de la connection CAS si acc�s direct d'un ent ou autre	
	 if (phpCAS::checkAuthentication()) {
		$frm->username=phpCAS::getUser();
//		if (phpCAS::getUser()=='esup9992')
//			$frm->username='erhar0062';
		$frm->password="passwdCas";		
		return;
	 }	 	

          if (isset($_GET["loginguest"]) && ($_GET["loginguest"]== true)) {
			$frm->username="guest";
			$frm->password="guest";
			return;
	  }		
	 
     if ($this->config->multiauth) {
          $authCAS = optional_param("authCAS");
          if ($authCAS=="NOCAS")
            return;

// choice authentication form for multi-authentication
// test pgtIou parameter for proxy mode (https connection
// in background from CAS server to the php server)
      if ($authCAS!="CAS" && !isset($_GET["pgtIou"])) {
            $navlinks = array();
            $navlinks[] = array('name' => $CASform, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);

            print_header("$site->fullname: $CASform", $site->fullname, $navigation);
            include($CFG->dirroot."/auth/cas/cas_form.html");
            print_footer();
            exit();
		 }
     }
// CAS authentication
     if (!phpCAS::isAuthenticated())
        {phpCAS::forceAuthentication();}
}
    /**
     * logout from the cas
     *
     * This function is called from admin/auth.php
     *
     */
    function prelogout_hook() {
        global $CFG;
	  if ($this->config->logoutcas ) {
	        $backurl = $CFG->wwwroot;
		  $this->connectCAS();
                phpCAS::logoutWithURL($backurl);
	     }
    }
    /**
     * Connect to the cas (clientcas connection or proxycas connection
     *
     * This function is called from admin/auth.php
     *
     */
    function connectCAS() {
	
	global $PHPCAS_CLIENT;
// mode proxy CAS
if ( !is_object($PHPCAS_CLIENT) ) {
        // Make sure phpCAS doesn't try to start a new PHP session when connecting to the CAS server.
	if  ($this->config->proxycas) {
	    phpCAS::proxy($this->config->casversion, $this-> config->hostname, (int) $this->config->port, $this->config->baseuri, false);
	}
// mode client CAS
	else {
	    phpCAS::client($this->config->casversion, $this-> config->hostname, (int) $this->config->port, $this->config->baseuri, false);
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
        include 'config.html';
    }
    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return string
     */
    function change_password_url() {
        return "";
    }
    /**
     * returns predefined usertypes
     *
     * @return array of predefined usertypes
     */
    function ldap_suppported_usertypes() {
        $types = array();
        $types['edir']='Novell Edirectory';
        $types['rfc2307']='posixAccount (rfc2307)';
        $types['rfc2307bis']='posixAccount (rfc2307bis)';
        $types['samba']='sambaSamAccount (v.3.0.7)';
        $types['ad']='MS ActiveDirectory';
        $types['default']=get_string('default');
        return $types;
    }
    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        // CAS settings
        if (!isset ($config->hostname))
            $config->hostname = '';
        if (!isset ($config->port))
            $config->port = '';
        if (!isset ($config->casversion))
            $config->casversion = '';
        if (!isset ($config->baseuri))
            $config->baseuri = '';
        if (!isset ($config->language))
            $config->language = '';
        if (!isset ($config->proxycas))
            $config->proxycas = '';
        if (!isset ($config->logoutcas))
            $config->logoutcas = '';
        if (!isset ($config->multiauth))
            $config->multiauth = '';
        // LDAP settings
        if (!isset($config->host_url))
            { $config->host_url = ''; }
        if (empty($config->ldapencoding))
            { $config->ldapencoding = 'utf-8'; }
        if (!isset($config->contexts))
            { $config->contexts = ''; }
        if (!isset($config->user_type))
            { $config->user_type = 'default'; }
        if (!isset($config->user_attribute))
            { $config->user_attribute = ''; }
        if (!isset($config->search_sub))
            { $config->search_sub = ''; }
        if (!isset($config->opt_deref))
            { $config->opt_deref = ''; }
        if (!isset($config->bind_dn))
            {$config->bind_dn = ''; }
        if (!isset($config->bind_pw))
            {$config->bind_pw = ''; }
        if (!isset($config->version))
            {$config->version = '2'; }
        if (!isset($config->objectclass))
            {$config->objectclass = ''; }
        if (!isset($config->memberattribute))
            {$config->memberattribute = ''; }
        if (!isset($config->memberattribute_isdn))
            {$config->memberattribute_isdn = ''; }
        if (!isset($config->attrcreators))
            {$config->attrcreators = ''; }
        if (!isset($config->groupecreators))
            {$config->groupecreators = ''; }
        if (!isset($config->removeuser))
            {$config->removeuser = 0; }
        // save CAS settings
        set_config('hostname',    $config->hostname,    'auth/cas');
        set_config('port',        $config->port,        'auth/cas');
        set_config('casversion',     $config->casversion,     'auth/cas');
        set_config('baseuri',     $config->baseuri,     'auth/cas');
        set_config('language',    $config->language,    'auth/cas');
        set_config('proxycas',     $config->proxycas,     'auth/cas');
        set_config('logoutcas',     $config->logoutcas,     'auth/cas');
        set_config('multiauth',     $config->multiauth,     'auth/cas');
        // save LDAP settings
        set_config('host_url', $config->host_url, 'auth/cas');
        set_config('ldapencoding', $config->ldapencoding, 'auth/cas');
        set_config('host_url', $config->host_url, 'auth/cas');
        set_config('contexts', $config->contexts, 'auth/cas');
        set_config('user_type', $config->user_type, 'auth/cas');
        set_config('user_attribute', $config->user_attribute, 'auth/cas');
        set_config('search_sub', $config->search_sub, 'auth/cas');
        set_config('opt_deref', $config->opt_deref, 'auth/cas');
        set_config('bind_dn', $config->bind_dn, 'auth/cas');
        set_config('bind_pw', $config->bind_pw, 'auth/cas');
        set_config('version', $config->version, 'auth/cas');
        set_config('objectclass', $config->objectclass, 'auth/cas');
        set_config('memberattribute', $config->memberattribute, 'auth/cas');
        set_config('memberattribute_isdn', $config->memberattribute_isdn, 'auth/cas');
        set_config('attrcreators', $config->attrcreators, 'auth/cas');
        set_config('groupecreators', $config->groupecreators, 'auth/cas');
        set_config('removeuser', $config->removeuser, 'auth/cas');
        return true;
    }
    /**
     * Initializes needed ldap variables for cas-module
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
        $default['objectclass'] = array(
                            'edir' => 'User',
                            'rfc2307' => 'posixAccount',
                            'rfc2307bis' => 'posixAccount',
                            'samba' => 'sambaSamAccount',
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
                            'samba' => '0', //is this right?
                            'ad' => '1',
                            'default' => '0'
                            );
        return $default;
    }
    /**
     * reads userinformation from ldap and return it in array()
     *
     * Read user information from external database and returns it as array().
     * Function should return all information available. If you are saving
     * this information to moodle user-table you should honor syncronization flags
     *
     * @param string $username username (with system magic quotes)
     *
     * @return mixed array with no magic quotes or false on error
     */
    function get_userinfo($username) {
        // No LDAP servers configured, so user info has to be provided
        // via other methods (CSV file, manually, etc.). Return empty
        // array so existing user info is not lost.
        if (empty($this->config->host_url)) {
            return array();
        }

        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);
        $ldapconnection = $this->ldap_connect();
        $attrmap = $this->ldap_attributes();
        $result = array();
        $search_attribs = array();
        foreach ($attrmap as $key=>$values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                if (!in_array($value, $search_attribs)) {
                    array_push($search_attribs, $value);
                }
            }
        }
        $user_dn = $this->ldap_find_userdn($ldapconnection, $extusername);
        if (!$user_info_result = ldap_read($ldapconnection, $user_dn, $this->config->objectclass, $search_attribs)) {
            return false; // error!
        }
        $user_entry = $this->ldap_get_entries($ldapconnection, $user_info_result);
        if (empty($user_entry)) {
            return false; // entry not found
        }
        foreach ($attrmap as $key=>$values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            $ldapval = NULL;
           foreach ($values as $value) {
                if ($value == 'dn') {
                    $result[$key] = $user_dn;
                }
                if (!array_key_exists(strtolower($value), $user_entry[0])) {
                    continue; // wrong data mapping!
                }
                if (is_array($user_entry[0][strtolower($value)])) {
                    $newval = $textlib->convert($user_entry[0][strtolower($value)][0], $this->config->ldapencoding, 'utf-8');
                } else {
                    $newval = $textlib->convert($user_entry[0][strtolower($value)], $this->config->ldapencoding, 'utf-8');
                }

                if (!empty($newval)) { // favour ldap entries that are set
                    $ldapval = $newval;
                }
            }
            if (!is_null($ldapval)) {
                $result[$key] = $ldapval;
            }
        }
        $this->ldap_close($ldapconnection);
        return $result;
    }
    /**
     * reads userinformation from ldap and return it in an object
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
        $user = new object();
        foreach ($user_array as $key=>$value) {
            $user->{$key} = $value;
        }
        return $user;
    }
    /**
     * connects to ldap server
     *
     * Tries connect to specified ldap servers.
     * Returns connection result or error.
     *
     * @return connection result
     */
    function ldap_connect($binddn='',$bindpwd='') {
        // Cache ldap connections (they are expensive to set up
        // and can drain the TCP/IP ressources on the server if we 
        // are syncing a lot of users (as we try to open a new connection
        // to get the user details). This is the least invasive way
        // to reuse existing connections without greater code surgery.
        if(!empty($this->ldapconnection)) {
            $this->ldapconns++;
            return $this->ldapconnection;
        }

        //Select bind password, With empty values use
        //ldap_bind_* variables or anonymous bind if ldap_bind_* are empty
        if ($binddn == '' and $bindpwd == '') {
            if (!empty($this->config->bind_dn)) {
               $binddn = $this->config->bind_dn;
            }
            if (!empty($this->config->bind_pw)) {
               $bindpwd = $this->config->bind_pw;
            }
        }
        $urls = explode(";",$this->config->host_url);
        foreach ($urls as $server) {
            $server = trim($server);
            if (empty($server)) {
                continue;
            }
            $connresult = ldap_connect($server);
            //ldap_connect returns ALWAYS true
            if (!empty($this->config->version)) {
                ldap_set_option($connresult, LDAP_OPT_PROTOCOL_VERSION, $this->config->version);
            }
            if ($this->config->user_type == 'ad') {
                 ldap_set_option($connresult, LDAP_OPT_REFERRALS, 0);
            }
            if (!empty($binddn)) {
                //bind with search-user
                //$debuginfo .= 'Using bind user'.$binddn.'and password:'.$bindpwd;
                $bindresult=ldap_bind($connresult, $binddn,$bindpwd);
            }
            else {
                //bind anonymously
                $bindresult=@ldap_bind($connresult);
            }
            if (!empty($this->config->opt_deref)) {
                ldap_set_option($connresult, LDAP_OPT_DEREF, $this->config->opt_deref);
            }
            if ($bindresult) {
                // Set the connection counter so we can call PHP's ldap_close()
                // when we call $this->ldap_close() for the last 'open' connection.
                $this->ldapconns = 1;  
                $this->ldapconnection = $connresult;
                return $connresult;
            }
            $debuginfo .= "<br/>Server: '$server' <br/> Connection: '$connresult'<br/> Bind result: '$bindresult'</br>";
        }
        //If any of servers are alive we have already returned connection
        print_error('auth_ldap_noconnect_all','auth',$this->config->user_type);
        return false;
    }
    /**
     * disconnects from a ldap server
     *
     */
    function ldap_close() {
        $this->ldapconns--;
        if($this->ldapconns == 0) {
            @ldap_close($this->ldapconnection);
            unset($this->ldapconnection);
        }
    }

    /**
     * retuns user attribute mappings between moodle and ldap
     *
     * @return array
     */
    function ldap_attributes () {
        $moodleattributes = array();
        foreach ($this->userfields as $field) {
            if (!empty($this->config->{"field_map_$field"})) {
                $moodleattributes[$field] = $this->config->{"field_map_$field"};
                if (preg_match('/,/',$moodleattributes[$field])) {
                    $moodleattributes[$field] = explode(',', $moodleattributes[$field]); // split ?
                }
            }
        }
        $moodleattributes['username'] = $this->config->user_attribute;
        return $moodleattributes;
    }
    /**
     * retuns dn of username
     *
     * Search specified contexts for username and return user dn
     * like: cn=username,ou=suborg,o=org
     *
     * @param mixed $ldapconnection  $ldapconnection result
     * @param mixed $username username (external encoding no slashes)
     *
     */
    function ldap_find_userdn ($ldapconnection, $extusername) {
        //default return value
        $ldap_user_dn = FALSE;
        //get all contexts and look for first matching user
        $ldap_contexts = explode(";",$this->config->contexts);
        if (!empty($this->config->create_context)) {
          array_push($ldap_contexts, $this->config->create_context);
        }
        foreach ($ldap_contexts as $context) {
            $context = trim($context);
            if (empty($context)) {
                continue;
            }
            if ($this->config->search_sub) {
                //use ldap_search to find first user from subtree
                $ldap_result = ldap_search($ldapconnection, $context, "(".$this->config->user_attribute."=".$this->filter_addslashes($extusername).")",array($this->config->user_attribute));
            }
            else {
                //search only in this context
                $ldap_result = ldap_list($ldapconnection, $context, "(".$this->config->user_attribute."=".$this->filter_addslashes($extusername).")",array($this->config->user_attribute));
            }
            $entry = ldap_first_entry($ldapconnection,$ldap_result);
            if ($entry) {
                $ldap_user_dn = ldap_get_dn($ldapconnection, $entry);
                break ;
            }
        }
        return $ldap_user_dn;
    }
    /**
     * Quote control characters in quoted "texts" used in ldap
     *
     * @param string
     */
    function ldap_addslashes($text) {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(array('"',   "\0"),
                            array('\\"', '\\00'), $text);
        return $text;
    }
    /**
     * returns all usernames from external database
     *
     * get_userlist returns all usernames from external database
     *
     * @return array
     */
    function get_userlist() {
        return $this->ldap_get_userlist("({$this->config->user_attribute}=*)");
    }
    /**
     * checks if user exists on external db
     *
     * @param string $username (with system magic quotes)
     */
    function user_exists($username) {
        $textlib = textlib_get_instance();
        $extusername = $textlib->convert(stripslashes($username), 'utf-8', $this->config->ldapencoding);
        //returns true if given username exist on ldap
        $users = $this->ldap_get_userlist("({$this->config->user_attribute}=".$this->filter_addslashes($extusername).")");
        return count($users);
    }
    /**
     * syncronizes user fron external db to moodle user table
     *
     * Sync is now using username attribute.
     *
     * Syncing users removes or suspends users that dont exists anymore in external db.
     * Creates new users and updates coursecreator status of users.
     *
     * @param int $bulk_insert_records will insert $bulkinsert_records per insert statement
     *                         valid only with $unsafe. increase to a couple thousand for
     *                         blinding fast inserts -- but test it: you may hit mysqld's
     *                         max_allowed_packet limit.
     * @param bool $do_updates will do pull in data updates from ldap if relevant
     */
    function sync_users ($bulk_insert_records = 1000, $do_updates = true) {
        global $CFG;

        if(empty($this->config->host_url)) {
            echo "No LDAP server configured for CAS! Syncing disabled.\n";
            return;
        }

        $textlib = textlib_get_instance();
        $droptablesql = array(); /// sql commands to drop the table (because session scope could be a problem for
                                 /// some persistent drivers like ODBTP (mssql) or if this function is invoked
                                 /// from within a PHP application using persistent connections
        // configure a temp table
        print "Configuring temp table\n";
        switch (strtolower($CFG->dbfamily)) {
            case 'mysql':
                $temptable = $CFG->prefix . 'extuser';
                $droptablesql[] = 'DROP TEMPORARY TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                execute_sql('CREATE TEMPORARY TABLE ' . $temptable . ' (username VARCHAR(64), PRIMARY KEY (username)) TYPE=MyISAM', false);
                break;
            case 'postgres':
                $temptable = $CFG->prefix . 'extuser';
                $droptablesql[] = 'DROP TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                $bulk_insert_records = 1; // no support for multiple sets of values
                execute_sql('CREATE TEMPORARY TABLE '. $temptable . ' (username VARCHAR(64), PRIMARY KEY (username))', false);
                break;
            case 'mssql':
                $temptable = '#'.$CFG->prefix . 'extuser'; /// MSSQL temp tables begin with #
                $droptablesql[] = 'DROP TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                $bulk_insert_records = 1; // no support for multiple sets of values
                execute_sql('CREATE TABLE ' . $temptable . ' (username VARCHAR(64), PRIMARY KEY (username))', false);
                break;
            case 'oracle':
                $temptable = $CFG->prefix . 'extuser';
                $droptablesql[] = 'TRUNCATE TABLE ' . $temptable; // oracle requires truncate before being able to drop a temp table
                $droptablesql[] = 'DROP TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
                execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
                echo "Creating temp table $temptable\n";
                $bulk_insert_records = 1; // no support for multiple sets of values
                execute_sql('CREATE GLOBAL TEMPORARY TABLE '.$temptable.' (username VARCHAR(64), PRIMARY KEY (username)) ON COMMIT PRESERVE ROWS', false);
                break;
        }
        print "Connecting to ldap...\n";
        $ldapconnection = $this->ldap_connect();
        if (!$ldapconnection) {
            $this->ldap_close($ldapconnection);
            print get_string('auth_ldap_noconnect','auth',$this->config->host_url);
            exit;
        }
        ////
        //// get user's list from ldap to sql in a scalable fashion
        ////
        // prepare some data we'll need
        $filter = "(&(".$this->config->user_attribute."=*)(".$this->config->objectclass."))";
        $contexts = explode(";",$this->config->contexts);
        if (!empty($this->config->create_context)) {
              array_push($contexts, $this->config->create_context);
        }
        $fresult = array();
        foreach ($contexts as $context) {
            $context = trim($context);
            if (empty($context)) {
                continue;
            }
            begin_sql();
            if ($this->config->search_sub) {
                //use ldap_search to find first user from subtree
                $ldap_result = ldap_search($ldapconnection, $context,
                                           $filter,
                                           array($this->config->user_attribute));
            } else {
                //search only in this context
                $ldap_result = ldap_list($ldapconnection, $context,
                                         $filter,
                                         array($this->config->user_attribute));
            }
            if ($entry = ldap_first_entry($ldapconnection, $ldap_result)) {
                do {
                    $value = ldap_get_values_len($ldapconnection, $entry, $this->config->user_attribute);
                    $value = $textlib->convert($value[0], $this->config->ldapencoding, 'utf-8');
                    array_push($fresult, $value);
                    if (count($fresult) >= $bulk_insert_records) {
                        $this->ldap_bulk_insert($fresult, $temptable);
                        $fresult = array();
                    }
                } while ($entry = ldap_next_entry($ldapconnection, $entry));
            }
            unset($ldap_result); // free mem
            // insert any remaining users and release mem
            if (count($fresult)) {
                $this->ldap_bulk_insert($fresult, $temptable);
                $fresult = array();
            }
            commit_sql();
        }
        /// preserve our user database
        /// if the temp table is empty, it probably means that something went wrong, exit
        /// so as to avoid mass deletion of users; which is hard to undo
        $count = get_record_sql('SELECT COUNT(username) AS count, 1 FROM ' . $temptable);
        $count = $count->{'count'};
        if ($count < 1) {
            print "Did not get any users from LDAP -- error? -- exiting\n";
            exit;
        } else {
            print "Got $count records from LDAP\n\n";
        }
/// User removal
        // find users in DB that aren't in ldap -- to be removed!
        // this is still not as scalable (but how often do we mass delete?)
        if (!empty($this->config->removeuser)) {
            $sql = "SELECT u.id, u.username, u.email, u.auth
                    FROM {$CFG->prefix}user u
                        LEFT JOIN $temptable e ON u.username = e.username
                    WHERE u.auth='cas'
                        AND u.deleted=0
                        AND e.username IS NULL";
            $remove_users = get_records_sql($sql);
            if (!empty($remove_users)) {
                print "User entries to remove: ". count($remove_users) . "\n";
                foreach ($remove_users as $user) {
                    if ($this->config->removeuser == 2) {
                        if (delete_user($user)) {
                            echo "\t"; print_string('auth_dbdeleteuser', 'auth', array($user->username, $user->id)); echo "\n";
                        } else {
                            echo "\t"; print_string('auth_dbdeleteusererror', 'auth', $user->username); echo "\n";
                        }
                    } else if ($this->config->removeuser == 1) {
                        $updateuser = new object();
                        $updateuser->id = $user->id;
                        $updateuser->auth = 'nologin';
                        if (update_record('user', $updateuser)) {
                            echo "\t"; print_string('auth_dbsuspenduser', 'auth', array($user->username, $user->id)); echo "\n";
                        } else {
                            echo "\t"; print_string('auth_dbsuspendusererror', 'auth', $user->username); echo "\n";
                        }
                    }
                }
            } else {
                print "No user entries to be removed\n";
            }
            unset($remove_users); // free mem!
        }
/// Revive suspended users
        if (!empty($this->config->removeuser) and $this->config->removeuser == 1) {
            $sql = "SELECT u.id, u.username
                    FROM $temptable e, {$CFG->prefix}user u
                    WHERE e.username=u.username
                        AND u.auth='nologin'";
            $revive_users = get_records_sql($sql);
            if (!empty($revive_users)) {
                print "User entries to be revived: ". count($revive_users) . "\n";
                begin_sql();
                foreach ($revive_users as $user) {
                    $updateuser = new object();
                    $updateuser->id = $user->id;
                    $updateuser->auth = 'cas';
                    if (update_record('user', $updateuser)) {
                        echo "\t"; print_string('auth_dbreviveduser', 'auth', array($user->username, $user->id)); echo "\n";
                    } else {
                        echo "\t"; print_string('auth_dbrevivedusererror', 'auth', $user->username); echo "\n";
                    }
                }
                commit_sql();
            } else {
                print "No user entries to be revived\n";
            }
            unset($revive_users);
        }
/// User Updates - time-consuming (optional)
        if ($do_updates) {
            // narrow down what fields we need to update
            $all_keys = array_keys(get_object_vars($this->config));
            $updatekeys = array();
            foreach ($all_keys as $key) {
                if (preg_match('/^field_updatelocal_(.+)$/',$key, $match)) {
                    // if we have a field to update it from
                    // and it must be updated 'onlogin' we
                    // update it on cron
                    if ( !empty($this->config->{'field_map_'.$match[1]})
                         and $this->config->{$match[0]} === 'onlogin') {
                        array_push($updatekeys, $match[1]); // the actual key name
                    }
                }
            }
            // print_r($all_keys); print_r($updatekeys);
            unset($all_keys); unset($key);
        } else {
            print "No updates to be done\n";
        }
        if ( $do_updates and !empty($updatekeys) ) { // run updates only if relevant
            $users = get_records_sql("SELECT u.username, u.id
                                      FROM {$CFG->prefix}user u
                                      WHERE u.deleted=0 AND u.auth='cas'");
            if (!empty($users)) {
                print "User entries to update: ". count($users). "\n";
                $sitecontext = get_context_instance(CONTEXT_SYSTEM);
                if (!empty($this->config->creators) and !empty($this->config->memberattribute)
                  and $roles = get_roles_with_capability('moodle/legacy:coursecreator', CAP_ALLOW)) {
                    $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
                } else {
                    $creatorrole = false;
                }
                begin_sql();
                $xcount = 0;
                $maxxcount = 100;
                foreach ($users as $user) {
                    echo "\t"; print_string('auth_dbupdatinguser', 'auth', array($user->username, $user->id));
                    if (!$this->update_user_record(addslashes($user->username), $updatekeys)) {
                        echo " - ".get_string('skipped');
                    }
                    echo "\n";
                    $xcount++;
                    // update course creators if needed
                    if ($creatorrole !== false) {
                        if ($this->iscreator($user->username)) {
                            role_assign($creatorrole->id, $user->id, 0, $sitecontext->id, 0, 0, 0, 'cas');
                        } else {
                            role_unassign($creatorrole->id, $user->id, 0, $sitecontext->id, 'cas');
                        }
                    }
                    if ($xcount++ > $maxxcount) {
                        commit_sql();
                        begin_sql();
                        $xcount = 0;
                    }
                }
                commit_sql();
                unset($users); // free mem
            }
        } else { // end do updates
            print "No updates to be done\n";
        }
/// User Additions
        // find users missing in DB that are in LDAP
        // note that get_records_sql wants at least 2 fields returned,
        // and gives me a nifty object I don't want.
        // note: we do not care about deleted accounts anymore, this feature was replaced by suspending to nologin auth plugin
        $sql = "SELECT e.username, e.username
                FROM $temptable e LEFT JOIN {$CFG->prefix}user u ON e.username = u.username
                WHERE u.id IS NULL";
        $add_users = get_records_sql($sql); // get rid of the fat
        if (!empty($add_users)) {
            print "User entries to add: ". count($add_users). "\n";
            $sitecontext = get_context_instance(CONTEXT_SYSTEM);
            if (!empty($this->config->creators) and !empty($this->config->memberattribute)
              and $roles = get_roles_with_capability('moodle/legacy:coursecreator', CAP_ALLOW)) {
                $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
            } else {
                $creatorrole = false;
            }
            begin_sql();
            foreach ($add_users as $user) {
                $user = $this->get_userinfo_asobj(addslashes($user->username));
                // prep a few params
                $user->modified   = time();
                $user->confirmed  = 1;
                $user->auth       = 'cas';
                $user->mnethostid = $CFG->mnet_localhost_id;
                if (empty($user->lang)) {
                    $user->lang = $CFG->lang;
                }
                $user = addslashes_recursive($user);
                if ($id = insert_record('user',$user)) {
                    echo "\t"; print_string('auth_dbinsertuser', 'auth', array(stripslashes($user->username), $id)); echo "\n";
                    $userobj = $this->update_user_record($user->username);
                    if (!empty($this->config->forcechangepassword)) {
                        set_user_preference('auth_forcepasswordchange', 1, $userobj->id);
                    }
                } else {
                    echo "\t"; print_string('auth_dbinsertusererror', 'auth', $user->username); echo "\n";
                }
                // add course creators if needed
                if ($creatorrole !== false and $this->iscreator(stripslashes($user->username))) {
                    role_assign($creatorrole->id, $user->id, 0, $sitecontext->id, 0, 0, 0, 'cas');
                }
            }
            commit_sql();
            unset($add_users); // free mem
        } else {
            print "No users to be added\n";
        }
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
     * @param string $username username (with system magic quotes)
     */
    function update_user_record($username, $updatekeys = false) {
        global $CFG;
        //just in case check text case
        $username = trim(moodle_strtolower($username));
        // get the current user record
        $user = get_record('user', 'username', $username, 'mnethostid', $CFG->mnet_localhost_id);
        if (empty($user)) { // trouble
            error_log("Cannot update non-existent user: ".stripslashes($username));
            print_error('auth_dbusernotexist','auth',$username);
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
                        set_field('user', $key, addslashes($value), 'id', $userid);
                    }
                }
            }
        } else {
            return false;
        }
        return get_record_select('user', "id = $userid AND deleted = 0");
    }
    /**
     * Bulk insert in SQL's temp table
     * @param array $users is an array of usernames
     */
    function ldap_bulk_insert($users, $temptable) {
        // bulk insert -- superfast with $bulk_insert_records
        $sql = 'INSERT INTO ' . $temptable . ' (username) VALUES ';
        // make those values safe
        $users = addslashes_recursive($users);
        // join and quote the whole lot
        $sql = $sql . "('" . implode("'),('", $users) . "')";
        print "\t+ " . count($users) . " users\n";
        execute_sql($sql, false);
    }
    /**
     * Returns true if user should be coursecreator.
     *
     * @param mixed $username    username (without system magic quotes)
     * @return boolean result
     */
    function iscreator($username) {
        if (empty($this->config->host_url) or (empty($this->config->attrcreators) && empty($this->config->groupecreators)) or empty($this->config->memberattribute)) {
            return null;
        }
        $textlib = textlib_get_instance();
        $extusername = $textlib->convert($username, 'utf-8', $this->config->ldapencoding);
//test for groupe creator
if (!empty($this->config->groupecreators))
   if ((boolean)$this->ldap_isgroupmember($extusername, $this->config->groupecreators))
        return true;
//build filter for attrcreator
if (!empty($this->config->attrcreators)) {
    $attrs = explode(";",$this->config->attrcreators);
    $filter = "(& (".$this->config->user_attribute."=$username)(|";
    foreach ($attrs as $attr){
        if(strpos($attr, "="))
        	$filter .= "($attr)";
        else
        	$filter .= "(".$this->config->memberattribute."=$attr)";
    }
    $filter .= "))";
    //search
    $result = $this->ldap_get_userlist($filter);
    if (count($result)!=0)
    	return true;
 	}

    return false;
    }
   /**
     * checks if user belong to specific group(s)
     *
     * Returns true if user belongs group in grupdns string.
     *
     * @param mixed $username    username
     * @param mixed $groupdns    string of group dn separated by ;
     *
     */
    function ldap_isgroupmember($extusername='', $groupdns='') {
    // Takes username and groupdn(s) , separated by ;
    // Returns true if user is member of any given groups
        $ldapconnection = $this->ldap_connect();
        if (empty($extusername) or empty($groupdns)) {
            return false;
            }
        if ($this->config->memberattribute_isdn) {
            $memberuser = $this->ldap_find_userdn($ldapconnection, $extusername);
        } else {
            $memberuser = $extusername;
        }
        if (empty($memberuser)) {
            return false;
        }
        $groups = explode(";",$groupdns);
        $result = false;
        foreach ($groups as $group) {
            $group = trim($group);
            if (empty($group)) {
                continue;
            }
            //echo "Checking group $group for member $username\n";
            $search = ldap_read($ldapconnection, $group,  '('.$this->config->memberattribute.'='.$this->filter_addslashes($memberuser).')', array($this->config->memberattribute));
            if (!empty($search) and ldap_count_entries($ldapconnection, $search)) {
                $info = $this->ldap_get_entries($ldapconnection, $search);
                if (count($info) > 0 ) {
                    // user is member of group
                    $result = true;
                    break;
                }
          }
        }
        $this->ldap_close();
        return $result;
    }
   /**
     * return all usernames from ldap
     *
     * @return array
     */
    function ldap_get_userlist($filter="*") {
    /// returns all users from ldap servers
        $fresult = array();
        $ldapconnection = $this->ldap_connect();
        if ($filter=="*") {
           $filter = "(&(".$this->config->user_attribute."=*)(".$this->config->objectclass."))";
        }
        $contexts = explode(";",$this->config->contexts);
        if (!empty($this->config->create_context)) {
              array_push($contexts, $this->config->create_context);
        }
        foreach ($contexts as $context) {
            $context = trim($context);
            if (empty($context)) {
                continue;
            }
            if ($this->config->search_sub) {
                //use ldap_search to find first user from subtree
                $ldap_result = ldap_search($ldapconnection, $context,$filter,array($this->config->user_attribute));
            }
            else {
                //search only in this context
                $ldap_result = ldap_list($ldapconnection, $context,
                                         $filter,
                                         array($this->config->user_attribute));
            }
            $users = $this->ldap_get_entries($ldapconnection, $ldap_result);
            //add found users to list
            for ($i=0;$i<count($users);$i++) {
                array_push($fresult, ($users[$i][$this->config->user_attribute][0]) );
            }
        }
        $this->ldap_close();
        return $fresult;
    }
    /**
     * return entries from ldap
     *
     * Returns values like ldap_get_entries but is
     * binary compatible and return all attributes as array
     *
     * @return array ldap-entries
     */
    function ldap_get_entries($conn, $searchresult) {
    //Returns values like ldap_get_entries but is
    //binary compatible
        $i=0;
        $fresult=array();
        $entry = ldap_first_entry($conn, $searchresult);
        do {
            $attributes = @ldap_get_attributes($conn, $entry);
            for ($j=0; $j<$attributes['count']; $j++) {
                $values = ldap_get_values_len($conn, $entry,$attributes[$j]);				

                if (is_array($values)) {
                $fresult[$i][strtolower($attributes[$j])] = $values;
                }
                else {
                    $fresult[$i][strtolower($attributes[$j])] = array($values);
                }
            }
            $i++;
        }
        while ($entry = @ldap_next_entry($conn, $entry));
        //were done

        return ($fresult);
    }
    /**
     * Sync roles for this user
     *
     * @param $user object user object (without system magic quotes)
     */
    function sync_roles($user) {
        $iscreator = $this->iscreator($user->username);
        if ($iscreator === null) {
            return; //nothing to sync - creators not configured
        }
        if ($roles = get_roles_with_capability('moodle/legacy:coursecreator', CAP_ALLOW)) {
            $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
            if ($iscreator) { // Following calls will not create duplicates
                role_assign($creatorrole->id, $user->id, 0, $systemcontext->id, 0, 0, 0, 'cas');
            } else {
                //unassign only if previously assigned by this plugin!
                role_unassign($creatorrole->id, $user->id, 0, $systemcontext->id, 'cas');
            }
        }
    }
   /**
     * Quote control characters in texts used in ldap filters - see rfc2254.txt
     *
     * @param string
     */
    function filter_addslashes($text) {
        $text = str_replace('\\', '\\5c', $text);
        $text = str_replace(array('*',    '(',    ')',    "\0"),
                            array('\\2a', '\\28', '\\29', '\\00'), $text);
        return $text;
    }
}
?>
