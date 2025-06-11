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
 * Data generator for repository plugin.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Project seminar (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Data generator for repository plugin.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Project seminar (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_nextcloud_generator extends testing_repository_generator {

    /**
     * Creates an issuer and a user.
     * @return \core\oauth2\issuer
     */
    public function test_create_issuer() {
        $issuerdata = new stdClass();
        $issuerdata->name = "Service";
        $issuerdata->clientid = "Clientid";
        $issuerdata->clientsecret = "Secret";
        $issuerdata->loginscopes = "openid profile email";
        $issuerdata->loginscopesoffline = "openid profile email";
        $issuerdata->baseurl = "";
        $issuerdata->image = "aswdf";

        // Create the issuer.
        $issuer = \core\oauth2\api::create_issuer($issuerdata);
        return $issuer;
    }

    /**
     * Creates the required endpoints.
     * @param int $issuerid
     * @return \core\oauth2\issuer
     */
    public function test_create_endpoints($issuerid) {
        $this->test_create_single_endpoint($issuerid, "ocs_endpoint");
        $this->test_create_single_endpoint($issuerid, "authorization_endpoint");
        $this->test_create_single_endpoint($issuerid, "webdav_endpoint", "https://www.default.test/webdav/index.php");
        $this->test_create_single_endpoint($issuerid, "token_endpoint");
        $this->test_create_single_endpoint($issuerid, "userinfo_endpoint");
    }

    /**
     * Create a single endpoint.
     *
     * @param int $issuerid
     * @param string $endpointtype
     * @param string $url
     * @return \core\oauth2\endpoint An instantiated endpoint
     */
    public function test_create_single_endpoint($issuerid, $endpointtype, $url="https://www.default.test") {
        $endpoint = new stdClass();
        $endpoint->name = $endpointtype;
        $endpoint->url = $url;
        $endpoint->issuerid = $issuerid;
        $return = \core\oauth2\api::create_endpoint($endpoint);
        return $return;
    }
}
