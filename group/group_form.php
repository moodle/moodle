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
 * A form for the creation and editing of groups.
 *
 * @copyright 2006 The Open University, N.D.Freear AT open.ac.uk, J.White AT open.ac.uk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core_group
 */

defined('MOODLE_INTERNAL') || die;

use core_group\visibility;

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Group form class
 *
 * @copyright 2006 The Open University, N.D.Freear AT open.ac.uk, J.White AT open.ac.uk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core_group
 */
class group_form extends moodleform {

    /**
     * Definition of the form
     */
    function definition () {
        global $USER, $CFG, $COURSE;
        $coursecontext = context_course::instance($COURSE->id);

        $mform =& $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];
        $group = $this->_customdata['group'];

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text','name', get_string('groupname', 'group'),'maxlength="254" size="50"');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text','idnumber', get_string('idnumbergroup'), 'maxlength="100" size="10"');
        $mform->addHelpButton('idnumber', 'idnumbergroup');
        $mform->setType('idnumber', PARAM_RAW);
        if (!has_capability('moodle/course:changeidnumber', $coursecontext)) {
            $mform->hardFreeze('idnumber');
        }

        $mform->addElement('editor', 'description_editor', get_string('groupdescription', 'group'), null, $editoroptions);
        $mform->setType('description_editor', PARAM_RAW);

        $mform->addElement('passwordunmask', 'enrolmentkey', get_string('enrolmentkey', 'group'), 'maxlength="254" size="24"', get_string('enrolmentkey', 'group'));
        $mform->addHelpButton('enrolmentkey', 'enrolmentkey', 'group');
        $mform->setType('enrolmentkey', PARAM_RAW);

        $visibilityoptions = [
            GROUPS_VISIBILITY_ALL => get_string('visibilityall', 'group'),
            GROUPS_VISIBILITY_MEMBERS => get_string('visibilitymembers', 'group'),
            GROUPS_VISIBILITY_OWN => get_string('visibilityown', 'group'),
            GROUPS_VISIBILITY_NONE => get_string('visibilitynone', 'group')
        ];
        $mform->addElement('select', 'visibility', get_string('visibility', 'group'), $visibilityoptions);
        $mform->addHelpButton('visibility', 'visibility', 'group');
        $mform->setType('visibility', PARAM_INT);

        $mform->addElement('advcheckbox', 'participation', '', get_string('participation', 'group'));
        $mform->addHelpButton('participation', 'participation', 'group');
        $mform->setType('participation', PARAM_BOOL);
        $mform->setDefault('participation', 1);
        $mform->hideIf('participation', 'visibility', 'in', [GROUPS_VISIBILITY_OWN, GROUPS_VISIBILITY_NONE]);

        // Group conversation messaging.
        if (\core_message\api::can_create_group_conversation($USER->id, $coursecontext)) {
            $mform->addElement('selectyesno', 'enablemessaging', get_string('enablemessaging', 'group'));
            $mform->addHelpButton('enablemessaging', 'enablemessaging', 'group');
            $mform->hideIf('enablemessaging', 'visibility', 'in', [GROUPS_VISIBILITY_OWN, GROUPS_VISIBILITY_NONE]);
        }

        $mform->addElement('static', 'currentpicture', get_string('currentpicture'));

        $mform->addElement('checkbox', 'deletepicture', get_string('delete'));
        $mform->setDefault('deletepicture', 0);

        $mform->addElement('filepicker', 'imagefile', get_string('newpicture', 'group'));
        $mform->addHelpButton('imagefile', 'newpicture', 'group');

        $handler = \core_group\customfield\group_handler::create();
        $handler->instance_form_definition($mform, empty($group->id) ? 0 : $group->id);
        $handler->instance_form_before_set_data($group);

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden','courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Extend the form definition after the data has been parsed.
     */
    public function definition_after_data() {
        global $COURSE, $DB, $USER;

        $mform = $this->_form;
        $groupid = $mform->getElementValue('id');
        $coursecontext = context_course::instance($COURSE->id);

        if ($group = $DB->get_record('groups', array('id' => $groupid))) {
            // If can create group conversation then get if a conversation area exists and it is enabled.
            if (\core_message\api::can_create_group_conversation($USER->id, $coursecontext)) {
                if (\core_message\api::is_conversation_area_enabled('core_group', 'groups', $groupid, $coursecontext->id)) {
                    $mform->getElement('enablemessaging')->setSelected(1);
                }
            }
            // Print picture.
            if (!($pic = print_group_picture($group, $COURSE->id, true, true, false))) {
                $pic = get_string('none');
                if ($mform->elementExists('deletepicture')) {
                    $mform->removeElement('deletepicture');
                }
            }
            $imageelement = $mform->getElement('currentpicture');
            $imageelement->setValue($pic);
        } else {
            if ($mform->elementExists('currentpicture')) {
                $mform->removeElement('currentpicture');
            }
            if ($mform->elementExists('deletepicture')) {
                $mform->removeElement('deletepicture');
            }
        }

        if ($DB->record_exists('groups_members', ['groupid' => $groupid])) {
            // If the group has members, lock visibility and participation fields.
            /** @var MoodleQuickForm_select $visibility */
            $visibility = $mform->getElement('visibility');
            $visibility->freeze();
            /** @var MoodleQuickForm_advcheckbox $participation */
            $participation = $mform->getElement('participation');
            $participation->freeze();
        }

        $handler = core_group\customfield\group_handler::create();
        $handler->instance_form_definition_after_data($this->_form, empty($groupid) ? 0 : $groupid);
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array $errors An array of errors
     */
    function validation($data, $files) {
        global $COURSE, $DB, $CFG;

        $errors = parent::validation($data, $files);

        $name = trim($data['name']);
        if (isset($data['idnumber'])) {
            $idnumber = trim($data['idnumber']);
        } else {
            $idnumber = '';
        }
        if ($data['id'] and $group = $DB->get_record('groups', array('id'=>$data['id']))) {
            if (core_text::strtolower($group->name) != core_text::strtolower($name)) {
                if (groups_get_group_by_name($COURSE->id,  $name)) {
                    $errors['name'] = get_string('groupnameexists', 'group', $name);
                }
            }
            if (!empty($idnumber) && $group->idnumber != $idnumber) {
                if (groups_get_group_by_idnumber($COURSE->id, $idnumber)) {
                    $errors['idnumber']= get_string('idnumbertaken');
                }
            }

            if ($data['enrolmentkey'] != '') {
                $errmsg = '';
                if (!empty($CFG->groupenrolmentkeypolicy) && $group->enrolmentkey !== $data['enrolmentkey']
                        && !check_password_policy($data['enrolmentkey'], $errmsg)) {
                    // Enforce password policy when the password is changed.
                    $errors['enrolmentkey'] = $errmsg;
                } else {
                    // Prevent twice the same enrolment key in course groups.
                    $sql = "SELECT id FROM {groups} WHERE id <> :groupid AND courseid = :courseid AND enrolmentkey = :key";
                    $params = array('groupid' => $data['id'], 'courseid' => $COURSE->id, 'key' => $data['enrolmentkey']);
                    if ($DB->record_exists_sql($sql, $params)) {
                        $errors['enrolmentkey'] = get_string('enrolmentkeyalreadyinuse', 'group');
                    }
                }
            }

        } else if (groups_get_group_by_name($COURSE->id, $name)) {
            $errors['name'] = get_string('groupnameexists', 'group', $name);
        } else if (!empty($idnumber) && groups_get_group_by_idnumber($COURSE->id, $idnumber)) {
            $errors['idnumber']= get_string('idnumbertaken');
        } else if ($data['enrolmentkey'] != '') {
            $errmsg = '';
            if (!empty($CFG->groupenrolmentkeypolicy) && !check_password_policy($data['enrolmentkey'], $errmsg)) {
                // Enforce password policy.
                $errors['enrolmentkey'] = $errmsg;
            } else if ($DB->record_exists('groups', array('courseid' => $COURSE->id, 'enrolmentkey' => $data['enrolmentkey']))) {
                // Prevent the same enrolment key from being used multiple times in course groups.
                $errors['enrolmentkey'] = get_string('enrolmentkeyalreadyinuse', 'group');
            }
        }

        $handler = \core_group\customfield\group_handler::create();
        $errors = array_merge($errors, $handler->instance_form_validation($data, $files));

        return $errors;
    }

    /**
     * Get editor options for this form
     *
     * @return array An array of options
     */
    function get_editor_options() {
        return $this->_customdata['editoroptions'];
    }
}
