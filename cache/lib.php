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
 * The core cache API.
 *
 * Pretty much just includes the mandatory classes and contains the misc classes that arn't worth separating into individual files.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are requried in order to use caching.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the required classes.
require_once($CFG->dirroot.'/cache/classes/interfaces.php');
require_once($CFG->dirroot.'/cache/classes/config.php');
require_once($CFG->dirroot.'/cache/classes/helper.php');
require_once($CFG->dirroot.'/cache/classes/factory.php');
require_once($CFG->dirroot.'/cache/classes/loaders.php');
require_once($CFG->dirroot.'/cache/classes/store.php');
require_once($CFG->dirroot.'/cache/classes/definition.php');

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

/**
 * A wrapper class used to handle ttl when the cache store doesn't natively support it.
 *
 * This class is exactly why you should use event driving invalidation of cache data rather than relying on ttl.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_ttl_wrapper {

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

/**
 * A cache exception class. Just allows people to catch cache exceptions.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_exception extends moodle_exception {
    /**
     * Constructs a new exception
     *
     * @param string $errorcode
     * @param string $module
     * @param string $link
     * @param mixed $a
     * @param mixed $debuginfo
     */
    public function __construct($errorcode, $module = 'cache', $link = '', $a = null, $debuginfo = null) {
        // This may appear like a useless override but you will notice that we have set a MUCH more useful default for $module.
        parent::__construct($errorcode, $module, $link, $a, $debuginfo);
    }
}

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
 */
class cacheable_object_array extends ArrayObject implements cacheable_object {

    /**
     * Constructs a new array object instance.
     * @param array $items
     */
    final public function __construct(array $items = array()) {
        parent::__construct($items, ArrayObject::STD_PROP_LIST);
    }

    /**
     * Returns the data to cache for this object.
     *
     * @return array An array of cache_cached_object instances.
     * @throws coding_exception
     */
    final public function prepare_to_cache() {
        $result = array();
        foreach ($this as $key => $value) {
            if ($value instanceof cacheable_object) {
                $value = new cache_cached_object($value);
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
     * @return cacheable_object_array
     * @throws coding_exception
     */
    final static public function wake_from_cache($data) {
        if (!is_array($data)) {
            throw new coding_exception('Invalid data type when reviving cacheable_array data');
        }
        $result = array();
        foreach ($data as $key => $value) {
            $result[$key] = $value->restore_object();
        }
        $class = __CLASS__;
        return new $class($result);
    }
}