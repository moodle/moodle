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
 * Class for loading/storing data purposes from the DB.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

/**
 * Class for loading/storing data purposes from the DB.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purpose extends \core\persistent {

    /**
     * Database table.
     */
    const TABLE = 'tool_dataprivacy_purpose';

    /** Items under GDPR Article 6.1. */
    const GDPR_ART_6_1_ITEMS = ['a', 'b', 'c', 'd', 'e', 'f'];

    /** Items under GDPR Article 9.2. */
    const GDPR_ART_9_2_ITEMS = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];

    /**
     * Extended constructor to fetch from the cache if available.
     *
     * @param int $id If set, this is the id of an existing record, used to load the data.
     * @param stdClass $record If set will be passed to {@link self::from_record()}.
     */
    public function __construct($id = 0, stdClass $record = null) {
        global $CFG;

        if ($id) {
            $cache = \cache::make('tool_dataprivacy', 'purpose');
            if ($data = $cache->get($id)) {

                // Replicate self::read.
                $this->from_record($data);

                // Validate the purpose record.
                $this->validate();

                // Now replicate the parent constructor.
                if (!empty($record)) {
                    $this->from_record($record);
                }
                if ($CFG->debugdeveloper) {
                    $this->verify_protected_methods();
                }
                return;
            }
        }
        parent::__construct($id, $record);
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'The purpose name.',
            ),
            'description' => array(
                'type' => PARAM_RAW,
                'description' => 'The purpose description.',
                'null' => NULL_ALLOWED,
                'default' => '',
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'lawfulbases' => array(
                'type' => PARAM_TEXT,
                'description' => 'Comma-separated IDs matching records in tool_dataprivacy_lawfulbasis.',
            ),
            'sensitivedatareasons' => array(
                'type' => PARAM_TEXT,
                'description' => 'Comma-separated IDs matching records in tool_dataprivacy_sensitive',
                'null' => NULL_ALLOWED,
                'default' => ''
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
     * Adds the new record to the cache.
     *
     * @return null
     */
    protected function after_create() {
        $cache = \cache::make('tool_dataprivacy', 'purpose');
        $cache->set($this->get('id'), $this->to_record());
    }

    /**
     * Updates the cache record.
     *
     * @param bool $result
     * @return null
     */
    protected function after_update($result) {
        $cache = \cache::make('tool_dataprivacy', 'purpose');
        $cache->set($this->get('id'), $this->to_record());
    }

    /**
     * Removes unnecessary stuff from db.
     *
     * @return null
     */
    protected function before_delete() {
        $cache = \cache::make('tool_dataprivacy', 'purpose');
        $cache->delete($this->get('id'));
    }

    /**
     * Is this purpose used?.
     *
     * @return null
     */
    public function is_used() {

        if (\tool_dataprivacy\contextlevel::is_purpose_used($this->get('id')) ||
                \tool_dataprivacy\context_instance::is_purpose_used($this->get('id'))) {
            return true;
        }

        $pluginconfig = get_config('tool_dataprivacy');
        $levels = \context_helper::get_all_levels();
        foreach ($levels as $level => $classname) {

            list($purposevar, $unused) = \tool_dataprivacy\data_registry::var_names_from_context($classname);
            if (!empty($pluginconfig->{$purposevar}) && $pluginconfig->{$purposevar} == $this->get('id')) {
                return true;
            }
        }

        return false;
    }
}
