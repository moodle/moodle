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

namespace local_ai_manager\local;

/**
 * Data object class for handling usage information when using an AI tool.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usage {

    /**
     * Constructor for creating a usage object.
     *
     * @param float $value the value of the overall usage (summed up)
     * @param float $customvalue1 the first customvalue, for example the amount of tokens in the prompt
     * @param float $customvalue2 the second customvalue, for example the amount of tokens in the response
     */
    public function __construct(
            /** @var float $value the value of the overall usage (summed up) */
            public readonly float $value,
            /** @var float $customvalue1 the first customvalue, for example the amount of tokens in the prompt */
            public readonly float $customvalue1 = 0.0,
            /** @var float $customvalue1 the second customvalue, for example the amount of tokens in the response */
            public readonly float $customvalue2 = 0.0
    ) {
    }
}
