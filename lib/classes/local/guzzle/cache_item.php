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

namespace core\local\guzzle;

/**
 * Class to define an interface for interacting with objects inside a cache.
 *
 * @package    core
 * @copyright  2022 safatshahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_item {

    /**
     * Key to be used to store and identify the cache.
     *
     * @var string $key cache item key.
     */
    private string $key;

    /**
     * Actual data of the cache.
     *
     * @var mixed $value cache data.
     */
    private $value;

    /**
     * The expiry of the cache item according to TTL.
     *
     * @var \DateTime|null $expiration TTL time for the cache item.
     */
    private ?\DateTime $expiration;

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @var bool $ishit determine the cache lookup.
     */
    private bool $ishit = false;

    /**
     * Constructor for the cache_item to get the key and module to retrieve or set the cache item.
     *
     * @param string $key The key for the current cache item.
     * @param string $module determines the location of the cache item.
     * @param int|null $ttl Time to live for the cache item.
     */
    public function __construct(string $key, string $module, ?int $ttl = null) {
        global $CFG, $USER;
        $this->key = $key;
        // Set the directory for cache.
        $dir = $CFG->cachedir . '/' . $module . '/';
        if (file_exists($dir)) {
            $filename = 'u' . $USER->id . '_' . md5(serialize($key));
            // If the cache fine exists, set the value from the cache file.
            if (file_exists($dir . $filename)) {
                $this->ishit = true;
                $this->expires_after($ttl);
                $fp = fopen($dir . $filename, 'rb');
                $size = filesize($dir . $filename);
                $content = fread($fp, $size);
                $this->value = unserialize($content);
            }
        }
    }

    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string The key string for this cache item.
     */
    public function get_key(): string {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cache value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed The value corresponding to this cache item's key, or null if not found.
     */
    public function get() {
        return $this->is_hit() ? $this->value : null;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return bool True if the request resulted in a cache hit. False otherwise.
     */
    public function is_hit():bool {
        if (!$this->ishit) {
            return false;
        }

        if ($this->expiration === null) {
            return true;
        }

        return $this->current_time()->getTimestamp() < $this->expiration->getTimestamp();
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value The serializable value to be stored.
     * @return static The invoked object.
     */
    public function set($value): cache_item {
        $this->ishit = true;
        $this->value = $value;

        return $this;
    }

    /**
     * Sets the absolute expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static The called object.
     */
    public function expires_at($expiration): cache_item {
        if (null === $expiration || $expiration instanceof \DateTimeInterface) {
            $this->expiration = $expiration;

            return $this;
        }

        throw new \coding_exception('Invalid argument passed');
    }

    /**
     * Sets the relative expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration. If null is passed explicitly, a default value MAY be used.
     *   If none is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static The called object.
     */
    public function expires_after($time): cache_item {
        if (is_int($time) && $time >= 0) {
            $this->expiration = $this->current_time()->add(new \DateInterval("PT{$time}S"));
        } else if ($time instanceof \DateInterval) {
            $this->expiration = $this->current_time()->add($time);
        } else {
            $this->expiration = $time;
        }

        return $this;
    }

    /**
     * Gets the current time in the user timezone.
     *
     * @return \DateTime
     */
    private function current_time(): \DateTime {
        return new \DateTime('now', new \DateTimeZone(get_user_timezone()));
    }
}
