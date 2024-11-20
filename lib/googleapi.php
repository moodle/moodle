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
 * Simple implementation of some Google API functions for Moodle.
 *
 * @package   core
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/oauthlib.php');

/**
 * Class for manipulating google documents through the google data api.
 *
 * Docs for this can be found here:
 * {@link http://code.google.com/apis/documents/docs/2.0/developers_guide_protocol.html}
 *
 * @package    core
 * @subpackage lib
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_docs {
    /** @var string Realm for authentication, need both docs and spreadsheet realm */
    const REALM            = 'https://docs.google.com/feeds/ https://spreadsheets.google.com/feeds/ https://docs.googleusercontent.com/';
    /** @var string Document list url */
    const DOCUMENTFEED_URL = 'https://docs.google.com/feeds/default/private/full';
    /** @var string Upload url */
    const UPLOAD_URL       = 'https://docs.google.com/feeds/upload/create-session/default/private/full?convert=false';

    /** @var google_oauth oauth curl class for making authenticated requests */
    private $googleoauth = null;

    /**
     * Constructor.
     *
     * @param google_oauth $googleoauth oauth curl class for making authenticated requests
     */
    public function __construct(google_oauth $googleoauth) {
        $this->googleoauth = $googleoauth;
        $this->reset_curl_state();
    }

    /**
     * Resets state on oauth curl object and set GData protocol
     * version
     */
    private function reset_curl_state() {
        $this->googleoauth->reset_state();
        $this->googleoauth->setHeader('GData-Version: 3.0');
    }

    /**
     * Returns a list of files the user has formated for files api
     *
     * @param string $search A search string to do full text search on the documents
     * @return mixed Array of files formated for fileapoi
     */
    public function get_file_list($search = '') {
        global $CFG, $OUTPUT;
        $url = self::DOCUMENTFEED_URL;

        if ($search) {
            $url.='?q='.urlencode($search);
        }

        $files = array();
        $content = $this->googleoauth->get($url);
        try {
            if (strpos($content, '<?xml') !== 0) {
                throw new moodle_exception('invalidxmlresponse');
            }
            $xml = new SimpleXMLElement($content);
        } catch (Exception $e) {
            // An error occured while trying to parse the XML, let's just return nothing. SimpleXML does not
            // return a more specific Exception, that's why the global Exception class is caught here.
            return $files;
        }
        date_default_timezone_set(core_date::get_user_timezone());
        foreach ($xml->entry as $gdoc) {
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
                case 'file':
                    $title  = (string)$gdoc->title;
                    // Some files don't have a content probably because the download has been restricted.
                    if (isset($gdoc->content)) {
                        $source = (string)$gdoc->content[0]->attributes()->src;
                    }
                    break;
            }

            $files[] =  array( 'title' => $title,
                'url' => "{$gdoc->link[0]->attributes()->href}",
                'source' => $source,
                'date'   => strtotime($gdoc->updated),
                'thumbnail' => (string) $OUTPUT->image_url(file_extension_icon($title))
            );
        }
        core_date::set_default_server_timezone();

        return $files;
    }

    /**
     * Sends a file object to google documents
     *
     * @param object $file File object
     * @return boolean True on success
     */
    public function send_file($file) {
        // First we create the 'resumable upload request'.
        $this->googleoauth->setHeader("Content-Length: 0");
        $this->googleoauth->setHeader("X-Upload-Content-Length: ". $file->get_filesize());
        $this->googleoauth->setHeader("X-Upload-Content-Type: ". $file->get_mimetype());
        $this->googleoauth->setHeader("Slug: ". $file->get_filename());
        $this->googleoauth->post(self::UPLOAD_URL);

        if ($this->googleoauth->info['http_code'] !== 200) {
            throw new moodle_exception('Cantpostupload');
        }

        // Now we http PUT the file in the location returned.
        $location = $this->googleoauth->response['Location'];
        if (empty($location)) {
            throw new moodle_exception('Nouploadlocation');
        }

        // Reset the curl object for actually sending the file.
        $this->reset_curl_state();
        $this->googleoauth->setHeader("Content-Length: ". $file->get_filesize());
        $this->googleoauth->setHeader("Content-Type: ". $file->get_mimetype());

        // We can't get a filepointer, so have to copy the file..
        $tmproot = make_temp_directory('googledocsuploads');
        $tmpfilepath = $tmproot.'/'.$file->get_contenthash();
        $file->copy_content_to($tmpfilepath);

        // HTTP PUT the file.
        $this->googleoauth->put($location, array('file'=>$tmpfilepath));

        // Remove the temporary file we created..
        unlink($tmpfilepath);

        if ($this->googleoauth->info['http_code'] === 201) {
            // Clear headers for further requests.
            $this->reset_curl_state();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Downloads a file using authentication
     *
     * @param string $url url of file
     * @param string $path path to save file to
     * @param int $timeout request timeout, default 0 which means no timeout
     * @return array stucture for repository download_file
     */
    public function download_file($url, $path, $timeout = 0) {
        $result = $this->googleoauth->download_one($url, null, array('filepath' => $path, 'timeout' => $timeout));
        if ($result === true) {
            $info = $this->googleoauth->get_info();
            if (isset($info['http_code']) && $info['http_code'] == 200) {
                return array('path'=>$path, 'url'=>$url);
            } else {
                throw new moodle_exception('cannotdownload', 'repository');
            }
        } else {
            throw new moodle_exception('errorwhiledownload', 'repository', '', $result);
        }
    }
}

/**
 * OAuth 2.0 client for Google Services
 *
 * @package   core
 * @copyright 2012 Dan Poltawski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_oauth extends oauth2_client {
    /**
     * Returns the auth url for OAuth 2.0 request
     * @return string the auth url
     */
    protected function auth_url() {
        return 'https://accounts.google.com/o/oauth2/auth';
    }

    /**
     * Returns the token url for OAuth 2.0 request
     * @return string the auth url
     */
    protected function token_url() {
        return 'https://accounts.google.com/o/oauth2/token';
    }

    /**
     * Resets headers and response for multiple requests
     */
    public function reset_state() {
        $this->header = array();
        $this->response = array();
    }

    /**
     * Make a HTTP request, we override the parents because we do not
     * want to send accept headers (this was a change in the parent class and we want to keep the old behaviour).
     *
     * @param string $url The URL to request
     * @param array $options
     * @param mixed $acceptheader Not used.
     * @return string
     */
    protected function request($url, $options = array(), $acceptheader = 'application/json') {
        return parent::request($url, $options, false);
    }
}
