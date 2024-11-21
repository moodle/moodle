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
 * Dependency injection.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use coding_exception;

/**
 * Dependency injection class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class di {

    /** @var container Our container. */
    protected static $container;

    /**
     * Get a thing.
     *
     * @param string $id The thing.
     * @return mixed
     */
    public static function get($id) {
        if (!static::$container) {
            static::$container = static::make_container();
        }
        return static::$container->get($id);
    }

    /**
     * Make the container.
     *
     * @return container
     */
    protected static function make_container() {
        if (local\plugin\addon::should_activate()) {
            $container = new \local_xp\local\container();
        } else {
            $container = new \block_xp\local\default_container();
        }
        return $container;
    }

    /**
     * Reset the container.
     *
     * @return void
     */
    public static function reset_container() {
        static::$container = null;
    }

    /**
     * Set the container.
     *
     * @param \block_xp\local\container $container The container.
     */
    public static function set_container(\block_xp\local\container $container) {
        if (!PHPUNIT_TEST) {
            throw new coding_exception('Containers can only be set during testing.');
        }
        self::$container = $container;
    }

}
