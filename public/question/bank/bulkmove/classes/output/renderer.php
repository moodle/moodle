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

namespace qbank_bulkmove\output;

/**
 * Class renderer.
 *
 * @package    qbank_bulkmove
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render bulk move.
     *
     * @param array $displaydata
     * @return string
     * @deprecated since Moodle 5.0.
     * @todo MDL-82413 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated('qbank_bulkmove\output\bulk_move', since: '5.0', mdl: 'MDL-71378')]
    public function render_bulk_move_form($displaydata) {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
        return $this->render_from_template('qbank_bulkmove/bulk_move', $displaydata);
    }

}
