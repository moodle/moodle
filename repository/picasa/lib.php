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
 * Picasa Repository Plugin
 *
 * @since 2.0
 * @package    repository
 * @subpackage picasa
 * @copyright  2009 Dan Poltawski
 * @author     Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/googleapi.php');

class repository_picasa extends repository {
    private $subauthtoken = '';

    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        global $USER;
        parent::__construct($repositoryid, $context, $options);

        // TODO: I wish there was somewhere we could explicitly put this outside of constructor..
        $googletoken = optional_param('token', false, PARAM_RAW);
        if($googletoken){
            $gauth = new google_authsub(false, $googletoken); // will throw exception if fails
            google_picasa::set_sesskey($gauth->get_sessiontoken(), $USER->id);
        }
        $this->check_login();
    }

    public function check_login() {
        global $USER;

        $sesskey = google_picasa::get_sesskey($USER->id);

        if($sesskey){
            try{
                $gauth = new google_authsub($sesskey);
                $this->subauthtoken = $sesskey;
                return true;
            }catch(Exception $e){
                // sesskey is not valid, delete store and re-auth
                google_picasa::delete_sesskey($USER->id);
            }
        }

        return false;
    }

    public function print_login(){
        global $CFG;
        $returnurl = $CFG->wwwroot.'/repository/repository_callback.php?callback=yes&repo_id='.$this->id;
        $authurl = google_authsub::login_url($returnurl, google_picasa::REALM);
        if($this->options['ajax']){
            $ret = array();
            $popup_btn = new stdClass();
            $popup_btn->type = 'popup';
            $popup_btn->url = $authurl;
            $ret['login'] = array($popup_btn);
            return $ret;
        } else {
            echo '<a target="_blank" href="'.$authurl.'">Login</a>';
        }
    }

    public function get_listing($path='', $page = '') {
        $picasa = new google_picasa(new google_authsub($this->subauthtoken));

        $ret = array();
        $ret['dynload'] = true;
        $ret['list'] = $picasa->get_file_list($path);
        return $ret;
    }

    public function search($query){
        $picasa = new google_picasa(new google_authsub($this->subauthtoken));

        $ret = array();
        $ret['list'] =  $picasa->do_photo_search($query);
        return $ret;
    }

    public function logout(){
        global $USER;

        $token = google_picasa::get_sesskey($USER->id);

        $gauth = new google_authsub($token);
        // revoke token from google
        $gauth->revoke_session_token();

        google_picasa::delete_sesskey($USER->id);
        $this->subauthtoken = '';

        return parent::logout();
    }

    public function get_name(){
        return get_string('pluginname', 'repository_picasa');
    }
    public function supported_filetypes() {
        return array('web_image');
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
}

// Icon for this plugin retrieved from http://www.iconspedia.com/icon/picasa-2711.html
// Where the license is said documented to be Free
