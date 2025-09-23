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
 * This plugin is used to access Google Drive.
 *
 * @since Moodle 2.0
 * @package    repository_googledocs
 * @copyright  2009 Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/filebrowser/file_browser.php');
require_once($CFG->libdir . '/google/lib.php');

use repository_googledocs\helper;
use repository_googledocs\googledocs_content_search;

/**
 * Google Docs Plugin
 *
 * @since Moodle 2.0
 * @package    repository_googledocs
 * @copyright  2009 Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_googledocs extends repository {

    /**
     * OAuth 2 client
     * @var \core\oauth2\client
     */
    private $client = null;

    /**
     * OAuth 2 Issuer
     * @var \core\oauth2\issuer
     */
    private $issuer = null;

    /** @var string Defines the path node identifier for the repository root. */
    const REPOSITORY_ROOT_ID = 'repository_root';

    /** @var string Defines the path node identifier for the my drive root. */
    const MY_DRIVE_ROOT_ID = 'root';

    /** @var string Defines the path node identifier for the shared drives root. */
    const SHARED_DRIVES_ROOT_ID = 'shared_drives_root';

    /** @var string Defines the path node identifier for the content search root. */
    const SEARCH_ROOT_ID = 'search';

    /**
     * Constructor.
     *
     * @param int $repositoryid repository instance id.
     * @param int|stdClass $context a context id or context object.
     * @param array $options repository options.
     * @param int $readonly indicate this repo is readonly or not.
     * @return void
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array(), $readonly = 0) {
        global $PAGE;
        parent::__construct($repositoryid, $context, $options, $readonly = 0);

        try {
            $this->issuer = \core\oauth2\api::get_issuer(get_config('googledocs', 'issuerid'));
        } catch (dml_missing_record_exception $e) {
            $this->disabled = true;
        }

        if ($this->issuer && !$this->issuer->get('enabled')) {
            $this->disabled = true;
        }

        $PAGE->requires->js_call_amd('repository_googledocs/upload', 'init');
    }

    /**
     * Get a cached user authenticated oauth client.
     *
     * @param moodle_url $overrideurl - Use this url instead of the repo callback.
     * @return \core\oauth2\client
     */
    public function get_user_oauth_client($overrideurl = false) {
        if ($this->client) {
            return $this->client;
        }
        if ($overrideurl) {
            $returnurl = $overrideurl;
        } else {
            $returnurl = new moodle_url('/repository/repository_callback.php');
            $returnurl->param('callback', 'yes');
            $returnurl->param('repo_id', $this->id);
            $returnurl->param('sesskey', sesskey());
        }

        $this->client = \core\oauth2\api::get_user_oauth_client($this->issuer, $returnurl, Google_Service_Drive::DRIVE_FILE, true);

        return $this->client;
    }

    /**
     * Checks whether the user is authenticate or not.
     *
     * @return bool true when logged in.
     */
    public function check_login() {
        $client = $this->get_user_oauth_client();
        return $client->is_logged_in();
    }

    /**
     * Print or return the login form.
     *
     * @return void|array for ajax.
     */
    public function print_login() {
        $client = $this->get_user_oauth_client();
        $url = $client->get_login_url();

        if ($this->options['ajax']) {
            $popup = new stdClass();
            $popup->type = 'popup';
            $popup->url = $url->out(false);
            return array('login' => array($popup));
        } else {
            echo '<a target="_blank" href="'.$url->out(false).'">'.get_string('login', 'repository').'</a>';
        }
    }

    /**
     * Print the login in a popup.
     *
     * @param array|null $attr Custom attributes to be applied to popup div.
     */
    public function print_login_popup($attr = null) {
        global $OUTPUT, $PAGE;

        $client = $this->get_user_oauth_client(false);
        $url = new moodle_url($client->get_login_url());
        $state = $url->get_param('state') . '&reloadparent=true';
        $url->param('state', $state);

        $PAGE->set_pagelayout('embedded');
        echo $OUTPUT->header();

        $repositoryname = get_string('pluginname', 'repository_googledocs');

        $button = new single_button(
            $url,
            get_string('logintoaccount', 'repository', $repositoryname),
            'post',
            single_button::BUTTON_PRIMARY
        );
        $button->add_action(new popup_action('click', $url, 'Login'));
        $button->class = 'mdl-align';
        $button = $OUTPUT->render($button);
        echo html_writer::div($button, '', $attr);

        echo $OUTPUT->footer();
    }

    /**
     * Build the breadcrumb from a path.
     *
     * @deprecated since Moodle 3.11.
     * @param string $path to create a breadcrumb from.
     * @return array containing name and path of each crumb.
     */
    protected function build_breadcrumb($path) {
        debugging('The function build_breadcrumb() is deprecated, please use get_navigation() from the ' .
            'googledocs repository content classes instead.', DEBUG_DEVELOPER);

        $bread = explode('/', $path);
        $crumbtrail = '';
        foreach ($bread as $crumb) {
            list($id, $name) = $this->explode_node_path($crumb);
            $name = empty($name) ? $id : $name;
            $breadcrumb[] = array(
                'name' => $name,
                'path' => $this->build_node_path($id, $name, $crumbtrail)
            );
            $tmp = end($breadcrumb);
            $crumbtrail = $tmp['path'];
        }
        return $breadcrumb;
    }

    /**
     * Generates a safe path to a node.
     *
     * Typically, a node will be id|Name of the node.
     *
     * @deprecated since Moodle 3.11.
     * @param string $id of the node.
     * @param string $name of the node, will be URL encoded.
     * @param string $root to append the node on, must be a result of this function.
     * @return string path to the node.
     */
    protected function build_node_path($id, $name = '', $root = '') {
        debugging('The function build_node_path() is deprecated, please use ' .
            '\repository_googledocs\helper::build_node_path() instead.', DEBUG_DEVELOPER);

        $path = $id;
        if (!empty($name)) {
            $path .= '|' . urlencode($name);
        }
        if (!empty($root)) {
            $path = trim($root, '/') . '/' . $path;
        }
        return $path;
    }

    /**
     * Returns information about a node in a path.
     *
     * @deprecated since Moodle 3.11.
     * @see self::build_node_path()
     * @param string $node to extrat information from.
     * @return array about the node.
     */
    protected function explode_node_path($node) {
        debugging('The function explode_node_path() is deprecated, please use ' .
            '\repository_googledocs\helper::explode_node_path() instead.', DEBUG_DEVELOPER);

        if (strpos($node, '|') !== false) {
            list($id, $name) = explode('|', $node, 2);
            $name = urldecode($name);
        } else {
            $id = $node;
            $name = '';
        }
        $id = urldecode($id);
        return array(
            0 => $id,
            1 => $name,
            'id' => $id,
            'name' => $name
        );
    }

    /**
     * List the files and folders.
     *
     * @param  string $path path to browse.
     * @param  string $page page to browse.
     * @return array of result.
     */
    public function get_listing($path='', $page = '') {
        if (empty($path)) {
            $pluginname = get_string('pluginname', 'repository_googledocs');
            $path = helper::build_node_path('repository_root', $pluginname);
        }

        // Make sure the current path points to "My Drive" before listing files.
        $mydrive = get_string('mydrive', 'repository_googledocs');
        $mydrivepath = helper::build_node_path('root', $mydrive);
        if (!str_contains($path, $mydrivepath)) {
            $path = $path . '/' . $mydrivepath;
        }

        if (!$this->issuer->get('enabled')) {
            // Empty list of files for disabled repository.
            return [
                'dynload' => false,
                'list' => [],
                'nologin' => true,
            ];
        }

        // We analyse the path to extract what to browse.
        $trail = explode('/', $path);
        $uri = array_pop($trail);
        list($id, $name) = helper::explode_node_path($uri);
        $service = new repository_googledocs\rest($this->get_user_oauth_client());

        // Define the content class object and query which will be used to get the contents for this path.
        if ($id === self::SEARCH_ROOT_ID) {
            // The special keyword 'search' is the ID of the node. This is possible as we can set up a breadcrumb in
            // the search results. Therefore, we should use the content search object to get the results from the
            // previously performed search.
            $contentobj = new googledocs_content_search($service, $path);
            // We need to deconstruct the node name in order to obtain the search term and use it as a query.
            $query = str_replace(get_string('searchfor', 'repository_googledocs'), '', $name);
            $query = trim(str_replace("'", "", $query));
        } else {
            // Otherwise, return and use the appropriate (based on the path) content browser object.
            $contentobj = helper::get_browser($service, $path);
            // Use the node ID as a query.
            $query = $id;
        }

        return [
            'dynload' => true,
            'defaultreturntype' => $this->default_returntype(),
            'path' => $contentobj->get_navigation(),
            'list' => $contentobj->get_content_nodes($query, [$this, 'filter']),
            'uploadfile' => true,
            'uploadevent' => 'repository_googledocs_upload',
            'repo_id' => $this->id,
            'contextid' => $this->context->id,
            'sesskey' => sesskey(),
        ];
    }

    /**
     * Search throughout the Google Drive.
     *
     * @param string $searchtext text to search for.
     * @param int $page search page.
     * @return array of results.
     */
    public function search($searchtext, $page = 0) {
        // Construct the path to the repository root.
        $pluginname = get_string('pluginname', 'repository_googledocs');
        $rootpath = helper::build_node_path(self::REPOSITORY_ROOT_ID, $pluginname);
        // Construct the path to the search results node.
        // Currently, when constructing the search node name, the search term is concatenated to the lang string.
        // This was done deliberately so that we can easily and accurately obtain the search term from the search node
        // name later when navigating to the search results through the breadcrumb navigation.
        $name = get_string('searchfor', 'repository_googledocs') . " '{$searchtext}'";
        $path = helper::build_node_path(self::SEARCH_ROOT_ID, $name, $rootpath);

        $service = new repository_googledocs\rest($this->get_user_oauth_client());
        $searchobj = new googledocs_content_search($service, $path);

        return [
            'dynload' => true,
            'path' => $searchobj->get_navigation(),
            'list' => $searchobj->get_content_nodes($searchtext, [$this, 'filter']),
            'manage' => 'https://drive.google.com/',
        ];
    }

    /**
     * Query Google Drive for files and folders using a search query.
     *
     * Documentation about the query format can be found here:
     *   https://developers.google.com/drive/search-parameters
     *
     * This returns a list of files and folders with their details as they should be
     * formatted and returned by functions such as get_listing() or search().
     *
     * @deprecated since Moodle 3.11.
     * @param string $q search query as expected by the Google API.
     * @param string $path parent path of the current files, will not be used for the query.
     * @param int $page page.
     * @return array of files and folders.
     */
    protected function query($q, $path = null, $page = 0) {
        debugging('The function query() is deprecated, please use get_content_nodes() from the ' .
            'googledocs repository content classes instead.', DEBUG_DEVELOPER);

        global $OUTPUT;

        $files = array();
        $folders = array();
        $config = get_config('googledocs');
        $fields = "files(id,name,mimeType,webContentLink,webViewLink,fileExtension,modifiedTime,size,thumbnailLink,iconLink)";
        $params = array('q' => $q, 'fields' => $fields, 'spaces' => 'drive');

        try {
            // Retrieving files and folders.
            $client = $this->get_user_oauth_client();
            $service = new repository_googledocs\rest($client);

            $response = $service->call('list', $params);
        } catch (Exception $e) {
            if ($e->getCode() == 403 && strpos($e->getMessage(), 'Access Not Configured') !== false) {
                // This is raised when the service Drive API has not been enabled on Google APIs control panel.
                throw new repository_exception('servicenotenabled', 'repository_googledocs');
            } else {
                throw $e;
            }
        }

        $gfiles = isset($response->files) ? $response->files : array();
        foreach ($gfiles as $gfile) {
            if ($gfile->mimeType == 'application/vnd.google-apps.folder') {
                // This is a folder.
                $folders[$gfile->name . $gfile->id] = array(
                    'title' => $gfile->name,
                    'path' => $this->build_node_path($gfile->id, $gfile->name, $path),
                    'date' => strtotime($gfile->modifiedTime),
                    'thumbnail' => $OUTPUT->image_url(file_folder_icon())->out(false),
                    'thumbnail_height' => 64,
                    'thumbnail_width' => 64,
                    'children' => array()
                );
            } else {
                // This is a file.
                $link = isset($gfile->webViewLink) ? $gfile->webViewLink : '';
                if (empty($link)) {
                    $link = isset($gfile->webContentLink) ? $gfile->webContentLink : '';
                }
                if (isset($gfile->fileExtension)) {
                    // The file has an extension, therefore we can download it.
                    $source = json_encode([
                        'id' => $gfile->id,
                        'name' => $gfile->name,
                        'exportformat' => 'download',
                        'link' => $link
                    ]);
                    $title = $gfile->name;
                } else {
                    // The file is probably a Google Doc file, we get the corresponding export link.
                    // This should be improved by allowing the user to select the type of export they'd like.
                    $type = str_replace('application/vnd.google-apps.', '', $gfile->mimeType);
                    $title = '';
                    $exporttype = '';
                    $types = get_mimetypes_array();

                    switch ($type){
                        case 'document':
                            $ext = $config->documentformat;
                            $title = $gfile->name . '.gdoc';
                            if ($ext === 'rtf') {
                                // Moodle user 'text/rtf' as the MIME type for RTF files.
                                // Google uses 'application/rtf' for the same type of file.
                                // See https://developers.google.com/drive/v3/web/manage-downloads.
                                $exporttype = 'application/rtf';
                            } else {
                                $exporttype = $types[$ext]['type'];
                            }
                            break;
                        case 'presentation':
                            $ext = $config->presentationformat;
                            $title = $gfile->name . '.gslides';
                            $exporttype = $types[$ext]['type'];
                            break;
                        case 'spreadsheet':
                            $ext = $config->spreadsheetformat;
                            $title = $gfile->name . '.gsheet';
                            $exporttype = $types[$ext]['type'];
                            break;
                        case 'drawing':
                            $ext = $config->drawingformat;
                            $title = $gfile->name . '.'. $ext;
                            $exporttype = $types[$ext]['type'];
                            break;
                    }
                    // Skips invalid/unknown types.
                    if (empty($title)) {
                        continue;
                    }
                    $source = json_encode([
                        'id' => $gfile->id,
                        'exportformat' => $exporttype,
                        'link' => $link,
                        'name' => $gfile->name
                    ]);
                }
                // Adds the file to the file list. Using the itemId along with the name as key
                // of the array because Google Drive allows files with identical names.
                $thumb = '';
                if (isset($gfile->thumbnailLink)) {
                    $thumb = $gfile->thumbnailLink;
                } else if (isset($gfile->iconLink)) {
                    $thumb = $gfile->iconLink;
                }
                $files[$title . $gfile->id] = array(
                    'title' => $title,
                    'source' => $source,
                    'date' => strtotime($gfile->modifiedTime),
                    'size' => isset($gfile->size) ? $gfile->size : null,
                    'thumbnail' => $thumb,
                    'thumbnail_height' => 64,
                    'thumbnail_width' => 64,
                );
            }
        }

        // Filter and order the results.
        $files = array_filter($files, array($this, 'filter'));
        core_collator::ksort($files, core_collator::SORT_NATURAL);
        core_collator::ksort($folders, core_collator::SORT_NATURAL);
        return array_merge(array_values($folders), array_values($files));
    }

    /**
     * Logout.
     *
     * @return string
     */
    public function logout() {
        $client = $this->get_user_oauth_client();
        $client->log_out();
        return parent::logout();
    }

    /**
     * Get a file.
     *
     * @param string $reference reference of the file.
     * @param string $file name to save the file to.
     * @return string JSON encoded array of information about the file.
     */
    public function get_file($reference, $filename = '') {
        global $CFG;

        if (!$this->issuer->get('enabled')) {
            throw new repository_exception('cannotdownload', 'repository');
        }

        $source = json_decode($reference);

        $client = null;
        if (!empty($source->usesystem)) {
            $client = \core\oauth2\api::get_system_oauth_client($this->issuer);
        } else {
            $client = $this->get_user_oauth_client();
        }

        $base = 'https://www.googleapis.com/drive/v3';

        $newfilename = false;
        if ($source->exportformat == 'download') {
            $params = ['alt' => 'media'];
            $sourceurl = new moodle_url($base . '/files/' . $source->id, $params);
            $source = $sourceurl->out(false);
        } else {
            $params = ['mimeType' => $source->exportformat];
            $sourceurl = new moodle_url($base . '/files/' . $source->id . '/export', $params);
            $types = get_mimetypes_array();
            $checktype = $source->exportformat;
            if ($checktype == 'application/rtf') {
                $checktype = 'text/rtf';
            }
            // Determine the relevant default import format config for the given file.
            switch ($source->googledoctype) {
                case 'document':
                    $importformatconfig = get_config('googledocs', 'documentformat');
                    break;
                case 'presentation':
                    $importformatconfig = get_config('googledocs', 'presentationformat');
                    break;
                case 'spreadsheet':
                    $importformatconfig = get_config('googledocs', 'spreadsheetformat');
                    break;
                case 'drawing':
                    $importformatconfig = get_config('googledocs', 'drawingformat');
                    break;
                default:
                    $importformatconfig = null;
            }

            foreach ($types as $extension => $info) {
                if ($info['type'] == $checktype && $extension === $importformatconfig) {
                    $newfilename = $source->name . '.' . $extension;
                    break;
                }
            }
            $source = $sourceurl->out(false);
        }

        // We use download_one and not the rest API because it has special timeouts etc.
        $path = $this->prepare_file($filename);
        $options = ['filepath' => $path, 'timeout' => 15, 'followlocation' => true, 'maxredirs' => 5];
        $success = $client->download_one($source, null, $options);

        if ($success) {
            @chmod($path, $CFG->filepermissions);

            $result = [
                'path' => $path,
                'url' => $reference,
            ];
            if (!empty($newfilename)) {
                $result['newfilename'] = $newfilename;
            }
            return $result;
        }
        throw new repository_exception('cannotdownload', 'repository');
    }

    /**
     * Prepare file reference information.
     *
     * We are using this method to clean up the source to make sure that it
     * is a valid source.
     *
     * @param string $source of the file.
     * @return string file reference.
     */
    public function get_file_reference($source) {
        // We could do some magic upgrade code here.
        return $source;
    }

    /**
     * What kind of files will be in this repository?
     *
     * @return array return '*' means this repository support any files, otherwise
     *               return mimetypes of files, it can be an array
     */
    public function supported_filetypes() {
        return '*';
    }

    /**
     * Tells how the file can be picked from this repository.
     *
     * @return int
     */
    public function supported_returntypes() {
        // We can only support references if the system account is connected.
        if (!empty($this->issuer) && $this->issuer->is_system_account_connected()) {
            $setting = get_config('googledocs', 'supportedreturntypes');
            if ($setting == 'internal') {
                return FILE_INTERNAL;
            } else if ($setting == 'external') {
                return FILE_CONTROLLED_LINK;
            } else {
                return FILE_CONTROLLED_LINK | FILE_INTERNAL;
            }
        } else {
            return FILE_INTERNAL;
        }
    }

    /**
     * Which return type should be selected by default.
     *
     * @return int
     */
    public function default_returntype() {
        $setting = get_config('googledocs', 'defaultreturntype');
        $supported = get_config('googledocs', 'supportedreturntypes');
        if (($setting == FILE_INTERNAL && $supported != 'external') || $supported == 'internal') {
            return FILE_INTERNAL;
        } else {
            return FILE_CONTROLLED_LINK;
        }
    }

    /**
     * Return names of the general options.
     * By default: no general option name.
     *
     * @return array
     */
    public static function get_type_option_names() {
        return array('issuerid', 'pluginname',
            'documentformat', 'drawingformat',
            'presentationformat', 'spreadsheetformat',
            'defaultreturntype', 'supportedreturntypes');
    }

    /**
     * Store the access token.
     */
    public function callback() {
        $client = $this->get_user_oauth_client();
        // This will upgrade to an access token if we have an authorization code and save the access token in the session.
        $client->is_logged_in();
    }

    /**
     * Repository method to serve the referenced file
     *
     * @see send_stored_file
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param int $lifetime Number of seconds before the file should expire from caches (null means $CFG->filelifetime)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, ?array $options = null) {
        if (!$this->issuer->get('enabled')) {
            throw new repository_exception('cannotdownload', 'repository');
        }

        $source = json_decode($storedfile->get_reference());

        $fb = get_file_browser();
        $context = context::instance_by_id($storedfile->get_contextid(), MUST_EXIST);
        $info = $fb->get_file_info($context,
                                   $storedfile->get_component(),
                                   $storedfile->get_filearea(),
                                   $storedfile->get_itemid(),
                                   $storedfile->get_filepath(),
                                   $storedfile->get_filename());

        if (empty($options['offline']) && !empty($info) && $info->is_writable() && !empty($source->usesystem)) {
            // Add the current user as an OAuth writer.
            $systemauth = \core\oauth2\api::get_system_oauth_client($this->issuer);

            if ($systemauth === false) {
                $details = 'Cannot connect as system user';
                throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
            }
            $systemservice = new repository_googledocs\rest($systemauth);

            // Get the user oauth so we can get the account to add.
            $url = moodle_url::make_pluginfile_url($storedfile->get_contextid(),
                                                   $storedfile->get_component(),
                                                   $storedfile->get_filearea(),
                                                   $storedfile->get_itemid(),
                                                   $storedfile->get_filepath(),
                                                   $storedfile->get_filename(),
                                                   $forcedownload);
            $url->param('sesskey', sesskey());
            $param = (isset($options['embed']) && $options['embed'] == true) ? false : $url;
            $userauth = $this->get_user_oauth_client($param);
            if (!$userauth->is_logged_in()) {
                if (isset($options['embed']) && $options['embed'] == true) {
                    // Due to Same-origin policy, we cannot redirect to googledocs login page.
                    // If the requested file is embed and the user is not logged in, add option to log in using a popup.
                    $this->print_login_popup(['style' => 'margin-top: 250px']);
                    exit;
                }
                redirect($userauth->get_login_url());
            }
            if ($userauth === false) {
                $details = 'Cannot connect as current user';
                throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
            }
            $userinfo = $userauth->get_userinfo();
            $useremail = $userinfo['email'];

            $this->add_temp_writer_to_file($systemservice, $source->id, $useremail);
        }

        if (!empty($options['offline'])) {
            $downloaded = $this->get_file($storedfile->get_reference(), $storedfile->get_filename());

            $filename = $storedfile->get_filename();
            if (isset($downloaded['newfilename'])) {
                $filename = $downloaded['newfilename'];
            }
            send_file($downloaded['path'], $filename, $lifetime, $filter, false, $forcedownload, '', false, $options);
        } else if ($source->link) {
            // Do not use redirect() here because is not compatible with webservice/pluginfile.php.
            header('Location: ' . $source->link);
        } else {
            $details = 'File is missing source link';
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
    }

    /**
     * See if a folder exists within a folder
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $foldername The folder we are looking for.
     * @param string $parentid The parent folder we are looking in.
     * @return string|boolean The file id if it exists or false.
     */
    protected function folder_exists_in_folder(\repository_googledocs\rest $client, $foldername, $parentid) {
        $q = '\'' . addslashes($parentid) . '\' in parents and trashed = false and name = \'' . addslashes($foldername). '\'';
        $fields = 'files(id, name)';
        $params = [ 'q' => $q, 'fields' => $fields];
        $response = $client->call('list', $params);
        $missing = true;
        foreach ($response->files as $child) {
            if ($child->name == $foldername) {
                return $child->id;
            }
        }
        return false;
    }

    /**
     * Create a folder within a folder
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $foldername The folder we are creating.
     * @param string $parentid The parent folder we are creating in.
     *
     * @return string The file id of the new folder.
     */
    protected function create_folder_in_folder(\repository_googledocs\rest $client, $foldername, $parentid) {
        $fields = 'id';
        $params = ['fields' => $fields];
        $folder = ['mimeType' => 'application/vnd.google-apps.folder', 'name' => $foldername, 'parents' => [$parentid]];
        $created = $client->call('create', $params, json_encode($folder));
        if (empty($created->id)) {
            $details = 'Cannot create folder:' . $foldername;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return $created->id;
    }

    /**
     * Get simple file info for humans.
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $fileid The file we are querying.
     *
     * @return stdClass
     */
    protected function get_file_summary(\repository_googledocs\rest $client, $fileid) {
        $fields = "id,name,owners,parents,webContentLink,webViewLink";
        $params = [
            'fileid' => $fileid,
            'fields' => $fields
        ];
        return $client->call('get', $params);
    }

    /**
     * Add a writer to the permissions on the file (temporary).
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $fileid The file we are updating.
     * @param string $email The email of the writer account to add.
     * @return boolean
     */
    protected function add_temp_writer_to_file(\repository_googledocs\rest $client, $fileid, $email) {
        // Expires in 7 days.
        $expires = new DateTime();
        $expires->add(new DateInterval("P7D"));

        $updateeditor = [
            'emailAddress' => $email,
            'role' => 'writer',
            'type' => 'user',
            'expirationTime' => $expires->format(DateTime::RFC3339)
        ];
        $params = ['fileid' => $fileid, 'sendNotificationEmail' => 'false', 'supportsAllDrives' => 'true'];
        $response = $client->call('create_permission', $params, json_encode($updateeditor));
        if (empty($response->id)) {
            $details = 'Cannot add user ' . $email . ' as a writer for document: ' . $fileid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return true;
    }


    /**
     * Add a writer to the permissions on the file.
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $fileid The file we are updating.
     * @param string $email The email of the writer account to add.
     * @return boolean
     */
    protected function add_writer_to_file(\repository_googledocs\rest $client, $fileid, $email) {
        $updateeditor = [
            'emailAddress' => $email,
            'role' => 'writer',
            'type' => 'user'
        ];
        $params = ['fileid' => $fileid, 'sendNotificationEmail' => 'false', 'supportsAllDrives' => 'true'];
        $response = $client->call('create_permission', $params, json_encode($updateeditor));
        if (empty($response->id)) {
            $details = 'Cannot add user ' . $email . ' as a writer for document: ' . $fileid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return true;
    }

    /**
     * Move from root to folder
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $fileid The file we are updating.
     * @param string $folderid The id of the folder we are moving to
     * @return boolean
     */
    protected function move_file_from_root_to_folder(\repository_googledocs\rest $client, $fileid, $folderid) {
        // Set the parent.
        $params = [
            'fileid' => $fileid, 'addParents' => $folderid, 'removeParents' => 'root'
        ];
        $response = $client->call('update', $params, ' ');
        if (empty($response->id)) {
            $details = 'Cannot move the file to a folder: ' . $fileid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return true;
    }

    /**
     * Prevent writers from sharing.
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $fileid The file we are updating.
     * @return boolean
     */
    protected function prevent_writers_from_sharing_file(\repository_googledocs\rest $client, $fileid) {
        // We don't want anyone but Moodle to change the sharing settings.
        $params = [
            'fileid' => $fileid
        ];
        $update = [
            'writersCanShare' => false
        ];
        $response = $client->call('update', $params, json_encode($update));
        if (empty($response->id)) {
            $details = 'Cannot prevent writers from sharing document: ' . $fileid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return true;
    }

    /**
     * Allow anyone with the link to read the file.
     *
     * @param \repository_googledocs\rest $client Authenticated client.
     * @param string $fileid The file we are updating.
     * @return boolean
     */
    protected function set_file_sharing_anyone_with_link_can_read(\repository_googledocs\rest $client, $fileid) {
        $updateread = [
            'type' => 'anyone',
            'role' => 'reader',
            'allowFileDiscovery' => 'false'
        ];
        $params = ['fileid' => $fileid, 'supportsAllDrives' => 'true'];
        $response = $client->call('create_permission', $params, json_encode($updateread));
        if (empty($response->id) || $response->id != 'anyoneWithLink') {
            $details = 'Cannot update link sharing for the document: ' . $fileid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return true;
    }

    /**
     * Called when a file is selected as a "link".
     * Invoked at MOODLE/repository/repository_ajax.php
     *
     * This is called at the point the reference files are being copied from the draft area to the real area
     * (when the file has really really been selected.
     *
     * @param string $reference this reference is generated by
     *                          repository::get_file_reference()
     * @param context $context the target context for this new file.
     * @param string $component the target component for this new file.
     * @param string $filearea the target filearea for this new file.
     * @param string $itemid the target itemid for this new file.
     * @return string updated reference (final one before it's saved to db).
     */
    public function reference_file_selected($reference, $context, $component, $filearea, $itemid) {
        global $CFG, $SITE;

        // What we need to do here is transfer ownership to the system user (or copy)
        // then set the permissions so anyone with the share link can view,
        // finally update the reference to contain the share link if it was not
        // already there (and point to new file id if we copied).

        // Get the details from the reference.
        $source = json_decode($reference);
        if (!empty($source->usesystem)) {
            // If we already copied this file to the system account - we are done.
            return $reference;
        }

        // Check this issuer is enabled.
        if ($this->disabled) {
            throw new repository_exception('cannotdownload', 'repository');
        }

        // Get a system oauth client and a user oauth client.
        $systemauth = \core\oauth2\api::get_system_oauth_client($this->issuer);

        if ($systemauth === false) {
            $details = 'Cannot connect as system user';
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        // Get the system user email so we can share the file with this user.
        $systemuserinfo = $systemauth->get_userinfo();
        $systemuseremail = $systemuserinfo['email'];

        $userauth = $this->get_user_oauth_client();
        if ($userauth === false) {
            $details = 'Cannot connect as current user';
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }

        $userservice = new repository_googledocs\rest($userauth);
        $systemservice = new repository_googledocs\rest($systemauth);

        $this->add_writer_to_file($userservice, $source->id, $systemuseremail);

        // Now move it to a sensible folder.
        $contextlist = array_reverse($context->get_parent_contexts(true));

        $cache = cache::make('repository_googledocs', 'folder');
        $parentid = 'root';
        $fullpath = 'root';
        $allfolders = [];
        foreach ($contextlist as $context) {
            // Prepare human readable context folders names, making sure they are still unique within the site.
            $prevlang = force_current_language($CFG->lang);
            $foldername = $context->get_context_name();
            force_current_language($prevlang);

            if ($context->contextlevel == CONTEXT_SYSTEM) {
                // Append the site short name to the root folder.
                $foldername .= ' ('.$SITE->shortname.')';
                // Append the relevant object id.
            } else if ($context->instanceid) {
                $foldername .= ' (id '.$context->instanceid.')';
            } else {
                // This does not really happen but just in case.
                $foldername .= ' (ctx '.$context->id.')';
            }

            $foldername = clean_param($foldername, PARAM_PATH);
            $allfolders[] = $foldername;
        }

        $allfolders[] = clean_param($component, PARAM_PATH);
        $allfolders[] = clean_param($filearea, PARAM_PATH);
        $allfolders[] = clean_param($itemid, PARAM_PATH);

        // Variable $allfolders is the full path we want to put the file in - so walk it and create each folder.

        foreach ($allfolders as $foldername) {
            // Make sure a folder exists here.
            $fullpath .= '/' . $foldername;

            $folderid = $cache->get($fullpath);
            if (empty($folderid)) {
                $folderid = $this->folder_exists_in_folder($systemservice, $foldername, $parentid);
            }
            if ($folderid !== false) {
                $cache->set($fullpath, $folderid);
                $parentid = $folderid;
            } else {
                // Create it.
                $parentid = $this->create_folder_in_folder($systemservice, $foldername, $parentid);
                $cache->set($fullpath, $parentid);
            }
        }

        $originalfile = $this->get_file_summary($userservice, $source->id);
        // Use the user service to download the file.
        $downloadedfile = $this->download_file(
            $userservice,
            $source->id,
            $originalfile->name,
        );
        // Upload the user file to the system drive.
        $uploaded = $this->upload_file(
            $systemservice,
            $downloadedfile['path'],
            $downloadedfile['newfilename'],
            $source->exportformat,
            $parentid,
        );
        // Add the original file owner as a writer to the file.
        $this->add_writer_to_file($systemservice, $uploaded->id, $originalfile->owners[0]->emailAddress);
        $newsource = $this->get_file_summary($systemservice, $uploaded->id);

        // Move the copied file to the correct folder.
        $this->move_file_from_root_to_folder($systemservice, $newsource->id, $parentid);

        // Set the sharing options.
        $this->set_file_sharing_anyone_with_link_can_read($systemservice, $newsource->id);
        $this->prevent_writers_from_sharing_file($systemservice, $newsource->id);

        // Update the returned reference so that the stored_file in moodle points to the newly copied file.
        $source->id = $newsource->id;
        $source->link = isset($newsource->webViewLink) ? $newsource->webViewLink : '';
        $source->usesystem = true;
        if (empty($source->link)) {
            $source->link = isset($newsource->webContentLink) ? $newsource->webContentLink : '';
        }
        $reference = json_encode($source);

        return $reference;
    }

    /**
     * Uploads a file to Google Docs using the provided REST client.
     *
     * @param \repository_googledocs\rest $client The REST client for Google Docs API communication.
     * @param string $filepath The local path to the file to be uploaded.
     * @param string $filename The name to assign to the uploaded file.
     * @param string $exportformat The export format for the file (e.g., 'pdf', 'docx').
     * @param string $parentid The ID of the parent folder in Google Drive where the file will be uploaded.
     * @return stdClass|coding_exception Returns the response from the Google Docs API after uploading the file.
     */
    public function upload_file(
        \repository_googledocs\rest $client,
        string $filepath,
        string $filename,
        string $exportformat,
        string $parentid
    ): stdClass {
        $fileinfo = [
            'name' => $filename,
            'mimeType' => $exportformat,
            'parents' => [$parentid], // We will move it later.
        ];
        $params = [
            'supportsAllDrives' => 'true', // Support shared drives.
            'uploadType' => 'resumable', // Use resumable upload.
        ];

        $headers = $client->call('upload', $params, json_encode($fileinfo));

        $uploadurl = '';
        // Google returns a location header with the location for the upload.
        foreach ($headers as $header) {
            if (stripos($header, 'Location:') === 0) {
                $uploadurl = trim(substr($header, strpos($header, ':') + 1));
            }
        }

        $params = ['uploadurl' => $uploadurl];
        return $client->call('upload_content', $params, file_get_contents($filepath), mime_content_type($filepath));
    }

    /**
     * Downloads a file from Google Docs using the provided user service.
     *
     * @param \repository_googledocs\rest $userservice The user service instance for Google Docs REST API.
     * @param string $fileid The ID of the file to download.
     * @param string $originalfilename The file original name
     * @return array|repository_exception The downloaded file content or relevant response.
     */
    protected function download_file(
        \repository_googledocs\rest $userservice,
        string $fileid,
        string $originalfilename
    ): array|repository_exception {
        global $CFG;

        $client = $this->get_user_oauth_client();
        $base = 'https://www.googleapis.com/drive/v3';
        $params = ['alt' => 'media'];
        $sourceurl = new moodle_url($base . '/files/' . $fileid, $params);

        $path = $this->prepare_file($originalfilename);
        $options = ['filepath' => $path, 'timeout' => $CFG->repositorygetfiletimeout, 'followlocation' => true, 'maxredirs' => 5];
        $success = $client->download_one($sourceurl->out(false), null, $options);
        if ($success) {
            @chmod($path, $CFG->filepermissions);
            return [
                'path' => $path,
                'newfilename' => $originalfilename,
            ];
        }

        throw new repository_exception('cannotdownload', 'repository');
    }

    /**
     * Get human readable file info from a the reference.
     *
     * @param string $reference
     * @param int $filestatus
     */
    public function get_reference_details($reference, $filestatus = 0) {
        if ($this->disabled) {
            throw new repository_exception('cannotdownload', 'repository');
        }
        if (empty($reference)) {
            return get_string('unknownsource', 'repository');
        }
        $source = json_decode($reference);
        if (empty($source->usesystem)) {
            return '';
        }
        $systemauth = \core\oauth2\api::get_system_oauth_client($this->issuer);

        if ($systemauth === false) {
            return '';
        }
        $systemservice = new repository_googledocs\rest($systemauth);
        $info = $this->get_file_summary($systemservice, $source->id);

        $owner = '';
        if (!empty($info->owners[0]->displayName)) {
            $owner = $info->owners[0]->displayName;
        }
        if ($owner) {
            return get_string('owner', 'repository_googledocs', $owner);
        } else {
            return $info->name;
        }
    }

    /**
     * Edit/Create Admin Settings Moodle form.
     *
     * @param moodleform $mform Moodle form (passed by reference).
     * @param string $classname repository class name.
     */
    public static function type_config_form($mform, $classname = 'repository') {
        $url = new moodle_url('/admin/tool/oauth2/issuers.php');
        $url = $url->out();

        $mform->addElement('static', null, '', get_string('oauth2serviceslink', 'repository_googledocs', $url));

        parent::type_config_form($mform);
        $options = [];
        $issuers = \core\oauth2\api::get_all_issuers();

        foreach ($issuers as $issuer) {
            $options[$issuer->get('id')] = s($issuer->get('name'));
        }

        $strrequired = get_string('required');

        $mform->addElement('select', 'issuerid', get_string('issuer', 'repository_googledocs'), $options);
        $mform->addHelpButton('issuerid', 'issuer', 'repository_googledocs');
        $mform->addRule('issuerid', $strrequired, 'required', null, 'client');

        $mform->addElement('static', null, '', get_string('fileoptions', 'repository_googledocs'));
        $choices = [
            'internal' => get_string('internal', 'repository_googledocs'),
            'external' => get_string('external', 'repository_googledocs'),
            'both' => get_string('both', 'repository_googledocs')
        ];
        $mform->addElement('select', 'supportedreturntypes', get_string('supportedreturntypes', 'repository_googledocs'), $choices);

        $choices = [
            FILE_INTERNAL => get_string('internal', 'repository_googledocs'),
            FILE_CONTROLLED_LINK => get_string('external', 'repository_googledocs'),
        ];
        $mform->addElement('select', 'defaultreturntype', get_string('defaultreturntype', 'repository_googledocs'), $choices);

        $mform->addElement('static', null, '', get_string('importformat', 'repository_googledocs'));

        // Documents.
        $docsformat = array();
        $docsformat['html'] = 'html';
        $docsformat['docx'] = 'docx';
        $docsformat['odt'] = 'odt';
        $docsformat['pdf'] = 'pdf';
        $docsformat['rtf'] = 'rtf';
        $docsformat['txt'] = 'txt';
        core_collator::ksort($docsformat, core_collator::SORT_NATURAL);

        $mform->addElement('select', 'documentformat', get_string('docsformat', 'repository_googledocs'), $docsformat);
        $mform->setDefault('documentformat', $docsformat['rtf']);
        $mform->setType('documentformat', PARAM_ALPHANUM);

        // Drawing.
        $drawingformat = array();
        $drawingformat['jpeg'] = 'jpeg';
        $drawingformat['png'] = 'png';
        $drawingformat['svg'] = 'svg';
        $drawingformat['pdf'] = 'pdf';
        core_collator::ksort($drawingformat, core_collator::SORT_NATURAL);

        $mform->addElement('select', 'drawingformat', get_string('drawingformat', 'repository_googledocs'), $drawingformat);
        $mform->setDefault('drawingformat', $drawingformat['pdf']);
        $mform->setType('drawingformat', PARAM_ALPHANUM);

        // Presentation.
        $presentationformat = array();
        $presentationformat['pdf'] = 'pdf';
        $presentationformat['pptx'] = 'pptx';
        $presentationformat['txt'] = 'txt';
        core_collator::ksort($presentationformat, core_collator::SORT_NATURAL);

        $str = get_string('presentationformat', 'repository_googledocs');
        $mform->addElement('select', 'presentationformat', $str, $presentationformat);
        $mform->setDefault('presentationformat', $presentationformat['pptx']);
        $mform->setType('presentationformat', PARAM_ALPHANUM);

        // Spreadsheet.
        $spreadsheetformat = array();
        $spreadsheetformat['csv'] = 'csv';
        $spreadsheetformat['ods'] = 'ods';
        $spreadsheetformat['pdf'] = 'pdf';
        $spreadsheetformat['xlsx'] = 'xlsx';
        core_collator::ksort($spreadsheetformat, core_collator::SORT_NATURAL);

        $str = get_string('spreadsheetformat', 'repository_googledocs');
        $mform->addElement('select', 'spreadsheetformat', $str, $spreadsheetformat);
        $mform->setDefault('spreadsheetformat', $spreadsheetformat['xlsx']);
        $mform->setType('spreadsheetformat', PARAM_ALPHANUM);
    }
}

/**
 * Callback to get the required scopes for system account.
 *
 * @param \core\oauth2\issuer $issuer
 * @return string
 */
function repository_googledocs_oauth2_system_scopes(\core\oauth2\issuer $issuer) {
    if ($issuer->get('id') == get_config('googledocs', 'issuerid')) {
        return Google_Service_Drive::DRIVE_FILE;
    }
    return '';
}
