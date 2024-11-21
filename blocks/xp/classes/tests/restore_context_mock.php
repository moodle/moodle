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

namespace block_xp\tests;

use base_logger;
use block_xp\local\backup\restore_context;

/**
 * Restore.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_context_mock extends restore_context {

    /** @var object The data. */
    protected $data;

    /**
     * Constructor.
     *
     * @param array|object $data The mocked data.
     */
    public function __construct($data) {
        $this->data = (object) $data;
    }

    /**
     * Get the course ID.
     *
     * @return int
     */
    public function get_course_id() {
        return (int) ($this->data->courseid ?? 0);
    }

    /**
     * Get the logger.
     *
     * @return base_logger
     */
    public function get_logger() {
        throw new \coding_exception('Not implemented at the moment.');
    }

    /**
     * Get a mapping.
     *
     * @param string $name The mapping name.
     * @param int $oldid The previous ID.
     * @return mixed|null
     */
    public function get_mapping_id($name, $oldid) {
        throw new \coding_exception('Not implemented at the moment.');
    }

    /**
     * Get the original context ID.
     *
     * @return int
     */
    public function get_original_course_context_id() {
        return (int) ($this->data->original_course_contextid ?? 0);
    }

    /**
     * Get the original course ID.
     *
     * @return int
     */
    public function get_original_course_id() {
        return (int) (($this->data->original_courseid ?? 0));
    }

    /**
     * Get the restore ID.
     *
     * @return string
     */
    public function get_restore_id() {
        throw new \coding_exception('Not implemented at the moment.');
    }

    /**
     * Get the user ID.
     *
     * @return int
     */
    public function get_user_id() {
        return (int) $this->data->userid;
    }

    /**
     * Whether the backup is restored on the same site.
     *
     * @return bool
     */
    public function is_same_site() {
        return (bool) ($this->data->samesite ?? true);
    }

}
