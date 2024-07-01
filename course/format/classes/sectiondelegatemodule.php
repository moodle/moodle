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

use cm_info;
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

        [$this->course, $this->cm] = get_course_and_cm_from_instance(
            $this->sectioninfo->itemid,
            $this->get_module_name(),
            $this->sectioninfo->course,
        );
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
}
