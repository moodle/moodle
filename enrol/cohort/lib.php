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
 * Cohort enrolment plugin.
 *
 * @package    enrol_cohort
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * COHORT_CREATEGROUP constant for automatically creating a group for a cohort.
 */
define('COHORT_CREATE_GROUP', -1);

/**
 * COHORT_NOGROUP constant for using no group for a cohort.
 */
define('COHORT_NOGROUP', 0);


/**
 * Cohort enrolment plugin implementation.
 * @author Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_cohort_plugin extends enrol_plugin {

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/cohort:config', $context);
    }

    /**
     * Returns localised name of enrol instance.
     *
     * @param stdClass $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance)) {
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol);

        } else if (empty($instance->name)) {
            $enrol = $this->get_name();
            $cohort = $DB->get_record('cohort', array('id'=>$instance->customint1));
            if (!$cohort) {
                return get_string('pluginname', 'enrol_'.$enrol);
            }
            $cohortname = format_string($cohort->name, true, array('context'=>context::instance_by_id($cohort->contextid)));
            if ($role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = role_get_name($role, context_course::instance($instance->courseid, IGNORE_MISSING), ROLENAME_BOTH);
                return get_string('pluginname', 'enrol_'.$enrol) . ' (' . $cohortname . ' - ' . $role .')';
            } else {
                return get_string('pluginname', 'enrol_'.$enrol) . ' (' . $cohortname . ')';
            }

        } else {
            return format_string($instance->name, true, array('context'=>context_course::instance($instance->courseid)));
        }
    }

    /**
     * Given a courseid this function returns true if the user is able to enrol or configure cohorts.
     * AND there are cohorts that the user can view.
     *
     * @param int $courseid
     * @return bool
     */
    public function can_add_instance($courseid) {
        global $CFG;
        require_once($CFG->dirroot . '/cohort/lib.php');
        $coursecontext = context_course::instance($courseid);
        if (!has_capability('moodle/course:enrolconfig', $coursecontext) or !has_capability('enrol/cohort:config', $coursecontext)) {
            return false;
        }
        return cohort_get_available_cohorts($coursecontext, 0, 0, 1) ? true : false;
    }

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array $fields instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = null) {
        global $CFG;

        // Allows multiple cohorts to be set on creation.
        if (!empty($fields['customint1'])) {
            $fields2 = $fields;
            if (!is_array($fields['customint1'])) {
                $fields['customint1'] = array($fields['customint1']);
            }
            foreach ($fields['customint1'] as $cid) {
                $fields2['customint1'] = $cid;
                if (!empty($fields['customint2']) && $fields['customint2'] == COHORT_CREATE_GROUP) {
                    // Create a new group for the cohort if requested.
                    $context = context_course::instance($course->id);
                    require_capability('moodle/course:managegroups', $context);
                    $groupid = enrol_cohort_create_new_group($course->id, $cid);
                    $fields2['customint2'] = $groupid;
                }
                $result = parent::add_instance($course, $fields2);
            }
        } else {
            $result = parent::add_instance($course, $fields);
        }
        require_once("$CFG->dirroot/enrol/cohort/locallib.php");
        $trace = new null_progress_trace();
        enrol_cohort_sync($trace, $course->id);
        $trace->finished();
        return $result;
    }

    /**
     * Update instance of enrol plugin.
     * @param stdClass $instance
     * @param stdClass $data modified instance fields
     * @return boolean
     */
    public function update_instance($instance, $data) {
        global $CFG;

        // NOTE: no cohort changes here!!!
        $context = context_course::instance($instance->courseid);
        if ($data->roleid != $instance->roleid) {
            // The sync script can only add roles, for perf reasons it does not modify them.
            $params = array(
                'contextid' => $context->id,
                'roleid' => $instance->roleid,
                'component' => 'enrol_cohort',
                'itemid' => $instance->id
            );
            role_unassign_all($params);
        }
        // Create a new group for the cohort if requested.
        if ($data->customint2 == COHORT_CREATE_GROUP) {
            require_capability('moodle/course:managegroups', $context);
            $groupid = enrol_cohort_create_new_group($instance->courseid, $data->customint1);
            $data->customint2 = $groupid;
        }

        $result = parent::update_instance($instance, $data);

        require_once("$CFG->dirroot/enrol/cohort/locallib.php");
        $trace = new null_progress_trace();
        enrol_cohort_sync($trace, $instance->courseid);
        $trace->finished();

        return $result;
    }

    /**
     * Called after updating/inserting course.
     *
     * @param bool $inserted true if course just inserted
     * @param stdClass $course
     * @param stdClass $data form data
     * @return void
     */
    public function course_updated($inserted, $course, $data) {
        // It turns out there is no need for cohorts to deal with this hook, see MDL-34870.
    }

    /**
     * Update instance status
     *
     * @param stdClass $instance
     * @param int $newstatus ENROL_INSTANCE_ENABLED, ENROL_INSTANCE_DISABLED
     * @return void
     */
    public function update_status($instance, $newstatus) {
        global $CFG;

        parent::update_status($instance, $newstatus);

        require_once("$CFG->dirroot/enrol/cohort/locallib.php");
        $trace = new null_progress_trace();
        enrol_cohort_sync($trace, $instance->courseid);
        $trace->finished();
    }

    /**
     * Does this plugin allow manual unenrolment of a specific user?
     * Yes, but only if user suspended...
     *
     * @param stdClass $instance course enrol instance
     * @param stdClass $ue record from user_enrolments table
     *
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol this user, false means nobody may touch this user enrolment
     */
    public function allow_unenrol_user(stdClass $instance, stdClass $ue) {
        if ($ue->status == ENROL_USER_SUSPENDED) {
            return true;
        }

        return false;
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB, $CFG;

        if (!$step->get_task()->is_samesite()) {
            // No cohort restore from other sites.
            $step->set_mapping('enrol', $oldid, 0);
            return;
        }

        if (!empty($data->customint2)) {
            $data->customint2 = $step->get_mappingid('group', $data->customint2);
        }

        if ($data->roleid and $DB->record_exists('cohort', array('id'=>$data->customint1))) {
            $instance = $DB->get_record('enrol', array('roleid'=>$data->roleid, 'customint1'=>$data->customint1, 'courseid'=>$course->id, 'enrol'=>$this->get_name()));
            if ($instance) {
                $instanceid = $instance->id;
            } else {
                $instanceid = $this->add_instance($course, (array)$data);
            }
            $step->set_mapping('enrol', $oldid, $instanceid);

            require_once("$CFG->dirroot/enrol/cohort/locallib.php");
            $trace = new null_progress_trace();
            enrol_cohort_sync($trace, $course->id);
            $trace->finished();

        } else if ($this->get_config('unenrolaction') == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
            $data->customint1 = 0;
            $instance = $DB->get_record('enrol', array('roleid'=>$data->roleid, 'customint1'=>$data->customint1, 'courseid'=>$course->id, 'enrol'=>$this->get_name()));

            if ($instance) {
                $instanceid = $instance->id;
            } else {
                $data->status = ENROL_INSTANCE_DISABLED;
                $instanceid = $this->add_instance($course, (array)$data);
            }
            $step->set_mapping('enrol', $oldid, $instanceid);

            require_once("$CFG->dirroot/enrol/cohort/locallib.php");
            $trace = new null_progress_trace();
            enrol_cohort_sync($trace, $course->id);
            $trace->finished();

        } else {
            $step->set_mapping('enrol', $oldid, 0);
        }
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $oldinstancestatus
     * @param int $userid
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        global $DB;

        if ($this->get_config('unenrolaction') != ENROL_EXT_REMOVED_SUSPENDNOROLES) {
            // Enrolments were already synchronised in restore_instance(), we do not want any suspended leftovers.
            return;
        }

        // ENROL_EXT_REMOVED_SUSPENDNOROLES means all previous enrolments are restored
        // but without roles and suspended.

        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid))) {
            $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, ENROL_USER_SUSPENDED);
        }
    }

    /**
     * Restore user group membership.
     * @param stdClass $instance
     * @param int $groupid
     * @param int $userid
     */
    public function restore_group_member($instance, $groupid, $userid) {
        // Nothing to do here, the group members are added in $this->restore_group_restored()
        return;
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/cohort:config', $context);
    }

    /**
     * Return an array of valid options for the status.
     *
     * @return array
     */
    protected function get_status_options() {
        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        return $options;
    }

    /**
     * Return an array of valid options for the cohorts.
     *
     * @param stdClass $instance
     * @param context $context
     * @return array
     */
    protected function get_cohort_options($instance, $context) {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/cohort/lib.php');

        $cohorts = array();

        if ($instance->id) {
            if ($cohort = $DB->get_record('cohort', array('id' => $instance->customint1))) {
                $name = format_string($cohort->name, true, array('context' => context::instance_by_id($cohort->contextid)));
                $cohorts = array($instance->customint1 => $name);
            } else {
                $cohorts = array($instance->customint1 => get_string('error'));
            }
        } else {
            $cohorts = array('' => get_string('choosedots'));
            $allcohorts = cohort_get_available_cohorts($context, 0, 0, 0);
            foreach ($allcohorts as $c) {
                $cohorts[$c->id] = format_string($c->name);
            }
        }
        return $cohorts;
    }

    /**
     * Return an array of valid options for the roles.
     *
     * @param stdClass $instance
     * @param context $coursecontext
     * @return array
     */
    protected function get_role_options($instance, $coursecontext) {
        global $DB;

        $roles = get_assignable_roles($coursecontext, ROLENAME_BOTH);
        $roles[0] = get_string('none');
        $roles = array_reverse($roles, true); // Descending default sortorder.

        // If the instance is already configured, but the configured role is no longer assignable in the course then add it back.
        if ($instance->id and !isset($roles[$instance->roleid])) {
            if ($role = $DB->get_record('role', array('id' => $instance->roleid))) {
                $roles[$instance->roleid] = role_get_name($role, $coursecontext, ROLENAME_BOTH);
            } else {
                $roles[$instance->roleid] = get_string('error');
            }
        }

        return $roles;
    }

    /**
     * Return an array of valid options for the groups.
     *
     * @param context $coursecontext
     * @return array
     */
    protected function get_group_options($coursecontext) {
        $groups = array(0 => get_string('none'));
        if (has_capability('moodle/course:managegroups', $coursecontext)) {
            $groups[COHORT_CREATE_GROUP] = get_string('creategroup', 'enrol_cohort');
        }

        foreach (groups_get_all_groups($coursecontext->instanceid) as $group) {
            $groups[$group->id] = format_string($group->name, true, array('context' => $coursecontext));
        }

        return $groups;
    }

    /**
     * We are a good plugin and don't invent our own UI/validation code path.
     *
     * @return boolean
     */
    public function use_standard_editing_ui() {
        return true;
    }

    /**
     * Add elements to the edit instance form.
     *
     * @param stdClass $instance
     * @param MoodleQuickForm $mform
     * @param context $coursecontext
     * @return bool
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $coursecontext) {

        $options = $this->get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'enrol_cohort'), $options);

        $options = ['contextid' => $coursecontext->id, 'multiple' => true];
        $mform->addElement('cohort', 'customint1', get_string('cohort', 'cohort'), $options);

        if ($instance->id) {
            $mform->setConstant('customint1', $instance->customint1);
            $mform->hardFreeze('customint1', $instance->customint1);
        } else {
            $mform->addRule('customint1', get_string('required'), 'required', null, 'client');
        }

        $roles = $this->get_role_options($instance, $coursecontext);
        $mform->addElement('select', 'roleid', get_string('assignrole', 'enrol_cohort'), $roles);
        $mform->setDefault('roleid', $this->get_config('roleid'));
        $groups = $this->get_group_options($coursecontext);
        $mform->addElement('select', 'customint2', get_string('addgroup', 'enrol_cohort'), $groups);
    }

    /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @param array $data array of ("fieldname" => value) of submitted data
     * @param array $files array of uploaded files "element_name" => tmp_file_path
     * @param object $instance The instance loaded from the DB
     * @param context $context The context of the instance we are editing
     * @return array of "element_name" => "error_description" if there are errors,
     *         or an empty array if everything is OK.
     * @return void
     */
    public function edit_instance_validation($data, $files, $instance, $context) {
        global $DB;
        $errors = array();
        // Allows multiple cohorts to be selected.
        list($sql1, $params1) = $DB->get_in_or_equal($data['customint1'], SQL_PARAMS_NAMED);
        $params = array(
            'roleid' => $data['roleid'],
            'courseid' => $data['courseid'],
            'id' => $data['id']
        );
        $params = array_merge($params, $params1);
        $sql = "roleid = :roleid AND customint1 $sql1 AND courseid = :courseid AND enrol = 'cohort' AND id <> :id";
        if ($DB->record_exists_select('enrol', $sql, $params)) {
            $errors['customint1'] = get_string('instanceexists', 'enrol_cohort');
        }
        $validstatus = array_keys($this->get_status_options());
        $validcohorts = array_keys($this->get_cohort_options($instance, $context));
        $validroles = array_keys($this->get_role_options($instance, $context));
        $validgroups = array_keys($this->get_group_options($context));
        $tovalidate = array(
            'status' => $validstatus,
            'roleid' => $validroles,
            'customint2' => $validgroups
        );
        $typeerrors = $this->validate_param_types($data, $tovalidate);
        // When creating a new cohort enrolment, we allow multiple cohorts in just one go.
        // When editing an existing enrolment, changing the cohort is no allowed, so cohort is a single value.
        if (is_array($data['customint1'])) {
            $cohorts = $data['customint1'];
        } else {
            $cohorts = [$data['customint1']];
        }

        $errors = array_merge($errors, $typeerrors);
        // Check that the cohorts passed are valid.
        if (!empty(array_diff($cohorts, $validcohorts))) {
            $errors['customint1'] = get_string('invaliddata', 'error');
        }
        return $errors;
    }

    /**
     * Check if data is valid for a given enrolment plugin
     *
     * @param array $enrolmentdata enrolment data to validate.
     * @param int|null $courseid Course ID.
     * @return array Errors
     */
    public function validate_enrol_plugin_data(array $enrolmentdata, ?int $courseid = null): array {
        global $DB;

        $errors = [];
        if (!enrol_is_enabled('cohort')) {
            $errors['plugindisabled'] =
                new lang_string('plugindisabled', 'enrol_cohort');
        }

        if (isset($enrolmentdata['addtogroup'])) {
            $addtogroup = $enrolmentdata['addtogroup'];
            if (($addtogroup == - COHORT_CREATE_GROUP) || $addtogroup == COHORT_NOGROUP) {
                if (isset($enrolmentdata['groupname'])) {
                    $errors['erroraddtogroupgroupname'] =
                        new lang_string('erroraddtogroupgroupname', 'group');
                }
            } else {
                $errors['erroraddtogroup'] =
                    new lang_string('erroraddtogroup', 'group');
            }
        }

        if ($courseid) {
            $enrolmentdata = $this->fill_enrol_custom_fields($enrolmentdata, $courseid);
            $error = $this->validate_plugin_data_context($enrolmentdata, $courseid);
            if ($error) {
                $errors['contextnotallowed'] = $error;
            }

            if (isset($enrolmentdata['groupname']) && $enrolmentdata['groupname']) {
                $groupname = $enrolmentdata['groupname'];
                if (!groups_get_group_by_name($courseid, $groupname)) {
                    $errors['errorinvalidgroup'] =
                        new lang_string('errorinvalidgroup', 'group', $groupname);
                }
            }
        }

        if (!isset($enrolmentdata['cohortidnumber'])) {
            $missingmandatoryfields = 'cohortidnumber';
        } else {
            $cohortidnumber = $enrolmentdata['cohortidnumber'];
            // Cohort idnumber is unique.
            $cohortid = $DB->get_field('cohort', 'id', ['idnumber' => $cohortidnumber]);

            if (!$cohortid) {
                $errors['unknowncohort'] =
                    new lang_string('unknowncohort', 'cohort', $cohortidnumber);
            }
        }

        if (!isset($enrolmentdata['role'])) {
            // We require role since we need it to identify enrol instance.
            if (isset($missingmandatoryfields)) {
                $missingmandatoryfields .= ', role';
            } else {
                $missingmandatoryfields = 'role';
            }
            $errors['missingmandatoryfields'] =
                new lang_string('missingmandatoryfields', 'tool_uploadcourse',
                    $missingmandatoryfields);
        } else {
            $roleid = $DB->get_field('role', 'id', ['shortname' => $enrolmentdata['role']]);
            if (!$roleid) {
                $errors['unknownrole'] =
                    new lang_string('unknownrole', 'error', s($enrolmentdata['role']));
            }
        }

        return $errors;
    }

    /**
     * Fill custom fields data for a given enrolment plugin.
     *
     * @param array $enrolmentdata enrolment data.
     * @param int $courseid Course ID.
     * @return array Updated enrolment data with custom fields info.
     */
    public function fill_enrol_custom_fields(array $enrolmentdata, int $courseid): array {
        global $DB;

        if (isset($enrolmentdata['cohortidnumber'])) {
            // Cohort idnumber is unique.
            $enrolmentdata['customint1'] =
                $DB->get_field('cohort', 'id', ['idnumber' => $enrolmentdata['cohortidnumber']]);
        }

        if (isset($enrolmentdata['addtogroup'])) {
            if ($enrolmentdata['addtogroup'] == COHORT_NOGROUP) {
                $enrolmentdata['customint2'] = COHORT_NOGROUP;
            } else if ($enrolmentdata['addtogroup'] == - COHORT_CREATE_GROUP) {
                $enrolmentdata['customint2'] = COHORT_CREATE_GROUP;
            }
        } else if (isset($enrolmentdata['groupname'])) {
            $enrolmentdata['customint2'] = groups_get_group_by_name($courseid, $enrolmentdata['groupname']);
        }
        return $enrolmentdata;
    }

    /**
     * Check if plugin custom data is allowed in relevant context.
     *
     * @param array $enrolmentdata enrolment data to validate.
     * @param int|null $courseid Course ID.
     * @return lang_string|null Error
     */
    public function validate_plugin_data_context(array $enrolmentdata, ?int $courseid = null): ?lang_string {
        if (isset($enrolmentdata['customint1'])) {
            $cohortid = $enrolmentdata['customint1'];
            $coursecontext = \context_course::instance($courseid);
            if (!cohort_get_cohort($cohortid, $coursecontext)) {
                return new lang_string('contextcohortnotallowed', 'cohort', $enrolmentdata['cohortidnumber']);
            }
        }
        $enrolmentdata += [
            'customint1' => null,
            'customint2' => null,
            'roleid' => 0,
        ];
        return parent::validate_plugin_data_context($enrolmentdata, $courseid);
    }

    /**
     * Add new instance of enrol plugin with custom settings,
     * called when adding new instance manually or when adding new course.
     * Used for example on course upload.
     *
     * Not all plugins support this.
     *
     * @param stdClass $course Course object
     * @param array|null $fields instance fields
     * @return int|null id of new instance or null if not supported
     */
    public function add_custom_instance(stdClass $course, ?array $fields = null): ?int {
        return $this->add_instance($course, $fields);
    }


    /**
     * Check if enrolment plugin is supported in csv course upload.
     *
     * @return bool
     */
    public function is_csv_upload_supported(): bool {
        return true;
    }

    /**
     * Finds matching instances for a given course.
     *
     * @param array $enrolmentdata enrolment data.
     * @param int $courseid Course ID.
     * @return stdClass|null Matching instance
     */
    public function find_instance(array $enrolmentdata, int $courseid): ?stdClass {
        global $DB;
        $instances = enrol_get_instances($courseid, false);

        $instance = null;
        if (isset($enrolmentdata['cohortidnumber']) && isset($enrolmentdata['role'])) {
            $cohortid = $DB->get_field('cohort', 'id', ['idnumber' => $enrolmentdata['cohortidnumber']]);
            $roleid = $DB->get_field('role', 'id', ['shortname' => $enrolmentdata['role']]);
            if ($cohortid && $roleid) {
                foreach ($instances as $i) {
                    if ($i->enrol == 'cohort' && $i->customint1 == $cohortid && $i->roleid == $roleid) {
                        $instance = $i;
                        break;
                    }
                }
            }
        }
        return $instance;
    }
}

/**
 * Prevent removal of enrol roles.
 * @param int $itemid
 * @param int $groupid
 * @param int $userid
 * @return bool
 */
function enrol_cohort_allow_group_member_remove($itemid, $groupid, $userid) {
    return false;
}

/**
 * Create a new group with the cohorts name.
 *
 * @param int $courseid
 * @param int $cohortid
 * @return int $groupid Group ID for this cohort.
 */
function enrol_cohort_create_new_group($courseid, $cohortid) {
    global $DB, $CFG;

    require_once($CFG->dirroot . '/group/lib.php');

    $groupname = $DB->get_field('cohort', 'name', array('id' => $cohortid), MUST_EXIST);
    $a = new stdClass();
    $a->name = $groupname;
    $a->increment = '';
    $groupname = trim(get_string('defaultgroupnametext', 'enrol_cohort', $a));
    $inc = 1;
    // Check to see if the cohort group name already exists. Add an incremented number if it does.
    while ($DB->record_exists('groups', array('name' => $groupname, 'courseid' => $courseid))) {
        $a->increment = '(' . (++$inc) . ')';
        $newshortname = trim(get_string('defaultgroupnametext', 'enrol_cohort', $a));
        $groupname = $newshortname;
    }
    // Create a new group for the cohort.
    $groupdata = new stdClass();
    $groupdata->courseid = $courseid;
    $groupdata->name = $groupname;
    $groupid = groups_create_group($groupdata);

    return $groupid;
}
