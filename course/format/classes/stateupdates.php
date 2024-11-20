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

namespace core_courseformat;

use coding_exception;
use core_courseformat\base as course_format;
use renderer_base;
use stdClass;
use course_modinfo;
use JsonSerializable;

/**
 * Class to track state actions.
 *
 * The methods from this class should be executed via "stateactions" methods.
 *
 * Each format plugin could extend this class to provide new updates to the frontend
 * mutation module.
 * Extended classes should be located in "format_XXX\course" namespace and
 * extends {@see \core_courseformat\stateupdates}.
 *
 * @package    core_course
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stateupdates implements JsonSerializable {

    /** @var course_format format the course format */
    protected $format;

    /** @var renderer_base renderer format renderer */
    protected $output;

    /** @var array the tracked updates */
    protected $updates;

    /**
     * State update class constructor.
     *
     * @param course_format $format Course format.
     */
    public function __construct(course_format $format) {
        global $PAGE;

        $this->format = $format;
        $this->output = $this->format->get_renderer($PAGE);
        $this->updates = [];
    }

    /**
     * Return the data to serialize the current track in JSON.
     *
     * @return stdClass the statement data structure
     */
    public function jsonSerialize(): array {
        return $this->updates;
    }

    /**
     * Add track about a general course state change.
     */
    public function add_course_put(): void {
        $courseclass = $this->format->get_output_classname('state\\course');
        $currentstate = new $courseclass($this->format);
        $this->add_update('course', 'put', $currentstate->export_for_template($this->output));
    }

    /**
     * Add track about a section state put.
     *
     * @param int $sectionid The affected section id.
     */
    public function add_section_put(int $sectionid): void {
        $this->create_or_put_section($sectionid, 'put');
    }

    /**
     * Add track about a new section created.
     *
     * @param int $sectionid The affected section id.
     */
    public function add_section_create(int $sectionid): void {
        $this->create_or_put_section($sectionid, 'create');
    }

    /**
     * Add track about section created or put.
     *
     * @param int $sectionid The affected section id.
     * @param string $action The action to track for the section ('create' or 'put').
     */
    protected function create_or_put_section(int $sectionid, string $action): void {
        if ($action != 'create' && $action != 'put') {
            throw new coding_exception(
                "Invalid action passed ($action) to create_or_put_section. Only 'create' and 'put' are valid."
            );
        }
        $course = $this->format->get_course();
        $modinfo = course_modinfo::instance($course);
        $format = $this->format;

        $section = $modinfo->get_section_info_by_id($sectionid, MUST_EXIST);

        if (!$format->is_section_visible($section)) {
            return;
        }

        $sectionclass = $format->get_output_classname('state\\section');
        $currentstate = new $sectionclass($this->format, $section);

        $this->add_update('section', $action, $currentstate->export_for_template($this->output));

        // If the section is delegated to a component, give the component oportunity to add updates.
        $delegated = $section->get_component_instance();
        if ($delegated) {
            $delegated->put_section_state_extra_updates($section, $this);
        }
    }

    /**
     * Add track about a section deleted.
     *
     * @deprecated since Moodle 4.1 MDL-74925 - please call add_section_remove() instead.
     * @param int $sectionid The affected section id.
     */
    public function add_section_delete(int $sectionid): void {
        debugging('add_section_delete() is deprecated. Please use add_section_remove() instead.', DEBUG_DEVELOPER);

        $this->add_update('section', 'remove', (object)['id' => $sectionid]);
    }

    /**
     * Add track about a section removed.
     *
     * @param int $sectionid The affected section id.
     */
    public function add_section_remove(int $sectionid): void {
        $this->add_update('section', 'remove', (object)['id' => $sectionid]);
    }

    /**
     * Add track about a course module state update.
     *
     * @param int $cmid the affected course module id
     */
    public function add_cm_put(int $cmid): void {
        $this->create_or_put_cm($cmid, 'put');
    }

    /**
     * Add track about a course module created.
     *
     * @param int $cmid the affected course module id
     */
    public function add_cm_create(int $cmid): void {
        $this->create_or_put_cm($cmid, 'create', true);
    }

    /**
     * Add track about section created or put.
     *
     * @param int $cmid The affected course module id.
     * @param string $action The action to track for the section ('create' or 'put').
     */
    protected function create_or_put_cm(int $cmid, string $action): void {
        $modinfo = course_modinfo::instance($this->format->get_course());

        $cm = $modinfo->get_cm($cmid);
        $section = $modinfo->get_section_info_by_id($cm->section);
        $format = $this->format;

        if (!$section->uservisible || !$cm->is_visible_on_course_page()) {
            return;
        }

        $cmclass = $format->get_output_classname('state\\cm');
        $currentstate = new $cmclass($this->format, $section, $cm);

        $this->add_update('cm', $action, $currentstate->export_for_template($this->output));
    }

    /**
     * Add track about a course module deleted.
     *
     * @deprecated since Moodle 4.1 MDL-74925 - please call add_cm_remove() instead.
     * @param int $cmid the affected course module id
     */
    public function add_cm_delete(int $cmid): void {
        debugging('add_cm_delete() is deprecated. Please use add_cm_remove() instead.', DEBUG_DEVELOPER);

        $this->add_update('cm', 'remove', (object)['id' => $cmid]);
    }

    /**
     * Add track about a course module removed.
     *
     * @param int $cmid the affected course module id
     */
    public function add_cm_remove(int $cmid): void {
        $this->add_update('cm', 'remove', (object)['id' => $cmid]);
    }

    /**
     * Add a valid update message to the update list.
     *
     * @param string $name the update name
     * @param string $action the update action (usually update, create, remove)
     * @param stdClass $fields the object fields
     */
    protected function add_update(string $name, string $action, stdClass $fields): void {
        $this->updates[] = (object)[
            'name' => $name,
            'action' => $action,
            'fields' => $fields,
        ];
    }

}
