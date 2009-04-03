<?php
/**
 * repository_webdav class
 *
 * @author Dongsheng Cai
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->libdir.'/webdavlib.php');

class repository_webdav extends repository {
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $this->wd = new webdav_client();
        if (empty($this->webdav_server)) {
            return;
        }
        $this->wd->set_server($this->webdav_server);
        if (empty($this->webdav_port)) {
            $this->wd->set_port(80);
        } else {
            $this->wd->set_port($this->webdav_port);
        }
        if(!empty($this->webdav_user)){
            $this->wd->set_user($this->webdav_user);
        }
        if(!empty($this->webdav_password)) {
            $this->wd->set_pass($this->webdav_password);
        }
        $this->wd->set_protocol(1);
        $this->wd->set_debug(false);
    }
    public function check_login() {
        global $SESSION;
        return true;
    }
    public function get_file($url, $title) {
        global $CFG;
        $path = $this->prepare_file($title);
        $buffer = '';
        $this->wd->open();
        $this->wd->get($url, $buffer);
        $fp = fopen($path, 'wb');
        fwrite($fp, $buffer);
        return $path;
    }
    public function global_search() {
        return false;
    }
    public function get_listing($path='', $page = '') {
        global $CFG;
        $list = array();
        $ret  = array();
        $ret['dynload'] = true;
        $ret['list'] = array();
        $ret['nosearch'] = true;
        $ret['nologin'] = true;
        $ret['path'] = array(array('name'=>'Root', 'path'=>0));
        $this->wd->open();
        if (empty($path)) {
            $dir = $this->wd->ls('/');
            $path = '/';
        } else {
            $dir = $this->wd->ls($path);
        }
        if (!is_array($dir)) {
            return $ret;
        }
        foreach ($dir as $v) {
            $ts = $this->wd->iso8601totime($v['creationdate']);
            $filedate = userdate($ts);
            if (!empty($v['resourcetype']) && $v['resourcetype'] == 'collection') {
                if ($path != $v['href']) {
                    $title = urldecode(substr($v['href'], strpos($v['href'], $path)+strlen($path)));
                    $ret['list'][] = array(
                        'title'=>$title,
                        'thumbnail'=>$CFG->pixpath.'/f/folder.gif',
                        'children'=>array(),
                        'date'=>$filedate,
                        'size'=>0,
                        'path'=>$v['href']
                    );
                }
            }else{
                $title = urldecode(substr($v['href'], strpos($v['href'], $path)+strlen($path)));
                $ret['list'][] = array(
                    'title'=>$title,
                    'thumbnail' => $CFG->pixpath .'/f/'. mimeinfo('icon32', $title),
                    'size'=>$v['getcontentlength'],
                    'date'=>$filedate,
                    'source'=>$v['href']
                );
            }
        }
        return $ret;
    }
    public static function get_instance_option_names() {
        return array('webdav_server', 'webdav_port', 'webdav_user', 'webdav_password');
    }

    public function instance_config_form(&$mform) {
        $mform->addElement('text', 'webdav_server', get_string('webdav_server', 'repository_webdav'), array('size' => '40'));
        $mform->addRule('webdav_server', get_string('required'), 'required', null, 'client');
        $mform->addElement('text', 'webdav_port', get_string('webdav_port', 'repository_webdav'), array('size' => '40'));
        $mform->addElement('text', 'webdav_user', get_string('webdav_user', 'repository_webdav'), array('size' => '40'));
        $mform->addElement('text', 'webdav_password', get_string('webdav_password', 'repository_webdav'), array('size' => '40'));
    }
}
