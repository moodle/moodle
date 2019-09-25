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
 * Communicate with backpacks.
 *
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

namespace core_badges;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

use cache;
use coding_exception;
use core_badges\external\assertion_exporter;
use core_badges\external\collection_exporter;
use core_badges\external\issuer_exporter;
use core_badges\external\badgeclass_exporter;
use curl;
use stdClass;
use context_system;

define('BADGE_ACCESS_TOKEN', 'access');
define('BADGE_USER_ID_TOKEN', 'user_id');
define('BADGE_BACKPACK_ID_TOKEN', 'backpack_id');
define('BADGE_REFRESH_TOKEN', 'refresh');
define('BADGE_EXPIRES_TOKEN', 'expires');

/**
 * Class for communicating with backpacks.
 *
 * @package   core_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backpack_api {

    /** @var string The email address of the issuer or the backpack owner. */
    private $email;

    /** @var string The base url used for api requests to this backpack. */
    private $backpackapiurl;

    /** @var integer The backpack api version to use. */
    private $backpackapiversion;

    /** @var string The password to authenticate requests. */
    private $password;

    /** @var boolean User or site api requests. */
    private $isuserbackpack;

    /** @var integer The id of the backpack we are talking to. */
    private $backpackid;

    /** @var \backpack_api_mapping[] List of apis for the user or site using api version 1 or 2. */
    private $mappings = [];

    /**
     * Create a wrapper to communicate with the backpack.
     *
     * The resulting class can only do either site backpack communication or
     * user backpack communication.
     *
     * @param stdClass $sitebackpack The site backpack record
     * @param mixed $userbackpack Optional - if passed it represents the users backpack.
     */
    public function __construct($sitebackpack, $userbackpack = false) {
        global $CFG;
        $admin = get_admin();

        $this->backpackapiurl = $sitebackpack->backpackapiurl;
        $this->backpackapiurl = $sitebackpack->backpackapiurl;
        $this->backpackapiversion = $sitebackpack->apiversion;
        $this->password = $sitebackpack->password;
        $this->email = !empty($CFG->badges_defaultissuercontact) ? $CFG->badges_defaultissuercontact : '';
        $this->isuserbackpack = false;
        $this->backpackid = $sitebackpack->id;
        if (!empty($userbackpack)) {
            if ($userbackpack->externalbackpackid != $sitebackpack->id) {
                throw new coding_exception('Incorrect backpack');
            }
            $this->isuserbackpack = true;
            $this->password = $userbackpack->password;
            $this->email = $userbackpack->email;
        }

        $this->define_mappings();
        // Clear the last authentication error.
        backpack_api_mapping::set_authentication_error('');
    }

    /**
     * Define the mappings supported by this usage and api version.
     */
    private function define_mappings() {
        if ($this->backpackapiversion == OPEN_BADGES_V2) {
            if ($this->isuserbackpack) {
                $mapping = [];
                $mapping[] = [
                    'collections',                              // Action.
                    '[URL]/backpack/collections',               // URL
                    [],                                         // Post params.
                    '',                                         // Request exporter.
                    'core_badges\external\collection_exporter', // Response exporter.
                    true,                                       // Multiple.
                    'get',                                      // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                $mapping[] = [
                    'user',                                     // Action.
                    '[SCHEME]://[HOST]/o/token',                // URL
                    ['username' => '[EMAIL]', 'password' => '[PASSWORD]'], // Post params.
                    '',                                         // Request exporter.
                    'oauth_token_response',                     // Response exporter.
                    false,                                      // Multiple.
                    'post',                                     // Method.
                    false,                                      // JSON Encoded.
                    false,                                      // Auth required.
                ];
                $mapping[] = [
                    'assertion',                                // Action.
                    // Badgr.io does not return the public information about a badge
                    // if the issuer is associated with another user. We need to pass
                    // the expand parameters which are not in any specification to get
                    // additional information about the assertion in a single request.
                    '[URL]/backpack/assertions/[PARAM2]?expand=badgeclass&expand=issuer',
                    [],                                         // Post params.
                    '',                                         // Request exporter.
                    'core_badges\external\assertion_exporter',  // Response exporter.
                    false,                                      // Multiple.
                    'get',                                      // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                $mapping[] = [
                    'badges',                                   // Action.
                    '[URL]/backpack/collections/[PARAM1]',      // URL
                    [],                                         // Post params.
                    '',                                         // Request exporter.
                    'core_badges\external\collection_exporter', // Response exporter.
                    true,                                       // Multiple.
                    'get',                                      // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                foreach ($mapping as $map) {
                    $map[] = true; // User api function.
                    $map[] = OPEN_BADGES_V2; // V2 function.
                    $this->mappings[] = new backpack_api_mapping(...$map);
                }
            } else {
                $mapping = [];
                $mapping[] = [
                    'user',                                     // Action.
                    '[SCHEME]://[HOST]/o/token',                // URL
                    ['username' => '[EMAIL]', 'password' => '[PASSWORD]'], // Post params.
                    '',                                         // Request exporter.
                    'oauth_token_response',                     // Response exporter.
                    false,                                      // Multiple.
                    'post',                                     // Method.
                    false,                                      // JSON Encoded.
                    false                                       // Auth required.
                ];
                $mapping[] = [
                    'issuers',                                  // Action.
                    '[URL]/issuers',                            // URL
                    '[PARAM]',                                  // Post params.
                    'core_badges\external\issuer_exporter',     // Request exporter.
                    'core_badges\external\issuer_exporter',     // Response exporter.
                    false,                                      // Multiple.
                    'post',                                     // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                $mapping[] = [
                    'badgeclasses',                             // Action.
                    '[URL]/issuers/[PARAM2]/badgeclasses',      // URL
                    '[PARAM]',                                  // Post params.
                    'core_badges\external\badgeclass_exporter', // Request exporter.
                    'core_badges\external\badgeclass_exporter', // Response exporter.
                    false,                                      // Multiple.
                    'post',                                     // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                $mapping[] = [
                    'assertions',                               // Action.
                    '[URL]/badgeclasses/[PARAM2]/assertions',   // URL
                    '[PARAM]',                                  // Post params.
                    'core_badges\external\assertion_exporter', // Request exporter.
                    'core_badges\external\assertion_exporter', // Response exporter.
                    false,                                      // Multiple.
                    'post',                                     // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                foreach ($mapping as $map) {
                    $map[] = false; // Site api function.
                    $map[] = OPEN_BADGES_V2; // V2 function.
                    $this->mappings[] = new backpack_api_mapping(...$map);
                }
            }
        } else {
            if ($this->isuserbackpack) {
                $mapping = [];
                $mapping[] = [
                    'user',                                     // Action.
                    '[URL]/displayer/convert/email',            // URL
                    ['email' => '[EMAIL]'],                     // Post params.
                    '',                                         // Request exporter.
                    'convert_email_response',                   // Response exporter.
                    false,                                      // Multiple.
                    'post',                                     // Method.
                    false,                                      // JSON Encoded.
                    false                                       // Auth required.
                ];
                $mapping[] = [
                    'groups',                                   // Action.
                    '[URL]/displayer/[PARAM1]/groups.json',     // URL
                    [],                                         // Post params.
                    '',                                         // Request exporter.
                    '',                                         // Response exporter.
                    false,                                      // Multiple.
                    'get',                                      // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                $mapping[] = [
                    'badges',                                   // Action.
                    '[URL]/displayer/[PARAM2]/group/[PARAM1].json',     // URL
                    [],                                         // Post params.
                    '',                                         // Request exporter.
                    '',                                         // Response exporter.
                    false,                                      // Multiple.
                    'get',                                      // Method.
                    true,                                       // JSON Encoded.
                    true                                        // Auth required.
                ];
                foreach ($mapping as $map) {
                    $map[] = true; // User api function.
                    $map[] = OPEN_BADGES_V1; // V1 function.
                    $this->mappings[] = new backpack_api_mapping(...$map);
                }
            } else {
                $mapping = [];
                $mapping[] = [
                    'user',                                     // Action.
                    '[URL]/displayer/convert/email',            // URL
                    ['email' => '[EMAIL]'],                     // Post params.
                    '',                                         // Request exporter.
                    'convert_email_response',                   // Response exporter.
                    false,                                      // Multiple.
                    'post',                                     // Method.
                    false,                                      // JSON Encoded.
                    false                                       // Auth required.
                ];
                foreach ($mapping as $map) {
                    $map[] = false; // Site api function.
                    $map[] = OPEN_BADGES_V1; // V1 function.
                    $this->mappings[] = new backpack_api_mapping(...$map);
                }
            }
        }
    }

    /**
     * Make an api request
     *
     * @param string $action The api function.
     * @param string $collection An api parameter
     * @param string $entityid An api parameter
     * @param string $postdata The body of the api request.
     * @return mixed
     */
    private function curl_request($action, $collection = null, $entityid = null, $postdata = null) {
        global $CFG, $SESSION;

        $curl = new curl();
        $authrequired = false;
        if ($this->backpackapiversion == OPEN_BADGES_V1) {
            $useridkey = $this->get_token_key(BADGE_USER_ID_TOKEN);
            if (isset($SESSION->$useridkey)) {
                if ($collection == null) {
                    $collection = $SESSION->$useridkey;
                } else {
                    $entityid = $SESSION->$useridkey;
                }
            }
        }
        foreach ($this->mappings as $mapping) {
            if ($mapping->is_match($action)) {
                return $mapping->request(
                    $this->backpackapiurl,
                    $collection,
                    $entityid,
                    $this->email,
                    $this->password,
                    $postdata,
                    $this->backpackid
                );
            }
        }

        throw new coding_exception('Unknown request');
    }

    /**
     * Get the id to use for requests with this api.
     *
     * @return integer
     */
    private function get_auth_user_id() {
        global $USER;

        if ($this->isuserbackpack) {
            return $USER->id;
        } else {
            // The access tokens for the system backpack are shared.
            return -1;
        }
    }

    /**
     * Get the name of the key to store this access token type.
     *
     * @param string $type
     * @return string
     */
    private function get_token_key($type) {
        // This should be removed when everything has a mapping.
        $prefix = 'badges_';
        if ($this->isuserbackpack) {
            $prefix .= 'user_backpack_';
        } else {
            $prefix .= 'site_backpack_';
        }
        $prefix .= $type . '_token';
        return $prefix;
    }

    /**
     * Normalise the return from a missing user request.
     *
     * @param string $status
     * @return mixed
     */
    private function check_status($status) {
        // V1 ONLY.
        switch($status) {
            case "missing":
                $response = array(
                    'status'  => $status,
                    'message' => get_string('error:nosuchuser', 'badges')
                );
                return $response;
        }
        return false;
    }

    /**
     * Make an api request to get an assertion
     *
     * @param string $entityid The id of the assertion.
     * @return mixed
     */
    public function get_assertion($entityid) {
        // V2 Only.
        if ($this->backpackapiversion == OPEN_BADGES_V1) {
            throw new coding_exception('Not supported in this backpack API');
        }

        return $this->curl_request('assertion', null, $entityid);
    }

    /**
     * Create a badgeclass assertion.
     *
     * @param string $entityid The id of the badge class.
     * @param string $data The structure of the badge class assertion.
     * @return mixed
     */
    public function put_badgeclass_assertion($entityid, $data) {
        // V2 Only.
        if ($this->backpackapiversion == OPEN_BADGES_V1) {
            throw new coding_exception('Not supported in this backpack API');
        }

        return $this->curl_request('assertions', null, $entityid, $data);
    }

    /**
     * Select collections from a backpack.
     *
     * @param string $backpackid The id of the backpack
     * @param stdClass[] $collections List of collections with collectionid or entityid.
     * @return boolean
     */
    public function set_backpack_collections($backpackid, $collections) {
        global $DB, $USER;

        // Delete any previously selected collections.
        $sqlparams = array('backpack' => $backpackid);
        $select = 'backpackid = :backpack ';
        $DB->delete_records_select('badge_external', $select, $sqlparams);
        $badgescache = cache::make('core', 'externalbadges');

        // Insert selected collections if they are not in database yet.
        foreach ($collections as $collection) {
            $obj = new stdClass();
            $obj->backpackid = $backpackid;
            if ($this->backpackapiversion == OPEN_BADGES_V1) {
                $obj->collectionid = (int) $collection;
            } else {
                $obj->entityid = $collection;
                $obj->collectionid = -1;
            }
            if (!$DB->record_exists('badge_external', (array) $obj)) {
                $DB->insert_record('badge_external', $obj);
            }
        }
        $badgescache->delete($USER->id);
        return true;
    }

    /**
     * Create a badgeclass
     *
     * @param string $entityid The id of the entity.
     * @param string $data The structure of the badge class.
     * @return mixed
     */
    public function put_badgeclass($entityid, $data) {
        // V2 Only.
        if ($this->backpackapiversion == OPEN_BADGES_V1) {
            throw new coding_exception('Not supported in this backpack API');
        }

        return $this->curl_request('badgeclasses', null, $entityid, $data);
    }

    /**
     * Create an issuer
     *
     * @param string $data The structure of the issuer.
     * @return mixed
     */
    public function put_issuer($data) {
        // V2 Only.
        if ($this->backpackapiversion == OPEN_BADGES_V1) {
            throw new coding_exception('Not supported in this backpack API');
        }

        return $this->curl_request('issuers', null, null, $data);
    }

    /**
     * Delete any user access tokens in the session so we will attempt to get new ones.
     *
     * @return void
     */
    public function clear_system_user_session() {
        global $SESSION;

        $useridkey = $this->get_token_key(BADGE_USER_ID_TOKEN);
        unset($SESSION->$useridkey);

        $expireskey = $this->get_token_key(BADGE_EXPIRES_TOKEN);
        unset($SESSION->$expireskey);
    }

    /**
     * Authenticate using the stored email and password and save the valid access tokens.
     *
     * @return integer The id of the authenticated user.
     */
    public function authenticate() {
        global $SESSION;

        $backpackidkey = $this->get_token_key(BADGE_BACKPACK_ID_TOKEN);
        $backpackid = isset($SESSION->$backpackidkey) ? $SESSION->$backpackidkey : 0;
        // If the backpack is changed we need to expire sessions.
        if ($backpackid == $this->backpackid) {
            if ($this->backpackapiversion == OPEN_BADGES_V2) {
                $useridkey = $this->get_token_key(BADGE_USER_ID_TOKEN);
                $authuserid = isset($SESSION->$useridkey) ? $SESSION->$useridkey : 0;
                if ($authuserid == $this->get_auth_user_id()) {
                    $expireskey = $this->get_token_key(BADGE_EXPIRES_TOKEN);
                    if (isset($SESSION->$expireskey)) {
                        $expires = $SESSION->$expireskey;
                        if ($expires > time()) {
                            // We have a current access token for this user
                            // that has not expired.
                            return -1;
                        }
                    }
                }
            } else {
                $useridkey = $this->get_token_key(BADGE_USER_ID_TOKEN);
                $authuserid = isset($SESSION->$useridkey) ? $SESSION->$useridkey : 0;
                if (!empty($authuserid)) {
                    return $authuserid;
                }
            }
        }
        return $this->curl_request('user', $this->email);
    }

    /**
     * Get all collections in this backpack.
     *
     * @return stdClass[] The collections.
     */
    public function get_collections() {
        global $PAGE;

        if ($this->authenticate()) {
            if ($this->backpackapiversion == OPEN_BADGES_V1) {
                $result = $this->curl_request('groups');
                if (isset($result->groups)) {
                    $result = $result->groups;
                }
            } else {
                $result = $this->curl_request('collections');
            }
            if ($result) {
                return $result;
            }
        }
        return [];
    }

    /**
     * Get one collection by id.
     *
     * @param integer $collectionid
     * @return stdClass The collection.
     */
    public function get_collection_record($collectionid) {
        global $DB;

        if ($this->backpackapiversion == OPEN_BADGES_V1) {
            return $DB->get_fieldset_select('badge_external', 'collectionid', 'backpackid = :bid', array('bid' => $collectionid));
        } else {
            return $DB->get_fieldset_select('badge_external', 'entityid', 'backpackid = :bid', array('bid' => $collectionid));
        }
    }

    /**
     * Disconnect the backpack from this user.
     *
     * @param integer $userid The user in Moodle
     * @param integer $backpackid The backpack to disconnect
     * @param integer $externalbackupid The external backpack to disconnect
     * @return boolean
     */
    public function disconnect_backpack($userid, $backpackid, $externalbackupid) {
        global $DB, $USER;

        if (\core\session\manager::is_loggedinas() || $userid != $USER->id) {
            // Can't change someone elses backpack settings.
            return false;
        }

        $badgescache = cache::make('core', 'externalbadges');

        $DB->delete_records('badge_external', array('backpackid' => $backpackid));
        $DB->delete_records('badge_backpack', array('userid' => $userid));
        $DB->delete_records('badge_external_backpack', array('id' => $externalbackupid));
        $badgescache->delete($userid);
        return true;
    }

    /**
     * Handle the response from getting a collection to map to an id.
     *
     * @param stdClass $data The response data.
     * @return string The collection id.
     */
    public function get_collection_id_from_response($data) {
        if ($this->backpackapiversion == OPEN_BADGES_V1) {
            return $data->groupId;
        } else {
            return $data->entityId;
        }
    }

    /**
     * Get the last error message returned during an authentication request.
     *
     * @return string
     */
    public function get_authentication_error() {
        return backpack_api_mapping::get_authentication_error();
    }

    /**
     * Get the list of badges in a collection.
     *
     * @param stdClass $collection The collection to deal with.
     * @param boolean $expanded Fetch all the sub entities.
     * @return stdClass[]
     */
    public function get_badges($collection, $expanded = false) {
        global $PAGE;

        if ($this->authenticate()) {
            if ($this->backpackapiversion == OPEN_BADGES_V1) {
                if (empty($collection->collectionid)) {
                    return [];
                }
                $result = $this->curl_request('badges', $collection->collectionid);
                return $result->badges;
            } else {
                if (empty($collection->entityid)) {
                    return [];
                }
                // Now we can make requests.
                $badges = $this->curl_request('badges', $collection->entityid);
                if (count($badges) == 0) {
                    return [];
                }
                $badges = $badges[0];
                if ($expanded) {
                    $publicassertions = [];
                    $context = context_system::instance();
                    $output = $PAGE->get_renderer('core', 'badges');
                    foreach ($badges->assertions as $assertion) {
                        $remoteassertion = $this->get_assertion($assertion);
                        // Remote badge was fetched nested in the assertion.
                        $remotebadge = $remoteassertion->badgeclass;
                        if (!$remotebadge) {
                            continue;
                        }
                        $apidata = badgeclass_exporter::map_external_data($remotebadge, $this->backpackapiversion);
                        $exporterinstance = new badgeclass_exporter($apidata, ['context' => $context]);
                        $remotebadge = $exporterinstance->export($output);

                        $remoteissuer = $remotebadge->issuer;
                        $apidata = issuer_exporter::map_external_data($remoteissuer, $this->backpackapiversion);
                        $exporterinstance = new issuer_exporter($apidata, ['context' => $context]);
                        $remoteissuer = $exporterinstance->export($output);

                        $badgeclone = clone $remotebadge;
                        $badgeclone->issuer = $remoteissuer;
                        $remoteassertion->badge = $badgeclone;
                        $remotebadge->assertion = $remoteassertion;
                        $publicassertions[] = $remotebadge;
                    }
                    $badges = $publicassertions;
                }
                return $badges;
            }
        }
    }
}
