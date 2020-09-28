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
 * Class cli_progress_tracker
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_uploaduser\local;

/**
 * Tracks the progress of the user upload and outputs it in CLI script (writes to STDOUT)
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cli_progress_tracker extends text_progress_tracker {

    /**
     * Output one line (followed by newline)
     * @param string $line
     */
    protected function output_line(string $line): void {
        cli_writeln($line);
    }
}
