<?php
/**
 * Google Docs Plugin
 *
 * @author Dan Poltawski <talktodan@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->libdir.'/googleapi.php');

class repository_googledocs extends repository {
    private $subauthtoken = '';

    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $USER;
        parent::__construct($repositoryid, $context, $options);

        // TODO: I wish there was somewhere we could explicitly put this outside of constructor..
        $googletoken = optional_param('token', false, PARAM_RAW);
        if($googletoken){
            $gauth = new google_authsub(false, $googletoken); // will throw exception if fails
            google_docs::set_sesskey($gauth->get_sessiontoken(), $USER->id);
        }
        $this->check_login();
    }

    public function check_login() {
        global $USER;

        $sesskey = google_docs::get_sesskey($USER->id);

        if($sesskey){
            try{
                $gauth = new google_authsub($sesskey);
                $this->subauthtoken = $sesskey;
                return true;
            }catch(Exception $e){
                // sesskey is not valid, delete store and re-auth
                google_docs::delete_sesskey($USER->id);
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
            $popup_btn->url = google_authsub::login_url($returnurl, google_docs::REALM);
            $ret['login'] = array($popup_btn);
            return $ret;
        }
    }

    public function get_listing($path='', $page = '') {
        $gdocs = new google_docs(new google_authsub($this->subauthtoken));

        $ret = array();
        $ret['dynload'] = true;
        $ret['list'] = $gdocs->get_file_list();
        return $ret;
    }

    public function search($query){
        $gdocs = new google_docs(new google_authsub($this->subauthtoken));

        $ret = array();
        $ret['dynload'] = true;
        $ret['list'] = $gdocs->get_file_list($query);
        return $ret;
    }

    public function logout(){
        global $USER;

        $token = google_docs::get_sesskey($USER->id);

        $gauth = new google_authsub($token);
        // revoke token from google
        $gauth->revoke_session_token();

        google_docs::delete_sesskey($USER->id);
        $this->subauthtoken = '';

        return parent::logout();
    }

    public function get_name(){
        return get_string('repositoryname', 'repository_googledocs');
    }

    public function get_file($url, $file) {
        global $CFG;
        $path = $this->prepare_file($file);

        $fp = fopen($path, 'w');
        $gdocs = new google_docs(new google_authsub($this->subauthtoken));
        $gdocs->download_file($url, $fp);

        return $path;
    }

    public function supported_filetypes() {
       return array('document');
    }
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }
}
//Icon from: http://www.iconspedia.com/icon/google-2706.html
