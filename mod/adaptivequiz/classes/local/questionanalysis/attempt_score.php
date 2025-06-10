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
 * This class provides access to various numeric representations of a score.
 *
 * @copyright  2013 Middlebury College {@link http://www.middlebury.edu/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\questionanalysis;

use mod_adaptivequiz\local\catalgo;

class attempt_score {

    /** @var float $measuredabilitylogits The measured ability of the attempt in logits. */
    protected $measuredabilitylogits = null;

    /** @var float $standarderrorlogits The standard error in the score in logits. */
    protected $standarderrorlogits = null;

    /** @var float $lowestlevel The lowest level of question in the adaptive quiz. */
    protected $lowestlevel = null;

    /** @var float $highestlevel The highest level of question in the adaptive quiz. */
    protected $highestlevel = null;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct ($measuredabilitylogits, $standarderrorlogits, $lowestlevel, $highestlevel) {
        $this->measuredabilitylogits = $measuredabilitylogits;
        $this->standarderrorlogits = $standarderrorlogits;
        $this->lowestlevel = $lowestlevel;
        $this->highestlevel = $highestlevel;
    }

    /**
     * Answer the measured ability in logits.
     *
     * @return float
     */
    public function measured_ability_in_logits () {
        return $this->measuredabilitylogits;
    }

    /**
     * Answer the standard error in logits.
     *
     * @return float
     */
    public function standard_error_in_logits () {
        return $this->standarderrorlogits;
    }

    /**
     * Answer the measured ability as a fraction 0-1.
     *
     * @return float
     */
    public function measured_ability_in_fraction () {
        return catalgo::convert_logit_to_fraction($this->measuredabilitylogits);
    }

    /**
     * Answer the standard error a fraction 0-0.5.
     *
     * @return float
     */
    public function standard_error_in_fraction () {
        return catalgo::convert_logit_to_percent($this->standarderrorlogits);
    }

    /**
     * Answer the measured ability on the adaptive quiz's scale
     *
     * @return float
     */
    public function measured_ability_in_scale () {
        return catalgo::map_logit_to_scale($this->measuredabilitylogits, $this->highestlevel, $this->lowestlevel);
    }

    /**
     * Answer the standard error on the adaptive quiz's scale
     *
     * @return float
     */
    public function standard_error_in_scale () {
        return catalgo::convert_logit_to_percent($this->standarderrorlogits) * ($this->highestlevel - $this->lowestlevel);
    }
}
