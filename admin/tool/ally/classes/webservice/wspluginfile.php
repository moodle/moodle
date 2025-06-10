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

namespace tool_ally\webservice;

defined('MOODLE_INTERNAL') || die;

use tool_ally\local;
use tool_ally\local_file;

use moodle_exception;
use webservice;
use webservice_access_exception;

require_once($CFG->dirroot . '/webservice/lib.php');

/**
 * Class for dealing with wspluginfile.php endpoint.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class wspluginfile {

    /**
     * Validate a ws pluginfile signature and return the authentication info array.
     * @param string $signature
     * @param string $iat
     * @param string $pathnamehash
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \webservice_access_exception
     */
    public function validate_wspluginfile_signature($signature, $iat, $pathnamehash) {
        if ($iat < time() - HOURSECS) {
            throw new \webservice_access_exception('The signature issued has expired');
        }
        $tokenobj = local::get_ws_token();

        $calcsig = local_file::generate_wspluginfile_signature($pathnamehash, $iat)->signature;
        if (strtolower($signature) !== strtolower($calcsig)) {
            $msg = 'Signature is invalid.';
            throw new \webservice_access_exception($msg);
        }

        $webservicelib = new webservice();
        $authenticationinfo = $webservicelib->authenticate_user($tokenobj->token);
        return ($authenticationinfo);
    }

    /**
     * Get file.
     * @param string $pathnamehash
     * @param string|null $token
     * @param string|null $signature
     * @param int|null $iat
     * @return bool|\stored_file
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws moodle_exception
     * @throws webservice_access_exception
     */
    public function get_file($pathnamehash, $token = null, $signature = null, $iat = null) {
        if ($token === null && $signature === null) {
            throw new webservice_access_exception('Required param of either "token" or "signature" missing');
        }

        $webservicelib = new webservice();

        if ($token) {
            $authenticationinfo = $webservicelib->authenticate_user($token);
        } else {
            if (empty($iat)) {
                throw new \invalid_parameter_exception('Required param "iat" missing.');
            }
            $authenticationinfo = $this->validate_wspluginfile_signature($signature, $iat, $pathnamehash);
        }

        $service = $authenticationinfo['service'];

        // Ensure that the service allows file downloads.
        $enabledfiledownload = (int) ($service->downloadfiles);
        if (empty($enabledfiledownload)) {
            throw new webservice_access_exception('Web service file downloading must be enabled in external service settings');
        }

        $fs = get_file_storage();
        $file = $fs->get_file_by_hash($pathnamehash);
        if (!$file) {
            throw new moodle_exception('filenotfound', 'error');
        }

        return $file;
    }
}
