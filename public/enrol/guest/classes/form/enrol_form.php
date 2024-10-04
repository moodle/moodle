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

declare(strict_types=1);

namespace enrol_guest\form;

use core\context\course as context_course;
use core_form\dynamic_form;
use core_text;
use moodle_url;

/**
 * Form for entering password for guest enrolment
 *
 * @package    enrol_guest
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_form extends dynamic_form {
    /** @var \stdClass */
    protected $instance;

    /**
     * Returns the instance of the enrolment method
     *
     * @throws \moodle_exception
     * @return \stdClass
     */
    protected function get_instance(): \stdClass {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');
        if ($this->instance === null) {
            $courseid = $this->optional_param('id', 0, PARAM_INT);
            $instanceid = $this->optional_param('instance', 0, PARAM_INT);
            // We need enrol_get_instances() to validate that the enrolment method is enabled.
            $instances = enrol_get_instances($courseid, true);
            if (empty($instances[$instanceid]) || $instances[$instanceid]->enrol !== 'guest') {
                throw new \moodle_exception('invalidenrolinstance', 'enrol');
            }
            $this->instance = $instances[$instanceid] ?? null;
        }
        return $this->instance;
    }

    #[\Override]
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('password', 'guestpassword', get_string('password', 'enrol_guest'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
    }

    #[\Override]
    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        $instance = $this->get_instance();

        if ($instance->password !== '') {
            if ($data['guestpassword'] !== $instance->password) {
                $plugin = enrol_get_plugin('guest');
                if ($plugin->get_config('showhint')) {
                    $hint = core_text::substr($instance->password, 0, 1);
                    $errors['guestpassword'] = get_string('passwordinvalidhint', 'enrol_guest', $hint);
                } else {
                    $errors['guestpassword'] = get_string('passwordinvalid', 'enrol_guest');
                }
            }
        }

        return $errors;
    }

    #[\Override]
    protected function check_access_for_dynamic_submission(): void {
        global $USER, $CFG;
        $courseid = $this->get_instance()->courseid;
        $course = get_course($courseid);
        $context = context_course::instance($this->get_instance()->courseid);
        if (!\core_course_category::can_view_course_info($course) && !is_enrolled($context, $USER, '', true)) {
            throw new \moodle_exception('coursehidden', '', $CFG->wwwroot . '/');
        }
    }

    #[\Override]
    protected function get_context_for_dynamic_submission(): \context {
        // This form is used for users who are not yet enrolled in the course and do not have access to the course.
        // For the purpose of permission checks they must be able to access the course category for this course.
        return context_course::instance($this->get_instance()->courseid)->get_parent_context();
    }

    #[\Override]
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $instance = $this->get_instance();
        return new moodle_url('/enrol/index.php', ['id' => $instance->courseid, 'instance' => $instance->id]);
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * Enrols the user in the course and returns the URL to redirect to
     *
     * @return string
     */
    public function process_dynamic_submission() {
        global $USER, $CFG, $SESSION;

        /** @var \enrol_guest_plugin $enrol */
        $enrol = enrol_get_plugin('guest');
        $instance = $this->get_instance();

        $enrol->mark_user_as_enrolled($instance, $this->get_data()->guestpassword);

        // Go to the originally requested page.
        if (!empty($SESSION->wantsurl)) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            require_once($CFG->dirroot . '/course/lib.php');
            $destination = course_get_url($instance->courseid);
        }
        return $destination;
    }

    #[\Override]
    public function set_data_for_dynamic_submission(): void {
        $instance = $this->get_instance();
        $this->set_data(['id' => $instance->courseid, 'instance' => $instance->id]);
    }
}
