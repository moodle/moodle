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

use core\output\plugin_renderer_base;
use core_availability\output\availability_info;

/**
 * Renderer for availability display.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_availability_renderer extends plugin_renderer_base {

    /**
     * @deprecated since Moodle 4.0 MDL-71691 - please do not use this function any more.
     */
    #[\core\attribute\deprecated(availability_info::class, since: '4.0', mdl: 'MDL-71691', final: true)]
    public function render_core_availability_multiple_messages(): void {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }
}
