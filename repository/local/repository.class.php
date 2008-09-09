<?php
/**
 * repository_local class
 * This is a subclass of repository class
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_local extends repository {

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        // TODO:
        // get the parameter from client side
        // $this->context can be used here.
        // When user upload a file, $action == 'upload'
        // You can use $_FILES to find that file
    }

    public function print_login($ajax = true) {
        global $SESSION;
        // TODO
        // Return file list in moodle
        return $this->get_listing();
    }

    private function _encode_path($filearea, $path, $visiblename) {
        return array('path'=>serialize(array($filearea, $path)), 'name'=>$visiblename);
    }

    private function _decode_path($path) {
        $filearea = '';
        $path = '';
        if (($file = unserialize($path)) !== false) {
            $filearea = $file[0];
            $path = $file[1];
        }
        return array('filearea' => $filearea, 'path' => $path);
    }

    public function get_listing($encodedpath = '', $search = '') {
        global $CFG;
        $ret = array();

        // no login required
        $ret['nologin'] = true;
        // todo: link to file manager
        $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary
       
        $browser = get_file_browser();
        $itemid = null;
        $filename = null;
        $filearea = null;
        $path = '/';

        if ($encodedpath != '') {
            list($filearea, $path) = $this->_decode_path($encodedpath);
        }
        
        $count = 0;

        if ($fileinfo = $browser->get_file_info($this->context, $filearea, $itemid, $path, $filename)) {
            $ret['path'] = array();
            $params = $fileinfo->get_params();
            $filearea = $params['filearea'];
            $ret['path'][] = $this->_encode_path($filearea, $path, $fileinfo->get_visible_name());
            if ($fileinfo->is_directory()) {
                $level = $fileinfo->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $ret['path'] = $this->_encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }
            }
            $ret['list'] = $this->build_tree($fileinfo, $search); //, $ret['path']);
            $ret['path'] = array_reverse($ret['path']);
        } else {
            // throw some "context/filearea/item/path/file not found" exception?
        }

        // this statement tells the file picker to load files dynamically (don't send the content of directories)
        // todo: add a thresold, where the picker automatically uses the dynamic mode - if there are too many files in
        // sub-directories - this should be calculated with a quick query, for the whole tree. Better optimizations
        // (like loading just a part of the sub-tree) can come later.
        // if ($count > $config_thresold) {
        //    $ret['dynload'] = true;
        //} else {
        $ret['dynload'] = false;
        //}

        if (empty($ret['list'])) {
            throw new repository_exception('emptyfilelist', 'repository_local');
        } else {
            return $ret;
        }
    }

    /**
     * Builds a tree of files, to be used by get_listing(). This function is 
     * then called recursively.
     *
     * @param $fileinfo an object returned by file_browser::get_file_info()
     * @param $search searched string
     * @param $path path to prepend to current element
     * @param $currentcount (in recursion) current number of elements, triggers dynamic mode if there's more than thresold.
     * @returns array describing the files under the passed $fileinfo 
     *
     * todo: take $search into account, and respect a threshold for dynamic loading
     */
    private function build_tree($fileinfo, $search) { //, $path, &$currentcount) {
        global $CFG;
           
        $children = $fileinfo->get_children();

        $list = array();
        foreach ($children as $child) {
            $filename = $child->get_visible_name();
            $filesize = $child->get_filesize();
            $filesize = $filesize ? display_size($filesize) : '';
            $filedate = $child->get_timemodified();
            $filedate = $filedate ? userdate($filedate) : '';
            $filetype = $child->get_mimetype();

            if ($child->is_directory()) {
                $path = array();
                $level = $child->get_parent();
                while ($level) {
                    $params = $level->get_params();
                    $path = $this->_encode_path($params['filearea'], $params['filepath'], $level->get_visible_name());
                    $level = $level->get_parent();
                }
                
                $tmp = array(
                    'title' => $child->get_visible_name(),
                    'size' => 0,
                    'date' => $filedate,
                    'path' => array_reverse($path),
                    'thumbnail' => $CFG->pixpath .'/f/folder.gif'
                );
                $tmp['children'] = $this->build_tree($child, $search);
                $list[] = $tmp;

            } else { // not a directory
                $list[] = array(
                    'title' => $filename,
                    'size' => $filesize,
                    'date' => $filedate,
                    'source' => $child->get_url(),
                    'thumbnail' => $CFG->pixpath .'/f/'. mimeinfo_from_type("icon", $filetype)
                );
            }
        }

        return $list;
    }

    public function print_listing() {
        // will be used in non-javascript file picker
    }

    public function print_search() {
        return true;
    }

    public static function has_admin_config() {
        return false;
    }

    public static function get_admin_option_names() {
        // todo: add dynamic loading threshold
        return array();
    }

    // empty function is necessary to make it possible to edit the name of the repository
    public function admin_config_form(&$mform) {
    }
    public function get_name(){
        return get_string('repositoryname', 'repository_local');;
    }
}
?>
