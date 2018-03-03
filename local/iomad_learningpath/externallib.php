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
 * Web service declarations
 *
 * @package    local_iomadlearninpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class local_iomad_learningpath_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function activate_parameters() {
        return new external_function_parameters(
            array(
                'pathid' => new external_value(PARAM_INT, 'ID of Learning Path'),
                'state' => new external_value(PARAM_INT, 'Active (1) / deactivate (0)'),
            )
        );
    }

    /** 
     * Returns description of method result
     * @return external_description
     */
    public static function activate_returns() {
        return new external_value(PARAM_BOOL, 'True if active state set correctly');
    }

    /**
     * Activate / Deactivate learning path
     * @param int $pathid 
     * @param int $state
     * @throws invalid_parameter_exception
     */
    public static function activate($pathid, $state) {
        global $DB;
error_log('got here at least id = ' . $pathid . ' state = ' . $state);

        // Validate params
        $params = self::validate_parameters(self::activate_parameters(), ['pathid' => $pathid, 'state' => $state]);

        // Find the learning path.
        if (!$path = $DB->get_record('local_iomad_learningpath', array('id' => $params['pathid']))) {
            throw new invalid_parameter_exception("Learning Path with id = $pathid does not exist");
        }

        // Check state
        if (($params['state'] != 0) && ($params['state'] != 1)) {
            throw new invalid_parameter_exception("State can only be 0 or 1. Value was $state");
        }
      
        // Set the new state.
        $path->active = $params['state'];
        $DB->update_record('local_iomad_learningpath', $path);

        return true;
    }
}
