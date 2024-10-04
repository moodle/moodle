<?php

namespace IMSGlobal\LTI\OAuth;

/**
 * Class to represent an %OAuth HMAC_SHA256 signature method
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 2015-11-30
 * @license https://opensource.org/licenses/MIT The MIT License
 */
/**
 * The HMAC-SHA256 signature method uses the HMAC-SHA256 signature algorithm as defined in [RFC6234]
 * where the Signature Base String is the text and the key is the concatenated values (each first
 * encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
 * character (ASCII code 38) even if empty.
 */
#[\AllowDynamicProperties]
class OAuthSignatureMethod_HMAC_SHA256 extends OAuthSignatureMethod {

    function get_name() {
        return "HMAC-SHA256";
    }

    public function build_signature($request, $consumer, $token) {

        $base_string = $request->get_signature_base_string();
        $request->base_string = $base_string;

        $key_parts = array(
          $consumer->secret,
          ($token) ? $token->secret : ""
        );

        $key_parts = OAuthUtil::urlencode_rfc3986($key_parts);
        $key = implode('&', $key_parts);

        return base64_encode(hash_hmac('sha256', $base_string, $key, true));

    }

}
