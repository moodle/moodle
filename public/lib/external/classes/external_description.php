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
 * Common ancestor of all parameter description classes
 *
 * @package    core_external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class external_description {
    /** @var string Description of element */
    public $desc;

    /** @var bool Element value required, null not allowed */
    public $required;

    /** @var mixed Default value */
    public $default;

    /** @var bool Allow null values */
    public $allownull;

    /**
     * Contructor.
     *
     * @param string $desc Description of element
     * @param int $required Whether the element value is required. Valid values are VALUE_DEFAULT, VALUE_REQUIRED, VALUE_OPTIONAL.
     * @param mixed $default The default value
     * @param bool $allownull Allow null value
     */
    public function __construct($desc, $required, $default, $allownull = NULL_NOT_ALLOWED) {
        if (!in_array($required, [VALUE_DEFAULT, VALUE_REQUIRED, VALUE_OPTIONAL], true)) {
            $requiredstr = $required;
            if (is_array($required)) {
                $requiredstr = "Array: " . implode(" ", $required);
            }
            debugging("Invalid \$required parameter value: '{$requiredstr}' .
                It must be either VALUE_DEFAULT, VALUE_REQUIRED, or VALUE_OPTIONAL", DEBUG_DEVELOPER);
        }
        $this->desc = $desc;
        $this->required = $required;
        $this->default = $default;
        $this->allownull = (bool)$allownull;
    }
}
