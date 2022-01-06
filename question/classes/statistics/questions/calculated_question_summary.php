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
 * Question statistics calculations class. Used in the quiz statistics report.
 *
 * @package    core_question
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\questions;
defined('MOODLE_INTERNAL') || die();

/**
 * Class calculated_question_summary
 *
 * This class is used to indicate the statistics for a random question slot should
 * be rendered with a link to a summary of the displayed questions.
 *
 * It's used in the limited view of the statistics calculation in lieu of adding
 * the stats for each subquestion individually.
 *
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculated_question_summary extends calculated {

    /**
     * @var int only set immediately before display in the table. The order of display in the table.
     */
    public $subqdisplayorder;

    /**
     * @var calculated[] The instances storing the calculated stats of the questions that are being summarised.
     */
    protected $subqstats;

    /**
     * calculated_question_summary constructor.
     *
     * @param \stdClass $question
     * @param int $slot
     * @param calculated[] $subqstats The instances of the calculated stats of the questions that are being summarised.
     */
    public function __construct($question, $slot, $subqstats) {
        parent::__construct($question, $slot);

        $this->subqstats = $subqstats;
        $this->subquestions = implode(',', array_column($subqstats, 'questionid'));
    }

    /**
     * This is a summary stat so never breakdown by variant.
     *
     * @return bool
     */
    public function break_down_by_variant() {
        return false;
    }

    /**
     * Returns the minimum and maximum values of the given attribute in the summarised calculated stats.
     *
     * @param string $attribute The attribute that we are looking for its extremums.
     * @return array An array of [min,max]
     */
    public function get_min_max_of($attribute) {
        $getmethod = 'get_min_max_of_' . $attribute;
        if (method_exists($this, $getmethod)) {
            return $this->$getmethod();
        } else {
            $min = $max = null;
            $set = false;

            // We cannot simply use min or max functions because, in theory, some attributes might be non-scalar.
            foreach (array_column($this->subqstats, $attribute) as $value) {
                if (is_scalar($value) || is_null($value)) {
                    if (!$set) {    // It is not good enough to check if (!isset($min)),
                                    // because $min might have been set to null in an earlier iteration.
                        $min = $value;
                        $max = $value;
                        $set = true;
                    }

                    $min  = $this->min($min, $value);
                    $max  = $this->max($max, $value);
                }
            }

            return [$min, $max];
        }
    }

    /**
     * Returns the minimum and maximum values of the standard deviation in the summarised calculated stats.
     * @return array An array of [min,max]
     */
    protected function get_min_max_of_sd() {
        $min = $max = null;
        $set = false;

        foreach ($this->subqstats as $subqstat) {
            if (isset($subqstat->sd) && $subqstat->maxmark) {
                $value = $subqstat->sd / $subqstat->maxmark;
            } else {
                $value = null;
            }

            if (!$set) {    // It is not good enough to check if (!isset($min)),
                            // because $min might have been set to null in an earlier iteration.
                $min = $value;
                $max = $value;
                $set = true;
            }

            $min = $this->min($min, $value);
            $max = $this->max($max, $value);
        }

        return [$min, $max];
    }

    /**
     * Find higher value.
     * A zero value is almost considered equal to zero in comparisons. The only difference is that when being compared to zero,
     * zero is higher than null.
     *
     * @param float|null $value1
     * @param float|null $value2
     * @return float|null
     */
    protected function max(float $value1 = null, float $value2 = null) {
        $temp1 = $value1 ?: 0;
        $temp2 = $value2 ?: 0;

        $tempmax = max($temp1, $temp2);

        if (!$tempmax && $value1 !== 0 && $value2 !== 0) {
            $max = null;
        } else {
            $max = $tempmax;
        }

        return $max;
    }

    /**
     * Find lower value.
     * A zero value is almost considered equal to zero in comparisons. The only difference is that when being compared to zero,
     * zero is lower than null.
     *
     * @param float|null $value1
     * @param float|null $value2
     * @return mixed|null
     */
    protected function min(float $value1 = null, float $value2 = null) {
        $temp1 = $value1 ?: 0;
        $temp2 = $value2 ?: 0;

        $tempmin = min($temp1, $temp2);

        if (!$tempmin && $value1 !== 0 && $value2 !== 0) {
            $min = null;
        } else {
            $min = $tempmin;
        }

        return $min;
    }
}
