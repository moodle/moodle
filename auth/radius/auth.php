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
 * CHAP support contributed by Stanislav Tsymbalov http://www.tsymbalov.net/
 *
 * 2006-08-31  File created.
 * 2008-03-12  CHAP support added by Stanislav Tsymbalov.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/authlib.php');

/**
 * RADIUS authentication plugin.
 */
class auth_plugin_radius extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_radius() {
        $this->authtype = 'radius';
        $this->config = get_config('auth/radius');
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
        require_once 'Auth/RADIUS.php';
        require_once 'Crypt/CHAP.php';

        // Added by Clive on 7th May for test purposes
        // printf("Username: $username <br/>");
        // printf("Password: $password <br/>");
        // printf("host: $this->config->host <br/>");
        // printf("nasport: $this->config->nasport <br/>");
        // printf("secret: $this->config->secret <br/>");

        // Added by Stanislav Tsymbalov on 12th March 2008 only for test purposes
        //$type = 'PAP';
        //$type = 'CHAP_MD5';
        //$type = 'MSCHAPv1';
        //$type = 'MSCHAPv2';
        $type = $this->config->radiustype;
        if (empty($type)) {
            $type = 'PAP';
        }

        $classname = 'Auth_RADIUS_' . $type;
        $rauth = new $classname($username, $password);
        $rauth->addServer($this->config->host, $this->config->nasport, $this->config->secret);

        $rauth->username = $username;

        switch($type) {
        case 'CHAP_MD5':
        case 'MSCHAPv1':
            $classname = $type == 'MSCHAPv1' ? 'Crypt_CHAP_MSv1' : 'Crypt_CHAP_MD5';
            $crpt = new $classname;
            $crpt->password = $password;
            $rauth->challenge = $crpt->challenge;
            $rauth->chapid = $crpt->chapid;
            $rauth->response = $crpt->challengeResponse();
            $rauth->flags = 1;
            // If you must use deprecated and weak LAN-Manager-Responses use this:
            // $rauth->lmResponse = $crpt->lmChallengeResponse();
            // $rauth->flags = 0;
            break;

        case 'MSCHAPv2':
            $crpt = new Crypt_CHAP_MSv2;
            $crpt->username = $username;
            $crpt->password = $password;
            $rauth->challenge = $crpt->authChallenge;
            $rauth->peerChallenge = $crpt->peerChallenge;
            $rauth->chapid = $crpt->chapid;
            $rauth->response = $crpt->challengeResponse();
            break;

        default:
            $rauth->password = $password;
            break;
        }

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
        if (!isset ($config->nasport)) {
            $config->nasport = '1812';
        }
        if (!isset($config->radiustype)) {
            $config->radiustype = 'PAP';
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
        set_config('radiustype', $config->radiustype, 'auth/radius');

        return true;
    }

}


