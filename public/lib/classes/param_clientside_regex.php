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

namespace core;

use Attribute;

/**
 * A JS-compatible regular expression to validate the format of a param.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class param_clientside_regex {
    /**
     * Create a clientside regular expression for use with a \core\param enum case.
     *
     * @param string $regex The Regular Expression that validates the param case
     */
    public function __construct(
        /** @var string The Regular Expression that validates the param case */
        public readonly string $regex,
    ) {
    }
}
