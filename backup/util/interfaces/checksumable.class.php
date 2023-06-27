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
 * @package    moodlecore
 * @subpackage backup-interfaces
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Interface to apply to all the classes we want to calculate their checksum
 *
 * Each class being part of @backup_controller will implement this interface
 * in order to be able to calculate one objective and unique checksum for
 * the whole controller class.
 *
 * TODO: Finish phpdocs
 */
interface checksumable {

    /**
     * This function will return one unique and stable checksum for one instance
     * of the class implementing it. It's each implementation responsibility to
     * do it recursively if needed and use optional store (caching) of the checksum if
     * necessary/possible
     */
    public function calculate_checksum();

    /**
     * Given one checksum, returns if matches object's checksum (true) or no (false)
     */
    public function is_checksum_correct($checksum);

}
