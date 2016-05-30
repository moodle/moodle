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
 * Box.net client.
 *
 * @package core
 * @author James Levy <james@box.net>
 * @link http://enabled.box.net
 * @access public
 * @version 1.0
 * @copyright copyright Box.net 2007
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/oauthlib.php');

/**
 * Box.net client class.
 *
 * @package    core
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class boxnet_client extends oauth2_client {

    /** @const API URL */
    const API = 'https://api.box.com/2.0';

    /** @const UPLOAD_API URL */
    const UPLOAD_API = 'https://upload.box.com/api/2.0';

    /**
     * Return authorize URL.
     *
     * @return string
     */
    protected function auth_url() {
        return 'https://www.box.com/api/oauth2/authorize';
    }

    /**
     * Create a folder.
     *
     * @param string $foldername The folder name.
     * @param int $parentid The ID of the parent folder.
     * @return array Information about the new folder.
     */
    public function create_folder($foldername, $parentid = 0) {
        $params = array('name' => $foldername, 'parent' => array('id' => (string) $parentid));
        $this->reset_state();
        $result = $this->post($this->make_url("/folders"), json_encode($params));
        $result = json_decode($result);
        return $result;
    }

    /**
     * Download the file.
     *
     * @param int $fileid File ID.
     * @param string $path Path to download the file to.
     * @return bool Success or not.
     */
    public function download_file($fileid, $path) {
        $this->reset_state();
        $result = $this->download_one($this->make_url("/files/$fileid/content"), array(),
            array('filepath' => $path, 'CURLOPT_FOLLOWLOCATION' => true));
        return ($result === true && $this->info['http_code'] === 200);
    }

    /**
     * Get info of a file.
     *
     * @param int $fileid File ID.
     * @return object
     */
    public function get_file_info($fileid) {
        $this->reset_state();
        $result = $this->request($this->make_url("/files/$fileid"));
        return json_decode($result);
    }

    /**
     * Get a folder content.
     *
     * @param int $folderid Folder ID.
     * @return object
     */
    public function get_folder_items($folderid = 0) {
        $this->reset_state();
        $result = $this->request($this->make_url("/folders/$folderid/items",
            array('fields' => 'id,name,type,modified_at,size,owned_by')));
        return json_decode($result);
    }

    /**
     * Log out.
     *
     * @return void
     */
    public function log_out() {
        if ($accesstoken = $this->get_accesstoken()) {
            $params = array(
                'client_id' => $this->get_clientid(),
                'client_secret' => $this->get_clientsecret(),
                'token' => $accesstoken->token
            );
            $this->reset_state();
            $this->post($this->revoke_url(), $params);
        }
        parent::log_out();
    }

    /**
     * Build a request URL.
     *
     * @param string $uri The URI to request.
     * @param array $params Query string parameters.
     * @param bool $uploadapi Whether this works with the upload API or not.
     * @return string
     */
    protected function make_url($uri, $params = array(), $uploadapi = false) {
        $api = $uploadapi ? self::UPLOAD_API : self::API;
        $url = new moodle_url($api . '/' . ltrim($uri, '/'), $params);
        return $url->out(false);
    }

    /**
     * Rename a file.
     *
     * @param int $fileid The file ID.
     * @param string $newname The new file name.
     * @return object Box.net file object.
     */
    public function rename_file($fileid, $newname) {
        // This requires a PUT request with data within it. We cannot use
        // the standard PUT request 'CURLOPT_PUT' because it expects a file.
        $data = array('name' => $newname);
        $options = array(
            'CURLOPT_CUSTOMREQUEST' => 'PUT',
            'CURLOPT_POSTFIELDS' => json_encode($data)
        );
        $url = $this->make_url("/files/$fileid");
        $this->reset_state();
        $result = $this->request($url, $options);
        $result = json_decode($result);
        return $result;
    }

    /**
     * Resets curl for multiple requests.
     *
     * @return void
     */
    public function reset_state() {
        $this->cleanopt();
        $this->resetHeader();
    }

    /**
     * Return the revoke URL.
     *
     * @return string
     */
    protected function revoke_url() {
        return 'https://www.box.com/api/oauth2/revoke';
    }

    /**
     * Share a file and return the link to it.
     *
     * @param string $fileid The file ID.
     * @param bool $businesscheck Whether or not to check if the user can share files, has a business account.
     * @return object
     */
    public function share_file($fileid, $businesscheck = true) {
        // Sharing the file, this requires a PUT request with data within it. We cannot use
        // the standard PUT request 'CURLOPT_PUT' because it expects a file.
        $data = array('shared_link' => array('access' => 'open', 'permissions' =>
            array('can_download' => true, 'can_preview' => true)));
        $options = array(
            'CURLOPT_CUSTOMREQUEST' => 'PUT',
            'CURLOPT_POSTFIELDS' => json_encode($data)
        );
        $this->reset_state();
        $result = $this->request($this->make_url("/files/$fileid"), $options);
        $result = json_decode($result);

        if ($businesscheck) {
            // Checks that the user has the right to share the file. If not, throw an exception.
            $this->reset_state();
            $this->head($result->shared_link->download_url);
            $info = $this->get_info();
            if ($info['http_code'] == 403) {
                throw new moodle_exception('No permission to share the file');
            }
        }

        return $result->shared_link;
    }

    /**
     * Search.
     *
     * @return object
     */
    public function search($query) {
        $this->reset_state();
        $result = $this->request($this->make_url('/search', array('query' => $query, 'limit' => 50, 'offset' => 0)));
        return json_decode($result);
    }

    /**
     * Return token URL.
     *
     * @return string
     */
    protected function token_url() {
        return 'https://www.box.com/api/oauth2/token';
    }

    /**
     * Upload a file.
     *
     * Please note that the file is named on Box.net using the path we are providing, and so
     * the file has the name of the stored_file hash.
     *
     * @param stored_file $storedfile A stored_file.
     * @param integer $parentid The ID of the parent folder.
     * @return object Box.net file object.
     */
    public function upload_file(stored_file $storedfile, $parentid = 0) {
        $url = $this->make_url('/files/content', array(), true);
        $options = array(
            'filename' => $storedfile,
            'parent_id' => $parentid
        );
        $this->reset_state();
        $result = $this->post($url, $options);
        $result = json_decode($result);
        return $result;
    }

}

/**
 * Box REST Client Library for PHP5 Developers.
 *
 * Deprecation note: As of the 14th of December 2013 Box.net APIv1, used by this class,
 * is reaching its end of life. Please use boxnet_client() instead.
 *
 * @package core
 * @author James Levy <james@box.net>
 * @link http://enabled.box.net
 * @access public
 * @version 1.0
 * @copyright copyright Box.net 2007
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since 2.6, 2.5.3, 2.4.7
 */
class boxclient {
    /** @var string */
    public $auth_token = '';
    /** @var string */
    private $_box_api_url = 'https://www.box.com/api/1.0/rest';
    private $_box_api_upload_url = 'http://upload.box.com/api/1.0/upload';
    private $_box_api_download_url = 'http://www.box.com/api/1.0/download';
    private $_box_api_auth_url = 'http://www.box.com/api/1.0/auth';
    private $_error_code = '';
    private $_error_msg = '';
    /** @var bool */
    private $debug = false;

    /**
     * @param string $api_key
     * @param string $auth_token
     * @param bool $debug
     */
    public function __construct($api_key, $auth_token = '', $debug = false) {
        $this->api_key    = $api_key;
        $this->auth_token = $auth_token;
        if (!empty($debug)) {
            $this->debug = true;
        } else {
            $this->debug = false;
        }
    }
    /**
     * Setup for Functions
     *
     * @param string $method
     * @param array $params
     * @return array
     */
    function makeRequest($method, $params = array()) {
        $this->_clearErrors();
        $c = new curl(array('debug'=>$this->debug, 'cache'=>true, 'module_cache'=>'repository'));
        $c->setopt(array('CURLOPT_FOLLOWLOCATION'=>1));
        try {
            if ($method == 'upload'){
                $request = $this->_box_api_upload_url.'/'.
                    $this->auth_token.'/'.$params['folder_id'];
                $xml = $c->post($request, $params);
            }else{
                $args = array();
                $xml = $c->get($this->_box_api_url, $params);
            }
            $xml_parser = xml_parser_create();
            // set $data here
            xml_parse_into_struct($xml_parser, $xml, $data);
            xml_parser_free($xml_parser);
        } catch (moodle_exception $e) {
            $this->setError(0, 'connection time-out or invalid url');
            return false;
        }
        return $data;
    }
    /**
     * @param array $params
     * @return array
     */
    function getTicket($params = array()) {
        $params['api_key'] = $this->api_key;
        $params['action']  = 'get_ticket';
        $ret_array = array();
        $data = $this->makeRequest('action=get_ticket', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            switch ($a['tag']) {
            case 'STATUS':
                $ret_array['status'] = $a['value'];
                break;
            case 'TICKET':
                $ret_array['ticket'] = $a['value'];
                break;
            }
        }
        return $ret_array;
    }

    /**
     * $options['username'] and $options['password'] must be
     * given, we  will use them to obtain a valid auth_token
     * To get a token, you should use following code:
     *
     * <code>
     * $box = new boxclient('dmls97d8j3i9tn7av8y71m9eb55vrtj4');
     * Get a ticket
     * $t = $box->getTicket();
     * $box->getAuthToken($t['ticket'], array(
     *              'username'=>'dongsheng@moodle.com',
     *              'password'=>'xxx'));
     * </code>
     *
     * @param string $ticket
     * @param string $username
     * @param string $password
     * @return mixed
     */
    function getAuthToken($ticket, $username, $password) {
        $c = new curl(array('debug'=>$this->debug));
        $c->setopt(array('CURLOPT_FOLLOWLOCATION'=>0));
        $param =  array(
            'login_form1'=>'',
            'login'=>$username,
            'password'=>$password,
            'dologin'=>1,
            '__login'=>1
            );
        try {
            $ret = $c->post($this->_box_api_auth_url.'/'.$ticket, $param);
        } catch (moodle_exception $e) {
            $this->setError(0, 'connection time-out or invalid url');
            return false;
        }
        $header = $c->getResponse();
        if(empty($header['location'])) {
            throw new repository_exception('invalidpassword', 'repository_boxnet');
        }
        $location = $header['location'];
        preg_match('#auth_token=(.*)$#i', $location, $matches);
        $auth_token = $matches[1];
        if(!empty($auth_token)) {
            $this->auth_token = $auth_token;
            return $auth_token;
        } else {
            throw new repository_exception('invalidtoken', 'repository_boxnet');
        }
    }
    /**
     * @param string $path Unused
     * @param array $params
     * @return array
     */
    function getfiletree($path, $params = array()) {
        $this->_clearErrors();
        $params['auth_token'] = $this->auth_token;
        $params['folder_id']  = 0;
        $params['api_key']    = $this->api_key;
        $params['action']     = 'get_account_tree';
        $params['onelevel']   = 1;
        $params['params[]']   = 'nozip';
        $c = new curl(array('debug'=>$this->debug));
        $c->setopt(array('CURLOPT_FOLLOWLOCATION'=>1));
        try {
            $args = array();
            $xml = $c->get($this->_box_api_url, $params);
        } catch (Exception $e){
        }
        $ret = array();
        $o = simplexml_load_string(trim($xml));
        if($o->status == 'listing_ok') {
            $tree = $o->tree->folder;
            $this->buildtree($tree, $ret);
        }
        return $ret;
    }

    /**
     * Get box.net file info
     *
     * @param string $fileid
     * @param int $timeout request timeout in seconds
     * @return stdClass|null
     */
    function get_file_info($fileid, $timeout = 0) {
        $this->_clearErrors();
        $params = array();
        $params['action']     = 'get_file_info';
        $params['file_id']    = $fileid;
        $params['auth_token'] = $this->auth_token;
        $params['api_key']    = $this->api_key;
        $http = new curl(array('debug'=>$this->debug));
        $xml = $http->get($this->_box_api_url, $params, array('timeout' => $timeout));
        if (!$http->get_errno()) {
            $o = simplexml_load_string(trim($xml));
            if ($o->status == 's_get_file_info') {
                return $o->info;
            }
        }
        return null;
    }

    /**
     * @param array $sax
     * @param array $tree Passed by reference
     */
    function buildtree($sax, &$tree){
        $sax = (array)$sax;
        $count = 0;
        foreach($sax as $k=>$v){
            if($k == 'folders'){
                $o = $sax[$k];
                foreach($o->folder as $z){
                    $tmp = array('title'=>(string)$z->attributes()->name,
                        'size'=>0, 'date'=>userdate(time()),
                        'thumbnail'=>'https://www.box.com/img/small_folder_icon.gif',
                        'path'=>array('name'=>(string)$z->attributes()->name, 'path'=>(int)$z->attributes()->id));
                    $tmp['children'] = array();
                    $this->buildtree($z, $tmp['children']);
                    $tree[] = $tmp;
                }
            } elseif ($k == 'files') {
                $val = $sax[$k]->file;
                foreach($val as $file){
                    $thumbnail = (string)$file->attributes()->thumbnail;
                    if (!preg_match('#^(?:http://)?([^/]+)#i', $thumbnail)) {
                        $thumbnail =  'http://www.box.com'.$thumbnail;
                    }
                    $tmp = array('title'=>(string)$file->attributes()->file_name,
                        'size'=>display_size((int)$file->attributes()->size),
                        'thumbnail'=>$thumbnail,
                        'date'=>userdate((int)$file->attributes()->updated),
                        'source'=> $this->_box_api_download_url .'/'
                            .$this->auth_token.'/'.(string)$file->attributes()->id,
                        'url'=>(string)$file->attributes()->shared_link);
                    $tree[] = $tmp;
                }
            }
            $count++;
        }
    }
    /**
     * @param array $params
     * @return bool|array Array or false
     */
    function getAccountTree($params = array()) {
        $params['auth_token'] = $this->auth_token;
        $params['folder_id']  = 0;
        $params['api_key']    = $this->api_key;
        $params['action']     = 'get_account_tree';
        $params['onelevel']   = 1;
        $params['params[]']   = 'nozip';
        $ret_array = array();
        $data = $this->makeRequest('action=get_account_tree', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        $tree_count=count($data);
        $entry_count = 0;
        for ($i=0; $i<$tree_count; $i++) {
            $a = $data[$i];
            switch ($a['tag'])
            {
            case 'FOLDER':
                if (@is_array($a['attributes'])) {
                    $ret_array['folder_id'][$i] = $a['attributes']['ID'];
                    $ret_array['folder_name'][$i] = $a['attributes']['NAME'];
                    $ret_array['shared'][$i] = $a['attributes']['SHARED'];
                }
                break;

            case 'FILE':
                if (@is_array($a['attributes'])) {
                    $ret_array['file_id'][$i] = $a['attributes']['ID'];
                    @$ret_array['file_name'][$i] = $a['attributes']['FILE_NAME'];
                    @$ret_array['file_keyword'][$i] = $a['attributes']['KEYWORD'];
                    @$ret_array['file_size'][$i] = display_size($a['attributes']['SIZE']);
                    @$ret_array['file_date'][$i] = userdate($a['attributes']['UPDATED']);
                    if (preg_match('#^(?:http://)?([^/]+)#i', $a['attributes']['THUMBNAIL'])) {
                        @$ret_array['thumbnail'][$i] =  $a['attributes']['THUMBNAIL'];
                    } else {
                        @$ret_array['thumbnail'][$i] =  'http://www.box.com'.$a['attributes']['THUMBNAIL'];
                    }
                    $entry_count++;
                }
                break;
            }
        }
        return $ret_array;
    }

    /**
     * @param string $new_folder_name
     * @param array $params
     * @return bool|array Array or false
     */
    function CreateFolder($new_folder_name, $params = array()) {
        $params['auth_token'] =  $this->auth_token;
        $params['api_key']    = $this->api_key;
        $params['action']     = 'create_folder';
        $params['name']       = $new_folder_name;
        $defaults = array(
            'parent_id'  => 0, //Set to '0' by default. Change to create within sub-folder.
            'share'     => 1, //Set to '1' by default. Set to '0' to make folder private.
        );
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $params)) {
                $params[$key] = $value;
            }
        }

        $ret_array = array();
        $data = $this->makeRequest('action=create_folder', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            if (!empty($a['value'])) {
                switch ($a['tag']) {

                case 'FOLDER_ID':
                    $ret_array['folder_id'] = $a['value'];
                    break;

                case 'FOLDER_NAME':
                    $ret_array['folder_name'] = $a['value'];
                    break;

                case 'FOLDER_TYPE_ID':
                    $ret_array['folder_type_id'] = $a['value'];
                    break;

                case 'SHARED':
                    $ret_array['shared'] = $a['value'];
                    break;

                case 'PASSWORD':
                    $ret_array['password'] = $a['value'];
                    break;
                }
            } else {
                $ret_array[strtolower($a['tag'])] = null;
            }
        }
        return $ret_array;
    }

    /**
     * Upload a File
     * @param array $params the file MUST be present in key 'file' and be a moodle stored_file object.
     * @return array|bool Array or false
     */
    function UploadFile ($params = array()) {
        $params['auth_token'] = $this->auth_token;
        // this param should be the full path of the file
        $params['new_file1']  = $params['file'];
        unset($params['file']);
        $defaults = array(
            'folder_id' => 0, //Set to '0' by default. Change to create within sub-folder.
            'share'     => 1, //Set to '1' by default. Set to '0' to make folder private.
        );
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $params)) {
                $params[$key] = $value;
            }
        }
        $ret_array = array();
        $entry_count = 0;
        $data = $this->makeRequest('upload', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        for ($i=0, $tree_count=count($data); $i<$tree_count; $i++) {
            $a = $data[$i];
            switch ($a['tag']) {
            case 'STATUS':
                $ret_array['status'] = $a['value'];
                break;

            case 'FILE':
                if (is_array($a['attributes'])) {
                    @$ret_array['file_name'][$i] = $a['attributes']['FILE_NAME'];
                    @$ret_array['id'][$i] = $a['attributes']['ID'];
                    @$ret_array['folder_name'][$i] = $a['attributes']['FOLDER_NAME'];
                    @$ret_array['error'][$i] = $a['attributes']['ERROR'];
                    @$ret_array['public_name'][$i] = $a['attributes']['PUBLIC_NAME'];
                    $entry_count++;
                }
                break;
            }
        }

        return $ret_array;
    }
    /**
     * @param string $fileid
     * @param string $newname
     * @return bool
     */
    function RenameFile($fileid, $newname) {
        $params = array(
            'api_key'    => $this->api_key,
            'auth_token' => $this->auth_token,
            'action'     => 'rename',
            'target'     => 'file',
            'target_id'  => $fileid,
            'new_name'   => $newname,
        );
        $data = $this->makeRequest('action=rename', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            switch ($a['tag']) {
                case 'STATUS':
                    if ($a['value'] == 's_rename_node') {
                        return true;
                    }
            }
        }
        return false;
    }

    /**
     * Register New User
     *
     * @param array $params
     * @return array|bool Outcome Array or false
     */
    function RegisterUser($params = array()) {
        $params['api_key'] = $this->api_key;
        $params['action']  = 'register_new_user';
        $params['login']   = $_REQUEST['login'];
        $params['password'] = $_REQUEST['password'];
        $ret_array = array();
        $data = $this->makeRequest('action=register_new_user', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            switch ($a['tag']) {
            case 'STATUS':
                $ret_array['status'] = $a['value'];
                break;

            case 'AUTH_TOKEN':
                $ret_array['auth_token'] = $a['value'];
                break;

            case 'LOGIN':
                $ret_array['login'] = $a['value'];
                break;
            case 'SPACE_AMOUNT':
                $ret_array['space_amount'] = $a['value'];
                break;
            case 'SPACE_USED':
                $ret_array['space_used'] = $a['value'];
                break;
            }
        }

        return $ret_array;
    }

    /**
     * Add Tags  (http://enabled.box.net/docs/rest#add_to_tag)
     *
     * @param string $tag
     * @param string $id Set to ID of file or folder
     * @param string $target_type File or folder
     * @param array $params
     * @return array|bool Outcome Array or false
     */
    function AddTag($tag, $id, $target_type, $params = array()) {
        $params['auth_token'] = $this->auth_token;
        $params['api_key']    = $this->api_key;
        $params['action']     = 'add_to_tag';
        $params['target']     = $target_type; // File or folder
        $params['target_id']  = $id; // Set to ID of file or folder
        $params['tags[]']     = $tag;
        $ret_array = array();
        $data = $this->makeRequest('action=add_to_tag', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            switch ($a['tag']) {
            case 'STATUS':
                $ret_array['status'] = $a['value'];

                break;
            }
        }
        return $ret_array;
    }

    /**
     * Public Share  (http://enabled.box.net/docs/rest#public_share)
     *
     * @param string $message
     * @param string $emails
     * @param string $id Set to ID of file or folder
     * @param string $target_type File or folder
     * @param string $password
     * @param array $params
     * @return array|bool Outcome Array or false
     */
    function PublicShare($message, $emails, $id, $target_type, $password, $params = array()) {
        $params['auth_token'] = $this->auth_token;
        $params['api_key']    = $this->api_key;
        $params['action']     = 'public_share';
        $params['target']     = $target_type;
        $params['target_id']  = $id;
        $params['password']   =  $password;
        $params['message']    = $message;
        $params['emails']     = $emails;
        $ret_array = array();
        $data = $this->makeRequest('action=public_share', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            switch ($a['tag']) {
            case 'STATUS':
                $ret_array['status'] = $a['value'];
                break;
            case 'PUBLIC_NAME':
                $ret_array['public_name'] = $a['value'];
                break;
            }
        }

        return $ret_array;
    }
    /**
     * Get Friends  (http://enabled.box.net/docs/rest#get_friends)
     *
     * @param array $params
     * @return array|bool Outcome Array or false
     */
    function GetFriends ($params = array()) {
        $params['auth_token'] = $this->auth_token;
        $params['action']     = 'get_friends';
        $params['api_key']    = $this->api_key;
        $params['params[]']   = 'nozip';
        $ret_array = array();
        $data = $this->makeRequest('action=get_friends', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            switch ($a['tag']) {
            case 'NAME':
                $ret_array['name'] = $a['value'];
                break;
            case 'EMAIL':
                $ret_array['email'] = $a['value'];
                break;
            case 'ACCEPTED':
                $ret_array['accepted'] = $a['value'];
                break;
            case 'AVATAR_URL':
                $ret_array['avatar_url'] = $a['value'];
                break;
            case 'ID':
                $ret_array['id'] = $a['value'];
                break;
            case 'URL':
                $ret_array['url'] = $a['value'];
                break;
            case 'STATUS':
                $ret_array['status'] = $a['value'];
                break;
            }
        }
        return $ret_array;
    }

    /**
     * Logout User  (http://enabled.box.net/docs/rest#get_friends)
     *
     * @param array $params
     * @return array|bool Outcome Array or false
     */
    function Logout($params = array()) {
        $params['auth_token'] = $this->auth_token;
        $params['api_key']    = $this->api_key;
        $params['action']     = 'logout';
        $ret_array = array();
        $data = $this->makeRequest('action=logout', $params);
        if ($this->_checkForError($data)) {
            return false;
        }
        foreach ($data as $a) {
            switch ($a['tag']) {
            case 'ACTION':
                $ret_array['logout'] = $a['value'];

                break;
            }
            return $ret_array;
        }
    }
    /**
     * @param array $data
     * @return bool
     */
    function _checkForError($data) {
        if ($this->_error_msg != '') {
            return true;
        }
        if (@$data[0]['attributes']['STAT'] == 'fail') {
            $this->_error_code = $data[1]['attributes']['CODE'];
            $this->_error_msg = $data[1]['attributes']['MSG'];
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isError() {
        if  ($this->_error_msg != '') {
            return true;
        }
        return false;
    }
    /**
     *
     */
    public function setError($code = 0, $msg){
        $this->_error_code = $code;
        $this->_error_msg  = $msg;
    }
    /**
     * @return string
     */
    function getErrorMsg() {
        return '<p>Error: (' . $this->_error_code . ') ' . $this->_error_msg . '</p>';
    }
    /**
     * @return string
     */
    function getErrorCode() {
        return $this->_error_code;
    }
    /**
     *
     */
    function _clearErrors() {
        $this->_error_code = '';
        $this->_error_msg = '';
    }

}
