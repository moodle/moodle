<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: NNTP Authentication
 *
 * Authenticates against an NNTP server.
 *
 * 2006-08-31  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * NNTP authentication plugin.
 */
class auth_plugin_nntp extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_nntp() {
        $this->authtype = 'nntp';
        $this->config = get_config('auth/nntp');
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
        if (! function_exists('imap_open')) {
            print_error('auth_nntpnotinstalled','auth');
            exit;
        }

        global $CFG;

        // try each multiple host
        $hosts = split(';', $this->config->host);
        foreach ($hosts as $host) {
            $host = '{' . trim($host) . ':' . $this->config->port . '/nntp}';

            error_reporting(0);
            $connection = imap_open($host, stripslashes($username), stripslashes($password), OP_HALFOPEN);
            error_reporting($CFG->debug);

            if ($connection) {
                imap_close($connection);
                return true;
            }
        }
        return false;
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
        if (!isset ($config->port)) {
            $config->port = '119';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('host', $config->host, 'auth/nntp');
        set_config('port', $config->port, 'auth/nntp');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/nntp');

        return true;
    }

}

?>
