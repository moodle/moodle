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
 * Tour renderable.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\output;

defined('MOODLE_INTERNAL') || die();

use tool_usertours\tour as toursource;

/**
 * Tour renderable.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tour implements \renderable {

    /**
     * @var The tour instance.
     */
    protected $tour;

    /**
     * The tour output.
     *
     * @param   toursource      $tour       The tour being output.
     */
    public function __construct (toursource $tour) {
        $this->tour = $tour;
    }

    /**
     * Prepare the data for export.
     *
     * @param   \renderer_base      $output     The output renderable.
     * @return  object
     */
    public function export_for_template(\renderer_base $output) {
        $result = (object) [
            'name'  => $this->tour->get_tour_key(),
            'steps' => [],
            'endtourlabel' => $this->tour->get_endtourlabel(),
            'displaystepnumbers' => $this->tour->get_display_step_numbers(),
        ];

        foreach ($this->tour->get_steps() as $step) {
            $result->steps[] = (new step($step))->export_for_template($output);
        }

        return $result;
    }
}
