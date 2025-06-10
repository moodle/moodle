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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\form;

use block_lsuxe\controllers\form_controller;
use block_lsuxe\form\groupform_autocomplete;
use block_lsuxe\models;
use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/blocks/lsuxe/lib.php');
require_once($CFG->libdir . '/formslib.php');


\MoodleQuickForm::registerElementType(
    'groupform_autocomplete',
    $CFG->dirroot . '/blocks/lsuxe/classes/form/groupform_autocomplete.php',
    '\\block_lsuxe\\form\\groupform_autocomplete'
);

class mappings_form extends \moodleform {

    /*
     * Moodle form definition
     */
    public function definition() {
        global $CFG;
        $mappingsctrl = new form_controller("moodles");
        $helpers = new \lsuxe_helpers();
        $moodleinstances = $mappingsctrl->get_records_by_prop("url", true);
        $formupdating = false;
        if (isset($this->_customdata->id)) {
            $formupdating = true;
        }

        $enableautocomplete = (bool)get_config('moodle', "block_lsuxe_enable_form_auto");
        $enabledesttest = (bool)get_config('moodle', "block_lsuxe_enable_dest_test");
        // Get data for the form.
        $mform =& $this->_form;

        // For styling purposes.
        $mform->addElement('html', '<span class="lsuxe_form_container">');
        // --------------------------------
        // Course Shortname.
        if ($enableautocomplete) {
            // USE THE AUTOCOMPLETE FEATURES FOR COURSE AND GROUP.
            $options = array('multiple' => false);
            $courseselect = $mform->addElement(
                'course',
                'srccourseshortname',
                get_string('srccourseshortname', 'block_lsuxe'),
                $options
            );

            if (isset($this->_customdata->shortname)) {
                $courseselect->setValue($this->_customdata->courseid);
            }

            // ----------------------------------------------------------------
            // Select Source Group Name, there could be MULTIPLE groups to choose
            // from. ** NOTE ** this must also match in the form_events.js code
            // where the group form resets.
            if (isset($this->_customdata->groupname)) {
                $defaultgroupselect = array($this->_customdata->groupid => $this->_customdata->groupname);
            } else {
                $defaultgroupselect = array("Please search for a course first");
            }

            // --------------------------------
            $mform->addElement(
                'select',
                'srccoursegroupnameselect',
                get_string('srccoursegroupname', 'block_lsuxe'),
                $defaultgroupselect
            );

        } else {
            // MANUAL ENTER THE COURSE AND GROUP INTO THE FIELDS.
            $mform->addElement(
                'text',
                'srccourseshortname',
                get_string('srccourseshortname', 'block_lsuxe')
            );
            $mform->setType(
                'srccourseshortname',
                PARAM_TEXT
            );

            if (isset($this->_customdata->shortname)) {
                $mform->setDefault('srccourseshortname', $this->_customdata->shortname);
            }

            // --------------------------------
            $mform->addElement(
                'text',
                'srccoursegroupname',
                get_string('srccoursegroupname', 'block_lsuxe')
            );

            $mform->setType(
                'srccoursegroupname',
                PARAM_TEXT
            );
            if (isset($this->_customdata->groupname)) {
                $mform->setDefault('srccoursegroupname', $this->_customdata->groupname);
            }
        }

        // --------------------------------
        $mform->addElement(
            'date_selector',
            'starttime',
            get_string('coursestarttime', 'block_lsuxe')
        );
        if (isset($this->_customdata->starttime) && $this->_customdata->starttime != "0") {
            $mform->setDefault('starttime', $this->_customdata->starttime);
        }

        $mform->addElement(
            'date_selector',
            'endtime',
            get_string('courseendtime', 'block_lsuxe')
        );
        if (isset($this->_customdata->endtime) && $this->_customdata->endtime != "0") {
            $mform->setDefault('endtime', $this->_customdata->endtime);
        }

        // --------------------------------
        // Moodle Instance.
        $mform->addElement(
            'select',
            'available_moodle_instances',
            get_string('destmoodleinstance', 'block_lsuxe'),
            $moodleinstances
        );
        if (isset($this->_customdata->destgroupprefix)) {
            $mform->setDefault('available_moodle_instances', $this->_customdata->destmoodleid);
        }

        // --------------------------------
        // Auth Method.
        $authmethods = $helpers->config_to_array('block_lsuxe_xe_auth_method', "mirror");
        $authselect = $mform->addElement(
            'select',
            'authmethod',
            get_string('authmethod', 'block_lsuxe'),
            $authmethods,
            []
        );
        if (isset($this->_customdata->authmethod)) {
            $authselect->setSelected($this->_customdata->authmethod);
        }

        if ($enableautocomplete) {
            // --------------------------------
            // Destination Course Group name autocomplete.
            $mform->addElement('html', '<span class="xe_dest_sn_wrap">');

            $destcourseselect = $mform->addElement(
                'groupform_autocomplete',
                'destcourseshortname',
                get_string('destcourseshortname', 'block_lsuxe'),
                ['class' => 'xe_dest_sn']
            );

            if (isset($this->_customdata->destcourseshortname)) {
                $destcourseselect->setValue(
                    array(
                        $this->_customdata->destcourseid,
                        $this->_customdata->destcourseshortname
                    )
                );
            }
            $mform->addElement('html', '</span>');

            // --------------------------------
            // Destination Course Group name manual entry.
            $mform->addElement(
                'text',
                'destcoursegroupname',
                get_string('destcoursegroupname', 'block_lsuxe')
            );
            $mform->setType(
                'destcoursegroupname',
                PARAM_TEXT
            );
            if (isset($this->_customdata->destgroupprefix)) {
                $mform->setDefault('destcoursegroupname', $this->_customdata->destgroupprefix);
            }

            $mform->addElement('hidden', 'srccoursegroupname');
            $mform->setType('srccoursegroupname', PARAM_TEXT);
            if (isset($this->_customdata->groupname)) {
                $mform->setDefault('srccoursegroupname', $this->_customdata->groupname);
            } else {
                $mform->setDefault('srccoursegroupname', "");
            }

        } else {
            // --------------------------------
            // Destination Course Group name autocomplete.
            $mform->addElement(
                'text',
                'destcourseshortname',
                get_string('destcourseshortname', 'block_lsuxe'),
                ['class' => 'xe_dest_sn']
            );
            $mform->setType(
                'destcourseshortname',
                PARAM_TEXT
            );
            if (isset($this->_customdata->destcourseshortname)) {
                $mform->setDefault('destcourseshortname', $this->_customdata->destcourseshortname);
            }

            // --------------------------------
            // Destination Course Group name manual entry.
            $mform->addElement(
                'text',
                'destcoursegroupname',
                get_string('destcoursegroupname', 'block_lsuxe')
            );
            $mform->setType(
                'destcoursegroupname',
                PARAM_TEXT
            );
            if (isset($this->_customdata->destgroupprefix)) {
                $mform->setDefault('destcoursegroupname', $this->_customdata->destgroupprefix);
            }
        }

        // --------------------------------
        // Interval.
        $intervals = $helpers->config_to_array('block_lsuxe_interval_list');
        $select = $mform->addElement(
            'select',
            'defaultupdateinterval',
            get_string('courseupdateinterval', 'block_lsuxe'),
            $intervals,
            []
        );
        if (isset($this->_customdata->updateinterval)) {
            $select->setSelected($this->_customdata->updateinterval);
        }

        // --------------------------------
        // Hidden Elements.

        // Moodle removes any select items that are added via AJAX. In order to save this
        // the value will be stored in this hidden input.
        $mform->addElement('hidden', 'srccourseid');
        $mform->setType('srccourseid', PARAM_INT);
        if (isset($this->_customdata->groupname)) {
            $mform->setDefault('srccourseid', $this->_customdata->courseid);
        } else {
            $mform->setDefault('srccourseid', "");
        }

        // --------------------------------
        $mform->addElement('hidden', 'srccoursegroupid');
        $mform->setType('srccoursegroupid', PARAM_INT);
        if (isset($this->_customdata->groupname)) {
            $mform->setDefault('srccoursegroupid', $this->_customdata->groupid);
        } else {
            $mform->setDefault('srccoursegroupid', "");
        }

        // --------------------------------
        $mform->addElement('hidden', 'destcourseid');
        $mform->setType('destcourseid', PARAM_INT);
        if (isset($this->_customdata->destcourseid)) {
            $mform->setDefault('destcourseid', $this->_customdata->destcourseid);
        } else {
            $mform->setDefault('destcourseid', "");
        }

        // For Page control list or view form.
        $mform->addElement('hidden', 'vform');
        $mform->setType('vform', PARAM_INT);
        $mform->setConstant('vform', 1);

        // Record id which is used to update.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        if ($formupdating) {
            $mform->setDefault('id', $this->_customdata->id);
        }

        // --------------------------------
        // Buttons!
        // If using the autocomplete we don't need to verify the source and dest as it's an
        // autocomplete feature.
        // The button can either be Save or Update for the submit action.
        $thissubmitbutton = $formupdating ? get_string('savechanges', 'block_lsuxe') : get_string('savemapping', 'block_lsuxe');

        if ($enableautocomplete) {
            $mform->addElement('submit', 'send', $thissubmitbutton);
        } else {
            $buttons = [
                $mform->createElement('submit', 'send', $thissubmitbutton),
                $mform->createElement('button', 'verifysource', get_string('verifysrccourse', 'block_lsuxe'))
            ];
            if ($enabledesttest) {
                $buttons[] = $mform->createElement('button', 'verifydest', get_string('verifydestcourse', 'block_lsuxe'));
            }
            $mform->addGroup($buttons, 'actions', '&nbsp;', [' '], false);
        }

        $mform->addElement('html', '</span>');
    }

    /**
     * Moodle form validation
     *
     * @param array $data  Data from the form.
     * @param array $files Any files in the form.
     *
     * @return array Errors returned for the required elements.
     */
    public function validation($data, $files) {
        $errors = [];
        $fuzzy = new \block_lsuxe\models\xemixed();

        $enableautocomplete = (bool)get_config('moodle', "block_lsuxe_enable_form_auto");

        // Check that we have at least one recipient.
        if (empty($data['srccourseshortname'])) {
            $errors['srccourseshortname'] = get_string('srccourseshortnameverify', 'block_lsuxe');
        } else {
            if (!$fuzzy->check_course_exists($data['srccourseshortname'], $enableautocomplete)) {
                $errors['srccourseshortname'] = get_string('mappingsformcourseerror', 'block_lsuxe');
            }
        }

        if (empty($data['srccoursegroupname'])) {
            $errors['srccoursegroupname'] = get_string('srccoursegroupnameverify', 'block_lsuxe');
        } else {
            if (!$fuzzy->check_group_exists($data['srccoursegroupname'])) {
                $errors['srccoursegroupname'] = get_string('mappingsformgrouperror', 'block_lsuxe');
            }
        }

        if (empty($data['destcourseshortname'])) {
            $errors['destcourseshortname'] = get_string('destcourseshortnameverify', 'block_lsuxe');
        }

        if (empty($data['destcoursegroupname'])) {
            $errors['destcoursegroupname'] = get_string('destcoursegroupnameverify', 'block_lsuxe');
        }

        return $errors;
    }
}
