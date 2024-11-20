<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Handles interaction with jupyter api.
 *
 * Reference for the used jupyterhub and jupyterlab api's:
 * https://jupyterhub.readthedocs.io/en/stable/reference/rest-api.html
 * https://jupyter-server.readthedocs.io/en/latest/developers/rest-api.html
 *
 * @package     mod_jupyter
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_jupyter;

defined('MOODLE_INTERNAL') || die();
require($CFG->dirroot . '/mod/jupyter/vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * Handles interaction with jupyter api.
 *
 * @package mod_jupyter
 */
class jupyterhub {
    /**
     * Uploads notebook file to users jupyter instance.
     *
     * @param string $user current user's username
     * @param int $contextid activity context id
     * @param int $courseid id of the moodle course
     * @param int $instanceid activity instance id
     * @param int $autograded
     * @return string path to file on jupyterhub server
     * @throws ConnectException
     * @throws RequestException
     */
    public static function load_notebook(string $user, int $contextid, int $courseid, int $instanceid, int $autograded)
    : string {
        self::check_user_status($user);

        $fs = get_file_storage();
        $filearea = $autograded ? 'assignment' : 'package';
        $files = $fs->get_area_files($contextid, 'mod_jupyter', $filearea, 0, 'id', false);
        $file = reset($files);
        $filename = $file->get_filename();

        $client = new Client(['headers' => ['Authorization' => 'token ' . get_config('mod_jupyter', 'jupyterhub_api_token')]]);
        $baseurl = self::get_url();
        $route = "{$baseurl}/user/{$user}/api/contents";

        try {
            // Check if file is already there.
            $client->get("{$route}/{$courseid}/{$instanceid}/{$filename}", ['query' => ['content' => '0']]);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getCode() == 404) {

                // Jupyter api doesnt support creating directories recursively so we have to it like this.
                $client->put("{$route}/{$courseid}", ['json' => ['type' => 'directory']]);
                $client->put("{$route}/{$courseid}/{$instanceid}", ['json' => ['type' => 'directory']]);

                $client->put("{$route}/{$courseid}/{$instanceid}/{$filename}", [
                    'json' => [
                        'type' => 'file',
                        'format' => 'base64',
                        'content' => base64_encode($file->get_content()),
                    ]
                ]);
            } else {
                throw $e;
            }
        }

        return "/hub/user-redirect/lab/tree/{$courseid}/{$instanceid}/{$filename}";
    }

    /**
     * Check if user exists and spawn server
     * @param string $user current user's username
     * @throws ConnectException
     * @throws RequestException
     */
    private static function check_user_status(string $user) {
        $client = new Client(['headers' => ['Authorization' => 'token ' . get_config('mod_jupyter', 'jupyterhub_api_token')]]);
        $baseurl = self::get_url();
        $route = "{$baseurl}/hub/api/users/{$user}";

        // Check if user exists.
        try {
            $res = $client->get($route);
        } catch (RequestException $e) {
            // Create user if not found.
            if ($e->hasResponse() && $e->getCode() == 404) {
                $res = $client->post($route);
            } else {
                // For other errors we throw the exception.
                throw $e;
            }
        }

        // Spawn users server if not running.
        if (json_decode($res->getBody(), true)["server"] == null) {
            $res = $client->post($route . "/server");
        }
    }

    /**
     * Reset notebook server by reuploading default notebookfile.
     * @param string $user current user's username
     * @param int $contextid activity context id
     * @param int $courseid id of the moodle course
     * @param int $instanceid activity instance id
     * @param int $autograded
     * @throws RequestException
     * @throws ConnectException
     */
    public static function reset_notebook(string $user, int $contextid, int $courseid, int $instanceid, int $autograded) {
        $fs = get_file_storage();
        $filearea = $autograded ? 'assignment' : 'package';
        $files = $fs->get_area_files($contextid, 'mod_jupyter', $filearea, 0, 'id', false);
        $file = reset($files);
        $filename = $file->get_filename();

        $client = new Client([ 'headers' => ['Authorization' => 'token ' . get_config('mod_jupyter', 'jupyterhub_api_token')]]);
        $baseurl = self::get_url();
        $route = "{$baseurl}/user/{$user}/api/contents/{$courseid}/{$instanceid}/{$filename}";

        try {
            $client->patch($route, [
                'json' => [
                    'path' => "{$courseid}/{$instanceid}/" . date('Y-m-d-H:i:s', time()) . "_{$filename}"
                ]
            ]);
            $client->put($route, [
                'json' => [
                    'type' => 'file',
                    'format' => 'base64',
                    'content' => base64_encode($file->get_content()),
                ]
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getCode() == 404) {
                $client->put($route, [
                    'json' => [
                        'type' => 'file',
                        'format' => 'base64',
                        'content' => base64_encode($file->get_content()),
                    ]
                ]);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Return the notebook file associated to the given parameters.
     * @param string $user user name of the owner of the notebook
     * @param int $courseid activity course id
     * @param int $instanceid activity instance id
     * @param string $filename notebook file name
     * @return string returns the decoded notebook file contents
     * @throws RequestException
     * @throws ConnectException
     */
    public static function get_notebook(string $user, int $courseid, int $instanceid, string $filename) {
        $client = new Client(['headers' => ['Authorization' => 'token ' . get_config('mod_jupyter', 'jupyterhub_api_token')]]);
        $baseurl = self::get_url();
        $route = "{$baseurl}/user/{$user}/api/contents/{$courseid}/{$instanceid}/{$filename}";

        $res = $client->get($route, [
            'query' => [
                'content' => '1',
                'format' => 'base64',
                'type' => 'file'
            ]
        ]);
        $res = json_decode($res->getBody(), true);
        return base64_decode($res['content']);
    }

    /**
     * Get jupyterhub url from config.
     * @return string $baseurl
     */
    private static function get_url(): string {
        $baseurl = get_config('mod_jupyter', 'jupyterhub_url');

        if (getenv('IS_CONTAINER') == 'yes') {
            $baseurl = str_replace(['127.0.0.1', 'localhost'], 'host.docker.internal', $baseurl);
        }

        if (substr($baseurl, -1) == "/") {
            $baseurl = substr($baseurl, 0, -1);
        }

        return $baseurl;
    }
}
