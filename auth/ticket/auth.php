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
 * @package     auth_ticket
 * @category    auth
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright   (C) 2010 ValEISTI (http://www.valeisti.fr)
 * @copyright   (C) 2012 onwards Valery Fremaux (http://www.mylearningfactory.com)
 *
 * implements an external access with encrypted access ticket for notification returns
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/ticket/lib.php');

/**
 * Moodle Ticket based authentication.
 */
class auth_plugin_ticket extends auth_plugin_base {

    /**
     * The name of the component. Used by the configuration.
     */
    const COMPONENT_NAME = 'auth_ticket';
    const LEGACY_COMPONENT_NAME = 'auth/ticket';

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'ticket';
        $config = get_config(self::COMPONENT_NAME);
        $legacyconfig = get_config(self::LEGACY_COMPONENT_NAME);
        $this->config = (object)array_merge((array)$legacyconfig, (array)$config);
    }

    /**
     * This function is normally used to determine if the username and password
     * are correct for local logins. Always returns false, as local users do not
     * need to login over mnet xmlrpc.
     *
     * @param string $username
     * @param string $password
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password) {

        // If everything failed, we let the next authentication plugin play.
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param object $config Configuration object
     * @param array $err
     * @param array $userfields
     */
    public function config_form($config, $err, $userfields) {
        global $CFG;

        include($CFG->dirroot.'/auth/ticket/config.html');
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param object $config Configuration object
     */
    public function process_config($config) {

        if (!$config) {
            $config = new StdClass();
            $config->shortvaliditydelay = 2;
            $config->longvaliditydelay = 24;
            $config->persistantvaliditydelay = 5;
        }

        // Set to defaults if undefined.
        $conf = $config->shortvaliditydelay * HOURSECS;
        $config->shortvaliditydelay = (@$config->shortvaliditydelay) ? $conf : HOURSECS * 2;
        $conf = $config->longvaliditydelay * HOURSECS;
        $config->longvaliditydelay = (@$config->longvaliditydelay) ? $conf : HOURSECS * 24;
        $conf = $config->persistantvaliditydelay * DAYSECS;
        if (!isset($config->persistantvaliditydelay)) {
            $config->persistantvaliditydelay = DAYSECS * 90;
            set_config('persistantvaliditydelay', DAYSECS * 90, 'auth_ticket');
        }
        $config->persistantvaliditydelay = (isset($config->persistantvaliditydelay)) ? $conf : DAYSECS * 90;
        $config->usessl = (isset($config->usessl)) ? $config->usessl : 1;

        // Save settings.
        set_config('shortvaliditydelay', $config->shortvaliditydelay, self::COMPONENT_NAME);
        set_config('longvaliditydelay', $config->longvaliditydelay, self::COMPONENT_NAME);
        set_config('persistantvaliditydelay', $config->persistantvaliditydelay, self::COMPONENT_NAME);
        set_config('usessl', $config->usessl, self::COMPONENT_NAME);

        return true;
    }

    /**
     * we do not propose any hooking for explicit login page
     *
     */
    public function loginpage_hook() {
        global $USER, $DB;
        global $frm; // We must catch the login/index.php $user credential holder.
        global $user;

        $config = get_config(self::COMPONENT_NAME);

        if (empty($config->longvaliditydelay)) {
            // Ensure defaults are set.
            $this->process_config(null);
            $config = get_config(self::COMPONENT_NAME);
        }

        $sealedticket = optional_param('ticket', null, PARAM_RAW);
        if (!$sealedticket) {
            // Do nothing but try other login methods.
            return false;
        }

        $ticket = ticket_decode($sealedticket);

        if (!empty($ticket)) {
            if (!$this->validate_timeguard($ticket)) {
                return false;
            }
            $user = $DB->get_record('user', array('username' => $ticket->username, 'deleted' => 0));

            $user = $USER = complete_user_login($user);
            $url = str_replace('\\', '', $ticket->wantsurl);
            redirect($url);
        }
        return false;
    }

    /**
     *
     */
    public function logoutpage_hook() {
        return;
    }

    /**
     * Checks the time validity of a ticket.
     *
     * @param objectref &$ticket
     */
    public function validate_timeguard(&$ticket) {

        $config = get_config(self::COMPONENT_NAME);

        if (empty($ticket->term)) {
            $ticket->term = 'short';
        }

        switch ($ticket->term) {
            case 'persistant': {
                /*
                 * This is a passthrough. However, we consider that a 6 years old ticket
                 * might be an exterme limit.
                 */
                if ($ticket->date < (time() - $config->persistantvaliditydelay)) {
                    return false;
                }
                break;
            }

            case 'long': {
                if ($ticket->date < (time() - $config->longvaliditydelay)) {
                    return false;
                }
                break;
            }

            case 'short':
            default :
                if ($ticket->date < (time() - $config->shortvaliditydelay)) {
                    return false;
                }
        }

        return true;
    }
}
