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

use core\exception\coding_exception;
use ArrayObject;

/**
 * An array of cacheable objects.
 *
 * This class allows a developer to create an array of cacheable objects and store that.
 * The cache API doesn't check items within an array to see whether they are cacheable. Such a check would be very costly to both
 * arrays using cacheable object and those that don't.
 * Instead the developer must explicitly use a cacheable_object_array instance.
 *
 * The following is one example of how this class can be used.
 * <code>
 * $data = array();
 * $data[] = new cacheable_object('one');
 * $data[] = new cacheable_object('two');
 * $data[] = new cacheable_object('three');
 * $cache->set(new cacheable_object_array($data));
 * </code>
 * Another example would be
 * <code>
 * $data = new cacheable_object_array();
 * $data[] = new cacheable_object('one');
 * $data[] = new cacheable_object('two');
 * $data[] = new cacheable_object('three');
 * $cache->set($data);
 * </code>
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_cache
 */
class cacheable_object_array extends ArrayObject implements cacheable_object_interface {
    /**
     * Constructs a new array object instance.
     * @param array $items
     */
    final public function __construct(array $items = []) {
        parent::__construct($items, ArrayObject::STD_PROP_LIST);
    }

    /**
     * Returns the data to cache for this object.
     *
     * @return cached_object[] An array of cached_object instances.
     * @throws coding_exception
     */
    final public function prepare_to_cache() {
        $result = [];
        foreach ($this as $key => $value) {
            if ($value instanceof cacheable_object_interface) {
                $value = new cached_object($value);
            } else {
                throw new coding_exception('Only cacheable_object instances can be added to a cacheable_array');
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Returns the cacheable_object_array that was originally sent to the cache.
     *
     * @param array $data
     * @return self
     * @throws coding_exception
     */
    final public static function wake_from_cache($data) {
        if (!is_array($data)) {
            throw new coding_exception('Invalid data type when reviving cacheable_array data');
        }
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = $value->restore_object();
        }
        $class = __CLASS__;
        return new $class($result);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cacheable_object_array::class, \cacheable_object_array::class);
