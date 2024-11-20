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

namespace core_cache;

/**
 * Cache store feature: keys are searchable.
 *
 * Cache stores can choose to implement this interface.
 * In order for a store to be usable as a session cache it must implement this interface.
 *
 * @package core_cache
 * @copyright Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface searchable_cache_interface {
    /**
     * Finds all of the keys being used by the cache store.
     *
     * @return array.
     */
    public function find_all();

    /**
     * Finds all of the keys whose keys start with the given prefix.
     *
     * @param string $prefix
     */
    public function find_by_prefix($prefix);
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(searchable_cache_interface::class, \cache_is_searchable::class);
