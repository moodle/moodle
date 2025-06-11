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
 * Class for exporting user data associated to a scale value
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use core\external\exporter;
use core_user\external\user_summary_exporter;
use renderer_base;

/**
 * Class for exporting user data associated to a scale value.
 *
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scale_value_user_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'email' => [
                'type' => PARAM_RAW,
            ],
        ];
    }

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    protected static function define_other_properties() {
        return [
            'fullname' => [
                'type' => PARAM_RAW,
            ],
            'userid' => [
                'type' => PARAM_INT,
            ],
            'profileimagesmall' => [
                'type' => PARAM_RAW,
            ],
            'profileurl' => [
                'type' => PARAM_RAW,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {

        $userexporter = new user_summary_exporter($this->data, '*', \MUST_EXIST);
        $userexport = $userexporter->export($output);

        return [
            'fullname' => $userexport->fullname,
            'profileimagesmall' => $userexport->profileimageurlsmall,
            'userid' => $userexport->id,
            'profileurl' => $userexport->profileurl,
        ];
    }
}
