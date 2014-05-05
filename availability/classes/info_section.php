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
 * Class handles conditional availability information for a section.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Class handles conditional availability information for a section.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class info_section extends info {
    /** @var \section_info Section. */
    protected $section;

    /**
     * Constructs with item details.
     *
     * @param \section_info $section Section object
     */
    public function __construct(\section_info $section) {
        parent::__construct($section->modinfo->get_course(), $section->visible,
                $section->availability);
        $this->section = $section;
    }

    protected function get_thing_name() {
        return get_section_name($this->section->course, $this->section->section);
    }

    public function get_context() {
        return \context_course::instance($this->get_course()->id);
    }

    protected function set_in_database($availability) {
        global $DB;
        $DB->set_field('course_sections', 'availability', $availability,
                array('id' => $this->section->id));
    }

    /**
     * Gets the section object. Intended for use by conditions.
     *
     * @return section_info Section
     */
    public function get_section() {
        return $this->section;
    }

}
