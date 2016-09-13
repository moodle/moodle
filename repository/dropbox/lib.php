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
     * @var int         The maximum file size to cache in the moodle filepool.
     */
    public $cachelimit = null;

    /**
     * @var repository  The actual repository.
     */
    protected $legacy;

    /**
     * Constructor of dropbox plugin.
     *
     * @inheritDocs
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = []) {
        $this->legacy = new repository_dropbox_legacy($repositoryid, $context, $options);

        parent::__construct($repositoryid, $context, $options);
    }

    /**
     * Repository method to serve the referenced file.
     *
     * @inheritDocs
     */
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, array $options = null) {
        return $this->legacy->send_file($storedfile, $lifetime, $filter, $forcedownload, $options);
    }

    /**
     * Return human readable reference information.
     * {@link stored_file::get_reference()}
     *
     * @inheritDocs
     */
    public function get_reference_details($reference, $filestatus = 0) {
        return $this->legacy->get_reference_details($reference, $filestatus);
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
        return $this->legacy->cache_file_by_reference($reference, $storedfile);
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
        return $this->legacy->get_file_source_info($source);
    }

    /**
     * Prepare file reference information.
     *
     * @inheritDocs
     */
    public function get_file_reference($source) {
        return $this->legacy->get_file_reference($source);
    }

    /**
     * Return file URL for external link.
     *
     * @inheritDocs
     */
    public function get_link($reference) {
        return $this->legacy->get_link($reference);
    }

    /**
     * Downloads a file from external repository and saves it in temp dir.
     *
     * @inheritDocs
     */
    public function get_file($reference, $saveas = '') {
        return $this->legacy->get_file($reference, $saveas);
    }

    /**
     * Dropbox plugin supports all kinds of files.
     *
     * @inheritDocs
     */
    public function supported_filetypes() {
        return $this->legacy->supported_filetypes();
    }

    /**
     * User cannot use the external link to dropbox.
     *
     * @inheritDocs
     */
    public function supported_returntypes() {
        return $this->legacy->supported_returntypes();
    }

    /**
     * Get dropbox files.
     *
     * @inheritDocs
     */
    public function get_listing($path = '', $page = '1') {
        return $this->legacy->get_listing($path, $page);
    }

    /**
     * Get dropbox files in the specified path.
     *
     * @param   string      $query      The search query
     * @param   int         $page       The page number
     * @return  array
     */
    public function search($query, $page = 0) {
        return parent::search($query, $page);
    }

    /**
     * Displays a thumbnail for current user's dropbox file.
     *
     * @inheritDocs
     */
    public function send_thumbnail($source) {
        return $this->legacy->send_thumbnail($source);
    }

    /**
     * Fixes references in DB that contains user credentials.
     *
     * @param   string      $packed     Content of DB field files_reference.reference
     * @return  string                  New serialized reference
     */
    protected function fix_old_style_reference($packed) {
        return $this->legacy->fix_old_style_reference($packed);
    }
    /**
     * Logout from dropbox.
     *
     * @inheritDocs
     */
    public function logout() {
        return $this->legacy->logout();
    }

    /**
     * Check if moodle has got access token and secret.
     *
     * @inheritDocs
     */
    public function check_login() {
        return $this->legacy->check_login();
    }

    /**
     * Generate dropbox login url.
     *
     * @inheritDocs
     */
    public function print_login() {
        return $this->legacy->print_login();
    }

    /**
     * Request access token.
     *
     * @inheritDocs
     */
    public function callback() {
        return $this->legacy->callback();
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
        return $this->legacy->cron();
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
        return $this->legacy->sync_reference($file);
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
