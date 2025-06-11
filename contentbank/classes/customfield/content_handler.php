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

namespace core_contentbank\customfield;

use core_customfield\api;
use core_customfield\field_controller;

/**
 * Content handler for content bank custom fields
 *
 * @package   core_contentbank
 * @copyright 2024 Daniel Neis Araujo <daniel@adapta.online>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_handler extends \core_customfield\handler {

    /**
     * @var content_handler
     */
    static protected $singleton;

    /**
     * @var \context
     */
    protected $parentcontext;

    /** @var int Field is displayed in the content bank edit page, visible to everybody */
    const VISIBLETOALL = 2;
    /** @var int Field is not displayed in the content bank edit page */
    const NOTVISIBLE = 0;

    /**
     * Returns a singleton
     *
     * @param int $itemid
     * @return \core_contentbank\customfield\content_handler
     */
    public static function create(int $itemid = 0): \core_contentbank\customfield\content_handler {
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
    public function can_configure(): bool {
        return has_capability('moodle/contentbank:configurecustomfields', $this->get_configuration_context());
    }

    /**
     * The current user can edit custom fields on the content bank.
     *
     * @param field_controller $field
     * @param int $instanceid id of the course to test edit permission
     * @return bool true if the current can edit custom fields, false otherwise
     */
    public function can_edit(field_controller $field, int $instanceid = 0): bool {
        $context = $this->get_instance_context($instanceid);
        return (!$field->get_configdata_property('locked') ||
                has_capability('moodle/contentbank:changelockedcustomfields', $context));
    }

    /**
     * The current user can view custom fields on the given course.
     *
     * @param field_controller $field
     * @param int $instanceid id of the course to test edit permission
     * @return bool true if the current can edit custom fields, false otherwise
     */
    public function can_view(field_controller $field, int $instanceid): bool {
        $visibility = $field->get_configdata_property('visibility');
        if ($visibility == self::NOTVISIBLE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Context that should be used for new categories created by this handler
     *
     * @return \context the context for configuration
     */
    public function get_configuration_context(): \context {
        return \context_system::instance();
    }

    /**
     * URL for configuration of the fields on this handler.
     *
     * @return \moodle_url The URL to configure custom fields for this component
     */
    public function get_configuration_url(): \moodle_url {
        return new \moodle_url('/contentbank/customfield.php');
    }

    /**
     * Returns the context for the data associated with the given instanceid.
     *
     * @param int $instanceid id of the record to get the context for
     * @return \context the context for the given record
     */
    public function get_instance_context(int $instanceid = 0): \context {
        global $DB;
        $contextid = $DB->get_field('contentbank_content', 'contextid', ['id' => $instanceid]);
        if (!$contextid || $contextid == SITEID) {
            return \context_system::instance();
        } else {
            return \context::instance_by_id($contextid);
        }
    }

    /**
     * Allows to add custom controls to the field configuration form that will be saved in configdata
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform) {
        $mform->addElement('header', 'content_handler_header', get_string('customfieldsettings', 'core_course'));
        $mform->setExpanded('content_handler_header', true);

        // If field is locked.
        $mform->addElement('selectyesno', 'configdata[locked]', get_string('customfield_islocked', 'core_course'));
        $mform->addHelpButton('configdata[locked]', 'customfield_islocked', 'core_course');

        // Field data visibility.
        $visibilityoptions = [self::VISIBLETOALL => get_string('customfield_visibletoall', 'core_course'),
            self::NOTVISIBLE => get_string('customfield_notvisible', 'core_course')];
        $mform->addElement('select', 'configdata[visibility]', get_string('customfield_visibility', 'core_course'),
            $visibilityoptions);
        $mform->addHelpButton('configdata[visibility]', 'customfield_visibility', 'core_course');
    }

    /**
     * Creates or updates custom field data.
     *
     * @param \restore_task $task
     * @param array $data
     */
    public function restore_instance_data_from_backup(\restore_task $task, array $data) {
        $courseid = $task->get_courseid();
        $context = $this->get_instance_context($courseid);
        $editablefields = $this->get_editable_fields($courseid);
        $records = api::get_instance_fields_data($editablefields, $courseid);
        $target = $task->get_target();
        $override = ($target != \backup::TARGET_CURRENT_ADDING && $target != \backup::TARGET_EXISTING_ADDING);

        foreach ($records as $d) {
            $field = $d->get_field();
            if ($field->get('shortname') === $data['shortname'] && $field->get('type') === $data['type']) {
                if (!$d->get('id') || $override) {
                    $d->set($d->datafield(), $data['value']);
                    $d->set('value', $data['value']);
                    $d->set('valueformat', $data['valueformat']);
                    $d->set('contextid', $context->id);
                    $d->save();
                }
                return;
            }
        }
    }
}
