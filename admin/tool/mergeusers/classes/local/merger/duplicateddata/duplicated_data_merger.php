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
 * Entity that helps merge conflicting records from both user to keep and to remove.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger\duplicateddata;

/**
 * API definition for a duplicated data merger.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface duplicated_data_merger {
    /**
     * Merges both list of records potentially conflicting.
     *
     * @param array $olddata data from the user to remove.
     * @param array $newdata data from the user to keep.
     * @return duplicated_data the result of the merge.
     */
    public function merge(object $olddata, object $newdata): duplicated_data;
}
