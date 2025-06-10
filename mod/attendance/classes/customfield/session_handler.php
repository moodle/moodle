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
 * Handler for custom fields on sessions.
 *
 * @package     mod_attendance
 * @copyright   2022 Dan Marsden <dan@danmarsden.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\customfield;

use core_customfield\api;
use core_customfield\field_controller;

/**
 * Handler for custom fields on sessions, based on Daniel Neis Araujo's modcustomfields class.
 *
 * @package     mod_attendance
 * @copyright   2022 Dan Marsden <dan@danmarsden.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class session_handler extends \core_customfield\handler {
    /** @var session_handler  */
    static protected $singleton;
    /** @var \context */
    protected $parentcontext;
    /** @var int Field is visible to everybody */
    const VISIBLETOALL = 2;
    /** @var int Field is only visible to teachers */
    const VISIBLETOTEACHERS = 1;
    /** @var int Field is not displayed in the course listing */
    const NOTVISIBLE = 0;

    /**
     * Returns a singleton
     *
     * @param int $itemid
     * @return session_handler
     */
    public static function create(int $itemid = 0) : \core_customfield\handler {
        if (static::$singleton === null) {
            self::$singleton = new static(0);
        }
        return self::$singleton;
    }

    /**
     * Run reset code after unit tests to reset the singleton usage.
     */
    public static function reset_caches(): void {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('This feature is only intended for use in unit tests');
        }

        static::$singleton = null;
    }

    /**
     * The current user can configure custom fields on this component.
     *
     * @return bool true if the current can configure custom fields, false otherwise
     */
    public function can_configure() : bool {
        return has_capability('moodle/course:configurecustomfields', $this->get_configuration_context());
    }

    /**
     * The current user can edit custom fields on the given course.
     *
     * @param field_controller $field
     * @param int $instanceid id of the course to test edit permission
     * @return bool true if the current can edit custom fields, false otherwise
     */
    public function can_edit(field_controller $field, int $instanceid = 0) : bool {
        global $PAGE;
        if ($instanceid) {
            $context = $this->get_instance_context($instanceid);
            return (!$field->get_configdata_property('locked') ||
                    has_capability('moodle/course:changelockedcustomfields', $context));
        } else {
            $context = $PAGE->context->get_course_context();
            return (!$field->get_configdata_property('locked') ||
                guess_if_creator_will_have_course_capability('moodle/course:changelockedcustomfields', $context));
        }
    }

    /**
     * The current user can view custom fields on the given course.
     *
     * @param field_controller $field
     * @param int $instanceid id of the course to test edit permission
     * @return bool true if the current can edit custom fields, false otherwise
     */
    public function can_view(field_controller $field, int $instanceid) : bool {
        $visibility = $field->get_configdata_property('visibility');
        if ($visibility == self::NOTVISIBLE) {
            return false;
        } else if ($visibility == self::VISIBLETOTEACHERS) {
            return has_capability('moodle/course:update', $this->get_instance_context($instanceid));
        } else {
            return true;
        }
    }

    /**
     * Context that should be used for new categories created by this handler
     *
     * @return \context the context for configuration
     */
    public function get_configuration_context() : \context {
        return \context_system::instance();
    }

    /**
     * URL for configuration of the fields on this handler.
     *
     * @return \moodle_url The URL to configure custom fields for this component
     */
    public function get_configuration_url() : \moodle_url {
        return new \moodle_url('/mod/attendance/customfield.php');
    }

    /**
     * Returns the context for the data associated with the given instanceid.
     *
     * @param int $instanceid id of the record to get the context for
     * @return \context the context for the given record
     */
    public function get_instance_context(int $instanceid = 0) : \context {
        global $DB;
        if ($instanceid > 0) {
            $attendanceid = $DB->get_field('attendance_sessions', 'attendanceid', ['id' => $instanceid]);
            $cm = get_coursemodule_from_instance('attendance', $attendanceid);
            return \context_module::instance($cm->id);
        } else {
            return \context_system::instance();
        }
    }

    /**
     * Allows to add custom controls to the field configuration form that will be saved in configdata
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform) {
        $mform->addElement('header', 'session_handler_header', get_string('customfieldsettings', 'core_course'));
        $mform->setExpanded('session_handler_header', true);

        // If field is locked.
        $mform->addElement('selectyesno', 'configdata[locked]', get_string('customfield_islocked', 'core_course'));
        $mform->addHelpButton('configdata[locked]', 'customfield_islocked', 'core_course');

        // Field data visibility.
        $visibilityoptions = [self::VISIBLETOALL => get_string('customfield_visibletoall', 'core_course'),
            self::VISIBLETOTEACHERS => get_string('customfield_visibletoteachers', 'core_course'),
            self::NOTVISIBLE => get_string('customfield_notvisible', 'core_course')];
        $mform->addElement('select', 'configdata[visibility]', get_string('customfield_visibility', 'core_course'),
            $visibilityoptions);
        $mform->addHelpButton('configdata[visibility]', 'customfield_visibility', 'core_course');
    }

    /**
     * Attendance session form a bit non-standard, use custom function to populate array of existing data.
     *
     * Example:
     *   $instance = $DB->get_record(...);
     *   // .... prepare editor, filemanager, add tags, etc.
     *   $handler->instance_form_before_set_data($instance);
     *   $form->set_data($instance);
     *
     * @param array $instance the instance that has custom fields, if 'id' attribute is present the custom
     *    fields for this instance will be added, otherwise the default values will be added.
     * @return array
     */
    public function instance_form_before_set_data_array(array $instance) {
        $instanceid = !empty($instance['id']) ? $instance['id'] : 0;
        $fields = api::get_instance_fields_data($this->get_editable_fields($instanceid), $instanceid);

        foreach ($fields as $formfield) {
            foreach ($fields as $formfield) {
                $instance[$formfield->get_form_element_name()] = $formfield->get_value();
            }
        }
        return $instance;
    }
    /**
     * Get list of custom fields that contain data in this attendance activity (hides fields that do not store anything)
     *
     * @param int $instanceid id of the record to get the context for
     * @return array
     */
    public function get_fields_for_display($instanceid) {
        global $DB;
        $categories = $this->get_categories_with_fields();
        $context = $this->get_instance_context($instanceid);
        $sql = "SELECT distinct fieldid FROM {customfield_data} where contextid = ?";
        $fieldswithdata = $DB->get_records_sql($sql, [$context->id]);
        $fields = [];
        foreach ($categories as $category) {
            foreach ($category->get_fields() as $field) {
                if ($this->can_view($field, $instanceid) && in_array($field->get('id'), array_keys($fieldswithdata))) {
                    $fields[$field->get('id')] = $field;
                }
            }
        }
        return $fields;
    }

    /**
     * Get raw data associated with all fields current user can view or edit
     *
     * @param int $activityid
     * @return array
     */
    public function get_instance_data_for_backup_by_activity(int $activityid) : array {
        global $DB;
        $finalfields = [];
        $sessions = $DB->get_records('attendance_sessions', ['attendanceid' => $activityid]);
        if (empty($sessions)) {
            return $finalfields;
        }
        foreach ($sessions as $session) {
            $data = $this->get_instance_data($session->id, true);
            foreach ($data as $d) {
                if ($d->get('id') && $this->can_backup($d->get_field(), $session->id)) {
                    $finalfields[] = [
                        'id' => $d->get('id'),
                        'sessionid' => $session->id,
                        'shortname' => $d->get_field()->get('shortname'),
                        'type' => $d->get_field()->get('type'),
                        'value' => $d->get_value(),
                        'valueformat' => $d->get('valueformat')];
                }
            }
        }
        return $finalfields;
    }

    /**
     * Creates or updates custom field data.
     *
     * @param \restore_task $task
     * @param array $data
     */
    public function restore_instance_data_from_backup(\restore_task $task, array $data) {
        $editablefields = $this->get_editable_fields($data['sessionid']);
        $records = api::get_instance_fields_data($editablefields, $data['sessionid']);
        $target = $task->get_target();
        $override = ($target != \backup::TARGET_CURRENT_ADDING && $target != \backup::TARGET_EXISTING_ADDING);

        foreach ($records as $d) {
            $field = $d->get_field();
            if ($field->get('shortname') === $data['shortname'] && $field->get('type') === $data['type']) {
                if (!$d->get('id') || $override) {
                    $d->set($d->datafield(), $data['value']);
                    $d->set('value', $data['value']);
                    $d->set('valueformat', $data['valueformat']);
                    $d->set('contextid', $task->get_contextid());
                    $d->save();
                }
                return;
            }
        }
    }
}

