<?php
/**
 * repository_upload class
 * A subclass of repository, which is used to upload file
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_upload extends repository {

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        if($action=='upload'){
            $this->info = repository_store_to_filepool('repo_upload_file');
        }
    }

    public function print_login($ajax = true) {
        global $SESSION;
        return $this->get_listing();
    }

    public function get_listing($path='', $search='') {
        global $CFG, $action;
        if($action=='upload'){
            return $this->info;
        }else{
            $ret = array();
            $ret['nologin'] = true;
            // define upload form in file picker
            $ret['upload'] = array('label'=>get_string('attachment', 'repository'), 'id'=>'repo-form');
            $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary
            $ret['list'] = array();
            $ret['dynload'] = false;
            return $ret;
        }
    }

    public function print_listing() {
    }

    public function print_search() {
        return true;
    }

    public static function has_admin_config() {
        return true;
    }

    public static function get_option_names() {
        return array();
    }

    // empty function is necessary to make it possible to edit the name of the repository
    public function admin_config_form(&$mform) {
    }
}
?>
