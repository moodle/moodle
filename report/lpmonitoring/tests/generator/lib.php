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
 * Competency data generator.
 *
 * @package    report_lpmonitoring
 * @category   test
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use report_lpmonitoring\report_competency_config;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Report competency config data generator class.
 *
 * @package    report_lpmonitoring
 * @category   test
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_lpmonitoring_generator extends component_generator_base {

    /**
     * Create a new report_competency_config.
     *
     * @param array|stdClass $record
     * @return recport_competency_config
     */
    public function create_report_competency_config($record = null) {
        $record = (object) $record;

        if (!isset($record->competencyframeworkid)) {
            throw new coding_exception('The competencyframeworkid value is required.');
        }
        if (!isset($record->scaleid)) {
            throw new coding_exception('The scaleid value is required.');
        }
        if (isset($record->scaleconfiguration)
                && (is_array($record->scaleconfiguration) || is_object($record->scaleconfiguration))) {
            // Conveniently encode the config.
            $record->scaleconfiguration = json_encode($record->scaleconfiguration);
        }

        $reportcompetencyconfig = new report_competency_config(0, $record);

        return $reportcompetencyconfig->create();
    }
}
