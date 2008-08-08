<?php
/**
 * repository_local class
 * This is a subclass of repository class
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_local extends repository{
    public $type = 'local';

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        // get the parameter from client side
        // $this->context can be used here.
    }
    public function print_login($ajax = true){
        global $SESSION;
        // TODO
        // Return file list in moodle
        // Also, this plugin should have ability to
        // upload files in user's computer, a iframe
        // need to be created. 
        return $this->get_listing();
    }
    public function get_listing($path = '/', $search = ''){
        global $SESSION;
        $ret = new stdclass;
        $ret->upload = array('name'=>'attachment', 'id'=>'', 'url'=>'');
        $ret->list  = array();
        // call file api get the list of the file
        $ret->list[] = array('title'=>'title','source'=>'download url', 'thumbnail'=>'url of thumbnail', 'date'=>'', 'size'=>'unknown');
        if(empty($ret)) {
            throw new repository_exception('emptyfilelist', 'repository_local');
        } else {
            return $ret;
        }
    }
    public function print_listing(){
        // will be used in non-javascript file picker
    }
    public function print_search(){
        return true;
    }
}
?>
