<?php
/**
 * repository_smb class
 *
 * @author Dongsheng Cai
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->libdir.'/smblib.php');

class repository_smb extends repository {
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
    }
    public function check_login() {
        global $SESSION;
        return true;
    }
    public function get_file($url, $title) {
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

        $content = '';
        $fp = fopen($url, 'r');
        while (!feof($fp)) {
            $content .= fread($fp, 1024*8);
        }
        $fp = fopen($dir.$file, 'wb');
        fwrite($fp, $content);
        return $dir.$file;
    }
    public function global_search() {
        return false;
    }
    public function get_listing($path='') {
        global $CFG;
        $list = array();
        $ret  = array();
        $ret['dynload'] = true;
        $ret['nosearch'] = true;
        $ret['nologin'] = true;
        $ret['list'] = array();
        $ret['path'] = array(array('name'=>'Root', 'path'=>0));
        if (empty($path)) {
            $path = $this->smb_server;
        }
        $fp = opendir($path);
        while (($file = readdir($fp)) !== false) {
            if (is_dir($path.$file)) {
                $ret['list'][] = array(
                    'title'=>$file,
                    'path'=>$path.$file.'/',
                    'thumbnail'=>$CFG->pixpath.'/f/folder.gif',
                    'size'=>0,
                    'date'=>'',
                    'children'=>array());
            } else {
                $ret['list'][] = array(
                    'title'=>$file,
                    'thumbnail' => $CFG->pixpath .'/f/'. mimeinfo("icon", $file),
                    'size'=>'',
                    'date'=>'',
                    'source'=>$path.$file);
            }
        }
        return $ret;
    }
    public static function get_instance_option_names() {
        return array('smb_server');
    }

    public function instance_config_form(&$mform) {
        $mform->addElement('text', 'smb_server', get_string('smb_server', 'repository_smb'), array('size' => '40'));
        $mform->addRule('smb_server', get_string('required'), 'required', null, 'client');
    }
}


