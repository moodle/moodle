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
 * A cached object wrapper.
 *
 * This class gets used when the data is an object that has implemented the cacheable_object interface.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_cached_object {

    /**
     * The class of the cacheable object
     * @var string
     */
    protected $class;

    /**
     * The data returned by the cacheable_object prepare_to_cache method.
     * @var mixed
     */
    protected $data;

    /**
     * Constructs a cached object wrapper.
     * @param cacheable_object $obj
     */
    public function __construct(cacheable_object $obj) {
        $this->class = get_class($obj);
        $this->data = $obj->prepare_to_cache();
    }

    /**
     * Restores the data as an instance of the cacheable_object class.
     * @return object
     */
    public function restore_object() {
        $class = $this->class;
        return $class::wake_from_cache($this->data);
    }
}
