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
 * Manual enrolment plugin main library file.
 *
 * @package   enrol_manual
 * @copyright 2010 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class enrol_manual_plugin extends enrol_plugin {

    public function roles_protected() {
        // users may tweak the roles later
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // users with unenrol cap may unenrol other users manually manually
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // users with manage cap may tweak period and status
        return true;
    }

    /**
     * Returns link to manual enrol UI if exists.
     * Does the access control tests automatically.
     *
     * @param object $instance
     * @return moodle_url
     */
    public function get_manual_enrol_link($instance) {
        $name = $this->get_name();
        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }

        if ($instance->courseid == SITEID) {
            return NULL;
        }

        if (!enrol_is_enabled($name)) {
            return NULL;
        }

        $context = get_context_instance(CONTEXT_COURSE, $instance->courseid, MUST_EXIST);

        if (!has_capability('enrol/manual:manage', $context)) {
            return NULL;
        }

        return new moodle_url('/enrol/manual/manage.php', array('enrolid'=>$instance->id, 'id'=>$instance->courseid));
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_candidate_link($courseid) {
        global $DB;

        if (!has_capability('moodle/course:enrolconfig', get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST))) {
            return NULL;
        }

        if ($DB->record_exists('enrol', array('courseid'=>$courseid, 'enrol'=>'manual'))) {
            return NULL;
        }

        return new moodle_url('/enrol/manual/addinstance.php', array('sesskey'=>sesskey(), 'id'=>$courseid));
    }

    /**
     * Adds enrol instance UI to course edit form
     *
     * @param object $instance enrol instance or null if does not exist yet
     * @param MoodleQuickForm $mform
     * @param object $data
     * @param object $context context of existing course or parent category if course does not exist
     * @return void
     */
    public function course_edit_form($instance, MoodleQuickForm $mform, $data, $context) {

        $i = isset($instance->id) ? $instance->id : 0;
        $plugin = enrol_get_plugin('manual');
        $header = $plugin->get_instance_name($instance);
        $config = has_capability('enrol/manual:config', $context);

        $mform->addElement('header', 'enrol_manual_header_'.$i, $header);


        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'enrol_manual_status_'.$i, get_string('status', 'enrol_manual'), $options);
        $mform->setDefault('enrol_manual_status_'.$i, $this->get_config('status'));
        $mform->setAdvanced('enrol_manual_status_'.$i, $this->get_config('status_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_manual_status_'.$i);
        }


        $mform->addElement('duration', 'enrol_manual_enrolperiod_'.$i, get_string('defaultperiod', 'enrol_manual'), array('optional' => true, 'defaultunit' => 86400));
        $mform->setDefault('enrol_manual_enrolperiod_'.$i, $this->get_config('enrolperiod'));
        $mform->setAdvanced('enrol_manual_enrolperiod_'.$i, $this->get_config('enrolperiod_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_manual_enrolperiod_'.$i);
        } else {
            $mform->disabledIf('enrol_manual_enrolperiod_'.$i, 'enrol_manual_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        if ($instance) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $this->get_config('roleid'));
        }
        $mform->addElement('select', 'enrol_manual_roleid_'.$i, get_string('defaultrole', 'role'), $roles);
        $mform->setDefault('enrol_manual_roleid_'.$i, $this->get_config('roleid'));
        $mform->setAdvanced('enrol_manual_roleid_'.$i, $this->get_config('roleid_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_manual_roleid_'.$i);
        } else {
            $mform->disabledIf('enrol_manual_roleid_'.$i, 'enrol_manual_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        // now add all values from enrol table
        if ($instance) {
            foreach($instance as $key=>$val) {
                $data->{'enrol_manual_'.$key.'_'.$i} = $val;
            }
        }
    }

    /**
     * Called after updating/inserting course.
     *
     * @param bool $inserted true if course just inserted
     * @param object $course
     * @param object $data form data
     * @return void
     */
    public function course_updated($inserted, $course, $data) {
        global $DB;

        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        if (has_capability('enrol/manual:config', $context)) {
            if ($inserted) {
                if (isset($data->enrol_manual_status_0)) {
                    $fields = array('status'=>$data->enrol_manual_status_0);
                    if ($fields['status'] == ENROL_INSTANCE_ENABLED) {
                        $fields['enrolperiod'] = $data->enrol_manual_enrolperiod_0;
                        $fields['roleid']      = $data->enrol_manual_roleid_0;
                    } else {
                        $fields['enrolperiod'] = $this->get_config('enrolperiod');
                        $fields['roleid']      = $this->get_config('roleid');
                    }
                    $this->add_instance($course, $fields);
                }
            } else {
                $instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'manual'));
                foreach ($instances as $instance) {
                    $i = $instance->id;
                    if (isset($data->{'enrol_manual_status_'.$i})) {
                        $instance->status       = $data->{'enrol_manual_status_'.$i};
                        $instance->timemodified = time();
                        if ($instance->status == ENROL_INSTANCE_ENABLED) {
                            $instance->enrolperiod = $data->{'enrol_manual_enrolperiod_'.$i};
                            $instance->roleid      = $data->{'enrol_manual_roleid_'.$i};
                        }
                        $DB->update_record('enrol', $instance);
                    }
                }
            }

        } else {
            if ($inserted) {
                if ($this->get_config('defaultenrol')) {
                    $this->add_default_instance($course);
                }
            } else {
                // bad luck, user can not change anything
            }
        }
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = array('status'=>$this->get_config('status'), 'enrolperiod'=>$this->get_config('enrolperiod', 0), 'roleid'=>$this->get_config('roleid', 0));
        return $this->add_instance($course, $fields);
    }

    public function cron() {
        // TODO: deal with $CFG->longtimenosee
    }
}

