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
 * Provides {@link flickr_client} class.
 *
 * @package     core
 * @copyright   2017 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/oauthlib.php');

/**
 * Simple Flickr API client implementing the features needed by Moodle
 *
 * @copyright 2017 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class flickr_client extends oauth_helper {

    /**
     * Base URL for Flickr OAuth 1.0 API calls.
     */
    const OAUTH_ROOT = 'https://www.flickr.com/services/oauth';

    /**
     * Base URL for Flickr REST API calls.
     */
    const REST_ROOT = 'https://api.flickr.com/services/rest';

    /**
     * Base URL for Flickr Upload API call.
     */
    const UPLOAD_ROOT = 'https://up.flickr.com/services/upload/';

    /**
     * Set up OAuth and initialize the client.
     *
     * The callback URL specified here will override the one specified in the
     * auth flow defined at Flickr Services.
     *
     * @param string $consumerkey
     * @param string $consumersecret
     * @param moodle_url|string $callbackurl
     */
    public function __construct($consumerkey, $consumersecret, $callbackurl = '') {

        parent::__construct([
            'api_root' => self::OAUTH_ROOT,
            'oauth_consumer_key' => $consumerkey,
            'oauth_consumer_secret' => $consumersecret,
            'oauth_callback' => $callbackurl,
        ]);
    }

    /**
     * Temporarily store the request token secret in the session.
     *
     * The request token secret is returned by the oauth request_token method.
     * It needs to be stored in the session before the user is redirected to
     * the Flickr to authorize the client. After redirecting back, this secret
     * is used for exchanging the request token with the access token.
     *
     * The identifiers help to avoid collisions between multiple calls to this
     * method from different plugins in the same session. They are used as the
     * session cache identifiers. Provide an associative array identifying the
     * particular method call. At least, the array must contain the 'caller'
     * with the caller's component name. Use additional items if needed.
     *
     * @param array $identifiers Identification of the call
     * @param string $secret
     */
    public function set_request_token_secret(array $identifiers, $secret) {

        if (empty($identifiers) || empty($identifiers['caller'])) {
            throw new coding_exception('Invalid call identification');
        }

        $cache = cache::make_from_params(cache_store::MODE_SESSION, 'core', 'flickrclient', $identifiers);
        $cache->set('request_token_secret', $secret);
    }

    /**
     * Returns previously stored request token secret.
     *
     * See {@link self::set_request_token_secret()} for more details on the
     * $identifiers argument.
     *
     * @param array $identifiers Identification of the call
     * @return string|bool False on error, string secret otherwise.
     */
    public function get_request_token_secret(array $identifiers) {

        if (empty($identifiers) || empty($identifiers['caller'])) {
            throw new coding_exception('Invalid call identification');
        }

        $cache = cache::make_from_params(cache_store::MODE_SESSION, 'core', 'flickrclient', $identifiers);

        return $cache->get('request_token_secret');
    }

    /**
     * Call a Flickr API method.
     *
     * @param string $function API function name like 'flickr.photos.getSizes' or just 'photos.getSizes'
     * @param array $params Additional API call arguments.
     * @param string $method HTTP method to use (GET or POST).
     * @return object|bool Response as returned by the Flickr or false on invalid authentication
     */
    public function call($function, array $params = [], $method = 'GET') {

        if (strpos($function, 'flickr.') !== 0) {
            $function = 'flickr.'.$function;
        }

        $params['method'] = $function;
        $params['format'] = 'json';
        $params['nojsoncallback'] = 1;

        $rawresponse = $this->request($method, self::REST_ROOT, $params);
        $response = json_decode($rawresponse);

        if (!is_object($response) || !isset($response->stat)) {
            throw new moodle_exception('flickr_api_call_failed', 'core_error', '', $rawresponse);
        }

        if ($response->stat === 'ok') {
            return $response;

        } else if ($response->stat === 'fail' && $response->code == 98) {
            // Authentication failure, give the caller a chance to re-authenticate.
            return false;

        } else {
            throw new moodle_exception('flickr_api_call_failed', 'core_error', '', $response);
        }

        return $response;
    }

    /**
     * Return the URL to fetch the given photo from.
     *
     * Flickr photos are distributed via farm servers staticflickr.com in
     * various sizes (resolutions). The method tries to find the source URL of
     * the photo in the highest possible resolution. Results are cached so that
     * we do not need to query the Flickr API over and over again.
     *
     * @param string $photoid Flickr photo identifier
     * @return string URL
     */
    public function get_photo_url($photoid) {

        $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'core', 'flickrclient');

        $url = $cache->get('photourl_'.$photoid);

        if ($url === false) {
            $response = $this->call('photos.getSizes', ['photo_id' => $photoid]);
            // Sizes are returned from smallest to greatest.
            if (!empty($response->sizes->size) && is_array($response->sizes->size)) {
                while ($bestsize = array_pop($response->sizes->size)) {
                    if (isset($bestsize->source)) {
                        $url = $bestsize->source;
                        break;
                    }
                }
            }
        }

        if ($url === false) {
            throw new repository_exception('cannotdownload', 'repository');

        } else {
            $cache->set('photourl_'.$photoid, $url);
        }

        return $url;
    }

    /**
     * Upload a photo from Moodle file pool to Flickr.
     *
     * Optional meta information are title, description, tags, is_public,
     * is_friend, is_family, safety_level, content_type and hidden.
     * See {@link https://www.flickr.com/services/api/upload.api.html}.
     *
     * Upload can't be asynchronous because then the query would not return the
     * photo ID which we need to add the photo to a photoset (album)
     * eventually.
     *
     * @param stored_file $photo stored in Moodle file pool
     * @param array $meta optional meta information
     * @return int|bool photo id, false on authentication failure
     */
    public function upload(stored_file $photo, array $meta = []) {

        $args = [
            'title' => isset($meta['title']) ? $meta['title'] : null,
            'description' => isset($meta['description']) ? $meta['description'] : null,
            'tags' => isset($meta['tags']) ? $meta['tags'] : null,
            'is_public' => isset($meta['is_public']) ? $meta['is_public'] : 0,
            'is_friend' => isset($meta['is_friend']) ? $meta['is_friend'] : 0,
            'is_family' => isset($meta['is_family']) ? $meta['is_family'] : 0,
            'safety_level' => isset($meta['safety_level']) ? $meta['safety_level'] : 1,
            'content_type' => isset($meta['content_type']) ? $meta['content_type'] : 1,
            'hidden' => isset($meta['hidden']) ? $meta['hidden'] : 2,
        ];

        $this->sign_secret = $this->consumer_secret.'&'.$this->access_token_secret;
        $params = $this->prepare_oauth_parameters(self::UPLOAD_ROOT, ['oauth_token' => $this->access_token] + $args, 'POST');

        $params['photo'] = $photo;

        $response = $this->http->post(self::UPLOAD_ROOT, $params);

        if ($response) {
            $xml = simplexml_load_string($response);

            if ((string)$xml['stat'] === 'ok') {
                return (int)$xml->photoid;

            } else if ((string)$xml['stat'] === 'fail' && (int)$xml->err['code'] == 98) {
                // Authentication failure.
                return false;

            } else {
                throw new moodle_exception('flickr_upload_failed', 'core_error', '',
                    ['code' => (int)$xml->err['code'], 'message' => (string)$xml->err['msg']]);
            }

        } else {
            throw new moodle_exception('flickr_upload_error', 'core_error', '', null, $response);
        }
    }
}
