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
 * Course copy form class.
 *
 * @package     core_backup
 * @copyright   2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup\output;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Course copy form class.
 *
 * @package     core_backup
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class copy_form extends \moodleform {

    /**
     * Build form for the course copy settings.
     *
     * {@inheritDoc}
     * @see \moodleform::definition()
     */
    public function definition() {
        global $CFG, $OUTPUT, $USER;

        $mform = $this->_form;
        $course = $this->_customdata['course'];
        $coursecontext = \context_course::instance($course->id);
        $courseconfig = get_config('moodlecourse');
        $returnto = $this->_customdata['returnto'];
        $returnurl = $this->_customdata['returnurl'];

        if (empty($course->category)) {
            $course->category = $course->categoryid;
        }

        // Course ID.
        $mform->addElement('hidden', 'courseid', $course->id);
        $mform->setType('courseid', PARAM_INT);

        // Return to type.
        $mform->addElement('hidden', 'returnto', null);
        $mform->setType('returnto', PARAM_ALPHANUM);
        $mform->setConstant('returnto', $returnto);

        // Notifications of current copies.
        $copies = \core_backup\copy\copy::get_copies($USER->id, $course->id);
        if (!empty($copies)) {
            $progresslink = new \moodle_url('/backup/copyprogress.php?', array('id' => $course->id));
            $notificationmsg = get_string('copiesinprogress', 'backup', $progresslink->out());
            $notification = $OUTPUT->notification($notificationmsg, 'notifymessage');
            $mform->addElement('html', $notification);
        }

        // Return to URL.
        $mform->addElement('hidden', 'returnurl', null);
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setConstant('returnurl', $returnurl);

        // Form heading.
        $mform->addElement('html', \html_writer::div(get_string('copycoursedesc', 'backup'), 'form-description mb-3'));

        // Course fullname.
        $mform->addElement('text', 'fullname', get_string('fullnamecourse'), 'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);

        // Course shortname.
        $mform->addElement('text', 'shortname', get_string('shortnamecourse'), 'maxlength="100" size="20"');
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);

        // Course category.
        $displaylist = \core_course_category::make_categories_list(\core_course\management\helper::get_course_copy_capabilities());
        if (!isset($displaylist[$course->category])) {
            // Always keep current category.
            $displaylist[$course->category] = \core_course_category::get($course->category, MUST_EXIST, true)->get_formatted_name();
        }
        $mform->addElement('autocomplete', 'category', get_string('coursecategory'), $displaylist);
        $mform->addHelpButton('category', 'coursecategory');

        // Course visibility.
        $choices = array();
        $choices['0'] = get_string('hide');
        $choices['1'] = get_string('show');
        $mform->addElement('select', 'visible', get_string('coursevisibility'), $choices);
        $mform->addHelpButton('visible', 'coursevisibility');
        $mform->setDefault('visible', $courseconfig->visible);
        if (!has_capability('moodle/course:visibility', $coursecontext)) {
            $mform->hardFreeze('visible');
            $mform->setConstant('visible', $course->visible);
        }

        // Course start date.
        $mform->addElement('date_time_selector', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        $date = (new \DateTime())->setTimestamp(usergetmidnight(time()));
        $date->modify('+1 day');
        $mform->setDefault('startdate', $date->getTimestamp());

        // Course enddate.
        $mform->addElement('date_time_selector', 'enddate', get_string('enddate'), array('optional' => true));
        $mform->addHelpButton('enddate', 'enddate');

        if (!empty($CFG->enablecourserelativedates)) {
            $attributes = [
                'aria-describedby' => 'relativedatesmode_warning'
            ];
            if (!empty($course->id)) {
                $attributes['disabled'] = true;
            }
            $relativeoptions = [
                0 => get_string('no'),
                1 => get_string('yes'),
            ];
            $relativedatesmodegroup = [];
            $relativedatesmodegroup[] = $mform->createElement('select', 'relativedatesmode', get_string('relativedatesmode'),
                $relativeoptions, $attributes);
            $relativedatesmodegroup[] = $mform->createElement('html', \html_writer::span(get_string('relativedatesmode_warning'),
                '', ['id' => 'relativedatesmode_warning']));
            $mform->addGroup($relativedatesmodegroup, 'relativedatesmodegroup', get_string('relativedatesmode'), null, false);
            $mform->addHelpButton('relativedatesmodegroup', 'relativedatesmode');
        }

        // Course ID number (default to the current course ID number; blank for users who can't change ID numbers).
        $mform->addElement('text', 'idnumber', get_string('idnumbercourse'), 'maxlength="100"  size="10"');
        $mform->setDefault('idnumber', $course->idnumber);
        $mform->addHelpButton('idnumber', 'idnumbercourse');
        $mform->setType('idnumber', PARAM_RAW);
        if (!has_capability('moodle/course:changeidnumber', $coursecontext)) {
            $mform->hardFreeze('idnumber');
            $mform->setConstant('idnumber', '');
        }

        // Keep source course user data.
        $mform->addElement('select', 'userdata', get_string('userdata', 'backup'),
            [0 => get_string('no'), 1 => get_string('yes')]);
        $mform->setDefault('userdata', 0);
        $mform->addHelpButton('userdata', 'userdata', 'backup');

        $requiredcapabilities = array(
            'moodle/restore:createuser', 'moodle/backup:userinfo', 'moodle/restore:userinfo'
        );
        if (!has_all_capabilities($requiredcapabilities, $coursecontext)) {
            $mform->hardFreeze('userdata');
            $mform->setConstant('userdata', 0);
        }

        // Keep manual enrolments.
        // Only get roles actually used in this course.
        $roles = role_fix_names(get_roles_used_in_context($coursecontext, false), $coursecontext);

        // Only add the option if there are roles in this course.
        if (!empty($roles) && has_capability('moodle/restore:createuser', $coursecontext)) {
            $rolearray = array();
            foreach ($roles as $role) {
                $roleid = 'role_' . $role->id;
                $rolearray[] = $mform->createElement('advcheckbox', $roleid,
                    $role->localname, '', array('group' => 2), array(0, $role->id));
            }

            $mform->addGroup($rolearray, 'rolearray', get_string('keptroles', 'backup'), ' ', false);
            $mform->addHelpButton('rolearray', 'keptroles', 'backup');
            $this->add_checkbox_controller(2);
        }

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitreturn', get_string('copyreturn', 'backup'));
        $buttonarray[] = $mform->createElement('submit', 'submitdisplay', get_string('copyview', 'backup'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);

    }

    /**
     * Validation of the form.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        // Add field validation check for duplicate shortname.
        $courseshortname = $DB->get_record('course', array('shortname' => $data['shortname']), 'fullname', IGNORE_MULTIPLE);
        if ($courseshortname) {
            $errors['shortname'] = get_string('shortnametaken', '', $courseshortname->fullname);
        }

        // Add field validation check for duplicate idnumber.
        if (!empty($data['idnumber'])) {
            $courseidnumber = $DB->get_record('course', array('idnumber' => $data['idnumber']), 'fullname', IGNORE_MULTIPLE);
            if ($courseidnumber) {
                $errors['idnumber'] = get_string('courseidnumbertaken', 'error', $courseidnumber->fullname);
            }
        }

        // Validate the dates (make sure end isn't greater than start).
        if ($errorcode = course_validate_dates($data)) {
            $errors['enddate'] = get_string($errorcode, 'error');
        }

        return $errors;
    }

}
