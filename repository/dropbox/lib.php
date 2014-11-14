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
 * @since Moodle 2.0
 * @package    repository_dropbox
 * @copyright  2012 Marina Glancy
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

/**
 * Repository to access Dropbox files
 *
 * @package    repository_dropbox
 * @copyright  2010 Dongsheng Cai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_dropbox extends repository {
    /** @var dropbox the instance of dropbox client */
    private $dropbox;
    /** @var array files */
    public $files;
    /** @var bool flag of login status */
    public $logged=false;
    /** @var int maximum size of file to cache in moodle filepool */
    public $cachelimit=null;

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
            'api_root' => 'https://api.dropbox.com/1/oauth',
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
        $list['manage'] = 'https://www.dropbox.com/home';
        $list['dynload'] = true;
        $list['nosearch'] = true;
        $list['logouturl'] = 'https://www.dropbox.com/logout';
        $list['message'] = get_string('logoutdesc', 'repository_dropbox');
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
        $dirslist = array();
        $fileslist = array();
        foreach ($files as $file) {
            if ($file->is_dir) {
                $dirslist[] = array(
                    'title' => substr($file->path, strpos($file->path, $current_path)+strlen($current_path)),
                    'path' => file_correct_filepath($file->path),
                    'date' => strtotime($file->modified),
                    'thumbnail' => $OUTPUT->pix_url(file_folder_icon(64))->out(false),
                    'thumbnail_height' => 64,
                    'thumbnail_width' => 64,
                    'children' => array(),
                );
            } else {
                $thumbnail = null;
                if ($file->thumb_exists) {
                    $thumburl = new moodle_url('/repository/dropbox/thumbnail.php',
                            array('repo_id' => $this->id,
                                'ctx_id' => $this->context->id,
                                'source' => $file->path,
                                'rev' => $file->rev // include revision to avoid cache problems
                            ));
                    $thumbnail = $thumburl->out(false);
                }
                $fileslist[] = array(
                    'title' => substr($file->path, strpos($file->path, $current_path)+strlen($current_path)),
                    'source' => $file->path,
                    'size' => $file->bytes,
                    'date' => strtotime($file->modified),
                    'thumbnail' => $OUTPUT->pix_url(file_extension_icon($file->path, 64))->out(false),
                    'realthumbnail' => $thumbnail,
                    'thumbnail_height' => 64,
                    'thumbnail_width' => 64,
                );
            }
        }
        $fileslist = array_filter($fileslist, array($this, 'filter'));
        $list['list'] = array_merge($dirslist, array_values($fileslist));
        return $list;
    }

    /**
     * Displays a thumbnail for current user's dropbox file
     *
     * @param string $string
     */
    public function send_thumbnail($source) {
        global $CFG;
        $saveas = $this->prepare_file('');
        try {
            $access_key = get_user_preferences($this->setting.'_access_key', '');
            $access_secret = get_user_preferences($this->setting.'_access_secret', '');
            $this->dropbox->set_access_token($access_key, $access_secret);
            $this->dropbox->get_thumbnail($source, $saveas, $CFG->repositorysyncimagetimeout);
            $content = file_get_contents($saveas);
            unlink($saveas);
            // set 30 days lifetime for the image. If the image is changed in dropbox it will have
            // different revision number and URL will be different. It is completely safe
            // to cache thumbnail in the browser for a long time
            send_file($content, basename($source), 30*24*60*60, 0, true);
        } catch (Exception $e) {}
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
        if (!empty($options['dropbox_cachelimit'])) {
            $this->cachelimit = (int)trim($options['dropbox_cachelimit']);
            set_config('dropbox_cachelimit', $this->cachelimit, 'dropbox');
        }
        unset($options['dropbox_key']);
        unset($options['dropbox_secret']);
        unset($options['dropbox_cachelimit']);
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
        } elseif ($config==='dropbox_cachelimit') {
            return $this->max_cache_bytes();
        } else {
            $options = parent::get_option();
            $options['dropbox_key'] = trim(get_config('dropbox', 'dropbox_key'));
            $options['dropbox_secret'] = trim(get_config('dropbox', 'dropbox_secret'));
            $options['dropbox_cachelimit'] = $this->max_cache_bytes();
        }
        return $options;
    }

    /**
     * Fixes references in DB that contains user credentials
     *
     * @param string $reference contents of DB field files_reference.reference
     */
    public function fix_old_style_reference($reference) {
        global $CFG;
        $ref = unserialize($reference);
        if (!isset($ref->url)) {
            $this->dropbox->set_access_token($ref->access_key, $ref->access_secret);
            $ref->url = $this->dropbox->get_file_share_link($ref->path, $CFG->repositorygetfiletimeout);
            if (!$ref->url) {
                // some error occurred, do not fix reference for now
                return $reference;
            }
        }
        unset($ref->access_key);
        unset($ref->access_secret);
        $newreference = serialize($ref);
        if ($newreference !== $reference) {
            // we need to update references in the database
            global $DB;
            $params = array(
                'newreference' => $newreference,
                'newhash' => sha1($newreference),
                'reference' => $reference,
                'hash' => sha1($reference),
                'repoid' => $this->id
            );
            $refid = $DB->get_field_sql('SELECT id FROM {files_reference}
                WHERE reference = :reference AND referencehash = :hash
                AND repositoryid = :repoid', $params);
            if (!$refid) {
                return $newreference;
            }
            $existingrefid = $DB->get_field_sql('SELECT id FROM {files_reference}
                    WHERE reference = :newreference AND referencehash = :newhash
                    AND repositoryid = :repoid', $params);
            if ($existingrefid) {
                // the same reference already exists, we unlink all files from it,
                // link them to the current reference and remove the old one
                $DB->execute('UPDATE {files} SET referencefileid = :refid
                    WHERE referencefileid = :existingrefid',
                    array('refid' => $refid, 'existingrefid' => $existingrefid));
                $DB->delete_records('files_reference', array('id' => $existingrefid));
            }
            // update the reference
            $params['refid'] = $refid;
            $DB->execute('UPDATE {files_reference}
                SET reference = :newreference, referencehash = :newhash
                WHERE id = :refid', $params);
        }
        return $newreference;
    }

    /**
     * Converts a URL received from dropbox API function 'shares' into URL that
     * can be used to download/access file directly
     *
     * @param string $sharedurl
     * @return string
     */
    private function get_file_download_link($sharedurl) {
        return preg_replace('|^(\w*://)www(.dropbox.com)|','\1dl\2',$sharedurl);
    }

    /**
     * Downloads a file from external repository and saves it in temp dir
     *
     * @throws moodle_exception when file could not be downloaded
     *
     * @param string $reference the content of files.reference field or result of
     * function {@link repository_dropbox::get_file_reference()}
     * @param string $saveas filename (without path) to save the downloaded file in the
     * temporary directory, if omitted or file already exists the new filename will be generated
     * @return array with elements:
     *   path: internal location of the file
     *   url: URL to the source (from parameters)
     */
    public function get_file($reference, $saveas = '') {
        global $CFG;
        $ref = unserialize($reference);
        $saveas = $this->prepare_file($saveas);
        if (isset($ref->access_key) && isset($ref->access_secret) && isset($ref->path)) {
            $this->dropbox->set_access_token($ref->access_key, $ref->access_secret);
            return $this->dropbox->get_file($ref->path, $saveas, $CFG->repositorygetfiletimeout);
        } else if (isset($ref->url)) {
            $c = new curl;
            $url = $this->get_file_download_link($ref->url);
            $result = $c->download_one($url, null, array('filepath' => $saveas, 'timeout' => $CFG->repositorygetfiletimeout, 'followlocation' => true));
            $info = $c->get_info();
            if ($result !== true || !isset($info['http_code']) || $info['http_code'] != 200) {
                throw new moodle_exception('errorwhiledownload', 'repository', '', $result);
            }
            return array('path'=>$saveas, 'url'=>$url);
        }
        throw new moodle_exception('cannotdownload', 'repository');
    }
    /**
     * Add Plugin settings input to Moodle form
     *
     * @param moodleform $mform Moodle form (passed by reference)
     * @param string $classname repository class name
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
        $mform->setType('dropbox_key', PARAM_RAW_TRIMMED);
        $mform->addElement('text', 'dropbox_secret', get_string('secret', 'repository_dropbox'), array('value'=>$secret,'size' => '40'));

        $mform->addRule('dropbox_key', $strrequired, 'required', null, 'client');
        $mform->addRule('dropbox_secret', $strrequired, 'required', null, 'client');
        $mform->setType('dropbox_secret', PARAM_RAW_TRIMMED);
        $str_getkey = get_string('instruction', 'repository_dropbox');
        $mform->addElement('static', null, '',  $str_getkey);

        $mform->addElement('text', 'dropbox_cachelimit', get_string('cachelimit', 'repository_dropbox'), array('size' => '40'));
        $mform->addRule('dropbox_cachelimit', null, 'numeric', null, 'client');
        $mform->setType('dropbox_cachelimit', PARAM_INT);
        $mform->addElement('static', 'dropbox_cachelimit_info', '',  get_string('cachelimit_info', 'repository_dropbox'));
    }

    /**
     * Option names of dropbox plugin
     *
     * @return array
     */
    public static function get_type_option_names() {
        return array('dropbox_key', 'dropbox_secret', 'pluginname', 'dropbox_cachelimit');
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
        return FILE_INTERNAL | FILE_REFERENCE | FILE_EXTERNAL;
    }

    /**
     * Return file URL for external link
     *
     * @param string $reference the result of get_file_reference()
     * @return string
     */
    public function get_link($reference) {
        global $CFG;
        $ref = unserialize($reference);
        if (!isset($ref->url)) {
            $this->dropbox->set_access_token($ref->access_key, $ref->access_secret);
            $ref->url = $this->dropbox->get_file_share_link($ref->path, $CFG->repositorygetfiletimeout);
        }
        return $this->get_file_download_link($ref->url);
    }

    /**
     * Prepare file reference information
     *
     * @param string $source
     * @return string file referece
     */
    public function get_file_reference($source) {
        global $USER, $CFG;
        $reference = new stdClass;
        $reference->path = $source;
        $reference->userid = $USER->id;
        $reference->username = fullname($USER);
        $reference->access_key = get_user_preferences($this->setting.'_access_key', '');
        $reference->access_secret = get_user_preferences($this->setting.'_access_secret', '');

        // by API we don't know if we need this reference to just download a file from dropbox
        // into moodle filepool or create a reference. Since we need to create a shared link
        // only in case of reference we analyze the script parameter
        $usefilereference = optional_param('usefilereference', false, PARAM_BOOL);
        if ($usefilereference) {
            $this->dropbox->set_access_token($reference->access_key, $reference->access_secret);
            $url = $this->dropbox->get_file_share_link($source, $CFG->repositorygetfiletimeout);
            if ($url) {
                unset($reference->access_key);
                unset($reference->access_secret);
                $reference->url = $url;
            }
        }
        return serialize($reference);
    }

    public function sync_reference(stored_file $file) {
        global $CFG;

        if ($file->get_referencelastsync() + DAYSECS > time()) {
            // Synchronise not more often than once a day.
            return false;
        }
        $ref = unserialize($file->get_reference());
        if (!isset($ref->url)) {
            // this is an old-style reference in DB. We need to fix it
            $ref = unserialize($this->fix_old_style_reference($file->get_reference()));
        }
        if (!isset($ref->url)) {
            return false;
        }
        $c = new curl;
        $url = $this->get_file_download_link($ref->url);
        if (file_extension_in_typegroup($ref->path, 'web_image')) {
            $saveas = $this->prepare_file('');
            try {
                $result = $c->download_one($url, array(), array('filepath' => $saveas, 'timeout' => $CFG->repositorysyncimagetimeout, 'followlocation' => true));
                $info = $c->get_info();
                if ($result === true && isset($info['http_code']) && $info['http_code'] == 200) {
                    $fs = get_file_storage();
                    list($contenthash, $filesize, $newfile) = $fs->add_file_to_pool($saveas);
                    $file->set_synchronized($contenthash, $filesize);
                    return true;
                }
            } catch (Exception $e) {}
        }
        $c->get($url, null, array('timeout' => $CFG->repositorysyncimagetimeout, 'followlocation' => true, 'nobody' => true));
        $info = $c->get_info();
        if (isset($info['http_code']) && $info['http_code'] == 200 &&
                array_key_exists('download_content_length', $info) &&
                $info['download_content_length'] >= 0) {
            $filesize = (int)$info['download_content_length'];
            $file->set_synchronized(null, $filesize);
            return true;
        }
        $file->set_missingsource();
        return true;
    }

    /**
     * Cache file from external repository by reference
     *
     * Dropbox repository regularly caches all external files that are smaller than
     * {@link repository_dropbox::max_cache_bytes()}
     *
     * @param string $reference this reference is generated by
     *                          repository::get_file_reference()
     * @param stored_file $storedfile created file reference
     */
    public function cache_file_by_reference($reference, $storedfile) {
        try {
            $this->import_external_file_contents($storedfile, $this->max_cache_bytes());
        } catch (Exception $e) {}
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
        global $USER;
        $ref  = unserialize($reference);
        $detailsprefix = $this->get_name();
        if (isset($ref->userid) && $ref->userid != $USER->id && isset($ref->username)) {
            $detailsprefix .= ' ('.$ref->username.')';
        }
        $details = $detailsprefix;
        if (isset($ref->path)) {
            $details .= ': '. $ref->path;
        }
        if (isset($ref->path) && !$filestatus) {
            // Indicate this is from dropbox with path
            return $details;
        } else {
            if (isset($ref->url)) {
                $details = $detailsprefix. ': '. $ref->url;
            }
            return get_string('lostsource', 'repository', $details);
        }
    }

    /**
     * Return the source information
     *
     * @param string $source
     * @return string
     */
    public function get_file_source_info($source) {
        global $USER;
        return 'Dropbox ('.fullname($USER).'): ' . $source;
    }

    /**
     * Returns the maximum size of the Dropbox files to cache in moodle
     *
     * Note that {@link repository_dropbox::sync_reference()} will try to cache images even
     * when they are bigger in order to generate thumbnails. However there is
     * a small timeout for downloading images for synchronisation and it will
     * probably fail if the image is too big.
     *
     * @return int
     */
    public function max_cache_bytes() {
        if ($this->cachelimit === null) {
            $this->cachelimit = (int)get_config('dropbox', 'dropbox_cachelimit');
        }
        return $this->cachelimit;
    }

    /**
     * Repository method to serve the referenced file
     *
     * This method is ivoked from {@link send_stored_file()}.
     * Dropbox repository first caches the file by reading it into temporary folder and then
     * serves from there.
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param int $lifetime Number of seconds before the file should expire from caches (null means $CFG->filelifetime)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, array $options = null) {
        $ref = unserialize($storedfile->get_reference());
        if ($storedfile->get_filesize() > $this->max_cache_bytes()) {
            header('Location: '.$this->get_file_download_link($ref->url));
            die;
        }
        try {
            $this->import_external_file_contents($storedfile, $this->max_cache_bytes());
            if (!is_array($options)) {
                $options = array();
            }
            $options['sendcachedexternalfile'] = true;
            send_stored_file($storedfile, $lifetime, $filter, $forcedownload, $options);
        } catch (moodle_exception $e) {
            // redirect to Dropbox, it will show the error.
            // We redirect to Dropbox shared link, not to download link here!
            header('Location: '.$ref->url);
            die;
        }
    }

    /**
     * Caches all references to Dropbox files in moodle filepool
     *
     * Invoked by {@link repository_dropbox_cron()}. Only files smaller than
     * {@link repository_dropbox::max_cache_bytes()} and only files which
     * synchronisation timeout have not expired are cached.
     */
    public function cron() {
        $fs = get_file_storage();
        $files = $fs->get_external_files($this->id);
        foreach ($files as $file) {
            try {
                // This call will cache all files that are smaller than max_cache_bytes()
                // and synchronise file size of all others
                $this->import_external_file_contents($file, $this->max_cache_bytes());
            } catch (moodle_exception $e) {}
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
