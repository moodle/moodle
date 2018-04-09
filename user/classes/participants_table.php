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
 * Contains the class used for the displaying the participants table.
 *
 * @package    core_user
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user;

use context;
use core_user\output\status_field;
use DateTime;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Class for the displaying the participants table.
 *
 * @package    core_user
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_table extends \table_sql {

    /**
     * @var int $courseid The course id
     */
    protected $courseid;

    /**
     * @var int|false False if groups not used, int if groups used, 0 for all groups.
     */
    protected $currentgroup;

    /**
     * @var int $accesssince The time the user last accessed the site
     */
    protected $accesssince;

    /**
     * @var int $roleid The role we are including, 0 means all enrolled users
     */
    protected $roleid;

    /**
     * @var int $enrolid The applied filter for the user enrolment ID.
     */
    protected $enrolid;

    /**
     * @var int $status The applied filter for the user's enrolment status.
     */
    protected $status;

    /**
     * @var string $search The string being searched.
     */
    protected $search;

    /**
     * @var bool $selectall Has the user selected all users on the page?
     */
    protected $selectall;

    /**
     * @var string[] The list of countries.
     */
    protected $countries;

    /**
     * @var \stdClass[] The list of groups with membership info for the course.
     */
    protected $groups;

    /**
     * @var string[] Extra fields to display.
     */
    protected $extrafields;

    /**
     * @var \stdClass $course The course details.
     */
    protected $course;

    /**
     * @var  context $context The course context.
     */
    protected $context;

    /**
     * @var \stdClass[] List of roles indexed by roleid.
     */
    protected $allroles;

    /**
     * @var \stdClass[] List of roles indexed by roleid.
     */
    protected $allroleassignments;

    /**
     * @var \stdClass[] Assignable roles in this course.
     */
    protected $assignableroles;

    /**
     * @var \stdClass[] Profile roles in this course.
     */
    protected $profileroles;

    /**
     * Sets up the table.
     *
     * @param int $courseid
     * @param int|false $currentgroup False if groups not used, int if groups used, 0 for all groups.
     * @param int $accesssince The time the user last accessed the site
     * @param int $roleid The role we are including, 0 means all enrolled users
     * @param int $enrolid The applied filter for the user enrolment ID.
     * @param int $status The applied filter for the user's enrolment status.
     * @param string|array $search The search string(s)
     * @param bool $bulkoperations Is the user allowed to perform bulk operations?
     * @param bool $selectall Has the user selected all users on the page?
     */
    public function __construct($courseid, $currentgroup, $accesssince, $roleid, $enrolid, $status, $search,
            $bulkoperations, $selectall) {
        global $CFG;

        parent::__construct('user-index-participants-' . $courseid);

        // Get the context.
        $this->course = get_course($courseid);
        $context = \context_course::instance($courseid, MUST_EXIST);
        $this->context = $context;

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        if ($bulkoperations) {
            $headers[] = get_string('select');
            $columns[] = 'select';
        }

        $headers[] = get_string('fullname');
        $columns[] = 'fullname';

        $extrafields = get_extra_user_fields($context);
        foreach ($extrafields as $field) {
            $headers[] = get_user_field_name($field);
            $columns[] = $field;
        }

        $headers[] = get_string('roles');
        $columns[] = 'roles';

        // Get the list of fields we have to hide.
        $hiddenfields = array();
        if (!has_capability('moodle/course:viewhiddenuserfields', $context)) {
            $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
        }

        // Add column for groups if the user can view them.
        $canseegroups = !isset($hiddenfields['groups']);
        if ($canseegroups) {
            $headers[] = get_string('groups');
            $columns[] = 'groups';
        }

        // Do not show the columns if it exists in the hiddenfields array.
        if (!isset($hiddenfields['lastaccess'])) {
            if ($courseid == SITEID) {
                $headers[] = get_string('lastsiteaccess');
            } else {
                $headers[] = get_string('lastcourseaccess');
            }
            $columns[] = 'lastaccess';
        }

        $canreviewenrol = has_capability('moodle/course:enrolreview', $context);
        if ($canreviewenrol && $courseid != SITEID) {
            $columns[] = 'status';
            $headers[] = get_string('participationstatus', 'enrol');
            $this->no_sorting('status');
        };

        $this->define_columns($columns);
        $this->define_headers($headers);

        // Make this table sorted by first name by default.
        $this->sortable(true, 'firstname');

        $this->no_sorting('select');
        $this->no_sorting('roles');
        if ($canseegroups) {
            $this->no_sorting('groups');
        }

        $this->set_attribute('id', 'participants');

        // Set the variables we need to use later.
        $this->currentgroup = $currentgroup;
        $this->accesssince = $accesssince;
        $this->roleid = $roleid;
        $this->search = $search;
        $this->enrolid = $enrolid;
        $this->status = $status;
        $this->selectall = $selectall;
        $this->countries = get_string_manager()->get_list_of_countries(true);
        $this->extrafields = $extrafields;
        $this->context = $context;
        if ($canseegroups) {
            $this->groups = groups_get_all_groups($courseid, 0, 0, 'g.*', true);
        }
        $this->allroles = role_fix_names(get_all_roles($this->context), $this->context);
        $this->allroleassignments = get_users_roles($this->context, [], true, 'c.contextlevel DESC, r.sortorder ASC');
        $this->assignableroles = get_assignable_roles($this->context, ROLENAME_ALIAS, false);
        $this->profileroles = get_profile_roles($this->context);
    }

    /**
     * Render the participants table.
     *
     * @param int $pagesize Size of page for paginated displayed table.
     * @param bool $useinitialsbar Whether to use the initials bar which will only be used if there is a fullname column defined.
     * @param string $downloadhelpbutton
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        global $PAGE;

        parent::out($pagesize, $useinitialsbar, $downloadhelpbutton);

        if (has_capability('moodle/course:enrolreview', $this->context)) {
            $params = ['contextid' => $this->context->id, 'courseid' => $this->course->id];
            $PAGE->requires->js_call_amd('core_user/status_field', 'init', [$params]);
        }
    }

    /**
     * Generate the select column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_select($data) {
        if ($this->selectall) {
            $checked = 'checked="true"';
        } else {
            $checked = '';
        }
        return '<input type="checkbox" class="usercheckbox" name="user' . $data->id . '" ' . $checked . '/>';
    }

    /**
     * Generate the fullname column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_fullname($data) {
        global $OUTPUT;

        return $OUTPUT->user_picture($data, array('size' => 35, 'courseid' => $this->course->id, 'includefullname' => true));
    }

    /**
     * User roles column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_roles($data) {
        global $OUTPUT;

        $roles = isset($this->allroleassignments[$data->id]) ? $this->allroleassignments[$data->id] : [];
        $editable = new \core_user\output\user_roles_editable($this->course,
                                                              $this->context,
                                                              $data,
                                                              $this->allroles,
                                                              $this->assignableroles,
                                                              $this->profileroles,
                                                              $roles);

        return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));
    }

    /**
     * Generate the groups column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_groups($data) {
        global $OUTPUT;

        $usergroups = [];
        foreach ($this->groups as $coursegroup) {
            if (isset($coursegroup->members[$data->id])) {
                $usergroups[] = $coursegroup->id;
            }
        }
        $editable = new \core_group\output\user_groups_editable($this->course, $this->context, $data, $this->groups, $usergroups);
        return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));
    }

    /**
     * Generate the country column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_country($data) {
        if (!empty($this->countries[$data->country])) {
            return $this->countries[$data->country];
        }
        return '';
    }

    /**
     * Generate the last access column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_lastaccess($data) {
        if ($data->lastaccess) {
            return format_time(time() - $data->lastaccess);
        }

        return get_string('never');
    }

    /**
     * Generate the status column.
     *
     * @param \stdClass $data The data object.
     * @return string
     */
    public function col_status($data) {
        global $CFG, $OUTPUT, $PAGE;

        $enrolstatusoutput = '';
        $canreviewenrol = has_capability('moodle/course:enrolreview', $this->context);
        if ($canreviewenrol) {
            $fullname = fullname($data);
            $coursename = $this->course->fullname;
            require_once($CFG->dirroot . '/enrol/locallib.php');
            $manager = new \course_enrolment_manager($PAGE, $this->course);
            $userenrolments = $manager->get_user_enrolments($data->id);
            foreach ($userenrolments as $ue) {
                $timestart = $ue->timestart;
                $timeend = $ue->timeend;
                $actions = $ue->enrolmentplugin->get_user_enrolment_actions($manager, $ue);
                $instancename = $ue->enrolmentinstancename;

                // Default status field label and value.
                $status = get_string('participationactive', 'enrol');
                $statusval = status_field::STATUS_ACTIVE;
                switch ($ue->status) {
                    case ENROL_USER_ACTIVE:
                        $currentdate = new DateTime();
                        $now = $currentdate->getTimestamp();
                        $isexpired = $timestart > $now || ($timeend > 0 && $timeend < $now);
                        $enrolmentdisabled = $ue->enrolmentinstance->status == ENROL_INSTANCE_DISABLED;
                        // If user enrolment status has not yet started/already ended or the enrolment instance is disabled.
                        if ($isexpired || $enrolmentdisabled) {
                            $status = get_string('participationnotcurrent', 'enrol');
                            $statusval = status_field::STATUS_NOT_CURRENT;
                        }
                        break;
                    case ENROL_USER_SUSPENDED:
                        $status = get_string('participationsuspended', 'enrol');
                        $statusval = status_field::STATUS_SUSPENDED;
                        break;
                }

                $statusfield = new status_field($instancename, $coursename, $fullname, $status, $timestart, $timeend, $actions);
                $statusfielddata = $statusfield->set_status($statusval)->export_for_template($OUTPUT);
                $enrolstatusoutput .= $OUTPUT->render_from_template('core_user/status_field', $statusfielddata);
            }
        }
        return $enrolstatusoutput;
    }

    /**
     * This function is used for the extra user fields.
     *
     * These are being dynamically added to the table so there are no functions 'col_<userfieldname>' as
     * the list has the potential to increase in the future and we don't want to have to remember to add
     * a new method to this class. We also don't want to pollute this class with unnecessary methods.
     *
     * @param string $colname The column name
     * @param \stdClass $data
     * @return string
     */
    public function other_cols($colname, $data) {
        // Do not process if it is not a part of the extra fields.
        if (!in_array($colname, $this->extrafields)) {
            return '';
        }

        return s($data->{$colname});
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        list($twhere, $tparams) = $this->get_sql_where();

        $total = user_get_total_participants($this->course->id, $this->currentgroup, $this->accesssince,
            $this->roleid, $this->enrolid, $this->status, $this->search, $twhere, $tparams);

        $this->pagesize($pagesize, $total);

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = 'ORDER BY ' . $sort;
        }

        $this->rawdata = user_get_participants($this->course->id, $this->currentgroup, $this->accesssince,
            $this->roleid, $this->enrolid, $this->status, $this->search, $twhere, $tparams, $sort, $this->get_page_start(),
            $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars(true);
        }
    }
}

