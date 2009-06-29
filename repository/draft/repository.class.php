<?php
/**
 * repository_draft class
 * This is a subclass of repository class
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class repository_draft extends repository {

    /**
     * @param int $repositoryid
     * @param int $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
    }

    /**
     * @param boolean $ajax
     * @return mixed
     */
    public function print_login() {
        return $this->get_listing();
    }

    /**
     *
     * @param string $path
     * @param string $path not used by this plugin
     * @return mixed
     */
    public function get_listing($path = '', $page = '') {
        global $CFG, $USER, $itemid;
        $ret = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = true;
        $ret['nologin'] = true;
        $ret['draftfiles'] = true;
        $list = array();

        $fs = get_file_storage();
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        $files = $fs->get_area_files($context->id, 'user_draft', $itemid);
        foreach ($files as $file) {
            if ($file->get_filename()!='.') {
                $node = array(
                    'title' => $file->get_filename(),
                    'size' => 0,
                    'date' => '',
                    'source'=> $file->get_id(),
                    'thumbnail' => $CFG->pixpath .'/f/text-32.png'
                );
                $list[] = $node;
            }
        }
        $ret['list'] = $list;
        return $ret;
    }

     /**
     * Return draft files information
     *
     * @global object $USER
     * @param string $fid file id
     * @param string $title
     * @param string $itemid
     * @return string the location of the file
     */
    public function get_file($fid, $title = '', $itemid = '') {
        global $USER;
        $ret = array();
        $browser = get_file_browser();
        $user_context = get_context_instance(CONTEXT_USER, $USER->id);
        $ret['itemid'] = $itemid;
        $ret['title']  = $title;
        $ret['contextid'] = $user_context->id;
        return $ret;
    }

    /**
     *
     * @return string  repository name
     */
    public function get_name(){
        return get_string('repositoryname', 'repository_draft');;
    }
}
?>
