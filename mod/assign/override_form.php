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
 * Settings form for overrides in the assign module.
 *
 * @package    mod_assign
 * @copyright  2016 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/mod/assign/mod_form.php');


/**
 * Form for editing settings overrides.
 *
 * @copyright  2016 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_override_form extends moodleform {

    /** @var object course module object. */
    protected $cm;

    /** @var object the assign settings object. */
    protected $assign;

    /** @var context the assign context. */
    protected $context;

    /** @var bool editing group override (true) or user override (false). */
    protected $groupmode;

    /** @var int groupid, if provided. */
    protected $groupid;

    /** @var int userid, if provided. */
    protected $userid;

    /** @var int sortorder, if provided. */
    protected $sortorder;

    /** @var int selecteduserid, if provided. */
    protected $selecteduserid;

    /**
     * Constructor.
     * @param moodle_url $submiturl the form action URL.
     * @param object $cm course module object.
     * @param object $assign the assign settings object.
     * @param object $context the assign context.
     * @param bool $groupmode editing group override (true) or user override (false).
     * @param object $override the override being edited, if it already exists.
     * @param int $selecteduserid the user selected in the form, if any.
     */
    public function __construct($submiturl, $cm, $assign, $context, $groupmode, $override, $selecteduserid = null) {

        $this->cm = $cm;
        $this->assign = $assign;
        $this->context = $context;
        $this->groupmode = $groupmode;
        $this->groupid = empty($override->groupid) ? 0 : $override->groupid;
        $this->userid = empty($override->userid) ? 0 : $override->userid;
        $this->sortorder = empty($override->sortorder) ? null : $override->sortorder;
        $this->selecteduserid = $selecteduserid;

        parent::__construct($submiturl, null, 'post');

    }

    /**
     * Define this form - called by the parent constructor
     */
    protected function definition() {
        global $DB, $OUTPUT, $PAGE;

        $cm = $this->cm;
        $mform = $this->_form;
        $userid = $this->selecteduserid ?? $this->userid ?: null;
        $assigninstance = $this->assign->get_instance($userid);
        $inrelativedatesmode = !empty($this->assign->get_course()->relativedatesmode);

        $assigngroupmode = groups_get_activity_groupmode($cm);
        $accessallgroups = ($assigngroupmode == NOGROUPS) || has_capability('moodle/site:accessallgroups', $this->context);

        if ($this->groupmode) {
            $mform->addElement('header', 'override', get_string('groupoverrides', 'assign'));
            // Group override.
            if ($this->groupid) {
                // There is already a groupid, so freeze the selector.
                $groupchoices = [
                    $this->groupid => format_string(groups_get_group_name($this->groupid), true, $this->context),
                ];
                $mform->addElement('select', 'groupid',
                        get_string('overridegroup', 'assign'), $groupchoices);
                $mform->freeze('groupid');
                // Add a sortorder element.
                $mform->addElement('hidden', 'sortorder', $this->sortorder);
                $mform->setType('sortorder', PARAM_INT);
                $mform->freeze('sortorder');
            } else {
                // Prepare the list of groups.
                // Only include the groups the current can access.
                $groups = $accessallgroups ? groups_get_all_groups($cm->course) : groups_get_activity_allowed_groups($cm);
                if (empty($groups)) {
                    // Generate an error.
                    $link = new moodle_url('/mod/assign/overrides.php', array('cmid' => $cm->id));
                    throw new \moodle_exception('groupsnone', 'assign', $link);
                }

                $groupchoices = array();
                foreach ($groups as $group) {
                    $groupchoices[$group->id] = format_string($group->name, true, $this->context);
                }
                unset($groups);

                if (count($groupchoices) == 0) {
                    $groupchoices[0] = get_string('none');
                }

                $mform->addElement('select', 'groupid',
                        get_string('overridegroup', 'assign'), $groupchoices);
                $mform->addRule('groupid', get_string('required'), 'required', null, 'client');
            }
        } else {
            $mform->addElement('header', 'override', get_string('useroverrides', 'assign'));
            // User override.
            if ($this->userid) {
                // There is already a userid, so freeze the selector.
                $user = $DB->get_record('user', array('id' => $this->userid));
                $userchoices = array();
                $userchoices[$this->userid] = fullname($user);
                $mform->addElement('select', 'userid',
                        get_string('overrideuser', 'assign'), $userchoices);
                $mform->freeze('userid');
            } else {
                // Prepare the list of users.
                $users = [];
                list($sort) = users_order_by_sql('u');

                // Get the list of appropriate users, depending on whether and how groups are used.
                $userfieldsapi = \core_user\fields::for_name();
                if ($accessallgroups) {
                    $users = get_enrolled_users($this->context, '', 0,
                            'u.id, u.email, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects, $sort);
                } else if ($groups = groups_get_activity_allowed_groups($cm)) {
                    $enrolledjoin = get_enrolled_join($this->context, 'u.id');
                    $userfields = 'u.id, u.email, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects;
                    list($ingroupsql, $ingroupparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
                    $params = $enrolledjoin->params + $ingroupparams;
                    $sql = "SELECT $userfields
                              FROM {user} u
                              JOIN {groups_members} gm ON gm.userid = u.id
                                   {$enrolledjoin->joins}
                             WHERE gm.groupid $ingroupsql
                                   AND {$enrolledjoin->wheres}
                          ORDER BY $sort";
                    $users = $DB->get_records_sql($sql, $params);
                }

                // Filter users based on any fixed restrictions (groups, profile).
                $info = new \core_availability\info_module($cm);
                $users = $info->filter_user_list($users);

                if (empty($users)) {
                    // Generate an error.
                    $link = new moodle_url('/mod/assign/overrides.php', array('cmid' => $cm->id));
                    throw new \moodle_exception('usersnone', 'assign', $link);
                }

                $userchoices = array();
                // TODO Does not support custom user profile fields (MDL-70456).
                $canviewemail = in_array('email', \core_user\fields::get_identity_fields($this->context, false));
                foreach ($users as $id => $user) {
                    if (empty($invalidusers[$id]) || (!empty($override) &&
                            $id == $override->userid)) {
                        if ($canviewemail) {
                            $userchoices[$id] = fullname($user) . ', ' . $user->email;
                        } else {
                            $userchoices[$id] = fullname($user);
                        }
                    }
                }
                unset($users);

                if (count($userchoices) == 0) {
                    $userchoices[0] = get_string('none');
                }
                $mform->addElement('searchableselector', 'userid',
                        get_string('overrideuser', 'assign'), $userchoices);
                $mform->addRule('userid', get_string('required'), 'required', null, 'client');

                if ($inrelativedatesmode) {
                    // If in relative dates mode then add the JS to reload the page when the user
                    // selection is changed to ensure that the correct dates are displayed.
                    $PAGE->requires->js_call_amd('mod_assign/override_form', 'init', [
                        $mform->getAttribute('id'),
                        'userid'
                    ]);
                }
            }

            if ($inrelativedatesmode) {
                if ($userid) {
                    $templatecontext = [
                        'allowsubmissionsfromdate' => $assigninstance->allowsubmissionsfromdate,
                        'duedate' => $assigninstance->duedate,
                        'cutoffdate' => $assigninstance->cutoffdate
                    ];
                    $html = $OUTPUT->render_from_template('mod_assign/override_form_user_defaults', $templatecontext);
                } else {
                    $html = get_string('noselection', 'form');
                }

                $groupelements = [];
                $groupelements[] = $mform->createElement('html', $html);
                $mform->addGroup($groupelements, null, get_string('userassignmentdefaults', 'mod_assign'), null, false);
            }
        }

        $users = $DB->get_fieldset_select('groups_members', 'userid', 'groupid = ?', array($this->groupid));
        array_push($users, $this->userid);
        $extensionmax = 0;
        foreach ($users as $value) {
            $extension = $DB->get_record('assign_user_flags', array('assignment' => $assigninstance->id,
                'userid' => $value));
            if ($extension) {
                if ($extensionmax < $extension->extensionduedate) {
                    $extensionmax = $extension->extensionduedate;
                }
            }
        }

        if ($extensionmax) {
            $assigninstance->extensionduedate = $extensionmax;
        }

        // Open and close dates.
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate',
            get_string('allowsubmissionsfromdate', 'assign'), array('optional' => true));
        $mform->setDefault('allowsubmissionsfromdate', $assigninstance->allowsubmissionsfromdate);

        $mform->addElement('date_time_selector', 'duedate', get_string('duedate', 'assign'), array('optional' => true));
        $mform->setDefault('duedate', $assigninstance->duedate);

        $mform->addElement('date_time_selector', 'cutoffdate', get_string('cutoffdate', 'assign'), array('optional' => true));
        $mform->setDefault('cutoffdate', $assigninstance->cutoffdate);

        if (isset($assigninstance->extensionduedate)) {
            $mform->addElement('static', 'extensionduedate', get_string('extensionduedate', 'assign'),
                userdate($assigninstance->extensionduedate));
        }

        // Time limit.
        $mform->addElement('duration', 'timelimit',
            get_string('timelimit', 'assign'), array('optional' => true));
        $mform->setDefault('timelimit', $assigninstance->timelimit);

        // Submit buttons.
        $mform->addElement('submit', 'resetbutton',
                get_string('reverttodefaults', 'assign'));

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton',
                get_string('save', 'assign'));
        $buttonarray[] = $mform->createElement('submit', 'againbutton',
                get_string('saveoverrideandstay', 'assign'));
        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonbar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonbar');

    }

    /**
     * Validate the submitted form data.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $mform =& $this->_form;
        $userid = $this->selecteduserid ?? $this->userid ?: null;
        $assigninstance = $this->assign->get_instance($userid);

        if ($mform->elementExists('userid')) {
            if (empty($data['userid'])) {
                $errors['userid'] = get_string('required');
            }
        }

        if ($mform->elementExists('groupid')) {
            if (empty($data['groupid'])) {
                $errors['groupid'] = get_string('required');
            }
        }

        // Ensure that the dates make sense.
        if (!empty($data['allowsubmissionsfromdate']) && !empty($data['cutoffdate'])) {
            if ($data['cutoffdate'] < $data['allowsubmissionsfromdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatefromdatevalidation', 'assign');
            }
        }

        if (!empty($data['allowsubmissionsfromdate']) && !empty($data['duedate'])) {
            if ($data['duedate'] < $data['allowsubmissionsfromdate']) {
                $errors['duedate'] = get_string('duedatevalidation', 'assign');
            }
        }

        if (!empty($data['cutoffdate']) && !empty($data['duedate'])) {
            if ($data['cutoffdate'] < $data['duedate'] ) {
                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'assign');
            }
        }

        // Ensure that override duedate/allowsubmissionsfromdate are before extension date if exist.
        if (!empty($assigninstance->extensionduedate) && !empty($data['duedate'])) {
            if ($assigninstance->extensionduedate < $data['duedate']) {
                $errors['duedate'] = get_string('extensionnotafterduedate', 'assign');
            }
        }
        if (!empty($assigninstance->extensionduedate) && !empty($data['allowsubmissionsfromdate'])) {
            if ($assigninstance->extensionduedate < $data['allowsubmissionsfromdate']) {
                $errors['allowsubmissionsfromdate'] = get_string('extensionnotafterfromdate', 'assign');
            }
        }

        // Ensure that at least one assign setting was changed.
        $changed = false;
        $keys = array('duedate', 'cutoffdate', 'allowsubmissionsfromdate', 'timelimit');
        foreach ($keys as $key) {
            if ($data[$key] != $assigninstance->{$key}) {
                $changed = true;
                break;
            }
        }

        if (!$changed) {
            $errors['allowsubmissionsfromdate'] = get_string('nooverridedata', 'assign');
        }

        return $errors;
    }
}
