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

namespace core_external;

/**
 * Associative array description class
 *
 * @package    core_external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_single_structure extends external_description {

    /** @var array Description of array keys key=>external_description */
    public $keys;

    /**
     * Constructor
     *
     * @param array $keys
     * @param string $desc
     * @param int $required
     * @param array $default
     * @param bool $allownull
     */
    public function __construct(
        array $keys,
        $desc = '',
        $required = VALUE_REQUIRED,
        $default = null,
        $allownull = NULL_NOT_ALLOWED
    ) {
        parent::__construct($desc, $required, $default, $allownull);
        $this->keys = $keys;
    }
}
