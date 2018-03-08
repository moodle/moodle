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

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/company.php');
require_once(dirname(__FILE__) . '/iomad.php');

require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/local/email/lib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/group/lib.php');


class company_user {

    /**
     * Creates a user using company user defaults and attaches it to a company
     * User will be emailed a password when the cron job has run
     * @param object $data
     * @return userid
     */
    public static function create( $data ) {
        global $DB, $CFG, $USER;

        if ( $data->companyid ) {
            $company = new company($data->companyid);
            $c = $company->get('shortname');
            $data->company = $c->shortname;
        } else {
            $company = company::by_shortname( $data->company );
        }

        // Deal with manager email CCs.
        $companyrec = $DB->get_record('company', array('id' => $company->id));
        if ($companyrec->managernotify == 0) {
            $headers = null;
        } else {
            $headers = serialize(array("Cc:".$USER->email));
        }

        $defaults = $company->get_user_defaults();
        $user = (object) array_merge( (array) $defaults, (array) $data);

        if (!empty($data->username)) {
            $user->username = $data->username;
        } else {
            $user->username = self::generate_username( $user->email, $data->use_email_as_username );
            $user->username = clean_param($user->username, PARAM_USERNAME);
        }

        // Deal with the company theme.
        $user->theme = $company->get_theme();

        if ($user->sendnewpasswordemails && !$user->preference_auth_forcepasswordchange) {
            throw new Exception(get_string('cannotemailnontemporarypasswords', 'local_iomad'));
        }

        /*
            There are 8 possible combinations of password, sendbyemail and forcepasswordchange
            fields:

            pwd     email yes   force change            -> temporary password
            pwd     email no    force change            -> temporary password
            pwd     email no    dont force change       -> not a temporary password

            no pwd  email yes   force change            -> create password -> store temp
            no pwd  email no    force change            -> create password -> store temp
            no pwd  email no    dont force change       -> create password -> store temp

            These two combinations shouldn't happen (caught by form validation and exception above):
            pwd    email yes dont force change->needs to be stored as temp password -> not secure
            no pwd email yes dont force change->create password->store temp->not secure

            The next set of variables ($sendemail, $passwordentered, $createpassword,
            $forcepasswordchange, $storetemppassword) are used to distinguish between
            the first 6 combinations and to take appropriate action.
        */

        $sendemail = $user->sendnewpasswordemails;
        $passwordentered = !empty($user->newpassword);
        $createpassword = !$passwordentered;
        $forcepasswordchange = $user->preference_auth_forcepasswordchange;
        // Store temp password unless password was entered and it's not going to be send by
        // email nor is it going to be forced to change.
        $storetemppassword = !( $passwordentered && !$sendemail && !$forcepasswordchange );

        if ($passwordentered) {
            $user->password = $user->newpassword;   // Don't hash it, user_create_user will do that.
        }

        $user->confirmed = 1;
        $user->mnethostid = $DB->get_field('mnet_application','id',['name'=>'moodle']);
        $user->maildisplay = 0; // Hide email addresses by default.

        // Create user record and return id.
        $id = user_create_user($user);
        $user->id = $id;

        // Passwords will be created and sent out on cron.
        if ($createpassword) {
            set_user_preference('create_password', 1, $user->id);
            $user->newpassword = generate_password();
            if (!empty($CFG->iomad_email_senderisreal)) {
                EmailTemplate::send('user_create', array('user' => $user, 'sender' => $USER, 'due' => $data->due));
            } else if (is_siteadmin($USER->id)) {
                EmailTemplate::send('user_create', array('user' => $user, 'due' => $data->due));
            } else {
                EmailTemplate::send('user_create',
                                     array('user' => $user,
                                           'due' => $data->due,
                                           'headers' => $headers));
            }
            $sendemail = false;
        }
        if ($forcepasswordchange) {
            set_user_preference('auth_forcepasswordchange', 1, $user->id);
        }

        if ($createpassword) {
            $DB->set_field('user', 'password', hash_internal_user_password($user->newpassword),
                            array('id' => $user->id));
        }

        if ($storetemppassword) {
            // Store password as temporary password, sendemail if necessary.
            self::store_temporary_password($user, $sendemail, $user->newpassword, false, $data->due);
        }

        // Attach user to company.
        // Do we have a department?
        if (empty($data->departmentid)) {
            $departmentinfo = $DB->get_record('department', array('company' => $company->id, 'parent' => 0));
            $data->departmentid = $departmentinfo->id;
        }
        // Deal with unset variable.
        if (empty($data->managertype)) {
            $data->managertype = 0;
        }
        // Create the user association.
        $DB->insert_record('company_users', array('userid' => $user->id,
                                                  'companyid' => $company->id,
                                                  'managertype' => $data->managertype,
                                                  'departmentid' => $data->departmentid));

        if ( isset($data->selectedcourses) ) {
            self::enrol($user, array_keys($data->selectedcourses));
        }

        // Deal with auto enrolments.
        if ($CFG->local_iomad_signup_autoenrol) {
            $company->autoenrol($user);
        }

        return $user->id;
    }

    /**
     * Removes a user's details from all company assignments and marks them as deleted
     * @param int userid
     * @return boolean
     */
    public static function delete( $userid ) {
        global $DB;

        // Get the company details for the user.
        $company = company::get_company_byuserid($userid);
        $context = context_system::instance();

        // Check if the user was a company manager.
        if ($DB->get_records('company_users', array('userid' => $userid, 'managertype' => 1,
                                                    'companyid' => $company->id))) {
            $companymanagerrole = $DB->get_record('role', array('shortname' => 'companymanager'));
            role_unassign($companymanagerrole->id, $userid, $context->id);
        }
        if ($DB->get_records('company_users', array('userid' => $userid, 'managertype' => 2,
                                                    'companyid' => $company->id))) {
            $departmentmanagerrole = $DB->get_record('role', array('shortname' => 'departmentmanager'));
            role_unassign($departmentmanagerrole->id, $userid, $context->id);
        }

        // Remove the user from the company.
        $DB->delete_records('company_users', array('userid' => $userid));

        // Deal with the company theme.
        $DB->set_field('user', 'theme', '', array('id' => $userid));

        // Delete the user.
        $user = $DB->get_record('user', array('id' => $userid));
        delete_user($user);
    }

    /**
     * Suspends a user and keeps the company details as was.
     * @param int userid
     * @return boolean
     */
    public static function suspend( $userid ) {
        global $DB;

        // Get the company details for the user.
        $company = company::get_company_byuserid($userid);
        $context = context_system::instance();

        // Get the users company record.
        $DB->set_field('company_users', 'suspended', 1, array('userid' => $userid,
                                                              'companyid' => $company->id));

        // Clear up any unused licenses.
        if ($userlicenses = $DB->get_records('companylicense_users', array('userid' => $userid,
                                                                       'isusing' => 0))) {
            foreach ($userlicenes as $userlicense) {
                $DB->delete_records('companylicense_users', array('id' => $userlicense->id));
                if ($licenserecord = $DB->get_record('companylicense', array('id' => $userlicense->licenseid))) {
                    $licensecount = $DB->count_records('companylicense_users', array('licenseid' => $licenserecord->id));
                    $licenserecord->used = $licensecount;
                    $DB->update_record('companylicense', $licenserecord);
                }
            }
        }

        // Mark user as suspended.
        $DB->set_field('user', 'suspended', 1, array('id' => $userid));

        // Log the user out.
        \core\session\manager::kill_user_sessions($userid);
    }

    /**
     * Unsuspends a user and keeps the company details as was.
     * @param int userid
     * @return boolean
     */
    public static function unsuspend( $userid ) {
        global $DB;

        // Get the company details for the user.
        $company = company::get_company_byuserid($userid);

        // Get the users company record.
        $DB->set_field('company_users', 'suspended', 0, array('userid' => $userid,
                                                              'companyid' => $company->id));

        // Mark user as suspended.
        $DB->set_field('user', 'suspended', 0, array('id' => $userid));
    }

    /**
     * Enrol a user in courses
     * @param object $user
     * @param array $courseids
     * @return void
     */
    public static function enrol($user, $courseids, $companyid=null, $rid = 0, $groupid = 0) {
        global $DB;
        // This function consists of code copied from uploaduser.php.

        $today = time();
        $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

        $manualcache  = array(); // Cache of used manual enrol plugins in each course.

        // We use only manual enrol plugin here, if it is disabled no enrol is done.
        if (enrol_is_enabled('manual')) {
            $manual = enrol_get_plugin('manual');
        } else {
            $manual = null;
        }

        foreach ($courseids as $courseid) {
            // Check if course is shared.
            if ($courseinfo = $DB->get_record('iomad_courses', array('courseid' => $courseid))) {
                if ($courseinfo->licensed == 1) {
                    continue;
                }
                if ($courseinfo->shared != 0) {
                    $shared = true;
                } else {
                    $shared = false;
                }
            }

            // Do we have course groups?
            if ($DB->get_record('course', array('id' => $courseid, 'groupmode' => 0))) {
                $grouped = false;
            } else {
                $grouped = true;
            }

            if (!isset($manualcache[$courseid])) {
                if ($instances = enrol_get_instances($courseid, false)) {
                    $manualcache[$courseid] = reset($instances);
                } else {
                    $manualcache[$courseid] = false;
                }
            }

            // Set it to the default course roleid.
            if (empty($rid)) {
                $rid = $manualcache[$courseid]->roleid;
            }
            if ($rid) {
                // Find duration.
                if (!empty($manualcache[$courseid]->enrolperiod)) {
                    $timeend = $today + $manualcache[$courseid]->enrolperiod;
                } else {
                    $timeend = 0;
                }
                if (!$DB->get_record('user_enrolments', array('userid' => $user->id, 'enrolid' => $manualcache[$courseid]->id))) {
                    $manual->enrol_user($manualcache[$courseid], $user->id, $rid, $today, $timeend, ENROL_USER_ACTIVE);
                }
                if ($shared || $grouped) {
                    if (!empty($companyid)) {
                        company::add_user_to_shared_course($courseid, $user->id, $companyid, $groupid);
                    }
                }

            }
        }
    }

    /**
     * Unenrol a user from a courses
     * @param object $user
     * @param array $courseids
     * @param int $companyid
     * @return void
     */
    public static function unenrol($user, $courseids, $companyid=null, $all = true) {
        global $DB, $PAGE;

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $isstudent = false;

        foreach ($courseids as $courseid) {
            $roles = get_user_roles(context_course::instance($courseid), $user->id, false);
            foreach ($roles as $role) {
                if (!$all && $role->roleid == $studentrole->id) {
                    $isstudent = true;
                } else {
                    $DB->delete_records('role_assignments', array('id' => $role->id));
                }
            }
            if (!$isstudent) {
                if (!$DB->get_record('iomad_courses', array('courseid' => $courseid, 'shared' => 0))) {
                    $shared = true;
                } else {
                    $shared = false;
                }
                $course = $DB->get_record('course', array('id' => $courseid));
                $courseenrolmentmanager = new course_enrolment_manager($PAGE, $course);

                $ues = $courseenrolmentmanager->get_user_enrolments($user->id);

                foreach ($ues as $ue) {
                    if ( $ue->enrolmentinstance->courseid == $courseid ) {
                        $courseenrolmentmanager->unenrol_user($ue);
                    }
                }
                if ($shared) {
                    if (!empty($companyid)) {
                        company::remove_user_from_shared_course($courseid,
                                                                $user->id,
                                                                $companyid);
                    }
                }
            }
        }
    }

    /**
     * Generate a username based on the email address of the user.
     * @param text $email
     * @return textDear nick@connectedshopping.com,
     */
    public static function generate_username( $email, $useemail=false ) {
        global $DB;

        if (empty($useemail)) {
            // First strip the domain name of the email address.
            $baseusername = preg_replace( "/@.*/", "", $email );
            $baseusername = clean_param($baseusername, PARAM_USERNAME);
            $username = $baseusername;

            // If the username already exists, try adding a random number
            // $variant to protect against infinite loop.
            $variant = $DB->count_records('user');
            while ($variant-- && $DB->record_exists('user', array('username' => $username))) {
                $username = $baseusername . rand(10, 99);
            }

            if ($variant == 0 ) {
                // Trying to make a sensible random username doesn't appear to work,
                // use the entire email address.
                $username = clean_param($email, PARAM_USERNAME);
            }
        } else {
            $username = $email;
        }

        return $username;
    }

    /* Creates a temporary password for the user and keeps track of whether to
     * email it to the user or not
     * @param stdclass $user
     * @param boolean $sendemail
     */
    public static function generate_temporary_password($user, $sendemail = false, $reset = false) {
        global $DB;

        if ( get_user_preferences('create_password', false, $user) || $reset) {
            $newpassword = generate_password();
            $DB->set_field('user', 'password', hash_internal_user_password($newpassword),
                            array('id' => $user->id));
            self::store_temporary_password($user, $sendemail, $newpassword, $reset);
            if ($reset) {
                set_user_preference('auth_forcepasswordchange', 1, $user->id);
            }
        }
    }

    /* Store the temporary password for the user
     * @param stdclass $user
     * @param boolean $sendemail
     * @param text $temppassword
     */
    public static function store_temporary_password($user, $sendemail, $temppassword, $reset = false, $due = 0) {
        global $CFG, $DB, $USER;
        if (empty($due)) {
            $due = time();
        }
        set_user_preference('iomad_temporary', self::rc4encrypt($temppassword), $user);
        unset_user_preference('create_password', $user);
        if ( $sendemail ) {
            if ($reset) {
                // Get the company details.
                $company = company::get_company_byuserid($user->id);
                $companyrec = $DB->get_record('company', array('id' => $company->id));
                if ($companyrec->managernotify == 0) {
                    $headers = null;
                } else {
                    $headers = serialize(array("Cc:".$USER->email));
                }
            } else {
                $company = new stdclass();
                $headers = serialize(array("Cc:".$USER->email));
            }
            $user->newpassword = $temppassword;
            if (!empty($CFG->iomad_email_senderisreal)) {
                if ($reset) {
                    EmailTemplate::send('user_reset', array('user' => $user,
                                                            'company' => $company,
                                                            'sender' => $USER,
                                                            'due' => $due));
                } else {
                    EmailTemplate::send('user_create', array('user' => $user, 'sender' => $USER));
                }
            } else if (is_siteadmin($USER->id)) {
                if ($reset) {
                    EmailTemplate::send('user_reset', array('user' => $user, 'company' => $company));
                } else {
                    EmailTemplate::send('user_create', array('user' => $user, 'due' => $due));
                }
            } else {
                if ($reset) {
                    EmailTemplate::send('user_reset',
                                         array('user' => $user,
                                         'due' => $due,
                                         'company' => $company,
                                         'headers' => $headers));
                } else {
                    EmailTemplate::send('user_create',
                                         array('user' => $user,
                                         'headers' => $headers));
                }
            }
        } else {
            unset_user_preference('iomad_send_password', $user);
        }
    }

    /* Get the user's temporary password
     * @param stdclass $user
     * @return text
     */
    public static function get_temporary_password($user) {
        $pwd = get_user_preferences('iomad_temporary', '', $user);
        if ($pwd != '') {
            $pwd = self::rc4decrypt($pwd);
        }
        return $pwd;
    }

    /* Encrypt a text string
     * @param text $data
     * @return text
     */
    private static function rc4encrypt($data) {
        $password = 'knfgjeingj';
        return endecrypt($password, $data, '');
    }

    /* Decrypt a text string
     * @param text $data
     * @return text
     */
    public static function rc4decrypt($data) {
        $password = 'knfgjeingj';
        return endecrypt($password, $data, 'de');
    }

    /* Check to see if a user can see a company
     * @param stdclass $company
     * @return boolean
     */
    public static function can_see_company( $company ) {
        global $USER;

        $context = context_system::instance();
        if ( !isset($company) ) {
            return true;
        }

        if (!isset($USER->profile["company"]) or empty($USER->profile["company"]) or
            iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
            return true;
        }

        // If companyid was passed in, retrieve the company object.
        if ( is_integer($company) ) {
            $company = new company($company);
        }

        // If company object, retrieve the shortname, otherwise assume the shortname was passed in.
        if ( is_object($company) ) {
            if ( isset($company->shortname) ) {
                $shortname = $company->shortname;
            } else {
                $shortname = $company->get_shortname();
            }
        } else {
            $shortname = $company;
        }

        return $USER->profile["company"] == $shortname;
    }

    /* Check is the user is associated to a company
     *
     * @return boolean
     */
    public static function is_company_user () {
        return iomad::is_company_user();
    }

    /* Get the company id
     *
     * @return int
     */
    public static function companyid() {
        return iomad::companyid();
    }

    /* Get the company shortname
     *
     * @return text
     */
    public static function companyshortname() {
        return iomad::companyshortname();
    }

    /* Regenerate the company profile info
     *
     */
    public static function reload_company() {
        global $USER;
        unset($USER->company);
        self::load_company();
    }

    /* Load the company profile info
     *
     */
    public static function load_company() {
        iomad::load_company();
    }

    // When the shortname of a company is changed,
    // all users that reference the company using the shortname need
    // to have these references updated.
    public static function update_company_reference($oldshortname, $newshortname) {
        global $DB, $USER;

        // No longer required as not using the profile any more.
        /*if (isset($newshortname) && $newshortname != "" && isset($oldshortname)
            && $oldshortname != "") {
            $sql = "UPDATE {user_info_data}
                    SET data = ?
                    WHERE
                        fieldid IN ( SELECT id FROM {user_info_field} WHERE shortname = 'company')
                        AND
                        data = ?";

            $DB->execute($sql, array( $newshortname, $oldshortname ));
            $USER->profile["company"] = $newshortname;
        }*/
    }

    /* Get the department name the user is assigned to.
     * @param int $userid
     * @return text
     */
    public static function get_department_name($userid) {
        global $DB;
        if (!$userdepartment = $DB->get_field_sql("SELECT d.name
                                                   FROM {department} d,
                                                   {company_users} cu
                                                   WHERE
                                                   d.id = cu.departmentid
                                                   AND
                                                   cu.userid = :userid",
                                                   array('userid' => $userid))) {
            $userdepartment = "";
        }
        return $userdepartment;
    }

    /**
     * Assign a user to a course group.
     * @param object $user
     * @param id $courseid
     * @param id $groupid
     * @return void
     */
    public static function assign_group($user, $courseid, $groupid) {
        global $DB;

        // Deal with any licenses.
        if ($licenseinfo = $DB->get_record('companylicense_users', array('licensecourseid' => $courseid, 'userid' => $user->id))) {
            $DB->set_field('companylicense_users', 'groupid', $groupid, array('id' => $licenseinfo->id));
        }

        // Clear down the user from all of the other course groups.
        $currentgroups = groups_get_all_groups($courseid);
        foreach ($currentgroups as $group) {
            groups_remove_member($group->id, $user->id);
        }

        // Add them to the selected group.
        groups_add_member($groupid, $user->id);
    }

    /**
     * Un assign a user from a course group.
     * @param object $user
     * @param id $courseid
     * @param id $groupid
     * @return void
     */
    public static function unassign_group($companyid, $user, $courseid, $groupid) {
        global $DB;

        groups_remove_member($groupid, $user->id);

        // Get the company group.
        $companygroup = company::get_company_group($companyid, $courseid);

        // Add them to the selected group.
        groups_add_member($companygroup->id, $user->id);

        // Deal with any licenses.
        if ($licenseinfo = $DB->get_record('companylicense_users', array('licensecourseid' => $courseid, 'userid' => $user->id))) {
            $DB->set_field('companylicense_users', 'groupid', $companygroup->id, array('id' => $licenseinfo->id));
        }
    }
}

/**
 * User Filter form used on the Iomad pages.
 *
 */
class iomad_user_filter_form extends moodleform {
    protected $companyid;
    protected $useshowall;
    protected $showhistoric;
    protected $addfrom;
    protected $addto;
    protected $addlicensestatus;
    protected $fromname;
    protected $toname;
    protected $useusertype;

    public function definition() {
        global $CFG, $DB, $USER, $SESSION;

        if (!empty($this->_customdata['useshowall'])) {
            $useshowall = true;
        } else {
            $useshowall = false;
        }

        if (!empty($this->_customdata['showhistoric'])) {
            $showhistoric = true;
        } else {
            $showhistoric = false;
        }

        if (!empty($this->_customdata['addfrom'])) {
            $this->addfrom = true;
            $this->fromname = $this->_customdata['addfrom'];
        } else {
            $this->addfrom = false;
        }

        if (!empty($this->_customdata['addto'])) {
            $this->addto = true;
            $this->toname = $this->_customdata['addto'];
        } else {
            $this->addto = false;
        }

        if (!empty($this->_customdata['addfromb'])) {
            $this->addfromb = true;
            $this->fromnameb = $this->_customdata['addfromb'];
        } else {
            $this->addfromb = false;
        }

        if (!empty($this->_customdata['addtob'])) {
            $this->addtob = true;
            $this->tonameb = $this->_customdata['addtob'];
        } else {
            $this->addtob = false;
        }

        if (!empty($this->_customdata['addlicensestatus'])) {
            $addlicensestatus = true;
        } else {
            $addlicensestatus = false;
        }

        if (!empty($this->_customdata['addlicenseusage'])) {
            $addlicenseusage = true;
        } else {
            $addlicenseusage = false;
        }

        if (!empty($this->_customdata['addusertype'])) {
            $useusertype = true;
        } else {
            $useusertype = false;
        }

        $mform =& $this->_form;
        $filtergroup = array();
        $mform->addElement('header', 'usersearchfields', format_string(get_string('usersearchfields', 'local_iomad')));
        $mform->addElement('text', 'firstname', get_string('firstnamefilter', 'local_iomad'), 'size="20"');
        $mform->addElement('text', 'lastname', get_string('lastnamefilter', 'local_iomad'), 'size="20"');
        $mform->addElement('text', 'email', get_string('emailfilter', 'local_iomad'), 'size="20"');
        $mform->addElement('hidden', 'departmentid');
        $mform->addElement('hidden', 'completiontype');
        $mform->addElement('hidden', 'eventid');
        $mform->addElement('hidden', 'courseid');
        $mform->addElement('hidden', 'licenseid');
        $mform->addElement('hidden', 'sort');
        $mform->setType('firstname', PARAM_CLEAN);
        $mform->setType('lastname', PARAM_CLEAN);
        $mform->setType('email', PARAM_EMAIL);
        $mform->setType('departmentid', PARAM_INT);
        $mform->setType('completiontype', PARAM_INT);
        $mform->setType('eventid', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);
        $mform->setType('licenseid', PARAM_INT);
        $mform->setType('sort', PARAM_ALPHA);
        $mform->setExpanded('usersearchfields', false);

        // Get company category.
        if ($category = $DB->get_record_sql('SELECT uic.id, uic.name
                                             FROM {user_info_category} uic, {company} c
                                             WHERE c.id = '.$this->_customdata['companyid'].'
                                             AND c.profileid=uic.id')) {
            // Get fields from company category.
            if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
                // Display the header and the fields.
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id);
                    if ($field->datatype == 'datetime') {
                        $formfield->field->required = false;
                    }
                    $formfield->edit_field($mform);
                }
            }
        }
        if ($useusertype) {
            $usertypearray = array ('a' => get_string('any'),
                                    '0' => get_string('user', 'block_iomad_company_admin'),
                                    '1' => get_string('companymanager', 'block_iomad_company_admin'),
                                    '2' => get_string('departmentmanager', 'block_iomad_company_admin'));
            $mform->addElement('select', 'usertype', get_string('usertype', 'block_iomad_company_admin'), $usertypearray);
        }

        if (iomad::has_capability('block/iomad_company_admin:viewsuspendedusers', context_system::instance())) {
            $mform->addElement('checkbox', 'showsuspended', get_string('show_suspended_users', 'local_iomad'));
        } else {
            $mform->addElement('hidden', 'showsuspended');
        }
        $mform->setType('showsuspended', PARAM_INT);

        if (!$useshowall) {
            $mform->addElement('hidden', 'showall');
            $mform->setType('showall', PARAM_BOOL);
        } else {
            $mform->addElement('checkbox', 'showall', get_string('show_all_company_users', 'block_iomad_company_admin'));
        }

        if (!$showhistoric) {
            $mform->addElement('hidden', 'showhistoric');
            $mform->setType('showhistoric', PARAM_BOOL);
        } else {
            $mform->addElement('checkbox', 'showhistoric', get_string('showhistoricusers', 'block_iomad_company_admin'));
        }

        if ($this->addfrom) {
            $mform->addElement('date_selector', $this->fromname, get_string($this->fromname, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($this->addto) {
            $mform->addElement('date_selector', $this->toname, get_string($this->toname, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($this->addfromb) {
            $mform->addElement('date_selector', $this->fromnameb, get_string($this->fromnameb, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($this->addtob) {
            $mform->addElement('date_selector', $this->tonameb, get_string($this->tonameb, 'block_iomad_company_admin'), array('optional' => 'yes'));
        }

        if ($addlicensestatus) {
            $licensestatusarray = array ('0' => get_string('any'),
                                      '1' => get_string('notinuse', 'block_iomad_company_admin'),
                                      '2' => get_string('inuse', 'block_iomad_company_admin'));
            $mform->addElement('select', 'licensestatus', get_string('licensestatus', 'block_iomad_company_admin'), $licensestatusarray);
        }

        if ($addlicenseusage) {
            $licenseusagearray = array ('0' => get_string('any'),
                                        '1' => get_string('notallocated', 'block_iomad_company_admin'),
                                        '2' => get_string('allocated', 'block_iomad_company_admin'));
            $mform->addElement('select', 'licenseusage', get_string('licenseuseage', 'block_iomad_company_admin'), $licenseusagearray);
        }

        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('userfilter', 'local_iomad'));
        if (!empty($this->_customdata['adddodownload'])) {
            $buttonarray[] = $mform->createElement('submit', 'dodownload', get_string("downloadcsv", 'local_report_completion'));
        }
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
        $mform->closeHeaderBefore('buttonar');
    }

    public function validation($data, $files) {
        
        $errors = array();
        if (!empty($this->fromname) && !empty($this->toname)) {
            if (!empty($data[$this->fromname]) && !empty($data[$this->toname])) {
                if ($data[$this->fromname] > $data[$this->toname]) {
                    $errors[$this->fromname] = get_string('errorinvaliddate', 'calendar');
                }
            }
        }
        if (!empty($this->fromnameb) && !empty($this->tonameb)) {
            if (!empty($data[$this->fromnameb]) && !empty($data[$this->tonameb])) {
                if ($data[$this->fromnameb] > $data[$this->tonameb]) {
                    $errors[$this->fromnameb] = get_string('errorinvaliddate', 'calendar');
                }
            }
        }
        return $errors;
    }
}

/**
 * User Filter form used on the Iomad pages.
 *
 */
class iomad_date_filter_form extends moodleform {
    protected $params = array();

    public function __construct($params) {
        $this->params = $params;
        parent::__construct();
    }

    public function definition() {
        global $CFG, $DB, $USER, $SESSION;

        $mform =& $this->_form;
        foreach ($this->params as $param => $value) {
            if ($param == 'compfrom' || $param == 'compto') {
                continue;
            }
            $mform->addElement('hidden', $param, $value);
            $mform->setType($param, PARAM_CLEAN);
        }
        $mform->addElement('date_selector', 'compfrom', get_string('compfrom', 'local_report_completion'), array('optional' => 'yes'));
        $mform->addElement('date_selector', 'compto', get_string('compto', 'local_report_completion'), array('optional' => 'yes'));
        $mform->setDefault('compfrom', 0);
        $this->add_action_buttons(false, get_string('userfilter', 'local_iomad'));
    }
}
