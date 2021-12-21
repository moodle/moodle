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
 * Microsoft Live Skydrive Repository Plugin
 *
 * @package    repository_onedrive
 * @copyright  2012 Lancaster University Network Services Ltd
 * @author     Dan Poltawski <dan.poltawski@luns.net.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Microsoft onedrive repository plugin.
 *
 * @package    repository_onedrive
 * @copyright  2012 Lancaster University Network Services Ltd
 * @author     Dan Poltawski <dan.poltawski@luns.net.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_onedrive extends repository {
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

    /**
     * Additional scopes required for drive.
     */
    const SCOPES = 'files.readwrite.all';

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

        try {
            $this->issuer = \core\oauth2\api::get_issuer(get_config('onedrive', 'issuerid'));
        } catch (dml_missing_record_exception $e) {
            $this->disabled = true;
        }

        if ($this->issuer && !$this->issuer->get('enabled')) {
            $this->disabled = true;
        }
    }

    /**
     * Get a cached user authenticated oauth client.
     *
     * @param moodle_url $overrideurl - Use this url instead of the repo callback.
     * @return \core\oauth2\client
     */
    protected function get_user_oauth_client($overrideurl = false) {
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

        $this->client = \core\oauth2\api::get_user_oauth_client($this->issuer, $returnurl, self::SCOPES, true);

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

        $repositoryname = get_string('pluginname', 'repository_onedrive');

        $button = new single_button($url, get_string('logintoaccount', 'repository', $repositoryname), 'post', true);
        $button->add_action(new popup_action('click', $url, 'Login'));
        $button->class = 'mdl-align';
        $button = $OUTPUT->render($button);
        echo html_writer::div($button, '', $attr);

        echo $OUTPUT->footer();
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
            $path = $this->build_node_path('root', get_string('pluginname', 'repository_onedrive'));
        }

        if ($this->disabled) {
            // Empty list of files for disabled repository.
            return ['dynload' => false, 'list' => [], 'nologin' => true];
        }

        // We analyse the path to extract what to browse.
        $trail = explode('/', $path);
        $uri = array_pop($trail);
        list($id, $name) = $this->explode_node_path($uri);

        // Handle the special keyword 'search', which we defined in self::search() so that
        // we could set up a breadcrumb in the search results. In any other case ID would be
        // 'root' which is a special keyword, or a parent (folder) ID.
        if ($id === 'search') {
            $q = $name;
            $id = 'root';

            // Append the active path for search.
            $str = get_string('searchfor', 'repository_onedrive', $searchtext);
            $path = $this->build_node_path('search', $str, $path);
        }

        // Query the Drive.
        $parent = $id;
        if ($parent != 'root') {
            $parent = 'items/' . $parent;
        }
        $q = '';
        $results = $this->query($q, $path, $parent);

        $ret = [];
        $ret['dynload'] = true;
        $ret['path'] = $this->build_breadcrumb($path);
        $ret['list'] = $results;
        $ret['manage'] = 'https://www.office.com/';
        return $ret;
    }

    /**
     * Search throughout the OneDrive
     *
     * @param string $searchtext text to search for.
     * @param int $page search page.
     * @return array of results.
     */
    public function search($searchtext, $page = 0) {
        $path = $this->build_node_path('root', get_string('pluginname', 'repository_onedrive'));
        $str = get_string('searchfor', 'repository_onedrive', $searchtext);
        $path = $this->build_node_path('search', $str, $path);

        // Query the Drive.
        $parent = 'root';
        $results = $this->query($searchtext, $path, 'root');

        $ret = [];
        $ret['dynload'] = true;
        $ret['path'] = $this->build_breadcrumb($path);
        $ret['list'] = $results;
        $ret['manage'] = 'https://www.office.com/';
        return $ret;
    }

    /**
     * Query OneDrive for files and folders using a search query.
     *
     * Documentation about the query format can be found here:
     *   https://developer.microsoft.com/en-us/graph/docs/api-reference/v1.0/resources/driveitem
     *   https://developer.microsoft.com/en-us/graph/docs/overview/query_parameters
     *
     * This returns a list of files and folders with their details as they should be
     * formatted and returned by functions such as get_listing() or search().
     *
     * @param string $q search query as expected by the Graph API.
     * @param string $path parent path of the current files, will not be used for the query.
     * @param string $parent Parent id.
     * @param int $page page.
     * @return array of files and folders.
     * @throws Exception
     * @throws repository_exception
     */
    protected function query($q, $path = null, $parent = null, $page = 0) {
        global $OUTPUT;

        $files = [];
        $folders = [];
        $fields = "folder,id,lastModifiedDateTime,name,size,webUrl,thumbnails";
        $params = ['$select' => $fields, '$expand' => 'thumbnails', 'parent' => $parent];

        try {
            // Retrieving files and folders.
            $client = $this->get_user_oauth_client();
            $service = new repository_onedrive\rest($client);

            if (!empty($q)) {
                $params['search'] = urlencode($q);

                // MS does not return thumbnails on a search.
                unset($params['$expand']);
                $response = $service->call('search', $params);
            } else {
                $response = $service->call('list', $params);
            }
        } catch (Exception $e) {
            if ($e->getCode() == 403 && strpos($e->getMessage(), 'Access Not Configured') !== false) {
                throw new repository_exception('servicenotenabled', 'repository_onedrive');
            } else if (strpos($e->getMessage(), 'mysite not found') !== false) {
                throw new repository_exception('mysitenotfound', 'repository_onedrive');
            }
        }

        $remotefiles = isset($response->value) ? $response->value : [];
        foreach ($remotefiles as $remotefile) {
            if (!empty($remotefile->folder)) {
                // This is a folder.
                $folders[$remotefile->id] = [
                    'title' => $remotefile->name,
                    'path' => $this->build_node_path($remotefile->id, $remotefile->name, $path),
                    'date' => strtotime($remotefile->lastModifiedDateTime),
                    'thumbnail' => $OUTPUT->image_url(file_folder_icon(64))->out(false),
                    'thumbnail_height' => 64,
                    'thumbnail_width' => 64,
                    'children' => []
                ];
            } else {
                // We can download all other file types.
                $title = $remotefile->name;
                $source = json_encode([
                        'id' => $remotefile->id,
                        'name' => $remotefile->name,
                        'link' => $remotefile->webUrl
                    ]);

                $thumb = '';
                $thumbwidth = 0;
                $thumbheight = 0;
                $extendedinfoerr = false;

                if (empty($remotefile->thumbnails)) {
                    // Try and get it directly from the item.
                    $params = ['fileid' => $remotefile->id, '$select' => $fields, '$expand' => 'thumbnails'];
                    try {
                        $response = $service->call('get', $params);
                        $remotefile = $response;
                    } catch (Exception $e) {
                        // This is not a failure condition - we just could not get extended info about the file.
                        $extendedinfoerr = true;
                    }
                }

                if (!empty($remotefile->thumbnails)) {
                    $thumbs = $remotefile->thumbnails;
                    if (count($thumbs)) {
                        $first = reset($thumbs);
                        if (!empty($first->medium) && !empty($first->medium->url)) {
                            $thumb = $first->medium->url;
                            $thumbwidth = min($first->medium->width, 64);
                            $thumbheight = min($first->medium->height, 64);
                        }
                    }
                }

                $files[$remotefile->id] = [
                    'title' => $title,
                    'source' => $source,
                    'date' => strtotime($remotefile->lastModifiedDateTime),
                    'size' => isset($remotefile->size) ? $remotefile->size : null,
                    'thumbnail' => $thumb,
                    'thumbnail_height' => $thumbwidth,
                    'thumbnail_width' => $thumbheight,
                ];
            }
        }

        // Filter and order the results.
        $files = array_filter($files, [$this, 'filter']);
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
     * @param string $filename filename to save the file to.
     * @return string JSON encoded array of information about the file.
     */
    public function get_file($reference, $filename = '') {
        global $CFG;

        if ($this->disabled) {
            throw new repository_exception('cannotdownload', 'repository');
        }
        $sourceinfo = json_decode($reference);

        $client = null;
        if (!empty($sourceinfo->usesystem)) {
            $client = \core\oauth2\api::get_system_oauth_client($this->issuer);
        } else {
            $client = $this->get_user_oauth_client();
        }

        $base = 'https://graph.microsoft.com/v1.0/';

        $sourceurl = new moodle_url($base . 'me/drive/items/' . $sourceinfo->id . '/content');
        $source = $sourceurl->out(false);

        // We use download_one and not the rest API because it has special timeouts etc.
        $path = $this->prepare_file($filename);
        $options = ['filepath' => $path, 'timeout' => 15, 'followlocation' => true, 'maxredirs' => 5];
        $result = $client->download_one($source, null, $options);

        if ($result) {
            @chmod($path, $CFG->filepermissions);
            return array(
                'path' => $path,
                'url' => $reference
            );
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
            $setting = get_config('onedrive', 'supportedreturntypes');
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
        $setting = get_config('onedrive', 'defaultreturntype');
        $supported = get_config('onedrive', 'supportedreturntypes');
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
        return array('issuerid', 'pluginname', 'defaultreturntype', 'supportedreturntypes');
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
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, array $options = null) {
        if ($this->disabled) {
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
            $systemservice = new repository_onedrive\rest($systemauth);

            // Get the user oauth so we can get the account to add.
            $url = moodle_url::make_pluginfile_url($storedfile->get_contextid(),
                                                   $storedfile->get_component(),
                                                   $storedfile->get_filearea(),
                                                   $storedfile->get_itemid(),
                                                   $storedfile->get_filepath(),
                                                   $storedfile->get_filename(),
                                                   $forcedownload);
            $url->param('sesskey', sesskey());
            $param = ($options['embed'] == true) ? false : $url;
            $userauth = $this->get_user_oauth_client($param);

            if (!$userauth->is_logged_in()) {
                if ($options['embed'] == true) {
                    // Due to Same-origin policy, we cannot redirect to onedrive login page.
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
     * @param \repository_onedrive\rest $client Authenticated client.
     * @param string $fullpath
     * @return string|boolean The file id if it exists or false.
     */
    protected function get_file_id_by_path(\repository_onedrive\rest $client, $fullpath) {
        $fields = "id";
        try {
            $response = $client->call('get_file_by_path', ['fullpath' => $fullpath, '$select' => $fields]);
        } catch (\core\oauth2\rest_exception $re) {
            return false;
        }
        return $response->id;
    }

    /**
     * Delete a file by full path.
     *
     * @param \repository_onedrive\rest $client Authenticated client.
     * @param string $fullpath
     * @return boolean
     */
    protected function delete_file_by_path(\repository_onedrive\rest $client, $fullpath) {
        try {
            $response = $client->call('delete_file_by_path', ['fullpath' => $fullpath]);
        } catch (\core\oauth2\rest_exception $re) {
            return false;
        }
        return true;
    }

    /**
     * Create a folder within a folder
     *
     * @param \repository_onedrive\rest $client Authenticated client.
     * @param string $foldername The folder we are creating.
     * @param string $parentid The parent folder we are creating in.
     *
     * @return string The file id of the new folder.
     */
    protected function create_folder_in_folder(\repository_onedrive\rest $client, $foldername, $parentid) {
        $params = ['parentid' => $parentid];
        $folder = [ 'name' => $foldername, 'folder' => [ 'childCount' => 0 ]];
        $created = $client->call('create_folder', $params, json_encode($folder));
        if (empty($created->id)) {
            $details = 'Cannot create folder:' . $foldername;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return $created->id;
    }

    /**
     * Get simple file info for humans.
     *
     * @param \repository_onedrive\rest $client Authenticated client.
     * @param string $fileid The file we are querying.
     *
     * @return stdClass
     */
    protected function get_file_summary(\repository_onedrive\rest $client, $fileid) {
        $fields = "folder,id,lastModifiedDateTime,name,size,webUrl,createdByUser";
        $response = $client->call('get', ['fileid' => $fileid, '$select' => $fields]);
        return $response;
    }

    /**
     * Add a writer to the permissions on the file (temporary).
     *
     * @param \repository_onedrive\rest $client Authenticated client.
     * @param string $fileid The file we are updating.
     * @param string $email The email of the writer account to add.
     * @return boolean
     */
    protected function add_temp_writer_to_file(\repository_onedrive\rest $client, $fileid, $email) {
        // Expires in 7 days.
        $expires = new DateTime();
        $expires->add(new DateInterval("P7D"));

        $updateeditor = [
            'recipients' => [[ 'email' => $email ]],
            'roles' => ['write'],
            'requireSignIn' => true,
            'sendInvitation' => false
        ];
        $params = ['fileid' => $fileid];
        $response = $client->call('create_permission', $params, json_encode($updateeditor));
        if (empty($response->value[0]->id)) {
            $details = 'Cannot add user ' . $email . ' as a writer for document: ' . $fileid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        // Store the permission id in the DB. Scheduled task will remove this permission after 7 days.
        if ($access = repository_onedrive\access::get_record(['permissionid' => $response->value[0]->id, 'itemid' => $fileid ])) {
            // Update the timemodified.
            $access->update();
        } else {
            $record = (object) [ 'permissionid' => $response->value[0]->id, 'itemid' => $fileid ];
            $access = new repository_onedrive\access(0, $record);
            $access->create();
        }
        return true;
    }

    /**
     * Allow anyone with the link to read the file.
     *
     * @param \repository_onedrive\rest $client Authenticated client.
     * @param string $fileid The file we are updating.
     * @return boolean
     */
    protected function set_file_sharing_anyone_with_link_can_read(\repository_onedrive\rest $client, $fileid) {

        $type = (isset($this->options['embed']) && $this->options['embed'] == true) ? 'embed' : 'view';
        $updateread = [
            'type' => $type,
            'scope' => 'anonymous'
        ];
        $params = ['fileid' => $fileid];
        $response = $client->call('create_link', $params, json_encode($updateread));
        if (empty($response->link)) {
            $details = 'Cannot update link sharing for the document: ' . $fileid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        return $response->link->webUrl;
    }

    /**
     * Given a filename, use the core_filetypes registered types to guess a mimetype.
     *
     * If no mimetype is known, return 'application/unknown';
     *
     * @param string $filename
     * @return string $mimetype
     */
    protected function get_mimetype_from_filename($filename) {
        $mimetype = 'application/unknown';
        $types = core_filetypes::get_types();
        $fileextension = '.bin';
        if (strpos($filename, '.') !== false) {
            $fileextension = substr($filename, strrpos($filename, '.') + 1);
        }

        if (isset($types[$fileextension])) {
            $mimetype = $types[$fileextension]['type'];
        }
        return $mimetype;
    }

    /**
     * Upload a file to onedrive.
     *
     * @param \repository_onedrive\rest $service Authenticated client.
     * @param \curl $curl Curl client to perform the put operation (with no auth headers).
     * @param \curl $authcurl Curl client that will send authentication headers
     * @param string $filepath The local path to the file to upload
     * @param string $mimetype The new mimetype
     * @param string $parentid The folder to put it.
     * @param string $filename The name of the new file
     * @return string $fileid
     */
    protected function upload_file(\repository_onedrive\rest $service, \curl $curl, \curl $authcurl,
                                   $filepath, $mimetype, $parentid, $filename) {
        // Start an upload session.
        // Docs https://developer.microsoft.com/en-us/graph/docs/api-reference/v1.0/api/item_createuploadsession link.

        $params = ['parentid' => $parentid, 'filename' => urlencode($filename)];
        $behaviour = [ 'item' => [ "@microsoft.graph.conflictBehavior" => "rename" ] ];
        $created = $service->call('create_upload', $params, json_encode($behaviour));
        if (empty($created->uploadUrl)) {
            $details = 'Cannot begin upload session:' . $parentid;
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }

        $options = ['file' => $filepath];

        // Try each curl class in turn until we succeed.
        // First attempt an upload with no auth headers (will work for personal onedrive accounts).
        // If that fails, try an upload with the auth headers (will work for work onedrive accounts).
        $curls = [$curl, $authcurl];
        $response = null;
        foreach ($curls as $curlinstance) {
            $curlinstance->setHeader('Content-type: ' . $mimetype);
            $size = filesize($filepath);
            $curlinstance->setHeader('Content-Range: bytes 0-' . ($size - 1) . '/' . $size);
            $response = $curlinstance->put($created->uploadUrl, $options);
            if ($curlinstance->errno == 0) {
                $response = json_decode($response);
            }
            if (!empty($response->id)) {
                // We can stop now - there is a valid file returned.
                break;
            }
        }

        if (empty($response->id)) {
            $details = 'File not created';
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }

        return $response->id;
    }


    /**
     * Called when a file is selected as a "link".
     * Invoked at MOODLE/repository/repository_ajax.php
     *
     * What should happen here is that the file should be copied to a new file owned by the moodle system user.
     * It should be organised in a folder based on the file context.
     * It's sharing permissions should allow read access with the link.
     * The returned reference should point to the newly copied file - not the original.
     *
     * @param string $reference this reference is generated by
     *                          repository::get_file_reference()
     * @param context $context the target context for this new file.
     * @param string $component the target component for this new file.
     * @param string $filearea the target filearea for this new file.
     * @param string $itemid the target itemid for this new file.
     * @return string $modifiedreference (final one before saving to DB)
     */
    public function reference_file_selected($reference, $context, $component, $filearea, $itemid) {
        global $CFG, $SITE;

        // What we need to do here is transfer ownership to the system user (or copy)
        // then set the permissions so anyone with the share link can view,
        // finally update the reference to contain the share link if it was not
        // already there (and point to new file id if we copied).
        $source = json_decode($reference);
        if (!empty($source->usesystem)) {
            // If we already copied this file to the system account - we are done.
            return $reference;
        }

        // Get a system and a user oauth client.
        $systemauth = \core\oauth2\api::get_system_oauth_client($this->issuer);

        if ($systemauth === false) {
            $details = 'Cannot connect as system user';
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }

        $userauth = $this->get_user_oauth_client();
        if ($userauth === false) {
            $details = 'Cannot connect as current user';
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }

        $systemservice = new repository_onedrive\rest($systemauth);

        // Download the file.
        $tmpfilename = clean_param($source->id, PARAM_PATH);
        $temppath = make_request_directory() . $tmpfilename;

        $options = ['filepath' => $temppath, 'timeout' => 60, 'followlocation' => true, 'maxredirs' => 5];
        $base = 'https://graph.microsoft.com/v1.0/';
        $sourceurl = new moodle_url($base . 'me/drive/items/' . $source->id . '/content');
        $sourceurl = $sourceurl->out(false);

        $result = $userauth->download_one($sourceurl, null, $options);

        if (!$result) {
            throw new repository_exception('cannotdownload', 'repository');
        }

        // Now copy it to a sensible folder.
        $contextlist = array_reverse($context->get_parent_contexts(true));

        $cache = cache::make('repository_onedrive', 'folder');
        $parentid = 'root';
        $fullpath = '';
        $allfolders = [];
        foreach ($contextlist as $context) {
            // Prepare human readable context folders names, making sure they are still unique within the site.
            $prevlang = force_current_language($CFG->lang);
            $foldername = $context->get_context_name();
            force_current_language($prevlang);

            if ($context->contextlevel == CONTEXT_SYSTEM) {
                // Append the site short name to the root folder.
                $foldername .= '_'.$SITE->shortname;
                // Append the relevant object id.
            } else if ($context->instanceid) {
                $foldername .= '_id_'.$context->instanceid;
            } else {
                // This does not really happen but just in case.
                $foldername .= '_ctx_'.$context->id;
            }

            $foldername = urlencode(clean_param($foldername, PARAM_PATH));
            $allfolders[] = $foldername;
        }

        $allfolders[] = urlencode(clean_param($component, PARAM_PATH));
        $allfolders[] = urlencode(clean_param($filearea, PARAM_PATH));
        $allfolders[] = urlencode(clean_param($itemid, PARAM_PATH));

        // Variable $allfolders now has the complete path we want to store the file in.
        // Create each folder in $allfolders under the system account.
        foreach ($allfolders as $foldername) {
            if ($fullpath) {
                $fullpath .= '/';
            }
            $fullpath .= $foldername;

            $folderid = $cache->get($fullpath);
            if (empty($folderid)) {
                $folderid = $this->get_file_id_by_path($systemservice, $fullpath);
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

        // Delete any existing file at this path.
        $path = $fullpath . '/' . urlencode(clean_param($source->name, PARAM_PATH));
        $this->delete_file_by_path($systemservice, $path);

        // Upload the file.
        $safefilename = clean_param($source->name, PARAM_PATH);
        $mimetype = $this->get_mimetype_from_filename($safefilename);
        // We cannot send authorization headers in the upload or personal microsoft accounts will fail (what a joke!).
        $curl = new \curl();
        $fileid = $this->upload_file($systemservice, $curl, $systemauth, $temppath, $mimetype, $parentid, $safefilename);

        // Read with link.
        $link = $this->set_file_sharing_anyone_with_link_can_read($systemservice, $fileid);

        $summary = $this->get_file_summary($systemservice, $fileid);

        // Update the details in the file reference before it is saved.
        $source->id = $summary->id;
        $source->link = $link;
        $source->usesystem = true;

        $reference = json_encode($source);

        return $reference;
    }

    /**
     * Get human readable file info from the reference.
     *
     * @param string $reference
     * @param int $filestatus
     */
    public function get_reference_details($reference, $filestatus = 0) {
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
        $systemservice = new repository_onedrive\rest($systemauth);
        $info = $this->get_file_summary($systemservice, $source->id);

        $owner = '';
        if (!empty($info->createdByUser->displayName)) {
            $owner = $info->createdByUser->displayName;
        }
        if ($owner) {
            return get_string('owner', 'repository_onedrive', $owner);
        } else {
            return $info->name;
        }
    }

    /**
     * Return true if any instances of the skydrive repo exist - and we can import them.
     *
     * @return bool
     * @deprecated since Moodle 4.0
     * @todo MDL-72620 This will be deleted in Moodle 4.4.
     */
    public static function can_import_skydrive_files() {
        global $DB;

        $skydrive = $DB->get_record('repository', ['type' => 'skydrive'], 'id', IGNORE_MISSING);
        $onedrive = $DB->get_record('repository', ['type' => 'onedrive'], 'id', IGNORE_MISSING);

        if (empty($skydrive) || empty($onedrive)) {
            return false;
        }

        $ready = true;
        try {
            $issuer = \core\oauth2\api::get_issuer(get_config('onedrive', 'issuerid'));
            if (!$issuer->get('enabled')) {
                $ready = false;
            }
            if (!$issuer->is_configured()) {
                $ready = false;
            }
        } catch (dml_missing_record_exception $e) {
            $ready = false;
        }
        if (!$ready) {
            return false;
        }

        $sql = "SELECT count('x')
                  FROM {repository_instances} i, {repository} r
                 WHERE r.type=:plugin AND r.id=i.typeid";
        $params = array('plugin' => 'skydrive');
        return $DB->count_records_sql($sql, $params) > 0;
    }

    /**
     * Import all the files that were created with the skydrive repo to this repo.
     *
     * @return bool
     * @deprecated since Moodle 4.0
     * @todo MDL-72620 This will be deleted in Moodle 4.4.
     */
    public static function import_skydrive_files() {
        global $DB;

        debugging('import_skydrive_files() is deprecated. Please migrate your files from repository_skydrive to ' .
            'repository_onedrive before it will be completely removed.', DEBUG_DEVELOPER);

        if (!self::can_import_skydrive_files()) {
            return false;
        }
        // Should only be one of each.
        $skydrivetype = repository::get_type_by_typename('skydrive');

        $skydriveinstances = repository::get_instances(['type' => 'skydrive']);
        $skydriveinstance = reset($skydriveinstances);
        $onedriveinstances = repository::get_instances(['type' => 'onedrive']);
        $onedriveinstance = reset($onedriveinstances);

        // Update all file references.
        $DB->set_field('files_reference', 'repositoryid', $onedriveinstance->id, ['repositoryid' => $skydriveinstance->id]);

        // Delete and disable the skydrive repo.
        $skydrivetype->delete();
        core_plugin_manager::reset_caches();

        $sql = "SELECT count('x')
                  FROM {repository_instances} i, {repository} r
                 WHERE r.type=:plugin AND r.id=i.typeid";
        $params = array('plugin' => 'skydrive');
        return $DB->count_records_sql($sql, $params) == 0;
    }

    /**
     * Edit/Create Admin Settings Moodle form.
     *
     * @param moodleform $mform Moodle form (passed by reference).
     * @param string $classname repository class name.
     */
    public static function type_config_form($mform, $classname = 'repository') {
        global $OUTPUT;

        $url = new moodle_url('/admin/tool/oauth2/issuers.php');
        $url = $url->out();

        $mform->addElement('static', null, '', get_string('oauth2serviceslink', 'repository_onedrive', $url));

        if (self::can_import_skydrive_files()) {
            debugging('can_import_skydrive_files() is deprecated. Please migrate your files from repository_skydrive to ' .
            'repository_onedrive before it will be completely removed.', DEBUG_DEVELOPER);

            $notice = get_string('skydrivefilesexist', 'repository_onedrive');
            $url = new moodle_url('/repository/onedrive/importskydrive.php');
            $attrs = ['class' => 'btn btn-primary'];
            $button = $OUTPUT->action_link($url, get_string('importskydrivefiles', 'repository_onedrive'), null, $attrs);
            $mform->addElement('static', null, '', $OUTPUT->notification($notice) . $button);
        }

        parent::type_config_form($mform);
        $options = [];
        $issuers = \core\oauth2\api::get_all_issuers();

        foreach ($issuers as $issuer) {
            $options[$issuer->get('id')] = s($issuer->get('name'));
        }

        $strrequired = get_string('required');

        $mform->addElement('select', 'issuerid', get_string('issuer', 'repository_onedrive'), $options);
        $mform->addHelpButton('issuerid', 'issuer', 'repository_onedrive');
        $mform->addRule('issuerid', $strrequired, 'required', null, 'client');

        $mform->addElement('static', null, '', get_string('fileoptions', 'repository_onedrive'));
        $choices = [
            'internal' => get_string('internal', 'repository_onedrive'),
            'external' => get_string('external', 'repository_onedrive'),
            'both' => get_string('both', 'repository_onedrive')
        ];
        $mform->addElement('select', 'supportedreturntypes', get_string('supportedreturntypes', 'repository_onedrive'), $choices);

        $choices = [
            FILE_INTERNAL => get_string('internal', 'repository_onedrive'),
            FILE_CONTROLLED_LINK => get_string('external', 'repository_onedrive'),
        ];
        $mform->addElement('select', 'defaultreturntype', get_string('defaultreturntype', 'repository_onedrive'), $choices);

    }
}

/**
 * Callback to get the required scopes for system account.
 *
 * @param \core\oauth2\issuer $issuer
 * @return string
 */
function repository_onedrive_oauth2_system_scopes(\core\oauth2\issuer $issuer) {
    if ($issuer->get('id') == get_config('onedrive', 'issuerid')) {
        return repository_onedrive::SCOPES;
    }
    return '';
}
