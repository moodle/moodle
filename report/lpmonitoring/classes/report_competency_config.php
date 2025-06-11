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
 * Class for loading/storing competency frameworks from the DB.
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;
defined('MOODLE_INTERNAL') || die();

use core_competency\competency_framework;
use lang_string;

require_once($CFG->libdir . '/grade/grade_scale.php');

/**
 * Class for loading/storing report competency configuration from the DB.
 *
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_competency_config extends \core\persistent {

    /** Table name. */
    const TABLE = 'report_competency_config';

    /** Default color. */
    const DEFAULT_COLOR = '#C1C7C9';

    /**
     * Magic method to capture getters and setters.
     * This is only available for competency persistents for backwards compatibility.
     * It is recommended to use get('propertyname') and set('propertyname', 'value') directly.
     *
     * @param  string $method Callee.
     * @param  array $arguments List of arguments.
     * @return mixed
     */
    final public function __call($method, $arguments) {
        debugging('deprecated magic method in report_competency_config', DEBUG_DEVELOPER);
        if (strpos($method, 'get_') === 0) {
            return $this->get(substr($method, 4));
        } else if (strpos($method, 'set_') === 0) {
            return $this->set(substr($method, 4), $arguments[0]);
        }
        throw new \coding_exception('Unexpected method call: ' . $method);
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'competencyframeworkid' => [
                'type' => PARAM_INT,
            ],
            'scaleid' => [
                'type' => PARAM_INT,
            ],
            'scaleconfiguration' => [
                'type' => PARAM_RAW,
                'default' => '',
            ],
        ];
    }

    /**
     * Return the scale.
     *
     * @return \grade_scale
     */
    public function get_scale() {
        $scale = \grade_scale::fetch(['id' => $this->get('scaleid')]);
        if ($scale) {
            $scale->load_items();
        }
        return $scale;
    }

    /**
     * Validate the framework id number.
     *
     * @param  string $value The id number of the framework.
     * @return bool|lang_string
     */
    protected function validate_competencyframeworkid($value) {
        global $DB;

        $params = [
            'id' => $value,
        ];

        if (!$DB->record_exists_select(competency_framework::TABLE, 'id = :id', $params)) {
            return new lang_string('invalidframework', 'report_lpmonitoring');
        }

        return true;
    }

    /**
     * Validate the scale ID.
     *
     * @param  string $value The scale ID.
     * @return bool|lang_string
     */
    protected function validate_scaleid($value) {
        global $DB;

        // Always validate that the scale exists.
        if (!$DB->record_exists_select('scale', 'id = :id', ['id' => $value])) {
            return new lang_string('invalidscaleid', 'error');
        }

        return true;
    }

    /**
     * Validate the scale configuration.
     *
     * @param  string $value The scale configuration.
     * @return bool|lang_string
     */
    protected function validate_scaleconfiguration($value) {
        $scale = self::get_scale();
        if (!$scale) {
            return true;
        }

        $scaleitems = $scale->scale_items;
        $scaleconfiguration = json_decode($this->get('scaleconfiguration'));
        foreach ($scaleitems as $key => $value) {
            if (empty($scaleconfiguration) || !array_key_exists($key, $scaleconfiguration)) {
                return new lang_string('invalidscaleconfiguration', 'report_lpmonitoring');
            }
        }
        return true;
    }

    /**
     * Set default values for scale
     *
     */
    public function set_default_scaleconfiguration() {
        $scale = self::get_scale();
        $scaleitems = $scale->scale_items;
        $scaleconfiguration = json_decode($this->get('scaleconfiguration'));
        foreach ($scaleitems as $key => $value) {
            if (empty($scaleconfiguration) || !array_key_exists($key, $scaleconfiguration)) {
                $scaleconfiguration[$key] = ['id' => $key + 1, 'color' => self::DEFAULT_COLOR];
            }
        }
        $this->set('scaleconfiguration', json_encode($scaleconfiguration));
    }

    /**
     * Get a scale configuration for a framework and a scale.
     *
     * @param int $frameworkid The framework id
     * @param int $scaleid The scale id
     * @return false|report_competency_config
     */
    public static function read_framework_scale_config($frameworkid, $scaleid) {
        global $DB;

        $sql = 'SELECT *
                  FROM {' . self::TABLE . '}
                 WHERE competencyframeworkid = ? AND scaleid = ?';
        $params = [$frameworkid, $scaleid];

        $record = $DB->get_record_sql($sql, $params);
        if (!$record) {
            return false;
        }
        return new report_competency_config(0, $record);
    }

}
