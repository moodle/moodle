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
 * Restore.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\backup;

use base_logger;
use context_course;
use restore_structure_step;

/**
 * Restore.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_context {

    /** @var restore_structure_step The step. */
    protected $step;

    /**
     * Constructor.
     *
     * The constructor is not public because we may change how this is constructed in the future.
     * We may also create an interface, etc. So for now this object must be created using the static
     * methods.
     *
     * @param restore_structure_step $step The step.
     */
    protected function __construct(restore_structure_step $step) {
        $this->step = $step;
    }

    /**
     * Get the course ID.
     *
     * @return int
     */
    public function get_course_id() {
        return (int) $this->step->get_task()->get_courseid();
    }

    /**
     * Get the course context.
     *
     * @return context_course
     */
    public function get_course_context() {
        return context_course::instance($this->get_course_id());
    }

    /**
     * Get the logger.
     *
     * @return base_logger
     */
    public function get_logger() {
        return $this->step->get_task()->get_logger();
    }

    /**
     * Get a mapping.
     *
     * @param string $name The mapping name.
     * @param int $oldid The previous ID.
     * @return mixed|null
     */
    public function get_mapping_id($name, $oldid) {
        return $this->step->get_mappingid($name, $oldid, null);
    }

    /**
     * Get the original context ID.
     *
     * @return int
     */
    public function get_original_course_context_id() {
        return (int) $this->step->get_task()->get_info()->original_course_contextid;
    }

    /**
     * Get the original course ID.
     *
     * @return int
     */
    public function get_original_course_id() {
        return (int) $this->step->get_task()->get_info()->original_course_id;
    }

    /**
     * Get the restore ID.
     *
     * @return string
     */
    public function get_restore_id() {
        return $this->step->get_task()->get_restoreid();
    }

    /**
     * Get the user ID.
     *
     * @return int
     */
    public function get_user_id() {
        return (int) $this->step->get_task()->get_userid();
    }

    /**
     * Whether the backup is restored on the same site.
     *
     * @return bool
     */
    public function is_same_site() {
        return (bool) $this->step->get_task()->is_samesite();
    }

    /**
     * Make from structure step.
     *
     * @param restore_structure_step $step The step.
     */
    public static function from_structure_step($step) {
        return new static($step);
    }

}
