<?php
/**
 * repository_box class
 * This is a subclass of repository class
 *
 * @author Dongsheng Cai
 * @version 0.1 dev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/repository/'.'lib.php');
require_once($CFG->dirroot.'/repository/'.'curl.class.php');
require_once($CFG->dirroot.'/repository/boxnet/'.'boxlibphp5.php');

class repository_boxnet extends repository{

    var $box;
    var $ticket;

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
            $c = new curl;
            $str = '';
            $c->setopt(array('CURLOPT_FOLLOWLOCATION'=>0));
            $param =  array(
                'login_form1'=>'',
                'login'=>$options['username'],
                'password'=>$options['password'],
                'dologin'=>1,
                '__login'=>1
                );
            $ret = $c->post('http://www.box.net/api/1.0/auth/'.$options['ticket'], $param);
            $header = $c->getResponse();
            $location = $header['location'];
            preg_match('#auth_token=(.*)$#i', $location, $matches);
            $auth_token = $matches[1];
            $SESSION->box_token = $auth_token;
        }
        // already logged
        if(!empty($SESSION->box_token)) {
            $this->box = new boxclient($options['api_key'], $SESSION->box_token);
            $options['auth_token'] = $SESSION->box_token;
            $action = 'list';
        } else {
            $this->box = new boxclient($options['api_key'], '');
            $action = '';
        }
        parent::__construct($repositoryid, $context, $options);
    }

    public function get_listing($path = '/', $search = ''){
        global $CFG;
        $list = array();
        $ret  = array();
        if($this->box){
            $tree = $this->box->getAccountTree();
            if($tree) {
                $filenames = $tree['file_name'];
                $fileids   = $tree['file_id'];
                foreach ($filenames as $n=>$v){
                    $list[] = array('title'=>$v, 'size'=>0, 'date'=>'',
                            'url'=>'http://box.net/api/1.0/download/'.$this->options['auth_token'].'/'.$fileids[$n],
                            'thumbnail'=>$CFG->pixpath.'/i/files.gif');
                }
                $this->listing = $list;
                $ret['list']   = $list;
                return $ret;
            } else {
                return null;
            }
        }
    }

    public function print_login(){
        if(!empty($this->box) && !empty($this->options['auth_token'])) {
            if($this->options['ajax']){
                return $this->get_listing();
            } else {
                echo $this->get_listing();
            }
        } else if(!empty($this->box)){
            // get a ticket from box.net
            $ticket_return = $this->box->getTicket();
            if($this->box->isError()) {
                if(!$this->options['ajax']){
                    echo $this->box->getErrorMsg();
                }
            } else {
                $this->ticket = $ticket_return['ticket'];
            }
            // use the ticket to get a auth_token
            // auth_token is the key to access the resources
            // of box.net
            // WARNING: this function won't return a auth_token
            // if auth_token is not existed, this function will
            // direct user to authentication page of box.net
            // If the user has been authenticated, box.net will
            // direct to a callback page (can be set in box.net)
            // the call back page will obtain the auth_token
            // ===============================================
            // Because the authentication process will be done
            // in box.net, so we need print a login link in this
            // function instead a login screen.

            if($this->ticket && ($this->options['auth_token'] == '')){
                $str = '';
                $str .= '<form id="moodle-repo-login">';
                $str .= '<input type="hidden" name="ticket" value="'.$this->ticket.'" />';
                $str .= '<input type="hidden" name="id" value="'.$this->repositoryid.'" />';
                $str .= '<label for="box_username">Username: <label>';
                $str .= '<input type="text" id="box_username" name="username" />';
                $str .= '<br/>';
                $str .= '<label for="box_password">Password: <label>';
                $str .= '<input type="password" id="box_password" name="password" />';
                $str .= '<input type="button" onclick="dologin()" value="Go" />';
                $str .= '</form>';
                if($this->options['ajax']){
                    $ret = array();
                    $ret['l'] = $str;
                    return $ret;
                } else {
                    echo $str;
                }
                //$this->box->getAuthToken($this->ticket);
            }
        } else {
        }
    }

    public function print_search(){
        echo '<input type="text" disabled="true" name="Search" value="search terms..." size="40" class="right"/>';
        return false;
    }
}

?>
