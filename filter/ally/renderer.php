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
 * Ally renderer.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package filter_ally
 */

use filter_ally\renderables\wrapper;

class filter_ally_renderer extends plugin_renderer_base {
    /**
     * Render ally wrapper.
     * @param wrapper $wrapper
     * @return bool|string
     * @throws moodle_exception
     */
    public function render_wrapper(wrapper $wrapper) {
        return $this->render_from_template('filter_ally/wrapper', $wrapper);
    }
}
