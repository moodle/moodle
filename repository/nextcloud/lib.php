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
 * Nextcloud repository plugin library.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Project seminar (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or
 */

use repository_nextcloud\issuer_management;
use repository_nextcloud\ocs_client;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/webdavlib.php');

/**
 * Nextcloud repository class.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Project seminar (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_nextcloud extends repository {
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
     * Additional scopes needed for the repository. Currently, nextcloud does not actually support/use scopes, so
     * this is intended as a hint at required functionality and will help declare future scopes.
     */
    const SCOPES = 'files ocs';

    /**
     * Webdav client which is used for webdav operations.
     *
     * @var \webdav_client
     */
    private $dav = null;

    /**
     * Basepath for WebDAV operations
     * @var string
     */
    private $davbasepath;

    /**
     * OCS client that uses the Open Collaboration Services REST API.
     * @var ocs_client
     */
    private $ocsclient;

    /**
     * @var oauth2_client System account client.
     */
    private $systemoauthclient = false;

    /**
     * OCS systemocsclient that uses the Open Collaboration Services REST API.
     * @var ocs_client
     */
    private $systemocsclient = null;

    /**
     * Name of the folder for controlled links.
     * @var string
     */
    private $controlledlinkfoldername;

    /**
     * Curl instance that can be used to fetch file from nextcloud instance.
     * @var curl
     */
    private $curl;

    /**
     * repository_nextcloud constructor.
     *
     * @param int $repositoryid
     * @param bool|int|stdClass $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        try {
            // Issuer from repository instance config.
            $issuerid = $this->get_option('issuerid');
            $this->issuer = \core\oauth2\api::get_issuer($issuerid);
        } catch (dml_missing_record_exception $e) {
            // A repository is marked as disabled when no issuer is present.
            $this->disabled = true;
            return;
        }

        try {
            // Load the webdav endpoint and parse the basepath.
            $webdavendpoint = issuer_management::parse_endpoint_url('webdav', $this->issuer);
            // Get basepath without trailing slash, because future uses will come with a leading slash.
            $basepath = $webdavendpoint['path'];
            if (strlen($basepath) > 0 && substr($basepath, -1) === '/') {
                $basepath = substr($basepath, 0, -1);
            }
            $this->davbasepath = $basepath;
        } catch (\repository_nextcloud\configuration_exception $e) {
            // A repository is marked as disabled when no webdav_endpoint is present
            // or it fails to parse, because all operations concerning files
            // rely on the webdav endpoint.
            $this->disabled = true;
            return;
        }
        $this->controlledlinkfoldername = $this->get_option('controlledlinkfoldername');

        if (!$this->issuer) {
            $this->disabled = true;
            return;
        } else if (!$this->issuer->get('enabled')) {
            // In case the Issuer is not enabled, the repository is disabled.
            $this->disabled = true;
            return;
        } else if (!issuer_management::is_valid_issuer($this->issuer)) {
            // Check if necessary endpoints are present.
            $this->disabled = true;
            return;
        }

        $this->ocsclient = new ocs_client($this->get_user_oauth_client());
        $this->curl = new curl();
    }

    /**
     * Get or initialise an oauth client for the system account.
     *
     * @return false|oauth2_client False if initialisation was unsuccessful, otherwise an initialised client.
     */
    private function get_system_oauth_client() {
        if ($this->systemoauthclient === false) {
            try {
                $this->systemoauthclient = \core\oauth2\api::get_system_oauth_client($this->issuer);
            } catch (\moodle_exception $e) {
                $this->systemoauthclient = false;
            }
        }
        return $this->systemoauthclient;
    }

    /**
     * Get or initialise an ocs client for the system account.
     *
     * @return null|ocs_client Null if initialisation was unsuccessful, otherwise an initialised client.
     */
    private function get_system_ocs_client() {
        if ($this->systemocsclient === null) {
            try {
                $systemoauth = $this->get_system_oauth_client();
                if (!$systemoauth) {
                    return null;
                }
                $this->systemocsclient = new ocs_client($systemoauth);
            } catch (\moodle_exception $e) {
                $this->systemocsclient = null;
            }
        }
        return $this->systemocsclient;
    }

    /**
     * Initiates the webdav client.
     *
     * @throws \repository_nextcloud\configuration_exception If configuration is missing (endpoints).
     */
    private function initiate_webdavclient() {
        if ($this->dav !== null) {
            return $this->dav;
        }

        $webdavendpoint = issuer_management::parse_endpoint_url('webdav', $this->issuer);

        // Selects the necessary information (port, type, server) from the path to build the webdavclient.
        $server = $webdavendpoint['host'];
        if ($webdavendpoint['scheme'] === 'https') {
            $webdavtype = 'ssl://';
            $webdavport = 443;
        } else if ($webdavendpoint['scheme'] === 'http') {
            $webdavtype = '';
            $webdavport = 80;
        }

        // Override default port, if a specific one is set.
        if (isset($webdavendpoint['port'])) {
            $webdavport = $webdavendpoint['port'];
        }

        // Authentication method is `bearer` for OAuth 2. Pass token of authenticated client, too.
        $this->dav = new \webdav_client($server, '', '', 'bearer', $webdavtype,
            $this->get_user_oauth_client()->get_accesstoken()->token);

        $this->dav->port = $webdavport;
        $this->dav->debug = false;
        return $this->dav;
    }

    /**
     * This function does exactly the same as in the WebDAV repository. The only difference is, that
     * the nextcloud OAuth2 client uses OAuth2 instead of Basic Authentication.
     *
     * @param string $reference relative path to the file.
     * @param string $title title of the file.
     * @return array|bool returns either the moodle path to the file or false.
     */
    public function get_file($reference, $title = '') {
        // Normal file.
        $reference = urldecode($reference);

        // Prepare a file with an arbitrary name - cannot be $title because of special chars (cf. MDL-57002).
        $path = $this->prepare_file(uniqid());
        $this->initiate_webdavclient();
        if (!$this->dav->open()) {
            return false;
        }
        $this->dav->get_file($this->davbasepath . $reference, $path);
        $this->dav->close();

        return array('path' => $path);
    }

    /**
     * This function does exactly the same as in the WebDAV repository. The only difference is, that
     * the nextcloud OAuth2 client uses OAuth2 instead of Basic Authentication.
     *
     * @param string $path relative path to the directory or file.
     * @param string $page page number (given multiple pages of elements).
     * @return array directory properties.
     */
    public function get_listing($path='', $page = '') {
        if (empty($path)) {
            $path = '/';
        }

        $ret = $this->get_listing_prepare_response($path);

        // Before any WebDAV method can be executed, a WebDAV client socket needs to be opened
        // which connects to the server.
        $this->initiate_webdavclient();
        if (!$this->dav->open()) {
            return $ret;
        }

        // Since the paths which are received from the PROPFIND WebDAV method are url encoded
        // (because they depict actual web-paths), the received paths need to be decoded back
        // for the plugin to be able to work with them.
        $ls = $this->dav->ls($this->davbasepath . urldecode($path));
        $this->dav->close();

        // The method get_listing return all information about all child files/folders of the
        // current directory. If no information was received, the directory must be empty.
        if (!is_array($ls)) {
            return $ret;
        }

        // Process WebDAV output and convert it into Moodle format.
        $ret['list'] = $this->get_listing_convert_response($path, $ls);
        return $ret;

    }

    /**
     * Use OCS to generate a public share to the requested file.
     * This method derives a download link from the public share URL.
     *
     * @param string $url relative path to the chosen file
     * @return string the generated download link.
     * @throws \repository_nextcloud\request_exception If nextcloud responded badly
     *
     */
    public function get_link($url) {
        // Create a read only public link, remember no update possible in this file/folder.
        $ocsparams = [
            'path' => $url,
            'shareType' => ocs_client::SHARE_TYPE_PUBLIC,
            'publicUpload' => false,
            'permissions' => ocs_client::SHARE_PERMISSION_READ
            ];

        $response = $this->ocsclient->call('create_share', $ocsparams);
        $xml = simplexml_load_string($response);

        if ($xml === false ) {
            throw new \repository_nextcloud\request_exception(array('instance' => $this->get_name(),
                'errormessage' => get_string('invalidresponse', 'repository_nextcloud')));
        }

        if ((string)$xml->meta->status !== 'ok') {
            throw new \repository_nextcloud\request_exception(array('instance' => $this->get_name(), 'errormessage' => sprintf(
                '(%s) %s', $xml->meta->statuscode, $xml->meta->message)));
        }

        // Take the share link and convert it into a download link.
        return ((string)$xml->data[0]->url) . '/download';
    }

    /**
     * This method does not do any translation of the file source.
     *
     * @param string $source source of the file, returned by repository as 'source' and received back from user (not cleaned)
     * @return string file reference, ready to be stored or json encoded string for public link reference
     */
    public function get_file_reference($source) {
        $usefilereference = optional_param('usefilereference', false, PARAM_BOOL);
        if ($usefilereference) {
            return json_encode([
                'type' => 'FILE_REFERENCE',
                'link' => $this->get_link($source),
            ]);
        }
        // The simple relative path to the file is enough.
        return $source;
    }

    /**
     * Called when a file is selected as a "access control link".
     * Invoked at MOODLE/repository/repository_ajax.php
     *
     * This is called at the point the reference files are being copied from the draft area to the real area.
     * What is done here is transfer ownership to the system user (by copying) then delete the intermediate share
     * used for that. Finally update the reference to point to new file name.
     *
     * @param string $reference this reference is generated by repository::get_file_reference()
     * @param context $context the target context for this new file.
     * @param string $component the target component for this new file.
     * @param string $filearea the target filearea for this new file.
     * @param string $itemid the target itemid for this new file.
     * @return string updated reference (final one before it's saved to db).
     * @throws \repository_nextcloud\configuration_exception
     * @throws \repository_nextcloud\request_exception
     * @throws coding_exception
     * @throws moodle_exception
     * @throws repository_exception
     */
    public function reference_file_selected($reference, $context, $component, $filearea, $itemid) {
        $source = json_decode($reference);

        if (is_object($source)) {
            if ($source->type != 'FILE_CONTROLLED_LINK') {
                // Only access controlled links need special handling; we are done.
                return $reference;
            }
            if (!empty($source->usesystem)) {
                // If we already copied this file to the system account - we are done.
                return $reference;
            }
        }

        // Check this issuer is enabled.
        if ($this->disabled || $this->get_system_oauth_client() === false || $this->get_system_ocs_client() === null) {
            throw new repository_exception('cannotdownload', 'repository');
        }

        $linkmanager = new \repository_nextcloud\access_controlled_link_manager($this->ocsclient, $this->get_system_oauth_client(),
            $this->get_system_ocs_client(), $this->issuer, $this->get_name());

        // Get the current user.
        $userauth = $this->get_user_oauth_client();
        if ($userauth === false) {
            $details = get_string('cannotconnect', 'repository_nextcloud');
            throw new \repository_nextcloud\request_exception(array('instance' => $this->get_name(), 'errormessage' => $details));
        }
        // 1. Share the File with the system account.
        $responsecreateshare = $linkmanager->create_share_user_sysaccount($reference);
        if ($responsecreateshare['statuscode'] == 403) {
            // File has already been shared previously => find file in system account and use that.
            $responsecreateshare = $linkmanager->find_share_in_sysaccount($reference);
        }

        // 2. Create a unique path in the system account.
        $createdfolder = $linkmanager->create_folder_path_access_controlled_links($context, $component, $filearea,
            $itemid);

        // 3. Copy File to the new folder path.
        $linkmanager->transfer_file_to_path($responsecreateshare['filetarget'], $createdfolder, 'copy');

        // 4. Delete the share.
        $linkmanager->delete_share_dataowner_sysaccount($responsecreateshare['shareid']);

        // Update the returned reference so that the stored_file in moodle points to the newly copied file.
        $filereturn = new stdClass();
        $filereturn->type = 'FILE_CONTROLLED_LINK';
        $filereturn->link = $createdfolder . $responsecreateshare['filetarget'];
        $filereturn->name = $reference;
        $filereturn->usesystem = true;
        $filereturn = json_encode($filereturn);

        return $filereturn;
    }

    /**
     * Repository method that serves the referenced file (created e.g. via get_link).
     * All parameters are there for compatibility with superclass, but they are ignored.
     *
     * @param stored_file $storedfile
     * @param int $lifetime (ignored)
     * @param int $filter (ignored)
     * @param bool $forcedownload (ignored)
     * @param array $options additional options affecting the file serving
     * @throws \repository_nextcloud\configuration_exception
     * @throws \repository_nextcloud\request_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function send_file($storedfile, $lifetime=null , $filter=0, $forcedownload=false, array $options = null) {
        $repositoryname = $this->get_name();
        $reference = json_decode($storedfile->get_reference());

        // If the file is a reference which means its a public link in nextcloud.
        if ($reference->type === 'FILE_REFERENCE') {
            // This file points to the public link just fetch the latest one from nextcloud repo.
            redirect($reference->link);
        }

        // 1. assure the client and user is logged in.
        if (empty($this->client) || $this->get_system_oauth_client() === false || $this->get_system_ocs_client() === null) {
            $details = get_string('contactadminwith', 'repository_nextcloud',
                get_string('noclientconnection', 'repository_nextcloud'));
            throw new \repository_nextcloud\request_exception(array('instance' => $repositoryname, 'errormessage' => $details));
        }

        // Download for offline usage. This is strictly read-only, so the file need not be shared.
        if (!empty($options['offline'])) {
            // Download from system account and provide the file to the user.
            $linkmanager = new \repository_nextcloud\access_controlled_link_manager($this->ocsclient,
                $this->get_system_oauth_client(), $this->get_system_ocs_client(), $this->issuer, $repositoryname);

            // Create temp path, then download into it.
            $filename = basename($reference->link);
            $tmppath = make_request_directory() . '/' . $filename;
            $linkmanager->download_for_offline_usage($reference->link, $tmppath);

            // Output the obtained file to the user and remove it from disk.
            send_temp_file($tmppath, $filename);

            // That's all.
            return;
        }

        if (!$this->client->is_logged_in()) {
            $this->print_login_popup(['style' => 'margin-top: 250px'], $options['embed']);
            return;
        }

        // Determining writeability of file from the using context.
        // Variable $info is null|\file_info. file_info::is_writable is only true if user may write for any reason.
        $fb = get_file_browser();
        $context = context::instance_by_id($storedfile->get_contextid(), MUST_EXIST);
        $info = $fb->get_file_info($context,
            $storedfile->get_component(),
            $storedfile->get_filearea(),
            $storedfile->get_itemid(),
            $storedfile->get_filepath(),
            $storedfile->get_filename());
        $maywrite = !empty($info) && $info->is_writable();

        $this->initiate_webdavclient();

        // Create the a manager to handle steps.
        $linkmanager = new \repository_nextcloud\access_controlled_link_manager($this->ocsclient, $this->get_system_oauth_client(),
            $this->get_system_ocs_client(), $this->issuer, $repositoryname);

        // 2. Check whether user has folder for files otherwise create it.
        $linkmanager->create_storage_folder($this->controlledlinkfoldername, $this->dav);

        $userinfo = $this->client->get_userinfo();
        $username = $userinfo['username'];

        // Creates a share between the systemaccount and the user.
        $responsecreateshare = $linkmanager->create_share_user_sysaccount($reference->link, $username, $maywrite);

        $statuscode = $responsecreateshare['statuscode'];

        if ($statuscode == 403) {
            $shareid = $linkmanager->get_shares_from_path($reference->link, $username);
        } else if ($statuscode == 100) {
            $filetarget = $linkmanager->get_share_information_from_shareid($responsecreateshare['shareid'], $username);
            $copyresult = $linkmanager->transfer_file_to_path($filetarget, $this->controlledlinkfoldername,
                'move', $this->dav);
            if (!($copyresult == 201 || $copyresult == 412)) {
                throw new \repository_nextcloud\request_exception(array('instance' => $repositoryname,
                    'errormessage' => get_string('couldnotmove', 'repository_nextcloud', $this->controlledlinkfoldername)));
            }
            $shareid = $responsecreateshare['shareid'];
        } else if ($statuscode == 997) {
            throw new \repository_nextcloud\request_exception(array('instance' => $repositoryname,
                'errormessage' => get_string('notauthorized', 'repository_nextcloud')));
        } else {
            $details = get_string('filenotaccessed', 'repository_nextcloud');
            throw new \repository_nextcloud\request_exception(array('instance' => $repositoryname, 'errormessage' => $details));
        }
        $filetarget = $linkmanager->get_share_information_from_shareid((int)$shareid, $username);

        // Obtain the file from Nextcloud using a Bearer token authenticated connection because we cannot perform a redirect here.
        // The reason is that Nextcloud uses samesite cookie validation, i.e. a redirected request would not be authenticated.
        // (Also the browser might use the session of a Nextcloud user that is different from the one that is known to Moodle.)
        $filename = basename($filetarget);
        $tmppath = make_request_directory() . '/' . $filename;
        $this->dav->open();

        // Concat webdav path with file path.
        $webdavendpoint = issuer_management::parse_endpoint_url('webdav', $this->issuer);
        $filetarget = ltrim($filetarget, '/');
        $filetarget = $webdavendpoint['path'] . $filetarget;

        // Write file into temp location.
        if (!$this->dav->get_file($filetarget, $tmppath)) {
            $this->dav->close();
            throw new repository_exception('cannotdownload', 'repository');
        }
        $this->dav->close();

        // Output the obtained file to the user and remove it from disk.
        send_temp_file($tmppath, $filename);
    }

    /**
     * Which return type should be selected by default.
     *
     * @return int
     */
    public function default_returntype() {
        $setting = $this->get_option('defaultreturntype');
        $supported = $this->get_option('supportedreturntypes');
        if (($setting == FILE_INTERNAL && $supported !== 'external') || $supported === 'internal') {
            return FILE_INTERNAL;
        }
        return FILE_CONTROLLED_LINK;
    }

    /**
     * Return names of the general options.
     * By default: no general option name.
     *
     * @return array
     */
    public static function get_type_option_names() {
        return array();
    }

    /**
     * Function which checks whether the user is logged in on the Nextcloud instance.
     *
     * @return bool false, if no Access Token is set or can be requested.
     */
    public function check_login() {
        $client = $this->get_user_oauth_client();
        return $client->is_logged_in();
    }

    /**
     * Get a cached user authenticated oauth client.
     *
     * @param bool|moodle_url $overrideurl Use this url instead of the repo callback.
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
     * Prints a simple Login Button which redirects to an authorization window from Nextcloud.
     *
     * @return mixed login window properties.
     * @throws coding_exception
     */
    public function print_login() {
        $client = $this->get_user_oauth_client();
        $loginurl = $client->get_login_url();
        if ($this->options['ajax']) {
            $ret = array();
            $btn = new \stdClass();
            $btn->type = 'popup';
            $btn->url = $loginurl->out(false);
            $ret['login'] = array($btn);
            return $ret;
        } else {
            echo html_writer::link($loginurl, get_string('login', 'repository'),
                    array('target' => '_blank',  'rel' => 'noopener noreferrer'));
        }
    }

    /**
     * Deletes the held Access Token and prints the Login window.
     *
     * @return array login window properties.
     */
    public function logout() {
        $client = $this->get_user_oauth_client();
        $client->log_out();
        return parent::logout();
    }

    /**
     * Sets up access token after the redirection from Nextcloud.
     */
    public function callback() {
        $client = $this->get_user_oauth_client();
        // If an Access Token is stored within the client, it has to be deleted to prevent the addition
        // of an Bearer authorization header in the request method.
        $client->log_out();

        // This will upgrade to an access token if we have an authorization code and save the access token in the session.
        $client->is_logged_in();
    }

    /**
     * Create an instance for this plug-in
     *
     * @param string $type the type of the repository
     * @param int $userid the user id
     * @param stdClass $context the context
     * @param array $params the options for this instance
     * @param int $readonly whether to create it readonly or not (defaults to not)
     * @return mixed
     * @throws dml_exception
     * @throws required_capability_exception
     */
    public static function create($type, $userid, $context, $params, $readonly=0) {
        require_capability('moodle/site:config', context_system::instance());
        return parent::create($type, $userid, $context, $params, $readonly);
    }

    /**
     * This method adds a select form and additional information to the settings form..
     *
     * @param \moodleform $mform Moodle form (passed by reference)
     * @return bool|void
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function instance_config_form($mform) {
        if (!has_capability('moodle/site:config', context_system::instance())) {
            $mform->addElement('static', null, '',  get_string('nopermissions', 'error', get_string('configplugin',
                'repository_nextcloud')));
            return false;
        }

        // Load configured issuers.
        $issuers = core\oauth2\api::get_all_issuers();
        $types = array();

        // Validates which issuers implement the right endpoints. WebDav is necessary for Nextcloud.
        $validissuers = [];
        foreach ($issuers as $issuer) {
            $types[$issuer->get('id')] = $issuer->get('name');
            if (\repository_nextcloud\issuer_management::is_valid_issuer($issuer)) {
                $validissuers[] = $issuer->get('name');
            }
        }

        // Render the form.
        $url = new \moodle_url('/admin/tool/oauth2/issuers.php');
        $mform->addElement('static', null, '', get_string('oauth2serviceslink', 'repository_nextcloud', $url->out()));

        $mform->addElement('select', 'issuerid', get_string('chooseissuer', 'repository_nextcloud'), $types);
        $mform->addRule('issuerid', get_string('required'), 'required', null, 'issuer');
        $mform->addHelpButton('issuerid', 'chooseissuer', 'repository_nextcloud');
        $mform->setType('issuerid', PARAM_INT);

        // All issuers that are valid are displayed seperately (if any).
        if (count($validissuers) === 0) {
            $mform->addElement('static', null, '', get_string('no_right_issuers', 'repository_nextcloud'));
        } else {
            $mform->addElement('static', null, '', get_string('right_issuers', 'repository_nextcloud',
                implode(', ', $validissuers)));
        }

        $mform->addElement('text', 'controlledlinkfoldername', get_string('foldername', 'repository_nextcloud'));
        $mform->addHelpButton('controlledlinkfoldername', 'foldername', 'repository_nextcloud');
        $mform->setType('controlledlinkfoldername', PARAM_TEXT);
        $mform->setDefault('controlledlinkfoldername', 'Moodlefiles');

        $mform->addElement('static', null, '', get_string('fileoptions', 'repository_nextcloud'));
        $choices = [
            'both' => get_string('both', 'repository_nextcloud'),
            'internal' => get_string('internal', 'repository_nextcloud'),
            'external' => get_string('external', 'repository_nextcloud'),
        ];
        $mform->addElement('select', 'supportedreturntypes', get_string('supportedreturntypes', 'repository_nextcloud'), $choices);

        $choices = [
            FILE_INTERNAL => get_string('internal', 'repository_nextcloud'),
            FILE_CONTROLLED_LINK => get_string('external', 'repository_nextcloud'),
        ];
        $mform->addElement('select', 'defaultreturntype', get_string('defaultreturntype', 'repository_nextcloud'), $choices);
    }

    /**
     * Save settings for repository instance
     *
     * @param array $options settings
     * @return bool
     */
    public function set_option($options = array()) {
        $options['issuerid'] = clean_param($options['issuerid'], PARAM_INT);
        $options['controlledlinkfoldername'] = clean_param($options['controlledlinkfoldername'], PARAM_TEXT);

        $ret = parent::set_option($options);
        return $ret;
    }

    /**
     * Names of the plugin settings
     *
     * @return array
     */
    public static function get_instance_option_names() {
        return ['issuerid', 'controlledlinkfoldername',
            'defaultreturntype', 'supportedreturntypes'];
    }

    /**
     * Method to define which file-types are supported (hardcoded can not be changed in Admin Menu)
     *
     * By default FILE_INTERNAL is supported. In case a system account is connected and an issuer exist,
     * FILE_CONTROLLED_LINK is supported.
     *
     * FILE_INTERNAL - the file is uploaded/downloaded and stored directly within the Moodle file system.
     * FILE_CONTROLLED_LINK - creates a copy of the file in Nextcloud from which private shares to permitted users will be
     * created. The file itself can not be changed any longer by the owner.
     *
     * @return int return type bitmask supported
     */
    public function supported_returntypes() {
        // We can only support references if the system account is connected.
        if (!empty($this->issuer) && $this->issuer->is_system_account_connected()) {
            $setting = $this->get_option('supportedreturntypes');
            if ($setting === 'internal') {
                return FILE_INTERNAL;
            } else if ($setting === 'external') {
                return FILE_CONTROLLED_LINK;
            } else {
                return FILE_CONTROLLED_LINK | FILE_INTERNAL | FILE_REFERENCE;
            }
        } else {
            return FILE_INTERNAL | FILE_REFERENCE;
        }
    }


    /**
     * Take the WebDAV `ls()' output and convert it into a format that Moodle's filepicker understands.
     *
     * @param string $dirpath Relative (urlencoded) path of the folder of interest.
     * @param array $ls Output by WebDAV
     * @return array Moodle-formatted list of directory contents; ready for use as $ret['list'] in get_listings
     */
    private function get_listing_convert_response($dirpath, $ls) {
        global $OUTPUT;
        $folders = array();
        $files = array();
        $parsedurl = issuer_management::parse_endpoint_url('webdav', $this->issuer);
        $basepath = rtrim('/' . ltrim($parsedurl['path'], '/ '), '/ ');

        foreach ($ls as $item) {
            if (!empty($item['lastmodified'])) {
                $item['lastmodified'] = strtotime($item['lastmodified']);
            } else {
                $item['lastmodified'] = null;
            }

            // Extracting object title from absolute path: First remove Nextcloud basepath.
            $item['href'] = substr(urldecode($item['href']), strlen($basepath));
            // Then remove relative path to current folder.
            $title = substr($item['href'], strlen($dirpath));

            if (!empty($item['resourcetype']) && $item['resourcetype'] == 'collection') {
                // A folder.
                if ($dirpath == $item['href']) {
                    // Skip "." listing.
                    continue;
                }

                $folders[strtoupper($title)] = array(
                    'title' => rtrim($title, '/'),
                    'thumbnail' => $OUTPUT->image_url(file_folder_icon(90))->out(false),
                    'children' => array(),
                    'datemodified' => $item['lastmodified'],
                    'path' => $item['href']
                );
            } else {
                // A file.
                $size = !empty($item['getcontentlength']) ? $item['getcontentlength'] : '';
                $files[strtoupper($title)] = array(
                    'title' => $title,
                    'thumbnail' => $OUTPUT->image_url(file_extension_icon($title, 90))->out(false),
                    'size' => $size,
                    'datemodified' => $item['lastmodified'],
                    'source' => $item['href']
                );
            }
        }
        ksort($files);
        ksort($folders);
        return array_merge($folders, $files);
    }

    /**
     * Print the login in a popup.
     *
     * @param array|null $attr Custom attributes to be applied to popup div.
     */
    private function print_login_popup($attr = null, $embed = false) {
        global $OUTPUT, $PAGE;

        if ($embed) {
            $PAGE->set_pagelayout('embedded');
        }

        $this->client = $this->get_user_oauth_client();
        $url = new moodle_url($this->client->get_login_url());
        $state = $url->get_param('state') . '&reloadparent=true';
        $url->param('state', $state);

        echo $OUTPUT->header();

        $button = new single_button($url, get_string('logintoaccount', 'repository', $this->get_name()),
            'post', true);
        $button->add_action(new popup_action('click', $url, 'Login'));
        $button->class = 'mdl-align';
        $button = $OUTPUT->render($button);
        echo html_writer::div($button, '', $attr);

        echo $OUTPUT->footer();
    }

    /**
     * Prepare response of get_listing; namely
     * - defining setting elements,
     * - filling in the parent path of the currently-viewed directory.
     *
     * @param string $path Relative path
     * @return array ret array for use as get_listing's $ret
     */
    private function get_listing_prepare_response($path) {
        $ret = [
            // Fetch the list dynamically. An AJAX request is sent to the server as soon as the user opens a folder.
            'dynload' => true,
            'nosearch' => true, // Disable search.
            'nologin' => false, // Provide a login link because a user logs into his/her private Nextcloud storage.
            'path' => array([ // Contains all parent paths to the current path.
                'name' => $this->get_meta()->name,
                'path' => '',
            ]),
            'defaultreturntype' => $this->default_returntype(),
            'manage' => $this->issuer->get('baseurl'), // Provide button to go into file management interface quickly.
            'list' => array(), // Contains all file/folder information and is required to build the file/folder tree.
            'filereferencewarning' => get_string('externalpubliclinkwarning', 'repository_nextcloud'),
        ];

        // If relative path is a non-top-level path, calculate all its parents' paths.
        // This is used for navigation in the file picker.
        if ($path != '/') {
            $chunks = explode('/', trim($path, '/'));
            $parent = '/';
            // Every sub-path to the last part of the current path is a parent path.
            foreach ($chunks as $chunk) {
                $subpath = $parent . $chunk . '/';
                $ret['path'][] = [
                    'name' => urldecode($chunk),
                    'path' => $subpath
                ];
                // Prepare next iteration.
                $parent = $subpath;
            }
        }
        return $ret;
    }

    /**
     * When a controlled link is clicked in the file picker get the human readable info about this file.
     *
     * @param string $reference
     * @param int $filestatus
     * @return string
     */
    public function get_reference_details($reference, $filestatus = 0) {
        if ($this->disabled) {
            throw new repository_exception('cannotdownload', 'repository');
        }
        if (empty($reference)) {
            return get_string('unknownsource', 'repository');
        }
        $source = json_decode($reference);
        $path = '';
        if (!empty($source->usesystem) && !empty($source->name)) {
            $path = $source->name;
        }

        return $path;
    }

    /**
     * Synchronize the external file if there is an update happened to it.
     *
     * If the file has been updated in the nextcloud instance, this method
     * would take care of the file we copy into the moodle file pool.
     *
     * The call to this method reaches from stored_file::sync_external_file()
     *
     * @param stored_file $file
     * @return bool true if synced successfully else false if not ready to sync or reference link not set
     */
    public function sync_reference(stored_file $file):bool {
        global $CFG;

        if ($file->get_referencelastsync() + DAYSECS > time()) {
            // Synchronize once per day.
            return false;
        }

        $reference = json_decode($file->get_reference());

        if (!isset($reference->link)) {
            return false;
        }

        $url = $reference->link;
        if (file_extension_in_typegroup($file->get_filepath() . $file->get_filename(), 'web_image')) {
            $saveas = $this->prepare_file(uniqid());
            try {
                $result = $this->curl->download_one($url, [], [
                    'filepath' => $saveas,
                    'timeout' => $CFG->repositorysyncimagetimeout,
                    'followlocation' => true,
                ]);

                $info = $this->curl->get_info();

                if ($result === true && isset($info['http_code']) && $info['http_code'] === 200) {
                    $file->set_synchronised_content_from_file($saveas);
                    return true;
                }
            } catch (Exception $e) {
                // If the download fails lets download with get().
                $this->curl->get($url, null, ['timeout' => $CFG->repositorysyncimagetimeout, 'followlocation' => true, 'nobody' => true]);
                $info = $this->curl->get_info();

                if (isset($info['http_code']) && $info['http_code'] === 200 &&
                    array_key_exists('download_content_length', $info) &&
                    $info['download_content_length'] >= 0) {
                        $filesize = (int)$info['download_content_length'];
                        $file->set_synchronized(null, $filesize);
                        return true;
                }

                $file->set_missingsource();
                return true;
            }
        }
        return false;
    }
}
