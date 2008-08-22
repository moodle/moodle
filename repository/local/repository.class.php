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

    public function get_listing($path = '/', $search = '') {
        global $CFG;
        $ret = array();

        // this statement tells the file picker to load files dynamically (don't send the content of directories)
        // todo: add a thresold, where the picker automatically uses the dynamic mode - if there are too many files in
        // sub-directories - this should be calculated with a quick query, for the whole tree. Better optimizations
        // (like loading just a part of the sub-tree) can come later.
        $ret['dynload'] = false;
        // no login required
        $ret['nologin'] = true;
        // define upload form in file picker
        // Use ajax upload file
        $ret['upload'] = array('name'=>get_string('attachment', 'repository'), 'id'=>'repo-form');
        // todo: link to file manager  
        $ret['manage'] = $CFG->wwwroot .'/files/index.php'; // temporary
       
        $browser = get_file_browser();
        $itemid = null;
        $filename = null;
        $filearea = null;

        if ($fileinfo = $browser->get_file_info($this->context, $filearea, $itemid, $path, $filename)) {
            $level = $fileinfo->get_parent();
            $path = array();
            while ($level) {
                $path[] = $level->get_visible_name();
                $level = $level->get_parent();
            }
            $ret['path'] = array_reverse($path);
            $ret['list'] = $this->build_tree($fileinfo, $search);
        } else {
            // throw some "path not found" exception?
        }

        if (empty($ret['list'])) {
            throw new repository_exception('emptyfilelist', 'repository_local');
        } else {
            return $ret;
        }
    }

    /**
     * Builds a tree of files, to be used by get_listing()
     *
     * @param $fileinfo an object returned by file_browser::get_file_info()
     * @returns array describing the files under the passed $fileinfo 
     *
     * todo: take $search into account, and respect a threshold for dynamic loading
     */
    private function build_tree($fileinfo, $search) {
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
                $level = $child->get_parent();
                while ($level) {
                    $path[] = $level->get_visible_name();
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
        return true;
    }

    public static function get_option_names() {
        // todo: add dynamic loading threshold
        return array();
    }

    // empty function is necessary to make it possible to edit the name of the repository
    public function admin_config_form(&$mform) {
    }
}
?>
