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
class activity extends \core_search\base_activity {

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Returns recordset containing required data for indexing activities.
     *
     * Overridden to discard records with courseid = 0.
     *
     * @param int $modifiedfrom timestamp
     * @param \context|null $context Context
     * @return \moodle_recordset|null Recordset, or null if no possible activities in given context
     */
    public function get_document_recordset($modifiedfrom = 0, ?\context $context = null) {
        global $DB;
        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql(
                $context, $this->get_module_name(), 'modtable');
        if ($contextjoin === null) {
            return null;
        }
        return $DB->get_recordset_sql('SELECT modtable.* FROM {' . $this->get_module_name() .
                '} modtable ' . $contextjoin . ' WHERE modtable.' . static::MODIFIED_FIELD_NAME .
                ' >= ? AND modtable.course != ? ORDER BY modtable.' . static::MODIFIED_FIELD_NAME .
                ' ASC',
                array_merge($contextparams, [$modifiedfrom, 0]));
    }

}
