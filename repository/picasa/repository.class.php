<?php
/**
 * Picasa Repository Plugin
 *
 * @author Dan Poltawski <talktodan@gmail.com>
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->libdir.'/googleapi.php');

class repository_picasa extends repository {
    private $subauthtoken = '';

    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $USER;
        parent::__construct($repositoryid, $context, $options);

        // TODO: I wish there was somewhere we could explicitly put this outside of constructor..
        $googletoken = optional_param('token', false, PARAM_RAW);
        if($googletoken){
            $gauth = new google_authsub(false, $googletoken); // will throw exception if fails
            google_picasa::set_sesskey($gauth->get_sessiontoken(), $USER->id);
        }

        # fixme - we are not checking login before all functions in the repo api.. eg search
        # MDL-17474
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

    public function print_login($ajax = true){ 
        global $CFG;
        if($ajax){
            $ret = array(); 
            $popup_btn = new stdclass; 
            $popup_btn->type = 'popup'; 
            $returnurl = $CFG->wwwroot.'/repository/ws.php?callback=yes&repo_id='.$this->id;
            $popup_btn->url = google_authsub::login_url($returnurl, google_picasa::REALM);
            $ret['login'] = array($popup_btn); 
            return $ret; 
        }
    }

    public function get_listing($path='') {
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
        return get_string('repositoryname', 'repository_picasa');
    }
}

// Icon for this plugin retrieved from http://www.iconspedia.com/icon/picasa-2711.html
// Where the license is said documented to be Free
