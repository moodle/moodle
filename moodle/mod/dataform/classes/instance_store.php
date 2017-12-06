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
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Instance store for dataform manager classes.
 */
class mod_dataform_instance_store {

    /** @var array List of stored instances. */
    protected static $instances = array();

    /**
     * Returns an instance from local store, null otherwise.
     *
     * @param int Dataform id.
     * @param string Instance type name.
     * @return mixed Instance object or null.
     */
    public static function instance($dataformid, $manager) {
        if (!empty(self::$instances[$dataformid][$manager])) {
            return self::$instances[$dataformid][$manager];
        }
        return null;
    }

    /**
     * Adds instance to local store.
     *
     * @param int Dataform id.
     * @param string Instance type name.
     * @param mixed Instance object.
     * @return mod_dataform_field_manager
     */
    public static function register($dataformid, $manager, $instance) {
        if (empty(self::$instances[$dataformid])) {
            self::$instances[$dataformid] = array();
        }
        self::$instances[$dataformid][$manager] = $instance;
    }
    /**
     * Removes instance from local store.
     *
     * @param int Dataform id.
     * @param string Instance type name.
     * @return void.
     */
    public static function unregister($dataformid = 0, $manager = null) {
        if (!$dataformid) {
            self::$instances = array();
        } else if (!$manager) {
            unset(self::$instances[$dataformid]);
        } else {
            unset(self::$instances[$dataformid][$manager]);
        }
    }
}
