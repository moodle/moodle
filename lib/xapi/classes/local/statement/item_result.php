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
 * Statement result for xAPI structure checking and usage.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use core_xapi\xapi_exception;
use DateInterval;
use Exception;
use stdClass;

/**
 * Abstract xAPI result class.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_result extends item {

    /** @var int The second of duration if present. */
    protected $duration;

    /** @var item_score the result score if present. */
    protected $score;

    /**
     * Function to create a result from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @param int $duration duration in seconds
     * @param item_score $score the provided score
     */
    protected function __construct(stdClass $data, int $duration = null, item_score $score = null) {
        parent::__construct($data);
        $this->duration = $duration;
        $this->score = $score;
    }

    /**
     * Function to create an item from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item item_result xAPI generated
     */
    public static function create_from_data(stdClass $data): item {

        $duration = null;
        if (!empty($data->duration)) {
            try {
                // Duration uses ISO 8601 format which is ALMOST compatible with PHP DateInterval.
                // Because we are mesuring human time we get rid of milliseconds, which are not
                // compatible with DateInterval (More info: https://bugs.php.net/bug.php?id=53831),
                // all other fractions like "P1.5Y" will throw an exception.
                $value = preg_replace('/[.,][0-9]*S/', 'S', $data->duration);
                $interval = new DateInterval($value);
                $duration = date_create('@0')->add($interval)->getTimestamp();
            } catch (Exception $e) {
                throw new xapi_exception('Invalid duration format.');
            }
        }

        $score = null;
        if (!empty($data->score)) {
            $score = item_score::create_from_data($data->score);
        }

        return new self($data, $duration, $score);
    }

    /**
     * Returns the duration in seconds (if present).
     *
     * @return int|null duration in seconds
     */
    public function get_duration(): ?int {
        return $this->duration;
    }

    /**
     * Returns the score.
     *
     * @return item_score|null the score item
     */
    public function get_score(): ?item_score {
        return $this->score;
    }
}
