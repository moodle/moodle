<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: RADIUS Authentication
 *
 * Authenticates against a RADIUS server.
 * Contributed by Clive Gould <clive@ce.bromley.ac.uk>
 *
 * 2006-08-31  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * RADIUS authentication plugin.
 */
class auth_plugin_radius {

    /**
     * The configuration details for the plugin.
     */
    var $config;

    /**
     * Constructor.
     */
    function auth_plugin_radius() {
        $this->config = get_config('auth/radius');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        require_once 'Auth/RADIUS.php';

        // Added by Clive on 7th May for test purposes
        // printf("Username: $username <br/>");
        // printf("Password: $password <br/>");
        // printf("host: $this->config->host <br/>");
        // printf("nasport: $this->config->nasport <br/>");
        // printf("secret: $this->config->secret <br/>");

        $rauth = new Auth_RADIUS_PAP(stripslashes($username), stripslashes($password));
        $rauth->addServer($this->config->host, $this->config->nasport, $this->config->secret);

        if (!$rauth->start()) {
            printf("Radius start: %s<br/>\n", $rauth->getError());
            exit;
        }

        $result = $rauth->send();
        if (PEAR::isError($result)) {
            printf("Radius send failed: %s<br/>\n", $result->getMessage());
            exit;
        } else if ($result === true) {
            // printf("Radius Auth succeeded<br/>\n");
            return true;
        } else {
            // printf("Radius Auth rejected<br/>\n");
            return false;
        }

        // get attributes, even if auth failed
        if (!$rauth->getAttributes()) {
            printf("Radius getAttributes: %s<br/>\n", $rauth->getError());
        } else {
            $rauth->dumpAttributes();
        }

        $rauth->close();
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
        if (!isset ($config->host)) {
            $config->host = '127.0.0.1';
        }
        if (!isset ($config->nasport)) {
            $config->nasport = '1812';
        }
        if (!isset ($config->secret)) {
            $config->secret = '';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('host',    $config->host,    'auth/radius');
        set_config('nasport', $config->nasport, 'auth/radius');
        set_config('secret',  $config->secret,  'auth/radius');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/radius');

        return true;
    }

}

?>
