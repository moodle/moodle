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

namespace mod_subsection;

use cm_info;
use context_module;
use completion_info;
use core_courseformat\formatactions;
use mod_subsection\event\course_module_viewed;
use moodle_page;
use section_info;
use stdClass;

/**
 * Class manager for subsection
 *
 * @package    mod_subsection
 * @copyright  2023 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** Module name. */
    const MODULE = 'subsection';

    /** The plugin name. */
    const PLUGINNAME = 'mod_subsection';

    /** @var string plugin path. */
    public $path;

    /** @var stdClass course_module record. */
    private $instance;

    /** @var context_module the current context. */
    private $context;

    /** @var cm_info course_modules record. */
    private $cm;

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
     * Create a manager instance from a record id.
     *
     * @param int $courseid the course id
     * @param int $id an activity id
     * @return manager
     */
    public static function create_from_id(int $courseid, int $id): self {
        $cm = get_coursemodule_from_instance('subsection', $id, $courseid);
        return self::create_from_coursemodule($cm);
    }

    /**
     * Create a manager instance from a subsection_record entry.
     *
     * @param stdClass $record the subsection_record record
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
     * Return the current module renderer.
     *
     * @param moodle_page|null $page the current page
     * @return \mod_subsection_renderer the module renderer
     */
    public function get_renderer(?moodle_page $page = null): \mod_subsection_renderer {
        global $PAGE;
        $page = $page ?? $PAGE;
        return $page->get_renderer(self::PLUGINNAME);
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
     * Get the delegated section info.
     *
     * @return section_info|null the delegated section info
     */
    public function get_delegated_section_info(): ?section_info {
        $delegatedsection = $this->cm->get_delegated_section_info();
        if (!$delegatedsection) {
            // Some restorations can produce a situation where the section is not found.
            // In that case, we create a new one.
            $delegatedsection = formatactions::section($this->cm->course)->create_delegated(
                self::PLUGINNAME,
                $this->cm->instance,
                (object) [
                    'name' => $this->cm->name,
                    'visible' => $this->cm->visible,
                    'availability' => (!empty($this->cm->availability)) ? $this->cm->availability : null,
                ],
            );
        }
        return $delegatedsection;
    }
}
