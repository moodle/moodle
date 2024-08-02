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
 * Helpers for unit tests.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait tool_usertours_helper_trait {
    /**
     * A helper to create an empty tour.
     *
     * @param   stdClass    $tourconfig     The configuration for the new tour
     * @param   bool        $persist        Whether to persist the data
     * @return  \tool_usertours\tour
     */
    public function helper_create_tour(?\stdClass $tourconfig = null, $persist = true) {
        $minvalues = [
            'id' => null,
            'pathmatch' => '/my/%',
            'enabled' => true,
            'name' => '',
            'description' => '',
            'configdata' => '',
            'displaystepnumbers' => true,
        ];

        if ($tourconfig === null) {
            $tourconfig = new \stdClass();
        }

        foreach ($minvalues as $key => $value) {
            if (!isset($tourconfig->$key)) {
                $tourconfig->$key = $value;
            }
        }

        $tour = \tool_usertours\tour::load_from_record($tourconfig, true);
        if ($persist) {
            $tour->persist(true);
        }

        return $tour;
    }

    /**
     * A helper to create an empty step for the specified tour.
     *
     * @param   stdClass    $stepconfig     The configuration for the new step
     * @param   bool        $persist        Whether to persist the data
     * @return  \tool_usertours\step
     */
    public function helper_create_step(?\stdClass $stepconfig = null, $persist = true) {
        $minvalues = [
            'id' => null,
            'title' => '',
            'content' => '',
            'targettype' => \tool_usertours\target::TARGET_UNATTACHED,
            'targetvalue' => '',
            'sortorder' => 0,
            'configdata' => '',
        ];

        if ($stepconfig === null) {
            $stepconfig = new \stdClass();
        }

        foreach ($minvalues as $key => $value) {
            if (!isset($stepconfig->$key)) {
                $stepconfig->$key = $value;
            }
        }

        $step = \tool_usertours\step::load_from_record($stepconfig, true);
        if ($persist) {
            $step->persist(true);
        }

        return $step;
    }
}
