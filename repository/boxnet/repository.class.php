<?php
/**
 * repository_boxnet class
 * This is a subclass of repository class
 *
 * @author Dongsheng Cai
 * @version 0.1 dev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->dirroot.'/repository/boxnet/'.'boxlibphp5.php');

class repository_boxnet extends repository{
    private $box;

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action;
        $options['username']   = optional_param('username', '', PARAM_RAW);
        $options['password']   = optional_param('password', '', PARAM_RAW);
        $options['ticket']     = optional_param('ticket', '', PARAM_RAW);
        $options['api_key']    = 'dmls97d8j3i9tn7av8y71m9eb55vrtj4';
        // reset session
        $reset = optional_param('reset', 0, PARAM_INT);
        if(!empty($reset)) {
            unset($SESSION->box_token);
        }
        // do login
        if(!empty($options['username'])
                    && !empty($options['password'])
                    && !empty($options['ticket']) )
        {
            $this->box = new boxclient($options['api_key']);
            $SESSION->box_token = $this->box->getAuthToken($options['ticket'], 
                $options['username'], $options['password']);
        }
        // already logged
        if(!empty($SESSION->box_token)) {
            if(empty($this->box)) {
                $this->box = new boxclient($options['api_key'], $SESSION->box_token);
            }
            $options['auth_token'] = $SESSION->box_token;
            if(empty($action)) {
                $action = 'list';
            }
        } else {
            $this->box = new boxclient($options['api_key']);
            if(!empty($action)) {
                $action = '';
            }
        }
        parent::__construct($repositoryid, $context, $options);
    }

    public function get_listing($path = '/', $search = ''){
        global $CFG;
        $list = array();
        $ret  = array();
        $tree = $this->box->getAccountTree();
        if(!empty($tree)) {
            $filenames = $tree['file_name'];
            $fileids   = $tree['file_id'];
            foreach ($filenames as $n=>$v){
                // do search
                if(!empty($search)) {
                    if(strstr($v, $search) !== false) {
                        $list[] = array('title'=>$v, 
                                'size'=>0,
                                'date'=>'',
                                'source'=>'http://box.net/api/1.0/download/'
                                    .$this->options['auth_token'].'/'.$fileids[$n],
                                'thumbnail'=>$CFG->pixpath.'/f/text.gif');
                    }
                } else {
                    $list[] = array('title'=>$v, 
                            'size'=>0,
                            'date'=>'',
                            'source'=>'http://box.net/api/1.0/download/'
                                .$this->options['auth_token'].'/'.$fileids[$n],
                            'thumbnail'=>$CFG->pixpath.'/f/text.gif');
                }
            }
            $this->listing = $list;
            $ret['list']   = $list;
            return $ret;
        } else {
            return null;
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
                $str = '';
                $str .= '<form id="moodle-repo-login">';
                $str .= '<input type="hidden" name="ticket" value="'.
                    $t['ticket'].'" />';
                $str .= '<input type="hidden" name="id" value="'.$this->repositoryid.'" />';
                $str .= '<label for="box_username">Username: <label><br/>';
                $str .= '<input type="text" id="box_username" name="username" />';
                $str .= '<br/>';
                $str .= '<label for="box_password">Password: <label><br/>';
                $str .= '<input type="password" id="box_password" name="password" /><br/>';
                $str .= '<input type="button" onclick="dologin()" value="Go" />';
                $str .= '</form>';
                if($this->options['ajax']){
                    $ret = array();
                    $ret['l'] = $str;
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
}

?>
