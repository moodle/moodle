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
 * Form to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class user_edit_form.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_edit_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition () {
        global $CFG, $COURSE, $USER;

        $mform = $this->_form;
        $editoroptions = null;
        $filemanageroptions = null;

        if (!is_array($this->_customdata)) {
            throw new coding_exception('invalid custom data for user_edit_form');
        }
        $editoroptions = $this->_customdata['editoroptions'];
        $filemanageroptions = $this->_customdata['filemanageroptions'];
        $user = $this->_customdata['user'];
        $userid = $user->id;

        if (empty($user->country)) {
            // We must unset the value here so $CFG->country can be used as default one.
            unset($user->country);
        }

        // Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required');

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        // Print the required moodle fields first.
        $mform->addElement('header', 'moodle', $strgeneral);

        // Shared fields.
        useredit_shared_definition($mform, $editoroptions, $filemanageroptions, $user);

        // Extra settigs.
        if (!empty($CFG->disableuserimages)) {
            $mform->removeElement('deletepicture');
            $mform->removeElement('imagefile');
            $mform->removeElement('imagealt');
        }

        // Next the customisable profile fields.
        profile_definition($mform, $userid);

        $this->add_action_buttons(false, get_string('updatemyprofile'));

        $this->set_data($user);
    }

    /**
     * Extend the form definition after the data has been parsed.
     */
    public function definition_after_data() {
        global $CFG, $DB, $OUTPUT;

        $mform = $this->_form;
        $userid = $mform->getElementValue('id');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }

        if ($user = $DB->get_record('user', array('id' => $userid))) {

            // Remove description.
            if (empty($user->description) && !empty($CFG->profilesforenrolledusersonly) && !$DB->record_exists('role_assignments', array('userid' => $userid))) {
                $mform->removeElement('description_editor');
            }

            // Print picture.
            $context = context_user::instance($user->id, MUST_EXIST);
            $fs = get_file_storage();
            $hasuploadedpicture = ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.png') || $fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.jpg'));
            if (!empty($user->picture) && $hasuploadedpicture) {
                $imagevalue = $OUTPUT->user_picture($user, array('courseid' => SITEID, 'size' => 64));
            } else {
                $imagevalue = get_string('none');
            }
            $imageelement = $mform->getElement('currentpicture');
            $imageelement->setValue($imagevalue);

            if ($mform->elementExists('deletepicture') && !$hasuploadedpicture) {
                $mform->removeElement('deletepicture');
            }

            // Disable fields that are locked by auth plugins.
            $fields = get_user_fieldnames();
            $authplugin = get_auth_plugin($user->auth);
            $customfields = $authplugin->get_custom_user_profile_fields();
            $fields = array_merge($fields, $customfields);
            foreach ($fields as $field) {
                if ($field === 'description') {
                    // Hard coded hack for description field. See MDL-37704 for details.
                    $formfield = 'description_editor';
                } else {
                    $formfield = $field;
                }
                if (!$mform->elementExists($formfield)) {
                    continue;
                }
                $value = $mform->getElement($formfield)->exportValue($mform->getElementValue($formfield)) ?: '';
                $configvariable = 'field_lock_' . $field;
                if (isset($authplugin->config->{$configvariable})) {
                    if ($authplugin->config->{$configvariable} === 'locked') {
                        $mform->hardFreeze($formfield);
                        $mform->setConstant($formfield, $value);
                    } else if ($authplugin->config->{$configvariable} === 'unlockedifempty' and $value != '') {
                        $mform->hardFreeze($formfield);
                        $mform->setConstant($formfield, $value);
                    }
                }
            }

            // Next the customisable profile fields.
            profile_definition_after_data($mform, $user->id);

        } else {
            profile_definition_after_data($mform, 0);
        }
    }

    /**
     * Validate incoming form data.
     * @param array $usernew
     * @param array $files
     * @return array
     */
    public function validation($usernew, $files) {
        global $CFG, $DB;

        $errors = parent::validation($usernew, $files);

        $usernew = (object)$usernew;
        $user    = $DB->get_record('user', array('id' => $usernew->id));

        // Validate email.
        if (!isset($usernew->email)) {
            // Mail not confirmed yet.
        } else if (!validate_email($usernew->email)) {
            $errors['email'] = get_string('invalidemail');
        } else if (($usernew->email !== $user->email) and $DB->record_exists('user', array('email' => $usernew->email, 'mnethostid' => $CFG->mnet_localhost_id))) {
            $errors['email'] = get_string('emailexists');
        }

        if (isset($usernew->email) and $usernew->email === $user->email and over_bounce_threshold($user)) {
            $errors['email'] = get_string('toomanybounces');
        }

        if (isset($usernew->email) and !empty($CFG->verifychangedemail) and !isset($errors['email']) and !has_capability('moodle/user:update', context_system::instance())) {
            $errorstr = email_is_not_allowed($usernew->email);
            if ($errorstr !== false) {
                $errors['email'] = $errorstr;
            }
        }

        // Next the customisable profile fields.
        $errors += profile_validation($usernew, $files);

        return $errors;
    }
}


