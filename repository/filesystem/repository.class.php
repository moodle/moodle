<?php // $Id$

class repository_filesystem extends repository {
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $this->root_path = trim($this->root_path);
        $this->block_list = array(
            '/etc',
            '/',
            'c:\windows',
            'c:/windows'
            );
        if (!empty($options['ajax'])) {
            // if created from filepicker
            if (empty($this->root_path)) {
                $ret = array();
                $ret['msg'] = get_string('invalidpath', 'repository_filesystem');
                $ret['nosearch'] = true;
                echo json_encode($ret);
                exit;
            } else {
                if (!is_dir($this->root_path)) {
                    $ret = array();
                    $ret['msg'] = get_string('invalidpath', 'repository_filesystem');
                    $ret['nosearch'] = true;
                    if ($options['ajax']) {
                        echo json_encode($ret);
                        exit;
                    }
                }
            }
            if ($this->root_path{strlen($this->root_path)-1} !== '/') {
                $this->root_path .= '/';
            }
        }
    }
    public function security_check($path) {
        $blocked = false;
        foreach ($this->block_list as $item) {
            if ($path == $item or $path == $item.'/') {
                $blocked = true;
                break;
            }
        }
        return $blocked;
    }
    public function get_listing($path = '', $page = '') {
        global $CFG;

        if ($this->security_check($this->root_path)) {
            $ret = array();
            $ret['msg'] = get_string('blockedpath', 'repository_filesystem');
            $ret['nosearch'] = true;
            echo json_encode($ret);
            exit;
        }

        $list = array();
        $list['list'] = array();
        // process breacrumb trail
        $list['path'] = array(
            array('name'=>'Root','path'=>'')
        );
        $trail = '';
        if (!empty($path)) {
            $parts = explode('/', $path);
            if (count($parts) > 1) {
                foreach ($parts as $part) {
                    $trail .= ('/'.$part);
                    $list['path'][] = array('name'=>$part, 'path'=>$trail);
                }
            } else {
                $list['path'][] = array('name'=>$path, 'path'=>$path);
            }
            $this->root_path .= ($path.'/');
        }
        // set options
        $list['manage'] = false;
        // dynamically loading
        $list['dynload'] = true;
        // the current path of this list.
        // set to true, the login link will be removed
        $list['nologin'] = true;
        // set to true, the search button will be removed
        $list['nosearch'] = true;
        if ($dh = opendir($this->root_path)) {
            while (($file = readdir($dh)) != false) {
                if ( $file != '.' and $file !='..') {
                    if (filetype($this->root_path.$file) == 'file') {
                        $list['list'][] = array(
                            'title' => $file,
                            'source' => $path.'/'.$file,
                            'size' => filesize($this->root_path.$file),
                            'date' => time(),
                            'thumbnail' => $CFG->pixpath .'/f/'. mimeinfo('icon32', $this->root_path.$file)
                        );
                    } else {
                        if (!empty($path)) {
                            $current_path = $path . '/'. $file;
                        } else {
                            $current_path = $file;
                        }
                        $list['list'][] = array(
                            'title' => $file,
                            'children' => array(),
                            'thumbnail' => $CFG->pixpath .'/f/folder-32.png',
                            'path' => $current_path
                            );
                    }
                }
            }
        }
        return $list;
    }
    // login 
    public function check_login() {
        return true;
    }
    // if check_login returns false,
    // this function will be called to print a login form.
    public function print_login() {
        return true;
    }
    //search
    // if this plugin support global search, if this function return
    // true, search function will be called when global searching working
    public function global_search() {
        return false;
    }
    public function search($text) {
        $search_result = array();
        $search_result['list'] = array();
        return $search_result;
    }
    // move file to local moodle
    public function get_file($file, $title = '') {
        global $CFG;
        if ($file{0} == '/') {
            $file = $this->root_path.substr($file, 1, strlen($file)-1);
        }
        // this is a hack to prevent move_to_file deleteing files
        // in local repository
        $CFG->repository_no_delete = true;
        return $file;
    }

    public function logout() {
        return true;
    }

    public static function get_instance_option_names() {
        return array('root_path');
    }

    public function instance_config_form(&$mform) {
        $mform->addElement('text', 'root_path', get_string('path', 'repository_filesystem'), array('value'=>'','size' => '40'));
        $warning = get_string('donotusesysdir', 'repository_filesystem');
        $warning .= '<ul>';
        foreach ($this->block_list as $item) {
            $warning .= '<li>'.$item.'</li>';
        }
        $warning .= '</ul>';
        $mform->addElement('static', null, '',  $warning);
    }

    public static function get_type_option_names() {
        return array();
    }
    public function type_config_form(&$mform) {
    }
}
