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
 * Authentication Plugin: POP3 Authentication
 * Authenticates against a POP3 server.
 *
 * @package auth_pop3
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * POP3 authentication plugin.
 */
class auth_plugin_pop3 extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'pop3';
        $this->config = get_config('auth_pop3');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_pop3() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
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
        if (!empty($this->config->changepasswordurl)) {
            return new moodle_url($this->config->changepasswordurl);
        } else {
            return null;
        }
    }

}


