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

namespace qbank_viewcreator\output;

/**
 * Class renderer.
 * @package    qbank_viewcreator
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render question creator.
     *
     * @param array $displaydata
     * @return string
     */
    public function render_creator_name($displaydata) {
        return $this->render_from_template('qbank_viewcreator/creator_display', $displaydata);
    }

    /**
     * Render question edit form callback.
     *
     * @param array $displaydata
     * @return string
     */
    public function render_version_info($displaydata) {
        return $this->render_from_template('qbank_viewcreator/version_info', $displaydata);
    }

}
