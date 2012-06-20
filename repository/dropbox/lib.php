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
 * This plugin is used to access user's dropbox files
 *
 * @since 2.0
 * @package    repository_dropbox
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

class repository_dropbox extends repository {
    /** @var dropbox the instance of dropbox client */
    private $dropbox;
    /** @var array files */
    public $files;
    /** @var bool flag of login status */
    public $logged=false;

    /** @var int cached file ttl */
    private $cachedfilettl = null;

    /**
     * Constructor of dropbox plugin
     *
     * @param int $repositoryid
     * @param stdClass $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        global $CFG;
        $options['page']    = optional_param('p', 1, PARAM_INT);
        parent::__construct($repositoryid, $context, $options);

        $this->setting = 'dropbox_';

        $this->dropbox_key = $this->get_option('dropbox_key');
        $this->dropbox_secret  = $this->get_option('dropbox_secret');

        // one day
        $this->cachedfilettl = 60 * 60 * 24;

        if (isset($options['access_key'])) {
            $this->access_key = $options['access_key'];
        } else {
            $this->access_key = get_user_preferences($this->setting.'_access_key', '');
        }
        if (isset($options['access_secret'])) {
            $this->access_secret = $options['access_secret'];
        } else {
            $this->access_secret = get_user_preferences($this->setting.'_access_secret', '');
        }

        if (!empty($this->access_key) && !empty($this->access_secret)) {
            $this->logged = true;
        }

        $callbackurl = new moodle_url($CFG->wwwroot.'/repository/repository_callback.php', array(
            'callback'=>'yes',
            'repo_id'=>$repositoryid
            ));

        $args = array(
            'oauth_consumer_key'=>$this->dropbox_key,
            'oauth_consumer_secret'=>$this->dropbox_secret,
            'oauth_callback' => $callbackurl->out(false),
            'api_root' => 'https://www.dropbox.com/1/oauth',
        );

        $this->dropbox = new dropbox($args);
    }

    /**
     * Set access key
     *
     * @param string $access_key
     */
    public function set_access_key($access_key) {
        $this->access_key = $access_key;
    }

    /**
     * Set access secret
     *
     * @param string $access_secret
     */
    public function set_access_secret($access_secret) {
        $this->access_secret = $access_secret;
    }


    /**
     * Check if moodle has got access token and secret
     *
     * @return bool
     */
    public function check_login() {
        return !empty($this->logged);
    }

    /**
     * Generate dropbox login url
     *
     * @return array
     */
    public function print_login() {
        $result = $this->dropbox->request_token();
        set_user_preference($this->setting.'_request_secret', $result['oauth_token_secret']);
        $url = $result['authorize_url'];
        if ($this->options['ajax']) {
            $ret = array();
            $popup_btn = new stdClass();
            $popup_btn->type = 'popup';
            $popup_btn->url = $url;
            $ret['login'] = array($popup_btn);
            return $ret;
        } else {
            echo '<a target="_blank" href="'.$url.'">'.get_string('login', 'repository').'</a>';
        }
    }

    /**
     * Request access token
     *
     * @return array
     */
    public function callback() {
        $token  = optional_param('oauth_token', '', PARAM_TEXT);
        $secret = get_user_preferences($this->setting.'_request_secret', '');
        $access_token = $this->dropbox->get_access_token($token, $secret);
        set_user_preference($this->setting.'_access_key', $access_token['oauth_token']);
        set_user_preference($this->setting.'_access_secret', $access_token['oauth_token_secret']);
    }

    /**
     * Get dropbox files
     *
     * @param string $path
     * @param int $page
     * @return array
     */
    public function get_listing($path = '', $page = '1') {
        global $OUTPUT;
        if (empty($path) || $path=='/') {
            $path = '/';
        } else {
            $path = file_correct_filepath($path);
        }
        $encoded_path = str_replace("%2F", "/", rawurlencode($path));

        $list = array();
        $list['list'] = array();
        $list['manage'] = false;
        $list['dynload'] = true;
        $list['nosearch'] = true;
        // process breadcrumb trail
        $list['path'] = array(
            array('name'=>get_string('dropbox', 'repository_dropbox'), 'path'=>'/')
        );

        $result = $this->dropbox->get_listing($encoded_path, $this->access_key, $this->access_secret);

        if (!is_object($result) || empty($result)) {
            return $list;
        }
        if (empty($result->path)) {
            $current_path = '/';
        } else {
            $current_path = file_correct_filepath($result->path);
        }

        $trail = '';
        if (!empty($path)) {
            $parts = explode('/', $path);
            if (count($parts) > 1) {
                foreach ($parts as $part) {
                    if (!empty($part)) {
                        $trail .= ('/'.$part);
                        $list['path'][] = array('name'=>$part, 'path'=>$trail);
                    }
                }
            } else {
                $list['path'][] = array('name'=>$path, 'path'=>$path);
            }
        }

        if (!empty($result->error)) {
            // reset access key
            set_user_preference($this->setting.'_access_key', '');
            set_user_preference($this->setting.'_access_secret', '');
            throw new repository_exception('repositoryerror', 'repository', '', $result->error);
        }
        if (empty($result->contents) or !is_array($result->contents)) {
            return $list;
        }
        $files = $result->contents;
        foreach ($files as $file) {
            if ($file->is_dir) {
                $list['list'][] = array(
                    'title' => substr($file->path, strpos($file->path, $current_path)+strlen($current_path)),
                    'path' => file_correct_filepath($file->path),
                    'size' => $file->size,
                    'date' => $file->modified,
                    'thumbnail' => $OUTPUT->pix_url(file_folder_icon(90))->out(false),
                    'children' => array(),
                );
            } else {
                $list['list'][] = array(
                    'title' => substr($file->path, strpos($file->path, $current_path)+strlen($current_path)),
                    'source' => $file->path,
                    'size' => $file->size,
                    'date' => $file->modified,
                    'thumbnail' => $OUTPUT->pix_url(file_extension_icon($file->path, 90))->out(false)
                );
            }
        }
        return $list;
    }
    /**
     * Logout from dropbox
     * @return array
     */
    public function logout() {
        set_user_preference($this->setting.'_access_key', '');
        set_user_preference($this->setting.'_access_secret', '');
        $this->access_key    = '';
        $this->access_secret = '';
        return $this->print_login();
    }

    /**
     * Set dropbox option
     * @param array $options
     * @return mixed
     */
    public function set_option($options = array()) {
        if (!empty($options['dropbox_key'])) {
            set_config('dropbox_key', trim($options['dropbox_key']), 'dropbox');
        }
        if (!empty($options['dropbox_secret'])) {
            set_config('dropbox_secret', trim($options['dropbox_secret']), 'dropbox');
        }
        unset($options['dropbox_key']);
        unset($options['dropbox_secret']);
        $ret = parent::set_option($options);
        return $ret;
    }

    /**
     * Get dropbox options
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '') {
        if ($config==='dropbox_key') {
            return trim(get_config('dropbox', 'dropbox_key'));
        } elseif ($config==='dropbox_secret') {
            return trim(get_config('dropbox', 'dropbox_secret'));
        } else {
            $options['dropbox_key'] = trim(get_config('dropbox', 'dropbox_key'));
            $options['dropbox_secret'] = trim(get_config('dropbox', 'dropbox_secret'));
        }
        $options = parent::get_option($config);
        return $options;
    }

    /**
     *
     * @param string $photo_id
     * @param string $file
     * @return string
     */
    public function get_file($filepath, $saveas = '') {
        $this->dropbox->set_access_token($this->access_key, $this->access_secret);
        $saveas = $this->prepare_file($saveas);
        return $this->dropbox->get_file($filepath, $saveas);
    }
    /**
     * Add Plugin settings input to Moodle form
     *
     * @param object $mform
     */
    public static function type_config_form($mform, $classname = 'repository') {
        global $CFG;
        parent::type_config_form($mform);
        $key    = get_config('dropbox', 'dropbox_key');
        $secret = get_config('dropbox', 'dropbox_secret');

        if (empty($key)) {
            $key = '';
        }
        if (empty($secret)) {
            $secret = '';
        }

        $strrequired = get_string('required');

        $mform->addElement('text', 'dropbox_key', get_string('apikey', 'repository_dropbox'), array('value'=>$key,'size' => '40'));
        $mform->addElement('text', 'dropbox_secret', get_string('secret', 'repository_dropbox'), array('value'=>$secret,'size' => '40'));

        $mform->addRule('dropbox_key', $strrequired, 'required', null, 'client');
        $mform->addRule('dropbox_secret', $strrequired, 'required', null, 'client');
        $str_getkey = get_string('instruction', 'repository_dropbox');
        $mform->addElement('static', null, '',  $str_getkey);
    }

    /**
     * Option names of dropbox plugin
     *
     * @return array
     */
    public static function get_type_option_names() {
        return array('dropbox_key', 'dropbox_secret', 'pluginname');
    }

    /**
     * Dropbox plugin supports all kinds of files
     *
     * @return array
     */
    public function supported_filetypes() {
        return '*';
    }

    /**
     * User cannot use the external link to dropbox
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL | FILE_REFERENCE;
    }

    /**
     * Prepare file reference information
     *
     * @param string $source
     * @return string file referece
     */
    public function get_file_reference($source) {
        $reference = new stdClass;
        $reference->path = $source;
        $reference->access_key = get_user_preferences($this->setting.'_access_key', '');
        $reference->access_secret = get_user_preferences($this->setting.'_access_secret', '');
        return serialize($reference);
    }

    /**
     * Returns information about file in this repository by reference
     * {@link repository::get_file_reference()}
     * {@link repository::get_file()}
     *
     * Returns null if file not found or is not readable
     *
     * @param stdClass $reference file reference db record
     * @return null|stdClass that has 'filepath' property
     */
    public function get_file_by_reference($reference) {
        $reference  = unserialize($reference->reference);
        $cachedfilepath = cache_file::get($reference, array('ttl' => $this->cachedfilettl));
        if ($cachedfilepath === false) {
            // Cache the file.
            $this->set_access_key($reference->access_key);
            $this->set_access_secret($reference->access_secret);
            $path = $this->get_file($reference->path);
            $cachedfilepath = cache_file::create_from_file($reference, $path['path']);
        }
        if ($cachedfilepath && is_readable($cachedfilepath)) {
            return (object)array('filepath' => $cachedfilepath);
        } else {
            return null;
        }
    }

    /**
     * Get file from external repository by reference
     * {@link repository::get_file_reference()}
     * {@link repository::get_file()}
     *
     * @param string $reference this reference is generated by
     *                          repository::get_file_reference()
     * @param stored_file $storedfile created file reference
     */
    public function cache_file_by_reference($reference, $storedfile) {
        $reference  = unserialize($reference);
        $path = $this->get_file($reference->path);
        cache_file::create_from_file($reference, $path['path']);
    }

    /**
     * Return human readable reference information
     * {@link stored_file::get_reference()}
     *
     * @param string $reference
     * @param int $filestatus status of the file, 0 - ok, 666 - source missing
     * @return string
     */
    public function get_reference_details($reference, $filestatus = 0) {
        $ref  = unserialize($reference);
        $details = $this->get_name();
        if (isset($ref->path)) {
            $details .=  ': '. $ref->path;
        }
        if (isset($ref->path) && !$filestatus) {
            // Indicate this is from dropbox with path
            return $details;
        } else {
            return get_string('lostsource', 'repository', $details);
        }
    }

    /**
     * Return the source information
     *
     * @param stdClass $filepath
     * @return string|null
     */
    public function get_file_source_info($filepath) {
        return 'Dropbox: ' . $filepath;
    }

    /**
     * Repository method to serve the referenced file
     *
     * This method is ivoked from {@link send_stored_file()}.
     * Dropbox repository first caches the file by reading it into temporary folder and then
     * serves from there.
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param int $lifetime Number of seconds before the file should expire from caches (default 24 hours)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime=86400 , $filter=0, $forcedownload=false, array $options = null) {
        $fileinfo = $this->get_file_by_reference((object)array('reference' => $storedfile->get_reference()));
        if ($fileinfo && !empty($fileinfo->filepath) && is_readable($fileinfo->filepath)) {
            $filename = $storedfile->get_filename();
            if ($options && isset($options['filename'])) {
                $filename = $options['filename'];
            }
            $dontdie = ($options && isset($options['dontdie']));
            send_file($fileinfo->filepath, $filename, $lifetime , $filter, false, $forcedownload, '', $dontdie);
        } else {
            send_file_not_found();
        }
    }

    public function cron() {
        $fs = get_file_storage();
        $files = $fs->get_external_files($this->id);
        foreach ($files as $file) {
            $reference = unserialize($file->get_reference());

            $cachedfile = cache_file::get($reference);
            if ($cachedfile === false) {
                // Re-fetch resource.
                $this->set_access_key($reference->access_key);
                $this->set_access_secret($reference->access_secret);
                $path = $this->get_file($reference->path);
                cache_file::create_from_file($reference, $path['path']);
            }
        }
    }
}

/**
 * Dropbox plugin cron task
 */
function repository_dropbox_cron() {
    $instances = repository::get_instances(array('type'=>'dropbox'));
    foreach ($instances as $instance) {
        $instance->cron();
    }
}
