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

namespace core_course\form;

use core_course\course_request;
use core_course_category;
use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");

/**
 * A form for a user to request a course.
 *
 * @package    core_course
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class request_course extends moodleform {
    #[\Override]
    public function definition() {
        global $CFG, $DB, $USER;

        $mform =& $this->_form;

        if ($pending = $DB->get_records('course_request', ['requester' => $USER->id])) {
            $mform->addElement('header', 'pendinglist', get_string('coursespending'));
            $list = [];
            foreach ($pending as $cp) {
                $list[] = format_string($cp->fullname);
            }
            $list = implode(', ', $list);
            $mform->addElement('static', 'pendingcourses', get_string('courses'), $list);
        }

        $mform->addElement('header', 'coursedetails', get_string('courserequestdetails'));

        $mform->addElement(
            'text',
            'fullname',
            get_string('fullnamecourse'),
            ['maxlength' => \core_course\constants::FULLNAME_MAXIMUM_LENGTH, 'size' => 50],
        );
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);

        $mform->addElement(
            'text',
            'shortname',
            get_string('shortnamecourse'),
            ['maxlength' => \core_course\constants::SHORTNAME_MAXIMUM_LENGTH, 'size' => 20],
        );
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);

        if (empty($CFG->lockrequestcategory)) {
            $displaylist = core_course_category::make_categories_list('moodle/course:request');
            $mform->addElement('autocomplete', 'category', get_string('coursecategory'), $displaylist);
            $mform->addRule('category', null, 'required', null, 'client');
            $mform->setDefault('category', $CFG->defaultrequestcategory);
            $mform->addHelpButton('category', 'coursecategory');
        }

        $mform->addElement(
            'editor',
            'summary_editor',
            get_string('summary'),
            null,
            course_request::summary_editor_options(),
        );
        $mform->addHelpButton('summary_editor', 'coursesummary');
        $mform->setType('summary_editor', PARAM_RAW);

        $mform->addElement('header', 'requestreason', get_string('courserequestreason'));

        $mform->addElement(
            'textarea',
            'reason',
            get_string('courserequestsupport'),
            ['rows' => '15', 'cols' => '50'],
        );
        $mform->addRule('reason', get_string('missingreqreason'), 'required', null, 'client');
        $mform->setType('reason', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('requestcourse'));
    }

    #[\Override]
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        $foundcourses = null;
        $foundreqcourses = null;

        if (!empty($data['shortname'])) {
            $foundcourses = $DB->get_records('course', ['shortname' => $data['shortname']]);
            $foundreqcourses = $DB->get_records('course_request', ['shortname' => $data['shortname']]);
        }
        if (!empty($foundreqcourses)) {
            if (!empty($foundcourses)) {
                $foundcourses = array_merge($foundcourses, $foundreqcourses);
            } else {
                $foundcourses = $foundreqcourses;
            }
        }

        if (!empty($foundcourses)) {
            foreach ($foundcourses as $foundcourse) {
                if (!empty($foundcourse->requester)) {
                    $pending = 1;
                    $foundcoursenames[] = $foundcourse->fullname . ' [*]';
                } else {
                    $foundcoursenames[] = $foundcourse->fullname;
                }
            }
            $foundcoursenamestring = implode(',', $foundcoursenames);

            $errors['shortname'] = get_string('shortnametaken', '', $foundcoursenamestring);
            if (!empty($pending)) {
                $errors['shortname'] .= get_string('starpending');
            }
        }

        return $errors;
    }
}
