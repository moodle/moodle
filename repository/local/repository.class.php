<?php
/**
 * repository_local class
 * This is a subclass of repository class
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_local extends repository{

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        // TODO:
        // get the parameter from client side
        // $this->context can be used here.
        // When user upload a file, $action == 'upload'
        // You can use $_FILES to find that file
    }

    public function print_login($ajax = true){
        global $SESSION;
        // TODO
        // Return file list in moodle
        return $this->get_listing();
    }
    public function get_listing($path = '/', $search = ''){
        global $SESSION;
        $ret = new stdclass;

        // this statement tells file picker to load files dramanically.
        $ret->dynload = true;

        // defina upload form in file picker
        // Use ajax upload file
        $ret->upload = array('name'=>get_string('attachment', 'repository'), 'id'=>'repo-form');
        $ret->list  = array();

        // TODO: set path and file area for folders, for example
        //
        // $ret->list[] = array('title'=>'folder1', 'size'=>0, 
        //          'date'=>'', 'path'=>'/', 'file_area'=>'course_files');
        //
        // call FILE API get the list of the file
        //
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
