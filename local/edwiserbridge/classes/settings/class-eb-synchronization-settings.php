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
 * Settings mod form
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

/**
 * form shown while adding Edwiser Bridge settings.
 */
class edwiserbridge_synchronization_form extends moodleform {
    
    /**
     * Defining synchronization form.
     */
    public function definition() {
        $mform         = $this->_form;
        $sites         = get_site_list();
        $sitekeys      = array_keys($sites);
        $defaultvalues = get_synch_settings($sitekeys[0]);

        $mform->addElement('select', 'wp_site_list', get_string('site-list', 'local_edwiserbridge'), $sites);

        // 1st Field
        // Course enrollment
        $mform->addElement(
            'advcheckbox',
            'course_enrollment',
            get_string('enrollment_checkbox', 'local_edwiserbridge'),
            get_string("enrollment_checkbox_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 2nd field
        // Course unenrollment
        $mform->addElement(
            'advcheckbox',
            'course_un_enrollment',
            get_string('unenrollment_checkbox', 'local_edwiserbridge'),
            get_string("unenrollment_checkbox_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 3rd field.
        // Course Creation.
        $mform->addElement(
            'advcheckbox',
            'course_creation',
            get_string('course_creation', 'local_edwiserbridge'),
            get_string("course_creation_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 4th field.
        // Course deletion.
        $mform->addElement(
            'advcheckbox',
            'course_deletion',
            get_string('course_deletion', 'local_edwiserbridge'),
            get_string("course_deletion_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 5th field.
        // user creation.
        $mform->addElement(
            'advcheckbox',
            'user_creation',
            get_string('user_creation', 'local_edwiserbridge'),
            get_string("user_creation_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 6th field.
        // User update
        $mform->addElement(
            'advcheckbox',
            'user_deletion',
            get_string('user_deletion', 'local_edwiserbridge'),
            get_string("user_deletion_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 7th field.
        // User deletion
        $mform->addElement(
            'advcheckbox',
            'user_updation',
            get_string('user_updation', 'local_edwiserbridge'),
            get_string("user_updation_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // Fill form with the existing values.

        if (!empty($defaultvalues)) {
            $mform->setDefault("course_enrollment", $defaultvalues["course_enrollment"]);
            $mform->setDefault("course_un_enrollment", $defaultvalues["course_un_enrollment"]);
            $mform->setDefault("user_creation", $defaultvalues["user_creation"]);
            $mform->setDefault("user_deletion", $defaultvalues["user_deletion"]);
            $mform->setDefault("course_creation", $defaultvalues["course_creation"]);
            $mform->setDefault("course_deletion", $defaultvalues["course_deletion"]);
            $mform->setDefault("user_updation", $defaultvalues["user_updation"]);
        }

        $mform->addElement(
            'html',
            '<div class="eb_connection_btns">
				<input type="submit" class="btn btn-primary eb_setting_btn" id="sync_submit" name="sync_submit" value="'
                . get_string("save", "local_edwiserbridge")
                . '"><input type="submit" class="btn btn-primary eb_setting_btn" id="sync_submit_continue"
                name="sync_submit_continue" value="' . get_string("save_cont", "local_edwiserbridge") . '">
			</div>'
        );
    }
}
