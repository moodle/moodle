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
//

/**
 * Provide static functions for creating and validating issuers.
 *
 * @package    repository_nextcloud
 * @copyright  2018 Jan Dageförde (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_nextcloud;

defined('MOODLE_INTERNAL') || die();

/**
 * Provide static functions for creating and validating issuers.
 *
 * @package    repository_nextcloud
 * @copyright  2018 Jan Dageförde (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issuer_management {

    /**
     * Check if an issuer provides all endpoints that are required by repository_nextcloud.
     * @param \core\oauth2\issuer $issuer An issuer.
     * @return bool True, if all endpoints exist; false otherwise.
     */
    public static function is_valid_issuer(\core\oauth2\issuer $issuer) {
        $endpointwebdav = false;
        $endpointocs = false;
        $endpointtoken = false;
        $endpointauth = false;
        $endpointuserinfo = false;
        $endpoints = \core\oauth2\api::get_endpoints($issuer);
        foreach ($endpoints as $endpoint) {
            $name = $endpoint->get('name');
            switch ($name) {
                case 'webdav_endpoint':
                    $endpointwebdav = true;
                    break;
                case 'ocs_endpoint':
                    $endpointocs = true;
                    break;
                case 'token_endpoint':
                    $endpointtoken = true;
                    break;
                case 'authorization_endpoint':
                    $endpointauth = true;
                    break;
                case 'userinfo_endpoint':
                    $endpointuserinfo = true;
                    break;
            }
        }
        return $endpointwebdav && $endpointocs && $endpointtoken && $endpointauth && $endpointuserinfo;
    }

    /**
     * Returns the parsed url parts of an endpoint of an issuer.
     * @param string $endpointname
     * @param \core\oauth2\issuer $issuer
     * @return array parseurl [scheme => https/http, host=>'hostname', port=>443, path=>'path']
     * @throws configuration_exception if an endpoint is undefined
     */
    public static function parse_endpoint_url(string $endpointname, \core\oauth2\issuer $issuer): array {
        $url = $issuer->get_endpoint_url($endpointname);
        if (empty($url)) {
            throw new configuration_exception(get_string('endpointnotdefined', 'repository_nextcloud', $endpointname));
        }
        return parse_url($url);
    }
}
