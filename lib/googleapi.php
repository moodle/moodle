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
                'thumbnail' => (string) $OUTPUT->pix_url(file_extension_icon($title, 32))
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
 * Class for manipulating picasa through the google data api.
 *
 * Docs for this can be found here:
 * {@link http://code.google.com/apis/picasaweb/developers_guide_protocol.html}
 *
 * @package   core
 * @copyright Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class google_picasa {
    /** @var string Realm for authentication */
    const REALM             = 'http://picasaweb.google.com/data/';
    /** @var string Upload url */
    const UPLOAD_LOCATION   = 'https://picasaweb.google.com/data/feed/api/user/default/albumid/default';
    /** @var string photo list url */
    const ALBUM_PHOTO_LIST  = 'https://picasaweb.google.com/data/feed/api/user/default/albumid/';
    /** @var string search url */
    const PHOTO_SEARCH_URL  = 'https://picasaweb.google.com/data/feed/api/user/default?kind=photo&q=';
    /** @var string album list url */
    const LIST_ALBUMS_URL   = 'https://picasaweb.google.com/data/feed/api/user/default';
    /** @var string manage files url */
    const MANAGE_URL        = 'http://picasaweb.google.com/';

    /** @var google_oauth oauth curl class for making authenticated requests */
    private $googleoauth = null;
    /** @var string Last album name retrievied */
    private $lastalbumname = null;

    /**
     * Constructor.
     *
     * @param google_oauth $googleoauth oauth curl class for making authenticated requests
     */
    public function __construct(google_oauth $googleoauth) {
        $this->googleoauth = $googleoauth;
        $this->googleoauth->setHeader('GData-Version: 2');
    }

    /**
     * Sends a file object to picasaweb
     *
     * @param object $file File object
     * @return boolean True on success
     */
    public function send_file($file) {
        $this->googleoauth->setHeader("Content-Length: ". $file->get_filesize());
        $this->googleoauth->setHeader("Content-Type: ". $file->get_mimetype());
        $this->googleoauth->setHeader("Slug: ". $file->get_filename());

        $this->googleoauth->post(self::UPLOAD_LOCATION, $file->get_content());

        if ($this->googleoauth->info['http_code'] === 201) {
            return true;
        } else {
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
    public function get_file_list($path = '') {
        if (!$path) {
            return $this->get_albums();
        } else {
            return $this->get_album_photos($path);
        }
    }

    /**
     * Returns list of photos in album specified
     *
     * @param int $albumid Photo album to list photos from
     * @return mixed $files A list of files for the file picker
     */
    public function get_album_photos($albumid) {
        $albumcontent = $this->googleoauth->get(self::ALBUM_PHOTO_LIST.$albumid);

        return $this->get_photo_details($albumcontent);
    }

    /**
     * Returns the name of the album for which get_photo_details was called last time.
     *
     * @return string
     */
    public function get_last_album_name() {
        return $this->lastalbumname;
    }

    /**
     * Does text search on the users photos and returns
     * matches in format for picasa api
     *
     * @param string $query Search terms
     * @return mixed $files A list of files for the file picker
     */
    public function do_photo_search($query) {
        $content = $this->googleoauth->get(self::PHOTO_SEARCH_URL.htmlentities($query));

        return $this->get_photo_details($content);
    }

    /**
     * Gets all the users albums and returns them as a list of folders
     * for the file picker
     *
     * @return mixes $files Array in the format get_listing uses for folders
     */
    public function get_albums() {
        $files = array();
        $content = $this->googleoauth->get(self::LIST_ALBUMS_URL);

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

        foreach ($xml->entry as $album) {
            $gphoto = $album->children('http://schemas.google.com/photos/2007');

            $mediainfo = $album->children('http://search.yahoo.com/mrss/');
            // Hacky...
            $thumbnailinfo = $mediainfo->group->thumbnail[0]->attributes();

            $files[] = array( 'title' => (string) $album->title,
                'date'  => userdate($gphoto->timestamp),
                'size'  => (int) $gphoto->bytesUsed,
                'path'  => (string) $gphoto->id,
                'thumbnail' => (string) $thumbnailinfo['url'],
                'thumbnail_width' => 160,  // 160 is the native maximum dimension.
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
    public function get_photo_details($rawxml) {
        $files = array();

        try {
            if (strpos($rawxml, '<?xml') !== 0) {
                throw new moodle_exception('invalidxmlresponse');
            }
            $xml = new SimpleXMLElement($rawxml);
        } catch (Exception $e) {
            // An error occured while trying to parse the XML, let's just return nothing. SimpleXML does not
            // return a more specific Exception, that's why the global Exception class is caught here.
            return $files;
        }
        $this->lastalbumname = (string)$xml->title;

        foreach ($xml->entry as $photo) {
            $gphoto = $photo->children('http://schemas.google.com/photos/2007');

            $mediainfo = $photo->children('http://search.yahoo.com/mrss/');
            $fullinfo = $mediainfo->group->content->attributes();
            // Hacky...
            $thumbnailinfo = $mediainfo->group->thumbnail[0]->attributes();

            // Derive the nicest file name we can.
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
                'thumbnail_width' => 72,  // 72 is the native maximum dimension.
                'thumbnail_height' => 72,
                'source' => (string) $fullinfo['url'],
                'url' => (string) $fullinfo['url']
            );
        }

        return $files;
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
}
