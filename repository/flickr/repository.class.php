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

    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $SESSION, $action, $CFG;
        $options['page']    = optional_param('p', 1, PARAM_INT);
        parent::__construct($repositoryid, $context, $options);
        $this->api_key = $this->get_option('api_key');
        if (empty($this->api_key)) {
        }
        $this->flickr = new phpFlickr($this->api_key);

        $reset = optional_param('reset', 0, PARAM_INT);
        $sess_name = 'flickrmail'.$this->id;
        if(!empty($reset)) {
            // logout from flickr
            unset($SESSION->$sess_name);
            set_user_preference('flickrmail'.$this->id, '');
        }

        if(!empty($SESSION->$sess_name)) {
            if(empty($action)) {
                $action = 'list';
            }
        } else {
            // get flickr account
            $account = optional_param('flickrmail', '', PARAM_RAW);
            if(!empty($account)) {
                $people = $this->flickr->people_findByEmail($account);
                if(!empty($people)) {
                    $remember = optional_param('remember', '', PARAM_RAW);
                    if(!empty($remember)) {
                        set_user_preference('flickrmail'.$this->id, $account);
                    }
                    $SESSION->$sess_name = $account;
                    if (empty($account)) {
                        $action = 'list';
                    } else {
                        $action = 'login';
                    }
                } else {
                    throw new repository_exception('invalidemail', 'repository_flickr');
                }
            } else {
                if($account = get_user_preferences('flickrmail'.$this->id, '')){
                    $SESSION->$sess_name = $account;
                    if(empty($action)) {
                        $action = 'list';
                    }
                } else {
                    $action = 'login';
                }
            }
        }
    }
    public function print_login($ajax = true){
        global $SESSION;
        $sess_name = 'flickrmail'.$this->id;
        if(empty($SESSION->$sess_name)) {
        $str =<<<EOD
<form id="moodle-repo-login">
<label for="account">Account (Email)</label><br/>
<input type='text' name='flickrmail' id='account' />
<input type='hidden' name='id' value='$this->id' /><br/>
<input type='checkbox' name='remember' id="keepid" value='true' /> <label for="keepid">Remember? </label>
<p><input type='button' onclick="repository_client.login()" value="Go" /></p>
</form>
EOD;
            if($ajax){
                $ret = array();
                $e1->label = get_string('username', 'repository_flickr').': ';
                $e1->id    = 'account';
                $e1->type = 'text';
                $e1->name = 'flickrmail';

                $e2->id   = 'keepid';
                $e2->label = get_string('remember', 'repository_flickr').' ';
                $e2->type = 'checkbox';
                $e2->name = 'remember';

                $e3->type = 'hidden';
                $e3->name = 'repo_id';
                $e3->value = $this->id;
                $ret['login'] = array($e1, $e2, $e3);
                return $ret;
            }else{
                echo $str;
            }
        } else {
            return $this->get_listing();
        }
    }
    public function get_listing($path = '1', $search = ''){
        global $SESSION;
        $sess_name = 'flickrmail'.$this->id;
        $people = $this->flickr->people_findByEmail($SESSION->$sess_name);
        $photos_url = $this->flickr->urls_getUserPhotos($people['nsid']);

        if(!empty($search)) {
            // do searching, if $path is not empty, ignore it.
            $photos = $this->flickr->photos_search(array('user_id'=>$people['nsid'], 'text'=>$search));
        } elseif(!empty($path) && empty($search)) {
            $photos = $this->flickr->people_getPublicPhotos($people['nsid'], 'original_format', 36, $path);
        }

        $ret = new stdclass;
        $ret->manage = $photos_url;
        $ret->list  = array();
        $ret->pages = $photos['pages'];
        if(is_int($path) && $path <= $ret->pages) {
            $ret->page = $path;
        } else {
            $ret->page = 1;
        }
        foreach ($photos['photo'] as $p) {
            if(empty($p['title'])) {
                $p['title'] = get_string('notitle', 'repository_flickr');
            }
            if (isset($p['originalformat'])) {
                $format = $p['originalformat'];
            } else {
                $format = 'jpg';
            }
            $ret->list[] =
                array('title'=>$p['title'].'.'.$format,'source'=>$p['id'],'id'=>$p['id'],'thumbnail'=>$this->flickr->buildPhotoURL($p, 'Square'), 'date'=>'', 'size'=>'unknown', 'url'=>$photos_url.$p['id']);
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
        $str .= <<<EOD
<style type='text/css'>
#paging{margin-top: 10px; clear:both}
#paging a{padding: 4px; border: 1px solid gray}
</style>
EOD;
        $str .= '<div id="paging">';
        for($i=1; $i <= $this->photos['pages']; $i++) {
            $str .= '<a href="###" onclick="cr('.$this->id.', '.$i.', 0)">';
            $str .= $i;
            $str .= '</a> ';
        }
        $str .= '</div>';
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

    public function admin_config_form(&$mform) {
        $api_key = get_config('flickr', 'api_key');
        if (empty($api_key)) {
            $api_key = '';
        }
        $strrequired = get_string('required');
        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_boxnet'), array('value'=>$api_key,'size' => '40'));
        $mform->addRule('api_key', $strrequired, 'required', null, 'client');
    }
    public static function get_admin_option_names(){
        return array('api_key');
    }

}
