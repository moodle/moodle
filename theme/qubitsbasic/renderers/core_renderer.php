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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_qubitsbasic
 * @copyright  2023 Qubits Dev Team.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class theme_qubitsbasic_core_renderer extends theme_boost\output\core_renderer {

    public function brand_color_code()
    {
        global $CFG;
        $osettings = $CFG->cursitesettings;
        $tltmp_txt = array(
            'primary_color' => $osettings->color1,
            'secondary_color' => $osettings->color2              
        );
        return $this->render_from_template('theme_qubitsbasic/custom/brandstylecode', $tltmp_txt);
    }
    
}