<?php
/**
 * repository_local class
 * This is a subclass of repository class
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/**
 *
 */
class repository_local extends repository {

    /**
     *
     * @global <type> $SESSION
     * @global <type> $action
     * @global <type> $CFG
     * @param <type> $repositoryid
     * @param <type> $context
     * @param <type> $options
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        // TODO:
        // get the parameter from client side
        // $this->context can be used here.
        // When user upload a file, $action == 'upload'
        // You can use $_FILES to find that file
    }

    /**
     *
     * @global <type> $SESSION
     * @param <type> $ajax
     * @return <type>
     */
    public function print_login($ajax = true) {
        global $SESSION;
        // TODO
        // Return file list in moodle
        return $this->get_listing();
    }

    /**
     *
     * @param <type> $path
     * @return <type>
     */
    private function _decode_path($path) {
        $filearea = '';
        $path = '';
        if (($file = unserialize($path)) !== false) {
            $filearea = $file[0];
            $path = $file[1];
        }
        return array('filearea' => $filearea, 'path' => $path);
    }

    /**
     *
     * @param <type> $search_text
     * @return <type>
     */
    public function search($search_text) {
        return $this->get_listing('', $search_text);
    }

    /**
     *
     * @global <type> $CFG
     * @param <type> $encodedpath
     * @param <type> $search
     * @return <type>
     */
    public function get_listing($encodedpath = '', $search = '') {
        global $CFG;

        try {
            return repository::get_user_file_tree($search);
        }
        catch (Exception $e) {
            throw new repository_exception('emptyfilelist', 'repository_local');
        }
    }

     /**
     * Download a file, this function can be overridden by
     * subclass.
     *
     * @global object $CFG
     * @param string $url the url of file
     * @param string $file save location
     * @return string the location of the file
     * @see curl package
     */
    public function get_file($url, $file = '') {
        global $CFG;
        if (!file_exists($CFG->dataroot.'/temp/download')) {
            mkdir($CFG->dataroot.'/temp/download/', 0777, true);
        }
        if (is_dir($CFG->dataroot.'/temp/download')) {
            $dir = $CFG->dataroot.'/temp/download/';
        }
        if (empty($file)) {
            $file = uniqid('repo').'_'.time().'.tmp';
        }
        if (file_exists($dir.$file)) {
            $file = uniqid('m').$file;
        }

        ///retrieve the file
        $fileparams = unserialize(base64_decode($url));
        $contextid = $fileparams[0];
        $filearea = $fileparams[1];
        $itemid = $fileparams[2];
        $filepath = $fileparams[3];
        $filename = $fileparams[4];
        $fs = get_file_storage();
        $sf = $fs->get_file($contextid, $filearea, $itemid, $filepath, $filename);
        $contents = $sf->get_content();
        $fp = fopen($dir.$file, 'w');
        fwrite($fp,$contents);
        fclose($fp);

        return $dir.$file;
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
    public function get_name(){
        return get_string('repositoryname', 'repository_local');;
    }
}
?>
