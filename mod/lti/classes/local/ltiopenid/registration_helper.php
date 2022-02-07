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
 * A Helper for LTI Dynamic Registration.
 *
 * @package    mod_lti
 * @copyright  2020 Claude Vervoort (Cengage), Carlos Costa, Adrian Hutchinson (Macgraw Hill)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\local\ltiopenid;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/lti/locallib.php');
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

/**
 * This class exposes functions for LTI Dynamic Registration.
 *
 * @package    mod_lti
 * @copyright  2020 Claude Vervoort (Cengage), Carlos Costa, Adrian Hutchinson (Macgraw Hill)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration_helper {
    /** score scope */
    const SCOPE_SCORE = 'https://purl.imsglobal.org/spec/lti-ags/scope/score';
    /** result scope */
    const SCOPE_RESULT = 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly';
    /** lineitem read-only scope */
    const SCOPE_LINEITEM_RO = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly';
    /** lineitem full access scope */
    const SCOPE_LINEITEM = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem';
    /** Names and Roles (membership) scope */
    const SCOPE_NRPS = 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';
    /** Tool Settings scope */
    const SCOPE_TOOL_SETTING = 'https://purl.imsglobal.org/spec/lti-ts/scope/toolsetting';

    /** Indicates the token is to create a new registration */
    const REG_TOKEN_OP_NEW_REG = 'reg';
    /** Indicates the token is to update an existing registration */
    const REG_TOKEN_OP_UPDATE_REG = 'reg-update';

    /**
     * Get an instance of this helper
     *
     * @return object
     */
    public static function get() {
        return new registration_helper();
    }

    /**
     * Function used to validate parameters.
     *
     * This function is needed because the payload contains nested
     * objects, and optional_param() does not support arrays of arrays.
     *
     * @param array $payload that may contain the parameter key
     * @param string $key the key of the value to be looked for in the payload
     * @param bool $required if required, not finding a value will raise a registration_exception
     *
     * @return mixed
     */
    private function get_parameter(array $payload, string $key, bool $required) {
        if (!isset($payload[$key]) || empty($payload[$key])) {
            if ($required) {
                throw new registration_exception('missing required attribute '.$key, 400);
            }
            return null;
        }
        $parameter = $payload[$key];
        // Cleans parameters to avoid XSS and other issues.
        if (is_array($parameter)) {
            return clean_param_array($parameter, PARAM_TEXT, true);
        }
        return clean_param($parameter, PARAM_TEXT);
    }

    /**
     * Transforms an LTI 1.3 Registration to a Moodle LTI Config.
     *
     * @param array $registrationpayload the registration data received from the tool.
     * @param string $clientid the clientid to be issued for that tool.
     *
     * @return object the Moodle LTI config.
     */
    public function registration_to_config(array $registrationpayload, string $clientid): object {
        $responsetypes = $this->get_parameter($registrationpayload, 'response_types', true);
        $initiateloginuri = $this->get_parameter($registrationpayload, 'initiate_login_uri', true);
        $redirecturis = $this->get_parameter($registrationpayload, 'redirect_uris', true);
        $clientname = $this->get_parameter($registrationpayload, 'client_name', true);
        $jwksuri = $this->get_parameter($registrationpayload, 'jwks_uri', true);
        $tokenendpointauthmethod = $this->get_parameter($registrationpayload, 'token_endpoint_auth_method', true);

        $applicationtype = $this->get_parameter($registrationpayload, 'application_type', false);
        $logouri = $this->get_parameter($registrationpayload, 'logo_uri', false);

        $ltitoolconfiguration = $this->get_parameter($registrationpayload,
            'https://purl.imsglobal.org/spec/lti-tool-configuration', true);

        $domain = $this->get_parameter($ltitoolconfiguration, 'domain', false);
        $targetlinkuri = $this->get_parameter($ltitoolconfiguration, 'target_link_uri', false);
        $customparameters = $this->get_parameter($ltitoolconfiguration, 'custom_parameters', false);
        $scopes = explode(" ", $this->get_parameter($registrationpayload, 'scope', false) ?? '');
        $claims = $this->get_parameter($ltitoolconfiguration, 'claims', false);
        $messages = $ltitoolconfiguration['messages'] ?? [];
        $description = $this->get_parameter($ltitoolconfiguration, 'description', false);

        // Validate domain and target link.
        if (empty($domain)) {
            throw new registration_exception('missing_domain', 400);
        }

        $targetlinkuri = $targetlinkuri ?: 'https://'.$domain;
        // Stripping www as this is ignored for domain matching.
        $domain = lti_get_domain_from_url($domain);
        if ($domain !== lti_get_domain_from_url($targetlinkuri)) {
            throw new registration_exception('domain_targetlinkuri_mismatch', 400);
        }

        // Validate response type.
        // According to specification, for this scenario, id_token must be explicitly set.
        if (!in_array('id_token', $responsetypes)) {
            throw new registration_exception('invalid_response_types', 400);
        }

        // According to specification, this parameter needs to be an array.
        if (!is_array($redirecturis)) {
            throw new registration_exception('invalid_redirect_uris', 400);
        }

        // According to specification, for this scenario private_key_jwt must be explicitly set.
        if ($tokenendpointauthmethod !== 'private_key_jwt') {
            throw new registration_exception('invalid_token_endpoint_auth_method', 400);
        }

        if (!empty($applicationtype) && $applicationtype !== 'web') {
            throw new registration_exception('invalid_application_type', 400);
        }

        $config = new stdClass();
        $config->lti_clientid = $clientid;
        $config->lti_toolurl = $targetlinkuri;
        $config->lti_tooldomain = $domain;
        $config->lti_typename = $clientname;
        $config->lti_description = $description;
        $config->lti_ltiversion = LTI_VERSION_1P3;
        $config->lti_organizationid_default = LTI_DEFAULT_ORGID_SITEID;
        $config->lti_icon = $logouri;
        $config->lti_coursevisible = LTI_COURSEVISIBLE_PRECONFIGURED;
        $config->lti_contentitem = 0;
        // Sets Content Item.
        if (!empty($messages)) {
            $messagesresponse = [];
            foreach ($messages as $value) {
                if ($value['type'] === 'LtiDeepLinkingRequest') {
                    $config->lti_contentitem = 1;
                    $config->lti_toolurl_ContentItemSelectionRequest = $value['target_link_uri'] ?? '';
                    array_push($messagesresponse, $value);
                }
            }
        }

        $config->lti_keytype = 'JWK_KEYSET';
        $config->lti_publickeyset = $jwksuri;
        $config->lti_initiatelogin = $initiateloginuri;
        $config->lti_redirectionuris = implode(PHP_EOL, $redirecturis);
        $config->lti_customparameters = '';
        // Sets custom parameters.
        if (isset($customparameters)) {
            $paramssarray = [];
            foreach ($customparameters as $key => $value) {
                array_push($paramssarray, $key . '=' . $value);
            }
            $config->lti_customparameters = implode(PHP_EOL, $paramssarray);
        }
        // Sets launch container.
        $config->lti_launchcontainer = LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS;

        // Sets Service info based on scopes.
        $config->lti_acceptgrades = LTI_SETTING_NEVER;
        $config->ltiservice_gradesynchronization = 0;
        $config->ltiservice_memberships = 0;
        $config->ltiservice_toolsettings = 0;
        if (isset($scopes)) {
            // Sets Assignment and Grade Services info.

            if (in_array(self::SCOPE_SCORE, $scopes)) {
                $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
                $config->ltiservice_gradesynchronization = 1;
            }
            if (in_array(self::SCOPE_RESULT, $scopes)) {
                $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
                $config->ltiservice_gradesynchronization = 1;
            }
            if (in_array(self::SCOPE_LINEITEM_RO, $scopes)) {
                $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
                $config->ltiservice_gradesynchronization = 1;
            }
            if (in_array(self::SCOPE_LINEITEM, $scopes)) {
                $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
                $config->ltiservice_gradesynchronization = 2;
            }

            // Sets Names and Role Provisioning info.
            if (in_array(self::SCOPE_NRPS, $scopes)) {
                $config->ltiservice_memberships = 1;
            }

            // Sets Tool Settings info.
            if (in_array(self::SCOPE_TOOL_SETTING, $scopes)) {
                $config->ltiservice_toolsettings = 1;
            }
        }

        // Sets privacy settings.
        $config->lti_sendname = LTI_SETTING_NEVER;
        $config->lti_sendemailaddr = LTI_SETTING_NEVER;
        if (isset($claims)) {
            // Sets name privacy settings.

            if (in_array('name', $claims)) {
                $config->lti_sendname = LTI_SETTING_ALWAYS;
            }
            if (in_array('given_name', $claims)) {
                $config->lti_sendname = LTI_SETTING_ALWAYS;
            }
            if (in_array('family_name', $claims)) {
                $config->lti_sendname = LTI_SETTING_ALWAYS;
            }

            // Sets email privacy settings.
            if (in_array('email', $claims)) {
                $config->lti_sendemailaddr = LTI_SETTING_ALWAYS;
            }
        }
        return $config;
    }

    /**
     * Adds to the config the LTI 1.1 key and sign it with the 1.1 secret.
     *
     * @param array $lticonfig reference to lticonfig to which to add the 1.1 OAuth info.
     * @param string $key - LTI 1.1 OAuth Key
     * @param string $secret - LTI 1.1 OAuth Secret
     *
     */
    private function add_previous_key_claim(array &$lticonfig, string $key, string $secret) {
        if ($key) {
            $oauthconsumer = [];
            $oauthconsumer['key'] = $key;
            $oauthconsumer['nonce'] = random_string(random_int(10, 20));
            $oauthconsumer['sign'] = hash('sha256', $key.$secret.$oauthconsumer['nonce']);
            $lticonfig['oauth_consumer'] = $oauthconsumer;
        }
    }

    /**
     * Transforms a moodle LTI 1.3 Config to an OAuth/LTI Client Registration.
     *
     * @param object $config Moodle LTI Config.
     * @param int $typeid which is the LTI deployment id.
     * @param object $type tool instance in case the tool already exists.
     *
     * @return array the Client Registration as an associative array.
     */
    public function config_to_registration(object $config, int $typeid, object $type = null): array {
        $configarray = [];
        foreach ((array)$config as $k => $v) {
            if (substr($k, 0, 4) == 'lti_') {
                $k = substr($k, 4);
            }
            $configarray[$k] = $v;
        }
        $config = (object) $configarray;
        $registrationresponse = [];
        $lticonfigurationresponse = [];
        $ltiversion = $type ? $type->ltiversion : $config->ltiversion;
        $lticonfigurationresponse['version'] = $ltiversion;
        if ($ltiversion === LTI_VERSION_1P3) {
            $registrationresponse['client_id'] = $type ? $type->clientid : $config->clientid;
            $registrationresponse['response_types'] = ['id_token'];
            $registrationresponse['jwks_uri'] = $config->publickeyset;
            $registrationresponse['initiate_login_uri'] = $config->initiatelogin;
            $registrationresponse['grant_types'] = ['client_credentials', 'implicit'];
            $registrationresponse['redirect_uris'] = explode(PHP_EOL, $config->redirectionuris);
            $registrationresponse['application_type'] = 'web';
            $registrationresponse['token_endpoint_auth_method'] = 'private_key_jwt';
        } else if ($ltiversion === LTI_VERSION_1 && $type) {
            $this->add_previous_key_claim($lticonfigurationresponse, $config->resourcekey, $config->password);
        } else if ($ltiversion === LTI_VERSION_2 && $type) {
            $toolproxy = $this->get_tool_proxy($type->toolproxyid);
            $this->add_previous_key_claim($lticonfigurationresponse, $toolproxy['guid'], $toolproxy['secret']);
        }
        $registrationresponse['client_name'] = $type ? $type->name : $config->typename;
        $registrationresponse['logo_uri'] = $type ? ($type->secureicon ?? $type->icon ?? '') : $config->icon ?? '';
        $lticonfigurationresponse['deployment_id'] = strval($typeid);
        $lticonfigurationresponse['target_link_uri'] = $type ? $type->baseurl : $config->toolurl ?? '';
        $lticonfigurationresponse['domain'] = $type ? $type->tooldomain : $config->tooldomain ?? '';
        $lticonfigurationresponse['description'] = $type ? $type->description ?? '' : $config->description ?? '';
        if ($config->contentitem ?? 0 == 1) {
            $contentitemmessage = [];
            $contentitemmessage['type'] = 'LtiDeepLinkingRequest';
            if (isset($config->toolurl_ContentItemSelectionRequest)) {
                $contentitemmessage['target_link_uri'] = $config->toolurl_ContentItemSelectionRequest;
            }
            $lticonfigurationresponse['messages'] = [$contentitemmessage];
        }
        if (isset($config->customparameters) && !empty($config->customparameters)) {
            $params = [];
            foreach (explode(PHP_EOL, $config->customparameters) as $param) {
                $split = explode('=', $param);
                $params[$split[0]] = $split[1];
            }
            $lticonfigurationresponse['custom_parameters'] = $params;
        }
        $scopesresponse = [];
        if ($config->ltiservice_gradesynchronization ?? 0 > 0) {
            $scopesresponse[] = self::SCOPE_SCORE;
            $scopesresponse[] = self::SCOPE_RESULT;
            $scopesresponse[] = self::SCOPE_LINEITEM_RO;
        }
        if ($config->ltiservice_gradesynchronization ?? 0 == 2) {
            $scopesresponse[] = self::SCOPE_LINEITEM;
        }
        if ($config->ltiservice_memberships ?? 0 == 1) {
            $scopesresponse[] = self::SCOPE_NRPS;
        }
        if ($config->ltiservice_toolsettings ?? 0 == 1) {
            $scopesresponse[] = self::SCOPE_TOOL_SETTING;
        }
        $registrationresponse['scope'] = implode(' ', $scopesresponse);

        $claimsresponse = ['sub', 'iss'];
        if ($config->sendname ?? '' == LTI_SETTING_ALWAYS) {
            $claimsresponse[] = 'name';
            $claimsresponse[] = 'family_name';
            $claimsresponse[] = 'given_name';
        }
        if ($config->sendemailaddr ?? '' == LTI_SETTING_ALWAYS) {
            $claimsresponse[] = 'email';
        }
        $lticonfigurationresponse['claims'] = $claimsresponse;
        $registrationresponse['https://purl.imsglobal.org/spec/lti-tool-configuration'] = $lticonfigurationresponse;
        return $registrationresponse;
    }

    /**
     * Validates the registration token is properly signed and not used yet.
     * Return the client id to use for this registration.
     *
     * @param string $registrationtokenjwt registration token
     *
     * @return array with 2 keys: clientid for the registration, type but only if it's an update
     */
    public function validate_registration_token(string $registrationtokenjwt): array {
        global $DB;
        // JWK::parseKeySet uses RS256 algorithm by default.
        $keys = JWK::parseKeySet(jwks_helper::get_jwks());
        $registrationtoken = JWT::decode($registrationtokenjwt, $keys);
        $response = [];
        // Get clientid from registrationtoken.
        $clientid = $registrationtoken->sub;
        if ($registrationtoken->scope == self::REG_TOKEN_OP_NEW_REG) {
            // Checks if clientid is already registered.
            if (!empty($DB->get_record('lti_types', array('clientid' => $clientid)))) {
                throw new registration_exception("token_already_used", 401);
            }
            $response['clientid'] = $clientid;
        } else if ($registrationtoken->scope == self::REG_TOKEN_OP_UPDATE_REG) {
            $tool = lti_get_type($registrationtoken->sub);
            if (!$tool) {
                throw new registration_exception("Unknown client", 400);
            }
            $response['clientid'] = $tool->clientid ?? $this->new_clientid();
            $response['type'] = $tool;
        } else {
            throw new registration_exception("Incorrect scope", 403);
        }
        return $response;
    }

    /**
     * Initializes an array with the scopes for services supported by the LTI module
     *
     * @return array List of scopes
     */
    public function lti_get_service_scopes() {

        $services = lti_get_services();
        $scopes = array();
        foreach ($services as $service) {
            $servicescopes = $service->get_scopes();
            if (!empty($servicescopes)) {
                $scopes = array_merge($scopes, $servicescopes);
            }
        }
        return $scopes;
    }

    /**
     * Generates a new client id string.
     *
     * @return string generated client id
     */
    public function new_clientid(): string {
        return random_string(15);
    }

    /**
     * Base64 encoded signature for LTI 1.1 migration.
     * @param string $key LTI 1.1 key
     * @param string $salt Salt value
     * @param string $secret LTI 1.1 secret
     *
     * @return string base64encoded hash
     */
    public function sign(string $key, string $salt, string $secret): string {
        return base64_encode(hash_hmac('sha-256', $key.$salt, $secret, true));
    }

    /**
     * Returns a tool proxy
     *
     * @param int $proxyid
     *
     * @return mixed Tool Proxy details
     */
    public function get_tool_proxy(int $proxyid) : array {
        return lti_get_tool_proxy($proxyid);
    }
}
