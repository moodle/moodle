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
 * @package auth_classlink
 * @author Gopal Sharma <gopalsharma66@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2020 Gopal Sharma <gopalsharma66@gmail.com>
 */

namespace auth_classlink\loginflow;

/**
 * Login flow for the oauth2 authorization code grant.
 */
class authcode extends \auth_classlink\loginflow\base {
    /**
     * Returns a list of potential IdPs that this authentication plugin supports. Used to provide links on the login page.
     *
     * @param string $wantsurl The relative url fragment the user wants to get to.
     * @return array Array of idps.
     */
    public function loginpage_idp_list($wantsurl) {
        if (empty($this->config->clientid) || empty($this->config->clientsecret)) {
            return [];
        }
        if (empty($this->config->authendpoint) || empty($this->config->tokenendpoint)) {
            return [];
        }

        if (!empty($this->config->customicon)) {
            $icon = new \pix_icon('0/customicon', get_string('pluginname', 'auth_classlink'), 'auth_classlink');
        } else {
            $icon = (!empty($this->config->icon)) ? $this->config->icon : 'auth_classlink:classlink';
            $icon = explode(':', $icon);
            if (isset($icon[1])) {
                list($iconcomponent, $iconkey) = $icon;
            } else {
                $iconcomponent = 'auth_classlink';
                $iconkey = 'classlink';
            }
            $icon = new \pix_icon($iconkey, get_string('pluginname', 'auth_classlink'), $iconcomponent);
        }

        return [
            [
                'url' => new \moodle_url('/auth/classlink/'),
                'icon' => $icon,
                'name' => $this->config->opname,
            ]
        ];
    }

    /**
     * Get an classlink parameter.
     *
     * This is a modification to PARAM_ALPHANUMEXT to add a few additional characters from Base64-variants.
     *
     * @param string $name The name of the parameter.
     * @param string $fallback The fallback value.
     * @return string The parameter value, or fallback.
     */
    protected function getclasslinkparam($name, $fallback = '') {
        $val = optional_param($name, $fallback, PARAM_RAW);
        $val = trim($val);
        $valclean = preg_replace('/[^A-Za-z0-9\_\-\.\+\/\=]/i', '', $val);
        if ($valclean !== $val) {
            \auth_classlink\utils::debug('Authorization error.', 'authcode::cleanclasslinkparam', $name);
            throw new \moodle_exception('errorauthgeneral', 'auth_classlink');
        }
        return $valclean;
    }

    /**
     * Handle requests to the redirect URL.
     *
     * @return mixed Determined by loginflow.
     */
    public function handleredirect() {
        global $CFG, $SESSION, $USER;

        $state = $this->getclasslinkparam('state');
        $code = $this->getclasslinkparam('code');
        $promptlogin = (bool)optional_param('promptlogin', 0, PARAM_BOOL);
        $promptaconsent = (bool)optional_param('promptaconsent', 0, PARAM_BOOL);
        $justauth = (bool)optional_param('justauth', 0, PARAM_BOOL);
        if (!empty($state)) {
            $requestparams = [
                'state' => $state,
                'code' => $code,
                'error_description' => optional_param('error_description', '', PARAM_TEXT),
            ];
            // Response from OP.
            $this->handleauthresponse($requestparams);
        } else {
            if (isloggedin() && empty($justauth) && empty($promptaconsent)) {
                if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
                    $urltogo = $SESSION->wantsurl;
                    unset($SESSION->wantsurl);
                } else {
                    set_user_preference('auth_classlink_edit', 0);
                    $userid = $USER->id;
                    $urltogo = new \moodle_url("/user/profile.php?id=$userid");
                }
                redirect($urltogo);
                die();
            }
            // Initial login request.
            $stateparams = ['forceflow' => 'authcode'];
            $extraparams = [];
            if ($promptaconsent === true) {
                $extraparams = ['prompt' => 'admin_consent'];
            }
            if ($justauth === true) {
                $stateparams['justauth'] = true;
            }
            $this->initiateauthrequest($promptlogin, $stateparams, $extraparams);
        }
    }

    /**
     * This is the primary method that is used by the authenticate_user_login() function in moodlelib.php.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password = null) {
        global $CFG, $DB;

        // Check user exists.
        $userfilters = ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'auth' => 'classlink'];
        $userexists = $DB->record_exists('user', $userfilters);

        // Check token exists.
        $tokenrec = $DB->get_record('auth_classlink_token', ['username' => $username]);
        $code = optional_param('code', null, PARAM_RAW);
        $tokenvalid = (!empty($tokenrec) && !empty($code) && $tokenrec->authcode === $code) ? true : false;
        return ($userexists === true && $tokenvalid === true) ? true : false;
    }

    /**
     * Initiate an authorization request to the configured OP.
     *
     * @param bool $promptlogin Whether to prompt for login or use existing session.
     * @param array $stateparams Parameters to store as state.
     * @param array $extraparams Additional parameters to send with the classlink request.
     */
    public function initiateauthrequest($promptlogin = false, array $stateparams = array(), array $extraparams = array()) {
        $client = $this->get_classlinkclient();
        $client->authrequest($promptlogin, $stateparams, $extraparams);
    }

    /**
     * Handle an authorization request response received from the configured OP.
     *
     * @param array $authparams Received parameters.
     */
    protected function handleauthresponse(array $authparams) {
        global $DB, $CFG, $STATEADDITIONALDATA, $USER;

        if (!empty($authparams['error_description'])) {
            // AADB2C90091 user cancel error code.
            if ( strstr( $authparams['error_description'], 'AADB2C90091' ) ) {
                redirect(new \moodle_url('/'));
            } else if (strstr( $authparams['error_description'], 'AADB2C90118' )) {
                // AADB2C90118: The user has forgotten their password.
                $lang = current_language();
                $url = get_config('auth_classlink', 'resetpassendpoint').
                        "&client_id=". get_config('auth_classlink', 'clientid').
                        "&nonce=defaultNonce
                        &redirect_uri=". $CFG->wwwroot."/auth/classlink/&
                        scope=openid
                        &response_type=code
                        &prompt=login&ui_locales=$lang";
                redirect($url);
            } else {
                \auth_classlink\utils::debug('Authorization error.', 'authcode::handleauthresponse', $authparams);
                throw new \moodle_exception('errorauthgeneral', 'auth_classlink');
            }
        }

        if (!isset($authparams['code'])) {
            \auth_classlink\utils::debug('No auth code received.', 'authcode::handleauthresponse', $authparams);
            throw new \moodle_exception('errorauthnoauthcode', 'auth_classlink');
        }

        if (!isset($authparams['state'])) {
            \auth_classlink\utils::debug('No state received.', 'authcode::handleauthresponse', $authparams);
            throw new \moodle_exception('errorauthunknownstate', 'auth_classlink');
        }

        // Validate and expire state.
        $staterec = $DB->get_record('auth_classlink_state', ['state' => $authparams['state']]);
        if (empty($staterec)) {
            throw new \moodle_exception('errorauthunknownstate', 'auth_classlink');
        }
        $orignonce = $staterec->nonce;
        $additionaldata = [];
        if (!empty($staterec->additionaldata)) {
            $additionaldata = @unserialize($staterec->additionaldata);
            if (!is_array($additionaldata)) {
                $additionaldata = [];
            }
        }
        $STATEADDITIONALDATA = $additionaldata;
        $DB->delete_records('auth_classlink_state', ['id' => $staterec->id]);

        // Get token from auth code.
        $client = $this->get_classlinkclient();
        $tokenparams = $client->tokenrequest($authparams['code']);
        if (!isset($tokenparams['id_token'])) {
            throw new \moodle_exception('errorauthnoidtoken', 'auth_classlink');
        }

        // Decode and verify idtoken.
        list($classlinkuniqid, $idtoken) = $this->process_idtoken($tokenparams['id_token'], $orignonce);

        // Check restrictions.
        $passed = $this->checkrestrictions($idtoken);
        if ($passed !== true && empty($additionaldata['ignorerestrictions'])) {
            $errstr = 'User prevented from logging in due to restrictions.';
            \auth_classlink\utils::debug($errstr, 'handleauthresponse', $idtoken);
            throw new \moodle_exception('errorrestricted', 'auth_classlink');
        }

        // This is for setting the system API user.
        if (isset($additionaldata['justauth']) && $additionaldata['justauth'] === true) {
            $eventdata = [
                'other' => [
                    'authparams' => $authparams,
                    'tokenparams' => $tokenparams,
                    'statedata' => $additionaldata,
                ]
            ];
            $event = \auth_classlink\event\user_authed::create($eventdata);
            $event->trigger();
            return true;
        }

        // Check if classlink user is already migrated.
        $tokenrec = $DB->get_record('auth_classlink_token', ['classlinkuniqid' => $classlinkuniqid]);
        if (isloggedin() === true && (empty($tokenrec) || (isset($USER->auth) && $USER->auth !== 'classlink'))) {

            // If user is already logged in and trying to link Office 365 account or use it for classlink.
            // Check if that Office 365 account already exists in moodle.
            $userrec = $DB->count_records_sql('SELECT COUNT(*)
                                                 FROM {user}
                                                WHERE username = ?
                                                      AND id != ?',
                    [$idtoken->claim('upn'), $USER->id]);

            if (!empty($userrec)) {
                if (empty($additionaldata['redirect'])) {
                    $redirect = '/auth/classlink/ucp.php?classlinkaccountconnected=true';
                } else if ($additionaldata['redirect'] == '/local/classlink/ucp.php') {
                    $redirect = $additionaldata['redirect'].'?action=connection&classlinkaccountconnected=true';
                } else {
                    throw new \moodle_exception('errorinvalidredirect_message', 'auth_classlink');
                }
                redirect(new \moodle_url($redirect));
            }

            // If the user is already logged in we can treat this as a "migration" - a user switching to classlink.
            $connectiononly = false;
            if (isset($additionaldata['connectiononly']) && $additionaldata['connectiononly'] === true) {
                $connectiononly = true;
            }
            $this->handlemigration($classlinkuniqid, $authparams, $tokenparams, $idtoken, $connectiononly);
            $redirect = (!empty($additionaldata['redirect'])) ? $additionaldata['redirect'] : '/auth/classlink/ucp.php';
            redirect(new \moodle_url($redirect));
        } else {
            // Otherwise it's a user logging in normally with classlink.
            $this->handlelogin($classlinkuniqid, $authparams, $tokenparams, $idtoken);
            redirect(core_login_get_return_url());
        }
    }

    /**
     * Handle a user migration event.
     *
     * @param string $classlinkuniqid A unique identifier for the user.
     * @param array $authparams Paramteres receieved from the auth request.
     * @param array $tokenparams Parameters received from the token request.
     * @param \auth_classlink\jwt $idtoken A JWT object representing the received id_token.
     * @param bool $connectiononly Whether to just connect the user (true), or to connect and change login method (false).
     */
    protected function handlemigration($classlinkuniqid, $authparams, $tokenparams, $idtoken, $connectiononly = false) {
        global $USER, $DB, $CFG;

        // Check if classlink user is already connected to a Moodle user.
        $tokenrec = $DB->get_record('auth_classlink_token', ['classlinkuniqid' => $classlinkuniqid]);
        if (!empty($tokenrec)) {
            $existinguserparams = ['username' => $tokenrec->username, 'mnethostid' => $CFG->mnet_localhost_id];
            $existinguser = $DB->get_record('user', $existinguserparams);
            if (empty($existinguser)) {
                $DB->delete_records('auth_classlink_token', ['id' => $tokenrec->id]);
            } else {
                if ($USER->username === $tokenrec->username) {
                    // Already connected to current user.
                    if ($connectiononly !== true && $USER->auth !== 'classlink') {
                        // Update auth plugin.
                        $DB->update_record('user', (object)['id' => $USER->id, 'auth' => 'classlink']);
                        $USER = $DB->get_record('user', ['id' => $USER->id]);
                        $USER->auth = 'classlink';
                    }
                    $this->updatetoken($tokenrec->id, $authparams, $tokenparams);
                    return true;
                } else {
                    // Classlink user connected to user that is not us. Can't continue.
                    throw new \moodle_exception('errorauthuserconnectedtodifferent', 'auth_classlink');
                }
            }
        }

        // Check if Moodle user is already connected to an classlink user.
        $tokenrec = $DB->get_record('auth_classlink_token', ['userid' => $USER->id]);
        if (!empty($tokenrec)) {
            if ($tokenrec->classlinkuniqid === $classlinkuniqid) {
                // Already connected to current user.
                if ($connectiononly !== true && $USER->auth !== 'classlink') {
                    // Update auth plugin.
                    $DB->update_record('user', (object)['id' => $USER->id, 'auth' => 'classlink']);
                    $USER = $DB->get_record('user', ['id' => $USER->id]);
                    $USER->auth = 'classlink';
                }
                $this->updatetoken($tokenrec->id, $authparams, $tokenparams);
                return true;
            } else {
                throw new \moodle_exception('errorauthuseralreadyconnected', 'auth_classlink');
            }
        }

        // Create token data.
        $tokenrec = $this->createtoken($classlinkuniqid, $USER->username, $authparams, $tokenparams, $idtoken, $USER->id);

        $eventdata = [
            'objectid' => $USER->id,
            'userid' => $USER->id,
            'other' => [
                'username' => $USER->username,
                'userid' => $USER->id,
                'classlinkuniqid' => $classlinkuniqid,
            ],
        ];
        $event = \auth_classlink\event\user_connected::create($eventdata);
        $event->trigger();

        // Switch auth method, if requested.
        if ($connectiononly !== true) {
            if ($USER->auth !== 'classlink') {
                $DB->delete_records('auth_classlink_prevlogin', ['userid' => $USER->id]);
                $userrec = $DB->get_record('user', ['id' => $USER->id]);
                if (!empty($userrec)) {
                    $prevloginrec = [
                        'userid' => $userrec->id,
                        'method' => $userrec->auth,
                        'password' => $userrec->password,
                    ];
                    $DB->insert_record('auth_classlink_prevlogin', $prevloginrec);
                }
            }
            $DB->update_record('user', (object)['id' => $USER->id, 'auth' => 'classlink']);
            $USER = $DB->get_record('user', ['id' => $USER->id]);
            $USER->auth = 'classlink';
        }

        return true;
    }

    /**
     * Determines whether the given Azure AD UPN is already matched to a Moodle user (and has not been completed).
     *
     * @return false|stdClass Either the matched Moodle user record, or false if not matched.
     */
    protected function check_for_matched($aadupn) {
        global $DB;
        $dbman = $DB->get_manager();
        if ($dbman->table_exists('local_o365_connections')) {
            $match = $DB->get_record('local_o365_connections', ['aadupn' => $aadupn]);
            if (!empty($match) && \local_o365\utils::is_o365_connected($match->muserid) !== true) {
                return $DB->get_record('user', ['id' => $match->muserid]);
            }
        }
        return false;
    }

    /**
     * Check for an existing user object.
     * @param string $classlinkuniqid The user object ID to look up.
     * @param string $username The original username.
     * @return string If there is an existing user object, return the username associated with it.
     *                If there is no existing user object, return the original username.
     */
    protected function check_objects($classlinkuniqid, $username) {
        global $DB;
        $user = null;
        $o365installed = $DB->get_record('config_plugins', ['plugin' => 'local_o365', 'name' => 'version']);
        if (!empty($o365installed)) {
            $sql = 'SELECT u.username
                      FROM {local_o365_objects} obj
                      JOIN {user} u ON u.id = obj.moodleid
                     WHERE obj.objectid = ? and obj.type = ?';
            $params = [$classlinkuniqid, 'user'];
            $user = $DB->get_record_sql($sql, $params);
        }
        return (!empty($user)) ? $user->username : $username;
    }

    /**
     * Handle a login event.
     *
     * @param string $classlinkuniqid A unique identifier for the user.
     * @param array $authparams Parameters receieved from the auth request.
     * @param array $tokenparams Parameters received from the token request.
     * @param \auth_classlink\jwt $idtoken A JWT object representing the received id_token.
     */
    protected function handlelogin($classlinkuniqid, $authparams, $tokenparams, $idtoken) {
        global $DB, $CFG;

        $user_use_login = $idtoken->claim('login_hint');

        $user_uselogin = trim(\core_text::strtolower($user_use_login));

        $classlinkuniqid = $user_uselogin;

        $tokenrec = $DB->get_record('auth_classlink_token', ['classlinkuniqid' => $classlinkuniqid]);
        if (!empty($tokenrec)) { 
            // Already connected user.

            if (empty($tokenrec->userid)) {
                // ERROR.
                echo 'First ERROR1';die();
            }
            $user = $DB->get_record('user', ['id' => $tokenrec->userid]);
            if (empty($user)) {
                // ERROR.
                echo 'First ERROR2';die();
            }
            $username = $user->username;
            $this->updatetoken($tokenrec->id, $authparams, $tokenparams);
            $user = authenticate_user_login($username, null, true);
            complete_user_login($user);
            return true;
        } else { 
            // No existing token, user not connected.
            //
            // Possibilities:
            // - Matched user.
            // - New user (maybe create).

            // Generate a Moodle username.
            // Use 'upn' if available for username (Azure-specific), or fall back to lower-case classlinkuniqid.

            $user = $DB->get_record('user', ['username' => $user_uselogin]);          
            if (empty($user)) {
                // ERROR.
                echo 'USER NOT FOUND IN MOODLE - ERROR2';die();
            }

            $username = $user->username;
            if (empty($username)) {
                $classlinkuniqid = $username;
                $username = $classlinkuniqid; 
            }

            // See if we have an object listing.
            $username = $this->check_objects($classlinkuniqid, $username);
            $matchedwith = $this->check_for_matched($username);
            if (!empty($matchedwith)) {
                $matchedwith->aadupn = $username;
                throw new \moodle_exception('errorusermatched', 'local_o365', null, $matchedwith);
            }
            $username = trim(\core_text::strtolower($username));
            $tokenrec = $this->createtoken($classlinkuniqid, $username, $authparams, $tokenparams, $idtoken);
           
            $existinguserparams = ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id];
            
            if ($DB->record_exists('user', $existinguserparams) !== true) { 
                // User does not exist. Create user if site allows, otherwise fail.
                if (empty($CFG->authpreventaccountcreation)) { 

                $tokenrec = $DB->get_record('auth_classlink_token', ['classlinkuniqid' => $classlinkuniqid]);

               
                if (!empty($tokenrec)) { 
                            // Already connected user.

                            if (empty($tokenrec->userid)) {
                                // ERROR.
                                echo 'ELSE New ERROR1';die();
                            }
                            $user = $DB->get_record('user', ['id' => $tokenrec->userid]);
                            if (empty($user)) {
                                // ERROR.
                                echo 'ELSE New ERROR2';die();
                            }
                            $username = $user->username;
                            $this->updatetoken($tokenrec->id, $authparams, $tokenparams);
                           
                            $user = authenticate_user_login($username, null, true);
                            
                            complete_user_login($user);
                            return true;
                        }
                    
                } else { 
                    // Trigger login failed event.
                    $failurereason = AUTH_LOGIN_NOUSER;
                    $eventdata = ['other' => ['username' => $username, 'reason' => $failurereason]];
                    $event = \core\event\user_login_failed::create($eventdata);
                    $event->trigger();
                    throw new \moodle_exception('errorauthloginfailednouser', 'auth_classlink', null, null, '1');
                }
            }
            $user = authenticate_user_login($username, null, true);

            if (!empty($user)) { 
                complete_user_login($user);
                return true;
            } else {
                if (!empty($tokenrec)) {
                    throw new \moodle_exception('errorlogintoconnectedaccount', 'auth_classlink', null, null, '2');
                } else {
                    throw new \moodle_exception('errorauthloginfailednouser', 'auth_classlink', null, null, '2');
                }
            }

            return true;
        }
    }
}
