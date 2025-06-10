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
 * The class represents questions number per each difficulty, this is what
 * {@link questions_repository::count_questions_number_per_difficulty()} returns.
 * The purpose of this class is keeping the related pieces of data together, as the client code normally requires both
 * difficulty level and number of questions for this difficulty set to perform its task.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\repository;

final class questions_number_per_difficulty {
    /**
     * @var int $difficulty
     */
    private $difficulty;

    /**
     * @var int $questionsnumber
     */
    private $questionsnumber;

    public function __construct(int $difficulty, int $questionsnumber) {
        $this->difficulty = $difficulty;
        $this->questionsnumber = $questionsnumber;
    }

    public function difficulty(): int {
        return $this->difficulty;
    }

    public function questions_number(): int {
        return $this->questionsnumber;
    }
}
