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

/**
 * Repository to access Dropbox files
 *
 * @package    repository_dropbox
 * @copyright  2010 Dongsheng Cai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_dropbox extends repository {
    /**
     * @var dropbox     The instance of dropbox client.
     */
    private $dropbox;

    /**
     * @var int         The maximum file size to cache in the moodle filepool.
     */
    public $cachelimit = null;

    /**
     * Constructor of dropbox plugin.
     *
     * @inheritDocs
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = []) {
        $options['page'] = optional_param('p', 1, PARAM_INT);
        parent::__construct($repositoryid, $context, $options);

        $returnurl = new moodle_url('/repository/repository_callback.php', [
                'callback'  => 'yes',
                'repo_id'   => $repositoryid,
                'sesskey'   => sesskey(),
            ]);

        // Create the dropbox API instance.
        $key = get_config('dropbox', 'dropbox_key');
        $secret = get_config('dropbox', 'dropbox_secret');
        $this->dropbox = new repository_dropbox\dropbox(
                $key,
                $secret,
                $returnurl
            );
    }

    /**
     * Repository method to serve the referenced file.
     *
     * @inheritDocs
     */
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, array $options = null) {
        $reference = $this->unpack_reference($storedfile->get_reference());

        $maxcachesize = $this->max_cache_bytes();
        if (empty($maxcachesize)) {
            // Always cache the file, regardless of size.
            $cachefile = true;
        } else {
            // Size available. Only cache if it is under maxcachesize.
            $cachefile = $storedfile->get_filesize() < $maxcachesize;
        }

        if (!$cachefile) {
            \core\session\manager::write_close();
            header('Location: ' . $this->get_file_download_link($reference->url));
            die;
        }

        try {
            $this->import_external_file_contents($storedfile, $this->max_cache_bytes());
            if (!is_array($options)) {
                $options = array();
            }
            $options['sendcachedexternalfile'] = true;
            \core\session\manager::write_close();
            send_stored_file($storedfile, $lifetime, $filter, $forcedownload, $options);
        } catch (moodle_exception $e) {
            // Redirect to Dropbox, it will show the error.
            // Note: We redirect to Dropbox shared link, not to the download link here!
            \core\session\manager::write_close();
            header('Location: ' . $reference->url);
            die;
        }
    }

    /**
     * Return human readable reference information.
     * {@link stored_file::get_reference()}
     *
     * @inheritDocs
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
            // Indicate this is from dropbox with path.
            return $details;
        } else {
            if (isset($ref->url)) {
                $details = $detailsprefix. ': '. $ref->url;
            }
            return get_string('lostsource', 'repository', $details);
        }
    }

    /**
     * Cache file from external repository by reference.
     * {@link repository::get_file_reference()}
     * {@link repository::get_file()}
     * Invoked at MOODLE/repository/repository_ajax.php.
     *
     * @inheritDocs
     */
    public function cache_file_by_reference($reference, $storedfile) {
        try {
            $this->import_external_file_contents($storedfile, $this->max_cache_bytes());
        } catch (Exception $e) {
            // Cache failure should not cause a fatal error. This is only a nice-to-have feature.
        }
    }

    /**
     * Return the source information.
     *
     * The result of the function is stored in files.source field. It may be analysed
     * when the source file is lost or repository may use it to display human-readable
     * location of reference original.
     *
     * This method is called when file is picked for the first time only. When file
     * (either copy or a reference) is already in moodle and it is being picked
     * again to another file area (also as a copy or as a reference), the value of
     * files.source is copied.
     *
     * @inheritDocs
     */
    public function get_file_source_info($source) {
        global $USER;
        return 'Dropbox ('.fullname($USER).'): ' . $source;
    }

    /**
     * Prepare file reference information.
     *
     * @inheritDocs
     */
    public function get_file_reference($source) {
        global $USER;
        $reference = new stdClass;
        $reference->userid = $USER->id;
        $reference->username = fullname($USER);
        $reference->path = $source;

        // Determine whether we are downloading the file, or should use a file reference.
        $usefilereference = optional_param('usefilereference', false, PARAM_BOOL);
        if ($usefilereference) {
            if ($data = $this->dropbox->get_file_share_info($source)) {
                $reference = (object) array_merge((array) $data, (array) $reference);
            }
        }

        return serialize($reference);
    }

    /**
     * Return file URL for external link.
     *
     * @inheritDocs
     */
    public function get_link($reference) {
        $unpacked = $this->unpack_reference($reference);

        return $this->get_file_download_link($unpacked->url);
    }

    /**
     * Downloads a file from external repository and saves it in temp dir.
     *
     * @inheritDocs
     */
    public function get_file($reference, $saveas = '') {
        $unpacked = $this->unpack_reference($reference);

        // This is a shared link, and hopefully it is still active.
        $downloadlink = $this->get_file_download_link($unpacked->url);

        $saveas = $this->prepare_file($saveas);
        file_put_contents($saveas, fopen($downloadlink, 'r'));

        return ['path' => $saveas];
    }

    /**
     * Dropbox plugin supports all kinds of files.
     *
     * @inheritDocs
     */
    public function supported_filetypes() {
        return '*';
    }

    /**
     * User cannot use the external link to dropbox.
     *
     * @inheritDocs
     */
    public function supported_returntypes() {
        return FILE_INTERNAL | FILE_REFERENCE | FILE_EXTERNAL;
    }

    /**
     * Get dropbox files.
     *
     * @inheritDocs
     */
    public function get_listing($path = '', $page = '1') {
        if (empty($path) || $path == '/') {
            $path = '';
        } else {
            $path = file_correct_filepath($path);
        }

        $list = [
                'list'      => [],
                'manage'    => 'https://www.dropbox.com/home',
                'logouturl' => 'https://www.dropbox.com/logout',
                'message'   => get_string('logoutdesc', 'repository_dropbox'),
                'dynload'   => true,
                'path'      => $this->process_breadcrumbs($path),
            ];

        // Note - we deliberately do not catch the coding exceptions here.
        try {
            $result = $this->dropbox->get_listing($path);
        } catch (\repository_dropbox\authentication_exception $e) {
            // The token has expired.
            return $this->print_login();
        } catch (\repository_dropbox\dropbox_exception $e) {
            // There was some other form of non-coding failure.
            // This could be a rate limit, or it could be a server-side error.
            // Just return early instead.
            return $list;
        }

        if (!is_object($result) || empty($result)) {
            return $list;
        }

        if (empty($result->entries) or !is_array($result->entries)) {
            return $list;
        }

        $list['list'] = $this->process_entries($result->entries);
        return $list;
    }

    /**
     * Get dropbox files in the specified path.
     *
     * @param   string      $query      The search query
     * @param   int         $page       The page number
     * @return  array
     */
    public function search($query, $page = 0) {
        $list = [
                'list'      => [],
                'manage'    => 'https://www.dropbox.com/home',
                'logouturl' => 'https://www.dropbox.com/logout',
                'message'   => get_string('logoutdesc', 'repository_dropbox'),
                'dynload'   => true,
            ];

        // Note - we deliberately do not catch the coding exceptions here.
        try {
            $result = $this->dropbox->search($query);
        } catch (\repository_dropbox\authentication_exception $e) {
            // The token has expired.
            return $this->print_login();
        } catch (\repository_dropbox\dropbox_exception $e) {
            // There was some other form of non-coding failure.
            // This could be a rate limit, or it could be a server-side error.
            // Just return early instead.
            return $list;
        }

        if (!is_object($result) || empty($result)) {
            return $list;
        }

        if (empty($result->matches) or !is_array($result->matches)) {
            return $list;
        }

        $list['list'] = $this->process_entries($result->matches);
        return $list;
    }

    /**
     * Displays a thumbnail for current user's dropbox file.
     *
     * @inheritDocs
     */
    public function send_thumbnail($source) {
        $content = $this->dropbox->get_thumbnail($source);

        // Set 30 days lifetime for the image.
        // If the image is changed in dropbox it will have different revision number and URL will be different.
        // It is completely safe to cache the thumbnail in the browser for a long time.
        send_file($content, basename($source), 30 * DAYSECS, 0, true);
    }

    /**
     * Fixes references in DB that contains user credentials.
     *
     * @param   string      $packed     Content of DB field files_reference.reference
     * @return  string                  New serialized reference
     */
    protected function fix_old_style_reference($packed) {
        $ref = unserialize($packed);
        $ref = $this->dropbox->get_file_share_info($ref->path);
        if (!$ref || empty($ref->url)) {
            // Some error occurred, do not fix reference for now.
            return $packed;
        }

        $newreference = serialize($ref);
        if ($newreference !== $packed) {
            // We need to update references in the database.
            global $DB;
            $params = array(
                'newreference'  => $newreference,
                'newhash'       => sha1($newreference),
                'reference'     => $packed,
                'hash'          => sha1($packed),
                'repoid'        => $this->id,
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
                // The same reference already exists, we unlink all files from it,
                // link them to the current reference and remove the old one.
                $DB->execute('UPDATE {files} SET referencefileid = :refid
                    WHERE referencefileid = :existingrefid',
                    array('refid' => $refid, 'existingrefid' => $existingrefid));
                $DB->delete_records('files_reference', array('id' => $existingrefid));
            }

            // Update the reference.
            $params['refid'] = $refid;
            $DB->execute('UPDATE {files_reference}
                SET reference = :newreference, referencehash = :newhash
                WHERE id = :refid', $params);
        }
        return $newreference;
    }

    /**
     * Unpack the supplied serialized reference, fixing it if required.
     *
     * @param   string      $packed     The packed reference
     * @return  object                  The unpacked reference
     */
    protected function unpack_reference($packed) {
        $reference = unserialize($packed);
        if (empty($reference->url)) {
            // The reference is missing some information. Attempt to update it.
            return unserialize($this->fix_old_style_reference($packed));
        }

        return $reference;
    }

    /**
     * Converts a URL received from dropbox API function 'shares' into URL that
     * can be used to download/access file directly
     *
     * @param string $sharedurl
     * @return string
     */
    protected function get_file_download_link($sharedurl) {
        $url = new \moodle_url($sharedurl);
        $url->param('dl', 1);

        return $url->out(false);
    }

    /**
     * Logout from dropbox.
     *
     * @inheritDocs
     */
    public function logout() {
        $this->dropbox->logout();

        return $this->print_login();
    }

    /**
     * Check if moodle has got access token and secret.
     *
     * @inheritDocs
     */
    public function check_login() {
        return $this->dropbox->is_logged_in();
    }

    /**
     * Generate dropbox login url.
     *
     * @inheritDocs
     */
    public function print_login() {
        $url = $this->dropbox->get_login_url();
        if ($this->options['ajax']) {
            $ret = array();
            $btn = new \stdClass();
            $btn->type = 'popup';
            $btn->url = $url->out(false);
            $ret['login'] = array($btn);
            return $ret;
        } else {
            echo html_writer::link($url, get_string('login', 'repository'), array('target' => '_blank'));
        }
    }

    /**
     * Request access token.
     *
     * @inheritDocs
     */
    public function callback() {
        $this->dropbox->callback();
    }

    /**
     * Caches all references to Dropbox files in moodle filepool.
     *
     * Invoked by {@link repository_dropbox_cron()}. Only files smaller than
     * {@link repository_dropbox::max_cache_bytes()} and only files which
     * synchronisation timeout have not expired are cached.
     *
     * @inheritDocs
     */
    public function cron() {
        $fs = get_file_storage();
        $files = $fs->get_external_files($this->id);
        $fetchedreferences = [];
        foreach ($files as $file) {
            if (isset($fetchedreferences[$file->get_referencefileid()])) {
                continue;
            }
            try {
                // This call will cache all files that are smaller than max_cache_bytes()
                // and synchronise file size of all others.
                $this->import_external_file_contents($file, $this->max_cache_bytes());
                $fetchedreferences[$file->get_referencefileid()] = true;
            } catch (moodle_exception $e) {
                // If an exception is thrown, just continue. This is only a pre-fetch to help speed up general use.
            }
        }
    }

    /**
     * Add Plugin settings input to Moodle form.
     *
     * @inheritDocs
     */
    public static function type_config_form($mform, $classname = 'repository') {
        parent::type_config_form($mform);
        $key    = get_config('dropbox', 'dropbox_key');
        $secret = get_config('dropbox', 'dropbox_secret');

        if (empty($key)) {
            $key = '';
        }
        if (empty($secret)) {
            $secret = '';
        }

        $mform->addElement('text', 'dropbox_key', get_string('apikey', 'repository_dropbox'), array('value'=>$key,'size' => '40'));
        $mform->setType('dropbox_key', PARAM_RAW_TRIMMED);
        $mform->addElement('text', 'dropbox_secret', get_string('secret', 'repository_dropbox'), array('value'=>$secret,'size' => '40'));

        $mform->addRule('dropbox_key',    get_string('required'), 'required', null, 'client');
        $mform->addRule('dropbox_secret', get_string('required'), 'required', null, 'client');
        $mform->setType('dropbox_secret', PARAM_RAW_TRIMMED);
        $mform->addElement('static', null, '', get_string('instruction', 'repository_dropbox'));
        $mform->addElement('static', null,
                get_string('oauth2redirecturi', 'repository_dropbox'),
                self::get_oauth2callbackurl()->out()
            );

        $mform->addElement('text', 'dropbox_cachelimit', get_string('cachelimit', 'repository_dropbox'), array('size' => '40'));
        $mform->addRule('dropbox_cachelimit', null, 'numeric', null, 'client');
        $mform->setType('dropbox_cachelimit', PARAM_INT);
        $mform->addElement('static', 'dropbox_cachelimit_info', '',  get_string('cachelimit_info', 'repository_dropbox'));

    }

    /**
     * Set options.
     *
     * @param   array   $options
     * @return  mixed
     */
    public function set_option($options = []) {
        if (!empty($options['dropbox_key'])) {
            set_config('dropbox_key', trim($options['dropbox_key']), 'dropbox');
            unset($options['dropbox_key']);
        }
        if (!empty($options['dropbox_secret'])) {
            set_config('dropbox_secret', trim($options['dropbox_secret']), 'dropbox');
            unset($options['dropbox_secret']);
        }
        if (!empty($options['dropbox_cachelimit'])) {
            $this->cachelimit = (int) trim($options['dropbox_cachelimit']);
            set_config('dropbox_cachelimit', $this->cachelimit, 'dropbox');
            unset($options['dropbox_cachelimit']);
        }

        return parent::set_option($options);
    }

    /**
     * Get dropbox options
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '') {
        if ($config === 'dropbox_key') {
            return trim(get_config('dropbox', 'dropbox_key'));
        } else if ($config === 'dropbox_secret') {
            return trim(get_config('dropbox', 'dropbox_secret'));
        } else if ($config === 'dropbox_cachelimit') {
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
     * Return the OAuth 2 Redirect URI.
     *
     * @return  moodle_url
     */
    public static function get_oauth2callbackurl() {
        global $CFG;

        return new moodle_url('/admin/oauth2callback.php');
    }

    /**
     * Option names of dropbox plugin.
     *
     * @inheritDocs
     */
    public static function get_type_option_names() {
        return [
                'dropbox_key',
                'dropbox_secret',
                'pluginname',
                'dropbox_cachelimit',
            ];
    }

    /**
     * Performs synchronisation of an external file if the previous one has expired.
     *
     * This function must be implemented for external repositories supporting
     * FILE_REFERENCE, it is called for existing aliases when their filesize,
     * contenthash or timemodified are requested. It is not called for internal
     * repositories (see {@link repository::has_moodle_files()}), references to
     * internal files are updated immediately when source is modified.
     *
     * Referenced files may optionally keep their content in Moodle filepool (for
     * thumbnail generation or to be able to serve cached copy). In this
     * case both contenthash and filesize need to be synchronized. Otherwise repositories
     * should use contenthash of empty file and correct filesize in bytes.
     *
     * Note that this function may be run for EACH file that needs to be synchronised at the
     * moment. If anything is being downloaded or requested from external sources there
     * should be a small timeout. The synchronisation is performed to update the size of
     * the file and/or to update image and re-generated image preview. There is nothing
     * fatal if syncronisation fails but it is fatal if syncronisation takes too long
     * and hangs the script generating a page.
     *
     * Note: If you wish to call $file->get_filesize(), $file->get_contenthash() or
     * $file->get_timemodified() make sure that recursion does not happen.
     *
     * Called from {@link stored_file::sync_external_file()}
     *
     * @inheritDocs
     */
    public function sync_reference(stored_file $file) {
        global $CFG;

        if ($file->get_referencelastsync() + DAYSECS > time()) {
            // Only synchronise once per day.
            return false;
        }

        $reference = $this->unpack_reference($file->get_reference());
        if (!isset($reference->url)) {
            // The URL to sync with is missing.
            return false;
        }

        $c = new curl;
        $url = $this->get_file_download_link($reference->url);
        if (file_extension_in_typegroup($reference->path, 'web_image')) {
            $saveas = $this->prepare_file('');
            try {
                $result = $c->download_one($url, [], [
                        'filepath' => $saveas,
                        'timeout' => $CFG->repositorysyncimagetimeout,
                        'followlocation' => true,
                    ]);
                $info = $c->get_info();
                if ($result === true && isset($info['http_code']) && $info['http_code'] == 200) {
                    $file->set_synchronised_content_from_file($saveas);
                    return true;
                }
            } catch (Exception $e) {
                // IF the download_one fails, we will attempt to download
                // again with get() anyway.
            }
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
     * Process a standard entries list.
     *
     * @param   array       $entries    The list of entries returned from the API
     * @return  array                   The manipulated entries for display in the file picker
     */
    protected function process_entries(array $entries) {
        global $OUTPUT;

        $dirslist   = [];
        $fileslist  = [];
        foreach ($entries as $entry) {
            $entrydata = $entry;
            if (isset($entrydata->metadata)) {
                // If this is metadata, fetch the metadata content.
                // We only use the consistent parts of the file, folder, and metadata.
                $entrydata = $entrydata->metadata;
            }
            if ($entrydata->{".tag"} === "folder") {
                $dirslist[] = [
                        'title'             => $entrydata->name,
                        // Use the display path here rather than lower.
                        // Dropbox is case insensitive but this leads to more accurate breadcrumbs.
                        'path'              => file_correct_filepath($entrydata->path_display),
                        'thumbnail'         => $OUTPUT->image_url(file_folder_icon(64))->out(false),
                        'thumbnail_height'  => 64,
                        'thumbnail_width'   => 64,
                        'children'          => array(),
                    ];
            } else if ($entrydata->{".tag"} === "file") {
                $fileslist[] = [
                        'title'             => $entrydata->name,
                        // Use the path_lower here to make life easier elsewhere.
                        'source'            => $entrydata->path_lower,
                        'size'              => $entrydata->size,
                        'date'              => strtotime($entrydata->client_modified),
                        'thumbnail'         => $OUTPUT->image_url(file_extension_icon($entrydata->path_lower, 64))->out(false),
                        'realthumbnail'     => $this->get_thumbnail_url($entrydata),
                        'thumbnail_height'  => 64,
                        'thumbnail_width'   => 64,
                    ];
            }
        }

        $fileslist = array_filter($fileslist, array($this, 'filter'));

        return array_merge($dirslist, array_values($fileslist));
    }

    /**
     * Process the breadcrumbs for a listing.
     *
     * @param   string      $path       The path to create breadcrumbs for
     * @return  array
     */
    protected function process_breadcrumbs($path) {
        // Process breadcrumb trail.
        // Note: Dropbox is case insensitive.
        // Without performing an additional API call, it isn't possible to get the path_display.
        // As a result, the path here is the path_lower.
        $breadcrumbs = [
            [
                'path' => '/',
                'name' => get_string('dropbox', 'repository_dropbox'),
            ],
        ];

        $path = rtrim($path, '/');
        $directories = explode('/', $path);
        $pathtodate = '';
        foreach ($directories as $directory) {
            if ($directory === '') {
                continue;
            }
            $pathtodate .= '/' . $directory;
            $breadcrumbs[] = [
                    'path'  => $pathtodate,
                    'name'  => $directory,
                ];
        }

        return $breadcrumbs;
    }

    /**
     * Grab the thumbnail URL for the specified entry.
     *
     * @param   object      $entry      The file entry as retrieved from the API
     * @return  moodle_url
     */
    protected function get_thumbnail_url($entry) {
        if ($this->dropbox->supports_thumbnail($entry)) {
            $thumburl = new moodle_url('/repository/dropbox/thumbnail.php', [
                // The id field in dropbox is unique - no need to specify a revision.
                'source'    => $entry->id,
                'path'      => $entry->path_lower,

                'repo_id'   => $this->id,
                'ctx_id'    => $this->context->id,
            ]);
            return $thumburl->out(false);
        }

        return '';
    }

    /**
     * Returns the maximum size of the Dropbox files to cache in moodle.
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
            $this->cachelimit = (int) get_config('dropbox', 'dropbox_cachelimit');
        }
        return $this->cachelimit;
    }
}

/**
 * Dropbox plugin cron task.
 */
function repository_dropbox_cron() {
    $instances = repository::get_instances(array('type'=>'dropbox'));
    foreach ($instances as $instance) {
        $instance->cron();
    }
}
