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

use Integrations\PhpSdk\TiiUser;
use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiPseudoUser;
use Integrations\PhpSdk\TiiMembership;
use Integrations\PhpSdk\TurnitinApiException;

/**
 * @package   plagiarism_turnitin
 * @copyright 2018 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

class turnitin_user {
    public $id;
    public $tiiuserid;
    private $role;
    public $firstname;
    public $lastname;
    public $fullname;
    public $email;
    public $username;
    public $useragreementaccepted;
    private $enrol;
    private $workflowcontext;
    private $instructorrubrics;

    public function __construct($id, $role = "Learner", $enrol = true, $workflowcontext = "site", $finduser = true) {
        $this->id = $id;
        $this->set_user_role($role);
        $this->enrol = $enrol;
        $this->workflowcontext = $workflowcontext;

        $this->firstname = "";
        $this->lastname = "";
        $this->fullname = "";
        $this->email = "";
        $this->username = "";

        if ($id != 0) {
            $this->get_moodle_user($this->id);
            if ($finduser === true) {
                $this->get_tii_user_id();
            }
        }
    }

    /**
     * Returns the Moodle User Data object for the specified user
     *
     * @param var $userid The moodle userid
     * @return object A properly built Moodle User Data object with rebuilt email address
     */
    public function get_moodle_user($userid) {
        global $DB;

        $user = $DB->get_record('user', array('id' => $userid));

        if (empty($user->email) || strpos($user->email, '@') === false) {
            $split = explode('.', $user->username);
            array_pop($split);
            $user->email = join('.', $split);
        }

        $this->firstname = stripslashes(str_replace('/', '', $user->firstname));
        $this->lastname = stripslashes(str_replace('/', '', $user->lastname));
        $this->fullname = fullname($user);

        // Set a default for first and last name in the event they are empty.
        $firstname = trim($this->firstname);
        $this->firstname = (empty($firstname)) ? "Moodle" : $firstname;
        $lastname = trim($this->lastname);
        $this->lastname = (empty($lastname)) ? "User ".$this->id : $lastname;

        $this->email = trim(html_entity_decode($user->email));
        $this->username = $user->username;

        $turnitinuser = $DB->get_record('plagiarism_turnitin_users', array('userid' => $this->id));

        $this->instructorrubrics = array();
        if (!empty($turnitinuser->instructor_rubrics)) {
            $this->instructorrubrics = (array)json_decode($turnitinuser->instructor_rubrics);
        }

        return $user;
    }

    /**
     * Get's the domain to use for creating a pseudo email address
     *
     * @return string The pseudo domain
     */
    public static function get_pseudo_domain() {
        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
        $domain = empty($config->plagiarism_turnitin_pseudoemaildomain) ? PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_DOMAIN : $config->plagiarism_turnitin_pseudoemaildomain;

        return $domain;
    }

    /**
     * Convert a regular firstname into the pseudo equivelant for student data privacy purpose
     *
     * @return string A pseudo firstname address
     */
    public function get_pseudo_firstname() {
        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();

        return !empty( $config->plagiarism_turnitin_pseudofirstname ) ? $config->plagiarism_turnitin_pseudofirstname : PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_FIRSTNAME;
    }

    /**
     * Convert a regular lastname into the pseudo equivelant for student data privacy purpose
     *
     * @param string $email The users email address
     * @return string A pseudo lastname address
     */
    public function get_pseudo_lastname() {
        global $DB;
        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
        $userinfo = $DB->get_record('user_info_data', array('userid' => $this->id, 'fieldid' => $config->plagiarism_turnitin_pseudolastname));

        if ((!isset($userinfo->data) || empty($userinfo->data)) && $config->plagiarism_turnitin_pseudolastname != 0 && $config->plagiarism_turnitin_lastnamegen == 1) {
            $uniqueid = strtoupper(strrev(uniqid()));
            $userinfoob = new stdClass();
            $userinfoob->userid = $this->id;
            $userinfoob->fieldid = $config->plagiarism_turnitin_pseudolastname;
            $userinfoob->data = $uniqueid;
            if ($userinfo != false) {
                $userinfoob->id = $userinfo->id;
                $DB->update_record('user_info_data', $userinfoob);
            } else {
                $DB->insert_record('user_info_data', $userinfoob);
            }
        } else if ($config->plagiarism_turnitin_pseudolastname != 0) {
            $uniqueid = $userinfo->data;
        } else {
            $uniqueid = get_string('user');
        }
        return $uniqueid;
    }

    /**
     * A function to return a Turnitin User ID if one exists in plagiarism_turnitin_users
     * or if none found, it will try and find user in Turnitin. If not found it
     * will create them in Turnitin if necessary
     *
     * @param object $user A data object for the user
     * @return var A Turnitin User ID or null
     */
    private function get_tii_user_id() {
        global $DB;
        $tiiuser = $DB->get_record("plagiarism_turnitin_users", array("userid" => $this->id), "turnitin_uid, user_agreement_accepted");
        if (!$tiiuser) {
            $this->tiiuserid = 0;
            $this->useragreementaccepted = 0;
        } else {
            $this->tiiuserid = (isset($tiiuser->turnitin_uid) && $tiiuser->turnitin_uid > 0 ) ? $tiiuser->turnitin_uid : 0;
            $this->useragreementaccepted = $tiiuser->user_agreement_accepted;
        }

        if (empty($this->tiiuserid)) {
            try {
                $this->tiiuserid = $this->find_tii_user_id();
            } catch (TurnitinApiException $e) {
                $this->tiiuserid = $this->create_tii_user();
            }

            if (!empty($this->tiiuserid)) {
                $this->save_tii_user();
            }
        }
    }


    /**
     * Check Turnitin to see if this user has previously registered
     *
     * @return integer Turnitin user id if found otherwise null
     * @throws TurnitinApiException
     */
    private function find_tii_user_id() {
        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
        $tiiuserid = null;

        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        if (!empty($config->plagiarism_turnitin_enablepseudo) && $this->role == "Learner") {
            $user = new TiiPseudoUser($this->get_pseudo_domain());
            $salt = empty($config->plagiarism_turnitin_pseudosalt) ? null : $config->plagiarism_turnitin_pseudosalt;
            $user->setPseudoSalt($salt);
        } else {
            $user = new TiiUser();
        }
        $user->setEmail($this->email);

        try {
            $response = $turnitincall->findUser($user);
            $finduser = $response->getUser();
            $tiiuserid = $finduser->getUserId();
            if (empty($tiiuserid)) {
                $tiiuserid = null;
            }
            return $tiiuserid;
        } catch (TurnitinApiException $e) {
            // In case of a Turnitin exception we rethrow as get_tii_user_id will catch this exception.
            throw $e;
        } catch (Exception $e) {
            $toscreen = ($this->workflowcontext == "cron") ? false : true;
            $turnitincomms->handle_exceptions($e, 'userfinderror', $toscreen);
        }
    }


    /**
     * Create the user on Turnitin
     *
     * @param object $user_details A data object for the user
     * @param var $role user role to create
     * @return var Turnitin user id
     */
    private function create_tii_user() {
        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
        $tiiuserid = null;

        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        // Convert the email, firstname and lastname to pseudos for students if the option is set in config
        // Unless the user is already logged as a tutor then use real details.
        if (!empty($config->plagiarism_turnitin_enablepseudo) && $this->role == "Learner") {
            $user = new TiiPseudoUser($this->get_pseudo_domain());
            $salt = empty($config->plagiarism_turnitin_pseudosalt) ? null : $config->plagiarism_turnitin_pseudosalt;
            $user->setPseudoSalt($salt);
            $user->setFirstName($this->get_pseudo_firstname());
            $user->setLastName($this->get_pseudo_lastname());
        } else {
            $user = new TiiUser();
            $user->setFirstName($this->firstname);
            $user->setLastName($this->lastname);
        }

        $user->setEmail($this->email);
        $user->setDefaultRole($this->role);

        try {
            $response = $turnitincall->createUser($user);
            $newuser = $response->getUser();
            $tiiuserid = $newuser->getUserId();

            plagiarism_turnitin_activitylog("Turnitin User created: ".$this->id." (".$tiiuserid.")", "REQUEST");

            return $tiiuserid;

        } catch (Exception $e) {
            $toscreen = ($this->workflowcontext == "cron") ? false : true;
            $turnitincomms->handle_exceptions($e, 'usercreationerror', $toscreen);
        }
    }


    /**
     * Edit the user's details on Turnitin (only name can be updated)
     *
     * @param object $user_details A data object for the user
     * @param var $role user role to create
     * @return boolean
     */
    public function edit_tii_user() {
        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();

        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        // Only update if pseudo is not enabled.
        if (empty($config->plagiarism_turnitin_enablepseudo)) {
            $user = new TiiUser();
            $user->setFirstName($this->firstname);
            $user->setLastName($this->lastname);

            $user->setUserId($this->tiiuserid);
            $user->setDefaultRole($this->role);

            try {
                $turnitincall->updateUser($user);
            } catch (Exception $e) {
                $toscreen = ($this->workflowcontext == "cron") ? false : true;
                $turnitincomms->handle_exceptions($e, 'userupdateerror', $toscreen);
                return false;
            }
        }
        return true;
    }


    /**
     * Remove Link between moodle user and Turnitin from database
     *
     * @global type $DB
     * @param int $tiidbid The Turnitin database id
     * @return void
     */
    public function unlink_user($tiidbid) {
        global $DB;
        $tiiuser = new stdClass();
        $tiiuser->id = $tiidbid;
        $tiiuser->turnitin_uid = 0;

        // Check if the deleted flag has been set. if yes delete the TII record rather than updating it.
        if ($DB->get_record("user", array('id' => $this->id, 'deleted' => 1), "deleted")) {
            $DB->delete_records('plagiarism_turnitin_users', array('userid' => $this->id));
        } else {
            $DB->update_record('plagiarism_turnitin_users', $tiiuser);
        }

        plagiarism_turnitin_activitylog("User unlinked: ".$this->id." (".$tiidbid.") ", "REQUEST");
    }


    /**
     * Save the link between the moodle user and Turnitin
     *
     * @global type $DB
     * @return void
     */
    private function save_tii_user() {
        global $DB;
        $user = new stdClass();
        $user->userid = $this->id;
        $user->turnitin_uid = $this->tiiuserid;
        $user->turnitin_utp = 1;
        if ($this->role == "Instructor") {
            $user->turnitin_utp = 2;
        }

        if ($turnitinuser = $DB->get_record("plagiarism_turnitin_users", array("userid" => $this->id))) {
            $user->id = $turnitinuser->id;
            $user->turnitin_utp = $turnitinuser->turnitin_utp;
            if ((!$DB->update_record('plagiarism_turnitin_users', $user))) {
                if ($this->workflowcontext != "cron") {
                    plagiarism_turnitin_print_error('userupdateerror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                    exit();
                }
            }
        } else if (!$DB->insert_record('plagiarism_turnitin_users', $user)) {
            if ($this->workflowcontext != "cron") {
                plagiarism_turnitin_print_error('userupdateerror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                exit();
            }
        }
    }


    /**
     * Enrol the user on this course/class in Turnitin
     *
     * @param type $tiicourseid id for the course/class in Turnitin
     * @return boolean
     */
    public function join_user_to_class($tiicourseid) {
        $turnitincomms = new turnitin_comms();

        // We only want an API log entry for this if diagnostic mode is set to Debugging.
        if (empty($config)) {
            $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
        }
        if (isset($config->plagiarism_turnitin_enablediagnostic) && $config->plagiarism_turnitin_enablediagnostic != 2) {
            $turnitincomms->set_diagnostic(0);
        }
        $turnitincall = $turnitincomms->initialise_api();

        $membership = new TiiMembership();
        $membership->setClassId($tiicourseid);
        $membership->setUserId($this->tiiuserid);
        $membership->setRole($this->role);

        try {
            $turnitincall->createMembership($membership);

            plagiarism_turnitin_activitylog("User ".$this->id." (".$this->tiiuserid.") joined to class (".
                $tiicourseid.")", "REQUEST");

            return true;
        } catch (Exception $e) {
            // Ignore exception as we don't need it, this saves time as the alternative
            // is checking all class memberships to see if user is already enrolled.
            $faultcode = $e->getFaultCode();
            if ($faultcode == 'invaliddata') {
                return null;
            } else {
                return false;
            }
        }
    }

    /**
     * Get whether the student has accepted the Turnitin User agreement
     *
     * @return boolean
     */
    public function get_accepted_user_agreement() {
        global $DB;

        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $user = new TiiUser();
        $user->setUserId($this->tiiuserid);

        try {
            $response = $turnitincall->readUser($user);
            $readuser = $response->getUser();

            if ($readuser->getAcceptedUserAgreement()) {
                $turnitinuser = $DB->get_record('plagiarism_turnitin_users', array('userid' => $this->id));

                $tiiuserinfo = new stdClass();
                $tiiuserinfo->id = $turnitinuser->id;
                $tiiuserinfo->user_agreement_accepted = 1;

                $DB->update_record('plagiarism_turnitin_users', $tiiuserinfo);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            // Avoid API calls when running unit tests.
            if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
                return true;
            }

            $this->set_user_values_from_tii();
            $this->get_accepted_user_agreement();
        }
    }

    /**
     * Set the number of user messages and any instructor rubrics from Turnitin
     */
    public function set_user_values_from_tii() {
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $user = new TiiUser();
        $user->setUserId($this->tiiuserid);

        try {
            $response = $turnitincall->readUser($user);
            $readuser = $response->getUser();

            $this->usermessages = $readuser->getUserMessages();
            $this->save_instructor_rubrics($readuser->getInstructorRubrics());

            $tiiuser = array(
                "id" => $readuser->getUserId(),
                "firstname" => $readuser->getFirstName(),
                "lastname" => $readuser->getLastName(),
                "email" => $readuser->getEmail()
            );

            return $tiiuser;

        } catch (Exception $e) {
            try {
                // We need to join the user to the account, we can only do that by adding the user to a class
                // make one and add them, then delete it. Awful workaround but should be rare.
                $class = new TiiClass();
                $uuid = uniqid(microtime() . '-');
                $class->setTitle($uuid);
                $response = $turnitincall->createClass($class);
                $newclass = $response->getClass();
                $tiiclassid = $newclass->getClassId();
                $membership = new TiiMembership();
                $membership->setRole($this->role);
                $membership->setUserId($this->tiiuserid);
                $membership->setClassId($tiiclassid);
                $turnitincall->createMembership($membership);
                $class->setClassId($tiiclassid);
                $turnitincall->deleteClass($class);

                $response = $turnitincall->readUser($user);
                $readuser = $response->getUser();

                $this->usermessages = $readuser->getUserMessages();
                $this->save_instructor_rubrics($readuser->getInstructorRubrics());

            } catch ( Exception $e ) {
                $turnitincomms->handle_exceptions($e, 'tiiusergeterror');
            }
        }
    }

    /**
     * Save the rubrics belonging to the user locally
     *
     * @param array $rubrics
     */
    private function save_instructor_rubrics($rubrics) {
        global $DB;

        $rubricarray = array();
        foreach ($rubrics as $rubric) {
            $rubricarray[$rubric->getRubricId()] = $rubric->getRubricName();
        }

        if ($turnitinuser = $DB->get_record("plagiarism_turnitin_users", array("userid" => $this->id))) {
            $turnitinuser->instructor_rubrics = json_encode($rubricarray);
            $DB->update_record('plagiarism_turnitin_users', $turnitinuser);
        }

        $this->instructorrubrics = $rubricarray;
    }

    /**
     * Get an array of any rubrics the instructor has
     *
     * @return int
     */
    public function get_user_role() {
        return $this->role;
    }

    /**
     * Set the rubrics the instructor has in Turnitin
     *
     * @return int
     */
    private function set_user_role($role) {
        $this->role = $role;
    }

    /**
     * Get an array of any rubrics the instructor has
     *
     * @return int
     */
    public function get_instructor_rubrics() {
        return $this->instructorrubrics;
    }

    /**
     * Get users for unlinking/relinking. Called from ajax.php via turnitin_settings.js.
     *
     * @global type $DB
     * @return array return array of users to display
     */
    public static function plagiarism_turnitin_getusers() {
        global $DB;

        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
        $return = array();
        $idisplaystart = optional_param('iDisplayStart', 0, PARAM_INT);
        $idisplaylength = optional_param('iDisplayLength', 10, PARAM_INT);
        $secho = optional_param('sEcho', 1, PARAM_INT);

        $displaycolumns = array('tu.userid', 'tu.turnitin_uid', 'mu.lastname', 'mu.firstname', 'mu.email');
        $queryparams = array();

        // Add sort to query.
        $isortcol[0] = optional_param('iSortCol_0', null, PARAM_INT);
        $isortingcols = optional_param('iSortingCols', 0, PARAM_INT);
        $queryorder = "";
        if (!is_null( $isortcol[0])) {
            $queryorder = " ORDER BY ";
            $startorder = $queryorder;
            for ($i = 0; $i < intval($isortingcols); $i++) {
                $isortcol[$i] = optional_param('iSortCol_'.$i, null, PARAM_INT);
                $bsortable[$i] = optional_param('bSortable_'.$isortcol[$i], null, PARAM_TEXT);
                $ssortdir[$i] = optional_param('sSortDir_'.$i, null, PARAM_TEXT);
                if ($bsortable[$i] == "true") {
                    $queryorder .= $displaycolumns[$isortcol[$i]]." ".$ssortdir[$i].", ";
                }
            }
            if ($queryorder == $startorder) {
                $queryorder = "";
            } else {
                $queryorder = substr_replace($queryorder, "", -2);
            }
        }

        // Add search to query.
        $ssearch = optional_param('sSearch', '', PARAM_TEXT);
        $querywhere = ' WHERE ( ';
        for ($i = 0; $i < count($displaycolumns); $i++) {
            $bsearchable[$i] = optional_param('bSearchable_'.$i, null, PARAM_TEXT);
            if (!is_null($bsearchable[$i]) && $bsearchable[$i] == "true" && $ssearch != '') {
                $include = true;
                if ($i <= 1) {
                    if (!is_int($ssearch) || is_null($ssearch)) {
                        $include = false;
                    }
                }

                if ($include) {
                    $querywhere .= $DB->sql_like($displaycolumns[$i], ':search_term_'.$i, false)." OR ";
                    $queryparams['search_term_'.$i] = '%'.$ssearch.'%';
                }
            }
        }
        if ( $querywhere == ' WHERE ( ' ) {
            $querywhere = "";
        } else {
            $querywhere = substr_replace( $querywhere, "", -3 );
            $querywhere .= " )";
        }

        $query = "SELECT tu.id AS id, tu.userid AS userid, tu.turnitin_uid AS turnitin_uid, tu.turnitin_utp AS turnitin_utp, ".
            "mu.firstname AS firstname, mu.lastname AS lastname, mu.email AS email ".
            "FROM {plagiarism_turnitin_users} tu ".
            "LEFT JOIN {user} mu ON tu.userid = mu.id ".$querywhere.$queryorder;

        $users = $DB->get_records_sql($query, $queryparams, $idisplaystart, $idisplaylength);
        $totalusers = count($DB->get_records_sql($query, $queryparams));

        $return["aaData"] = array();
        foreach ($users as $user) {
            $checkbox = html_writer::checkbox('userids[]', $user->id, false, '', array("class" => "browser_checkbox"));

            $pseudoemail = "";
            if (!empty($config->plagiarism_turnitin_enablepseudo)) {
                $pseudouser = new TiiPseudoUser(turnitin_user::get_pseudo_domain());
                $pseudouser->setEmail($user->email);
                $pseudoemail = $pseudouser->getEmail();
            }

            $aadata = array($checkbox);
            $user->turnitin_uid = ($user->turnitin_uid == 0) ? '' : $user->turnitin_uid;
            $userdetails = array($user->turnitin_uid, format_string($user->lastname), format_string($user->firstname), $pseudoemail);
            $return["aaData"][] = array_merge($aadata, $userdetails);
        }
        $return["sEcho"] = $secho;
        $return["iTotalRecords"] = count($users);
        $return["iTotalDisplayRecords"] = $totalusers;
        return $return;
    }
}