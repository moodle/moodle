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
 * Contains class \core\output\icon_system_standard
 *
 * @package    core
 * @category   output
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use renderer_base;
use pix_icon;

defined('MOODLE_INTERNAL') || die();

/**
 * Standard icon rendering. No mapping - img tags used.
 *
 * @package    core
 * @category   output
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icon_system_standard extends icon_system {

    public function render_pix_icon(renderer_base $output, pix_icon $icon) {
        $data = $icon->export_for_template($output);
        return $output->render_from_template('core/pix_icon', $data);
    }

    public function get_amd_name() {
        return 'core/icon_system_standard';
    }

}

