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

global $CFG;

require_once('iomad.php');
require_once('company.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/local/email/lib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/lib/formslib.php');


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

        $defaults = $company->get_user_defaults();
        $user = (object) array_merge( (array) $defaults, (array) $data);

        $user->username = self::generate_username( $user->email );
        $user->username = clean_param($user->username, PARAM_USERNAME);
        
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
        $user->mnethostid = 1;
        $user->maildisplay = 0; // Hide email addresses by default.

        // Create user record and return id.
        $id = user_create_user($user);
        $user->id = $id;

        // Passwords will be created and sent out on cron.
        if ($createpassword) {
            set_user_preference('create_password', 1, $user->id);
            $user->newpassword = generate_password();
            if (!empty($CFG->iomad_email_senderisreal)) {
                EmailTemplate::send('user_create', array('user' => $user, 'sender' => $USER));
            } else {
                EmailTemplate::send('user_create',
                                     array('user' => $user,
                                           'headers' => serialize(array("To:". $user->email.", ".$USER->email))));
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
            self::store_temporary_password($user, $sendemail, $user->newpassword);
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

        return $user->id;
    }

    /**
     * Enrol a user in courses
     * @param object $user
     * @param array $courseids
     * @return void
     */
    public static function enrol($user, $courseids, $companyid=null, $rid = 0) {
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
                $timeend = 0;
                $manual->enrol_user($manualcache[$courseid], $user->id, $rid, $today,
                                    $timeend, ENROL_USER_ACTIVE);
                if ($shared) {
                    if (!empty($companyid)) {
                        company::add_user_to_shared_course($courseid, $user->id, $companyid);
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
    public static function unenrol($user, $courseids, $companyid=null) {
        global $DB, $PAGE;

        foreach ($courseids as $courseid) {
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
    }

    /**
     * Generate a username based on the email address of the user.
     * @param text $email
     * @return textDear nick@connectedshopping.com,

Thank you for your request.

     */
    public static function generate_username( $email ) {
        global $DB;

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

        return $username;
    }

    /* Creates a temporary password for the user and keeps track of whether to
     * email it to the user or not
     * @param stdclass $user
     * @param boolean $sendemail
     */
    public static function generate_temporary_password($user, $sendemail = false) {
        global $DB;

        if ( get_user_preferences('create_password', false, $user) ) {
            $newpassword = generate_password();
            $DB->set_field('user', 'password', hash_internal_user_password($newpassword),
                            array('id' => $user->id));
            self::store_temporary_password($user, $sendemail, $newpassword);
        }
    }

    /* Store the temporary password for the user
     * @param stdclass $user
     * @param boolean $sendemail
     * @param text $temppassword
     */
    public static function store_temporary_password($user, $sendemail, $temppassword) {
        global $CFG, $USER;
        set_user_preference('iomad_temporary', self::rc4encrypt($temppassword), $user);
        unset_user_preference('create_password', $user);

        if ( $sendemail ) {
            $user->newpassword = $temppassword;
            if (!empty($CFG->iomad_email_senderisreal)) {
                EmailTemplate::send('user_create', array('user' => $user, 'sender' => $USER));
            } else {
                EmailTemplate::send('user_create',
                                     array('user' => $user,
                                     'headers' => serialize(array("To:". $user->email.", ".$USER->email))));
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
            has_capability('block/iomad_company_admin:company_add', $context)) {
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
}

/**
 * User Filter form used on the Iomad pages.
 *
 */
class iomad_user_filter_form extends moodleform {
    protected $companyid;

    public function definition() {
        global $CFG, $DB, $USER, $SESSION;

        $mform =& $this->_form;
        $filtergroup = array();
        $mform->addElement('header', '', format_string(get_string('usersearchfields', 'local_report_users')));
        $mform->addElement('text', 'firstname', get_string('firstnamefilter', 'local_report_users'), 'size="20"');
        $mform->addElement('text', 'lastname', get_string('lastnamefilter', 'local_report_users'), 'size="20"');
        $mform->addElement('text', 'email', get_string('emailfilter', 'local_report_users'), 'size="20"');
        $mform->addElement('hidden', 'departmentid');
        $mform->addElement('hidden', 'eventid');
        $mform->setType('firstname', PARAM_CLEAN);
        $mform->setType('lastname', PARAM_CLEAN);
        $mform->setType('email', PARAM_EMAIL);
        $mform->setType('departmentid', PARAM_INT);
        $mform->setType('eventid', PARAM_INT);

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

        $this->add_action_buttons(false, get_string('userfilter', 'local_report_users'));
    }
}
