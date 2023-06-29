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
 * @copyright  2020 Tung Thai based on Totara Learning Solutions Ltd {@link http://www.totaralms.com/} dode
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

namespace core_badges;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

use cache;
use coding_exception;
use context_system;
use moodle_url;
use core_badges\backpack_api2p1_mapping;
use core_badges\oauth2\client;
use curl;
use stdClass;
use core\oauth2\issuer;
use core\oauth2\endpoint;
use core\oauth2\discovery\imsbadgeconnect;

/**
 * To process badges with backpack and control api request and this class using for Open Badge API v2.1 methods.
 *
 * @package   core_badges
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backpack_api2p1 {

    /** @var object is the external backpack. */
    private $externalbackpack;

    /** @var array define api mapping. */
    private $mappings = [];

    /** @var false|null|stdClass|\core_badges\backpack_api2p1 to */
    private $tokendata;

    /** @var null clienid. */
    private $clientid = null;

    /** @var null version api of the backpack. */
    protected $backpackapiversion;

    /** @var issuer The OAuth2 Issuer for this backpack */
    protected $issuer;

    /** @var endpoint The apiBase endpoint */
    protected $apibase;

    /**
     * backpack_api2p1 constructor.
     *
     * @param object $externalbackpack object
     * @throws coding_exception error message
     */
    public function __construct($externalbackpack) {

        if (!empty($externalbackpack)) {
            $this->externalbackpack = $externalbackpack;
            $this->backpackapiversion = $externalbackpack->apiversion;
            $this->get_clientid = $this->get_clientid($externalbackpack->oauth2_issuerid);

            if (!($this->tokendata = $this->get_stored_token($externalbackpack->id))
                && $this->backpackapiversion != OPEN_BADGES_V2P1) {
                throw new coding_exception('Backpack incorrect');
            }
        }

        $this->define_mappings();
    }

    /**
     * Initialises or returns the OAuth2 issuer associated to this backpack.
     *
     * @return issuer
     */
    protected function get_issuer(): issuer {
        if (!isset($this->issuer)) {
            $this->issuer = new \core\oauth2\issuer($this->externalbackpack->oauth2_issuerid);
        }
        return $this->issuer;
    }

    /**
     * Gets the apiBase url associated to this backpack.
     *
     * @return string
     */
    protected function get_api_base_url(): string {
        if (!isset($this->apibase)) {
            $apibase = endpoint::get_record([
                'issuerid' => $this->externalbackpack->oauth2_issuerid,
                'name' => 'apiBase',
            ]);

            if (empty($apibase)) {
                imsbadgeconnect::create_endpoints($this->get_issuer());
                $apibase = endpoint::get_record([
                    'issuerid' => $this->externalbackpack->oauth2_issuerid,
                    'name' => 'apiBase',
                ]);
            }

            $this->apibase = $apibase;
        }

        return $this->apibase->get('url');
    }


    /**
     * Define the mappings supported by this usage and api version.
     */
    private function define_mappings() {
        if ($this->backpackapiversion == OPEN_BADGES_V2P1) {

            $mapping = [];
            $mapping[] = [
                'post.assertions',                               // Action.
                '[URL]/assertions',   // URL
                '[PARAM]',                                  // Post params.
                false,                                      // Multiple.
                'post',                                     // Method.
                true,                                       // JSON Encoded.
                true                                        // Auth required.
            ];

            $mapping[] = [
                'get.assertions',                               // Action.
                '[URL]/assertions',   // URL
                '[PARAM]',                                  // Post params.
                false,                                      // Multiple.
                'get',                                     // Method.
                true,                                       // JSON Encoded.
                true                                        // Auth required.
            ];

            foreach ($mapping as $map) {
                $map[] = false; // Site api function.
                $map[] = OPEN_BADGES_V2P1; // V2 function.
                $this->mappings[] = new backpack_api2p1_mapping(...$map);
            }

        }
    }

    /**
     * Disconnect the backpack from this user.
     *
     * @param object $backpack to disconnect.
     * @return bool
     * @throws \dml_exception
     */
    public function disconnect_backpack($backpack) {
        global $USER, $DB;

        if ($backpack) {
            $DB->delete_records_select('badge_external', 'backpackid = :backpack', ['backpack' => $backpack->id]);
            $DB->delete_records('badge_backpack', ['id' => $backpack->id]);
            $DB->delete_records('badge_backpack_oauth2', ['externalbackpackid' => $this->externalbackpack->id,
                'userid' => $USER->id]);

            return true;
        }
        return false;
    }

    /**
     * Make an api request.
     *
     * @param string $action The api function.
     * @param string $postdata The body of the api request.
     * @return mixed
     */
    public function curl_request($action, $postdata = null) {
        $tokenkey = $this->tokendata->token;
        foreach ($this->mappings as $mapping) {
            if ($mapping->is_match($action)) {
                return $mapping->request(
                    $this->get_api_base_url(),
                    $tokenkey,
                    $postdata
                );
            }
        }

        throw new coding_exception('Unknown request');
    }

    /**
     * Get token.
     *
     * @param int $externalbackpackid ID of external backpack.
     * @return oauth2\badge_backpack_oauth2|false|stdClass|null
     */
    protected function get_stored_token($externalbackpackid) {
        global $USER;

        $token = \core_badges\oauth2\badge_backpack_oauth2::get_record(
            ['externalbackpackid' => $externalbackpackid, 'userid' => $USER->id]);
        if ($token !== false) {
            $token = $token->to_record();
            return $token;
        }
        return null;
    }

    /**
     * Get client id.
     *
     * @param int $issuerid id of Oauth2 service.
     * @throws coding_exception
     */
    private function get_clientid($issuerid) {
        $issuer = \core\oauth2\api::get_issuer($issuerid);
        if (!empty($issuer)) {
            $this->issuer = $issuer;
            $this->clientid = $issuer->get('clientid');
        }
    }

    /**
     * Export a badge to the backpack site.
     *
     * @param string $hash of badge issued.
     * @return array
     * @throws \moodle_exception
     * @throws coding_exception
     */
    public function put_assertions($hash) {
        $data = [];
        if (!$hash) {
            return false;
        }

        $issuer = $this->get_issuer();
        $client = new client($issuer, new moodle_url('/badges/mybadges.php'), '', $this->externalbackpack);
        if (!$client->is_logged_in()) {
            $redirecturl = new moodle_url('/badges/mybadges.php', ['error' => 'backpackexporterror']);
            redirect($redirecturl);
        }

        $this->tokendata = $this->get_stored_token($this->externalbackpack->id);

        $assertion = new \core_badges_assertion($hash, OPEN_BADGES_V2);
        $data['assertion'] = $assertion->get_badge_assertion();
        $response = $this->curl_request('post.assertions', $data);
        if ($response && isset($response->status->statusCode) && $response->status->statusCode == 200) {
            $msg['status'] = \core\output\notification::NOTIFY_SUCCESS;
            $msg['message'] = get_string('addedtobackpack', 'badges');
        } else {
            $msg['status'] = \core\output\notification::NOTIFY_ERROR;
            $msg['message'] = get_string('backpackexporterror', 'badges', $data['assertion']['badge']['name']);
        }
        return $msg;
    }
}
