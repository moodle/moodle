<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    core
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Simple implementation of some Google API functions for Moodle.
 */

defined('MOODLE_INTERNAL') || die();

 /** Include essential file */
require_once($CFG->libdir.'/filelib.php');

/**
 * Base class for google authenticated http requests
 *
 * Most Google API Calls required that requests are sent with an
 * Authorization header + token. This class extends the curl class
 * to aid this
 *
 * @package    moodlecore
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class google_auth_request extends curl {
    protected $token = '';
    private $persistantheaders = array();

    // Must be overridden with the authorization header name
    public static function get_auth_header_name() {
        throw new coding_exception('get_auth_header_name() method needs to be overridden in each subclass of google_auth_request');
    }

    protected function request($url, $options = array()){
        if($this->token){
            // Adds authorisation head to a request so that it can be authentcated
            $this->setHeader('Authorization: '. $this->get_auth_header_name().'"'.$this->token.'"');
        }

        foreach($this->persistantheaders as $h){
            $this->setHeader($h);
        }

        $ret = parent::request($url, $options);
        // reset headers for next request
        $this->header = array();
        return $ret;
    }

    protected function multi($requests, $options = array()) {
        if($this->token){
            // Adds authorisation head to a request so that it can be authentcated
            $this->setHeader('Authorization: '. $this->get_auth_header_name().'"'.$this->token.'"');
        }

        foreach($this->persistantheaders as $h){
            $this->setHeader($h);
        }

        $ret = parent::multi($requests, $options);
        // reset headers for next request
        $this->header = array();
        return $ret;
    }

    public function get_sessiontoken(){
        return $this->token;
    }

    public function add_persistant_header($header){
        $this->persistantheaders[] = $header;
    }
}

/*******
 * The following two classes are usd to implement AuthSub google
 * authtentication, as documented here:
 * http://code.google.com/apis/accounts/docs/AuthSub.html
 *******/

/**
 * Used to uprade a google AuthSubRequest one-time token into
 * a session token which can be used long term.
 *
 * @package    moodlecore
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_authsub_request extends google_auth_request {
    const AUTHSESSION_URL = 'https://www.google.com/accounts/AuthSubSessionToken';

    /**
     * Constructor. Calls constructor of its parents
     *
     * @param string $authtoken The token to upgrade to a session token
     */
    public function __construct($authtoken){
        parent::__construct();
        $this->token = $authtoken;
    }

    /**
     * Requests a long-term session token from google based on the
     *
     * @return string Sub-Auth token
     */
    public function get_session_token(){
        $content = $this->get(google_authsub_request::AUTHSESSION_URL);

        if( preg_match('/token=(.*)/i', $content, $matches) ){
            return $matches[1];
        }else{
            throw new moodle_exception('could not upgrade google authtoken to session token');
        }
    }

    public static function get_auth_header_name(){
        return 'AuthSub token=';
    }
}

/**
 * Allows http calls using google subauth authorisation
 *
 * @package    moodlecore
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_authsub extends google_auth_request {
    const LOGINAUTH_URL    = 'https://www.google.com/accounts/AuthSubRequest';
    const VERIFY_TOKEN_URL = 'https://www.google.com/accounts/AuthSubTokenInfo';
    const REVOKE_TOKEN_URL = 'https://www.google.com/accounts/AuthSubRevokeToken';

    /**
     * Constructor, allows subauth requests using the response from an initial
     * AuthSubRequest or with the subauth long-term token. Note that constructing
     * this object without a valid token will cause an exception to be thrown.
     *
     * @param string $sessiontoken A long-term subauth session token
     * @param string $authtoken A one-time auth token wich is used to upgrade to session token
     * @param mixed  @options Options to pass to the base curl object
     */
    public function __construct($sessiontoken = '', $authtoken = '', $options = array()){
        parent::__construct($options);

        if( $authtoken ){
            $gauth = new google_authsub_request($authtoken);
            $sessiontoken = $gauth->get_session_token();
        }

        $this->token = $sessiontoken;
        if(! $this->valid_token() ){
            throw new moodle_exception('Invalid subauth token');
        }
    }

    /**
     * Tests if a subauth token used is valid
     *
     * @return boolean true if token valid
     */
    public function valid_token(){
        $this->get(google_authsub::VERIFY_TOKEN_URL);

        if($this->info['http_code'] === 200){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Calls googles api to revoke the subauth token
     *
     * @return boolean Returns true if token succesfully revoked
     */
    public function revoke_session_token(){
        $this->get(google_authsub::REVOKE_TOKEN_URL);

        if($this->info['http_code'] === 200){
            $this->token = '';
            return true;
        }else{
            return false;
        }
    }

    /**
     * Creates a login url for subauth request
     *
     * @param string $returnaddr The address which the user should be redirected to recieve the token
     * @param string $realm The google realm which is access is being requested
     * @return string URL to bounce the user to
     */
    public static function login_url($returnaddr, $realm){
        $uri = google_authsub::LOGINAUTH_URL.'?next='
            .urlencode($returnaddr)
            .'&scope='
            .urlencode($realm)
            .'&session=1&secure=0';

        return $uri;
    }

    public static function get_auth_header_name(){
        return 'AuthSub token=';
    }
}

/**
 * Class for manipulating google documents through the google data api
 * Docs for this can be found here:
 * {@link http://code.google.com/apis/documents/docs/2.0/developers_guide_protocol.html}
 *
 * @package    moodlecore
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_docs {
    // need both docs and the spreadsheets realm
    const REALM            = 'https://docs.google.com/feeds/ https://spreadsheets.google.com/feeds/ https://docs.googleusercontent.com/';
    const DOCUMENTFEED_URL = 'https://docs.google.com/feeds/default/private/full';
    const USER_PREF_NAME   = 'google_authsub_sesskey';

    private $google_curl = null;

    /**
     * Constructor.
     *
     * @param object A google_auth_request object which can be used to do http requests
     */
    public function __construct($google_curl){
        if(is_a($google_curl, 'google_auth_request')){
            $this->google_curl = $google_curl;
            $this->google_curl->add_persistant_header('GData-Version: 3.0');
        }else{
            throw new moodle_exception('Google Curl Request object not given');
        }
    }

    public static function get_sesskey($userid){
        return get_user_preferences(google_docs::USER_PREF_NAME, false, $userid);
    }

    public static function set_sesskey($value, $userid){
        return set_user_preference(google_docs::USER_PREF_NAME, $value, $userid);
    }

    public static function delete_sesskey($userid){
        return unset_user_preference(google_docs::USER_PREF_NAME, $userid);
    }

    /**
     * Returns a list of files the user has formated for files api
     *
     * @param string $search A search string to do full text search on the documents
     * @return mixed Array of files formated for fileapoi
     */
    #FIXME
    public function get_file_list($search = ''){
        global $CFG, $OUTPUT;
        $url = google_docs::DOCUMENTFEED_URL;

        if($search){
            $url.='?q='.urlencode($search);
        }
        $content = $this->google_curl->get($url);

        $xml = new SimpleXMLElement($content);


        $files = array();
        foreach($xml->entry as $gdoc){
            $docid  = (string) $gdoc->children('http://schemas.google.com/g/2005')->resourceId;
            list($type, $docid) = explode(':', $docid);

            $title  = '';
            $source = '';
            // FIXME: We're making hard-coded choices about format here.
            // If the repo api can support it, we could let the user
            // chose.
            switch($type){
                case 'document':
                    $title = $gdoc->title.'.rtf';
                    $source = 'https://docs.google.com/feeds/download/documents/Export?id='.$docid.'&exportFormat=rtf';
                    break;
                case 'presentation':
                    $title = $gdoc->title.'.ppt';
                    $source = 'https://docs.google.com/feeds/download/presentations/Export?id='.$docid.'&exportFormat=ppt';
                    break;
                case 'spreadsheet':
                    $title = $gdoc->title.'.xls';
                    $source = 'https://spreadsheets.google.com/feeds/download/spreadsheets/Export?key='.$docid.'&exportFormat=xls';
                    break;
                case 'pdf':
                    $title  = (string)$gdoc->title;
                    $source = (string)$gdoc->content[0]->attributes()->src;
                    break;
            }

            if(!empty($source)){
                $files[] =  array( 'title' => $title,
                    'url' => "{$gdoc->link[0]->attributes()->href}",
                    'source' => $source,
                    'date'   => usertime(strtotime($gdoc->updated)),
                    'thumbnail' => (string) $OUTPUT->pix_url(file_extension_icon($title, 32))
                );
            }
        }

        return $files;
    }

    /**
     * Sends a file object to google documents
     *
     * @param object $file File object
     * @return boolean True on success
     */
    public function send_file($file){
        $this->google_curl->setHeader("Content-Length: ". $file->get_filesize());
        $this->google_curl->setHeader("Content-Type: ". $file->get_mimetype());
        $this->google_curl->setHeader("Slug: ". $file->get_filename());

        $this->google_curl->post(google_docs::DOCUMENTFEED_URL, $file->get_content());

        if($this->google_curl->info['http_code'] === 201){
            return true;
        }else{
            return false;
        }
    }

    public function download_file($url, $fp){
        return $this->google_curl->download(array( array('url'=>$url, 'file' => $fp) ));
    }
}

/**
 * Class for manipulating picasa through the google data api
 * Docs for this can be found here:
 * {@link http://code.google.com/apis/picasaweb/developers_guide_protocol.html}
 *
 * @package    moodlecore
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_picasa {
    const REALM             = 'http://picasaweb.google.com/data/';
    const USER_PREF_NAME    = 'google_authsub_sesskey_picasa';
    const UPLOAD_LOCATION   = 'https://picasaweb.google.com/data/feed/api/user/default/albumid/default';
    const ALBUM_PHOTO_LIST  = 'https://picasaweb.google.com/data/feed/api/user/default/albumid/';
    const PHOTO_SEARCH_URL  = 'https://picasaweb.google.com/data/feed/api/user/default?kind=photo&q=';
    const LIST_ALBUMS_URL   = 'https://picasaweb.google.com/data/feed/api/user/default';
    const MANAGE_URL        = 'http://picasaweb.google.com/';

    private $google_curl = null;

    /**
     * Constructor.
     *
     * @param object A google_auth_request object which can be used to do http requests
     */
    public function __construct($google_curl){
        if(is_a($google_curl, 'google_auth_request')){
            $this->google_curl = $google_curl;
            $this->google_curl->add_persistant_header('GData-Version: 2');
        }else{
            throw new moodle_exception('Google Curl Request object not given');
        }
    }

    public static function get_sesskey($userid){
        return get_user_preferences(google_picasa::USER_PREF_NAME, false, $userid);
    }

    public static function set_sesskey($value, $userid){
        return set_user_preference(google_picasa::USER_PREF_NAME, $value, $userid);
    }

    public static function delete_sesskey($userid){
        return unset_user_preference(google_picasa::USER_PREF_NAME, $userid);
    }

    /**
     * Sends a file object to picasaweb
     *
     * @param object $file File object
     * @return boolean True on success
     */
    public function send_file($file){
        $this->google_curl->setHeader("Content-Length: ". $file->get_filesize());
        $this->google_curl->setHeader("Content-Type: ". $file->get_mimetype());
        $this->google_curl->setHeader("Slug: ". $file->get_filename());

        $this->google_curl->post(google_picasa::UPLOAD_LOCATION, $file->get_content());

        if($this->google_curl->info['http_code'] === 201){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Returns list of photos for file picker.
     * If top level then returns list of albums, otherwise
     * photos within an album.
     *
     * @param string $path The path to files (assumed to be albumid)
     * @return mixed $files A list of files for the file picker
     */
    public function get_file_list($path = ''){
        if(!$path){
            return $this->get_albums();
        }else{
            return $this->get_album_photos($path);
        }
    }

    /**
     * Returns list of photos in album specified
     *
     * @param int $albumid Photo album to list photos from
     * @return mixed $files A list of files for the file picker
     */
    public function get_album_photos($albumid){
        $albumcontent = $this->google_curl->get(google_picasa::ALBUM_PHOTO_LIST.$albumid);

        return $this->get_photo_details($albumcontent);
    }

    /**
     * Does text search on the users photos and returns
     * matches in format for picasa api
     *
     * @param string $query Search terms
     * @return mixed $files A list of files for the file picker
     */
    public function do_photo_search($query){
        $content = $this->google_curl->get(google_picasa::PHOTO_SEARCH_URL.htmlentities($query));

        return $this->get_photo_details($content);
    }

    /**
     * Gets all the users albums and returns them as a list of folders
     * for the file picker
     *
     * @return mixes $files Array in the format get_listing uses for folders
     */
    public function get_albums(){
        $content = $this->google_curl->get(google_picasa::LIST_ALBUMS_URL);
        $xml = new SimpleXMLElement($content);

        $files = array();

        foreach($xml->entry as $album){
            $gphoto = $album->children('http://schemas.google.com/photos/2007');

            $mediainfo = $album->children('http://search.yahoo.com/mrss/');
            //hacky...
            $thumbnailinfo = $mediainfo->group->thumbnail[0]->attributes();

            $files[] = array( 'title' => (string) $gphoto->name,
                'date'  => userdate($gphoto->timestamp),
                'size'  => (int) $gphoto->bytesUsed,
                'path'  => (string) $gphoto->id,
                'thumbnail' => (string) $thumbnailinfo['url'],
                'thumbnail_width' => 160,  // 160 is the native maximum dimension
                'thumbnail_height' => 160,
                'children' => array(),
            );

        }

        return $files;
    }

    /**
     * Recieves XML from a picasa list of photos and returns
     * array in format for file picker.
     *
     * @param string $rawxml XML from picasa api
     * @return mixed $files A list of files for the file picker
     */
    public function get_photo_details($rawxml){

        $xml = new SimpleXMLElement($rawxml);

        $files = array();

        foreach($xml->entry as $photo){
            $gphoto = $photo->children('http://schemas.google.com/photos/2007');

            $mediainfo = $photo->children('http://search.yahoo.com/mrss/');
            $fullinfo = $mediainfo->group->content->attributes();
            //hacky...
            $thumbnailinfo = $mediainfo->group->thumbnail[0]->attributes();

            // Derive the nicest file name we can
            if (!empty($mediainfo->group->description)) {
                $title = shorten_text((string)$mediainfo->group->description, 20, false, '');
                $title = clean_filename($title).'.jpg';
            } else {
                $title = (string)$mediainfo->group->title;
            }

            $files[] = array(
                'title' => $title,
                'date'  => userdate($gphoto->timestamp),
                'size' => (int) $gphoto->size,
                'path' => $gphoto->albumid.'/'.$gphoto->id,
                'thumbnail' => (string) $thumbnailinfo['url'],
                'thumbnail_width' => 72,  // 72 is the native maximum dimension
                'thumbnail_height' => 72,
                'source' => (string) $fullinfo['url'],
                'url' => (string) $fullinfo['url']
            );
        }

        return $files;
    }

}

/**
 * Beginings of an implementation of Clientogin authenticaton for google
 * accounts as documented here:
 * {@link http://code.google.com/apis/accounts/docs/AuthForInstalledApps.html#ClientLogin}
 *
 * With this authentication we have to accept a username and password and to post
 * it to google. Retrieving a token for use afterwards.
 *
 * @package    moodlecore
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_authclient extends google_auth_request {
    const LOGIN_URL = 'https://www.google.com/accounts/ClientLogin';

    public function __construct($sessiontoken = '', $username = '', $password = '', $options = array() ){
        parent::__construct($options);

        if($username and $password){
            $param =  array(
                'accountType'=>'GOOGLE',
                'Email'=>$username,
                'Passwd'=>$password,
                'service'=>'writely'
            );

            $content = $this->post(google_authclient::LOGIN_URL, $param);

            if( preg_match('/auth=(.*)/i', $content, $matches) ){
                $sessiontoken = $matches[1];
            }else{
                throw new moodle_exception('could not upgrade authtoken');
            }

        }

        if($sessiontoken){
            $this->token = $sessiontoken;
        }else{
            throw new moodle_exception('no session token specified');
        }
    }

    public static function get_auth_header_name(){
        return 'GoogleLogin auth=';
    }
}
