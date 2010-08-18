<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: POP3 Authentication
 *
 * Authenticates against a POP3 server.
 *
 * 2006-08-31  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * POP3 authentication plugin.
 */
class auth_plugin_pop3 extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_pop3() {
        $this->authtype = 'pop3';
        $this->config = get_config('auth/pop3');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        if (! function_exists('imap_open')) {
            print_error('auth_pop3notinstalled','auth_pop3');
            exit;
        }

        global $CFG;
        $hosts = explode(';', $this->config->host);   // Could be multiple hosts
        foreach ($hosts as $host) {                 // Try each host in turn
            $host = trim($host);

            // remove any trailing slash
            if (substr($host, -1) == '/') {
                $host = substr($host, 0, strlen($host) - 1);
            }

            switch ($this->config->type) {
                case 'pop3':
                    $host = '{'.$host.":{$this->config->port}/pop3}{$this->config->mailbox}";
                break;

                case 'pop3notls':
                    $host = '{'.$host.":{$this->config->port}/pop3/notls}{$this->config->mailbox}";
                break;

                case 'pop3cert':
                    $host = '{'.$host.":{$this->config->port}/pop3/ssl/novalidate-cert}{$this->config->mailbox}";
                break;
            }

            error_reporting(0);
            $connection = imap_open($host, $username, $password);
            error_reporting($CFG->debug);

            if ($connection) {
                imap_close($connection);
                return true;
            }
        }
        return false;  // No matches found
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
        return !empty($this->config->changepasswordurl);
    }

    /**
     * Returns the URL for changing the user's pw, or false if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return new moodle_url($this->config->changepasswordurl);
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
        global $OUTPUT;

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
        if (!isset ($config->type)) {
            $config->type = 'pop3notls';
        }
        if (!isset ($config->port)) {
            $config->port = '143';
        }
        if (!isset ($config->mailbox)) {
            $config->mailbox = 'INBOX';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('host',    $config->host,    'auth/pop3');
        set_config('type',    $config->type,    'auth/pop3');
        set_config('port',    $config->port,    'auth/pop3');
        set_config('mailbox', $config->mailbox, 'auth/pop3');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/pop3');

        return true;
    }

}


