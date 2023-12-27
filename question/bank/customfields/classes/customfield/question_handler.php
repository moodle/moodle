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

namespace qbank_customfields\customfield;

use core_customfield\api;
use core_customfield\field_controller;
use core_customfield\output\field_data;

/**
 * Question handler for custom fields.
 *
 * @package     qbank_customfields
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_handler extends \core_customfield\handler {

    /**
     * @var question_handler
     */
    static protected $singleton;

    /**
     * @var \context
     */
    protected $parentcontext;

    /** @var int Field is displayed in the question display and question preview, visible to everybody */
    const VISIBLETOALL = 2;
    /** @var int Field is displayed in the question display and question preview but only for "teachers" */
    const VISIBLETOTEACHERS = 1;
    /** @var int Field is not displayed in the question display and question preview */
    const NOTVISIBLE = 0;

    /**
     * Creates the custom field handler and returns a singleton.
     * Itemid is always zero as the custom fields are the same
     * for every question across the system.
     *
     * @param int $itemid Always zero.
     * @return \qbank_customfields\customfield\question_handler
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
        return has_capability('qbank/customfields:configurecustomfields', $this->get_configuration_context());
    }

    /**
     * The current user can edit custom fields for the given question.
     *
     * @param field_controller $field
     * @param int $instanceid id of the question to test edit permission
     * @return bool true if the current can edit custom fields, false otherwise
     */
    public function can_edit(field_controller $field, int $instanceid = 0) : bool {
        if ($instanceid) {
            $context = $this->get_instance_context($instanceid);
        } else {
            $context = $this->get_parent_context();
        }

        return (!$field->get_configdata_property('locked') ||
                has_capability('qbank/customfields:changelockedcustomfields', $context));
    }

    /**
     * The current user can view custom fields for the given question.
     *
     * @param field_controller $field
     * @param int $instanceid id of the question to test edit permission
     * @return bool true if the current can edit custom fields, false otherwise
     */
    public function can_view(field_controller $field, int $instanceid) : bool {
        $visibility = $field->get_configdata_property('visibility');
        if ($visibility == self::NOTVISIBLE) {
            return false;
        } else if ($visibility == self::VISIBLETOTEACHERS) {
            return has_capability('qbank/customfields:viewhiddencustomfields', $this->get_instance_context($instanceid));
        } else {
            return true;
        }
    }

    /**
     * Determine if the current user can view custom field in their given context.
     * This determines if the user can see the field at all not just the field for
     * a particular instance.
     * Used primarily in showing or not the field in the question bank table.
     *
     * @param field_controller $field The field trying to be viewed.
     * @param \context $context The context the field is being displayed in.
     * @return bool true if the current can edit custom fields, false otherwise.
     */
    public function can_view_type(field_controller $field, \context $context) : bool {
        $visibility = $field->get_configdata_property('visibility');
        if ($visibility == self::NOTVISIBLE) {
            return false;
        } else if ($visibility == self::VISIBLETOTEACHERS) {
            return has_capability('qbank/customfields:viewhiddencustomfields', $context);
        } else {
            return true;
        }
    }

    /**
     * Sets parent context for the question.
     *
     * This may be needed when question is being created, there is no question context but we need to check capabilities
     *
     * @param \context $context
     */
    public function set_parent_context(\context $context): void {
        $this->parentcontext = $context;
    }

    /**
     * Returns the parent context for the question.
     *
     * @return \context
     */
    protected function get_parent_context() : \context {
        if ($this->parentcontext) {
            return $this->parentcontext;
        } else {
            return \context_system::instance();
        }
    }

    /**
     * Context that should be used for new categories created by this handler.
     *
     * @return \context the context for configuration
     */
    public function get_configuration_context() : \context {
        return \context_system::instance();
    }

    /**
     * URL for configuration page for the fields for the question custom fields.
     *
     * @return \moodle_url The URL to configure custom fields for this component
     */
    public function get_configuration_url() : \moodle_url {
        return new \moodle_url('/question/customfield.php');
    }

    /**
     * Returns the context for the data associated with the given instanceid.
     *
     * @param int $instanceid id of the record to get the context for
     * @return \context the context for the given record
     * @throws \coding_exception
     */
    public function get_instance_context(int $instanceid = 0) : \context {
        if ($instanceid > 0) {
            $questiondata = \question_bank::load_question_data($instanceid);
            $contextid = $questiondata->contextid;
            $context = \context::instance_by_id($contextid);
            return $context;
        } else {
            throw new \coding_exception('Instance id must be provided.');
        }
    }

    /**
     * Given a field and instance id get all the filed data.
     *
     * @param field_controller $field The field to get the data for.
     * @param int $instanceid The instance id to get the data for.
     * @return \core_customfield\data_controller The fetched data.
     */
    public function get_field_data(\core_customfield\field_controller $field, int $instanceid): \core_customfield\data_controller {
        $fields = [$field->get('id') => $field];
        $fieldsdata = api::get_instance_fields_data($fields, $instanceid);
        return $fieldsdata[$field->get('id')];
    }

    /**
     * For a given instance id (question id) get the categories and the
     * fields with any data. Return an array of categories containing an
     * array of field names and values that is ready to be passed to a renderer.
     *
     * @param int $instanceid The instance id to get the data for.
     * @return array $cfdata The fetched data
     */
    public function get_categories_fields_data(int $instanceid): array {
        // Prepare custom fields data.
        $instancedata = $this->get_instance_data($instanceid);

        $cfdata = [];

        foreach ($instancedata as $instance) {
            $field = $instance->get_field();

            if ($this->can_view($field, $instanceid)) {
                $category = $instance->get_field()->get_category()->get('name');
                $fieldname = $field->get_formatted_name();
                $fieldvalue = $this->get_field_data($field, $instanceid)->export_value();
                $cfdata[$category][] = ['name' => $fieldname,  'value' => $fieldvalue];
            }

        }

        return $cfdata;
    }

    /**
     * Get the custom data for the given field
     * and render HTML ready for display in question table.
     *
     * @param object $fielddata The field data used for display.
     * @return string The HTML to display in the table column.
     */
    public function display_custom_field_table(object $fielddata) : string {
        global $PAGE;

        $output = $PAGE->get_renderer('qbank_customfields');
        $outputdata = new field_data($fielddata);

        return $output->render_for_table($outputdata);
    }

    /**
     * Render the custom field category and filed data as HTML ready for display.
     *
     * @param array $catfielddata Array of categories and field names and values.
     * @return string The HTML to display.
     */
    public function display_custom_categories_fields(array $catfielddata) : string {
        global $PAGE;
        $output = $PAGE->get_renderer('qbank_customfields');

        return $output->render_for_preview($catfielddata);
    }

    /**
     * Add custom controls to the field configuration form that will be saved.
     *
     * @param \MoodleQuickForm $mform The form to add the custom fields to.
     */
    public function config_form_definition(\MoodleQuickForm $mform): void {
        $mform->addElement('header', 'question_handler_header',
                get_string('customfieldsettings', 'qbank_customfields'));
        $mform->setExpanded('question_handler_header', true);

        // If field is locked.
        $mform->addElement('selectyesno', 'configdata[locked]',
                get_string('customfield_islocked', 'qbank_customfields'));
        $mform->addHelpButton('configdata[locked]', 'customfield_islocked', 'qbank_customfields');

        // Field data visibility.
        $visibilityoptions = [
                self::VISIBLETOALL => get_string('customfield_visibletoall', 'qbank_customfields'),
                self::VISIBLETOTEACHERS => get_string('customfield_visibletoteachers', 'qbank_customfields'),
                self::NOTVISIBLE => get_string('customfield_notvisible', 'qbank_customfields')
        ];
        $mform->addElement('select', 'configdata[visibility]',
                get_string('customfield_visibility', 'qbank_customfields'),
                $visibilityoptions);
        $mform->addHelpButton(
                'configdata[visibility]', 'customfield_visibility', 'qbank_customfields');
    }

    /**
     * Creates or updates the question custom field data when restoring from a backup.
     *
     * @param \restore_task $task
     * @param array $data
     */
    public function restore_instance_data_from_backup(\restore_task $task, array $data): void {

        $editablefields = $this->get_editable_fields($data['newquestion']);
        $records = api::get_instance_fields_data($editablefields, $data['newquestion']);
        $target = $task->get_target();
        $override = ($target != \backup::TARGET_CURRENT_ADDING && $target != \backup::TARGET_EXISTING_ADDING);

        foreach ($records as $d) {
            $field = $d->get_field();
            if ($field->get('shortname') === $data['shortname'] && $field->get('type') === $data['type']) {
                if (!$d->get('id') || $override) {
                    $d->set($d->datafield(), $data['value']);
                    $d->set('value', $data['value']);
                    $d->set('valueformat', $data['valueformat']);
                    $d->set('contextid', $data['fieldcontextid']);
                    $d->save();
                }
                return;
            }
        }
    }
}
