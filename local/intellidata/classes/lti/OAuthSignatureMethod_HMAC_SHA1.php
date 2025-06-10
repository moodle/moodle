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
 * OAuth Signature Method HMAC SHA.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\lti;

/**
 * OAuth Signature Method HMAC SHA.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class OAuthSignatureMethod_HMAC_SHA1 extends OAuthSignatureMethod {

    /**
     * Get name.
     *
     * @return string
     */
    public function get_name() {
        return "HMAC-SHA1";
    }

    /**
     * Build signature.
     *
     * @param $request
     * @param $consumer
     * @param $token
     * @return string
     */
    public function build_signature($request, $consumer, $token) {
        global $oauthlastcomputedsignature;
        $oauthlastcomputedsignature = false;

        $basestring = $request->get_signature_base_string();
        $request->base_string = $basestring;

        $keyparts = [
            $consumer->secret,
            ($token) ? $token->secret : "",
        ];

        $keyparts = OAuthUtil::urlencode_rfc3986($keyparts);
        $key = implode('&', $keyparts);

        $computedsignature = base64_encode(hash_hmac('sha1', $basestring, $key, true));
        $oauthlastcomputedsignature = $computedsignature;

        return $computedsignature;
    }
}
