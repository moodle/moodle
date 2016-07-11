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
 * Search area for mod_survey activities.
 *
 * @package    mod_survey
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_survey\search;

defined('MOODLE_INTERNAL') || die();

/**
 * Search area for mod_survey activities.
 *
 * @package    mod_survey
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity extends \core_search\area\base_activity {

    /**
     * Returns recordset containing required data for indexing activities.
     *
     * Overwritten to discard records with courseid = 0.
     *
     * @param int $modifiedfrom timestamp
     * @return \moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;
        $select = 'course != ? AND ' . static::MODIFIED_FIELD_NAME . ' >= ?';
        return $DB->get_recordset_select($this->get_module_name(), $select, array(0, $modifiedfrom),
                static::MODIFIED_FIELD_NAME . ' ASC');
    }

}
