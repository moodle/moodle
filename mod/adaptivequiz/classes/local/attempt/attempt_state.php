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
 * A class to emulate enum type for attempt state.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_adaptivequiz\local\attempt;

final class attempt_state {

    public const IN_PROGRESS = 'inprogress';

    public const COMPLETED = 'complete';

    /**
     * @var string $stateasstring
     */
    private $stateasstring;

    private function __construct(string $state) {
        $this->stateasstring = $state;
    }

    public function is_in_progress(): bool {
        return self::IN_PROGRESS === $this->stateasstring;
    }

    public function is_completed(): bool {
        return self::COMPLETED === $this->stateasstring;
    }

    public static function in_progress(): self {
        return new self(self::IN_PROGRESS);
    }

    public static function completed(): self {
        return new self(self::COMPLETED);
    }
}
