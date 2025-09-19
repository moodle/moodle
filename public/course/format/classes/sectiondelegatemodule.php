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

use action_menu;
use cm_info;
use core_courseformat\base as course_format;
use core_courseformat\formatactions;
use core_courseformat\output\local\content\section\controlmenu;
use core_courseformat\stateupdates;
use renderer_base;
use section_info;
use stdClass;

/**
 * Class sectiondelegatemodule
 *
 * @package    core_courseformat
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class sectiondelegatemodule extends sectiondelegate {
    /** @var section_info $sectioninfo The section_info object of the delegated section module */

    /** @var cm_info|null $cm The cm_info object of the delegated section module */
    private $cm = null;

    /** @var stdClass|null $course The course object of the delegated section module */
    private $course = null;

    /**
     * Constructor.
     * @param section_info $sectioninfo
     */
    public function __construct(
        protected section_info $sectioninfo
    ) {
        parent::__construct($sectioninfo);
        /** @var \core_course\modinfo $modinfo */
        $modinfo = $sectioninfo->modinfo;
        try {
            // Disabled or missing plugins can throw exceptions.
            $this->cm = $modinfo->get_instance_of(
                $this->get_module_name(),
                $this->sectioninfo->itemid,
            );
            $this->course = $modinfo->get_course();
        } catch (\Exception $e) {
            $this->cm = null;
            $this->course = null;
        }
    }

    /**
     * Check if the delegated component is enabled.
     *
     * @return bool
     */
    public function is_enabled(): bool {
        return $this->cm !== null;
    }

    /**
     * Get the delegated section id controlled by a specific cm.
     *
     * This method is used when reverse search is needed bu we cannot access the database.
     * This happens mostly on backup and restore. Do NOT use for normal operations.
     *
     * @param stdClass|cm_info $cm a course module compatible data structure.
     * @return int the section id.
     */
    public static function delegated_section_id(stdClass|cm_info $cm): int {
        global $DB;
        return $DB->get_field(
            'course_sections',
            'id',
            [
                'course' => $cm->course,
                'component' => explode('\\', static::class)[0],
                'itemid' => $cm->instance,
            ],
            MUST_EXIST
        );
    }

    /**
     * Get the parent section of the current delegated section.
     *
     * @return section_info|null
     */
    public function get_parent_section(): ?section_info {
        return $this->cm->get_section_info();
    }

    /**
     * Get the course object.
     *
     * @return cm_info
     */
    public function get_cm(): cm_info {
        return $this->cm;
    }

    /**
     * Get the course object.
     *
     * @return stdClass
     */
    public function get_course(): stdClass {
        return $this->course;
    }

    /**
     * Get the module name from the section component frankenstyle name.
     *
     * @return string
     */
    private function get_module_name(): string {
        return \core_component::normalize_component($this->sectioninfo->component)[1];
    }

    /**
     * Sync the section renaming with the activity name.
     *
     * @param section_info $section
     * @param string|null $newname
     * @return string|null
     */
    public function preprocess_section_name(section_info $section, ?string $newname): ?string {
        $cm = get_coursemodule_from_instance($this->get_module_name(), $section->itemid);
        if (!$cm) {
            return $newname;
        }
        if (empty($newname) || $newname === $cm->name) {
            return $cm->name;
        }
        formatactions::cm($section->course)->rename($cm->id, $newname);
        return $newname;
    }

    /**
     * Allow delegate plugin to modify the available section menu.
     *
     * @param course_format $format The course format instance.
     * @param controlmenu $controlmenu The control menu instance.
     * @param renderer_base $output The renderer instance.
     * @return action_menu|null The new action menu with the list of edit control items or null if no action menu is available.
     */
    public function get_section_action_menu(
        course_format $format,
        controlmenu $controlmenu,
        renderer_base $output,
    ): ?action_menu {
        $controlmenuclass = $format->get_output_classname('content\\cm\\delegatedcontrolmenu');
        $controlmenu = new $controlmenuclass(
            $format,
            $this->sectioninfo,
            $this->cm,
        );
        return $controlmenu->get_action_menu($output);
    }

    /**
     * Add extra state updates when put or create a section.
     *
     * @param section_info $section the affected section.
     * @param stateupdates $updates the state updates object to notify the UI.
     */
    public function put_section_state_extra_updates(section_info $section, stateupdates $updates): void {
        $cm = get_coursemodule_from_instance($this->get_module_name(), $section->itemid);
        $updates->add_cm_put($cm->id);
    }

    public function section_updated(stdClass $sectionrecord): void {
        global $DB;

        $cmrecord = [];
        if (isset($sectionrecord->availability) && $sectionrecord->availability !== $this->cm->availability) {
            $cmrecord['availability'] = $sectionrecord->availability;
        }

        if (isset($sectionrecord->visible) && $sectionrecord->visible !== $this->cm->visible) {
            $cmrecord['visible'] = $sectionrecord->visible;
            $cmrecord['visibleold'] = $sectionrecord->visible;
        }

        if (empty($cmrecord)) {
            return;
        }

        $cmrecord['id'] = $this->cm->id;
        $DB->update_record('course_modules', (object)$cmrecord);
    }
}
