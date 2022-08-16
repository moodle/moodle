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

namespace mod_data;

use cm_info;
use context_module;
use completion_info;
use data_field_base;
use mod_data\event\course_module_viewed;
use mod_data\event\template_viewed;
use mod_data\event\template_updated;
use core_component;
use stdClass;

/**
 * Class manager for database activity
 *
 * @package    mod_data
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** Module name. */
    const MODULE = 'data';

    /** Template list with their files required to save the information of a preset. */
    const TEMPLATES_LIST = [
        'listtemplate' => 'listtemplate.html',
        'singletemplate' => 'singletemplate.html',
        'asearchtemplate' => 'asearchtemplate.html',
        'addtemplate' => 'addtemplate.html',
        'rsstemplate' => 'rsstemplate.html',
        'csstemplate' => 'csstemplate.css',
        'jstemplate' => 'jstemplate.js',
        'listtemplateheader' => 'listtemplateheader.html',
        'listtemplatefooter' => 'listtemplatefooter.html',
        'rsstitletemplate' => 'rsstitletemplate.html',
    ];

    /** @var string plugin path. */
    private $path;

    /** @var stdClass course_module record. */
    private $instance;

    /** @var context_module the current context. */
    private $context;

    /** @var cm_info course_modules record. */
    private $cm;

    /** @var array the current data_fields records.
     * Do not access this attribute directly, use $this->get_field_records instead
     */
    private $_fieldrecords = null;

    /**
     * Class constructor.
     *
     * @param cm_info $cm course module info object
     * @param stdClass $instance activity instance object.
     */
    public function __construct(cm_info $cm, stdClass $instance) {
        global $CFG;
        $this->cm = $cm;
        $this->instance = $instance;
        $this->context = context_module::instance($cm->id);
        $this->instance->cmidnumber = $cm->idnumber;
        $this->path = $CFG->dirroot . '/mod/' . self::MODULE;
    }

    /**
     * Create a manager instance from an instance record.
     *
     * @param stdClass $instance an activity record
     * @return manager
     */
    public static function create_from_instance(stdClass $instance): self {
        $cm = get_coursemodule_from_instance(self::MODULE, $instance->id);
        // Ensure that $this->cm is a cm_info object.
        $cm = cm_info::create($cm);
        return new self($cm, $instance);
    }

    /**
     * Create a manager instance from a course_modules record.
     *
     * @param stdClass|cm_info $cm an activity record
     * @return manager
     */
    public static function create_from_coursemodule($cm): self {
        global $DB;
        // Ensure that $this->cm is a cm_info object.
        $cm = cm_info::create($cm);
        $instance = $DB->get_record(self::MODULE, ['id' => $cm->instance], '*', MUST_EXIST);
        return new self($cm, $instance);
    }

    /**
     * Create a manager instance from a data_record entry.
     *
     * @param stdClass $record the data_record record
     * @return manager
     */
    public static function create_from_data_record($record): self {
        global $DB;
        $instance = $DB->get_record(self::MODULE, ['id' => $record->dataid], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance(self::MODULE, $instance->id);
        $cm = cm_info::create($cm);
        return new self($cm, $instance);
    }

    /**
     * Return the current context.
     *
     * @return context_module
     */
    public function get_context(): context_module {
        return $this->context;
    }

    /**
     * Return the current instance.
     *
     * @return stdClass the instance record
     */
    public function get_instance(): stdClass {
        return $this->instance;
    }

    /**
     * Return the current cm_info.
     *
     * @return cm_info the course module
     */
    public function get_coursemodule(): cm_info {
        return $this->cm;
    }

    /**
     * Trigger module viewed event and set the module viewed for completion.
     *
     * @param stdClass $course course object
     */
    public function set_module_viewed(stdClass $course) {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        // Trigger module viewed event.
        $event = course_module_viewed::create([
            'objectid' => $this->instance->id,
            'context' => $this->context,
        ]);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('course_modules', $this->cm);
        $event->add_record_snapshot(self::MODULE, $this->instance);
        $event->trigger();

        // Completion.
        $completion = new completion_info($course);
        $completion->set_module_viewed($this->cm);
    }

    /**
     * Trigger module template viewed event.
     */
    public function set_template_viewed() {
        // Trigger an event for viewing templates.
        $event = template_viewed::create([
            'context' => $this->context,
            'courseid' => $this->cm->course,
            'other' => [
                'dataid' => $this->instance->id,
            ],
        ]);
        $event->add_record_snapshot(self::MODULE, $this->instance);
        $event->trigger();
    }

    /**
     * Return if the database has fields.
     *
     * @return bool true if the database has fields
     */
    public function has_fields(): bool {
        global $DB;
        if ($this->_fieldrecords === null) {
            return $DB->record_exists('data_fields', ['dataid' => $this->instance->id]);
        }
        return !empty($this->_fieldrecords);
    }

    /**
     * Return the database fields.
     *
     * @return data_field_base[] the field instances.
     */
    public function get_fields(): array {
        $result = [];
        $fieldrecords = $this->get_field_records();
        foreach ($fieldrecords as $fieldrecord) {
            $result[$fieldrecord->id] = $this->get_field($fieldrecord);
        }
        return $result;
    }

    /**
     * Return the field records (the current data_fields records).
     *
     * @return stdClass[] an array of records
     */
    public function get_field_records() {
        global $DB;
        if ($this->_fieldrecords === null) {
            $this->_fieldrecords = $DB->get_records('data_fields', ['dataid' => $this->instance->id]);
        }
        return $this->_fieldrecords;
    }

    /**
     * Return a specific field instance from a field record.
     *
     * @param stdClass $fieldrecord the fieldrecord to convert
     * @return data_field_base the data field class instance
     */
    public function get_field(stdClass $fieldrecord): data_field_base {
        $filepath = "{$this->path}/field/{$fieldrecord->type}/field.class.php";
        $classname = "data_field_{$fieldrecord->type}";
        if (!file_exists($filepath)) {
            return new data_field_base($fieldrecord, $this->instance, $this->cm);
        }
        require_once($filepath);
        if (!class_exists($classname)) {
            return new data_field_base($fieldrecord, $this->instance, $this->cm);
        }
        $newfield = new $classname($fieldrecord, $this->instance, $this->cm);
        return $newfield;
    }

    /**
     * Return a specific template.
     *
     * NOTE: this method returns a default template if the module template is empty.
     * However, it won't update the template database field.
     *
     * @param string $templatename
     * @param array $options extra display options array
     * @return template the template instance
     */
    public function get_template(string $templatename, array $options = []): template {
        if ($templatename === 'single') {
            $templatename = 'singletemplate';
        }
        $instance = $this->instance;
        $templatecontent = $instance->{$templatename} ?? '';
        if (empty($templatecontent)) {
            $templatecontent = data_generate_default_template($instance, $templatename, 0, false, false);
        }
        // Some templates have extra options.
        if ($templatename === 'singletemplate') {
            $options['comments'] = true;
            $options['ratings'] = true;
        }
        return new template($this, $templatecontent, $options);
    }

    /**
     * Update the database templates.
     *
     * @param stdClass $newtemplates an object with all the new templates
     * @return bool if updated successfully.
     */
    public function update_templates(stdClass $newtemplates): bool {
        global $DB;
        $record = (object)[
            'id' => $this->instance->id,
        ];
        foreach (self::TEMPLATES_LIST as $templatename => $templatefile) {
            if (!isset($newtemplates->{$templatename})) {
                continue;
            }
            $record->{$templatename} = $newtemplates->{$templatename};
        }

        // The add entry form cannot repeat tags.
        if (isset($record->addtemplate) && !data_tags_check($this->instance->id, $record->addtemplate)) {
                return false;
        }

        $DB->update_record(self::MODULE, $record);
        $this->instance = $DB->get_record(self::MODULE, ['id' => $this->cm->instance], '*', MUST_EXIST);

        // Trigger an event for saving the templates.
        $event = template_updated::create(array(
            'context' => $this->context,
            'courseid' => $this->cm->course,
            'other' => array(
                'dataid' => $this->instance->id,
            )
        ));
        $event->trigger();

        return true;
    }

    /**
     * Returns an array of all the available presets.
     *
     * @return array A list with the datapreset plugins and the presets saved by users.
     */
    public function get_available_presets(): array {
        // First load the datapreset plugins that exist within the modules preset dir.
        $pluginpresets = static::get_available_plugin_presets();

        // Then find the presets that people have saved.
        $savedpresets = static::get_available_saved_presets();

        return array_merge($pluginpresets, $savedpresets);
    }

    /**
     * Returns an array of all the presets that users have saved to the site.
     *
     * @return array A list with the preset saved by the users.
     */
    public function get_available_saved_presets(): array {
        global $USER;

        $presets = [];

        $fs = get_file_storage();
        $files = $fs->get_area_files(DATA_PRESET_CONTEXT, DATA_PRESET_COMPONENT, DATA_PRESET_FILEAREA);
        if (empty($files)) {
            return $presets;
        }
        $canviewall = has_capability('mod/data:viewalluserpresets', $this->get_context());
        foreach ($files as $file) {
            $isnotdirectory = ($file->is_directory() && $file->get_filepath() == '/') || !$file->is_directory();
            $userid = $file->get_userid();
            $cannotviewfile = !$canviewall && $userid != $USER->id;
            if ($isnotdirectory || $cannotviewfile) {
                continue;
            }

            $preset = preset::create_from_storedfile($this, $file);
            $presets[] = $preset;
        }

        return $presets;
    }

    /**
     * Returns an array of all the available plugin presets.
     *
     * @return array A list with the datapreset plugins.
     */
    public static function get_available_plugin_presets(): array {
        $presets = [];

        $dirs = core_component::get_plugin_list('datapreset');
        foreach ($dirs as $dir => $fulldir) {
            if (preset::is_directory_a_preset($fulldir)) {
                $preset = preset::create_from_plugin(null, $dir);
                $presets[] = $preset;
            }
        }

        return $presets;
    }
}
