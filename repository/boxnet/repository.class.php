<?php
/**
 * repository_boxnet class
 * This is a subclass of repository class
 *
 * @author Dongsheng Cai
 * @version 0.1 dev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->libdir.'/boxlib.php');

class repository_boxnet extends repository{
    private $box;

    public function set_option($options = array()){
        if (!empty($options['api_key'])) {
            set_config('api_key', trim($options['api_key']), 'boxnet');
        }
        unset($options['api_key']);
        $ret = parent::set_option($options);
        return $ret;
    }

    public function get_option($config = ''){
        if($config==='api_key'){
            return trim(get_config('boxnet', 'api_key'));
        } else {
            $options['api_key'] = trim(get_config('boxnet', 'api_key'));
        }
        $options = parent::get_option($config);
        return $options;
    }

    public function global_search(){
        global $SESSION;
        $sess_name = 'box_token'.$this->id;
        if (empty($SESSION->$sess_name)) {
            return false;
        } else {
            return true;
        }
    }

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action;
        $options['username']   = optional_param('boxusername', '', PARAM_RAW);
        $options['password']   = optional_param('boxpassword', '', PARAM_RAW);
        $options['ticket']     = optional_param('ticket', '', PARAM_RAW);
        $reset                 = optional_param('reset', 0, PARAM_INT);
        parent::__construct($repositoryid, $context, $options);
        $this->api_key = $this->get_option('api_key');
        if (empty($this->api_key)) {
        }
        $sess_name = 'box_token'.$this->id;
        // reset session
        if(!empty($reset)) {
            unset($SESSION->$sess_name);
        }
        // do login
        if(!empty($options['username'])
                    && !empty($options['password'])
                    && !empty($options['ticket']) )
        {
            $this->box = new boxclient($this->api_key);
            try{
                $SESSION->$sess_name = $this->box->getAuthToken($options['ticket'], 
                    $options['username'], $options['password']);
            } catch (repository_exception $e) {
                throw $e;
            }
            if ($SESSION->$sess_name) {
                $action = 'list';
            } else {
                $action = 'login';
            }
        }
        // already logged
        if(!empty($SESSION->$sess_name)) {
            if(empty($this->box)) {
                $this->box = new boxclient($this->api_key, $SESSION->$sess_name);
            }
            $this->auth_token = $SESSION->$sess_name;
            if(empty($action)) {
                $action = 'list';
            }
        } else {
            $this->box = new boxclient($this->api_key);
            // print login
            $action = 'login';
        }
    }

    public function get_login(){
        global $DB;
        if ($entry = $DB->get_record('repository_instances', array('id'=>$this->id))) {
            $ret->username = $entry->username;
            $ret->password = $entry->password;
        } else {
            $ret->username = '';
            $ret->password = '';
        }
        return $ret;
    }
    public function get_listing($path = '/', $search = ''){
        global $CFG, $SESSION;
        $list = array();
        $ret  = array();
        if (!empty($search)) {
            $tree = $this->box->getAccountTree();
            if (!empty($tree)) {
                $filenames = $tree['file_name'];
                $fileids   = $tree['file_id'];
                $filesizes = $tree['file_size'];
                $filedates = $tree['file_date'];
                $fileicon  = $tree['thumbnail'];
                foreach ($filenames as $n=>$v){
                    if(strstr($v, $search) !== false) {
                        $list[] = array('title'=>$v, 
                                'size'=>$filesizes[$n],
                                'date'=>$filedates[$n],
                                'source'=>'http://box.net/api/1.0/download/'
                                    .$this->options['auth_token'].'/'.$fileids[$n],
                                'thumbnail'=>$CFG->pixpath.'/f/'.mimeinfo('icon', $v));
                    }
                }
            }
            $ret['list'] = $list;
            return $ret;
        }
        $tree = $this->box->getfiletree($path);
        if(!empty($tree)) {
            // TODO: think about how to search
            $ret['list']   = $tree;
            $ret['manage'] = 'http://www.box.net/files';
            $ret['path'] = array(array('name'=>'Root', 'path'=>0));
            $this->listing = $tree;
            return $ret;
        } else {
            $sess_name = 'box_token'.$this->id;
            unset($SESSION->$sess_name);
            throw new repository_exception('nullfilelist', 'repository_boxnet');
        }
    }

    public function print_login(){
        if(!empty($this->options['auth_token'])) {
            if($this->options['ajax']){
                return $this->get_listing();
            } else {
                // format file list and 
                // print list
            }
        } else {
            $t = $this->box->getTicket();
            if(empty($this->options['auth_token'])) {
                $ret = $this->get_login();
                $str = '';
                $str .= '<form id="moodle-repo-login">';
                $str .= '<input type="hidden" name="ticket" value="'.
                    $t['ticket'].'" />';
                $str .= '<input type="hidden" name="id" value="'.$this->id.'" />';
                $str .= '<label for="box_username">Username: <label><br/>';
                $str .= '<input type="text" id="box_username" name="username" value="'.$ret->username.'" />';
                $str .= '<br/>';
                $str .= '<label for="box_password">Password: <label><br/>';
                $str .= '<input type="password" value="'.$ret->password.'" id="box_password" name="password" /><br/>';
                $str .= '<input type="button" onclick="repository_client.login()" value="Go" />';
                $str .= '</form>';
                if ($this->options['ajax']) {
                    $e1->type = 'hidden';
                    $e1->name = 'ticket';
                    $e1->value = $t['ticket'];

                    $e2->type = 'hidden';
                    $e2->name = 'repo_id';
                    $e2->value = $this->id;

                    $e3->label = get_string('username', 'repository_boxnet').': ';
                    $e3->id    = 'box_username';
                    $e3->type  = 'text';
                    $e3->name  = 'boxusername';
                    $e3->value = $ret->username;
                    
                    $e4->label = get_string('password', 'repository_boxnet').': ';
                    $e4->id    = 'box_password';
                    $e4->type  = 'password';
                    $e4->name  = 'boxpassword';

                    $e5->type = 'popup';
                    $e5->url = 'http://dongsheng.moodle.com/m20/repository/callback.php';

                    $ret = array();
                    $ret['login'] = array($e1, $e2, $e3, $e4, $e5);
                    return $ret;
                } else {
                    echo $str;
                }
            }
        }
    }

    public function print_search(){
        return false;
    }

    public static function has_admin_config() {
        return true;
    }

    public static function has_instance_config() {
        return true;
    }

    public static function has_multiple_instances(){
        return true;
    }

    public static function get_admin_option_names(){
        return array('api_key');
    }

    public static function get_instance_option_names(){
        return array('share_url');
    }

    public function admin_config_form(&$mform) {
        $public_account = get_config('boxnet', 'public_account');
        $api_key = get_config('boxnet', 'api_key');
        if (empty($api_key)) {
            $api_key = '';
        }
        $strrequired = get_string('required');
        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_boxnet'), array('value'=>$api_key,'size' => '40'));
        $mform->addRule('api_key', $strrequired, 'required', null, 'client');
    }

    public function instance_config_form(&$mform) {
        $share_url = get_config('boxnet', 'share_url');
        $mform->addElement('text', 'share_url', get_string('shareurl', 'repository_boxnet'), array('value'=>$share_url));
    }
}

?>
