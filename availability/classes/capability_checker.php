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
 * Used while evaluating conditions in bulk.
 *
 * This object caches get_users_by_capability results in case they are needed
 * by multiple conditions.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Used while evaluating conditions in bulk.
 *
 * This object caches get_users_by_capability results in case they are needed
 * by multiple conditions.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class capability_checker {
    /** @var \context Course or module context */
    protected $context;

    /** @var array Associative array of capability => result */
    protected $cache = array();

    /**
     * Constructs for given context.
     *
     * @param \context $context Context
     */
    public function __construct(\context $context) {
        $this->context = $context;
    }

    /**
     * Gets users on course who have the specified capability. Returns an array
     * of user objects which only contain the 'id' field. If the same capability
     * has already been checked (e.g. by another condition) then a cached
     * result will be used.
     *
     * More fields are not necessary because this code is only used to filter
     * users from an existing list.
     *
     * @param string $capability Required capability
     * @return array Associative array of user id => objects containing only id
     */
    public function get_users_by_capability($capability) {
        if (!array_key_exists($capability, $this->cache)) {
            $this->cache[$capability] = get_users_by_capability(
                    $this->context, $capability, 'u.id');
        }
        return $this->cache[$capability];
    }
}
