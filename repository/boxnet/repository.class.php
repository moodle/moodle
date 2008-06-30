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
require_once($CFG->dirroot.'/repository/boxnet/'.'boxlibphp5.php');

class repository_boxnet extends repository{

    var $api_key    = 'dmls97d8j3i9tn7av8y71m9eb55vrtj4';
    var $box;
    var $ticket;

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        $repositoryid = -1;
        parent::__construct($repositoryid, $context, $options);
        if(!empty($options['api_key'])){
            $this->api_key = $options['api_key'];
        }
        if(empty($this->options['auth_token'])) {
            $this->box = new boxclient($this->api_key, '');
        } else {
            $this->box = new boxclient($this->api_key, $this->options['auth_token']);
        }
    }

    public function get_listing($path = '0', $search = ''){
        $ret = array();
        if($this->box){
            $tree  = $this->box->getAccountTree();
            if($tree) {
                $filenames = $tree['file_name'];
                $fileids   = $tree['file_id'];
                foreach ($filenames as $n=>$v){
                    $ret[] = array('name'=>$v, 'size'=>0, 'date'=>'',
                            'url'=>'http://box.net/api/1.0/download/'.$this->options['auth_token'].'/'.$fileids[$n]);
                }
                return $ret;
            } else {
                return null;
            }
        }
    }

    public function print_login(){
        if($this->box){
            // get a ticket from box.net
            $ticket_return = $this->box->getTicket();
            if($this->box->isError()) {
                echo $this->box->getErrorMsg();
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
                $this->box->getAuthToken($this->ticket);
                return false;
            } else {
                echo 'Logged';
                return true;
            }
        } else {
            return false;
        }
    }

    public function print_search(){
        return false;
    }
}

?>
