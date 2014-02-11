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
 * Script to let a user create a user for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');

class report_scheduler_form extends company_moodleform {
    protected $title = '';
    protected $description = '';
    protected $context = null;
    protected $courseselector = null;

    public function __construct($actionurl, $companyid) {
        global $CFG;

        $this->selectedcompany = $companyid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);

        parent::moodleform($actionurl);
    }

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header', get_string('companyuser', 'block_iomad_company_admin'));

        $mform->addElement('hidden', 'companyid', $this->selectedcompany);

        /* copied from /user/editlib.php */
        $strrequired = get_string('required');

        $nameordercheck = new stdClass();
        $nameordercheck->firstname = 'a';
        $nameordercheck->lastname  = 'b';
        if (fullname($nameordercheck) == 'b a' ) {  // See MDL-4325.
            $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
            $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        } else {
            $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
            $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
        }

        $mform->addRule('firstname', $strrequired, 'required', null, 'client');
        $mform->setType('firstname', PARAM_NOTAGS);

        $mform->addRule('lastname', $strrequired, 'required', null, 'client');
        $mform->setType('lastname', PARAM_NOTAGS);

        // Do not show email field if change confirmation is pending.
        if (!empty($CFG->emailchangeconfirmation) and !empty($user->preference_newemail)) {
            $notice = get_string('auth_emailchangepending', 'auth_email', $user);
            $notice .= '<br /><a href="edit.php?cancelemailchange=1&amp;id='.$user->id.'">'
                    . get_string('auth_emailchangecancel', 'auth_email') . '</a>';
            $mform->addElement('static', 'emailpending', get_string('email'), $notice);
        } else {
            $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
            $mform->addRule('email', $strrequired, 'required', null, 'client');
        }
        /* /copied from /user/editlib.php */

        $mform->addElement('static', 'blankline', '', '');
        if (!empty($CFG->passwordpolicy){
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('passwordunmask', 'newpassword', get_string('newpassword'), 'size="20"');
        $mform->addHelpButton('newpassword', 'newpassword');
        $mform->setType('newpassword', PARAM_RAW);
        $mform->addElement('static', 'generatepassword', '', get_string('leavepasswordemptytogenerate',
                            'block_iomad_company_admin'));

        $mform->addElement('advcheckbox', 'preference_auth_forcepasswordchange', get_string('forcepasswordchange'));
        $mform->addHelpButton('preference_auth_forcepasswordchange', 'forcepasswordchange');
        $mform->setDefault('preference_auth_forcepasswordchange', 1);

        $mform->addElement('selectyesno', 'sendnewpasswordemails', get_string('sendnewpasswordemails',
                            'block_iomad_company_admin'));
        $mform->setDefault('sendnewpasswordemails', 1);

        $mform->addElement('header', 'courses', get_string('courses', 'block_iomad_company_admin'));
        if (!$this->courseselector = $this->add_course_selector(true, 20, false) ) {
            $mform->addElement('html', get_string('nocourses', 'block_iomad_company_admin'));
        }

        $this->add_action_buttons();
    }

    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->title = '';
            $data->description = '';

            if ($this->title) {
                $data->title = $this->title;
            }

            if ($this->description) {
                $data->description = $this->description;
            }

            if ($this->courseselector) {
                $data->selectedcourses = $this->courseselector->get_selected_courses();
            }
        }
        return $data;
    }

    // Perform some extra moodle validation.
    /* copied from /user/edit_form.php */
    public function validation($usernew, $files) {
        global $CFG, $DB;

        $errors = parent::validation($usernew, $files);

        $usernew = (object)$usernew;

        // Validate email.
        if ($DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id))) {
            $errors['email'] = get_string('emailexists');
        }

        if (!empty($usernew->newpassword)) {
            $errmsg = '';// Prevent eclipse warning.
            if (!check_password_policy($usernew->newpassword, $errmsg)) {
                $errors['newpassword'] = $errmsg;
            }
        }

        // It is insecure to send passwords by email without forcing them to be changed on first login.
        if (!$usernew->preference_auth_forcepasswordchange && $usernew->sendnewpasswordemails) {
            $errors['preference_auth_forcepasswordchange'] = get_string('sendemailsforcepasswordchange',
                    'block_iomad_company_admin', array( 'forcechange' => get_string('forcepasswordchange'),
                    'sendemail' => get_string('sendnewpasswordemails', 'block_iomad_company_admin') ) );
        }

        return $errors;
    }

}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', company_user::companyid(), PARAM_INTEGER);

$context = context_system::instance();
require_login();
$PAGE->set_context($context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('company_list.php', $urlparams);

$blockpage = new blockpage($PAGE, $OUTPUT, 'iomad_report_scheduler', 'block', 'user_create_title');
$blockpage->setup($urlparams);

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
require_capability('block/iomad_company_admin:user_create', $context);

// Set the companyid to bypass the company select form if possible.
if ( $companyid == 0 ) {
    $companyid = company_user::companyid();
}

$companyform = new company_select_form($PAGE->url, $companyid, 'createuserforcompany');
$mform = new report_scheduler_form($PAGE->url, $companyid);

if ( $companyform->is_cancelled() || $mform->is_cancelled() ) {
    if ( $returnurl ) {
        redirect($returnurl);
    } else {
        redirect($companylist);
    }
} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;

    if ( $companyid > 0 ) {
        $data->companyid = $companyid;
    }

    if (!$userid = company_user::create($data)) {
        $this->verbose("Error inserting a new user in the database!");
        if (!$this->get('ignore_errors')) {
            die();
        }
    }

    redirect($PAGE->url, get_string('usercreated', 'block_iomad_company_admin'));
}
echo "URL = " . $PAGE->url . "</br>";
/* echo "<pre>";
print_r($PAGE);
echo "</pre>"; */

// Display the form.
$blockpage->display_header();
$companyform->display();

if ( $companyid > 0 ) {
    $mform->display();
}

echo $OUTPUT->footer();

