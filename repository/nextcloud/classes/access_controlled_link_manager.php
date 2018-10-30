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
 * Manages the creation and usage of access controlled links.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Nina Herrmann (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_nextcloud;

use context;
use \core\oauth2\api;
use \core\notification;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/webdavlib.php');

/**
 * Manages the creation and usage of access controlled links.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Nina Herrmann (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access_controlled_link_manager{
    /**
     * OCS client that uses the Open Collaboration Services REST API.
     * @var ocs_client
     */
    protected $ocsclient;
    /**
     * ocsclient of the systemaccount.
     * @var ocs_client
     */
    protected $systemocsclient;
    /**
     * Client to manage oauth2 features from the systemaccount.
     * @var \core\oauth2\client
     */
    protected $systemoauthclient;
    /**
     * Client to manage webdav request from the systemaccount..
     * @var \webdav_client
     */
    protected $systemwebdavclient;
    /**
     * Issuer from the oauthclient.
     * @var \core\oauth2\issuer
     */
    protected $issuer;
    /**
     * Name of the related repository.
     * @var string
     */
    protected $repositoryname;

    /**
     * Access_controlled_link_manager constructor.
     * @param ocs_client $ocsclient
     * @param \core\oauth2\client $systemoauthclient
     * @param ocs_client $systemocsclient
     * @param \core\oauth2\issuer $issuer
     * @param string $repositoryname
     * @throws configuration_exception
     */
    public function __construct($ocsclient, $systemoauthclient, $systemocsclient, $issuer, $repositoryname) {
        $this->ocsclient = $ocsclient;
        $this->systemoauthclient = $systemoauthclient;
        $this->systemocsclient = $systemocsclient;

        $this->repositoryname = $repositoryname;
        $this->issuer = $issuer;
        $this->systemwebdavclient = $this->create_system_dav();
    }

    /**
     * Deletes the share of the systemaccount and a user. In case the share could not be deleted a notification is
     * displayed.
     * @param int $shareid Remote ID of the share to be deleted.
     */
    public function delete_share_dataowner_sysaccount($shareid) {
        $shareid = (int) $shareid;
        $deleteshareparams = [
            'share_id' => $shareid
        ];
        $deleteshareresponse = $this->ocsclient->call('delete_share', $deleteshareparams);
        $xml = simplexml_load_string($deleteshareresponse);

        if (empty($xml->meta->statuscode) || $xml->meta->statuscode != 100 ) {
            notification::warning('You just shared a file with a access controlled link.
             However, the share between you and the systemaccount could not be deleted and is still present in your instance.');
        }
    }

    /**
     * Creates a share between a user and the system account. If $username is set the sharing direction is system account -> user,
     * otherwise user -> system account.
     * @param string $path Remote path of the file that will be shared
     * @param string $username optional when set the file is shared with the corresponding user otherwise with
     * the systemaccount.
     * @param bool $maywrite if false, only(!) read access is granted.
     * @return array statuscode, shareid, and filetarget
     * @throws request_exception
     */
    public function create_share_user_sysaccount($path, $username = null, $maywrite = false) {
        $result = array();

        if ($username != null) {
            $shareusername = $username;
        } else {
            $systemaccount = \core\oauth2\api::get_system_account($this->issuer);
            $shareusername = $systemaccount->get('username');
        }
        $permissions = ocs_client::SHARE_PERMISSION_READ;
        if ($maywrite) {
            // Add more privileges (write, reshare) if allowed for the given user.
            $permissions |= ocs_client::SHARE_PERMISSION_ALL;
        }
        $createshareparams = [
            'path' => $path,
            'shareType' => ocs_client::SHARE_TYPE_USER,
            'publicUpload' => false,
            'shareWith' => $shareusername,
            'permissions' => $permissions,
        ];

        // File is now shared with the system account.
        if ($username === null) {
            $createshareresponse = $this->ocsclient->call('create_share', $createshareparams);
        } else {
            $createshareresponse = $this->systemocsclient->call('create_share', $createshareparams);
        }
        $xml = simplexml_load_string($createshareresponse);

        $statuscode = (int)$xml->meta->statuscode;
        if ($statuscode != 100 && $statuscode != 403) {
            $details = get_string('filenotaccessed', 'repository_nextcloud');
            throw new request_exception(get_string('request_exception',
                'repository_nextcloud', array('instance' => $this->repositoryname, 'errormessage' => $details)));
        }
        $result['shareid'] = (int)$xml->data->id;
        $result['statuscode'] = $statuscode;
        $result['filetarget'] = (string)$xml->data[0]->file_target;

        return $result;
    }

    /** Copy or moves a file to a new path.
     * @param string $srcpath source path
     * @param string $dstpath
     * @param string $operation move or copy
     * @param  \webdav_client $webdavclient needed when moving files.
     * @return String Http-status of the request
     * @throws configuration_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     * @throws \repository_nextcloud\request_exception
     */
    public function transfer_file_to_path($srcpath, $dstpath, $operation, $webdavclient = null) {
        $this->systemwebdavclient->open();
        $webdavendpoint = issuer_management::parse_endpoint_url('webdav', $this->issuer);

        $srcpath = ltrim($srcpath, '/');
        $sourcepath = $webdavendpoint['path'] . $srcpath;
        $dstpath = ltrim($dstpath, '/');
        $destinationpath = $webdavendpoint['path'] . $dstpath . '/' . $srcpath;

        if ($operation === 'copy') {
            $result = $this->systemwebdavclient->copy_file($sourcepath, $destinationpath, true);
        } else if ($operation === 'move') {
            $result = $webdavclient->move($sourcepath, $destinationpath, false);
            if ($result == 412) {
                // A file with that name already exists at that target. Find a unique location!
                $increment = 0; // Will be appended to/inserted into the filename.
                // Define the pattern that is used to insert the increment to the filename.
                if (substr_count($srcpath, '.') === 0) {
                    // No file extension; append increment to the (sprintf-escaped) name.
                    $namepattern = str_replace('%', '%%', $destinationpath) . ' (%s)';
                } else {
                    // Append the increment to the second-to-last component, which is presumably the one before the extension.
                    // Again, the original path is sprintf-escaped.
                    $components = explode('.', str_replace('%', '%%', $destinationpath));
                    $components[count($components) - 2] .= ' (%s)';
                    $namepattern = implode('.', $components);
                }
            }
            while ($result == 412) {
                $increment++;
                $destinationpath = sprintf($namepattern, $increment);
                $result = $webdavclient->move($sourcepath, $destinationpath, false);
            }
        }
        $this->systemwebdavclient->close();
        if (!($result == 201 || $result == 412)) {
            $details = get_string('contactadminwith', 'repository_nextcloud',
                'A webdav request to ' . $operation . ' a file failed.');
            throw new request_exception(array('instance' => $this->repositoryname, 'errormessage' => $details));
        }
        return $result;
    }

    /**
     * Creates a unique folder path for the access controlled link.
     * @param context $context
     * @param string $component
     * @param string $filearea
     * @param string $itemid
     * @return string $result full generated path.
     * @throws request_exception If the folder path cannot be created.
     */
    public function create_folder_path_access_controlled_links($context, $component, $filearea, $itemid) {
        global $CFG, $SITE;
        // The fullpath to store the file is generated from the context.
        $contextlist = array_reverse($context->get_parent_contexts(true));
        $fullpath = '';
        $allfolders = [];
        foreach ($contextlist as $ctx) {
            // Prepare human readable context folders names, making sure they are still unique within the site.
            $prevlang = force_current_language($CFG->lang);
            $foldername = $ctx->get_context_name();
            force_current_language($prevlang);

            if ($ctx->contextlevel === CONTEXT_SYSTEM) {
                // Append the site short name to the root folder.
                $foldername .= ' ('.$SITE->shortname.')';
                // Append the relevant object id.
            } else if ($ctx->instanceid) {
                $foldername .= ' (id '.$ctx->instanceid.')';
            } else {
                // This does not really happen but just in case.
                $foldername .= ' (ctx '.$ctx->id.')';
            }

            $foldername = clean_param($foldername, PARAM_FILE);
            $allfolders[] = $foldername;
        }

        $allfolders[] = clean_param($component, PARAM_FILE);
        $allfolders[] = clean_param($filearea, PARAM_FILE);
        $allfolders[] = clean_param($itemid, PARAM_FILE);

        // Extracts the end of the webdavendpoint.
        $parsedwebdavurl = issuer_management::parse_endpoint_url('webdav', $this->issuer);
        $webdavprefix = $parsedwebdavurl['path'];
        $this->systemwebdavclient->open();
        // Checks whether folder exist and creates non-existent folders.
        foreach ($allfolders as $foldername) {
            $fullpath .= '/' . $foldername;
            $isdir = $this->systemwebdavclient->is_dir($webdavprefix . $fullpath);
            // Folder already exist, continue.
            if ($isdir === true) {
                continue;
            }
            $response = $this->systemwebdavclient->mkcol($webdavprefix . $fullpath);

            if ($response != 201) {
                $this->systemwebdavclient->close();
                $details = get_string('contactadminwith', 'repository_nextcloud',
                    get_string('pathnotcreated', 'repository_nextcloud', $fullpath));
                throw new request_exception(array('instance' => $this->repositoryname,
                    'errormessage' => $details));
            }
        }
        $this->systemwebdavclient->close();
        return $fullpath;
    }

    /** Creates a new webdav_client for the system account.
     * @return \webdav_client
     * @throws configuration_exception
     */
    public function create_system_dav() {
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

        // Authentication method is `bearer` for OAuth 2. Pass oauth client from which WebDAV obtains the token when needed.
        $dav = new \webdav_client($server, '', '', 'bearer', $webdavtype,
            $this->systemoauthclient->get_accesstoken()->token, $webdavendpoint['path']);

        $dav->port = $webdavport;
        $dav->debug = false;
        return $dav;
    }

    /** Creates a folder to store access controlled links.
     * @param string $controlledlinkfoldername
     * @param \webdav_client $webdavclient
     * @throws \coding_exception
     * @throws configuration_exception
     * @throws request_exception
     */
    public function create_storage_folder($controlledlinkfoldername, $webdavclient) {
        $parsedwebdavurl = issuer_management::parse_endpoint_url('webdav', $this->issuer);
        $webdavprefix = $parsedwebdavurl['path'];
        // Checks whether folder exist and creates non-existent folders.
        $webdavclient->open();
        $isdir = $webdavclient->is_dir($webdavprefix . $controlledlinkfoldername);
        // Folder already exist, continue.
        if (!$isdir) {
            $responsecreateshare = $webdavclient->mkcol($webdavprefix . $controlledlinkfoldername);

            if ($responsecreateshare != 201) {
                $webdavclient->close();
                throw new request_exception(array('instance' => $this->repositoryname,
                    'errormessage' => get_string('contactadminwith', 'repository_nextcloud',
                    'The folder to store files in the user account could not be created.')));
            }
        }
        $webdavclient->close();
    }

    /** Gets all shares from a path (the path is file specific) and extracts the share of a specific user. In case
     * multiple shares exist the first one is taken. Multiple shares can only appear when shares are created outside
     * of this plugin, therefore this case is not handled.
     * @param string $path
     * @param string $username
     * @return \SimpleXMLElement
     * @throws \moodle_exception
     */
    public function get_shares_from_path($path, $username) {
        $ocsparams = [
            'path' => $path,
            'reshares' => true
        ];

        $getsharesresponse = $this->systemocsclient->call('get_shares', $ocsparams);
        $xml = simplexml_load_string($getsharesresponse);
        $validelement = array();
        foreach ($fileid = $xml->data->element as $element) {
            if ($element->share_with == $username) {
                $validelement = $element;
                break;
            }
        }
        if (empty($validelement)) {
            throw new request_exception(array('instance' => $this->repositoryname,
                'errormessage' => get_string('filenotaccessed', 'repository_nextcloud')));

        }
        return $validelement->id;
    }

    /** This method can only be used if the response is from a newly created share. In this case there is more information
     * in the response. For a reference refer to
     * https://docs.nextcloud.com/server/13/developer_manual/core/ocs-share-api.html#get-information-about-a-known-share.
     * @param int $shareid
     * @param string $username
     * @return mixed the id of the share
     * @throws \coding_exception
     * @throws \repository_nextcloud\request_exception
     */
    public function get_share_information_from_shareid($shareid, $username) {
        $ocsparams = [
            'share_id' => (int) $shareid
        ];

        $shareinformation = $this->ocsclient->call('get_information_of_share', $ocsparams);
        $xml = simplexml_load_string($shareinformation);
        foreach ($fileid = $xml->data->element as $element) {
            if ($element->share_with == $username) {
                $validelement = $element;
                break;
            }
        }
        if (empty($validelement)) {
            throw new request_exception(array('instance' => $this->repositoryname,
                'errormessage' => get_string('filenotaccessed', 'repository_nextcloud')));

        }
        return (string) $validelement->file_target;
    }

    /**
     * Find a file that has previously been shared with the system account.
     * @param string $path Path to file in user context.
     * @return array shareid: ID of share, filetarget: path to file in sys account.
     * @throws request_exception If the share cannot be resolved.
     */
    public function find_share_in_sysaccount($path) {
        $systemaccount = \core\oauth2\api::get_system_account($this->issuer);
        $systemaccountuser = $systemaccount->get('username');

        // Find out share ID from user files.
        $ocsparams = [
            'path' => $path,
            'reshares' => true
        ];

        $getsharesresponse = $this->ocsclient->call('get_shares', $ocsparams);
        $xml = simplexml_load_string($getsharesresponse);
        $validelement = array();
        foreach ($fileid = $xml->data->element as $element) {
            if ($element->share_with == $systemaccountuser) {
                $validelement = $element;
                break;
            }
        }
        if (empty($validelement)) {
            throw new request_exception(array('instance' => $this->repositoryname,
                'errormessage' => get_string('filenotaccessed', 'repository_nextcloud')));
        }
        $shareid = (int) $validelement->id;

        // Use share id to find file name in system account's context.
        $ocsparams = [
            'share_id' => $shareid
        ];

        $shareinformation = $this->systemocsclient->call('get_information_of_share', $ocsparams);
        $xml = simplexml_load_string($shareinformation);
        foreach ($fileid = $xml->data->element as $element) {
            if ($element->share_with == $systemaccountuser) {
                $validfile = $element;
                break;
            }
        }
        if (empty($validfile)) {
            throw new request_exception(array('instance' => $this->repositoryname,
                'errormessage' => get_string('filenotaccessed', 'repository_nextcloud')));

        }
        return [
            'shareid' => $shareid,
            'filetarget' => (string) $validfile->file_target
            ];
    }
}
