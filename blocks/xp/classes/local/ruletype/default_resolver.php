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
 * Resolver.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\ruletype;

/**
 * Resolver.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_resolver implements resolver {

    /** @var (ruletype|false)[] The types. */
    protected $types = [];

    /**
     * Get type by name.
     *
     * @param string $name The type name.
     * @return ruletype|null
     */
    public function get_type($name): ?ruletype {
        if (!isset($this->types[$name])) {
            $class = "block_xp\\local\\ruletype\\$name";
            $this->types[$name] = class_exists($class) ? new $class() : false;
        }
        return $this->types[$name] ?: null;
    }

    /**
     * Get type name.
     *
     * @param ruletype $type The type.
     * @return string
     */
    public function get_type_name(ruletype $type): string {
        return str_replace("block_xp\\local\\ruletype\\", '', get_class($type));
    }

}
