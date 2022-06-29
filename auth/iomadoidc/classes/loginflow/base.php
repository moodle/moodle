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
 * Definition of base login flow class.
 *
 * @package auth_iomadoidc
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   auth_oidc by James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadoidc\loginflow;

use auth_iomadoidc\jwt;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/iomadoidc/lib.php');

/**
 * A base loginflow class.
 */
class base {
    /** @var object Plugin config. */
    public $config;

    /** @var \auth_iomadoidc\httpclientinterface An HTTP client to use. */
    protected $httpclient;

    /**
     * Constructor.
     */
    public function __construct() {
        global $SESSION;

        if (empty($SESSION->currenteditingcompany)) {
            $SESSION->currenteditingcompany = 0;
            return;
        }
        $companyid = $SESSION->currenteditingcompany;

        $default = [
            'opname' => get_string('pluginname', 'auth_iomadoidc')
        ];
        $siteconfig = (array)get_config('auth_iomadoidc');
        $configitems = array('opname',
                             'clientid',
                             'clientsecret',
                             'authendpoint',
                             'tokenendpoint',
                             'iomadoidcresource',
                             'iomadoidcscope',
                             'forceredirect',
                             'autoappend',
                             'domainhint',
                             'loginflow',
                             'userrestrictions',
                             'debugmode',
                             'icon');

        $storedconfig = array();
        foreach ( $configitems as $configitem) {
            if (!empty($siteconfig[$configitem . "_$companyid"])) {
                $storedconfig[$configitem] = $siteconfig[$configitem . "_$companyid"];
            } else {
                $storedconfig[$configitem] = $siteconfig[$configitem];
            }
        }


        foreach ($storedconfig as $configname => $configvalue) {
            if (strpos($configname, 'field_updatelocal_') === 0 && $configvalue == 'always') {
                $storedconfig[$configname] = 'onlogin';
            }
        }

        $this->config = (object)array_merge($default, $storedconfig);
    }

    /**
     * Returns a list of potential IdPs that this authentication plugin supports. Used to provide links on the login page.
     *
     * @param string $wantsurl The relative url fragment the user wants to get to.
     * @return array Array of idps.
     */
    public function loginpage_idp_list($wantsurl) {
        return [];
    }

    /**
     * This is the primary method that is used by the authenticate_user_login() function in moodlelib.php.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password = null) {
        return false;
    }

    /**
     * Provides a hook into the login page.
     *
     * @param object &$frm Form object.
     * @param object &$user User object.
     * @return bool
     */
    public function loginpage_hook(&$frm, &$user) {
        return true;
    }

    /**
     * Read user information from external database and returns it as array().
     *
     * @param string $username username
     * @return mixed array with no magic quotes or false on error
     */
    public function get_userinfo($username) {
        global $DB;

        $tokenrec = $DB->get_record('auth_iomadoidc_token', ['username' => $username]);
        if (empty($tokenrec)) {
            return false;
        }

        if ($DB->record_exists('user', ['username' => $username])) {
            $eventtype = 'login';
        } else {
            $eventtype = 'create';
        }

        $fieldmappingfromtoken = true;

        if (auth_iomadoidc_is_local_365_installed()) {
            // Check if multi tenants is enabled. User from additional tenants can only sync fields from token.
            $userfromadditionaltenant = false;
            $hostingtenantid = get_config('local_o365', 'aadtenantid');
            $token = jwt::instance_from_encoded($tokenrec->token);
            if ($token->claim('tid') != $hostingtenantid) {
                $userfromadditionaltenant = true;
            }

            if (!$userfromadditionaltenant) {
                if (\local_o365\feature\usersync\main::fieldmap_require_graph_api_call($eventtype)) {
                    // If local_o365 is installed, and field mapping uses fields not covered by token,
                    // then call Graph API function to get user details.
                    $apiclient = \local_o365\utils::get_api($tokenrec->userid);
                    if ($apiclient) {
                        $fieldmappingfromtoken = false;
                        $userdata = $apiclient->get_user($tokenrec->iomadoidcuniqid, true);
                    }
                } else {
                    // If local_o365 is installed, but all field mapping fields are in token, then use token.
                    $fieldmappingfromtoken = false;
                    $idtoken = jwt::instance_from_encoded($tokenrec->idtoken);

                    $oid = $idtoken->claim('oid');
                    if (!empty($oid)) {
                        $userdata['objectId'] = $oid;
                    }

                    $upn = $idtoken->claim('upn');
                    if (!empty($upn)) {
                        $userdata['userPrincipalName'] = $upn;
                    } else if (isset($tokenrec->iomadoidcusername) && $tokenrec->iomadoidcusername) {
                        $userdata['userPrincipalName'] = $tokenrec->iomadoidcusername;
                    }

                    $firstname = $idtoken->claim('given_name');
                    if (!empty($firstname)) {
                        $userdata['givenName'] = $firstname;
                    }

                    $lastname = $idtoken->claim('family_name');
                    if (!empty($lastname)) {
                        $userdata['surname'] = $lastname;
                    }

                    $email = $idtoken->claim('email');
                    if (!empty($email)) {
                        $userdata['mail'] = $email;
                    } else {
                        if (!empty($upn)) {
                            $aademailvalidateresult = filter_var($upn, FILTER_VALIDATE_EMAIL);
                            if (!empty($aademailvalidateresult)) {
                                $userdata['mail'] = $aademailvalidateresult;
                            }
                        }
                    }
                }

                // Call the function in local_o365 to map fields.
                $updateduser = \local_o365\feature\usersync\main::apply_configured_fieldmap($userdata, new stdClass(), 'login');
                $userinfo = (array)$updateduser;
            }
        }

        if ($fieldmappingfromtoken) {
            // If local_o365 is not installed, use information from user token.
            $userdata = [];

            $idtoken = jwt::instance_from_encoded($tokenrec->idtoken);

            $objectid = $idtoken->claim('oid');
            if (!$objectid) {
                $userdata['objectId'] = $objectid;
            }

            $upn = $idtoken->claim('upn');
            if (!empty($upn)) {
                $userdata['userPrincipalName'] = $upn;
            }

            $firstname = $idtoken->claim('given_name');
            if (!empty($firstname)) {
                $userdata['givenName'] = $firstname;
            }

            $lastname = $idtoken->claim('family_name');
            if (!empty($lastname)) {
                $userdata['surname'] = $lastname;
            }

            $email = $idtoken->claim('email');
            if (!empty($email)) {
                $userdata['mail'] = $email;
            } else {
                if (!empty($upn)) {
                    $aademailvalidateresult = filter_var($upn, FILTER_VALIDATE_EMAIL);
                    if (!empty($aademailvalidateresult)) {
                        $userdata['mail'] = $aademailvalidateresult;
                    }
                }
            }

            $updateduser = static::apply_configured_fieldmap_from_token($userdata, 'login');
            $userinfo = (array)$updateduser;
        }

        return $userinfo;
    }

    /**
     * Apply configured field mapping from token information to an empty user object.
     *
     * @param array $userdata
     * @param string $eventtype
     * @return stdClass
     */
    public static function apply_configured_fieldmap_from_token(array $userdata, string $eventtype) {
        $user = new stdClass();

        $fieldmappings = auth_iomadoidc_get_field_mappings();

        foreach ($fieldmappings as $localfield => $fieldmapping) {
            $remotefield = $fieldmapping['field_map'];
            $behavior = $fieldmapping['update_local'];

            if ($behavior !== 'on' . $eventtype && $behavior !== 'always') {
                // Field mapping doesn't apply to this event type.
                continue;
            }

            if (isset($userdata[$remotefield])) {
                $user->$localfield = $userdata[$remotefield];
            }
        }

        return $user;
    }

    /**
     * Set an HTTP client to use.
     *
     * @param \auth_iomadoidc\httpclientinterface $httpclient
     */
    public function set_httpclient(\auth_iomadoidc\httpclientinterface $httpclient) {
        $this->httpclient = $httpclient;
    }

    /**
     * Handle IOMADoIDC disconnection from Moodle account.
     *
     * @param bool $justremovetokens If true, just remove the stored IOMADoIDC tokens for the user, otherwise revert login methods.
     * @param bool $donotremovetokens If true, do not remove tokens when disconnecting. This migrates from a login account to a
     *                                "linked" account.
     * @param \moodle_url|null $redirect Where to redirect if successful.
     * @param \moodle_url|null $selfurl The page this is accessed from. Used for some redirects.
     * @param  $userid
     */
    public function disconnect($justremovetokens = false, $donotremovetokens = false, \moodle_url $redirect = null,
                               \moodle_url $selfurl = null, $userid = null) {
        global $USER, $DB, $CFG;
        if ($redirect === null) {
            $redirect = new \moodle_url('/auth/iomadoidc/ucp.php');
        }
        if ($selfurl === null) {
            $selfurl = new \moodle_url('/auth/iomadoidc/ucp.php', ['action' => 'disconnectlogin']);
        }

        // Get the record of the user involved. Current user if no ID received.
        if (empty($userid)) {
            $userid = $USER->id;
        }
        $userrec = $DB->get_record('user', ['id' => $userid]);
        if (empty($userrec)) {
            redirect($redirect);
            die();
        }

        if ($justremovetokens === true) {
            // Delete token data.
            $DB->delete_records('auth_iomadoidc_token', ['userid' => $userrec->id]);
            $eventdata = ['objectid' => $userrec->id, 'userid' => $userrec->id];
            $event = \auth_iomadoidc\event\user_disconnected::create($eventdata);
            $event->trigger();
            redirect($redirect);
        } else {
            global $OUTPUT, $PAGE;
            require_once($CFG->dirroot.'/user/lib.php');
            $PAGE->set_url($selfurl->out());
            $PAGE->set_context(\context_system::instance());
            $PAGE->set_pagelayout('standard');
            $USER->editing = false;

            $ucptitle = get_string('ucp_disconnect_title', 'auth_iomadoidc', $this->config->opname);
            $PAGE->navbar->add($ucptitle, $PAGE->url);
            $PAGE->set_title($ucptitle);

            // Check if we have recorded the user's previous login method.
            $prevmethodrec = $DB->get_record('auth_iomadoidc_prevlogin', ['userid' => $userrec->id]);
            $prevauthmethod = null;
            if (!empty($prevmethodrec) && is_enabled_auth($prevmethodrec->method) === true) {
                $prevauthmethod = $prevmethodrec->method;
            }
            // Manual is always available, we don't need it twice.
            if ($prevauthmethod === 'manual') {
                $prevauthmethod = null;
            }

            // We need either the user's previous method or the manual login plugin to be enabled for disconnection.
            if (empty($prevauthmethod) && is_enabled_auth('manual') !== true) {
                throw new \moodle_exception('errornodisconnectionauthmethod', 'auth_iomadoidc');
            }

            // Check to see if the user has a username created by IOMADoIDC, or a self-created username.
            // IOMADoIDC-created usernames are usually very verbose, so we'll allow them to choose a sensible one.
            // Otherwise, keep their existing username.
            $iomadoidctoken = $DB->get_record('auth_iomadoidc_token', ['userid' => $userrec->id]);
            $ccun = (isset($iomadoidctoken->iomadoidcuniqid) && strtolower($iomadoidctoken->iomadoidcuniqid) === $userrec->username) ? true : false;
            $customdata = [
                'canchooseusername' => $ccun,
                'prevmethod' => $prevauthmethod,
                'donotremovetokens' => $donotremovetokens,
                'redirect' => $redirect,
                'userid' => $userrec->id,
            ];

            $mform = new \auth_iomadoidc\form\disconnect($selfurl, $customdata);

            if ($mform->is_cancelled()) {
                redirect($redirect);
            } else if ($fromform = $mform->get_data()) {
                if (empty($fromform->newmethod) || ($fromform->newmethod !== $prevauthmethod &&
                        $fromform->newmethod !== 'manual')) {
                    throw new \moodle_exception('errorauthdisconnectinvalidmethod', 'auth_iomadoidc');
                }

                $updateduser = new stdClass;

                if ($fromform->newmethod === 'manual') {
                    if (empty($fromform->password)) {
                        throw new \moodle_exception('errorauthdisconnectemptypassword', 'auth_iomadoidc');
                    }
                    if ($customdata['canchooseusername'] === true) {
                        if (empty($fromform->username)) {
                            throw new \moodle_exception('errorauthdisconnectemptyusername', 'auth_iomadoidc');
                        }

                        if (strtolower($fromform->username) !== $userrec->username) {
                            $newusername = strtolower($fromform->username);
                            $usercheck = ['username' => $newusername, 'mnethostid' => $CFG->mnet_localhost_id];
                            if ($DB->record_exists('user', $usercheck) === false) {
                                $updateduser->username = $newusername;
                            } else {
                                throw new \moodle_exception('errorauthdisconnectusernameexists', 'auth_iomadoidc');
                            }
                        }
                    }
                    $updateduser->auth = 'manual';
                    $updateduser->password = $fromform->password;
                } else if ($fromform->newmethod === $prevauthmethod) {
                    $updateduser->auth = $prevauthmethod;
                    // We can't use user_update_user as it will rehash the value.
                    if (!empty($prevmethodrec->password)) {
                        $manualuserupdate = new stdClass;
                        $manualuserupdate->id = $userrec->id;
                        $manualuserupdate->password = $prevmethodrec->password;
                        $DB->update_record('user', $manualuserupdate);
                    }
                }

                // Update user.
                $updateduser->id = $userrec->id;
                try {
                    user_update_user($updateduser);
                } catch (\Exception $e) {
                    throw new \moodle_exception($e->errorcode, '', $selfurl);
                }

                // Delete token data.
                if (empty($fromform->donotremovetokens)) {
                    $DB->delete_records('auth_iomadoidc_token', ['userid' => $userrec->id]);

                    $eventdata = ['objectid' => $userrec->id, 'userid' => $userrec->id];
                    $event = \auth_iomadoidc\event\user_disconnected::create($eventdata);
                    $event->trigger();
                }

                // If we're dealing with the current user, refresh the object.
                if ($userrec->id == $USER->id) {
                    $USER = $DB->get_record('user', ['id' => $USER->id]);
                }

                if (!empty($fromform->redirect)) {
                    redirect($fromform->redirect);
                } else {
                    redirect($redirect);
                }
            }

            echo $OUTPUT->header();
            $mform->display();
            echo $OUTPUT->footer();
        }
    }

    /**
     * Handle requests to the redirect URL.
     *
     * @return mixed Determined by loginflow.
     */
    public function handleredirect() {

    }

    /**
     * Construct the OpenID Connect client.
     *
     * @return \auth_iomadoidc\iomadoidcclient The constructed client.
     */
    protected function get_iomadoidcclient() {
        global $CFG;
        if (empty($this->httpclient) || !($this->httpclient instanceof \auth_iomadoidc\httpclientinterface)) {
            $this->httpclient = new \auth_iomadoidc\httpclient();
        }
        if (empty($this->config->clientid) || empty($this->config->clientsecret)) {
            throw new \moodle_exception('errorauthnocreds', 'auth_iomadoidc');
        }
        if (empty($this->config->authendpoint) || empty($this->config->tokenendpoint)) {
            throw new \moodle_exception('errorauthnoendpoints', 'auth_iomadoidc');
        }

        $clientid = (isset($this->config->clientid)) ? $this->config->clientid : null;
        $clientsecret = (isset($this->config->clientsecret)) ? $this->config->clientsecret : null;
        $redirecturi = (!empty($CFG->loginhttps)) ? str_replace('http://', 'https://', $CFG->wwwroot) : $CFG->wwwroot;
        $redirecturi .= '/auth/iomadoidc/';
        $tokenresource = (isset($this->config->iomadoidcresource)) ? $this->config->iomadoidcresource : null;
        $scope = (isset($this->config->iomadoidcscope)) ? $this->config->iomadoidcscope : null;

        $client = new \auth_iomadoidc\iomadoidcclient($this->httpclient);
        $client->setcreds($clientid, $clientsecret, $redirecturi, $tokenresource, $scope);

        $client->setendpoints(['auth' => $this->config->authendpoint, 'token' => $this->config->tokenendpoint]);

        return $client;
    }

    /**
     * Process an idtoken, extract uniqid and construct jwt object.
     *
     * @param string $idtoken Encoded id token.
     * @param string $orignonce Original nonce to validate received nonce against.
     * @return array List of iomadoidcuniqid and constructed idtoken jwt.
     */
    protected function process_idtoken($idtoken, $orignonce = '') {
        // Decode and verify idtoken.
        $idtoken = jwt::instance_from_encoded($idtoken);
        $sub = $idtoken->claim('sub');
        if (empty($sub)) {
            \auth_iomadoidc\utils::debug('Invalid idtoken', 'base::process_idtoken', $idtoken);
            throw new \moodle_exception('errorauthinvalididtoken', 'auth_iomadoidc');
        }
        $receivednonce = $idtoken->claim('nonce');
        if (!empty($orignonce) && (empty($receivednonce) || $receivednonce !== $orignonce)) {
            \auth_iomadoidc\utils::debug('Invalid nonce', 'base::process_idtoken', $idtoken);
            throw new \moodle_exception('errorauthinvalididtoken', 'auth_iomadoidc');
        }

        // Use 'oid' if available (Azure-specific), or fall back to standard "sub" claim.
        $iomadoidcuniqid = $idtoken->claim('oid');
        if (empty($iomadoidcuniqid)) {
            $iomadoidcuniqid = $idtoken->claim('sub');
        }
        return [$iomadoidcuniqid, $idtoken];
    }

    /**
     * Check user restrictions, if present.
     *
     * This check will return false if there are restrictions in place that the user did not meet, otherwise it will return
     * true. If there are no restrictions in place, this will return true.
     *
     * @param jwt $idtoken The ID token of the user who is trying to log in.
     * @return bool Whether the restriction check passed.
     */
    protected function checkrestrictions(jwt $idtoken) {
        $restrictions = (isset($this->config->userrestrictions)) ? trim($this->config->userrestrictions) : '';
        $hasrestrictions = false;
        $userpassed = false;
        if ($restrictions !== '') {
            $restrictions = explode("\n", $restrictions);
            // Match "UPN" (Azure-specific) if available, otherwise match iomadoidc-standard "sub".
            $tomatch = $idtoken->claim('upn');
            if (empty($tomatch)) {
                $tomatch = $idtoken->claim('sub');
            }
            foreach ($restrictions as $restriction) {
                $restriction = trim($restriction);
                if ($restriction !== '') {
                    $hasrestrictions = true;
                    ob_start();
                    try {
                        $pattern = '/'.$restriction.'/';
                        if (isset($this->config->userrestrictionscasesensitive) && !$this->config->userrestrictionscasesensitive) {
                            $pattern .= 'i';
                        }
                        $count = @preg_match($pattern, $tomatch, $matches);
                        if (!empty($count)) {
                            $userpassed = true;
                            break;
                        }
                    } catch (\Exception $e) {
                        $debugdata = [
                            'exception' => $e,
                            'restriction' => $restriction,
                            'tomatch' => $tomatch,
                        ];
                        \auth_iomadoidc\utils::debug('Error running user restrictions.', 'handleauthresponse', $debugdata);
                    }
                    $contents = ob_get_contents();
                    ob_end_clean();
                    if (!empty($contents)) {
                        $debugdata = [
                            'contents' => $contents,
                            'restriction' => $restriction,
                            'tomatch' => $tomatch,
                        ];
                        \auth_iomadoidc\utils::debug('Output while running user restrictions.', 'handleauthresponse', $debugdata);
                    }
                }
            }
        }
        return ($hasrestrictions === true && $userpassed !== true) ? false : true;
    }

    /**
     * Create a token for a user, thus linking a Moodle user to an OpenID Connect user.
     *
     * @param string $iomadoidcuniqid A unique identifier for the user.
     * @param array $username The username of the Moodle user to link to.
     * @param array $authparams Parameters receieved from the auth request.
     * @param array $tokenparams Parameters received from the token request.
     * @param jwt $idtoken A JWT object representing the received id_token.
     * @param int $userid
     * @param null|string $originalupn
     * @return stdClass The created token database record.
     */
    protected function createtoken($iomadoidcuniqid, $username, $authparams, $tokenparams, jwt $idtoken, $userid = 0,
        $originalupn = null) {
        global $DB;

        if (!is_null($originalupn)) {
            $iomadoidcusername = $originalupn;
        } else {
            // Determine remote username. Use 'upn' if available (Azure-specific), or fall back to standard 'sub'.
            $iomadoidcusername = $idtoken->claim('upn');
            if (empty($iomadoidcusername)) {
                $iomadoidcusername = $idtoken->claim('sub');
            }
        }

        // We should not fail here (idtoken was verified earlier to at least contain 'sub', but just in case...).
        if (empty($iomadoidcusername)) {
            throw new \moodle_exception('errorauthinvalididtoken', 'auth_iomadoidc');
        }

        // Cleanup old invalid token with the same iomadoidcusername.
        $DB->delete_records('auth_iomadoidc_token', ['iomadoidcusername' => $iomadoidcusername]);

        // Handle "The existing token for this user does not contain a valid user ID" error.
        if ($userid == 0) {
            $userrec = $DB->get_record('user', ['username' => $username]);
            if ($userrec) {
                $userid = $userrec->id;
            }
        }

        $tokenrec = new stdClass;
        $tokenrec->iomadoidcuniqid = $iomadoidcuniqid;
        $tokenrec->username = $username;
        $tokenrec->userid = $userid;
        $tokenrec->iomadoidcusername = $iomadoidcusername;
        $tokenrec->scope = !empty($tokenparams['scope']) ? $tokenparams['scope'] : 'openid profile email';
        $tokenrec->tokenresource = !empty($tokenparams['resource']) ? $tokenparams['resource'] : $this->config->iomadoidcresource;
        $tokenrec->scope = !empty($tokenparams['scope']) ? $tokenparams['scope'] : $this->config->iomadoidcscope;
        $tokenrec->authcode = $authparams['code'];
        $tokenrec->token = $tokenparams['access_token'];
        if (!empty($tokenparams['expires_on'])) {
            $tokenrec->expiry = $tokenparams['expires_on'];
        } else if (isset($tokenparams['expires_in'])) {
            $tokenrec->expiry = time() + $tokenparams['expires_in'];
        } else {
            $tokenrec->expiry = time() + DAYSECS;
        }
        $tokenrec->refreshtoken = !empty($tokenparams['refresh_token']) ? $tokenparams['refresh_token'] : ''; // TBD?
        $tokenrec->idtoken = $tokenparams['id_token'];
        $tokenrec->id = $DB->insert_record('auth_iomadoidc_token', $tokenrec);
        return $tokenrec;
    }

    /**
     * Update a token with a new auth code and access token data.
     *
     * @param int $tokenid The database record ID of the token to update.
     * @param array $authparams Parameters receieved from the auth request.
     * @param array $tokenparams Parameters received from the token request.
     */
    protected function updatetoken($tokenid, $authparams, $tokenparams) {
        global $DB;
        $tokenrec = new stdClass;
        $tokenrec->id = $tokenid;
        $tokenrec->authcode = $authparams['code'];
        $tokenrec->token = $tokenparams['access_token'];
        if (!empty($tokenparams['expires_on'])) {
            $tokenrec->expiry = $tokenparams['expires_on'];
        } else if (isset($tokenparams['expires_in'])) {
            $tokenrec->expiry = time() + $tokenparams['expires_in'];
        } else {
            $tokenrec->expiry = time() + DAYSECS;
        }
        $tokenrec->refreshtoken = !empty($tokenparams['refresh_token']) ? $tokenparams['refresh_token'] : ''; // TBD?
        $tokenrec->idtoken = $tokenparams['id_token'];
        $DB->update_record('auth_iomadoidc_token', $tokenrec);
    }
}
