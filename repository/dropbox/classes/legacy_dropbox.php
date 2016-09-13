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
 * A helper class to access dropbox resources
 *
 * @since Moodle 2.0
 * @package    repository_dropbox
 * @copyright  2012 Marina Glancy
 * @copyright  2010 Dongsheng Cai
 * @author     Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/oauthlib.php');

/**
 * Authentication class to access Dropbox API
 *
 * @package    repository_dropbox
 * @copyright  2010 Dongsheng Cai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_dropbox_legacy_dropbox extends oauth_helper {
    /** @var string dropbox access type, can be dropbox or sandbox */
    private $mode = 'dropbox';
    /** @var string dropbox api url*/
    private $dropbox_api = 'https://api.dropbox.com/1';
    /** @var string dropbox content api url*/
    private $dropbox_content_api = 'https://api-content.dropbox.com/1';

    /**
     * Constructor for dropbox class
     *
     * @param array $args
     */
    function __construct($args) {
        parent::__construct($args);
    }

    /**
     * Get file listing from dropbox
     *
     * @param string $path
     * @param string $token
     * @param string $secret
     * @return array
     */
    public function get_listing($path='/', $token='', $secret='') {
        $url = $this->dropbox_api.'/metadata/'.$this->mode.$path;
        $content = $this->get($url, array(), $token, $secret);
        $data = json_decode($content);
        return $data;
    }

    /**
     * Prepares the filename to pass to Dropbox API as part of URL
     *
     * @param string $filepath
     * @return string
     */
    protected function prepare_filepath($filepath) {
        $info = pathinfo($filepath);
        $dirname = $info['dirname'];
        $basename = $info['basename'];
        $filepath = $dirname . rawurlencode($basename);
        if ($dirname != '/') {
            $filepath = $dirname . '/' . $basename;
            $filepath = str_replace("%2F", "/", rawurlencode($filepath));
        }
        return $filepath;
    }

    /**
     * Retrieves the default (64x64) thumbnail for dropbox file
     *
     * @throws moodle_exception when file could not be downloaded
     *
     * @param string $filepath local path in Dropbox
     * @param string $saveas path to file to save the result
     * @param int $timeout request timeout in seconds, 0 means no timeout
     * @return array with attributes 'path' and 'url'
     */
    public function get_thumbnail($filepath, $saveas, $timeout = 0) {
        $url = $this->dropbox_content_api.'/thumbnails/'.$this->mode.$this->prepare_filepath($filepath);
        if (!($fp = fopen($saveas, 'w'))) {
            throw new moodle_exception('cannotwritefile', 'error', '', $saveas);
        }
        $this->setup_oauth_http_options(array('timeout' => $timeout, 'file' => $fp, 'BINARYTRANSFER' => true));
        $result = $this->get($url);
        fclose($fp);
        if ($result === true) {
            return array('path'=>$saveas, 'url'=>$url);
        } else {
            unlink($saveas);
            throw new moodle_exception('errorwhiledownload', 'repository', '', $result);
        }
    }

    /**
     * Downloads a file from Dropbox and saves it locally
     *
     * @throws moodle_exception when file could not be downloaded
     *
     * @param string $filepath local path in Dropbox
     * @param string $saveas path to file to save the result
     * @param int $timeout request timeout in seconds, 0 means no timeout
     * @return array with attributes 'path' and 'url'
     */
    public function get_file($filepath, $saveas, $timeout = 0) {
        $url = $this->dropbox_content_api.'/files/'.$this->mode.$this->prepare_filepath($filepath);
        if (!($fp = fopen($saveas, 'w'))) {
            throw new moodle_exception('cannotwritefile', 'error', '', $saveas);
        }
        $this->setup_oauth_http_options(array('timeout' => $timeout, 'file' => $fp, 'BINARYTRANSFER' => true));
        $result = $this->get($url);
        fclose($fp);
        if ($result === true) {
            return array('path'=>$saveas, 'url'=>$url);
        } else {
            unlink($saveas);
            throw new moodle_exception('errorwhiledownload', 'repository', '', $result);
        }
    }

    /**
     * Returns direct link to Dropbox file
     *
     * @param string $filepath local path in Dropbox
     * @param int $timeout request timeout in seconds, 0 means no timeout
     * @return string|null information object or null if request failed with an error
     */
    public function get_file_share_link($filepath, $timeout = 0) {
        $url = $this->dropbox_api.'/shares/'.$this->mode.$this->prepare_filepath($filepath);
        $this->setup_oauth_http_options(array('timeout' => $timeout));
        $result = $this->post($url, array('short_url'=>0));
        if (!$this->http->get_errno()) {
            $data = json_decode($result);
            if (isset($data->url)) {
                return $data->url;
            }
        }
        return null;
    }

    /**
     * Sets Dropbox API mode (dropbox or sandbox, default dropbox)
     *
     * @param string $mode
     */
    public function set_mode($mode) {
        $this->mode = $mode;
    }
}
