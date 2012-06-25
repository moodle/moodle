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
     * Download a file, this function can be overridden by subclass. {@link curl}
     *
     * @param string $url the url of file
     * @param string $filename save location
     * @return string the location of the file
     */
    public function get_file($url, $filename = '') {
        global $USER;
        $cookiename = uniqid('', true) . '.cookie';
        $dir = make_temp_directory('repository/equella/' . $USER->id);
        $cookiepathname = $dir . '/' . $cookiename;
        $path = $this->prepare_file($filename);
        $fp = fopen($path, 'w');
        $c = new curl(array('cookie'=>$cookiepathname));
        $c->download(array(array('url'=>$url, 'file'=>$fp)), array('CURLOPT_FOLLOWLOCATION'=>true));
        // Close file handler.
        fclose($fp);
        // Delete cookie jar.
        unlink($cookiepathname);
        return array('path'=>$path, 'url'=>$url);
    }

    /**
     * Returns information about file in this repository by reference
     * {@link repository::get_file_reference()}
     * {@link repository::get_file()}
     *
     * Returns null if file not found or can not be accessed
     *
     * @param stdClass $reference file reference db record
     * @return null|stdClass containing attribute 'filepath'
     */
    public function get_file_by_reference($reference) {
        $ref = unserialize(base64_decode($reference->reference));
        $url = $this->appendtoken($ref->url);

        if (!$url) {
            // Occurs when the user isn't known..
            return null;
        }

        // We use this cache to get the correct file size.
        $cachedfilepath = cache_file::get($url, array('ttl' => 0));
        if ($cachedfilepath === false) {
            // Cache the file.
            $path = $this->get_file($url);
            $cachedfilepath = cache_file::create_from_file($url, $path['path']);
        }

        if ($cachedfilepath && is_readable($cachedfilepath)) {
            return (object)array('filepath' => $cachedfilepath);
        }
        return null;
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
