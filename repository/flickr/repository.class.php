<?php
/**
 * repository_flickr class
 * This plugin is used to access user's private flickr repository
 *
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->libdir.'/flickrlib.php');

class repository_flickr extends repository {
    private $flickr;
    public $photos;

    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $SESSION, $CFG;
        $options['page']    = optional_param('p', 1, PARAM_INT);
        parent::__construct($repositoryid, $context, $options);

        $this->setting = 'flickr_';

        $this->api_key = $this->get_option('api_key');
        $this->secret  = $this->get_option('secret');

        $this->token = get_user_preferences($this->setting, '');
        $this->nsid  = get_user_preferences($this->setting.'_nsid', '');

        $this->flickr = new phpFlickr($this->api_key, $this->secret, $this->token);

        $frob  = optional_param('frob', '', PARAM_RAW);
        if (empty($this->token) && !empty($frob)) {
            $auth_info = $this->flickr->auth_getToken($frob);
            $this->token = $auth_info['token'];
            $this->nsid  = $auth_info['user']['nsid'];
            set_user_preference($this->setting, $auth_info['token']);
            set_user_preference($this->setting.'_nsid', $auth_info['user']['nsid']);
        }

    }
    public function check_login() {
        return !empty($this->token);
    }
    public function logout() {
        set_user_preference($this->setting, '');
        set_user_preference($this->setting.'_nsid', '');
        $this->token = '';
        $this->nsid  = '';
        return $this->print_login();
    }
    public function set_option($options = array()) {
        if (!empty($options['api_key'])) {
            set_config('api_key', trim($options['api_key']), 'flickr');
        }
        if (!empty($options['secret'])) {
            set_config('secret', trim($options['secret']), 'flickr');
        }
        unset($options['api_key']);
        unset($options['secret']);
        $ret = parent::set_option($options);
        return $ret;
    }

    public function get_option($config = '') {
        if ($config==='api_key') {
            return trim(get_config('flickr', 'api_key'));
        } elseif ($config ==='secret') {
            return trim(get_config('flickr', 'secret'));
        } else {
            $options['api_key'] = trim(get_config('flickr', 'api_key'));
            $options['secret']  = trim(get_config('flickr', 'secret'));
        }
        $options = parent::get_option($config);
        return $options;
    }

    public function global_search() {
        if (empty($this->token)) {
            return false;
        } else {
            return true;
        }
    }
    public function print_login($ajax = true) {
        if ($ajax) {
            $ret = array();
            $popup_btn = new stdclass;
            $popup_btn->type = 'popup';
            $popup_btn->url = $this->flickr->auth();
            $ret['login'] = array($popup_btn);
            return $ret;
        }
    }
    private function build_list($photos, $path = 1) {
        $photos_url = $this->flickr->urls_getUserPhotos($this->nsid);
        $ret = array();
        $ret['manage'] = $photos_url;
        $ret['list']  = array();
        $ret['pages'] = $photos['pages'];
        if(is_int($path) && $path <= $ret['pages']) {
            $ret['page'] = $path;
        } else {
            $ret['page'] = 1;
        }
        if (!empty($photos['photo'])) {
            foreach ($photos['photo'] as $p) {
                if(empty($p['title'])) {
                    $p['title'] = get_string('notitle', 'repository_flickr');
                }
                if (isset($p['originalformat'])) {
                    $format = $p['originalformat'];
                } else {
                    $format = 'jpg';
                }
                $ret['list'][] = array('title'=>$p['title'].'.'.$format,'source'=>$p['id'],
                    'id'=>$p['id'],'thumbnail'=>$this->flickr->buildPhotoURL($p, 'Square'),
                    'date'=>'', 'size'=>'unknown', 'url'=>$photos_url.$p['id']);
            }
        }
        return $ret;
    }
    public function search($search_text) {
        $photos = $this->flickr->photos_search(array(
            'user_id'=>$this->nsid,
            'per_page'=>25,
            'extras'=>'original_format',
            'text'=>$search_text
            ));
        return $this->build_list($photos);
    }
    public function get_listing($path = '1') {
        $photos_url = $this->flickr->urls_getUserPhotos($this->nsid);

        $photos = $this->flickr->photos_search(array(
            'user_id'=>$this->nsid,
            'per_page'=>25,
            'page'=>$path,
            'extras'=>'original_format'
            ));
        return $this->build_list($photos, $path);
    }
    public function print_listing() {
        return false;
    }
    public function get_file($photo_id, $file = '') {
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

    public static function has_multiple_instances() {
        return false;
    }

    public function instance_config_form(&$mform) {
        $mform->addElement('text', 'email_address', get_string('emailaddress', 'repository_flickr'));
        $mform->addRule('email_address', get_string('required'), 'required', null, 'client');
    }

    public static function get_instance_option_names() {
        return array('email_address');
    }

    public function admin_config_form(&$mform) {
        global $CFG;
        $api_key = get_config('flickr', 'api_key');
        $secret = get_config('flickr', 'secret');

        

        if (empty($api_key)) {
            $api_key = '';
        }
        if (empty($secret)) {
            $secret = '';
        }

        $strrequired = get_string('required');
        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_flickr'), array('value'=>$api_key,'size' => '40'));
        $mform->addElement('text', 'secret', get_string('secret', 'repository_flickr'), array('value'=>$secret,'size' => '40'));

        //retrieve the flickr instances
        $instances = repository_get_instances(array(),null,false,"flickr");
        if (empty($instances)) {
            $callbackurl = get_string("callbackwarning","repository_flickr");
             $mform->addElement('static', null, '',  $callbackurl);
        }
        else {
             $callbackurl = $CFG->wwwroot.'/repository/ws.php?callback=yes&amp;repo_id='.$instances[0]->id;
              $mform->addElement('static', 'callbackurl', '', get_string('callbackurltext', 'repository_flickr', $callbackurl));
        }
       

        $mform->addRule('api_key', $strrequired, 'required', null, 'client');
        $mform->addRule('secret', $strrequired, 'required', null, 'client');
    }

    public static function get_admin_option_names() {
        return array('api_key', 'secret');
    }

}
