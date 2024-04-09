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

namespace mod_quiz\output\grades;

use html_writer;
use renderable;
use stdClass;

/**
 * Represents a grade out of a give total, that wants to be output in a particular way.
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2024 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_out_of implements renderable {
    /** @var string Indicates we want a short rendering. Also the lang string used. */
    const SHORT = 'outofshort';

    /** @var string Indicates we want the default rendering. Also the lang string used. */
    const NORMAL = 'outof';

    /** @var string like normal, but with the percent equivalent in brackets. Also the lang string used */
    const WITH_PERCENT = 'outofpercent';

    /**
     * Constructor.
     *
     * @param stdClass $quiz Quiz settings.
     * @param float $grade the mark to show.
     * @param float $maxgrade the total to show it out of.
     * @param string|null $name optional, a name for what this grade is.
     * @param string $style which format to use, grade_out_of::NORMAL (default), ::SHORT or ::WITH_PERCENT.
     */
    public function __construct(

        /** @var stdClass Quiz settings (so we can access the settings like decimal places). */
        public readonly stdClass $quiz,

        /** @var float the grade to show. */
        public float $grade,

        /** @var float the total the grade is out of. */
        public float $maxgrade,

        /** @var string|null optional, a name for what this grade is. Must be output via format_string. */
        public readonly ?string $name = null,

        /** @var string The display style, one of the consts above. */
        public readonly string $style = self::NORMAL,

    ) {
    }

    /**
     * Get the lang string to use to display the grade in the requested style.
     *
     * @return string lang string key from the mod_quiz lang pack.
     */
    public function get_string_key(): string {
        return $this->style;
    }

    /**
     * Get the formatted values to be inserted into the {@see get_string_key()} string placeholders.
     *
     * Values are not styled. To apply the recommended styling, call {@see style_formatted_values()}
     *
     * @return stdClass to be passed as the third argument to get_string().
     */
    public function get_formatted_values(): stdClass {
        $a = new stdClass();
        $a->grade = quiz_format_grade($this->quiz, $this->grade);
        $a->maxgrade = quiz_format_grade($this->quiz, $this->maxgrade);
        if ($this->style === self::WITH_PERCENT) {
            $a->percent = format_float($this->grade * 100 / $this->maxgrade,
                    $this->quiz->decimalpoints, true, true);
        }
        return $a;
    }

    /**
     * Apply the normal styling to the values returned by {@see get_formatted_values()}.
     *
     * @param stdClass $a formatted values, as returned by get_formatted_values.
     * @return stdClass same structure, with some values wrapped in &lt;b> tags.
     */
    public function style_formatted_values(stdClass $a): stdClass {
        if ($this->style !== self::SHORT) {
            $a->grade = html_writer::tag('b', $a->grade);
        }
        if ($this->style === self::WITH_PERCENT) {
            $a->percent = html_writer::tag('b', $a->percent);
        }
        return $a;
    }
}
