<?php
/**
 * repository_url class
 * A subclass of repository, which is used to download a file from a specific url
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_url extends repository {

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
        global $SESSION, $action, $CFG;
        parent::__construct($repositoryid, $context, $options);
        $this->client_id = $options['client_id'];
        $this->file_url = optional_param('download_from', '', PARAM_RAW);
    }

    public function get_file($url, $file = '') {
        global $CFG;
        $path = $this->prepare_file($file);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));

        return $path;
    }

    public function check_login() {
        global $action;
        if (!empty($this->file_url)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     *
     * @global object $SESSION
     * @param boolean $ajax
     * @return mixed
     */
    public function print_login($ajax = true) {
        $url = new stdclass;
        $url->label = get_string('url', 'repository_url').': ';
        $url->id   = 'fileurl-'.$this->client_id;
        $url->type = 'text';
        $url->name = 'fileiii';

        $title = new stdclass;
        $title->label = get_string('rename', 'repository_url').': ';
        $title->id    = 'newname-'.$this->client_id;
        $title->type = 'text';
        $title->name = 'file';

        $ret['login'] = array($url, $title);
        $ret['login_btn_label'] = get_string('download', 'repository_url');
        $ret['login_btn_action'] = 'download';
        return $ret;
    }

    /**
     *
     * @param mixed $path
     * @param string $search
     * @return array
     */
    public function get_listing($path='', $page='') {
        $this->print_login();
    }

    public function get_name(){
        return get_string('repositoryname', 'repository_url');;
    }
}
?>
