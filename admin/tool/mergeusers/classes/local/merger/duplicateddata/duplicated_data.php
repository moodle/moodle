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
 * Entity that gathers both the list of items to update and to delete.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger\duplicateddata;

/**
 * Entity that gathers both the list of items to update and to delete.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class duplicated_data {
    /**
     * Builds en empty entity.
     *
     * @return static
     */
    public static function from_empty() {
        return new static([], []);
    }

    /**
     * Builds the instance with the given lists of ids.
     *
     * @param array $toremove
     * @param array $toupdate
     * @return static
     */
    public static function from_remove_and_update(array $toremove, array $toupdate) {
        return new static(array_combine($toremove, $toremove), array_combine($toupdate, $toupdate));
    }

    /**
     * Builds the instance with just the list of ids to remove.
     *
     * @param array $toremove
     * @return static
     */
    public static function from_remove(array $toremove) {
        return new static(array_combine($toremove, $toremove), []);
    }

    /**
     * Initializes a duplicated data with the given list of ids.
     *
     * @param array $toremove
     * @param array $toupdate
     */
    private function __construct(
        /** @var array list of records ids to delete. */
        private readonly array $toremove,
        /** @var array list of records ids to update. */
        private readonly array $toupdate,
    ) {
    }

    /**
     * List of records ids to remove.
     *
     * @return array
     */
    public function to_remove(): array {
        return $this->toremove;
    }

    /**
     * List the records to update.
     *
     * @return array
     */
    public function to_update(): array {
        return $this->toupdate;
    }
}
