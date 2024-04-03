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

namespace core\progress;

use core\output\stored_progress_bar;

/**
 * Progress handler which updates a stored progress bar.
 *
 * @package    core
 * @copyright  2024 Catalyst IT Europe Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stored extends base {
    /**
     * Constructs the progress reporter.
     *
     * @param stored_progress_bar $bar The stored progress bar to update.
     */
    public function __construct(
        /**
         * @var stored_progress_bar $bar The stored progress bar to update.
         */
        protected stored_progress_bar $bar,
    ) {
    }

    /**
     * Updates the progress in the database.
     * Database update frequency is set by $interval.
     *
     * @see \core\progress\base::update_progress()
     */
    public function update_progress() {
        // Get progress.
        [$min] = $this->get_progress_proportion_range();

        $message = '';
        if ($this->is_in_progress_section()) {
            $message = $this->get_current_description();
        }
        // Update progress bar.
        $this->bar->update_full($min * 100, $message);
    }
}
