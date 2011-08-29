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

require_once($CFG->libdir.'/boxlib.php');

/**
 * repository_boxnet class
 * This is a subclass of repository class
 *
 * @since 2.0
 * @package    repository
 * @subpackage boxnet
 * @copyright  2009 Dongsheng Cai
 * @author     Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_boxnet extends repository {
    private $boxclient;

    /**
     * Constructor
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $this->api_key = $this->get_option('api_key');
        $this->setting_prefix = 'boxnet_';

        $this->auth_token = get_user_preferences($this->setting_prefix.'_auth_token', '');
        $this->logged = false;
        if (!empty($this->auth_token)) {
            $this->logged = true;
        }
        // already logged
        if(!empty($this->logged)) {
            if(empty($this->boxclient)) {
                $this->boxclient = new boxclient($this->api_key, $this->auth_token);
            }
        } else {
            $this->boxclient = new boxclient($this->api_key);
        }
    }

    /**
     * check if user logged
     * @return boolean
     */
    public function check_login() {
        return $this->logged;
    }

    /**
     * reset auth token
     *
     * @return string
     */
    public function logout() {
        // reset auth token
        set_user_preference($this->setting_prefix . '_auth_token', '');
        return $this->print_login();
    }

    /**
     * Save settings
     *
     * @param array $options
     * @return mixed
     */
    public function set_option($options = array()) {
        if (!empty($options['api_key'])) {
            set_config('api_key', trim($options['api_key']), 'boxnet');
        }
        unset($options['api_key']);
        $ret = parent::set_option($options);
        return $ret;
    }

    /**
     * Get settings
     *
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '') {
        if($config==='api_key') {
            return trim(get_config('boxnet', 'api_key'));
        } else {
            $options['api_key'] = trim(get_config('boxnet', 'api_key'));
        }
        $options = parent::get_option($config);
        return $options;
    }

    /**
     * Search files from box.net
     *
     * @global object $OUTPUT
     * @param string $search_text
     * @return mixed
     */
    public function search($search_text) {
        global $OUTPUT;
        $list = array();
        $ret  = array();
        $tree = $this->boxclient->getAccountTree();
        if (!empty($tree)) {
            $filenames = $tree['file_name'];
            $fileids   = $tree['file_id'];
            $filesizes = $tree['file_size'];
            $filedates = $tree['file_date'];
            $fileicon  = $tree['thumbnail'];
            foreach ($filenames as $n=>$v){
                if(strstr(strtolower($v), strtolower($search_text)) !== false) {
                    $list[] = array('title'=>$v,
                            'size'=>$filesizes[$n],
                            'date'=>$filedates[$n],
                            'source'=>'http://box.net/api/1.0/download/'
                                .$this->auth_token.'/'.$fileids[$n],
                            'thumbnail' => $OUTPUT->pix_url(file_extension_icon($v, 32))->out(false));
                }
            }
        }
        $ret['list'] = array_filter($list, array($this, 'filter'));
        return $ret;
    }

    /**
     * Get file listing
     *
     * @param string $path
     * @return mixed
     */
    public function get_listing($path = '/', $page = ''){
        $list = array();
        $ret  = array();
        $ret['list'] = array();
        $tree = $this->boxclient->getfiletree($path);
        $ret['manage'] = 'http://www.box.net/files';
        $ret['path'] = array(array('name'=>'Root', 'path'=>0));
        if(!empty($tree)) {
            $ret['list'] = array_filter($tree, array($this, 'filter'));
        }
        return $ret;
    }

    /**
     * Return login form
     *
     * @return array
     */
    public function print_login(){
        $t = $this->boxclient->getTicket();
        if ($this->options['ajax']) {
            $popup_btn = new stdClass();
            $popup_btn->type = 'popup';
            $popup_btn->url = ' https://www.box.net/api/1.0/auth/' . $t['ticket'];

            $ret = array();
            $ret['login'] = array($popup_btn);
            return $ret;
        } else {
            echo '<table>';
            echo '<tr><td><label>'.get_string('username', 'repository_boxnet').'</label></td>';
            echo '<td><input type="text" name="boxusername" /></td></tr>';
            echo '<tr><td><label>'.get_string('password', 'repository_boxnet').'</label></td>';
            echo '<td><input type="password" name="boxpassword" /></td></tr>';
            echo '<input type="hidden" name="ticket" value="'.$t['ticket'].'" />';
            echo '</table>';
            echo '<input type="submit" value="'.get_string('enter', 'repository').'" />';
        }
    }

    /**
     * Names of the plugin settings
     * @return array
     */
    public static function get_type_option_names() {
        return array('api_key', 'pluginname');
    }

    /**
     * Store the auth token returned by box.net
     */
    public function callback() {
        $this->auth_token  = optional_param('auth_token', '', PARAM_TEXT);
        set_user_preference($this->setting_prefix . '_auth_token',    $this->auth_token);
    }

    /**
     * Add Plugin settings input to Moodle form
     * @param object $mform
     */
    public function type_config_form($mform) {
        global $CFG;
        parent::type_config_form($mform);
        $public_account = get_config('boxnet', 'public_account');
        $api_key = get_config('boxnet', 'api_key');
        if (empty($api_key)) {
            $api_key = '';
        }
        $strrequired = get_string('required');
        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_boxnet'), array('value'=>$api_key,'size' => '40'));
        $mform->addRule('api_key', $strrequired, 'required', null, 'client');
        $mform->addElement('static', null, '',  get_string('information','repository_boxnet'));

        //retrieve the flickr instances
        $params = array();
        $params['context'] = array();
        //$params['currentcontext'] = $this->context;
        $params['onlyvisible'] = false;
        $params['type'] = 'boxnet';
        $instances = repository::get_instances($params);
        if (empty($instances)) {
            $callbackurl = get_string('callbackwarning', 'repository_boxnet');
            $mform->addElement('static', null, '',  $callbackurl);
        } else {
            $instance = array_shift($instances);
            $callbackurl = $CFG->wwwroot.'/repository/repository_callback.php?repo_id='.$instance->id;
            $mform->addElement('static', 'callbackurl', '', get_string('callbackurltext', 'repository_boxnet', $callbackurl));
        }
    }
    /**
     * Box.net supports file linking and copying
     * @return string
     */
    public function supported_returntypes() {
        return FILE_INTERNAL | FILE_EXTERNAL;
    }
}

