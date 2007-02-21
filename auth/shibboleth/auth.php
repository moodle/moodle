<?php
/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Shibboleth Authentication
 *
 * Authentication using Shibboleth.
 *
 * 10.2004 SHIBBOLETH Authentication functions v.0.1
 * 05.2005 Various extensions and fixes by Lukas Haemmerle
 * 10.2005 Added better error messags
 * 05.2006 Added better handling of mutli-valued attributes
 * Distributed under GPL (c)Markus Hagman 2004-2006
 *
 * 2006-08-28  File created, code imported from lib.php
 * 2006-10-27  Upstream 1.7 changes merged in, added above credits from lib.php :-)
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * Shibboleth authentication plugin.
 */
class auth_plugin_shibboleth {

    /**
     * The configuration details for the plugin.
     */
    var $config;

    /**
     * Constructor.
     */
    function auth_plugin_shibboleth() {
        $this->config = get_config('auth/shibboleth');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        // If we are in the shibboleth directory then we trust the server var
        if (!empty($_SERVER[$config->user_attribute])) {
            return ($_SERVER[$config->user_attribute] == $username);
        } else {
            // If we are not, the user has used the manual login and the login name is
            // unknown, so we return false.
            return false;
        }
    }

    function get_userinfo($username) {
    // reads user information from shibboleth attributes and return it in array()
        global $CFG;

        // Check whether we have got all the essential attributes
        if (
               empty($_SERVER[$config->user_attribute])
            || empty($_SERVER[$config->field_map_firstname])
            || empty($_SERVER[$config->field_map_lastname])
            || empty($_SERVER[$config->field_map_email])
            ) {
            error(get_string( 'shib_not_all_attributes_error', 'auth' , "'".$config->user_attribute."' ('".$_SERVER[$config->user_attribute]."'), '".$config->field_map_firstname."' ('".$_SERVER[$config->field_map_firstname]."'), '".$config->field_map_lastname."' ('".$_SERVER[$config->field_map_lastname]."') and '".$config->field_map_email."' ('".$_SERVER[$config->field_map_email]."')"));
        }

        $attrmap = $this->get_attributes();

        $result = array();
        $search_attribs = array();

        foreach ($attrmap as $key=>$value) {
            $result[$key] = $this->get_first_string($_SERVER[$value]);
        }

         // Provide an API to modify the information to fit the Moodle internal
        // data representation
        if (
              $config->convert_data
              && $config->convert_data != ''
              && is_readable($config->convert_data)
            ) {

            // Include a custom file outside the Moodle dir to
            // modify the variable $moodleattributes
            include($config->convert_data);
        }

        return $result;
    }

    /*
     * Returns array containg attribute mappings between Moodle and Shibboleth.
     */
    function get_attributes() {
        $configarray = (array) $this->config;

        $fields = array("firstname", "lastname", "email", "phone1", "phone2",
                        "department", "address", "city", "country", "description",
                        "idnumber", "lang", "guid");

        $moodleattributes = array();
        foreach ($fields as $field) {
            if ($configarray["field_map_$field"]) {
                $moodleattributes[$field] = $configarray["field_map_$field"];
            }
        }
        $moodleattributes['username'] = $configarray["user_attribute"];

        return $moodleattributes;
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
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset($config->auth_instructions) or empty($config->user_attribute)) {
            $config->auth_instructions = get_string('shibboleth_instructions', 'auth', $CFG->wwwroot.'/auth/shibboleth/index.php');
        }
        if (!isset ($config->user_attribute)) {
            $config->user_attribute = '';
        }
        if (!isset ($config->convert_data)) {
            $config->convert_data = '';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('user_attribute',    $config->user_attribute,    'auth/shibboleth');
        set_config('convert_data',      $config->convert_data,      'auth/shibboleth');
        set_config('auth_instructions', $config->auth_instructions, 'auth/shibboleth');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/shibboleth');

        return true;
    }

    /**
     * Cleans and returns first of potential many values (multi-valued attributes)
     */
    function get_first_string($string) {
        $list = split( ';', $string);
        $clean_string = rtrim($list[0]);

        return $clean_string;
    }
}

?>
