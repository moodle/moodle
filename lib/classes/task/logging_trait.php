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
 * This file defines a trait to assist with logging in tasks.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * This trait includes functions to assist with logging in tasks.
 *
 * @package    core
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait logging_trait {

    /**
     * @var \progress_trace
     */
    protected $_trace = null;

    /**
     * @var \stdClass
     */
    protected $_tracestats = null;

    /**
     * Get the progress_trace.
     *
     * @return  \progress_trace
     */
    protected function get_trace() {
        if (null === $this->_trace) {
            $this->_trace = new \text_progress_trace();
            $this->_tracestats = (object) [];
        }

        return $this->_trace;
    }

    /**
     * Log a message to the progress tracer.
     *
     * @param   string  $message
     * @param   int     $depth
     */
    protected function log($message, $depth = 1) {
        $this->get_trace()
            ->output($message, $depth);
    }

    /**
     * Log a start message to the progress tracer.
     *
     * @param   string  $message
     * @param   int     $depth
     */
    protected function log_start($message, $depth = 0) {
        $this->get_trace();

        if (defined('MDL_PERFTOLOG') && MDL_PERFTOLOG) {
            $this->_tracestats->$depth = [
                'mem' => memory_get_usage(),
                'time' => microtime(),
            ];
        }

        $this->log($message, $depth);
    }

    /**
     * Log an end message to the progress tracer.
     *
     * @param   string  $message
     * @param   int     $depth
     */
    protected function log_finish($message, $depth = 0) {
        $this->log($message, $depth);
        if (isset($this->_tracestats->$depth)) {
            $startstats = $this->_tracestats->$depth;
            $this->log(
                    sprintf("Time taken %s, memory total: %s, Memory growth: %s, Memory peak: %s",
                        microtime_diff($startstats['time'], microtime()),
                        display_size(memory_get_usage()),
                        display_size(memory_get_usage() - $startstats['mem']),
                        display_size(memory_get_peak_usage())
                    ),
                    $depth + 1
                );
        }
    }
}
