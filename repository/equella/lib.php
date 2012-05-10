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
    private $memetypes;

    /**
     * Constructor
     *
     * @param int $repositoryid repository instance id
     * @param int|stdClass a context id or context object
     * @param array $options repository options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);

        if (!empty($this->options['mimetypes'])) {
            $this->mimetypes = $this->options['mimetypes'];
            $this->mimetypes = array_unique(array_map(array($this, 'toMimeType'), $this->options['mimetypes']));
        } else {
            $this->mimetypes = array();
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
        $cancelurl = $callbackurl;
        $mimetypesstr = implode(',', $this->mimetypes); 
        $url = $this->get_option('equella_url')
                . '?method=lms'
                . '&returnurl='.urlencode($callbackurl)
                . '&returnprefix=tle'
                . '&template=standard'
                . '&token='.urlencode($this->getssotoken('read'))
                . '&cancelurl='.urlencode($cancelurl)
                . '&courseId='.urlencode($COURSE->id)
                . '&action='.urlencode($this->get_option('equella_action'))
                . '&forcePost=true'
                . '&cancelDisabled=true'
                . '&attachmentUuidUrls=true'
                . '&options='.urlencode($this->get_option('equella_options') . '&mimeTypes=' . $mimetypesstr);
        $list = array();
        $list['object'] = array();
        $list['object']['type'] = 'text/html';
        $list['object']['src'] = $url;
        return $list;
    }

    /**
     * Supported equella file types
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_EXTERNAL | FILE_REFERENCE;
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
     * Send equella file to browser
     *
     * @param stored_file $stored_file
     */
    public function send_file($stored_file) {
        $resourceurl = base64_decode($stored_file->get_reference());
        $url = $this->appendtoken($resourceurl);
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
        $mform->setDefault('equella_action', 'selectOrAdd');
        $mform->addElement('text', 'equella_admin_username', get_string('adminusername', 'repository_equella'));
        $mform->addElement('text', 'equella_options', get_string('equellaoptions', 'repository_equella'));
        $choices = array(
            'none' => get_string('restrictionnone', 'repository_equella'),
            'itemonly' => get_string('restrictionitemsonly', 'repository_equella'),
            'attachmentonly' => get_string('restrictionattachmentsonly', 'repository_equella'),
        );
        $mform->addElement('select', 'equella_select_restriction', get_string('selectrestriction', 'repository_equella'), $choices);

        $mform->addElement('header', '', get_string('defaultrolesettings', 'repository_equella'));
        $mform->addElement('text', 'equella_shareid', get_string('sharedid', 'repository_equella'));
        $mform->addElement('text', 'equella_sharedsecret', get_string('sharedsecrets', 'repository_equella'));

        $mform->addElement('header', '', get_string('teacherrolesettings', 'repository_equella'));
        $mform->addElement('text', 'equella_editingteacher_shareid', get_string('sharedid', 'repository_equella'));
        $mform->setDefault('equella_editingteacher_shareid', 'editingteacher');
        $mform->addElement('text', 'equella_editingteacher_sharedsecret', get_string('sharedsecrets', 'repository_equella'));

        $mform->addElement('header', '', get_string('managerrolesettings', 'repository_equella'));
        $mform->addElement('text', 'equella_manager_shareid', get_string('sharedid', 'repository_equella'));
        $mform->setDefault('equella_manager_shareid', 'manager');
        $mform->addElement('text', 'equella_manager_sharedsecret', get_string('sharedsecrets', 'repository_equella'));
    }

    /**
     * Names of the instance settings
     *
     * @return array
     */
    public static function get_instance_option_names() {
        return array('equella_url', 'equella_action',
            'equella_select_restruction', 'equella_options',
            'equella_location', 'equella_admin_username',
            'equella_shareid', 'equella_sharedsecret',
            'equella_editingteacher_shareid', 'equella_editingteacher_sharedsecret',
            'equella_manager_shareid', 'equella_manager_sharedsecret',
        );
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
        return $this->append_with_token($url, $this->getssotoken($readwrite));
    }

    /**
     * Append token to equella url
     *
     * @param string $url
     * @param string $token
     * @return string
     */
    function append_with_token($url, $token) {
        return $url . (strpos($url, '?') != false ? '&' : '?') . 'token=' . urlencode($token);
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
     * Generate equella sso token api
     *
     * @return string
     */
    function getssotoken_api() {
        return equella_getssotoken_raw($this->get_option('equella_admin_username'), $this->get_option('equella_shareid'), $this->get_option('equella_sharedsecret'));
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
            $context_sys = get_system_context();
            $context_cc = get_context_instance(CONTEXT_COURSECAT, $COURSE->category);
            $context_c = get_context_instance(CONTEXT_COURSE, $COURSE->id);

            foreach(get_all_editing_roles() as $role) {
                //does user have this role?
                if(user_has_role_assignment($USER->id, $role->id, $context_sys->id) ||
                    user_has_role_assignment($USER->id, $role->id, $context_cc->id) ||
                    user_has_role_assignment($USER->id, $role->id, $context_c->id)) {
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
