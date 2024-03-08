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
 * For use in unit tests that require an info module which isn't really used.
 *
 * @package core_availability
 * @copyright 2019 Ferran Recio
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * For use in unit tests that require an info module which isn't really used.
 *
 * @package core_availability
 * @copyright 2019 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mock_info_module extends info_module {
    /** @var int User id for modinfo */
    protected $userid;

    /** @var \cm_info Activity. */
    protected $cm;

    /**
     * Constructs with item details.
     *
     * @param int $userid Userid for modinfo (if used)
     * @param \cm_info $cm Course-module object
     */
    public function __construct($userid = 0, \cm_info $cm = null) {
        parent::__construct($cm);
        $this->userid = $userid;
        $this->cm = $cm;
    }

    /**
     * Just returns a mock name.
     *
     * @return string Name of item
     */
    protected function get_thing_name() {
        return 'Mock Module';
    }

    /**
     * Returns the current context.
     *
     * @return \context Context for this item
     */
    public function get_context() {
        return \context_course::instance($this->get_course()->id);
    }

    /**
     * Returns the cappability used to ignore access restrictions.
     *
     * @return string Name of capability used to view hidden items of this type
     */
    protected function get_view_hidden_capability() {
        return 'moodle/course:ignoreavailabilityrestrictions';
    }

    /**
     * Mocks don't need to save anything into DB.
     *
     * @param string $availability New JSON value
     */
    protected function set_in_database($availability) {
    }

    /**
     * Obtains the modinfo associated with this availability information.
     *
     * Note: This field is available ONLY for use by conditions when calculating
     * availability or information.
     *
     * @return \course_modinfo Modinfo
     * @throws \coding_exception If called at incorrect times
     */
    public function get_modinfo() {
        // Allow modinfo usage outside is_available etc., so we can use this
        // to directly call into condition is_available.
        if (!$this->userid) {
            throw new \coding_exception('Need to set mock_info userid');
        }
        return get_fast_modinfo($this->course, $this->userid);
    }

    /**
     * Override course-module info.
     * @param \cm_info $cm
     */
    public function set_cm(\cm_info $cm) {
        $this->cm = $cm;
    }
}
