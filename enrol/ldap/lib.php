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
 * LDAP enrolment plugin implementation.
 *
 * This plugin synchronises enrolment and roles with a LDAP server.
 *
 * @package    enrol
 * @subpackage ldap
 * @author     Iñaki Arenaza - based on code by Martin Dougiamas, Martin Langhoff and others
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_ldap_plugin extends enrol_plugin {
    protected $enrol_localcoursefield = 'idnumber';
    protected $enroltype = 'enrol_ldap';
    protected $errorlogtag = '[ENROL LDAP] ';

    /**
     * Constructor for the plugin. In addition to calling the parent
     * constructor, we define and 'fix' some settings depending on the
     * real settings the admin defined.
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->libdir.'/ldaplib.php');

        // Do our own stuff to fix the config (it's easier to do it
        // here than using the admin settings infrastructure). We
        // don't call $this->set_config() for any of the 'fixups'
        // (except the objectclass, as it's critical) because the user
        // didn't specify any values and relied on the default values
        // defined for the user type she chose.
        $this->load_config();

        // Make sure we get sane defaults for critical values.
        $this->config->ldapencoding = $this->get_config('ldapencoding', 'utf-8');
        $this->config->user_type = $this->get_config('user_type', 'default');

        $ldap_usertypes = ldap_supported_usertypes();
        $this->config->user_type_name = $ldap_usertypes[$this->config->user_type];
        unset($ldap_usertypes);

        $default = ldap_getdefaults();
        // Remove the objectclass default, as the values specified there are for
        // users, and we are dealing with groups here.
        unset($default['objectclass']);

        // Use defaults if values not given. Dont use this->get_config()
        // here to be able to check for 0 and false values too.
        foreach ($default as $key => $value) {
            // Watch out - 0, false are correct values too, so we can't use $this->get_config()
            if (!isset($this->config->{$key}) or $this->config->{$key} == '') {
                $this->config->{$key} = $value[$this->config->user_type];
            }
        }

        if (empty($this->config->objectclass)) {
            // Can't send empty filter. Fix it for now and future occasions
            $this->set_config('objectclass', '(objectClass=*)');
        } else if (stripos($this->config->objectclass, 'objectClass=') === 0) {
            // Value is 'objectClass=some-string-here', so just add ()
            // around the value (filter _must_ have them).
            // Fix it for now and future occasions
            $this->set_config('objectclass', '('.$this->config->objectclass.')');
        } else if (stripos($this->config->objectclass, '(') !== 0) {
            // Value is 'some-string-not-starting-with-left-parentheses',
            // which is assumed to be the objectClass matching value.
            // So build a valid filter with it.
            $this->set_config('objectclass', '(objectClass='.$this->config->objectclass.')');
        } else {
            // There is an additional possible value
            // '(some-string-here)', that can be used to specify any
            // valid filter string, to select subsets of users based
            // on any criteria. For example, we could select the users
            // whose objectClass is 'user' and have the
            // 'enabledMoodleUser' attribute, with something like:
            //
            //   (&(objectClass=user)(enabledMoodleUser=1))
            //
            // In this particular case we don't need to do anything,
            // so leave $this->config->objectclass as is.
        }
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function instance_deleteable($instance) {
        if (!enrol_is_enabled('ldap')) {
            return true;
        }

        if (!$this->get_config('ldap_host') or !$this->get_config('objectclass') or !$this->get_config('course_idnumber')) {
            return true;
        }

        // TODO: connect to external system and make sure no users are to be enrolled in this course
        return false;
    }

    /**
     * Forces synchronisation of user enrolments with LDAP server.
     * It creates courses if the plugin is configured to do so.
     *
     * @param object $user user record
     * @return void
     */
    public function sync_user_enrolments($user) {
        global $DB;

        $ldapconnection = $this->ldap_connect();
        if (!$ldapconnection) {
            return;
        }

        if (!is_object($user) or !property_exists($user, 'id')) {
            throw new coding_exception('Invalid $user parameter in sync_user_enrolments()');
        }

        if (!property_exists($user, 'idnumber')) {
            debugging('Invalid $user parameter in sync_user_enrolments(), missing idnumber');
            $user = $DB->get_record('user', array('id'=>$user->id));
        }

        // We may need a lot of memory here
        @set_time_limit(0);
        raise_memory_limit(MEMORY_HUGE);

        // Get enrolments for each type of role.
        $roles = get_all_roles();
        $enrolments = array();
        foreach($roles as $role) {
            // Get external enrolments according to LDAP server
            $enrolments[$role->id]['ext'] = $this->find_ext_enrolments($ldapconnection, $user->idnumber, $role);

            // Get the list of current user enrolments that come from LDAP
            $sql= "SELECT e.courseid, ue.status, e.id as enrolid, c.shortname
                     FROM {user} u
                     JOIN {role_assignments} ra ON (ra.userid = u.id AND ra.component = 'enrol_ldap' AND ra.roleid = :roleid)
                     JOIN {user_enrolments} ue ON (ue.userid = u.id AND ue.enrolid = ra.itemid)
                     JOIN {enrol} e ON (e.id = ue.enrolid)
                     JOIN {course} c ON (c.id = e.courseid)
                    WHERE u.deleted = 0 AND u.id = :userid";
            $params = array ('roleid'=>$role->id, 'userid'=>$user->id);
            $enrolments[$role->id]['current'] = $DB->get_records_sql($sql, $params);
        }

        $ignorehidden = $this->get_config('ignorehiddencourses');
        $courseidnumber = $this->get_config('course_idnumber');
        foreach($roles as $role) {
            foreach ($enrolments[$role->id]['ext'] as $enrol) {
                $course_ext_id = $enrol[$courseidnumber][0];
                if (empty($course_ext_id)) {
                    error_log($this->errorlogtag.get_string('extcourseidinvalid', 'enrol_ldap'));
                    continue; // Next; skip this one!
                }

                // Create the course if required
                $course = $DB->get_record('course', array($this->enrol_localcoursefield=>$course_ext_id));
                if (empty($course)) { // Course doesn't exist
                    if ($this->get_config('autocreate')) { // Autocreate
                        error_log($this->errorlogtag.get_string('createcourseextid', 'enrol_ldap',
                                                                array('courseextid'=>$course_ext_id)));
                        if (!$newcourseid = $this->create_course($enrol)) {
                            continue;
                        }
                        $course = $DB->get_record('course', array('id'=>$newcourseid));
                    } else {
                        error_log($this->errorlogtag.get_string('createnotcourseextid', 'enrol_ldap',
                                                                array('courseextid'=>$course_ext_id)));
                        continue; // Next; skip this one!
                    }
                }

                // Deal with enrolment in the moodle db
                // Add necessary enrol instance if not present yet;
                $sql = "SELECT c.id, c.visible, e.id as enrolid
                          FROM {course} c
                          JOIN {enrol} e ON (e.courseid = c.id AND e.enrol = 'ldap')
                         WHERE c.id = :courseid";
                $params = array('courseid'=>$course->id);
                if (!($course_instance = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE))) {
                    $course_instance = new stdClass();
                    $course_instance->id = $course->id;
                    $course_instance->visible = $course->visible;
                    $course_instance->enrolid = $this->add_instance($course_instance);
                }

                if (!$instance = $DB->get_record('enrol', array('id'=>$course_instance->enrolid))) {
                    continue; // Weird; skip this one.
                }

                if ($ignorehidden && !$course_instance->visible) {
                    continue;
                }

                if (empty($enrolments[$role->id]['current'][$course->id])) {
                    // Enrol the user in the given course, with that role.
                    $this->enrol_user($instance, $user->id, $role->id);
                    // Make sure we set the enrolment status to active. If the user wasn't
                    // previously enrolled to the course, enrol_user() sets it. But if we
                    // configured the plugin to suspend the user enrolments _AND_ remove
                    // the role assignments on external unenrol, then enrol_user() doesn't
                    // set it back to active on external re-enrolment. So set it
                    // unconditionnally to cover both cases.
                    $DB->set_field('user_enrolments', 'status', ENROL_USER_ACTIVE, array('enrolid'=>$instance->id, 'userid'=>$user->id));
                    error_log($this->errorlogtag.get_string('enroluser', 'enrol_ldap',
                                                            array('user_username'=> $user->username,
                                                                  'course_shortname'=>$course->shortname,
                                                                  'course_id'=>$course->id)));
                } else {
                    if ($enrolments[$role->id]['current'][$course->id]->status == ENROL_USER_SUSPENDED) {
                        // Reenable enrolment that was previously disabled. Enrolment refreshed
                        $DB->set_field('user_enrolments', 'status', ENROL_USER_ACTIVE, array('enrolid'=>$instance->id, 'userid'=>$user->id));
                        error_log($this->errorlogtag.get_string('enroluserenable', 'enrol_ldap',
                                                                array('user_username'=> $user->username,
                                                                      'course_shortname'=>$course->shortname,
                                                                      'course_id'=>$course->id)));
                    }
                }

                // Remove this course from the current courses, to be able to detect
                // which current courses should be unenroled from when we finish processing
                // external enrolments.
                unset($enrolments[$role->id]['current'][$course->id]);
            }

            // Deal with unenrolments.
            $transaction = $DB->start_delegated_transaction();
            foreach ($enrolments[$role->id]['current'] as $course) {
                $context = context_course::instance($course->courseid);
                $instance = $DB->get_record('enrol', array('id'=>$course->enrolid));
                switch ($this->get_config('unenrolaction')) {
                    case ENROL_EXT_REMOVED_UNENROL:
                        $this->unenrol_user($instance, $user->id);
                        error_log($this->errorlogtag.get_string('extremovedunenrol', 'enrol_ldap',
                                                                array('user_username'=> $user->username,
                                                                      'course_shortname'=>$course->shortname,
                                                                      'course_id'=>$course->courseid)));
                        break;
                    case ENROL_EXT_REMOVED_KEEP:
                        // Keep - only adding enrolments
                        break;
                    case ENROL_EXT_REMOVED_SUSPEND:
                        if ($course->status != ENROL_USER_SUSPENDED) {
                            $DB->set_field('user_enrolments', 'status', ENROL_USER_SUSPENDED, array('enrolid'=>$instance->id, 'userid'=>$user->id));
                            error_log($this->errorlogtag.get_string('extremovedsuspend', 'enrol_ldap',
                                                                    array('user_username'=> $user->username,
                                                                          'course_shortname'=>$course->shortname,
                                                                          'course_id'=>$course->courseid)));
                        }
                        break;
                    case ENROL_EXT_REMOVED_SUSPENDNOROLES:
                        if ($course->status != ENROL_USER_SUSPENDED) {
                            $DB->set_field('user_enrolments', 'status', ENROL_USER_SUSPENDED, array('enrolid'=>$instance->id, 'userid'=>$user->id));
                        }
                        role_unassign_all(array('contextid'=>$context->id, 'userid'=>$user->id, 'component'=>'enrol_ldap', 'itemid'=>$instance->id));
                        error_log($this->errorlogtag.get_string('extremovedsuspendnoroles', 'enrol_ldap',
                                                                array('user_username'=> $user->username,
                                                                      'course_shortname'=>$course->shortname,
                                                                      'course_id'=>$course->courseid)));
                        break;
                }
            }
            $transaction->allow_commit();
        }

        $this->ldap_close($ldapconnection);
    }

    /**
     * Forces synchronisation of all enrolments with LDAP server.
     * It creates courses if the plugin is configured to do so.
     *
     * @return void
     */
    public function sync_enrolments() {
        global $CFG, $DB;

        $ldapconnection = $this->ldap_connect();
        if (!$ldapconnection) {
            return;
        }

        $ldap_pagedresults = ldap_paged_results_supported($this->get_config('ldap_version'));

        // we may need a lot of memory here
        @set_time_limit(0);
        raise_memory_limit(MEMORY_HUGE);

        // Get enrolments for each type of role.
        $roles = get_all_roles();
        $enrolments = array();
        foreach($roles as $role) {
            // Get all contexts
            $ldap_contexts = explode(';', $this->config->{'contexts_role'.$role->id});

            // Get all the fields we will want for the potential course creation
            // as they are light. Don't get membership -- potentially a lot of data.
            $ldap_fields_wanted = array('dn', $this->config->course_idnumber);
            if (!empty($this->config->course_fullname)) {
                array_push($ldap_fields_wanted, $this->config->course_fullname);
            }
            if (!empty($this->config->course_shortname)) {
                array_push($ldap_fields_wanted, $this->config->course_shortname);
            }
            if (!empty($this->config->course_summary)) {
                array_push($ldap_fields_wanted, $this->config->course_summary);
            }
            array_push($ldap_fields_wanted, $this->config->{'memberattribute_role'.$role->id});

            // Define the search pattern
            $ldap_search_pattern = $this->config->objectclass;

            $ldap_cookie = '';
            foreach ($ldap_contexts as $ldap_context) {
                $ldap_context = trim($ldap_context);
                if (empty($ldap_context)) {
                    continue; // Next;
                }

                $flat_records = array();
                do {
                    if ($ldap_pagedresults) {
                        ldap_control_paged_result($ldapconnection, $this->config->pagesize, true, $ldap_cookie);
                    }

                    if ($this->config->course_search_sub) {
                        // Use ldap_search to find first user from subtree
                        $ldap_result = @ldap_search($ldapconnection,
                                                    $ldap_context,
                                                    $ldap_search_pattern,
                                                    $ldap_fields_wanted);
                    } else {
                        // Search only in this context
                        $ldap_result = @ldap_list($ldapconnection,
                                                  $ldap_context,
                                                  $ldap_search_pattern,
                                                  $ldap_fields_wanted);
                    }
                    if (!$ldap_result) {
                        continue; // Next
                    }

                    if ($ldap_pagedresults) {
                        ldap_control_paged_result_response($ldapconnection, $ldap_result, $ldap_cookie);
                    }

                    // Check and push results
                    $records = ldap_get_entries($ldapconnection, $ldap_result);

                    // LDAP libraries return an odd array, really. fix it:
                    for ($c = 0; $c < $records['count']; $c++) {
                        array_push($flat_records, $records[$c]);
                    }
                    // Free some mem
                    unset($records);
                } while ($ldap_pagedresults && !empty($ldap_cookie));

                // If LDAP paged results were used, the current connection must be completely
                // closed and a new one created, to work without paged results from here on.
                if ($ldap_pagedresults) {
                    $this->ldap_close(true);
                    $ldapconnection = $this->ldap_connect();
                }

                if (count($flat_records)) {
                    $ignorehidden = $this->get_config('ignorehiddencourses');
                    foreach($flat_records as $course) {
                        $course = array_change_key_case($course, CASE_LOWER);
                        $idnumber = $course{$this->config->course_idnumber}[0];
                        print_string('synccourserole', 'enrol_ldap',
                                     array('idnumber'=>$idnumber, 'role_shortname'=>$role->shortname));

                        // Does the course exist in moodle already?
                        $course_obj = $DB->get_record('course', array($this->enrol_localcoursefield=>$idnumber));
                        if (empty($course_obj)) { // Course doesn't exist
                            if ($this->get_config('autocreate')) { // Autocreate
                                error_log($this->errorlogtag.get_string('createcourseextid', 'enrol_ldap',
                                                                        array('courseextid'=>$idnumber)));
                                if (!$newcourseid = $this->create_course($course)) {
                                    continue;
                                }
                                $course_obj = $DB->get_record('course', array('id'=>$newcourseid));
                            } else {
                                error_log($this->errorlogtag.get_string('createnotcourseextid', 'enrol_ldap',
                                                                        array('courseextid'=>$idnumber)));
                                continue; // Next; skip this one!
                            }
                        }

                        // Enrol & unenrol

                        // Pull the ldap membership into a nice array
                        // this is an odd array -- mix of hash and array --
                        $ldapmembers = array();

                        if (array_key_exists('memberattribute_role'.$role->id, $this->config)
                            && !empty($this->config->{'memberattribute_role'.$role->id})
                            && !empty($course[$this->config->{'memberattribute_role'.$role->id}])) { // May have no membership!

                            $ldapmembers = $course[$this->config->{'memberattribute_role'.$role->id}];
                            unset($ldapmembers['count']); // Remove oddity ;)

                            // If we have enabled nested groups, we need to expand
                            // the groups to get the real user list. We need to do
                            // this before dealing with 'memberattribute_isdn'.
                            if ($this->config->nested_groups) {
                                $users = array();
                                foreach ($ldapmembers as $ldapmember) {
                                    $grpusers = $this->ldap_explode_group($ldapconnection,
                                                                          $ldapmember,
                                                                          $this->config->{'memberattribute_role'.$role->id});

                                    $users = array_merge($users, $grpusers);
                                }
                                $ldapmembers = array_unique($users); // There might be duplicates.
                            }

                            // Deal with the case where the member attribute holds distinguished names,
                            // but only if the user attribute is not a distinguished name itself.
                            if ($this->config->memberattribute_isdn
                                && ($this->config->idnumber_attribute !== 'dn')
                                && ($this->config->idnumber_attribute !== 'distinguishedname')) {
                                // We need to retrieve the idnumber for all the users in $ldapmembers,
                                // as the idnumber does not match their dn and we get dn's from membership.
                                $memberidnumbers = array();
                                foreach ($ldapmembers as $ldapmember) {
                                    $result = ldap_read($ldapconnection, $ldapmember, '(objectClass=*)',
                                                        array($this->config->idnumber_attribute));
                                    $entry = ldap_first_entry($ldapconnection, $result);
                                    $values = ldap_get_values($ldapconnection, $entry, $this->config->idnumber_attribute);
                                    array_push($memberidnumbers, $values[0]);
                                }

                                $ldapmembers = $memberidnumbers;
                            }
                        }

                        // Prune old ldap enrolments
                        // hopefully they'll fit in the max buffer size for the RDBMS
                        $sql= "SELECT u.id as userid, u.username, ue.status,
                                      ra.contextid, ra.itemid as instanceid
                                 FROM {user} u
                                 JOIN {role_assignments} ra ON (ra.userid = u.id AND ra.component = 'enrol_ldap' AND ra.roleid = :roleid)
                                 JOIN {user_enrolments} ue ON (ue.userid = u.id AND ue.enrolid = ra.itemid)
                                 JOIN {enrol} e ON (e.id = ue.enrolid)
                                WHERE u.deleted = 0 AND e.courseid = :courseid ";
                        $params = array('roleid'=>$role->id, 'courseid'=>$course_obj->id);
                        $context = context_course::instance($course_obj->id);
                        if (!empty($ldapmembers)) {
                            list($ldapml, $params2) = $DB->get_in_or_equal($ldapmembers, SQL_PARAMS_NAMED, 'm', false);
                            $sql .= "AND u.idnumber $ldapml";
                            $params = array_merge($params, $params2);
                            unset($params2);
                        } else {
                            $shortname = format_string($course_obj->shortname, true, array('context' => $context));
                            print_string('emptyenrolment', 'enrol_ldap',
                                         array('role_shortname'=> $role->shortname,
                                               'course_shortname' => $shortname));
                        }
                        $todelete = $DB->get_records_sql($sql, $params);

                        if (!empty($todelete)) {
                            $transaction = $DB->start_delegated_transaction();
                            foreach ($todelete as $row) {
                                $instance = $DB->get_record('enrol', array('id'=>$row->instanceid));
                                switch ($this->get_config('unenrolaction')) {
                                case ENROL_EXT_REMOVED_UNENROL:
                                    $this->unenrol_user($instance, $row->userid);
                                    error_log($this->errorlogtag.get_string('extremovedunenrol', 'enrol_ldap',
                                                                            array('user_username'=> $row->username,
                                                                                  'course_shortname'=>$course_obj->shortname,
                                                                                  'course_id'=>$course_obj->id)));
                                    break;
                                case ENROL_EXT_REMOVED_KEEP:
                                    // Keep - only adding enrolments
                                    break;
                                case ENROL_EXT_REMOVED_SUSPEND:
                                    if ($row->status != ENROL_USER_SUSPENDED) {
                                        $DB->set_field('user_enrolments', 'status', ENROL_USER_SUSPENDED, array('enrolid'=>$instance->id, 'userid'=>$row->userid));
                                        error_log($this->errorlogtag.get_string('extremovedsuspend', 'enrol_ldap',
                                                                                array('user_username'=> $row->username,
                                                                                      'course_shortname'=>$course_obj->shortname,
                                                                                      'course_id'=>$course_obj->id)));
                                    }
                                    break;
                                case ENROL_EXT_REMOVED_SUSPENDNOROLES:
                                    if ($row->status != ENROL_USER_SUSPENDED) {
                                        $DB->set_field('user_enrolments', 'status', ENROL_USER_SUSPENDED, array('enrolid'=>$instance->id, 'userid'=>$row->userid));
                                    }
                                    role_unassign_all(array('contextid'=>$row->contextid, 'userid'=>$row->userid, 'component'=>'enrol_ldap', 'itemid'=>$instance->id));
                                    error_log($this->errorlogtag.get_string('extremovedsuspendnoroles', 'enrol_ldap',
                                                                            array('user_username'=> $row->username,
                                                                                  'course_shortname'=>$course_obj->shortname,
                                                                                  'course_id'=>$course_obj->id)));
                                    break;
                                }
                            }
                            $transaction->allow_commit();
                        }

                        // Insert current enrolments
                        // bad we can't do INSERT IGNORE with postgres...

                        // Add necessary enrol instance if not present yet;
                        $sql = "SELECT c.id, c.visible, e.id as enrolid
                                  FROM {course} c
                                  JOIN {enrol} e ON (e.courseid = c.id AND e.enrol = 'ldap')
                                 WHERE c.id = :courseid";
                        $params = array('courseid'=>$course_obj->id);
                        if (!($course_instance = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE))) {
                            $course_instance = new stdClass();
                            $course_instance->id = $course_obj->id;
                            $course_instance->visible = $course_obj->visible;
                            $course_instance->enrolid = $this->add_instance($course_instance);
                        }

                        if (!$instance = $DB->get_record('enrol', array('id'=>$course_instance->enrolid))) {
                            continue; // Weird; skip this one.
                        }

                        if ($ignorehidden && !$course_instance->visible) {
                            continue;
                        }

                        $transaction = $DB->start_delegated_transaction();
                        foreach ($ldapmembers as $ldapmember) {
                            $sql = 'SELECT id,username,1 FROM {user} WHERE idnumber = ? AND deleted = 0';
                            $member = $DB->get_record_sql($sql, array($ldapmember));
                            if(empty($member) || empty($member->id)){
                                print_string ('couldnotfinduser', 'enrol_ldap', $ldapmember);
                                continue;
                            }

                            $sql= "SELECT ue.status
                                     FROM {user_enrolments} ue
                                     JOIN {enrol} e ON (e.id = ue.enrolid)
                                     JOIN {role_assignments} ra ON (ra.itemid = e.id AND ra.component = 'enrol_ldap')
                                    WHERE e.courseid = :courseid AND ue.userid = :userid";
                            $params = array('courseid'=>$course_obj->id, 'userid'=>$member->id);
                            $userenrolment = $DB->get_record_sql($sql, $params);

                            if(empty($userenrolment)) {
                                $this->enrol_user($instance, $member->id, $role->id);
                                // Make sure we set the enrolment status to active. If the user wasn't
                                // previously enrolled to the course, enrol_user() sets it. But if we
                                // configured the plugin to suspend the user enrolments _AND_ remove
                                // the role assignments on external unenrol, then enrol_user() doesn't
                                // set it back to active on external re-enrolment. So set it
                                // unconditionnally to cover both cases.
                                $DB->set_field('user_enrolments', 'status', ENROL_USER_ACTIVE, array('enrolid'=>$instance->id, 'userid'=>$member->id));
                                error_log($this->errorlogtag.get_string('enroluser', 'enrol_ldap',
                                                                        array('user_username'=> $member->username,
                                                                              'course_shortname'=>$course_obj->shortname,
                                                                              'course_id'=>$course_obj->id)));

                            } else {
                                if ($userenrolment->status == ENROL_USER_SUSPENDED) {
                                    // Reenable enrolment that was previously disabled. Enrolment refreshed
                                    $DB->set_field('user_enrolments', 'status', ENROL_USER_ACTIVE, array('enrolid'=>$instance->id, 'userid'=>$member->id));
                                    error_log($this->errorlogtag.get_string('enroluserenable', 'enrol_ldap',
                                                                            array('user_username'=> $member->username,
                                                                                  'course_shortname'=>$course_obj->shortname,
                                                                                  'course_id'=>$course_obj->id)));
                                }
                            }
                        }
                        $transaction->allow_commit();
                    }
                }
            }
        }
        @$this->ldap_close();
    }

    /**
     * Connect to the LDAP server, using the plugin configured
     * settings. It's actually a wrapper around ldap_connect_moodle()
     *
     * @return mixed A valid LDAP connection or false.
     */
    protected function ldap_connect() {
        global $CFG;
        require_once($CFG->libdir.'/ldaplib.php');

        // Cache ldap connections. They are expensive to set up
        // and can drain the TCP/IP ressources on the server if we
        // are syncing a lot of users (as we try to open a new connection
        // to get the user details). This is the least invasive way
        // to reuse existing connections without greater code surgery.
        if(!empty($this->ldapconnection)) {
            $this->ldapconns++;
            return $this->ldapconnection;
        }

        if ($ldapconnection = ldap_connect_moodle($this->get_config('host_url'), $this->get_config('ldap_version'),
                                                  $this->get_config('user_type'), $this->get_config('bind_dn'),
                                                  $this->get_config('bind_pw'), $this->get_config('opt_deref'),
                                                  $debuginfo)) {
            $this->ldapconns = 1;
            $this->ldapconnection = $ldapconnection;
            return $ldapconnection;
        }

        // Log the problem, but don't show it to the user. She doesn't
        // even have a chance to see it, as we redirect instantly to
        // the user/front page.
        error_log($this->errorlogtag.$debuginfo);

        return false;
    }

    /**
     * Disconnects from a LDAP server
     *
     */
    protected function ldap_close() {
        $this->ldapconns--;
        if($this->ldapconns == 0) {
            @ldap_close($this->ldapconnection);
            unset($this->ldapconnection);
        }
    }

    /**
     * Return multidimensional array with details of user courses (at
     * least dn and idnumber).
     *
     * @param resource $ldapconnection a valid LDAP connection.
     * @param string $memberuid user idnumber (without magic quotes).
     * @param object role is a record from the mdl_role table.
     * @return array
     */
    protected function find_ext_enrolments (&$ldapconnection, $memberuid, $role) {
        global $CFG;
        require_once($CFG->libdir.'/ldaplib.php');

        if (empty($memberuid)) {
            // No "idnumber" stored for this user, so no LDAP enrolments
            return array();
        }

        $ldap_contexts = trim($this->get_config('contexts_role'.$role->id));
        if (empty($ldap_contexts)) {
            // No role contexts, so no LDAP enrolments
            return array();
        }

        $extmemberuid = textlib::convert($memberuid, 'utf-8', $this->get_config('ldapencoding'));

        if($this->get_config('memberattribute_isdn')) {
            if (!($extmemberuid = $this->ldap_find_userdn ($ldapconnection, $extmemberuid))) {
                return array();
            }
        }

        $ldap_search_pattern = '';
        if($this->get_config('nested_groups')) {
            $usergroups = $this->ldap_find_user_groups($ldapconnection, $extmemberuid);
            if(count($usergroups) > 0) {
                foreach ($usergroups as $group) {
                    $ldap_search_pattern .= '('.$this->get_config('memberattribute_role'.$role->id).'='.$group.')';
                }
            }
        }

        // Default return value
        $courses = array();

        // Get all the fields we will want for the potential course creation
        // as they are light. don't get membership -- potentially a lot of data.
        $ldap_fields_wanted = array('dn', $this->get_config('course_idnumber'));
        $fullname  = $this->get_config('course_fullname');
        $shortname = $this->get_config('course_shortname');
        $summary   = $this->get_config('course_summary');
        if (isset($fullname)) {
            array_push($ldap_fields_wanted, $fullname);
        }
        if (isset($shortname)) {
            array_push($ldap_fields_wanted, $shortname);
        }
        if (isset($summary)) {
            array_push($ldap_fields_wanted, $summary);
        }

        // Define the search pattern
        if (empty($ldap_search_pattern)) {
            $ldap_search_pattern = '('.$this->get_config('memberattribute_role'.$role->id).'='.ldap_filter_addslashes($extmemberuid).')';
        } else {
            $ldap_search_pattern = '(|' . $ldap_search_pattern .
                                       '('.$this->get_config('memberattribute_role'.$role->id).'='.ldap_filter_addslashes($extmemberuid).')' .
                                   ')';
        }
        $ldap_search_pattern='(&'.$this->get_config('objectclass').$ldap_search_pattern.')';

        // Get all contexts and look for first matching user
        $ldap_contexts = explode(';', $ldap_contexts);
        $ldap_pagedresults = ldap_paged_results_supported($this->get_config('ldap_version'));
        foreach ($ldap_contexts as $context) {
            $context = trim($context);
            if (empty($context)) {
                continue;
            }

            $ldap_cookie = '';
            $flat_records = array();
            do {
                if ($ldap_pagedresults) {
                    ldap_control_paged_result($ldapconnection, $this->config->pagesize, true, $ldap_cookie);
                }

                if ($this->get_config('course_search_sub')) {
                    // Use ldap_search to find first user from subtree
                    $ldap_result = @ldap_search($ldapconnection,
                                                $context,
                                                $ldap_search_pattern,
                                                $ldap_fields_wanted);
                } else {
                    // Search only in this context
                    $ldap_result = @ldap_list($ldapconnection,
                                              $context,
                                              $ldap_search_pattern,
                                              $ldap_fields_wanted);
                }

                if (!$ldap_result) {
                    continue;
                }

                if ($ldap_pagedresults) {
                    ldap_control_paged_result_response($ldapconnection, $ldap_result, $ldap_cookie);
                }

                // Check and push results. ldap_get_entries() already
                // lowercases the attribute index, so there's no need to
                // use array_change_key_case() later.
                $records = ldap_get_entries($ldapconnection, $ldap_result);

                // LDAP libraries return an odd array, really. Fix it.
                for ($c = 0; $c < $records['count']; $c++) {
                    array_push($flat_records, $records[$c]);
                }
                // Free some mem
                unset($records);
            } while ($ldap_pagedresults && !empty($ldap_cookie));

            // If LDAP paged results were used, the current connection must be completely
            // closed and a new one created, to work without paged results from here on.
            if ($ldap_pagedresults) {
                $this->ldap_close(true);
                $ldapconnection = $this->ldap_connect();
            }

            if (count($flat_records)) {
                $courses = array_merge($courses, $flat_records);
            }
        }

        return $courses;
    }

    /**
     * Search specified contexts for the specified userid and return the
     * user dn like: cn=username,ou=suborg,o=org. It's actually a wrapper
     * around ldap_find_userdn().
     *
     * @param resource $ldapconnection a valid LDAP connection
     * @param string $userid the userid to search for (in external LDAP encoding, no magic quotes).
     * @return mixed the user dn or false
     */
    protected function ldap_find_userdn($ldapconnection, $userid) {
        global $CFG;
        require_once($CFG->libdir.'/ldaplib.php');

        $ldap_contexts = explode(';', $this->get_config('user_contexts'));
        $ldap_defaults = ldap_getdefaults();

        return ldap_find_userdn($ldapconnection, $userid, $ldap_contexts,
                                '(objectClass='.$ldap_defaults['objectclass'][$this->get_config('user_type')].')',
                                $this->get_config('idnumber_attribute'), $this->get_config('user_search_sub'));
    }

    /**
     * Find the groups a given distinguished name belongs to, both directly
     * and indirectly via nested groups membership.
     *
     * @param resource $ldapconnection a valid LDAP connection
     * @param string $memberdn distinguished name to search
     * @return array with member groups' distinguished names (can be emtpy)
     */
    protected function ldap_find_user_groups($ldapconnection, $memberdn) {
        $groups = array();

        $this->ldap_find_user_groups_recursively($ldapconnection, $memberdn, $groups);
        return $groups;
    }

    /**
     * Recursively process the groups the given member distinguished name
     * belongs to, adding them to the already processed groups array.
     *
     * @param resource $ldapconnection
     * @param string $memberdn distinguished name to search
     * @param array reference &$membergroups array with already found
     *                        groups, where we'll put the newly found
     *                        groups.
     */
    protected function ldap_find_user_groups_recursively($ldapconnection, $memberdn, &$membergroups) {
        $result = @ldap_read($ldapconnection, $memberdn, '(objectClass=*)', array($this->get_config('group_memberofattribute')));
        if (!$result) {
            return;
        }

        if ($entry = ldap_first_entry($ldapconnection, $result)) {
            do {
                $attributes = ldap_get_attributes($ldapconnection, $entry);
                for ($j = 0; $j < $attributes['count']; $j++) {
                    $groups = ldap_get_values_len($ldapconnection, $entry, $attributes[$j]);
                    foreach ($groups as $key => $group) {
                        if ($key === 'count') {  // Skip the entries count
                            continue;
                        }
                        if(!in_array($group, $membergroups)) {
                            // Only push and recurse if we haven't 'seen' this group before
                            // to prevent loops (MS Active Directory allows them!!).
                            array_push($membergroups, $group);
                            $this->ldap_find_user_groups_recursively($ldapconnection, $group, $membergroups);
                        }
                    }
                }
            }
            while ($entry = ldap_next_entry($ldapconnection, $entry));
        }
    }

    /**
     * Given a group name (either a RDN or a DN), get the list of users
     * belonging to that group. If the group has nested groups, expand all
     * the intermediate groups and return the full list of users that
     * directly or indirectly belong to the group.
     *
     * @param resource $ldapconnection a valid LDAP connection
     * @param string $group the group name to search
     * @param string $memberattibute the attribute that holds the members of the group
     * @return array the list of users belonging to the group. If $group
     *         is not actually a group, returns array($group).
     */
    protected function ldap_explode_group($ldapconnection, $group, $memberattribute) {
        switch ($this->get_config('user_type')) {
            case 'ad':
                // $group is already the distinguished name to search.
                $dn = $group;

                $result = ldap_read($ldapconnection, $dn, '(objectClass=*)', array('objectClass'));
                $entry = ldap_first_entry($ldapconnection, $result);
                $objectclass = ldap_get_values($ldapconnection, $entry, 'objectClass');

                if (!in_array('group', $objectclass)) {
                    // Not a group, so return immediately.
                    return array($group);
                }

                $result = ldap_read($ldapconnection, $dn, '(objectClass=*)', array($memberattribute));
                $entry = ldap_first_entry($ldapconnection, $result);
                $members = @ldap_get_values($ldapconnection, $entry, $memberattribute); // Can be empty and throws a warning
                if ($members['count'] == 0) {
                    // There are no members in this group, return nothing.
                    return array();
                }
                unset($members['count']);

                $users = array();
                foreach ($members as $member) {
                    $group_members = $this->ldap_explode_group($ldapconnection, $member, $memberattribute);
                    $users = array_merge($users, $group_members);
                }

                return ($users);
                break;
            default:
                error_log($this->errorlogtag.get_string('explodegroupusertypenotsupported', 'enrol_ldap',
                                                        $this->get_config('user_type_name')));

                return array($group);
        }
    }

    /**
     * Will create the moodle course from the template
     * course_ext is an array as obtained from ldap -- flattened somewhat
     * NOTE: if you pass true for $skip_fix_course_sortorder
     * you will want to call fix_course_sortorder() after your are done
     * with course creation.
     *
     * @param array $course_ext
     * @param boolean $skip_fix_course_sortorder
     * @return mixed false on error, id for the newly created course otherwise.
     */
    function create_course($course_ext, $skip_fix_course_sortorder=false) {
        global $CFG, $DB;

        require_once("$CFG->dirroot/course/lib.php");

        // Override defaults with template course
        $template = false;
        if ($this->get_config('template')) {
            if ($template = $DB->get_record('course', array('shortname'=>$this->get_config('template')))) {
                $template = fullclone(course_get_format($template)->get_course());
                unset($template->id); // So we are clear to reinsert the record
                unset($template->fullname);
                unset($template->shortname);
                unset($template->idnumber);
            }
        }
        if (!$template) {
            $courseconfig = get_config('moodlecourse');
            $template = new stdClass();
            $template->summary        = '';
            $template->summaryformat  = FORMAT_HTML;
            $template->format         = $courseconfig->format;
            $template->newsitems      = $courseconfig->newsitems;
            $template->showgrades     = $courseconfig->showgrades;
            $template->showreports    = $courseconfig->showreports;
            $template->maxbytes       = $courseconfig->maxbytes;
            $template->groupmode      = $courseconfig->groupmode;
            $template->groupmodeforce = $courseconfig->groupmodeforce;
            $template->visible        = $courseconfig->visible;
            $template->lang           = $courseconfig->lang;
            $template->groupmodeforce = $courseconfig->groupmodeforce;
        }
        $course = $template;

        $course->category = $this->get_config('category');
        if (!$DB->record_exists('course_categories', array('id'=>$this->get_config('category')))) {
            $categories = $DB->get_records('course_categories', array(), 'sortorder', 'id', 0, 1);
            $first = reset($categories);
            $course->category = $first->id;
        }

        // Override with required ext data
        $course->idnumber  = $course_ext[$this->get_config('course_idnumber')][0];
        $course->fullname  = $course_ext[$this->get_config('course_fullname')][0];
        $course->shortname = $course_ext[$this->get_config('course_shortname')][0];
        if (empty($course->idnumber) || empty($course->fullname) || empty($course->shortname)) {
            // We are in trouble!
            error_log($this->errorlogtag.get_string('cannotcreatecourse', 'enrol_ldap'));
            error_log($this->errorlogtag.var_export($course, true));
            return false;
        }

        $summary = $this->get_config('course_summary');
        if (!isset($summary) || empty($course_ext[$summary][0])) {
            $course->summary = '';
        } else {
            $course->summary = $course_ext[$this->get_config('course_summary')][0];
        }

        // Check if the shortname already exists if it does - skip course creation.
        if ($DB->record_exists('course', array('shortname' => $course->shortname))) {
            error_log($this->errorlogtag . get_string('duplicateshortname', 'enrol_ldap', $course));
            return false;
        }

        $newcourse = create_course($course);
        return $newcourse->id;
    }
}

