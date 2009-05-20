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
    public function print_login($ajax = true) {
        return $this->get_listing();
    }

    /**
     *
     * @param string $search_text
     * @return mixed
     */
    public function search($search_text) {
        return $this->get_listing('', '', $search_text);
    }

    /**
     *
     * @param string $encodedpath
     * @param string $path not used by this plugin
     * @param string $search
     * @return mixed
     */
    public function get_listing($encodedpath = '', $page = '', $search = '') {
        global $CFG;
        $ret = array();
        $ret['dynload'] = true;
        $list = array();

        try {
            $browser = get_file_browser();
            if (!empty($encodedpath)) {
                $decodedpath = unserialize(base64_decode($encodedpath));
                $itemid   = $decodedpath['itemid'];
                $filename = $decodedpath['filename'];
                $filearea = $decodedpath['filearea'];
                $filepath = $decodedpath['filepath'];
                $context  = get_context_instance_by_id($decodedpath['contextid']);
            } else {
                $itemid   = null;
                $filename = null;
                $filearea = null;
                $filepath = null;
                $context  = get_system_context();
            }

            if ($fileinfo = $browser->get_file_info($context, $filearea, $itemid, '/', $filename)) {
                $level = $fileinfo->get_parent();
                while ($level) {
                    $params = base64_encode(serialize($level->get_params()));
                    $path[] = array('name'=>$level->get_visible_name(), 'path'=>$params);
                    $level = $level->get_parent();
                }
                $path = array_reverse($path);
                $ret['path'] = $path;
                $children = $fileinfo->get_children();
                foreach ($children as $child) {
                    if ($child->is_directory()) {
                        $params = base64_encode(serialize($child->get_params()));
                        $node = array(
                            'title' => $child->get_visible_name(),
                            'size' => 0,
                            'date' => '',
                            'path' => $params,
                            'children'=>array(),
                            'thumbnail' => $CFG->wwwroot .'/pix/f/folder-32.png'
                        );
                        $list[] = $node;
                    } else {
                        $params = base64_encode(serialize($child->get_params()));
                        $node = array(
                            'title' => $child->get_visible_name(),
                            'size' => 0,
                            'date' => '',
                            'source'=> $params,
                            'thumbnail' => $CFG->wwwroot .'/pix/f/text-32.png'
                        );
                        $list[] = $node;
                    }
                }
            }
        }
        catch (Exception $e) {
            throw new repository_exception('emptyfilelist', 'repository_local');
        }
        $ret['list'] = $list;
        return $ret;
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
    public function get_file($encoded, $title = '', $itemid = '', $ctx_id) {
        global $USER;
        $params = unserialize(base64_decode($encoded));
        $contextid = $params['contextid'];
        $filearea  = $params['filearea'];
        $filepath  = $params['filepath'];
        $filename  = $params['filename'];
        $fileitemid = $params['itemid'];
        $fs = get_file_storage();
        $oldfile = $fs->get_file($contextid, $filearea, $fileitemid, $filepath, $filename);

        $now = time();
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        $recored = new stdclass;
        $record->filearea = 'user_draft';
        $record->contextid = $context->id;
        $record->filename  = $title;
        $record->filepath  = '/';
        $record->timecreated  = $now;
        $record->timemodified = $now;
        $record->userid       = $USER->id;
        $record->mimetype = $oldfile->get_mimetype();
        if (!empty($itemid)) {
            $record->itemid   = $itemid;
        }
        $newfile = $fs->create_file_from_storedfile($record, $oldfile->get_id());
        return $newfile;
    }

    /**
     *
     * @return string
     */
    public function get_name(){
        return get_string('repositoryname', 'repository_local');;
    }
}
?>
