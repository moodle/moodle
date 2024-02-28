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
 * Class for loading/storing data purpose overrides from the DB.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

/**
 * Class for loading/storing data purpose overrides from the DB.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purpose_override extends \core\persistent {

    /**
     * Database table.
     */
    const TABLE = 'tool_dataprivacy_purposerole';

    /**
     * Return the definition of the properties of this model.
     *
     * @return  array
     */
    protected static function define_properties() {
        return array(
            'purposeid' => array(
                'type' => PARAM_INT,
                'description' => 'The purpose that that this override relates to',
            ),
            'roleid' => array(
                'type' => PARAM_INT,
                'description' => 'The role that that this override relates to',
            ),
            'lawfulbases' => array(
                'type' => PARAM_TEXT,
                'description' => 'Comma-separated IDs matching records in tool_dataprivacy_lawfulbasis.',
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'sensitivedatareasons' => array(
                'type' => PARAM_TEXT,
                'description' => 'Comma-separated IDs matching records in tool_dataprivacy_sensitive',
                'null' => NULL_ALLOWED,
                'default' => null,
            ),
            'retentionperiod' => array(
                'type' => PARAM_ALPHANUM,
                'description' => 'Retention period. ISO_8601 durations format (as in DateInterval format).',
                'default' => '',
            ),
            'protected' => array(
                'type' => PARAM_INT,
                'description' => 'Data retention with higher precedent over user\'s request to be forgotten.',
                'default' => '0',
            ),
        );
    }

    /**
     * Get all role overrides for the purpose.
     *
     * @param   purpose $purpose
     * @return  array
     */
    public static function get_overrides_for_purpose(purpose $purpose): array {
        $cache = \cache::make('tool_dataprivacy', 'purpose_overrides');

        $overrides = [];
        $alldata = $cache->get($purpose->get('id'));
        if (false === $alldata) {
            $tocache = [];
            foreach (self::get_records(['purposeid' => $purpose->get('id')]) as $override) {
                $tocache[] = $override->to_record();
                $overrides[$override->get('roleid')] = $override;
            }
            $cache->set($purpose->get('id'), $tocache);
        } else {
            foreach ($alldata as $data) {
                $override = new self(0, $data);
                $overrides[$override->get('roleid')] = $override;
            }
        }

        return $overrides;
    }

    /**
     * Adds the new record to the cache.
     *
     * @return null
     */
    protected function after_create() {
        $cache = \cache::make('tool_dataprivacy', 'purpose_overrides');
        $cache->delete($this->get('purposeid'));
    }

    /**
     * Updates the cache record.
     *
     * @param bool $result
     * @return null
     */
    protected function after_update($result) {
        $cache = \cache::make('tool_dataprivacy', 'purpose_overrides');
        $cache->delete($this->get('purposeid'));
    }

    /**
     * Removes unnecessary stuff from db.
     *
     * @return null
     */
    protected function before_delete() {
        $cache = \cache::make('tool_dataprivacy', 'purpose_overrides');
        $cache->delete($this->get('purposeid'));
    }
}
