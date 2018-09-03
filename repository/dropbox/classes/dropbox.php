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
 * Dropbox V2 API.
 *
 * @since       Moodle 3.2
 * @package     repository_dropbox
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_dropbox;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/oauthlib.php');

/**
 * Dropbox V2 API.
 *
 * @package     repository_dropbox
 * @copyright   Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dropbox extends \oauth2_client {

    /**
     * Create the DropBox API Client.
     *
     * @param   string      $key        The API key
     * @param   string      $secret     The API secret
     * @param   string      $callback   The callback URL
     */
    public function __construct($key, $secret, $callback) {
        parent::__construct($key, $secret, $callback, '');
    }

    /**
     * Returns the auth url for OAuth 2.0 request.
     *
     * @return string the auth url
     */
    protected function auth_url() {
        return 'https://www.dropbox.com/oauth2/authorize';
    }

    /**
     * Returns the token url for OAuth 2.0 request.
     *
     * @return string the auth url
     */
    protected function token_url() {
        return 'https://api.dropboxapi.com/oauth2/token';
    }

    /**
     * Return the constructed API endpoint URL.
     *
     * @param   string      $endpoint   The endpoint to be contacted
     * @return  moodle_url              The constructed API URL
     */
    protected function get_api_endpoint($endpoint) {
        return new \moodle_url('https://api.dropboxapi.com/2/' . $endpoint);
    }

    /**
     * Return the constructed content endpoint URL.
     *
     * @param   string      $endpoint   The endpoint to be contacted
     * @return  moodle_url              The constructed content URL
     */
    protected function get_content_endpoint($endpoint) {
        return new \moodle_url('https://api-content.dropbox.com/2/' . $endpoint);
    }

    /**
     * Make an API call against the specified endpoint with supplied data.
     *
     * @param   string      $endpoint   The endpoint to be contacted
     * @param   array       $data       Any data to pass to the endpoint
     * @return  object                  Content decoded from the endpoint
     */
    protected function fetch_dropbox_data($endpoint, $data = []) {
        $url = $this->get_api_endpoint($endpoint);
        $this->cleanopt();
        $this->resetHeader();

        if ($data === null) {
            // Some API endpoints explicitly expect a data submission of 'null'.
            $options['CURLOPT_POSTFIELDS'] = 'null';
        } else {
            $options['CURLOPT_POSTFIELDS'] = json_encode($data);
        }
        $options['CURLOPT_POST'] = 1;
        $this->setHeader('Content-Type: application/json');

        $response = $this->request($url, $options);
        $result = json_decode($response);

        $this->check_and_handle_api_errors($result);

        if ($this->has_additional_results($result)) {
            // Any API endpoint returning 'has_more' will provide a cursor, and also have a matching endpoint suffixed
            // with /continue which takes that cursor.
            if (preg_match('_/continue$_', $endpoint) === 0) {
                // Only add /continue if it is not already present.
                $endpoint .= '/continue';
            }

            // Fetch the next page of results.
            $additionaldata = $this->fetch_dropbox_data($endpoint, [
                    'cursor' => $result->cursor,
                ]);

            // Merge the list of entries.
            $result->entries = array_merge($result->entries, $additionaldata->entries);
        }

        if (isset($result->has_more)) {
            // Unset the cursor and has_more flags.
            unset($result->cursor);
            unset($result->has_more);
        }

        return $result;
    }

    /**
     * Whether the supplied result is paginated and not the final page.
     *
     * @param   object      $result     The result of an operation
     * @return  boolean
     */
    public function has_additional_results($result) {
        return !empty($result->has_more) && !empty($result->cursor);
    }

    /**
     * Fetch content from the specified endpoint with the supplied data.
     *
     * @param   string      $endpoint   The endpoint to be contacted
     * @param   array       $data       Any data to pass to the endpoint
     * @return  string                  The returned data
     */
    protected function fetch_dropbox_content($endpoint, $data = []) {
        $url = $this->get_content_endpoint($endpoint);
        $this->cleanopt();
        $this->resetHeader();

        $options['CURLOPT_POST'] = 1;
        $this->setHeader('Content-Type: ');
        $this->setHeader('Dropbox-API-Arg: ' . json_encode($data));

        $response = $this->request($url, $options);

        $this->check_and_handle_api_errors($response);
        return $response;
    }

    /**
     * Check for an attempt to handle API errors.
     *
     * This function attempts to deal with errors as per
     * https://www.dropbox.com/developers/documentation/http/documentation#error-handling.
     *
     * @param   mixed      $data       The returned content.
     * @throws  moodle_exception
     */
    protected function check_and_handle_api_errors($data) {
        if ($this->info['http_code'] == 200) {
            // Dropbox only returns errors on non-200 response codes.
            return;
        }

        switch($this->info['http_code']) {
            case 400:
                // Bad input parameter. Error message should indicate which one and why.
                throw new \coding_exception('Invalid input parameter passed to DropBox API.');
                break;
            case 401:
                // Bad or expired token. This can happen if the access token is expired or if the access token has been
                // revoked by Dropbox or the user. To fix this, you should re-authenticate the user.
                throw new authentication_exception('Authentication token expired');
                break;
            case 409:
                // Endpoint-specific error. Look to the JSON response body for the specifics of the error.
                throw new \coding_exception('Endpoint specific error: ' . $data->error_summary);
                break;
            case 429:
                // Your app is making too many requests for the given user or team and is being rate limited. Your app
                // should wait for the number of seconds specified in the "Retry-After" response header before trying
                // again.
                throw new rate_limit_exception();
                break;
            default:
                break;
        }

        if ($this->info['http_code'] >= 500 && $this->info['http_code'] < 600) {
            throw new \invalid_response_exception($this->info['http_code'] . ": " . $data);
        }
    }

    /**
     * Get file listing from dropbox.
     *
     * @param   string      $path       The path to query
     * @return  object                  The returned directory listing, or null on failure
     */
    public function get_listing($path = '') {
        if ($path === '/') {
            $path = '';
        }

        $data = $this->fetch_dropbox_data('files/list_folder', [
                'path' => $path,
            ]);

        return $data;
    }

    /**
     * Get file search results from dropbox.
     *
     * @param   string      $query      The search query
     * @return  object                  The returned directory listing, or null on failure
     */
    public function search($query = '') {
        $data = $this->fetch_dropbox_data('files/search', [
                'path' => '',
                'query' => $query,
            ]);

        return $data;
    }

    /**
     * Whether the entry is expected to have a thumbnail.
     * See docs at https://www.dropbox.com/developers/documentation/http/documentation#files-get_thumbnail.
     *
     * @param   object      $entry      The file entry received from the DropBox API
     * @return  boolean                 Whether dropbox has a thumbnail available
     */
    public function supports_thumbnail($entry) {
        if ($entry->{".tag"} !== "file") {
            // Not a file. No thumbnail available.
            return false;
        }

        // Thumbnails are available for files under 20MB with file extensions jpg, jpeg, png, tiff, tif, gif, and bmp.
        if ($entry->size > 20 * 1024 * 1024) {
            return false;
        }

        $supportedtypes = [
                'jpg'   => true,
                'jpeg'  => true,
                'png'   => true,
                'tiff'  => true,
                'tif'   => true,
                'gif'   => true,
                'bmp'   => true,
            ];

        $extension = substr($entry->path_lower, strrpos($entry->path_lower, '.') + 1);
        return isset($supportedtypes[$extension]) && $supportedtypes[$extension];
    }

    /**
     * Retrieves the thumbnail for the content, as supplied by dropbox.
     *
     * @param   string      $path       The path to fetch a thumbnail for
     * @return  string                  Thumbnail image content
     */
    public function get_thumbnail($path) {
        $content = $this->fetch_dropbox_content('files/get_thumbnail', [
                'path' => $path,
            ]);

        return $content;
    }

    /**
     * Fetch a valid public share link for the specified file.
     *
     * @param   string      $id         The file path or file id of the file to fetch information for.
     * @return  object                  An object containing the id, path, size, and URL of the entry
     */
    public function get_file_share_info($id) {
        // Attempt to fetch any existing shared link first.
        $data = $this->fetch_dropbox_data('sharing/list_shared_links', [
                'path'      => $id,
            ]);

        if (isset($data->links)) {
            $link = reset($data->links);
            if (isset($link->{".tag"}) && $link->{".tag"} === "file") {
                return $this->normalize_file_share_info($link);
            }
        }

        // No existing link available.
        // Create a new one.
        $link = $this->fetch_dropbox_data('sharing/create_shared_link_with_settings', [
                'path'      => $id,
                'settings'  => [
                    'requested_visibility'  => 'public',
                ],
            ]);

        if (isset($link->{".tag"}) && $link->{".tag"} === "file") {
            return $this->normalize_file_share_info($link);
        }

        // Some kind of error we don't know how to handle at this stage.
        return null;
    }

    /**
     * Normalize the file share info.
     *
     * @param   object $entry   Information retrieved from share endpoints
     * @return  object          Normalized entry information to store as repository information
     */
    protected function normalize_file_share_info($entry) {
        return (object) [
                'id'    => $entry->id,
                'path'  => $entry->path_lower,
                'url'   => $entry->url,
            ];
    }

    /**
     * Process the callback.
     */
    public function callback() {
        $this->log_out();
        $this->is_logged_in();
    }

    /**
     * Revoke the current access token.
     *
     * @return string
     */
    public function logout() {
        try {
            $this->fetch_dropbox_data('auth/token/revoke', null);
        } catch(authentication_exception $e) {
            // An authentication_exception may be expected if the token has
            // already expired.
        }
    }
}
