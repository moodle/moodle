<?php
/**
 * repository_upload class
 * A subclass of repository, which is used to upload file
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/**
 *
 */
class repository_upload extends repository {

    /**
     *
     * @global <type> $SESSION
     * @global <type> $action
     * @global <type> $CFG
     * @param <type> $repositoryid
     * @param <type> $context
     * @param <type> $options
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        if($action=='upload'){
            $filepath = '/'.uniqid().'/';
            $this->info = repository_store_to_filepool('repo_upload_file', 'user_draft', $filepath);
        }
    }

    /**
     *
     * @global <type> $SESSION
     * @param <type> $ajax
     * @return <type>
     */
    public function print_login($ajax = true) {
        global $SESSION;
        return $this->get_listing();
    }

    /**
     *
     * @global <type> $CFG
     * @global <type> $action
     * @param <type> $path
     * @param <type> $search
     * @return <type>
     */
    public function get_listing($path='', $search='') {
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
     */
    public function print_listing() {
         // will be used in non-javascript file picker
    }

    /**
     *
     * @return <type>
     */
    public function print_search() {
        return true;
    }

    /**
     *
     * @return <type> 
     */
    public function get_name(){
        return get_string('repositoryname', 'repository_upload');;
    }
}
?>
