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

namespace tool_ally;

use core_files\hook\after_file_created;

/**
 * Hook callbacks for tool_ally.
 *
 * @package   tool_ally
 * @copyright 2024 onwards University College London {@link https://www.ucl.ac.uk/}
 * @author    Ivan Lam (lkcivan@gmail.com)
 * @author    Leon Stringer (leon.stringer@ucl.ac.uk)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Callback for after file created.
     * @param \core_files\hook\after_file_created $hook
     */
    public static function after_file_created(after_file_created $hook): void {
        file_processor::push_file_update($hook->storedfile);
        cache::instance()->invalidate_file_keys($hook->storedfile);
    }
}
