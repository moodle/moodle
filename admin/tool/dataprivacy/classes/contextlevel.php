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
 * Class for loading/storing context level data from the DB.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;
defined('MOODLE_INTERNAL') || die();

/**
 * Class for loading/storing context level data from the DB.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contextlevel extends \core\persistent {

    /**
     * Database table.
     */
    const TABLE = 'tool_dataprivacy_ctxlevel';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'contextlevel' => array(
                'type' => PARAM_INT,
                'description' => 'The context level.',
            ),
            'purposeid' => array(
                'type' => PARAM_INT,
                'description' => 'The purpose id.',
            ),
            'categoryid' => array(
                'type' => PARAM_INT,
                'description' => 'The category id.',
            ),
        );
    }

    /**
     * Returns an instance by contextlevel.
     *
     * @param mixed $contextlevel
     * @param mixed $exception
     * @return null
     */
    public static function get_record_by_contextlevel($contextlevel, $exception = true) {
        global $DB;

        $cache = \cache::make('tool_dataprivacy', 'contextlevel');
        if ($data = $cache->get($contextlevel)) {
            return new static(0, $data);
        }

        if (!$record = $DB->get_record(self::TABLE, array('contextlevel' => $contextlevel))) {
            if (!$exception) {
                return false;
            } else {
                throw new \dml_missing_record_exception(self::TABLE);
            }
        }

        return new static(0, $record);
    }

    /**
     * Is the provided purpose used by any contextlevel?
     *
     * @param int $purposeid
     * @return bool
     */
    public static function is_purpose_used($purposeid) {
        global $DB;
        return $DB->record_exists(self::TABLE, array('purposeid' => $purposeid));
    }

    /**
     * Is the provided category used by any contextlevel?
     *
     * @param int $categoryid
     * @return bool
     */
    public static function is_category_used($categoryid) {
        global $DB;
        return $DB->record_exists(self::TABLE, array('categoryid' => $categoryid));
    }

    /**
     * Adds the new record to the cache.
     *
     * @return null
     */
    protected function after_create() {
        $cache = \cache::make('tool_dataprivacy', 'contextlevel');
        $cache->set($this->get('contextlevel'), $this->to_record());
    }

    /**
     * Updates the cache record.
     *
     * @param bool $result
     * @return null
     */
    protected function after_update($result) {
        $cache = \cache::make('tool_dataprivacy', 'contextlevel');
        $cache->set($this->get('contextlevel'), $this->to_record());
    }

    /**
     * Removes unnecessary stuff from db.
     *
     * @return null
     */
    protected function before_delete() {
        $cache = \cache::make('tool_dataprivacy', 'contextlevel');
        $cache->delete($this->get('contextlevel'));
    }
}
