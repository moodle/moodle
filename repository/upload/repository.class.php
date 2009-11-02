<?php
/**
 * repository_upload class
 * A subclass of repository, which is used to upload file
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_upload extends repository {

    /**
     *
     * @global object $SESSION
     * @global string $action
     * @global object $CFG
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $_FILES, $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        $itemid = optional_param('itemid', '', PARAM_INT);
        $filepath = optional_param('savepath', '/', PARAM_PATH);
        if($action=='upload'){
            $this->info = repository::store_to_filepool('repo_upload_file', 'user_draft', $filepath, $itemid);
        }
    }

    /**
     *
     * @global object $SESSION
     * @param boolean $ajax
     * @return mixed
     */
    public function print_login($ajax = true) {
        global $SESSION;
        return $this->get_listing();
    }

    /**
     *
     * @global object $CFG
     * @global string $action
     * @param mixed $path
     * @param string $search
     * @return array
     */
    public function get_listing($path='', $page='') {
        global $CFG, $action;
        if($action=='upload'){
            return $this->info;
        }else{
            $ret = array();
            $ret['nologin']  = true;
            $ret['nosearch'] = true;
            // define upload form in file picker
            $ret['upload'] = array('label'=>get_string('attachment', 'repository'), 'id'=>'repo-form');
            $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary
            $ret['list'] = array();
            $ret['dynload'] = false;
            return $ret;
        }
    }

    /**
     *
     * @return string
     */
    public function get_name(){
        return get_string('repositoryname', 'repository_upload');
    }
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }
}

