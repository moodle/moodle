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
 * Authentication Plugin: IMAP Authentication
 * Authenticates against an IMAP server.
 *
 * @package auth_imap
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * IMAP authentication plugin.
 */
class auth_plugin_imap extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'imap';
        $this->config = get_config('auth/imap');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_imap() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
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
            print_error('auth_imapnotinstalled','mnet');
            return false;
        }

        global $CFG;
        $hosts = explode(';', $this->config->host);   // Could be multiple hosts

        foreach ($hosts as $host) {                 // Try each host in turn
            $host = trim($host);

            switch ($this->config->type) {
                case 'imapssl':
                    $host = '{'.$host.":{$this->config->port}/imap/ssl}";
                break;

                case 'imapcert':
                    $host = '{'.$host.":{$this->config->port}/imap/ssl/novalidate-cert}";
                break;

                case 'imaptls':
                    $host = '{'.$host.":{$this->config->port}/imap/tls}";
                break;

                case 'imapnosslcert':
                    $host = '{'.$host.":{$this->config->port}/imap/novalidate-cert}";
                break;

                default:
                    $host = '{'.$host.":{$this->config->port}/imap}";
            }

            error_reporting(0);
            $connection = imap_open($host, $username, $password, OP_HALFOPEN);
            error_reporting($CFG->debug);

            if ($connection) {
                imap_close($connection);
                return true;
            }
        }

        return false;  // No match
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
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        if (!empty($this->config->changepasswordurl)) {
            return new moodle_url($this->config->changepasswordurl);
        } else {
            return null;
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
            $config->type = 'imap';
        }
        if (!isset ($config->port)) {
            $config->port = '143';
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('host', $config->host, 'auth/imap');
        set_config('type', $config->type, 'auth/imap');
        set_config('port', $config->port, 'auth/imap');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/imap');

        return true;
    }

}


