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
 * Authentication Plugin: FirstClass Authentication
 * Authentication using a FirstClass server.

 * @package auth_fc
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

require_once 'fcFPP.php';

/**
 * FirstClass authentication plugin.
 */
class auth_plugin_fc extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_fc() {
        $this->authtype = 'fc';
        $this->config = get_config('auth/fc');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        global $CFG;
        $retval = false;

        // Don't allow blank usernames or passwords
        if (!$username or !$password) {
            return $retval;
        }

        $fpp = new fcFPP($this->config->host, $this->config->fppport);
        if ($fpp->open()) {
            if ($fpp->login($username, $password)) {
                $retval = true;
            }
        }
        $fpp->close();

        return $retval;
    }

    /**
     * Get user information from FirstCLass server and return it in an array.
     * Localize this routine to fit your needs.
     */
    function get_userinfo($username) {
        /*
        Moodle                FirstCLass fieldID in UserInfo form
        ------                -----------------------------------
        firstname             1202
        lastname              1204
        email                 1252
        icq                   -
        phone1                1206
        phone2                1207 (Fax)
        institution           -
        department            -
        address               1205
        city                  -
        country               -
        lang                  -
        timezone              8030 (Not used yet. Need to figure out how FC codes timezones)

        description           Get data from users resume. Pictures will be removed.

        */

        $userinfo = array();

        $fpp = new fcFPP($this->config->host, $this->config->fppport);
        if ($fpp->open()) {
            if ($fpp->login($this->config->userid, $this->config->passwd)) {
                $userinfo['firstname']   = $fpp->getUserInfo($username,"1202");
                $userinfo['lastname']    = $fpp->getUserInfo($username,"1204");
                $userinfo['email']       = strtok($fpp->getUserInfo($username,"1252"),',');
                $userinfo['phone1']      = $fpp->getUserInfo($username,"1206");
                $userinfo['phone2']      = $fpp->getUserInfo($username,"1207");
                $userinfo['description'] = $fpp->getResume($username);
            }
        }
        $fpp->close();

        foreach($userinfo as $key => $value) {
            if (!$value) {
                unset($userinfo[$key]);
            }
        }

        return $userinfo;
    }

    /**
     * Get users group membership from the FirstClass server user and check if
     * user is member of one of the groups of creators.
     */
    function iscreator($username) {
        if (! $this->config->creators) {
            return null;
        }

        $fcgroups = array();

        $fpp = new fcFPP($this->config->host, $this->config->fppport);
        if ($fpp->open()) {
            if ($fpp->login($this->config->userid, $this->config->passwd)) {
                $fcgroups = $fpp->getGroups($username);
            }
        }
        $fpp->close();

        if ((! $fcgroups)) {
            return false;
        }

        $creators = explode(";", $this->config->creators);

        foreach($creators as $creator) {
            if (in_array($creator, $fcgroups)) {
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
     * Sync roles for this user
     *
     * @param $user object user object (without system magic quotes)
     */
    function sync_roles($user) {
        $iscreator = $this->iscreator($user->username);
        if ($iscreator === null) {
            return; //nothing to sync - creators not configured
        }

        if ($roles = get_archetype_roles('coursecreator')) {
            $creatorrole = array_shift($roles);      // We can only use one, let's use the first one
            $systemcontext = context_system::instance();

            if ($iscreator) { // Following calls will not create duplicates
                role_assign($creatorrole->id, $user->id, $systemcontext->id, 'auth_fc');
            } else {
                //unassign only if previously assigned by this plugin!
                role_unassign($creatorrole->id, $user->id, $systemcontext->id, 'auth_fc');
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
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset($config->host)) {
            $config->host = "127.0.0.1";
        }
        if (!isset($config->fppport)) {
            $config->fppport = "3333";
        }
        if (!isset($config->userid)) {
            $config->userid = "fcMoodle";
        }
        if (!isset($config->passwd)) {
            $config->passwd = "";
        }
        if (!isset($config->creators)) {
            $config->creators = "";
        }
        if (!isset($config->changepasswordurl)) {
            $config->changepasswordurl = '';
        }

        // save settings
        set_config('host',      $config->host,     'auth/fc');
        set_config('fppport',   $config->fppport,  'auth/fc');
        set_config('userid',    $config->userid,   'auth/fc');
        set_config('passwd',    $config->passwd,   'auth/fc');
        set_config('creators',  $config->creators, 'auth/fc');
        set_config('changepasswordurl', $config->changepasswordurl, 'auth/fc');

        return true;
    }

}


