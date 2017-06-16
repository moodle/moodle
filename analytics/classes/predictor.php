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
 * Predictions processor interface.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Predictors interface.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface predictor {

    /**
     * Is it ready to predict?
     *
     * @return bool
     */
    public function is_ready();

    /**
     * Train the provided dataset.
     *
     * @param int $modelid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function train($modelid, \stored_file $dataset, $outputdir);

    /**
     * Predict the provided dataset samples.
     *
     * @param int $modelid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function predict($modelid, \stored_file $dataset, $outputdir);

    /**
     * evaluate
     *
     * @param int $modelid
     * @param float $maxdeviation
     * @param int $niterations
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function evaluate($modelid, $maxdeviation, $niterations, \stored_file $dataset, $outputdir);
}
