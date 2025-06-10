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
 * The class defines a configuration object with range of questions difficulty. Acts as a container for related pieces
 * of data - a value object. Normally is set from a corresponding activity record's values, thus, it doesn't perform
 * any validation of the parameters when instantiated.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report;

use stdClass;

final class questions_difficulty_range {

    /**
     * @var int $lowestlevel
     */
    private $lowestlevel;

    /**
     * @var int $highestlevel
     */
    private $highestlevel;

    private function __construct(int $lowestlevel, int $highestlevel) {
        $this->lowestlevel = $lowestlevel;
        $this->highestlevel = $highestlevel;
    }

    public function lowest_level(): int {
        return $this->lowestlevel;
    }

    public function highest_level(): int {
        return $this->highestlevel;
    }

    /**
     * @param stdClass $instance A record from {adaptivequiz}.
     */
    public static function from_activity_instance(stdClass $instance): self {
        return new self($instance->lowestlevel, $instance->highestlevel);
    }
}
