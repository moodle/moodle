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
 * Keeps track of the analysis results.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analysis;

defined('MOODLE_INTERNAL') || die();

/**
 * Keeps track of the analysis results.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class result {

    /**
     * @var int
     */
    protected $modelid;

    /**
     * @var bool
     */
    protected $includetarget;

    /**
     * @var array Analysis options
     */
    protected $options;

    /**
     * Stores analysis data at instance level.
     * @param int   $modelid
     * @param bool  $includetarget
     * @param array $options
     */
    public function __construct(int $modelid, bool $includetarget, array $options) {
        $this->modelid = $modelid;
        $this->includetarget = $includetarget;
        $this->options = $options;
    }

    /**
     * Retrieves cached results during evaluation.
     *
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @param  \core_analytics\analysable                $analysable
     * @return mixed It can be in whatever format the result uses.
     */
    public function retrieve_cached_result(\core_analytics\local\time_splitting\base $timesplitting,
        \core_analytics\analysable $analysable) {
        return false;
    }

    /**
     * Stores the analysis results.
     *
     * @param  array $results
     * @return bool            True if anything was successfully analysed
     */
    abstract public function add_analysable_results(array $results): bool;

    /**
     * Formats the result.
     *
     * @param  array                                     $data
     * @param  \core_analytics\local\target\base         $target
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @param  \core_analytics\analysable                $analysable
     * @return mixed It can be in whatever format the result uses
     */
    abstract public function format_result(array $data, \core_analytics\local\target\base $target,
            \core_analytics\local\time_splitting\base $timesplitting, \core_analytics\analysable $analysable);

    /**
     * Returns the results of the analysis.
     * @return array
     */
    abstract public function get(): array;
}