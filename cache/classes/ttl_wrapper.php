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
 * A wrapper class used to handle ttl when the cache store doesn't natively support it.
 *
 * This class is exactly why you should use event driving invalidation of cache data rather than relying on ttl.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ttl_wrapper {
    /**
     * The data being stored.
     * @var mixed
     */
    public $data;

    /**
     * When the cache data expires as a timestamp.
     * @var int
     */
    public $expires;

    /**
     * Constructs a ttl cache wrapper.
     *
     * @param mixed $data
     * @param int $ttl The time to live in seconds.
     */
    public function __construct($data, $ttl) {
        $this->data = $data;
        $this->expires = cache::now() + (int)$ttl;
    }

    /**
     * Returns true if the data has expired.
     * @return int
     */
    public function has_expired() {
        return ($this->expires < cache::now());
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(ttl_wrapper::class, \cache_ttl_wrapper::class);
