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

namespace mod_quiz\question;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/engine/lib.php');

/**
 * An extension of question_display_options that includes the extra options used by the quiz.
 *
 * @package   mod_quiz
 * @category  question
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class display_options extends \question_display_options {
    /**
     * The bitmask patterns use in the review option settings.
     *
     * In the quiz settings, the review... (e.g. reviewmarks) values are
     * bit patterns that allow what is visible to be change at different times.
     * These constants define which bit is for which time.
     *
     * @var int bit used to indicate 'during the attempt'.
     */
    const DURING = 0x10000;

    /** @var int as above, bit used to indicate 'immediately after the attempt'. */
    const IMMEDIATELY_AFTER = 0x01000;

    /** @var int as above, bit used to indicate 'later while the quiz is still open'. */
    const LATER_WHILE_OPEN = 0x00100;

    /** @var int as above, bit used to indicate 'after the quiz is closed'. */
    const AFTER_CLOSE = 0x00010;

    /**
     * @var bool if this is false, then the student is not allowed to review
     * anything about the attempt.
     */
    public $attempt = true;

    /**
     * @var int whether the attempt overall feedback is visible.
     */
    public $overallfeedback = self::VISIBLE;

    /**
     * Set up the various options from the quiz settings, and a time constant.
     *
     * @param \stdClass $quiz the quiz settings from the database.
     * @param int $when of the constants {@see DURING}, {@see IMMEDIATELY_AFTER},
     *      {@see LATER_WHILE_OPEN} or {@see AFTER_CLOSE}.
     * @return display_options instance of this class set up appropriately.
     */
    public static function make_from_quiz(\stdClass $quiz, int $when): self {
        $options = new self();

        $options->attempt = self::extract($quiz->reviewattempt, $when, true, false);
        $options->correctness = self::extract($quiz->reviewcorrectness, $when);
        $options->marks = self::extract($quiz->reviewmarks, $when,
                self::MARK_AND_MAX, self::MAX_ONLY);
        $options->feedback = self::extract($quiz->reviewspecificfeedback, $when);
        $options->generalfeedback = self::extract($quiz->reviewgeneralfeedback, $when);
        $options->rightanswer = self::extract($quiz->reviewrightanswer, $when);
        $options->overallfeedback = self::extract($quiz->reviewoverallfeedback, $when);

        $options->numpartscorrect = $options->feedback;
        $options->manualcomment = $options->feedback;

        if ($quiz->questiondecimalpoints != -1) {
            $options->markdp = $quiz->questiondecimalpoints;
        } else {
            $options->markdp = $quiz->decimalpoints;
        }

        return $options;
    }

    /**
     * Helper function to return one value or another depending on whether one bit is set.
     *
     * @param int $setting the setting to unpack (e.g. $quiz->reviewmarks).
     * @param int $when of the constants {@see DURING}, {@see IMMEDIATELY_AFTER},
     *      {@see LATER_WHILE_OPEN} or {@see AFTER_CLOSE}.
     * @param bool|int $whenset value to return when the bit is set.
     * @param bool|int $whennotset value to return when the bit is set.
     * @return bool|int $whenset or $whennotset, depending.
     */
    protected static function extract(int $setting, int $when,
            $whenset = self::VISIBLE, $whennotset = self::HIDDEN) {
        if ($setting & $when) {
            return $whenset;
        } else {
            return $whennotset;
        }
    }
}
