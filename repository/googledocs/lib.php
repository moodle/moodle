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
require_once($CFG->libdir . '/google/lib.php');

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
     * Google Client.
     * @var Google_Client
     */
    private $client = null;

    /**
     * Google Drive Service.
     * @var Google_Drive_Service
     */
    private $service = null;

    /**
     * Session key to store the accesstoken.
     * @var string
     */
    const SESSIONKEY = 'googledrive_accesstoken';

    /**
     * URI to the callback file for OAuth.
     * @var string
     */
    const CALLBACKURL = '/admin/oauth2callback.php';

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
        parent::__construct($repositoryid, $context, $options, $readonly = 0);

        $callbackurl = new moodle_url(self::CALLBACKURL);

        $this->client = get_google_client();
        $this->client->setClientId(get_config('googledocs', 'clientid'));
        $this->client->setClientSecret(get_config('googledocs', 'secret'));
        $this->client->setScopes(array(Google_Service_Drive::DRIVE_READONLY));
        $this->client->setRedirectUri($callbackurl->out(false));
        $this->service = new Google_Service_Drive($this->client);

        $this->check_login();
    }

    /**
     * Returns the access token if any.
     *
     * @return string|null access token.
     */
    protected function get_access_token() {
        global $SESSION;
        if (isset($SESSION->{self::SESSIONKEY})) {
            return $SESSION->{self::SESSIONKEY};
        }
        return null;
    }

    /**
     * Store the access token in the session.
     *
     * @param string $token token to store.
     * @return void
     */
    protected function store_access_token($token) {
        global $SESSION;
        $SESSION->{self::SESSIONKEY} = $token;
    }

    /**
     * Callback method during authentication.
     *
     * @return void
     */
    public function callback() {
        if ($code = optional_param('oauth2code', null, PARAM_RAW)) {
            $this->client->authenticate($code);
            $this->store_access_token($this->client->getAccessToken());
        }
    }

    /**
     * Checks whether the user is authenticate or not.
     *
     * @return bool true when logged in.
     */
    public function check_login() {
        if ($token = $this->get_access_token()) {
            $this->client->setAccessToken($token);
            return true;
        }
        return false;
    }

    /**
     * Print or return the login form.
     *
     * @return void|array for ajax.
     */
    public function print_login() {
        $returnurl = new moodle_url('/repository/repository_callback.php');
        $returnurl->param('callback', 'yes');
        $returnurl->param('repo_id', $this->id);
        $returnurl->param('sesskey', sesskey());

        $url = new moodle_url($this->client->createAuthUrl());
        $url->param('state', $returnurl->out_as_local_url(false));
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
    * Build the breadcrumb from a path.
    *
    * @param string $path to create a breadcrumb from.
    * @return array containing name and path of each crumb.
    */
    protected function build_breadcrumb($path) {
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
    * @param string $id of the node.
    * @param string $name of the node, will be URL encoded.
    * @param string $root to append the node on, must be a result of this function.
    * @return string path to the node.
    */
    protected function build_node_path($id, $name = '', $root = '') {
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
    * @see self::build_node_path()
    * @param string $node to extrat information from.
    * @return array about the node.
    */
    protected function explode_node_path($node) {
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
            $path = $this->build_node_path('root', get_string('pluginname', 'repository_googledocs'));
        }

        // We analyse the path to extract what to browse.
        $trail = explode('/', $path);
        $uri = array_pop($trail);
        list($id, $name) = $this->explode_node_path($uri);

        // Handle the special keyword 'search', which we defined in self::search() so that
        // we could set up a breadcrumb in the search results. In any other case ID would be
        // 'root' which is a special keyword set up by Google, or a parent (folder) ID.
        if ($id === 'search') {
            return $this->search($name);
        }

        // Query the Drive.
        $q = "'" . str_replace("'", "\'", $id) . "' in parents";
        $q .= ' AND trashed = false';
        $results = $this->query($q, $path);

        $ret = array();
        $ret['dynload'] = true;
        $ret['path'] = $this->build_breadcrumb($path);
        $ret['list'] = $results;
        return $ret;
    }

    /**
     * Search throughout the Google Drive.
     *
     * @param string $search_text text to search for.
     * @param int $page search page.
     * @return array of results.
     */
    public function search($search_text, $page = 0) {
        $path = $this->build_node_path('root', get_string('pluginname', 'repository_googledocs'));
        $path = $this->build_node_path('search', $search_text, $path);

        // Query the Drive.
        $q = "fullText contains '" . str_replace("'", "\'", $search_text) . "'";
        $q .= ' AND trashed = false';
        $results = $this->query($q, $path);

        $ret = array();
        $ret['dynload'] = true;
        $ret['path'] = $this->build_breadcrumb($path);
        $ret['list'] = $results;
        return $ret;
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
     * @param string $q search query as expected by the Google API.
     * @param string $path parent path of the current files, will not be used for the query.
     * @param int $page page.
     * @return array of files and folders.
     */
    protected function query($q, $path = null, $page = 0) {
        global $OUTPUT;

        $files = array();
        $folders = array();
        $fields = "items(id,title,mimeType,downloadUrl,fileExtension,exportLinks,modifiedDate,fileSize,thumbnailLink)";
        $params = array('q' => $q, 'fields' => $fields);

        try {
            // Retrieving files and folders.
            $response = $this->service->files->listFiles($params);
        } catch (Google_Service_Exception $e) {
            if ($e->getCode() == 403 && strpos($e->getMessage(), 'Access Not Configured') !== false) {
                // This is raised when the service Drive API has not been enabled on Google APIs control panel.
                throw new repository_exception('servicenotenabled', 'repository_googledocs');
            } else {
                throw $e;
            }
        }

        $items = isset($response['items']) ? $response['items'] : array();
        foreach ($items as $item) {
            if ($item['mimeType'] == 'application/vnd.google-apps.folder') {
                // This is a folder.
                $folders[$item['title'] . $item['id']] = array(
                    'title' => $item['title'],
                    'path' => $this->build_node_path($item['id'], $item['title'], $path),
                    'date' => strtotime($item['modifiedDate']),
                    'thumbnail' => $OUTPUT->pix_url(file_folder_icon(64))->out(false),
                    'thumbnail_height' => 64,
                    'thumbnail_width' => 64,
                    'children' => array()
                );
            } else {
                // This is a file.
                if (isset($item['fileExtension'])) {
                    // The file has an extension, therefore there is a download link.
                    $title = $item['title'];
                    $source = $item['downloadUrl'];
                } else {
                    // The file is probably a Google Doc file, we get the corresponding export link.
                    // This should be improved by allowing the user to select the type of export they'd like.
                    $type = str_replace('application/vnd.google-apps.', '', $item['mimeType']);
                    $title = '';
                    $exportType = '';
                    switch ($type){
                        case 'document':
                            $title = $item['title'] . '.rtf';
                            $exportType = 'application/rtf';
                            break;
                        case 'presentation':
                            $title = $item['title'] . '.pptx';
                            $exportType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                            break;
                        case 'spreadsheet':
                            $title = $item['title'] . '.xlsx';
                            $exportType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                            break;
                    }
                    // Skips invalid/unknown types.
                    if (empty($title) || !isset($item['exportLinks'][$exportType])) {
                        continue;
                    }
                    $source = $item['exportLinks'][$exportType];
                }
                // Adds the file to the file list. Using the itemId along with the title as key
                // of the array because Google Drive allows files with identical names.
                $files[$title . $item['id']] = array(
                    'title' => $title,
                    'source' => $source,
                    'date' => strtotime($item['modifiedDate']),
                    'size' => isset($item['fileSize']) ? $item['fileSize'] : null,
                    'thumbnail' => $OUTPUT->pix_url(file_extension_icon($title, 64))->out(false),
                    'thumbnail_height' => 64,
                    'thumbnail_width' => 64,
                    // Do not use real thumbnails as they wouldn't work if the user disabled 3rd party
                    // plugins in his browser, or if they're not logged in their Google account.
                );

                // Sometimes the real thumbnails can't be displayed, for example if 3rd party cookies are disabled
                // or if the user is not logged in Google anymore. But this restriction does not seem to be applied
                // to a small subset of files.
                $extension = strtolower(pathinfo($title, PATHINFO_EXTENSION));
                if (isset($item['thumbnailLink']) && in_array($extension, array('jpg', 'png', 'txt', 'pdf'))) {
                    $files[$title . $item['id']]['realthumbnail'] = $item['thumbnailLink'];
                }
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
        $this->store_access_token(null);
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

        $auth = $this->client->getAuth();
        $request = $auth->authenticatedRequest(new Google_Http_Request($reference));
        if ($request->getResponseHttpCode() == 200) {
            $path = $this->prepare_file($filename);
            $content = $request->getResponseBody();
            if (file_put_contents($path, $content) !== false) {
                @chmod($path, $CFG->filepermissions);
                return array(
                    'path' => $path,
                    'url' => $reference
                );
            }
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
        return clean_param($source, PARAM_URL);
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
     * Maximum value is FILE_INTERNAL | FILE_EXTERNAL | FILE_REFERENCE.
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_INTERNAL;
    }

    /**
     * Return names of the general options.
     * By default: no general option name.
     *
     * @return array
     */
    public static function get_type_option_names() {
        return array('clientid', 'secret', 'pluginname');
    }

    /**
     * Edit/Create Admin Settings Moodle form.
     *
     * @param moodleform $mform Moodle form (passed by reference).
     * @param string $classname repository class name.
     */
    public static function type_config_form($mform, $classname = 'repository') {

        $callbackurl = new moodle_url(self::CALLBACKURL);

        $a = new stdClass;
        $a->docsurl = get_docs_url('Google_OAuth_2.0_setup');
        $a->callbackurl = $callbackurl->out(false);

        $mform->addElement('static', null, '', get_string('oauthinfo', 'repository_googledocs', $a));

        parent::type_config_form($mform);
        $mform->addElement('text', 'clientid', get_string('clientid', 'repository_googledocs'));
        $mform->setType('clientid', PARAM_RAW_TRIMMED);
        $mform->addElement('text', 'secret', get_string('secret', 'repository_googledocs'));
        $mform->setType('secret', PARAM_RAW_TRIMMED);

        $strrequired = get_string('required');
        $mform->addRule('clientid', $strrequired, 'required', null, 'client');
        $mform->addRule('secret', $strrequired, 'required', null, 'client');
    }
}
// Icon from: http://www.iconspedia.com/icon/google-2706.html.
