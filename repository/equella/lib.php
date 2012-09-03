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
 * This plugin is used to access equella repositories.
 *
 * @since 2.3
 * @package    repository_equella
 * @copyright  2012 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');

/**
 * repository_equella class implements equella_client
 *
 * @since 2.3
 * @package    repository_equella
 * @copyright  2012 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_equella extends repository {
    /** @var array mimetype filter */
    private $mimetypes = array();

    /**
     * Constructor
     *
     * @param int $repositoryid repository instance id
     * @param int|stdClass $context a context id or context object
     * @param array $options repository options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);

        if (isset($this->options['mimetypes'])) {
            $mt = $this->options['mimetypes'];
            if (!empty($mt) && is_array($mt) && !in_array('*', $mt)) {
                $this->mimetypes = array_unique(array_map(array($this, 'to_mime_type'), $mt));
            }
        }
    }

    /**
     * Display embedded equella interface
     *
     * @param string $path
     * @param mixed $page
     * @return array
     */
    public function get_listing($path = null, $page = null) {
        global $COURSE;
        $callbackurl = new moodle_url('/repository/equella/callback.php', array('repo_id'=>$this->id));

        $mimetypesstr = '';
        $restrict = '';
        if (!empty($this->mimetypes)) {
            $mimetypesstr = '&mimeTypes=' . implode(',', $this->mimetypes);
            // We're restricting to a mime type, so we always restrict to selecting resources only.
            $restrict = '&attachmentonly=true';
        } else if ($this->get_option('equella_select_restriction') != 'none') {
            // The option value matches the EQUELLA paramter name.
            $restrict = '&' . $this->get_option('equella_select_restriction') . '=true';
        }

        $url = $this->get_option('equella_url')
                . '?method=lms'
                . '&returnurl='.urlencode($callbackurl)
                . '&returnprefix=tle'
                . '&template=standard'
                . '&token='.urlencode($this->getssotoken('write'))
                . '&courseId='.urlencode($COURSE->idnumber)
                . '&courseCode='.urlencode($COURSE->shortname)
                . '&action=searchThin'
                . '&forcePost=true'
                . '&cancelDisabled=true'
                . '&attachmentUuidUrls=true'
                . '&options='.urlencode($this->get_option('equella_options') . $mimetypesstr)
                . $restrict;
        $list = array();
        $list['object'] = array();
        $list['object']['type'] = 'text/html';
        $list['object']['src'] = $url;
        $list['nologin']  = true;
        $list['nosearch'] = true;
        $list['norefresh'] = true;
        return $list;
    }

    /**
     * Supported equella file types
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_REFERENCE;
    }

    /**
     * Prepare file reference information
     *
     * @param string $source
     * @return string file referece
     */
    public function get_file_reference($source) {
        return $source;
    }

    /**
     * Counts the number of failed connections.
     *
     * If we received the connection timeout more than 3 times in a row, we don't attemt to
     * connect to the server any more during this request.
     *
     * This function is used by {@link repository_equella::get_file_by_reference()} that
     * synchronises the file size of referenced files.
     *
     * @param int $errno omit if we just want to know the return value, the last curl_errno otherwise
     * @return bool true if we had less than 3 failed connections, false if no more connections
     * attempts recommended
     */
    private function connection_result($errno = null) {
        static $countfailures = array();
        $sess = sesskey();
        if (!array_key_exists($sess, $countfailures)) {
            $countfailures[$sess] = 0;
        }
        if ($errno !== null) {
            if ($errno == 0) {
                // reset count of failed connections
                $countfailures[$sess] = 0;
            } else if ($errno == 7 /*CURLE_COULDNT_CONNECT*/ || $errno == 9 /*CURLE_REMOTE_ACCESS_DENIED*/) {
                // problems with server
                $countfailures[$sess]++;
            }
        }
        return ($countfailures[$sess] < 3);
    }

    /**
     * Decide whether or not the file should be synced
     *
     * @param stored_file $storedfile
     * @return bool
     */
    public function sync_individual_file(stored_file $storedfile) {
        // if we had several unsuccessfull attempts to connect to server - do not try any more
        return $this->connection_result();
    }


    /**
     * Download a file, this function can be overridden by subclass. {@link curl}
     *
     * @param string $reference the source of the file
     * @param string $filename filename (without path) to save the downloaded file in the
     * temporary directory
     * @return null|array null if download failed or array with elements:
     *   path: internal location of the file
     *   url: URL to the source (from parameters)
     */
    public function get_file($reference, $filename = '') {
        global $USER;
        $ref = @unserialize(base64_decode($reference));
        if (!isset($ref->url) || !($url = $this->appendtoken($ref->url))) {
            // Occurs when the user isn't known..
            return null;
        }
        $path = $this->prepare_file($filename);
        $cookiepathname = $this->prepare_file($USER->id. '_'. uniqid('', true). '.cookie');
        $c = new curl(array('cookie'=>$cookiepathname));
        $result = $c->download_one($url, null, array('filepath' => $path, 'followlocation' => true, 'timeout' => self::GETFILE_TIMEOUT));
        // Delete cookie jar.
        if (file_exists($cookiepathname)) {
            unlink($cookiepathname);
        }
        if ($result !== true) {
            throw new moodle_exception('errorwhiledownload', 'repository', '', $result);
        }
        return array('path'=>$path, 'url'=>$url);
    }

    /**
     * Returns information about file in this repository by reference
     *
     * If the file is an image we download the contents and save it in our filesystem
     * so we can generate thumbnails. Otherwise we just request the file size.
     * Returns null if file not found or can not be accessed
     *
     * @param stdClass $reference file reference db record
     * @return stdClass|null contains one of the following:
     *   - 'filesize' (for non-image files or files we failed to retrieve fully because of timeout)
     *   - 'filepath' (for image files that we retrieived and saved)
     */
    public function get_file_by_reference($reference) {
        global $USER;
        $ref = @unserialize(base64_decode($reference->reference));
        if (!isset($ref->url) || !($url = $this->appendtoken($ref->url))) {
            // Occurs when the user isn't known..
            return null;
        }

        $return = null;
        $cookiepathname = $this->prepare_file($USER->id. '_'. uniqid('', true). '.cookie');
        $c = new curl(array('cookie' => $cookiepathname));
        if (file_extension_in_typegroup($ref->filename, 'web_image')) {
            $path = $this->prepare_file('');
            $result = $c->download_one($url, null, array('filepath' => $path, 'followlocation' => true, 'timeout' => self::SYNCIMAGE_TIMEOUT));
            if ($result === true) {
                $return = (object)array('filepath' => $path);
            }
        } else {
            $result = $c->head($url, array('followlocation' => true, 'timeout' => self::SYNCFILE_TIMEOUT));
        }
        // Delete cookie jar.
        if (file_exists($cookiepathname)) {
            unlink($cookiepathname);
        }

        $this->connection_result($c->get_errno());
        $curlinfo = $c->get_info();
        if ($return === null && isset($curlinfo['http_code']) && $curlinfo['http_code'] == 200
                && array_key_exists('download_content_length', $curlinfo)
                && $curlinfo['download_content_length'] >= 0) {
            // we received a correct header and at least can tell the file size
            $return = (object)array('filesize' => $curlinfo['download_content_length']);
        }
        return $return;
    }

    /**
     * Repository method to serve the referenced file
     *
     * @param stored_file $storedfile the file that contains the reference
     * @param int $lifetime Number of seconds before the file should expire from caches (default 24 hours)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($stored_file, $lifetime=86400 , $filter=0, $forcedownload=false, array $options = null) {
        $reference  = unserialize(base64_decode($stored_file->get_reference()));
        $url = $this->appendtoken($reference->url);
        if ($url) {
            header('Location: ' . $url);
        } else {
            send_file_not_found();
        }
    }

    /**
     * Add Instance settings input to Moodle form
     *
     * @param moodleform $mform
     */
    public static function instance_config_form($mform) {
        $mform->addElement('text', 'equella_url', get_string('equellaurl', 'repository_equella'));
        $mform->setType('equella_url', PARAM_URL);

        $strrequired = get_string('required');
        $mform->addRule('equella_url', $strrequired, 'required', null, 'client');

        $mform->addElement('text', 'equella_options', get_string('equellaoptions', 'repository_equella'));
        $mform->setType('equella_options', PARAM_NOTAGS);

        $choices = array(
            'none' => get_string('restrictionnone', 'repository_equella'),
            'itemonly' => get_string('restrictionitemsonly', 'repository_equella'),
            'attachmentonly' => get_string('restrictionattachmentsonly', 'repository_equella'),
        );
        $mform->addElement('select', 'equella_select_restriction', get_string('selectrestriction', 'repository_equella'), $choices);

        $mform->addElement('header', '',
            get_string('group', 'repository_equella', get_string('groupdefault', 'repository_equella')));
        $mform->addElement('text', 'equella_shareid', get_string('sharedid', 'repository_equella'));
        $mform->setType('equella_shareid', PARAM_RAW);
        $mform->addRule('equella_shareid', $strrequired, 'required', null, 'client');

        $mform->addElement('text', 'equella_sharedsecret', get_string('sharedsecrets', 'repository_equella'));
        $mform->setType('equella_sharedsecret', PARAM_RAW);
        $mform->addRule('equella_sharedsecret', $strrequired, 'required', null, 'client');

        foreach (self::get_all_editing_roles() as $role) {
            $mform->addElement('header', '', get_string('group', 'repository_equella', format_string($role->name)));
            $mform->addElement('text', "equella_{$role->shortname}_shareid", get_string('sharedid', 'repository_equella'));
            $mform->setType("equella_{$role->shortname}_shareid", PARAM_RAW);
            $mform->addElement('text', "equella_{$role->shortname}_sharedsecret",
                get_string('sharedsecrets', 'repository_equella'));
            $mform->setType("equella_{$role->shortname}_sharedsecret", PARAM_RAW);
        }
    }

    /**
     * Names of the instance settings
     *
     * @return array
     */
    public static function get_instance_option_names() {
        $rv = array('equella_url', 'equella_select_restriction', 'equella_options',
            'equella_shareid', 'equella_sharedsecret'
        );

        foreach (self::get_all_editing_roles() as $role) {
            array_push($rv, "equella_{$role->shortname}_shareid");
            array_push($rv, "equella_{$role->shortname}_sharedsecret");
        }

        return $rv;
    }

    /**
     * Generate equella token
     *
     * @param string $username
     * @param string $shareid
     * @param string $sharedsecret
     * @return string
     */
    private static function getssotoken_raw($username, $shareid, $sharedsecret) {
        $time = time() . '000';
        return urlencode($username)
            . ':'
            . $shareid
            . ':'
            . $time
            . ':'
            . base64_encode(pack('H*', md5($username . $shareid . $time . $sharedsecret)));
    }

    /**
     * Append token
     *
     * @param string $url
     * @param $readwrite
     * @return string
     */
    private function appendtoken($url, $readwrite = null) {
        $ssotoken = $this->getssotoken($readwrite);
        if (!$ssotoken) {
            return false;
        }
        return $url . (strpos($url, '?') != false ? '&' : '?') . 'token=' . urlencode($ssotoken);
    }

    /**
     * Generate equella sso token
     *
     * @param string $readwrite
     * @return string
     */
    private function getssotoken($readwrite = 'read') {
        global $USER;

        if (empty($USER->username)) {
            return false;
        }

        if ($readwrite == 'write') {

            foreach (self::get_all_editing_roles() as $role) {
                if (user_has_role_assignment($USER->id, $role->id, $this->context->id)) {
                    // See if the user has a role that is linked to an equella role.
                    $shareid = $this->get_option("equella_{$role->shortname}_shareid");
                    if (!empty($shareid)) {
                        return $this->getssotoken_raw($USER->username, $shareid,
                            $this->get_option("equella_{$role->shortname}_sharedsecret"));
                    }
                }
            }
        }
        // If we are only reading, use the unadorned shareid and secret.
        $shareid = $this->get_option('equella_shareid');
        if (!empty($shareid)) {
            return $this->getssotoken_raw($USER->username, $shareid, $this->get_option('equella_sharedsecret'));
        }
    }

    private static function get_all_editing_roles() {
        return get_roles_with_capability('moodle/course:manageactivities', CAP_ALLOW);
    }

    /**
     * Convert moodle mimetypes list to equella format
     *
     * @param string $value
     * @return string
     */
    private static function to_mime_type($value) {
        return mimeinfo('type', $value);
    }

    /**
     * Return the source information
     *
     * @param stdClass $url
     * @return string|null
     */
    public function get_file_source_info($url) {
        $ref = unserialize(base64_decode($url));
        return 'EQUELLA: ' . $ref->filename;
    }

    /**
     * Return human readable reference information
     * {@link stored_file::get_reference()}
     *
     * @param string $reference
     * @param int $filestatus status of the file, 0 - ok, 666 - source missing
     * @return string
     */
    public function get_reference_details($reference, $filestatus = 0) {
        if (!$filestatus) {
            $ref = unserialize(base64_decode($reference));
            return $this->get_name(). ': '. $ref->filename;
        } else {
            return get_string('lostsource', 'repository', '');
        }
    }
}
