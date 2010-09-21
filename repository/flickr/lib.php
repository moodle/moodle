<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * repository_flickr class
 * This plugin is used to access user's private flickr repository
 *
 * @since 2.0
 * @package    repository
 * @subpackage flickr
 * @copyright  2009 Dongsheng Cai
 * @author     Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/flickrlib.php');

/**
 *
 */
class repository_flickr extends repository {
    private $flickr;
    public $photos;

    /**
     *
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
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

    /**
     *
     * @return bool
     */
    public function check_login() {
        return !empty($this->token);
    }

    /**
     *
     * @return mixed
     */
    public function logout() {
        set_user_preference($this->setting, '');
        set_user_preference($this->setting.'_nsid', '');
        $this->token = '';
        $this->nsid  = '';
        return $this->print_login();
    }

    /**
     *
     * @param array $options
     * @return mixed
     */
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

    /**
     *
     * @param string $config
     * @return mixed
     */
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

    /**
     *
     * @return bool
     */
    public function global_search() {
        if (empty($this->token)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @return null
     */
    public function print_login() {
        if ($this->options['ajax']) {
            $ret = array();
            $popup_btn = new stdClass();
            $popup_btn->type = 'popup';
            $popup_btn->url = $this->flickr->auth();
            $ret['login'] = array($popup_btn);
            return $ret;
        } else {
            echo '<a target="_blank" href="'.$this->flickr->auth().'">'.get_string('login', 'repository').'</a>';
        }
    }

    /**
     *
     * @param mixed $photos
     * @param int $page
     * @return array
     */
    private function build_list($photos, $page = 1) {
        $photos_url = $this->flickr->urls_getUserPhotos($this->nsid);
        $ret = array();
        $ret['manage'] = $photos_url;
        $ret['list']  = array();
        $ret['pages'] = $photos['pages'];
        $ret['total'] = $photos['total'];
        $ret['perpage'] = $photos['perpage'];
        if($page <= $ret['pages']) {
            $ret['page'] = $page;
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
                $format = '.'.$format;
                // append extensions to the files
                if (substr($p['title'], strlen($p['title'])-strlen($format)) != $format) {
                    $p['title'] .= $format;
                }
                $ret['list'][] = array('title'=>$p['title'],'source'=>$p['id'],
                    'id'=>$p['id'],'thumbnail'=>$this->flickr->buildPhotoURL($p, 'Square'),
                    'date'=>'', 'size'=>'unknown', 'url'=>$photos_url.$p['id']);
            }
        }
        return $ret;
    }

    /**
     *
     * @param string $search_text
     * @return array
     */
    public function search($search_text) {
        $photos = $this->flickr->photos_search(array(
            'user_id'=>$this->nsid,
            'per_page'=>24,
            'extras'=>'original_format',
            'text'=>$search_text
            ));
        $ret = $this->build_list($photos);
        $ret['list'] = array_filter($ret['list'], array($this, 'filter'));
        return $ret;
    }

    /**
     *
     * @param string $path
     * @param int $page
     * @return array
     */
    public function get_listing($path = '', $page = '1') {
        $photos_url = $this->flickr->urls_getUserPhotos($this->nsid);

        $photos = $this->flickr->photos_search(array(
            'user_id'=>$this->nsid,
            'per_page'=>24,
            'page'=>$page,
            'extras'=>'original_format'
            ));
        return $this->build_list($photos, $page);
    }

    public function get_link($photo_id) {
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
        return $url;
    }

    /**
     *
     * @param string $photo_id
     * @param string $file
     * @return string
     */
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
        $path = $this->prepare_file($file);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(
            array('url'=>$url, 'file'=>$fp)
        ));
        return array('path'=>$path, 'url'=>$url);
    }

    /**
     * Add Plugin settings input to Moodle form
     * @param object $mform
     */
    public function type_config_form($mform) {
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
        $params = array();
        $params['context'] = array();
        //$params['currentcontext'] = $this->context;
        $params['onlyvisible'] = false;
        $params['type'] = 'flickr';
        $instances = repository::get_instances($params);
        if (empty($instances)) {
            $callbackurl = get_string('callbackwarning', 'repository_flickr');
            $mform->addElement('static', null, '',  $callbackurl);
        } else {
            $instance = array_shift($instances);
            $callbackurl = $CFG->wwwroot.'/repository/repository_callback.php?repo_id='.$instance->id;
            $mform->addElement('static', 'callbackurl', '', get_string('callbackurltext', 'repository_flickr', $callbackurl));
        }

        $mform->addRule('api_key', $strrequired, 'required', null, 'client');
        $mform->addRule('secret', $strrequired, 'required', null, 'client');
    }

    /**
     * Names of the plugin settings
     * @return array
     */
    public static function get_type_option_names() {
        return array('api_key', 'secret', 'pluginname');
    }
    public function supported_filetypes() {
        return array('web_image');
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
}
