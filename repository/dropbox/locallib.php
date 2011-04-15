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
 * dropbox class
 * A helper class to access dropbox resources
 *
 * @since 2.0
 * @package    repository
 * @subpackage dropbox
 * @copyright  2010 Dongsheng Cai
 * @author     Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/oauthlib.php');

class dropbox extends oauth_helper {
    /** dropbox access type, can be dropbox or sandbox */
    private $mode = 'dropbox';
    /** dropbox api url*/
    private $dropbox_api = 'http://api.dropbox.com/0';
    /** dropbox content api url*/
    private $dropbox_content_api = 'http://api-content.dropbox.com/0';

    function __construct($args) {
        parent::__construct($args);
    }
    /**
     * Get file listing from dropbox
     */
    public function get_listing($path='/', $token='', $secret='') {
        $url = $this->dropbox_api.'/metadata/'.$this->mode.$path;
        $content = $this->get($url, array(), $token, $secret);
        $data = json_decode($content);
        return $data;
    }

    /**
     * Get user account info
     */
    public function get_account_info($token, $secret) {
        $url = $this->dropbox_api.'/account/info';
        $content = $this->get($url, array(), $token, $secret);
        return $content;
    }

    /**
     * Download a file
     */
    public function get_file($filepath, $saveas) {
        $info = pathinfo($filepath);
        $dirname = $info['dirname'];
        $basename = $info['basename'];
        $filepath = $dirname . rawurlencode($basename);
        if ($dirname != '/') {
            $filepath = $dirname . '/' . $basename;
            $filepath = str_replace("%2F", "/", rawurlencode($filepath));
        }

        $url = $this->dropbox_content_api.'/files/'.$this->mode.$filepath;
        $content = $this->get($url, array());
        file_put_contents($saveas, $content);
        return array('path'=>$saveas, 'url'=>$url);
    }

    public function set_mode($mode) {
        $this->mode = $mode;
    }
}
