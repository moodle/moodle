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
 * Moodle API functions.
 *
 * @package search_solr
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Gets status checks contributed by this plugin.
 *
 * If Solr is enabled and indexing is on, returns a check that the connection works.
 *
 * @return core\check\check[] Array of status checks
 */
function search_solr_status_checks(): array {
    global $CFG;

    // No checks if search engine is not set to Solr, or is disabled.
    if (!\core_search\manager::is_indexing_enabled() || $CFG->searchengine !== 'solr') {
        return [];
    }

    // Since it's turned on and set to Solr, configuration really should be OK and we ought to
    // show if it isn't, so turn on the check.
    return [new \search_solr\check\connection()];
}
