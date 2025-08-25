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

namespace core_admin;

/**
 * Process admin search results.
 *
 * @package    core_admin
 * @copyright  2025 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_search {
    /** @var string Search match for a page title. */
    const SEARCH_MATCH_PAGE_TITLE = 'title';

    /** @var string Search match for a setting short name. */
    const SEARCH_MATCH_SETTING_SHORT_NAME = 'shortname';

    /** @var string Search match for a setting display name. */
    const SEARCH_MATCH_SETTING_DISPLAY_NAME = 'displayname';

    /** @var string Search match for a setting value. */
    const SEARCH_MATCH_SETTING_VALUE = 'value';

    /** @var string Search match for a setting helper. */
    const SEARCH_MATCH_SETTING_HELPER = 'helper';

    /**
     * Get a prioritised list of search match types.
     *
     * The order will determine how results will be displayed.
     * Items higher in the list will be displayed first.
     *
     * @return array The list of priorities.
     */
    private static function get_search_match_priorities(): array {
        return [
            self::SEARCH_MATCH_PAGE_TITLE,
            self::SEARCH_MATCH_SETTING_SHORT_NAME,
            self::SEARCH_MATCH_SETTING_DISPLAY_NAME,
            self::SEARCH_MATCH_SETTING_VALUE,
            self::SEARCH_MATCH_SETTING_HELPER,
        ];
    }

    /**
     * Sort search results according to a set of priorities.
     *
     * @param array $results The unsorted results.
     * @return array The sorted results.
     */
    public static function sort_search_results(array $results): array {
        $priorities = self::get_search_match_priorities();
        // If there is no searchmatchype property, use this priority.
        $defaultpriority = count($priorities);
        uasort($results, function ($a, $b) use ($priorities, $defaultpriority) {

            $prioritya = array_search($a->searchmatchtype, $priorities);
            $priorityb = array_search($b->searchmatchtype, $priorities);

            $prioritya = ($prioritya === false) ? $defaultpriority : $prioritya;
            $priorityb = ($priorityb === false) ? $defaultpriority : $priorityb;

            return $prioritya <=> $priorityb;
        });

        return $results;
    }
}
