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

namespace core\attribute;

/**
 * Attribute to describe a deprecated item.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\Attribute]
class deprecated {
    /**
     * A deprecated item.
     *
     * This attribute can be applied to any function, class, method, constant, property, enum, etc.
     *
     * Note: The mere presence of the attribute does not do anything. It must be checked by some part of the code.
     *
     * @param null|string $replacement Any replacement for the deprecated thing
     * @param null|string $since When it was deprecated
     * @param null|string $reason Why it was deprecated
     * @param null|string $mdl Link to the Moodle Tracker issue for more information
     * @param bool $final Whether this is a final deprecation
     * @param bool $emit Whether to emit a deprecation warning
     */
    public function __construct(
        public readonly ?string $replacement,
        public readonly ?string $since = null,
        public readonly ?string $reason = null,
        public readonly ?string $mdl = null,
        public readonly bool $final = false,
        public readonly bool $emit = true,
    ) {
        if ($replacement === null && $reason === null && $mdl === null) {
            throw new \coding_exception(
                'A deprecated item which is not deprecated must provide a reason, or an issue number.',
            );
        }
    }
}
