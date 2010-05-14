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
 * @package moodlecore
 * @subpackage repository
 * @copyright 2010 Dongsheng Cai
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/oauthlib.php');

class dropbox extends oauth_helper {
    private $mode = 'sandbox';
    private $dropbox_api = 'http://api.dropbox.com/0';
    private $dropbox_content_api = 'http://api-content.dropbox.com/0';
    function __construct($args) {
        parent::__construct($args);
    }
    public function get_listing($path='/', $token='', $secret='') {
        $url = $this->dropbox_api.'/metadata/'.$this->mode.$path;
        $content = $this->get($url, array(), $token, $secret);
        $data = json_decode($content);
        return $data;
    }
    public function get_account_info($token, $secret) {
        $url = $this->dropbox_api.'/account/info';
        $content = $this->get($url, array(), $token, $secret);
        return $content;
    }
    public function get_file($filepath, $saveas) {
        $url = 'http://api-content.dropbox.com/0/files/sandbox'.$filepath;
        $content = $this->get($url, array());
        file_put_contents($saveas, $content);
        return array('path'=>$saveas, 'url'=>$url);
    }
}
