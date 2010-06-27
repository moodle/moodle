<?php
/**
 * Picasa Portfolio Plugin
 *
 * @author Dan Poltawski <talktodan@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->libdir.'/portfolio/plugin.php');
require_once($CFG->libdir.'/googleapi.php');

class portfolio_plugin_picasa extends portfolio_plugin_push_base {
    private $sessionkey;

    public function supported_formats() {
        return array(PORTFOLIO_FORMAT_IMAGE, PORTFOLIO_FORMAT_VIDEO);
    }

    public static function get_name() {
        return get_string('pluginname', 'portfolio_picasa');
    }

    public function prepare_package() {
        // we send the files as they are, no prep required
        return true;
    }

    public function get_interactive_continue_url(){
        return 'http://picasaweb.google.com/';
    }

    public function expected_time($callertime) {
        return $callertime;
    }

    public function send_package() {
        if(!$this->sessionkey){
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_picasa');
        }

        $picasa = new google_picasa(new google_authsub($this->sessionkey));

        foreach ($this->exporter->get_tempfiles() as $file) {

            if(!$picasa->send_file($file)){
                throw new portfolio_plugin_exception('sendfailed', 'portfolio_picasa', $file->get_filename());
            }
        }
    }

    public function steal_control($stage) {
        global $CFG;
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }

        $sesskey = google_picasa::get_sesskey($this->get('user')->id);

        if($sesskey){
            try{
                $gauth = new google_authsub($sesskey);
                $this->sessionkey = $sesskey;
                return false;
            }catch(Exception $e){
                // sesskey is not valid, delete store and re-auth
                google_picasa::delete_sesskey($this->get('user')->id);
            }
        }

        return google_authsub::login_url($CFG->wwwroot.'/portfolio/add.php?postcontrol=1&id=' . $this->exporter->get('id') . '&sesskey=' . sesskey(), google_picasa::REALM);
    }

    public function post_control($stage, $params) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return;
        }

        if(!array_key_exists('token', $params)){
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_picasa');
        }

        // we now have our auth token, get a session token..
        $gauth = new google_authsub(false, $params['token']);

        $this->sessionkey = $gauth->get_sessiontoken();

        google_picasa::set_sesskey($this->sessionkey, $this->get('user')->id);
    }

    public static function allows_multiple_instances() {
        return false;
    }
}

/**
 * Registers to the user_deleted event to revoke any
 * subauth tokens we have from them
 *
 * @param $user user object
 * @return boolean true in all cases as its only minor cleanup
 */
function portfolio_picasa_user_deleted($user){
    // it is only by luck that the user prefstill exists now?
    // We probably need a pre-delete event?
    if($sesskey = google_picasa::get_sesskey($user->id)){
        try{
            $gauth = new google_authsub($sesskey);

            $gauth->revoke_session_token();
        }catch(Exception $e){
            // we don't care that much about success- just being good
            // google api citzens
            return true;
        }
    }

    return true;
}
