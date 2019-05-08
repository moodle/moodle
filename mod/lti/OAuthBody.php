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
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

namespace moodle\mod\lti; // Using a namespace as the basicLTI module imports classes with the same names.

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/lti/OAuth.php');
require_once($CFG->dirroot . '/mod/lti/TrivialStore.php');

/**
 *
 * @param int $typeid LTI type ID.
 * @param string[] $scopes  Array of scopes which give permission for the current request.
 *
 * @return string|int|boolean  The OAuth consumer key, the LTI type ID for the validated bearer token,
                               true for requests not requiring a scope, otherwise false.
 */
function get_oauth_key_from_headers($typeid = null, $scopes = null) {
    global $DB;

    $now = time();

    $requestheaders = OAuthUtil::get_headers();

    if (isset($requestheaders['Authorization'])) {
        if (substr($requestheaders['Authorization'], 0, 6) == "OAuth ") {
            $headerparameters = OAuthUtil::split_header($requestheaders['Authorization']);

            return format_string($headerparameters['oauth_consumer_key']);
        } else if (empty($scopes)) {
            return true;
        } else if (substr($requestheaders['Authorization'], 0, 7) == 'Bearer ') {
            $tokenvalue = trim(substr($requestheaders['Authorization'], 7));
            $conditions = array('token' => $tokenvalue);
            if (!empty($typeid)) {
                $conditions['typeid'] = intval($typeid);
            }
            $token = $DB->get_record('lti_access_tokens', $conditions);
            if ($token) {
                // Log token access.
                $DB->set_field('lti_access_tokens', 'lastaccess', $now, array('id' => $token->id));
                $permittedscopes = json_decode($token->scope);
                if ((intval($token->validuntil) > $now) && !empty(array_intersect($scopes, $permittedscopes))) {
                    return intval($token->typeid);
                }
            }
        }
    }
    return false;
}

function handle_oauth_body_post($oauthconsumerkey, $oauthconsumersecret, $body, $requestheaders = null) {

    if ($requestheaders == null) {
        $requestheaders = OAuthUtil::get_headers();
    }

    // Must reject application/x-www-form-urlencoded.
    if (isset($requestheaders['Content-type'])) {
        if ($requestheaders['Content-type'] == 'application/x-www-form-urlencoded' ) {
            throw new OAuthException("OAuth request body signing must not use application/x-www-form-urlencoded");
        }
    }

    if (isset($requestheaders['Authorization']) && (substr($requestheaders['Authorization'], 0, 6) == "OAuth ")) {
        $headerparameters = OAuthUtil::split_header($requestheaders['Authorization']);
        $oauthbodyhash = $headerparameters['oauth_body_hash'];
    }

    if ( ! isset($oauthbodyhash)  ) {
        throw new OAuthException("OAuth request body signing requires oauth_body_hash body");
    }

    // Verify the message signature.
    $store = new TrivialOAuthDataStore();
    $store->add_consumer($oauthconsumerkey, $oauthconsumersecret);

    $server = new OAuthServer($store);

    $method = new OAuthSignatureMethod_HMAC_SHA1();
    $server->add_signature_method($method);
    $request = OAuthRequest::from_request();

    try {
        $server->verify_request($request);
    } catch (\Exception $e) {
        $message = $e->getMessage();
        throw new OAuthException("OAuth signature failed: " . $message);
    }

    $postdata = $body;

    $hash = base64_encode(sha1($postdata, true));

    if ( $hash != $oauthbodyhash ) {
        throw new OAuthException("OAuth oauth_body_hash mismatch");
    }

    return $postdata;
}
