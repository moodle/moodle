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
 * This plugin is used to access flickr pictures
 *
 * @since Moodle 2.0
 * @package    repository_flickr
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir.'/flickrclient.php');

/**
 * This plugin is used to access user's private flickr repository
 *
 * @since Moodle 2.0
 * @package    repository_flickr
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_flickr extends repository {

    /** @var flickr_client */
    protected $flickr;

    /** @var string oauth consumer key */
    protected $api_key;

    /** @var string oauth consumer secret */
    protected $secret;

    /** @var string oauth access token */
    protected $accesstoken;

    /** @var string oauth access token secret */
    protected $accesstokensecret;

    public $photos;

    /**
     * Stores sizes of images to prevent multiple API call
     */
    static private $sizes = array();

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

        $this->api_key = $this->get_option('api_key');
        $this->secret  = $this->get_option('secret');

        $this->accesstoken = get_user_preferences('repository_flickr_access_token');
        $this->accesstokensecret = get_user_preferences('repository_flickr_access_token_secret');

        $callbackurl = new moodle_url('/repository/repository_callback.php', ['repo_id' => $repositoryid]);
        $this->flickr = new flickr_client($this->api_key, $this->secret, $callbackurl);
        $this->flickr->set_access_token($this->accesstoken, $this->accesstokensecret);
    }

    /**
     * Check if the user has authorized us to make requests to Flickr API.
     *
     * @return bool
     */
    public function check_login() {

        if (empty($this->accesstoken) || empty($this->accesstokensecret)) {
            return false;

        } else {
            return true;
        }
    }

    /**
     * Purge the stored access token and related user data.
     *
     * @return string
     */
    public function logout() {

        set_user_preference('repository_flickr_access_token', null);
        set_user_preference('repository_flickr_access_token_secret', null);

        $this->accesstoken = null;
        $this->accesstokensecret = null;

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
     * Show the interface to log in to Flickr..
     *
     * @return string|array
     */
    public function print_login() {

        $reqtoken = $this->flickr->request_token();
        $this->flickr->set_request_token_secret(['caller' => 'repository_flickr'], $reqtoken['oauth_token_secret']);

        // Even when the Flick auth docs states the "perms" argument is
        // optional, it does not work without it.
        $authurl = new moodle_url($reqtoken['authorize_url'], array('perms' => 'read'));

        if ($this->options['ajax']) {
            return [
                'login' => [
                    [
                        'type' => 'popup',
                        'url' => $authurl->out(false),
                    ],
                ],
            ];

        } else {
            echo '<a target="_blank" href="'.$authurl->out().'">'.get_string('login', 'repository').'</a>';
        }
    }

    /**
     * Search for the user's photos at Flickr
     *
     * @param string $searchtext Photos with title, description or tags containing the text will be returned
     * @param int $page Page number to load
     * @return array
     */
    public function search($searchtext, $page = 0) {

        $response = $this->flickr->call('photos.search', [
            'user_id' => 'me',
            'per_page' => 24,
            'extras' => 'original_format,url_sq,url_o,date_upload,last_update,owner_name,license',
            'page' => $page,
            'text' => $searchtext,
        ]);

        if ($response === false) {
            $this->logout();
            return [];
        }

        // Convert the response to the format expected by the filepicker.
        $ret = [
            'manage' => 'https://www.flickr.com/photos/organize',
            'list' => [],
            'pages' => $response->photos->pages,
            'total' => $response->photos->total,
            'perpage' => $response->photos->perpage,
            'page' => $response->photos->page,
        ];

        if (!empty($response->photos->photo)) {
            foreach ($response->photos->photo as $p) {
                if (empty($p->title)) {
                    $p->title = get_string('notitle', 'repository_flickr');
                }

                if (isset($p->originalformat)) {
                    $format = $p->originalformat;
                } else {
                    $format = 'jpg';
                }
                $format = '.'.$format;

                // Append extension to the file name.
                if (substr($p->title, strlen($p->title) - strlen($format)) != $format) {
                    $p->title .= $format;
                }

                // Perform a HEAD request to the image to obtain it's Content-Length.
                $curl = new curl();
                $curl->head($p->url_o);

                $ret['list'][] = [
                    'title' => $p->title,
                    'source' => $p->id,
                    'id' => $p->id,
                    'thumbnail' => $p->url_sq,
                    'datecreated' => $p->dateupload,
                    'datemodified' => $p->lastupdate,
                    'url' => $p->url_o,
                    'author' => $p->ownername,
                    'size' => (int)($curl->get_info()['download_content_length']),
                    'image_width' => $p->width_o,
                    'image_height' => $p->height_o,
                    'license' => $this->license4moodle((int) $p->license),
                ];
            }
        }

        // Filter file listing to display specific types only.
        $ret['list'] = array_filter($ret['list'], array($this, 'filter'));

        return $ret;
    }

    /**
     * Map Flickr license ID to those used internally by Moodle
     *
     * @param int $licenseid
     * @return string
     */
    public function license4moodle(int $licenseid): string {
        $license = [
            0 => 'allrightsreserved',
            1 => 'cc-nc-sa',
            2 => 'cc-nc',
            3 => 'cc-nc-nd',
            4 => 'cc',
            5 => 'cc-sa',
            6 => 'cc-nd',
            7 => 'other'
        ];
        return $license[$licenseid];
    }

    /**
     *
     * @param string $path
     * @param int $page
     * @return array
     */
    public function get_listing($path = '', $page = '') {
        return $this->search('', $page);
    }

    public function get_link($photoid) {
        return $this->flickr->get_photo_url($photoid);
    }

    /**
     *
     * @param string $photoid
     * @param string $file
     * @return string
     */
    public function get_file($photoid, $file = '') {
        return parent::get_file($this->flickr->get_photo_url($photoid), $file);
    }

    /**
     * Add Plugin settings input to Moodle form
     * @param object $mform
     */
    public static function type_config_form($mform, $classname = 'repository') {
        global $CFG;
        $api_key = get_config('flickr', 'api_key');
        $secret = get_config('flickr', 'secret');

        if (empty($api_key)) {
            $api_key = '';
        }
        if (empty($secret)) {
            $secret = '';
        }

        parent::type_config_form($mform);

        $strrequired = get_string('required');
        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_flickr'), array('value'=>$api_key,'size' => '40'));
        $mform->setType('api_key', PARAM_RAW_TRIMMED);
        $mform->addElement('text', 'secret', get_string('secret', 'repository_flickr'), array('value'=>$secret,'size' => '40'));
        $mform->setType('secret', PARAM_RAW_TRIMMED);

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

    /**
     * Return the source information
     *
     * @param string $photoid
     * @return string|null
     */
    public function get_file_source_info($photoid) {
        return $this->flickr->get_photo_url($photoid);
    }

    /**
     * Handle the oauth authorize callback
     *
     * This is to exchange the approved request token for an access token.
     */
    public function callback() {

        $token = required_param('oauth_token', PARAM_RAW);
        $verifier = required_param('oauth_verifier', PARAM_RAW);
        $secret = $this->flickr->get_request_token_secret(['caller' => 'repository_flickr']);

        // Exchange the request token for the access token.
        $accesstoken = $this->flickr->get_access_token($token, $secret, $verifier);

        // Store the access token and the access token secret in the user preferences.
        set_user_preference('repository_flickr_access_token', $accesstoken['oauth_token']);
        set_user_preference('repository_flickr_access_token_secret', $accesstoken['oauth_token_secret']);
    }
}
