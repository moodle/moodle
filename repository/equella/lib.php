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

require_once($CFG->dirroot . '/repository/lib.php');

class repository_equella extends repository {
    /** @var array mimetype filter */
    private $mimetypes = array();

    /**
     * Constructor
     *
     * @param int $repositoryid repository instance id
     * @param int|stdClass a context id or context object
     * @param array $options repository options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);

        if (isset($this->options['mimetypes'])) {
            $mt = $this->options['mimetypes'];
            if (!empty($mt) && !in_array('*', $mt)) {
                $this->mimetypes = array_unique(array_map(array($this, 'toMimeType'), $mt));
            }
        }
    }

    /**
     * Display embedded equella interface
     *
     * @param string $path
     * @param mixed $page
     * @param array
     */
    public function get_listing($path = null, $page = null) {
        global $CFG, $COURSE;
        $callbackurl = $CFG->wwwroot . '/repository/equella/callback.php?repo_id=' . $this->id;

        $mimetypesstr = '';
        $restrict = '';
        if (!empty($this->mimetypes)) {
            $mimetypesstr = '&mimeTypes=' . implode(',', $this->mimetypes);
            // We're restricting to a mime type, so we always restrict to selecting resources only.
            $restrict = '&attachmentonly=true';
        } elseif ($this->get_option('equella_select_restriction') != 'none') {
            // The option value matches the EQUELLA paramter name.
            $restrict = '&' . $this->get_option('equella_select_restriction') . '=true';
        }

        $url = $this->get_option('equella_url')
                . '?method=lms'
                . '&returnurl='.urlencode($callbackurl)
                . '&returnprefix=tle'
                . '&template=standard'
                . '&token='.urlencode($this->getssotoken('write'))
                . '&courseId='.urlencode($COURSE->id)
                . '&action='.urlencode($this->get_option('equella_action'))
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
        return base64_encode($source);
    }

    /**
     * Get file from external repository by reference
     * {@link repository::get_file_reference()}
     * {@link repository::get_file()}
     *
     * @param stdClass $reference file reference db record
     * @return stdClass|null|false
     */
    public function get_file_by_reference($reference) {
        $ref = base64_decode($reference->reference);
        $url = $this->appendtoken($ref);

        // we use this cache to get the correct file size
        $cachedfilepath = cache_file::get($url, array('ttl' => 0));
        if ($cachedfilepath === false) {
            // Cache the file.
            $path = $this->get_file($url);
            $cachedfilepath = cache_file::create_from_file($url, $path['path']);
        }

        $fileinfo = new stdClass;
        $fileinfo->filepath = $cachedfilepath;

        return $fileinfo;
    }

    /**
     * Send equella file to browser
     *
     * @param stored_file $stored_file
     */
    public function send_file($stored_file) {
        $reference = base64_decode($stored_file->get_reference());
        $url = $this->appendtoken($reference);
        header('Location: ' . $url);
    }

    /**
     * Add Instance settings input to Moodle form
     *
     * @param moodleform $mform
     */
    public function instance_config_form($mform) {
        $mform->addElement('text', 'equella_url', get_string('equellaurl', 'repository_equella'));
        $mform->addElement('text', 'equella_action', get_string('equellaaction', 'repository_equella'));
        $mform->setDefault('equella_action', 'searchThin');
        $mform->addElement('text', 'equella_options', get_string('equellaoptions', 'repository_equella'));
        $choices = array(
            'none' => get_string('restrictionnone', 'repository_equella'),
            'itemonly' => get_string('restrictionitemsonly', 'repository_equella'),
            'attachmentonly' => get_string('restrictionattachmentsonly', 'repository_equella'),
        );
        $mform->addElement('select', 'equella_select_restriction', get_string('selectrestriction', 'repository_equella'), $choices);

        $mform->addElement('header', '', get_string('group', 'repository_equella', get_string('group.default', 'repository_equella')));
        $mform->addElement('text', 'equella_shareid', get_string('sharedid', 'repository_equella'));
        $mform->addElement('text', 'equella_sharedsecret', get_string('sharedsecrets', 'repository_equella'));

        foreach( self::get_all_editing_roles() as $role ) {
            $mform->addElement('header', '', get_string('group', 'repository_equella', format_string($role->name)));
            $mform->addElement('text', "equella_{$role->shortname}_shareid", get_string('sharedid', 'repository_equella'));
            $mform->addElement('text', "equella_{$role->shortname}_sharedsecret", get_string('sharedsecrets', 'repository_equella'));
        }
    }

    /**
     * Names of the instance settings
     *
     * @return array
     */
    public static function get_instance_option_names() {
        $rv = array('equella_url', 'equella_action',
            'equella_select_restriction', 'equella_options',
            'equella_shareid', 'equella_sharedsecret'
        );

        foreach( self::get_all_editing_roles() as $role ) {
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
    function getssotoken_raw($username, $shareid, $sharedsecret) {
        $time = mktime() . '000';
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
    function appendtoken($url, $readwrite = null) {
        return $url . (strpos($url, '?') != false ? '&' : '?') . 'token=' . urlencode($this->getssotoken($readwrite));
    }

    /**
     * Build equella url
     *
     * @param string $urlpart
     * @return string
     */
    function full_url($urlpart) {
        return str_ireplace('signon.do', $urlpart, $this->get_option('equella_url'));
    }

    /**
     * Generate equella sso token
     *
     * @param string $readwrite
     * @return string
     */
    function getssotoken($readwrite = 'read') {
        global $USER, $COURSE;

        if( $readwrite == 'write' ) {
            $context_sys = context_system::instance();
            if (!empty($COURSE->category)) {
                $context_cc = context_coursecat::instance($COURSE->category);
            }
            $context_c = context_course::instance($COURSE->id);

            foreach( self::get_all_editing_roles() as $role) {
                //does user have this role?
                $hasroleassignment = false;
                if (user_has_role_assignment($USER->id, $role->id, $context_sys->id)) {
                    $hasroleassignment = true;
                }
                if (!$hasroleassignment && !empty($context_cc) && user_has_role_assignment($USER->id, $role->id, $context_cc->id)) {
                    $hasroleassignment = true;
                }
                if (!$hasroleassignment && user_has_role_assignment($USER->id, $role->id, $context_c->id)) {
                    $hasroleassignment = true;
                }
                if ($hasroleassignment) {
                    //see if the user has a role that is linked to an equella role
                    $shareid = $this->get_option("equella_{$role->shortname}_shareid");
                    if( !empty($shareid) ) {
                        return $this->getssotoken_raw($USER->username, $shareid, $this->get_option("equella_{$role->shortname}_sharedsecret"));
                    }
                }
            }
        }
        //if we are only reading, use the unadorned shareid and secret
        $shareid = $this->get_option('equella_shareid');
        if(!empty($shareid)) {
            return $this->getssotoken_raw($USER->username, $shareid, $this->get_option('equella_sharedsecret'));
        }
    }

    private static function get_all_editing_roles() {
        global $DB;
        $sql = "SELECT r.* FROM {role_capabilities} rc
                     INNER JOIN {role} r
                                ON rc.roleid = r.id
                          WHERE capability = :capability AND permission = 1
                       ORDER BY r.shortname";
        return $DB->get_records_sql($sql, array('capability' => 'moodle/course:manageactivities'));
    }

    /**
     * Convert moodle mimetypes list to equella format
     *
     * @param string $value
     * @return string
     */
    private function toMimeType($value) {
        return mimeinfo('type', $value);
    }
}
