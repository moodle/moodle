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

namespace enrol_self\form;

use core\context\course as context_course;
use core\context\system as context_system;
use core_form\dynamic_form;
use core_text;
use html_writer;
use moodle_url;

/**
 * Form for entering password for self enrolment
 *
 * @package    enrol_self
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_form extends dynamic_form {
    /** @var \stdClass */
    protected $instance;
    /** @var \enrol_self_plugin */
    protected $plugin = null;

    /**
     * Returns the enrolment method
     *
     * @return \enrol_self_plugin
     */
    protected function get_plugin(): \enrol_self_plugin {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');
        if ($this->plugin === null) {
            $this->plugin = enrol_get_plugin('self');
        }
        return $this->plugin;
    }

    /**
     * Returns the instance of the enrolment method
     *
     * @return \stdClass
     */
    protected function get_instance(): \stdClass {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');
        if ($this->instance === null) {
            // Method enrol_get_instances() will also validate that the enrolment method and the instance are enabled.
            $courseid = $this->optional_param('id', 0, PARAM_INT);
            $instanceid = $this->optional_param('instance', 0, PARAM_INT);
            $instances = enrol_get_instances($courseid, true);
            $this->instance = $instances[$instanceid] ?? null;
            if (empty($this->instance) || $this->instance->enrol !== 'self') {
                throw new \moodle_exception('invalidenrolinstance', 'enrol');
            }
        }
        return $this->instance;
    }

    #[\Override]
    public function definition() {
        global $USER, $OUTPUT, $CFG;

        $mform = $this->_form;

        $mform->addElement('password', 'enrolpassword', get_string('password', 'enrol_self'));

        // Display keyholders - list of users who have 'enrol/self:holdkey' capability.
        $context = context_course::instance($this->instance->courseid);
        $userfieldsapi = \core_user\fields::for_userpic();
        $ufields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $keyholders = get_users_by_capability($context, 'enrol/self:holdkey', $ufields);
        $keyholdercount = 0;
        foreach ($keyholders as $keyholder) {
            $keyholdercount++;
            if ($keyholdercount === 1) {
                $mform->addElement('static', 'keyholder', '', get_string('keyholder', 'enrol_self'));
            }
            if ($USER->id == $keyholder->id
                    || has_capability('moodle/user:viewdetails', context_system::instance())
                    || has_coursecontact_role($keyholder->id)) {
                $profileurl = new moodle_url('/user/profile.php', ['id' => $keyholder->id, 'course' => $this->instance->courseid]);
                $profilelink = html_writer::link($profileurl, fullname($keyholder));
            } else {
                $profilelink = fullname($keyholder);
            }
            $profilepic = $OUTPUT->user_picture($keyholder, ['size' => 35, 'courseid' => $this->instance->courseid]);
            $mform->addElement('static', 'keyholder' . $keyholdercount, '', $profilepic . $profilelink);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
    }

    #[\Override]
    public function validation($data, $files) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/enrol/self/locallib.php');

        $errors = parent::validation($data, $files);
        $instance = $this->get_instance();
        if ($data['enrolpassword'] !== $instance->password) {
            if ($instance->customint1) {
                // Check group enrolment key.
                if (!enrol_self_check_group_enrolment_key($instance->courseid, $data['enrolpassword'])) {
                    // We can not hint because there are probably multiple passwords.
                    $errors['enrolpassword'] = get_string('passwordinvalid', 'enrol_self');
                }
            } else {
                $plugin = enrol_get_plugin('self');
                if ($plugin->get_config('showhint')) {
                    $hint = core_text::substr($instance->password, 0, 1);
                    $errors['enrolpassword'] = get_string('passwordinvalidhint', 'enrol_self', $hint);
                } else {
                    $errors['enrolpassword'] = get_string('passwordinvalid', 'enrol_self');
                }
            }
        }

        return $errors;
    }

    #[\Override]
    protected function check_access_for_dynamic_submission(): void {
        global $USER, $CFG;
        $instance = $this->get_instance();
        $courseid = $instance->courseid;
        $course = get_course($courseid);
        $context = context_course::instance($instance->courseid);
        if (!\core_course_category::can_view_course_info($course) && !is_enrolled($context, $USER, '', true)) {
            throw new \moodle_exception('coursehidden', '', $CFG->wwwroot . '/');
        }
        if (isguestuser()) {
            throw new \moodle_exception('noguestaccess', 'enrol');
        }
        $canselfenrol = $this->get_plugin()->can_self_enrol($instance);
        if ($canselfenrol !== true) {
            throw new \moodle_exception($canselfenrol);
        }
        if (!$instance->password) {
            throw new \moodle_exception('nopassword', 'enrol_self');
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
        global $CFG, $SESSION;
        $this->get_plugin()->enrol_self($this->get_instance(), $this->get_data());

        // Go to the originally requested page.
        if (!empty($SESSION->wantsurl)) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            require_once($CFG->dirroot . '/course/lib.php');
            $destination = course_get_url($this->get_instance()->courseid);
        }
        return $destination;
    }

    #[\Override]
    public function set_data_for_dynamic_submission(): void {
        $instance = $this->get_instance();
        $this->set_data(['id' => $instance->courseid, 'instance' => $instance->id]);
    }
}
