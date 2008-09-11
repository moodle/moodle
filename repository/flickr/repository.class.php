<?php
/**
 * repository_flickr class
 * This is a subclass of repository class
 *
 * @author Dongsheng Cai
 * @version 0.1 dev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->dirroot.'/repository/flickr/'.'phpFlickr.php');

class repository_flickr extends repository{
    private $flickr;
    public $photos;

    public function set_option($options = array()){
        if (!empty($options['api_key'])) {
            set_config('api_key', trim($options['api_key']), 'flickr');
        }
        unset($options['api_key']);
        $ret = parent::set_option($options);
        return $ret;
    }

    public function get_option($config = ''){
        if($config==='api_key'){
            return trim(get_config('flickr', 'api_key'));
        } else {
            $options['api_key'] = trim(get_config('flickr', 'api_key'));
        }
        $options = parent::get_option($config);
        return $options;
    }

    public function global_search(){
        global $SESSION;
        if (empty($this->token)) {
            return false;
        } else {
            return true;
        }
    }

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        $options['page']    = optional_param('p', 1, PARAM_INT);
        parent::__construct($repositoryid, $context, $options);

        $this->setting = 'flickr_';

        $this->api_key = $this->get_option('api_key');
        //TODO: put secret into database
        $this->secret = '';

        $this->token = get_user_preferences($this->setting, '');
        $this->nsid  = get_user_preferences($this->setting.'_nsid', '');

        $this->flickr = new phpFlickr($this->api_key, $this->secret, $this->token);

        $reset = optional_param('reset', 0, PARAM_INT);
        if(empty($this->token)){
            $frob  = optional_param('frob', '', PARAM_RAW);
            if(!empty($frob)){
                $auth_info = $this->flickr->auth_getToken($frob);
                $this->token = $auth_info['token'];
                $this->nsid  = $auth_info['user']['nsid'];
                set_user_preference($this->setting, $auth_info['token']);
                set_user_preference($this->setting.'_nsid', $auth_info['user']['nsid']);
                $this->perm  = $auth_info['token'];
            }else{
                $action = 'login';
            }
        }
        if(!empty($reset)) {
            set_user_preference($this->setting, '');
            set_user_preference($this->setting.'_nsid', '');
            $this->token = '';
            $this->nsid  = '';
            $action = 'login';
        }

        if(!empty($this->token)) {
            if(empty($action)){
                $action = 'list';
            }
        } else {
            $action = 'login';
        }
    }
    public function print_login($ajax = true){
        global $SESSION;
        if(!empty($this->token)){
        }
        if(empty($this->token)) {
            if($ajax){
                $ret = array();
                $popup_btn = new stdclass;
                $popup_btn->type = 'popup';
                $popup_btn->url = $this->flickr->auth();
                $ret['login'] = array($popup_btn);
                return $ret;
            }
        } else {
            return $this->get_listing();
        }
    }
    public function get_listing($path = '1', $search = ''){
        global $SESSION;
        $nsid = get_user_preferences($this->setting.'_nsid', '');
        $photos_url = $this->flickr->urls_getUserPhotos($nsid);

        if(!empty($search)) {
            $photos = $this->flickr->photos_search(array(
                'per_page'=>25,
                'page'=>$path,
                'extras'=>'original_format',
                'text'=>$search
                ));
        } elseif(!empty($path) && empty($search)) {
            $photos = $this->flickr->photos_search(array(
                'user_id'=>$nsid,
                'per_page'=>25,
                'page'=>$path,
                'extras'=>'original_format'
                ));
        }
        $ret = array();
        $ret['manage'] = $photos_url;
        $ret['list']  = array();
        $ret['pages'] = $photos['pages'];
        if(is_int($path) && $path <= $ret['pages']) {
            $ret['page'] = $path;
        } else {
            $ret['page'] = 1;
        }
        if(!empty($photos['photo'])){
            foreach ($photos['photo'] as $p) {
                if(empty($p['title'])) {
                    $p['title'] = get_string('notitle', 'repository_flickr');
                }
                if (isset($p['originalformat'])) {
                    $format = $p['originalformat'];
                } else {
                    $format = 'jpg';
                }
                $ret['list'][] =
                    array('title'=>$p['title'].'.'.$format,'source'=>$p['id'],'id'=>$p['id'],'thumbnail'=>$this->flickr->buildPhotoURL($p, 'Square'), 'date'=>'', 'size'=>'unknown', 'url'=>$photos_url.$p['id']);
            }
        }
        if(empty($ret)) {
            throw new repository_exception('nullphotolist', 'repository_flickr');
        } else {
            return $ret;
        }
    }
    public function print_listing(){
        if(empty($this->photos)){
            $this->get_listing();
        }
        $str = '';
        $str .= '<h2>Account: <span>'.$this->photos['a'].'</span></h2>';
        foreach ((array)$this->photos['photo'] as $photo) {
            $str .= "<a href='".$this->photos['url'].$photo[id]."'>";
            $str .= "<img border='0' alt='$photo[title]' ".
                "src=" . $photo['thumbnail'] . ">";
            $str .= "</a>";
            $i++;

            if ($i % 4 == 0) {
                $str .= "<br/>";
            }
        }
        echo $str;
    }
    public function print_search(){
        echo '<input type="text" name="Search" value="search terms..." size="40" class="right"/>';
        return true;
    }
    public function get_file($photo_id, $file = ''){
        global $CFG;
        $result = $this->flickr->photos_getSizes($photo_id);
        $url = '';
        if(!empty($result[4])) {
            $url = $result[4]['source'];
        } elseif(!empty($result[3])) {
            $url = $result[3]['source'];
        } elseif(!empty($result[2])) {
            $url = $result[2]['source'];
        }
        if (!file_exists($CFG->dataroot.'/repository/download')) {
            mkdir($CFG->dataroot.'/repository/download/', 0777, true);
        }
        if(is_dir($CFG->dataroot.'/repository/download')) {
            $dir = $CFG->dataroot.'/repository/download/';
        }

        if(empty($file)) {
            $file = $photo_id.'_'.time().'.jpg';
        }
        if(file_exists($dir.$file)) {
            $file = uniqid('m').$file;
        }
        $fp = fopen($dir.$file, 'w');
        $c = new curl;
        $c->download(array(
            array('url'=>$url, 'file'=>$fp)
        ));
        return $dir.$file;
    }
    public static function has_admin_config() {
        return true;
    }

    public static function has_multiple_instances() {
        return true;
    }

    public static function has_instance_config() {
        return true;
    }

    public function instance_config_form(&$mform) {
        $mform->addElement('text', 'email_address', get_string('emailaddress', 'repository_flickr'));
        $mform->addRule('email_address', get_string('required'), 'required', null, 'client');
    }

    public static function get_instance_option_names(){
        return array('email_address');
    }

    public function admin_config_form(&$mform) {
        $api_key = get_config('flickr', 'api_key');
        $secret = get_config('flickr', 'secret');
        $callbackurl = get_config('flickr', 'callbackurl');
        if (empty($api_key)) {
            $api_key = '';
        }
        if (empty($secret)) {
            $secret = '';
        }
        if (empty($callbackurl)) {
            $callbackurl = '';
        }
        $strrequired = get_string('required');
        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_flickr'), array('value'=>$api_key,'size' => '40'));
        $mform->addElement('text', 'secret', get_string('secret', 'repository_flickr'), array('value'=>$secret,'size' => '40'));
        $mform->addElement('text', 'callbackurl', get_string('callbackurl', 'repository_flickr'), array('value'=>$callbackurl,'size' => '40'));

        $mform->addRule('api_key', $strrequired, 'required', null, 'client');
        $mform->addRule('secret', $strrequired, 'required', null, 'client');
        $mform->addRule('callbackurl', $strrequired, 'required', null, 'client');
    }

    public static function get_admin_option_names(){
        return array('api_key');
    }

}
