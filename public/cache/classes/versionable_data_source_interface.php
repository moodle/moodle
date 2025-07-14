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
 * Versionable cache data source.
 *
 * This interface extends the main cache data source interface to add an extra required method if
 * the data source is to be used for a versioned cache.
 *
 * @package core_cache
 * @copyright Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface versionable_data_source_interface extends data_source_interface {
    /**
     * Loads the data for the key provided ready formatted for caching.
     *
     * If there is no data for that key, or if the data for the required key has an older version
     * than the specified $requiredversion, then this returns null.
     *
     * If there is data then $actualversion should be set to the actual version number retrieved
     * (may be the same as $requiredversion or newer).
     *
     * @param string|int $key The key to load.
     * @param int $requiredversion Minimum required version
     * @param mixed $actualversion Should be set to the actual version number retrieved
     * @return mixed What ever data should be returned, or false if it can't be loaded.
     */
    public function load_for_cache_versioned($key, int $requiredversion, &$actualversion);
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(versionable_data_source_interface::class, \cache_data_source_versionable::class);
