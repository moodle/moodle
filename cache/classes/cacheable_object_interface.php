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
 * Cacheable object.
 *
 * This interface can be implemented by any class that is going to be passed into a cache and allows it to take control of the
 * structure and the information about to be cached, as well as how to deal with it when it is retrieved from a cache.
 * Think of it like serialisation and the __sleep and __wakeup methods.
 * This is used because cache stores are responsible for how they interact with data and what they do when storing it. This
 * interface ensures there is always a guaranteed action.
 *
 * @package core_cache
 * @copyright Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface cacheable_object_interface {
    /**
     * Prepares the object for caching. Works like the __sleep method.
     *
     * @return mixed The data to cache, can be anything except a class that implements the cacheable_object... that would
     *      be dumb.
     */
    public function prepare_to_cache();

    /**
     * Takes the data provided by prepare_to_cache and reinitialises an instance of the associated from it.
     *
     * @param mixed $data
     * @return object The instance for the given data.
     */
    public static function wake_from_cache($data);
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cacheable_object_interface::class, \cacheable_object::class);
