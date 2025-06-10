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
 * A class to display a table with user's own attempts on the activity's view page.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\output;

use renderable;
use stdClass;

final class user_attempt_summary implements renderable {

    /**
     * @var string $attemptstate
     */
    public $attemptstate;
    /**
     * @var int $timefinished
     */
    public $timefinished;
    /**
     * @var float $abilitymeasure
     */
    public $abilitymeasure;
    /**
     * @var int $lowestquestiondifficulty
     */
    public $lowestquestiondifficulty;
    /**
     * @var int $highestquestiondifficulty
     */
    public $highestquestiondifficulty;

    /**
     * @param stdClass $attempt A record from {adaptivequiz_attempt}. attemptstate, timemodified, measure are
     * the expected fields.
     * @param stdClass $adaptivequiz A record from {adaptivequiz}. lowestlevel, highestlevel, showabilitymeasure are
     * the expected fields.
     */
    public static function from_db_records(stdClass $attempt, stdClass $adaptivequiz): self {
        $return = new self();
        $return->attemptstate = !empty($attempt->attemptstate) ? $attempt->attemptstate : '';
        $return->timefinished = !empty($attempt->timemodified) ? $attempt->timemodified : 0;
        $return->abilitymeasure = !empty($attempt->measure) && $adaptivequiz->showabilitymeasure
            ? $attempt->measure
            : 0;
        $return->lowestquestiondifficulty = !empty($adaptivequiz->lowestlevel) ? $adaptivequiz->lowestlevel : 0;
        $return->highestquestiondifficulty = !empty($adaptivequiz->highestlevel) ? $adaptivequiz->highestlevel : 0;

        return $return;
    }
}
